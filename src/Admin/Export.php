<?php

namespace WooRaffles\Admin;

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

class Export extends Template
{
    public function __construct()
    {
        parent::__construct();

        add_action('admin_menu', [self::class, 'createRoutes']);
    }

    public static function createRoutes()
    {
        add_submenu_page(
            'options-writing.php',
            '',
            '',
            'manage_options',
            'woo-raffles-export',
            [self::class, 'execute'],
        );
        add_submenu_page(
            'options-writing.php',
            '',
            '',
            'manage_options',
            'woo-raffles-export-rpd',
            [self::class, 'executeQuickie'],
        );
    }

    public static function execute()
    {
        $product_id = sanitize_text_field($_GET['post'] ?? '');
        $file_type = sanitize_text_field($_GET['file_type'] ?? 'csv');
        do_action('woo_raffles_export_file', $product_id, $file_type);
        self::getPart('close', 'tab');
        exit();
    }

    public static function executeQuickie()
    {
        $product_ids = sanitize_text_field($_GET['pids'] ?? '');
        $quotes = sanitize_text_field($_GET['quotes'] ?? '');
        do_action('woo_raffles_export_quickie', $product_ids, $quotes);
        self::getPart('close', 'tab');
        exit();
    }
}
