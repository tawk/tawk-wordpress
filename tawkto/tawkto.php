<?php
/*
Plugin Name: Tawk.to Live Chat
Plugin URI: https://www.tawk.to
Description: Embeds Tawk.to live chat widget to your site
Version: 0.5.4
Author: Tawkto
Text Domain: tawk-to-live-chat
*/
if(!class_exists('TawkTo_Settings')){

	class TawkTo_Settings{
		const TAWK_WIDGET_ID_VARIABLE = 'tawkto-embed-widget-widget-id';
		const TAWK_PAGE_ID_VARIABLE = 'tawkto-embed-widget-page-id';
		const TAWK_VISIBILITY_OPTIONS = 'tawkto-visibility-options';

		public function __construct(){

			if(!get_option('tawkto-visibility-options',false))
			{
			$visibility = array (
				'always_display' => 1,
				'show_onfrontpage' => 0,
				'show_oncategory' => 0,
				'show_ontagpage' => 0,
				'show_onarticlepages' => 0,
				'exclude_url' => 0,
				'excluded_url_list' => '',
				'include_url' => 0,
				'included_url_list' => '',
				'display_on_shop' => 0,
				'display_on_productcategory' => 0,
				'display_on_productpage' => 0,
				'display_on_producttag' => 0,
				'enable_visitor_recognition' => 1
			);
			update_option( 'tawkto-visibility-options', $visibility);
			}

			add_action('admin_init', array(&$this, 'admin_init'));
			add_action('admin_menu', array(&$this, 'add_menu'));
			add_action('wp_ajax_tawkto_setwidget',  array(&$this, 'action_setwidget'));
			add_action('wp_ajax_tawkto_removewidget',  array(&$this, 'action_removewidget'));
			#add_action('admin_head', array(&$this,'tawk_custom_admin_style') );

			add_action('admin_enqueue_scripts', array($this,'tawk_settings_assets') );
			add_action( 'admin_notices', array($this,'tawk_admin_notice') );
		}

		public function tawk_settings_assets($hook)
		{
			if($hook != 'settings_page_tawkto_plugin')
				return;

			wp_register_style( 'tawk_admin_style', plugins_url( 'assets/tawk.admin.css' , __FILE__ ) );
        	wp_enqueue_style( 'tawk_admin_style' );

        	wp_enqueue_script( 'tawk_admin_script', plugins_url( 'assets/tawk.admin.js' , __FILE__ ) );

		}

		public function admin_init(){
			register_setting( 'tawk_options', 'tawkto-visibility-options', array(&$this,'validate_options') );
		}

		public function action_setwidget() {
			header('Content-Type: application/json');

			if (!isset($_POST['pageId']) || !isset($_POST['widgetId'])) {
				echo json_encode(array('success' => FALSE));
				die();
			}

			if (!self::ids_are_correct($_POST['pageId'], $_POST['widgetId'])) {
				echo json_encode(array('success' => FALSE));
				die();
			}

			update_option(self::TAWK_PAGE_ID_VARIABLE, $_POST['pageId']);
			update_option(self::TAWK_WIDGET_ID_VARIABLE, $_POST['widgetId']);


			echo json_encode(array('success' => TRUE));
			die();
		}

		function tawk_admin_notice() {

			if( isset($_GET["settings-updated"]) )
			{
				?>
				<div class="notice notice-warning is-dismissible">
					<p><?php _e( 'You might need to clear cache if your using a cache plugin to see your updates', 'tawk-to-live-chat' ); ?></p>
				</div>
				<?php
			}
		}

		public function action_removewidget() {
			header('Content-Type: application/json');

			update_option(self::TAWK_PAGE_ID_VARIABLE, '');
			update_option(self::TAWK_WIDGET_ID_VARIABLE, '');

			echo json_encode(array('success' => TRUE));
			die();
		}

		public function validate_options($input){
			$visibility_toggle_fields = array(
				'always_display',
				'show_onfrontpage',
				'show_oncategory',
				'show_ontagpage',
				'show_onarticlepages',
				'exclude_url',
				'include_url',
				'display_on_shop',
				'display_on_productcategory',
				'display_on_productpage',
				'display_on_producttag',
				'enable_visitor_recognition'
			);
			$visibility_text_fields = array('excluded_url_list', 'included_url_list');

			self::validate_visibility_toggle_fields($input, $visibility_toggle_fields);
			self::validate_text_fields($input, $visibility_text_fields);

			return $input;
		}

		public function add_menu(){
			add_options_page(
				__('Tawk.to Settings','tawk-to-live-chat'),
				__('Tawk.to','tawk-to-live-chat'),
				'manage_options',
				'tawkto_plugin',
				array(&$this, 'create_plugin_settings_page')
			);
		}

		public function tawk_custom_admin_style(){
			echo '<style>
				    .form-table th.tawksetting {
				      width: 350px;
				    }
				    .tawknotice{
				    	font-size:14px;
				    }
				  </style>';
		}

		public function create_plugin_settings_page(){

			global $wpdb;

			if(!current_user_can('manage_options'))	{
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}

			$page_id = get_option(self::TAWK_PAGE_ID_VARIABLE);
			$widget_id = get_option(self::TAWK_WIDGET_ID_VARIABLE);
			$base_url = 'https://plugins.tawk.to';

			$iframe_url = $base_url.'/generic/widgets'
					.'?currentWidgetId='.$widget_id
					.'&currentPageId='.$page_id
					.'&transparentBackground=1'
					.'&pltf=wordpress';


			include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
		}

		public static function ids_are_correct($page_id, $widget_id) {
			return preg_match('/^[0-9A-Fa-f]{24}$/', $page_id) === 1 && preg_match('/^[a-z0-9]{1,50}$/i', $widget_id) === 1;
		}

		private static function validate_text_fields(&$fields, $field_names) {
			foreach ($field_names as $field_name) {
				if (isset($fields[$field_name])) {
					$fields[$field_name] = sanitize_text_field($fields[$field_name]);
					continue;
				}

				$fields[$field_name] = '';
			}

			return;
		}

		private static function validate_visibility_toggle_fields(&$fields, $field_names) {
			foreach ($field_names as $field_name) {
				if (isset($fields[$field_name]) && $fields[$field_name] == '1') {
					$fields[$field_name] = 1;
					continue;
				}

				$fields[$field_name] = 0;
			}

			return;
		}
	}
}

