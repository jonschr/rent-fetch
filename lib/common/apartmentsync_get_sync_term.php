<?php

/**
 * Get the sync term and return it in seconds
 */
function apartmentsync_get_sync_term_in_seconds() {
    $sync_term = get_field( 'sync_term', 'option' );
    
    if ( empty( $sync_term ) )
        $sync_term = 'paused';
        
    if ( $sync_term == 'daily' )
        $sync_term = 86400;
        
    if ( $sync_term == 'hourly' )
        $sync_term = 3600;
        
    if ( $sync_term == 'continuous' )
        $sync_term = 60;
    
    return $sync_term;
    
}

/**
 * Get the sync terms and just return it directly
 */
function apartmentsync_get_sync_term_string() {
    $sync_term = get_field( 'sync_term', 'option' );
    return $sync_term;
}

/**
 * Check whether the sync term has changed
 */
function apartmentsync_check_if_sync_term_has_changed() {
    $current_sync_term = get_field( 'sync_term', 'option' );
    $old_sync_term = get_transient( 'apartmentsync_sync_term' );
    
    if ( empty( $old_sync_term ) )
        set_transient( 'apartmentsync_sync_term', $current_sync_term, YEAR_IN_SECONDS );
        
    // if the old one and the new one don't match (it's changed), then reset the transient and return true
    if ( $current_sync_term != $old_sync_term ) {        
        delete_transient( 'apartmentsync_sync_term' );
        set_transient( 'apartmentsync_sync_term', $current_sync_term, YEAR_IN_SECONDS );
        return true;
    }
    
    // return false if it hasn't changed
    return false;
}