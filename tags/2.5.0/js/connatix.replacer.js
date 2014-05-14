jQuery(document).ready(function(){
    jQuery("#save-button").click(function(){
        jQuery(".connatix-preloader").show();
    });
    
    jQuery(".delete-button").click(function(){
        jQuery(this).parent().parent().remove();
    });
});