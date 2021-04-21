<?php

add_action( 'apartmentsync_do_get_properties_yardi', 'apartmentsync_get_properties_yardi' );
function apartmentsync_get_properties_yardi() {
    
    // notify the user, then bail if we're missing credential data
    if ( apartmentsync_check_creds_yardi() == false ) {
        add_action( 'admin_notices', 'apartmentsync_yardi_missing_user_pass_notice');
        return;
    }
    
    $yardi_integration_creds = get_field( 'yardi_integration_creds', 'option' );
    $properties = $yardi_integration_creds['yardi_property_code'];      
    $properties = explode( ',', $properties );
    $yardi_api_key = $yardi_integration_creds['yardi_api_key'];
    $sync_term = get_field( 'sync_term', 'option' );
    $data_sync = get_field( 'data_sync', 'option' );
        
    foreach( $properties as $property ) {
            
        // if syncing is paused or data dync is off, then bail, as we won't be restarting anything
        if ( $sync_term == 'paused' || $data_sync == 'delete' || $data_sync == 'nosync' ) {
            as_unschedule_action( 'apartmentsync_do_get_yardi_property_from_api', array( $property, $yardi_api_key ), 'yardi' );
            as_unschedule_all_actions( 'apartmentsync_do_get_yardi_property_from_api', array( $property, $yardi_api_key ), 'yardi' );
            apartmentsync_verbose_log( "Sync term has changed for pulling property from API. Removing upcoming actions." );
            continue;
        }
                        
        if ( as_next_scheduled_action( 'apartmentsync_do_get_yardi_property_from_api', array( $property, $yardi_api_key ), 'yardi' ) == false ) {
            apartmentsync_verbose_log( "Upcoming actions not found. Scheduling tasks $sync_term to get Yardi property $property from API." );    
            as_enqueue_async_action( 'apartmentsync_do_get_yardi_property_from_api', array( $property, $yardi_api_key ), 'yardi' );
            as_schedule_recurring_action( time(), apartmentsync_get_sync_term_in_seconds(), 'apartmentsync_do_get_yardi_property_from_api', array( $property, $yardi_api_key ), 'yardi' );
        }            
    }
}

add_action( 'init', 'test_funct' );
function test_funct() {
        
    $voyager_id = '1002univ';
    $yardi_api_key = '532b316d-fbcb-480c-b1bd-481fbe699360';
    
    do_action( 'test_act', $voyager_id, $yardi_api_key );

}


//* Get the property using the Properties API
// add_action( 'test_act', 'get_yardi_property_from_api', 10, 2 );
add_action( 'apartmentsync_do_get_yardi_property_from_api', 'get_yardi_property_from_api', 10, 2 );
function get_yardi_property_from_api( $voyager_id, $yardi_api_key ) {
   
    apartmentsync_verbose_log( "Pulling property data for $voyager_id from yardi API." );
            
    // Do the API request
    $url = sprintf( 'https://api.rentcafe.com/rentcafeapi.aspx?requestType=property&type=marketingData&apiToken=%s&VoyagerPropertyCode=%s', $yardi_api_key, $voyager_id ); // path to your JSON file
    
    $data = file_get_contents( $url ); // put the contents of the file into a variable        
    $propertydata = json_decode( $data, true ); // decode the JSON feed
    $errorcode = null;
        
    if ( !$errorcode && !empty( $propertydata ) ) {
        apartmentsync_verbose_log( "Yardi returned property data for property $voyager_id successfully. New transient set: yardi_property_id_$voyager_id" );                
        
        do_action( 'apartmentsync_do_save_property_data_to_cpt', $propertydata );
        
    } elseif( !$errorcode && empty( $propertydata ) ) {
        apartmentsync_log( "No property data received from Yardi for property $voyager_id." );
    } else {
        apartmentsync_log( "Property API query: Yardi returned error code $errorcode for property $voyager_id." );
    }
    
}

//* Get the property from the Images API and update the property
// add_action( 'test_act', 'get_yardi_property_images_from_api', 10, 2 );
add_action( 'apartmentsync_do_get_yardi_property_from_api', 'get_yardi_property_images_from_api', 10, 2 );
function get_yardi_property_images_from_api( $voyager_id, $yardi_api_key ) {
                
    // Do the API request
    $url = sprintf( 'https://api.rentcafe.com/rentcafeapi.aspx?requestType=images&type=propertyImages&apiToken=%s&VoyagerPropertyCode=%s', $yardi_api_key, $voyager_id ); // path to your JSON file
    $propertydata = file_get_contents( $url ); // put the contents of the file into a variable        
        
    if ( empty( $propertydata ) )
        return;    
        
    // var_dump( $propertydata );
    
    // query to find out if there's already a post for this property
    $args = array(
        'post_type' => 'properties',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'relation' => 'AND',
                array(
                    array(
                        'key' => 'property_source',
                        'value' => 'yardi',
                    ),
                    array(
                        'key'   => 'voyager_property_code',
                        'value' => $voyager_id,
                    ),
                ),
            ),
        ),
    );
    
    $property_query = new WP_Query( $args );
    $properties = $property_query->posts;
    
    if ( $properties ) {
        foreach( $properties as $property ) {
            $success_update_photos = update_post_meta( $property->ID, 'property_images', $propertydata );
        }
    }
}

