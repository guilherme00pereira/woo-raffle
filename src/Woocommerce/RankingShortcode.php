<?php

namespace WooRaffles\Woocommerce;

use WooRaffles\Admin\Database;

if (!defined('ABSPATH')) {
    exit;
}

class RankingShortcode extends Template
{
    public function __construct()
    {
        parent::__construct();

        add_shortcode('woo-raffles-ranking', [self::class, 'content']);
    }

    public static function content($attrs)
    {
        extract(shortcode_atts(array(
            'id' => 0,
            'quantidade' => 10,
            'tipo' => 'quantidade',
        ), $attrs));

        $default = true;

        $product_id = intval($attrs['id'] ?? 0);
        $type = $attrs['tipo'] ?? 'quantidade';

        if ($product_id > 0) {
            $product = wc_get_product($product_id);
            if ($product) {
                $data = self::getResultsQtyByProductId($product->get_id(), intval($attrs['quantidade'] ?? 10));
                $default = false;
            }
        } elseif ($type === 'valor') {
            $data = self::getResultsTotal(intval($attrs['quantidade'] ?? 10));
            $default = false;
        }

        if ($default) {
            $data = self::getResultsQty(intval($attrs['quantidade'] ?? 10));
        }

        ob_start();
        self::getPart('ranking', 'content', [
            'data' => $data,
        ]);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    protected static function getResultsQty($limit)
    {
        global $wpdb;

        $table_name = Database::$table_name;

        return $wpdb->get_results(
            $wpdb->prepare("
                SELECT COUNT(wrf.generated_number) AS qty, pst.post_author,
                    CONCAT(
                        (SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_billing_first_name' AND post_id = wrf.order_id), 
                        ' ', 
                        (SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_billing_last_name' AND post_id = wrf.order_id)
                    ) AS user_name
                FROM {$wpdb->prefix}{$table_name} wrf 
                LEFT JOIN {$wpdb->prefix}posts pst ON pst.ID = wrf.order_id 
                GROUP BY pst.post_author, user_name
                WHERE wrf.order_item_id NOT NULL
                ORDER BY qty DESC
                LIMIT %d;
            ", $limit)
        );
    }

    protected static function getResultsQtyByProductId($product_id, $limit)
{
    global $wpdb;

    $table_name = Database::$table_name;

    $wpdb->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

    return $wpdb->get_results(
        $wpdb->prepare("
            SELECT
                COUNT(wrf.generated_number) AS qty, pst.post_author,
                (
                    SELECT GROUP_CONCAT(pt2.meta_value SEPARATOR ' ') 
                    FROM {$wpdb->prefix}postmeta pt2
                    WHERE (pt2.meta_key = '_billing_first_name' || pt2.meta_key = '_billing_last_name') 
                    AND pt2.post_id = wrf.order_id
                ) AS user_name,
                REPLACE(REPLACE((SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_billing_cpf' AND post_id = wrf.order_id), '.', ''), '-', '') AS cpf
            FROM {$wpdb->prefix}{$table_name} wrf 
            LEFT JOIN {$wpdb->prefix}posts pst ON pst.ID = wrf.order_id 
            WHERE wrf.product_id = %d
            GROUP BY cpf
            ORDER BY qty DESC
            LIMIT %d;
        ", $product_id, $limit)
    );
}

    protected static function getResultsTotal($limit)
    {
        global $wpdb;

        $table_name = Database::$table_name;
        $data =  $wpdb->get_results(
            $wpdb->prepare("
                SELECT SUM(pstm.meta_value) AS qty, pst.post_author,
                    CONCAT(
                        (SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_billing_first_name' AND post_id = wrf.order_id), 
                        ' ', 
                        (SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_billing_last_name' AND post_id = wrf.order_id)
                    ) AS user_name
                FROM {$wpdb->prefix}{$table_name} wrf 
                LEFT JOIN {$wpdb->prefix}posts pst ON pst.ID = wrf.order_id 
                LEFT JOIN {$wpdb->prefix}postmeta pstm ON pstm.post_id = pst.ID 
                WHERE pstm.meta_key = '_order_total'
                GROUP BY pst.post_author, user_name
                ORDER BY qty DESC
                LIMIT %d;
            ", $limit)
        );

        return $data;
    }
}