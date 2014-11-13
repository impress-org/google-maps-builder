<?php
/**
 * Represents the view for the public-facing component
 *
 * @package   Google_Maps_Builder
 * @author    Devin Walker <devin@wordimpress.com>
 * @license   GPL-2.0+
 * @link      http://wordimpress.com
 * @copyright 2014 WordImpress, Devin Walker
 */
?>

<div class="google-maps-builder-wrap">

	<div id="google-maps-builder-<?php echo $id; ?>" class="google-maps-builder" <?php echo ! empty( $id ) ? ' data-map-id="' . $id . '"' : '">Error: NO MAP ID'; ?> style="width:<?php echo $visual_info['width'] . $visual_info['map_width_unit']; ?>; height:<?php echo $visual_info['height']; ?>px"></div>

</div>