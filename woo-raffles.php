<?php
use UPFlex\MixUp\Core\Base;
use UPFlex\MixUp\Core\Instance\Create;

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

/**
 * @wordpress-plugin
 * Plugin Name:       WooSorteios
 * Description:       Plugin criado para sorteios no Woocommerce.
 * Version:           2.1.5
 * Author:            Agência UPFlex
 * Text Domain:       woo-raffles
 * Domain Path:       /
 */

require_once __DIR__ . '/vendor/autoload.php';

const WOORAFFLES_FILE = __FILE__;

define('WOORAFFLES_BASENAME', plugin_basename(__FILE__));
define('WOORAFFLES_DIR', plugin_dir_path(__FILE__));
define('WOORAFFLES_URL', plugin_dir_url(__FILE__));

try {
    Create::run(
        'WooRaffles\Woocommerce',
        Base::class,
        [],
        WOORAFFLES_DIR
    );

    Create::run(
        'WooRaffles\Admin',
        Base::class,
        [],
        WOORAFFLES_DIR
    );

    Create::run(
        'WooRaffles\Front',
        Base::class,
        [],
        WOORAFFLES_DIR
    );
} catch (ReflectionException $e) {
    error_log($e->getMessage());
}