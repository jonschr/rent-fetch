<?php
/*
	Plugin Name: Rent Fetch
	Plugin URI: https://github.com/jonschr/rent-fetch
    Description: Syncs properties, and floorplans with various rental APIs
	Version: 3.9
    Author: Brindle Digital
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
define( 'RENTFETCH_DIR', plugin_dir_path( __FILE__ ) );
define( 'RENTFETCH_PATH', plugin_dir_url( __FILE__ ) );

// Define path and URL to the ACF plugin.
define( 'RENTFETCH_ACF_PATH', plugin_dir_path( __FILE__ ) . 'vendor/acf/' );
define( 'RENTFETCH_ACF_URL', plugin_dir_url( __FILE__ ) . 'vendor/acf/' );

// Define the version of the plugin
define ( 'RENTFETCH_VERSION', '3.9' );

//////////////////////////////
// INCLUDE ACTION SCHEDULER //
//////////////////////////////

require_once( plugin_dir_path( __FILE__ ) . 'vendor/action-scheduler/action-scheduler.php' );

///////////////////
// FILE INCLUDES //
///////////////////

//* Logging
foreach ( glob( RENTFETCH_DIR . "lib/initialization/*.php", GLOB_NOSORT ) as $filename ){
    require_once $filename;
}

//* CPTs
foreach ( glob( RENTFETCH_DIR . "lib/post-type/*.php", GLOB_NOSORT ) as $filename ){
    require_once $filename;
}

//* Common functions
foreach ( glob( RENTFETCH_DIR . "lib/common/*.php", GLOB_NOSORT ) as $filename ){
    require_once $filename;
}

//* Taxonomies
foreach ( glob( RENTFETCH_DIR . "lib/tax/*.php", GLOB_NOSORT ) as $filename ){
    require_once $filename;
}

//* CPT connections
foreach ( glob( RENTFETCH_DIR . "lib/cpt-connections/*.php", GLOB_NOSORT ) as $filename ){
    require_once $filename;
}

//* Settings pages
foreach ( glob( RENTFETCH_DIR . "lib/options-pages/*.php", GLOB_NOSORT ) as $filename ){
    require_once $filename;
}

//* Templates
foreach ( glob( RENTFETCH_DIR . "lib/template-functions/*.php", GLOB_NOSORT ) as $filename ){
    require_once $filename;
}

//* Shortcodes
foreach ( glob( RENTFETCH_DIR . "lib/shortcode/*.php", GLOB_NOSORT ) as $filename ){
    require_once $filename;
}

//* Process requires
foreach ( glob( RENTFETCH_DIR . "lib/api/*.php", GLOB_NOSORT ) as $filename ){
    require_once $filename;
}

//* Include everything in /lib/api
foreach ( glob( RENTFETCH_DIR . "lib/api/*/*.php", GLOB_NOSORT ) as $filename ){
    require_once $filename;
}

//* Gutenberg blocks
foreach ( glob( RENTFETCH_DIR . "lib/block/*/*.php", GLOB_NOSORT ) as $filename ){
    require_once $filename;
}

//////////////////////
// START THE ENGINE //
//////////////////////

add_action( 'init', 'rentfetch_start_sync' );
function rentfetch_start_sync() {
    
    //* REMOVE ALL ACTIONS ADDED BY OLD APARTMENTSYNC, ADDED IN 3.0, CAN BE REMOVED IN A LATER RELEASE
    // as_unschedule_action( 'apartmentsync_do_get_yardi_property_from_api' );
    as_unschedule_all_actions( 'apartmentsync_do_get_yardi_property_from_api' );
    // as_unschedule_action( 'apartmentsync_do_get_yardi_floorplans_from_api_for_property' );
    as_unschedule_all_actions( 'apartmentsync_do_get_yardi_floorplans_from_api_for_property' );
    // as_unschedule_action( 'apartmentsync_do_fetch_yardi_floorplans' );
    as_unschedule_all_actions( 'apartmentsync_do_fetch_yardi_floorplans' );
    // as_unschedule_action( 'do_get_yardi_property_from_api' );
    as_unschedule_all_actions( 'do_get_yardi_property_from_api' );
    // as_unschedule_action( 'do_get_yardi_floorplans_from_api_for_property' );
    as_unschedule_all_actions( 'do_get_yardi_floorplans_from_api_for_property' );
    // as_unschedule_action( 'do_fetch_yardi_floorplans' );
    as_unschedule_all_actions( 'do_fetch_yardi_floorplans' );
    
    $sync_term = get_field( 'sync_term', 'option' );
    $data_sync = get_field( 'data_sync', 'option' );
            
    if ( $sync_term == 'paused' || $data_sync == 'delete' || $data_sync == 'nosync' ) {       
         
        // yardi
        as_unschedule_all_actions( 'rentfetch_do_get_yardi_property_from_api' );
        as_unschedule_all_actions( 'rentfetch_do_get_yardi_floorplans_from_api_for_property' );
        as_unschedule_all_actions( 'rentfetch_do_fetch_yardi_floorplans' );
        as_unschedule_all_actions( 'rentfetch_do_remove_floorplans_from_orphan_yardi_properties_specific' );
        as_unschedule_all_actions( 'rentfetch_do_remove_orphan_yardi_properties' );
        
        // appfolio
        // as_unschedule_action( 'rentfetch_appfolio_do_process_and_save_floorplans' );
        as_unschedule_all_actions( 'rentfetch_appfolio_do_process_and_save_floorplans' );
        
        // geocoding
        as_unschedule_all_actions( 'rentfetch_geocoding_get_lat_long' );
        
        
    } else {
        
        //* We're doing these async because we don't want them constantly triggering on each pageload.
        if ( as_next_scheduled_action( 'rentfetch_do_sync_logic' ) === false  ) 
            as_enqueue_async_action( 'rentfetch_do_sync_logic' );
            
        // if ( as_next_scheduled_action( 'rentfetch_do_chron_activation' ) === false  ) 
        //     as_enqueue_async_action( 'rentfetch_do_chron_activation' );
            
        if ( as_next_scheduled_action( 'rentfetch_do_remove_old_data' ) === false  ) 
            as_enqueue_async_action( 'rentfetch_do_remove_old_data' );
            
    }
        
    //* Delete everything if we're set to delete
    if ( $data_sync == 'delete' )
        do_action( 'rentfetch_do_delete' );
            
    // do_action( 'rentfetch_do_sync_logic' );
    // do_action( 'rentfetch_do_chron_activation' );
        
    // // Look and see whether there's another scheduled action waiting
    // var_dump( as_next_scheduled_action( 'rentfetch_do_sync_logic' ) ); 
    // var_dump( as_next_scheduled_action( 'rentfetch_do_chron_activation' ) );
    
}

////////////////////
// PLUGIN UPDATER //
////////////////////

// Updater
require RENTFETCH_DIR . 'vendor/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/jonschr/rent-fetch',
	__FILE__,
	'rent-fetch'
);

// Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch( 'main' );
