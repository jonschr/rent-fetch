<?php

add_action( 'apartmentsync_do_sync_logic', 'apartmentsync_start_the_sync' );
function apartmentsync_start_the_sync() {
    
    $data_sync = get_field( 'data_sync', 'option' );
    
    //* bail if they've said "don't sync" or if they've not set that option
    if ( $data_sync == 'nosync' || $data_sync == null )
        return;
        
    //* if it's a full sync, then do that action
    if ( $data_sync == 'fullsync' )
        do_action( 'apartment_do_full_sync' );
    
    //* if it's an update-only sync, then do that action
    if ( $data_sync == 'updatesync' )
        do_action( 'apartment_do_update_sync' );
        
}

//* run an action to start a sync for each enabled integration
add_action( 'apartment_do_full_sync', 'apartmentsync_determine_platform' );
add_action( 'apartment_do_update_sync', 'apartmentsync_determine_platform' );
function apartmentsync_determine_platform() {
    
    $enabled_integrations = get_field( 'enabled_integrations', 'option' );
    foreach ( $enabled_integrations as $enabled_integration ) {
        
        // use action scheduler to set up regular actions to grab from the API
        // if ( false === as_next_scheduled_action( 'apartmentsync_get_floorplans_' . $enabled_integration ) )
        //     as_schedule_recurring_action( apartmentsync_get_sync_term_in_seconds(), 'apartmentsync_get_floorplans_' . $enabled_integration );
        
        // action to get the floorplans and put them in a transient
        do_action( 'apartmentsync_get_floorplans_' . $enabled_integration );
    }
    
}

