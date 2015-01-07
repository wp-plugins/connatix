var connatix = {
    bind: function(){
        //bind of the go to page functionality
        connatix.jQuery("select[name=categoryID]").change(function(){
            var url = connatix.jQuery(this).find("option:selected").attr("data-link");
            connatix.jQuery(".category-page-link").attr("href", url);
        });
        
        this.refreshAdUnit();
        connatix.jQuery(".ad-unit").find("input[name=skip_adunit]").click(function(){
            connatix.refreshAdUnit();
        });
        
        connatix.jQuery(".ad-unit").find("select[data-ignore=true]").change(function(){
            connatix.refreshAdUnit();
        });
        
        connatix.jQuery(".connatix-ad-delete").click(function(){
           if(confirm("Do you really want to delete this ad unit ?"))
               return true;
           return false;
            
        });
    },
    refreshAdUnit: function()
    {
        var form = connatix.jQuery(connatix.jQuery(".ad-unit")[0]);
        if(form.find("input[data-ignore=true]").length > 0)
        {
            var checked = form.find("input[data-ignore=true]").is(":checked");

            form.find("input").prop('disabled', checked);
            form.find("select").prop('disabled', checked);

            form.find("input[data-ignore=true]").prop("disabled", false);
        }else 
        //if the page is using a select for the ad unit (inpost)    
        if(form.find("select[data-ignore=true]"))
        {
            var checked = parseInt(form.find("select[data-ignore=true]").val()) > 0;
            
            form.find("input").prop('disabled', checked);
            form.find("select").prop('disabled', checked);

            form.find("select[data-ignore=true]").prop("disabled", false);
        }
    }
};


//Bootstrap
connatix.jQuery = jQuery.noConflict();
connatix.jQuery(function(){
    connatix.bind();
});