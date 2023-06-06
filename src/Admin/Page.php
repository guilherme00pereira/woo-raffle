<?php

namespace WooRaffles\Admin;

class Page extends Template
{

    public function __construct()
    {
        parent::__construct();
        add_action('admin_menu', [$this, 'addMenu']);
        add_action('wp_ajax_ajaxGetRaffleData', [$this, 'ajaxGetRaffleData']);
    }

    public function addMenu()
    {
        add_menu_page(
            __('Sorteio', 'woo-raffles'),
            __('Sorteio', 'woo-raffles'),
            'manage_options',
            'woo-raffles',
            [$this, 'render'],
            'dashicons-tickets-alt',
            56
        );
    }

    public function render()
    {
        $this->enqueueScript();

        ob_start();
        self::getPart('page', 'index', []);

        $content = ob_get_contents();
        ob_end_clean();

        echo $content;
    }

    public function enqueueScript()
    {
        wp_enqueue_script('woo-raffle-admin-page', WOORAFFLES_URL . 'assets/js/admin-page.js', ['jquery-core'], false, true);
        wp_localize_script('woo-raffle-admin-page', 'ajaxobj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('woo-raffle-admin-page'),
            'action_ajaxGetRaffleData' => 'ajaxGetRaffleData',
        ]);
    }

    public function ajaxGetRaffleData()
    {
        try {
            $product_id = $_POST['product_id'];
            $quota_number = $_POST['quota_number'];
            $raffleData = Database::getRaffleData($product_id, $quota_number);
            wp_send_json_success('success');
        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage());
        }
        wp_die();
    }

}