<?php

namespace WooRaffles\Front;

use WooRaffles\Admin\Database;

if (!defined('ABSPATH')) {
    exit;
}

class ProgressBarShortcode extends Template
{

    public function __construct()
    {
        parent::__construct();

        add_shortcode('meta_rifa_v2', [$this, 'content']);

    }

    public function content($atts): string
    {
        wp_enqueue_style('woo-raffle-progress-bar', WOORAFFLES_URL . 'assets/css/progress-bar.css');

        if ($atts == "" or $atts == null) {
            $rifa = "";
        } else {
            $rifa = $atts['rifa'];
        }

        $cotas = get_field("numero_de_cotas", $rifa);

        $pedidos = Database::getOrdersIdsByProductId($rifa);

        $a = 0;
        $b = 0;
        $array_cotas = array();


        while ($a < count($pedidos)):

            $order = wc_get_order($pedidos[$a]);

            $array_cotas[$b]["status"] = $order->get_status();

            if ($array_cotas[$b]["status"] == "completed" || $array_cotas[$b]["status"] == "processing"):

                // OBTER OS GLOBAIS
                if (get_post_meta($order->get_order_number(), 'billing_cotasescolhidas', true)):

                    $array_cotas[$b]["cotas_escolhidas"] = get_post_meta($order->get_order_number(), 'billing_cotasescolhidas', true);

                endif;

                // OBTER DA VERSÃO 3
                // PERCORRER TODOS OS ITENS COMPRADOS
                foreach ($order->get_items() as $item_key => $item):

                    $product_id = $item->get_product_id();

                    if ($item->get_meta('billing_cotasescolhidas') && $product_id == $rifa):

                        $numeros_escolhidos = $item->get_meta('billing_cotasescolhidas');

                        $array_cotas[$b]["cotas_escolhidas"] = $array_cotas[$b]["cotas_escolhidas"] . $numeros_escolhidos;

                    endif;

                endforeach;

                $b++;

            endif;

            $a++;
        endwhile;

        $a = 0;
        $super = array();
        $total_r = 0;
        $c = 0;
        while ($a < count($array_cotas)):

            $temp = explode(",", $array_cotas[$a]["cotas_escolhidas"]);
            //$total_r = $total_r + count($temp);

            $b = 0;
            while ($b < count($temp)):

                if ($temp[$b] != "") $super[$c] = $temp[$b];

                $b++;
                $c++;

            endwhile;

            $a++;
        endwhile;

        $total_r = count(array_unique($super));

        if ($total_r == 0) {
            return '

          <div class="rifa-barra-de-progresso" data-prova-real="0" data-prova-two="' . count($array_cotas) . '">
              <div class="quantidade-ate-meta" style="width:0% !important">&nbsp;</div>
          </div>
          <p class="descricao-meta-rifa">
            0' . '% ' . esc_html__('da meta alcançada', 'plugin-rifa-drope') . '
          </p>

        ';
        }

        $x = $total_r / $cotas;
        $y = $x * 100;
        //$super = array_unique($super);
        //print_r($super);
        return '

        <div class="rifa-barra-de-progresso" data-prova-real="' . $total_r . '" data-prova-two="' . count($array_cotas) . '">
            <div class="quantidade-ate-meta" style="width:' . $y . '% !important">&nbsp;</div>
        </div>
        <p class="descricao-meta-rifa">
          ' . number_format($y, 2, ".", ",") . '% ' . esc_html__('da meta alcançada', 'plugin-rifa-drope') . '
        </p>

      ';
    }
}