<?php

namespace WooRaffles\Admin;

class Page extends Template
{

    public function __construct()
    {
        parent::__construct();
        add_action('admin_menu', [$this, 'addMenu']);
    }

    public function addMenu()
    {
        add_menu_page(
            __('Sorteio', 'woo-raffles'),
            __('Sorteio', 'woo-raffles'),
            'manage_options',
            'woo-raffles',
            [$this, 'render'],
            'dashicons-tickets-alt',
            56
        );
    }

    public function render()
    {
        ob_start();

        self::getPart('page', 'index', []);

        $content = ob_get_contents();
        ob_end_clean();

        echo $content;
    }
}