<?php
 
namespace WooRaffles\Admin;
 
use UPFlex\MixUp\Core\Base;
use WooRaffles\Woocommerce\GenerateNumbers;
 
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}
 
class WhatsappOrderStatus extends Base
{
    public static string $button_text = '';
    public static string $button_url = '';
    public static string $image = '';
    public static string $message = '';
    public static string $message_admin = '';
 
    public function __construct()
    {
        add_action('woocommerce_order_status_cancelled', [self::class, 'cancelled'], 9);
        add_action('woocommerce_order_status_completed', [self::class, 'completed']);
        add_action('woocommerce_checkout_order_processed', [self::class, 'pendingMp']);
        add_action('woocommerce_order_status_failed', [self::class, 'failed']);
        add_action('woocommerce_order_status_on-hold', [self::class, 'pending'], 9);
        add_action('woocommerce_order_status_pending', [self::class, 'pending']);
        add_action('woo_raffles_after_numbers_generated', [self::class, 'processing']);
        add_action('woocommerce_order_status_refunded', [self::class, 'refunded']);
        add_action('woocommerce_order_note_added', [self::class, 'tracking', 10, 2]);
    }
 
    public static function cancelled($order_id)
    {
        self::resetVariables();
 
        if (have_rows('woo_group_cancelled', 9991274)):
            while (have_rows('woo_group_cancelled', 9991274)) : the_row();
                self::$message = get_sub_field("woo_message_cancelled", 9991274) ?? '';
                self::$message_admin = get_field("woo_message_cancelled_admin", 9991274) ?? '';
                self::$image = get_sub_field('woo_image_cancelled', 9991274) ?? '';
                self::$button_text = get_sub_field('woo_button_cancelled', 9991274) ?? '';
                self::$button_url = get_sub_field('woo_button_url_cancelled', 9991274) ?? '';
            endwhile;
        endif;
 
        $order = wc_get_order($order_id);
        self::runAction($order_id, $order->correios_tracking_code ?? '');
        GenerateNumbers::delete($order_id);
    }
 
    public static function completed($order_id)
    {
        self::resetVariables();
 
        if (have_rows('woo_group_completed', 9991274)):
            while (have_rows('woo_group_completed', 9991274)) : the_row();
                self::$message = get_sub_field("woo_message_completed", 9991274) ?? '';
                self::$message_admin = get_field("woo_message_completed_admin", 9991274) ?? '';
                self::$image = get_sub_field('woo_image_completed', 9991274) ?? '';
                self::$button_text = get_sub_field('woo_button_completed', 9991274) ?? '';
                self::$button_url = get_sub_field('woo_button_url_completed', 9991274) ?? '';
            endwhile;
        endif;
 
        self::runAction($order_id);
    }
 
    public static function failed($order_id)
    {
        self::resetVariables();
 
        if (have_rows('woo_group_failed', 9991274)):
            while (have_rows('woo_group_failed', 9991274)) : the_row();
                self::$message = get_sub_field("woo_message_failed", 9991274) ?? '';
                self::$message_admin = get_field("woo_message_failed_admin", 9991274) ?? '';
                self::$image = get_sub_field('woo_image_failed', 9991274) ?? '';
                self::$button_text = get_sub_field('woo_button_failed', 9991274) ?? '';
                self::$button_url = get_sub_field('woo_button_url_failed', 9991274) ?? '';
            endwhile;
        endif;
 
        self::runAction($order_id);
    }
 
    public static function pendingMp($order_id)
    {
        $order = wc_get_order($order_id);
        $payment_method = $order->get_payment_method();
        if ($payment_method == "cod" or $payment_method == "bacs" or $payment_method == "pix_gateway") : return; endif;
 
        self::pending($order_id);
    }
 
    public static function pending($order_id)
    {
        self::resetVariables();
 
        if (have_rows('woo_group_pending', 9991274)):
            while (have_rows('woo_group_pending', 9991274)) : the_row();
                self::$message = get_sub_field("woo_message_pending", 9991274) ?? '';
                self::$message_admin = get_field("woo_message_pending_admin", 9991274) ?? '';
                self::$image = get_sub_field('woo_image_pending', 9991274) ?? '';
                self::$button_text = get_sub_field('woo_button_pending', 9991274) ?? '';
                self::$button_url = get_sub_field('woo_button_url_pending', 9991274) ?? '';
            endwhile;
        endif;
 
        self::runAction($order_id);
    }
 
    public static function processing($order_id)
    {
        self::resetVariables();
 
        if (have_rows('woo_group_processing', 9991274)):
            while (have_rows('woo_group_processing', 9991274)) : the_row();
                self::$message = get_sub_field("woo_message_processing", 9991274) ?? '';
                self::$message_admin = get_field("woo_message_processing_admin", 9991274) ?? '';
                self::$image = get_sub_field('woo_image_processing', 9991274) ?? '';
                self::$button_text = get_sub_field('woo_button_processing', 9991274) ?? '';
                self::$button_url = get_sub_field('woo_button_url_processing', 9991274) ?? '';
            endwhile;
        endif;
 
        self::runAction($order_id, '', false);
    }
 
