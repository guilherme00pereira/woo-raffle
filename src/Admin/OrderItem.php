<?php

namespace WooRaffles\Admin;

use WooRaffles\Woocommerce\GenerateNumbers;

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

class OrderItem extends Template
{
    public function __construct()
    {
        parent::__construct();

        add_action('woocommerce_order_item_meta_start', [self::class, 'metaGeneratedContent'], 10, 4);
        add_action('woocommerce_order_item_line_item_html', [self::class, 'numbersGeneratedContent'], 10, 3);
        add_action('wp_ajax_ajaxSaveOrderItemOpenNumbers', [self::class, 'ajaxSaveOrderItemOpenNumbers']);
    }

    public static function metaGeneratedContent($item_id, $item)
    {
        $product_id = $item->get_product_id();

        self::getPart('orders-numbers', 'plain', [
            'numbers' => GenerateNumbers::getNumbers($item_id, $product_id, 'generated_number'),
            'str_pad_left' => get_post_meta($product_id, '_woo_raffles_str_pad_left', true) ?? 5,
        ]);
    }
    

    public static function numbersGeneratedContent($item_id, $item, $order)
    {
        $product = $item->get_product();
        $id = $product->get_id();
        $description = $product->get_short_description();
        $random_quotes = get_post_meta($id, '_woo_raffles_numbers_random', true);
        self::getPart('orders', 'numbers', [
            'numbers' => GenerateNumbers::getNumbers($item_id, $id, 'generated_number'),
            'str_pad_left' => get_post_meta($id, '_woo_raffles_str_pad_left', true) ?? 5,
            'description' => $description,
            'item_id' => $item_id,
            'order' => $order,
            'random' => $random_quotes,
        ]);
    }

    public static function ajaxSaveOrderItemOpenNumbers(): void
    {

        try {
            $item_id = $_POST['item_id'] ?? '';
            $numbers = $_POST['numbers'] ?? '';

            if(empty($numbers) && empty($item_id))
            {
                wp_send_json_error('Números não informados', 500);
            }
            $numbers = explode(',', $numbers);
            $meta = wc_get_order_item_meta($item_id, __('Números Escolhidos', 'woo-raffles'));
            if(!empty($meta)) {
                $meta = explode(',', $meta);
                $merge = array_unique(array_merge($meta, $numbers));
                sort($merge);
                wc_update_order_item_meta($item_id, __('Números Escolhidos', 'woo-raffles'), implode(',', $merge));
            } else {
                sort($numbers);
                wc_add_order_item_meta($item_id, __('Números Escolhidos', 'woo-raffles'), implode(',', $numbers));
            }
            wp_send_json_success('Números adicionados', 200);
        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage(), 500);
        }
        wp_die();
    }
}
