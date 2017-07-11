<?php
/**
 * Maps Builder
 *
 * Plugin Name:       Maps Builder
 * Plugin URI:        http://mapsbuilder.wordimpress.com/
 * Description:       Create stylish and powerful Google Maps quickly and easily.
 * Version:           2.1.2
 * Author:            WordImpress
 * Author URI:        https://wordimpress.com/
 * Text Domain:       google-maps-builder
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define Constants
 */
// Plugin Folder Path
if ( ! defined( 'GMB_PLUGIN_PATH' ) ) {
	define( 'GMB_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}
// Plugin Folder URL
if ( ! defined( 'GMB_PLUGIN_URL' ) ) {
	define( 'GMB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
// Plugin base
if ( ! defined( 'GMB_PLUGIN_BASE' ) ) {
	define( 'GMB_PLUGIN_BASE', plugin_basename( __FILE__ ) );
}
// Plugin version
if ( ! defined( 'GMB_VERSION' ) ) {
	define( 'GMB_VERSION', '2.1.2' );
}
// Plugin Root File
if ( ! defined( 'GMB_PLUGIN_FILE' ) ) {
	define( 'GMB_PLUGIN_FILE', __FILE__ );
}


if ( ! class_exists( 'Google_Maps_Builder' ) ) :

	/**
	 * Load plugin if core lib is present
	 */
	if ( ! file_exists( GMB_PLUGIN_PATH . 'vendor/wordimpress/maps-builder-core/core.php' ) ) {
		add_action( 'admin_notices', 'gmb_no_core_lib' );

		/**
		 * Print admin notice if no dependencies
		 *
		 * @uses "admin_notice" hook
		 */
		function gmb_no_core_lib() {
			printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html__( 'Your install of Maps Builder is missing its Composer dependencies and can not load.', 'maps-builder-pro' ) );
		}
	} else {
		require_once GMB_PLUGIN_PATH . 'vendor/wordimpress/maps-builder-core/core.php';

		/**
		 * Main Maps Builder Class
		 *
		 * @since 2.0
		 */
		final class Google_Maps_Builder extends Google_Maps_Builder_Core {


			/**
			 * @var Google_Maps_Builder The one true Google Maps Builder instance
			 * @since 2.0
			 */
			private static $instance;

			/**
			 * Prevent new instances
			 */
			private function __construct() {
				//you can not haz instance
			}

			/**
			 * User meta key for marking welcome message as dismissed
			 *
			 * @since 2.1.0
			 *
			 * @var string
			 */
			protected $hide_welcome_key = 'gmb_hide_welcome';

			/**
			 * Main Google_Maps_Builder Instance
			 *
			 * Insures that only one instance of Google_Maps_Builder exists in memory at any one
			 * time. Also prevents needing to define globals all over the place.
			 *
			 * @since     2.0
			 * @static
			 * @static    var array $instance
			 * @uses      Google_Maps_Builder::setup_constants() Setup the constants needed
			 * @uses      Google_Maps_Builder::includes() Include the required files
			 * @uses      Google_Maps_Builder::load_textdomain() load the language files
			 * @see       Google_Maps_Builder()
			 * @return    Google_Maps_Builder
			 */
			public static function instance() {
				if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Google_Maps_Builder ) ) {

					self::$instance = new Google_Maps_Builder();

					add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

					self::$instance->includes();
					self::$instance->activate = new Google_Maps_Builder_Activate();
					self::$instance->scripts  = new Google_Maps_Builder_Scripts();
					self::$instance->settings = new Google_Maps_Builder_Settings();
					self::$instance->engine   = new Google_Maps_Builder_Engine();
					self::$instance->html     = new Google_Maps_Builder_HTML_Elements();

					// Read plugin meta
					// Check that function get_plugin_data exists
					if ( ! function_exists( 'get_plugin_data' ) ) {
						require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
					}
					self::$instance->meta = get_plugin_data( GMB_PLUGIN_FILE, false );

				}

				return self::$instance;
			}


			/**
			 * Include required files
			 *
			 * @access protected
			 * @since  2.0
			 * @return void
			 */
			protected function includes() {
				$this->include_core_classes();
				$this->load_activate();

				$this->cmb2_load();
				$this->load_files();
				require_once GMB_PLUGIN_PATH . 'includes/class-gmb-scripts.php';
				require_once GMB_PLUGIN_PATH . 'includes/class-gmb-html-elements.php';

				if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

					$this->load_admin();
					//Admin

					//@TODO only load when needed
					$this->init_map_editor_admin();

				}

			}

		}

	}

endif; // End if class_exists check

