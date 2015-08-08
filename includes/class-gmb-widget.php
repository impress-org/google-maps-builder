<?php
/**
 * Give Form Widget
 *
 * @package     WordImpress
 * @subpackage  Admin/Forms
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Give Forms Widget.
 *
 * @since 1.0
 * @return void
 */
function init_gmb_forms_widget() {
	register_widget( 'Google_Maps_Builder_Widget' );
}

add_action( 'widgets_init', 'init_gmb_forms_widget' );

/**
 *  Google Places Reviews
 *
 * @description: The Google Places Reviews
 * @since      : 2.0
 */
class Google_Maps_Builder_Widget extends WP_Widget {

	/**
	 * Array of Private Options
	 *
	 * @since    2.0
	 *
	 * @var array
	 */
	public $widget_defaults = array(
		'title' => '',
		'id'    => '',
	);


	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {

		$this->plugin_slug = Google_Maps_Builder()->get_plugin_slug();

		parent::__construct(
			'gmb_maps_widget', // Base ID
			__( 'Maps Builder Widget', $this->plugin_slug ), // Name
			array(
				'classname'   => 'gmb-maps-widget',
				'description' => __( 'Display a Google Map in your theme\'s widget powered sidebar.', $this->plugin_slug )
			) //Args
		);

		//Actions
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_widget_scripts' ) );


	}

	//Load Widget JS Script ONLY on Widget page
	public function admin_widget_scripts( $hook ) {

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		//Widget Script
		if ( $hook == 'widgets.php' ) {

			wp_register_style( $this->plugin_slug . '-admin-styles', GMB_PLUGIN_URL . 'assets/css/gmb-admin' . $suffix . '.css', array(), GMB_VERSION );
			wp_enqueue_style( $this->plugin_slug . '-admin-styles' );

			wp_register_script( 'gmb-qtip', GMB_PLUGIN_URL . 'assets/js/plugins/jquery.qtip' . $suffix . '.js', array( 'jquery' ), GMB_VERSION );
			wp_enqueue_script( 'gmb-qtip' );

			wp_register_script( 'gmb-admin-widgets-scripts', GMB_PLUGIN_URL . 'assets/js/admin/admin-widget' . $suffix . '.js', array( 'jquery' ), GMB_VERSION, false );
			wp_enqueue_script( 'gmb-admin-widgets-scripts' );
		}


	}


	/**
	 * Back-end widget form.
	 *
	 * @param array $instance
	 *
	 * @return null
	 * @see WP_Widget::form()
	 */
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->widget_defaults ); ?>

		<!-- Title -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title', 'gpr' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>


		<?php
		//Query Give Forms
		$args      = array(
			'post_type'      => 'google_maps',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
		);
		$gmb_forms = get_posts( $args );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>"><?php _e( 'Select a Map:', $this->plugin_slug ); ?>
				<span class="dashicons gmb-tooltip-icon" data-tooltip="<?php _e( 'Select a map that you would like to embed in this widget area.', $this->plugin_slug ); ?>"></span>
			</label>
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'id' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>">
				<option value="current"><?php _e( 'Please select...', $this->plugin_slug ); ?></option>
				<?php foreach ( $gmb_forms as $gmb_form ) { ?>
					<option <?php selected( absint( $instance['id'] ), $gmb_form->ID ); ?> value="<?php echo esc_attr( $gmb_form->ID ); ?>"><?php echo $gmb_form->post_title; ?></option>
				<?php } ?>
			</select>
		</p>
		<!-- Give Form Field -->

		<div class="gmb-widget-upgrade clear">
			<span class="powered-by"></span>
			<a href="https://wordimpress.com/plugins/maps-builder-pro?utm_source=MBF&utm_medium=BANNER&utm_content=WIDGET&utm_campaign=MBF%20Widgets" target="_blank" class="button button-small"><?php _e( 'Upgrade to Pro', $this->plugin_slug ); ?>
				<span class="new-window"></span></a>
		</div>

		<?php
	} //end form function


	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];

		do_action( 'gmb_before_forms_widget' );

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		$atts = array(
			'id' => $instance['id'],
		);

		//Ensure a map has been set
		if ( $instance['id'] !== 'current' ) {
			echo Google_Maps_Builder()->engine->google_maps_shortcode( $atts );
		}


		echo $args['after_widget'];

		do_action( 'gmb_after_forms_widget' );

	}


	/**
	 * Updates the widget options via foreach loop
	 *
	 * @DESC: Saves the widget options
	 * @SEE WP_Widget::update
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		//loop through options array and save to new instance
		foreach ( $this->widget_defaults as $field => $value ) {
			$instance[ $field ] = strip_tags( stripslashes( $new_instance[ $field ] ) );
		}

		return $instance;

	}


}