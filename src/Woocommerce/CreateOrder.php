<?php

namespace WooRaffles\Woocommerce;

use UPFlex\MixUp\Core\Base;
use WooRaffles\Admin\Database;

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

class CreateOrder extends Base
{

    public function __construct()
    {
        
        add_action('woocommerce_checkout_create_order_line_item', [self::class, 'addOpenNumbers'], 10, 4);
    }

    public static function addOpenNumbers($item, $cart_item_key, $values, $order)
    {
        $key_meta_data = 'woo_raffles_numbers';

        if (empty($values[$key_meta_data])) {
            return;
        }

        $item->add_meta_data(
            __('Números Escolhidos', 'woo-raffles'),
            implode(',', $values[$key_meta_data])
        );
    }
    
}
