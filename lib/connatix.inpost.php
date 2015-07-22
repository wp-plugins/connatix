<?php

require_once(plugin_dir_path(__FILE__) . "connatix.php");

class ConnatixInpostPlugin extends ConnatixPlugin {

    public static $OPTIONS_KEY = "connatix_infeed_options100Release";
    public static $POST_ACTION = "connatix_infeed_handle_post";

    public function __construct() {
        parent::__construct();

        // Creates the AD page
        add_action(ConnatixInpostPlugin::$POST_ACTION, array($this, 'connatix_create_ad_page'));

        add_action('wp_enqueue_scripts', array($this, 'connatix_adding_scripts'));

        add_action('admin_notices', array($this, 'connatix_admin_notices'));
    }

    public function connatix_admin_notices() {
        if ($this->_message != null) {
            echo $this->_message;
            $this->_message = null;
        }
    }

    public function register_plugin_static() {
        wp_register_style('connatix-css', plugin_dir_url(__FILE__) . '../css/connatix.js.css');
        wp_enqueue_style('connatix-css');

        wp_register_script('connatix-js', plugin_dir_url(__FILE__) . '../js/connatix.js.js');
        wp_enqueue_script('connatix-js');
    }

    public function connatix_render_form() {

        settings_fields('connatix_plugin_options');

        //set the fields that will be vizible in the phtml file 
        $options = get_option(ConnatixInpostPlugin::$OPTIONS_KEY);

        if ($options == null)
            $options = new ConnatixInpostOptions();

        require_once plugin_dir_path(__FILE__) . "../forms/connatix.inpost.form.phtml";
    }

    public function connatix_clean_input() {
        $options_clean = get_option(ConnatixInpostPlugin::$OPTIONS_KEY);

        foreach ($options_clean as $row) {
            $row['token'] = str_replace(" ", "", $row['token']);
            $row['dest'] = str_replace(" ", "", $row['dest']);
            //$data = wp_filter_nohtml_kses($input[1]['token']); // Sanitize textbox input (strip html tags, and escape characters)
        }
        update_option(ConnatixInpostPlugin::$OPTIONS_KEY, $options_clean);
    }

    public function validate_post($params) {
        $valid = true;

        $valid = $valid && (isset($params["dest"]) && strlen($params["dest"]) > 0);

        if ($valid == false)
            $this->connatix_show_message("The data you submitted is invalid", "error");

        return $valid;
    }

}

class ConnatixInpostOptions {

    public $_id;
    public $_token;
    public $_pos = 1;
    public $_dest;
    public $_path = "/";
    public $_dom_path;
    public $_categoryID = 0; //0 means homepage for now
    public $_type = 0;

}
