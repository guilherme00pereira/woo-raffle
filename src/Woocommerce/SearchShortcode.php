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
        wp_enqueue_script(
            'woo_raffles_number_search',
            WOORAFFLES_URL . 'assets/js/search.js',
            ['jquery-core', 'jquery-mask' ],
            '1.0.1',
            true);

        extract(shortcode_atts([
            'id' => 0,
        ], $attrs));

        ob_start();

        $product_ids = $attrs['id'] ?? '';
        $this->getPart('search', 'form', ['product_ids' => $product_ids,]);

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    protected function getResults($cpf, $productIds)
    {
        global $wpdb;

        $table_name = Database::$table_name;

        $cpf_clean = preg_replace('/[^0-9]/', '', $cpf);

        $wpdb->query("SET session group_concat_max_len=500000;");

        $sqlProduct = !empty($productIds) ?  "AND wrf.product_id in ({$productIds})" : "";

        return $wpdb->get_results(
            $wpdb->prepare("
            SELECT wrf.product_id AS product, wrf.order_id,
            GROUP_CONCAT(wrf.generated_number ORDER BY wrf.generated_number ASC SEPARATOR ',') AS quotes
            FROM {$wpdb->prefix}{$table_name} wrf 
            INNER JOIN {$wpdb->prefix}postmeta pst ON pst.post_id = wrf.order_id 
            WHERE pst.meta_key = '_billing_cpf' AND (pst.meta_value = %s OR pst.meta_value = %s)
            AND wrf.order_item_id != ''
            {$sqlProduct}
            GROUP BY wrf.order_id
            ORDER BY wrf.generated_number ASC;
        ", $cpf, $cpf_clean)
        );
    }

    public function getProductNumbersByCPF()
    {
        try {
            if (isset($_GET['cpf']) && strlen($_GET['cpf']) > 0) {
                $cpf = sanitize_text_field($_GET['cpf'] ?? '');
                $productIds = sanitize_text_field($_GET['product_ids'] ?? 0);
                //$cpf = preg_replace('/[^0-9]*([0-9]{3})[^0-9]*([0-9]{3})[^0-9]*([0-9]{3})[^0-9]*([0-9]{2})[^0-9]*/', '$1$2$3$4', $cpf);
                $cpf = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cpf);
                $dbItems = $this->getResults($cpf, $productIds);
                $total = 0;
                $data = [];

                foreach ($dbItems as $item) {
                    $generated_numbers = explode(',', $item->quotes);
                    $total += count($generated_numbers);
                    $product = wc_get_product($item->product);
                    if ($product) {
                        $data[$item->product][] = [
                            'order_id' => $item->order_id,
                            'product' => $product->get_name(),
                            'generated_numbers' => $generated_numbers,
                            'globos' => (int)get_field("numero_globos", $product) ?? 3
                        ];
                    } else {
                        wp_send_json_error([ 'message' => 'CPF inválido.' ], 400);
                        wp_die();
                    }
                }
                
                ob_start();
                $this->getPart('search', 'content', [
                    'total' => $total,
                    'data' => $data,
                ]);
                $content = ob_get_contents();
                ob_end_clean();
                wp_send_json_success([ 'html' => $content ], 200);

            } else {
                wp_send_json_error([ 'html' => 'CPF inválido.' ], 400);
            }
            wp_die();
        } catch (\Exception $e) {
            wp_send_json_error([ 'html' => $e->getMessage() ], 500);
            wp_die();
        }
    }
}
?>