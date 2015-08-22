<?php
/**
 * General Settings: Represents the tab view for Google Maps Builder.
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

			<h3><?php _e( 'General Settings', $this->plugin_slug ); ?></h3>

			<p><?php _e( 'Customize how Google Maps Builder functions within WordPress.', $this->plugin_slug ); ?></p>

			<?php cmb2_metabox_form( $this->general_option_fields(), self::$key ); ?>
		</div>
		<div class="col-md-2">

				</div>
	</div>
</div>
