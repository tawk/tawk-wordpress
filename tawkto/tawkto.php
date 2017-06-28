<?php
/*
Plugin Name: Tawk.to Live Chat
Plugin URI: https://tawk.to
Description: Embeds Tawk.to live chat widget to your site
Version: 0.3.2
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
				'display_on_producttag' => 0
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
			        <p><?php _e( 'You might need to clear cache if your using a cache plugin to see your udpates', 'tawk-to-live-chat' ); ?></p>
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

			$input['always_display'] = ($input['always_display'] != '1')? 0 : 1;
			$input['show_onfrontpage'] = ($input['show_onfrontpage'] != '1')? 0 : 1;
			$input['show_oncategory'] = ($input['show_oncategory'] != '1')? 0 : 1;
			$input['show_ontagpage'] = ($input['show_ontagpage'] != '1')? 0 : 1;
			$input['show_onarticlepages'] = ($input['show_onarticlepages'] != '1')? 0 : 1;
			$input['exclude_url'] = ($input['exclude_url'] != '1')? 0 : 1;
			$input['excluded_url_list'] = sanitize_text_field($input['excluded_url_list']);
			$input['include_url'] = ($input['include_url'] != '1')? 0 : 1;
			$input['included_url_list'] = sanitize_text_field($input['included_url_list']);
			$input['display_on_shop'] = ($input['display_on_shop'] != '1')? 0 : 1;
			$input['display_on_productcategory'] = ($input['display_on_productcategory'] != '1')? 0 : 1;
			$input['display_on_productpage'] = ($input['display_on_productpage'] != '1')? 0 : 1;
			$input['display_on_producttag'] = ($input['display_on_producttag'] != '1')? 0 : 1;

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
					.'&transparentBackground=1';


			include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
		}

		public static function ids_are_correct($page_id, $widget_id) {
			return preg_match('/^[0-9A-Fa-f]{24}$/', $page_id) === 1 && preg_match('/^[a-z0-9]{1,50}$/i', $widget_id) === 1;
		}
	}
}

if(!class_exists('TawkTo')){
	class TawkTo{
		public function __construct(){
			$tawkto_settings = new TawkTo_Settings();
			add_shortcode( 'tawkto', array($this,'shortcode_print_embed_code') );

		}

		public function check_if_user_logged_in()
		{
	        if ( is_user_logged_in() ){
	        	$current_user = wp_get_current_user();
	        	$current_user_name = $current_user->user_firstname . ' '.$current_user->user_lastname;
	        	$current_user_email = $current_user->user_email;
	        }
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
				'display_on_producttag' => 0
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
			add_action('wp_footer',  array($this, 'embed_code'));
		}

		public function embed_code()
		{
			$page_id = get_option('tawkto-embed-widget-page-id');
			$widget_id = get_option('tawkto-embed-widget-widget-id');
			$user_js = '';

			global $current_user;
	        	$current_user = wp_get_current_user();
	        	$current_user_name = $current_user->user_firstname . ' '.$current_user->user_lastname;
	        	$current_user_email = $current_user->user_email;
	        	$user_js = '
					Tawk_API.visitor = {
					    name  : "'.$current_user_name.'",
					    email : "'. $current_user_email.'"
					};
				';
				
			
			if(!empty($page_id) && !empty($widget_id))
			{
				include(sprintf("%s/templates/widget.php", dirname(__FILE__)));
			}
		}

		public function print_embed_code()
		{
			$vsibility = get_option( 'tawkto-visibility-options' );
			
			$display = FALSE;

			if(($vsibility['show_onfrontpage'] == 1) && (is_home() || is_front_page()) ){ $display = TRUE; }
			if(($vsibility['show_oncategory'] == 1) && is_category() ){ $display = TRUE; }
			if(($vsibility['show_ontagpage'] == 1) && is_tag() ){ $display = TRUE; }
			if($vsibility['always_display'] == 1){ $display = TRUE; }
			if(($vsibility['show_onarticlepages'] == 1) && is_single() ){ $display = TRUE; }

			if(($vsibility['exclude_url'] == 1)){
				$excluded_url_list = $vsibility['excluded_url_list'];

				$current_url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				$current_url = urldecode($current_url);

				$ssl      = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' );
			    $sp       = strtolower( $_SERVER['SERVER_PROTOCOL'] );
			    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );

			    $current_url = $protocol.'://'.$current_url;
			    $current_url = strtolower($current_url);

				$excluded_url_list = preg_split("/,/", $excluded_url_list);
				foreach($excluded_url_list as $exclude_url)
				{
					$exclude_url = strtolower(urldecode(trim($exclude_url)));
					if(!empty($exclude_url))
					{
						if (strpos($current_url, $exclude_url) !== false) 
						{
							if(strcmp($current_url, $exclude_url) === 0)
							{
								$display = false;
							}
						}
					}
				}
			}

			if(isset($vsibility['include_url']) && $vsibility['include_url'] == 1){
				$included_url_list = $vsibility['included_url_list'];
				$current_url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				$current_url = urldecode($current_url);

				$ssl      = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' );
			    $sp       = strtolower( $_SERVER['SERVER_PROTOCOL'] );
			    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );

			    $current_url = $protocol.'://'.$current_url;
			    $current_url = strtolower($current_url);

				$included_url_list = preg_split("/,/", $included_url_list);
				foreach($included_url_list as $include_url)
				{
					$include_url = strtolower(urldecode(trim($include_url)));
					if(!empty($include_url))
					{
						if (strpos($current_url, $include_url) !== false) 
						{
							if(strcmp($current_url, $include_url) === 0)
							{
								$display = TRUE;
							}
						}
					}
				}
			}

			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
   			{
				if(($vsibility['display_on_shop'] == 1) && is_shop() ){ $display = TRUE; }
				if(($vsibility['display_on_productcategory'] == 1) && is_product_category() ){ $display = TRUE; }
				if(($vsibility['display_on_productpage'] == 1) && is_product() ){ $display = TRUE; }
				if(($vsibility['display_on_producttag'] == 1) && is_product_tag() ){ $display = TRUE; }
			}

			if($display == TRUE)
			{
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