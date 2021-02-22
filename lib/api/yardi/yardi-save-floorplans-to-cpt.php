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
        
        // if syncing is paused or data dync is off, then stop everything
        if ( $sync_term == 'paused' ) {
            as_unschedule_all_actions( 'apartmentsync_do_fetch_yardi_floorplans', array( $property ), 'yardi' );
            return;
        }
        
        if ( apartmentsync_check_if_sync_term_has_changed() === true ) {
            as_unschedule_all_actions( 'apartmentsync_do_fetch_yardi_floorplans', array( $property ), 'yardi' );
            apartmentsync_verbose_log( "Sync term has changed. Rescheduling upcoming actions $sync_term to save Yardi floorplans for property $property as posts." );
        }
                
        if ( as_next_scheduled_action( 'apartmentsync_do_fetch_yardi_floorplans' ) === false ) {
            apartmentsync_verbose_log( "Upcoming actions not found. Scheduling tasks $sync_term to save Yardi floorplans for property $property as posts." );    
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
        apartmentsync_verbose_log( "No transient currently set for property $property (transient should be named yardi_floorplans_property_id_$property), so we're ending the process." );
        return;
    }
        
    apartmentsync_verbose_log( "Transient found for Yardi property $property (named yardi_floorplans_property_id_$property). Looping through data." );
    
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
    $matchingposts = get_posts( $args );
    $count = count( $matchingposts );
    
    // insert the post if there isn't one already (this prevents duplicates)
    if ( !$matchingposts ) {
        apartmentsync_verbose_log( "Floorplan $FloorplanId, $FloorplanName, does not exist yet in the database. Inserting." );
        apartmentsync_insert_yardi_floorplan( $floorplan );
        
    // if there's exactly one post found, then update the meta for that
    } elseif ( $count == 1 ) {
        apartmentsync_verbose_log( "Floorplan $FloorplanId, $FloorplanName, already exists in the database. Checking post meta." );
        apartmentsync_update_yardi_floorplan( $floorplan, $matchingposts );
        
    // if there are more than one found, delete all of those that match and add fresh, since we likely have some bad data
    } elseif( $count > 1 ) {
        apartmentsync_verbose_log( "$count posts for floorplan $FloorplanId found. Removing duplicates and reinserting fresh." );
        foreach ($matchingposts as $matchingpost) {
            wp_delete_post( $matchingpost->ID, true );
        }
        apartmentsync_insert_yardi_floorplan( $floorplan );
    }
}

/**
 * Insert a new floorplan into the database
 *
 * @param   array  $floorplan  provided from the Yardi transient
 *
 * @return  none              
 */
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
    $FloorplanSource = 'yardi'; // this one doesn't come from the API. This is our identifier that says "this caame from the API."
    
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
            'floorplan_source'          => $FloorplanSource,
        ),
    );
    
    $post_id = wp_insert_post( $floorplan_meta );
    
}

/**
 * Update an individual Yardi floorplan in place
 *
 * @param   array  $floorplan      the floorplan data from the transient
 * @param   object  $matchingposts  an object comprised of posts from get_posts expected to contain only one post
 *
 * @return  none  
 */
