jQuery(function() {
    document.getElementById("defaultOpen").click();

    console.log(jQuery("#always_display").prop("checked"));
    if(jQuery("#always_display").prop("checked")){
        jQuery('.twk_selected_display').hide();
        jQuery('#show_onfrontpage').prop('disabled', true);
        jQuery('#show_oncategory').prop('disabled', true);
        jQuery('#show_ontagpage').prop('disabled', true);
        jQuery('#show_onarticlepages').prop('disabled', true);
        jQuery('#include_url').prop('disabled', true);
    }else{
        jQuery('.twk_selected_display').show();
        
    }

    jQuery("#always_display").change(function() {
        if(this.checked){
            jQuery('.twk_selected_display').fadeOut();
            jQuery('#show_onfrontpage').prop('disabled', true);
            jQuery('#show_oncategory').prop('disabled', true);
            jQuery('#show_ontagpage').prop('disabled', true);
            jQuery('#show_onarticlepages').prop('disabled', true);
            jQuery('#include_url').prop('disabled', true);
        }else{
            jQuery('.twk_selected_display').fadeIn();
            jQuery('#show_onfrontpage').prop('disabled', false);
            jQuery('#show_oncategory').prop('disabled', false);
            jQuery('#show_ontagpage').prop('disabled', false);
            jQuery('#show_onarticlepages').prop('disabled', false);
            jQuery('#include_url').prop('disabled', false);
        }
    });


    jQuery("#exclude_url").change(function() {
        if(this.checked){
            jQuery("#exlucded_urls_container").fadeIn();
        }else{
            jQuery("#exlucded_urls_container").fadeOut();
        }
    });

    if(jQuery("#include_url").prop("checked")){
        jQuery("#included_urls_container").show();
    }

    jQuery("#include_url").change(function() {
        if(this.checked){
            jQuery("#included_urls_container").fadeIn();
        }else{
            jQuery("#included_urls_container").fadeOut();
        }
    });

    if(jQuery("#exclude_url").prop("checked")){
        jQuery("#exlucded_urls_container").fadeIn();
    }

});


function opentab(evt, tabName) {
    // Declare all variables
    var i, tabcontent, tablinks;

    tabcontent = document.getElementsByClassName("tawktabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    tablinks = document.getElementsByClassName("tawktablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}