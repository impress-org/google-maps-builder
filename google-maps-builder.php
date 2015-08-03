<?php
/**
 * Google Maps Builder
 *
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


/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * Get instances of Google_Maps_Builder class
 */
//add_action( 'plugins_loaded', array( 'Google_Maps_Builder', 'get_instance' ) );
//add_action( 'plugins_loaded', array( 'Google_Maps_Builder_Engine', 'get_instance' ) );


if ( ! class_exists( 'Google_Maps_Builder' ) ) : /**
 * Main Give Class
 *
 * @since 2.0
 */ {
	final class Google_Maps_Builder {

		/** Singleton *************************************************************/

		/**
		 * @var Google_Maps_Builder The one true Give
		 * @since 2.0
		 */
		private static $instance;


		/**
		 *
		 * Unique identifier for plugin.
		 *
		 * The variable name is used as the text domain when internationalizing strings
		 * of text. Its value should match the Text Domain file header in the main
		 * plugin file.
		 *
		 * @since    1.0.0
		 *
		 * @var      string
		 */
		protected $plugin_slug = 'google-maps-builder';


		/**
		 * Activation Object
		 *
		 * @var object
		 * @since 2.0
		 */
		public $activate;


		/**
		 * GMB Scripts Object
		 *
		 * @var object
		 * @since 2.0
		 */
		public $scripts;

		/**
		 * GMB Settings Object
		 *
		 * @var object
		 * @since 2.0
		 */
		public $settings;

		/**
		 * GMB Engine Object
		 *
		 * @var object
		 * @since 2.0
		 */
		public $engine;

		/**
		 * GMB Plugin Meta
		 *
		 * @var object
		 * @since 2.0
		 */
		public $meta;

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
				self::$instance->setup_constants();

				add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

				self::$instance->includes();
				self::$instance->activate = new Google_Maps_Builder_Activate();
				self::$instance->scripts  = new Google_Maps_Builder_Scripts();
				self::$instance->settings = new Google_Maps_Builder_Settings();
				self::$instance->engine   = new Google_Maps_Builder_Engine();

				//Init CPT (after CMB2 -> hence the 10000 priority)
				add_action( 'init', array( self::$instance, 'setup_post_type' ), 10000 );

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
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object, therefore we don't want the object to be cloned.
		 *
		 * @since  2.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'gmb' ), '2.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since  2.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'gmb' ), '2.0' );
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since  2.0
		 * @return void
		 */
		private function setup_constants() {

			// Define Constants
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
				define( 'GMB_VERSION', '2.0' );
			}
			// Plugin Root File
			if ( ! defined( 'GMB_PLUGIN_FILE' ) ) {
				define( 'GMB_PLUGIN_FILE', __FILE__ );
			}

		}

		/**
		 * Include required files
		 *
		 * @access private
		 * @since  2.0
		 * @return void
		 */
		private function includes() {

			require_once GMB_PLUGIN_PATH . 'includes/class-gmb-activate.php';
			require_once GMB_PLUGIN_PATH . 'includes/libraries/metabox/init.php';
			require_once GMB_PLUGIN_PATH . 'includes/class-gmb-scripts.php';
			require_once GMB_PLUGIN_PATH . 'includes/class-gmb-widget.php';
			require_once GMB_PLUGIN_PATH . 'includes/class-gmb-engine.php';
			require_once GMB_PLUGIN_PATH . 'includes/admin/class-gmb-settings.php';

			if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

				//Upgrades
				require_once GMB_PLUGIN_PATH . 'includes/admin/upgrades/upgrade-functions.php';
				require_once GMB_PLUGIN_PATH . 'includes/admin/upgrades/upgrades.php';

				//Admin
				require_once GMB_PLUGIN_PATH . 'includes/admin/class-gmb-admin.php';
				require_once GMB_PLUGIN_PATH . 'includes/admin/class-gmb-shortcode-generator.php';

			}

		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since  2.0
		 * @return void
		 */
		public function load_textdomain() {
			// Set filter for Give's languages directory
			$gmb_lang_dir = dirname( plugin_basename( GMB_PLUGIN_FILE ) ) . '/languages/';
			$gmb_lang_dir = apply_filters( 'gmb_languages_directory', $gmb_lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'gmb' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'gmb', $locale );

			// Setup paths to current locale file
			$mofile_local  = $gmb_lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/gmb/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/gmb folder
				load_textdomain( 'gmb', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/gmb/languages/ folder
				load_textdomain( 'gmb', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'gmb', false, $gmb_lang_dir );
			}
		}


		/**
		 * Return the plugin slug.
		 *
		 * @since    1.0.0
		 *
		 * @return   string.
		 */
		public function get_plugin_slug() {
			return $this->plugin_slug;
		}


		/**
		 * Registers and sets up the Maps Builder custom post type
		 *
		 * @since 1.0
		 * @return void
		 */
		function setup_post_type() {

			$post_slug     = gmb_get_option( 'gmb_custom_slug' );
			$menu_position = gmb_get_option( 'gmb_menu_position' );
			$has_archive   = filter_var( gmb_get_option( 'gmb_has_archive' ), FILTER_VALIDATE_BOOLEAN );
			$labels        = array(
				'name'               => _x( 'Google Maps', 'post type general name', $this->plugin_slug ),
				'singular_name'      => _x( 'Map', 'post type singular name', $this->plugin_slug ),
				'menu_name'          => _x( 'Google Maps', 'admin menu', $this->plugin_slug ),
				'name_admin_bar'     => _x( 'Google Maps', 'add new on admin bar', $this->plugin_slug ),
				'add_new'            => _x( 'Add New', 'map', $this->plugin_slug ),
				'add_new_item'       => __( 'Add New Map', $this->plugin_slug ),
				'new_item'           => __( 'New Map', $this->plugin_slug ),
				'edit_item'          => __( 'Edit Map', $this->plugin_slug ),
				'view_item'          => __( 'View Map', $this->plugin_slug ),
				'all_items'          => __( 'All Maps', $this->plugin_slug ),
				'search_items'       => __( 'Search Maps', $this->plugin_slug ),
				'parent_item_colon'  => __( 'Parent Maps:', $this->plugin_slug ),
				'not_found'          => __( 'No Maps found.', $this->plugin_slug ),
				'not_found_in_trash' => __( 'No Maps found in Trash.', $this->plugin_slug ),
			);

			$args = array(
				'labels'             => $labels,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array(
					'slug' => isset( $post_slug ) ? sanitize_title( $post_slug ) : 'google-maps'
				),
				'capability_type'    => 'post',
				'has_archive'        => isset( $has_archive ) ? $has_archive : true,
				'hierarchical'       => false,
				'menu_position'      => ! empty( $menu_position ) ? intval( $menu_position ) : '23.1',
				'supports'           => array( 'title' )
			);

			register_post_type( 'google_maps', $args );

		}


	}
}

endif; // End if class_exists check


/**
 * The main function responsible for returning the one true Give
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $gmb = Give(); ?>
 *
 * @since 2.0
 * @return object - The one true Give Instance
 */
function Google_Maps_Builder() {
	return Google_Maps_Builder::instance();
}

// Get Give Running
Google_Maps_Builder();

