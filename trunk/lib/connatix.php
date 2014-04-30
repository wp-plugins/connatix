<?php
abstract class ConnatixPlugin 
{
   public static $PLUGIN_FILE = "connatix/connatix.php";
   
   protected $plugin_key = "";
   
   protected $_message = null;
    
   
   public function __construct() {
        add_action('admin_init', array($this, 'connatix_init'));
        add_action('admin_init', array($this, 'connatix_restrict_admin'), 1);
        add_action('admin_menu', array($this, 'connatix_add_options_page'));
        add_action('admin_enqueue_scripts',array($this,"register_plugin_static"));
        add_action("plugins_loaded", array($this,"connatix_plugin_loaded"));
        
        add_action('init', array($this,'connatix_permalink'));
        
        add_filter('parse_query', array($this, 'connatix_exclude_pages_from_admin'));
        
        add_filter('plugin_action_links', array($this, 'connatix_plugin_action_links'), 10, 2);
   }
   
   public function connatix_plugin_loaded()
   {
       
   }
   
   public function connatix_permalink()
   {
       global $wp_rewrite;
   }
   
   
   public function handlePost()
   {
       if(isset($_POST["action"]) && $this->validate_post($_POST)) 
            do_action($_POST["action"]);
   }
   /*
    * PUBLIC METHODS
    */
   
   public function connatix_plugin_action_links($links, $file) {
       if ($file == ConnatixPlugin::$PLUGIN_FILE) {
            $connatix_links = '<a href="' . get_admin_url() . 'options-general.php?page='.ConnatixPlugin::$PLUGIN_FILE.'">' . __('Settings') . '</a>';

            // make the 'Settings' link appear first
            array_unshift($links, $connatix_links);
        }

        return $links;
   }
   
   public function connatix_add_options_page() {
        add_options_page('Connatix Plugin Options Page', 'Connatix Plugin', 'manage_options', "connatix/connatix", array($this, 'connatix_render_form'));
   }
   
   public function connatix_restrict_admin() {
        //what if the user is a subscriber ? he will get the die and the error
        //DO NOT UNCOMMENT THIS UNTIL Proper validation
        //if (!current_user_can('manage_options')) {
        //    wp_die(__('You are not allowed to access this part of the site.'));
        //}
   }
   
   public function connatix_show_message($text, $type='updated')
   {
       $this->_message = "<div class='".$type."'><p>".$text."</p></div>";
   }
   
   
   /*
    * ABSTRACT METHODS
    * 
    */
   public function register_plugin_static(){
       wp_register_style('connatix-css', plugin_dir_url(  __FILE__ ) . '../css/connatix.'.$this->plugin_key.'.css' );
       wp_enqueue_style('connatix-css' );
       
       wp_register_script('connatix-js', plugin_dir_url( __FILE__ ) . '../js/connatix.'.$this->plugin_key.'.js');
       wp_enqueue_script( 'connatix-js' );
       
   }
   
   abstract public function validate_post($params);

   public function connatix_init() {
       
       if(isset($_REQUEST["page"]) && $_REQUEST["page"] == ConnatixPlugin::$PLUGIN_FILE)
       {
            $this->handlePost();
       }
   
       register_setting('connatix_plugin_options', 'connatix_options');
   }
   public function connatix_exclude_pages_from_admin($query) {
        global $pagenow, $post_type;

        $options = get_option(ConnatixReplacerPlugin::$OPTIONS_KEY);
        
        if (!is_admin()) {
            return $query;
        }
        
        if (is_admin() && $pagenow == 'edit.php' && is_array($query->query_vars['post__not_in'])) {
              //  array_push ($query->query_vars['post__not_in'], $options->_id);
        }
    }
   
   /*
    * STATIC METHODS
    */
    /*
   
   public static function connatix_add_defaults() {
        $options = get_option('connatix_options');
        if (!is_array($options)) {
            delete_option('connatix_options');
            $arr = array();
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
   public static function connatix_delete_plugin_options() {
        $options = get_option('connatix_options');
        foreach ($options as $row) {
            wp_delete_post($row["page_id"], true);
        }
        delete_option('connatix_options');
    }

   public static function connatix_deactivate_plugin() {
        $options = get_option('connatix_options');
        foreach ($options as $row) {
            wp_delete_post($row["page_id"], true);
        }
   }
     
     */
    
    
}