<?php

namespace WooRaffles\Admin;

use UPFlex\MixUp\Core\Base;

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

class Stock extends Base
{
    public function __construct()
    {
        add_action('woocommerce_product_set_stock', [self::class, 'change']);
    }

    public static function change($product)
    {
        $max_stock = get_post_meta($product->get_id(), '_woo_raffles_max_stock', true);

        $qty = $product->get_stock_quantity() + $product->get_total_sales();

        if ($qty > intval($max_stock) && intval($max_stock) > 0) {
            $product->set_stock_quantity($max_stock - $product->get_total_sales());
            $product->save();
        }
    }
}
