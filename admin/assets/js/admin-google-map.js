/**
 * Google Maps CPT Handling
 *
 * Nice Demos:
 * https://developers.google.com/maps/documentation/javascript/examples/geocoding-simple
 *
 */


(function ( $ ) {
	"use strict";

	/**
	 * Window Load functions
	 */
	$( window ).load( function () {

		toggle_metabox_fields();

		//tooltips
		initialize_tooltips();

		//Map type Metabox on load
		initialize_map( $( '#map' ) );

		//Latitude on Change
		$( '#gmb_lat_lng-latitude' ).on( 'change', function () {
			lat_lng_field_change( map );
		} );
		//Longitude on Change
		$( '#gmb_lat_lng-longitude' ).on( 'change', function () {
			lat_lng_field_change( map );
		} );

		//click to add marker
		$( '.drop-marker' ).on( 'click', function ( e ) {
			e.preventDefault();
			if ( $( this ).hasClass( 'active' ) ) {
				$( this ).text( 'Drop a Marker' ).removeClass( 'active' );
				map.setOptions( {draggableCursor: null} ); //reset cursor
			} else {
				$( this ).text( 'Click on the Map' ).addClass( 'active' );
				map.setOptions( {draggableCursor: 'crosshair'} );
				var dropped_marker_event = google.maps.event.addListener( map, 'click', function ( event ) {
					drop_marker( event.latLng, dropped_marker_event );
				} );
			}
		} );


		//Radius Fields
		var current_radius;

		//Search Radius Circle
		$( '#gmb_search_radius' ).on( 'focus', function () {
			current_radius = $( this ).val();
			calc_radius( map, parseInt( $( this ).val() ) );
		} ).focusout( function () {
			if ( current_radius !== $( this ).val() ) {
				perform_places_search();
			}
			radius_circle.setMap( null ); //removes circle on focus out
			radius_marker.setMap( null ); //removes circle on focus out
		} );

		//Places Type Field
		$( '[name^="gmb_places_search_multicheckbox"]' ).on( 'change', function () {
			//Show message if not already displayed
			if ( $( '.places-change-message' ).length === 0 ) {
				$( '.cmb_id_gmb_places_search_multicheckbox ul' ).prepend( '<div class="wpgp-message places-change-message clear"><p>Place selections have changed.</p><a href="#" class="button update-places-map">Update Map</a></div>' );
				$( '.places-change-message' ).slideDown();
			}

		} );

		//Places Update Map Button
		$( document ).on( 'click', '.update-places-map', function ( e ) {
			e.preventDefault();
			scroll_to_field( "#google_maps_preview_metabox" );
			perform_places_search();
			$( this ).parent().fadeOut( function () {
				$( this ).remove();
			} );
		} );

		//Update lat lng message
		$( '.lat-lng-update-btn' ).on( 'click', function ( e ) {
			e.preventDefault();
			$( '.lat-lng-change-message' ).slideUp();
			$( '#gmb_lat_lng-latitude' ).val( $( this ).attr( 'data-lat' ) );
			$( '#gmb_lat_lng-longitude' ).val( $( this ).attr( 'data-lng' ) );
		} );


		//Add New Marker
		$( document ).on( 'click', '.add-marker', function ( e ) {

			e.preventDefault();
			hover_circle.setVisible( false );

			//update marker with set marker
			var location_marker = new google.maps.Marker( {
				position : tentative_location_marker.getPosition(),
				map      : map,
				icon     : gmb_data.plugin_url + "/public/assets/img/default-marker.png",
				zIndex   : google.maps.Marker.MAX_ZINDEX + 1,
				optimized: false
			} );

			//hide tentative green marker
			tentative_location_marker.setVisible( false );

			//get current number of repeatable rows ie markers
			var index = get_marker_index();

			var reference = $( this ).data( 'reference' );

			//add data to fields
			$( '#gmb_markers_group_' + index + '_title' ).val( $( this ).data( 'title' ) );
			$( '#gmb_markers_group_' + index + '_lat' ).val( $( this ).data( 'lat' ) );
			$( '#gmb_markers_group_' + index + '_lng' ).val( $( this ).data( 'lng' ) );
			$( '#gmb_markers_group_' + index + '_reference' ).val( reference );

			get_editable_info_window( index, location_marker );

			//location clicked
			google.maps.event.addListener( location_marker, 'click', function () {
				get_info_window_content( index, location_marker );
			} );

		} );

		//Map Marker Set
		set_map_marker_icon();

		//Map Type
		$( '#gmb_type' ).change( function () {
			set_map_type( true );
		} );
		//Map Theme
		$( '#gmb_theme' ).change( function () {
			set_map_theme( true );
		} );
		//street view
		$( '#gmb_street_view' ).change( function () {
			set_street_view();
		} );
		//Pan
		$( '#gmb_pan' ).change( function () {
			set_pan_control();
		} );
		//Draggable
		$( '#gmb_draggable' ).change( function () {
			set_draggable();
		} );
		//Double Click Zoom
		$( '#gmb_double_click' ).change( function () {
			set_double_click_zoom();
		} );
		//Double Click Zoom
		$( '#gmb_wheel_zoom' ).change( function () {
			set_mouse_wheel_scroll();
		} );
		//Map Type Control
		$( '#gmb_map_type_control' ).change( function () {
			set_map_type_control();
		} );
		//Zoom Control
		$( '#gmb_zoom_control' ).change( function () {
			set_map_zoom_control();
		} );
		//Width/Height
		$( "#gmb_width_height-width, #gmb_width_height-height" ).keyup( function () {
			delay( function () {
				set_map_size();
			}, 500 );
		} );
		$( 'input[name="gmb_width_height[map_width_unit]"]' ).change( function () {
			set_map_size();
		} );

	} ); //End Window Load


	var map;
	var places_service;
	var lat_lng;
	var zoom;
	var lat_field;
	var lng_field;
	var radius_circle;
	var radius_marker;
	var place;
	var autocomplete;
	var info_bubble;
	var info_bubble_array = [];
	var tentative_location_marker;
	var location_marker;
	var location_marker_array = [];
	var search_markers = [];
	var hover_circle;
	var initial_location;
	var delay = (function () {
		var timer = 0;
		return function ( callback, ms ) {
			clearTimeout( timer );
			timer = setTimeout( callback, ms );
		};
	})();

	/**
	 * Place marker on Map on Click
	 *
	 * @param lat_lng
	 * @param event
	 */
	function drop_marker( lat_lng, event ) {

		var lat = lat_lng.lat();
		var lng = lat_lng.lng();

		//hide any tentative markers already in place
		if ( typeof drop_location_marker !== 'undefined' ) {
			drop_location_marker.setVisible( false );
		}

		$( '.drop-marker' ).removeClass( 'active' ).text( 'Drop a Marker' ); //reset drop button
		map.setOptions( {draggableCursor: null} ); //reset cursor
		google.maps.event.removeListener( event ); //remove map click event

		//add marker at clicked location
		var drop_location_marker = new Marker( {
			position : lat_lng,
			map      : map,
			icon     : gmb_data.plugin_url + "/public/assets/img/default-marker.png",
			zIndex   : google.maps.Marker.MAX_ZINDEX + 1,
			optimized: false
		} );

		//get current number of repeatable rows ie markers
		var index = get_marker_index();

		//add data to fields
		$( '#gmb_markers_group_' + index + '_title' ).val( 'Point ' + index );
		$( '#gmb_markers_group_' + index + '_lat' ).val( lat );
		$( '#gmb_markers_group_' + index + '_lng' ).val( lng );

		get_editable_info_window( index, drop_location_marker );

		google.maps.event.addListener( drop_location_marker, 'click', function () {
			get_info_window_content( index, drop_location_marker );
		} );

	}

	/**
	 * Map Intialize
	 *
	 * Sets up and configures the Google Map
	 *
	 * @param map_canvas
	 */
	function initialize_map( map_canvas ) {

		lat_field = $( '#gmb_lat_lng-latitude' );
		lng_field = $( '#gmb_lat_lng-longitude' );
		var latitude = ((lat_field.val()) ? lat_field.val() : '');
		var longitude = ((lng_field.val()) ? lng_field.val() : '');
		zoom = parseInt( $( '#gmb_zoom' ).val() );
		lat_lng = new google.maps.LatLng( latitude, longitude );

		var mapOptions = {
			zoom             : zoom,
			streetViewControl: false,
			styles           : [
				{
					stylers: [
						{visibility: 'simplified'}
					]
				},
				{
					elementType: 'labels', stylers: [
					{visibility: 'off'}
				]
				}
			]
		};

		map = new google.maps.Map( map_canvas[0], mapOptions );
		places_service = new google.maps.places.PlacesService( map );


		//Handle Map Geolocation
		if ( navigator.geolocation && gmb_data.geolocate_setting === 'yes' && longitude == '' && latitude == '' ) {
			navigator.geolocation.getCurrentPosition( function ( position ) {
				initial_location = new google.maps.LatLng( position.coords.latitude, position.coords.longitude );
				map.setCenter( initial_location ); //set map with location
				lat_field.val( position.coords.latitude ); //set lat field
				lng_field.val( position.coords.longitude ); //set lng field
			} );
		}
		// Presaved longitude and latitude is in place
		else if ( latitude !== '' && longitude !== '' ) {

			//set map with saved lat/lng
			map.setCenter( new google.maps.LatLng( latitude, longitude ) );

		}
		// Browser doesn't support Geolocation
		else {
			alert( 'Geolocation service failed.' );
			initial_location = new google.maps.LatLng( gmb_data.default_lat, gmb_data.default_lng );
			lat_field.val( gmb_data.default_lat ); //set lat field
			lng_field.val( gmb_data.default_lng ); //set lng field
			map.setCenter( initial_location );
		}


		//Set various map view options
		set_map_type( false );
		if ( $( '#gmb_theme' ).val() !== 'none' ) {
			set_map_theme( false );
		}
		set_street_view();
		set_pan_control();
		set_draggable();
		set_double_click_zoom();
		set_mouse_wheel_scroll();
		set_map_type_control();
		set_map_zoom_control();


		//Setup Autocomplete field if undefined
		if ( typeof(autocomplete) == 'undefined' ) {

			autocomplete = new google.maps.places.Autocomplete( $( '#gmb_geocoder' )[0] );
			autocomplete.bindTo( 'bounds', map );

			//Autocomplete event listener
			google.maps.event.addListener( autocomplete, 'place_changed', function () {

				//Clear autocomplete input value
				$( '#gmb_geocoder' ).one( 'blur', function () {
					$( '#gmb_geocoder' ).val( "" );
				} );
				setTimeout( function () {
					$( '#gmb_geocoder' ).val( "" );
				}, 10 );


				if ( typeof tentative_location_marker !== 'undefined' ) {
					tentative_location_marker.setVisible( false );
				}

				//get place information
				place = autocomplete.getPlace();

				//set lat lng input values
				lat_field.val( place.geometry.location.lat() );
				lng_field.val( place.geometry.location.lng() );


				if ( !place.geometry ) {
					alert( 'Error: Place not found!' );
					return;
				}

				map.setCenter( place.geometry.location );
				add_tentative_marker( map, place.reference );

			} );
		}

		//InfoBubble - Contains the place's information and content
		info_bubble = new google.maps.InfoWindow( {
			maxWidth: 315
		} );

		/**
		 * Map Event Listeners
		 */
			//map loaded fully (fires once)
		google.maps.event.addListenerOnce( map, 'idle', function () {
			handle_map_zoom( map );
			add_markers( map );

			//toggle places
			if ( typeof $( '.cmb_id_gmb_show_places input:radio' ).prop( 'checked' ) !== 'undefined' && $( '.cmb_id_gmb_show_places input:radio:checked' ).val() === 'yes' ) {
				perform_places_search();
			}

		} );

		//map Zoom Changed
		google.maps.event.addListener( map, 'zoom_changed', function () {
			handle_map_zoom( map );
		} );

		//Update lng and lat on map drag
		google.maps.event.addListener( map, 'dragend', function () {
			var map_center = map.getCenter();
			$( '.lat-lng-change-message' ).slideDown();
			$( '.lat-lng-update-btn' ).attr( 'data-lat', map_center.lat() );
			$( '.lat-lng-update-btn' ).attr( 'data-lng', map_center.lng() );
		} );


	} //end initialize_map


	/**
	 * Shows a Marker when Autocomplete search is used
	 * @param map
	 * @param reference
	 */
	function add_tentative_marker( map, reference ) {

		var map_center = map.getCenter();

		//Marker for map
		tentative_location_marker = new google.maps.Marker( {
			map      : map,
			title    : 'Map Icons',
			animation: google.maps.Animation.DROP,
			position : new google.maps.LatLng( map_center.lat(), map_center.lng() ),
			icon     : new google.maps.MarkerImage( gmb_data.plugin_url + "/public/assets/img/temp-marker.png" ),
			zIndex   : google.maps.Marker.MAX_ZINDEX + 1,
			optimized: false
		} );

		//EVENTS
		var location_marker_mouseover = google.maps.event.addListener( tentative_location_marker, 'mouseover', function ( event ) {
			add_circle( reference );
		} );
		var location_marker_mouseout = google.maps.event.addListener( tentative_location_marker, 'mouseout', function ( event ) {
			hover_circle.setVisible( false );
		} );

		//location clicked
		google.maps.event.addListener( tentative_location_marker, 'click', function () {
			//remove event listeners
			google.maps.event.removeListener( location_marker_mouseover );
			google.maps.event.removeListener( location_marker_mouseout );
			//show circle
			hover_circle.setVisible( true );
			//update marker icons
			//Get initial place details from reference
			add_tenative_info_window( reference, tentative_location_marker );
		} );


		//Update map with marker position according to lat/lng
		tentative_location_marker.setVisible( true );
		map.setZoom( zoom );


	}


	/**
	 * Set the editable marker window content
	 */
	function add_tenative_info_window( reference, marker ) {

		var request = {
			key      : gmb_data.api_key,
			reference: reference
		};

		places_service.getDetails( request, function ( place, status ) {

			if ( status == google.maps.places.PlacesServiceStatus.OK ) {

				var lat = place.geometry.location.lat();
				var lng = place.geometry.location.lng();

				var info_window_content = '<p class="place-title">' + place.name + '</p>';

				info_window_content += add_place_content_to_info_window( place );


				info_window_content += '<div class="infowindow-toolbar clear"><a href="#" class="add-marker" data-title="' + place.name + '" data-reference="' + place.reference + '"  data-lat="' + lat + '" data-lng="' + lng + '">Add to Map</a></div>';

				info_window_content = set_info_window_wrapper( info_window_content ); //wraps the content in div and returns

				info_bubble.setContent( info_window_content ); //sets the info window content

				info_bubble.open( map, marker ); //opens the info window

				//close info window button
				google.maps.event.addListener( info_bubble, 'closeclick', function () {
					//Get initial place details from reference
					hover_circle.setVisible( false );

				} );


			}

		} );

	}


	/**
	 * info_bubble Content for Place Details
	 *
	 * This marker contains more information about the place
	 *
	 * @param place
	 */
	function add_place_content_to_info_window( place ) {

		var info_window_content;

		//additional info wrapper
		info_window_content = '<div class="marker-info-wrapper">';

		//place address
		info_window_content += ((place.formatted_address) ? '<div class="place-address">' + place.formatted_address + '</div>' : '' );

		//place phone
		info_window_content += ((place.formatted_phone_number) ? '<div class="place-phone">' + place.formatted_phone_number + '</div>' : '' );

		//place website
		info_window_content += ((place.website) ? '<div class="place-website"><a href="' + place.website + '" target="_blank" rel="nofollow" title="Click to visit the ' + place.name + ' website">Website</a></div>' : '' );

		//rating
		if ( place.rating ) {
			info_window_content += '<div class="rating-wrap clear">' +
			'<p class="numeric-rating">' + place.rating + '</p>' +
			'<div class="star-rating-wrap">' +
			'<div class="star-rating-size" style="width:' + (65 * place.rating / 5) + 'px;"></div>' +
			'</div>' +
			'</div>'
		}


		//close wrapper
		info_window_content += '</div>';


		return info_window_content;

	}


	/**
	 * info_bubble Content for Place Details
	 *
	 * This marker contains more information about the place
	 */
	function get_editable_info_window( index, marker ) {

		info_bubble.close();

		info_bubble.setContent( '<div id="infobubble-content" class="loading"></div>' );

		info_bubble.open( map, marker );

		var info_window_data = get_info_window_saved_data( index );

		var info_window_content;

		//default title
		if ( !info_window_data.title ) {
			info_window_data.title = 'Point ' + index;
		}

		//place name
		info_window_content = '<input class="edit-place-title" data-field="#gmb_markers_group_' + index + '_title" type="text" value="' + info_window_data.title + '">';

		info_window_content += '<textarea class="edit-place-description" data-field="#gmb_markers_group_' + index + '_description">' + info_window_data.desc + '</textarea>';

		//info_window_content += add_place_content_to_info_window( place );

		//toolbar
		info_window_content += '<div class="infowindow-toolbar clear"><ul id="save-toolbar">' +
		'<li class="info-window-save"><div class="google-btn-blue google-btn google-save-btn" data-tooltip="Save changes" data-index="' + index + '">Save</div></li>' +
		'<li class="info-window-cancel"><div class="google-btn-default google-btn google-cancel-btn" data-tooltip="Cancel edit" data-index="' + index + '">Cancel</div></li>' +
		'</ul>' +
		'<span class="marker-edit-link-wrap" data-index="' + index + '"><a href="#TB_inline?width=600&height=550&inlineId=marker-icon-modal" data-tooltip="Change icon" class="marker-edit-link thickbox"></a></span>' +
		'</div>';

		info_window_content = set_info_window_wrapper( info_window_content );
		info_bubble.setContent( info_window_content );
		initialize_tooltips(); //refresh tooltips

		//Save info window content
		google.maps.event.addDomListener( $( '.google-save-btn' )[0], 'click', function () {

			//take info window vals and save to markers' repeatable group
			var title_field_id = $( '.edit-place-title' ).data( 'field' );
			var title_field_val = $( '.edit-place-title' ).val();

			var desc_field_id = $( '.edit-place-description' ).data( 'field' );
			var desc_field_val = $( '.edit-place-description' ).val();

			$( title_field_id ).val( title_field_val );
			$( desc_field_id ).val( desc_field_val );

			//close info window and remove marker circle
			get_info_window_content( $( this ).data( 'index' ), marker );
			google.maps.event.removeListener( save_icon_listener ); //remove this event listener
			google.maps.event.removeListener( edit_marker_icon_button_click ); //remove this event listener

		} );


		//Close Click
		google.maps.event.addDomListener( info_bubble, 'closeclick', function () {
			google.maps.event.removeListener( save_icon_listener ); //remove this event listener
			google.maps.event.removeListener( edit_marker_icon_button_click ); //remove this event listener
		} );

		//Cancel info window content
		google.maps.event.addDomListener( $( '.google-cancel-btn' )[0], 'click', function () {
			//close info window and remove marker circle
			get_info_window_content( $( this ).data( 'index' ), marker );
			google.maps.event.removeListener( save_icon_listener ); //remove this event listener
			google.maps.event.removeListener( edit_marker_icon_button_click ); //remove this event listener

		} );

		//Infowindow pin icon click to open ThickBox modal
		var edit_marker_icon_button_click = google.maps.event.addDomListener( $( '.marker-edit-link-wrap' )[0], 'click', function () {
			$( '.save-marker-button' ).attr( 'data-marker-index', $( this ).data( 'index' ) ); //Set the index for this marker
		} );


		//Marker Modal Update Icon
		var save_icon_listener = google.maps.event.addDomListener( $( '.save-marker-button' )[0], 'click', function () {

			var marker_position = marker.getPosition();
			var marker_icon_data;
			var marker_icon = $( this ).data( 'marker' );
			var marker_icon_color = $( this ).data( 'marker-color' );
			var label_color = $( this ).data( 'label-color' );

			//Inline style for marker to set
			var marker_label_inline_style = 'color:' + label_color + '; ';
			if ( marker_icon === 'MAP_PIN' ) {
				marker_label_inline_style += 'font-size: 20px;position: relative; top: -3px;'; //position: relative; top: -44px; font-size: 24px;
			} else if ( marker_icon == 'SQUARE_PIN' ) {
				marker_label_inline_style += 'font-size: 20px;position: relative; top: 12px;';
			}

			//collect marker data from submit button
			var marker_label_data = '<i class="' + $( this ).data( 'label' ) + '" style="' + marker_label_inline_style + '"></i>';

			//Set marker icon data
			if ( marker_icon == '' ) {
				//default icon
				marker_icon_data = gmb_data.plugin_url + '/public/assets/img/default-marker.png';
				$( '#gmb_markers_group_' + index + '_marker' ).val( '' );
				marker_label_data = '';
			} else {
				//maps-icon
				marker_icon_data = '{ path : ' + marker_icon + ', fillColor : "' + marker_icon_color + '", fillOpacity : 1, strokeColor : "", strokeWeight: 0, scale : 1 / 3 }';
				$( '#gmb_markers_group_' + index + '_marker' ).val( marker_icon_data );
				marker_icon_data = eval( '(' + marker_icon_data + ')' )
			}

			//remove current marker
			marker.setMap( null );

			//Update fields with necessary data
			$( '#gmb_markers_group_' + index + '_label' ).val( marker_label_data );

			//Update Icon
			marker = new Marker( {
				position: marker_position,
				map     : map,
				zIndex  : 9,
				icon    : marker_icon_data,
				label   : marker_label_data
			} );

			//Add event listener to new marker
			google.maps.event.addListener( marker, 'click', function () {
				get_info_window_content( index, marker );
			} );

			//Clean up modal and close
			$( '.icon, .marker-item' ).removeClass( 'marker-item-selected' ); //reset modal
			$( '.marker-icon-row, .save-marker-icon, .marker-icon-color-wrap, .marker-label-color-wrap' ).hide(); //reset modal
			$( this ).removeData( 'marker' ); //Remove data
			$( this ).removeData( 'marker-color' ); //Remove data
			$( this ).removeData( 'label' ); //Remove data
			$( this ).removeData( 'label-color' ); //Remove data
			tb_remove(); //close TB lightbox
			google.maps.event.removeListener( save_icon_listener ); //remove this event listener
			google.maps.event.removeListener( edit_marker_icon_button_click ); //remove this event listener

		} );

	}

	/**
	 * Wrap Info Window Content
	 *
	 * Help function that sets a div container around info window
	 * @param content
	 */
	function set_info_window_wrapper( content ) {

		var info_window_content = '<div id="infobubble-content" class="main-place-infobubble-content">';

		info_window_content += content;

		info_window_content += '</div>';

		return info_window_content;

	}


	/**
	 * Adds a marker circle
	 */
	function add_circle( reference ) {

		hover_circle = new google.maps.Marker( {
			position : tentative_location_marker.getPosition(),
			zIndex   : google.maps.Marker.MAX_ZINDEX - 1,
			optimized: false,
			icon     : {
				path         : google.maps.SymbolPath.CIRCLE,
				scale        : 20,
				strokeWeight : 3,
				strokeOpacity: 0.9,
				strokeColor  : '#FFF',
				fillOpacity  : .3,
				fillColor    : '#FFF'
			},
			map      : map
		} );


		google.maps.event.addListener( hover_circle, 'click', function () {
			//Get initial place details from reference
			add_tenative_info_window( reference, tentative_location_marker );
		} );
		google.maps.event.addListener( tentative_location_marker, 'click', function () {
			//Get initial place details from reference
			hover_circle.setVisible( true );
		} );

	}


	/**
	 *  Add Markers
	 *
	 * This is the marker that first displays on load for the main location or place
	 *
	 * @param map
	 */
	function add_markers( map ) {

		clear_main_markers();

		//Loop through repeatable field of markers
		$( "#gmb_markers_group_repeat .repeatable-grouping" ).each( function ( index ) {

			var marker_icon = gmb_data.plugin_url + '/public/assets/img/default-marker.png';
			var marker_label = '';

			//check for custom marker and label data
			var custom_marker_icon = $( '#gmb_markers_group_' + index + '_marker' ).val();
			if ( custom_marker_icon.length > 0 ) {
				marker_icon = eval( "(" + custom_marker_icon + ")" );
			}
			var custom_label = $( '#gmb_markers_group_' + index + '_label' ).val();
			if ( custom_label.length > 0 ) {
				marker_label = custom_label;
			}

			//Marker for map
			var location_marker = new Marker( {
				map   : map,
				zIndex: 9,
				icon  : marker_icon,
				label : marker_label
			} );

			var marker_lat = $( '#gmb_markers_group_' + index + '_lat' ).val();
			var marker_lng = $( '#gmb_markers_group_' + index + '_lng' ).val();

			location_marker.setPosition( new google.maps.LatLng( marker_lat, marker_lng ) );
			location_marker.setVisible( true );

			google.maps.event.addListener( location_marker, 'click', function () {
				get_info_window_content( index, location_marker );
			} );

		} ); //end $.each()

	}


	function get_info_window_saved_data( index ) {

		var info_window_data = new Object();

		info_window_data.title = $( '#gmb_markers_group_' + index + '_title' ).val();
		info_window_data.desc = $( '#gmb_markers_group_' + index + '_description' ).val();
		info_window_data.reference = $( '#gmb_markers_group_' + index + '_reference' ).val();
		info_window_data.lat = $( '#gmb_markers_group_' + index + '_lat' ).val();
		info_window_data.lng = $( '#gmb_markers_group_' + index + '_lng' ).val();

		return info_window_data;


	}

	/**
	 * Queries to get Google Place Details information
	 *
	 * Help function
	 * @param index
	 * @param marker
	 */
	function get_info_window_content( index, marker ) {

		info_bubble.close();

		info_bubble.setContent( '<div id="infobubble-content" class="loading"></div>' );

		info_bubble.open( map, marker );

		var info_window_data = get_info_window_saved_data( index );

		var info_window_content;


		//Show place information within info bubble
		if ( info_window_data.reference ) {

			var request = {
				reference: info_window_data.reference
			};
			places_service.getDetails( request, function ( place, status ) {
				if ( status == google.maps.places.PlacesServiceStatus.OK ) {
					//place name
					info_window_content = '<p class="place-title">' + info_window_data.title + '</p>';

					info_window_content += '<div class="place-description">' + info_window_data.desc + '</div>';

					info_window_content += add_place_content_to_info_window( place );
					//toolbar
					info_window_content += '<div class="infowindow-toolbar"><ul id="edit-toolbar">' +
					'<li class="edit-info" data-index="' + index + '" data-tooltip="Edit Marker"></li>' +
					'<li class="trash-marker" data-index="' + index + '" data-tooltip="Delete Marker"></li>' +
					'</ul>' +
					'</div>';

					add_edit_events( info_window_content, marker );

				}
			} ); //end getPlaces


		} else {
			//Only show saved data (no place lookup)

			//place name
			info_window_content = '<p class="place-title">' + info_window_data.title + '</p>';

			info_window_content += '<div class="place-description">' + info_window_data.desc + '</div>';
			//toolbar
			info_window_content += '<div class="infowindow-toolbar"><ul id="edit-toolbar">' +
			'<li class="edit-info" data-index="' + index + '" data-tooltip="Edit Marker"></li>' +
			'<li class="trash-marker" data-index="' + index + '" data-tooltip="Delete Marker"></li>' +
			'</ul>' +
			'</div>';

			add_edit_events( info_window_content, marker );


		}


	}

	/**
	 * Add Edit Events
	 *
	 * Sets up Google Map event listeners and other setup for info bubbles
	 *
	 * @param content
	 * @param marker
	 */
	function add_edit_events( content, marker ) {

		content = set_info_window_wrapper( content ); //wraps the content in div and returns
		info_bubble.setContent( content ); //set infowindow content
		initialize_tooltips(); //refresh tooltips

		//edit button event
		google.maps.event.addDomListener( $( '.edit-info' )[0], 'click', function () {
			//Edit Marker
			get_editable_info_window( $( this ).data( 'index' ), marker );
		} );

		//trash button event
		google.maps.event.addDomListener( $( '.trash-marker' )[0], 'click', function () {
			var index = $( this ).data( 'index' );

			//if first item clear out all input values
			if ( index === 0 ) {
				$( 'tr[data-iterator="' + index + '"] ' ).find( 'input,textarea' ).val( '' );
			}

			//trigger remove row button click for this specific markers row
			$( 'tr[data-iterator="' + index + '"] .remove-group-row' ).trigger( 'click' );
			//close info window and remove marker
			info_bubble.close();
			marker.setVisible( false );
		} );

	}


	/**
	 * Marker Index
	 *
	 * Helper function that returns the appropriate index for the repeatable group
	 *
	 */
	function get_marker_index() {
		//Create a new marker repeatable meta group
		var index = parseInt( $( '#gmb_markers_group_repeat tr.repeatable-grouping' ).last().attr( 'data-iterator' ) );
		var existing_vals = $( 'tr[data-iterator="0"] ' ).find( 'input,textarea' ).val();

		//Ensure appropriate index is used for marker
		if ( existing_vals && index === 0 ) {
			$( '.add-group-row.button' ).trigger( 'click' );
			index = 1;
		} else if ( index !== 0 ) {
			$( '.add-group-row.button' ).trigger( 'click' );
			//recount rows
			index = parseInt( $( '#gmb_markers_group_repeat tr.repeatable-grouping' ).last().attr( 'data-iterator' ) );
		}
		return index;
	}

	/**
	 * Google Places Marker Info Window
	 *
	 * @param place
	 * @param marker
	 */
	function get_place_info_window_content( place, marker ) {

		info_bubble.setContent( '<div id="infobubble-content" class="loading"></div>' );

		info_bubble.open( map, marker );

		var request = {
			reference: place.reference
		};
		places_service.getDetails( request, function ( place, status ) {
			if ( status == google.maps.places.PlacesServiceStatus.OK ) {

				var info_window_content;

				//place name
				info_window_content = '<p class="place-title">' + place.name + '</p>';

				info_window_content += add_place_content_to_info_window( place );

				info_window_content = set_info_window_wrapper( info_window_content ); //wraps the content in div and returns

				info_bubble.setContent( info_window_content );

				initialize_tooltips(); //refresh tooltips

			}
		} );
	}


	/**
	 * Get Places Types Array
	 *
	 * Loops through checkboxes and returns array of checked values
	 *
	 * @returns get_places_type
	 */
	function get_places_type_array() {

		var types_array = [];

		$( '.cmb_id_gmb_places_search_multicheckbox input[type="checkbox"]' ).each( function () {
			if ( $( this ).is( ':checked' ) ) {
				types_array.push( $( this ).val() );
			}

		} );

		return types_array;

	}


	/**
	 * Google Places Nearby Search
	 *
	 */
	function perform_places_search() {

		$( '.places-loading' ).fadeIn();
		$( '.warning-message' ).hide().empty();

		var types_array = get_places_type_array();

		clear_search_markers();

		//Check if any place types are selected
		if ( types_array.length > 0 ) {

			//perform search request
			var request = {
				location: return_lat_lng(),
				types   : types_array,
				radius  : parseInt( $( '#gmb_search_radius' ).val() )
			};
			places_service.nearbySearch( request, places_search_callback );
		}
		//Display notice that no places are selected
		else {

			show_warning_message( '<strong>Notice: No Place Types are selected</strong><br/> Please select the types of places you would like to display on this map using the Place Type field checkboxes found below.' );

		}

	}


	/**
	 * Warning Messages
	 *
	 * Helper function that shows a warning message below the google map
	 * @param message
	 */
	function show_warning_message( message ) {
		$( '.wpgp-loading' ).fadeOut(); //fade out all loading items
		$( '.warning-message' ).empty().append( '<p>' + message + '</p>' ).fadeIn();
	}


	/**
	 *
	 * Returns Maps current Long and Latitude Object
	 *
	 * Helper Function
	 *
	 * @returns lat_lng
	 */
	function return_lat_lng() {
		var map_center = map.getCenter();
		var lat_lng = new google.maps.LatLng( map_center.lat(), map_center.lng() );
		return lat_lng;
	}

	/**
	 * Map Zoom
	 *
	 * Sets the map zoom field and variable
	 *
	 */
	function handle_map_zoom( map ) {

		var new_zoom = map.getZoom();

		$( '#gmb_zoom' ).val( new_zoom );

		$( '#gmb_zoom' ).on( 'change', function () {
			map.setZoom( parseInt( $( this ).val() ) );
		} );

	}

	/**
	 * Map Lat Lng
	 *
	 * Sets the map zoom field and variable
	 */
	function lat_lng_field_change( map ) {
		var pan_point = new google.maps.LatLng( $( lat_field ).val(), $( lng_field ).val() );
		map.panTo( pan_point );
	}


	/**
	 * Places Search Callback
	 *
	 * Used to loop through results and call function to create search result markers
	 *
	 * @param results
	 * @param status
	 * @param pagination
	 */
	function places_search_callback( results, status, pagination ) {

		var i = 0;
		var result;

		//setup new markers
		if ( status == google.maps.places.PlacesServiceStatus.OK ) {

			//place new markers
			for ( i = 0; result = results[i]; i++ ) {
				create_search_result_marker( results[i] );
			}

			//show all pages of results
			//@see: http://stackoverflow.com/questions/11665684/more-than-20-results-by-pagination-with-google-places-api
			if ( pagination.hasNextPage ) {
				pagination.nextPage();
			} else {
				$( '.places-loading' ).fadeOut();
			}

		}
	}

	/**
	 * Create Search Result Marker
	 *
	 * Used with Places Search to place markers on map
	 *
	 * @param place
	 */
	function create_search_result_marker( place ) {

		var search_marker = new Marker( {
			map      : map,
			zIndex   : 0,
			optimized: false
		} );
		//setup marker icon
		search_marker.setIcon( /** @type {google.maps.Icon} */({
			url       : place.icon,
			size      : new google.maps.Size( 24, 24 ),
			origin    : new google.maps.Point( 0, 0 ),
			anchor    : new google.maps.Point( 17, 34 ),
			scaledSize: new google.maps.Size( 24, 24 )
		}) );

		search_marker.setPosition( place.geometry.location );
		search_marker.setVisible( true );


		google.maps.event.addListener( search_marker, 'click', function () {
			get_place_info_window_content( place, search_marker );
		} );

		search_markers.push( search_marker )

	}


	/**
	 * Clears Main Markers
	 *
	 * Used to clear out main location marker to prevent from displaying multiple
	 *
	 */
	function clear_main_markers() {

		//clear markers
		for ( var i = 0; i < location_marker_array.length; i++ ) {
			location_marker_array[i].setMap( null );
		}
		location_marker_array.length = 0;

		//clear infowindows
		for ( i = 0; i < info_bubble_array.length; i++ ) {
			info_bubble_array[i].close();
			google.maps.event.trigger( info_bubble_array[i], 'closeclick' );
		}
		info_bubble_array.length = 0;
	}

	/**
	 * Clears Search Markers
	 *
	 * Used to clear out main search markers
	 *
	 */
	function clear_search_markers() {

		//remove existing markers
		for ( var i = 0; i < search_markers.length; i++ ) {
			search_markers[i].setMap( null );
		}
		search_markers = [];

	}


	/**
	 * Geocode new marker position
	 *
	 * Perform nearby search request to see if the marker landed on a place
	 *
	 * @see: http://stackoverflow.com/questions/5688745/google-maps-v3-draggable-marker
	 * @param pos
	 */
	function geocode_position( pos ) {

		var request = {
			location: pos,
			radius  : 10
		};
		places_service.nearbySearch( request, function ( results, status ) {

			if ( status == google.maps.places.PlacesServiceStatus.OK ) {

				var info_bubble_content = '';
				info_bubble.close();

				//if more than one result ask the user which one?
				if ( results.length > 1 ) {

					info_bubble_content = '<div id="infobubble-content"><p>Hmm, it looks like there are multiple places in this area. Please confirm which place you would like this marker to display:</p>';

					for ( var i = 0; i < results.length; i++ ) {
						info_bubble_content += '<a class="marker-confirm-place"  data-reference="' + results[i].reference + '" data-name-address="' + results[i].name + ', ' + results[i].vicinity + '">' + results[i].name + '</a>';
					}

					info_bubble_content += '</div>';

					//setup click event for links
					google.maps.event.addDomListener( info_bubble, 'domready', function () {
						$( '.marker-confirm-place' ).on( 'click', function ( e ) {
							e.preventDefault();
							$( '#gmb_geocoder' ).val( $( this ).data( 'name-address' ) );
							$( '#gmb_reference' ).val( $( this ).data( 'reference' ) );
							info_bubble.close();
							get_info_window_content( $( this ).data( 'reference' ) );
							//info_bubble.open( location_marker );
						} );
					} );


				}

				info_bubble.setContent( info_bubble_content );

				info_bubble.open( map, location_marker );


			}

		} );

	}


	/**
	 * Scroll to Selector
	 *
	 * Helper function that scroll the user up to the map
	 */
	function scroll_to_field( selector ) {
		//scroll to the map
		$( 'html, body' ).animate( {
			scrollTop: parseInt( $( selector ).offset().top )
		}, 600 );
	}

	/**
	 * Marker Drag End
	 *
	 * Executes after a user drags the initial marker
	 *
	 * @param marker
	 */
	function marker_drag_end( marker ) {

		var map_center = marker.getPosition();
		geocode_position( map_center );
		//update with new map coordinates
		$( lat_field ).val( map_center.lat() );
		$( lng_field ).val( map_center.lng() );

		//Map centered on this location
		map.panTo( map_center );

	}

	/**
	 * Radius Circle
	 *
	 * Draws a circle when user focuses on the radius input
	 *
	 * @see: http://jsfiddle.net/yV6xv/3730/
	 * @param map
	 * @param radiusVal
	 */
	function calc_radius( map, radiusVal ) {

		//update marker with set marker
		radius_marker = new Marker( {
			position : map.getCenter(),
			map      : map,
			icon     : {
				path        : MAP_PIN,
				fillColor   : '#0E77E9',
				fillOpacity : 0,
				strokeColor : '',
				strokeWeight: 0,
				scale       : 1 / 4
			},
			label    : '<i class="map-icon-crosshairs radius-label"></i>',
			zIndex   : google.maps.Marker.MAX_ZINDEX + 1,
			optimized: false
		} );

		radius_circle = new google.maps.Circle( {
			map          : map,
			fillColor    : '#BBD8E9',
			fillOpacity  : 0.3,
			radius       : radiusVal,
			strokeColor  : '#BBD8E9',
			strokeOpacity: 0.9,
			strokeWeight : 2
		} );

		radius_circle.bindTo( 'center', radius_marker, 'position' );

	}


	/**
	 * Show/ Hide Map Fields
	 *
	 * Helper function that handles all the toggle elements within the CPT admin post screen
	 *
	 */
	function toggle_metabox_fields() {

		var show_places = $( '.cmb_id_gmb_show_places input:radio' );

		//Places Metabox
		if ( show_places.prop( 'checked' ) === true ) {
			$( '.cmb_id_gmb_search_radius' ).show();
			$( '.cmb_id_gmb_places_search_multicheckbox' ).show();
		}

		//Nothing checked yet so select 'No' by default
		if ( show_places.prop( 'checked' ) === false ) {
			$( '#gmb_show_places2' ).prop( 'checked', true );
		}

		show_places.on( 'change', function () {

			$( '.cmb_id_gmb_search_radius' ).toggle();
			$( '.cmb_id_gmb_places_search_multicheckbox' ).toggle();

			if ( $( this ).val() === 'no' ) {
				clear_search_markers();
			} else {
				perform_places_search();
			}

		} );

	}

	/**
	 * Set Map Size
	 */
	function set_map_size() {
		var map_width = $( '#gmb_width_height-width' ).val();
		var map_width_value = $( 'input[name="gmb_width_height[map_width_unit]"]:checked' ).val();
		var map_height = $( '#gmb_width_height-height' ).val();
		$( '#map' ).css( {
			'width' : map_width + map_width_value,
			'height': map_height
		} );
	}


	/**
	 * Set Zoom Control
	 */
	function set_map_zoom_control() {

		var zoom_control = $( '#gmb_zoom_control' ).val().toUpperCase();

		if ( zoom_control == 'NONE' ) {
			map.setOptions( {
				zoomControl: false
			} );
		} else {
			map.setOptions( {
				zoomControl       : true,
				zoomControlOptions: {
					style: google.maps.ZoomControlStyle[zoom_control]
				}
			} );
		}
	}


	/**
	 * Set Map Type Control
	 */
	function set_map_type_control() {
		var map_type_control = $( '#gmb_map_type_control' ).val().toUpperCase();
		if ( map_type_control == 'NONE' ) {
			map.setOptions( {
				mapTypeControl: false
			} );
		} else {
			map.setOptions( {
				mapTypeControl       : true,
				mapTypeControlOptions: {
					style: google.maps.MapTypeControlStyle[map_type_control]
				}
			} );
		}
	}

	/**
	 * Sets Mouse Wheel Scroll
	 */
	function set_mouse_wheel_scroll() {
		var mouse_wheel_scroll = $( '#gmb_wheel_zoom' ).val();
		if ( mouse_wheel_scroll === 'none' ) {
			map.setOptions( {
				scrollwheel: false
			} );
		} else {
			map.setOptions( {
				scrollwheel: true
			} );
		}
	}

	/**
	 * Sets Double Click Zoom on Map
	 */
	function set_double_click_zoom() {
		var double_click_zoom = $( '#gmb_double_click' ).val();
		if ( double_click_zoom === 'none' ) {
			map.setOptions( {
				disableDoubleClickZoom: true
			} );
		} else {
			map.setOptions( {
				disableDoubleClickZoom: false
			} );
		}
	}

	/**
	 * Sets Draggable Map
	 */
	function set_draggable() {
		var draggable = $( '#gmb_draggable' ).val();
		if ( draggable == 'none' ) {
			map.setOptions( {
				draggable: false
			} );
		} else {
			map.setOptions( {
				draggable: true
			} );
		}
	}

	/**
	 * Sets the Pan Control
	 */
	function set_pan_control() {

		var pan = $( '#gmb_pan' ).val();
		if ( pan === 'none' ) {
			map.setOptions( {
				panControl: false
			} );
		} else {
			map.setOptions( {
				panControl: true
			} );
		}
	}

	/**
	 * Sets the Street View Control
	 */
	function set_street_view() {

		var street_view = $( '#gmb_street_view' ).val();
		if ( street_view === 'none' ) {
			map.setOptions( {
				streetViewControl: false
			} );
		} else {
			map.setOptions( {
				streetViewControl: true
			} );
		}
	}

	/**
	 * Sets the Map Type
	 *
	 * Changes the Google Map type and resets theme to none
	 */
	function set_map_type( reset ) {
		if ( reset === true ) {
			$( '#gmb_theme' ).val( 'none' );
			$( '#gmb_theme_json' ).val( 'none' );
		}

		var map_type = $( '#gmb_type' ).val().toUpperCase();
		map.setOptions( {
			mapTypeId: google.maps.MapTypeId[map_type],
			styles   : false
		} );
	}

	/**
	 * Sets the Map Theme
	 *
	 * Uses Snazzy Maps JSON arrow to set the colors for the map
	 *
	 */
	function set_map_theme( reset ) {
		if ( reset === true ) {
			$( '#gmb_type' ).val( 'RoadMap' );
			$( '#gmb_theme_json' ).val( 'none' );
		}
		//AJAX to get JSON data for Snazzy
		$.getJSON( gmb_data.snazzy, function ( data ) {

			var map_theme_input_val = parseInt( $( '#gmb_theme' ).val() );

			if ( map_theme_input_val === 'none' ) {
				set_map_type();
			}
			$.each( data, function ( index ) {

				if ( data[index].id === map_theme_input_val ) {
					map_theme_input_val = eval( data[index].json );
					$( '#gmb_theme_json' ).val( data[index].json );
				}

			} );

			map.setOptions( {
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				styles   : map_theme_input_val
			} );
		} );

	}


	/**
	 * JS for Marker Icon Modal
	 */
	function set_map_marker_icon() {

		//Marker Item Click
		$( '.marker-item' ).on( 'click', function () {

			var marker_data = $( this ).data( 'marker' );
			$( '.marker-item' ).removeClass( 'marker-item-selected' );
			$( this ).addClass( 'marker-item-selected' );

			//default marker
			if ( marker_data == 'default' ) {

				$( '.marker-icon-row, .marker-icon-color-wrap, .marker-label-color-wrap' ).slideUp();
				$( '.save-marker-icon' ).slideDown();
				$( '#TB_window .save-marker-button' ).attr( 'data-marker', '' );
				$( '#TB_window .save-marker-button' ).attr( 'data-label', '' );

			} else {
				//custom markers
				$( '.marker-icon-color-wrap, .marker-icon-row' ).slideDown();
				$( '#TB_window .save-marker-button' ).attr( 'data-marker', marker_data ); //Set marker data attribute on save btn
			}

		} );


		//Icon Click
		$( '.icon' ).on( 'click', function () {
			$( '.icon' ).removeClass( 'marker-item-selected' );
			$( this ).addClass( 'marker-item-selected' );
			$( '.save-marker-icon, .marker-label-color-wrap' ).slideDown(); //slide down save button
			$( '#TB_window .save-marker-button' ).attr( 'data-label', $( this ).find( 'span' ).attr( 'class' ) ); //Set marker data attribute on save btn
		} );


		/**
		 * Colors
		 */
		//Setup colorpickers
		var color_picker_options = {
			// you can declare a default color here, or in the data-default-color attribute on the input
			// a callback to fire whenever the color changes to a valid color
			change  : function ( event, ui ) {

				var this_color = ui.color.toString();

				//Marker Color
				if ( $( this ).hasClass( 'marker-color' ) === true ) {

					$( '.save-marker-button' ).attr( 'data-marker-color', this_color );
					$( '.marker-svg polygon, .marker-svg path' ).attr( 'fill', this_color );

				} else if ( $( this ).hasClass( 'label-color' ) === true ) {

					$( '.save-marker-button' ).attr( 'data-label-color', this_color );
					$( '.icon-inner span' ).css( 'color', this_color );

				}


			},
			// a callback to fire when the input is emptied or an invalid color
			clear   : function () {
			},
			// hide the color picker controls on load
			hide    : true,
			// show a group of common colors beneath the square
			// or, supply an array of colors to customize further
			palettes: true
		};

		$( '.color-picker' ).wpColorPicker( color_picker_options );


	}

	/**
	 * Refresh Tooltips
	 *
	 * Helper function to refresh tooltips when elements added dynamically to DOM
	 */
	function initialize_tooltips() {
		$( '[data-tooltip!=""]' ).qtip( { // Grab all elements with a non-blank data-tooltip attr.
			content : {
				attr: 'data-tooltip' // Tell qTip2 to look inside this attr for its content
			},
			hide    : {
				fixed: true,
				delay: 100
			},
			position: {
				my: 'top center',
				at: 'bottom center'
			},
			style   : {
				classes: 'qtip-tipsy'
			},
			show    : {
				when  : {
					event: 'focus'
				},
				effect: function () {
					$( this ).fadeIn( 200 );
				}
			}
		} );
	}

}( jQuery ));