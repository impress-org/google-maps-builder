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
 * @copyright 2014 WordImpress, Devin Walker
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
	private function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin            = Google_Maps_Builder::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		//Add metaboxes and fields to CPT
		add_filter( 'cmb_meta_boxes', array( $this, 'cpt_metaboxes_fields' ), 1 );

		//Custom Meta Fields
		add_action( 'cmb_render_google_geocoder', array( $this, 'cmb_render_google_geocoder' ), 10, 2 );
		add_action( 'cmb_render_google_maps_preview', array( $this, 'cmb_render_google_maps_preview' ), 10, 2 );
		add_action( 'cmb_render_search_options', array( $this, 'cmb_render_search_options' ), 10, 2 );
		add_action( 'cmb_render_width_height', array( $this, 'cmb_render_width_height' ), 10, 2 );
		add_action( 'cmb_render_lat_lng', array( $this, 'cmb_render_lat_lng' ), 10, 2 );
		add_action( 'post_submitbox_misc_actions', array( $this, 'gmb_add_shortcode_to_publish_metabox' ) );


	}

	/**
	 *
	 * Add Shortcode to Publish Metabox
	 *
	 */
	public function gmb_add_shortcode_to_publish_metabox() {

		if ('google_maps' !== get_post_type())
			return false;

		global $post;
		//Shortcode column with select all input
		$shortcode = htmlentities( '[google_maps id="' . $post->ID . '"]' );
		echo '<div class="shortcode-wrap box-sizing"><label>' . __('Map Shortcode:', $this->plugin_slug) . '</label><input onClick="this.setSelectionRange(0, this.value.length)" type="text" class="shortcode-input" readonly value="' . $shortcode . '"></div>';

	}

	/**
	 * Get Default Map Options
	 *
	 * Helper function that returns default map options from settings
	 * @return array
	 */
	public function get_default_map_options() {

		$width_height = gmb_get_option( 'gmb_width_height' );
		//		$lat_lng = gmb_get_option( 'gmb_lat_lng' );

		$defaults = array(
			'width'      => ( isset( $width_height['width'] ) ) ? $width_height['width'] : '100',
			'width_unit' => ( isset( $width_height['map_width_unit'] ) ) ? $width_height['map_width_unit'] : '%',
			'height'     => ( isset( $width_height['height'] ) ) ? $width_height['height'] : '600',
			//			'latitude'     => ( isset( $lat_lng['latitude'] ) ) ? $lat_lng['latitude'] : '600',
			//			'longitude'     => ( isset( $lat_lng['longitude'] ) ) ? $lat_lng['longitude'] : '600',
		);

		return $defaults;

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
	 * Register and enqueue admin-specific style sheet.
	 *
	 * Return early if no settings page is registered.
	 * @since     1.0.0
	 *
	 * @return    null
	 */
	public function enqueue_admin_styles( $hook ) {

		global $post;
		$suffix = defined( 'GMB_DEBUG' ) && GMB_DEBUG ? '' : '.min';

		//Only enqueue scripts for CPT on post type screen
		if ( $hook == 'post-new.php' || $hook == 'post.php' && 'google_maps' === $post->post_type ) {

			wp_enqueue_style( $this->plugin_slug . '-admin-styles', plugins_url( 'assets/css/admin' . $suffix . '.css', __FILE__ ), array(), Google_Maps_Builder::VERSION );
			wp_enqueue_style( $this->plugin_slug . '-map-icons', plugins_url( 'includes/map-icons/css/map-icons.css', dirname( __FILE__ ) ), array(), Google_Maps_Builder::VERSION );
			wp_enqueue_style( $this->plugin_slug . '-map-tooltips', plugins_url( 'includes/tooltips/jquery.qtip' . $suffix . '.css', __FILE__ ), array(), Google_Maps_Builder::VERSION );

		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts( $hook ) {
		global $post;
		$suffix = defined( 'GMB_DEBUG' ) && GMB_DEBUG ? '' : '.min';

		//Only enqueue scripts for CPT on post type screen
		if ( $hook == 'post-new.php' || $hook == 'post.php' && 'google_maps' === $post->post_type ) {

			wp_enqueue_script( $this->plugin_slug . '-admin-gmaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places', array( 'jquery' ) );
			wp_enqueue_script( $this->plugin_slug . '-map-icons', plugins_url( 'includes/map-icons/js/map-icons.js', dirname( __FILE__ ) ), array( 'jquery' ) );
			wp_enqueue_script( $this->plugin_slug . '-admin-map-builder', plugins_url( 'assets/js/admin-google-map' . $suffix . '.js', __FILE__ ), array( 'jquery' ), Google_Maps_Builder::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-admin-qtip', plugins_url( 'includes/tooltips/jquery.qtip' . $suffix . '.js', __FILE__ ), array( 'jquery' ), Google_Maps_Builder::VERSION, true );


			$snazzy    = wp_remote_fopen( GMB_PLUGIN_URL . '/includes/snazzy.php' );
			$api_key   = gmb_get_option( 'gmb_api_key' );
			$geolocate = gmb_get_option( 'gmb_lat_lng' );

			$maps_data = array(
				'api_key'           => $api_key,
				'geolocate_setting' => isset( $geolocate['geolocate_map'] ) ? $geolocate['geolocate_map'] : 'yes',
				'default_lat'       => isset( $geolocate['latitude'] ) ? $geolocate['latitude'] : '32.715738',
				'default_lng'       => isset( $geolocate['longitude'] ) ? $geolocate['longitude'] : '-117.16108380000003',
				'plugin_url'        => GMB_PLUGIN_URL,
				'snazzy'            => json_encode( $snazzy )
			);
			wp_localize_script( $this->plugin_slug . '-admin-map-builder', 'gmb_data', $maps_data );

		}

		wp_enqueue_style( 'dashicons' );


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

	public function cpt_metaboxes_fields( array $meta_boxes ) {

		$prefix = 'gmb_'; // Prefix for all fields

		$default_options = $this->get_default_map_options();

		$meta_boxes['google_maps_metabox']         = array(
			'id'         => 'google_maps_metabox',
			'title'      => __( 'Google Map Markers', $this->plugin_slug ),
			'pages'      => array( 'google_maps' ), // post type
			'context'    => 'normal', //  'normal', 'advanced', or 'side'
			'priority'   => 'high', //  'high', 'core', 'default' or 'low'
			'show_names' => true, // Show field names on the left
			'fields'     => array(
				array(
					'name' => __( 'Create Marker', $this->plugin_slug ),
					'id'   => $prefix . 'geocoder',
					'type' => 'google_geocoder',
					'std'  => 'San Diego, CA, United States',
					'desc' => '',
				),
			),
		);
		$meta_boxes['google_maps_preview_metabox'] = array(
			'id'         => 'google_maps_preview_metabox',
			'title'      => __( 'Google Map Preview', $this->plugin_slug ),
			'pages'      => array( 'google_maps' ), // post type
			'context'    => 'normal', //  'normal', 'advanced', or 'side'
			'priority'   => 'core', //  'high', 'core', 'default' or 'low'
			'show_names' => false, // Show field names on the left
			'fields'     => array(
				array(
					'name' => 'Map Preview',
					'id'   => $prefix . 'preview',
					'type' => 'google_maps_preview',
					'std'  => '',
				),

			),
		);
		$meta_boxes['google_maps_markers']         = array(
			'id'         => 'google_maps_markers',
			'title'      => __( 'Google Map Markers', $this->plugin_slug ),
			'pages'      => array( 'google_maps' ), // post type
			'context'    => 'normal', //  'normal', 'advanced', or 'side'
			'priority'   => 'low', //  'high', 'core', 'default' or 'low'
			'show_names' => true, // Show field names on the left
			'fields'     => array(
				array(
					'id'          => $prefix . 'markers_group',
					'type'        => 'group',
					'description' => __( 'Generatemap m marke. You may update marker data here in bulk ps', $this->plugin_slug ),
					'options'     => array(
						'add_button'    => __( 'Add Another Marker', $this->plugin_slug ),
						'remove_button' => __( 'Remove Marker', $this->plugin_slug ),
						'sortable'      => true, // beta
					),
					// Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
					'fields'      => array(
						array(
							'name' => 'Marker Title',
							'id'   => 'title',
							'type' => 'text',
						),
						array(
							'name'        => 'Marker Description',
							'description' => 'Write a short description for this marker',
							'id'          => 'description',
							'type'        => 'textarea_small',
						),
						array(
							'name' => 'Marker Reference',
							'id'   => 'reference',
							'type' => 'text',
						),
						array(
							'name' => 'Hide Place Details',
							'id'   => 'hide_details',
							'type' => 'checkbox',
						),
						array(
							'name' => 'Marker Latitude',
							'id'   => 'lat',
							'type' => 'text',
						),
						array(
							'name' => 'Marker Longitude',
							'id'   => 'lng',
							'type' => 'text',
						),
						array(
							'name' => 'Marker Data',
							'id'   => 'marker',
							'type' => 'textarea_code',
						),
						array(
							'name' => 'Marker Label Data',
							'id'   => 'label',
							'type' => 'textarea_code',
						),
					),
				),
			),
		);


		$meta_boxes['google_maps_search_options'] = array(
			'id'         => 'google_maps_search_options',
			'title'      => __( 'Google Places', $this->plugin_slug ),
			'pages'      => array( 'google_maps' ), // post type
			'context'    => 'normal', //  'normal', 'advanced', or 'side'
			'priority'   => 'core', //  'high', 'core', 'default' or 'low'
			'show_names' => true, // Show field names on the left
			'fields'     => array(
				array(
					'name'    => __( 'Show Places?', $this->plugin_slug ),
					'desc'    => __( 'Display establishments, prominent points of interest, geographic locations, and more.', $this->plugin_slug ),
					'id'      => $prefix . 'show_places',
					'type'    => 'radio_inline',
					'options' => array(
						'yes' => __( 'Yes', 'cmb' ),
						'no'  => __( 'No', 'cmb' ),
					),
				),
				array(
					'name' => __( 'Search Radius', $this->plugin_slug ),
					'desc' => __( 'Defines the distance (in meters) within which to return Place results. The maximum allowed radius is 50,000 meters.', $this->plugin_slug ),
					'std'  => '1000',
					'id'   => $prefix . 'search_radius',
					'type' => 'text_small'
				),
				array(
					'name'    => __( 'Place Types', $this->plugin_slug ),
					'desc'    => __( 'Select which type of places you would like to display on this map.', $this->plugin_slug ),
					'id'      => $prefix . 'places_search_multicheckbox',
					'type'    => 'multicheck',
					'options' => array(
						'accounting'              => __( 'Accounting', $this->plugin_slug ),
						'airport'                 => __( 'Airport', $this->plugin_slug ),
						//						'amusement_park'          => __('Amusement Park', $this->plugin_slug ),
						//						'aquarium'                => __('Aquarium', $this->plugin_slug ),
						//						'art_gallery'             => __('Art Gallery',
						'atm'                     => __( 'ATM', $this->plugin_slug ),
						'bakery'                  => __( 'Bakery', $this->plugin_slug ),
						'bank'                    => __( 'Bank', $this->plugin_slug ),
						'bar'                     => __( 'Bar', $this->plugin_slug ),
						//						'beauty_salon'            => __('Beauty Salon', $this->plugin_slug ),
						//						'bicycle_store'           => __('Bicycle Store', $this->plugin_slug ),
						//						'book_store'              => __('Book Store', $this->plugin_slug ),
						'bowling_alley'           => __( 'Bowling Alley', $this->plugin_slug ),
						'bus_station'             => __( 'Bus Station', $this->plugin_slug ),
						'cafe'                    => __( 'Cafe', $this->plugin_slug ),
						'campground'              => __( 'Campground', $this->plugin_slug ),
						//						'car_dealer'              => __('Car Dealer', $this->plugin_slug ),
						//						'car_rental'              => __('Car Rental', $this->plugin_slug ),
						//						'car_repair'              => __('Car Repair', $this->plugin_slug ),
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
						//						'doctor'                  => __('Doctor', $this->plugin_slug ),
						//						'electrician'             => __('Electrician', $this->plugin_slug ),
						//						'electronics_store'       => __('Electronics Store', $this->plugin_slug ),
						'embassy'                 => __( 'Embassy', $this->plugin_slug ),
						'establishment'           => __( 'Establishment', $this->plugin_slug ),
						'finance'                 => __( 'Finance', $this->plugin_slug ),
						'fire_station'            => __( 'Fire Station', $this->plugin_slug ),
						'florist'                 => __( 'Florist', $this->plugin_slug ),
						'food'                    => __( 'Food', $this->plugin_slug ),
						'funeral_home'            => __( 'Funeral Home', $this->plugin_slug ),
						'furniture_store'         => __( 'Furniture_store', $this->plugin_slug ),
						//						'gas_station'             => __('Gas Station', $this->plugin_slug ),
						//						'general_contractor'      => __('General Contractor', $this->plugin_slug ),
						//						'grocery_or_supermarket'  => __('Grocery or Supermarket', $this->plugin_slug ),
						'gym'                     => __( 'Gym', $this->plugin_slug ),
						'hair_care'               => __( 'Hair Care', $this->plugin_slug ),
						'hardware_store'          => __( 'Hardware Store', $this->plugin_slug ),
						'health'                  => __( 'Health', $this->plugin_slug ),
						'hindu_temple'            => __( 'Hindu Temple', $this->plugin_slug ),
						//						'home_goods_store'        => __('Home Goods Store', $this->plugin_slug ),
						//						'hospital'                => __('Hospital', $this->plugin_slug ),
						//						'insurance_agency'        => __('Insurance Agency', $this->plugin_slug ),
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
						//						'mosque'                  => __('Mosque', $this->plugin_slug ),
						//						'movie_rental'            => __('Movie Rental', $this->plugin_slug ),
						//						'movie_theater'           => __('Movie Theater', $this->plugin_slug ),
						//						'moving_company'          => __('Moving Company', $this->plugin_slug ),
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
						//						'real_estate_agency'      => __('Real Estate Agency', $this->plugin_slug ),
						//						'restaurant'              => __('Restaurant', $this->plugin_slug ),
						//						'roofing_contractor'      => __('Roofing Contractor', $this->plugin_slug ),
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
						//						'train_station'           => __('Train Station', $this->plugin_slug ),
						//						'travel_agency'           => __('Travel Agency', $this->plugin_slug ),
						'university'              => __( 'University', $this->plugin_slug ),
						'veterinary_care'         => __( 'Veterinary Care', $this->plugin_slug ),
						'zoo'                     => __( 'Zoo', $this->plugin_slug )
					),
				),
			),
		);

		$meta_boxes['google_maps_options'] = array(
			'id'         => 'google_maps_options',
			'title'      => __( 'Google Map Display Options', $this->plugin_slug ),
			'pages'      => array( 'google_maps' ), // post type
			'context'    => 'side', //  'normal', 'advanced', or 'side'
			'priority'   => 'default', //  'high', 'core', 'default' or 'low'
			'show_names' => true, // Show field names on the left
			'fields'     => array(
				array(
					'name'           => __( 'Map Size', $this->plugin_slug ),
					'id'             => $prefix . 'width_height',
					'type'           => 'width_height',
					'width_std'      => $default_options['width'],
					'width_unit_std' => $default_options['width_unit'],
					'height_std'     => $default_options['height'],
					'desc'           => '',
				),
				array(
					'name'    => __( 'Map Location', $this->plugin_slug ),
					'id'      => $prefix . 'lat_lng',
					'type'    => 'lat_lng',
					'lat_std' => '',
					'lng_std' => '',
					'desc'    => '',
				),

				array(
					'name'    => 'Map Type',
					'id'      => $prefix . 'type',
					'type'    => 'select',
					'std'     => 'default',
					'options' => array(
						array( 'name' => __( 'Road Map', $this->plugin_slug ), 'value' => 'RoadMap' ),
						array( 'name' => __( 'Satellite', $this->plugin_slug ), 'value' => 'Satellite' ),
						array( 'name' => __( 'Hybrid', $this->plugin_slug ), 'value' => 'Hybrid' ),
						array( 'name' => __( 'Terrain', $this->plugin_slug ), 'value' => 'Terrain' ),
					),
				),
				array(
					'name'    => 'Map Theme',
					'desc'    => sprintf( __( 'Set optional preconfigured styles. <a href="%s" class="snazzy-link new-window"  target="_blank">Snazzy Maps</a>', $this->plugin_slug ), esc_url( 'http://snazzymaps.com' ) ),
					'id'      => $prefix . 'theme',
					'type'    => 'select',
					'std'     => 'none',
					'options' => array(
						array( 'name' => __( 'None', $this->plugin_slug ), 'value' => 'none' ),
						array( 'name' => __( 'Aqua', $this->plugin_slug ), 'value' => '68' ),
						array( 'name' => __( 'A Dark World', $this->plugin_slug ), 'value' => '73' ),
						array( 'name' => __( 'Bluish', $this->plugin_slug ), 'value' => '28' ),
						array( 'name' => __( 'Cool Grey', $this->plugin_slug ), 'value' => '80' ),
						array( 'name' => __( 'Clean Cut', $this->plugin_slug ), 'value' => '77' ),
						array( 'name' => __( 'Flat Green', $this->plugin_slug ), 'value' => '36' ),
						array( 'name' => __( 'MapBox', $this->plugin_slug ), 'value' => '44' ),
						array( 'name' => __( 'Muted Blue', $this->plugin_slug ), 'value' => '83' ),
						array( 'name' => __( 'Old Timey', $this->plugin_slug ), 'value' => '22' ),
						array( 'name' => __( 'Pale Dawn', $this->plugin_slug ), 'value' => '1' ),
						array( 'name' => __( 'Paper', $this->plugin_slug ), 'value' => '19' ),
						array( 'name' => __( 'Lunar Landscape', $this->plugin_slug ), 'value' => '37' ),
						array( 'name' => __( 'Shade of Green', $this->plugin_slug ), 'value' => '75' ),
						array( 'name' => __( 'Shift Worker', $this->plugin_slug ), 'value' => '27' ),
						array( 'name' => __( 'Subtle Grayscale', $this->plugin_slug ), 'value' => '15' ),
						array( 'name' => __( 'The Endless Atlas', $this->plugin_slug ), 'value' => '50' ),
					),
				),
				array(
					'name'    => 'Map Theme JSON',
					'desc'    => 'Contains the map theme JSON',
					'default' => 'none',
					'id'      => $prefix . 'theme_json',
					'type'    => 'textarea_code'
				),
				array(
					'name'    => 'Zoom',
					'desc'    => __( 'Adjust the map zoom (0-21)', $this->plugin_slug ),
					'id'      => $prefix . 'zoom',
					'type'    => 'select',
					'std'     => '15',
					'options' => array(
						array( 'name' => '21', 'value' => '21' ),
						array( 'name' => '20', 'value' => '20' ),
						array( 'name' => '19', 'value' => '19' ),
						array( 'name' => '18', 'value' => '18' ),
						array( 'name' => '17', 'value' => '17' ),
						array( 'name' => '16', 'value' => '16' ),
						array( 'name' => '15', 'value' => '15' ),
						array( 'name' => '14', 'value' => '14' ),
						array( 'name' => '13', 'value' => '13' ),
						array( 'name' => '12', 'value' => '12' ),
						array( 'name' => '11', 'value' => '11' ),
						array( 'name' => '10', 'value' => '10' ),
						array( 'name' => '9', 'value' => '9' ),
						array( 'name' => '8', 'value' => '8' ),
						array( 'name' => '7', 'value' => '7' ),
						array( 'name' => '6', 'value' => '6' ),
						array( 'name' => '5', 'value' => '5' ),
						array( 'name' => '4', 'value' => '4' ),
						array( 'name' => '3', 'value' => '3' ),
						array( 'name' => '2', 'value' => '2' ),
						array( 'name' => '1', 'value' => '1' ),
						array( 'name' => '0', 'value' => '0' ),

					)
				),
			    array(
					'name'  => 'Custom Map Marker Icon',
					'desc'  => 'Use a custom map marker for the map.',
					'id'    => $prefix . 'map_marker',
					'type'  => 'file',
					'allow' => array( 'url', 'attachment' ),
			    ),
			),
		);

		$meta_boxes['google_maps_control_options'] = array(
			'id'         => 'google_maps_control_options',
			'title'      => __( 'Google Map Control Options', $this->plugin_slug ),
			'pages'      => array( 'google_maps' ), // post type
			'context'    => 'side', //  'normal', 'advanced', or 'side'
			'priority'   => 'default', //  'high', 'core', 'default' or 'low'
			'show_names' => true, // Show field names on the left
			'fields'     => array(
				array(
					'name'    => 'Zoom Control',
					'id'      => $prefix . 'zoom_control',
					'type'    => 'select',
					'std'     => 'default',
					'options' => array(
						array( 'name' => __( 'None', $this->plugin_slug ), 'value' => 'none' ),
						array( 'name' => __( 'Small', $this->plugin_slug ), 'value' => 'small' ),
						array( 'name' => __( 'Large', $this->plugin_slug ), 'value' => 'large' ),
						array( 'name' => __( 'Default', $this->plugin_slug ), 'value' => 'default' ),
					),
				),
				array(
					'name'    => 'Street View',
					'id'      => $prefix . 'street_view',
					'type'    => 'select',
					'std'     => 'true',
					'options' => array(
						array( 'name' => __( 'None', $this->plugin_slug ), 'value' => 'none' ),
						array( 'name' => __( 'Standard', $this->plugin_slug ), 'value' => 'true' ),
					),
				),
				array(
					'name'    => 'Pan Control',
					'id'      => $prefix . 'pan',
					'type'    => 'select',
					'std'     => 'true',
					'options' => array(
						array( 'name' => __( 'None', $this->plugin_slug ), 'value' => 'none' ),
						array( 'name' => __( 'Standard', $this->plugin_slug ), 'value' => 'true' ),
					),
				),
				array(
					'name'    => 'Map Type Control',
					'id'      => $prefix . 'map_type_control',
					'type'    => 'select',
					'std'     => 'horizontal_bar',
					'options' => array(
						array( 'name' => __( 'None', $this->plugin_slug ), 'value' => 'none' ),
						array( 'name' => __( 'Dropdown Menu', $this->plugin_slug ), 'value' => 'dropdown_menu' ),
						array( 'name' => __( 'Horizontal Bar', $this->plugin_slug ), 'value' => 'horizontal_bar' ),
					),
				),

				array(
					'name'    => 'Draggable Map',
					'id'      => $prefix . 'draggable',
					'type'    => 'select',
					'std'     => 'true',
					'options' => array(
						array( 'name' => __( 'None', $this->plugin_slug ), 'value' => 'none' ),
						array( 'name' => __( 'Standard', $this->plugin_slug ), 'value' => 'true' ),
					),
				),
				array(
					'name'    => 'Double Click to Zoom',
					'id'      => $prefix . 'double_click',
					'type'    => 'select',
					'std'     => 'true',
					'options' => array(
						array( 'name' => __( 'None', $this->plugin_slug ), 'value' => 'none' ),
						array( 'name' => __( 'Standard', $this->plugin_slug ), 'value' => 'true' ),
					),
				),
				array(
					'name'    => 'Mouse Wheel to Zoom',
					'id'      => $prefix . 'wheel_zoom',
					'type'    => 'select',
					'std'     => 'none',
					'options' => array(
						array( 'name' => __( 'None', $this->plugin_slug ), 'value' => 'none' ),
						array( 'name' => __( 'Standard', $this->plugin_slug ), 'value' => 'true' ),
					),
				),
			),

		);

		return $meta_boxes;

	}


	/**
	 * CMB Width Height
	 *
	 * Custom CMB field for Gmap width and height
	 *
	 * @param $field
	 * @param $meta
	 */
	function cmb_render_width_height( $field, $meta ) {
		$default_options = $this->get_default_map_options();
		$meta            = wp_parse_args(
			$meta, array(
				'width'          => $default_options['width'],
				'height'         => $default_options['height'],
				'map_width_unit' => $default_options['width_unit'],
			)
		);

		$output = '<div id="width_height_wrap" class="clear">';
		//width
		$output .= '<div id="width_wrap" class="clear">';
		$output .= '<label class="width-label size-label">Width:</label><input type="text" class="regular-text map-width" name="' . $field['id'] . '[width]" id="' . $field['id'] . '-width" value="' . ( $meta['width'] ? $meta['width'] : $field['width_std'] ) . '" />';
		$output .= '<div id="size_labels_wrap">';
		$output .= '<input id="width_unit_percent" type="radio" name="' . $field['id'] . '[map_width_unit]" class="width_radio" value="%" ' . ( $meta['map_width_unit'] === '%' || $field['width_unit_std'] === '%' ? 'checked="checked"' : '' ) . '><label class="width_unit_label">%</label>';
		$output .= '<input id="width_unit_px" type="radio" name="' . $field['id'] . '[map_width_unit]" class="width_radio" value="px" ' . ( $meta['map_width_unit'] === 'px' ? 'checked="checked"' : '' ) . ' ><label class="width_unit_label">px</label>';
		$output .= '</div>';
		$output .= '</div>';

		//height
		$output .= '<div id="height_wrap" class="clear">';
		$output .= '<label for="' . $field['id'] . '[height]" class="height-label size-label">Height:</label><input type="text" class="regular-text map-height" name="' . $field['id'] . '[height]" id="' . $field['id'] . '-height" value="' . ( $meta['height'] ? $meta['height'] : $field['height_std'] ) . '" />';
		$output .= '</div>';
		$output .= '</div>';


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
	function cmb_render_lat_lng( $field, $meta ) {
		$meta = wp_parse_args(
			$meta, array(
				'latitude'  => '',
				'longitude' => '',
			)
		);

		//lat lng
		$output = '<div id="lat-lng-wrap">
					<div class="coordinates-wrap clear">
							<div class="lat-lng-wrap lat-wrap clear"><span>Latitude: </span>
							<input type="text" class="regular-text latitude" name="' . $field['id'] . '[latitude]" id="' . $field['id'] . '-latitude" value="' . ( $meta['latitude'] ? $meta['latitude'] : $field['lat_std'] ) . '" />
							</div>
							<div class="lat-lng-wrap lng-wrap clear"><span>Longitude: </span>
							<input type="text" class="regular-text longitude" name="' . $field['id'] . '[longitude]" id="' . $field['id'] . '-longitude" value="' . ( $meta['longitude'] ? $meta['longitude'] : $field['lng_std'] ) . '" />
							</div>';
		$output .= '<div class="wpgp-message lat-lng-change-message clear"><p>Lat/lng changed</p><a href="#" class="button lat-lng-update-btn button-small" data-lat="" data-lng="">Update</a></div>';
		$output .= '</div><!-- /.coordinates-wrap -->
						</div>';


		echo $output;


	}

	/**
	 *  Custom Google Geocoder field
	 * @since  1.0.0
	 * @return array
	 */
	function cmb_render_google_geocoder( $field, $meta ) {

		$meta = wp_parse_args(
			$meta, array(
				'geocode' => '',
			)
		);

		echo '<div class="autocomplete-wrap"><input type="text" name="' . $field['id'] . '[geocode]" id="' . $field['id'] . '" value="" class="search-autocomplete" /><p class="autocomplete-description">' .
			sprintf( __( 'Enter the name of a place or an address above to create a map marker or %s', $this->plugin_slug ), '<a href="#" class="drop-marker button button-small">Drop a Marker</a>' ) .
			'</p></div>';

		//'desc'    => sprintf( __( 'Set optional preconfigured styles. <a href="%s" class="snazzy-link new-window"  target="_blank">Snazzy Maps</a>', $this->plugin_slug ), esc_url( 'http://snazzymaps.com' ) ),


		//Markers Modal
		add_thickbox();
		include( 'views/markers.php' );

	}

	/**
	 *  Custom Google Geocoder field
	 * @since  1.0.0
	 */
	function cmb_render_google_maps_preview( $field, $meta ) {
		global $post;
		$meta            = wp_parse_args( $meta, array() );
		$wh_value        = get_post_meta( $post->ID, 'gmb_width_height', true );
		$default_options = $this->get_default_map_options();


		$map_height    = isset( $wh_value['height'] ) ? $wh_value['height'] : $default_options['height'];
		$map_width     = isset( $wh_value['width'] ) ? $wh_value['width'] : $default_options['width'];
		$map_width_val = isset( $wh_value['map_width_unit'] ) ? $wh_value['map_width_unit'] : $default_options['width_unit'];

		$output = '<div class="places-loading wpgp-loading">Loading Places</div><div id="google-map-wrap">';
		$output .= '<div id="map" style="height:' . $map_height . 'px; width:' . $map_width . $map_width_val . '"></div>';
		$output .= '</div>';
		$output .= '<div class="warning-message wpgp-message"></div>';

		echo $output;

	}


} //end class