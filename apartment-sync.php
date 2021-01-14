<?php
/*
	Plugin Name: Apartment Sync
	Plugin URI: https://github.com/jonschr/apartment-sync
    Description: Syncs neighborhoods, properties, and floorplans with various apartment rental APIs
	Version: 0.1
    Author: Brindle Digital & Elodin Design
    Author URI: https://www.brindledigital.com/

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.
*/


/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
    die( "Sorry, you are not allowed to access this page directly." );
}

// Plugin directory
define( 'APARTMENT_SYNC', dirname( __FILE__ ) );

// Define the version of the plugin
define ( 'APARTMENT_SYNC_VERSION', '0.1' );

/////////////
// UPDATER //
/////////////

require 'vendor/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/jonschr/apartment-sync',
	__FILE__,
	'apartment-sync'
);

// Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');

/////////////////
// INCLUDE ACF //
/////////////////

// Define path and URL to the ACF plugin.
define( 'APARTMENT_SYNC_ACF_PATH', plugin_dir_path( __FILE__ ) . 'vendor/acf/' );
define( 'APARTMENT_SYNC_ACF_URL', plugin_dir_url( __FILE__ ) . 'vendor/acf/' );

// Include the ACF plugin.
include_once( APARTMENT_SYNC_ACF_PATH . 'acf.php' );

// Customize the url setting to fix incorrect asset URLs.
add_filter('acf/settings/url', 'apartment_sync_acf_settings_url');
function apartment_sync_acf_settings_url( $url ) {
    return APARTMENT_SYNC_ACF_URL;
}

// (Optional) Hide the ACF admin menu item.
// add_filter('acf/settings/show_admin', 'apartment_sync_acf_settings_show_admin');
function apartment_sync_acf_settings_show_admin( $show_admin ) {
    return false;
}

