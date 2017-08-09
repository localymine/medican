<?php
/**
 * This is the actual ordernumber plugin class for WooCommerce.
 * Copyright (C) 2015 Reinhold Kainhofer, Open Tools
 * Author: Open Tools, Reinhold Kainhofer
 * Author URI: http://open-tools.net
 * License: GPL2+
*/
if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}
if (!class_exists( 'OpenToolsOrdernumbersBasic' )) 
	require_once (dirname(__FILE__) . '/ordernumbers_woocommerce_basic.php');
	
	
/* 
* RK: This debugging function is taken from the debug_backtrace documentation page of php.net: 
* Send the output from a backtrace to the error_log 
* @param string $message Optional message that will be sent the the error_log before the backtrace 
*/ 
function log_trace($message = '') { 
    $trace = debug_backtrace(); 
    if ($message) { 
        error_log($message); 
    } 
    $caller = array_shift($trace); 
    $function_name = $caller['function']; 
    error_log(sprintf('%s: Called from %s:%s', $function_name, $caller['file'], $caller['line'])); 
    foreach ($trace as $entry_id => $entry) { 
        $entry['file'] = $entry['file'] ? : '-'; 
        $entry['line'] = $entry['line'] ? : '-'; 
        if (empty($entry['class'])) { 
            error_log(sprintf('%s %3s. %s() %s:%s', $function_name, $entry_id + 1, $entry['function'], $entry['file'], $entry['line'])); 
        } else { 
            error_log(sprintf('%s %3s. %s->%s() %s:%s', $function_name, $entry_id + 1, $entry['class'], $entry['function'], $entry['file'], $entry['line'])); 
        } 
    } 
} 

class OpenToolsOrdernumbers extends OpenToolsOrdernumbersBasic {
	/**
	 * Construct the plugin object
	 */
	public function __construct($basename) {
		parent::__construct($basename);
		$this->is_advanced = true;

		$this->helper->registerCallback('setupStoreReplacements',		array($this, 'setupStoreReplacements'));
		$this->helper->registerCallback('setupOrderReplacements',		array($this, 'setupOrderReplacements'));
		$this->helper->registerCallback('setupUserReplacements',		array($this, 'setupUserReplacements'));
		$this->helper->registerCallback('setupShippingReplacements',	array($this, 'setupShippingReplacements'));
		$this->helper->registerCallback('setupThirdPartyReplacements',	array($this, 'setupThirdPartyReplacements'));
	}
	/**
	 * Override the initializeBasicSettings method, which restricts some functionality in the basic plugin.
	 */
	protected function initializeBasicSettings() {}
	
	/**
	 * Install all neccessary filters and actions for this plugin
	 */
	protected function initializeHooks() {
		parent::initializeHooks();
		
		// Custom table widget for custom variable definitions: Hooks for creating and storing values
		add_action( 'woocommerce_admin_field_ordernumber_variables',    array( $this, 'admin_field_variables' ) );
		add_action( 'pre_update_option_ordernumber_variables',          array( $this, 'update_option_variables'));
		
		// SUPPORT FOR BUILT-IN PLUGINS AND PAYMENT METHODS:
		$this->paypal_invoicenumber_init();

		// THIRD-PARTY PLUGIN SUPPORT
		// Install hooks for third-party plugin support:
		$this->thirdparty_invoicenumber_init();
		// Support for specific plugins:
		$this->thirdparty_wpo_wcpdf_init();
		$this->thirdparty_wt_wc_pdf_invoice_init();
		$this->thirdparty_bewpi_invoices_init();
		$this->thirdparty_yith_pdf_invoice_init();
		$this->thirdparty_wc_delivery_notes_init();
		// END THIRD-PARTY PLUGIN SUPPORT
		
	}
	
