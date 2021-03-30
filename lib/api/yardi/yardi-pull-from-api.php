<?php

add_action( 'apartmentsync_get_floorplans_yardi', 'apartmentsync_get_floorplans_yardi' );
function apartmentsync_get_floorplans_yardi() {
    
    // notify the user, then bail if we're missing credential data
    if ( apartmentsync_check_creds_yardi() == false ) {
        add_action( 'admin_notices', 'apartmentsync_yardi_missing_user_pass_notice');
        return;
    }
        
    $yardi_integration_creds = get_field( 'yardi_integration_creds', 'option' );
    $properties = $yardi_integration_creds['yardi_property_code'];    
    $yardi_api_key = $yardi_integration_creds['yardi_api_key'];
    $sync_term = apartmentsync_get_sync_term_string();
    
    // as_schedule_recurring_action( $timestamp, $interval_in_seconds, $hook, $args, $group );
    
    $properties = explode( ',', $properties );
    foreach( $properties as $property ) {
            
        $floorplans = get_transient( 'yardi_floorplans_property_id_' . $property );
        
        // bail if we already have a transient with this data in it
        if ( $floorplans != false )
            continue;
            
        // if syncing is paused or data dync is off, then stop everything
        if ( $sync_term == 'paused' ) {
            as_unschedule_action( 'do_get_yardi_floorplans_from_api_for_property', array( $property, $yardi_integration_creds, $yardi_api_key ), 'yardi' );
            as_unschedule_all_actions( 'do_get_yardi_floorplans_from_api_for_property', array( $property, $yardi_integration_creds, $yardi_api_key ), 'yardi' );
            continue;
        }
        
        if ( apartmentsync_check_if_sync_term_has_changed() === true ) {
            as_unschedule_action( 'do_get_yardi_floorplans_from_api_for_property', array( $property, $yardi_integration_creds, $yardi_api_key ), 'yardi' );
            as_unschedule_all_actions( 'do_get_yardi_floorplans_from_api_for_property', array( $property, $yardi_integration_creds, $yardi_api_key ), 'yardi' );
            apartmentsync_verbose_log( "Sync term has changed. Rescheduling upcoming actions $sync_term to get Yardi property $property floorplans from API." );
        }
                
        if ( $sync_term != 'paused' &&  as_next_scheduled_action( 'do_get_yardi_floorplans_from_api_for_property', array( $property, $yardi_integration_creds, $yardi_api_key ), 'yardi' ) === false ) {
            apartmentsync_verbose_log( "Upcoming actions not found. Scheduling tasks $sync_term to get Yardi property $property floorplans from API." );    
            as_enqueue_async_action( 'do_get_yardi_floorplans_from_api_for_property', array( $property, $yardi_integration_creds, $yardi_api_key ), 'yardi' );
            as_schedule_recurring_action( time(), apartmentsync_get_sync_term_in_seconds(), 'do_get_yardi_floorplans_from_api_for_property', array( $property, $yardi_integration_creds, $yardi_api_key ), 'yardi' );
        }            
    }
}

add_action( 'do_get_yardi_floorplans_from_api_for_property', 'get_yardi_floorplans_from_api_for_property', 10, 3 );
function get_yardi_floorplans_from_api_for_property( $property, $yardi_integration_creds, $yardi_api_key ) {
    
    apartmentsync_verbose_log( "Transient not found for Yardi property $property (yardi_floorplans_property_id_$property). Pulling new data from https://api.rentcafe.com/rentcafeapi.aspx?requestType=floorplan." );
        
    // Do the API request
    $url = sprintf( 'https://api.rentcafe.com/rentcafeapi.aspx?requestType=floorplan&apiToken=%s&VoyagerPropertyCode=%s', $yardi_api_key, $property ); // path to your JSON file
    $data = file_get_contents( $url ); // put the contents of the file into a variable        
    $floorplans = json_decode( $data, true ); // decode the JSON feed
    $errorcode = null;
    
    if ( isset( $floorplans[0]['Error'] ) ) {
        
        do_action( 'apartmentsync_yardi_floorplan_show_error', $floorplans[0]['Error'], $property );

        // error has happened
        $errorcode = $floorplans[0]['Error'];
    }
        
    if ( !$errorcode && !empty( $floorplans ) ) {
        set_transient( 'yardi_floorplans_property_id_' . $property, $floorplans, apartmentsync_get_sync_term_in_seconds() );
        apartmentsync_verbose_log( "Yardi returned a list of floorplans for property $property successfully. New transient set: yardi_floorplans_property_id_$property" );                
    } elseif( !$errorcode && empty( $floorplans ) ) {
        apartmentsync_log( "No data received from Yardi for property $property." );
    } else {
        apartmentsync_log( "Yardi returned error code $errorcode for property $property." );
    }
    
}