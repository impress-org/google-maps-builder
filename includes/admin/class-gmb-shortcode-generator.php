<?php

/**
 * GMB_Shortcode_Generator class.
 *
 * @description: Adds a TinyMCE button that's clickable
 *
 * @since      2.0
 */
class GMB_Shortcode_Generator {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_head', array( $this, 'add_shortcode_button' ), 20 );
		add_filter( 'tiny_mce_version', array( $this, 'refresh_mce' ), 20 );
		add_filter( 'mce_external_languages', array( $this, 'add_tinymce_lang' ), 20, 1 );

		// Tiny MCE button icon
		add_action( 'admin_head', array( $this, 'set_tinymce_button_icon' ) );

		add_action( 'wp_ajax_gmb_shortcode_iframe', array( $this, 'gmb_shortcode_iframe' ), 9 );
	}

	/**
	 * Add a button for the GPR shortcode to the WP editor.
	 */
	public function add_shortcode_button() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// check if WYSIWYG is enabled
		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_shortcode_tinymce_plugin' ), 10 );
			add_filter( 'mce_buttons', array( $this, 'register_shortcode_button' ), 10 );
		}
	}

	/**
	 * Add TinyMCE language function.
	 *
	 * @param array $arr
	 *
	 * @return array
	 */
	public function add_tinymce_lang( $arr ) {
		$arr['gmb_shortcode_button'] = GMB_PLUGIN_PATH . '/includes/admin/shortcode-generator-i18n.php';

		return $arr;
	}

	/**
	 * Register the shortcode button.
	 *
	 * @param array $buttons
	 *
	 * @return array
	 */
	public function register_shortcode_button( $buttons ) {

		array_push( $buttons, '|', 'gmb_shortcode_button' );

		return $buttons;
	}

	/**
	 * Add the shortcode button to TinyMCE
	 *
	 * @param array $plugin_array
	 *
	 * @return array
	 */
	public function add_shortcode_tinymce_plugin( $plugin_array ) {

		$plugin_array['gmb_shortcode_button'] = GMB_PLUGIN_URL . '/assets/js/admin-shortcode.js';

		return $plugin_array;
	}

	/**
	 * Force TinyMCE to refresh.
	 *
	 * @param int $ver
	 *
	 * @return int
	 */
	public function refresh_mce( $ver ) {
		$ver += 3;

		return $ver;
	}

	/**
	 * Adds admin styles for setting the tinymce button icon
	 */
	public static function set_tinymce_button_icon() {
		?>
		<style>
			i.mce-i-gmb {
				font: 400 20px/1 dashicons;

				padding: 0;
				vertical-align: top;
				speak: none;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
				margin-left: -2px;
				padding-right: 2px
			}

			#gmb_shortcode_dialog-body {
				background: #F1F1F1;
			}

			.gmb-shortcode-submit {
				margin: 0 -15px;
				position: fixed;
				bottom: 0;
				background: #FFF;
				width: 100%;
				padding: 15px;
				border-top: 1px solid #DDD;
			}

			div.place-id-set {
				clear: both;
				float: left;
				width: 100%;
			}

		</style>
		<?php
	}

	/**
	 * Display the contents of the iframe used when the GPR Shortcode Generator is clicked
	 * TinyMCE button is clicked.
	 *
	 * @param int $ver
	 *
	 * @return int
	 */
	public static function gmb_shortcode_iframe() {
		set_current_screen( 'google-maps-builder' );
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		//Shortcode Generator Specific JS
		wp_register_script( 'gmb-shortcode-generator', GMB_PLUGIN_URL . '/assets/js/shortcode-iframe' . $suffix . '.js', array( 'jquery' ) );
		wp_enqueue_script( 'gmb-shortcode-generator' );


		iframe_header(); ?>

		<style>
			#gmb-wrap {
				margin: 0 1em;
				overflow: hidden;
				padding-bottom: 75px;
			}

			/* iFrame Styles */
			#gmb_settings label {
				margin-bottom: 3px;
				display: block;
			}

			div.gmb-shortcode-hidden-fields-wrap {
				display: none;
			}

			.gmb-place-search-wrap > div.gmb-autocomplete {
				width: 65%;
				margin-right: 2%;
				float: left;
			}

			.gmb-place-search-wrap > div.gmb-place-type > select {
				height: 36px;
				line-height: 36px;
			}

			div.updated {
				width: 100%;
				float: left;
				box-sizing: border-box;
			}

			div.place-id-not-set {
				border-color: orange;
			}
		</style>
		<div class="wrap" id="gmb-wrap">
			<form id="gmb_settings" style="float: left; width: 100%;">
				<?php do_action( 'gmb_shortcode_iframe_before' ); ?>
				<fieldset id="gmb_location_lookup_fields" class="gmb-place-search-wrap clear" style="margin:1em 0;">
					<div class="gmb-autocomplete">
						<label for="gmb_location_lookup"><strong><?php _e( 'Location Lookup:', 'google-maps-builder' ); ?></strong></label>
						<input type="text" id="gmb_location_lookup" name="gmb_location_lookup" class="widefat gmb-autocomplete" />
					</div>

					<div class="updated place-id-not-set">
						<p><?php _e( '<strong>Create a Shortcode</strong>: Start creating a Google Places Review shortcode by looking up your business or location using the lookup field above.', 'google-maps-builder' ); ?></p>
					</div>
					<div class="updated place-id-set" style="display: none;">
						<p><?php esc_attr_e( 'The Google Place ID is set for this location.', 'google-maps-builder' ); ?></p>
					</div>

				</fieldset>


				<div class="updated gmb-edit-shortcode" style="display: none;">
					<p><?php _e( '<strong>Edit Active Shortcode:</strong> Customize the options for this Google Places Reviews shortcode by adjusting the options below.', 'google-maps-builder' ); ?></p>
				</div>

				<a href="#" class="gmb-toggle-shortcode-fields" style="display: none;box-shadow: none;margin: 0 0 20px;">&raquo; <?php _e( '<strong>View Additional Shortcode Options</strong> (all optional)', 'google-maps-builder' ); ?>
				</a>

				<?php do_action( 'gmb_shortcode_iframe_after' ); ?>

				<fieldset class="gmb-shortcode-submit">
					<input id="gmb_submit" type="submit" class="button-small button-primary" value="<?php _e( 'Create Shortcode', 'google-maps-builder' ); ?>" />
					<input id="gmb_cancel" type="button" class="button-small button-secondary" value="<?php _e( 'Cancel', 'google-maps-builder' ); ?>" />
				</fieldset>

			</form>
		</div>


		<?php iframe_footer();
		exit();
	}
}

new GMB_Shortcode_Generator();