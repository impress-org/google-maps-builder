<?php

/**
 * CMB Theme Options
 * @version 0.1.0
 */
class Google_Maps_Builder_Settings extends Google_Maps_Builder_Core_Settings {

	/**
	 * Option key, and option page slug
	 * @var string
	 */
	protected static $key = 'gmb_settings';


	/**
	 * Constructor
	 * @since 0.1.0
	 */
	public function __construct() {
		parent::__construct();
		$this->page_name = __( 'Google Maps Builder Settings', $this->plugin_slug );

		$this->plugin_slug = Google_Maps_Builder()->get_plugin_slug();
		add_action( 'cmb2_render_lat_lng_default', array( $this, 'cmb2_render_lat_lng_default' ), 10, 2 );

		//upsell markup
		add_action( 'gmb_settings_page_after_logo', array( $this, 'settings_upsell' ) );
		add_action( 'gmb_social_media_after_logo', array( $this, 'settings_social_media_upsell' ) );
	}


	/**
	 * Add links to Plugin listings view
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	function add_plugin_page_links( $links, $file ) {

		if ( $file == GMB_PLUGIN_BASE ) {

			// Add Widget Page link to our plugin
			$settings_link = '<a href="edit.php?post_type=google_maps&page=' . self::$key . '" title="' . __( 'Visit the Google Maps Builder plugin settings page', $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>';
			$go_pro_link   = '<a href="
https://wordimpress.com/plugins/maps-builder-pro?utm_source=MBF&utm_medium=BANNER&utm_content=LISTING&utm_campaign=MBF%20LISTING" title="' . __( 'Upgrade to Maps Builder Pro', $this->plugin_slug ) . '" target="_blank">' . __( 'Upgrade to Pro', $this->plugin_slug ) . '</a>';
			array_unshift( $links, $settings_link );
			array_push( $links, $go_pro_link );
		}

		return $links;
	}

	function add_plugin_meta_links( $meta, $file ) {

		if ( $file == GMB_PLUGIN_BASE ) {
			$meta[] = "<a href='http://wordpress.org/support/view/plugin-reviews/google-maps-builder' target='_blank' title='" . __( 'Rate Google Maps Builder on WordPress.org', $this->plugin_slug ) . "'>" . __( 'Rate Plugin', $this->plugin_slug ) . "</a>";
			$meta[] = '<a href="http://wordpress.org/support/plugin/google-maps-builder/" target="_blank" title="' . __( 'Get plugin support via the WordPress community', $this->plugin_slug ) . '">' . __( 'Support', $this->plugin_slug ) . '</a>';
			$meta[] = __( 'Thank you for using Maps Builder', $this->plugin_slug );
		}

		return $meta;
	}

	/**
	 * Add upsell in settings page
	 *
	 * @since 2.1.0
	 *
	 * @uses "gmb_settings_page_after_logo" action
	 */
	public function  settings_upsell(){ ?>
		<a href="https://wordimpress.com/plugins/maps-builder-pro?utm_source=MBF&utm_medium=BANNER&utm_content=SETTINGS&utm_campaign=MBF%20Settings" target="_blank" class="button button-primary gmb-orange-btn gmb-settings-header-btn">
			<?php _e( 'Upgrade to Pro', $this->plugin_slug ); ?>
		</a>
	<?php
	}

	/**
	 * Add upsell in social media section
	 *
	 * @since 2.1.0
	 *
	 * @uses "gmb_social_media_after_logo" action
	 */
	public function settings_social_media_upsell(){?>
		<div class="go-pro">
			<a href="https://wordimpress.com/plugins/maps-builder-pro?utm_source=MBF&amp;utm_medium=BANNER&amp;utm_content=SETTINGS&amp;utm_campaign=MBF%20Settings" target="_blank" class="button button-primary button-small gmb-orange-btn gmb-settings-header-btn">
				<?php esc_html_e( 'Upgrade to Pro', 'google-maps-builder' ); ?>
			</a>
		</div>
	<?php
	}

	/**
	 * Handle main data for the settings page
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	protected function settings_page_data(){
		//place holder
		$data = array(
			'welcome' => sprintf( '%1s Maps Builder %s', __( 'Welcome to', 'maps-builder-pro' ), Google_Maps_Builder()->meta['Version']  ),
			'sub_heading' => $this->sub_heading()
		);
		return $this->view_data( $data, true );
	}

	/**
	 * Sub heading markup for settings page
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	protected function sub_heading(){
		$out = __( 'Thanks for using Maps Builder', 'google-maps-pro' );
		$out .=  sprintf( __( 'To get started, read over the %1$sdocumentation%2$s, take a gander at the settings, and build yourself some maps! If you enjoy this plugin please consider telling a friend, rating it %3$s5-stars%2$s, or purchasing the %4$sPro%2$s edition.', $this->plugin_slug ), '<a href="https://wordimpress.com/documentation/maps-builder-pro/" target="_blank">', '</a>', '<a href="https://wordpress.org/support/view/plugin-reviews/google-maps-builder?filter=5#postform" target="_blank">', '<a href="https://wordimpress.com/plugins/maps-builder-pro?utm_source=MBF&amp;utm_medium=BANNER&amp;utm_content=SETTINGS&amp;utm_campaign=MBF%20Settings" target="_blank">' );

		return $out;

	}


}

