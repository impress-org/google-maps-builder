/**
 * Google Maps in Magnific
 *
 * @since 2.0
 */
var gmb_data;
(function ( $ ) {

	"use strict";

	$( document ).ready( function () {

		var poststuff = $( 'form#post' ),
			postboxes = $( '.postbox' ).not( '.cmb-row, .cmb-repeatable-grouping' ),
			map = $( '#map' ),
			submit_btn = '<input type="submit" class="button button-primary button-large magnific-submit" id="gmb-publish" value="' + gmb_data.i18n.update_map + '" >',
			placeholder_id,
			placeholder_gid = 0,
			viewport = $( window ).height() - 40;

		$( '#map-builder' ).on( 'click', function ( e ) {
			e.preventDefault();
			magnific_builder();
		} );

		//Open by default?
		if ( gmb_data.modal_default === 'true' ) {
			magnific_builder();
		}


		function magnific_builder() {

			//Initialize Magnific Too
			$.magnificPopup.open( {

				callbacks: {

					beforeOpen: function () {

						lightbox_resize();

						//Add save button
						if ( $( '.magnific-submit' ).length === 0 ) {
							$( '.magnific-builder #postbox-container-1' ).append( submit_btn );
						}

						//Move metaboxes to sidebar and hide other none-GMB metaboxes in Magnific modal
						postboxes.each( function ( index, value ) {

							var postbox = $( this );
							var postbox_id = postbox.attr( 'id' );

							//Check that this is a GMB metabox
							if ( typeof postbox_id !== 'undefined' && postbox_id.match( /^\google_maps/ ) ) {

								//Move metaboxes to the sidebar
								var parent = postbox.parent();

								gmb_close_metaboxes( postbox );

								//Only move and close if not in sidebar & not the map preview
								if ( parent.attr( 'id' ) == 'normal-sortables' && postbox.attr( 'id' ) !== 'google_maps_preview_metabox' ) {
									placeholder_id = 'placeholder-' + placeholder_gid++;

									//Move em
									postbox.before( '<div class="placeholder ' + placeholder_id + '"></div>' ) // Save a DOM "bookmark"
										.appendTo( '#side-sortables' ) // Move the element to container
										.data( 'placeholder', placeholder_id ); // Store it's placeholder's info

								}


							} else {
								//hide non GMB metaboxes
								$( this ).addClass( 'mfp-hide' );
							}

							//Disable metabox dragging/sorting
							if ( typeof $.fn.sortable !== 'undefined' ) {
								$( '.meta-box-sortables' ).sortable( {
									disabled: true
								} );
							}

						} );


					},
					open      : function () {
						google.maps.event.trigger( window.map, 'resize' ); //Refresh Google Map
					},
					resize    : function () {
						if ( $.magnificPopup.instance.isOpen === true ) {
							lightbox_resize();
						}
					},
					close     : function () {
						postboxes.removeClass( 'mfp-hide' );
						poststuff.removeClass( 'mfp-hide' );
						poststuff.removeClass( 'magnific-builder' );
						$( '#postbox-container-1' ).outerHeight( '' );

						//reenable metabox dragging/sorting
						if ( typeof $.fn.sortable !== 'undefined' ) {
							$( '.meta-box-sortables' ).sortable( {
								disabled: false
							} );
						}

						//Move back metaboxes to original positions
						postboxes.each( function ( index, value ) {

							// Move back out of container
							$( this )
								.appendTo( '.placeholder.' + $( this ).data( 'placeholder' ) )  // Move it back to it's proper location
								.unwrap() // Remove the placeholder
								.data( 'placeholder', undefined );  // Unset placeholder data

						} );

						//Refresh Google Maps view
						google.maps.event.trigger( window.map, 'resize' ); //refresh map to get exact center
					}
				},//end callbacks
				items    : {
					src : poststuff,
					type: 'inline'
				},
				midClick : true
			} );

		}

		/**
		 * Close and toggle metaboxes
		 * @param postbox
		 */
		function gmb_close_metaboxes( postbox ) {
			if ( postbox.attr( 'id' ) === 'google_maps_preview_metabox' ) {
				//ensure Gmap metabox always is open
				postbox.removeClass( 'closed' );
			} else {
				//Close all other GMB metaboxes by default
				postbox.addClass( 'closed' );
			}
		}

		/**
		 * Window/Lightbox Resize
		 *
		 * @description: Resizes modal elements as the browser resizes & refreshes Google Maps
		 * @since 2.0
		 */
		function lightbox_resize() {
			poststuff.addClass( 'magnific-builder' ).height( viewport );
			$( '#map' ).height( viewport );
			$( '#postbox-container-1' ).outerHeight( viewport );
		}

		//Form Modal Submit button
		$( 'body' ).on( 'click', '.magnific-submit', function ( e ) {
			e.preventDefault();
			$( '#post_status' ).val( 'Publish' );
			jQuery( '#publish' ).click();
		} );

	} );
}( jQuery ));