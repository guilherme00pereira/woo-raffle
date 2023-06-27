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
        self::getPart('orders', 'numbers', [
            'numbers' => GenerateNumbers::getNumbers($item_id, $id, 'generated_number'),
            'str_pad_left' => get_post_meta($id, '_woo_raffles_str_pad_left', true) ?? 5,
            'description' => $description,
            'item_id' => $item_id,
        ]);
    }
}
