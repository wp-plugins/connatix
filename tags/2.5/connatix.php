<?php

/**
 * Plugin Name: Connatix
 * Plugin URI: http://connatix.com/
 * Description: This plugin will offer you an easy way to set up your CONNATIX ads. Use Settings to set up your options.
 * Version: 2.5
 * Author: Connatix
 * Author URI: http://connatix.com
 * License: GPL2
 */

/*  Copyright 2014 Connatix  (email : support@connatix.com)

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



require_once(ABSPATH . '/wp-admin/includes/plugin.php');
require_once(ABSPATH . WPINC . '/pluggable.php');


//require_once(plugin_dir_path( __FILE__ ) . "lib/menu.php.phtml");

    
//$connatix_menu = null;
//$connatix_menu = new menu();

if(!isset($_GET['plugin']))
    $_GET['plugin'] = null;

switch($_GET['plugin'])
{ 
    case "infeed":
        require_once(plugin_dir_path( __FILE__ ) . "config.php");
        require_once(plugin_dir_path( __FILE__ ) . "lib/connatix.js.php");
        $cntx = new ConnatixJSPlugin();
        //$connatix_menu->connatix_selected_option(2);
    break;
    default :
        require_once(plugin_dir_path( __FILE__ ) . "config.php");
        require_once(plugin_dir_path( __FILE__ ) . "lib/connatix.js.php");
        $cntx = new ConnatixJSPlugin();
        //$connatix_menu->connatix_selected_option(2);
    

}


//ConnatixPlugin::connatix_delete_plugin_options();
