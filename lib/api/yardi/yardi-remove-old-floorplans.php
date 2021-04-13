<?php

add_action( 'apartmentsync_do_remove_old_data', 'apartmentsync_do_remove_floorplans_from_orphan_yardi_properties' );
function apartmentsync_do_remove_floorplans_from_orphan_yardi_properties() {
            
    $sync_term = get_field( 'sync_term', 'option' );
    $data_sync = get_field( 'data_sync', 'option' );
    
    // if syncing is paused or data dync is off, then then bail, as we won't be restarting anything
    if ( $sync_term == 'paused' || $data_sync == 'delete' || $data_sync == 'nosync' ) {
        as_unschedule_action( 'apartmentsync_do_remove_floorplans_from_orphan_yardi_properties_specific', array(), 'yardi' );
        as_unschedule_all_actions( 'apartmentsync_do_remove_floorplans_from_orphan_yardi_properties_specific', array(), 'yardi' );
        return;
    }
    
    if ( as_next_scheduled_action( 'apartmentsync_do_remove_floorplans_from_orphan_yardi_properties_specific', array(), 'yardi' ) == false ) {
        apartmentsync_verbose_log( "Scheduling regular task to remove floorplans from properties that are no longer set to sync." );
        as_schedule_recurring_action( time(), apartmentsync_get_sync_term_in_seconds(), 'apartmentsync_do_remove_floorplans_from_orphan_yardi_properties_specific', array(), 'yardi' );
    }
 
}

function apartentsync_get_meta_values( $key = '', $type = 'post', $status = 'publish' ) {

    global $wpdb;

    if( empty( $key ) )
        return;

    $r = $wpdb->get_col( $wpdb->prepare( "
        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key = %s 
        AND p.post_status = %s 
        AND p.post_type = %s
    ", $key, $status, $type ) );

    return $r;
}

// //* TEMP activation of this function
// add_action( 'init', 'apartmentsync_remove_floorplans_from_orphan_yardi_properties_specific' );

add_action( 'apartmentsync_do_remove_floorplans_from_orphan_yardi_properties_specific', 'apartmentsync_remove_floorplans_from_orphan_yardi_properties_specific' );
function apartmentsync_remove_floorplans_from_orphan_yardi_properties_specific() {
    
    // get the property ids which exist in our floorplans CPT 'property_id' meta field
    $property_ids_attached_to_floorplans = apartentsync_get_meta_values( 'voyager_property_code', 'floorplans' );
    $property_ids_attached_to_floorplans = array_unique( $property_ids_attached_to_floorplans );
    
    // get the property ids from the setting
    $yardi_integration_creds = get_field( 'yardi_integration_creds', 'option' );
    $yardi_api_key = $yardi_integration_creds['yardi_api_key'];
    $properties_in_setting = $yardi_integration_creds['yardi_property_code'];
    $properties_in_setting = explode( ',', $properties_in_setting );
    
    // get the ones that are in floorplans, but that aren't in the setting
    $properties = array_diff( $property_ids_attached_to_floorplans, $properties_in_setting );
    
    if ( $properties == null )
        return;
    
    // for each property that's in the DB but not in our list, do a query for floorplans that correspond, then delete those
    foreach( $properties as $property ) {
        
        // remove upcoming actions for pulling floorplans from the API
        apartmentsync_verbose_log( "Property $property found in published floorplans, but not found in setting. Removing upcoming api actions." );
        as_unschedule_action( 'do_get_yardi_floorplans_from_api_for_property', array( $property, $yardi_api_key ), 'yardi' );
        as_unschedule_all_actions( 'do_get_yardi_floorplans_from_api_for_property', array( $property, $yardi_api_key ), 'yardi' );
        
        // remove upcoming actions for syncing floorplans
        apartmentsync_verbose_log( "Property $property found in published floorplans, but not found in setting. Removing upcoming CPT update actions." );
        as_unschedule_action( 'apartmentsync_do_fetch_yardi_floorplans', array( $property ), 'yardi' );
        as_unschedule_all_actions( 'apartmentsync_do_fetch_yardi_floorplans', array( $property ), 'yardi' );
        
        $args = array(
            'post_type' => 'floorplans',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'relation' => 'AND',
                    array(
                        'key' => 'floorplan_source',
                        'value' => 'yardi',
                    ),
                    array(
                        'key'   => 'voyager_property_code',
                        'value' => $property,
                    ),
                ),
            ),
        );
        
        $floorplan_query = new WP_Query($args);
        $floorplanstodelete = $floorplan_query->posts;
        
        foreach ($floorplanstodelete as $floorplantodelete) {
            apartmentsync_verbose_log( "Deleting floorplan $floorplantodelete->ID." );
            wp_delete_post( $floorplantodelete->ID, true );
        }
                
    }
    
}