	protected function initializeSettingsGeneral() {
		// Remove the NAG screen of the basic version
		return array();
	}
	/**
	 * Return the tooltip for the number format settings textinput (the two plugin versions have different features!)
	 */
	protected function getNumberFormatSettingsLabel() {
		return $this->helper->__( 'The format for the order numbers (variables can be entered as [...], the counter is indicated by the #). To use a different counter name than displayed, put the custom counter name after a |, e.g. "[year]-[month]/#|[year]" to use the month in the order number, but reset the counter only yearly. Advanced settings for the counter can be added as [#####:start/step], e.g. [#:100] to start new counters at 100, or [#/5] to increment the counter by 5. The number of # in the format determine how many digits are shown at least, e.g. [########] will always show at least 8 digits for the counter, e.g. 00000014.');
	}
	protected function addGlobalCounterSettings($settings) {
		$settings[] = array(
				'title'		=> $this->helper->__( 'Use global counter'),
				'desc' 		=> $this->helper->__( 'A global counter never resets. Non-global counters run within each number format and reset whenever any variable changes.'),
				'id' 		=> 'ordernumber_global',
				'type' 		=> 'checkbox',
				'default'	=> 'no',
			);
		return $settings;
	}
	
	
	protected function initializeSettingsOther() {
		return array_merge(
			$this->initializeSettingsInvoiceNumbers(),
			$this->initializeSettingsReplacements()
		);
	}
	
	protected function initializeSettingsInvoiceNumbers() {
		$settings = array(
			/**
			 * Invoice number settings 
			 */
			
			array(
				'name' 		=> $this->helper->__( 'Advanced Invoice Numbers'),
				'desc'		=> $this->helper->__('This plugin currently supports modifying the invoice number formats of the following invoicing plugins: <a href="https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/">WooCommerce PDF Invoices & Packing Slips</a>'),
				'type' 		=> 'title',
				'id' 		=> 'invoice_options'
			),
			
			array(
				'name' 		=> $this->helper->__( 'Customize Invoice Numbers'),
				'desc' 		=> $this->helper->__( 'Check to use custom invoice numbers rather than the default format of your invoicing plugin.'),
				'id' 		=> 'customize_invoice',
				'type' 		=> 'checkbox',
				'default'	=> 'no'
			),
			array(
				'title'		=> $this->helper->__( 'Invoice number format'),
				'desc' 		=> $this->helper->__( 'The format for the invoice numbers (variables can be entered as [...], the counter is indicated by the #). To use a different counter name than displayed, put the custom counter name after a |, e.g. "[year]-[month]/#|[year]" to use the month in the invoice number, but reset the counter only yearly. Advanced settings for the counter can be added as [#####:start/step], e.g. [#:100] to start new counters at 100, or [#/5] to increment the counter by 5. The number of # in the format determine how many digits are shown at least, e.g. [########] will always show at least 8 digits for the counter, e.g. 00000014.'),
				'desc_tip'	=> true,
				'id' 		=> 'invoice_format',
				'default'	=> '#',
				'type' 		=> 'text',
				'css'		=> 'width: 100%',
			),
			array(
				'title'		=> $this->helper->__( 'Use global counter'),
				'desc' 		=> $this->helper->__( 'A global counter never resets. Non-global counters run within each number format and reset whenever any variable changes.'),
				'id' 		=> 'invoice_global',
				'type' 		=> 'checkbox',
				'default'	=> 'no',
			),
			array(
				'name' 		=> $this->helper->__( 'All invoice number counters'),
				'desc'		=> $this->helper->__( 'View and modify the current counter values. The counter value is the value used for the previous number. All changes are immediately applied!'),
				'desc_tip'	=> true,
				'id' 		=> 'ordernumber_counters',
				'type' 		=> 'ordernumber_counters',
				'nrtype' 	=> 'invoice',
			),
			array( 'type' => 'sectionend', 'id' => 'invoice_options' )
		);
		
		add_option ('customize_invoice', 'no');
		add_option ('invoice_format',    "#");
		add_option ('invoice_global',    'no');
		return $settings;
	}
	
	protected function initializeSettingsReplacements() {
		$settings = array(
			array(
				'name' 		=> $this->helper->__( 'Custom Variables'),
				'desc'		=> $this->helper->__( 'Define your own (conditional) variables for use in the number formats'),
				'type' 		=> 'title',
				'id' 		=> 'ordernumber_variables'
			),
			array(
				'id' 		=> 'ordernumber_variables',
				'type' 		=> 'ordernumber_variables',
			),
			array( 'type' => 'sectionend', 'id' => 'ordernumber_variables' )
		);
 		add_option ('ordernumber_variables',  array());
 		return $settings;
	}

	
	/**
	 * Render the Custom Variables configuration table
	 */
	public function admin_field_variables($settings) {
		$variables = get_option( $settings['id'], array() );
		if (!is_array($variables)) {
			$variables = array();
		} ?>
		<tr valign="top">
		    <td class="forminp forminp-<?php echo sanitize_title( $settings['type'] ) ?>" colspan="2">
				<?php
					print $this->helper->custom_variables_create_table($settings['id'], $variables);
				?>
			</td>
		</tr> 
		<?php
	}

