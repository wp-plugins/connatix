<?php

/**
 * Plugin Name: Connatix
 * Plugin URI: http://connatix.com/
 * Description: This plugin will offer you an easy way to set up your CONNATIX ads. Use Settings to set up your options.
 * Version: 3.1.3
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


global $connatix_product;

require_once(ABSPATH . '/wp-admin/includes/plugin.php');
require_once(ABSPATH . WPINC . '/pluggable.php');

require_once(plugin_dir_path( __FILE__ ) . "config.php");
require_once(plugin_dir_path( __FILE__ ) . "widgets/connatix.widget.infeed.php");
require_once(plugin_dir_path( __FILE__ ) . "lib/connatix.js.php");
require_once(plugin_dir_path( __FILE__ ) . "lib/connatix.inpost.php");

$connatix_product = isset($_GET["connatix_product"]) && ($_GET["connatix_product"] == "infeed" || $_GET["connatix_product"] == "inpost") ? $_GET["connatix_product"] : "infeed";

switch($connatix_product)
{
    case "infeed":
        $cntx = new ConnatixJSPlugin();
    break;
    case "inpost":
        $cntx = new ConnatixInpostPlugin();
    break;
    default:
        $cntx = new ConnatixJSPlugin();
}