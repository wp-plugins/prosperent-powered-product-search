<?php
/*
Plugin Name: ProsperShop
Description: Add a shop to your blog in a few clicks.
Version: 3.5
Author: Prosperent Brandon
Author URI: http://prosperent.com
Plugin URI: http://community.prosperent.com/forumdisplay.php?33-Prosperent-Plugins
License: GPLv3

    Copyright 2012  Prosperent Brandon  (email : brandon@prosperent.com)

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

// Default caching time for products (in seconds)
if (!defined( 'PROSPERSHOP_CACHE_PRODS'))
    define( 'PROSPERSHOP_CACHE_PRODS', 604800 );
// Default caching time for trends and coupons (in seconds)
if (!defined( 'PROSPERSHOP_CACHE_COUPS'))
    define( 'PROSPERSHOP_CACHE_COUPS', 3600 );

if (!defined( 'WP_CONTENT_DIR'))
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if (!defined('PROSPERSHOP_URL'))
    define('PROSPERSHOP_URL', plugin_dir_url(__FILE__));
if (!defined('PROSPERSHOP_PATH'))
    define('PROSPERSHOP_PATH', plugin_dir_path(__FILE__));
if (!defined('PROSPERSHOP_BASENAME'))
    define('PROSPERSHOP_BASENAME', plugin_basename(__FILE__));
if (!defined('PROSPERSHOP_FOLDER'))
    define('PROSPERSHOP_FOLDER', plugin_basename(dirname(__FILE__)));
if (!defined('PROSPERSHOP_FILE'))
    define('PROSPERSHOP_FILE', basename((__FILE__)));
if (!defined('PROSPERSHOP_CACHE'))
	define('PROSPERSHOP_CACHE', WP_CONTENT_DIR . '/prosperent_cache');
if (!defined('PROSPERSHOP_INCLUDE'))
	define('PROSPERSHOP_INCLUDE', PROSPERSHOP_PATH . 'includes');
if (!defined('PROSPERSHOP_MODEL'))
	define('PROSPERSHOP_MODEL', PROSPERSHOP_INCLUDE . '/models');
if (!defined('PROSPERSHOP_WIDGET'))
	define('PROSPERSHOP_WIDGET', PROSPERSHOP_INCLUDE . '/widgets');
if (!defined('PROSPERSHOP_VIEW'))
	define('PROSPERSHOP_VIEW', PROSPERSHOP_INCLUDE . '/views');
if (!defined('PROSPERSHOP_IMG'))
	define('PROSPERSHOP_IMG', PROSPERSHOP_URL . 'includes/img');
if (!defined('PROSPERSHOP_JS'))
	define('PROSPERSHOP_JS', PROSPERSHOP_URL . 'includes/js');
if (!defined('PROSPERSHOP_CSS'))
	define('PROSPERSHOP_CSS', PROSPERSHOP_URL . 'includes/css');
if (!defined('PROSPERSHOP_THEME'))
	define('PROSPERSHOP_THEME', WP_CONTENT_DIR . '/prosperent-themes');

error_reporting(0);   

require_once(PROSPERSHOP_INCLUDE . '/ProsperIndexController.php');

if(is_admin())
{
	require_once(PROSPERSHOP_INCLUDE . '/ProsperAdminController.php');
}
