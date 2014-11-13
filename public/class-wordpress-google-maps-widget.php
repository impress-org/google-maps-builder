<?php
/**
 * Google Maps Widget
 *
 * The widget class for WordPress Google Places
 *
 * @package   Google_Maps_Builder
 * @author    Devin Walker <devin@wordimpress.com>
 * @license   GPL-2.0+
 * @link      http://wordimpress.com
 * @copyright 2014 WordImpress, Devin Walker
 */

class Google_Maps_Builder_Widget extends WP_Widget {


	/**
	 * Array of Private Options
	 *
	 * @since    1.0.0
	 *
	 * @var array
	 */
	private $options = array(
		'title'                  => '',
		'api_option'             => '',
		'place_detail_search'    => '',
		'place_detail_reference' => ''
	);

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin            = Google_Maps_Builder::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		parent::__construct(
			'widget-google-places',
			__( 'WordPress Google Places', $this->plugin_slug ),
			array(
				'classname'   => 'widget-google-places',
				'description' => __( 'Add Google business reviews, ratings and maps and more.', $this->plugin_slug )
			)
		);

		// Register admin styles and scripts
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// Register site styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );

		// Refreshing the widget's cached output with each new post
		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

	} // end constructor


	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array $args     The array of form elements
	 * @param array $instance The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		// Check if there is a cached output
		$cache = wp_cache_get( 'widget-google-places', 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset ( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset ( $cache[$args['widget_id']] ) ) {
			return print $cache[$args['widget_id']];
		}

		// go on with your widget logic, put everything into a string and …
		extract( $args, EXTR_SKIP );

		$title                  = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
		$api_option             = empty( $instance['api_option'] ) ? '' : apply_filters( 'api_option', $instance['api_option'] );
		$place_detail_search    = empty( $instance['place_detail_search'] ) ? '' : apply_filters( 'place_detail_search', $instance['place_detail_search'] );
		$place_detail_reference = empty( $instance['place_detail_reference'] ) ? '' : apply_filters( 'place_detail_search', $instance['place_detail_reference'] );

		$widget_string = $before_widget;

		ob_start();
		include( plugin_dir_path( __FILE__ ) . 'views/widget.php' );
		$widget_string .= ob_get_clean();
		$widget_string .= $after_widget;


		$cache[$args['widget_id']] = $widget_string;

		wp_cache_set( 'widget-google-places', $cache, 'widget' );

		print $widget_string;

	} // end widget


	public function flush_widget_cache() {
		wp_cache_delete( 'widget-google-places', 'widget' );
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array new_instance The new instance of values to be generated via the update.
	 * @param array old_instance The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		//loop through options array and save to new instance
		foreach ( $this->options as $option => $value ) {
			$instance[$option] = strip_tags( stripslashes( $new_instance[$option] ) );
		}

		return $instance;

	} // end widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		//loop through options array and save options to new instance
		foreach ( $this->options as $option => $value ) {
			${$option} = ! isset( $instance[$option] ) ? '' : esc_attr( $instance[$option] );
		}

		// Display the admin form
		include( plugin_dir_path( __FILE__ ) . '../admin/views/admin-widget.php' );

	} // end form


	/*--------------------------------------------------*/
	/* Public Functions
	/*--------------------------------------------------*/
	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles( $hook ) {

		if ( $hook == 'widgets.php' ) {
			wp_enqueue_style( 'wordpress-google-places-admin-styles', plugins_url( '../admin/assets/css/admin-widget.css', __FILE__ ) );
		}


	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
	public function register_admin_scripts( $hook ) {
		if ( $hook == 'widgets.php' ) {

			wp_enqueue_script( 'wordpress-google-places-gmaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places', array( 'jquery' ) );
			wp_enqueue_script( 'wordpress-google-places-admin-script', plugins_url( '../admin/assets/js/admin-widget.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_script( 'wordpress-google-places-tipsy', plugins_url( '../admin/assets/js/tipsy.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_script( 'wordpress-google-places-infobubble', plugins_url( '../public/assets/js/infobubble-compiled.js', __FILE__ ), array( 'jquery' ) );


		}

	} // end register_admin_scripts

	/**
	 * Registers and enqueues widget-specific styles.
	 */
	public function register_widget_styles() {

		//	wp_enqueue_style( 'wordpress-google-places-widget-styles', plugins_url( '/css/widget.css' ) );

	} // end register_widget_styles

	/**
	 * Registers and enqueues widget-specific scripts.
	 */
	public function register_widget_scripts() {

		//	wp_enqueue_script( 'wordpress-google-places-script', plugins_url( '/js/widget.js' ), array( 'jquery' ) );


	} // end register_widget_scripts


} // end class

//initialize Google_Maps_Builder
add_action( 'widgets_init', create_function( '', 'register_widget("Google_Maps_Builder_Widget");' ) );