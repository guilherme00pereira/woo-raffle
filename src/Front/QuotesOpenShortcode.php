<?php

namespace WooRaffles\Front;

use WooRaffles\Admin\Database;
use WooRaffles\Admin\Raffle;
use Exception;

if (!defined('ABSPATH')) {
    exit;
}

class QuotesOpenShortcode extends Template
{
    public function __construct()
    {
        parent::__construct();

        add_shortcode('woo-raffles-cotas_abertas', [$this, 'content_v2']);
        add_action('wp_ajax_ajaxApiRifaInfos', [$this, 'ajaxApiRifaInfos']);
        add_action('wp_ajax_no´priv_ajaxApiRifaInfos', [$this, 'ajaxApiRifaInfos']);
        add_action('wp_ajax_addToCart', [$this, 'addToCart']);
        add_action( 'wp_ajax_nopriv_addToCart', [$this, 'addToCart'] );
    }

    public function content($attrs)
    {
        extract(shortcode_atts(array(
            'id' => 0,
        ), $attrs));

        ob_start();

        $product_id = $attrs['id'] ?? '';

        $product = wc_get_product($product_id);

        $qty = $product->get_stock_quantity() + $product->get_total_sales();

        $numbers_disabled = self::getNumbersByProductId($product_id);
        $numbers_selected = [];

        $cart = \WC()->cart;
        if ($cart) {
            foreach ($cart->get_cart() as $cart_item) {
                if ((int)$product_id === $cart_item['product_id']) {
                    $numbers_selected = $cart_item['woo_raffles_numbers'] ?? [];
                }
            }
        }

        self::getPart('quotes', 'open', [
                'numbers_disabled' => $numbers_disabled,
                'numbers_selected' => $numbers_selected,
                'product_id' => $product_id,
                'qty' => $qty,
            ]
        );

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    public static function getNumbersByProductId($product_id): array
    {
        global $wpdb;

        $table_name = Database::$table_name;

        return $wpdb->get_col(
            $wpdb->prepare(
                "SELECT wrf.generated_number
                        FROM {$wpdb->prefix}{$table_name} wrf
                        WHERE wrf.product_id = %d;",
                $product_id,
            )
        );
    }

    public function content_v2($attrs): string
    {
        $product_id = $attrs['id'];

        $this->enqueueStyleAndScript($product_id);

        $total_total = 0; // TOTAL TOTAL
            $total_d = 0; // DISPONIVEIS
            $total_r = 0; // RESERVADAS
            $total_c = 0; // COMPRAPAS
            $total_p = 0; // PARTICIPANTES

            $quantidade_cotas = get_field("numero_de_cotas",$product_id);

            if($quantidade_cotas == ""): $quantidade_cotas = 0; endif;

            $cores_modelos_todas = get_field("cores_modelos_todas",$product_id);
            $cor_de_fundo_aba_todas = $cores_modelos_todas["cor_de_fundo_aba_todas"];
            $cor_do_texto_e_borda_aba_todas = $cores_modelos_todas["cor_do_texto_e_borda_aba_todas"];

            $cores_modelos_livres = get_field("cores_modelos_livres",$product_id);
            $cor_de_fundo_aba_livres = $cores_modelos_livres["cor_de_fundo_aba_livres"];
            $cor_do_texto_e_borda_aba_livres = $cores_modelos_livres["cor_do_texto_e_borda_aba_livres"];

            $cores_modelos_reservadas = get_field("cores_modelos_reservadas",$product_id);
            $cor_de_fundo_aba_reservadas = $cores_modelos_reservadas["cor_de_fundo_aba_reservadas"];
            $cor_do_texto_e_borda_aba_reservadas = $cores_modelos_reservadas["cor_do_texto_e_borda_aba_reservadas"];

            $cores_modelos_pagas = get_field("cores_modelos_pagas",$product_id);
            $cor_de_fundo_aba_pagas = $cores_modelos_pagas["cor_de_fundo_aba_pagas"];
            $cor_do_texto_e_borda_aba_pagas = $cores_modelos_pagas["cor_do_texto_e_borda_aba_pagas"];

            $estilo_modelo4 = "

     /* CSS MODELO 6 */
     

     .label-aba-todas,
     .label-aba-todas:hover,
     .cotas-disponiveis-modelo-4 .pcss3t > input:checked + label.label-aba-todas{
       background: {$cor_de_fundo_aba_todas} !important;
       border:1px solid {$cor_do_texto_e_borda_aba_todas} !important;
       color: {$cor_do_texto_e_borda_aba_todas} !important;
     }

     .label-aba-participantes,
     .label-aba-participantes:hover{
       background: {$cor_de_fundo_aba_todas} !important;
       border:1px solid {$cor_do_texto_e_borda_aba_todas} !important;
       border-bottom:1px solid {$cor_do_texto_e_borda_aba_todas} !important;
       color: {$cor_do_texto_e_borda_aba_todas} !important;
     }

     .label-aba-livres,
     .label-aba-livres:hover,
     .cotas-disponiveis-modelo-4 .pcss3t > input:checked + label.label-aba-livres{
       background: {$cor_de_fundo_aba_livres} !important;
       border:1px solid {$cor_do_texto_e_borda_aba_livres} !important;
       color: {$cor_do_texto_e_borda_aba_livres} !important;
     }

     .label-aba-reservadas,
     .label-aba-reservadas:hover,
     .cotas-disponiveis-modelo-4 .pcss3t > input:checked + label.label-aba-reservadas{
       background: {$cor_de_fundo_aba_reservadas} !important;
       border:1px solid {$cor_do_texto_e_borda_aba_reservadas} !important;
       color: {$cor_do_texto_e_borda_aba_reservadas} !important;
     }
     .modelo-4-reservada{
       background: {$cor_de_fundo_aba_reservadas};
       border: 1px solid {$cor_do_texto_e_borda_aba_reservadas} !important;
       color:{$cor_do_texto_e_borda_aba_reservadas};
     }
     .modelo-4-reservada label{
       background: {$cor_de_fundo_aba_reservadas} !important;
       color:{$cor_do_texto_e_borda_aba_reservadas} !important;
     }

     .label-aba-pagas,
     .label-aba-pagas:hover,
     .cotas-disponiveis-modelo-4 .pcss3t > input:checked + label.label-aba-pagas{
       background: {$cor_de_fundo_aba_pagas} !important;
       border:1px solid {$cor_do_texto_e_borda_aba_pagas} !important;
       color: {$cor_do_texto_e_borda_aba_pagas} !important;
     }
     .modelo-4-comprada{
       background: {$cor_de_fundo_aba_pagas};
       border: 1px solid {$cor_do_texto_e_borda_aba_pagas} !important;
       color: {$cor_do_texto_e_borda_aba_pagas};
     }
     .modelo-4-comprada label{
       background: {$cor_de_fundo_aba_pagas} !important;
       color: {$cor_do_texto_e_borda_aba_pagas} !important;
     }

  ";


            $estilo_cota = "";

            if(get_field("tipo_de_cota",$product_id)=="Cota redonda"):

                $estilo_cota = '

       .form-check-label {
           margin-bottom: 0;
           border-radius: 100% !important;
           height: 53px !important;
       }
       .cotas-disponiveis .form-check {
           border-radius: 100% !important;
       }


   ';

            endif;


            return '

         <input type="hidden" id="idDoProdutoInput" value="'.$product_id.'" />

         <style>

             .cotas-disponiveis .form-check{
               border: 1px solid ' . get_field("cor_do_borda_cota_antes_selecao", $product_id) . ' !important;
             }

             .cotas-disponiveis .form-check input:checked + label{
               background: ' . get_field("cor_de_fundo_selecao", $product_id) . ' !important;
               border: 1px solid ' . get_field("cor_de_fundo_selecao", $product_id) . ';
             }

             #modalRifa .coluna-1 span{
               background: ' . get_field("cor_de_fundo_selecao_modal_rodape", $product_id) . ' !important;
             }

             #modalRifa .coluna-2 h3 a{
               color: ' . get_field("cor_do_texto_btn_finalizar_compra") . '  !important;
               background: ' . get_field("cor_de_fundo_btn_finalizar_compra", $product_id) . ' !important;
           }

