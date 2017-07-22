<?php

/**
 * @package Tawk.to Widget for Wordpress
 * @author Tawk.to
 * @copyright (C) 2014- Tawk.to
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}
?>

<div class="tawkheader">
  <div class="tawkmel">
    <img src="<?php echo plugins_url( 'assets/tawky_big.png' , dirname(__FILE__) ) ?>">
  </div>
  <div class="tawkheadtext">
    <?php _e('tawk.to Plugin Settings','tawk-to-live-chat'); ?>
  </div>
</div>
<div class="tawkaction">
<?php submit_button(); ?>
</div>
<div class="tawksettingsbody">
	<div class="tawktabs">
	  <button class="tawktablinks" onclick="opentab(event, 'account')" id="defaultOpen">Account Settings</button>
	  <button class="tawktablinks" onclick="opentab(event, 'visibility')">Visibility Options</button>
	  	<?php 
	  	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
   		{
   		?>
	  		<button class="tawktablinks" onclick="opentab(event, 'woocommerce')">Woocomerce Options</button>
	  	<?php } ?>
	</div>

	<div id="account" class="tawktabcontent" >
  <?php 
      $page_id = get_option(self::TAWK_PAGE_ID_VARIABLE);
      $widget_id = get_option(self::TAWK_WIDGET_ID_VARIABLE);
      if(isset($_GET["override"]) && $_GET["override"] == 1){
        $override = TRUE;
      }else{
        $override = false;
      }
    $display_widgetsettings = false;
    if(($page_id == NULL) || ($widget_id == NULL)){
        $display_widgetsettings = true;
    }
    if($override == TRUE){
        $display_widgetsettings = true; 
    }
  if ($display_widgetsettings == TRUE){
  ?>
		<iframe
			id="tawkIframe"
			src=""
			style="min-height: 295px; width : 100%; border: none; margin-top: 20px">
	  	</iframe>
	  	<script>
	  	  var currentHost = window.location.protocol + "//" + window.location.host;
	  		var url = "<?php echo $iframe_url ?>&parentDomain=" + currentHost;
	  		jQuery('#tawkIframe').attr('src', url);
	  		var iframe = jQuery('#tawk_widget_customization')[0];

	  		window.addEventListener('message', function(e) {
                if(e.origin === '<?php echo $base_url ?>') {

                    if(e.data.action === 'setWidget') {
                        setWidget(e);
                    }

                    if(e.data.action === 'removeWidget') {
                        removeWidget(e);
                    }
                }
            });

            function setWidget(e) {
                jQuery.post(ajaxurl, {
                    action : 'tawkto_setwidget',
                    pageId : e.data.pageId,
                    widgetId : e.data.widgetId
                }, function(r) {
                    if(r.success) { 
                        e.source.postMessage({action: 'setDone'}, '<?php echo $base_url ?>');
                    } else {
                        e.source.postMessage({action: 'setFail'}, '<?php echo $base_url ?>');
                    }

                });
            }

            function removeWidget(e) {
                jQuery.post(ajaxurl, {action: 'tawkto_removewidget'}, function(r) {
                    if(r.success) {
                        e.source.postMessage({action: 'removeDone'}, '<?php echo $base_url ?>');
                    } else {
                        e.source.postMessage({action: 'removeFail'}, '<?php echo $base_url ?>');
                    }
                });
            }
	  	</script>
      <?php
   }else{
      echo "<h2>Property and widget is already set.</h2>";
      $tawk_admin_url = admin_url('options-general.php?page=tawkto_plugin&override=1');
      echo 'if you wish to reselect property or widget <a href="'.$tawk_admin_url.'">click here</a>';
    }
  ?>
	</div>
<form method="post" action="options.php">
   <?php
   settings_fields( 'tawk_options' );
   do_settings_sections( 'tawk_options' );

   $visibility = get_option( 'tawkto-visibility-options',FALSE );
   if($visibility == FALSE){
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
   }
   ?>
	<div id="visibility" class="tawktabcontent">
	   <div id="tawkvisibilitysettings">
    <h2><?php _e('Visibility Options','tawk-to-live-chat'); ?></h2>
    <p class='tawknotice'>
    <?php _e('Please Note: that you can use the visibility options below, or you can show the tawk.to widget','tawk-to-live-chat'); ?>
    <BR>
    <?php _e('on any page independent of these visibility options by simply using the <b>[tawkto]</b> shortcode in','tawk-to-live-chat'); ?>
    <BR> 
    <?php _e('the post or page.','tawk-to-live-chat'); ?>
    </p>
    <table class="form-table">
      <tr valign="top">
      <th class="tawksetting" scope="row"><?php _e('Always show Tawk.To widget on every page','tawk-to-live-chat'); ?></th>
      <td>
        <label class="switch">
        <input type="checkbox" class="slider round" id="always_display" name="tawkto-visibility-options[always_display]" value="1" <?php echo checked( 1, $visibility['always_display'], false ); ?> />
      <div class="slider round"></div>
        </label>
      </td>
      </tr>
      <tr valign="top" class="twk_selected_display">
      <th class="tawksetting" scope="row"><?php _e('Show on front page','tawk-to-live-chat'); ?></th>
      <td>
<label class="switch">
      <input type="checkbox" class="slider round" id="show_onfrontpage" name="tawkto-visibility-options[show_onfrontpage]" value="1" <?php echo checked( 1, $visibility['show_onfrontpage'], false ); ?> />
   <div class="slider round"></div>
        </label>
      </td>
      </tr>
      <tr valign="top" class="twk_selected_display">
      <th class="tawksetting" scope="row"><?php _e('Show on Category pages','tawk-to-live-chat'); ?></th>
      <td>
      <label class="switch">
      <input type="checkbox" class="slider round" id="show_oncategory" name="tawkto-visibility-options[show_oncategory]" value="1" <?php echo checked( 1, $visibility['show_oncategory'], false ); ?> />
      <div class="slider round"></div>
        </label>
      </td>
      </tr>
      <tr valign="top"  class="twk_selected_display">
      <th class="tawksetting" scope="row"><?php _e('Show on Tag pages','tawk-to-live-chat'); ?></th>
      <td>
      <label class="switch">
      <input type="checkbox" class="slider round" id="show_ontagpage" name="tawkto-visibility-options[show_ontagpage]" value="1" <?php echo checked( 1, $visibility['show_ontagpage'], false ); ?> />
      <div class="slider round"></div>
        </label>
      </td>
      </tr>
      <tr valign="top"  class="twk_selected_display">
      <th class="tawksetting" scope="row"><?php _e('Show on Single Post Pages','tawk-to-live-chat'); ?></th>
      <td>
      <label class="switch">
      <input type="checkbox" class="slider round" id="show_onarticlepages" name="tawkto-visibility-options[show_onarticlepages]" value="1" <?php echo checked( 1, $visibility['show_onarticlepages'], false ); ?> />
      <div class="slider round"></div>
        </label>
      </td>
      </tr>
      <tr valign="top">
      <th class="tawksetting" scope="row"><?php _e('Exclude on specific url','tawk-to-live-chat'); ?></th>
      <td>
      <label class="switch">
      <input type="checkbox" class="slider round" id="exclude_url" name="tawkto-visibility-options[exclude_url]" value="1" <?php echo checked( 1, $visibility['exclude_url'], false ); ?> />
      <div class="slider round"></div>
        </label>
      	<div id="exlucded_urls_container" style="display:none;">
      	<textarea id="excluded_url_list" name="tawkto-visibility-options[excluded_url_list]" cols="50" rows="10"><?php echo $visibility['excluded_url_list']; ?></textarea><BR>
      	<?php _e('Enter the url where you <b>DO NOT</b> want the widget to display.','tawk-to-live-chat'); ?>
      	<BR>
				<?php _e('Separate entries with comma','tawk-to-live-chat'); ?>(,).<BR>
      	</div>
      </td>
      </tr>
      <tr valign="top"  class="twk_selected_display">
      <th class="tawksetting" scope="row"><?php _e('Include on specific url','tawk-to-live-chat'); ?></th>
      <td>
      <label class="switch">
      <input type="checkbox" class="slider round" id="include_url" name="tawkto-visibility-options[include_url]" value="1" <?php echo checked( 1, $visibility['include_url'], false ); ?> />
      <div class="slider round"></div>
        </label>
      	<div id="included_urls_container" style="display:none;">
      	<textarea id="included_url_list" name="tawkto-visibility-options[included_url_list]" cols="50" rows="10"><?php echo $visibility['included_url_list']; ?></textarea><BR>
      	<?php _e('Enter the url where you <b>WANT</b> the widget to display.','tawk-to-live-chat'); ?><BR>
				<?php _e('Separate entries with comma ','tawk-to-live-chat'); ?>(,).<BR>
      	</div>
      </td>
      </tr>
    </table>
	</div>
	</div>

	<div id="woocommerce" class="tawktabcontent">
	   <?php 
	   if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
	   {

	   if(!isset($visibility['display_on_shop']))
	   {
		   	$visibility['display_on_shop'] = 0;
		   	$visibility['display_on_productcategory'] = 0;
		   	$visibility['display_on_productpage'] = 0;
		   	$visibility['display_on_producttag'] = 0;
	   }
	   ?>
		   <div id="tawkvisibilitysettings woocommerce">
		      <h2><?php _e('Woocommerce visibility Options','tawk-to-live-chat'); ?></h2>
		      <table class="form-table">
		      <tr valign="top">
            <th class="tawksetting" scope="row"><?php _e('Display on Shop main page','tawk-to-live-chat'); ?></th>
		        <td>
            <label class="switch">
            <input type="checkbox" class="slider round" id="display_on_shop" name="tawkto-visibility-options[display_on_shop]" value="1" <?php echo checked( 1, $visibility['display_on_shop'], false ); ?> />
            <div class="slider round"></div>
        </label>
            </td>
		      </tr>
		      <tr valign="top">
		      <th class="tawksetting" scope="row"><?php _e('Display on product category pages','tawk-to-live-chat'); ?></th>
		      <td>
          <label class="switch">
          <input type="checkbox" class="slider round" id="display_on_productcategory" name="tawkto-visibility-options[display_on_productcategory]" value="1" <?php echo checked( 1, $visibility['display_on_productcategory'], false ); ?> />
          <div class="slider round"></div>
        </label>
        </td>
		      </tr>

		     <tr valign="top">
		      <th class="tawksetting" scope="row"><?php _e('Display on single product page','tawk-to-live-chat'); ?></th>
		  <td>
      <label class="switch">
      <input type="checkbox" class="slider round" id="display_on_productpage" name="tawkto-visibility-options[display_on_productpage]" value="1" <?php echo checked( 1, $visibility['display_on_productpage'], false ); ?> />
      <div class="slider round"></div>
        </label>
      </td>
		      </tr>
		      <tr valign="top">
		      <th class="tawksetting" scope="row"><?php _e('Display on product tag pages','tawk-to-live-chat'); ?></th>
		  <td>
      <label class="switch">
      <input type="checkbox" class="slider round" id="display_on_producttag" name="tawkto-visibility-options[display_on_producttag]" value="1" <?php echo checked( 1, $visibility['display_on_producttag'], false ); ?> />
      <div class="slider round"></div>
        </label>
      </td>
		      </tr>
		      </table>
		   </div>
	   <?php
		}
	   ?>
	</div>

</div>
<div class="tawkaction">
  <div class="tawkfootaction">
  <?php submit_button(); ?>
  </div>
  <div class="tawkfoottext">
  Having trouble and need some help? Check out our <a class="tawklink" href="https://www.tawk.to/knowledgebase/" target="_blank">Knowledge Base</a>.
  </div>
</div>

</form>