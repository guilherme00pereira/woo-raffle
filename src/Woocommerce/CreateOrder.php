<?php

namespace WooRaffles\Woocommerce;

use UPFlex\MixUp\Core\Base;
use WooRaffles\Admin\Database;

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

class CreateOrder extends Base
{
    protected static array $numbers = [];

    public function __construct()
    {
        add_action('woocommerce_checkout_update_order_meta', [self::class, 'saveDataOrder'], 10, 2);
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

        $product_id = $item['product_id'] ?? 0;
        self::$numbers[$product_id] = $values[$key_meta_data];
    }

    public static function saveDataOrder($order_id)
    {
        $order = wc_get_order($order_id);
        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            if (self::$numbers[$product_id]) {
                self::generateValuesRandomInsert($order_id, 0, $product_id, self::$numbers[$product_id]);
            }
        }
    }

    protected static function generateValuesRandomInsert($order_id, $item_id, $product_id, $numbers_selected)
    {
        global $wpdb;

        $checkNumbersAllowed = false;
        $sold_numbers = Database::getSoldQuotes($product_id);
        $table_name = Database::$table_name;
        $product = wc_get_product($product_id);
        $total_numbers = $product->get_stock_quantity('') + $sold_numbers;
        $raffle_random_quote = get_post_meta($product_id, '_woo_raffles_numbers_random', true);

        $numbers_query = "INSERT INTO {$wpdb->base_prefix}{$table_name} (generated_number, order_id, order_item_id, product_id) VALUES ";
        $numbers_result = $wpdb->get_col("SELECT generated_number FROM {$wpdb->base_prefix}{$table_name} WHERE product_id = {$product_id} ORDER BY generated_number ASC");

        foreach ($numbers_selected as $number_selected) {

            if ($raffle_random_quote === 'no'):

                $numbers_query .= $wpdb->prepare(
                    "(%d, %d, %d, %d),",
                    $number_selected,
                    $order_id,
                    $item_id,
                    $product_id
                );

                $numbers_result[] = $number_selected;
                $checkNumbersAllowed = true;

            else:
                $numbers_sales = range(0, $total_numbers);

                $numbers_allowed = array_diff($numbers_sales, $numbers_result);

                if ($numbers_allowed) {
                    $number_rand = array_rand($numbers_allowed, 1);
                    $number_decided = in_array($number_selected, $numbers_allowed) ? $number_selected : $numbers_allowed[$number_rand];

                    $numbers_query .= $wpdb->prepare(
                        "(%d, %d, %d, %d),",
                        $number_decided,
                        $order_id,
                        $item_id,
                        $product_id
                    );

                    $numbers_result[] = $number_decided;
                    $checkNumbersAllowed = true;
                }
            endif;
        }

        if ($checkNumbersAllowed) {
            $wpdb->query(rtrim($numbers_query, ',') . ';');
        }
    }
}
