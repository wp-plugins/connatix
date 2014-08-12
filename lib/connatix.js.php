<?php

define("CONNATIX_JS_QA", false);
define("CONNATIX_JS_VERSION", "1.0.0");

require_once(plugin_dir_path( __FILE__ ) . "ipg.php");

class ConnatixJSPlugin extends ConnatixPlugin {
    
    public static $OPTIONS_KEY = "connatix_js_options100Release";
    public static $POST_ACTION = "connatix_js_handle_post";
    public static $PAGE_NAME = "connatix100Release";
    
    
    public function __construct() {
        parent::__construct();
        
        // Creates the AD page
        add_action(ConnatixJSPlugin::$POST_ACTION, array($this, 'connatix_create_ad_page'));
        
        add_action('wp_enqueue_scripts', array($this,'connatix_adding_scripts') ); 
        
        add_action( 'admin_notices', array($this,'connatix_admin_notices'));
    }

    public function connatix_admin_notices()
    {
        if($this->_message != null)
        {
            echo $this->_message;
            $this->_message = null;
        }
       
    }
    
   
    
    public function register_plugin_static() {
        wp_register_style('connatix-css', plugin_dir_url(  __FILE__ ) . '../css/connatix.js.css' );
        wp_enqueue_style('connatix-css' );
   
        wp_register_script('connatix-js', plugin_dir_url( __FILE__ ) . '../js/connatix.js.js');
        wp_enqueue_script( 'connatix-js' );
    }

    
    public function connatix_render_form() {
        
        settings_fields('connatix_plugin_options');
        
        //get all the categories from wordpress: 
        $categories = get_categories();
        if($categories == null || !is_array($categories))
            $categories = array();
        
        //set the fields that will be vizible in the phtml file 
        $options = get_option(ConnatixJSPlugin::$OPTIONS_KEY);
        
        if($options == null)
            $options = new ConnatixJSOPtions();
        
        require_once plugin_dir_path( __FILE__ ) . "../forms/connatix.js.form.phtml";
    }

    public function connatix_clean_input() {
        $options_clean = get_option(ConnatixJSPlugin::$OPTIONS_KEY);
        
        foreach ($options_clean as $row) {
            $row['token'] = str_replace(" ", "", $row['token']);
            $row['dest'] = str_replace(" ", "", $row['dest']);
            //$data = wp_filter_nohtml_kses($input[1]['token']); // Sanitize textbox input (strip html tags, and escape characters)
        }
        update_option(ConnatixJSPlugin::$OPTIONS_KEY, $options_clean);
    }

   

    public function connatix_ad_params($token, $pos, $page_id, $dest, $id, $path, $categoryID = 0) {
        global $ad_params;

        $ad_params = array(
            "token" => $token,
            "pos" => $pos,
            "page_id" => $page_id,
            "dest" => $dest,
            "id" => $id,
            "path" => $path,
            "categoryID" => $categoryID
        );
    }

    public function connatix_create_ad_page() {
        
        //if there is no option , create the page
        $options = $this->get_options($_POST);
        
        $page_title = "<!--".ConnatixJSPlugin::$PAGE_NAME."-->";
        $body =  "<script type='text/javascript' src='//cdn.connatix.com/min/connatix.renderer.destination.min.js'></script>";
        
        $page = get_page_by_title($page_title);
        
        if (!$page) {
            $_p = array();
            $_p['post_title'] = $page_title;
            $_p['post_name'] = $options->_dest;
            $_p['post_content'] = $body;
            $_p['post_status'] = 'publish';
            $_p['post_type'] = 'page';
            $_p['comment_status'] = 'closed';
            $_p['ping_status'] = 'closed';
            $_p['post_category'] = array(1);
            
            wp_insert_post($_p);
            
            $page = get_page_by_title($page_title);
        }
        
        //make sure the page is not trashed or changed
        $page->post_status = 'publish';
        $page->post_title = $page_title;
        $page->post_name = $options->_dest;
        $page->post_content = $body;
        
        wp_update_post($page);
        
        $options->_id = $page->ID;
        
        update_option(ConnatixJSPlugin::$OPTIONS_KEY, $options);
        
        $this->connatix_show_message("Connatix <b>InFeed</b> settings successful saved !!","updated");
    }
    
    public function validate_post($params)
    {
        $valid = true;
        
        $valid = $valid && (isset($params["dest"]) && strlen($params["dest"]) > 0);
        if(isset($params["pos"]))
            $valid = $valid && (isset($params["pos"]) && is_numeric($params["pos"]));
        
        if($valid == false)
            $this->connatix_show_message("The data you submitted is invalid", "error");
     
        return $valid;
    }
    
    private function get_options($params)
    {
        $options = new ConnatixJSOPtions();
        
        $options->_token = $params["token"];
        if(isset($params["pos"]))
            $options->_pos = $params["pos"];
        if(isset($params["dest"]))
            $options->_dest = $params["dest"];
        if(isset($params["dom_path"]))
            $options->_dom_path = $params["dom_path"];
        if(isset($params["categoryID"]))
            $options->_categoryID = $params["categoryID"];
        if(isset($params["skip_adunit"]))
            $options->_skip_adunit = isset($params["skip_adunit"]) && $params["skip_adunit"] == 1 ? 1 : 0; 
       
        return $options;
    }    
    
    
    
    function connatix_adding_scripts() {
    }   
}

class ConnatixJSOPtions
{
    public $_id;
    public $_token;
    public $_pos = 1;
    public $_dest;
    public $_path = "/";
    public $_dom_path;
    public $_categoryID = 0; //0 means homepage for now
    public $_skip_adunit = 0;
}
