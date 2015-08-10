<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Google_Maps_Builder
 * @author    Devin Walker <devin@wordimpress.com>
 * @license   GPL-2.0+
 * @link      http://wordimpress.com
 * @copyright 2015 WordImpress, Devin Walker
 */

?>

<div class="wrap">

	<?php global $current_user;
	$user_id = $current_user->ID;
	//	delete_user_meta($user_id, 'gmb_hide_welcome' ); //ONLY FOR TESTING
	// Check that the user hasn't already clicked to ignore the welcome message and that they have appropriate permissions
	if ( ! get_user_meta( $user_id, 'gmb_hide_welcome' ) && current_user_can( 'install_plugins' ) ) {
		?>
		<div class="container welcome-header">
			<div class="row">

				<div class="col-md-9">
					<h1 class="main-heading"><?php _e( 'Welcome to Maps Builder', $this->plugin_slug ); ?><?php echo Google_Maps_Builder()->meta['Version']; ?></h1>

					<p class="main-subheading"><?php _e( 'Thanks for using Maps Builder', $this->plugin_slug ); ?> <?php echo Google_Maps_Builder()->meta['Version']; ?>. <?php echo sprintf( __( 'To get started, read over the %1$sdocumentation%2$s, take a gander at the settings, and build yourself some maps! If you enjoy this plugin please consider telling a friend, rating it %3$s5-stars%2$s, or purchasing the %4$sPro%2$s edition.', $this->plugin_slug ), '<a href="https://wordimpress.com/documentation/maps-builder-pro/" target="_blank">', '</a>', '<a href="https://wordpress.org/support/view/plugin-reviews/google-maps-builder?filter=5#postform" target="_blank">', '<a href="https://wordimpress.com/plugins/maps-builder-pro?utm_source=MBF&amp;utm_medium=BANNER&amp;utm_content=SETTINGS&amp;utm_campaign=MBF%20Settings" target="_blank">' ); ?></p>
					<?php include( 'social-media.php' ); ?>

				</div>

				<div class="col-md-3">
					<div class="logo-svg">
						<?php include( 'mascot-svg.php' ); ?>
					</div>
				</div>
			</div>
		</div>

	<?php } ?>

	<div class="logo-svg logo-svg-small pull-right" <?php echo( ! get_user_meta( $user_id, 'gmb_hide_welcome' ) ?
		'style="display:none;"' : '' ); ?>>
		<div class="gmb-plugin-heading"><?php _e( 'Maps Builder - Free Edition', $this->plugin_slug ); ?></div>
		<?php include( 'logo-svg.php' ); ?>
		<a href="https://wordimpress.com/plugins/maps-builder-pro?utm_source=MBF&utm_medium=BANNER&utm_content=SETTINGS&utm_campaign=MBF%20Settings" target="_blank" class="button button-primary gmb-orange-btn gmb-settings-header-btn"><?php _e( 'Upgrade to Pro', $this->plugin_slug ); ?></a>
	</div>


	<?php
	/**
	 * Option tabs
	 *
	 * Better organize our options in tabs
	 *
	 * @see: http://code.tutsplus.com/tutorials/the-complete-guide-to-the-wordpress-settings-api-part-5-tabbed-navigation-for-your-settings-page--wp-24971
	 */
	$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'map_options';
	?>
	<h2 class="nav-tab-wrapper">
		<a href="?post_type=google_maps&page=<?php echo self::$key; ?>" class="nav-tab <?php echo $active_tab == 'map_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Map Options', $this->plugin_slug ); ?></a>
		<a href="?post_type=google_maps&page=<?php echo self::$key; ?>&tab=general_settings" class="nav-tab <?php echo $active_tab == 'general_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'General Options', $this->plugin_slug ); ?></a>
		<a href="?post_type=google_maps&page=<?php echo self::$key; ?>&tab=system_info" class="nav-tab <?php echo $active_tab == 'system_info' ? 'nav-tab-active' : ''; ?>"><?php _e( 'System Info', $this->plugin_slug ); ?></a>
	</h2>


	<?php
	/**
	 * Get the appropriate tab
	 */
	switch ( $active_tab ) {
		case 'map_options':
			include( 'tab-map-options.php' );
			break;
		case 'general_settings':
			include( 'tab-general-settings.php' );
			break;
		case 'system_info':
			include( 'tab-system-info.php' );
			break;
		default :
			include( 'tab-map-options.php' );
			break;
	}
	?>


</div>