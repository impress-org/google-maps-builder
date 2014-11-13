(function ($) {
	"use strict";

	$(function () {

		gmb_widget_toggles();
		gmb_widget_tooltips();

		//Initialize Gmap when user clicks an option
		place_details_on_click();
		place_search_on_click();


	});

	/*
	 * Function to Refresh jQuery toggles for wpgp Widget Pro upon saving specific widget
	 */
	$(document).ajaxSuccess(function (e, xhr, settings) {

		gmb_widget_toggles();
		gmb_widget_tooltips();
		refresh_google_map();
		place_details_on_click();
		place_search_on_click();


	});
	$(document).ajaxStop(function (e, xhr, settings) {

	});

	/**
	 * Place Details Radio
	 *
	 * This function handles displaying the Google Map and initializing gmaps
	 */
	function place_details_on_click() {
		//set up the click event
		$('.place-details-api-option-wrap').on('click', function () {
			var map_wrap = $(this).parentsUntil('form').find('.google-autocomplete-map-wrap');
			var map_canvas = $(map_wrap).find('.map-canvas');

			//slide down the autocomplete map
			$(map_wrap).slideDown('normal', function () {
				//check if map initialized already by checking for children in canvas
				if (map_canvas.children().length == 0) {
					//no map so initialize
					place_details_autocomplete_initialize(map_canvas);
				}

			}); //slide down
		}); //click
	}

	/**
	 * Place Search Radio
	 *
	 * This function handles displaying the Google Map and initializing gmaps
	 */
	function place_search_on_click() {
		$('.place-search-api-option-wrap').on('click', function () {
			//Slide up Autocomplete Map
			$('.google-autocomplete-map-wrap').slideUp('normal');
		});
	}


	/**
	 * Refresh Gmap
	 */
	function refresh_google_map() {

		//check if Place Details Option enabled and/or map is displayed

			//initialize map
		var google_widget = jQuery('.widget-inside:visible').has('.google-autocomplete-map-wrap');


//		console.log(google_widget);
//
//		var map_canvas = google_widget.find('.map-canvas');
//		place_details_autocomplete_initialize(map_canvas);

//		$('.google-autocomplete-map-wrap').slideDown('normal', function () {
//			var map_canvas = $(this).parentsUntil('form').find('.map-canvas');
//			place_details_autocomplete_initialize(map_canvas);
//		});

	}


	/**
	 * Widget Autocomplete Map
	 *
	 * Created the map with autocomplete searching used within the widget admin UI
	 *
	 * @see: https://developers.google.com/maps/documentation/javascript/examples/places-autocomplete
	 */
	function place_details_autocomplete_initialize(map_canvas) {

		var latitude = '32.713240';
		var longitude = '-117.159443';
		var referenceField = $(map_canvas[0]).parent().find('.place-detail-reference').val();

		var mapOptions = {
			center            : new google.maps.LatLng(latitude, longitude),
			zoom              : 13,
			zoomControl       : true,
			zoomControlOptions: {
				style   : google.maps.ZoomControlStyle.SMALL,
				position: google.maps.ControlPosition.LEFT_BOTTOM
			},
			mapTypeControl    : false,
			streetViewControl : false
		};

		var map = new google.maps.Map(map_canvas[0], mapOptions);


		//Check to see if this widget already has been setup
		//@see: https://developers.google.com/maps/documentation/javascript/examples/place-details
		if (referenceField) {
			var service = new google.maps.places.PlacesService(map);
			var request = {
				reference: referenceField
			};

			service.getDetails(request, function (place, status) {
				if (status == google.maps.places.PlacesServiceStatus.OK) {

					set_marker_open_infobubble(map, place);

				}
			});

		}

		var input = /** @type {HTMLInputElement} */(
				$(map_canvas[0]).parent().find('.pac-input')[0]);

		var types = $(map_canvas[0]).parent().find('.type-selector')[0];
		map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
		map.controls[google.maps.ControlPosition.TOP_CENTER].push(types);

		var autocomplete = new google.maps.places.Autocomplete(input);
		autocomplete.bindTo('bounds', map);


		//Autocomplete Place Change
		google.maps.event.addListener(autocomplete, 'place_changed', function () {

			var place = autocomplete.getPlace();
			if (!place.geometry) {
				return;
			}

			//set reference hidden input value
			$(map_canvas[0]).parent().find('.place-detail-reference').val(place.reference);

			// If the place has a geometry, then present it on a map.
			set_marker_open_infobubble(map, place);


		});

		// Sets a listener on a radio button to change the filter type on Places
		// Autocomplete.
		function setupClickListener(id, types) {
			var radioButton = document.getElementById(id);
			google.maps.event.addDomListener(radioButton, 'click', function () {
				autocomplete.setTypes(types);
			});
		}

		setupClickListener('changetype-all', []);
		setupClickListener('changetype-establishment', ['establishment']);
		setupClickListener('changetype-geocode', ['geocode']);

	}


	function set_marker_open_infobubble(map, place) {

		//Marker for map
		var marker = new google.maps.Marker({
			map: map
		});
		marker.setVisible(false);

		//Custom InfoBubble
		var infoBubble = new InfoBubble({
			maxWidth: 300
		});
		//Update map with saved place detail
		// If the place has a geometry, then present it on a map.
		if (place.geometry.viewport) {
			map.fitBounds(place.geometry.viewport);
		} else {
			map.setCenter(place.geometry.location);
			map.setZoom(17);  // Why 17? Because it looks good.
		}
		//setup marker icon
		marker.setIcon(/** @type {google.maps.Icon} */({
			url       : place.icon,
			size      : new google.maps.Size(71, 71),
			origin    : new google.maps.Point(0, 0),
			anchor    : new google.maps.Point(17, 34),
			scaledSize: new google.maps.Size(35, 35)
		}));
		marker.setPosition(place.geometry.location);
		marker.setVisible(true);

		var address = '';
		if (place.address_components) {
			address = [
				(place.address_components[0] && place.address_components[0].short_name || ''),
				(place.address_components[1] && place.address_components[1].short_name || ''),
				(place.address_components[2] && place.address_components[2].short_name || '')
			].join(' ');
		}

		infoBubble.setContent('<div><strong>' + place.name + '</strong><br>' + address);
		infoBubble.open(map, marker);

	}


	/**
	 * Toggles Widget Panels
	 */
	function gmb_widget_toggles() {

		//API Method Toggle
		$('#widgets-right .widget-api-option .wpgp-method-span:not("clickable")').each(function () {

			$(this).addClass("clickable").unbind("click").click(function () {
				$(this).parent().parent().find('.toggled').slideUp().removeClass('toggled');
				$(this).find('input').attr('checked', 'checked');
				if ($(this).hasClass('search-api-option-wrap')) {
					$(this).parent().next('.toggle-api-option-1').slideToggle().toggleClass('toggled');
				} else {
					$(this).parent().next().next('.toggle-api-option-2').slideToggle().toggleClass('toggled');
				}
			});
		});


	}


	/**
	 * Tooltips
	 */
	function gmb_widget_tooltips() {
		//Tooltips for admins
		$('.tooltip-info').tipsy({
			fade    : true,
			html    : true,
			gravity : 's',
			delayOut: 1000,
			delayIn : 500
		});
	}


}(jQuery));
