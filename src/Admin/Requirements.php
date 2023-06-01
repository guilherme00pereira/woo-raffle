<?php

namespace WooRaffles\Admin;

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

class Requirements
{
    public function __construct()
    {
        add_action('admin_init', [self::class, 'has_parent_plugin']);
    }

    public static function has_parent_plugin()
    {
        if (is_admin() && current_user_can('activate_plugins') &&
            (!class_exists(\WC_Cart::class))) {

            add_action('admin_notices', [self::class, 'notice']);

            deactivate_plugins(WOORAFFLES_BASENAME);

            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }
        }
    }

    public static function notice()
    {
        ?>
        <div class="error">
            <p><?php echo wp_kses_post(__('Desculpe, mas o plugin <strong>WooSorteios</strong> requer que o plugin <strong>"Woocommerce"</strong> esteja instalado e ativo.', 'woo-raffles')); ?></p>
        </div>
        <?php
    }

}
