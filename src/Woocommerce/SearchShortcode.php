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

    public function content($attrs)
    {
        wp_enqueue_script('woo_raffles_number_search', WOORAFFLES_URL . 'assets/js/search.js', ['jquery-core', 'jquery-mask', ], '1.0.1', true);

        extract(shortcode_atts(array(
            'colunas' => 1,
            'id' => 0,
        ), $attrs));

        $columns = $attrs['colunas'] ?? 1;
        $id = $attrs['id'] ?? 0;

        $cpf = sanitize_text_field($_POST['cpf'] ?? '');
        $cpf = preg_replace('/[^0-9]*([0-9]{3})[^0-9]*([0-9]{3})[^0-9]*([0-9]{3})[^0-9]*([0-9]{2})[^0-9]*/', '$1$2$3$4', $cpf);

        $data = [];
        if (isset($_POST['cpf'])) {
            $data = strlen($cpf) > 0 ? $this->getResults($cpf, $id) : [];
        }

        ob_start();
        $this->getPart('search', 'form', ['cpf' => $cpf]);

        if (!empty($data)) {
            $this->getPart('search', 'content', ['columns' => $columns, 'data' => $data]);
        }

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    protected function getResults($cpf, $product_id = 0)
    {
        global $wpdb;

        $table_name = Database::$table_name;

        $wpdb->query("SET session group_concat_max_len=500000;");

        $query = '';

        if ($product_id > 0) {
            $query = $wpdb->prepare('AND wrf.product_id = %s', $product_id);
        }

        return $wpdb->get_results(
            $wpdb->prepare("
            SELECT wrf.product_id,
                   GROUP_CONCAT(wrf.generated_number ORDER BY wrf.generated_number ASC SEPARATOR ',') AS quotes
            FROM {$wpdb->prefix}{$table_name} wrf 
            INNER JOIN {$wpdb->prefix}postmeta pst ON pst.post_id = wrf.order_id 
            WHERE pst.meta_key = '_billing_cpf' AND (REPLACE(REPLACE(REPLACE(REPLACE(pst.meta_value, '.', ''), '-', ''), ' ', ''), '_', '') = %s)
                AND wrf.order_item_id != ''
                AND wrf.product_id = %s
            GROUP BY product_id
            ORDER BY wrf.generated_number ASC;
        ", $cpf, $product_id)
        );
    }

    public function getProductNumbersByCPF()
    {

    }
}
?>