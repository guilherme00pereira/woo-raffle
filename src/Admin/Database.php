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

    public static function getOrdersIdsByProductId(
        $product_id,
        $order_status = array( 'wc-completed', 'wc-processing', 'wc-on-hold', 'wc-pending' )
    ): array
    {

        global $wpdb;

        $results = $wpdb->get_col("
        SELECT order_items.order_id
        FROM {$wpdb->prefix}woocommerce_order_items as order_items
        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
        WHERE posts.post_type = 'shop_order'
        AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
        AND order_items.order_item_type = 'line_item'
        AND order_item_meta.meta_key = '_product_id'
        AND order_item_meta.meta_value = '$product_id'
    ");

        return $results;
    }

    public static function getRaffleQuotaInfo($product_id, $quotas) {
        global $wpdb;
        $data = [];
        $orderId = "";
        $sql = "select rn.order_id, pm.meta_key, pm.meta_value FROM {$wpdb->base_prefix}postmeta pm 
                inner join {$wpdb->base_prefix}woo_raffles_numbers rn on rn.order_id = pm.post_id
                where rn.generated_number in (%s)
                and pm.post_id = rn.order_id
                and rn.product_id = %s
                and (pm.meta_key = '_billing_first_name'
                or pm.meta_key = '_billing_last_name'
                or pm.meta_key = '_billing_phone')";
        $result = $wpdb->get_results($wpdb->prepare($sql, implode($quotas, ','), $product_id), ARRAY_A);
        foreach ($result as $value) {
            $orderId = $value['order_id'];
            $data[$value['meta_key']] = $value['meta_value'];
        }
        $sql = "SELECT post_status FROM {$wpdb->base_prefix}posts WHERE ID = %s";
        $data['status'] = $wpdb->get_var($wpdb->prepare($sql, $orderId));
        $data['pedido'] = $orderId;
        return $data;
    }

    public static function getSoldQuotes($product_id): ?string
    {
        global $wpdb;
        $sql = "SELECT count(*) as total FROM {$wpdb->base_prefix}woo_raffles_numbers WHERE product_id = %s";
        return $wpdb->get_var($wpdb->prepare($sql, $product_id));
    }
}
