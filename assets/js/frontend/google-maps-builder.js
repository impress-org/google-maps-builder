/**
 * Maps Builder JS
 *
 * @description: Frontend form rendering
 */
var gmb_data;

(function ( $ ) {
	"use strict";
	var map;
	var places_service;
	var place;
	var search_markers = [];

	/*
	 * Global load function for other plugins / themes to use
	 *
	 * ex: google_maps_builder_load( object );
	 */
	window.google_maps_builder_load = function ( map_canvas ) {
		if ( !$( map_canvas ).hasClass( 'google-maps-builder' ) ) {
			return 'invalid Google Maps Builder';
		}
		initialize_map( map_canvas );
	};

	$( document ).ready( function () {

		var google_maps = $( '.google-maps-builder' );
		/*
		 * Loop through maps and initialize
		 */
		google_maps.each( function ( index, value ) {

			initialize_map( $( google_maps[index] ) );

		} );

		// fix for bootstrap tabs
		$( 'a[data-toggle="tab"]' ).on( 'shown.bs.tab', function ( e ) {
			var panel = $( e.target ).attr( 'href' );
			load_hidden_map( panel );
		} );
//Beaver Builder Tabs
		$( '.fl-tabs-label' ).on( 'click', function ( e ) {
			var panel = $( '.fl-tabs-panel-content.fl-tab-active' ).get( 0 );
			load_hidden_map( panel );
		} );
		//Tabby Tabs:
		$( '.responsive-tabs__list__item' ).on( 'click', function ( e ) {
			var panel = $( '.responsive-tabs__panel--active' ).get( 0 );
			load_hidden_map( panel );
		} );
	} );

	/**
	 * Map Init After the fact
	 *
	 * @description Good for tabs / ajax - pass in wrapper div class/id
	 * @since 2.0
	 */
	function load_hidden_map( parent ) {
		var google_hidden_maps = $( parent ).find( '.google-maps-builder' );
		if ( !google_hidden_maps.length ) {
			return;
		}
		google_hidden_maps.each( function ( index, value ) {
			//google.maps.event.trigger( map, 'resize' ); //TODO: Ideally we'd resize the map rather than reinitialize for faster performance, but that requires a bit of rewrite in how the plugin works
			initialize_map( $( google_hidden_maps[index] ) );
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
		//info_window - Contains the place's information and content
		var info_window = new google.maps.InfoWindow( {
			maxWidth: 315
		} );

		var map_id = $( map_canvas ).data( 'map-id' );
		var map_data = gmb_data[map_id];
		var latitude = (map_data.map_params.latitude) ? map_data.map_params.latitude : '32.713240';
		var longitude = (map_data.map_params.longitude) ? map_data.map_params.longitude : '-117.159443';
		var map_options = {
			center: new google.maps.LatLng( latitude, longitude ),
			zoom  : parseInt( map_data.map_params.zoom ),
			styles: [
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
		map = new google.maps.Map( map_canvas[0], map_options );
		places_service = new google.maps.places.PlacesService( map );

		set_map_options( map, map_data );
		set_map_theme( map, map_data );
		set_map_markers( map, map_data, info_window );

		//Display places?
		if ( map_data.places_api.show_places === 'yes' ) {
			perform_places_search( map, map_data, info_window );
		}


	} //end initialize_map


	/**
	 * Set Map Options
	 *
	 * Sets up map controls and theme
	 *
	 */
	function set_map_theme( map, map_data ) {

		var map_type = map_data.map_theme.map_type.toUpperCase();
		var map_theme = map_data.map_theme.map_theme_json;
		console.log( map_data.map_theme );

		//Custom (Snazzy) Theme
		if ( map_type === 'ROADMAP' && map_theme !== 'none' ) {

			map.setOptions( {
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				styles   : eval( map_theme )
			} );

		} else {
			//standard theme
			map.setOptions( {
				mapTypeId: google.maps.MapTypeId[map_type],
				styles   : false
			} );

		}


	}

	/**
	 * Set Map Options
	 *
	 * Sets up map controls and theme
	 *
	 */
	function set_map_options( map, map_data ) {

		//Zoom control
		var zoom_control = map_data.map_controls.zoom_control.toLowerCase();
		if ( zoom_control === 'none' ) {
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

		//Mouse Wheel Zoom
		var mouse_zoom = map_data.map_controls.wheel_zoom.toLowerCase();
		if ( mouse_zoom === 'none' ) {
			map.setOptions( {
				scrollwheel: false
			} );
		} else {
			map.setOptions( {
				scrollwheel: true
			} );
		}

		//Pan Control
		var pan = map_data.map_controls.pan_control.toLowerCase();
		if ( pan === 'none' ) {
			map.setOptions( {
				panControl: false
			} );
		} else {
			map.setOptions( {
				panControl: true
			} );
		}


		//Street View Control
		var street_view = map_data.map_controls.street_view.toLowerCase();
		if ( street_view === 'none' ) {
			map.setOptions( {
				streetViewControl: false
			} );
		} else {
			map.setOptions( {
				streetViewControl: true
			} );
		}

		//Map Double Click
		var double_click_zoom = map_data.map_controls.double_click_zoom.toLowerCase();
		if ( double_click_zoom === 'none' ) {
			map.setOptions( {
				disableDoubleClickZoom: true
			} );
		} else {
			map.setOptions( {
				disableDoubleClickZoom: false
			} );
		}

		//Map Draggable
		var draggable = map_data.map_controls.draggable.toLowerCase();
		if ( draggable === 'none' ) {
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
	 * Set Map Markers
	 */
	function set_map_markers( map, map_data, info_window ) {

		var map_markers = map_data.map_markers;

		//Loop through repeatable field of markers
		$( map_markers ).each( function ( index, marker_data ) {

			var marker_label = '';

			//check for custom marker and label data
			var marker_icon = map_data.map_params.default_marker; //Default marker icon here

			//Marker Image Icon
			if ( marker_data.marker_img ) {
				marker_icon = marker_data.marker_img;
			}
			//SVG Icon
			else if ( (typeof marker_data.marker !== 'undefined' && marker_data.marker.length > 0) && (typeof marker_data.label !== 'undefined' && marker_data.label.length > 0) ) {
				marker_icon = eval( "(" + marker_data.marker + ")" );
				marker_label = marker_data.label
			}


			//Marker for map
			var location_marker = new Marker( {
				map         : map,
				zIndex      : 9,
				icon        : marker_icon,
				custom_label: marker_label
			} );

			var marker_lat = marker_data.lat;
			var marker_lng = marker_data.lng;

			location_marker.setPosition( new google.maps.LatLng( marker_lat, marker_lng ) );
			location_marker.setVisible( true );


			google.maps.event.addListener( location_marker, 'click', function () {
				info_window.close();
				info_window.setContent( '<div id="infobubble-content" class="loading"></div>' );
				set_info_window_content( marker_data, info_window );
				info_window.open( map, location_marker );
			} );

		} ); //end $.each()


	}

	/**
	 * Set Infowindow Content
	 *
	 * @description: Queries to get Google Place Details information
	 *
	 * @param marker_data
	 * @param info_window
	 */
	function set_info_window_content( marker_data, info_window ) {

		var info_window_content;

		//place name
		if ( marker_data.title ) {
			info_window_content = '<p class="place-title">' + marker_data.title + '</p>';
		}

		if ( marker_data.description ) {
			info_window_content += '<div class="place-description">' + marker_data.description + '</div>';
		}

		//Does this marker have a place_id
		if ( marker_data.place_id && marker_data.hide_details !== 'on' ) {

			var request = {
				key    : gmb_data.api_key,
				placeId: marker_data.place_id
			};

			//Get details from Google on this place
			places_service.getDetails( request, function ( place, status ) {

				if ( status == google.maps.places.PlacesServiceStatus.OK ) {

					info_window_content += set_place_content_in_info_window( place );
					info_window_content = set_info_window_wrapper( info_window_content ); //wraps the content in div and returns
					info_window.setContent( info_window_content ); //set marker content

				}
			} );
		} else {
			info_window_content = set_info_window_wrapper( info_window_content ); //wraps the content in div and returns
			info_window.setContent( info_window_content ); //set marker content
		}


	}


	/**
	 * info_window Content for Place Details
	 *
	 * This marker contains more information about the place
	 *
	 * @param place
	 */
	function set_place_content_in_info_window( place ) {

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
	 * Google Places Nearby Search
	 */
	function perform_places_search( map, map_data, info_window ) {

		var map_center = map.getCenter();
		var types_array = map_data.places_api.search_places;

		//remove existing markers
		for ( var i = 0; i < search_markers.length; i++ ) {
			search_markers[i].setMap( null );
		}
		search_markers = [];

		//Check if any place types are selected
		if ( types_array.length > 0 ) {

			//perform search request
			var request = {
				key     : gmb_data.api_key,
				location: new google.maps.LatLng( map_center.lat(), map_center.lng() ),
				types   : types_array,
				radius  : map_data.places_api.search_radius
			};
			places_service.nearbySearch( request, function ( results, status, pagination ) {

				var i = 0;
				var result;

				//setup new markers
				if ( status == google.maps.places.PlacesServiceStatus.OK ) {

					//place new markers
					for ( i = 0; result = results[i]; i++ ) {
						create_search_result_marker( map, results[i], info_window );
					}

					//show all pages of results @see: http://stackoverflow.com/questions/11665684/more-than-20-results-by-pagination-with-google-places-api
					if ( pagination.hasNextPage ) {
						pagination.nextPage();
					}

				}

			} );
		}

	}


	/**
	 * Create Search Result Marker
	 *
	 * Used with Places Search to place markers on map
	 * @param map
	 * @param place
	 * @param info_window
	 */
	function create_search_result_marker( map, place, info_window ) {

		var search_marker = new google.maps.Marker( {
			map: map
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

			info_window.close();
			info_window.setContent( '<div id="infobubble-content" class="loading"></div>' );

			var marker_data = {
				title   : place.name,
				place_id: place.place_id
			};

			set_info_window_content( marker_data, info_window );

			info_window.open( map, search_marker );

		} );

		search_markers.push( search_marker )

	}


}( jQuery ));