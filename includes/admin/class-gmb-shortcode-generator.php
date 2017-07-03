<?php

/**
 * GMB_Shortcode_Generator class.
 *
 * @description: Adds a TinyMCE button that's clickable
 *
 * @since      2.0
 */
class GMB_Shortcode_Generator extends Google_Maps_Builder_Core_Shortcode_Generator {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'gmb_after_shortcode_form', array( $this, 'form_upsell') );
		add_action( 'gmb_shortcode_iframe_style', array( $this, 'upsell_css' ) );
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

	/**
	 * Add upsell markup to shortcode form
	 *
	 * @since 2.1.0
	 *
	 * @uses "gmb_after_shortcode_form"
	 */
	public function form_upsell(){?>
		<a href="https://wordimpress.com/plugins/maps-builder-pro?utm_source=MBF&utm_medium=BANNER&utm_content=SHORTCODE&utm_campaign=MBF%20Shortcode" class="button button-small shortcode-upsell" target="_blank">
			<?php _e( 'Go Pro', 'google-maps-builder' ); ?>
			<span class="dashicons dashicons-external"></span>
		</a>
	<?php

	}

	/**
	 * Add extra css for upsell
	 *
	 * @since 2.1.0
	 *
	 * @uses "gmb_shortcode_iframe_style" action
	 */
	public function upsell_css(){ ?>
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
	<?php

	}

}