             /*.pcss3t > input:checked + label{
                 border-bottom: 2px solid ' . get_field("cor_do_borda_abas_livres_reservadas",$product_id) . ' !important;
           }*/

           '.$estilo_cota.'

           '.$estilo_modelo4.'

          </style>

         <div class="cotas-disponiveis cotas-disponiveis-modelo-4 cotas-disponiveis-modelo-6" id="cotasDisponiveisSelector">
                <!-- ABAS -->
                <div class="page-tabs">
                   <div class="pcss3t pcss3t-height-auto">

                          <input type="radio" name="pcss3t" checked id="tabTodos" class="tab-content-first">
                          <label class="label-aba-todas" for="tabTodos">'. esc_html__( 'TODAS', 'plugin-rifa-drope') .' (<span id="totalTodos">0</span>)</label>

                          <input type="radio" name="pcss3t" id="tab1" class="tab-content-1">
                          <label class="label-aba-livres" for="tab1">'. esc_html__( 'LIVRES', 'plugin-rifa-drope') .' (<span id="totalL">0</span>)</label>

                          <input type="radio" name="pcss3t" id="tab2" class="tab-content-2">
                          <label class="label-aba-reservadas" for="tab2">'. esc_html__( 'RESERVADAS', 'plugin-rifa-drope') .' (<span id="totalR">0</span>)</label>

