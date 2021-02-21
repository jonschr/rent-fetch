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

// (Optional) Hide the ACF admin menu item.
// add_filter('acf/settings/show_admin', 'apartmentsync_acf_settings_show_admin');
function apartmentsync_acf_settings_show_admin( $show_admin ) {
    return false;
}

///////////////////
// FILE INCLUDES //
///////////////////

//* Common functions
require_once( 'lib/common/apartmentsync_get_sync_term.php' );

//* Process requires
require_once( 'lib/api/start-sync.php' ); // kick off the sync process
require_once( 'lib/api/yardi/yardi-check-credentials.php' );
require_once( 'lib/api/yardi/yardi-pull-from-api.php' );
require_once( 'lib/api/yardi/yardi-save-floorplans-to-cpt.php' );
require_once( 'lib/api/entrata/entrata-sync.php' );
require_once( 'lib/api/entrata/entrata-check-credentials.php' );

///////////////////
// FUNCTIONALITY //
///////////////////

add_action( 'init', 'apartmentsync_start_the_engine' );
function apartmentsync_start_the_engine() {
    
    // Exit function if doing an AJAX request
    if ( wp_doing_ajax() == true )
        return;
    
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
    
}

///////////////////////////////////////////
// SCHEDULE TASKS FOR ADDING TO DATABASE //
///////////////////////////////////////////

add_action( 'init', 'apartmentsync_chron_activation' );
function apartmentsync_chron_activation() {
    
    // Exit function if doing an AJAX request
    if ( wp_doing_ajax() == true )
        return;
    
    // apartmentsync_log( 'Checking whether a chron job needs to be scheduled' );
    
    // get the sync term from the settings
    $sync_term = apartmentsync_get_sync_term_in_seconds();

    // if the chron should be paused, then remove upcoming jobs
    if ( $sync_term == 'paused' ) {
        // clear the chron if we're paused
        wp_clear_scheduled_hook( 'apartmentsync_do_run_chron' );
        apartmentsync_log( "Removing chron job: apartmentsync_do_run_chron" );
        
    // if we should have a chron running, then get that set if needed
    } else {
        
        if ( !wp_next_scheduled( 'apartmentsync_do_run_chron' )) {
            // set the chron if we aren't paused
            wp_schedule_event( time(), $sync_term, 'apartmentsync_do_run_chron' );
            apartmentsync_log( "Scheduling apartmentsync_do_run_chron chron job: setting for $sync_term seconds" );
        } else {
            // apartmentsync_log( "Chron job apartmentsync_do_run_chron is already scheduled $sync_term" );        
        }
        
    }
}
 
add_action( 'apartmentsync_do_run_chron', 'apartmentsync_run_chron' );
function apartmentsync_run_chron() {
    
    $enabled_integrations = get_field( 'enabled_integrations', 'option' );
    
    // bail if there aren't any integrations enabled
    if ( !$enabled_integrations )
        return;
    
    foreach ( $enabled_integrations as $enabled_integration ) {
        
        // action to get the floorplans and put them in a transient
        do_action( 'apartmentsync_do_save_' . $enabled_integration . '_floorplans_to_cpt' );
        
    }
}

// on plugin deactivation, remove the chron events
register_deactivation_hook( __FILE__, 'apartmentsync_deactivate_chron' );
function apartmentsync_deactivate_chron() {
    wp_clear_scheduled_hook( 'apartmentsync_do_run_chron' );
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

/////////////
// LOGGING //
/////////////

//* Allows for console_logging data (into the javascript logs)
function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
}

function apartmentsync_log($message) { 
    
    if( is_array( $message ) )
        $message = json_encode($message); 
        
    $file = fopen( WP_CONTENT_DIR . "/apartment-sync-debug.log", "a" );
    fwrite($file, date('Y-m-d h:i:s') . " " . $message . "\n"); 
    fclose($file); 
    
}