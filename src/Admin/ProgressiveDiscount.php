<?php

namespace WooRaffles\Admin;

use Mattiasgeniar\Percentage\Percentage;
use UPFlex\MixUp\Core\Base;

if (!defined('ABSPATH')) {
    exit;
}

class ProgressiveDiscount extends Base
{
    protected static string $key_field_meta_data = 'woo_raffles_progressive_discount_key';
    protected static string $key_meta_data = 'woo_raffles_progressive_discount';

    public function __construct()
    {
        add_action('acf/init', [$this, 'fieldGroup']);
        add_action('woocommerce_before_calculate_totals', [$this, 'cartItem'], 20, 1);

        add_action('wp_ajax_nopriv_woo_discount_progressive', [$this, 'getNumbers']);
        add_action('wp_ajax_woo_discount_progressive', [$this, 'getNumbers']);

        add_filter('woocommerce_cart_item_quantity', [$this, 'changeQuantity'], 10, 3);
        add_filter('woocommerce_get_item_data', [$this, 'getItemData'], 10, 2);
    }

    public function cartItem($cart)
    {
        if (did_action('woocommerce_before_calculate_totals') >= 2)
            return;

        foreach ($cart->get_cart() as $cart_item):
            $key_field = $cart_item[self::$key_field_meta_data] ?? '';
            $product_id = $cart_item['product_id'] ?? '';
            $qty = $cart_item[self::$key_meta_data] ?? 0;

            if (!(int)$qty) {
                continue;
            }

            $discount_in_product = have_rows('desconto_progressivo', $product_id);

            if ($discount_in_product):
                while (have_rows('desconto_progressivo', $product_id)) : the_row();

                    if ((int)get_sub_field('quantidade') === (int)$cart_item['quantity']):

                        $row_index = get_row_index();

                        if(intval($key_field) !== $row_index && $key_field !== '') continue;

                        $price = $cart_item['data']->get_regular_price();
                        $y = Percentage::of( floatval(get_sub_field('porcentagem')) ?? 0.0, $price);
                        $price = $price - $y;
                        $cart_item['data']->set_price($price);
                    endif;
                endwhile;
            endif;
        endforeach;
    }

    public function changeQuantity($product_quantity, $cart_item_key, $cart_item)
    {
        $qty = $cart_item[self::$key_meta_data] ?? 0;

        if ($qty) {
            return $qty;
        }

        return $product_quantity;
    }

