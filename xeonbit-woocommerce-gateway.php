<?php
/*
Plugin Name: Xeonbit Woocommerce Gateway
Plugin URI: https://github.com/xeonbit-integrations/xeonbitwp
Description: Extends WooCommerce by adding a Xeonbit Gateway
Version: 3.0.0
Tested up to: 4.9.8
Author: mosu-forge, SerHack
Author URI: https://xeonbitintegrations.com/
*/
// This code isn't for Dark Net Markets, please report them to Authority!

defined( 'ABSPATH' ) || exit;

// Constants, you can edit these if you fork this repo
define('XEONBIT_GATEWAY_MAINNET_EXPLORER_URL', 'https://explorer.xeonbit.com/');
define('XEONBIT_GATEWAY_TESTNET_EXPLORER_URL', 'https://explorer.xeonbit.com/');
define('XEONBIT_GATEWAY_ADDRESS_PREFIX', 0xB1);
define('XEONBIT_GATEWAY_ADDRESS_PREFIX_INTEGRATED', 0xB3);
define('XEONBIT_GATEWAY_ATOMIC_UNITS', 12);
define('XEONBIT_GATEWAY_ATOMIC_UNIT_THRESHOLD', 10); // Amount under in atomic units payment is valid
define('XEONBIT_GATEWAY_DIFFICULTY_TARGET', 60);

// Do not edit these constants
define('XEONBIT_GATEWAY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('XEONBIT_GATEWAY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('XEONBIT_GATEWAY_ATOMIC_UNITS_POW', pow(10, XEONBIT_GATEWAY_ATOMIC_UNITS));
define('XEONBIT_GATEWAY_ATOMIC_UNITS_SPRINTF', '%.'.XEONBIT_GATEWAY_ATOMIC_UNITS.'f');

// Include our Gateway Class and register Payment Gateway with WooCommerce
add_action('plugins_loaded', 'xeonbit_init', 1);
function xeonbit_init() {

    // If the class doesn't exist (== WooCommerce isn't installed), return NULL
    if (!class_exists('WC_Payment_Gateway')) return;

    // If we made it this far, then include our Gateway Class
    require_once('include/class-xeonbit-gateway.php');

    // Create a new instance of the gateway so we have static variables set up
    new Xeonbit_Gateway($add_action=false);

    // Include our Admin interface class
    require_once('include/admin/class-xeonbit-admin-interface.php');

    add_filter('woocommerce_payment_gateways', 'xeonbit_gateway');
    function xeonbit_gateway($methods) {
        $methods[] = 'Xeonbit_Gateway';
        return $methods;
    }

    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'xeonbit_payment');
    function xeonbit_payment($links) {
        $plugin_links = array(
            '<a href="'.admin_url('admin.php?page=xeonbit_gateway_settings').'">'.__('Settings', 'xeonbit_gateway').'</a>'
        );
        return array_merge($plugin_links, $links);
    }

    add_filter('cron_schedules', 'xeonbit_cron_add_one_minute');
    function xeonbit_cron_add_one_minute($schedules) {
        $schedules['one_minute'] = array(
            'interval' => 60,
            'display' => __('Once every minute', 'xeonbit_gateway')
        );
        return $schedules;
    }

    add_action('wp', 'xeonbit_activate_cron');
    function xeonbit_activate_cron() {
        if(!wp_next_scheduled('xeonbit_update_event')) {
            wp_schedule_event(time(), 'one_minute', 'xeonbit_update_event');
        }
    }

    add_action('xeonbit_update_event', 'xeonbit_update_event');
    function xeonbit_update_event() {
        Xeonbit_Gateway::do_update_event();
    }

    add_action('woocommerce_thankyou_'.Xeonbit_Gateway::get_id(), 'xeonbit_order_confirm_page');
    add_action('woocommerce_order_details_after_order_table', 'xeonbit_order_page');
    add_action('woocommerce_email_after_order_table', 'xeonbit_order_email');

    function xeonbit_order_confirm_page($order_id) {
        Xeonbit_Gateway::customer_order_page($order_id);
    }
    function xeonbit_order_page($order) {
        if(!is_wc_endpoint_url('order-received'))
            Xeonbit_Gateway::customer_order_page($order);
    }
    function xeonbit_order_email($order) {
        Xeonbit_Gateway::customer_order_email($order);
    }

    add_action('wc_ajax_xeonbit_gateway_payment_details', 'xeonbit_get_payment_details_ajax');
    function xeonbit_get_payment_details_ajax() {
        Xeonbit_Gateway::get_payment_details_ajax();
    }

    add_filter('woocommerce_currencies', 'xeonbit_add_currency');
    function xeonbit_add_currency($currencies) {
        $currencies['Xeonbit'] = __('Xeonbit', 'xeonbit_gateway');
        return $currencies;
    }

    add_filter('woocommerce_currency_symbol', 'xeonbit_add_currency_symbol', 10, 2);
    function xeonbit_add_currency_symbol($currency_symbol, $currency) {
        switch ($currency) {
        case 'Xeonbit':
            $currency_symbol = 'XNB';
            break;
        }
        return $currency_symbol;
    }

    if(Xeonbit_Gateway::use_xeonbit_price()) {

        // This filter will replace all prices with amount in Xeonbit (live rates)
        add_filter('wc_price', 'xeonbit_live_price_format', 10, 3);
        function xeonbit_live_price_format($price_html, $price_float, $args) {
            if(!isset($args['currency']) || !$args['currency']) {
                global $woocommerce;
                $currency = strtoupper(get_woocommerce_currency());
            } else {
                $currency = strtoupper($args['currency']);
            }
            return Xeonbit_Gateway::convert_wc_price($price_float, $currency);
        }

        // These filters will replace the live rate with the exchange rate locked in for the order
        // We must be careful to hit all the hooks for price displays associated with an order,
        // else the exchange rate can change dynamically (which it should for an order)
        add_filter('woocommerce_order_formatted_line_subtotal', 'xeonbit_order_item_price_format', 10, 3);
        function xeonbit_order_item_price_format($price_html, $item, $order) {
            return Xeonbit_Gateway::convert_wc_price_order($price_html, $order);
        }

        add_filter('woocommerce_get_formatted_order_total', 'xeonbit_order_total_price_format', 10, 2);
        function xeonbit_order_total_price_format($price_html, $order) {
            return Xeonbit_Gateway::convert_wc_price_order($price_html, $order);
        }

        add_filter('woocommerce_get_order_item_totals', 'xeonbit_order_totals_price_format', 10, 3);
        function xeonbit_order_totals_price_format($total_rows, $order, $tax_display) {
            foreach($total_rows as &$row) {
                $price_html = $row['value'];
                $row['value'] = Xeonbit_Gateway::convert_wc_price_order($price_html, $order);
            }
            return $total_rows;
        }

    }

    add_action('wp_enqueue_scripts', 'xeonbit_enqueue_scripts');
    function xeonbit_enqueue_scripts() {
        if(Xeonbit_Gateway::use_xeonbit_price())
            wp_dequeue_script('wc-cart-fragments');
        if(Xeonbit_Gateway::use_qr_code())
            wp_enqueue_script('xeonbit-qr-code', XEONBIT_GATEWAY_PLUGIN_URL.'assets/js/qrcode.min.js');

        wp_enqueue_script('xeonbit-clipboard-js', XEONBIT_GATEWAY_PLUGIN_URL.'assets/js/clipboard.min.js');
        wp_enqueue_script('xeonbit-gateway', XEONBIT_GATEWAY_PLUGIN_URL.'assets/js/xeonbit-gateway-order-page.js');
        wp_enqueue_style('xeonbit-gateway', XEONBIT_GATEWAY_PLUGIN_URL.'assets/css/xeonbit-gateway-order-page.css');
    }

    // [xeonbit-price currency="USD"]
    // currency: BTC, GBP, etc
    // if no none, then default store currency
    function xeonbit_price_func( $atts ) {
        global  $woocommerce;
        $a = shortcode_atts( array(
            'currency' => get_woocommerce_currency()
        ), $atts );

        $currency = strtoupper($a['currency']);
        $rate = Xeonbit_Gateway::get_live_rate($currency);
        if($currency == 'BTC')
            $rate_formatted = sprintf('%.8f', $rate / 1e8);
        else
            $rate_formatted = sprintf('%.5f', $rate / 1e8);

        return "<span class=\"xeonbit-price\">1 XNB = $rate_formatted $currency</span>";
    }
    add_shortcode('xeonbit-price', 'xeonbit_price_func');


    // [xeonbit-accepted-here]
    function xeonbit_accepted_func() {
        return '<img src="'.XEONBIT_GATEWAY_PLUGIN_URL.'assets/images/xeonbit-accepted-here.png" />';
    }
    add_shortcode('xeonbit-accepted-here', 'xeonbit_accepted_func');
    
    // [xnb-accepted-here]
    function xnb_accepted_func() {
        return '<img src="'.XEONBIT_GATEWAY_PLUGIN_URL.'assets/images/XNB-accepted-here.jpg" />';
    }
    add_shortcode('xnb-accepted-here', 'xnb_accepted_func');

}

