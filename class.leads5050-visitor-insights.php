<?php
/**
 * Project: leads5050-visitor-insights [class.leads5050-visitor-insights.php]
 * Description: Main class for the Leads5050 Visitor Insights Plugin
 * potential leads and new customers
 * Author: Clinton [Leads5050]
 * License: GPLv3
 * Copyright 2020 CreatorSEO (email : info@creatorseo.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You can find a copy of the GNU General Public License at the link
 * http://www.gnu.org/licenses/gpl.html or write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */


/* Leads5050 (class.leads5050-visitor-insights.php) v.1.0 */
//require_once( LREFI_DIR . 'inc/leads5050-shortcodes.php' );
require_once( LREFI_DIR . 'inc/leads5050-ajax-functions.php' );
require_once( LREFI_DIR .'inc/leads5050-function-lib.php');

class leads5050 {
	protected $pluginloc;
	protected $homeInfo = array(); //scheme, domain etc.
	protected $logfile = array(
		'LOGFILE' => array('2020-01-01 00:00:00'=>'Activate 123')
	);
	protected $options = array(
		'domain' => '',
		'license' => '',
		'switch' => 'ON'
	);

	/**
	 * Initialise the plugin class
	 * @param string $loc the full directory and filename for the plugin
	 */
	public function __construct($loc) {
		$this->pluginloc = strlen($loc)? $loc: __FILE__;
		$basename = plugin_basename($this->pluginloc);
		$this->homeInfo = wp_parse_url(home_url());
		$this->options['domain'] = home_url();
		if (is_admin()){
			add_action('admin_enqueue_scripts', array($this, 'leads5050_enqueue_main'));
			add_action('admin_init',array($this, 'leads5050_register_settings'));
			add_action('admin_menu', array($this, 'leads5050_admin_menu'));
			add_filter('plugin_action_links_'.$basename, array($this, 'leads5050_settings_link'));
			//manage the stored variable and option values when registering or deactivating
			register_activation_hook($loc, array($this, 'leads5050_load_options' ));
			register_deactivation_hook($loc, array($this, 'leads5050_unset_options' ));
			register_uninstall_hook ($loc, array($this, 'leads5050_uninstall'));
		} else {
			wp_enqueue_script('leads5050-footer-script', $this->leads5050_enqueue_script(), '', null, true);
		}
		//Load a function that runs after all plugins are registered to ensure so that all plugin filters and actions are defined
		add_action('plugins_loaded', array($this, 'leads5050_late_loader'));
		//----- Ranking AJAX Menu Actions -----
		//the 'leads5050_set_license' in the lines below refers to the action defined in the js AJAX file
		add_action( 'wp_ajax_nopriv_leads5050_set_license', array($this, 'leads5050_set_license'));
		add_action( 'wp_ajax_leads5050_set_license', array($this, 'leads5050_set_license'));
		add_action( 'wp_ajax_leads5050_visit_report', 'leads5050_visit_report');
	}

	// -------------------- Add styles and scripts --------------------
	/**
	 * @param $hook - the admin_enqueue_scripts action provides the $hook_suffix for the current admin page.
	 * This is used to load the scripts only for the admin pages associated with the plugin
	 * HOOK: "toplevel_page_leads5050-code"
	 */
	function leads5050_enqueue_main($hook){
		//only run the script if the user is not logged in
		wp_enqueue_style('leads5050-css', plugins_url('css/leads5050.css', __FILE__));
		if (stristr($hook, 'leads5050')) {
			wp_enqueue_script('leads5050-js', plugins_url(('js/leads5050-admin.js?x='.rand(5,300)), __FILE__), array('jquery'), '1.0', true);
			$the_domain = isset($this->options['domain']) && strlen($this->options['domain'])? $this->options['domain']: home_url();
			wp_localize_script('leads5050-js', 'varzl', array('ajax_url' => admin_url('admin-ajax.php'), 'hash'=>base64_encode($the_domain)));
		}
	}

