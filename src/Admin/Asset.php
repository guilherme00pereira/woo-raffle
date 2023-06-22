<?php

namespace WooRaffles\Admin;

use UPFlex\MixUp\Core\Base;

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

class Asset extends Base
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [self::class, 'script']);
    }

    public static function script()
    {
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css');
        wp_enqueue_style('bootstrap', WOORAFFLES_URL . 'assets/bootstrap-grid.css');
        wp_enqueue_style('woo_raffles', WOORAFFLES_URL . 'assets/css/admin.css');

        wp_register_style('woo-raffle-progress-bar', WOORAFFLES_URL . 'assets/css/progress-bar.css');
        wp_register_style('woo-raffle-quotes-open', WOORAFFLES_URL . 'assets/css/quotes-open.css');
        wp_register_style('woo-raffle-multi-dropdown-style', WOORAFFLES_URL . 'assets/js/jquery.sumoselect/sumoselect.min.css');

        wp_enqueue_script('jquery-repeater', WOORAFFLES_URL . 'assets/jquery.repeater.min.js', ['jquery-core']);
        wp_enqueue_script('woo_raffles', WOORAFFLES_URL . 'assets/js/admin.js', ['jquery-core', 'jquery-repeater']);

        wp_register_script('woo-raffle-quotes-open', WOORAFFLES_URL . 'assets/js/quotes-open.js', ['jquery-core']);
        wp_register_script('woo-raffle-admin-page', WOORAFFLES_URL . 'assets/js/admin-page.js', ['jquery-core']);
        wp_register_script('woo-raffle-multi-dropdown', WOORAFFLES_URL . 'assets/js/jquery.sumoselect/jquery.sumoselect.min.js', ['jquery-core']);
        wp_register_script('woo-raffle-errors-numbers', WOORAFFLES_URL . 'assets/js/errors-numbers.js', ['jquery-core']);
    }
}
