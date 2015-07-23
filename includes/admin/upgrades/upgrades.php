<?php
/**
 * Upgrade Screen
 *
 * @subpackage  includes/admin/upgrades
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render Upgrades Screen
 *
 * @since 2.0
 * @return void
 */
function gmb_upgrades_screen() {

	$action = isset( $_GET['gmb-upgrade'] ) ? sanitize_text_field( $_GET['gmb-upgrade'] ) : '';
	$step   = isset( $_GET['step'] ) ? absint( $_GET['step'] ) : 1;
	$total  = isset( $_GET['total'] ) ? absint( $_GET['total'] ) : false;
	$custom = isset( $_GET['custom'] ) ? absint( $_GET['custom'] ) : 0;
	$number = isset( $_GET['number'] ) ? absint( $_GET['number'] ) : 100;
	$steps  = round( ( $total / $number ), 0 );

	$doing_upgrade_args = array(
		'page'        => 'gmb-upgrades',
		'gmb-upgrade' => $action,
		'step'        => $step,
		'total'       => $total,
		'custom'      => $custom,
		'steps'       => $steps
	);
	update_option( 'gmb_doing_upgrade', $doing_upgrade_args );
	if ( $step > $steps ) {
		// Prevent a weird case where the estimate was off. Usually only a couple.
		$steps = $step;
	}
	?>
	<div class="wrap">
		<h2><?php _e( 'Maps Builder - Upgrade', 'gmb' ); ?></h2>

		<?php if ( ! empty( $action ) ) : ?>

			<div id="gmb-upgrade-status">
				<p><?php _e( 'The upgrade process has started, please be patient. This could take several minutes. You will be automatically redirected when the upgrade is finished.', 'gmb' ); ?></p>

				<?php if ( ! empty( $total ) ) : ?>
					<p>
						<strong><?php printf( __( 'Step %d of approximately %d running', 'gmb' ), $step, $steps ); ?></strong>
					</p>
				<?php endif; ?>
			</div>
			<script type="text/javascript">
				setTimeout( function () {
					document.location.href = "index.php?gmb_action=<?php echo $action; ?>&step=<?php echo $step; ?>&total=<?php echo $total; ?>&custom=<?php echo $custom; ?>";
				}, 250 );
			</script>

		<?php else : ?>

			<div id="gmb-upgrade-status" class="updated" style="margin-top:15px;">
				<p style="margin-bottom:8px;">
					<?php _e( 'The upgrade process has started, please do not close your browser or refresh. This could take several minutes. You will be automatically redirected when the upgrade has finished.', 'gmb' ); ?>
					<img src="<?php echo GMB_PLUGIN_URL . '/assets/img/loading.gif'; ?>" id="gmb-upgrade-loader" style="position:relative; top:3px;" />
				</p>
			</div>
			<script type="text/javascript">
				jQuery( document ).ready( function () {
					// Trigger upgrades on page load
					var data = {action: 'gmb_trigger_upgrades'};
					var el_upgrade_status = jQuery( '#gmb-upgrade-status' );

					//Trigger via AJAX
					jQuery.post( ajaxurl, data, function ( response ) {

						//Uncomment for debugging
//						jQuery( '#gmb-upgrade-status' ).after( response );

						//Success Message
						if ( response == 'complete' ) {

							el_upgrade_status.hide();
							el_upgrade_status.after( '<div class="updated"><p>The upgrade process has completed successfully. Hooray! You will now be redirected back to your previous page.</p></div>' );

							//Send user back to prev page
							setTimeout( function () {
								history.back();
							}, 4000 );

						}
					} );
				} );
			</script>

		<?php endif; ?>

	</div>
	<?php
}