    public function fieldGroup()
    {
        acf_add_local_field_group(array(
            'key' => 'group_60a553ae2d1fb',
            'title' => 'Opções Desconto Progressivo',
            'fields' => array(
                array(
                    'key' => 'field_615122e625900',
                    'label' => 'Layout da exibição das faixas',
                    'name' => 'layout_faixas',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'Layout 1' => 'Layout 1',
                        'Layout 2' => 'Layout 2',
                    ),
                    'default_value' => false,
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 0,
                    'return_format' => 'value',
                    'ajax' => 0,
                    'placeholder' => '',
                ),
                array(
                    'key' => 'field_60a5541fae39f',
                    'label' => 'Faixas de Desconto progressivo',
                    'name' => 'desconto_progressivo',
                    'type' => 'repeater',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'collapsed' => '',
                    'min' => 0,
                    'max' => 0,
                    'layout' => 'row',
                    'button_label' => 'Adicionar faixa de desconto progressivo',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_610b4d1f720eb',
                            'label' => 'Regra ou faixa',
                            'name' => '',
                            'type' => 'accordion',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'open' => 0,
                            'multi_expand' => 0,
                            'endpoint' => 0,
                        ),
                        array(
                            'key' => 'field_60a55434ae3a4',
                            'label' => 'Valor visível',
                            'name' => 'valor_mkt',
                            'type' => 'text',
                            'instructions' => 'Por exemplo: R$ 50,00',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'min' => '',
                            'max' => '',
                            'step' => '',
                        ),
                        array(
                            'key' => 'field_60a55434ae3z8',
                            'label' => 'Texto números',
                            'name' => 'texto_numeros',
                            'type' => 'text',
                            'instructions' => 'Se preenchido, irá substituir o texto padrão ex." 5 NÚMEROS "',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => 'Exemplo: COMPRE 3 LEVE 6',
                        ),
                        array(
                            'key' => 'field_60a55434ae3a5',
                            'label' => 'Título dessa opção',
                            'name' => 'titulo_opcao',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'min' => '',
                            'max' => '',
                            'step' => '',
                        ),
                        array(
                            'key' => 'field_60a55434ae3a6',
                            'label' => 'Subtítulo dessa opção',
                            'name' => 'subtitulo_opcao',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'min' => '',
                            'max' => '',
                            'step' => '',
                        ),
                        array(
                            'key' => 'field_60a55445ae3a1',
                            'label' => 'Porcentagem',
                            'name' => 'porcentagem',
                            'type' => 'number',
                            'instructions' => 'Apenas números, utilize ponto para decimais.',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'min' => '',
                            'max' => '',
                            'step' => '',
                        ),
                        array(
                            'key' => 'field_60a55445ae3b1',
                            'label' => 'Quantidade de números',
                            'name' => 'quantidade',
                            'type' => 'number',
                            'instructions' => 'Apenas números',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'min' => '',
                            'max' => '',
                            'step' => '',
                        ),
                        array(
                            'key' => 'field_615122e62592c',
                            'label' => 'Destacar essa opção',
                            'name' => 'destacar_essa_opcao',
                            'type' => 'select',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'choices' => array(
                                'Normal' => 'Normal',
                                'Destacar' => 'Destacar',
                            ),
                            'default_value' => false,
                            'allow_null' => 0,
                            'multiple' => 0,
                            'ui' => 0,
                            'return_format' => 'value',
                            'ajax' => 0,
                            'placeholder' => '',
                        ),
                    ),
                ),

            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'product',
                    ),
                ),
            ),
            'menu_order' => 2,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));
    }

    public function getItemData($data, $cart_item)
    {
        $key_field = $cart_item[self::$key_field_meta_data] ?? '';
        $product_id = $cart_item['product_id'] ?? '';
        $qty = $cart_item[self::$key_meta_data] ?? 0;

        if ((int)$qty > 0) {
            $discount_in_product = have_rows('desconto_progressivo', $product_id);

            if ($discount_in_product):
                while (have_rows('desconto_progressivo', $cart_item['product_id'])) : the_row();

                    $percent = get_sub_field('porcentagem');

                    if ((int)get_sub_field('quantidade') === (int)$cart_item['quantity'] && intval($percent) > 0):

                        $row_index = get_row_index();

                        if(intval($key_field) !== $row_index && $key_field !== '') continue;

                        $data[] = array(
                            'name' => __('Desconto Aplicado', 'woo-raffles'),
                            'value' => "- $percent%"
                        );
                    endif;

                endwhile;
            endif;
        }

        return $data;
    }

    public function getNumbers()
    {
        $key = '';

        $field_key = sanitize_text_field($_POST['key'] ?? 1);
        $product_id = sanitize_text_field($_POST['product_id'] ?? '');
        $qty = sanitize_text_field($_POST['qty'] ?? '');

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
                    [self::$key_meta_data => $qty, self::$key_field_meta_data => $field_key]
                );
            } catch (\Exception $e) {
                error_log($e->getMessage());
                wp_send_json_error(['msg' => __('Ocorreu um erro ao adicionar o produto ao carrinho.', 'woo-raffles'), 'error' => true]);
            }

            if ($key) {
                $msg = $is_removed
                    ? __('Quantidade atualizada com sucesso.', 'woo-raffles')
                    : __('Quantidade adicionada com sucesso.', 'woo-raffles');
                $error = false;
                $redirect = wc_get_checkout_url();
                wp_send_json_success(compact('msg', 'error', 'redirect'));
            }
        } else {
            wp_send_json_error(['msg' => __('O produto não possui estoque suficiente.', 'woo-raffles'), 'error' => true]);
        }

        wp_die();
    }

    protected function removeItemInCart($product_id, $cart): bool
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