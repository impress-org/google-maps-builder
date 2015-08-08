/**
 * Maps Builder Settings JS
 */
(function ( $ ) {
	"use strict";

	$( function () {

		//Hide Welcome Message
		$( '.hide-welcome' ).on( 'click', function ( e ) {
			e.preventDefault();
			$( '.welcome-header' ).slideUp( 'normal', function () {
				$( '.logo-svg-small' ).fadeIn(); //Show new tiny logo
			} ); //slide up welcome header

			var data = {
				action: 'hide_welcome'
			};
			$.post( ajaxurl, data, function ( response ) {

				//Do something here if necessary

			} );

		} );

		//Geolocate position change
		var geolocate_radio = $( '.geolocate-radio-wrap input:radio' );
		if ( geolocate_radio.prop( 'checked' ) === true ) {
			$( '#lat-lng-wrap' ).hide();
		}
		if ( geolocate_radio.prop( 'checked' ) === false ) {
			$( '#lat-lng-wrap' ).show();
		}
		geolocate_radio.on( 'change', function () {
			$( '#lat-lng-wrap' ).toggle();
		} );

		//Default setting for Has Archive Inline Radio
		if ( $( '#gmb_has_archive1' ).prop( 'checked' ) === false && $( '#gmb_has_archive2' ).prop( 'checked' ) === false ) {
			$( '#gmb_has_archive1' ).prop( 'checked', true );
		}
		//Default setting for the
		if ( $( '#gmb_open_builder1' ).prop( 'checked' ) === false && $( '#gmb_open_builder2' ).prop( 'checked' ) === false ) {
			$( '#gmb_open_builder2' ).prop( 'checked', true );
		}

		//Label Click Helper
		$( 'input:radio + label' ).on( 'click', function () {
			$( this ).prev( 'input:radio' ).prop( 'checked', true );
		} );

	} );


}( jQuery ));