function apartmentsync_update_yardi_floorplan( $floorplan, $matchingposts ) {
    
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
    $FloorplanName = wp_strip_all_tags( $floorplan['FloorplanName'] );
    $MaximumDeposit = $floorplan['MaximumDeposit'];
    $MaximumRent = $floorplan['MaximumRent'];
    $MaximumSQFT = $floorplan['MaximumSQFT'];
    $MinimumDeposit = $floorplan['MinimumDeposit'];
    $MinimumRent = $floorplan['MinimumRent'];
    $MinimumSQFT = $floorplan['MinimumSQFT'];
    $PropertyId = $floorplan['PropertyId'];
    $PropertyShowsSpecials = $floorplan['PropertyShowsSpecials'];
    $UnitTypeMapping = $floorplan['UnitTypeMapping'];
    $FloorplanSource = 'yardi'; // this one doesn't come from the API. This is our identifier that says "this caame from the API."
    
    // SAMPLE ARRAY
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
            'floorplan_source'          => $FloorplanSource,
        ),
    );
    
    // The Loop
    if ( $matchingposts ) {
        
        foreach ( $matchingposts as $matchingpost ) {
            
            $post_id = $matchingpost->ID;
            
            if ( $FloorplanName != $matchingpost->post_title ) {
                // update post title
                $arr = array( 
                    'post_title' => $FloorplanName,
                    'ID' => $post_id,
                );
                wp_update_post( $arr );
                apartmentsync_log( "Floorplan $FloorplanId title updated: post_title is now $FloorplanName." );
            }
            
            // update post meta (NOTE: update_post_meta returns false if it doesn't update, true if it does)
            $success_availability_url = update_post_meta( $post_id, 'availability_url', $AvailabilityURL );
            if ( $success_availability_url == true )
                apartmentsync_log( "Floorplan $FloorplanId meta updated: availability_url is now $AvailabilityURL." );
            
            $success_available_units = update_post_meta( $post_id, 'available_units', $AvailableUnitsCount );
            if ( $success_available_units == true )
                apartmentsync_log( "Floorplan $FloorplanId meta updated: available_units is now $AvailableUnitsCount." );
            
            $success_baths = update_post_meta( $post_id, 'baths', $Baths );
            if ( $success_baths == true )
                apartmentsync_log( "Floorplan $FloorplanId meta updated: baths is now $Baths." );
            
            $success_beds = update_post_meta( $post_id, 'beds', $Beds );
            if ( $success_beds == true )
                apartmentsync_log( "Floorplan $FloorplanId meta updated: beds is now $Beds." );
                
            $success_has_specials = update_post_meta( $post_id, 'has_specials', $FloorplanHasSpecials );
            if ( $success_has_specials == true )
                apartmentsync_log( "Floorplan $FloorplanId meta updated: has_specials is now $FloorplanHasSpecials." );
                                
            $success_maximum_deposit = update_post_meta( $post_id, 'maximum_deposit', $MaximumDeposit );
            if ( $success_maximum_deposit == true )
                apartmentsync_log( "Floorplan $FloorplanId meta updated: maximum_deposit is now $MaximumDeposit." );
                
            $success_maximum_rent = update_post_meta( $post_id, 'maximum_rent', $MaximumRent );
            if ( $success_maximum_rent == true )
                apartmentsync_log( "Floorplan $FloorplanId meta updated: maximum_rent is now $MaximumRent." );
                
            $success_maximum_sqft = update_post_meta( $post_id, 'maximum_sqft', $MaximumSQFT );
            if ( $success_maximum_sqft == true )
                apartmentsync_log( "Floorplan $FloorplanId meta updated: maximum_sqft is now $MaximumSQFT." );
                
            $success_minimum_deposit = update_post_meta( $post_id, 'minimum_deposit', $MinimumDeposit );
            if ( $success_minimum_deposit == true )
                apartmentsync_log( "Floorplan $FloorplanId meta updated: minimum_deposit is now $MinimumDeposit." );
                
            $success_minimum_rent = update_post_meta( $post_id, 'minimum_rent', $MinimumRent );
            if ( $success_minimum_rent == true )
                apartmentsync_log( "Floorplan $FloorplanId meta updated: minimum_rent is now $MinimumRent." );
                
            $success_minimum_sqft = update_post_meta( $post_id, 'minimum_sqft', $MinimumSQFT );
            if ( $success_minimum_sqft == true )
                apartmentsync_log( "Floorplan $FloorplanId meta updated: minimum_sqft is now $MinimumSQFT." );
                
            $success_property_id = update_post_meta( $post_id, 'property_id', $PropertyId );
            if ( $success_property_id == true )
                apartmentsync_log( "Floorplan $FloorplanId meta updated: property_id is now $PropertyId." );
                
            $success_property_show_specials = update_post_meta( $post_id, 'property_show_specials', $PropertyShowsSpecials );
            if ( $success_property_show_specials == true )
                apartmentsync_log( "Floorplan $FloorplanId meta updated: property_show_specials is now $PropertyShowsSpecials." );
                
            $success_unit_type_mapping = update_post_meta( $post_id, 'unit_type_mapping', $UnitTypeMapping );
            if ( $success_unit_type_mapping == true )
                apartmentsync_log( "Floorplan $FloorplanId meta updated: unit_type_mapping is now $UnitTypeMapping." );
                
            $success_floorplan_source = update_post_meta( $post_id, 'floorplan_source', $FloorplanSource );
            if ( $success_floorplan_source == true )
                apartmentsync_log( "Floorplan $FloorplanId meta updated: floorplan_source is now $FloorplanSource." );
            
            
        }   
        
        wp_reset_postdata();
    }
    
}