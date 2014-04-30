<?php


class ConnatixPlugin{
    
    public static $OPTION_GROUP = "connatix_plugin_options";
    public static $OPTION_NAME = "connatix_options";
    public static $POST_ACTION = "connatix_infeed_active";
    public static $PAGE_NAME = "connatix";
    public static $PLUGIN_FILE = "connatix/connatix.php";
    private $AD_PARAMS;
   
    public function __construct() {
        register_activation_hook(__FILE__, 'ConnatixPlugin::connatix_add_defaults'); //when activated
        register_uninstall_hook(__FILE__, 'ConnatixPlugin::connatix_delete_plugin_options'); //when deleted
        register_deactivation_hook(__FILE__,'ConnatixPlugin::connatix_deactivate_plugin'); //when deactivated
        
        add_action('init', array($this,'connatix_permalink'));
        add_action('admin_init', array($this, 'connatix_restrict_admin'), 1);
        add_action('admin_init', array($this, 'connatix_init'),2);
        add_action('admin_init', array($this, 'connatix_valid_to_make_pages'));
        add_action('admin_menu', array($this, 'connatix_add_options_page'));
        add_action('admin_enqueue_scripts',array($this,"connatix_register_plugin_static"));
        add_action("plugins_loaded", array($this,"connatix_plugin_loaded"));
        add_action('loop_start', array($this, 'connatix_verify_page_post_ad'));
        add_action('the_post',array($this, 'connatix_in_loop'));

        
        add_filter('wp_list_pages_excludes', array($this, 'connatix_page_ids'));
        add_filter('parse_query', array($this, 'connatix_exclude_pages_from_admin'));
        add_filter('plugin_action_links', array($this, 'connatix_plugin_action_links'), 10, 2);
        
    }

 
    public function connatix_register_plugin_static() {
        wp_register_style('connatix-css', plugin_dir_url(  __FILE__ ) . '../css/connatix.infeed.server.css' );
        wp_enqueue_style('connatix-css' );
     
        //jquery compatibility
        //wp_enqueue_script( 'jquery' );
     
        wp_register_script('connatix-js', plugin_dir_url( __FILE__ ) . '../js/connatix.infeed.server.js'); 
        wp_enqueue_script( 'connatix-js' ); 
    }

    public function connatix_plugin_loaded()
   {
       
   }
   
    public function connatix_add_defaults() {
        
            $arr =  array();
            $arr[1]["token"] = "";
            $arr[1]["pos"] = 1;
            $arr[1]["page_id"] = 0;
            $arr[1]["dest"] = "promoted";
            $arr[1]["javascript"] = "";
            $arr[1]["id"] = "1";
            $arr[1]["path"] = "/";

	update_option(ConnatixPlugin::$OPTION_NAME, $arr);
    
}

   
    public static function connatix_delete_plugin_options() {
        $options = get_option(ConnatixPlugin::$OPTION_NAME);
        $this->connatix_delete_pages($options);
        delete_option(ConnatixPlugin::$OPTION_NAME);
    }

    public static function connatix_deactivate_plugin() {
        $options = get_option(ConnatixPlugin::$OPTION_NAME);
        $this->connatix_delete_pages($options);
    }

    public function connatix_validation( $input ) {

       foreach( $input as $key => $value) {
           
           $input[$key]["token"] = str_replace(" ", "", $input[$key]["token"]);
           $input[$key]["dest"] = str_replace(" ", "", $input[$key]["dest"]);   
           
        }
     return $input;
    }
   
    public function connatix_add_options_page() {
        add_options_page('Connatix Plugin Options Page', 'Connatix Plugin', 'manage_options', "connatix/connatix", array($this, 'connatix_render_form'));
        
   }
   
   
    public function connatix_permalink()
     {
       global $wp_rewrite;
       
   }
   
   public function connatix_plugin_action_links($links, $file) {
       if ($file == ConnatixPlugin::$PLUGIN_FILE) {
            $connatix_links = '<a href="' . get_admin_url() . 'options-general.php?page='.ConnatixPlugin::$PLUGIN_FILE.'">' . __('Settings') . '</a>';

            // make the 'Settings' link appear first
            array_unshift($links, $connatix_links);
        }

    return $links;
   }
   
       
    public function connatix_init() {
       
       register_setting(ConnatixPlugin::$OPTION_GROUP, ConnatixPlugin::$OPTION_NAME, array($this,'connatix_validation'));
             
   }
   
   public function connatix_render_form() {
        
        $options = get_option(ConnatixPlugin::$OPTION_NAME);
        
            if($options == null){
                $this->connatix_add_defaults();
                $options = get_option(ConnatixPlugin::$OPTION_NAME);
            }
        
        require_once plugin_dir_path( __FILE__ ) . "../forms/connatix.infeed.server.form.phtml";
      
 
        
    }
   
