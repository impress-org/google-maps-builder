<?php
/**
 * Google Maps Builder
 *
 * @package   Google_Maps_Builder
 * @author    Devin Walker <devin@wordimpress.com>
 * @license   GPL-2.0+
 * @link      http://wordimpress.com
 * @copyright 2014 WordImpress, Devin Walker
 *
 * @wordpress-google-places
 * Plugin Name:       Google Maps Builder
 * Plugin URI:        http://wordimpress.com/
 * Description:       Create stylish and powerful Google Maps quickly and easily.
 * Version:           2.0
 * Author:            WordImpress
 * Author URI:        http://wordimpress.com/
 * Text Domain:       google-maps-builder
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define Constants
define( 'GMB_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'GMB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GMB_PLUGIN_BASE', plugin_basename( __FILE__ ) );

//CMB2 INIT
require_once( GMB_PLUGIN_PATH . 'includes/libraries/metabox/init.php' );

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
require_once( GMB_PLUGIN_PATH . 'includes/class-wordpress-google-maps.php' );
//require_once( GMB_PLUGIN_PATH . 'includes/class-wordpress-google-maps-widget.php' ); Widget coming soon :)
require_once( GMB_PLUGIN_PATH . 'includes/class-wordpress-google-maps-engine.php' );
require_once( GMB_PLUGIN_PATH . 'includes/admin/class-wordpress-google-maps-settings.php' );


/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Google_Maps_Builder', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Google_Maps_Builder', 'deactivate' ) );

/*
 * Get instances of Google_Maps_Builder class
 */
add_action( 'plugins_loaded', array( 'Google_Maps_Builder', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'Google_Maps_Builder_Engine', 'get_instance' ) );


/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality (CMB2)
 *----------------------------------------------------------------------------*/
if ( is_admin() ) {

	require_once( GMB_PLUGIN_PATH . 'includes/admin/class-wordpress-google-maps-admin.php' );
	add_action( 'plugins_loaded', array( 'Google_Maps_Builder_Admin', 'get_instance' ) );

}

