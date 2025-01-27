<?php
/**
 * @package Tawk.to Widget for WordPress
 * @copyright (C) 2014- Tawk.to
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * Plugin Name: Tawk.to Live Chat
 * Plugin URI: https://www.tawk.to
 * Description: Embeds Tawk.to live chat widget to your site
 * Version: 0.9.0
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
		const TAWK_PRIVACY_OPTIONS      = 'tawkto-privacy-options';
		const TAWK_SECURITY_OPTIONS     = 'tawkto-security-options';
		const TAWK_ACTION_SET_WIDGET    = 'tawkto-set-widget';
		const TAWK_ACTION_REMOVE_WIDGET = 'tawkto-remove-widget';
		const CIPHER                    = 'AES-256-CBC';
		const CIPHER_IV_LENGTH          = 16;
		const NO_CHANGE                 = 'nochange';
		const TAWK_API_KEY              = 'tawkto-js-api-key';

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
			self::init_options();

			add_action( 'wp_loaded', array( &$this, 'init' ) );
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'admin_menu', array( &$this, 'add_menu' ) );
			add_action( 'wp_ajax_tawkto_setwidget', array( &$this, 'action_setwidget' ) );
			add_action( 'wp_ajax_tawkto_removewidget', array( &$this, 'action_removewidget' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'tawk_settings_assets' ) );
			add_action( 'admin_notices', array( $this, 'tawk_admin_notice' ) );
		}

		/**
		 * Initializes the plugin data
		 *
		 * @return void
		 */
		public function init() {
			if ( is_admin() ) {
				if ( false === function_exists( 'get_plugin_data' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}

				$plugin_data = get_plugin_data( __FILE__ );

				$this->plugin_ver = $plugin_data['Version'];
			}
		}

		/**
		 * Initialize default option values
		 *
		 * @return void
		 */
		public static function init_options() {
			$options = self::get_default_options();

			if ( ! get_option( self::TAWK_VISIBILITY_OPTIONS, false ) ) {
				update_option( self::TAWK_VISIBILITY_OPTIONS, $options['visibility'] );
			}

			if ( ! get_option( self::TAWK_PRIVACY_OPTIONS, false ) ) {
				update_option( self::TAWK_PRIVACY_OPTIONS, $options['privacy'] );
			}

			if ( ! get_option( self::TAWK_SECURITY_OPTIONS, false ) ) {
				update_option( self::TAWK_SECURITY_OPTIONS, $options['security'] );
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
			register_setting( 'tawk_options', self::TAWK_VISIBILITY_OPTIONS, array( &$this, 'validate_visibility_options' ) );
			register_setting( 'tawk_options', self::TAWK_PRIVACY_OPTIONS, array( &$this, 'validate_privacy_options' ) );
			register_setting( 'tawk_options', self::TAWK_SECURITY_OPTIONS, array( &$this, 'validate_security_options' ) );
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
		 * @return mixed
		 */
		public function validate_visibility_options( $input ) {
			$toggle_fields = array(
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
			);

			$text_fields = array(
				'excluded_url_list',
				'included_url_list',
			);

			$visibility = get_option( self::TAWK_VISIBILITY_OPTIONS, array() );

			self::validate_toggle_fields( $input, $toggle_fields );
			self::validate_text_fields( $input, $text_fields );

			$visibility = array_merge( $visibility, $input );

			return $visibility;
		}

		/**
		 * Validates the selected privacy options
		 *
		 * @param mixed $input - Privacy option fields.
		 * @return mixed
		 */
		public function validate_privacy_options( $input ) {
			$toggle_fields = array(
				'enable_visitor_recognition',
			);

			$privacy = get_option( self::TAWK_PRIVACY_OPTIONS, array() );

			self::validate_toggle_fields( $input, $toggle_fields );

			$privacy = array_merge( $privacy, $input );

			return $privacy;
		}

		/**
		 * Validates the selected security options
		 *
		 * @param mixed $input - Security option fields.
		 * @return mixed
		 */
		public function validate_security_options( $input ) {
			$text_fields = array(
				'js_api_key',
			);

			$security = get_option( self::TAWK_SECURITY_OPTIONS, array() );

			self::validate_text_fields( $input, $text_fields );
			self::validate_js_api_key( $input );

			$security = array_merge( $security, $input );

			return $security;
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

			$default_options = self::get_default_options();
			$visibility      = get_option( self::TAWK_VISIBILITY_OPTIONS, array() );
			$privacy         = get_option( self::TAWK_PRIVACY_OPTIONS, array() );
			$security        = get_option( self::TAWK_SECURITY_OPTIONS, array() );

			foreach ( $default_options['visibility'] as $key => $value ) {
				if ( ! isset( $visibility[ $key ] ) ) {
					$visibility[ $key ] = $value;
				}
			}

			foreach ( $default_options['privacy'] as $key => $value ) {
				if ( ! isset( $privacy[ $key ] ) ) {
					$privacy[ $key ] = $value;
				}
			}

			foreach ( $default_options['security'] as $key => $value ) {
				if ( ! isset( $security[ $key ] ) ) {
					$security[ $key ] = $value;
				}
			}

			if ( ! empty( $security['js_api_key'] ) ) {
				$security['js_api_key'] = self::NO_CHANGE;
			}

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
		 * Validate JS API Key field
		 *
		 * @param array $fields - List of fields.
		 * @return void
		 * @throws Exception - Error validating JS API Key.
		 */
		private static function validate_js_api_key( &$fields ) {
			if ( self::NO_CHANGE === $fields['js_api_key'] ) {
				unset( $fields['js_api_key'] );
				return;
			}

			delete_transient( self::TAWK_API_KEY );

			if ( '' === $fields['js_api_key'] ) {
				return;
			}

			try {
				if ( 40 !== strlen( $fields['js_api_key'] ) ) {
					throw new Exception( 'Invalid key. Please provide value with 40 characters' );
				}

				$fields['js_api_key'] = self::get_encrypted_data( $fields['js_api_key'] );
			} catch ( Exception $e ) {
				self::show_tawk_options_error( 'Javascript API Key: ' . $e->getMessage() );

				unset( $fields['js_api_key'] );
			}
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
		private static function validate_toggle_fields( &$fields, $field_names ) {
			foreach ( $field_names as $field_name ) {
				if ( isset( $fields[ $field_name ] ) && '1' === $fields[ $field_name ] ) {
					$fields[ $field_name ] = 1;
					continue;
				}

				$fields[ $field_name ] = 0;
			}
		}

		/**
		 * Retrieves default visibility options
		 *
		 * @return array
		 */
		public static function get_default_options() {
			$config = include plugin_dir_path( __FILE__ ) . 'includes/default_config.php';

			return $config;
		}

		/**
		 * Encrypt data
		 *
		 * @param string $data - Data to be encrypted.
		 * @return string
		 * @throws Exception - Error encrypting data.
		 */
		private static function get_encrypted_data( $data ) {
			if ( ! defined( 'SECURE_AUTH_KEY' ) ) {
				throw new Exception( 'SECURE_AUTH_KEY is not defined' );
			}

			try {
				$iv = random_bytes( self::CIPHER_IV_LENGTH );
			} catch ( Exception $e ) {
				throw new Exception( 'Error generating IV' );
			}

			$encrypted_data = openssl_encrypt( $data, self::CIPHER, SECURE_AUTH_KEY, 0, $iv );

			if ( false === $encrypted_data ) {
				throw new Exception( 'Error encrypting data' );
			}

			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			$encrypted_data = base64_encode( $iv . $encrypted_data );

			if ( false === $encrypted_data ) {
				throw new Exception( 'Error encoding data' );
			}

			return $encrypted_data;
		}

		/**
		 * Decrypt data
		 *
		 * @param string $data - Data to be decrypted.
		 * @return string
		 */
		private static function get_decrypted_data( $data ) {
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			$decoded_data = base64_decode( $data );

			if ( false === $decoded_data ) {
				return '';
			}

			$iv             = substr( $decoded_data, 0, self::CIPHER_IV_LENGTH );
			$encrypted_data = substr( $decoded_data, self::CIPHER_IV_LENGTH );

			$decrypted_data = openssl_decrypt( $encrypted_data, self::CIPHER, SECURE_AUTH_KEY, 0, $iv );

			if ( false === $decrypted_data ) {
				return '';
			}

			return $decrypted_data;
		}

		/**
		 * Retrieves JS API Key
		 *
		 * @return string
		 */
		public static function get_js_api_key() {
			if ( ! empty( get_transient( self::TAWK_API_KEY ) ) ) {
				return get_transient( self::TAWK_API_KEY );
			}

			$security = get_option( self::TAWK_SECURITY_OPTIONS );

			if ( ! isset( $security['js_api_key'] ) ) {
				return '';
			}

			$key = self::get_decrypted_data( $security['js_api_key'] );

			set_transient( self::TAWK_API_KEY, $key, 60 * 60 );

			return $key;
		}

		/**
		 * Adds settings error
		 *
		 * @param string $message - Error message.
		 * @return void
		 */
		private static function show_tawk_options_error( $message ) {
			add_settings_error(
				'tawk_options',
				'tawk_error',
				$message,
				'error'
			);
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

			TawkTo_Settings::init_options();

			add_option( TawkTo_Settings::TAWK_PAGE_ID_VARIABLE, '', '', 'yes' );
			add_option( TawkTo_Settings::TAWK_WIDGET_ID_VARIABLE, '', '', 'yes' );
			add_option( self::PLUGIN_VERSION_VARIABLE, self::get_plugin_version(), '', 'yes' );
		}

		/**
		 * Cleans up plugin data on deactivation
		 */
		public static function deactivate() {
			delete_option( TawkTo_Settings::TAWK_PAGE_ID_VARIABLE );
			delete_option( TawkTo_Settings::TAWK_WIDGET_ID_VARIABLE );
			delete_option( TawkTo_Settings::TAWK_VISIBILITY_OPTIONS );
			delete_option( TawkTo_Settings::TAWK_PRIVACY_OPTIONS );
			delete_option( TawkTo_Settings::TAWK_SECURITY_OPTIONS );
			delete_option( self::PLUGIN_VERSION_VARIABLE );

			delete_transient( TawkTo_Settings::TAWK_API_KEY );
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

				$js_api_key = TawkTo_Settings::get_js_api_key();
				if ( ! empty( $user_info['email'] ) && ! empty( $js_api_key ) ) {
					$user_info['hash'] = hash_hmac( 'sha256', $user_info['email'], $js_api_key );
				}

				return wp_json_encode( $user_info );
			}
			return null;
		}

		/**
		 * Creates the embed code
		 */
		public function embed_code() {
			$page_id   = get_option( TawkTo_Settings::TAWK_PAGE_ID_VARIABLE );
			$widget_id = get_option( TawkTo_Settings::TAWK_WIDGET_ID_VARIABLE );
			$privacy   = get_option( TawkTo_Settings::TAWK_PRIVACY_OPTIONS );

			// default value.
			$enable_visitor_recognition = true;

			if ( isset( $privacy ) && isset( $privacy['enable_visitor_recognition'] ) ) {
				$enable_visitor_recognition = 1 === $privacy['enable_visitor_recognition'];
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
			$visibility = get_option( TawkTo_Settings::TAWK_VISIBILITY_OPTIONS );
			$display    = false;

			if ( 1 === $visibility['always_display'] ) {
				$display = true;
			}

			if ( ( 1 === $visibility['show_onfrontpage'] ) && ( is_home() || is_front_page() ) ) {
				$display = true;
			}

			if ( ( 1 === $visibility['show_oncategory'] ) && is_category() ) {
				$display = true;
			}

			if ( ( 1 === $visibility['show_ontagpage'] ) && is_tag() ) {
				$display = true;
			}

			if ( ( 1 === $visibility['show_onarticlepages'] ) && is_single() ) {
				$display = true;
			}

			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
				if ( ( 1 === $visibility['display_on_shop'] ) && is_shop() ) {
					$display = true;
				}

				if ( ( 1 === $visibility['display_on_productcategory'] ) && is_product_category() ) {
					$display = true;
				}

				if ( ( 1 === $visibility['display_on_productpage'] ) && is_product() ) {
					$display = true;
				}

				if ( ( 1 === $visibility['display_on_producttag'] ) && is_product_tag() ) {
					$display = true;
				}
			}

			if ( isset( $visibility['include_url'] ) && 1 === $visibility['include_url'] ) {
				$current_url = $this->get_current_url();

				$included_url_list = $visibility['included_url_list'];
				$included_url_list = array_map( 'trim', preg_split( '/,/', $included_url_list ) );

				if ( UrlPatternMatcher::match( $current_url, $included_url_list ) ) {
					$display = true;
				}
			}

			if ( isset( $visibility['exclude_url'] ) && ( 1 === $visibility['exclude_url'] ) ) {
				$current_url = $this->get_current_url();

				$excluded_url_list = $visibility['excluded_url_list'];
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
