<?php
/**
 * @package Tawk.to Widget for WordPress
 * @author Tawk.to
 * @copyright (C) 2014- Tawk.to
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$has_woocommerce = in_array(
	'woocommerce/woocommerce.php',
	apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
	true
);

$tawky_big_img_url = plugins_url( 'assets/tawky_big.png', dirname( __FILE__ ) );
?>

<div id="tawk-header">
	<div class="tawk-mel">
		<img <?php echo esc_attr( 'src=' . $tawky_big_img_url ); ?> />
	</div>
	<div class="tawk-header-text">
		<?php esc_html_e( 'tawk.to Plugin Settings', 'tawk-to-live-chat' ); ?>
	</div>
</div>

<div class="tawk-action">
	<?php
	submit_button(
		null,
		'primary',
		'submit-header',
		true,
		array(
			'form' => 'tawk-settings-form',
		)
	);
	?>
</div>

<div id="tawk-settings-body">
	<div id="tawk-tabs">
		<button class="tawk-tab-links"
				onclick="opentab( event, 'account' )"
				id="defaultOpen">
			Account Settings
		</button>
		<button class="tawk-tab-links"
				onclick="opentab( event, 'visibility' )">
			Visibility Options
		</button>
		<button class="tawk-tab-links"
				onclick="opentab( event, 'privacy' )">
			Privacy Options
		</button>
		<?php if ( $has_woocommerce ) { ?>
			<button class="tawk-tab-links"
					onclick="opentab( event, 'woocommerce' )">
				Woocomerce Options
			</button>
		<?php } ?>
	</div>

	<div id="account" class="tawk-tab-content">
		<?php
			$page_id   = get_option( self::TAWK_PAGE_ID_VARIABLE );
			$widget_id = get_option( self::TAWK_WIDGET_ID_VARIABLE );

		$override = filter_input( INPUT_GET, 'override', FILTER_VALIDATE_INT );
		if ( 1 === $override ) {
			$override = true;
		} else {
			$override = false;
		}

			$page_id_exist   = true === isset( $page_id ) && false === empty( $page_id );
			$widget_id_exist = true === isset( $widget_id ) && false === empty( $widget_id );

			$display_widget_settings = false;
		if ( false === $page_id_exist || false === $widget_id_exist ) {
			$display_widget_settings = true;
		}

		if ( true === $override ) {
			$display_widget_settings = true;
		}

		if ( true === $display_widget_settings ) {
			wp_enqueue_script( 'tawk-selection', plugin_dir_url( __DIR__ ) . 'assets/js/tawk.selection.js', array(), $plugin_ver, false );
			wp_localize_script(
				'tawk-selection',
				'tawkSelectionData',
				array(
					'url'   => array(
						'base'   => $base_url,
						'iframe' => $iframe_url,
					),
					'nonce' => array(
						'setWidget'    => $set_widget_nonce,
						'removeWidget' => $remove_widget_nonce,
					),
				)
			);
			?>
		<iframe id="tawk-iframe" src=""></iframe>
			<?php
		} else {
			$tawk_admin_url = admin_url( 'options-general.php?page=tawkto_plugin&override=1' );
			?>
			<h2><?php esc_html_e( 'Property and widget is already set.', 'tawk-to-live-chat' ); ?></h2>
			<?php esc_html_e( 'if you wish to reselect property or widget', 'tawk-to-live-chat' ); ?>
			<a href=<?php echo esc_html( $tawk_admin_url ); ?>>click here</a>
		<?php } ?>
	</div>

	<form id='tawk-settings-form' method="post" action="options.php">
		<?php
			settings_fields( 'tawk_options' );
			do_settings_sections( 'tawk_options' );

			$visibility = get_option( 'tawkto-visibility-options', false );
		if ( false === $visibility ) {
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
		}

		if ( ! isset( $visibility['enable_visitor_recognition'] ) ) {
			// default value.
			$visibility['enable_visitor_recognition'] = 1;
		}
		?>
		<div id="visibility" class="tawk-tab-content">
			<div id="tawk-visibility-settings">
				<h2><?php esc_html_e( 'Visibility Options', 'tawk-to-live-chat' ); ?></h2>
				<p class='tawk-notice'>
					<?php esc_html_e( 'Please Note: that you can use the visibility options below, or you can show the tawk.to widget', 'tawk-to-live-chat' ); ?>
					<br>
					<?php esc_html_e( 'on any page independent of these visibility options by simply using the', 'tawk-to-live-chat' ); ?>
					<b><?php esc_html_e( '[tawkto]', 'tawk-to-live-chat' ); ?></b>
					<?php esc_html_e( 'shortcode in', 'tawk-to-live-chat' ); ?>
					<br>
					<?php esc_html_e( 'the post or page.', 'tawk-to-live-chat' ); ?>
				</p>
				<table class="form-table">
					<tr valign="top">
						<th class="tawk-setting" scope="row">
							<?php esc_html_e( 'Always show Tawk.To widget on every page', 'tawk-to-live-chat' ); ?>
						</th>
						<td>
							<label class="switch">
								<input type="checkbox"
										class="slider round"
										id="always-display"
										name="tawkto-visibility-options[always_display]"
										value="1"
										<?php echo checked( 1, $visibility['always_display'], false ); ?> />
								<div class="slider round"></div>
							</label>
						</td>
					</tr>
					<tr valign="top" class="tawk-selected-display">
						<th class="tawk-setting" scope="row">
							<?php esc_html_e( 'Show on front page', 'tawk-to-live-chat' ); ?>
						</th>
						<td>
							<label class="switch">
								<input type="checkbox"
										class="slider round"
										id="show-onfrontpage" name="tawkto-visibility-options[show_onfrontpage]"
										value="1"
										<?php echo checked( 1, $visibility['show_onfrontpage'], false ); ?> />
								<div class="slider round"></div>
							</label>
						</td>
					</tr>
					<tr valign="top" class="tawk-selected-display">
						<th class="tawk-setting" scope="row">
							<?php esc_html_e( 'Show on Category pages', 'tawk-to-live-chat' ); ?>
						</th>
						<td>
							<label class="switch">
								<input type="checkbox"
										class="slider round"
										id="show-oncategory" name="tawkto-visibility-options[show_oncategory]"
										value="1"
										<?php echo checked( 1, $visibility['show_oncategory'], false ); ?> />
								<div class="slider round"></div>
							</label>
						</td>
					</tr>
					<tr valign="top"  class="tawk-selected-display">
						<th class="tawk-setting" scope="row">
							<?php esc_html_e( 'Show on Tag pages', 'tawk-to-live-chat' ); ?>
						</th>
						<td>
							<label class="switch">
								<input type="checkbox"
										class="slider round"
										id="show-ontagpage"
										name="tawkto-visibility-options[show_ontagpage]"
										value="1"
										<?php echo checked( 1, $visibility['show_ontagpage'], false ); ?> />
								<div class="slider round"></div>
							</label>
						</td>
					</tr>
					<tr valign="top"  class="tawk-selected-display">
						<th class="tawk-setting" scope="row">
							<?php esc_html_e( 'Show on Single Post Pages', 'tawk-to-live-chat' ); ?>
						</th>
						<td>
							<label class="switch">
								<input type="checkbox"
										class="slider round"
										id="show-onarticlepages"
										name="tawkto-visibility-options[show_onarticlepages]"
										value="1"
										<?php echo checked( 1, $visibility['show_onarticlepages'], false ); ?> />
								<div class="slider round"></div>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th class="tawk-setting" scope="row">
							<?php esc_html_e( 'Exclude on specific url', 'tawk-to-live-chat' ); ?>
						</th>
						<td>
							<label class="switch">
								<input type="checkbox"
										class="slider round"
										id="exclude-url"
										name="tawkto-visibility-options[exclude_url]"
										value="1"
										<?php echo checked( 1, $visibility['exclude_url'], false ); ?> />
								<div class="slider round"></div>
							</label>
							<div id="exlucded-urls-container" style="display:none;">
								<textarea id="excluded-url-list"
										name="tawkto-visibility-options[excluded_url_list]"
										cols="50"
										rows="10"><?php echo esc_html( $visibility['excluded_url_list'] ); ?></textarea>
								<br>
								<?php esc_html_e( 'Enter the url where you', 'tawk-to-live-chat' ); ?>
								<b><?php esc_html_e( 'DO NOT', 'tawk-to-live-chat' ); ?></b>
								<?php esc_html_e( 'want the widget to display.', 'tawk-to-live-chat' ); ?>
								<br>
								<?php esc_html_e( 'Separate entries with comma', 'tawk-to-live-chat' ); ?>(,).
								<br>
								<?php esc_html_e( 'Add (*) at the end of the entry to match wildcard url.', 'tawk-to-live-chat' ); ?>
								<br>
							</div>
						</td>
					</tr>
					<tr valign="top"  class="tawk-selected-display">
						<th class="tawk-setting" scope="row"><?php esc_html_e( 'Include on specific url', 'tawk-to-live-chat' ); ?></th>
						<td>
							<label class="switch">
								<input type="checkbox"
										class="slider round"
										id="include-url"
										name="tawkto-visibility-options[include_url]"
										value="1"
										<?php echo checked( 1, $visibility['include_url'], false ); ?> />
								<div class="slider round"></div>
							</label>
							<div id="included-urls-container" style="display:none;">
								<textarea id="included-url-list"
									name="tawkto-visibility-options[included_url_list]"
									cols="50"
									rows="10"><?php echo esc_html( $visibility['included_url_list'] ); ?></textarea>
								<br>
								<?php esc_html_e( 'Enter the url where you', 'tawk-to-live-chat' ); ?>
								<b><?php esc_html_e( 'WANT', 'tawk-to-live-chat' ); ?></b>
								<?php esc_html_e( 'the widget to display.', 'tawk-to-live-chat' ); ?>
								<br>
								<?php esc_html_e( 'Separate entries with comma ', 'tawk-to-live-chat' ); ?>(,).
								<br>
								<?php esc_html_e( 'Add (*) at the end of the entry to match wildcard url.', 'tawk-to-live-chat' ); ?>
								<br>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div id="woocommerce" class="tawk-tab-content">
			<?php
			if ( $has_woocommerce ) {
				if ( ! isset( $visibility['display_on_shop'] ) ) {
					$visibility['display_on_shop']            = 0;
					$visibility['display_on_productcategory'] = 0;
					$visibility['display_on_productpage']     = 0;
					$visibility['display_on_producttag']      = 0;
				}
				?>
				<div id="woocommerce">
					<h2>
					<?php esc_html_e( 'Woocommerce visibility Options', 'tawk-to-live-chat' ); ?>
					</h2>
					<table class="form-table">
						<tr valign="top">
							<th class="tawk-setting" scope="row">
							<?php esc_html_e( 'Display on Shop main page', 'tawk-to-live-chat' ); ?>
							</th>
							<td>
								<label class="switch">
									<input type="checkbox"
											class="slider round"
											id="display-on-shop"
											name="tawkto-visibility-options[display_on_shop]"
											value="1"
										<?php echo checked( 1, $visibility['display_on_shop'], false ); ?> />
									<div class="slider round"></div>
								</label>
							</td>
						</tr>
						<tr valign="top">
							<th class="tawk-setting" scope="row">
							<?php esc_html_e( 'Display on product category pages', 'tawk-to-live-chat' ); ?>
							</th>
							<td>
								<label class="switch">
									<input type="checkbox"
											class="slider round"
											id="display-on-productcategory"
											name="tawkto-visibility-options[display_on_productcategory]"
											value="1"
										<?php echo checked( 1, $visibility['display_on_productcategory'], false ); ?> />
									<div class="slider round"></div>
								</label>
							</td>
						</tr>

						<tr valign="top">
							<th class="tawk-setting" scope="row">
							<?php esc_html_e( 'Display on single product page', 'tawk-to-live-chat' ); ?>
							</th>
							<td>
								<label class="switch">
									<input type="checkbox"
											class="slider round"
											id="display-on-productpage"
											name="tawkto-visibility-options[display_on_productpage]"
											value="1"
										<?php echo checked( 1, $visibility['display_on_productpage'], false ); ?> />
									<div class="slider round"></div>
								</label>
							</td>
						</tr>
						<tr valign="top">
							<th class="tawk-setting" scope="row">
							<?php esc_html_e( 'Display on product tag pages', 'tawk-to-live-chat' ); ?>
							</th>
							<td>
								<label class="switch">
									<input type="checkbox"
											class="slider round"
											id="display-on-producttag"
											name="tawkto-visibility-options[display_on_producttag]"
											value="1"
										<?php echo checked( 1, $visibility['display_on_producttag'], false ); ?> />
									<div class="slider round"></div>
								</label>
							</td>
						</tr>
					</table>
				</div>
			<?php } ?>
		</div>

		<div id="privacy" class="tawk-tab-content">
			<h2>
				<?php esc_html_e( 'Privacy Options', 'tawk-to-live-chat' ); ?>
			</h2>
			<table class="form-table">
				<tr valign="top">
					<th class="tawk-setting" scope="row">
						<?php esc_html_e( 'Enable Visitor Recognition', 'tawk-to-live-chat' ); ?>
					</th>
					<td>
						<label class="switch">
							<input type="checkbox"
									class="slider round"
									id="enable-visitor-recognition"
									name="tawkto-visibility-options[enable_visitor_recognition]"
									value="1"
									<?php echo checked( 1, $visibility['enable_visitor_recognition'], false ); ?> />
							<div class="slider round"></div>
						</label>
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>

<div class="tawk-action">
	<div class="tawk-footer-action">
		<?php
		submit_button(
			null,
			'primary',
			'submit-footer',
			true,
			array(
				'form' => 'tawk-settings-form',
			)
		);
		?>
	</div>
	<div class="tawk-footer-text">
		Having trouble and need some help? Check out our <a class="tawk-link" href="https://www.tawk.to/knowledgebase/" target="_blank">Knowledge Base</a>.
	</div>
</div>
