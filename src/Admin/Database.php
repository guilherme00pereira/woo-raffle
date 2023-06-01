<?php

namespace WooRaffles\Admin;

use UPFlex\MixUp\Core\Base;

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

class Database extends Base
{
    protected static bool $table_exist = false;
    public static string $table_name = 'woo_raffles_numbers';

    public function __construct()
    {
        register_activation_hook(WOORAFFLES_FILE, [self::class, 'createTables']);
    }

    protected static function createFunctions()
    {
        self::createAutoInc();
    }

    protected static function createAutoInc()
    {
        global $wpdb;

        $table_name = self::$table_name;

        $sql = "CREATE FUNCTION IF NOT EXISTS autoInc( send_product_id INT ) 
                    RETURNS INT DETERMINISTIC 
                    BEGIN DECLARE getCount INT(11) ; 
                        SET getCount = ( 
                            SELECT COUNT(generated_number) 
                            FROM {$wpdb->base_prefix}{$table_name} 
                            WHERE product_id = send_product_id 
                        ) + 1 ; 
                        RETURN getCount; 
                    END;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $wpdb->hide_errors();

        $wpdb->query($sql);
    }

    public static function createTables()
    {
        if (self::tableExist()) {
            self::createFunctions();
            return;
        }

        $table_name = self::$table_name;

        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->base_prefix}{$table_name} (
			id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			generated_number int(11) NOT NULL,
			order_id int(11) NOT NULL,
			order_item_id int(11) NOT NULL,
			product_id int(11) NOT NULL,
  			PRIMARY KEY (id),
            UNIQUE KEY raffle_uniq_id (generated_number, product_id)
		) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $wpdb->hide_errors();

        $wpdb->query($sql);

        self::$table_exist = self::tableExist(true);

        self::createFunctions();
    }

    public static function tableExist($force = false): bool
    {
        global $wpdb;

        if (!$force && isset(self::$table_exist)) {
            return self::$table_exist;
        }

        $table_name = self::$table_name;

        $table_exist = $wpdb->get_var(
            $wpdb->prepare(
                'SHOW TABLES LIKE %s',
                $wpdb->esc_like("{$wpdb->base_prefix}{$table_name}")
            )
        );

        self::$table_exist = (bool)$table_exist;

        return self::$table_exist;
    }
}
