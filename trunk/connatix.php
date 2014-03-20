
<?php

/**
 * Plugin Name: Connatix
 * Plugin URI: http://connatix.com/
 * Description: This plugin will offer you an easy way to set up your CONNATIX ads. Use Settings to set up your options.
 * Version: 2.1
 * Author: Alexandru Manea
 * Author URI:
 * License: GPL2
 */


/*  Copyright 2014  Alexandru Manea  (email : alexandru.manea@greppysystems.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */

// ------------------------------------------------------------------------------
//function requires_wordpress_version() {
//    global $wp_version;
//    $plugin = plugin_basename(__FILE__);
//    $plugin_data = get_plugin_data(__FILE__, false);
//
//    if (version_compare($wp_version, "3.3", "<")) {
//	if (is_plugin_active($plugin)) {
//	    deactivate_plugins($plugin);
//	    wp_die("'" . $plugin_data['Name'] . "' requires WordPress 3.3 or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to <a href='" . admin_url() . "'>WordPress admin</a>.");
//	}
//    }
//}
//
//add_action('admin_init', 'requires_wordpress_version');


// ------------------------------------------------------------------------------
// Object for WP_Rewrite

$wp_rewrite = new WP_Rewrite();
$ok = 0;
//Use QA? true|false
$use_qa = false;


$ad_params = array (
                "token"         => "",
                "pos"           => "",
                "page_id"       => "",
                "dest"          => "",
                "id"            => "",
                "path"          => ""
);


define('CONNATIX_PLUGIN_URL', plugin_dir_url( __FILE__ ));
// ------------------------------------------------------------------------------
// Delete options table entries ONLY when plugin deactivated AND deleted
function connatix_delete_plugin_options() {
    $options = get_option('connatix_options');
    foreach ($options as $row) {
        wp_delete_post($row[page_id],true);
    }
    delete_option('connatix_options');
    delete_option('connatix_options2');
   
       
}

// ------------------------------------------------------------------------------
// Deletes the page when the plugin is deactivated or else it will appear in the pages list
function connatix_deactivate_plugin(){
    $options = get_option('connatix_options');
    foreach ($options as $row) {
        wp_delete_post($row[page_id],true);
    }
    
}

// ------------------------------------------------------------------------------
// Define default option settings
function connatix_add_defaults() {
    $options = get_option('connatix_options');
    if (!is_array($options)) {
	delete_option('connatix_options'); 
	$arr =  array();
        $arr[1]["token"] = "";
        $arr[1]["pos"] = 1;
        $arr[1]["page_id"] = "";
        $arr[1]["dest"] = "promoted";
        $arr[1]["javascript"] = "";
        $arr[1]["id"] = "1";
        $arr[1]["path"] = "/";

	update_option('connatix_options', $arr);
    }
}


// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_init', 'connatix_init' )
// Init plugin options to white list our options
function connatix_init() {
    register_setting('connatix_plugin_options', 'connatix_options');
    register_setting('connatix_plugin_options2', 'connatix_options2');
}


// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_menu', 'connatix_add_options_page');
// Add menu page
function connatix_add_options_page() {
    add_options_page('Connatix Plugin Options Page', 'Connatix Plugin', 'manage_options', __FILE__, 'connatix_render_form');
}


