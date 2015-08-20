/**
 *  Maps Directions
 *
 *  @description: Adds directions functionality to the maps builder
 *  @copyright: http://opensource.org/licenses/gpl-2.0.php GNU Public License
 *  @since: 2.0
 */

var gmb_data;
var gmb_upload_marker;
var trafficLayer = new google.maps.TrafficLayer();
var transitLayer = new google.maps.TransitLayer();
var bicycleLayer = new google.maps.BicyclingLayer();
var placeSearchAutocomplete;

(function ( $ ) {

	"use strict";

	/**
	 * Kick it off on Window Load
	 */
	$( window ).on( 'load', function () {

		set_map_goto_location_autocomplete();
		set_map_edit_title();

		//Set lng and lat when map dragging
		google.maps.event.addListener( map, 'drag', function () {
			set_toolbar_lat_lng();
		} );
		//Set lng and lat when map dragging
		google.maps.event.addListener( map, 'dragend', function () {
			set_toolbar_lat_lng();
		} );

		//Set lng and lat when map dragging
		google.maps.event.addListener( map, 'zoom_changed', function () {
			set_toolbar_lat_lng();
		} );

		//Initialize Magnific/Modal Functionality Too
		$( 'body' ).on( 'click', '.gmb-magnific-inline', function ( e ) {

			e.preventDefault();
			var target = '.' + $( this ).data( 'target' ); //target element class name
			var autofocus = $( this ).data( 'auto-focus' ); //autofocus option

			//Modal in modal?
			//We can't have a magnific inside magnific so CSS3 modal it is
			if ( $.magnificPopup.instance.isOpen === true ) {

				//Open CSS modal
				$( target ).before( '<div class="modal-placeholder"></div>' ) // Save a DOM "bookmark"
					.removeClass( 'mfp-hide' ) //ensure it's visible
					.appendTo( '.magnific-builder #poststuff' ); // Move the element to container

				//Check if wrapped properly
				var inner_wrap = $( target ).find( '.inner-modal-wrap' );
				var inner_wrap_container = $( target ).find( '.inner-modal-container' );

				//Not wrapped, wrap it
				if ( inner_wrap.length == 0 && inner_wrap_container.length == 0 ) {

					$( target ).addClass( 'white-popup' ).wrapInner( '<div class="inner-modal-wrap"><div class="inner-modal-container"><div class="inner-modal clearfix"></div></div></div>' );
					$( '<button type="button" class="gmb-modal-close">&times;</button>' ).prependTo( $( target ).find( '.inner-modal' ) );
				}

				//Add close functionality to outside overlay
				$( target ).on( 'click', function ( e ) {
					//only on overlay
					if ( $( e.target ).hasClass( 'inner-modal-wrap' ) || $( e.target ).hasClass( 'inner-modal-container' ) ) {
						// Move back out of container
						close_modal_within_modal( target );
					}
				} );
				//Close button
				$( '.gmb-modal-close' ).on( 'click', function () {
					close_modal_within_modal( target );
				} );

				//Autofocus
				if ( autofocus == true ) {
					$( target ).find( 'input[type="text"]' ).focus();
				}

			}
			//Normal modal open
			else {
				$.magnificPopup.open( {
					callbacks: {
						beforeOpen: function () {
							$( target ).addClass( 'white-popup' );
						}
					},
					items    : {
						src : $( target ),
						type: 'inline'
					},
					midClick : true
				} );
			}
		} );


	} );


	/**
	 * Goto Location Autocomplete
	 */
	function set_map_goto_location_autocomplete() {
		var modal = $( '.map-autocomplete-wrap' );
		var input = $( '#map-location-autocomplete' ).get( 0 );
		var location_autocomplete = new google.maps.places.Autocomplete( input );
		location_autocomplete.bindTo( 'bounds', map );

		google.maps.event.addListener( location_autocomplete, 'place_changed', function () {

			var place = location_autocomplete.getPlace();
			if ( !place.geometry ) {
				window.alert( "Autocomplete's returned place contains no geometry" );
				return;
			}

			// If the place has a geometry, then present it on a map.
			if ( place.geometry.viewport ) {
				map.fitBounds( place.geometry.viewport );
			} else {
				map.setCenter( place.geometry.location );
				map.setZoom( 17 );  // Why 17? Because it looks good.
			}

			//Close modal
			$( modal ).find( '.mfp-close' ).trigger( 'click' );
			close_modal_within_modal( modal );

		} );

		//Tame the enter key to not save the widget while using the autocomplete input
		google.maps.event.addDomListener( input, 'keydown', function ( e ) {
			if ( e.keyCode == 13 ) {
				e.preventDefault();
			}
		} );

	}

	/**
	 * Close a Modal within Modal
	 *
	 * @param modal
	 */
	function close_modal_within_modal( modal ) {
		// Move back out of container
		$( modal )
			.addClass( 'mfp-hide' ) //ensure it's hidden
			.appendTo( '.modal-placeholder' )  // Move it back to it's proper location
			.unwrap(); // Remove the placeholder
	}


	/**
	 * Edit Title within Modal
	 */
	function set_map_edit_title() {

		//When edit title button is clicked insert title into feax input
		$( '.edit-title' ).on( 'click', function () {
			$( '#modal_title' ).val( $( 'input#title' ).val() );
		} );

		//when feax title input is changed update default title field
		$( '#modal_title' ).on( 'blur', function () {
			$( 'input#title' ).val( $( this ).val() );
		} );

	}


	/**
	 * Update Toolbar Lat/Lng
	 */
	function set_toolbar_lat_lng() {

		var lat_lng_sidebar_btn = $( '.lat-lng-update-btn' );
		var lat_lng_toolbar_btn = $( '.update-lat-lng' );

		var map_center = map.getCenter();
		$( '.live-latitude' ).text( map_center.lat() );
		$( '.live-longitude' ).text( map_center.lng() );
		lat_lng_toolbar_btn.attr( 'data-lat', map_center.lat() );
		$( '.lat-lng-change-message' ).slideDown();

		lat_lng_toolbar_btn.attr( 'data-lng', map_center.lng() );
		lat_lng_sidebar_btn.attr( 'data-lat', map_center.lat() );
		lat_lng_sidebar_btn.attr( 'data-lng', map_center.lng() );

		lat_lng_sidebar_btn.removeAttr( 'disabled' );
		lat_lng_toolbar_btn.removeAttr( 'disabled' );

	}


}( jQuery ));