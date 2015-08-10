<?php
/**
 * System Info Tab - Displays useful debugging info for support reasons
 *
 * @package   Google_Maps_Builder
 * @author    Devin Walker <devin@wordimpress.com>
 * @license   GPL-2.0+
 * @link      http://wordimpress.com
 * @copyright 2015 WordImpress, Devin Walker
 */

?>

<div class="container">
	<div class="row">
		<div class="col-md-10">

			<h3><?php _e( 'System Info', $this->plugin_slug ); ?></h3>

			<p><?php _e( 'The following displays useful information about your website that may be requested from you for support reasons.', $this->plugin_slug ); ?></p>

			<form class="gmb-form" method="post" id="system_settings" enctype="multipart/form-data" encoding="multipart/form-data">
				<?php gmb_system_info_callback(); ?>
			</form>

		</div>
		<div class="col-md-2">

		</div>
	</div>
</div>
