<?php
/**
 * Scripts
 *
 * @package     GMB
 * @subpackage  Functions
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Google_Maps_Builder_Scripts {

	/**
	 * Var for loading google maps api
	 * Var for dependency
	 */
	protected $load_maps_api = true;

	protected $load_maps_api_dep = 'jquery';


	/**
	 * Constructor
	 * @since 0.1.0
	 */
	public function __construct() {

		$this->plugin_slug = Google_Maps_Builder()->get_plugin_slug();

		//Frontend
		add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_scripts' ), 11 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_styles' ) );

		add_action( 'init', array( $this, 'register_gmap_scripts' ) );

		add_action( 'wp_footer', array( $this, 'print_gmap_footer' ), 100 );

		add_action( 'wp_head', array( $this, 'check_for_multiple_google_maps_api_calls' ) );


		//Admin
		add_action( 'admin_init', array( $this, 'multiple_maps_enqueued_warning_ignore' ) );

		add_action( 'admin_notices', array( $this, 'multiple_maps_enqueued_warning' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_pointer_script_style' ) );

		add_action( 'admin_head', array( $this, 'icon_style' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}


	/*----------------------------------------------------------------------------------
	Frontend
	------------------------------------------------------------------------------------*/

	/**
	 * Load Frontend Scripts
	 *
	 * Enqueues the required scripts to display maps on the frontend only.
	 *
	 * @since 1.0
	 * @global $give_options
	 * @global $post
	 * @return void
	 */
	function load_frontend_scripts() {

		$js_dir     = GMB_PLUGIN_URL . 'assets/js/frontend/';
		$js_plugins = GMB_PLUGIN_URL . 'assets/js/plugins/';
		$suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Use minified libraries if SCRIPT_DEBUG is turned off
		wp_register_script( $this->plugin_slug . '-plugin-script', $js_dir . 'google-maps-builder' . $suffix . '.js', array( 'jquery' ), GMB_VERSION, true );
		wp_register_script( 'google-maps-builder-maps-icons', GMB_PLUGIN_URL . 'includes/libraries/map-icons/js/map-icons.js', array( 'jquery' ), GMB_VERSION, true );
		wp_localize_script( $this->plugin_slug . '-plugin-script', 'gmb_data', array() );


	}


	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	function enqueue_frontend_styles() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'google-maps-builder-plugin-styles', GMB_PLUGIN_URL . 'assets/css/google-maps-builder' . $suffix . '.css', array(), GMB_VERSION );
		wp_enqueue_style( 'google-maps-builder-plugin-styles' );

		wp_register_style( 'google-maps-builder-map-icons', GMB_PLUGIN_URL . 'includes/libraries/map-icons/css/map-icons.css', array(), GMB_VERSION );
		wp_enqueue_style( 'google-maps-builder-map-icons' );

	}


	/**
	 * Register Gmaps Scripts
	 *
	 * @description We separate loading from Google's API for compatibility with other themes and plugins
	 *
	 */
	function register_gmap_scripts() {

		wp_register_script( 'google-maps-builder-gmaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places', array( 'jquery' ) );

	}

	/**
	 * Print Gmap Footer
	 */
	function print_gmap_footer() {
		if ( $this->load_maps_api ) {
			wp_print_scripts( 'google-maps-builder-gmaps' );
		}
		wp_print_scripts( 'google-maps-builder-plugin-script' );
		wp_print_scripts( 'google-maps-builder-maps-icons' );
	}

	/**
	 * Load Google Maps API
	 *
	 * @description: Determine if Google Maps API script has already been loaded
	 * @since      : 1.0.3
	 * @return bool $multiple_google_maps_api
	 */
	function check_for_multiple_google_maps_api_calls() {

		global $wp_scripts;

		if ( ! $wp_scripts ) {
			return false;
		}

		//loop through registered scripts
		foreach ( $wp_scripts->registered as $registered_script ) {
			//find any that have the google script as the source, ensure it's not enqueud by this plugin
			if (
				strpos( $registered_script->src, 'maps.googleapis.com/maps/api/js' ) !== false &&
				strpos( $registered_script->handle, 'google-maps-builder' ) === false
			) {

				if ( strpos( $registered_script->src, 'places' ) == false ) {

					$registered_script->src = $registered_script->src . '&libraries=places';

				}

				$this->load_maps_api     = false;
				$this->load_maps_api_dep = $registered_script->handle;
				//ensure we can detect scripts on the frontend from backend; we'll use an option to do this
				if ( ! is_admin() ) {
					update_option( 'gmb_google_maps_conflict', true );
				}

			}

		}

		//Ensure that if user resolved conflict on frontend we remove the option flag
		if ( $this->load_maps_api === false && ! is_admin() ) {
			update_option( 'gmb_google_maps_conflict', false );
		}

	}

	/**
	 * Set Usermeta to ignore the Warning
	 *
	 * @description: The user wants to forget the warning
	 * @since      : 1.0.3
	 */
	function multiple_maps_enqueued_warning_ignore() {
		global $current_user;
		$user_id = $current_user->ID;
		/* If user clicks to ignore the notice, add that to their user meta */
		if ( isset( $_GET['gmb_ignore_maps_notice'] ) && $_GET['gmb_ignore_maps_notice'] == '0' ) {
			add_user_meta( $user_id, 'gmb_ignore_maps_notice', 'true', true );
		}
	}


	/*----------------------------------------------------------------------------------
	WP-Admin
	------------------------------------------------------------------------------------*/

	/**
	 * Admin Notices For Multiple Maps Displayed
	 *
	 * @description: Warns the user that a theme or plugin may be inserting Google Maps API js multiple times
	 * @since      : 1.0.3
	 */
	function multiple_maps_enqueued_warning() {

		global $current_user;
		$user_id                  = $current_user->ID;
		$gmb_google_maps_conflict = get_option( 'gmb_google_maps_conflict' );

		// Check that the user hasn't already clicked to ignore the message and that they have appropriate permissions
		// And, most importantly, that Google Maps are actually being enqueued twice
		if ( ! get_user_meta( $user_id, 'gmb_ignore_maps_notice' ) && current_user_can( 'install_plugins' ) && $this->load_maps_api === false || ! get_user_meta( $user_id, 'gmb_ignore_maps_notice' ) && current_user_can( 'install_plugins' ) && $gmb_google_maps_conflict === '1' ) {

			echo '<div class="updated error clearfix"><p>';
			parse_str( $_SERVER['QUERY_STRING'], $params );
			printf( __( '<strong>Google Maps Conflict Detected:</strong> It appears that a plugin or theme that you are using is including also Google Maps API JavaScript on your website. This means there will be a conflict with the Google Maps Builder plugin. Please <a href="%1$s" target="_blank">dequeue</a> the additional Google Maps JavaScript call to return the plugin to a working state. ' ), 'http://codex.wordpress.org/Function_Reference/wp_dequeue_script' );
			echo '</p>';

			printf( __( '<a href="%1$s" rel="nofollow" class="button" style="display:inline-block; margin: 0 0 10px;">Hide Warning</a>' ), '?' . http_build_query( array_merge( $params, array( 'gmb_ignore_maps_notice' => '0' ) ) ) );

			echo '</div>';

		}
	}

	/**
	 * ADMIN: Activation Welcome Tooltip Scripts
	 *
	 * @param $hook_suffix
	 */
	function admin_enqueue_pointer_script_style( $hook_suffix ) {

		// Assume pointer shouldn't be shown
		$enqueue_pointer_script_style = false;

		// Get array list of dismissed pointers for current user and convert it to array
		$dismissed_pointers = explode( ',', get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		$key                = array_search( 'gmb_welcome_pointer', $dismissed_pointers ); // $key = 2;

		// Check if our pointer is not among dismissed ones
		if ( ! in_array( 'gmb_welcome_pointer', $dismissed_pointers ) ) {
			$enqueue_pointer_script_style = true;

			// Add footer scripts using callback function
			add_action( 'admin_print_footer_scripts', array( $this, 'welcome_pointer_print_scripts' ) );
		}

		// Enqueue pointer CSS and JS files, if needed
		if ( $enqueue_pointer_script_style ) {
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );
		}

	}


	/**
	 * Print Activation Message
	 */
	function welcome_pointer_print_scripts() {
		$pointer_content = '<h3>' . __( 'Welcome to the Maps Builder', $this->plugin_slug ) . '</h3>';
		$pointer_content .= '<p>' . __( 'Thank you for using Maps Builder for WordPress. To stay up to date on the latest plugin updates, enhancements, and news please sign up for our mailing list.', $this->plugin_slug ) . '</p>';
		$pointer_content .= '<div id="mc_embed_signup" style="padding: 0 15px;"><form action="http://wordimpress.us3.list-manage2.com/subscribe/post?u=3ccb75d68bda4381e2f45794c&amp;id=83609e2883" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate><div class="mc-field-group" style="margin: 0 0 10px;"><input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" style="margin-right:5px;width:230px;" placeholder="my.email@wordpress.com"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div><div id="mce-responses" class="clear"><div class="response" id="mce-error-response" style="display:none"></div><div class="response" id="mce-success-response" style="display:none"></div></div><div style="position: absolute; left: -5000px;"><input type="text" name="b_3ccb75d68bda4381e2f45794c_83609e2883" value=""></div></form></div>';
		?>

		<script type="text/javascript">
			//<![CDATA[
			jQuery( document ).ready( function ( $ ) {
				$( '#menu-posts-google_maps' ).pointer( {
					content     : '<?php echo $pointer_content; ?>',
					position    : {
						edge : 'left', // arrow direction
						align: 'center' // vertical alignment
					},
					pointerWidth: 350,
					close       : function () {
						$.post( ajaxurl, {
							pointer: 'gmb_welcome_pointer', // pointer ID
							action : 'dismiss-wp-pointer'
						} );
					}
				} ).pointer( 'open' );
			} );
			//]]>
		</script>

		<?php
	}


	/**
	 * Admin Dashicon
	 *
	 * @description Displays a cute lil map dashicon on our CPT
	 */
	function icon_style() {
		?>
		<style rel="stylesheet" media="screen">
			#adminmenu #menu-posts-google_maps div.wp-menu-image:before {
				font-family: 'dashicons' !important;
				content: '\f231';
			}
		</style>
		<?php return;
	}

	/**
	 *
	 * Register and enqueue admin-specific style sheet.
	 *
	 * Return early if no settings page is registered.
	 * @since     1.0.0
	 *
	 * @param $hook
	 *
	 * @return    null
	 *
	 */
	function enqueue_admin_styles( $hook ) {

		global $post;
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		//Only enqueue scripts for CPT on post type screen
		if ( ( $hook == 'post-new.php' || $hook == 'post.php' ) && 'google_maps' === $post->post_type || $hook == 'google_maps_page_gmb_settings' ) {

			wp_register_style( $this->plugin_slug . '-admin-styles', GMB_PLUGIN_URL . 'assets/css/gmb-admin' . $suffix . '.css', array(), GMB_VERSION );
			wp_enqueue_style( $this->plugin_slug . '-admin-styles' );

			wp_register_style( $this->plugin_slug . '-map-icons', GMB_PLUGIN_URL . 'includes/libraries/map-icons/css/map-icons.css', array(), GMB_VERSION );
			wp_enqueue_style( $this->plugin_slug . '-map-icons' );

		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @param $hook
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	function enqueue_admin_scripts( $hook ) {
		global $post;
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$js_dir     = GMB_PLUGIN_URL . 'assets/js/admin/';
		$js_plugins = GMB_PLUGIN_URL . 'assets/js/plugins/';


		//Only enqueue scripts for CPT on post type screen
		if ( ( $hook == 'post-new.php' || $hook == 'post.php' ) && 'google_maps' === $post->post_type ) {

			wp_enqueue_style( 'wp-color-picker' );

			wp_register_script( $this->plugin_slug . '-admin-magnific-popup', $js_plugins . 'gmb-magnific' . $suffix . '.js', array( 'jquery' ), GMB_VERSION );
			wp_enqueue_script( $this->plugin_slug . '-admin-magnific-popup' );

			wp_register_script( $this->plugin_slug . '-admin-gmaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places', array( 'jquery' ) );
			wp_enqueue_script( $this->plugin_slug . '-admin-gmaps' );

			wp_register_script( $this->plugin_slug . '-map-icons', GMB_PLUGIN_URL . 'includes/libraries/map-icons/js/map-icons.js', array( 'jquery' ) );
			wp_enqueue_script( $this->plugin_slug . '-map-icons' );

			wp_register_script( $this->plugin_slug . '-admin-qtip', $js_plugins . 'jquery.qtip' . $suffix . '.js', array( 'jquery' ), GMB_VERSION, true );
			wp_enqueue_script( $this->plugin_slug . '-admin-qtip' );

			//Map controls
			wp_register_script( $this->plugin_slug . '-admin-map-builder', $js_dir . 'admin-google-map' . $suffix . '.js', array(
				'jquery',
				'wp-color-picker'
			), GMB_VERSION );
			wp_enqueue_script( $this->plugin_slug . '-admin-map-builder' );

			//Modal builder
			wp_register_script( $this->plugin_slug . '-admin-magnific-builder', $js_dir . 'admin-maps-magnific' . $suffix . '.js', array(
				'jquery',
				'wp-color-picker'
			), GMB_VERSION );
			wp_enqueue_script( $this->plugin_slug . '-admin-magnific-builder' );

			//Settings
			wp_register_script( $this->plugin_slug . '-admin-settings', $js_dir . 'admin-settings' . $suffix . '.js', array( 'jquery' ), GMB_VERSION );
			wp_enqueue_script( $this->plugin_slug . '-admin-settings' );

			$api_key   = gmb_get_option( 'gmb_api_key' );
			$geolocate = gmb_get_option( 'gmb_lat_lng' );

			$maps_data = array(
				'api_key'           => $api_key,
				'geolocate_setting' => isset( $geolocate['geolocate_map'] ) ? $geolocate['geolocate_map'] : 'yes',
				'default_lat'       => isset( $geolocate['latitude'] ) ? $geolocate['latitude'] : '32.715738',
				'default_lng'       => isset( $geolocate['longitude'] ) ? $geolocate['longitude'] : '-117.16108380000003',
				'plugin_url'        => GMB_PLUGIN_URL,
				'default_marker'    => apply_filters( 'gmb_default_marker', GMB_PLUGIN_URL . 'assets/img/default-marker.png' ),
				'ajax_loader'       => set_url_scheme( apply_filters( 'gmb_ajax_preloader_img', GMB_PLUGIN_URL . 'assets/images/spinner.gif' ), 'relative' ),
				'snazzy'            => GMB_PLUGIN_URL . 'assets/js/admin/snazzy.json',
				'modal_default'     => gmb_get_option( 'gmb_open_builder' ),
				'i18n'              => array(
					'update_map'               => __( 'Update Map', $this->plugin_slug ),
					'places_selection_changed' => __( 'Place selections have changed.', $this->plugin_slug ),
					'multiple_places'          => __( 'Hmm, it looks like there are multiple places in this area. Please confirm which place you would like this marker to display:', $this->plugin_slug )
				),
			);
			wp_localize_script( $this->plugin_slug . '-admin-map-builder', 'gmb_data', $maps_data );

		}

		wp_enqueue_style( 'dashicons' );


	}

}//end class


