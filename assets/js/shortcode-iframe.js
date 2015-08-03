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

		//Toggle fields
		$( '.gmb-toggle-shortcode-fields' ).on( 'click', function () {
			$( '.gmb-shortcode-hidden-fields-wrap' ).slideToggle();
		} );

		//New or Existing Shortcode?
		if ( existing_shortcode === true ) {
			$( '.gmb-edit-shortcode' ).show(); //show edit options
			$( '.gmb-shortcode-hidden-fields-wrap' ).show(); //show table of options
			$( '#gmb_location_lookup_fields' ).hide(); //hide lookup fields (already set)
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
		var id = gmb_get_attr( custom_params.shortcode_params, 'id' ),
			title = gmb_get_attr( custom_params.shortcode_params, 'title' ),
			theme = gmb_get_attr( custom_params.shortcode_params, 'widget_style' ),
			alignment = gmb_get_attr( custom_params.shortcode_params, 'align' ),
			max_width = gmb_get_attr( custom_params.shortcode_params, 'max_width' ),
			review_limit = gmb_get_attr( custom_params.shortcode_params, 'review_limit' ),
			cache = gmb_get_attr( custom_params.shortcode_params, 'cache' ),
			rating_filter = gmb_get_attr( custom_params.shortcode_params, 'review_filter' ),
			review_char_limit = gmb_get_attr( custom_params.shortcode_params, 'review_char_limit' ),
			pre_content = gmb_get_attr( custom_params.shortcode_params, 'pre_content' ),
			post_content = gmb_get_attr( custom_params.shortcode_params, 'post_content' ),
			hide_header = gmb_get_attr( custom_params.shortcode_params, 'hide_header' ),
			hide_google_image = gmb_get_attr( custom_params.shortcode_params, 'hide_google_image' ),
			hide_out_of_rating = gmb_get_attr( custom_params.shortcode_params, 'hide_out_of_rating' ),
			target_blank = gmb_get_attr( custom_params.shortcode_params, 'target_blank' ),
			no_follow = gmb_get_attr( custom_params.shortcode_params, 'no_follow' );

		//Set Place ID (very important)
		if ( id ) {
			$( '#gmb_widget_place_id' ).val( id );
		} else {
			alert( 'There was no Place ID found for this shortcode. Please create a new one.' );
			return false;
		}

		//Change default settings to customized ones using the values of the variables set above
		if ( title ) {
			$( '#gmb_widget_title' ).val( title );
		}
		if ( theme ) {
			$( '#gmb_widget_theme' ).val( theme );
		}
		if ( alignment ) {
			$( '#gmb_widget_alignment' ).val( alignment );
		}
		if ( max_width ) {
			$( '#gmb_widget_maxwidth' ).val( max_width );
		}
		if ( rating_filter ) {
			$( '#gmb_widget_review_filter' ).val( rating_filter );
		}
		if ( review_char_limit ) {
			$( '#gmb_widget_review_char_limit' ).val( review_char_limit );
		}
		if ( pre_content ) {
			$( '#gmb_widget_pre_content' ).val( pre_content );
		}
		if ( post_content ) {
			$( '#gmb_widget_post_content' ).val( post_content );
		}
		if ( cache ) {
			$( '#gmb_widget_cache' ).val( cache );
		}
		if ( hide_header == 'true' ) {
			$( '#gmb_widget_hide_header' ).prop( 'checked', true );
		}
		if ( hide_google_image == 'true' ) {
			$( '#gmb_widget_hide_google_image' ).prop( 'checked', true );
		}
		if ( hide_out_of_rating == 'true' ) {
			$( '#gmb_widget_hide_out_of_rating' ).prop( 'checked', true );
		}
		if ( no_follow == 'false' ) {
			$( '#gmb_widget_no_follow' ).prop( 'checked', false );
		}
		if ( target_blank == 'false' ) {
			$( '#gmb_widget_target_blank' ).prop( 'checked', false );
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
				place_id = $( '[name="gmb_widget_place_id"]' ).val(),
				title = $( '[name="gmb_widget_title"]' ).val(),
				widget_theme = $( '[name="gmb_widget_theme"]' ).val(),
				align = $( '[name="gmb_widget_alignment"]' ).val(),
				max_width = $( '[name="gmb_widget_maxwidth"]' ).val(),
				review_limit = $( '[name="gmb_widget_review_limit"]' ).val(),
				review_char_limit = $( '[name="gmb_widget_review_char_limit"]' ).val(),
				review_filter = $( '[name="gmb_widget_review_filter"]' ).val(),
				pre_content = $( '[name="gmb_widget_pre_content"]' ).val(),
				post_content = $( '[name="gmb_widget_post_content"]' ).val(),
				hide_header = $( '[name="gmb_widget_hide_header"]' ).is( ':checked' ),
				hide_google_image = $( '[name="gmb_widget_hide_google_image"]' ).is( ':checked' ),
				hide_out_of_rating = $( '[name="gmb_widget_hide_out_of_rating"]' ).is( ':checked' ),
				no_follow = $( '[name="gmb_widget_no_follow"]' ).is( ':checked' ),
				target_blank = $( '[name="gmb_widget_target_blank"]' ).is( ':checked' ),
				cache = $( '[name="gmb_widget_cache"]' ).val(),
				shortcode;

			//Let's do some validation to ensure the location's place ID is set
			if ( place_id === '' ) {
				alert( 'Missing Location\'s Place ID. Did you lookup a location?' );
				return false;
			}

			//Form the shortcode
			shortcode = '[' + args.shortcode;

			//Start with the ID
			if ( place_id ) {
				shortcode += ' id="' + place_id + '"';
			}

			//Title
			if ( title ) {
				shortcode += ' title="' + title + '"';
			}

			//Widget Style
			if ( widget_theme !== 'Minimal Light' ) {
				shortcode += ' widget_style="' + widget_theme + '"';
			}

			//align
			if ( align !== 'none' ) {
				shortcode += ' align="' + align + '"';
			}

			//max-width
			if ( max_width !== '' && max_width !== '250px' ) {
				shortcode += ' max_width="' + max_width + '"';
			}

			//pre_content
			if ( pre_content !== '' ) {
				shortcode += ' pre_content="' + pre_content + '"';
			}

			//review_limit
			if ( review_limit !== '5' ) {
				shortcode += ' review_limit="' + review_limit + '"';
			}

			//review_char_limit
			if ( review_char_limit !== '' && review_char_limit !== '250' ) {
				shortcode += ' review_char_limit="' + review_char_limit + '"';
			}

			//review_filter
			if ( review_filter !== 'none' ) {
				shortcode += ' review_filter="' + review_filter + '"';
			}
			//pre_content
			if ( pre_content !== '' ) {
				shortcode += ' pre_content="' + pre_content + '"';
			}

			//post_content
			if ( post_content !== '' ) {
				shortcode += ' post_content="' + post_content + '"';
			}

			//cache
			if ( cache !== '' && cache !== '2 Days' ) {
				shortcode += ' cache="' + cache + '"';
			}

			//hide_header
			if ( hide_header == true ) {
				shortcode += ' hide_header="true"';
			}

			//hide_google_image
			if ( hide_google_image == true ) {
				shortcode += ' hide_google_image="true"';
			}

			//hide_out_of_rating
			if ( hide_out_of_rating == true ) {
				shortcode += ' hide_out_of_rating="true"';
			}

			//no_follow
			if ( no_follow !== true ) {
				shortcode += ' no_follow="false"';
			}

			//target_blank
			if ( target_blank !== true ) {
				shortcode += ' target_blank="false"';
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