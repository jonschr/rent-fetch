<?php
/*
	Plugin Name: Rent Fetch
	Plugin URI: https://github.com/jonschr/rent-fetch
    Description: Syncs properties, and floorplans with various rental APIs
	Version: 3.10.1
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

// Define the version of the plugin
define ( 'RENTFETCH_VERSION', '3.10.1' );

// Plugin directory
define( 'RENTFETCH_DIR', plugin_dir_path( __FILE__ ) );
define( 'RENTFETCH_PATH', plugin_dir_url( __FILE__ ) );

// Define path and URL to the ACF plugin.
define( 'RENTFETCH_ACF_PATH', plugin_dir_path( __FILE__ ) . 'vendor/acf/' );
define( 'RENTFETCH_ACF_URL', plugin_dir_url( __FILE__ ) . 'vendor/acf/' );

//////////////////////////////
// INCLUDE ACTION SCHEDULER //
//////////////////////////////

require_once( plugin_dir_path( __FILE__ ) . 'vendor/action-scheduler/action-scheduler.php' );

// $table_list = array(
//     'actionscheduler_actions',
//     'actionscheduler_logs',
//     'actionscheduler_groups',
//     'actionscheduler_claims',
// );

// $found_tables = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}actionscheduler%'" );

// foreach ( $table_list as $table_name ) {
//     if ( ! in_array( $wpdb->prefix . $table_name, $found_tables ) ) {
//         // Table is missing, recreate it
//         $sql = "CREATE TABLE {$wpdb->prefix}{$table_name} (
//             // Define table structure here
//         );";

//         // Execute the SQL query to recreate the table
//         $wpdb->query( $sql );

//         // Additional actions after table recreation, if needed

//         // Output a success message
//         echo "Table {$wpdb->prefix}{$table_name} created successfully.";
//     }
// }


// $table_list = array(
//     'actionscheduler_actions',
//     'actionscheduler_logs',
//     'actionscheduler_groups',
//     'actionscheduler_claims',
// );

// $found_tables = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}actionscheduler%'" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
// foreach ( $table_list as $table_name ) {
//     if ( ! in_array( $wpdb->prefix . $table_name, $found_tables ) ) {
        
//         // $this->admin_notices[] = array(
//         //     'class'   => 'error',
//         //     'message' => __( 'It appears one or more database tables were missing. Attempting to re-create the missing table(s).' , 'action-scheduler' ),
//         // );
//         // add_action( 'action_scheduler/created_table', array( $store, 'set_autoincrement' ), 10, 2 );

//         $store_schema  = new ActionScheduler_StoreSchema();
//         $logger_schema = new ActionScheduler_LoggerSchema();
//         $store_schema->register_tables( true );
//         $logger_schema->register_tables( true );

//         // remove_action( 'action_scheduler/created_table', array( $store, 'set_autoincrement' ), 10 );
        
//         parent::display_admin_notices();

//         return;
//     }
// }

///////////////////
// FILE INCLUDES //
///////////////////

//* require_once each file in lib
foreach ( glob( RENTFETCH_DIR . "lib/*.php", GLOB_NOSORT ) as $filename ){
    require_once $filename;
}

// //* require_once each file in each subdirectory of lib
foreach ( glob( RENTFETCH_DIR . "lib/*", GLOB_ONLYDIR ) as $dir ){
    foreach ( glob( $dir . "/*.php", GLOB_NOSORT ) as $filename ){
        require_once $filename;
    }
}

//* require_once each file in lib and in all subdirectories of lib
foreach ( glob( RENTFETCH_DIR . "lib/*", GLOB_ONLYDIR ) as $dir ){
    foreach ( glob( $dir . "/*", GLOB_ONLYDIR ) as $subdir ){
        foreach ( glob( $subdir . "/*.php", GLOB_NOSORT ) as $filename ){
            require_once $filename;
        }
    }
}

//////////////////////
// START THE ENGINE //
//////////////////////
function rentfetch_database_tables_missing_notice() {
    echo '<div class="notice notice-error is-dismissible">';
    echo '<p>' . __( '<strong>Rent Fetch:</strong> The Action Scheduler tables appear to be missing. Please <a href="/wp-admin/tools.php?page=action-scheduler">vist the Action Scheduler admin page</a> to regenerate those.', 'rentfetch' ) . '</p>';
    echo '</div>';
}

//! Perhaps add this in a later release. This function drops the Action Scheduler tables if they are over 500MB; however, administrative action is required to fix this.
//! maybe consider programatically deactivating then reactivating Rent Fetch to regen the tables instead?
// add_action( 'init', 'rentfetch_drop_large_actionscheduler_tables' );
function rentfetch_drop_large_actionscheduler_tables() {
    
    global $wpdb;
    
    $table_list = array(
        'actionscheduler_actions',
        'actionscheduler_logs',
        'actionscheduler_groups',
        'actionscheduler_claims',
    );

    $found_tables = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}actionscheduler%'" );

    foreach ($table_list as $table_name) {
        if (in_array($wpdb->prefix . $table_name, $found_tables)) {
            // Get table size in bytes
            $query = "SELECT data_length + index_length AS size FROM information_schema.TABLES WHERE table_schema = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}{$table_name}'";
            $result = $wpdb->get_row($query);
            
            // Check if the query was successful
            if ($result !== null) {
                $size_in_bytes = (int) $result->size;
                
                // Check if the table size exceeds 500MB (in bytes)
                if ($size_in_bytes > 500 * 1024 * 1024) {
                    // Drop the table
                    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}{$table_name}");
                    
                    // Output a success message
                    echo "Table {$wpdb->prefix}{$table_name} dropped successfully.<br>";
                }
            }
        }
    }
}

add_action( 'wp_loaded', 'rentfetch_start_sync' );
function rentfetch_start_sync() {
    
    global $wpdb;

    $table_list = array(
        'actionscheduler_actions',
        'actionscheduler_logs',
        'actionscheduler_groups',
        'actionscheduler_claims',
    );
        
    foreach( $table_list as $table ) {
        
        $found_tables = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}{$table}'" );
        
        // if the tables are missing, output the notice and return
        if ( !$found_tables ) {
            
            add_action( 'admin_notices', 'rentfetch_database_tables_missing_notice' );
            
            return;
        }
    }
    
    
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
    
    $sync_term = get_option( 'options_sync_term' );
    $data_sync = get_option( 'options_data_sync' );
            
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
