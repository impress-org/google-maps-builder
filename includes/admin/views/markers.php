<?php
/**
 *  Markers
 *
 * @description: Appears in modal
 * @since      :
 * @created    : 4/29/14
 */
?>
<div class="white-popup marker-icon-modal mfp-hide">
	<div class="inner-modal-wrap">
		<div class="inner-modal-container">
			<div class="inner-modal">
				<button type="button" class="gmb-modal-close">&times;</button>
				<div class="marker-description-wrap clear">
					<h3><?php _e( 'Customize Map Marker', $this->plugin_slug ); ?></h3>

					<p><?php _e( 'Customize your Google Maps markers by selecting a marker graphic and icon. Integration made possible from the excellent Maps Icon library.', $this->plugin_slug ); ?></p>
				</div>

				<div class="marker-row clear">
					<h3><?php _e( 'Step 1: Select a Marker', $this->plugin_slug ); ?></h3>

					<div class="marker-item" data-marker="MAP_PIN" data-toggle="map-svg-icons">
						<div class="marker-svg">
							<svg version="1.0" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="50px" height="50px" viewBox="0 0 100 165" enable-background="new 0 0 100 165" xml:space="preserve"><path fill="#428BCA" d="M50,0C22.382,0,0,21.966,0,49.054C0,76.151,50,165,50,165s50-88.849,50-115.946C100,21.966,77.605,0,50,0z"></path>
				</svg>
						</div>
						<div class="marker-description"><?php _e( 'Map Pin', $this->plugin_slug ); ?></div>
					</div>

					<div class="marker-item" data-marker="SQUARE_PIN" data-toggle="map-svg-icons">
						<div class="marker-svg">
							<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="50px" height="50px" viewBox="0 0 100 120" enable-background="new 0 0 100 120" xml:space="preserve"><polygon fill="#428BCA" points="100,0 0,0 0,100 36.768,100 50.199,119.876 63.63,100 100,100 "></polygon></svg>
						</div>
						<div class="marker-description"><?php _e( 'Square Pin', $this->plugin_slug ); ?></div>
					</div>
					<div class="marker-item" data-marker="default" data-toggle="default-icons-row">
						<div class="marker-svg default-marker">
							<img src="<?php echo apply_filters( 'gmb_default_marker', GMB_PLUGIN_URL . 'assets/img/spotlight-poi.png' ); ?>" class="default-marker" />
						</div>
						<div class="marker-description"><?php _e( 'Default', $this->plugin_slug ); ?></div>
					</div>

				</div>

				<div class="marker-icon-row map-svg-icons clear">

					<div class="marker-icon-color-wrap clear">
						<div class="marker-color-picker-wrap">
							<input type="text" name="color" id="color" value="#428BCA" class="color-picker marker-color" />
						</div>
						<p class="color-desc"><?php _e( 'Customize the marker color?', $this->plugin_slug ); ?></p>
					</div>

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

					<div class="marker-label-color-wrap clear">
						<div class="marker-color-picker-wrap">
							<input type="text" name="color" id="color" class="color-picker label-color" value="#444444" />
						</div>
						<p class="color-desc"><?php _e( 'Customize the icon color?', $this->plugin_slug ); ?></p>
					</div>

				</div>
				<!--/.marker-icon-row -->


				<div class="marker-icon-row default-icons-row gmb-hidden clear">

					<h3><?php _e( 'Step 2: Select a Marker Icon', $this->plugin_slug ); ?></h3>

					<ul class="map-icons-list">
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue-blank.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue-dot.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerA.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerB.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerC.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerD.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerE.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerF.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerG.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerH.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerI.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerJ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerK.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerL.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerM.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerN.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerO.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerP.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerQ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerR.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerS.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerT.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerU.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerV.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerW.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerX.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerY.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/blue_MarkerZ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown-blank.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown-dot.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerA.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerB.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerC.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerD.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerE.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerF.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerG.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerH.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerI.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerJ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerK.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerL.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerM.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerN.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerO.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerP.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerQ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerR.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerS.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerT.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerU.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerV.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerW.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerX.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerY.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/brown_MarkerZ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen-blank.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen-dot.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerA.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerB.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerC.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerD.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerE.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerF.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerG.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerH.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerI.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerJ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerK.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerL.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerM.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerN.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerO.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerP.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerQ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerR.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerS.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerT.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerU.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerV.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerW.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerX.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerY.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/darkgreen_MarkerZ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green-blank.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green-dot.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerA.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerB.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerC.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerD.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerE.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerF.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerG.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerH.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerI.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerJ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerK.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerL.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerM.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerN.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerO.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerP.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerQ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerR.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerS.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerT.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerU.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerV.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerW.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerX.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/green_MarkerY.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange-blank.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange-dot.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerA.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerB.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerC.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerD.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerE.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerF.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerG.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerH.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerI.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerJ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerK.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerL.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerM.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerN.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerO.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerP.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerQ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerR.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerS.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerT.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerU.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerV.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerW.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerX.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerY.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/orange_MarkerZ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue-blank.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue-dot.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerA.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerB.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerC.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerD.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerE.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerF.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerG.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerH.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerI.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerJ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerK.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerL.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerM.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerN.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerO.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerP.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerQ.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerR.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerS.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerT.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerU.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerV.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerW.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerX.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerY.png' ?>" alt=""></a>
						</li>
						<li>
							<a href="#" class="maps-icon"><img src="<?php echo GMB_PLUGIN_URL . 'assets/img/default-icons/paleblue_MarkerZ.png' ?>" alt=""></a>
						</li>
					</ul>
				</div>

				<div class="save-marker-icon clear">
					<p class="save-text"><?php _e( 'Marker is ready to be set.', $this->plugin_slug ); ?></p>
					<button class="button button-primary button-large save-marker-button" data-marker="" data-marker-color="#428BCA" data-label="" data-label-color="#FFFFFF" data-marker-index="">Set Marker</button>
				</div>

			</div>
		</div>
	</div>
</div>