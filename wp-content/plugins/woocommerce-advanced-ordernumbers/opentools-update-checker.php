<?php
/**
 * OpenTools Plugin Update Checker Library
 * 
 * Copyright 2016 Reinhold Kainhofer
 * Extends the plugin-update-checker by Janis Elsts
 * Released under the MIT license. 
 */


// *****************************************************************
// * PLUGIN UPDATES (using plugin-update-checker and a self-written update server script)
// * http://w-shadow.com/blog/2010/09/02/automatic-updates-for-any-plugin/
// *****************************************************************

if (!class_exists('OpenToolsPluginUpdateChecker')):

require 'plugin-update-checker/plugin-update-checker.php';

class OpenToolsPluginUpdateChecker extends PluginUpdateChecker_2_1 {
	protected $credvars = array();
	protected $ajaxurl = '';
	public function __construct($metadataUrl, $pluginFile, $slug = '', $checkPeriod = 12, $optionName = '', $muPluginFile = '')
	{
		parent::__construct($metadataUrl, $pluginFile, $slug, $checkPeriod, $optionName, $muPluginFile);
// 		$this->debugMode = TRUE;
		$this->installOTHooks();
	}
	public function declareCredentials($credential_def) {
		$this->credvars = $credential_def;
		// Append the update credentials to the update server link
		$this->addQueryArgFilter(array($this, 'appendQueryArgsCredentials'));
		
	}
	
	protected function installOTHooks() 
	{
		$this->ajaxurl = is_network_admin()?network_admin_url( 'admin-ajax.php' ): admin_url( 'admin-ajax.php' );
		
		add_action('admin_print_scripts-plugins.php', array($this, 'addCredentialCheckScripts'));
		add_action('admin_print_styles-plugins.php',  array($this, 'addCredentialCheckStyles'));

// 		add_filter('plugin_row_meta', array($this, 'displayUpdateCredentialsLink'), 9, 2);
		add_filter('plugin_action_links_'.$this->pluginFile, array($this, 'displayUpdateCredentialsLink'), 9, 2);

		add_action( 'wp_ajax_getUpdateCredentialsRow_'.$this->slug,		array( &$this, 'getUpdateCredentialsRow') );
		add_action( 'wp_ajax_submitUpdateCredentials_'.$this->slug,		array( &$this, 'submitUpdateCredentials') );
	}

	protected function getCredentials($slug) 
	{
		$credentials = array('validated' => FALSE);
		foreach ($this->credvars as $credkey => $credname) {
			$credentials[$credkey] = get_option('otup_credentials_'.$slug.'_'.$credkey);
		}
		$credentials['validated']    = get_option('otup_credentials_validated_'.$slug);
		return $credentials;
	}
	
	protected function setCredentials($slug, $credentials, $validated = false) 
	{
		foreach ($credentials as $credkey => $credvalue) {
			update_option('otup_credentials_'.$slug.'_'.$credkey, $credvalue, false);
		}
		update_option('otup_credentials_validated_'.$slug,    $validated,   false);
	}


	public function addCredentialCheckScripts() {
		wp_register_script( 'opentools-updatecheck', plugins_url('assets/js/opentools-updatecheck.js', __FILE__), array('jquery'));
		wp_enqueue_script( 'opentools-updatecheck');
	}
	
	public function addCredentialCheckStyles() {
		wp_register_style( 'opentools-updatecheck', plugins_url('assets/css/opentools-updatecheck.css', __FILE__));
		wp_enqueue_style( 'opentools-updatecheck');
	}
	
	/** Append the ordernumber and order password to the update server URL 
	 */
	public function appendQueryArgsCredentials($queryArgs) {
		$credentials = $this->getCredentials($this->slug);
		foreach ($credentials as $credkey => $credvalue) {
			$queryArgs[$credkey] = $credvalue;
		}
		return $queryArgs;
	}

