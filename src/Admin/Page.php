<?php

namespace WooRaffles\Admin;

class Page extends Template
{

    public function __construct()
    {
        parent::__construct();
        add_action('admin_menu', [$this, 'addMenu']);
        add_action('wp_ajax_ajaxGetRaffleData', [$this, 'ajaxGetRaffleData']);
        add_action('wp_ajax_ajaxSaveThumbLogo', [$this, 'ajaxSaveThumbLogo']);
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
            'action_ajaxSaveThumbLogo' => 'ajaxSaveThumbLogo',
            'logo_export_attachment_post_id' => get_option('raffle_logo_export_attachment_id', 0),
        ]);
        wp_enqueue_media();
    }

    public function ajaxGetRaffleData()
    {
        try {
            $product_id = $_GET['pid'];
            $quota = $_GET['cota'];
            $raffleData = Database::getRaffleQuotaInfo($product_id, $quota);
            $html = "
                <div class='raffle-customer-data'>
                    <h4>Dados do cliente:</h4>
                    <div class='raffle-customer-data__item'>
                        <span class='raffle-customer-data__item__label'>Nome:</span>
                        <span class='raffle-customer-data__item__value'>{$raffleData['_billing_first_name']} {$raffleData['_billing_last_name']} </span>
                    </div>
                    <div class='raffle-customer-data__item'>
                        <span class='raffle-customer-data__item__label'>E-mail:</span>
                        <span class='raffle-customer-data__item__value'>{$raffleData['_billing_email']}</span>
                    </div>
                    <div class='raffle-customer-data__item'>
                        <span class='raffle-customer-data__item__label'>Telefone:</span>
                        <span class='raffle-customer-data__item__value'>{$raffleData['_billing_phone']}</span>
                    </div>
                    <div class='raffle-customer-data__item'>
                        <span class='raffle-customer-data__item__label'>Número:</span>
                        <span class='raffle-customer-data__item__value'>{$raffleData['_billing_cpf']}</span>
                    </div>
                </div>
            ";
            $response = [
                'raffled'       => true,
                'customerData'  => $html
            ];
            wp_send_json_success($response);
        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage());
        }
        wp_die();
    }

    public function ajaxSaveThumbLogo()
    {
        try {
            $attachment_id = $_POST['attachment_id'];
            update_option('raffle_logo_export_attachment_id', $attachment_id);
            wp_send_json_success('Logo salvo com sucesso!');
        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage());
        }
        wp_die();
    }

}