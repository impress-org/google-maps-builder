<?php
/**
 * CMB Theme Options
 * @version 0.1.0
 */
class Google_Maps_Builder_Settings {


	/**
	 * Array of metaboxes/fields
	 * @var array
	 */
	protected static $plugin_options = array();

	public $plugin_slug;

	public $options_page;

	/**
	 * Option key, and option page slug
	 * @var string
	 */
	protected static $key = 'gmb_settings';


	/**
	 * Constructor
	 * @since 0.1.0
	 */
	public function __construct() {

		$plugin            = Google_Maps_Builder::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->meta        = $plugin->meta;


		//Create Settings submenu
		add_action( 'admin_init', array( $this, 'mninit' ) );
		add_action( 'admin_menu', array( $this, 'add_page' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'wp_ajax_hide_welcome', array( $this, 'hide_welcome_callback' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_pointer_script_style' ) );
		add_action( 'cmb_render_lat_lng_default', array( $this, 'cmb_render_lat_lng_default' ), 10, 2 );

		//Add links/information to plugin row meta
		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links' ), 10, 2 );
		add_filter( 'plugin_action_links', array( $this, 'add_plugin_page_links' ), 10, 2 );

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
		$pointer_content .= '<p>' . __( 'Thank you for using Google Maps Builder for WordPress. To stay up to date on the latest plugin updates, enhancements and news please sign up for our mailing list.', $this->plugin_slug ) . '</p>';
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
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function mninit() {

		register_setting( self::$key, self::$key );
	}

	/**
	 * Add menu options page
	 * @since 1.0.0
	 */
	public function add_page() {

		$this->options_page = add_submenu_page(
			'edit.php?post_type=google_maps',
			__( 'Google Maps Builder Settings', $this->plugin_slug ),
			__( 'Settings', $this->plugin_slug ),
			'manage_options',
			self::$key,
			array( $this, 'admin_page_display' )
		);

	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 *
	 * @since     1.0.0
	 *
	 * @param $hook
	 */
	public function enqueue_admin_styles( $hook ) {

		$suffix = defined( 'GMB_DEBUG' ) && GMB_DEBUG ? '' : '.min';
		$screen = get_current_screen();

		//Only enqueue scripts for Setting screen
		if ( $this->options_page == $screen->id ) {

			wp_enqueue_style( $this->plugin_slug . '-settings-grid', plugins_url( 'assets/css/grid' . $suffix . '.css', __FILE__ ), array(), Google_Maps_Builder::VERSION );
			wp_enqueue_style( $this->plugin_slug . '-settings-styles', plugins_url( 'assets/css/admin-settings' . $suffix . '.css', __FILE__ ), array(), Google_Maps_Builder::VERSION );

		}


	}

	/**
	 * Register and enqueue admin-specific JavaScript
	 *
	 *
	 * @since     1.0.0
	 *
	 * @param $hook
	 */
	public function enqueue_admin_scripts( $hook ) {
		global $post;
		$suffix = defined( 'GMB_DEBUG' ) && GMB_DEBUG ? '' : '.min';
		$screen = get_current_screen();

		//Only enqueue scripts for Setting screen
		if ( $this->options_page == $screen->id ) {

			wp_enqueue_script( $this->plugin_slug . '-admin-settings', plugins_url( 'assets/js/admin-settings' . $suffix . '.js', __FILE__ ), array( 'jquery' ), Google_Maps_Builder::VERSION );

		}


	}


	/**
	 * Hide the Settings welcome on click
	 *
	 * Sets a user meta key that once set
	 *
	 */
	public function hide_welcome_callback() {
		global $current_user;
		$user_id = $current_user->ID;
		add_user_meta( $user_id, 'gmb_hide_welcome', 'true', true );
		wp_die(); // ajax call must die to avoid trailing 0 in your response
	}


	/**
	 * Admin page markup. Mostly handled by CMB
	 * @since  0.1.0
	 */
	public function admin_page_display() {

		include( 'views/settings-page.php' );

	}

	/**
	 * General Option Fields
	 * Defines the plugin option metabox and field configuration
	 * @since  1.0.0
	 * @return array
	 */
	public function general_option_fields() {

		// Only need to initiate the array once per page-load
		if ( ! empty( self::$plugin_options ) ) {
			return self::$plugin_options;
		}

		$prefix = 'gmb_';

		self::$plugin_options = array(
			'id'         => 'plugin_options',
			'show_on'    => array( 'key' => 'options-page', 'value' => array( self::$key, ), ),
			'show_names' => true,
			'fields'     => array(
				array(
					'name'    => __( 'Post Type Slug', $this->plugin_slug ),
					'desc'    => sprintf( __( 'Customize the default slug for this post type. <a href="%s">Resave (flush) permalinks</a> after customizing.', $this->plugin_slug ), esc_url( '/wp-admin/options-permalink.php' ) ),
					'default' => 'google-maps',
					'id'      => $prefix . 'custom_slug',
					'type'    => 'text_small'
				),
				array(
					'name'    => __( 'Menu Position', $this->plugin_slug ),
					'desc'    => sprintf( __( 'Set the menu position for Google Maps Builder. See the <a href="%s" class="new-window">menu_position</a> arg.', $this->plugin_slug ), esc_url( 'http://codex.wordpress.org/Function_Reference/register_post_type#Arguments' ) ),
					'default' => '25',
					'id'      => $prefix . 'menu_position',
					'type'    => 'text_small'
				),
				array(
					'name'    => __( 'Has Archive', $this->plugin_slug ),
					'id'      => $prefix . 'has_archive',
					'desc'    => sprintf( __( 'Controls the post type archive page. See <a href="%s">Resave (flush) permalinks</a> after customizing.', $this->plugin_slug ), esc_url( '/wp-admin/options-permalink.php' ) ),
					'type'    => 'radio_inline',
					'options' => array(
						'true'  => __( 'Yes', 'cmb' ),
						'false' => __( 'No', 'cmb' ),
					),
				),
			),
		);

		return self::$plugin_options;

	}

	/**
	 * Map Option Fields
	 * Defines the plugin option metabox and field configuration
	 * @since  1.0.0
	 * @return array
	 */
	public function map_option_fields() {

		// Only need to initiate the array once per page-load
		if ( ! empty( self::$plugin_options ) ) {
			return self::$plugin_options;
		}

		$prefix = 'gmb_';

		self::$plugin_options = array(
			'id'         => 'plugin_options',
			'show_on'    => array( 'key' => 'options-page', 'value' => array( self::$key, ), ),
			'show_names' => true,
			'fields'     => array(
				array(
					'name'           => __( 'Map Size', $this->plugin_slug ),
					'id'             => $prefix . 'width_height',
					'type'           => 'width_height',
					'width_std'      => '100',
					'width_unit_std' => '%',
					'height_std'     => '600',
					'lat_std'        => '32.7153292',
					'lng_std'        => '-117.15725509',
					'desc'           => '',
				),
				array(
					'name'    => __( 'Map Location', $this->plugin_slug ),
					'id'      => $prefix . 'lat_lng',
					'type'    => 'lat_lng_default',
					'lat_std' => '32.7153292',
					'lng_std' => '-117.15725509',
					'desc'    => '',
				),
				array(
					'name' => __( 'Places API Key', $this->plugin_slug ),
					'desc' => sprintf( __( 'API keys are manage through the <a href="%1$s" class="new-window" target="_blank" class="new-window">Google API Console</a>. For more information please see <a href="%2$s"  class="new-window" title="Google Places API Introduction">this article</a>.', $this->plugin_slug ), esc_url( 'https://code.google.com/apis/console/?noredirect' ), esc_url( 'https://developers.google.com/places/documentation/#Authentication' ) ),
					'id'   => $prefix . 'api_key',
					'type' => 'text',
				),
			),
		);

		return self::$plugin_options;

	}

	/**
	 * CMB Lat Lng
	 *
	 * Custom CMB field for Gmap latitude and longitude
	 *
	 * @param $field
	 * @param $meta
	 */
	function cmb_render_lat_lng_default( $field, $meta ) {

		$meta = wp_parse_args(
			$meta, array(
				'geolocate_map' => 'yes',
				'latitude'      => '',
				'longitude'     => '',
			)
		);

		//Geolocate
		$output = '<div id="width_wrap" class="clear">';
		$output .= '<label class="geocode-label size-label">' . __( 'Geolocate Position', $this->plugin_slug ) . ':</label>';
		$output .= '<div id="size_labels_wrap" class="geolocate-radio-wrap">';
		$output .= '<input id="geolocate_map_yes" type="radio" name="' . $field['id'] . '[geolocate_map]" class="geolocate_map_radio radio-left" value="yes" ' . ( $meta['geolocate_map'] === 'yes' ? 'checked="checked"' : '' ) . '><label class="yes-label label-left">' . __( 'Yes', $this->plugin_slug ) . '</label>';

		$output .= '<input id="geolocate_map_no" type="radio" name="' . $field['id'] . '[geolocate_map]" class="geolocate_map_radio radio-left" value="no" ' . ( $meta['geolocate_map'] === 'no' ? 'checked="checked"' : '' ) . ' ><label class="no-label label-left">' . __( 'No', $this->plugin_slug ) . '</label>';
		$output .= '</div>';

		//lat_lng
		$output .= '<div id="lat-lng-wrap"><div class="coordinates-wrap clear">';
		$output .= '<div class="lat-lng-wrap lat-wrap clear"><span>Latitude: </span>
								<input type="text" class="regular-text latitude" name="' . $field['id'] . '[latitude]" id="' . $field['id'] . '-latitude" value="' . ( $meta['latitude'] ? $meta['latitude'] : $field['lat_std'] ) . '" />
								</div>
								<div class="lat-lng-wrap lng-wrap clear"><span>Longitude: </span>
								<input type="text" class="regular-text longitude" name="' . $field['id'] . '[longitude]" id="' . $field['id'] . '-longitude" value="' . ( $meta['longitude'] ? $meta['longitude'] : $field['lng_std'] ) . '" />
								</div>';
		$output .= '<p class="small-desc">' . sprintf( __( 'For quick lat/lng lookup use <a href="%s" class="new-window"  target="_blank">this service</a>', $this->plugin_slug ), esc_url( 'http://www.latlong.net/' ) ) . '</p>';
		$output .= '</div><!-- /.search-coordinates-wrap -->
				</div>';


		echo $output;


	}


	/**
	 * Make public the protected $key variable.
	 * @since  0.1.0
	 * @return string  Option key
	 */
	public static function key() {
		return self::$key;
	}


	/**
	 * Add links to Plugin listings view
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	function add_plugin_page_links( $links, $file ) {

		if ( $file == GMB_PLUGIN_BASE ) {

			// Add Widget Page link to our plugin
			$settings_link = '<a href="edit.php?post_type=google_maps&page=' . self::$key . '" title="' . __( 'Visit the Google Maps Builder plugin settings page', $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>';
			array_unshift( $links, $settings_link );

		}

		return $links;
	}

	function add_plugin_meta_links( $meta, $file ) {

		if ( $file == GMB_PLUGIN_BASE ) {
			$meta[] = "<a href='http://wordpress.org/support/view/plugin-reviews/google-maps-builder' target='_blank' title='" . __( 'Rate Google Maps Builder on WordPress.org', $this->plugin_slug ) . "'>" . __( 'Rate Plugin', $this->plugin_slug ) . "</a>";
			$meta[] = '<a href="http://wordpress.org/support/plugin/google-maps-builder/" target="_blank" title="' . __( 'Get plugin support via the WordPress community', $this->plugin_slug ) . '">' . __( 'Support', $this->plugin_slug ) . '</a>';
			$meta[] = __( 'Thank You for using Google Maps Builder', $this->plugin_slug );
		}

		return $meta;
	}


}

// Get it started
$Google_Maps_Builder_Settings = new Google_Maps_Builder_Settings();

/**
 * Wrapper function around cmb_get_option
 * @since  0.1.0
 *
 * @param  string $key Options array key
 *
 * @return mixed        Option value
 */
function gmb_get_option( $key = '' ) {
	return cmb_get_option( Google_Maps_Builder_Settings::key(), $key );
}