	/** 
	 * Store the variable replacements array into the options. Need to transpose the array before we can store it into the options...
	 * This filter is called directly before the option is saved.
	 */
	public function update_option_variables ($value) {
		return OrdernumberHelper::transposeCustomVariables($value);
	}


	/** ***********************************************************
	 * 
	 *  REPLACEMENT FUNCTIONS
	 *
	 **************************************************************/
	
	public function setupAddressReplacements(&$reps, $prefix, $address, $nrtype) {
		$reps["[email]"]     = $address->billing_email;
		$reps["[firstname]"] = $address->billing_first_name;
		$reps["[lastname]"]  = $address->billing_last_name;

		$reps["[company]"]   = $address->billing_company;
		$reps["[zip]"]       = $address->billing_postcode;
		$reps["[postcode]"]  = $address->billing_postcode;
		$reps["[city]"]      = $address->billing_city;
    
		$country = $address->billing_country;
		$state = $address->billing_state;
		$allcountries = WC()->countries->get_countries();
		$states = WC()->countries->get_states($country);
		$reps["[country]"]     = $country;
		$reps["[countryname]"] = ( isset( $allcountries[ $country ] ) ) ? $allcountries[ $country ] : $country;

		$reps["[state]"]       = $state;
		$reps["[statename]"]   = ( $country && $state && isset( $states[ $country ][ $state ] ) ) ? $states[ $country ][ $state ] : $state;
	}
	
	public function setupStoreReplacements (&$reps, $order, $nrtype) {
	}
    
	public function setupOrderReplacements (&$reps, $order, $nrtype) {
		$reps["[orderid]"] = $order->id;
		
		if ($nrtype != 'ordernumber') {
			$reps["[ordernumber]"] = $order->get_order_number();
		}
		$reps["[orderstatus]"] = $order->get_status();
		$reps["[currency]"]    = $order->get_order_currency();

		$this->setupAddressReplacements($reps, "", $order, $nrtype);
	
		$reps["[articles]"]    = $order->get_item_count();
// 		$reps["[downloadpermitted]"] = $order->is_download_permitted();
// 		$reps["[hasdownloads]"] = $order->has_downloadable_item();
// 		$reps["[coupons]"] = $order->get_used_coupons();
		$reps["[ordertotal]"]      = $order->get_total();
		$reps["[amount]"]      = $order->get_total();
		$reps["[ordersubtotal]"]      = $order->get_subtotal();
		$reps["[totaltax]"]      = $order->get_total_tax();
		$reps["[totalshipping]"]      = $order->get_total_shipping();
		
		// List-valued properties for custom variable checks:
		// TODO: Also implement variable for:
		//  - Shipping needed
		//  - Downloads available
		$lineitems = $order->get_items();
		$skus = array();
		$categories = array();
		$tags = array();
		$shippingclasses = array();
		$vendors = array();
		foreach ($lineitems as $l) {
			$p = $order->get_product_from_item($l);
			$skus[$p->get_sku()] = 1;
			foreach (wc_get_product_terms( $p->id, 'product_cat') as $c) {
				$categories[$c->slug] = 1;
			}
			foreach (wc_get_product_terms( $p->id, 'product_tag') as $c) {
				$tags[$c->slug] = 1;
			}
			$shippingclasses[$p->get_shipping_class()] = 1;
			
			// THIRD-PARTY SUPPORT
			// "WC Vendors"  support (vendors stored as post author)
			if (class_exists("WC_Vendors")) {
				$vendor = $p->post->post_author;
				$vnd = get_user_by('id', $vendor);  // Get user name by user id
				$vendors[] = $vendor;
				$vendors[] = $vnd->user_login;
			}
			
			// "WooThemes Vendor Products" support (vendors stored in its own taxonomy)
			if (class_exists("WooCommerce_Product_Vendors") && function_exists("get_product_vendors")) {
				foreach (get_product_vendors($p->id) as $vendor) {
					$vendors[] = $vendor->slug;
					$vendors[] = $vendor->ID;
				}
			}
			
			// "YITH WooCommerce Multi Vendor" support (vendors stored in its own taxonomy)
			if (function_exists("yith_get_vendor")) {
				$vendor = yith_get_vendor($p->id, 'product');
				if ($vendor->is_valid()) {
					$vendors[] = $vendor->slug;
					$vendors[] = $vendor->term_id;
				}
			}
			// END THIRD-PARTY SUPPORT
		}
		$reps["[skus]"] = array_keys($skus);
		$reps["[categories]"] = array_keys($categories);
		$reps["[tags]"] = array_keys($tags);
		$reps["[shippingclasses]"] = array_keys($shippingclasses);
		$reps["[vendors]"] = array_unique($vendors);
	}

