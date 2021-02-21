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
    
    apartmentsync_log( $sync_term );
    return $sync_term;
    
}