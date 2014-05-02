var connatix = {};
connatix.jQuery = jQuery.noConflict();
connatix.jQuery(function(){
connatix.jQuery(document).ready(function(){
    
    //bind the change of the input on the page
    connatix.jQuery("input[name=token]").bind("blur",function(){
        var token = connatix.jQuery(this).val();
        
        if(token.length == 0)
            return;
        
        connatix.jQuery(".connatix-preloader").show();
        
        connatix.jQuery.getJSON(CONNATIX_PARTNER_API + token + "&callback=?", function( data ) {
            connatix.jQuery("input[name=dom_path]").val(data.path);
            connatix.jQuery("input[name=dest]").val(data.destinationPage);
        }).always(function() {
            connatix.jQuery(".connatix-preloader").hide();
          });;
    });
    
    
    //bind of the go to page functionality
    connatix.jQuery("select[name=categoryID]").change(function(){
        var url = connatix.jQuery(this).find("option:selected").attr("data-link");
        connatix.jQuery(".category-page-link").attr("href", url);
    });
});
});




/*
 * OBSOLETE
function hidesection(){
    connatix.jQuery("#form1").toggle();
    connatix.jQuery(".poof").toggle();
    connatix.jQuery("#form2").toggle();
}
             
function connatix_add_values(){
    var path = connatix.jQuery("#path").val(); 
    var token = connatix.jQuery("#token").val();
    var dest = connatix.jQuery("#dest").val();
    var pos = connatix.jQuery("#pos").val();
    if(path!=="" && token!=="" && dest!=="" && pos!=="") {
        var unq= new Date().valueOf();
        connatix.jQuery('#connatix_ad_management tr:last').before('<tr><td><input type="hidden" name="connatix_options['+unq+'][path]" value="'+path+'"/>'+path+'</td><td><input type="hidden" name="connatix_options['+unq+'][token]" value="'+token+'"/>'+token+'</td><td><input type="hidden" name="connatix_options['+unq+'][dest]" value="'+dest+'"/>'+dest+'</td><td><input type="hidden" name="connatix_options['+unq+'][pos]" value="'+pos+'"/>'+pos+'</td><td><input type="hidden" name="connatix_options['+unq+'][id]" value="'+unq+'"/><input type="button" style="width: 130px !important;" onclick="connatix_remove_line()" class="button-primary" value="Delete" /></td></tr>');
        connatix.jQuery("#path").val('');
        connatix.jQuery("#token").val('');
        connatix.jQuery("#dest").val('');
        connatix.jQuery("#pos").val('');                          
    } else 
        {alert("Inputs cannot be empty!");}
    }
                
function connatix_remove_line () {
    connatix.jQuery('#connatix_ad_management tr').not(':first').not(':last').on("click",function() {
    var tr = connatix.jQuery(this).closest('tr');
    tr.css("background-color","#ff5e06");
    tr.fadeOut(400, function(){
        tr.remove();
        });
    return false;
    });
}   
*/

    