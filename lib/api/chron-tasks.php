<?php


///////////////////////////////////////////
// SCHEDULE TASKS FOR ADDING TO DATABASE //
///////////////////////////////////////////

add_action( 'apartmentsync_do_chron_activation', 'apartmentsync_chron_activation' );
function apartmentsync_chron_activation() {
    
    apartmentsync_log( 'Checking whether a chron job needs to be scheduled' );
    
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
            apartmentsync_log( "Chron job apartmentsync_do_run_chron is already scheduled $sync_term" );        
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