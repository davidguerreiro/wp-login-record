<?php
/**
 * Plugin Name: Log Record
 * Plugin URI: https://93digital.co.uk
 * Description: This plugin saves and keeps a record of the users who have logged-in in your site.
 * Version: 1.0.0
 * Author: David Guerreiro ( 93Digital )
 * Author URI: https://93digital.co.com
 * License: GPL2
 */

 /*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/

// Make sure this plugin is exposing data when called directly.
if ( ! function_exists( 'add_action' ) ) {
    echo "You know what, I am a WP Plugin so install me properly and do not cheat :)";
    exit();
}

define( 'LOG_VERSION', '1.0' );
define( 'LOG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// activate plugin.
register_activation_hook( __FILE__, array( 'Base', 'plugin_activation' ) );

// load classes - always base first.
require_once( LOG_PLUGIN_DIR . '/class/base.php' );
require_once( LOG_PLUGIN_DIR . '/class/action.php' );

// init plugin.
add_action( 'init', array( 'Base', 'base_init' ) );
add_action( 'init', array( 'Action', 'action_init' ) );


