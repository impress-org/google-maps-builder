<?php
/**
 *  Handles Upgrade Functionality
 *
 * @copyright   Copyright (c) 2015, WordImpress
 * @since       : 2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display Upgrade Notices
 *
 * @since 2.0
 * @return void
 */
function gmb_show_upgrade_notices() {

	//Uncomment for testing ONLY - Never leave uncommented unless testing:
	//delete_option( 'gmb_refid_upgraded' );

	// Don't show notices on the upgrades page
	if ( isset( $_GET['page'] ) && $_GET['page'] == 'gmb-upgrades' ) {
		return;
	}

	//Check to see if we have any posts
	$gmb_posts = get_posts( array( 'post_type' => 'google_maps', 'posts_per_page' => 10 ) );
	if(empty($gmb_posts)){
		update_option( 'gmb_refid_upgraded', 'upgraded' );//mark as updated
		return; //Don't run if there's no posts!
	}

	$gmb_version = get_option( 'gmb_version' );

	if ( ! $gmb_version ) {
		// 2.0 is the first version to use this option so we must add it
		$gmb_version = '2.0';
	}
	update_option( 'gmb_version', GMB_VERSION );

	$gmb_version = preg_replace( '/[^0-9.].*/', '', $gmb_version );

	if ( version_compare( $gmb_version, '2.0', '<=' ) && ! get_option( 'gmb_refid_upgraded' ) ) {
		printf(
			'<div class="updated"><p><strong>Google Maps Builder Notice:</strong> ' . esc_html__( 'Google has updated their Maps API to use the new Google Places ID rather than previous Reference ID. The old method will soon be deprecated and eventually go offline. We are being proactive and would like to update your maps to use the new Places ID. Once you upgrade, your maps should work just fine but remember to make a backup prior to upgrading. If you choose not to upgrade Google will eventually take the old reference ID offline (no date has been given). Please contact WordImpress support via our website if you have any further questions or issues. %sClick here to upgrade your maps to use the new Places ID%s', 'gmb' ) . '</p></div>',
			'<br><a href="' . esc_url( admin_url( 'options.php?page=gmb-upgrades' ) ) . '" class="button button-primary" style="margin-top:10px;">',
			'</a>'
		);
	}


}

add_action( 'admin_notices', 'gmb_show_upgrade_notices' );


/**
 * Creates the upgrade page
 *
 * links to global variables
 *
 * @since 2.0
 */
function gmb_add_upgrade_submenu_page() {

	$gmb_upgrades_screen = add_submenu_page( null, __( 'Maps Builder Upgrades', 'gmb' ), __( 'Maps Builder Upgrades', 'gmb' ), 'activate_plugins', 'gmb-upgrades', 'gmb_upgrades_screen' );

}

add_action( 'admin_menu', 'gmb_add_upgrade_submenu_page', 10 );

/**
 * Triggers all upgrade functions
 *
 * This function is usually triggered via AJAX
 *
 * @since 2.0
 * @return void
 */
function gmb_trigger_upgrades() {

	if ( ! current_user_can( 'activate_plugins' ) ) {
		wp_die( __( 'You do not have permission to do plugin upgrades', 'gmb' ), __( 'Error', 'gmb' ), array( 'response' => 403 ) );
	}

	$gmb_version = get_option( 'gmb_version' );

	//Is the option above in the db?
	if ( ! $gmb_version ) {
		// 2.0 is the first version to use this option so we must add it
		$gmb_version = '2.0';
		add_option( 'gmb_version', $gmb_version );
	}

	if ( version_compare( GMB_VERSION, $gmb_version, '>=' ) && ! get_option( 'gmb_refid_upgraded' ) ) {
		gmb_v2_upgrades();
	}

	update_option( 'gmb_version', $gmb_version );

	if ( DOING_AJAX ) {
		die( 'complete' );
	} // Let AJAX know that the upgrade is complete
}

add_action( 'wp_ajax_gmb_trigger_upgrades', 'gmb_trigger_upgrades' );


/**
 * Upgrade from Google Reference ID to Places ID
 *
 * @since 2.0
 * @uses  WP_Query
 * @return void
 */
function gmb_v2_upgrades() {

	//Set key variables
	$google_api_key = gmb_get_option( 'gmb_api_key' );

	//Loop through maps
	$args = array(
		'post_type'      => 'google_maps',
		'posts_per_page' => - 1
	);

	// The Query
	$the_query = new WP_Query( $args );

	// The CPT Loop
	if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post();

		//Repeater markers data
		$markers = get_post_meta( get_the_ID(), 'gmb_markers_group', true );

		//If no markers skip
		if ( ! empty( $markers ) ) {

			//Markers loop
			foreach ( $markers as $key => $marker ) {

				$ref_id   = isset( $marker['reference'] ) ? $marker['reference'] : '';
				$place_id = isset( $marker['place_id'] ) ? $marker['place_id'] : '';

				//No ref ID -> skip; If place_id already there skip
				if ( empty( $ref_id ) ) {
					continue;
				}
				if ( ! empty( $place_id ) ) {
					continue;
				}
				//cURL the Google API for the Google Place ID
				$google_places_url = add_query_arg(
					array(
						'reference' => $ref_id,
						'key'       => $google_api_key
					),
					'https://maps.googleapis.com/maps/api/place/details/json'
				);

				$response = wp_remote_get( $google_places_url,
					array(
						'timeout'   => 15,
						'sslverify' => false
					)
				);

				// make sure the response came back okay
				if ( is_wp_error( $response ) ) {
					return;
				}

				// decode the license data
				$response = json_decode( $response['body'], true );

				//Place ID is there, now let's update the widget data
				if ( isset( $response['result']['place_id'] ) ) {

					//Add Place ID to markers array
					$markers[ $key ]['place_id'] = $response['result']['place_id'];

				}

				//Pause for 2 seconds so we don't overwhelm the Google API with requests
				sleep( 2 );


			} //end foreach

			//Update repeater data with new data
			update_post_meta( get_the_ID(), 'gmb_markers_group', $markers );

		} //endif

	endwhile; endif;

	// Reset Post Data
	wp_reset_postdata();

	//Update our options and GTF out
	update_option( 'gmb_refid_upgraded', 'upgraded' );

}