add_action( 'apartmentsync_do_save_property_data_to_cpt', 'apartmentsync_save_property_data_to_cpt', 10, 1 );
function apartmentsync_save_property_data_to_cpt( $property_data ) {
    
    $property_data = $property_data[0];
    
    // bail if there's no property data
    if ( !isset( $property_data['PropertyData'] ) )
        return;

    // bail if there's no voyager property code
    if ( !isset( $property_data['PropertyData']['VoyagerPropertyCode'] ) )
        return;
        
    $voyager_property_code = $property_data['PropertyData']['VoyagerPropertyCode'];
            
    // query to find out if there's already a post for this property
    $args = array(
        'post_type' => 'properties',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'relation' => 'AND',
                array(
                    array(
                        'key' => 'property_source',
                        'value' => 'yardi',
                    ),
                    array(
                        'key'   => 'voyager_property_code',
                        'value' => $voyager_property_code,
                    ),
                ),
            ),
        ),
    );
    
    $property_query = new WP_Query( $args );
    $properties = $property_query->posts;    
    $count = count( $properties );
    
    // if there's no post, then add one
    if ( $count === 0 )
        do_action( 'apartmentsync_do_insert_yardi_property', $property_data );
    
    // if there's a post, update it
    if ( $count === 1 )
        do_action( 'apartmentsync_do_update_yardi_property', $property_data );
    
    // if we somehow got multiple posts, delete them
    if ( $count > 1 ) {
        foreach( $properties as $property ) {
            wp_delete_post( $property->ID, true );
        }
    }
}

add_action( 'apartmentsync_do_insert_yardi_property', 'apartmentsync_insert_yardi_property', 10, 1 );
function apartmentsync_insert_yardi_property( $property_data ) {
            
    $title = $property_data['PropertyData']['name'];
    $address = $property_data['PropertyData']['address'];
    $city = $property_data['PropertyData']['city'];
    $state = $property_data['PropertyData']['state'];
    $zipcode = $property_data['PropertyData']['zipcode'];
    $url = $property_data['PropertyData']['url'];
    $description = $property_data['PropertyData']['description'];
    $email = $property_data['PropertyData']['email'];
    $phone = $property_data['PropertyData']['phone'];
    $latitude = $property_data['PropertyData']['Latitude'];
    $longitude = $property_data['PropertyData']['Longitude'];
    $propertycode = $property_data['PropertyData']['PropertyCode'];
    $property_id = $property_data['PropertyData']['PropertyId'];
    $voyager_property_code = $property_data['PropertyData']['VoyagerPropertyCode'];
    $property_source = 'yardi';
        
    //* bail if we don't have a title
    if ( !$title )
        return;
    
    // Create post object
    $property_meta = array(
        'post_title'  => wp_strip_all_tags( $title ),
        'post_status' => 'publish',
        'post_type'   => 'properties',
        'meta_input'  => array(
            'address'               => $address,
            'city'                  => $city,
            'state'                 => $state,
            'zipcode'               => $zipcode,
            'url'                   => $url,
            'description'           => $description,
            'email'                 => $email,
            'phone'                 => $phone,
            'latitude'              => $latitude,
            'longitude'             => $longitude,
            'property_code'         => $propertycode,
            'voyager_property_code' => $voyager_property_code,
            'property_id'           => $property_id,
            'property_source'       => $property_source,
        ),
    );
    
    // insert the post
    $post_id = wp_insert_post( $property_meta );

}

add_action( 'apartmentsync_do_update_yardi_property', 'apartmentsync_update_yardi_property', 10, 1 );
function apartmentsync_update_yardi_property( $property_data ) {
    
    $voyager_property_code = $property_data['PropertyData']['VoyagerPropertyCode'];
        
    // this function doesn't have access to the post ID it's updating, so let's get that by querying the DB
    $args = array(
        'post_type' => 'properties',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'relation' => 'AND',
                array(
                    array(
                        'key' => 'property_source',
                        'value' => 'yardi',
                    ),
                    array(
                        'key'   => 'voyager_property_code',
                        'value' => $voyager_property_code,
                    ),
                ),
            ),
        ),
    );
    
    $property_query = new WP_Query( $args );
    $properties = $property_query->posts;
    $post_id = $properties[0]->ID;
    
    
    //* Amenities
    
    // now that we have the post ID, remove existing amenities
    wp_delete_object_term_relationships( $post_id, array( 'amenities' ) );

    // get the array of amenities
    $amenities = $property_data['Amenities'];
        
    // for each of those amenities, grab the name, then add it
    foreach ( $amenities as $amenity ) {
        
        // get the name
        $name = $amenity['AmenityName'];
                
        // this function checks if the amenity exists, creates it if not, then adds it to the post
        apartmentsync_set_post_term( $post_id, $name, 'amenities' );
    }
    
    //* Pets
    if ( !isset( $property_data['PetPolicy'] ) )
        return;
        
    if ( !isset( $property_data['PetPolicy'][0] ) )
        return;
        
    if ( !isset( $property_data['PetPolicy'][0]['PetType'] ) )
        return;
    
    $pets = $property_data['PetPolicy'][0]['PetType'];
        
    $success_pets = update_post_meta( $post_id, 'pets', $pets );
    if ( $success_pets == true )
        apartmentsync_log( "Property $voyager_property_code meta updated: pets is now $pets." );
        
}