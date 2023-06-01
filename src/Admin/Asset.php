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
        wp_enqueue_style('woo_raffles', WOORAFFLES_URL . 'assets/admin.css');

        wp_enqueue_script('jquery-repeater', WOORAFFLES_URL . 'assets/jquery.repeater.min.js', ['jquery-core']);
        wp_enqueue_script('woo_raffles', WOORAFFLES_URL . 'assets/admin.js', ['jquery-core', 'jquery-repeater']);
    }
}
