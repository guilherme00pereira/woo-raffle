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

        add_shortcode('woo-raffles-cotas_abertas', [$this, 'content']);
        add_shortcode('woo-raffles-cotas_abertas_v2', [$this, 'content_v2']);
        add_action('wp_ajax_ajaxApiRifaInfos', [$this, 'ajaxApiRifaInfos']);
        add_action('wp_ajax_nopriv_ajaxApiRifaInfos', [$this, 'ajaxApiRifaInfos']);
    }

    #region oldcode

    public function content($attrs)
    {
        $this->enqueueStyleAndScript();

        extract(shortcode_atts(array(
            'id' => 0,
        ), $attrs));

        ob_start();

        $product_id = $attrs['id'] ?? '';
        $product = wc_get_product($product_id);
        $globos = get_field("numero_globos", $product_id);
        $numeros = get_field("numero_de_cotas", $product_id);
        $limit = get_field("max_por_pagina", $product_id);
        $all_numbers = range(0, $numeros);
        $numbers_payed =  Database::getPayedNumbers($product_id);
        $numbers_reserved = Database::getReserverdNumbers($product_id);
        $numbers_selected = [];
        $cart = \WC()->cart;
        
        if ($cart) {
            foreach ($cart->get_cart() as $cart_item) {
                if ((int)$product_id === $cart_item['product_id']) {
                    $cart->remove_cart_item($cart_item['key']);
                }
            }
        }

        self::getPart('quotes', 'open', [
                'all_numbers' => $all_numbers,
                'globos' => $globos,
                'limit' => $limit,
                'numbers_payed' => count($numbers_payed) > 0 ? $numbers_payed : [],
                'numbers_reserved' => count($numbers_reserved) > 0 ? explode(',', $numbers_reserved[0]): [],
                'numbers_selected' => $numbers_selected,
                'product_id' => $product_id,
                'style_shortcode' => $this->getShorcodeStyles($product_id),
                'price' => $product->get_price(),
            ]
        );

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * @return void
     */
    public function enqueueStyleAndScript(): void
    {
        wp_enqueue_style('woo-raffle-quotes-open', WOORAFFLES_URL . 'assets/css/quotes-open.css');
    }

    

    private function getShorcodeStyles($product_id): array
    {
        $cores_modelos_todas = get_field("cores_modelos_todas", $product_id);
        $cor_de_fundo_aba_todas = $cores_modelos_todas["cor_de_fundo_aba_todas"];
        $cor_do_texto_e_borda_aba_todas = $cores_modelos_todas["cor_do_texto_e_borda_aba_todas"];

        $cores_modelos_livres = get_field("cores_modelos_livres", $product_id);
        $cor_de_fundo_aba_livres = $cores_modelos_livres["cor_de_fundo_aba_livres"];
        $cor_do_texto_e_borda_aba_livres = $cores_modelos_livres["cor_do_texto_e_borda_aba_livres"];

        $cores_modelos_reservadas = get_field("cores_modelos_reservadas", $product_id);
        $cor_de_fundo_aba_reservadas = $cores_modelos_reservadas["cor_de_fundo_aba_reservadas"];
        $cor_do_texto_e_borda_aba_reservadas = $cores_modelos_reservadas["cor_do_texto_e_borda_aba_reservadas"];

        $cores_modelos_pagas = get_field("cores_modelos_pagas", $product_id);
        $cor_de_fundo_aba_pagas = $cores_modelos_pagas["cor_de_fundo_aba_pagas"];
        $cor_do_texto_e_borda_aba_pagas = $cores_modelos_pagas["cor_do_texto_e_borda_aba_pagas"];

        $cor_fundo_botao_finalizar_compra = get_field("cor_de_fundo_btn_finalizar_compra", $product_id);
        $cor_texto_botao_finalizar_compra = get_field("cor_do_texto_btn_finalizar_compra", $product_id);

        $cor_fundo_botao_carregar_mais_numeros = get_field("cor_de_fundo_btn_carregar_mais_numeros", $product_id);
        $cor_texto_botao_carregar_mais_numeros = get_field("cor_do_texto_btn_carregar_mais_numeros", $product_id);

        $cor_fundo_btn_selected = get_field("cor_de_fundo_selecao", $product_id);
        $cor_texto_btn_selected = get_field("cor_do_texto_selecao", $product_id);

        return [
            'btn_selected' => "background-color: {$cor_fundo_btn_selected} !important; border: 1px solid {$cor_texto_btn_selected} !important; color: {$cor_texto_btn_selected} !important;",
            'aba_todas' => "background-color: {$cor_de_fundo_aba_todas} ; border:1px solid {$cor_do_texto_e_borda_aba_todas} ; color: {$cor_do_texto_e_borda_aba_todas} ;",
            'aba_livres' => "background-color: {$cor_de_fundo_aba_livres} ; border:1px solid {$cor_do_texto_e_borda_aba_livres} ; color: {$cor_do_texto_e_borda_aba_livres} ;",
            'aba_reservadas' => "background-color: {$cor_de_fundo_aba_reservadas} ; border:1px solid {$cor_do_texto_e_borda_aba_reservadas} ; color: {$cor_do_texto_e_borda_aba_reservadas} ;",
            'aba_pagas' => "background-color: {$cor_de_fundo_aba_pagas} ; border:1px solid {$cor_do_texto_e_borda_aba_pagas} ; color: {$cor_do_texto_e_borda_aba_pagas} ;",
            'btn_finalizar_compra' => "background-color: {$cor_fundo_botao_finalizar_compra} ; border: none !important; color: {$cor_texto_botao_finalizar_compra} ",
            'btn_carregar_mais_numeros' => "background-color: {$cor_fundo_botao_carregar_mais_numeros}; border: none !important; color: {$cor_texto_botao_carregar_mais_numeros}",
        ];
    }
}