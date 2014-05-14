_connatix_initialized = false;
_interval = setInterval(function() {
   if(typeof jQuery === "undefined")
       return;
   
   var path = jQuery("meta[name=connatix-token]").attr("data-path");
        
   if(jQuery(path).length>0)
   {
        var token = jQuery("meta[name=connatix-token]").attr("value");
        var pos = jQuery("meta[name=connatix-token]").attr("data-position");
        pos = parseInt(pos);
        
        if(_connatix_initialized == false)
        {
            var script = "<script type='text/javascript' src='//cdn.connatix.com/min/connatix.renderer.min.js' data-connatix-token='"+token+"'></script>";
          
            if(pos == 0)
                jQuery(script).insertBefore(jQuery(jQuery(".jcorg-yt-thumbnails").children()[0]));
            else
                jQuery(script).insertAfter(jQuery(jQuery(".jcorg-yt-thumbnails").children()[pos-1]));
                
            _connatix_initialized = true;
        }
        
        clearInterval(_interval);
   }
   
}, 100);