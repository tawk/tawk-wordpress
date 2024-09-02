<?php
/**
 * @package Tawk.to Widget for WordPress
 * @author Tawk.to
 * @copyright (C) 2014- Tawk.to
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

?>

<!--Start of Tawk.to Script (0.8.6)-->
<script id="tawk-script" type="text/javascript">
var Tawk_API = Tawk_API || {};
<?php if ( isset( $customer_details ) && $enable_visitor_recognition ) : ?>
Tawk_API.visitor = JSON.parse(<?php echo wp_json_encode( $customer_details ); ?>);
<?php endif ?>
var Tawk_LoadStart=new Date();
(function(){
	var s1 = document.createElement( 'script' ),s0=document.getElementsByTagName( 'script' )[0];
	s1.async = true;
	s1.src = '<?php echo esc_url( 'https://embed.tawk.to/' . $page_id . '/' . $widget_id ); ?>';
	s1.charset = 'UTF-8';
	s1.setAttribute( 'crossorigin','*' );
	s0.parentNode.insertBefore( s1, s0 );
})();
</script>
<!--End of Tawk.to Script (0.8.6)-->
