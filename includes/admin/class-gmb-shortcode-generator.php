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

		$this->plugin_slug = Google_Maps_Builder()->get_plugin_slug();

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
		global $post, $pagenow;

		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}
		//Be sure to not allow on out post type
		if ( ! isset( $post->post_type ) || $post->post_type === 'google_maps' ) {
			return;
		}

		if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
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

		$plugin_array['gmb_shortcode_button'] = GMB_PLUGIN_URL . 'assets/js/admin/admin-shortcode.js';

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
		wp_register_script( 'gmb-shortcode-generator', GMB_PLUGIN_URL . 'assets/js/admin/shortcode-iframe' . $suffix . '.js', array( 'jquery' ) );
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

			.gmb-place-search-wrap > div.gmb-map-select {
				width: 65%;
				margin-right: 2%;
				float: left;
			}

			div.updated {
				width: 100%;
				float: left;
				box-sizing: border-box;
			}

			div.gmb-edit-shortcode {
				border-color: orange;
			}

			.shortcode-upsell {
				position: absolute;
				bottom: 10px;
				right: 10px;
				padding: 5px 10px !important;
				font-size: 13px !important;
			}

			.shortcode-upsell span.dashicons {
				font-size: 12px;
				height: 14px;
				position: relative;
				top: 3px;
				opacity: 0.8;
				width: 12px;
			}
		</style>
		<div class="wrap" id="gmb-wrap">
			<form id="gmb_settings" style="float: left; width: 100%;">
				<?php do_action( 'gmb_shortcode_iframe_before' ); ?>
				<fieldset id="gmb_location_lookup_fields" class="gmb-place-search-wrap clear" style="margin:1em 0;">
					<div class="gmb-map-select">
						<label for="gmb_location_lookup"><strong><?php _e( 'Choose a Map', 'google-maps-builder' ); ?></strong></label>
						<?php echo self::maps_dropdown(); ?>
					</div>
				</fieldset>

				<div class="updated new-shortcode">
					<p><?php _e( '<strong>Insert Shortcode</strong>: Select your desired map from the dropdown above then click create shortcode below.', 'google-maps-builder' ); ?></p>
				</div>

				<div class="updated gmb-edit-shortcode" style="display: none;">
					<p><?php _e( '<strong>Edit Active Shortcode:</strong> Customize the map for this shortcode by modifying the map selection above.', 'google-maps-builder' ); ?></p>
				</div>

				<?php do_action( 'gmb_shortcode_iframe_after' ); ?>

				<fieldset class="gmb-shortcode-submit">
					<input id="gmb_submit" type="submit" class="button-small button-primary" value="<?php _e( 'Create Shortcode', 'google-maps-builder' ); ?>" />
					<input id="gmb_cancel" type="button" class="button-small button-secondary" value="<?php _e( 'Cancel', 'google-maps-builder' ); ?>" />
				</fieldset>

			</form>
			<a href="https://wordimpress.com/plugins/maps-builder-pro?utm_source=MBF&utm_medium=BANNER&utm_content=SHORTCODE&utm_campaign=MBF%20Shortcode" class="button button-small shortcode-upsell" target="_blank"><?php _e( 'Go Pro', 'google-maps-builder' ); ?>
				<span class="dashicons dashicons-external"></span></a>
		</div>


		<?php iframe_footer();
		exit();
	}


	/**
	 * Renders an HTML Dropdown of all the Give Forms
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param array $args Arguments for the dropdown
	 *
	 * @return string $output Give forms dropdown
	 */
	public static function maps_dropdown( $args = array() ) {

		$defaults = array(
			'name'        => 'gmb-maps',
			'id'          => 'gmb-maps',
			'class'       => '',
			'multiple'    => false,
			'selected'    => 0,
			'chosen'      => false,
			'number'      => 30,
			'placeholder' => __( 'Select a Map', 'google-maps-builder' )
		);

		$args = wp_parse_args( $args, $defaults );

		$maps = get_posts( array(
			'post_type'      => 'google_maps',
			'orderby'        => 'title',
			'order'          => 'ASC',
			'posts_per_page' => $args['number']
		) );

		$options = array();

		if ( $maps ) {
			$options[0] = __( 'Select a Map', 'google-maps-builder' );
			foreach ( $maps as $map ) {
				$options[ absint( $map->ID ) ] = esc_html( $map->post_title );
			}
		} else {
			$options[0] = __( 'No Maps Found', 'google-maps-builder' );
		}

		// This ensures that any selected maps are included in the drop down
		if ( is_array( $args['selected'] ) ) {
			foreach ( $args['selected'] as $item ) {
				if ( ! in_array( $item, $options ) ) {
					$options[ $item ] = get_the_title( $item );
				}
			}
		} elseif ( is_numeric( $args['selected'] ) && $args['selected'] !== 0 ) {
			if ( ! in_array( $args['selected'], $options ) ) {
				$options[ $args['selected'] ] = get_the_title( $args['selected'] );
			}
		}

		$output = self::select( array(
			'name'             => $args['name'],
			'selected'         => $args['selected'],
			'id'               => $args['id'],
			'class'            => $args['class'],
			'options'          => $options,
			'chosen'           => $args['chosen'],
			'multiple'         => $args['multiple'],
			'placeholder'      => $args['placeholder'],
			'show_option_all'  => false,
			'show_option_none' => false
		) );

		return $output;
	}


	/**
	 * Renders an HTML Dropdown
	 *
	 * @since 2.0
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public static function select( $args = array() ) {

		$defaults = array(
			'options'          => array(),
			'name'             => null,
			'class'            => '',
			'id'               => '',
			'selected'         => 0,
			'chosen'           => false,
			'placeholder'      => null,
			'multiple'         => false,
			'show_option_all'  => _x( 'All', 'all dropdown items', 'google-maps-builder' ),
			'show_option_none' => _x( 'None', 'no dropdown items', 'google-maps-builder' )
		);

		$args = wp_parse_args( $args, $defaults );

		if ( $args['multiple'] ) {
			$multiple = ' MULTIPLE';
		} else {
			$multiple = '';
		}

		if ( $args['chosen'] ) {
			$args['class'] .= 'gmb-select-chosen';
		}

		if ( $args['placeholder'] ) {
			$placeholder = $args['placeholder'];
		} else {
			$placeholder = '';
		}

		$output = '<select name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( sanitize_key( str_replace( '-', '_', $args['id'] ) ) ) . '" class="gmb-select ' . esc_attr( $args['class'] ) . '"' . $multiple . ' data-placeholder="' . $placeholder . '">';

		if ( $args['show_option_all'] ) {
			if ( $args['multiple'] ) {
				$selected = selected( true, in_array( 0, $args['selected'] ), false );
			} else {
				$selected = selected( $args['selected'], 0, false );
			}
			$output .= '<option value="all"' . $selected . '>' . esc_html( $args['show_option_all'] ) . '</option>';
		}

		if ( ! empty( $args['options'] ) ) {

			if ( $args['show_option_none'] ) {
				if ( $args['multiple'] ) {
					$selected = selected( true, in_array( - 1, $args['selected'] ), false );
				} else {
					$selected = selected( $args['selected'], - 1, false );
				}
				$output .= '<option value="-1"' . $selected . '>' . esc_html( $args['show_option_none'] ) . '</option>';
			}

			foreach ( $args['options'] as $key => $option ) {

				if ( $args['multiple'] && is_array( $args['selected'] ) ) {
					$selected = selected( true, in_array( $key, $args['selected'] ), false );
				} else {
					$selected = selected( $args['selected'], $key, false );
				}

				$output .= '<option value="' . esc_attr( $key ) . '"' . $selected . '>' . esc_html( $option ) . '</option>';
			}
		}

		$output .= '</select>';

		return $output;
	}

}

new GMB_Shortcode_Generator();