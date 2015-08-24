/**
 * TinyMCE plugin
 *
 * @see: http://generatewp.com/take-shortcodes-ultimate-level/ (heavily referenced)
 */
(function () {

	tinymce.PluginManager.add( 'gmb_shortcode_button', function ( editor, url ) {

		var ed = tinymce.activeEditor;
		var sh_tag = 'google_maps';

		/**
		 * Open Shortcode Generator Modal
		 *
		 * @param ui
		 * @param v
		 */
		function gmb_open_modal( ui, v ) {

			editor.windowManager.open( {
				title     : ed.getLang( 'gmb.shortcode_generator_title' ),
				id        : 'gmb_shortcode_dialog',
				width     : 600,
				height    : 250,
				resizable : true,
				scrollbars: true,
				url       : ajaxurl + '?action=gmb_shortcode_iframe'
			}, {
				shortcode       : ed.getLang( 'gmb.shortcode_tag' ),
				shortcode_params: window.decodeURIComponent( v )
			} );
		}

		//add popup
		editor.addCommand( 'gmb_shortcode_popup', gmb_open_modal );

		editor.addButton( 'gmb_shortcode_button', {
			title  : ed.getLang( 'gmb.shortcode_generator_title' ),
			icon   : 'gmb dashicons-location-alt',
			onclick: gmb_open_modal
		} );

		//replace from shortcode to an image placeholder
		editor.on( 'BeforeSetcontent', function ( event ) {
			event.content = gmb_replace_shortcode( event.content );
		} );

		//replace from image placeholder to shortcode
		editor.on( 'GetContent', function ( event ) {
			event.content = gmb_restore_shortcode( event.content );
		} );


		//open popup on placeholder double click
		editor.on( 'DblClick', function ( e ) {
			var cls = e.target.className.indexOf( 'wp-google-maps-builder' );
			var attributes = e.target.attributes['data-gmb-attr'].value;

			if ( e.target.nodeName == 'IMG' && cls > -1 ) {
				editor.execCommand( 'gmb_shortcode_popup', false, attributes );
			}
		} );

		/**
		 * Helper functions
		 */
		function getAttr( s, n ) {
			n = new RegExp( n + '=\"([^\"]+)\"', 'g' ).exec( s );
			return n ? window.decodeURIComponent( n[1] ) : '';
		}

		/**
		 * Maps Replace Shortcode
		 *
		 * @param content
		 * @returns {XML|*|string|void}
		 */
		function gmb_replace_shortcode( content ) {
			return content.replace( /\[google_maps([^\]]*)\]/g, function ( all, attr, con ) {
				return gmb_shortcode_html( 'wp-google-maps-builder', attr, con );
			} );
		}

		/**
		 * Restore Shortcodes
		 */
		function gmb_restore_shortcode( content ) {
			return content.replace( /(?:<p(?: [^>]+)?>)*(<img [^>]+>)(<\/p>)*/g, function ( match, image ) {
				var data = getAttr( image, 'data-gmb-attr' );
				if ( data ) {
					return '<p>[' + sh_tag + data + ']</p>';
				}
				return match;
			} );
		}

		/**
		 * HTML
		 *
		 * @param cls string - Class name
		 * @param data
		 * @param con
		 * @returns {string}
		 */
		function gmb_shortcode_html( cls, data, con ) {

			var placeholder = url + '/maps-shortcode-placeholder.jpg';
			data = window.encodeURIComponent( data );

			return '<img src="' + placeholder + '" class="mceItem ' + cls + '" ' + 'data-gmb-attr="' + data + '" data-mce-resize="false" data-mce-placeholder="1" />';
		}

	} );


})();