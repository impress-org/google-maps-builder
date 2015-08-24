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

		$this->plugin_slug = Google_Maps_Builder()->get_plugin_slug();

		//Create Settings submenu
		add_action( 'admin_init', array( $this, 'mninit' ) );
		add_action( 'admin_menu', array( $this, 'add_page' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'wp_ajax_hide_welcome', array( $this, 'hide_welcome_callback' ) );
		add_action( 'cmb2_render_lat_lng_default', array( $this, 'cmb2_render_lat_lng_default' ), 10, 2 );

		//Add links/information to plugin row meta
		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links' ), 10, 2 );
		add_filter( 'plugin_action_links', array( $this, 'add_plugin_page_links' ), 10, 2 );
		add_filter( 'cmb2_get_metabox_form_format', array( $this, 'gmb_modify_cmb2_form_output' ), 10, 3 );

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
	 * Hide the Settings welcome on click
	 *
	 * Sets a user meta key that once set
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
					'desc'    => sprintf( __( 'Customize the default slug for the Maps Builder post type. %1$sResave (flush) permalinks%2$s after customizing.', $this->plugin_slug ), '<a href="' . esc_url( '/wp-admin/options-permalink.php' ) . '">"', '</a>' ),
					'default' => 'google-maps',
					'id'      => $prefix . 'custom_slug',
					'type'    => 'text_small'
				),
				array(
					'name'    => __( 'Menu Position', $this->plugin_slug ),
					'desc'    => sprintf( __( 'Set the menu position for Google Maps Builder. See the %1$smenu_position arg%2$s.', $this->plugin_slug ), '<a href="' . esc_url( 'http://codex.wordpress.org/Function_Reference/register_post_type#menu_position' ) . '" class="new-window" target="_blank">', '</a>' ),
					'default' => '21.3',
					'id'      => $prefix . 'menu_position',
					'type'    => 'text_small'
				),
				array(
					'name'    => __( 'Has Archive', $this->plugin_slug ),
					'id'      => $prefix . 'has_archive',
					'desc'    => sprintf( __( 'Controls the post type archive page. See %1s$Resave (flush) permalinks%2s$ after customizing.', $this->plugin_slug ), '<a href="' . esc_url( '/wp-admin/options-permalink.php' ) . '">"', '</a>' ),
					'type'    => 'radio_inline',
					'options' => array(
						'true'  => __( 'Yes', 'cmb' ),
						'false' => __( 'No', 'cmb' ),
					),
				),
				array(
					'name'    => __( 'Opening Map Builder', $this->plugin_slug ),
					'id'      => $prefix . 'open_builder',
					'desc'    => __( 'Do you want the Map Builder customizer to open by default when editing maps?', $this->plugin_slug ),
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
					'name' => __( 'Google Maps API Key', $this->plugin_slug ),
					'desc' => sprintf( __( 'The Google Maps JavaScript API v3 does not require an API key to function correctly. However, Google strongly encourages you to load the Maps API using an APIs Console key which allows you to monitor your Maps API usage. %1$sLearn how to obtain an API key%2$s.', $this->plugin_slug ), '<a href="' . esc_url( 'https://developers.google.com/maps/documentation/javascript/tutorial#api_key' ) . '" target="_blank" class="new-window">', '</a>' ),
					'id'   => $prefix . 'maps_api_key',
					'type' => 'text',
				),
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
	function cmb2_render_lat_lng_default( $field, $meta ) {

		$meta = wp_parse_args(
			$meta, array(
				'geolocate_map' => 'yes',
				'latitude'      => '',
				'longitude'     => '',
			)
		);

		//Geolocate
		$output = '<div id="geolocate-wrap" class="clear">';
		$output .= '<label class="geocode-label size-label">' . __( 'Geolocate Position', $this->plugin_slug ) . ':</label>';
		$output .= '<div class="geolocate-radio-wrap size-labels-wrap">';
		$output .= '<label class="yes-label label-left"><input id="geolocate_map_yes" type="radio" name="' . $field->args['id'] . '[geolocate_map]" class="geolocate_map_radio radio-left" value="yes" ' . ( $meta['geolocate_map'] === 'yes' ? 'checked="checked"' : '' ) . '>' . __( 'Yes', $this->plugin_slug ) . '</label>';

		$output .= '<label class="no-label label-left"><input id="geolocate_map_no" type="radio" name="' . $field->args['id'] . '[geolocate_map]" class="geolocate_map_radio radio-left" value="no" ' . ( $meta['geolocate_map'] === 'no' ? 'checked="checked"' : '' ) . ' >' . __( 'No', $this->plugin_slug ) . '</label>';
		$output .= '</div>';
		$output .= '</div>';

		//lat_lng
		$output .= '<div id="lat-lng-wrap"><div class="coordinates-wrap clear">';
		$output .= '<div class="lat-lng-wrap lat-wrap clear"><span>' . __( 'Latitude', $this->plugin_slug ) . ': </span>
						<input type="text" class="regular-text latitude" name="' . $field->args['id'] . '[latitude]" id="' . $field->args['id'] . '-latitude" value="' . ( $meta['latitude'] ? $meta['latitude'] : $field->args['lat_std'] ) . '" /></div><div class="lat-lng-wrap lng-wrap clear"><span>' . __( 'Longitude', $this->plugin_slug ) . ': </span>
								<input type="text" class="regular-text longitude" name="' . $field->args['id'] . '[longitude]" id="' . $field->args['id'] . '-longitude" value="' . ( $meta['longitude'] ? $meta['longitude'] : $field->args['lng_std'] ) . '" />
								</div>';
		$output .= '<p class="small-desc">' . sprintf( __( 'For quick lat/lng lookup use <a href="%s" class="new-window"  target="_blank">this service</a>', $this->plugin_slug ), esc_url( 'http://www.latlong.net/' ) ) . '</p>';
		$output .= '</div><!-- /.search-coordinates-wrap -->';

		$output .= '</div>'; //end #geolocate-wrap
		$output .= '<p class="cmb2-metabox-description">' . __( 'When creating a new map the plugin will use your current longitude and latitude for the base location. If you see a blank space instead of the map, this is most likely because you have denied permission for location sharing. You may also specify a default longitude and latitude by turning off this option.', $this->plugin_slug ) . '</p>';


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
			$go_pro_link   = '<a href="
https://wordimpress.com/plugins/maps-builder-pro?utm_source=MBF&utm_medium=BANNER&utm_content=LISTING&utm_campaign=MBF%20LISTING" title="' . __( 'Upgrade to Maps Builder Pro', $this->plugin_slug ) . '" target="_blank">' . __( 'Upgrade to Pro', $this->plugin_slug ) . '</a>';
			array_unshift( $links, $settings_link );
			array_push( $links, $go_pro_link );
		}

		return $links;
	}

	function add_plugin_meta_links( $meta, $file ) {

		if ( $file == GMB_PLUGIN_BASE ) {
			$meta[] = "<a href='http://wordpress.org/support/view/plugin-reviews/google-maps-builder' target='_blank' title='" . __( 'Rate Google Maps Builder on WordPress.org', $this->plugin_slug ) . "'>" . __( 'Rate Plugin', $this->plugin_slug ) . "</a>";
			$meta[] = '<a href="http://wordpress.org/support/plugin/google-maps-builder/" target="_blank" title="' . __( 'Get plugin support via the WordPress community', $this->plugin_slug ) . '">' . __( 'Support', $this->plugin_slug ) . '</a>';
			$meta[] = __( 'Thank you for using Maps Builder', $this->plugin_slug );
		}

		return $meta;
	}

	/**
	 * Modify CMB2 Default Form Output
	 *
	 * @param string @args
	 *
	 * @since 2.0
	 *
	 * @param $form_format
	 * @param $object_id
	 * @param $cmb
	 *
	 * @return string
	 */
	function gmb_modify_cmb2_form_output( $form_format, $object_id, $cmb ) {

		//only modify the give settings form
		if ( 'gmb_settings' == $object_id && 'plugin_options' == $cmb->cmb_id ) {

			return '<form class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<div class="gmb-submit-wrap"><input type="submit" name="submit-cmb" value="' . __( 'Save Settings', 'give' ) . '" class="button-primary"></div></form>';
		}

		return $form_format;

	}


}

/**
 * Wrapper function around cmb_get_option
 * @since  0.1.0
 *
 * @param  string $key Options array key
 *
 * @return mixed        Option value
 */
function gmb_get_option( $key = '' ) {
	return cmb2_get_option( Google_Maps_Builder_Settings::key(), $key );
}
