<?php

namespace WooRaffles\Admin;

use UPFlex\MixUp\Core\Base;
use WC_Order;

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

class Stock extends Base
{
    public function __construct()
    {
        add_action('woocommerce_product_set_stock', [self::class, 'change']);
        add_action('save_post_product', [self::class, 'updateStock'], 10, 3);
        add_filter('woocommerce_ajax_add_order_item_validation', [self::class, 'checkAdminOutOfStockOrderAttempt'], 10, 4);
        add_action( 'woocommerce_order_status_processing_to_cancelled', array( $this, 'restoreOrderStock'), 10, 1 );
        add_action( 'woocommerce_order_status_completed_to_cancelled', array( $this, 'restoreOrderStock'), 10, 1 );
        add_action( 'woocommerce_order_status_on-hold_to_cancelled', array( $this, 'restoreOrderStock'), 10, 1 );
        add_action( 'woocommerce_order_status_processing_to_refunded', array( $this, 'restoreOrderStock'), 10, 1 );
        add_action( 'woocommerce_order_status_completed_to_refunded', array( $this, 'restoreOrderStock'), 10, 1 );
        add_action( 'woocommerce_order_status_on-hold_to_refunded', array( $this, 'restoreOrderStock'), 10, 1 );

    }

    public static function updateStock($post_id, $post, $update)
    {
        $product = wc_get_product($post_id);
        if($product)
        {
            $max_stock = get_post_meta($post_id, 'numero_de_cotas', true);
            update_post_meta($post_id, '_woo_raffles_max_stock', $max_stock);
            $product->set_stock_quantity($max_stock);
            $product->save();
        }
    }

    public static function change($product)
    {
        $max_stock = get_post_meta($product->get_id(), '_woo_raffles_max_stock', true);

        //$qty = $product->get_stock_quantity() + $product->get_total_sales();
        $total_sales = Database::getSoldQuotes($product->get_id());
        $qty = $product->get_stock_quantity() + $total_sales;

        if ($qty > intval($max_stock) && intval($max_stock) > 0) {
            $product->set_stock_quantity($max_stock - $total_sales);
            $product->save();
        }
    }

    public static function checkAdminOutOfStockOrderAttempt($validation_error, $product, $order, $qty)
    {
        if ( $validation_error && !$product->is_in_stock() ) {
            $validation_error->add( 'product-out-of-stock', __('Product Out of Stock', 'woocommerce') );
        }
        if ( $validation_error && ( $product->get_stock_quantity() < $qty ) ) {
            $validation_error->add( 'product-low-stock', __('Product low of Stock', 'woocommerce') );
        }
        return $validation_error;
    }

    public function restoreOrderStock($order_id ) {
        $order = new WC_Order( $order_id );
        if ( ! get_option('woocommerce_manage_stock') == 'yes' && ! sizeof( $order->get_items() ) > 0 ) {
            return;
        }
        foreach ( $order->get_items() as $item ) {
            if ( $item['product_id'] > 0 ) {
                $_product = $order->get_product_from_item( $item );
                if ( $_product && $_product->exists() && $_product->managing_stock() ) {
                    $old_stock = $_product->stock;
                    $qty = apply_filters( 'woocommerce_order_item_quantity', $item['qty'], $this, $item );
                    $new_quantity = $_product->increase_stock( $qty );
                    do_action( 'woocommerce_auto_stock_restored', $_product, $item );
                    $order->add_order_note( sprintf( __( 'Item #%s stock incremented from %s to %s.', 'woocommerce' ), $item['product_id'], $old_stock, $new_quantity) );
                    $order->send_stock_notifications( $_product, $new_quantity, $item['qty'] );
                }
            }
        }
    }
}
