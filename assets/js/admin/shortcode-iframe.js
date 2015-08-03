/**
 *  Google Places Reviews JS: WP Admin Shortcode Generator
 *
 *  @description: JavaScripts for the shortcode generator iframe
 *  @since: 1.3
 */

(function ( $ ) {
	"use strict";

	var custom_params = '';
	var existing_shortcode = false;

	$( document ).ready( function () {

		//Cancel button (closes iframe modal)
		$( '#gmb_cancel' ).on( 'click', function ( e ) {
			top.tinymce.activeEditor.windowManager.close();
			e.preventDefault();
		} );

		custom_params = top.tinyMCE.activeEditor.windowManager.getParams();

		//Are there custom params?
		if ( custom_params.shortcode_params !== 'undefined' ) {
			existing_shortcode = true;
		}

		//Get things going for various functions
		init();

	} );

	// Init
	// @public
	function init() {

		gmb_generator_submit();

		//iframe sizing
		setTimeout( function () {
			$( 'body.iframe' ).css( {height: 'auto'} );
		}, 200 );


		//New or Existing Shortcode?
		if ( existing_shortcode === true ) {
			$( '.new-shortcode' ).hide(); //hide lookup fields (already set)
			$( '.gmb-edit-shortcode' ).show(); //show edit options
			$( '#gmb_submit' ).val( 'Edit Shortcode' ); //Change submit button text
			gmb_set_existing_params( custom_params ); //Set default options
		}


	}


	/**
	 * Set Existing Options
	 *
	 * @description Sets the generator options according to the user's already preset shortcode configuration
	 * @param custom_params obj
	 */
	function gmb_set_existing_params( custom_params ) {

		//Set variables from passed custom_params
		var id = gmb_get_attr( custom_params.shortcode_params, 'id' );

		//Set Place ID (very important)
		if ( id ) {
			$( '#gmb_maps' ).val( id );
		} else {
			alert( 'There was no Map ID found for this shortcode. Please create a new one.' );
			return false;
		}

	}


	/**
	 * Shortcode Generator On Submit
	 *
	 * @description: Outputs the shortcode in TinyMCE and does minor validation
	 */
	function gmb_generator_submit() {

		$( '#gmb_settings' ).on( 'submit', function ( e ) {
			e.preventDefault();

			//Set our variables
			var args = top.tinymce.activeEditor.windowManager.getParams(),
				map_id = $( '#gmb_maps' ).val(),
				shortcode;

			//Form the shortcode
			shortcode = '[' + args.shortcode;

			//Start with the ID
			if ( map_id ) {
				shortcode += ' id="' + map_id + '"';
			}

			shortcode += ']';

			top.tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, shortcode );
			top.tinymce.activeEditor.windowManager.close();

		} );


	}

	/**
	 * Get Attribute
	 *
	 * @description: Helper function that plucks options from passed string
	 */
	function gmb_get_attr( s, n ) {
		n = new RegExp( n + '=\"([^\"]+)\"', 'g' ).exec( s );
		return n ? window.decodeURIComponent( n[1] ) : '';
	}


})( jQuery );