// ------------------------------------------------------------------------------
// CALLBACK FUNCTION SPECIFIED IN: add_options_page()
// Render the Plugin options form
function connatix_render_form() {
    ?>
<!--Load css ,jquery lib and javascript-->
<link rel="stylesheet" href="<?php echo CONNATIX_PLUGIN_URL. "connatix_design.css?v=2.1"; ?>" type="text/css"/> 
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="<?php echo CONNATIX_PLUGIN_URL. "connatix_js.js"; ?>"></script>

    <!--The page-->
    <div class="wrap">
	<!-- Display Plugin Icon, Header, and Description -->
        <div class="icon32" id="icon-options-general"></div>
	<h2><img src="<?php echo CONNATIX_PLUGIN_URL. "logo_v2.png"; ?> " width="30" height="30" alt="logo_v2" style="vertical-align: top" /> Connatix Plugin Options</h2>
        <br><h3 class="poof" style="padding-left:10px;box-shadow: 8px 4px 6px 2px gray;border-radius: 1px;padding-top: 5px;padding-bottom: 5px;">ADVANCED SETUP <i>(for anvanced users only)</i></h3>
	
    <!-- Beginning of the Plugin Options Form 1 -->
    <form method="post" action="options.php" id="form1">
	    <?php settings_fields('connatix_plugin_options');
	          $options = get_option('connatix_options');
                  $the_page1 = get_page_by_title("<!--" . $options[1]['token'] . "-->");
             ?>

            <!--General settings section with it's fields and notes-->
            <div id="postlist"><div id="heads-up" class="postbox ">
            <h3><span>GENERAL SETTINGS</span></h3>
            <div class="inside">
                
        <table class="form-table">
            <tr>
                <td class="connatix_head"><b>PUBLIC TOKEN:</b></td>
                <td><abbr title="The public token provided."><input type="text" maxlength="100" name="connatix_options[1][token]" value="<?php echo $options[1]['token']; ?>" /></abbr></td>
            </tr>
            <tr>
            <td colspan="2"> <span class="description">* Insert the public token provided, here.</span></td>
            </tr>
        </table></div></div>
        
            <!--Listing ad unit section with it's fields and notes-->
            <div id="heads-up" class="postbox ">
            <h3><span>LISTING AD UNIT</span></h3>
            <div class="inside">
                
        <table class="form-table">
            <tr>
                <td class="connatix_head"><b>LISTING POSITION</b></td>
                <td><abbr title="Position where the ad should be in posts."><input type="number" name="connatix_options[1][pos]" maxlength="4" value="<?php echo $options[1]['pos']; ?>" /></abbr></td>
            </tr>
            <tr>
                <td colspan="3"> <span class="description">* Insert the listing position for the ad.</span></td>
            </tr>
        </table></div></div>
            
            <!--Destination page setup section with it's fields and notes-->
            <div id="heads-up" class="postbox ">
            <h3><span>DESTINATION PAGE SETUP</span></h3>
            <div class="inside">
                
        <table class="form-table">
            <tr>
                <td class="connatix_head"><b>PERMANENT LINK:</b></td>
                <td class="connatix_in"><?php echo get_site_url()."/"; ?><input type="hidden" name="connatix_options[1][id]" value="1"/><input type="hidden" name="connatix_options[1][path]" value="/"/></td>
                <td><abbr title="Custom permalink."><input type="text" name="connatix_options[1][dest]" placeholder = "destpage" maxlength="20" value="<?php echo $the_page1->post_name; ?>" /></abbr></td>
            </tr>
            <tr>
                <td colspan="3"><b><span style="color:green;">Current permalink: &nbsp;</b><?php echo get_permalink($options[1]['page_id']); ?></span></td>
            </tr>
            <tr>
                <td colspan="3"> <span class="description">* Insert the destination page name (or let the default value: "promoted"). This will set your permalink structure to: " /%postname%/ ". Be sure that in Setting -> General, the fields "WordPress Address (URL)" and "Site Address (URL)" are not empty.</span></td>
                
            </tr>
        </table></div></div>
        </div>      
       
                     
            <!--Submit form button-->
            <p class="submit" id="mybutton">
                <input type="submit" class="button-primary" value="Save Changes" />
                <input type="button" class="button-primary" id="connatix-advanced" style="float: right" value="Show Advanced Setup" />
            </p>
    </form>
    <!--End first form-->
        
    <!--Advanced settings section (expandable) with it's fields and notes-->
        
    <!--Second form-->    
    <form method="post" action="options.php" id="form2">  
            <?php settings_fields('connatix_plugin_options');
                   $options = get_option('connatix_options');
            ?>
            <div id="postlist"><div  id="heads-up" class="postbox">
            <h3><span>AD MANAGEMENT</span></h3>
            <div class="inside">

        <table id="connatix_ad_management" class="form-table" border="1" style="border:1px solid #eee ">        
            <!--Table header-->
            <tr>
                <th>AD URL Pattern</th>
                <th>AD Token</th>
                <th>Destination Page</th>
                <th>Listing Position</th> 
                <th>Action</th>
                
            </tr>    
            <!--Adding data to the HTML table-->
            <?php
            $advanced = false;
            if($options!= null){
                $k = 0;$advanced = false;
                foreach ($options as $row) {
                           
            ?>    
            <tr>
                <td><input type="hidden" name="connatix_options[<?php echo $row['id']; ?>][path]" value="<?php echo $row['path']; ?>"/><?php echo $row['path']; ?></td>
                <td><input type="hidden" name="connatix_options[<?php echo $row['id']; ?>][token]" value="<?php echo $row['token']; ?>"/><?php echo $row['token']; ?></td>
                <td><input type="hidden" name="connatix_options[<?php echo $row['id']; ?>][dest]" value="<?php echo $row['dest']; ?>"/><?php echo $row['dest']; ?></td>
                <td><input type="hidden" name="connatix_options[<?php echo $row['id']; ?>][pos]" value="<?php echo $row['pos']; ?>"/><?php echo $row['pos']; ?></td>
                <td><input type="hidden" name="connatix_options[<?php echo $row['id']; ?>][id]" value="<?php echo $row['id']; ?>"/><input type="button" style="width: 130px !important;" onclick="connatix_remove_line()" class="button-primary" value="Delete" /></td>
            </tr>
            <?php
                $k++;
                }
                if($k>1){    
                $advanced = true;
                }
            }    
            ?>    
            <!--Basic input fields for the table-->
            <tr>
                <td><abbr title='Enter the name of the page you want your ad to be on. " / " is for homepage, you can put multiple strings separated by: " , "'><input type="text" maxlength="100" id="path" value="" /></abbr></td>
                <td><abbr title="The public token provided."><input type="text" id="token" maxlength="100" value="" /></abbr></td>
                <td><span style="float: left;font-size: 22px;font-weight: 100;">.../</span><abbr title="Destination page name."><input type="text" id="dest" placeholder = "destpage" maxlength="20" value="" /></abbr></td>
                <td><abbr title="Position where the ad should be in posts."><input type="number" id="pos" value="" style="width:130px" maxlength="4" /></abbr></td>
                <td><input type="button" style="width: 130px !important;" id="connatix_add_values" class="button-primary" value="Add entry" /></td>
            </tr>
        </table></div></div>
            
            <!--Javascript textarea section-->
            <div id="heads-up" class="postbox">
            <h3><span>JAVASCRIPT</span></h3>
            <div class="inside">
                
        <table class="form-table" >
                <tr>
                    <td><b>Javascript code:</b><abbr title = "Here you can add your own javascript for responsiveness or other things." ><textarea name="connatix_options[1][javascript]" rows="5" cols="40" type='textarea'><?php echo $options[1]['javascript']; ?></textarea><br /></abbr></td>
                </tr>
                <tr>
                   <td> <span class="description">* The javascript in this box will pe applied to the ad. Use ' not " .</span></td>
                </tr>
        </table></div></div></div>
            
            <p class="submit" id="mybutton">
                <input type="submit" class="button-primary" value="Save Changes" />
                <input type="button" class="button-primary" id="connatix-advanced2" style="float: right" value="Hide Advanced Setup" />
            </p>
    </form>
    <!--End second form-->
    
    <!--Footer-->
    <br/><br/><hr/><br/><br/><img src="<?php echo CONNATIX_PLUGIN_URL. "logo.png"; ?>" width="194" height="30" alt="logo" style="display: block; margin-left: auto; margin-right: auto;" />
    </div>

        
	
        <!--The javascript for advanced section hide functions + buttons-->
        <script>
            $(document).ready(function(){
                  <?php if ($advanced==false){ ?>
                  page1();        
                  <?php }else{ ?>
                  page2();
                  <?php } ?>
                  $("#connatix-advanced").click(hidesection);
                  $("#connatix-advanced2").click(hidesection);
                  $("#connatix_add_values").click(connatix_add_values);  
            });
            
        </script>
        
        

    <?php
}


