<?php
/**
 * @package Tawk.to Widget for WordPress
 * @copyright (C) 2014- Tawk.to
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * Plugin Name: Tawk.to Live Chat
 * Plugin URI: https://www.tawk.to
 * Description: Embeds Tawk.to live chat widget to your site
 * Version: 0.8.6
 * Author: Tawkto
 * Text Domain: tawk-to-live-chat
 * License: GPLv3
 **/

require_once dirname( __FILE__ ) . '/vendor/autoload.php';
require_once dirname( __FILE__ ) . '/upgrade.manager.php';

use Tawk\Modules\UrlPatternMatcher;

if ( ! class_exists( 'TawkTo_Settings' ) ) {
	/**
	 * Admin settings module for the tawk.to plugin
	 */
	class TawkTo_Settings {
		const TAWK_WIDGET_ID_VARIABLE   = 'tawkto-embed-widget-widget-id';
		const TAWK_PAGE_ID_VARIABLE     = 'tawkto-embed-widget-page-id';
		const TAWK_VISIBILITY_OPTIONS   = 'tawkto-visibility-options';
		const TAWK_ACTION_SET_WIDGET    = 'tawkto-set-widget';
		const TAWK_ACTION_REMOVE_WIDGET = 'tawkto-remove-widget';

		/**
		 * @var $plugin_ver Plugin version
		 */
		private $plugin_ver = '';

		/**
		 * __construct
		 *
		 * @return void
		 */
		public function __construct() {
			if ( ! get_option( 'tawkto-visibility-options', false ) ) {
				$visibility = array(
					'always_display'             => 1,
					'show_onfrontpage'           => 0,
					'show_oncategory'            => 0,
					'show_ontagpage'             => 0,
					'show_onarticlepages'        => 0,
					'exclude_url'                => 0,
					'excluded_url_list'          => '',
					'include_url'                => 0,
					'included_url_list'          => '',
					'display_on_shop'            => 0,
					'display_on_productcategory' => 0,
					'display_on_productpage'     => 0,
					'display_on_producttag'      => 0,
					'enable_visitor_recognition' => 1,
				);
				update_option( 'tawkto-visibility-options', $visibility );
			}

			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'admin_menu', array( &$this, 'add_menu' ) );
			add_action( 'wp_ajax_tawkto_setwidget', array( &$this, 'action_setwidget' ) );
			add_action( 'wp_ajax_tawkto_removewidget', array( &$this, 'action_removewidget' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'tawk_settings_assets' ) );
			add_action( 'admin_notices', array( $this, 'tawk_admin_notice' ) );

			if ( is_admin() ) {
				if ( false === function_exists( 'get_plugin_data' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}

				$plugin_data = get_plugin_data( __FILE__ );

				$this->plugin_ver = $plugin_data['Version'];
			}
		}

		/**
		 * Retrieves tawk.to admin settings assets
		 *
		 * @param  string $hook - hook name.
		 * @return void
		 */
		public function tawk_settings_assets( $hook ) {
			if ( 'settings_page_tawkto_plugin' !== $hook ) {
				return;
			}

			wp_register_style(
				'tawk_admin_style',
				plugins_url( 'assets/css/tawk.admin.css', __FILE__ ),
				array(),
				$this->plugin_ver
			);
			wp_enqueue_style( 'tawk_admin_style' );

			wp_enqueue_script(
				'tawk_admin_script',
				plugins_url( 'assets/js/tawk.admin.js', __FILE__ ),
				array(),
				$this->plugin_ver,
				true
			);

		}

		/**
		 * Initialize plugin settings
		 *
		 * @return void
		 */
		public function admin_init() {
			register_setting( 'tawk_options', 'tawkto-visibility-options', array( &$this, 'validate_options' ) );
		}

		/**
		 * Saves the selected property and widget to the database.
		 */
		public function action_setwidget() {
			header( 'Content-Type: application/json' );

			if ( false === wp_is_json_request() ) {
				$response['success'] = false;
				$response['message'] = 'Invalid request';
				wp_send_json( $response );
				wp_die();
			};

			$post_data = json_decode( file_get_contents( 'php://input' ), true );

			$response = array(
				'success' => true,
			);

			if ( false === $this->validate_request_auth( self::TAWK_ACTION_SET_WIDGET, $post_data ) ) {
				$response['success'] = false;
				$response['message'] = 'Unauthorized';
				wp_send_json( $response );
				wp_die();
			};

			if ( ! isset( $post_data['pageId'] ) || ! isset( $post_data['widgetId'] ) ) {
				$response['success'] = false;
				wp_send_json( $response );
				wp_die();
			}

			if ( ! self::ids_are_correct( $post_data['pageId'], $post_data['widgetId'] ) ) {
				$response['success'] = false;
				wp_send_json( $response );
				wp_die();
			}

			update_option( self::TAWK_PAGE_ID_VARIABLE, $post_data['pageId'] );
			update_option( self::TAWK_WIDGET_ID_VARIABLE, $post_data['widgetId'] );

			wp_send_json( $response );
			wp_die();
		}

		/**
		 * Plugin notice for tawk.to
		 */
		public function tawk_admin_notice() {
			$settings_updated = filter_input( INPUT_GET, 'settings-updated', FILTER_VALIDATE_BOOLEAN );
			if ( true === $settings_updated ) {
				?>
				<div class="notice notice-warning is-dismissible">
					<p><?php esc_html_e( 'You might need to clear cache if you are using a cache plugin to see your updates', 'tawk-to-live-chat' ); ?></p>
				</div>
				<?php
			}
		}

		/**
		 * Removes the selected property and widget from the database.
		 */
		public function action_removewidget() {
			header( 'Content-Type: application/json' );

			if ( false === wp_is_json_request() ) {
				$response['success'] = false;
				$response['message'] = 'Invalid request';
				wp_send_json( $response );
				wp_die();
			};

			$post_data = json_decode( file_get_contents( 'php://input' ), true );

			$response = array(
				'success' => true,
			);

			if ( false === $this->validate_request_auth( self::TAWK_ACTION_REMOVE_WIDGET, $post_data ) ) {
				$response['success'] = false;
				$response['message'] = 'Unauthorized';
				wp_send_json( $response );
				wp_die();
			};

			update_option( self::TAWK_PAGE_ID_VARIABLE, '' );
			update_option( self::TAWK_WIDGET_ID_VARIABLE, '' );

			wp_send_json( $response );
			wp_die();
		}

		/**
		 * Validates action requests auth
		 *
		 * @param  string $action - Action to be done.
		 * @param  array  $post_data - Parsed JSON payload for the action.
		 * @return boolean
		 */
		private function validate_request_auth( $action, $post_data = array() ) {
			if ( false === current_user_can( 'administrator' ) ) {
				return false;
			}

			if ( false === isset( $post_data['nonce'] ) ) {
				return false;
			}

			if ( false === wp_verify_nonce( $post_data['nonce'], $action ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Validates the selected visibility options
		 *
		 * @param  array $input - Visibility option fields.
		 * @return boolean
		 */
		public function validate_options( $input ) {
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
				'enable_visitor_recognition',
			);

			$visibility_text_fields = array(
				'excluded_url_list',
				'included_url_list',
			);

			self::validate_visibility_toggle_fields( $input, $visibility_toggle_fields );
			self::validate_text_fields( $input, $visibility_text_fields );

			return $input;
		}

		/**
		 * Adds the tawk.to plugin settings in the admin menu.
		 */
		public function add_menu() {
			add_options_page(
				__( 'Tawk.to Settings', 'tawk-to-live-chat' ),
				__( 'Tawk.to', 'tawk-to-live-chat' ),
				'manage_options',
				'tawkto_plugin',
				array( &$this, 'create_plugin_settings_page' )
			);
		}

		/**
		 * Initializes the plugin settings page.
		 */
		public function create_plugin_settings_page() {
			global $wpdb;

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html_e( 'You do not have sufficient permissions to access this page.' ) );
			}

			$page_id   = get_option( self::TAWK_PAGE_ID_VARIABLE );
			$widget_id = get_option( self::TAWK_WIDGET_ID_VARIABLE );
			$base_url  = 'https://plugins.tawk.to';

			$iframe_url = $base_url . '/generic/widgets'
				. '?currentWidgetId=' . $widget_id
				. '&currentPageId=' . $page_id
				. '&transparentBackground=1'
				. '&pltf=WordPress';

			$set_widget_nonce    = wp_create_nonce( self::TAWK_ACTION_SET_WIDGET );
			$remove_widget_nonce = wp_create_nonce( self::TAWK_ACTION_REMOVE_WIDGET );
			$plugin_ver          = $this->plugin_ver;

			include sprintf( '%s/templates/settings.php', dirname( __FILE__ ) );
		}


		/**
		 * Verifies if the provided property and widget ids are correct.
		 *
		 * @param  string $page_id - Property Id.
		 * @param  string $widget_id - Widget Id.
		 * @return boolean
		 */
		public static function ids_are_correct( $page_id, $widget_id ) {
			return 1 === preg_match( '/^[0-9A-Fa-f]{24}$/', $page_id ) && 1 === preg_match( '/^[a-z0-9]{1,50}$/i', $widget_id );
		}

		/**
		 * Validates and sanitizes text fields
		 *
		 * @param  array $fields - List of fields.
		 * @param  array $field_names - List of field names to be validated.
		 * @return void
		 */
		private static function validate_text_fields( &$fields, $field_names ) {
			foreach ( $field_names as $field_name ) {
				if ( isset( $fields[ $field_name ] ) ) {
					$fields[ $field_name ] = sanitize_text_field( $fields[ $field_name ] );
					continue;
				}

				$fields[ $field_name ] = '';
			}
		}

		/**
		 * Validates and sanitizes visibility toggle fields
		 *
		 * @param  array $fields - List of fields.
		 * @param  array $field_names - List of field names to be validated.
		 * @return void
		 */
		private static function validate_visibility_toggle_fields( &$fields, $field_names ) {
			foreach ( $field_names as $field_name ) {
				if ( isset( $fields[ $field_name ] ) && '1' === $fields[ $field_name ] ) {
					$fields[ $field_name ] = 1;
					continue;
				}

				$fields[ $field_name ] = 0;
			}
		}
	}
}

