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
        add_action('widgets_init',
            create_function('', 'return register_widget("Connatix_Widget_Infeed");')
        );
        
        add_filter( 'the_content', array($this,'filter_connatix_content_alter' ));
        add_action('wp_head', array($this,'connatix_head'));
   }
   
    public function filter_connatix_content_alter($content)
    {
        if(!is_single())
            return $content;
        
       //check if there is any inpost add installed
       $options = get_option(ConnatixInpostPlugin::$OPTIONS_KEY);
       
       if($options != null && isset($options->_token) && strlen($options->_token) > 10 && ($options->_type == 1 || $options->_type == 2))
       {
           $script = "<script type='text/javascript' src='http://cdn.connatix.com/min/connatix.renderer.inpost.connatix.min.js' data-connatix-token='".$options->_token."'></script>";
           switch($options->_type)
           {
               case 1:
                   return  $content = $script . $content;
               case 2: 
                   return  $content = $content . $script;
                   break;
           }
       }
       
       return $content;
    }
    
    public function connatix_head()
    {
       $valid_page = is_single();
       //insert in the head only if the type is custom location
       if($valid_page)
       {
            $options = get_option(ConnatixInpostPlugin::$OPTIONS_KEY);
            if($options->_type == 0 && $options->_token != null && strlen($options->_token) > 0)
                echo "<script type='text/javascript' src='http://cdn.connatix.com/min/connatix.bootstrap.inpost.min.js' data-token='".$options->_token."' data-position='" . $options->_pos . "' data-path='".$options->_dom_path."'></script>";
       }
       
       
       $options = get_option(ConnatixJSPlugin::$OPTIONS_KEY);
       if($options != null && $options->_skip_adunit == 0 && $options->_token != null && strlen($options->_token) > 0)
       {
           $valid_page = ($options->_categoryID == 0) ? is_home() : is_category($options->_categoryID);
           if($valid_page)
               echo "<script type='text/javascript' src='http://cdn.connatix.com/min/connatix.bootstrap.min.js' data-token='".$options->_token."' data-position='" . $options->_pos . "' data-path='".$options->_dom_path."'></script>";
       }
            
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
        
        $ids = $this->get_product_ids();
        
        if (is_admin() && $pagenow == 'edit.php' && is_array($query->query_vars['post__not_in'])) {
            
            foreach($ids as $id)
            {
                array_push ($query->query_vars['post__not_in'], $id);
            }
        }
        
        return $query;
    }
    
    public function get_product_ids()
    {
        $ids = array();
        
        $options = get_option(ConnatixJSPlugin::$OPTIONS_KEY);
        array_push($ids, $options->_id);
        
        $options = get_option(ConnatixInpostPlugin::$OPTIONS_KEY);
        array_push($ids, $options->_id);
        
        return $ids;
    }
}