// ------------------------------------------------------------------------------
// Cleans input.
function connatix_clean_input() {
    $options_clean = get_option('connatix_options');
    foreach ($options_clean as $row){
        $row['token'] = str_replace(" ", "", $row['token']);
        $row['dest'] = str_replace(" ", "", $row['dest']);
        //$data = wp_filter_nohtml_kses($input[1]['token']); // Sanitize textbox input (strip html tags, and escape characters)
    } 
    update_option('connatix_options', $options_clean);    
}


// ------------------------------------------------------------------------------
// Display a Settings link on the main Plugins page
function connatix_plugin_action_links($links, $file) {
    if ($file == plugin_basename(__FILE__)) {
	$connatix_links = '<a href="' . get_admin_url() . 'options-general.php?page=connatix/connatix.php">' . __('Settings') . '</a>';

	// make the 'Settings' link appear first
	array_unshift($links, $connatix_links);
    }

    return $links;
}


// ------------------------------------------------------------------------------
// Current ad params.
function connatix_ad_params ($token, $pos, $page_id, $dest, $id, $path){
	global $ad_params;
	
	$ad_params = array (
                "token"         => $token,
                "pos"           => $pos,
                "page_id"       => $page_id,
                "dest"          => $dest,
                "id"            => $id,
                "path"          => $path
);
}


