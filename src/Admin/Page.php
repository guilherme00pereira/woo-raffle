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
            'read',
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

            $raffleData = Database::getRaffleQuotesInfo($product_ids, [$quota1, $quota2, $quota3]);

            if (count($raffleData) == 0) {
                $html = "<td colspan='5'>Nenhum dado retornado.</td>";
            } else {
                foreach($raffleData as $key => $value)
                {
                    $status = $this->highlightStatus($value['status']);
                    $html .= "
                    <tr>
                        <td>
                            {$value['sorteio']}
                        </td>
                        <td>
                            {$value['cota']}
                        <td>
                            <a href='post.php?post={$key}&action=edit' target='_blank'>
                                {$key}
                            </a>
                        </td>
                        <td>
                            " . $status . "
                        </td>
                        <td>
                            {$value['nome']} {$value['sobrenome']}
                        </td>
                        <td>
                            {$value['telefone']}
                        </td>
                    </tr>
                    }";
                }
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
