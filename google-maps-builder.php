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
 * Version:           1.0.1
 * Author:            dlocc, wordimpress
 * Author URI:        http://wordimpress.com/
 * Text Domain:       google-maps-builder
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
// Define Constants
define( 'GMB_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'GMB_PLUGIN_URL', plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) );
define( 'GMB_PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'GMB_DEBUG', false );

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
require_once( plugin_dir_path( __FILE__ ) . 'public/class-wordpress-google-maps.php' );
//require_once( plugin_dir_path( __FILE__ ) . 'public/class-wordpress-google-maps-widget.php' );
require_once( plugin_dir_path( __FILE__ ) . 'admin/class-wordpress-google-maps-settings.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/class-wordpress-google-maps-engine.php' );


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
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/
if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-wordpress-google-maps-admin.php' );
	add_action( 'plugins_loaded', array( 'Google_Maps_Builder_Admin', 'get_instance' ) );

}
