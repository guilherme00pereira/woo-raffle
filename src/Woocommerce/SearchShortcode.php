<?php

namespace WooRaffles\Woocommerce;

use WooRaffles\Admin\Database;

if (!defined('ABSPATH')) {
    exit;
}

class SearchShortcode extends Template
{
    public function __construct()
    {
        parent::__construct();

        add_shortcode('woo-raffles-buscar', [$this, 'content']);
        add_action('wp_ajax_getProductNumbersByCPF', [$this, 'getProductNumbersByCPF']);
        add_action('wp_ajax_nopriv_getProductNumbersByCPF', [$this, 'getProductNumbersByCPF']);
    }

    public function content()
    {
        wp_enqueue_script(
            'woo_raffles_number_search',
            WOORAFFLES_URL . 'assets/js/search.js',
            ['jquery-core', 'jquery-mask' ],
            '1.0.1',
            true);

        ob_start();
        $this->getPart('search', 'form');

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    protected function getResults($cpf)
    {
        global $wpdb;

        $table_name = Database::$table_name;

        $wpdb->query("SET session group_concat_max_len=500000;");

        return $wpdb->get_results(
            $wpdb->prepare("
            SELECT wrf.product_id AS product, wrf.order_id,
            GROUP_CONCAT(wrf.generated_number ORDER BY wrf.generated_number ASC SEPARATOR ',') AS quotes
            FROM {$wpdb->prefix}{$table_name} wrf 
            INNER JOIN {$wpdb->prefix}postmeta pst ON pst.post_id = wrf.order_id 
            WHERE pst.meta_key = '_billing_cpf' AND pst.meta_value = %s
            AND wrf.order_item_id != ''
            GROUP BY product
            ORDER BY wrf.generated_number ASC;
        ", $cpf)
        );
    }

    public function getProductNumbersByCPF()
    {
        if (isset($_GET['cpf']) && strlen($_GET['cpf']) > 0) {
            $cpf = sanitize_text_field($_GET['cpf'] ?? '');
            //$cpf = preg_replace('/[^0-9]*([0-9]{3})[^0-9]*([0-9]{3})[^0-9]*([0-9]{3})[^0-9]*([0-9]{2})[^0-9]*/', '$1$2$3$4', $cpf);
            $data = $this->getResults($cpf);

            ob_start();
            $this->getPart('search', 'content', [
                'data' => $data,
            ]);
            $content = ob_get_contents();
            ob_end_clean();

            wp_send_json_success([ 'html' => $content ], 200);

        } else {
            wp_send_json_error([ 'message' => 'CPF inválido.' ], 400);
        }
    }
}
?>