<?php

namespace WooRaffles\Woocommerce;

use UPFlex\MixUp\Core\Base;

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

class AddToCart extends Base
{
    public function __construct()
    {
        add_action('wp_ajax_nopriv_woo_numbers_selected', [self::class, 'getNumbers']);
        add_action('wp_ajax_woo_numbers_selected', [self::class, 'getNumbers']);

        add_filter('woocommerce_cart_item_quantity', [self::class, 'changeQuantity'], 10, 3);
    }

    public static function getNumbers()
    {
        $error = true;
        $key = '';
        $key_meta_data = 'woo_raffles_numbers';
        $msg = __('O produto não possui estoque suficiente.', 'woo-raffles');
        $redirect = null;

        $product_id = sanitize_text_field($_POST['product_id'] ?? '');
        $numbers = sanitize_text_field($_POST['numbers'] ?? '');
        $numbers_selected = explode(',', $numbers);
        $qty = count($numbers_selected);

        $product = wc_get_product($product_id);

        if ((int)$product_id > 0 && ($qty <= $product->get_stock_quantity())) {
            $cart = \WC()->cart;
            $is_removed = self::removeItemInCart($product_id, $cart);

            try {
                $key = $cart->add_to_cart(
                    $product_id,
                    $qty,
                    0,
                    [],
                    [$key_meta_data => $numbers_selected]
                );
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }

            if ($key) {
                $msg = $is_removed
                    ? _n('Número atualizado com sucesso.', 'Números atualizados com sucesso.', $qty, 'woo-raffles')
                    : _n('Número adicionado com sucesso.', 'Números adicionados com sucesso.', $qty, 'woo-raffles');
                $error = false;
                $redirect = wc_get_checkout_url();
            }
        }

        echo json_encode(['error' => $error, 'msg' => $msg, 'redirect' => $redirect]);
        exit;
    }

    public function changeQuantity($product_quantity, $cart_item_key, $cart_item): int
    {
        $numbers = $cart_item['woo_raffles_numbers'] ?? [];

        if ($numbers) {
            return count($numbers);
        }

        return $product_quantity;
    }

    protected static function removeItemInCart($product_id, $cart): bool
    {
        if ($cart) {
            foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
                if ((int)$product_id === $cart_item['product_id']) {
                    $cart->remove_cart_item($cart_item_key);

                    return true;
                }
            }
        }

        return false;
    }

    public function addToCart() {
  
        $produto_id = intval( $_POST['id'] );
        $quantidade = intval( $_POST['qtd'] );
        
        global $woocommerce;
        
        $num_max_cotas_por_cliente = get_field("num_maximo_reservas",$produto_id);
        $num_mix_cotas_por_cliente = get_field("num_minimo_reservas",$produto_id);
  
        $msg_num_maximo_reservas = get_field("msg_num_maximo_reservas",$produto_id);
        $msg_num_minimo_reservas = get_field("msg_num_minimo_reservas",$produto_id);
  
        $msg_explicativa_num_maximo = get_field("msg_explicativa_num_maximo",$produto_id);
        $msg_explicativa_num_minimo = get_field("msg_explicativa_num_minimo",$produto_id);
        
        $texto_btn_finalizar_compra = get_field("texto_btn_finalizar_compra",$produto_id);
        $currency = get_woocommerce_currency_symbol();
  
  
        $aprovacao = "0";
        $erros     = "0";
  
        if($quantidade <= $num_max_cotas_por_cliente || 
           $num_max_cotas_por_cliente == 0 || 
           $num_max_cotas_por_cliente == NULL):
  
          $aprovacao = "1";
  
        else:
  
          $erros = "XXX";
  
        endif;
  
        if($quantidade >= $num_mix_cotas_por_cliente || 
           $num_mix_cotas_por_cliente == 0 || 
           $num_mix_cotas_por_cliente == NULL):
  
          $aprovacao = "1";
  
        else:
  
          $erros = "YYY";
  
        endif;
  
        $limpar_carrinho = get_field("limpar_carrinho",$produto_id);
  
              if($limpar_carrinho=="Sim"):
                global $woocommerce;
                $woocommerce->cart->empty_cart(); 
              endif;
  
        if($aprovacao=="1" && $erros == "0"):
  
            // PRIMEIRO LIMPAMOS O CARRINHO
            //$woocommerce->cart->empty_cart();
  
            // DEPOIS ADICIONADOS O PRODUTO E A QUANTIDADE NO CARRINHO
            //$woocommerce->cart->add_to_cart($produto_id,$quantidade);
  
            // RECUPERAR ALGUMAS INFORMAÇOES SOBRE O PRODUTO
            $product = wc_get_product( $produto_id );
            $valor_produto = $product->get_regular_price();
            $promocao_produto = $product->get_sale_price();
  
            if($promocao_produto):
              $valor_produto = $promocao_produto;
            endif;
                            
            // DEPOIS RETORNAMOS O TOTAL DO CARRINHO
            $data = array('sucesso' => "200",
                          'valor' => $valor_produto, 
                          'valor_no_carrinho' => WC()->cart->total, 
                          'texto_btn_finalizar_compra' => $texto_btn_finalizar_compra, 
                          'currency' => $currency);
  
            $json_string = json_encode($data, JSON_PRETTY_PRINT);
       
            echo $json_string; 
            
        else:
  
          $data = array('sucesso' => "400", 'erros' => $erros, 'texto_btn_finalizar_compra' => $texto_btn_finalizar_compra, 'currency' => $currency, 'msg_num_maximo_reservas' => $msg_num_maximo_reservas, 'msg_num_minimo_reservas' => $msg_num_minimo_reservas, 'msg_explicativa_num_maximo' => $msg_explicativa_num_maximo, 'msg_explicativa_num_minimo' => $msg_explicativa_num_minimo);
  
          $json_string = json_encode($data, JSON_PRETTY_PRINT);
       
          echo $json_string;
  
        endif;
  
        wp_die();
  
    }
}
