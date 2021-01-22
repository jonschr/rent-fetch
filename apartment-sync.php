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
define( 'APARTMENTSYNC', dirname( __FILE__ ) );

// Define the version of the plugin
define ( 'APARTMENTSYNC_VERSION', '0.1' );

//* Requires
require_once( 'lib/api/start-sync.php' ); // kick off the sync process
require_once( 'lib/api/yardi/yardi-check-credentials.php' );
require_once( 'lib/api/yardi/yardi-pull-from-api.php' );
require_once( 'lib/api/yardi/yardi-save-floorplans-to-cpt.php' );
require_once( 'lib/api/entrata/entrata-sync.php' );
require_once( 'lib/api/entrata/entrata-check-credentials.php' );

///////////////////
// FUNCTIONALITY //
///////////////////

add_action( 'after_setup_theme', 'apartmentsync_start_the_engine' );
function apartmentsync_start_the_engine() {
    
    //* settings pages
    require_once( 'lib/options-pages/main-settings.php' );
    require_once( 'lib/options-pages/sync-actions.php' );
    
    //* figure out whether this is a single 
    $apartment_site_type = get_field( 'apartment_site_type', 'option' );
        
    //* floorplans post type
    require_once( 'lib/post-type/floorplans.php' );
    
    //* only register the properties and neighborhoods post types if this is a 'multiple' site
    if ( $apartment_site_type == 'multiple' ) {
        // if we aren't running a site for multiple properties, we don't need the neighborhood or property content types
        require_once( 'lib/post-type/properties.php' );
        require_once( 'lib/post-type/neighborhoods.php' );
    }
        
    //* kick off the logic for getting data from apis
    do_action( 'apartmentsync_do_sync_logic' );
    do_action( 'apartmentsync_do_save_yardi_floorplans_to_cpt' );
    
    
}

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

// // Define path and URL to the ACF plugin.
// define( 'APARTMENTSYNC_ACF_PATH', plugin_dir_path( __FILE__ ) . 'vendor/acf/' );
// define( 'APARTMENTSYNC_ACF_URL', plugin_dir_url( __FILE__ ) . 'vendor/acf/' );

// // Include the ACF plugin.
// include_once( APARTMENTSYNC_ACF_PATH . 'acf.php' );

// // Customize the url setting to fix incorrect asset URLs.
// add_filter('acf/settings/url', 'apartmentsync_acf_settings_url');
// function apartmentsync_acf_settings_url( $url ) {
//     return APARTMENTSYNC_ACF_URL;
// }

// // (Optional) Hide the ACF admin menu item.
// // add_filter('acf/settings/show_admin', 'apartmentsync_acf_settings_show_admin');
// function apartmentsync_acf_settings_show_admin( $show_admin ) {
//     return false;
// }

function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
}