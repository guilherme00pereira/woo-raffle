<?php

namespace WooRaffles\Front;

use UPFlex\MixUp\Core\Base;

if (!defined('ABSPATH')) {
    exit;
}

class ProgressiveDiscountShortcode extends Base
{
    public function __construct()
    {
        add_shortcode('woo-raffles-descontos_progressivos', [self::class, 'shortcodeContent']);
    }

    public static function shortcodeContent($attrs): string
    {
        $html = '';

        extract(shortcode_atts(array(
            'id' => '',
            'exibir' => [],
        ), $attrs));

        $product_id = $attrs['id'] ?? get_the_ID();
        $exibir = $attrs['exibir'] ?? '';
        $destacar = $attrs['destacar'] ?? '';
        if(!empty($exibir)) $exibir = explode(',', $exibir);
        

        if (have_rows('desconto_progressivo', $product_id)):
            
                $faixas = get_field('layout_faixas', $product_id);

                if ($faixas == '' || $faixas == 'Layout 1') $layout_faixa = "";
                if ($faixas == 'Layout 2') $layout_faixa = 'layout-2';

                $html = '	    	   
                <div class="caixa-descontos-progressivo caixa-descontos-progressivo-checkout ' . $layout_faixa . '">
                        <div id="woo_raffles_discount_notice">
                            <p class="hidden"></p>
                        </div>
        
                        <main class="itens">
        
                            <section class="itens__container">
        
                                <section class="itens__container__grid">
                                
                                <input type="hidden" id="woo_raffles_product_id" value="' . $product_id . '" />
        
                ';

                $a = 0;
                $y = 0;

                while (have_rows('desconto_progressivo', $product_id)) : the_row();
                    
                    $row_index = get_row_index();
                    if($exibir == '' || in_array(($row_index), $exibir)):
                    
                    $saida = "números";

                    if (get_sub_field('quantidade') == 1) $saida = "número";

                    //$destacar_essa_opcao = get_sub_field('destacar_essa_opcao');
                    $classe_destaque = "";
                    $html_destaque = "";

                    if ((int)$destacar == ($row_index)):
                        $classe_destaque = 'destacar_essa_opcao';
                        $html_destaque = '<span class="estoudestacado">MELHOR OPÇÃO</span>';
                    endif;

                    $html = $html . '
                                    <label class="itens__container__grid__item ' . $classe_destaque . '" for="woo_raffles_discount_qty' . $a . '"> ' . $html_destaque . '
                                        <input id="woo_raffles_discount_qty' . $a . '"  name="woo_raffles_discount_qty" type="radio" data-field="' . $row_index . '" value="' . get_sub_field('quantidade') . '" ' . ((int)$destacar == ($row_index) ? "checked=\"\"" : "") . '>
                                        <section>
                                            <div>
                                                <h2>' . get_sub_field('quantidade') . ' ' . $saida . '</h2>
                                                <h4>' . get_sub_field('titulo_opcao') . '</h4>
                                            </div>
                                            <div style="align-items: center;">
                                                <small>' . get_sub_field('subtitulo_opcao') . '</small>
                                                <h3>' . get_sub_field('valor_mkt') . '</h3>
                                            </div>
                                        </section>
                                    </label>		

                            ';
                    $a++;
                    $y++;
                    endif; // End if check ir rule is in_array "Exibir"

                endwhile;

                if ($faixas == 'Layout 2') $html = $html . '<br clear="both">';

                $html .= '
                            </section>

                            <p class="caixa-link-action">
                            <a href="#" id="woo_raffles_discount_submit" class="desconto-progressivo-link-action">
                                Participar
                            </a>
                            </p>

                        </section>

                    </main>

                </div>
                ';

        endif;

        return $html;
    }
}