	public function setupUserReplacements (&$reps, $details, $nrtype) {
		$reps["[ipaddress]"]   = $details->customer_ip_address;
		$uid = $details->get_user_id();
		$userinfo = get_userdata($uid);
		$reps["[userid]"]      = $uid;
		$roles = array();
		if (isset($userinfo->roles) && is_array($userinfo->roles)) {
			$roles = $userinfo->roles;
		}
		$reps["[userroles]"] = $roles;
	}

	public function setupShippingReplacements(&$reps, $order, $nrtype) {
		$reps["[shipping]"] = $order->get_total_shipping();
		$smethods = array();
		$reps["[shippingmethodids]"] = array();
		$reps["[shippingmethodtypes]"] = array();
		$reps["[shippinginstanceids]"] = array();
		foreach ($order->get_shipping_methods() as $ship) {
			$smethods[] = $ship['name'];
			$reps["[shippingmethodids]"][] = $ship['method_id'];
			// With the shipping zones introduced in WC 2.6, the method_id
			// contains the shipping method type and the instance ID separated
			// by a :. If the ":" is not present, it is a legacy method and 
			// has no instance ID.
			$ids = explode(':', $ship['method_id'], 2);
			$reps["[shippingmethodtypes]"][] = $ids[0];
			if (isset($ids[1])) {
				$reps["[shippinginstanceids]"][] = $ids[1];
			}
		}
		$reps["[shippingmethods]"] = implode(", ", $smethods);
	}
	
	/*public function setupInvoiceReplacements (&$reps, $invoice, $order, $nrtype) {
		$reps["[invoiceid]"] = $invoice->getId();
	}*/

	public function setupThirdPartyReplacements (&$reps, $details, $nrtype) {
		$reps = apply_filters( 'opentools_ordernumber_replacements', $reps, $details, $nrtype);
	}

	
// PAYPAL SUPPORT
	protected function paypal_invoicenumber_init() {
		add_filter ('woocommerce_paypal_args', array( &$this, 'paypal_arguments'), 10, 2/*<= Also get the order object! */);
	}
	
	public function paypal_arguments($vals, $order) {
		if ($this->invoicenumbers_activated()) {
			$vals['invoice'] = $this->get_or_create_number($order->order_id, $order, 'invoice');
		}
		return $vals;
	}

// THIRD-PARTY PLUGIN SUPPORT
	
	/** ****************************************************************
	 *  Generic Invoice Number handling for third-party invoice plugins
	 ** ****************************************************************
	 *
	 *  - Filter woocommerce_generate_invoice_number($default, $order) to create the invoice number
	 *  - Filter woocommerce_invoice_number($default, $orderID) to retrieve the 
	 *    invoice number (also create the invoice number if it does not yet exist)
	 */

	protected function thirdparty_invoicenumber_init() {
		// The filter to actually return the order number for the given order
		add_filter ('woocommerce_generate_invoice_number', array( &$this, 'thirdparty_create_invoicenumber'), 10, 2/*<= Also get the order object! */);
		add_filter ('woocommerce_invoice_number', array( &$this, 'thirdparty_get_invoicenumber'), 10, 2/*<= Also get the order ID! */);
	}
	
