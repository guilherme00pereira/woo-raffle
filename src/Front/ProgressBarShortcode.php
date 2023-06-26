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

        $barra = get_field("cor_de_fundo_barra_progresso", $rifa);
        $fonte = get_field("cor_do_texto_barra_progresso", $rifa);

        $cotas = get_field("numero_de_cotas", $rifa);
        $soldNumbers = Database::getSoldQuotes($rifa);
        $percentage = $soldNumbers / $cotas * 100;

        return '
        <div class="rifa-barra-de-progresso" data-prova-real="' . $percentage . '">
            <div class="quantidade-ate-meta" style="width:' . $percentage . '% !important; background-color: '. $barra . ';">&nbsp;</div>
        </div>
        <p class="descricao-meta-rifa" style="color: ' . $fonte . ';">
          ' . number_format($percentage, 2, ".", ",") . '% ' . esc_html__('da meta alcançada', 'plugin-rifa-drope') . '
        </p>

      ';
    }
}