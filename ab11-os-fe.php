<?php
/*
Plugin Name: Awaken Benehime Online Schedule Front End
Plugin URI: http://www.rappahannock.edu
Description: Online Schedule for RCC website.
Version: 1.1.0
Author: Michael Greene
Author URI: http://profmikegreene.com
License: This plugin is for RCC
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-ab11-os-fe.php' );

register_activation_hook( __FILE__, array( 'AB11_OS_FE', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'AB11_OS_FE', 'deactivate' ) );



?>