	/**
	 * Callback function for WooThemes PDF Invoices to generate an invoice number for an order
	 * The hook to customize invoice numbers (requests the invoice number from the database; 
	 * creates a new invoice number if no entry exists in the database)
	 */
	function thirdparty_create_invoicenumber($default, $order) {
		if ($this->invoicenumbers_activated()) {
			return $this->get_or_create_number($default, $order, 'invoice');
		} else {
			return $default;
		}
	}
	
	function thirdparty_get_invoicenumber($default, $orderid) {
		if ($this->invoicenumbers_activated()) {
			$_of = new WC_Order_Factory();
			$order = $_of->get_order($orderid);
			return $this->get_number($orderid, $order, 'invoice');
		} else {
			return $default;
		}
	}
	
	
	/** ************************************************************
	 *  Support for WPO WooCommerce PDF Invoices and Packaging Slips
	 ** ************************************************************
	 *
	 *  - Invoice numbers are stored in the _wcpdf_invoice_number post meta
	 *  - the filter wpo_wcpdf_invoice_number($invoice_number, $order_number, $order_id, $order_data) is called 
	 *    to format the (existing) invoice number retrieved from that post meta
	 *  - The action wpo_wcpdf_process_template_order($template_type, $order_id) is called right before
	 *    the invoice is created. There we can already set the _wcpdf_invoice_number post meta with our own value
	 */
	 
	/**
	 * Initialize support for WPO WooCommerce PDF Invoices and Packaging Slips
	 */
	protected function thirdparty_wpo_wcpdf_init() {
		// Patch by OpenTools implements the generic interface, so nothing special needs to be done, except to include hints in the plugin config
// 		add_filter ('wpo_wcpdf_invoice_number', array($this, 'thirdparty_wpo_wcpdf_invoice_number'), 30, 4);
// 		add_action ('wpo_wcpdf_process_template_order', array($this, 'thirdparty_wpo_wcpdf_create_number'), 10, 2);

		// Disable the invoice number-related controls in the config of the other plugin
		add_action ('woocommerce_page_wpo_wcpdf_options_page', array($this, 'thirdparty_wpo_wcpdf_remove_options'));
		// If this plugin is enabled, but invoice numbers are not, display an information message and a link to the config
		add_action ('woocommerce_page_wpo_wcpdf_options_page', array($this, 'thirdparty_wpo_wcpdf_configuration_link'));
	}
	 
	/**
	 * Support for WPO WooCommere PDF Invoices and Packaging Slips
	 * Filter to return the invoice number => simply return the first argument unchanged (was already 
	 * created in the correct format, no need to format it now again) 
	 */
	function thirdparty_wpo_wcpdf_invoice_number($invoice_number, $order_number, $order_id, $order_date) {
		if ($this->invoicenumbers_activated() ) {
			$_of = new WC_Order_Factory();  
			$order = $_of->get_order($order_id);
			$nr = $this->get_or_create_number($order_id, $order, 'invoice');
			if ($nr == $order_id) {
				// No number was found, so the default is the order id => reset to invoice number
				return $invoice_number;
			} else {
				return $nr;
			}
		} else {
			return $invoice_number;
		}
	}
	/**
	 * The action to actually create the number and store it as post meta with the order 
	 */
	function thirdparty_wpo_wcpdf_create_number($type, $orderid) {
		if ($type=='invoice' && $this->invoicenumbers_activated() ) {
			$_of = new WC_Order_Factory();  
			$order = $_of->get_order($orderid);
			$number = $this->get_or_create_number($orderid, $order, $type);
			// TODO: Store the invoice number counter in _wcpdf_invoice_number and the custom invoice 
			// number in the opentools meta, because the plugin assumes the number to be numeric...
			update_post_meta( $orderid, '_wcpdf_invoice_number', $number );
		}
	}
	
