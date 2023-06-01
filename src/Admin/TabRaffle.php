<?php

namespace WooRaffles\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class TabRaffle extends Template
{
    public function __construct()
    {
        parent::__construct();

        add_action('woocommerce_process_product_meta_simple', [self::class, 'saveFields']);
        add_action('woocommerce_product_data_panels', [self::class, 'content']);

        add_filter('product_type_options', [self::class, 'options']);
        add_filter('woocommerce_product_data_tabs', [self::class, 'tabs']);
    }

    public static function content()
    {
        self::getPart('raffle-tab', 'content');
    }

    public static function options($options)
    {
        $options['woo_raffle'] = [
            'id' => '_woo_raffle',
            'wrapper_class' => 'show_if_simple',
            'label' => __('Sorteio', 'woocommerce'),
            'description' => __('Criando sorteio no produto.', 'woocommerce'),
            'default' => 'no',
        ];

        return $options;
    }

    public static function saveFields($post_id)
    {
        $is_woo_raffle = isset($_POST['_woo_raffle']) ? 'yes' : 'no';
        update_post_meta($post_id, '_woo_raffle', $is_woo_raffle);

        $is_numbers_random = isset($_POST['_woo_raffles_numbers_random']) ? 'yes' : 'no';
        update_post_meta($post_id, '_woo_raffles_numbers_random', $is_numbers_random);

        $is_numbers_open = isset($_POST['_woo_raffles_numbers_open']) ? 'yes' : 'no';
        update_post_meta($post_id, '_woo_raffles_numbers_open', $is_numbers_open);

        $str_pad_left = intval($_POST['_woo_raffles_str_pad_left'] ?? 5);
        update_post_meta($post_id, '_woo_raffles_str_pad_left', $str_pad_left);

        $max_stock = intval($_POST['_woo_raffles_max_stock'] ?? 0);
        update_post_meta($post_id, '_woo_raffles_max_stock', $max_stock);

        $product = wc_get_product($post_id);

        Stock::change($product);
    }

    public static function tabs($tabs)
    {
        $show_class = 'show_if_woo_raffle';

        $tabs['woo_raffle'] = [
            'label' => __('Sorteio', 'woo-raffles'),
            'target' => 'woo_raffle_options',
            'class' => [$show_class],
        ];

        return $tabs;
    }
}