<?php

namespace WooRaffles\Woocommerce;

use UPFlex\MixUp\Core\Base;

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

class AddToCart extends Base
{
    public function __construct()
    {
        add_action('wp_ajax_nopriv_woo_numbers_selected', [self::class, 'getNumbers']);
        add_action('wp_ajax_woo_numbers_selected', [self::class, 'getNumbers']);

        add_filter('woocommerce_cart_item_quantity', [self::class, 'changeQuantity'], 10, 3);
    }

    public static function getNumbers()
    {
        $error = true;
        $key = '';
        $key_meta_data = 'woo_raffles_numbers';
        $msg = __('O produto não possui estoque suficiente.', 'woo-raffles');
        $redirect = null;

        $product_id = sanitize_text_field($_POST['product_id'] ?? '');
        $numbers = sanitize_text_field($_POST['numbers'] ?? '');
        $numbers_selected = explode(',', $numbers);
        $qty = count($numbers_selected);

        $product = wc_get_product($product_id);

        if ((int)$product_id > 0 && ($qty <= $product->get_stock_quantity())) {
            $cart = \WC()->cart;
            $is_removed = self::removeItemInCart($product_id, $cart);

            try {
                $key = $cart->add_to_cart(
                    $product_id,
                    $qty,
                    0,
                    [],
                    [$key_meta_data => $numbers_selected]
                );
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }

            if ($key) {
                $msg = $is_removed
                    ? _n('Número atualizado com sucesso.', 'Números atualizados com sucesso.', $qty, 'woo-raffles')
                    : _n('Número adicionado com sucesso.', 'Números adicionados com sucesso.', $qty, 'woo-raffles');
                $error = false;
                $redirect = wc_get_checkout_url();
            }
        }

        echo json_encode(['error' => $error, 'msg' => $msg, 'redirect' => $redirect]);
        exit;
    }

    public function changeQuantity($product_quantity, $cart_item_key, $cart_item)
    {
        $numbers = $cart_item['woo_raffles_numbers'] ?? [];

        if ($numbers) {
            return count($numbers);
        }

        return $product_quantity;
    }

    protected static function removeItemInCart($product_id, $cart): bool
    {
        if ($cart) {
            foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
                if ((int)$product_id === $cart_item['product_id']) {
                    $cart->remove_cart_item($cart_item_key);

                    return true;
                }
            }
        }

        return false;
    }
}
