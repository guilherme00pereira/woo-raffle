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
}
