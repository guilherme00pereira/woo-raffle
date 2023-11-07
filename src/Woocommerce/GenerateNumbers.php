<?php

namespace WooRaffles\Woocommerce;

use UPFlex\MixUp\Core\Base;
use WooRaffles\Admin\Database;
use function Sodium\add;

if (!defined('ABSPATH')) {
    exit;
}

class GenerateNumbers extends Base
{
    public function __construct()
    {
        add_action('woocommerce_order_status_cancelled', [self::class, 'delete']);
        add_action('woocommerce_order_status_on-hold', [self::class, 'delete']);
        add_action('woocommerce_order_status_refunded', [self::class, 'delete']);
        add_action('woocommerce_order_status_processing', [self::class, 'insert']);
        add_action('wp_trash_post', [self::class, 'removeOrdersNumbers'], 10, 1);
    }

    public static function getNumbers($item_id, $product_id, $columns = '*')
    {
        global $wpdb;

        $table_name = Database::$table_name;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT $columns FROM {$wpdb->base_prefix}{$table_name} WHERE order_item_id = %d AND product_id = %d ORDER BY generated_number ASC",
                $item_id,
                $product_id,
            )
        );
    }

    public static function removeOrdersNumbers($order_id)
    {
        global $wpdb;
        $table_name = Database::$table_name;
        $wpdb->delete("{$wpdb->base_prefix}{$table_name}", ['order_id' => $order_id]);
    }

    public static function getNumberRaffle($product_id)
    {
        global $wpdb;

        $table_name = Database::$table_name;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT generated_number, order_id
                        FROM {$wpdb->base_prefix}{$table_name} 
                        WHERE product_id = %d 
                        ORDER BY RAND()
                        LIMIT 1",
                $product_id,
            )
        );
    }

    public static function delete($order_id)
    {
        global $wpdb;

        $table_name = Database::$table_name;

        $order = wc_get_order($order_id);

        if ($order) {
            $wpdb->delete("{$wpdb->base_prefix}{$table_name}", ['order_id' => $order_id]);
        }
    }

    public static function insert($order_id)
    {
        global $wpdb;

        $table_name = Database::$table_name;

        $order = wc_get_order($order_id);

        if ($order) {
            foreach ($order->get_items() as $item_id => $item) {
                if (self::checkExistsInOrder($item_id)) {
                    $product_id = $item->get_product_id();
                    $wpdb->update("{$wpdb->base_prefix}{$table_name}", ['order_item_id' => $item_id], ['order_id' => $order_id, 'product_id' => $product_id]);
                } else {
                    self::insertValuesQuery($order_id, $item, $item_id);
                }
            }
        }

        do_action('woo_raffles_after_numbers_generated', $order_id);
    }

    protected static function checkExistsInOrder($order_item_id): ?string
    {
        global $wpdb;

        $table_name = Database::$table_name;

        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->base_prefix}{$table_name} WHERE order_item_id = %s",
                $order_item_id
            )
        );

    }

    protected static function insertValuesQuery($order_id, $item, $item_id)
    {
        global $wpdb;

        $table_name = Database::$table_name;

        $product_id = $item->get_product_id();

        if (get_post_meta($product_id, '_woo_raffle', true) !== 'yes') {
            return;
        }

        //$wpdb->query('SET session cte_max_recursion_depth=500000;');
        $random_numbers = get_post_meta($product_id, '_woo_raffles_numbers_random', true);

        if ($random_numbers === 'yes') {
            self::generateValuesRandomInsert($order_id, $item_id, $product_id, $item->get_quantity());
        } else {
            try {
                $numbers = wc_get_order_item_meta($item_id, 'Números Escolhidos', true);
            } catch (\Exception $e) {
                $numbers = null;
            }
            if (!is_null($numbers)) {
                $numbers = explode(',', $numbers);
                $numbers = array_map('trim', $numbers);
                foreach ($numbers as $number) {
                    $numbers_query = $wpdb->prepare("INSERT INTO {$wpdb->base_prefix}{$table_name} 
                    (generated_number, order_id, order_item_id, product_id) VALUES 
                    (%s, %s, %s, %s);",
                        $number,
                        $order_id,
                        $item_id,
                        $product_id
                    );
                    $wpdb->query($numbers_query);
                }
            }
        }
    }

    protected static function generateValuesRandomInsert($order_id, $item_id, $product_id, $quantity = 1)
    {
        global $wpdb;

        $checkNumbersAllowed = false;
        $sold_numbers = Database::getSoldQuotes($product_id);
        $table_name = Database::$table_name;
        $product = wc_get_product($product_id);
        $total_numbers = $product->get_stock_quantity('') + $sold_numbers;
        $numbers_sales = range(1, $total_numbers);

        $numbers_query = "INSERT INTO {$wpdb->base_prefix}{$table_name} (generated_number, order_id, order_item_id, product_id) VALUES ";
        $numbers_result = $wpdb->get_col("SELECT generated_number FROM {$wpdb->base_prefix}{$table_name} WHERE product_id = {$product_id} ORDER BY generated_number ASC");
        $numbers_allowed = array_diff($numbers_sales, $numbers_result);

        if ($numbers_allowed) {
            shuffle($numbers_allowed);
            $numbers_allowed = array_slice($numbers_allowed, 0, $quantity);

            foreach ($numbers_allowed as $number_allowed) {
                $numbers_query .= $wpdb->prepare(
                    "(%d, %d, %d, %d),",
                    $number_allowed,
                    $order_id,
                    $item_id,
                    $product_id
                );
            }

            $checkNumbersAllowed = true;
        }

        if ($checkNumbersAllowed) {
            $wpdb->query(rtrim($numbers_query, ',') . ';');
        }
    }


}