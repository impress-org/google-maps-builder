<?php
/**
 *  Markers
 *
 * @description: Appears in modal
 * @since      :
 * @created    : 4/29/14
 */
?>

<div id="marker-icon-modal" style="display:none;">

<div class="marker-description clear">
	<p><?php _e( 'Customize your Google Maps markers by selecting a marker graphic and icon. Integration made possible from the excellent Maps Icon library.', $this->plugin_slug ); ?></p>
</div>

<div class="marker-row clear">
	<h3><?php _e( 'Step 1: Select a Marker', $this->plugin_slug ); ?></h3>

	<div class="marker-item" data-marker="MAP_PIN">
		<div class="marker-svg">
			<svg version="1.0" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="50px" height="50px" viewBox="0 0 100 165" enable-background="new 0 0 100 165" xml:space="preserve"><path fill="#428BCA" d="M50,0C22.382,0,0,21.966,0,49.054C0,76.151,50,165,50,165s50-88.849,50-115.946C100,21.966,77.605,0,50,0z"></path>
				</svg>
		</div>
		<div class="marker-description"><?php _e( 'Map Pin', $this->plugin_slug ); ?></div>
	</div>

	<div class="marker-item" data-marker="SQUARE_PIN">
		<div class="marker-svg">
			<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="50px" height="50px" viewBox="0 0 100 120" enable-background="new 0 0 100 120" xml:space="preserve"><polygon fill="#428BCA" points="100,0 0,0 0,100 36.768,100 50.199,119.876 63.63,100 100,100 "></polygon></svg>
		</div>
		<div class="marker-description"><?php _e( 'Square Pin', $this->plugin_slug ); ?></div>
	</div>
	<div class="marker-item" data-marker="default">
		<div class="marker-svg">
			<img src="<?php echo GMB_PLUGIN_URL . '/public/assets/img/default-marker.png' ?>" class="default-marker" />
		</div>
		<div class="marker-description"><?php _e( 'Default', $this->plugin_slug ); ?></div>
	</div>

</div>

<div class="marker-icon-color-wrap clear">

	<div class="marker-color-picker-wrap"><input type="text" name="color" id="color" value="#428BCA" class="color-picker marker-color" />
	</div>
	<p class="color-desc"><?php _e( 'Customize the marker color?', $this->plugin_slug ); ?></p>

</div>


<div class="marker-icon-row clear">
	<h3><?php _e( 'Step 2: Select a Marker Icon', $this->plugin_slug ); ?></h3>

	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-art-gallery"></span>
			art-gallery
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-campground"></span>
			campground
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-bank"></span>
			bank
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-hair-care"></span>
			hair-care
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-gym"></span>
			gym
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-point-of-interest"></span>
			point-of-interest
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-post-box"></span>
			post-box
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-post-office"></span>
			post-office
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-university"></span>
			university
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-beauty-salon"></span>
			beauty-salon
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-atm"></span>
			atm
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-rv-park"></span>
			rv-park
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-school"></span>
			school
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-library"></span>
			library
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-spa"></span>
			spa
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-route"></span>
			route
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-postal-code"></span>
			postal-code
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-stadium"></span>
			stadium
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-postal-code-prefix"></span>
			postal-code-prefix
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-museum"></span>
			museum
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-finance"></span>
			finance
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-natural-feature"></span>
			natural-feature
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-funeral-home"></span>
			funeral-home
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-cemetery"></span>
			cemetery
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-park"></span>
			park
		</div>
	</div>
	<div class="icon">
		<div class="icon-inner">
			<span class="map-icon-lodging"></span>
			lodging
		</div>
	</div>
</div>
<!--/.marker-icon-row -->

<div class="marker-label-color-wrap clear">
	<div class="marker-color-picker-wrap"><input type="text" name="color" id="color" class="color-picker label-color"  value="#444444" /></div>
	<p class="color-desc"><?php _e( 'Customize the icon color?', $this->plugin_slug ); ?></p>
</div>

<div class="save-marker-icon clear">
	<p class="save-text"><?php _e( 'Marker is ready to be set.', $this->plugin_slug ); ?></p>
	<button class="button button-primary button-large save-marker-button" data-marker="" data-marker-color="#428BCA" data-label="" data-label-color="#FFFFFF" data-marker-index="">Set Marker</button>
</div>

</div>