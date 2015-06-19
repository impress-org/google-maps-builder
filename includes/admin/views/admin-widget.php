<?php
/**
 * Represents the admin view for Google Maps Builder widget.
 * *
 * @package   Google_Maps_Builder
 * @author    Devin Walker <devin@wordimpress.com>
 * @license   GPL-2.0+
 * @link      http://wordimpress.com
 * @copyright 2014 WordImpress, Devin Walker
 */

?>
<!-- Title -->
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title', $this->plugin_slug ); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
</p>


<!-- API Options -->
<p class="widget-api-option">
	<label for="<?php echo $this->get_field_id( 'api_option' ); ?>"><?php _e( 'API Request Method:', $this->plugin_slug ); ?></label><br />
    <span class="wpgp-method-span place-details-api-option-wrap">
    <input type="radio" name="<?php echo $this->get_field_name( 'api_option' ); ?>" class="<?php echo $this->get_field_id( 'api_option' ); ?> " value="0" <?php checked( '0', $api_option ); ?>><span class="wpgp-method-label"><?php _e( 'Place Details', $this->plugin_slug ); ?></span><img src="<?php echo GMB_PLUGIN_URL . '/admin/assets/img/help.png' ?>" title="<?php _e( 'Google Place Details allows you to display more details about a particular establishment. This method returns more comprehensive information about the indicated place such as its complete address, phone number, user rating and reviews.', $this->plugin_slug ); ?>" class="tooltip-info" width="16" height="16" /><br />
    </span>
    <span class="wpgp-method-span place-search-api-option-wrap">
    <input type="radio" name="<?php echo $this->get_field_name( 'api_option' ); ?>" class="<?php echo $this->get_field_id( 'api_option' ); ?> business-api-option" value="1" <?php checked( '1', $api_option ); ?>><span class="wpgp-method-label"><?php _e( 'Place Search', $this->plugin_slug ); ?></span><img src="<?php echo GMB_PLUGIN_URL . '/admin/assets/img/help.png' ?>" title="<?php _e( 'This option allows you to query for place information on a variety of categories, such as: establishments, prominent points of interest, geographic locations, and more. You can search for places either by proximity or a text string. A Place Search returns a list of Places along with summary information about each Place; additional information is available via a Place Details query.', $this->plugin_slug ); ?>" class="tooltip-info" width="16" height="16" />
    </span>
</p>

<!-- Google Places Lookup Autocomplete Search Form -->
<div class="google-autocomplete-map-wrap" <?php if ( $api_option == '0' ) { ?> style="display:block;" <?php } ?>>
	<input class="pac-input controls" type="text" placeholder="Enter a location">

	<div class="map-canvas"></div>

	<div class="type-selector controls">
		<ul class="map-control-list clearfix">
			<li>
				<input type="radio" name="type" id="changetype-all" checked="checked">
				<label for="changetype-all"><?php _e( 'All' ); ?></label>
			</li>
			<li>
				<input type="radio" name="type" id="changetype-establishment">
				<label for="changetype-establishment"><?php _e( 'Establishments' ); ?></label>
			</li>
			<li>
				<input type="radio" name="type" id="changetype-geocode">
				<label for="changetype-geocode"><?php _e( 'Geocodes' ); ?></label>
			</li>
		</ul>
	</div>


	<input class="widefat place-detail-reference" id="<?php echo $this->get_field_id( 'place_detail_reference' ); ?>" name="<?php echo $this->get_field_name( 'place_detail_reference' ); ?>" type="text" value="<?php echo $place_detail_reference; ?>" />

</div>

<div class="widget-footer">
	<div class="powered-by-google"></div>
</div>