    public static function refunded($order_id)
    {
        self::resetVariables();
 
        if (have_rows('woo_group_refunded', 9991274)):
            while (have_rows('woo_group_refunded', 9991274)) : the_row();
                self::$message = get_sub_field("woo_message_refunded", 9991274) ?? '';
                self::$message_admin = get_field("woo_message_refunded_admin", 9991274) ?? '';
                self::$image = get_sub_field('woo_image_refunded', 9991274) ?? '';
                self::$button_text = get_sub_field('woo_button_refunded', 9991274) ?? '';
                self::$button_url = get_sub_field('woo_button_url_refunded', 9991274) ?? '';
            endwhile;
        endif;
 
        self::runAction($order_id);
    }
 
    public static function tracking($comment_id, $order)
    {
        self::resetVariables();
 
        if (is_plugin_active('woocommerce-correios/woocommerce-correios.php')) {
 
            $comment_obj = get_comment($comment_id);
            $customer_note = $comment_obj->comment_content;
 
            if ((str_contains($customer_note, "Adicionado o código de rastreamento dos Correios")) || (str_contains($customer_note, "Added a Correios tracking code"))) {
                $order_id = $order->get_id();
                $order = wc_get_order($order_id);
                self::runAction($order_id, $order->correios_tracking_code ?? '');
            }
 
        }
    }
 
    protected static function getNumbers($order_id, $item_key, $product_id): string
    {
        global $wpdb;
 
        $table_name = Database::$table_name;
        $str_pad_left = get_post_meta($product_id, '_woo_raffles_str_pad_left', true) ?? 5;
 
        $result = $wpdb->get_col(
            $wpdb->prepare("
                    SELECT LPAD(generated_number, {$str_pad_left}, '0')
                    FROM {$wpdb->base_prefix}{$table_name} 
                    WHERE order_id = %d AND order_item_id = %d
                    ORDER BY generated_number ASC
                ", $order_id, $item_key
            )
        );
 
        return implode(', ', $result);
    }
 
    protected static function resetVariables()
    {
        self::$message = '';
        self::$message_admin = '';
        self::$image = '';
        self::$button_text = '';
        self::$button_url = '';
    }
 
    protected static function runAction($order_id, $code_zip = '', $force_send = true)
    {
        $order = wc_get_order($order_id);
        $name = $order->get_billing_first_name();
        $email = $order->get_billing_email();
        $order_billing_phone = $order->get_billing_phone();
        $phone = $order_billing_phone;
        $phone_admin = get_field('wpp_administrador', 9991274);
        $order_billing_address_1 = $order->get_billing_address_1();
        $order_billing_address_2 = $order->get_billing_address_2();
        $order_billing_city = $order->get_billing_city();
        $order_billing_state = $order->get_billing_state();
        $order_billing_postcode = $order->get_billing_postcode();
        $order_billing_country = $order->get_billing_country();
        $order_total = "R$" . $order->get_total();
        $shipping_total = "R$" . $order->get_total_shipping();
        $payment_method = $order->get_payment_method_title();
        $phone = number_internationalization($order_billing_country, $phone);
        $address = $order_billing_address_1 . ', ' . $order_billing_address_2 . ', ' . $order_billing_city . ', ' . $order_billing_state . ', ' . $order_billing_postcode . ', ' . $order_billing_country . '. ';
        $vendor_phone = getPhoneVendor($order_billing_country, $order_id);
 
        $numbers_selected = '';
        $product_list = '';
 
        foreach ($order->get_items() as $item_key => $item):
            $product_list .= $item->get_name() . ' (' . $item->get_quantity() . 'und.' . ' - R$' . $item->get_total() . '), ';
 
            $product_id = $item->get_product_id();
 
            $quotes = self::getNumbers($order_id, $item_key, $product_id);
 
            $numbers_selected .= strlen($quotes) > 0 ? $quotes : '';
        endforeach;
 
        if (!strlen($numbers_selected) && !$force_send) {
            return;
        }

        if (!empty(self::$message)) :
            woo_whatsapp_send_message($order_id, $name, $product_list, $address, $order_total, $shipping_total, $payment_method, $numbers_selected, $phone, $code_zip, $email, self::$message, self::$image, self::$button_text, self::$button_url);
        endif;
 
        if (!empty($vendor_phone)) :
            woo_whatsapp_send_message_admin($order_id, $name, $product_list, $address, $order_total, $shipping_total, $payment_method, $numbers_selected, $vendor_phone, $code_zip, $email, self::$message_admin, self::$image, self::$button_text, self::$button_url);
        endif;
 
        if (!empty(self::$message_admin) && !empty($phone_admin)) :
            woo_whatsapp_send_message_admin($order_id, $name, $product_list, $address, $order_total, $shipping_total, $payment_method, $numbers_selected, $phone_admin, $code_zip, $email, self::$message_admin, self::$image, self::$button_text, self::$button_url);
        endif;
    }
}


/*
 * *[CLIENTE]* parabénsss pela sua participação!!✅\n\nCHEGOU a sua vez de andar de *Jaguar...*😍😍
 * \n\nOu receber *150mil NA CONTA!!* 👏👏\n\nAbaixo estão seus números da sorte👇\n\n🔵🔵🔵\n[PRODUTOS]\n\n[COTAS_RIFA]
 * \n🔵🔵🔵\n\nBOA SORTE!!🍀🍀\n\nE você GANHOU um Desconto em nossa *Ação SECRETA* que é exclusiva para os participantes
 * do *Jaguar*😱\n\nSe caso você quer receber me diga *SIM* e eu irei enviar pra você👇
 *
 */