	/**
	 * Add a "Update Credentials" link to the plugin row in the "Plugins" page. By default,
	 * the new link will appear after the "Visit plugin site" link. 
	 *
	 * You can change the link text by using the "otup_enter_update_credentials-$slug" filter.
	 * Returning an empty string from the filter will disable the link.
	 *
	 * @param array $pluginMeta Array of meta links.
	 * @param string $pluginFile
	 * @return array
	 */
	public function displayUpdateCredentialsLink($links, $pluginFile) {
		$isRelevant = ($pluginFile == $this->pluginFile)
		              || (!empty($this->muPluginFile) && $pluginFile == $this->muPluginFile);
		$isRelevant = $isRelevant && !empty($this->credvars);

		if ( $isRelevant && current_user_can('update_plugins') ) {
			$credentials = $this->getCredentials($this->slug);
			$linkText = apply_filters('otup_enter_update_credentials-' . $this->slug, __('Update Credentials', 'oton-updates'));
			if ( !empty($linkText) ) {
				$iconyesno = $credentials['validated']?'yes':'no';
				$link = sprintf('<a href="#" onClick=\'return showUpdateCredentialsRow(this);\' class="dashicons-before dashicons-'.$iconyesno.' otup_credentials_link_'.$this->slug.'" data-slug="%s" data-nonce="%s" data-ajaxurl="%s" >%s</a>', esc_attr($this->slug), esc_attr(wp_create_nonce( 'otup_enter_update_credentials' )), esc_attr($this->ajaxurl), $linkText);
				array_unshift($links, $link);
			}
		}
		return $links;
	}
	
	
	/**
	* If the user has clicked on the "Update Credentials" link, display the input boxes after the plugin row.
	*
	* @param string $file
	* @param array  $plugin_data
	* @return false|void
	*/
	public function getUpdateCredentialsRow() {
		$json = array('row' => '', 'message'=>'Unsuccessful');
		if (isset($_REQUEST['slug']) && $_REQUEST['slug'] != $this->slug) {
			// This update checker is not responsible. Action is for another plugin
			return;
		}
		
		$showCredentials = isset($_REQUEST['slug'])
			&& $_REQUEST['slug'] == $this->slug
			&& current_user_can('update_plugins')
			&& check_ajax_referer('otup_enter_update_credentials')
			&& !empty($this->credvars);
		
		if ( $showCredentials && (is_network_admin() || !is_multisite() )) {
			$slug = $this->slug;
			if ( is_network_admin() ) {
				$active_class = is_plugin_active_for_network( $this->pluginFile ) ? 'active': '';
			} else {
				$active_class = is_plugin_active( $this->pluginFile ) ? 'active' : '';
			}
			
			$current_credentials = $this->getCredentials($slug);
			
			$tr = '<tr class="' . $active_class . ' otup_update_credentials" id="' . esc_attr( $slug . '-credentials' ) . '" >';
			$tr .= '<th colspan="3" class="check-column colspanchange">';
			$tr .= '<div class="update-credentials">';
			$tr .= '<div class="update-credentials-message">';
			$tr .= '</div>';
			$tr .= '<div class="update-credentials-form">';
			
			foreach ($this->credvars as $credkey => $credname) {
				$tr .= $credname    . " <input type=\"text\" name=\"otup_update_credentials[$slug][$credkey]\" value=\"" . esc_attr($current_credentials[$credkey]) . "\">&nbsp;&nbsp;&nbsp;";
			}

			$tr .= sprintf('<input type="submit" class="button otup_update_credentials_submit" onclick="return submitUpdateCredentials(this);" data-slug="%s" data-nonce="%s" data-ajaxurl="%s" data-credentialvars=\'%s\'>',
				esc_attr($this->slug),
				esc_attr(wp_create_nonce( 'otup_enter_update_credentials_'.$slug )),
				esc_attr($this->ajaxurl),
				esc_attr(json_encode(array_keys($this->credvars)))
			);

			$tr .= '</div>';
			$tr .= '</div></th></tr>';
			$json['row'] = $tr;
			$json['message'] = '';
		} else {
			$json['message'] = __("No permissions to modify update credentials", "opentools-updatecheck");
		}
		wp_send_json($json);
	}


	/**
	 * Check the submitted update credentials for correctness and save them
	 *
	 * @return void
	 */
	public function submitUpdateCredentials() {
		if (isset($_REQUEST['slug']) && $_REQUEST['slug'] != $this->slug) {
			// This update checker is not responsible. Action is for another plugin
			return;
		}
		
		$json = array('message' => '', 'success' => FALSE);
		$slug = isset($_REQUEST['slug'])?($_REQUEST['slug']):"INVALIDSLUG";

		$submitCredentials = ($slug == $this->slug)
			&& current_user_can('update_plugins')
			&& check_ajax_referer('otup_enter_update_credentials_'.$slug);
		$submitCredentials = $submitCredentials && !empty($this->credvars);

		if ( $submitCredentials ) {
			$credentials = array();
			foreach ($this->credvars as $credkey=>$credname) {
				if (isset($_REQUEST[$credkey])) {
					$credentials[$credkey] = $_REQUEST[$credkey];
				}
			}
			
			$message = "";
			$validated = $this->checkUpdateCredentials($credentials, $message);
			$this->setCredentials($this->slug, $credentials, $validated);
			
			$json['success'] = $validated;
			
			if ($validated) {
				if ( is_network_admin() ) {
					$active_class = is_plugin_active_for_network( $this->pluginFile ) ? 'active': '';
				} else {
					$active_class = is_plugin_active( $this->pluginFile ) ? 'active' : '';
				}

				$json['message'] .= __("Update credentials successfully validated and saved. Automatic updates will be provided.", "opentools-updatecheck");
			} else {
				$json['message'] = $message;
			}
		} else {
			$json['message'] = __("No permissions to modify update credentials", "opentools-updatecheck");
		}
		wp_send_json($json);
	}

	public function checkUpdateCredentials($credentials, &$message)
	{
		$this->setCredentials($this->slug, $credentials);
		$success = FALSE;
		$updateinfo = $this->requestInfo(array());
		if ($updateinfo && isset($updateinfo->download_url)) {
			$downloadurl = $updateinfo->download_url;
			$downloadurl = apply_filters('puc_check_download_query_args-'.$this->slug, $downloadurl);
		
			$headers = get_headers($downloadurl);
			list($version, $status_code, $msg) = explode(' ',$headers[0], 3);
		
			// Check the HTTP Status code
			$message = $msg;
			$success = ($status_code==200);
		} else {
			$message = __('Unable to access plugin download URL. Please check your credentials.');
			if ($this->debugMode) {
				$message .= "<pre>updateinfo: ".print_r($updateinfo,1)."</pre>";
			}
		}
		return $success;
	}
	
	public function addAccessCheckQueryArgFilter($callback){
		add_filter('puc_check_download_query_args-'.$this->slug, $callback);
	}

};

endif;



// *****************************************************************
