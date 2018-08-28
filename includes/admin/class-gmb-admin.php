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
 * @copyright 2016 WordImpress, Devin Walker
 */

/**
 * Class Google_Maps_Builder_Admin
 */
class Google_Maps_Builder_Admin extends Google_Maps_Builder_Core_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		parent::__construct();

		add_action( 'cmb2_render_google_maps_preview', array( $this, 'cmb2_render_google_maps_preview' ), 10, 2 );
		// Load admin style sheet and JavaScript.
		add_action( 'wp_ajax_hide_welcome', array( $this, 'hide_welcome_callback' ) );

		//Add links/information to plugin row meta
		add_filter( 'cmb2_get_metabox_form_format', array( $this, 'gmb_modify_cmb2_form_output' ), 10, 3 );

		//Widget upsell
		add_action( 'gmb_after_widget_form', array( $this, 'widget_upsell' ) );

		//Useful class for free-only styling
		add_filter( 'admin_body_class', array( $this, 'admin_body_classes' ) );

		// Remove quick edit link and added preview map link.
		add_filter( 'post_row_actions', array( $this, 'remove_row_actions' ), 10, 2 );
		add_action( 'wp_ajax_prview_map_action', array( $this, 'prview_map_action_callback' ) );

	}

	/**
	 *  Custom Google Geocoder field
	 *
	 * @since  1.0.0
	 */
	function cmb2_render_google_maps_preview( $field, $meta ) {

		/* @var $post */
		global $post;

		$meta            = wp_parse_args( $meta, array() );
		$wh_value        = get_post_meta( $post->ID, 'gmb_width_height', true );
		$lat_lng         = get_post_meta( $post->ID, 'gmb_lat_lng', true );
		$default_options = $this->get_default_map_options();

		$output = '<div class="places-loading wpgp-loading">' . __( 'Loading Places', 'google-maps-builder' ) . '</div><div id="google-map-wrap">';
		$output .= '<div id="map" style="height:600px; width:100%;"></div>';

		$output .= '<div class="map-modal-upsell"><p class="upsell-intro">' . __( 'Want more?', 'google-maps-builder' ) . '</p><a href="https://wordimpress.com/plugins/maps-builder-pro?utm_source=MBF&utm_medium=BANNER&utm_content=MODAL&utm_campaign=MBF%20Modal" class="button button-small upsell-button" target="_blank">' . __( 'Go Pro!', 'google-maps-builder' ) . '</a></div>';

		//Toolbar
		$output .= '<div id="map-toolbar">';
		$output .= '<button class="add-location button button-small gmb-magnific-inline" data-target="cmb2-id-gmb-geocoder" data-auto-focus="true"><span class="dashicons dashicons-pressthis"></span>' . __( 'Add Location', 'google-maps-builder' ) . '</button>';
		$output .= '<button class="drop-marker button button-small"><span class="dashicons dashicons-location"></span>' . __( 'Drop a Marker', 'google-maps-builder' ) . '</button>';
		$output .= '<button class="goto-location button button-small gmb-magnific-inline" data-target="map-autocomplete-wrap" data-auto-focus="true"><span class="dashicons dashicons-admin-site"></span>' . __( 'Goto Location', 'google-maps-builder' ) . '</button>';
		$output .= '<button class="edit-title button  button-small gmb-magnific-inline" data-target="map-title-wrap" data-auto-focus="true"><span class="dashicons dashicons-edit"></span>' . __( 'Edit Map Title', 'google-maps-builder' ) . '</button>';

		$output .= '<div class="live-lat-lng-wrap clearfix">';
		$output .= '<button disabled class="update-lat-lng button button-small">' . __( 'Set Lat/Lng', 'google-maps-builder' ) . '</button>';
		$output .= '<div class="live-latitude-wrap"><span class="live-latitude-label">' . __( 'Lat:', 'google-maps-builder' ) . '</span><span class="live-latitude">' . ( isset( $lat_lng['latitude'] ) ? $lat_lng['latitude'] : '' ) . '</span></div>';
		$output .= '<div class="live-longitude-wrap"><span class="live-longitude-label">' . __( 'Lng:', 'google-maps-builder' ) . '</span><span class="live-longitude">' . ( isset( $lat_lng['longitude'] ) ? $lat_lng['longitude'] : '' ) . '</span></div>';
		$output .= '</div>'; //End .live-lat-lng-wrap
		$output .= '</div>'; //End #map-toolbar
		$output .= '</div>'; //End #map


		//@TODO: Obviously Need Wrapper function
		$output .= '<div class="white-popup mfp-hide map-title-wrap">';
		$output .= '<div class="inner-modal-wrap">';
		$output .= '<div class="inner-modal-container">';
		$output .= '<div class="inner-modal clearfix">';
		$output .= '<label for="post_title" class="map-title">' . __( 'Map Title', 'google-maps-builder' ) . '</label>';
		$output .= '<p class="cmb2-metabox-description">' . __( 'Give your Map a descriptive title', 'google-maps-builder' ) . '</p>';
		$output .= '<button type="button" class="gmb-modal-close">&times;</button><input type="text" name="model_post_title" size="30" value="' . get_the_title() . '" id="modal_title" spellcheck="true" autocomplete="off" placeholder="' . __( 'Enter map title', 'google-maps-builder' ) . '">';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="white-popup mfp-hide map-autocomplete-wrap">';
		$output .= '<div class="inner-modal-wrap">';
		$output .= '<div class="inner-modal-container">';
		$output .= '<div class="inner-modal clearfix">';
		$output .= '<label for="map-location-autocomplete" class="map-title">' . __( 'Enter a Location', 'google-maps-builder' ) . '</label>';
		$output .= '<p class="cmb2-metabox-description">' . __( 'Type your point of interest below and the map will be re-centered over that location', 'google-maps-builder' ) . '</p>';
		$output .= '<button type="button" class="gmb-modal-close">&times;</button>';
		$output .= '<input type="text" name="" size="30" id="map-location-autocomplete">';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="warning-message wpgp-message"></div>';

		//Markers Modal
		gmb_include_view( 'admin/views/markers.php', false, $this->view_data() );

		echo apply_filters( 'google_maps_preview', $output );

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


	/**
	 * Add upsell to the widget form
	 *
	 * @since 2.1.0
	 *
	 * @uses "gmb_after_widget_form" action
	 */
	public function widget_upsell() {
		?>
		<div class="gmb-widget-upgrade clear">
			<span class="powered-by"></span>
			<a href="https://wordimpress.com/plugins/maps-builder-pro?utm_source=MBF&utm_medium=BANNER&utm_content=WIDGET&utm_campaign=MBF%20Widgets" target="_blank" class="button button-small">
				<?php _e( 'Upgrade to Pro', 'google-maps-builder' ); ?>
				<span class="new-window"></span>
			</a>
		</div>
		<?php

	}


	/**
	 * Adds a Free
	 *
	 * @param  String $classes Current body classes.
	 *
	 * @return String          Altered body classes.
	 */
	function admin_body_classes( $classes ) {

		global $post;

		if ( isset( $post->post_type ) && $post->post_type == 'google_maps' ) {
			$classes .= 'maps-builder-free';
		}

		return $classes;

	}

	/**
	 * @param $actions contains actions for edit, quick edit etc...
	 * @param $post contains global post value
	 *
	 * @return mixed
	 */

	function remove_row_actions( $actions, $post ) {
		global $current_screen;
		if ( $current_screen->post_type != 'google_maps' ) {
			return $actions;
		}
		add_thickbox();


		?>
		<style>
			div#TB_ajaxContent {
				width: 100% !important;
				box-sizing: border-box;
				max-width: 100%;
				height: calc(100% - 30px) !important;
				overflow: hidden;
				padding: 0;
			}

			div#TB_window {
				width: 75% !important;
				height: 65%;
				margin: 0 auto !important;
				left: 0;
				right: 0;
				max-width: 75% !important;
				top: 10%;
				box-sizing: border-box;
				padding: 15px;
				overflow: hidden;
			}
		</style>
		<div id="gmb-preview-map"></div>
		<?php

		// Remove the Quick Edit link
		if ( isset( $actions['inline hide-if-no-js'] ) ) {
			unset( $actions['inline hide-if-no-js'] );
			$actions['custom'] = '<a href="#TB_inline?width=1400px&height=600px&inlineId=gmb-preview-map" data-id="' . $post->ID . '" class="thickbox gmb-load-map">' . sprintf( __( 'Preview Map', 'google-maps-builder' ) ) . '</a>';
		}

		return $actions;
	}

	/**
	 * Callback function for prview map.
	 */
	function prview_map_action_callback() {
		$map_id = isset( $_POST['map_id'] ) ? $_POST['map_id'] : '';
		//gather data for this shortcode
		$post        = get_post( $map_id );
		$all_meta    = get_post_custom( $map_id );
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
			),
		) );

		$maphtml                    = '<div class="google-maps-builder-wrap"> 	<div id="google-maps-builder-' . $map_id . '" class="google-maps-builder" data-map-id="' . $map_id . '" style="width: 1400px; height:600px;"></div></div>';
		$responseArray              = array();
		$responseArray['localized'] = $localized_data;
		$responseArray['maphtml']   = $maphtml;
		echo wp_send_json( $responseArray );
		wp_die();
	}


} //end class
