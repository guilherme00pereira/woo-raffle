<?php

namespace WooRaffles\Woocommerce;

use WooRaffles\Admin\Database;

if (!defined('ABSPATH')) {
    exit;
}

class QuotesOpenShortcode extends Template
{
    public function __construct()
    {
        parent::__construct();

        add_shortcode('woo-raffles-cotas_abertas', [self::class, 'content']);
    }

    public static function content($attrs)
    {
        extract(shortcode_atts(array(
            'id' => 0,
        ), $attrs));

        ob_start();

        $product_id = $attrs['id'] ?? '';

        $product = wc_get_product($product_id);

        $qty = $product->get_stock_quantity() + $product->get_total_sales();

        $numbers_disabled = self::getNumbersByProductId($product_id);
        $numbers_selected = [];

        $cart = \WC()->cart;
        if ($cart) {
            foreach ($cart->get_cart() as $cart_item) {
                if ((int)$product_id === $cart_item['product_id']) {
                    $numbers_selected = $cart_item['woo_raffles_numbers'] ?? [];
                }
            }
        }

        self::getPart('quotes', 'open', [
                'numbers_disabled' => $numbers_disabled,
                'numbers_selected' => $numbers_selected,
                'product_id' => $product_id,
                'qty' => $qty,
            ]
        );

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    public static function getNumbersByProductId($product_id): array
    {
        global $wpdb;

        $table_name = Database::$table_name;

        return $wpdb->get_col(
            $wpdb->prepare(
                "SELECT wrf.generated_number
                        FROM {$wpdb->prefix}{$table_name} wrf
                        WHERE wrf.product_id = %d;",
                $product_id,
            )
        );
    }
}