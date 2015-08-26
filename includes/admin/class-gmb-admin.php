<?php

/**
 * Google Maps Admin
 *
 * The admin is considered the single post view where you build maps
 *
 * @package   Google_Maps_Builder_Admin
 * @author    Devin Walker <devin@wordimpress.com>
 * @license   GPL-2.0+
 * @link      http://wordimpress.com
 * @copyright 2015 WordImpress, Devin Walker
 */
class Google_Maps_Builder_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;


	/**
	 * Array of metaboxes/fields
	 *
	 * @since    1.0.0
	 *
	 * @var array
	 */
	protected static $plugin_options = array();

	/**
	 * Array of metaboxes/fields
	 *
	 * @since    1.0.0
	 *
	 * @var array
	 */
	protected static $default_map_options;


	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		$this->plugin_slug = Google_Maps_Builder()->get_plugin_slug();

		//CPT
		add_filter( 'manage_edit-google_maps_columns', array( $this, 'setup_custom_columns' ) );
		add_action( 'manage_google_maps_posts_custom_column', array( $this, 'configure_custom_columns' ), 10, 2 );
		add_filter( 'get_user_option_closedpostboxes_google_maps', array( $this, 'closed_meta_boxes' ) );

		//Custom Meta Fields
		add_action( 'cmb2_render_google_geocoder', array( $this, 'cmb2_render_google_geocoder' ), 10, 2 );
		add_action( 'cmb2_render_google_maps_preview', array( $this, 'cmb2_render_google_maps_preview' ), 10, 2 );
		add_action( 'cmb2_render_search_options', array( $this, 'cmb2_render_search_options' ), 10, 2 );
		add_action( 'cmb2_render_width_height', array( $this, 'cmb2_render_width_height' ), 10, 2 );
		add_action( 'cmb2_render_lat_lng', array( $this, 'cmb2_render_lat_lng' ), 10, 2 );
		add_action( 'post_submitbox_misc_actions', array( $this, 'gmb_add_shortcode_to_publish_metabox' ) );

		//Add metaboxes and fields to CPT
		add_action( 'cmb2_init', array( $this, 'cpt2_metaboxes_fields' ) );

	}

	/**
	 * Add Shortcode to Publish Metabox
	 * @return bool
	 */
	public function gmb_add_shortcode_to_publish_metabox() {

		if ( 'google_maps' !== get_post_type() ) {
			return false;
		}

		global $post;

		//Only enqueue scripts for CPT on post type screen
		if ( 'google_maps' === $post->post_type ) {
			echo '<a href="#" class="button button-primary" id="map-builder"><span class="dashicons dashicons-location-alt"></span>' . __( 'Open Map Builder', $this->plugin_slug ) . '</a>';
			//Shortcode column with select all input
			$shortcode = htmlentities( '[google_maps id="' . $post->ID . '"]' );
			echo '<div class="shortcode-wrap box-sizing"><label>' . __( 'Map Shortcode:', $this->plugin_slug ) . '</label><input onClick="this.setSelectionRange(0, this.value.length)" type="text" class="shortcode-input" readonly value="' . $shortcode . '"></div>';

		}

		return false;
	}

	/**
	 * Get Default Map Options
	 *
	 * Helper function that returns default map options from settings
	 * @return array
	 */
	public function get_default_map_options() {

		$width_height = gmb_get_option( 'gmb_width_height' );

		$defaults = array(
			'width'       => ( isset( $width_height['width'] ) ) ? $width_height['width'] : '100',
			'width_unit'  => ( isset( $width_height['map_width_unit'] ) ) ? $width_height['map_width_unit'] : '%',
			'height'      => ( isset( $width_height['height'] ) ) ? $width_height['height'] : '600',
			'height_unit' => ( isset( $width_height['map_height_unit'] ) ) ? $width_height['map_height_unit'] : 'px'
		);

		return $defaults;

	}

	/**
	 * Register our setting to WP
	 * @since  1.0.0
	 */
	public function settings_init() {
		register_setting( $this->plugin_slug, $this->plugin_slug );
	}


	/**
	 * Defines the Google Places CPT metabox and field configuration
	 * @since  1.0.0
	 * @return array
	 */
	public function cpt2_metaboxes_fields() {

		$prefix          = 'gmb_';
		$default_options = $this->get_default_map_options();

		// MARKER WITH AUTOCOMPLETE
		$marker_box = cmb2_get_metabox( array(
			'id'           => 'google_maps_markers',
			'title'        => __( 'Map Markers', $this->plugin_slug ),
			'object_types' => array( 'google_maps' ), // post type
			'context'      => 'normal', //  'normal', 'advanced', or 'side'
			'priority'     => 'high', //  'high', 'core', 'default' or 'low'
			'show_names'   => true, // Show field names on the left
		) );
		$marker_box->add_field( array(
			'name' => __( 'Create Marker', $this->plugin_slug ),
			'id'   => $prefix . 'geocoder',
			'type' => 'google_geocoder'
		) );
		$group_field_id = $marker_box->add_field( array(
			'name'        => __( 'Existing Markers', $this->plugin_slug ),
			'id'          => $prefix . 'markers_group',
			'type'        => 'group',
			'description' => __( 'Map marker data is contained within the repeatable fields below. You may add or update marker data here or directly on the map.', $this->plugin_slug ) . '<a href="#" class="button button-small toggle-repeater-groups">' . __( 'Toggle Marker Groups', $this->plugin_slug ) . '</a>',
			'options'     => array(
				'group_title'   => __( 'Marker: {#}', 'cmb' ),
				'add_button'    => __( 'Add Another Marker', $this->plugin_slug ),
				'remove_button' => __( 'Remove Marker', $this->plugin_slug ),
				'sortable'      => true, // beta
			),
		) );
		$marker_box->add_group_field( $group_field_id, array(
			'name' => __( 'Marker Title', $this->plugin_slug ),
			'id'   => 'title',
			'type' => 'text',
		) );
		$marker_box->add_group_field( $group_field_id, array(
			'name'        => __( 'Marker Description', $this->plugin_slug ),
			'description' => __( 'Write a short description for this marker', $this->plugin_slug ),
			'id'          => 'description',
			'type'        => 'textarea_small',
		) );
		$marker_box->add_group_field( $group_field_id, array(
			'name' => __( 'Marker Reference', $this->plugin_slug ),
			'id'   => 'reference',
			'type' => 'text',
		) );
		$marker_box->add_group_field( $group_field_id, array(
			'name' => __( 'Marker Place ID', $this->plugin_slug ),
			'id'   => 'place_id',
			'type' => 'text',
		) );
		$marker_box->add_group_field( $group_field_id, array(
			'name' => __( 'Hide Place Details', $this->plugin_slug ),
			'id'   => 'hide_details',
			'type' => 'checkbox',
		) );
		$marker_box->add_group_field( $group_field_id, array(
			'name' => __( 'Marker Latitude', $this->plugin_slug ),
			'id'   => 'lat',
			'type' => 'text',
		) );
		$marker_box->add_group_field( $group_field_id, array(
			'name' => __( 'Marker Longitude', $this->plugin_slug ),
			'id'   => 'lng',
			'type' => 'text',
		) );
		$marker_box->add_group_field( $group_field_id, array(
			'name'        => __( 'Marker Image', $this->plugin_slug ),
			'id'          => 'marker_img',
			'type'        => 'file',
			'row_classes' => 'gmb-hidden',
			'options'     => array(
				'url'                  => false,
				'add_upload_file_text' => __( 'Add Marker Image', $this->plugin_slug )
			),
		) );
		$marker_box->add_group_field( $group_field_id, array(
			'name'        => __( 'Marker Data', $this->plugin_slug ),
			'id'          => 'marker',
			'row_classes' => 'gmb-hidden',
			'type'        => 'textarea_code',
		) );
		$marker_box->add_group_field( $group_field_id, array(
			'name'        => __( 'Marker Label Data', $this->plugin_slug ),
			'id'          => 'label',
			'row_classes' => 'gmb-hidden',
			'type'        => 'textarea_code',
		) );

		// PREVIEW
		$preview_box = cmb2_get_metabox( array(
			'id'           => 'google_maps_preview_metabox',
			'title'        => __( 'Google Map Preview', $this->plugin_slug ),
			'object_types' => array( 'google_maps' ), // post type
			'context'      => 'normal', //  'normal', 'advanced', or 'side'
			'priority'     => 'high', //  'high', 'core', 'default' or 'low'
			'show_names'   => false, // Show field names on the left
		) );

		$preview_box->add_field( array(
			'name'    => __( 'Map Preview', $this->plugin_slug ),
			'id'      => $prefix . 'preview',
			'type'    => 'google_maps_preview',
			'default' => '',
		) );

		// SEARCH OPTIONS
		$search_options = cmb2_get_metabox( array(
			'id'           => 'google_maps_search_options',
			'title'        => __( 'Google Places', $this->plugin_slug ),
			'object_types' => array( 'google_maps' ), // post type
			'context'      => 'normal', //  'normal', 'advanced', or 'side'
			'priority'     => 'core', //  'high', 'core', 'default' or 'low'
			'show_names'   => true, // Show field names on the left
		) );

		$search_options->add_field(
			array(
				'name'    => __( 'Show Places?', $this->plugin_slug ),
				'desc'    => __( 'Display establishments, prominent points of interest, geographic locations, and more.', $this->plugin_slug ),
				'id'      => $prefix . 'show_places',
				'type'    => 'radio_inline',
				'options' => array(
					'yes' => __( 'Yes', 'cmb' ),
					'no'  => __( 'No', 'cmb' ),
				),
			)
		);

		$search_options->add_field(
			array(
				'name'    => __( 'Search Radius', $this->plugin_slug ),
				'desc'    => __( 'Defines the distance (in meters) within which to return Place results. The maximum allowed radius is 50,000 meters.', $this->plugin_slug ),
				'default' => '1000',
				'id'      => $prefix . 'search_radius',
				'type'    => 'text_small'
			)
		);

		$search_options->add_field(
			array(
				'name'    => __( 'Place Types', $this->plugin_slug ),
				'desc'    => __( 'Select which type of places you would like to display on this map.', $this->plugin_slug ),
				'id'      => $prefix . 'places_search_multicheckbox',
				'type'    => 'multicheck',
				'options' => apply_filters( 'gmb_place_types', array(
					'accounting'              => __( 'Accounting', $this->plugin_slug ),
					'airport'                 => __( 'Airport', $this->plugin_slug ),
					'amusement_park'          => __( 'Amusement Park', $this->plugin_slug ),
					'aquarium'                => __( 'Aquarium', $this->plugin_slug ),
					'art_gallery'             => __( 'Art Gallery', $this->plugin_slug ),
					'atm'                     => __( 'ATM', $this->plugin_slug ),
					'bakery'                  => __( 'Bakery', $this->plugin_slug ),
					'bank'                    => __( 'Bank', $this->plugin_slug ),
					'bar'                     => __( 'Bar', $this->plugin_slug ),
					'beauty_salon'            => __( 'Beauty Salon', $this->plugin_slug ),
					'bicycle_store'           => __( 'Bicycle Store', $this->plugin_slug ),
					'book_store'              => __( 'Book Store', $this->plugin_slug ),
					'bowling_alley'           => __( 'Bowling Alley', $this->plugin_slug ),
					'bus_station'             => __( 'Bus Station', $this->plugin_slug ),
					'cafe'                    => __( 'Cafe', $this->plugin_slug ),
					'campground'              => __( 'Campground', $this->plugin_slug ),
					'car_dealer'              => __( 'Car Dealer', $this->plugin_slug ),
					'car_rental'              => __( 'Car Rental', $this->plugin_slug ),
					'car_repair'              => __( 'Car Repair', $this->plugin_slug ),
					'car_wash'                => __( 'Car Wash', $this->plugin_slug ),
					'casino'                  => __( 'Casino', $this->plugin_slug ),
					'cemetery'                => __( 'Cemetery', $this->plugin_slug ),
					'church'                  => __( 'Church', $this->plugin_slug ),
					'city_hall'               => __( 'City Hall', $this->plugin_slug ),
					'clothing_store'          => __( 'Clothing Store', $this->plugin_slug ),
					'convenience_store'       => __( 'Convenience Store', $this->plugin_slug ),
					'courthouse'              => __( 'Courthouse', $this->plugin_slug ),
					'dentist'                 => __( 'Dentist', $this->plugin_slug ),
					'department_store'        => __( 'Department Store', $this->plugin_slug ),
					'doctor'                  => __( 'Doctor', $this->plugin_slug ),
					'electrician'             => __( 'Electrician', $this->plugin_slug ),
					'electronics_store'       => __( 'Electronics Store', $this->plugin_slug ),
					'embassy'                 => __( 'Embassy', $this->plugin_slug ),
					'establishment'           => __( 'Establishment', $this->plugin_slug ),
					'finance'                 => __( 'Finance', $this->plugin_slug ),
					'fire_station'            => __( 'Fire Station', $this->plugin_slug ),
					'florist'                 => __( 'Florist', $this->plugin_slug ),
					'food'                    => __( 'Food', $this->plugin_slug ),
					'funeral_home'            => __( 'Funeral Home', $this->plugin_slug ),
					'furniture_store'         => __( 'Furniture_store', $this->plugin_slug ),
					'gas_station'             => __( 'Gas Station', $this->plugin_slug ),
					'general_contractor'      => __( 'General Contractor', $this->plugin_slug ),
					'grocery_or_supermarket'  => __( 'Grocery or Supermarket', $this->plugin_slug ),
					'gym'                     => __( 'Gym', $this->plugin_slug ),
					'hair_care'               => __( 'Hair Care', $this->plugin_slug ),
					'hardware_store'          => __( 'Hardware Store', $this->plugin_slug ),
					'health'                  => __( 'Health', $this->plugin_slug ),
					'hindu_temple'            => __( 'Hindu Temple', $this->plugin_slug ),
					'home_goods_store'        => __( 'Home Goods Store', $this->plugin_slug ),
					'hospital'                => __( 'Hospital', $this->plugin_slug ),
					'insurance_agency'        => __( 'Insurance Agency', $this->plugin_slug ),
					'jewelry_store'           => __( 'Jewelry Store', $this->plugin_slug ),
					'laundry'                 => __( 'Laundry', $this->plugin_slug ),
					'lawyer'                  => __( 'Lawyer', $this->plugin_slug ),
					'library'                 => __( 'Library', $this->plugin_slug ),
					'liquor_store'            => __( 'Liquor Store', $this->plugin_slug ),
					'local_government_office' => __( 'Local Government Office', $this->plugin_slug ),
					'locksmith'               => __( 'Locksmith', $this->plugin_slug ),
					'lodging'                 => __( 'Lodging', $this->plugin_slug ),
					'meal_delivery'           => __( 'Meal Delivery', $this->plugin_slug ),
					'meal_takeaway'           => __( 'Meal Takeaway', $this->plugin_slug ),
					'mosque'                  => __( 'Mosque', $this->plugin_slug ),
					'movie_rental'            => __( 'Movie Rental', $this->plugin_slug ),
					'movie_theater'           => __( 'Movie Theater', $this->plugin_slug ),
					'moving_company'          => __( 'Moving Company', $this->plugin_slug ),
					'museum'                  => __( 'Museum', $this->plugin_slug ),
					'night_club'              => __( 'Night Club', $this->plugin_slug ),
					'painter'                 => __( 'Painter', $this->plugin_slug ),
					'park'                    => __( 'Park', $this->plugin_slug ),
					'parking'                 => __( 'Parking', $this->plugin_slug ),
					'pet_store'               => __( 'Pet Store', $this->plugin_slug ),
					'pharmacy'                => __( 'Pharmacy', $this->plugin_slug ),
					'physiotherapist'         => __( 'Physiotherapist', $this->plugin_slug ),
					'place_of_worship'        => __( 'Place of Worship', $this->plugin_slug ),
					'plumber'                 => __( 'Plumber', $this->plugin_slug ),
					'police'                  => __( 'Police', $this->plugin_slug ),
					'post_office'             => __( 'Post Office', $this->plugin_slug ),
					'real_estate_agency'      => __( 'Real Estate Agency', $this->plugin_slug ),
					'restaurant'              => __( 'Restaurant', $this->plugin_slug ),
					'roofing_contractor'      => __( 'Roofing Contractor', $this->plugin_slug ),
					'rv_park'                 => __( 'RV Park', $this->plugin_slug ),
					'school'                  => __( 'School', $this->plugin_slug ),
					'shoe_store'              => __( 'Shoe Store', $this->plugin_slug ),
					'shopping_mall'           => __( 'Shopping Mall', $this->plugin_slug ),
					'spa'                     => __( 'Spa', $this->plugin_slug ),
					'stadium'                 => __( 'Stadium', $this->plugin_slug ),
					'storage'                 => __( 'Storage', $this->plugin_slug ),
					'store'                   => __( 'Store', $this->plugin_slug ),
					'subway_station'          => __( 'Subway Station', $this->plugin_slug ),
					'synagogue'               => __( 'Synagogue', $this->plugin_slug ),
					'taxi_stand'              => __( 'Taxi Stand', $this->plugin_slug ),
					'train_station'           => __( 'Train Station', $this->plugin_slug ),
					'travel_agency'           => __( 'Travel Agency', $this->plugin_slug ),
					'university'              => __( 'University', $this->plugin_slug ),
					'veterinary_care'         => __( 'Veterinary Care', $this->plugin_slug ),
					'zoo'                     => __( 'Zoo', $this->plugin_slug )
				) )
			)
		);

		/**
		 * Display Options
		 */
		$display_options = cmb2_get_metabox( array(
			'id'           => 'google_maps_options',
			'title'        => __( 'Display Options', $this->plugin_slug ),
			'object_types' => array( 'google_maps' ), // post type
			'context'      => 'side', //  'normal', 'advanced', or 'side'
			'priority'     => 'default', //  'high', 'core', 'default' or 'low'
			'show_names'   => true, // Show field names on the left
		) );

		$display_options->add_field( array(
			'name'           => __( 'Map Size', $this->plugin_slug ),
			'id'             => $prefix . 'width_height',
			'type'           => 'width_height',
			'width_std'      => $default_options['width'],
			'width_unit_std' => $default_options['width_unit'],
			'height_std'     => $default_options['height'],
			'desc'           => '',
		) );
		$display_options->add_field( array(
			'name'    => __( 'Map Location', $this->plugin_slug ),
			'id'      => $prefix . 'lat_lng',
			'type'    => 'lat_lng',
			'lat_std' => '',
			'lng_std' => '',
			'desc'    => '',
		) );
		$display_options->add_field( array(
			'name'    => __( 'Map Type', $this->plugin_slug ),
			'id'      => $prefix . 'type',
			'type'    => 'select',
			'default' => 'default',
			'options' => array(
				'RoadMap'   => __( 'Road Map', $this->plugin_slug ),
				'Satellite' => __( 'Satellite', $this->plugin_slug ),
				'Hybrid'    => __( 'Hybrid', $this->plugin_slug ),
				'Terrain'   => __( 'Terrain', $this->plugin_slug )
			),
		) );

		$display_options->add_field( array(
			'name'    => 'Map Theme',
			'desc'    => sprintf( __( 'Set optional preconfigured %1$sSnazzy Maps%2$s styles. %3$sUpgrade to Pro%2$s for more styles and to set your own.', $this->plugin_slug ), '<a href="' . esc_url( 'http://snazzymaps.com' ) . '" class="snazzy-link new-window"  target="_blank">', '</a>', '<a href="' . esc_url( 'https://wordimpress.com/plugins/maps-builder-pro/?utm_source=MBF&utm_medium=BANNER&utm_term=MAP_OPTIONS&utm_campaign=MBF' ) . '" class="new-window"  target="_blank">' ),
			'id'      => $prefix . 'theme',
			'type'    => 'select',
			'default' => 'none',
			'options' => array(
				'none' => __( 'None', $this->plugin_slug ),
				'68'   => __( 'Aqua', $this->plugin_slug ),
				'73'   => __( 'A Dark World', $this->plugin_slug ),
				'28'   => __( 'Bluish', $this->plugin_slug ),
				'80'   => __( 'Cool Grey', $this->plugin_slug ),
				'77'   => __( 'Clean Cut', $this->plugin_slug ),
				'36'   => __( 'Flat Green', $this->plugin_slug ),
				'44'   => __( 'MapBox', $this->plugin_slug ),
				'83'   => __( 'Muted Blue', $this->plugin_slug ),
				'22'   => __( 'Old Timey', $this->plugin_slug ),
				'1'    => __( 'Pale Dawn', $this->plugin_slug ),
				'19'   => __( 'Paper', $this->plugin_slug ),
				'37'   => __( 'Lunar Landscape', $this->plugin_slug ),
				'75'   => __( 'Shade of Green', $this->plugin_slug ),
				'27'   => __( 'Shift Worker', $this->plugin_slug ),
				'15'   => __( 'Subtle Grayscale', $this->plugin_slug ),
				'50'   => __( 'The Endless Atlas', $this->plugin_slug ),
			)
		) );
		$display_options->add_field( array(
			'name'        => __( 'Map Theme JSON', $this->plugin_slug ),
			'desc'        => 'Contains the map theme JSON',
			'row_classes' => 'hidden',
			'default'     => 'none',
			'id'          => $prefix . 'theme_json',
			'type'        => 'textarea_code'
		) );

		$display_options->add_field( array(
			'name'    => 'Zoom',
			'desc'    => __( 'Adjust the map zoom (0-21)', $this->plugin_slug ),
			'id'      => $prefix . 'zoom',
			'type'    => 'select',
			'default' => '15',
			'options' => apply_filters( 'gmb_map_zoom_levels', array(
					'21' => '21',
					'20' => '20',
					'19' => '19',
					'18' => '18',
					'17' => '17',
					'16' => '16',
					'15' => '15',
					'14' => '14',
					'13' => '13',
					'12' => '12',
					'11' => '11',
					'10' => '10',
					'9'  => '9',
					'8'  => '8',
					'7'  => '7',
					'6'  => '6',
					'5'  => '5',
					'4'  => '4',
					'3'  => '3',
					'2'  => '2',
					'1'  => '1',
					'0'  => '0',
				)
			)
		) );

		// CONTROL OPTIONS

		$control_options = cmb2_get_metabox( array(
			'id'           => 'google_maps_control_options',
			'title'        => __( 'Map Controls', $this->plugin_slug ),
			'object_types' => array( 'google_maps' ), // post type
			'context'      => 'side', //  'normal', 'advanced', or 'side'
			'priority'     => 'default', //  'high', 'core', 'default' or 'low'
			'show_names'   => true, // Show field names on the left
		) );

		$control_options->add_field( array(
			'name'        => __( 'Zoom Control', $this->plugin_slug ),
			'before'      => '<span class="gmb-tooltip-text">' . __( 'Test test 123', $this->plugin_slug ) . '</span>',
			'row_classes' => 'gmb-tooltip-field',
			'id'          => $prefix . 'zoom_control',
			'type'        => 'select',
			'default'     => 'default',
			'options'     => array(
				'none'    => __( 'None', $this->plugin_slug ),
				'small'   => __( 'Small', $this->plugin_slug ),
				'large'   => __( 'Large', $this->plugin_slug ),
				'default' => __( 'Default', $this->plugin_slug ),
			),
		) );

		$control_options->add_field( array(
			'name'    => __( 'Street View', $this->plugin_slug ),
			'id'      => $prefix . 'street_view',
			'type'    => 'select',
			'default' => 'true',
			'options' => array(
				'none' => __( 'None', $this->plugin_slug ),
				'true' => __( 'Standard', $this->plugin_slug ),
			),
		) );

		$control_options->add_field( array(
			'name'    => __( 'Pan Control', $this->plugin_slug ),
			'id'      => $prefix . 'pan',
			'type'    => 'select',
			'default' => 'true',
			'options' => array(
				'none' => __( 'None', $this->plugin_slug ),
				'true' => __( 'Standard', $this->plugin_slug ),
			),
		) );

		$control_options->add_field( array(
			'name'    => __( 'Map Type Control', $this->plugin_slug ),
			'id'      => $prefix . 'map_type_control',
			'type'    => 'select',
			'default' => 'horizontal_bar',
			'options' => array(
				'none'           => __( 'None', $this->plugin_slug ),
				'dropdown_menu'  => __( 'Dropdown Menu', $this->plugin_slug ),
				'horizontal_bar' => __( 'Horizontal Bar', $this->plugin_slug ),
			),
		) );

		$control_options->add_field( array(
			'name'    => __( 'Draggable Map', $this->plugin_slug ),
			'id'      => $prefix . 'draggable',
			'type'    => 'select',
			'default' => 'true',
			'options' => array(
				'none' => __( 'None', $this->plugin_slug ),
				'true' => __( 'Standard', $this->plugin_slug ),
			),
		) );

		$control_options->add_field( array(
			'name'    => __( 'Double Click to Zoom', $this->plugin_slug ),
			'id'      => $prefix . 'double_click',
			'type'    => 'select',
			'default' => 'true',
			'options' => array(
				'none' => __( 'None', $this->plugin_slug ),
				'true' => __( 'Standard', $this->plugin_slug ),
			),
		) );

		$control_options->add_field( array(
			'name'    => __( 'Mouse Wheel to Zoom', $this->plugin_slug ),
			'id'      => $prefix . 'wheel_zoom',
			'type'    => 'select',
			'default' => 'true',
			'options' => array(
				'none' => __( 'Disabled', $this->plugin_slug ),
				'true' => __( 'Standard', $this->plugin_slug ),
			),
		) );

	}

	/**
	 * CMB Width Height
	 *
	 * Custom CMB field for Gmap width and height
	 *
	 * @param $field
	 * @param $meta
	 */
	function cmb2_render_width_height( $field, $meta ) {
		$default_options = $this->get_default_map_options();
		$meta            = wp_parse_args(
			$meta, array(
				'width'           => $default_options['width'],
				'map_width_unit'  => $default_options['width_unit'],
				'height'          => $default_options['height'],
				'map_height_unit' => $default_options['height_unit'],
			)
		);

		$output = '<div id="width_height_wrap" class="clear clearfix">';
		//width
		$output .= '<div id="width_wrap" class="clear">';
		$output .= '<label class="width-label size-label">' . __( 'Width', $this->plugin_slug ) . '</label><input type="text" class="regular-text map-width" name="' . $field->args( 'id' ) . '[width]" id="' . $field->args( 'id' ) . '-width" value="' . ( $meta['width'] ? $meta['width'] : $field->args( 'width_std' ) ) . '" />';
		$output .= '<div class="size-labels-wrap">';
		$output .= '<input id="width_unit_percent" type="radio" name="' . $field->args( 'id' ) . '[map_width_unit]" class="width_radio" value="%" ' . ( $meta['map_width_unit'] === '%' || $field->args( 'width_unit_std' ) === '%' ? 'checked="checked"' : '' ) . '><label class="width_unit_label">%</label>';
		$output .= '<input id="width_unit_px" type="radio" name="' . $field->args( 'id' ) . '[map_width_unit]" class="width_radio" value="px" ' . ( $meta['map_width_unit'] === 'px' ? 'checked="checked"' : '' ) . ' ><label class="width_unit_label">px</label>';
		$output .= '</div>';
		$output .= '</div>';

		//height
		$output .= '<div id="height_wrap" class="clear clearfix">';
		$output .= '<label for="' . $field->args( 'id' ) . '[height]" class="height-label size-label">' . __( 'Height', $this->plugin_slug ) . ':</label><input type="text" class="regular-text map-height" name="' . $field->args( 'id' ) . '[height]" id="' . $field->args( 'id' ) . '-height" value="' . ( $meta['height'] ? $meta['height'] : $field->args( 'height_std' ) ) . '" />';

		$output .= '<div class="size-labels-wrap">';
		$output .= '<input id="height_unit_percent" type="radio" name="' . $field->args( 'id' ) . '[map_height_unit]" class="height_radio" value="%" ' . ( $meta['map_height_unit'] === '%' || $field->args( 'height_unit_std' ) === '%' ? 'checked="checked"' : '' ) . '><label class="height_unit_label">%</label>';
		$output .= '<input id="height_unit_px" type="radio" name="' . $field->args( 'id' ) . '[map_height_unit]" class="height_radio" value="px" ' . ( $meta['map_height_unit'] === 'px' ? 'checked="checked"' : '' ) . ' ><label class="height_unit_label">px</label>';
		$output .= '</div>';
		$output .= '</div>';

		$output .= '<p class="cmb2-metabox-description">' . __( 'Configure the default map width and height.', $this->plugin_slug ) . '</p>';

		$output .= '</div>'; //end #width_height_wrap

		echo $output;

	}


	/**
	 * CMB Lat Lng
	 *
	 * Custom CMB field for Gmap latitude and longitude
	 *
	 * @param $field
	 * @param $meta
	 */
	function cmb2_render_lat_lng( $field, $meta ) {
		$meta = wp_parse_args(
			$meta, array(
				'latitude'  => '',
				'longitude' => '',
			)
		);

		//lat lng
		$output = '<div id="lat-lng-wrap">
					<div class="coordinates-wrap clear">
							<div class="lat-lng-wrap lat-wrap clear"><span>' . __( 'Latitude:', $this->plugin_slug ) . '</span>
							<input type="text" class="regular-text latitude" name="' . $field->args( 'id' ) . '[latitude]" id="' . $field->args( 'id' ) . '-latitude" value="' . ( $meta['latitude'] ? $meta['latitude'] : $field->args( 'lat_std' ) ) . '" />
							</div>
							<div class="lat-lng-wrap lng-wrap clear"><span>' . __( 'Longitude:', $this->plugin_slug ) . '</span>
							<input type="text" class="regular-text longitude" name="' . $field->args( 'id' ) . '[longitude]" id="' . $field->args( 'id' ) . '-longitude" value="' . ( $meta['longitude'] ? $meta['longitude'] : $field->args( 'lng_std' ) ) . '" />
							</div>';
		$output .= '<div class="wpgp-message lat-lng-change-message clear"><p>' . __( 'Lat/lng changed', $this->plugin_slug ) . '</p><a href="#" class="button lat-lng-update-btn button-small" data-lat="" data-lng="">' . __( 'Update', $this->plugin_slug ) . '</a></div>';
		$output .= '</div><!-- /.coordinates-wrap -->
						</div>';


		echo $output;


	}

	/**
	 *  Custom Google Geocoder field
	 * @since  1.0.0
	 * @return array
	 */
	function cmb2_render_google_geocoder( $field, $meta ) {

		$meta = wp_parse_args(
			$meta, array(
				'geocode' => '',
			)
		);

		echo '<div class="autocomplete-wrap"><input type="text" name="' . $field->args( 'id' ) . '[geocode]" id="' . $field->args( 'id' ) . '" value="" class="search-autocomplete" /><p class="autocomplete-description">' .
		     sprintf( __( 'Enter the name of a place or an address above to create a map marker or %1$sDrop a Marker%2$s', $this->plugin_slug ), '<a href="#" class="drop-marker button button-small"><span class="dashicons dashicons-location"></span>', '</a>' ) .
		     '</p></div>';

	}

	/**
	 *  Custom Google Geocoder field
	 * @since  1.0.0
	 */
	function cmb2_render_google_maps_preview( $field, $meta ) {
		global $post;
		$meta            = wp_parse_args( $meta, array() );
		$wh_value        = get_post_meta( $post->ID, 'gmb_width_height', true );
		$lat_lng         = get_post_meta( $post->ID, 'gmb_lat_lng', true );
		$default_options = $this->get_default_map_options();

		$output = '<div class="places-loading wpgp-loading">' . __( 'Loading Places', $this->plugin_slug ) . '</div><div id="google-map-wrap">';
		$output .= '<div id="map" style="height:600px; width:100%;"></div>';

		$output .= '<div class="map-modal-upsell"><p class="upsell-intro">' . __( 'Want more?', $this->plugin_slug ) . '</p><a href="https://wordimpress.com/plugins/maps-builder-pro?utm_source=MBF&utm_medium=BANNER&utm_content=MODAL&utm_campaign=MBF%20Modal" class="button button-small upsell-button" target="_blank">' . __( 'Go Pro!', $this->plugin_slug ) . '</a></div>';

		//Toolbar
		$output .= '<div id="map-toolbar">';
		$output .= '<button class="add-location button button-small gmb-magnific-inline" data-target="cmb2-id-gmb-geocoder" data-auto-focus="true"><span class="dashicons dashicons-pressthis"></span>' . __( 'Add Location', $this->plugin_slug ) . '</button>';
		$output .= '<button class="drop-marker button button-small"><span class="dashicons dashicons-location"></span>' . __( 'Drop a Marker', $this->plugin_slug ) . '</button>';
		$output .= '<button class="goto-location button button-small gmb-magnific-inline" data-target="map-autocomplete-wrap" data-auto-focus="true"><span class="dashicons dashicons-admin-site"></span>' . __( 'Goto Location', $this->plugin_slug ) . '</button>';
		$output .= '<button class="edit-title button  button-small gmb-magnific-inline" data-target="map-title-wrap" data-auto-focus="true"><span class="dashicons dashicons-edit"></span>' . __( 'Edit Map Title', $this->plugin_slug ) . '</button>';

		$output .= '<div class="live-lat-lng-wrap clearfix">';
		$output .= '<button disabled class="update-lat-lng button button-small">' . __( 'Set Lat/Lng', $this->plugin_slug ) . '</button>';
		$output .= '<div class="live-latitude-wrap"><span class="live-latitude-label">' . __( 'Lat:', $this->plugin_slug ) . '</span><span class="live-latitude">' . ( isset( $lat_lng['latitude'] ) ? $lat_lng['latitude'] : '' ) . '</span></div>';
		$output .= '<div class="live-longitude-wrap"><span class="live-longitude-label">' . __( 'Lng:', $this->plugin_slug ) . '</span><span class="live-longitude">' . ( isset( $lat_lng['longitude'] ) ? $lat_lng['longitude'] : '' ) . '</span></div>';
		$output .= '</div>'; //End .live-lat-lng-wrap
		$output .= '</div>'; //End #map-toolbar
		$output .= '</div>'; //End #map


		//@TODO: Obviously Need Wrapper function
		$output .= '<div class="white-popup mfp-hide map-title-wrap">';
		$output .= '<div class="inner-modal-wrap">';
		$output .= '<div class="inner-modal-container">';
		$output .= '<div class="inner-modal clearfix">';
		$output .= '<label for="post_title" class="map-title">' . __( 'Map Title', $this->plugin_slug ) . '</label>';
		$output .= '<p class="cmb2-metabox-description">' . __( 'Give your Map a descriptive title', $this->plugin_slug ) . '</p>';
		$output .= '<button type="button" class="gmb-modal-close">&times;</button><input type="text" name="model_post_title" size="30" value="' . get_the_title() . '" id="modal_title" spellcheck="true" autocomplete="off" placeholder="' . __( 'Enter map title', $this->plugin_slug ) . '">';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="white-popup mfp-hide map-autocomplete-wrap">';
		$output .= '<div class="inner-modal-wrap">';
		$output .= '<div class="inner-modal-container">';
		$output .= '<div class="inner-modal clearfix">';
		$output .= '<label for="map-location-autocomplete" class="map-title">' . __( 'Enter a Location', $this->plugin_slug ) . '</label>';
		$output .= '<p class="cmb2-metabox-description">' . __( 'Type your point of interest below and the map will be re-centered over that location', $this->plugin_slug ) . '</p>';
		$output .= '<button type="button" class="gmb-modal-close">&times;</button>';
		$output .= '<input type="text" name="" size="30" id="map-location-autocomplete">';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="warning-message wpgp-message"></div>';

		//Markers Modal
		include( 'views/markers.php' );

		echo apply_filters( 'google_maps_preview', $output );

	}

	/**
	 * Setup Custom CPT Columns
	 *
	 * @param $columns
	 *
	 * @return array
	 */
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


} //end class

new Google_Maps_Builder_Admin();