	/** 
	 * The action that is called for the WPO WCPDF invoice plugin only, when the options page is loaded.
	 * If this plugin is enabled and invoice numbers are configured, we simply remove all invoice number-specific
	 * settings, because this plugin will be responsible....
	 */
	function thirdparty_wpo_wcpdf_remove_options() {
		global $wp_settings_fields;
		if ($this->invoicenumbers_activated()) {
			$wp_settings_fields['wpo_wcpdf_template_settings']['invoice']['display_number']['title'] = $this->helper->__('Display invoice number');
			$wp_settings_fields['wpo_wcpdf_template_settings']['invoice']['display_number']['args']['description'] = $this->helper->__('The <a href="admin.php?page=wc-settings&tab=checkout&section=ordernumber">Open Tools Ordernumber plugin</a> has invoice numbers enabled and will generate invoice numbers for this plugin.' );
			unset($wp_settings_fields['wpo_wcpdf_template_settings']['invoice']['next_invoice_number']);
			unset($wp_settings_fields['wpo_wcpdf_template_settings']['invoice']['invoice_number_formatting']);
			unset($wp_settings_fields['wpo_wcpdf_template_settings']['invoice']['yearly_reset_invoice_number']);
		} else {
			$wp_settings_fields['wpo_wcpdf_template_settings']['invoice']['display_number']['args']['description'] = $this->helper->__('To let the Open Tools ordernumber plugin create invoice numbers with your desired format, please enable invoices in <a href="admin.php?page=wc-settings&tab=checkout&section=ordernumber">that plugin\'s configuration page</a>.' );
		}
	}

	/**
	 * If this plugin is installed, but not configured for invoice numbers, 
	 * Display an information message about this plugin and a link to its config.
	 */
	function thirdparty_wpo_wcpdf_configuration_link() {
		global $wp_settings_fields;
		if (!$this->invoicenumbers_activated()) {
			$wp_settings_fields['wpo_wcpdf_template_settings']['invoice']['display_number']['args']['description'] = '<i>' . sprintf($this->helper->__( 'The Open Tools Ordernumbers plugin is installed, but not configured to create invoice numbers. Configure it <a href="%s">here</a> to create invoice numbers.'), $this->invoicenumbers_config_link()) . '</i>';
		}
	}



	/** ************************************************************
	 *  Support for WooCommerce PDF Invoice (woocommerce-pdf-invoice) by WooThemes
	 ** ************************************************************
	 */
	 
	protected function thirdparty_wt_wc_pdf_invoice_init() {
		// Patch by OpenTools implements the generic interface, so nothing special needs to be done
	}



	/** ************************************************************
	 *  Support for WooCommerce PDF Invoices (woocommerce-pdf-invoices) by Bas Elbers
	 ** ************************************************************
	 */
	 
	protected function thirdparty_bewpi_invoices_init() {
		// The plugin has code itself to hide the counter settings when a third-party plugin is enabled
		// Patch by OpenTools implements the generic interface, so nothing special needs to be done
	}


	/** ************************************************************
	 *  Support for YITH WooCommerce PDF Invoice (yith-woocommerce-pdf-invoice)
	 ** ************************************************************
	 */
	 
	protected function thirdparty_yith_pdf_invoice_init() {
		// Patch by OpenTools implements the generic interface, so nothing special needs to be done
	}

	/** ************************************************************
	 *  Support for WooCommerce Print Invoice and Delivery Notes (woocommerce-delivery-notes) by Triggvy Gunderson
	 ** ************************************************************
	 */
	 
	protected function thirdparty_wc_delivery_notes_init() {
		// Patch by OpenTools implements the generic interface, so nothing special needs to be done
		// Setting use WP Settings API, remove the counter-specific settings
		add_filter ('wcdn_get_settings_no_section', array($this, 'thirdparty_wc_delivery_notes_configuration_link'));
	}

	/**
	 * If this plugin is installed, but not configured for invoice numbers, 
	 * Display an information message about this plugin and a link to its config.
	 */
	function thirdparty_wc_delivery_notes_configuration_link($settings) {
		if (!$this->invoicenumbers_activated()) {
			$settings['invoice_options']['desc'] = '<i>' . sprintf($this->helper->__( 'The Open Tools Ordernumbers plugin is installed, but not configured to create invoice numbers. Configure it <a href="%s">here</a> to create invoice numbers.'), $this->invoicenumbers_config_link()) . '</i>';
		}
		return $settings;
	}

// END THIRD-PARTY PLUGIN SUPPORT

}