register_deactivation_hook(__FILE__, 'xeonbit_deactivate');
function xeonbit_deactivate() {
    $timestamp = wp_next_scheduled('xeonbit_update_event');
    wp_unschedule_event($timestamp, 'xeonbit_update_event');
}

register_activation_hook(__FILE__, 'xeonbit_install');
function xeonbit_install() {
    global $wpdb;
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . "xeonbit_gateway_quotes";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
               order_id BIGINT(20) UNSIGNED NOT NULL,
               payment_id VARCHAR(16) DEFAULT '' NOT NULL,
               currency VARCHAR(6) DEFAULT '' NOT NULL,
               rate BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               amount BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               paid TINYINT NOT NULL DEFAULT 0,
               confirmed TINYINT NOT NULL DEFAULT 0,
               pending TINYINT NOT NULL DEFAULT 1,
               created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
               PRIMARY KEY (order_id)
               ) $charset_collate;";
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . "xeonbit_gateway_quotes_txids";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
               id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
               payment_id VARCHAR(16) DEFAULT '' NOT NULL,
               txid VARCHAR(64) DEFAULT '' NOT NULL,
               amount BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               height MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
               PRIMARY KEY (id),
               UNIQUE KEY (payment_id, txid, amount)
               ) $charset_collate;";
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . "xeonbit_gateway_live_rates";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
               currency VARCHAR(6) DEFAULT '' NOT NULL,
               rate BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
               PRIMARY KEY (currency)
               ) $charset_collate;";
        dbDelta($sql);
    }
}
