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
        add_action('wp_ajax_getAmountGeneratedErrors', [self::class, 'getAmountGeneratedErrors']);
        add_action('wp_ajax_getStockErrors', [self::class, 'getStockErrors']);
        add_action('wp_ajax_getOrderWithNoNumbersError', [self::class, 'getOrderWithNoNumbersError']);
    }

    public static function getAmountGeneratedErrors()
    {
        global $wpdb;
        $table_name = Database::$table_name;
        $wpdb->query("SET session group_concat_max_len=500000;");
        $html = "";
        $items = $wpdb->get_results(
            "
                SELECT wrf2.order_id, wrf2.order_item_id, wrf2.product_id,
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
                ORDER BY order_id ASC;
                "
        );
        foreach ($items as $item)
        {
            $url = admin_url("post.php?post=$item->order_id&action=edit");
            $html .= '<tr data-pid="' . $item->product_id . '">
                <th scope="row">
                <a href="' . $url . '" target="_blank">
                    ' . $item->order_id . '
                </a>
            </th>
            <td>
                ' . sprintf(__("A quantidade vendida foi %d. E a quantidade gerada foi: %d.", "woo-raffles"), $item->qty, $item->sum_quotes) . '
            </td>
            </tr>';
        }
        wp_send_json_success($html);
        wp_die();
    }

    public static function getStockErrors()
    {
        global $wpdb;
        $table_name = Database::$table_name;
        $wpdb->query("SET session group_concat_max_len=500000;");
        $html = "";
        $items = $wpdb->get_results(
            "
                SELECT 
                        wrf.order_id, wrf.order_item_id, wrf.product_id,
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
                ORDER BY order_id ASC;
                "
        );
        foreach ($items as $item)
        {
            $url = admin_url("post.php?post=$item->order_id&action=edit");
            $html .= '<tr data-pid="' . $item->product_id . '">
                <th scope="row">
                <a href="' . $url . '" target="_blank">
                    ' . $item->order_id . '
                </a>
            </th>
            <td>
                ' . __('Números ultrapassaram a soma do estoque e das vendas.', 'woo-raffles') . '
            </td>
            </tr>';
        }
        wp_send_json_success($html);
        wp_die();
    }

    public static function getOrderWithNoNumbersError()
    {
        global $wpdb;
        $table_name = Database::$table_name;
        $wpdb->query("SET session group_concat_max_len=500000;");
        $html = "";
        $items = $wpdb->get_results(
            "
                select ID from {$wpdb->base_prefix}posts
                where post_status = 'wc-processing'
                and ID not in (select distinct(order_id) from {$wpdb->base_prefix}{$table_name})
                ORDER BY ID ASC;
                "
        );
        foreach ($items as $item)
        {
            $url = admin_url("post.php?post=$item->ID&action=edit");
            $html .= '<tr>
                <th scope="row">
                    <a href="' . $url . '" target="_blank">
                        ' . $item->ID . '
                    </a>
                </th>
                <td>
                    ' . __('Pedido sem números gerados.', 'woo-raffles') . '
                </td>
                </tr>';
        }
        wp_send_json_success($html);
        wp_die();
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
        wp_enqueue_script('woo-raffle-errors-numbers', WOORAFFLES_URL . 'assets/js/errors-numbers.js', ['jquery-core'], '1.0.0', true);

        self::getPart('error', 'numbers');
    }
}