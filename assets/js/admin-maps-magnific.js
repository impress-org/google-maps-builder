/**
 * Google Maps in Magnific
 *
 * @since 2.0
 */
var gmb_data;

(function ( $ ) {

	"use strict";

	$( document ).ready( function () {

		var poststuff = $( '#poststuff' );

		//Initialize Magnific Too
		$.magnificPopup.open( {


			callbacks: {
				beforeOpen  : function () {
					poststuff.addClass( 'magnific-builder' )
				},
				elementParse: function ( item ) {
					// Function will fire for each target element
					// "item.el" is a target DOM element (if present)
					// "item.src" is a source that you may modify

					console.log( 'Parsing content. Item object that is being parsed:', item );
				},


				close: function () {
					poststuff.removeClass( 'mfp-hide' );
					poststuff.removeClass( 'magnific-builder' );
				}
			},

			items   : {
				src : poststuff,
				type: 'inline'
			},
			midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
		} );
	} );
}( jQuery ));