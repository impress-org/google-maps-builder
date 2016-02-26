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
		add_action( 'gmb_after_widget_form', array( $this, 'widget_upsell' ) );

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
	 * Add upsell to the widget form
	 *
	 * @since 2.1.0
	 *
	 * @uses "gmb_after_widget_form" action
	 */
	public function widget_upsell(){?>
		<div class="gmb-widget-upgrade clear">
			<span class="powered-by"></span>
			<a href="https://wordimpress.com/plugins/maps-builder-pro?utm_source=MBF&utm_medium=BANNER&utm_content=WIDGET&utm_campaign=MBF%20Widgets" target="_blank" class="button button-small">
				<?php _e( 'Upgrade to Pro', $this->plugin_slug ); ?>
				<span class="new-window"></span>
			</a>
		</div>
		<?php

	}


} //end class
