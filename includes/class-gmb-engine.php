<?php

/**
 * Maps Builder Engine
 *
 * The Google Maps engine class for WordPress Google Maps Builder
 *
 * @package   Google_Maps_Builder
 * @author    Devin Walker <devin@wordimpress.com>
 * @license   GPL-2.0+
 * @link      http://wordimpress.com
 * @copyright 2015 WordImpress, Devin Walker
 */

/**
 * Class Google_Maps_Builder_Engine
 */
class Google_Maps_Builder_Engine extends Google_Maps_Builder_Core_Engine {


	/**
	 * Google_Maps_Builder_Engine constructor.
	 */
	public function __construct() {

		parent::__construct();

	}


	/**
	 * Google Maps Builder Shortcode
	 *
	 * Google Maps output relies on the shortcode to display
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	public function google_maps_shortcode( $atts ) {

		$atts = shortcode_atts(
			array(
				'title'     => '',
				'id'        => '',
				'reference' => '',
			), $atts, 'google_maps'
		);

		//gather data for this shortcode
		$post     = get_post( $atts['id'] );
		$all_meta = get_post_custom( $atts['id'] );

		$visual_info = maybe_unserialize( $all_meta['gmb_width_height'][0] );
		$lat_lng     = maybe_unserialize( $all_meta['gmb_lat_lng'][0] );

		//Put markers into an array for JS usage
		$map_marker_array   = array();
		$markers_repeatable = isset( $all_meta['gmb_markers_group'][0] ) ? maybe_unserialize( $all_meta['gmb_markers_group'][0] ) : '';

		if ( is_array( $markers_repeatable ) ) {
			foreach ( $markers_repeatable as $marker ) {
				array_push( $map_marker_array, $marker );
			}
		}

		//Send data for AJAX usage
		//Add params to AJAX for Shortcode Usage
		//@see: http://benjaminrojas.net/using-wp_localize_script-dynamically/
		$localized_data = apply_filters( 'gmb_localized_data', array(
			$post->ID => array(
				'id'               => $atts['id'],
				'map_params'       => array(
					'title'          => $post->post_title,
					'width'          => $visual_info['width'],
					'height'         => $visual_info['height'],
					'latitude'       => $lat_lng['latitude'],
					'longitude'      => $lat_lng['longitude'],
					'zoom'           => ! empty( $all_meta['gmb_zoom'][0] ) ? $all_meta['gmb_zoom'][0] : '15',
					'default_marker' => apply_filters( 'gmb_default_marker', GMB_PLUGIN_URL . 'assets/img/spotlight-poi.png' ),
				),
				'map_controls'     => array(
					'zoom_control'      => ! empty( $all_meta['gmb_zoom_control'][0] ) ? strtoupper( $all_meta['gmb_zoom_control'][0] ) : 'STANDARD',
					'pan_control'       => ! empty( $all_meta['gmb_pan'][0] ) ? $all_meta['gmb_pan'][0] : 'none',
					'map_type_control'  => ! empty( $all_meta['gmb_map_type_control'][0] ) ? $all_meta['gmb_map_type_control'][0] : 'none',
					'draggable'         => ! empty( $all_meta['gmb_draggable'][0] ) ? $all_meta['gmb_draggable'][0] : 'none',
					'double_click_zoom' => ! empty( $all_meta['gmb_double_click'][0] ) ? $all_meta['gmb_double_click'][0] : 'none',
					'wheel_zoom'        => ! empty( $all_meta['gmb_wheel_zoom'][0] ) ? $all_meta['gmb_wheel_zoom'][0] : 'none',
					'street_view'       => ! empty( $all_meta['gmb_street_view'][0] ) ? $all_meta['gmb_street_view'][0] : 'none',
				),
				'map_theme'        => array(
					'map_type'       => ! empty( $all_meta['gmb_type'][0] ) ? $all_meta['gmb_type'][0] : 'RoadMap',
					'map_theme_json' => ! empty( $all_meta['gmb_theme_json'][0] ) ? $all_meta['gmb_theme_json'][0] : 'none',

				),
				'map_markers'      => $map_marker_array,
				'plugin_url'       => GMB_PLUGIN_URL,
				'places_api'       => array(
					'show_places'   => ! empty( $all_meta['gmb_show_places'][0] ) ? $all_meta['gmb_show_places'][0] : 'no',
					'search_radius' => ! empty( $all_meta['gmb_search_radius'][0] ) ? $all_meta['gmb_search_radius'][0] : '3000',
					'search_places' => ! empty( $all_meta['gmb_places_search_multicheckbox'][0] ) ? maybe_unserialize( $all_meta['gmb_places_search_multicheckbox'][0] ) : '',
				),
				'map_markers_icon' => ! empty( $all_meta['gmb_map_marker'] ) ? $all_meta['gmb_map_marker'][0] : 'none',
			)
		) );

		$this->array_push_localized_script( $localized_data );

		ob_start();

		include $this->get_google_maps_template( 'public.php' );

		return apply_filters( 'gmb_shortcode_output', ob_get_clean() );

	}


}
