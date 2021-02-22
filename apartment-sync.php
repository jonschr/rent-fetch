<?php
/*
	Plugin Name: Apartment Sync
	Plugin URI: https://github.com/jonschr/apartment-sync
    Description: Syncs neighborhoods, properties, and floorplans with various apartment rental APIs
	Version: 0.2
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
define( 'APARTMENTSYNC_DIR', plugin_dir_path( __FILE__ ) );

// Define the version of the plugin
define ( 'APARTMENTSYNC_VERSION', '0.2' );

//////////////////////////////
// INCLUDE ACTION SCHEDULER //
//////////////////////////////

require_once( plugin_dir_path( __FILE__ ) . 'vendor/action-scheduler/action-scheduler.php' );

/////////////////
// INCLUDE ACF //
/////////////////

// Define path and URL to the ACF plugin.
define( 'APARTMENTSYNC_ACF_PATH', plugin_dir_path( __FILE__ ) . 'vendor/acf/' );
define( 'APARTMENTSYNC_ACF_URL', plugin_dir_url( __FILE__ ) . 'vendor/acf/' );

// Include the ACF plugin.
include_once( APARTMENTSYNC_ACF_PATH . 'acf.php' );

// Customize the url setting to fix incorrect asset URLs.
add_filter('acf/settings/url', 'apartmentsync_acf_settings_url');
function apartmentsync_acf_settings_url( $url ) {
    return APARTMENTSYNC_ACF_URL;
}

///////////////////
// FILE INCLUDES //
///////////////////

//* ACF fields
require_once( 'lib/acf-fields/floorplan-details.php' );
require_once( 'lib/acf-fields/settings.php' );

//* Common functions
require_once( 'lib/common/apartmentsync_get_sync_term.php' );

//* Documentation
require_once( 'lib/options-pages/documentation-sidebar-link.php' );

//* Process requires
require_once( 'lib/api/pull-from-apis.php' ); // kick off the sync process
require_once( 'lib/api/save-to-cpt.php' ); // kick off chron processes for converting transients into posts
require_once( 'lib/api/delete-all.php' ); // adds functionality to delete all from the backend
require_once( 'lib/api/yardi/yardi-check-credentials.php' );
require_once( 'lib/api/yardi/yardi-pull-from-api.php' );
require_once( 'lib/api/yardi/yardi-save-floorplans-to-cpt.php' );
require_once( 'lib/api/entrata/entrata-sync.php' );
require_once( 'lib/api/entrata/entrata-check-credentials.php' );

//////////////////////
// START THE ENGINE //
//////////////////////

add_action( 'init', 'apartmentsync_start_sync' );
function apartmentsync_start_sync() {
        
    //* get data from apis
    do_action( 'apartmentsync_do_sync_logic' ); // lib/api/save-to-cpt.php
    
    //* set up chrons for reading the transients into posts
    do_action( 'apartmentsync_do_chron_activation' ); // lib/api/pull-from-apis.php
    
}

//////////////////////
// CPT REGISTRATION //
//////////////////////

add_action( 'after_setup_theme', 'apartmentsync_register_content_types' );
function apartmentsync_register_content_types() {
        
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
    
}

////////////////////
// PLUGIN UPDATER //
////////////////////

require 'vendor/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/jonschr/apartment-sync',
	__FILE__,
	'apartment-sync'
);

// Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');

/////////////
// LOGGING //
/////////////

//* Allows for console_logging data (into the javascript logs)
function console_log( $data ){
    echo '<script>';
        echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
}

//* Add debug logging
function apartmentsync_log($message) { 
    
    if( is_array( $message ) )
        $message = json_encode($message); 
        
    $file = fopen( WP_CONTENT_DIR . "/apartment-sync-debug.log", "a" );
    fwrite($file, date('Y-m-d h:i:s') . " " . $message . "\n"); 
    fclose($file); 
    
    $file = fopen( WP_CONTENT_DIR . "/apartment-sync-debug-verbose.log", "a" );
    fwrite($file, date('Y-m-d h:i:s') . " " . $message . "\n"); 
    fclose($file); 
    
}

//* Add debug verbose logging
function apartmentsync_verbose_log($message) { 
    
    if( is_array( $message ) )
        $message = json_encode($message); 
                
    $file = fopen( WP_CONTENT_DIR . "/apartment-sync-debug-verbose.log", "a" );
    fwrite($file, date('Y-m-d h:i:s') . " " . $message . "\n"); 
    fclose($file); 
    
}