   public function connatix_valid_to_make_pages(){
        
        if (isset($_GET['settings-updated']) && isset($_GET['page'])){
            $valid = $_GET['page']== "connatix/connatix" || $_GET['page']== ConnatixPlugin::$PLUGIN_FILE;
            if ( $valid && $_GET['settings-updated']== true ){
                $options = get_option(ConnatixPlugin::$OPTION_NAME);
                $this->connatix_delete_pages($options);
                $this->connatix_make_pages($options);

            } 
       }     
    }
   
    public function connatix_delete_pages($options) {
                
        foreach ($options as $key => $value) {
            wp_delete_post($options[$key]["page_id"], true);
        }
    }
    
    public function connatix_make_pages($options){
       
       foreach ($options as $key => $value) {
           
               $options[$key]["page_id"] = $this->connatix_create_ad_page( $options[$key]["token"],  $options[$key]["dest"]);
           
       }
       update_option(ConnatixPlugin::$OPTION_NAME, $options);
   }
   
  

    public function connatix_create_ad_page($token, $dest) {
        global $wp_rewrite;
        $page_title = "<!--".ConnatixPlugin::$PAGE_NAME."-->";
        $wp_rewrite->set_permalink_structure( '/%postname%/');
        $qa = CONNATIX_QA ? "qa." : "";
        $body =  "<script type='text/javascript' src='//".$qa."cdn.connatix.com/min/connatix.renderer.min.js' mode='fast' data-connatix-token='" . $token . "'></script>";
        
        
            $_p = array();
            $_p['post_title'] = $page_title;
            $_p['post_name'] = $dest;
            $_p['post_content'] = $body;
            $_p['post_status'] = 'publish';
            $_p['post_type'] = 'page';
            $_p['comment_status'] = 'closed';
            $_p['ping_status'] = 'closed';
            $_p['post_category'] = array(1);
       
            $page_id = wp_insert_post($_p);
          
            return $page_id;
    }

     
   
    public function connatix_verify_page_post_ad() {
        $found = false;
        $options = get_option(ConnatixPlugin::$OPTION_NAME);
       
        foreach ($options as $row) {
            if ($row['path'] == "/" && is_home() && $found == false) {
                $this->connatix_ad_params($row['token'], $row['pos'], $row['page_id'], $row['dest'], $row['id'], $row['path']);
                $found = true;
            } else {
                if ($found == false) {
                    $allinone = str_replace(" ", "", $row['path']);
                    $piece = explode(",", $allinone);
                    $subject = $_SERVER['REQUEST_URI'];
                    for ($i = 0; $i < sizeof($piece); $i++) {
                        if ($piece[$i] != null && preg_match("/.*(" . $piece[$i] . ")/i", $subject)) {
                            $this->connatix_ad_params($row['token'], $row['pos'], $row['page_id'], $row['dest'], $row['id'], $row['path']);
                            $found = true;
                        }
                    }
                }
            }
        }
    }

    public function connatix_in_loop(){
     global $ok;
     $options = get_option(ConnatixPlugin::$OPTION_NAME);
     $qa = CONNATIX_QA ? "qa." : "";
     $ok++;
     if($ok == $this->AD_PARAMS['pos']){
     echo "<script type='text/javascript' src='//".$qa."cdn.connatix.com/min/connatix.renderer.min.js' data-connatix-event='connatix-loaded' mode='fast' data-connatix-token='" . $this->AD_PARAMS['token'] . "'></script>";
     //echo '<script>jq_connatix(window).on("connatix-loaded", function (){eval("' . $options[1]['javascript'] . '")});</script>';
      
     ?>
       
        <script>
            jq_connatix(window).on('connatix-loaded', function (){eval("<?php echo $options[1]['javascript'] ?> ");});
        </script>
        
    <?php
     
     
     }
    }


    public function connatix_page_ids(){
         $options = get_option(ConnatixPlugin::$OPTION_NAME);
         $excluded_ids = array();
         foreach ($options as $row){
         array_push($excluded_ids, $row['page_id']);
         }
         return $excluded_ids;

    }
   

    public function connatix_exclude_pages_from_admin($query) {

        $excluded_ids = $this->connatix_page_ids();
        if (!is_admin()) {
            return $query;
        }


        global $pagenow, $post_type;

        if (is_admin() && $pagenow == 'edit.php') {
            $query->query_vars['post__not_in'] = $excluded_ids;
        }
    }
    
     public function connatix_ad_params($token, $pos, $page_id, $dest, $id, $path) {
         $this->AD_PARAMS = array(
            "token" => $token,
            "pos" => $pos,
            "page_id" => $page_id,
            "dest" => $dest,
            "id" => $id,
            "path" => $path
        );
    }

    
    public function connatix_restrict_admin() {
        echo 'da';die();
        if (!current_user_can('manage_options')) {
            wp_die(__('You are not allowed to access this part of the site.'));
        }
   }
    
               
}