// ------------------------------------------------------------------------------
// Verifies if the page should display the ad
  function connatix_verify_page_post_ad(){
      $found = false;
      $options = get_option('connatix_options');
      foreach ($options as $row) {
          if ($row['path'] == "/" && is_home() && $found == false){
              connatix_ad_params($row['token'], $row['pos'], $row['page_id'], $row['dest'], $row['id'], $row['path']);
              $found = true;
              
          }else{
              if($found == false){
                $allinone = str_replace(" ", "", $row['path']); 
                $piece = explode(",",$allinone);                           
                $subject = $_SERVER['REQUEST_URI'];
                for($i=0;$i<sizeof($piece);$i++){
                        if($piece[$i] != null && preg_match("/.*(".$piece[$i].")/i", $subject)){
                        connatix_ad_params($row['token'], $row['pos'], $row['page_id'], $row['dest'], $row['id'], $row['path']);
                        $found = true;
                    }
                                            
                }
             }
          }
          
      }
}


// ------------------------------------------------------------------------------
// Shows the Ad in the list of posts
function connatix_in_loop(){
    global $ok, $use_qa, $ad_params;
    $options = get_option('connatix_options');
    $ok++;
    if($ok == $ad_params['pos']){
    if ($use_qa === true) {
        echo "<script type='text/javascript' src='//qa.cdn.connatix.com/min/connatix.renderer.min.js' mode='fast' data-connatix-event='connatix-loaded' data-connatix-token='" . $ad_params['token'] . "'></script>";
    } else {
        echo "<script type='text/javascript' src='//cdn.connatix.com/min/connatix.renderer.min.js' mode='fast' data-connatix-event='connatix-loaded' data-connatix-token='" .  $ad_params['token'] . "'></script>";
    }
    
    ?>
       
        <script>
            jq_connatix(window).on('connatix-loaded', function (){eval("<?php echo $options[1]['javascript'] ?> ");});
        </script>
        
    <?php
        
    }
}