                          <input type="radio" name="pcss3t" id="tab3" class="tab-content-3">
                          <label class="label-aba-pagas" for="tab3">'. esc_html__( 'PAGAS', 'plugin-rifa-drope') .' (<span id="totalC">0</span>)</label>

                        <ul style="padding-left:0px;margin-left:0px;">

                          <!-- ABA ZERO -->
                          <li class="tab-content aba-modelo4 aba-modelo4-0 tab-content-first" style="padding-left:0px !important;padding-right:0px !important;" id="iteneRifaAba0">
                          </li>
                          <!-- ABA ZERO -->

                          <!-- ABA UM -->
                          <li class="tab-content aba-modelo4 aba-modelo4-1 tab-content-1" style="padding-left:0px !important;padding-right:0px !important;" id="iteneRifaAba1">
                          </li>
                          <!-- ABA UM -->

                          <!-- ABA DOIS -->
                          <li class="tab-content aba-modelo4 aba-modelo4-2 tab-content-2" style="padding-left:0px !important;padding-right:0px !important;" id="iteneRifaAba2">
                          </li>
                          <!-- ABA DOIS -->

                          <!-- ABA TRES -->
                          <li class="tab-content aba-modelo4 aba-modelo4-3 tab-content-3" style="padding-left:0px !important;padding-right:0px !important;"  id="iteneRifaAba3">
                          </li>
                          <!-- ABA TRES -->

                        </ul>

                        <p style="text-align:center;display:block;">
                            <a href="javascript:void(0)" class="btn-carregar-mais-numeros">
                            '. esc_html__( 'Carregar mais números', 'plugin-rifa-drope') .'
                            </a>
                        </p>

