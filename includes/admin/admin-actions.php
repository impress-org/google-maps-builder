<?php
/**
 * Admin Actions
 *
 * @package     GMB
 * @subpackage  Admin/Actions
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Processes all GMB actions sent via POST and GET by looking for the 'gmb-action'
 * request and running do_action() to call the function
 *
 * @since 2.0
 * @return void
 */
function gmb_process_actions() {
	if ( isset( $_POST['gmb_action'] ) ) {
		do_action( 'gmb_' . $_POST['gmb_action'], $_POST );
	}

	if ( isset( $_GET['gmb_action'] ) ) {
		do_action( 'gmb_' . $_GET['gmb_action'], $_GET );
	}
}
add_action( 'admin_init', 'gmb_process_actions' );
