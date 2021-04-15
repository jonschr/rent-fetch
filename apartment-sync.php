<?php
/*
	Plugin Name: Apartment Sync
	Plugin URI: https://github.com/jonschr/apartment-sync
    Description: Syncs neighborhoods, properties, and floorplans with various apartment rental APIs
	Version: 0.39.1
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
define( 'APARTMENTSYNC_PATH', plugin_dir_url( __FILE__ ) );

// Define the version of the plugin
define ( 'APARTMENTSYNC_VERSION', '0.39.1' );

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

//! UNCOMMENT THIS FILTER TO SAVE ACF FIELDS TO PLUGIN
// add_filter('acf/settings/save_json', 'apartmentsync_acf_json_save_point');
function apartmentsync_acf_json_save_point( $path ) {
    
    // update path
    $path = APARTMENTSYNC_DIR . 'acf-json';
    
    // return
    return $path;
    
}

add_filter( 'acf/settings/load_json', 'apartmentsync_acf_json_load_point' );
function apartmentsync_acf_json_load_point( $paths ) {
    
    // remove original path (optional)
    unset($paths[0]);
    
    // append path
    $paths[] = APARTMENTSYNC_DIR . 'acf-json';
    
    // return
    return $paths;
    
}

///////////////////////
// ADMIN COLUMNS PRO //
///////////////////////

// add_filter( 'acp/storage/file/directory/writable', '__return_false' ); //! CHANGE TO __return_true TO MAKE CHANGES
// add_filter( 'acp/storage/file/directory', 'apartmentsync_acp_storage_file_directory' );
// function apartmentsync_acp_storage_file_directory( $path ) {
// 	// Use a writable path, directory will be created for you
//     return APARTMENTSYNC_DIR . '/acp-settings';
// }

use AC\ListScreenRepository\Storage\ListScreenRepositoryFactory;
use AC\ListScreenRepository\Rules;
use AC\ListScreenRepository\Rule;
add_filter( 'acp/storage/repositories', function( array $repositories, ListScreenRepositoryFactory $factory ) {
    
    //! Change $writable to true to allow changes to columns for the content types below
    $writable = false;
    
    // 2. Add rules to target individual list tables.
    // Defaults to Rules::MATCH_ANY added here for clarity, other option is Rules::MATCH_ALL
    $rules = new Rules( Rules::MATCH_ANY );
    $rules->add_rule( new Rule\EqualType( 'floorplans' ) );
    $rules->add_rule( new Rule\EqualType( 'properties' ) );
    $rules->add_rule( new Rule\EqualType( 'neighborhoods' ) );
    
    // 3. Register your repository to the stack
    $repositories['apartment-sync'] = $factory->create(
        APARTMENTSYNC_DIR . '/acp-settings',
        $writable,
        $rules
    );
    
    return $repositories;
    
}, 10, 2 );

///////////////////
// FILE INCLUDES //
///////////////////

//* CPTs
require_once( 'lib/post-type/floorplans.php' );
require_once( 'lib/post-type/properties.php' );
require_once( 'lib/post-type/neighborhoods.php' );

//* Taxonomies
require_once( 'lib/tax/floorplantype.php' );
require_once( 'lib/tax/amenities.php' );
require_once( 'lib/tax/propertytype.php' );
require_once( 'lib/tax/areas.php' );

//* CPT connections
require_once( 'lib/cpt-connections/properties-to-neighborhoods.php' );

//* Common functions
require_once( 'lib/common/apartmentsync_get_sync_term.php' );
require_once( 'lib/common/set_post_terms.php' );

//* Settings pages
require_once( 'lib/options-pages/main-settings.php' );
// require_once( 'lib/options-pages/sync-actions.php' ); // currently unused

//* Templates
require_once( 'template/template-detection.php' );
require_once( 'template/floorplan-in-archive.php' );
require_once( 'template/single-properties-property-images.php' );

//* Shortcodes
require_once( 'shortcode/search-properties-map.php' );
require_once( 'shortcode/search-properties-starter.php' );

//* Documentation
require_once( 'lib/options-pages/documentation-sidebar-link.php' );

//* Process requires
require_once( 'lib/api/pull-from-apis.php' ); // kick off the sync process
require_once( 'lib/api/save-to-cpt.php' ); // kick off chron processes for converting transients into posts
require_once( 'lib/api/delete-all.php' ); // adds functionality to delete all from the backend
require_once( 'lib/api/yardi/yardi-check-credentials.php' );
require_once( 'lib/api/yardi/yardi-get-properties.php' );
require_once( 'lib/api/yardi/yardi-pull-floorplans-from-api.php' );
require_once( 'lib/api/yardi/yardi-save-floorplans-to-cpt.php' );
require_once( 'lib/api/yardi/yardi-remove-old-floorplans.php' );
require_once( 'lib/api/entrata/entrata-sync.php' );
require_once( 'lib/api/entrata/entrata-check-credentials.php' );

//* Gutenberg blocks
require_once( 'block/floorplangrid/floorplangrid.php' );

//////////////////////
// START THE ENGINE //
//////////////////////

add_action( 'init', 'apartmentsync_start_sync' );
function apartmentsync_start_sync() {
    
    //* We're doing these async because we don't want them constantly triggering on each pageload. We'd still like to bundle together our syncing and our chron
    
    if ( as_next_scheduled_action( 'apartmentsync_do_sync_logic' ) === false  ) 
        as_enqueue_async_action( 'apartmentsync_do_sync_logic' );
        
    if ( as_next_scheduled_action( 'apartmentsync_do_chron_activation' ) === false  ) 
        as_enqueue_async_action( 'apartmentsync_do_chron_activation' );
        
    if ( as_next_scheduled_action( 'apartmentsync_do_remove_old_data' ) === false  ) 
        as_enqueue_async_action( 'apartmentsync_do_remove_old_data' );
            
    // do_action( 'apartmentsync_do_sync_logic' );
    // do_action( 'apartmentsync_do_chron_activation' );
        
    // // Look and see whether there's another scheduled action waiting
    // var_dump( as_next_scheduled_action( 'apartmentsync_do_sync_logic' ) ); 
    // var_dump( as_next_scheduled_action( 'apartmentsync_do_chron_activation' ) );
    
}

//////////////////////
// CPT REGISTRATION //
//////////////////////


// add_action( 'init', 'apartmentsync_register_content_types' );
// function apartmentsync_register_content_types() {
            
//     //* figure out whether this is a single 
//     $apartment_site_type = get_field( 'apartment_site_type', 'option' );
        
//     //* floorplans post type
//     require_once( 'lib/post-type/floorplans.php' );
//     require_once( 'lib/tax/floorplantype.php' );
    
//     //* only register the properties and neighborhoods post types if this is a 'multiple' site
//     if ( $apartment_site_type == 'multiple' ) {
        
//         // only include the properties and neighborhoods if we have multiple properties needed
//         require_once( 'lib/post-type/properties.php' );
//         require_once( 'lib/tax/amenities.php' );
//         require_once( 'lib/tax/propertytype.php' );
//         require_once( 'lib/post-type/neighborhoods.php' );
//         require_once( 'lib/tax/areas.php' );
        
//         // connect properties and neighborhoods
//         require_once( 'lib/cpt-connections/properties-to-neighborhoods.php' );
        
//     }
    
// }

//////////////
// ENQUEUES //
//////////////

add_action( 'wp_enqueue_scripts', 'apartmentsync_enqueue_scripts_stylesheets' );
function apartmentsync_enqueue_scripts_stylesheets() {
	
	// Plugin styles
    wp_register_style( 'apartmentsync-single-properties', APARTMENTSYNC_PATH . 'css/single-properties.css', array(), APARTMENTSYNC_VERSION, 'screen' );
    wp_register_style( 'apartmentsync-floorplan-in-archive', APARTMENTSYNC_PATH . 'css/floorplan-in-archive.css', array(), APARTMENTSYNC_VERSION, 'screen' );
    
    // Properties map
    wp_register_style( 'apartmentsync-search-properties-map', APARTMENTSYNC_PATH . 'css/search-properties-map.css', array(), APARTMENTSYNC_VERSION, 'screen' );
    wp_register_script( 'apartmentsync-search-properties-ajax', APARTMENTSYNC_PATH . 'js/apartmentsync-search-properties-ajax.js', array( 'jquery' ), APARTMENTSYNC_VERSION, true );
    wp_register_script( 'apartmentsync-search-properties-script', APARTMENTSYNC_PATH . 'js/apartmentsync-search-properties-script.js', array( 'jquery' ), APARTMENTSYNC_VERSION, true );
    
    // Properties searchbar
    wp_register_script( 'apartmentsync-search-filters-general', APARTMENTSYNC_PATH . 'js/apartmentsync-search-filters-general.js', array( 'jquery' ), APARTMENTSYNC_VERSION, true );
    wp_register_script( 'apartmentsync-searchbar', APARTMENTSYNC_PATH . 'js/apartmentsync-searchbar.js', array( 'jquery' ), APARTMENTSYNC_VERSION, true );
    
    // Fancybox
    wp_register_style( 'apartmentsync-fancybox-style', APARTMENTSYNC_PATH . 'vendor/fancybox/jquery.fancybox.min.css', array(), APARTMENTSYNC_VERSION, 'screen' );
    wp_register_script( 'apartmentsync-fancybox-script', APARTMENTSYNC_PATH . 'vendor/fancybox/jquery.fancybox.min.js', array( 'jquery' ), APARTMENTSYNC_VERSION, true );
    	
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
$myUpdateChecker->setBranch( 'main' );

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

//////////////////////////////
// CLEAR OUT ACTIONS FASTER //
//////////////////////////////

/**
 * Change Action Scheduler default purge to 1 hour (because we generate a TON of actions)
 */
add_filter( 'action_scheduler_retention_period', 'wpb_action_scheduler_purge' );
function wpb_action_scheduler_purge() {
    return HOUR_IN_SECONDS;
}
