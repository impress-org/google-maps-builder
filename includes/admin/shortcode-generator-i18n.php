<?php

$strings = 'tinyMCE.addI18n({' . _WP_Editors::$mce_locale . ':{
	gmb:{
		shortcode_generator_title: "' . esc_js( __( 'Maps Builder Shortcode Generator', 'google-maps-reviews' ) ) . '",
		shortcode_tag: "' . esc_js( apply_filters( 'gmb_shortcode_tag', 'google_maps' ) ) . '"
	}
}})';