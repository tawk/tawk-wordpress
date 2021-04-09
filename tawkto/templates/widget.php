<!--Start of Tawk.to Script (0.5.4)-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{};
<?php
if(isset($customer_details) && $enable_visitor_recognition) {
	echo 'Tawk_API.visitor = '. $customer_details.';';
}
?>
var Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='<?php echo esc_url('https://embed.tawk.to/'.$page_id.'/'.$widget_id); ?>';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script (0.5.4)-->
