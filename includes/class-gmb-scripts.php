<?php
/**
 * Scripts
 *
 * @package     GMB
 * @subpackage  Functions
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Google_Maps_Builder_Scripts extends Google_Maps_Builder_Core_Scripts_Init {

	/**
	 * Load additional admin scripts
	 *
	 * @since 2.1.0
	 *
	 * @uses "admin_enqueue_scripts"
	 *
	 * @param $hook
	 */
	public function admin_hooks( $hook ){
		global $post;
		$js_dir = GMB_PLUGIN_URL . 'assets/js/admin/';
		$js_plugins = GMB_PLUGIN_URL . 'assets/js/plugins/';
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( ( $hook == 'post-new.php' || $hook == 'post.php' ) && 'google_maps' === $post->post_type ) {
			//pro only
			wp_register_script( $this->plugin_slug . '-admin-free', $js_dir . 'admin-free' . $suffix . '.js', array( 'jquery' ), GMB_VERSION );
			wp_enqueue_script( $this->plugin_slug . '-admin-free' );

		}

		//Import/Export Scripts
		if ( $hook == 'google_maps_page_gmb_import_export' ) {
			wp_register_script( $this->plugin_slug . '-admin-import-export', $js_dir . 'admin-import-export' . $suffix . '.js', array( 'jquery' ), GMB_VERSION );
			wp_enqueue_script( $this->plugin_slug . '-admin-import-export' );


		}

	}

}//end class
