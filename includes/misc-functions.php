<?php
/**
 * Misc Functions
 *
 * @package     Google_Maps_Builder
 * @subpackage  Functions
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Checks whether function is disabled.
 *
 * @since 2.0
 *
 * @param string $function Name of the function.
 *
 * @return bool Whether or not function is disabled.
 */
function gmb_is_func_disabled( $function ) {
	$disabled = explode( ',', ini_get( 'disable_functions' ) );

	return in_array( $function, $disabled );
}

/**
 * Wrapper function for wp_die(). This function adds filters for wp_die() which
 * kills execution of the script using wp_die(). This allows us to then to work
 * with functions using gmb_die() in the unit tests.
 *
 * @since  2.0
 * @return void
 */
function gmb_die( $message = '', $title = '', $status = 400 ) {
	add_filter( 'wp_die_ajax_handler', '_gmb_die_handler', 10, 3 );
	add_filter( 'wp_die_handler', '_gmb_die_handler', 10, 3 );
	wp_die( $message, $title, array( 'response' => $status ) );
}


/**
 * Check if AJAX works as expected
 *
 * @since 2.0
 * @return bool True if AJAX works, false otherwise
 */
function gmb_test_ajax_works() {

	// Check if the Airplane Mode plugin is installed
	if ( class_exists( 'Airplane_Mode_Core' ) ) {

		$airplane = Airplane_Mode_Core::getInstance();

		if ( method_exists( $airplane, 'enabled' ) ) {

			if ( $airplane->enabled() ) {
				return true;
			}

		} else {

			if ( $airplane->check_status() == 'on' ) {
				return true;
			}
		}
	}

	add_filter( 'block_local_requests', '__return_false' );

	if ( get_transient( '_gmb_ajax_works' ) ) {
		return true;
	}

	$params = array(
		'sslverify'  => false,
		'timeout'    => 30,
		'body'       => array(
			'action' => 'gmb_test_ajax'
		)
	);

	$ajax  = wp_remote_post( gmb_get_ajax_url(), $params );
	$works = true;

	if ( is_wp_error( $ajax ) ) {

		$works = false;

	} else {

		if( empty( $ajax['response'] ) ) {
			$works = false;
		}

		if( empty( $ajax['response']['code'] ) || 200 !== (int) $ajax['response']['code'] ) {
			$works = false;
		}

		if( empty( $ajax['response']['message'] ) || 'OK' !== $ajax['response']['message'] ) {
			$works = false;
		}

		if( ! isset( $ajax['body'] ) || 0 !== (int) $ajax['body'] ) {
			$works = false;
		}

	}

	if ( $works ) {
		set_transient( '_gmb_ajax_works', '1', DAY_IN_SECONDS );
	}

	return $works;
}




/**
 * Get AJAX URL
 *
 * @since 2.0
 * @return string
 */
function gmb_get_ajax_url() {
	$scheme = defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN ? 'https' : 'admin';

	$current_url = gmb_get_current_page_url();
	$ajax_url    = admin_url( 'admin-ajax.php', $scheme );

	if ( preg_match( '/^https/', $current_url ) && ! preg_match( '/^https/', $ajax_url ) ) {
		$ajax_url = preg_replace( '/^http/', 'https', $ajax_url );
	}

	return apply_filters( 'gmb_ajax_url', $ajax_url );
}

/**
 * Get the current page URL
 *
 * @since 2.0
 * @return string $page_url Current page URL
 */
function gmb_get_current_page_url() {

	if ( is_front_page() ) :
		$page_url = home_url();
	else :
		$page_url = 'http';

		if ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) {
			$page_url .= "s";
		}

		$page_url .= "://";

		if ( isset( $_SERVER["SERVER_PORT"] ) && $_SERVER["SERVER_PORT"] != "80" ) {
			$page_url .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$page_url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
	endif;

	return apply_filters( 'gmb_get_current_page_url', esc_url( $page_url ) );
}


/**
 * Get user host
 *
 * Returns the webhost this site is using if possible
 *
 * @since 1.0
 * @return mixed string $host if detected, false otherwise
 */
function gmb_get_host() {
	$host = false;

	if ( defined( 'WPE_APIKEY' ) ) {
		$host = 'WP Engine';
	} elseif ( defined( 'PAGELYBIN' ) ) {
		$host = 'Pagely';
	} elseif ( DB_HOST == 'localhost:/tmp/mysql5.sock' ) {
		$host = 'ICDSoft';
	} elseif ( DB_HOST == 'mysqlv5' ) {
		$host = 'NetworkSolutions';
	} elseif ( strpos( DB_HOST, 'ipagemysql.com' ) !== false ) {
		$host = 'iPage';
	} elseif ( strpos( DB_HOST, 'ipowermysql.com' ) !== false ) {
		$host = 'IPower';
	} elseif ( strpos( DB_HOST, '.gridserver.com' ) !== false ) {
		$host = 'MediaTemple Grid';
	} elseif ( strpos( DB_HOST, '.pair.com' ) !== false ) {
		$host = 'pair Networks';
	} elseif ( strpos( DB_HOST, '.stabletransit.com' ) !== false ) {
		$host = 'Rackspace Cloud';
	} elseif ( strpos( DB_HOST, '.sysfix.eu' ) !== false ) {
		$host = 'SysFix.eu Power Hosting';
	} elseif ( strpos( $_SERVER['SERVER_NAME'], 'Flywheel' ) !== false ) {
		$host = 'Flywheel';
	} else {
		// Adding a general fallback for data gathering
		$host = 'DBH: ' . DB_HOST . ', SRV: ' . $_SERVER['SERVER_NAME'];
	}

	return $host;
}
