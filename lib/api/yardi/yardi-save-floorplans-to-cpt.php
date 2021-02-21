<?php

/**
 * For the Yardi integration, run a check credentials function, then start the process for each property
 */
add_action( 'apartmentsync_do_save_yardi_floorplans_to_cpt', 'apartmentsync_save_yardi_floorplans_to_cpt' );
function apartmentsync_save_yardi_floorplans_to_cpt() {
            
    // get the list of properties
    $yardi_integration_creds = get_field( 'yardi_integration_creds', 'option' );
    $properties = $yardi_integration_creds['yardi_property_code'];
    $sync_term = apartmentsync_get_sync_term_string();
        
    $properties = explode( ',', $properties );
    foreach( $properties as $property ) {
        
        // if syncing is paused, then stop everything
        if ( $sync_term == 'paused' ) {
            as_unschedule_all_actions( 'apartmentsync_do_fetch_yardi_floorplans', array( $property ), 'yardi' );
            return;
        }
        
        if ( apartmentsync_check_if_sync_term_has_changed() === true ) {
            as_unschedule_all_actions( 'apartmentsync_do_fetch_yardi_floorplans', array( $property ), 'yardi' );
            apartmentsync_log( "Sync term has changed. Rescheduling upcoming actions $sync_term to save Yardi floorplans for property $property as posts." );
        }
                
        if ( as_next_scheduled_action( 'apartmentsync_do_fetch_yardi_floorplans' ) === false ) {
            apartmentsync_log( "Upcoming actions not found. Scheduling tasks $sync_term to save Yardi floorplans for property $property as posts." );    
            as_schedule_recurring_action( time(), apartmentsync_get_sync_term_in_seconds(), 'apartmentsync_do_fetch_yardi_floorplans', array( $property ), 'yardi' );
        }
    }
}

/**
 * For a particular property in Yardi, grab the transient, then start processing floorplans
 */
add_action( 'apartmentsync_do_fetch_yardi_floorplans', 'apartmentsync_fetch_yardi_floorplans', 10, 1 );
function apartmentsync_fetch_yardi_floorplans( $property ) {
    
    $floorplans = get_transient( 'yardi_floorplans_property_id_' . $property );
            
    // bail if we do not have a transient with this data in it
    if ( $floorplans == false ) {
        apartmentsync_log( "No transient currently set for property $property (transient should be named yardi_floorplans_property_id_$property), so we're ending the process." );
        return;
    }
        
    apartmentsync_log( "Transient found for Yardi property $property (named yardi_floorplans_property_id_$property). Looping through data." );
    
    foreach( $floorplans as $floorplan ) {
                   
        apartmentsync_sync_yardi_floorplan_to_cpt( $floorplan );
        
    }
}

/**
 * For a particular floorplan, perform a sync
 */
function apartmentsync_sync_yardi_floorplan_to_cpt( $floorplan ) {
    
    $FloorplanId = $floorplan['FloorplanId'];
    $FloorplanName = $floorplan['FloorplanName'];
    
    // do a query to check and see if a post already exists with this ID 
    $args = array(
        'post_type' => 'floorplans',
        'meta_query' => array(
            array(
                'key' => 'floorplan_id',
                'value' => $FloorplanId,
                'compare' => '=',
            )
        )
    );
    $query = new WP_Query($args);
    
    // insert the post if there isn't one already (this prevents duplicates)
    if ( !$query->have_posts() ) {
        apartmentsync_log( "Floorplan $FloorplanId, $FloorplanName, does not exist yet in the database. Inserting." );
        apartmentsync_insert_yardi_floorplan( $floorplan );
    } else {
        apartmentsync_update_yardi_floorplan( $floorplan );
        apartmentsync_log( "Floorplan $FloorplanId, $FloorplanName, already exists in the database. Skipping." );
    }
    
}

function apartmentsync_insert_yardi_floorplan( $floorplan ) {
    
    // all of the available variables
    $AvailabilityURL = $floorplan['AvailabilityURL'];
    $AvailableUnitsCount = $floorplan['AvailableUnitsCount'];
    $Baths = intval( $floorplan['Baths'] );
    $Beds = intval( $floorplan['Beds'] );
    $FloorplanHasSpecials = $floorplan['FloorplanHasSpecials'];
    $FloorplanId = $floorplan['FloorplanId'];
    $FloorplanImageAltText = $floorplan['FloorplanImageAltText'];
    $FloorplanImageName = $floorplan['FloorplanImageName'];
    $FloorplanImageURL = $floorplan['FloorplanImageURL'];
    $FloorplanName = $floorplan['FloorplanName'];
    $MaximumDeposit = $floorplan['MaximumDeposit'];
    $MaximumRent = $floorplan['MaximumRent'];
    $MaximumSQFT = $floorplan['MaximumSQFT'];
    $MinimumDeposit = $floorplan['MinimumDeposit'];
    $MinimumRent = $floorplan['MinimumRent'];
    $MinimumSQFT = $floorplan['MinimumSQFT'];
    $PropertyId = $floorplan['PropertyId'];
    $PropertyShowsSpecials = $floorplan['PropertyShowsSpecials'];
    $UnitTypeMapping = $floorplan['UnitTypeMapping'];
    
    // Create post object
    $floorplan_meta = array(
        'post_title'    => wp_strip_all_tags( $FloorplanName ),
        'post_status'   => 'publish',
        'post_type'     => 'floorplans',
        'meta_input'    => array(
            'availability_url'          => $AvailabilityURL,
            'available_units'           => $AvailableUnitsCount,
            'baths'                     => $Baths,
            'beds'                      => $Beds,
            'has_specials'              => $FloorplanHasSpecials,
            'floorplan_id'              => $FloorplanId,
            'maximum_deposit'           => $MaximumDeposit,
            'maximum_rent'              => $MaximumRent,
            'maximum_sqft'              => $MaximumSQFT,
            'minimum_deposit'           => $MinimumDeposit,
            'minimum_rent'              => $MinimumRent,
            'minimum_sqft'              => $MinimumSQFT,
            'property_id'               => $PropertyId,
            'property_show_specials'    => $PropertyShowsSpecials,
            'unit_type_mapping'         => $UnitTypeMapping,
        ),
    );
    
    $post_id = wp_insert_post( $floorplan_meta );
    
}

function apartmentsync_update_yardi_floorplan( $floorplan ) {
    
}