if(!class_exists('TawkTo')){
	class TawkTo{
		public function __construct(){
			$tawkto_settings = new TawkTo_Settings();
			add_shortcode( 'tawkto', array($this,'shortcode_print_embed_code') );

		}

		public static function activate(){

			$visibility = array (
				'always_display' => 1,
				'show_onfrontpage' => 0,
				'show_oncategory' => 0,
				'show_ontagpage' => 0,
				'show_onarticlepages' => 0,
				'exclude_url' => 0,
				'excluded_url_list' => '',
				'include_url' => 0,
				'included_url_list' => '',
				'display_on_shop' => 0,
				'display_on_productcategory' => 0,
				'display_on_productpage' => 0,
				'display_on_producttag' => 0,
				'enable_visitor_recognition' => 1
			);

			add_option(TawkTo_Settings::TAWK_PAGE_ID_VARIABLE, '', '', 'yes');
			add_option(TawkTo_Settings::TAWK_WIDGET_ID_VARIABLE, '', '', 'yes');
			add_option(TawkTo_Settings::TAWK_VISIBILITY_OPTIONS, $visibility, '', 'yes');
		}

		public static function deactivate(){
			delete_option(TawkTo_Settings::TAWK_PAGE_ID_VARIABLE);
			delete_option(TawkTo_Settings::TAWK_WIDGET_ID_VARIABLE);
			delete_option(TawkTo_Settings::TAWK_VISIBILITY_OPTIONS);
		}

		public function shortcode_print_embed_code(){
			add_action('wp_footer',  array($this, 'embed_code'),100);
		}

		public function getCurrentCustomerDetails () {
			if(is_user_logged_in() ){
				$current_user = wp_get_current_user();
				$user_info = array(
					'name' => $current_user->display_name,
					'email' => $current_user->user_email
				);
				return json_encode($user_info);
			}
			return NULL;
		}

		public function embed_code() {
			$page_id = get_option('tawkto-embed-widget-page-id');
			$widget_id = get_option('tawkto-embed-widget-widget-id');
			$visibility = get_option('tawkto-visibility-options');

			$enable_visitor_recognition = true; // default value

			if (isset($visibility) && isset($visibility['enable_visitor_recognition'])) {
				$enable_visitor_recognition = $visibility['enable_visitor_recognition'] == 1;
			}

			if ($enable_visitor_recognition) {
				$customer_details = $this->getCurrentCustomerDetails();
			}

			if (!empty($page_id) && !empty($widget_id)) {
				include(sprintf("%s/templates/widget.php", dirname(__FILE__)));
			}
		}

		private function get_current_url() {
			$current_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$current_url = urldecode($current_url);

			$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';

			return strtolower($protocol . $current_url);
		}

		private function match_url($url, $url_pattern) {
			// do partial match if wildcard character matched at the end pattern
			if (substr($url_pattern, -1) === '*') {
				$url_pattern = substr($url_pattern, 0, -1);

				return (strpos($url, $url_pattern) === 0);
			}

			// do extact match if wildcard character not matched at the end pattern
			return (strcmp($url, $url_pattern) === 0);
		}

		public function print_embed_code() {
			$vsibility = get_option('tawkto-visibility-options');
			$display = false;

			if ($vsibility['always_display'] == 1) {
				$display = true;
			}

			if (($vsibility['show_onfrontpage'] == 1) && (is_home() || is_front_page())) {
				$display = true;
			}

			if (($vsibility['show_oncategory'] == 1) && is_category()) {
				$display = true;
			}

			if (($vsibility['show_ontagpage'] == 1) && is_tag()) {
				$display = true;
			}

			if (($vsibility['show_onarticlepages'] == 1) && is_single()) {
				$display = true;
			}

			if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
				if (($vsibility['display_on_shop'] == 1) && is_shop()) {
					$display = true;
				}

				if (($vsibility['display_on_productcategory'] == 1) && is_product_category()) {
					$display = true;
				}

				if (($vsibility['display_on_productpage'] == 1) && is_product()) {
					$display = true;
				}

				if (($vsibility['display_on_producttag'] == 1) && is_product_tag()) {
					$display = true;
				}
			}

			if (isset($vsibility['include_url']) && $vsibility['include_url'] == 1) {
				$current_url = $this->get_current_url();

				$included_url_list = $vsibility['included_url_list'];
				$included_url_list = preg_split("/,/", $included_url_list);

				foreach ($included_url_list as $include_url) {
					$include_url = strtolower(urldecode(trim($include_url)));

					if (!empty($include_url) && $this->match_url($current_url, $include_url)) {
						$display = true;
					}
				}
			}

			if (isset($vsibility['exclude_url']) && ($vsibility['exclude_url'] == 1)) {
				$current_url = $this->get_current_url();

				$excluded_url_list = $vsibility['excluded_url_list'];
				$excluded_url_list = preg_split("/,/", $excluded_url_list);

				foreach ($excluded_url_list as $exclude_url) {
					$exclude_url = strtolower(urldecode(trim($exclude_url)));

					if (!empty($exclude_url) && $this->match_url($current_url, $exclude_url)) {
						$display = false;
					}
				}
			}

			if ($display) {
				$this->embed_code();
			}
		}

		/**
		 * migrate old tawk to embed code to new version.
		 *
		 * old version contained embed code script, from that
		 * markup we need only page id and widget id
		 */
		public function migrate_embed_code() {

			$old_tawkto_embed_code = get_option('tawkto-embed-code');

			if(empty($old_tawkto_embed_code)) {
				return;
			}

			$matches = array();
			preg_match('/https:\/\/embed.tawk.to\/([0-9A-Fa-f]{24})\/([a-z0-9]{1,50})/', $old_tawkto_embed_code, $matches);

			if(isset($matches[1]) && isset($matches[2]) && TawkTo_Settings::ids_are_correct($matches[1], $matches[2])) {
				update_option(TawkTo_Settings::TAWK_PAGE_ID_VARIABLE, $matches[1]);
				update_option(TawkTo_Settings::TAWK_WIDGET_ID_VARIABLE, $matches[2]);
			}

			delete_option('tawkto-embed-code');
		}
	}
}

if(class_exists('TawkTo')){
	register_activation_hook(__FILE__, array('TawkTo', 'activate'));
	register_deactivation_hook(__FILE__, array('TawkTo', 'deactivate'));

	$tawkto = new TawkTo();

	if(isset($tawkto)){
		$tawkto->migrate_embed_code();

		function tawkto_plugin_settings_link($links){
			$settings_link = '<a href="options-general.php?page=tawkto_plugin">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}

		$plugin = plugin_basename(__FILE__);
		add_filter("plugin_action_links_$plugin", 'tawkto_plugin_settings_link');
	}

	add_action('wp_footer',  array($tawkto, 'print_embed_code'));
}
