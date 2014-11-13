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
 * @copyright 2014 WordImpress, Devin Walker
 */

?>

<div class="wrap">

	<?php global $current_user;
	$user_id = $current_user->ID;
	// Check that the user hasn't already clicked to ignore the welcome message and that they have appropriate permissions
	if ( ! get_user_meta( $user_id, 'gmb_hide_welcome' ) && current_user_can( 'install_plugins' ) ) {
		?>
		<div class="container welcome-header">
			<div class="row">

				<div class="col-md-9">
					<h1 class="main-heading"><?php _e( 'Welcome to Google Maps Builder', $this->plugin_slug ); ?> <?php echo $this->meta['Version']; ?></h1>

					<p class="main-subheading"><?php _e( 'Thanks for using Google Maps Builder', $this->plugin_slug ); ?> <?php echo $this->meta['Version']; ?>. <?php _e( 'To get started, read over the documentation, take a gander at the settings, and build yourself some maps! If you enjoy this plugin please consider telling a friend, following us or purchasing the Pro edition (coming soon!).', $this->plugin_slug ); ?></p>
					<?php include( 'social-media.php' ); ?>

				</div>

				<div class="col-md-3">
					<div class="logo-svg">
						<?php include( 'logo-svg.php' ); ?>
					</div>
				</div>
			</div>
		</div>

	<?php } ?>

	<div class="logo-svg logo-svg-small pull-left" <?php echo (!get_user_meta( $user_id, 'gmb_hide_welcome') ?
	'style="display:none;"' : ''); ?>>
		<div class="gmb-plugin-heading">Google Maps Builder <?php echo $this->meta['Version']; ?></div>
		<?php include ('logo-svg-small.php'); ?>
	</div>


	<?php
	/**
	 * Option tabs
	 *
	 * Better organize our options in tabs
	 *
	 * @see: http://code.tutsplus.com/tutorials/the-complete-guide-to-the-wordpress-settings-api-part-5-tabbed-navigation-for-your-settings-page--wp-24971
	 */
	$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'map_options';
	?>
	<h2 class="nav-tab-wrapper">
		<a href="?post_type=google_maps&page=<?php echo self::$key; ?>" class="nav-tab <?php echo $active_tab == 'map_options' ? 'nav-tab-active' : ''; ?>"><?php _e('Map Options', $this->plugin_slug); ?></a>
		<a href="?post_type=google_maps&page=<?php echo self::$key; ?>&tab=general_settings" class="nav-tab <?php echo $active_tab == 'general_settings' ? 'nav-tab-active' : ''; ?>"><?php _e('General Options', $this->plugin_slug); ?></a>
	</h2>


	<?php
	/**
	 * Get the appropriate tab
	 */
	switch ($active_tab) {
	case 'map_options':
	include ('tab-map-options.php');
	break;
	case 'general_settings':
	include ('tab-general-settings.php');
	break;
	default :
	include ('tab-map-options.php');
	break;
	}
	?>


</div>