if ( ! class_exists( 'TawkTo' ) ) {

	$plugin_file_data = get_file_data(
		__FILE__,
		array(
			'Version' => 'Version',
		),
		'plugin'
	);

	/**
	 * Main tawk.to module
	 */
	class TawkTo {
		const PLUGIN_VERSION_VARIABLE = 'tawkto-version';

		/**
		 * @var $plugin_version Plugin version
		 */
		private static $plugin_version;

		/**
		 * __construct
		 *
		 * @return void
		 */
		public function __construct() {
			$tawkto_settings = new TawkTo_Settings();
			add_shortcode( 'tawkto', array( $this, 'shortcode_print_embed_code' ) );
		}

		/**
		 * Retrieves plugin version
		 *
		 * @return string plugin version
		 */
		public static function get_plugin_version() {
			if ( false === isset( self::$plugin_version ) ) {
				$plugin_file_data = get_file_data(
					__FILE__,
					array(
						'Version' => 'Version',
					),
					'plugin'
				);

				self::$plugin_version = $plugin_file_data['Version'];
			}

			return self::$plugin_version;
		}

		/**
		 * Initializes plugin data on activation.
		 */
		public static function activate() {
			global $plugin_file_data;

			$visibility = array(
				'always_display'             => 1,
				'show_onfrontpage'           => 0,
				'show_oncategory'            => 0,
				'show_ontagpage'             => 0,
				'show_onarticlepages'        => 0,
				'exclude_url'                => 0,
				'excluded_url_list'          => '',
				'include_url'                => 0,
				'included_url_list'          => '',
				'display_on_shop'            => 0,
				'display_on_productcategory' => 0,
				'display_on_productpage'     => 0,
				'display_on_producttag'      => 0,
				'enable_visitor_recognition' => 1,
			);

			add_option( TawkTo_Settings::TAWK_PAGE_ID_VARIABLE, '', '', 'yes' );
			add_option( TawkTo_Settings::TAWK_WIDGET_ID_VARIABLE, '', '', 'yes' );
			add_option( TawkTo_Settings::TAWK_VISIBILITY_OPTIONS, $visibility, '', 'yes' );
			add_option( self::PLUGIN_VERSION_VARIABLE, self::get_plugin_version(), '', 'yes' );
		}

		/**
		 * Cleans up plugin data on deactivation
		 */
		public static function deactivate() {
			delete_option( TawkTo_Settings::TAWK_PAGE_ID_VARIABLE );
			delete_option( TawkTo_Settings::TAWK_WIDGET_ID_VARIABLE );
			delete_option( TawkTo_Settings::TAWK_VISIBILITY_OPTIONS );
			delete_option( self::PLUGIN_VERSION_VARIABLE );
		}

		/**
		 * Shortcode for tawk.to to inject the embed code.
		 */
		public function shortcode_print_embed_code() {
			add_action( 'wp_footer', array( $this, 'embed_code' ), 100 );
		}

		/**
		 * Retrieves customer details
		 *
		 * @return array - Customer details
		 */
		public function get_current_customer_details() {
			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				$user_info    = array(
					'name'  => $current_user->display_name,
					'email' => $current_user->user_email,
				);
				return wp_json_encode( $user_info );
			}
			return null;
		}

		/**
		 * Creates the embed code
		 */
		public function embed_code() {
			$page_id    = get_option( 'tawkto-embed-widget-page-id' );
			$widget_id  = get_option( 'tawkto-embed-widget-widget-id' );
			$visibility = get_option( 'tawkto-visibility-options' );

			// default value.
			$enable_visitor_recognition = true;

			if ( isset( $visibility ) && isset( $visibility['enable_visitor_recognition'] ) ) {
				$enable_visitor_recognition = 1 === $visibility['enable_visitor_recognition'];
			}

			if ( $enable_visitor_recognition ) {
				$customer_details = $this->get_current_customer_details();
			}

			if ( ! empty( $page_id ) && ! empty( $widget_id ) ) {
				include sprintf( '%s/templates/widget.php', dirname( __FILE__ ) );
			}
		}

		/**
		 * Retrieves current URL
		 *
		 * @return string
		 */
		private function get_current_url() {
			$http_host   = '';
			$request_uri = '';

			// sanitize and remove backslashes.
			if ( true === isset( $_SERVER['HTTP_HOST'] ) ) {
				$http_host = sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) );
			}

			if ( true === isset( $_SERVER['REQUEST_URI'] ) ) {
				$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
			}

			$current_url = urldecode( $http_host . $request_uri );

			$protocol = ( ! empty( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ) ? 'https://' : 'http://';

			return strtolower( $protocol . $current_url );
		}

		/**
		 * Prints the embed code when it is allowed to be displayed.
		 *
		 * @return void
		 */
		public function print_embed_code() {
			$vsibility = get_option( 'tawkto-visibility-options' );
			$display   = false;

			if ( 1 === $vsibility['always_display'] ) {
				$display = true;
			}

			if ( ( 1 === $vsibility['show_onfrontpage'] ) && ( is_home() || is_front_page() ) ) {
				$display = true;
			}

			if ( ( 1 === $vsibility['show_oncategory'] ) && is_category() ) {
				$display = true;
			}

			if ( ( 1 === $vsibility['show_ontagpage'] ) && is_tag() ) {
				$display = true;
			}

			if ( ( 1 === $vsibility['show_onarticlepages'] ) && is_single() ) {
				$display = true;
			}

			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
				if ( ( 1 === $vsibility['display_on_shop'] ) && is_shop() ) {
					$display = true;
				}

				if ( ( 1 === $vsibility['display_on_productcategory'] ) && is_product_category() ) {
					$display = true;
				}

				if ( ( 1 === $vsibility['display_on_productpage'] ) && is_product() ) {
					$display = true;
				}

				if ( ( 1 === $vsibility['display_on_producttag'] ) && is_product_tag() ) {
					$display = true;
				}
			}

			if ( isset( $vsibility['include_url'] ) && 1 === $vsibility['include_url'] ) {
				$current_url = $this->get_current_url();

				$included_url_list = $vsibility['included_url_list'];
				$included_url_list = array_map( 'trim', preg_split( '/,/', $included_url_list ) );

				if ( UrlPatternMatcher::match( $current_url, $included_url_list ) ) {
					$display = true;
				}
			}

			if ( isset( $vsibility['exclude_url'] ) && ( 1 === $vsibility['exclude_url'] ) ) {
				$current_url = $this->get_current_url();

				$excluded_url_list = $vsibility['excluded_url_list'];
				$excluded_url_list = array_map( 'trim', preg_split( '/,/', $excluded_url_list ) );

				if ( UrlPatternMatcher::match( $current_url, $excluded_url_list ) ) {
					$display = false;
				};
			}

			if ( $display ) {
				$this->embed_code();
			}
		}

		/**
		 * Migrate old tawk to embed code to new version.
		 *
		 * Old version contained embed code script, from that
		 * markup we need only page id and widget id
		 */
		public function migrate_embed_code() {
			$old_tawkto_embed_code = get_option( 'tawkto-embed-code' );

			if ( empty( $old_tawkto_embed_code ) ) {
				return;
			}

			$matches = array();
			preg_match( '/https:\/\/embed.tawk.to\/([0-9A-Fa-f]{24})\/([a-z0-9]{1,50})/', $old_tawkto_embed_code, $matches );

			if ( isset( $matches[1] ) && isset( $matches[2] ) && TawkTo_Settings::ids_are_correct( $matches[1], $matches[2] ) ) {
				update_option( TawkTo_Settings::TAWK_PAGE_ID_VARIABLE, $matches[1] );
				update_option( TawkTo_Settings::TAWK_WIDGET_ID_VARIABLE, $matches[2] );
			}

			delete_option( 'tawkto-embed-code' );
		}
	}
}

if ( class_exists( 'TawkTo' ) ) {
	register_activation_hook( __FILE__, array( 'TawkTo', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'TawkTo', 'deactivate' ) );

	$tawkto = new TawkTo();

	$upgrade_manager = new TawkToUpgradeManager(
		TawkTo::get_plugin_version(),
		TawkTo::PLUGIN_VERSION_VARIABLE
	);
	$upgrade_manager->register_hooks();

	if ( isset( $tawkto ) ) {
		// these are called every page load.
		$tawkto->migrate_embed_code();

		/**
		 * Adds plugin settings link
		 *
		 * @param  array $links - List of links from WordPress admin.
		 * @return array Updated list of links
		 */
		function tawkto_plugin_settings_link( $links ) {
			$settings_link = '<a href="options-general.php?page=tawkto_plugin">Settings</a>';
			array_unshift( $links, $settings_link );
			return $links;
		}

		$plugin_base_name = plugin_basename( __FILE__ );
		add_filter( 'plugin_action_links_' . $plugin_base_name, 'tawkto_plugin_settings_link' );
	}

	add_action( 'wp_footer', array( $tawkto, 'print_embed_code' ) );
}
