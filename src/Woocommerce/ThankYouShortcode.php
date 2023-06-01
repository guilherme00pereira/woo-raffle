<?php

namespace WooRaffles\Woocommerce;

use WooRaffles\Admin\Database;

if (!defined('ABSPATH')) {
    exit;
}

class ThankYouShortcode extends Template
{
    public function __construct()
    {
        parent::__construct();

        add_shortcode('woo-raffles-ordem_finalizada', [self::class, 'content']);
    }

    public static function content($attrs)
    {
        extract(shortcode_atts(array(
            'id' => 0,
            'colunas' => 1,
        ), $attrs));

        $columns = $attrs['colunas'] ?? 1;
        $order_id = $attrs['id'] ?? 0;

        ob_start();

        if (isset($order) || intval($order_id) > 0) :
            $data = self::getResults($order ? $order->get_id() : $order_id);

            self::getPart('search', 'content', ['columns' => $columns, 'data' => $data]);
        endif;

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    protected static function getResults($order_id)
    {
        global $wpdb;

        $table_name = Database::$table_name;

        $wpdb->query("SET session group_concat_max_len=500000;");

        return $wpdb->get_results(
            $wpdb->prepare("
                SELECT wrf.product_id,
                   GROUP_CONCAT(wrf.generated_number ORDER BY wrf.generated_number ASC SEPARATOR ',') AS quotes
                FROM {$wpdb->prefix}{$table_name} wrf
                WHERE wrf.order_id = %d
                GROUP BY wrf.product_id
                ORDER BY wrf.generated_number ASC;
            ", $order_id)
        );
    }
}