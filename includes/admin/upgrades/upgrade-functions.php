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
	delete_option( 'gmb_refid_upgraded' );

	// Don't show notices on the upgrades page
	if ( isset( $_GET['page'] ) && $_GET['page'] == 'gmb-upgrades' ) {
		return;
	}

	$gmb_version = get_option( 'gmb_version' );

	if ( ! $gmb_version ) {
		// 2.0 is the first version to use this option so we must add it
		$gmb_version = '2.0';
	}

	$gmb_version = preg_replace( '/[^0-9.].*/', '', $gmb_version );

	if ( version_compare( $gmb_version, '2.0', '<=' ) && ! get_option( 'gmb_refid_upgraded' ) ) {
		printf(
			'<div class="updated"><p><strong>Google Maps Builder Notice:</strong> ' . esc_html__( 'Google has updated their Maps API to use the new Google Places ID rather than previous Reference ID. The old method will soon be deprecated and eventually go offline. We are being proactive and would like to update your maps to use the new Places ID. Once you upgrade, your maps should work just fine. If you choose not to upgrade Google will eventually take the old reference ID offline (no date has been given). Please contact WordImpress support via our website if you have any further questions or issues. %sClick here to upgrade your maps to use the new Places ID%s.', 'gmb' ) . '</p></div>',
			'<br><br><strong><a href="' . esc_url( admin_url( 'options.php?page=gmb-upgrades' ) ) . '">',
			'</a></strong>'
		);
	}

	update_option( 'gmb_version', Google_Maps_Builder::VERSION );


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

	if ( version_compare( Google_Maps_Builder::VERSION, $gmb_version, '>=' ) && ! get_option( 'gmb_refid_upgraded' ) ) {
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

	//Upgrade the Reference ID
	$plugin_options = get_option( 'googleplacesreviews_options' );
	$google_api_key = $plugin_options['google_places_api_key'];

	//Loop through widgets' options
	foreach ( $gmb_widget_options as $key => $widget ) {

		$ref_id   = isset( $widget['reference'] ) ? $widget['reference'] : '';
		$place_id = isset( $widget['place_id'] ) ? $widget['place_id'] : '';

		//If no place AND there's a ref ID proceed
		if ( empty( $place_id ) && ! empty( $ref_id ) ) {

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
				) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return;
			}

			// decode the license data
			$response = json_decode( $response['body'], true );

			//Place ID is there, now let's update the widget data
			if ( isset( $response['result']['place_id'] ) ) {

				//Add Place ID to GPR widgets options array
				$gmb_widget_options[ $key ]['place_id'] = $response['result']['place_id'];

			}


		}
		//Pause for 2 seconds so we don't overwhelm the Google API with requests
		sleep( 2 );
	}

	//Update our options and GTF out
	update_option( 'gmb_refid_upgraded', 'upgraded' );
	update_option( 'widget_gmb_widget', $gmb_widget_options );

}