                   </div>
                 </div>
                 <!-- ABAS -->
           </div>';
    }

    public function ajaxApiRifaInfos($request): array
    {
        try {
            $rifa = $_GET["rifa"];

            if ($rifa == ""):
                $queryParams = $request->get_query_params();
                $rifa = $queryParams['rifa'];
            endif;

            $numeros = get_field("numero_de_cotas", $rifa);
            $globos = get_field("numero_globos", $rifa);
            $consultas = Raffle::cotas_livres_ou_reservadas($rifa, $globos);

            $reservas = $consultas["reservas"];
            $participantes = $consultas["participantes"];

            $product = wc_get_product($rifa);
            $preco_regular = $product->get_regular_price();
            $preco_promocao = $product->get_sale_price();

            $url_pagamento = wc_get_checkout_url();

            $exibir_nome_do_comprador_ou_texto_padrao = get_field("exibir_nome_do_comprador_ou_texto_padrao", $rifa);
            $texto_tooltip_reserva_presencial = get_field("texto_tooltip_reserva_presencial", $rifa);
            $texto_tooltip_reserva = get_field("texto_tooltip_reserva", $rifa);
            $texto_tooltip_comprado = get_field("texto_tooltip_comprado", $rifa);


            // CORREÇÃO DE NUMEROS LIVRES DO ARRAY
            $a = 0;
            $c = 0;
            $numeros_participantes = array();

            if (!$participantes):
                $participantes = array();
            endif;

            while ($a < count((array)$participantes)):

                $quebra = explode(",", $participantes[$a]["cotas"]);
                $b = 0;
                while ($b < count($quebra)):

                    if ($quebra[$b] != ""): $numeros_participantes[$c] = $quebra[$b];
                        $c++; endif;

                    $b++;
                endwhile;

                $a++;
            endwhile;

            $a = 0;
            $c = 0;
            while ($a < $numeros):

                $novo_numeros[$c] = Raffle::coloque_zero($a, $globos);
                $c++;

                $a++;
            endwhile;
            // FINAL CORREÇÂO DE NUMEROS LIVRES DO ARRAY


            $novo_numeros_livres = array_diff($novo_numeros, $numeros_participantes);
            $novo_numeros_livres = array_values($novo_numeros_livres);

            if ($participantes == ""):
                $participantes = array();
            endif;

            if ($novo_numeros_livres == ""):
                $novo_numeros_livres = array();
            endif;


            echo json_encode( ['sucesso' => "200",
                'rifa' => $rifa,
                'cotas' => $numeros,
                'globos' => get_field("numero_globos", $rifa),
                'max_por_pagina' => get_field("max_por_pagina", $rifa),
                'exibir_nome_do_comprador_ou_texto_padrao' => $exibir_nome_do_comprador_ou_texto_padrao,
                'texto_tooltip_reserva_presencial' => $texto_tooltip_reserva_presencial,
                'texto_tooltip_reserva' => $texto_tooltip_reserva,
                'texto_tooltip_comprado' => $texto_tooltip_comprado,
                'preco_regular' => $preco_regular,
                'preco_promo' => $preco_promocao,
                'url_checkout' => $url_pagamento,
                'livres' => $novo_numeros_livres,
                'reservas' => $reservas,
                'participantes' => $participantes
            ] );
        } catch (Exception $e) {
            echo json_encode( ['sucesso' => "500", 'erro' => $e->getMessage()] );
        }
        wp_die();
    }

    public function addToCart()
    {

    }

    /**
     * @return void
     */
    public function enqueueStyleAndScript( $product_id): void
    {
        wp_enqueue_style('woo-raffle-quotes-open', WOORAFFLES_URL . 'assets/css/quotes-open.css');
        wp_enqueue_script('woo-raffle-quotes-open', WOORAFFLES_URL . 'assets/js/quotes-open.js', ['jquery-core'], false, true);

        wp_localize_script('woo-raffle-quotes-open', 'ajaxobj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'raffle_nonce' => wp_create_nonce('woo-raffle-quotes-open'),
            'productId' => $product_id,
            'action_ajaxApiRifaInfos' => 'ajaxApiRifaInfos',
            'action_ajaxAddToCart' => 'addToCart'
        ]);
    }
}