	function leads5050_enqueue_script(){
		$code = get_option('leads5050_code', '');
		$options = get_option('leads5050_options');
		$my_code = '';
		if ( isset($code) && strlen($code)==5 && $options['switch']=='ON' ){
			$my_code = esc_url(trim('https://repixa.com/js/tagReferer.js?api='.esc_html($code).''));
		}
		return ($my_code);
	}

	/**
	 * Late loading function for actions that runs after all plugins are loaded
	 */
	function leads5050_late_loader(){

	}

	// -------------------- Options and Variables - Admin Settings Form Definition --------------------
	function leads5050_admin_menu() {
		add_management_page('Leads5050 Insights', 'Leads5050 Insights', 'manage_options', 'leads5050-insights',
			array($this, 'leads5050_options_page'));
	}

	/**
	 * @param $links - When the 'plugin_action_links_(plugin file name)' filter is called, it is passed one parameter:
	 * namely the links to show on the plugins overview page in an array.
	 *
	 * @return mixed
	 */
	function leads5050_settings_link($links) {
		$url = get_admin_url().'tools.php?page=leads5050-insights';
		$settings_link = '<a href="'.$url.'">' . __("Settings") . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	function leads5050_register_settings() {
		register_setting('leads5050_group', 'leads5050_options', array($this, 'leads5050_validate'));
	}

	/**
	 * Validate and transform the values submitted to the options form
	 * @param array $input - options results from the form submission
	 * @return array|false - validated and transformed options results
	 */
	function leads5050_validate($input){
		//enter validation and transformation here
		$url_parse = wp_parse_url($input['domain']);
		$output['domain'] = $url_parse['scheme'].'://'.$url_parse['host'];
		if ( filter_var($output['domain'], FILTER_VALIDATE_URL) && strlen($input['license'])==5 ){
			$output['license'] = $input['license'];
			$output['switch'] = in_array($input['switch'], array("ON", "OFF"))? $input['switch']: "OFF";
		} else {
			$output = array('domain'=>'', 'license'=>'00000', 'switch'=>"OFF");
		}
		return $output;
	}

	function leads5050_options_page() {
		$options = get_option('leads5050_options');
		$options = is_array($options)? array_merge($this->options,$options): $this->options;
		$my_domain = (!is_null($options['domain']) && strlen($options['domain']))? $options['domain'] : home_url();
		$my_domain = substr($my_domain,0,4)=='http'? $my_domain: 'http://'.$my_domain; //assume http if not supplied
		$my_api_number = get_option('leads5050_code', '');
		if(current_user_can('manage_options')) {
			echo "<div class='wrap'>";
				//echo "<h3>Options</h3><pre>".var_export($options,true)."</pre>";
				echo "<h2>".esc_html( get_admin_page_title() )."</h2>";
				echo "<h3>Setup / Settings</h3>";
				echo "<div><button id='leads5050_get_api_btn' class='button button-secondary'>CLICK HERE to Start</button></div>";
				echo "<div id='leads5050-main-api-form'>";
					//allow settings notification
					settings_errors();
					echo "<form action='options.php' method='post'>";
						settings_fields('leads5050_group'); //This line must be inside the form tags!!
						//do_settings_fields('leads5050_group');
						echo "<table class='form-table'>";
							echo "<tr valign='top' class='leads5050-input-hdr'><th colspan='3'>GENERAL SETTINGS</th></tr>";
							echo "<tr valign='top'><th scope='row'>DOMAIN</th>";
							echo "<td>".esc_url($my_domain)."<input type='hidden' name='leads5050_options[domain]' value='".esc_url($my_domain)."' /></td>";
							echo '<td><a href="#" class="leads5050_link"><span class="dashicons dashicons-editor-help"></span></a>
			            		<div class="leads5050_tooltip">Your domain - this was selected for you</div></td></tr>';
							echo "<tr valign='top'><th scope='row'>LICENSE</th>";
							echo "<td><span id='leads5050-api-key-value'>".esc_html($my_api_number)."</span>";
							echo "<input id='leads5050_form_api_value' name='leads5050_options[license]' type='hidden' value='".esc_html($my_api_number)."' /> ";
							echo "<span id='leads5050_btn_confirm'></span>";
							echo '<td><a href="#" class="leads5050_link"><span class="dashicons dashicons-editor-help"></span></a>
			            		<div class="leads5050_tooltip">Your 5 digit licence assigned to your domain - this was created using the button above</div></td></tr>';
							$control = leads5050_dynamic_options(array("ON", "OFF"),'leads5050_options[switch]',esc_attr($options['switch']), '',false);
							echo "<tr valign='top'><th scope='row'>Tracking</th><td>".$control."</td>";
							echo '<td><a href="#" class="leads5050_link"><span class="dashicons dashicons-editor-help"></span></a>
			            		<div class="leads5050_tooltip">Default is ON for visitors to be tracked</div></td></tr>';
						echo "</table>";
						submit_button();
					echo "</form>";
					echo "<div id='leads5050-visitor-info'>";
						echo "<table class='form-table'>";
							echo "<tr valign='top' class='leads5050-input-hdr'><th>VISIT INFORMATION</th></tr>";
							echo "<tr valign='top'><td><span id='leads5050_visit_container'>Fetching data....</span></td></tr>";
						echo "</table>";
					echo "</div>";
				echo "</div>";
			echo "</div><hr />";
		} else {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		//$options = get_option('leads5050_options');
	}

	// -------------------- AJAX Call Function --------------------
	/**
	 * The AJAX call function is defined below
	 * This function is defined by
	 * 		add_action( 'wp_ajax_nopriv_leads5050_set_license', array($this, 'leads5050_set_license'));
	 *		add_action( 'wp_ajax_leads5050_set_license', array($this, 'leads5050_set_license'));
	 * as the ajax program to run for visitors and administrators respectively
	 */
	function leads5050_set_license() {
		$jsn['error'] = '';
		$jsn['api'] = '';
		$jsn['success'] = false;
		if (isset($_POST['api_license']) && strlen($_POST['api_license'])==5){
			$jsn['api'] = sanitize_text_field($_POST['api_license']);
			if ( update_option( 'leads5050_code', $jsn['api'] ) === false ) {
				$jsn['error'] = 'WARNING: API value was not changed';
			} else {
				$options  = get_option( 'leads5050_options' );
				$options['license'] = $jsn['api'];
				if ( update_option( 'leads5050_options', $options ) === false ) {
					$jsn['error'] = 'WARNING: Option value was not changed';
				} else {
					$jsn['success'] = true;
//					leads5050_update_log_file( 'API Records', 'Updated - '.$jsn['api']);
				}
			}
		} else {
			$jsn['error'] = 'ERROR: API value not received or incorrect';
		}
		$status = 'API: '.(strlen($jsn['api'])? $jsn['api']: 'Not Set');
		$status .= strlen($jsn['error'])? ' | '.$jsn['error']: '';
		$status .= $jsn['success']? ' | Licence Created Successfully': '';
		leads5050_update_log_file( 'LICENSE CREATE OR REFRESH' , $status, 'append', true, 3 );
		return $jsn['success'];
	}


	// -------------------- Define actions to be taken when installing and uninstalling the Plugin --------------------
	function leads5050_load_options() {
		add_option('leads5050_options', $this->options);
		add_option('leads5050_log', $this->logfile);
		add_option('leads5050_code', '');
	}

	function leads5050_unset_options() {
//		delete_option('leads5050_options');
//		delete_option('leads5050_log');
//		delete_option('leads5050_code');
	}

	function leads5050_uninstall() {
		delete_option('leads5050_options');
		delete_option('leads5050_log');
		delete_option('leads5050_code');
	}
}
