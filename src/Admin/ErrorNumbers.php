<?php

namespace WooRaffles\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class ErrorNumbers extends Template
{
    public function __construct()
    {
        parent::__construct();

        add_action('admin_menu', [self::class, 'menu']);
        add_filter('woo_raffles_error_numbers', [self::class, 'getErrors'], 10, 3);
    }

    public static function getErrors($error, $sum_quotes, $qty): string
    {
        if ($error === 'amount_generated') {
            return sprintf(__('A quantidade vendida foi %d. E a quantidade gerada foi: %d.', 'woo-raffles'), $qty, $sum_quotes);
        }

        return __('Números ultrapassaram a soma do estoque e das vendas.', 'woo-raffles');
    }

    protected static function getNumbers()
    {
        global $wpdb;

        $table_name = Database::$table_name;

        $wpdb->query("SET session group_concat_max_len=500000;");

        return $wpdb->get_results(
            $wpdb->prepare("
                (
                    SELECT 
                        wrf.order_id, wrf.order_item_id,
                        GROUP_CONCAT(LPAD(wrf.generated_number, 5, '0') ORDER BY wrf.generated_number ASC SEPARATOR ',') AS quotes,
                        '' AS sum_quotes,
                        '' AS qty,
                        'over_stock' AS error
                    FROM {$wpdb->base_prefix}{$table_name} wrf 
                    INNER JOIN {$wpdb->base_prefix}posts pst ON pst.ID = wrf.order_id
                    WHERE wrf.generated_number > (
                        SELECT SUM(pm.meta_value) 
                         FROM {$wpdb->base_prefix}postmeta pm 
                         WHERE (pm.meta_key = '_stock' || pm.meta_key = 'total_sales') 
                            AND pm.post_id = wrf.product_id LIMIT 1
                    )
                    GROUP BY wrf.order_id, wrf.order_item_id
                )
                UNION ALL
                (
                    SELECT wrf2.order_id, wrf2.order_item_id,
                        GROUP_CONCAT(LPAD(wrf2.generated_number, 5, '0') ORDER BY wrf2.generated_number ASC SEPARATOR ', ') AS quotes,
                        COUNT(wrf2.generated_number) AS sum_quotes, 
                        (
                            SELECT CAST(woi.meta_value AS UNSIGNED)
                            FROM {$wpdb->base_prefix}woocommerce_order_itemmeta woi 
                            WHERE woi.meta_key = '_qty' 
                            AND woi.order_item_id = wrf2.order_item_id LIMIT 1
                        ) AS qty,
                        'amount_generated' AS error
                    FROM {$wpdb->base_prefix}{$table_name} wrf2
                    INNER JOIN {$wpdb->base_prefix}posts pst2 ON pst2.ID = wrf2.order_id 
                    GROUP BY wrf2.order_id, wrf2.order_item_id
                    HAVING sum_quotes < qty
                )
                ORDER BY order_id ASC;
                "
            )
        );
    }

    public static function menu()
    {
        add_submenu_page(
            'woocommerce',
            __('Pedidos com erro', 'woo-raffles'),
            __('Pedidos com erro', 'woo-raffles'),
            'manage_options',
            'woo-raffles-error-numbers',
            [self::class, 'pageContents'],
        );
    }

    public static function pageContents()
    {
        $numbers = self::getNumbers();

        self::getPart('error', 'numbers', [
            'numbers' => $numbers,
            'title' => __('Pedidos com erros nos números', 'woo-raffles'),
        ]);
    }
}