// ------------------------------------------------------------------------------
// Creates a page with the required code in it
function connatix_create_ad_page(){
    global $wp_rewrite, $use_qa;
    $wp_rewrite->set_permalink_structure( '/%postname%/');
    $options = get_option('connatix_options');
    foreach ($options as $row){
        $the_page_title = "<!--".$row['token']."-->";
        if ($use_qa === true) {
            $the_page_content = "<script type='text/javascript' src='//qa.cdn.connatix.com/min/connatix.renderer.min.js' mode='fast' data-connatix-token='" . $row['token'] . "'></script>";
        } else {
            $the_page_content = "<script type='text/javascript' src='//cdn.connatix.com/min/connatix.renderer.min.js' mode='fast' data-connatix-token='" . $row['token'] . "'></script>";
        }


        $the_page = get_page_by_title($the_page_title);
        if ( ! $the_page ) {
                
                wp_delete_post($row['page_id'],true);
                // Create post object
                $_p = array();
                $_p['post_title'] = $the_page_title;
                $_p['post_name'] = $row['dest'];
                $_p['post_content'] = $the_page_content;
                $_p['post_status'] = 'publish';
                $_p['post_type'] = 'page';
                $_p['comment_status'] = 'closed';
                $_p['ping_status'] = 'closed';
                $_p['post_category'] = array(1); // the default 'Uncatrgorised'


                // Insert the post into the database
                $the_page_id = wp_insert_post( $_p );

            }
            else {
                // the plugin may have been previously active and the page may just be trashed...

                $the_page_id = $the_page->ID;

                //make sure the page is not trashed or changed

                $the_page->post_status = 'publish';
                $the_page->post_title = $the_page_title;
                $the_page->post_name = $row['dest'];
                $the_page->post_content = $the_page_content;
                $the_page_id = wp_update_post( $the_page );


	}
        
        $arr = get_option('connatix_options');
        $page_ids = get_option('connatix_options2');
        if ($page_ids == null){
            $page_ids = array();
        }
            
        
        $arr[$row['id']]['page_id'] = $the_page_id;
        
        if(!in_array($the_page_id, $page_ids)){
            array_push($page_ids, $the_page_id);
        }
           
        update_option('connatix_options', $arr);
        update_option('connatix_options2', $page_ids);
        
    }
        
}


// ------------------------------------------------------------------------------
// Remakes the page after the modifications
function connatix_remake_ad_page() {
    
    $options2 = get_option('connatix_options2');
    for ($i=0; $i<sizeof($options2);$i++) {
            wp_delete_post($options2[$i],true);
            
    }
        
}


// ------------------------------------------------------------------------------
// Excluding the ad page from the header list of pages
//function connatix_inviz_page($excluded_ids){
//     $options = get_option('connatix_options');
//     //$excluded_ids = array();
//     foreach ($options as $row){
//     array_push($excluded_ids, $row[page_id]);
//     }
//     return $excluded_ids;
//     
//}


// ------------------------------------------------------------------------------
// Excluding the ad page from admin panel


function connatix_exclude_pages_from_admin($query) {

   $options = get_option('connatix_options2');
    if (!is_admin()) {
         return $query;
     }
    
    
    global $pagenow, $post_type;
      
    if (is_admin() && $pagenow == 'edit.php') {
        $query->query_vars['post__not_in'] = $options;
    }       
}


// ------------------------------------------------------------------------------
// If not administrator, kill WordPress execution and provide a message
function connatix_restrict_admin(){
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __('You are not allowed to access this part of the site.') );
	}
}




// ------------------------------------------------------------------------
// REGISTER HOOKS & CALLBACK FUNCTIONS:
// ------------------------------------------------------------------------
// Set-up Action and Filter Hooks
// ------------------------------------------------------------------------
// ACTIVATION AND DEACTIVATION HOOK
register_activation_hook(__FILE__, 'connatix_add_defaults'); //when activated
register_uninstall_hook(__FILE__, 'connatix_delete_plugin_options'); //when deleted
register_deactivation_hook(__FILE__, 'connatix_deactivate_plugin'); //when deactivated

// Initialization, setting up option page and the link to it
add_action('admin_init', 'connatix_init');
add_action('admin_init', 'connatix_restrict_admin', 1 );
add_action('admin_menu', 'connatix_add_options_page');
add_filter('plugin_action_links', 'connatix_plugin_action_links', 10, 2);


add_action('loop_start','connatix_verify_page_post_ad');

// THE LOOP: doing stuff in the loop
add_action('the_post','connatix_in_loop');

// Creates the AD page
add_action('connatix_delete_pages', 'connatix_remake_ad_page');
add_action('connatix_create_pages', 'connatix_create_ad_page');
do_action('connatix_delete_pages');
do_action('connatix_create_pages');

// Excludes the AD page from the header list of pages and from admin page
//add_filter('wp_list_pages_excludes', 'connatix_inviz_page');
add_filter( 'parse_query', 'connatix_exclude_pages_from_admin' );
// ------------------------------------------------------------------------



