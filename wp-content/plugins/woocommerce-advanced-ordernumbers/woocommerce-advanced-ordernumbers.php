<?php
/**
 * Plugin Name: Woocommerce Advanced Ordernumbers
 * Plugin URI: http://open-tools.net/woocommerce/advanced-ordernumbers-for-woocommerce.html
 * Description: Lets the user freely configure the order numbers in WooCommerce.
 * Version: 1.3.9
 * Author: Open Tools
 * Author URI: http://open-tools.net
 * Text Domain: woocommerce-advanced-ordernumbers
 * Domain Path: 
 * License: GPL2+
 * Network: true
 * WC requires at least: 2.2
 * WC tested up to: 2.6
*/

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}
// Define a global flag so the basic plugin can deactivate itself if this plugin is loaded
if ( !defined('OPENTOOLS_ADVANCED_ORDERNUMBERS') ) {
	define ('OPENTOOLS_ADVANCED_ORDERNUMBERS', true);
}

// *****************************************************************
// * PLUGIN UPDATES
// *****************************************************************
require 'opentools-update-checker.php';
$myUpdateChecker = new OpenToolsPluginUpdateChecker(
    'http://www.open-tools.net/UpdateServer/index.php?package=WooCommerce&extension=AdvancedOrdernumbers',
    __FILE__,
    'woocommerce-advanced-ordernumbers'
);
// $myUpdateChecker ->debugMode = true;

$myUpdateChecker->declareCredentials(array(
	'order_number' => __('Order Number:'), 
	'order_pass'   => __('Order Password:'),
));
$myUpdateChecker->addAccessCheckQueryArgFilter('oton_addAccessCheckArg');
function oton_addAccessCheckArg($downloadurl) {
	return $downloadurl . (parse_url($downloadurl, PHP_URL_QUERY) ? '&' : '?') . 'check_access=1';
}

// $myUpdateChecker->checkForUpdates();
// *****************************************************************


function otaon_is_wc_active() {
// Makes sure the plugin is defined before trying to use it
	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}
	return 
		in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) 
		||
		is_plugin_active_for_network( 'woocommerce/woocommerce.php' );
}

/**
 * Check if WooCommerce is active
 **/
if ( otaon_is_wc_active() ) {

	if (file_exists(plugin_dir_path( __FILE__ ) . '/ordernumbers_woocommerce.php') && !class_exists("OpenToolsOrdernumbers")) {
		require_once( plugin_dir_path( __FILE__ ) . '/ordernumbers_woocommerce.php');
	} 
	if (!class_exists("OpenToolsOrdernumbersBasic"))
		require_once( plugin_dir_path( __FILE__ ) . '/ordernumbers_woocommerce_basic.php');

	// instantiate the plugin class
	if (class_exists("OpenToolsOrdernumbers")) {
		$ordernumber_plugin = new OpenToolsOrdernumbers(plugin_basename(__FILE__));
	} elseif (class_exists("OpenToolsOrdernumbersBasic")) {
		$ordernumber_plugin = new OpenToolsOrdernumbersBasic(plugin_basename(__FILE__));
	}

}
