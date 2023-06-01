<?php

namespace WooRaffles\Front;

use UPFlex\MixUp\Core\Base;

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

class Asset extends Base
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [self::class, 'script'], 11);
    }

    public static function script()
    {
        wp_dequeue_style('font-awesome');
        wp_deregister_style('font-awesome');

        wp_enqueue_style('bootstrap', WOORAFFLES_URL . 'assets/bootstrap-grid.css');
        wp_enqueue_style('discount-progressive', WOORAFFLES_URL . 'assets/discount_progressive.css');
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css');
        wp_enqueue_style('simplepagination', WOORAFFLES_URL . 'assets/simplePagination.css');
        wp_enqueue_style('woo_raffles', WOORAFFLES_URL . 'assets/front.css');

        wp_enqueue_script('jquery-pagination', WOORAFFLES_URL . 'assets/pagination.min.js', ['jquery-core'], '2.5.0', true);
        wp_enqueue_script('jquery-mask', WOORAFFLES_URL . 'assets/jquery.mask.min.js', ['jquery-core'], '1.14.16', true);
        wp_enqueue_script('woo_raffles', WOORAFFLES_URL . 'assets/front.js', ['jquery-core', 'jquery-mask', 'jquery-pagination'], '1.0.1', true);
    }
}
