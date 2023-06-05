<?php

namespace WooRaffles\Admin;

use UPFlex\MixUp\Core\Base;
use WooRaffles\Woocommerce\GenerateNumbers;

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

class Raffle extends Base
{
    public function __construct()
    {
        add_action('wp_ajax_woo_drawn_number', [self::class, 'getNumber']);
    }

    public static function getNumber()
    {
        $msg = __('Você não possui a permissão necessária! Utilize um usuário válido.', 'woo-raffles');

        if (wp_doing_ajax()) {
            $product_id = sanitize_text_field($_POST['product_id'] ?? '');

            if ($product_id) {
                $str_pad_left = get_post_meta($product_id, '_woo_raffles_str_pad_left', true) ?? 5;

                $number = GenerateNumbers::getNumberRaffle($product_id);

                $msg = $number
                    ? __(
                        sprintf('O número sorteado foi: %d <a href="%s" target="_blank">[Pedido %d]</a>.',
                            str_pad($number->generated_number, $str_pad_left, '0'),
                            admin_url("/post.php?post={$number->order_id}&action=edit"),
                            $number->order_id
                        ),
                        'woo-raffles'
                    )
                    : __('Nenhum número localizado', 'woo-raffles');

                if ($number) {
                    update_post_meta($product_id, '_woo_raffles_raffled_number', $number->generated_number, $number->generated_number);
                    update_post_meta($product_id, '_woo_raffles_raffled_order', $number->order_id, $number->order_id);
                }
            }
        }

        echo json_encode(['msg' => $msg]);
        exit;
    }

    public static function coloque_zero($input0, $globos): string
    {
        return str_pad($input0, $globos, "0", STR_PAD_LEFT);
    }


    public static function cotas_livres_ou_reservadas($produto_id, $globos): array
    {
        $pedidos = Database::getOrdersIdsByProductId($produto_id);
        $cotas = [];
        $participantes = [];
        $a = 0;
        $listaTodosOsNumeros = [];
        $novo_array_list = [];
        $array_final = [];

        // MONTAR O ARRAY COM NUMEROS DA RIFA (GERAL)
        $numero_de_cotas = get_field("numero_de_cotas",$produto_id);

        while($a<$numero_de_cotas):

            $listaTodosOsNumeros[$a] = self::coloque_zero($a,$globos);

            $a++;
        endwhile;

        $listaTodosOsNumerosZerado = $listaTodosOsNumeros;

        $a = 0;
        $b = 0;

        // MONTAR O ARRAY COM OS NUMEROS INDISPONIVEIS
        while($a<count($pedidos)):

            // VERSAO 2
            if(get_post_meta( $pedidos[$a], 'billing_cotasescolhidas', true )!=""):

                $temp = get_post_meta( $pedidos[$a], 'billing_cotasescolhidas', true );

                $participantes[$a]["cotas"] = $temp;

                $temp2 = explode(",",$temp);

                $c = 0;
                while($c<count($temp2)):

                    if($temp2[$c]!=""):

                        $cotas[$b] = $temp2[$c];

                        $b++;

                    endif;

                    $c++;
                endwhile;

            endif;
            // FINAL VERSO 2

            // VERSAO 3
            $order = wc_get_order($pedidos[$a]);
            $prova = "";


            // PERCORRER TODOS OS ITENS COMPRADOS
            foreach ($order->get_items() as $item_key => $item ):

                $product_id   = $item->get_product_id();

                // APPEND ITENS DA VERSAO 3
                if($item->get_meta('billing_cotasescolhidas') && $product_id==$produto_id):

                    $numeros_escolhidos = $item->get_meta('billing_cotasescolhidas');
                    $prova = $prova . $numeros_escolhidos . " ### ";

                    $participantes[$a]["cotas"] = $numeros_escolhidos;

                    $temp2 = explode(",",$numeros_escolhidos);

                    $c = 0;

                    $cotas = [];

                    while($c<count($temp2)):

                        if($temp2[$c]!="" && !in_array($temp2[$c], $cotas)):

                            $cotas[$b] = $temp2[$c];
                            $b++;

                        endif;

                        $c++;
                    endwhile;


                endif;

            endforeach;
            // FINAL VERSAO 3

            $status_pedido = $order->get_status();
            $order_data = $order->get_data();

            $order_billing_first_name = $order_data['billing']['first_name'];
            $order_billing_last_name = $order_data['billing']['last_name'];
            $order_billing_email = $order_data['billing']['email'];
            $order_billing_phone = $order_data['billing']['phone'];

            $participantes[$a]["status"] = $status_pedido;
            $participantes[$a]["nome"] = $order_billing_first_name;
            $participantes[$a]["sobrenome"] = $order_billing_last_name;
            $participantes[$a]["email"] = $order_billing_email;
            $participantes[$a]["phone"] = $order_billing_phone;

            $a++;

        endwhile;

        if(!$cotas):
            $cotas = array();
        endif;

        // FILTRAR
        if(count($cotas)>0) $novo_array_list = array_diff($listaTodosOsNumeros, $cotas);
        if(count($cotas)==0) $novo_array_list = $listaTodosOsNumeros;
        // $novo_array_list = lista de livres
        // $cotas = lista dos ocupados

        $a = 0;
        $b = 0;

        // O FILTRO VAI FUNCIONAR TANTO PARA V2 COMO PARA V3 (INCRIVEL NÃO?)
        while($a<count($novo_array_list)):

            if(array_key_exists($a,$novo_array_list)){

                $array_final[$b] = $novo_array_list[$a];
                $b++;

            }

            $a++;
        endwhile;

        if($cotas==""):
            $cotas = array();
        endif;

        if($array_final==""):
            $array_final = $listaTodosOsNumerosZerado;
        endif;


        $livres = array_diff($array_final, $cotas);
        $livres_n = array_values($livres);

        return array('sucesso'  => "200",
            'reservas' => $cotas,
            'livres'   => $livres_n,
            'participantes' => $participantes,
            'prova'    => 0);

    }
}
