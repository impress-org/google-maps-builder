<?php
/**
 * WordPress Google Maps.
 *
 * The core plugin class
 *
 * @package   Google_Maps_Builder
 * @author    Devin Walker <devin@wordimpress.com>
 * @license   GPL-2.0+
 * @link      http://wordimpress.com
 * @copyright 2014 WordImpress, Devin Walker
 */

class Google_Maps_Builder {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

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
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Read plugin meta
		// Check that function get_plugin_data exists
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$this->meta = get_plugin_data( GMB_PLUGIN_PATH . '/google-maps-builder.php', false );

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		//Initialize metaboxes
		add_action( 'init', array( $this, 'initialize_cmb_meta_boxes' ), 1 );
		//Init CPT
		add_action( 'init', array( $this, 'setup_post_type' ), 1 );

		//CPT
		add_filter( 'manage_edit-google_maps_columns', array( $this, 'setup_custom_columns' ) );
		add_action( 'manage_google_maps_posts_custom_column', array( $this, 'configure_custom_columns' ), 10, 2 );
		add_filter( 'get_user_option_closedpostboxes_google_maps', array( $this, 'closed_meta_boxes' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'admin_head', array( $this, 'icon_style' ) );

	}

	/**
	 * Intialize Custom Metaboxes and Fields
	 *
	 * Admin options are setup using Custom Metaboxes and Fields
	 *
	 * @see: https://github.com/WebDevStudios/Custom-Metaboxes-and-Fields-for-WordPress/wiki/Using-CMB-to-create-an-Admin-Theme-Options-Page
	 * @see: https://github.com/WebDevStudios/Custom-Metaboxes-and-Fields-for-WordPress/wiki/Basic-Usage
	 * @see: https://github.com/WebDevStudios/Custom-Metaboxes-and-Fields-for-WordPress/wiki
	 */
	function initialize_cmb_meta_boxes() {
		require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/metabox/init.php' );
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
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	/**
	 * Registers and sets up the Google Maps BUilder custom post type
	 *
	 * @since 1.0
	 * @return void
	 */
	function setup_post_type() {

		$post_slug     = gmb_get_option( 'gmb_custom_slug' );
		$menu_position = gmb_get_option( 'gmb_menu_position' );
		$has_archive = filter_var(gmb_get_option( 'gmb_has_archive' ), FILTER_VALIDATE_BOOLEAN);

		$labels = array(
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
			'has_archive'        => isset($has_archive) ? $has_archive : true,
			'hierarchical'       => false,
			'menu_position'      => ! empty( $menu_position ) ? intval($menu_position) : 25,
			'supports'           => array( 'title' )
		);

		register_post_type( 'google_maps', $args );

	}

	/**
	 * Icon Style
	 *
	 * @param  {null}
	 *
	 * @return {output} html
	 */
	public function icon_style() {
		?>
		<style rel="stylesheet" media="screen">
			#adminmenu #menu-posts-google_maps div.wp-menu-image:before {
				font-family: 'dashicons' !important;
				content: '\f231';
			}
		</style>
	<?php
	}

	/**
	 * Close certain metaboxes by default
	 *
	 * @param $closed
	 *
	 * @return array
	 */
	function closed_meta_boxes( $closed ) {

		if ( false === $closed ) {
			$closed = array( 'google_maps_options', 'google_maps_control_options', 'google_maps_markers' );
		}

		return $closed;
	}


	function setup_custom_columns( $columns ) {
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'title'     => __( 'Google Map Title', $this->plugin_slug ),
			'shortcode' => __( 'Shortcode', $this->plugin_slug ),
			'date'      => __( 'Creation Date', $this->plugin_slug )
		);

		return $columns;
	}


	/**
	 * Configure Custom Columns
	 *
	 * Sets the content of the custom column contents
	 *
	 * @param $column
	 * @param $post_id
	 */
	function configure_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'shortcode' :

				//Shortcode column with select all input
				$shortcode = htmlentities( '[google_maps id="' . $post_id . '"]' );
				echo '<input onClick="this.setSelectionRange(0, this.value.length)" type="text" class="shortcode-input" readonly value="' . $shortcode . '">';

				break;
			/* Just break out of the switch statement for everything else. */
			default :
				break;
		}
	}


	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide       True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		//Remove Welcome Message Meta so User Sees it Again
		global $current_user;
		$user_id = $current_user->ID;
		delete_user_meta( $user_id, 'gmb_hide_welcome' );

		//Display Tooltip
		$dismissed_pointers = explode( ',', get_user_meta( $user_id, 'dismissed_wp_pointers', true ) );
		add_action( 'admin_enqueue_scripts', array( self::$instance, 'enqueue_pointer_script_style' ) );

		// Check if our pointer is among dismissed ones and delete that mofo
		if ( in_array( 'gmb_welcome_pointer', $dismissed_pointers ) ) {
			$key = array_search( 'gmb_welcome_pointer', $dismissed_pointers );
			delete_user_meta( $user_id, 'dismissed_wp_pointers', $key['gmb_welcome_pointer'] );
		}


		//Multisite Checks
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide       True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}


	/**
	 * Activation Welcome Tooltip Scripts
	 *
	 * @param $hook_suffix
	 */
	function enqueue_pointer_script_style( $hook_suffix ) {

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
		$pointer_content = '<h3>' . __( 'Welcome to the Google Maps Builder', $this->plugin_slug ) . '</h3>';
		$pointer_content .= '<p>' . __( 'Thank you for activating Google Maps Builder for WordPress. To stay up to date on the latest plugin updates, enhancements and news please sign up for our mailing list.', $this->plugin_slug ) . '</p>';
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
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int $blog_id ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		$suffix = defined( 'GMB_DEBUG' ) && GMB_DEBUG ? '' : '.min';

		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/google-maps-builder' . $suffix . '.css', __FILE__ ), array(), self::VERSION );
		wp_enqueue_style( $this->plugin_slug . '-map-icons', plugins_url( 'includes/map-icons/css/map-icons.css', dirname( __FILE__ ) ), array(), self::VERSION );

	}


	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$suffix = defined( 'GMB_DEBUG' ) && GMB_DEBUG ? '' : '.min';

		wp_enqueue_script( $this->plugin_slug . '-gmaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places', array( 'jquery' ) );
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/google-maps-builder' . $suffix . '.js', __FILE__ ), array( 'jquery' ), self::VERSION, true );
		wp_enqueue_script( $this->plugin_slug . '-maps-icons', plugins_url( 'includes/map-icons/js/map-icons.js', dirname( __FILE__ ) ), array( 'jquery' ), self::VERSION, true );

		wp_localize_script( $this->plugin_slug . '-plugin-script', 'gmb_data', array() );

	}


}
