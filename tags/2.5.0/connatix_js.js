/* 
    Document   : connatix_js
    Author     : alex
*/

function page1(){
    $("#form1").show();
    $("#form2").hide();
    $(".poof").hide();            
}
            
function page2(){
    $("#form1").hide();
    $("#form2").show();
    $(".poof").show();            
}
            
function hidesection(){
    $("#form1").toggle();
    $(".poof").toggle();
    $("#form2").toggle();
}
             
function connatix_add_values(){
    var path = $("#path").val(); 
    var token = $("#token").val();
    var dest = $("#dest").val();
    var pos = $("#pos").val();
    if(path!=="" && token!=="" && dest!=="" && pos!=="") {
        var unq= new Date().valueOf();
        $('#connatix_ad_management tr:last').before('<tr><td><input type="hidden" name="connatix_options['+unq+'][path]" value="'+path+'"/>'+path+'</td><td><input type="hidden" name="connatix_options['+unq+'][token]" value="'+token+'"/>'+token+'</td><td><input type="hidden" name="connatix_options['+unq+'][dest]" value="'+dest+'"/>'+dest+'</td><td><input type="hidden" name="connatix_options['+unq+'][pos]" value="'+pos+'"/>'+pos+'</td><td><input type="hidden" name="connatix_options['+unq+'][id]" value="'+unq+'"/><input type="button" style="width: 130px !important;" onclick="connatix_remove_line()" class="button-primary" value="Delete" /></td></tr>');
        $("#path").val('');
        $("#token").val('');
        $("#dest").val('');
        $("#pos").val('');                          
    } else 
        {alert("Inputs cannot be empty!");}
    }
                
function connatix_remove_line () {
    $('#connatix_ad_management tr').not(':first').not(':last').on("click",function() {
    var tr = $(this).closest('tr');
    tr.css("background-color","#ff5e06");
    tr.fadeOut(400, function(){
        tr.remove();
        });
    return false;
    });
}   

    