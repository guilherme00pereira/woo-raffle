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
        add_action('wp_ajax_ajaxRemoveThumbLogo', [$this, 'ajaxRemoveThumbLogo']);
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
        wp_enqueue_style('woo-raffle-multi-dropdown-style', WOORAFFLES_URL . 'assets/js/jquery.sumoselect/sumoselect.min.css');
        wp_enqueue_script('woo-raffle-admin-page', WOORAFFLES_URL . 'assets/js/admin-page.js', ['jquery-core'], false, true);
        wp_enqueue_script('woo-raffle-multi-dropdown', WOORAFFLES_URL . 'assets/js/jquery.sumoselect/jquery.sumoselect.min.js', ['jquery-core'], false, true);

        wp_localize_script('woo-raffle-admin-page', 'ajaxobj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('woo-raffle-admin-page'),
            'action_ajaxGetRaffleData' => 'ajaxGetRaffleData',
            'action_ajaxSaveThumbLogo' => 'ajaxSaveThumbLogo',
            'action_ajaxRemoveThumbLogo' => 'ajaxRemoveThumbLogo',
            'logo_export_attachment_post_id' => get_option('raffle_logo_export_attachment_id', 0),
        ]);
        wp_enqueue_media();
    }

    public function ajaxGetRaffleData()
    {
        try {
            $product_ids = $_GET['pid'];
            $quota1= $_GET['cota1'];
            $quota2= $_GET['cota2'];
            $quota3= $_GET['cota3'];
            $raffleData = Database::getRaffleQuotaInfo($product_ids, [$quota1, $quota2, $quota3]);
            if ($raffleData['status'] == null) {
                $html = "<p>Cota não encontrada.</p>";
            } else {
                $status = $this->highlightStatus($raffleData['status']);
                $html = "
                <div class='raffle-customer-data'>
                    <h4>Dados do pedido:</h4>
                    <div>
                        <span class='raffle-customer-data-label'>Pedido:</span>
                        <a href='post.php?post={$raffleData['pedido']}&action=edit' target='_blank'>
                            {$raffleData['pedido']}
                        </a>
                        (" . $status . ")
                    </div>
                    <div>
                        <span class='raffle-customer-data-label'>Nome:</span>
                        <span>{$raffleData['_billing_first_name']} {$raffleData['_billing_last_name']} </span>
                    </div>
                    <div>
                        <span class='raffle-customer-data-label'>Telefone:</span>
                        <span>{$raffleData['_billing_phone']}</span>
                    </div>
                </div>
            ";
            }
            $response = [
                'raffled'       => true,
                'customerData'  => $html
            ];
            wp_send_json_success($response, 200);
        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage(), 500);
        }
        wp_die();
    }

    public function ajaxSaveThumbLogo()
    {
        try {
            $attachment_id = $_POST['attachment_id'];
            update_option('raffle_logo_export_attachment_id', $attachment_id);
            wp_send_json_success('Logo salvo com sucesso!', 200);
        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage(), 500);
        }
        wp_die();
    }

    public function ajaxRemoveThumbLogo()
    {
        try {
            $delete = delete_option('raffle_logo_export_attachment_id');
            if($delete) {
                wp_send_json_success('Logo removido com sucesso!', 200);
            } else {
                wp_send_json_error('Não foi possível remove a logo', 500);    
            }
        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage(), 500);
        }
        wp_die();
    }

    private function highlightStatus($status): string
    {
        if(!empty($status)) {
            $color = '#ff0000';
            if ($status === 'wc-processing') {
                $color = '#00a100';
                $status = 'Processando';
            } else if ($status === 'wc-completed') {
                $color = '#0071a1';
                $status = 'Concluído';
            } else if ($status === 'wc-cancelled') {
                $status = 'Cancelado';
            } else if ($status === 'wc-refunded') {
                $status = 'Reembolsado';
            } else if ($status === 'wc-failed') {
                $status = 'Falhou';
            } else if ($status === 'wc-on-hold') {
                $color = '#ffa200';
                $status = 'Aguardando';
            } else {
                $color = '#ffa200';
                $status = 'Pendente';
            }
            return "<span style='color: {$color}'>{$status}</span>";
        }
        return '';
    }

}