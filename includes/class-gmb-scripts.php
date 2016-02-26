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

class Google_Maps_Builder_Scripts  {

	/**
	 * Plugin slug
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $plugin_slug;

	/**
	 * Load scripts by context
	 *
	 * @since 2.1.0
	 */
	public function __construct(){
		$this->plugin_slug = Google_Maps_Builder::instance()->get_plugin_slug();
		if( is_admin() ){
			new Google_Maps_Builder_Core_Admin_Scripts();
		}else{
			 new Google_Maps_Builder_Core_Front_End_Scripts();

		}

	}

}//end class
