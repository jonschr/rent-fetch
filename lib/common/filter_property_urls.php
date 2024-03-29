<?php

//* Filter for URLs for a property (used on the single properties template)
add_filter( 'rentfetch_filter_property_url', 'rentfetch_add_prefix_to_property_url', 10, 1 );
function rentfetch_add_prefix_to_property_url( $url ) {
    
    // force http:// to be added to the URL if it's missing
    if (strpos($url,'http') === false)
        $url = 'http://'.$url;
        
    return $url;
    
}

/**
 * Filter the property permalink
 */
add_filter( 'rentfetch_property_archives_filter_property_permalink', 'rentfetch_property_archives_filter_property_permalink', 10, 1 );
function rentfetch_property_archives_filter_property_permalink( $url ) {
    
    global $post;
    
    $permalink = get_the_permalink( get_the_ID() );
    $url = get_post_meta( get_the_ID(), 'url', true );
    $use_individual_properties_template = get_field( 'use_individual_properties_template', 'option' );
        
    if ( $use_individual_properties_template !== false ) {
        
        // just return the permalink
        return $permalink;
        
    } else {
        
        // if we're not using the permalink, then use the URL if one exists, otherwise nothing
        if ( $url ) {
            return esc_url( $url );
        } else {
            return null;
        }
        
    }
}

/**
 * Filter the property URL target
 */
add_filter( 'rentfetch_property_archives_filter_property_permalink_target', 'rentfetch_property_archives_filter_property_permalink_target', 10, 1 );
function rentfetch_property_archives_filter_property_permalink_target( $target ) {
    
    global $post;
    
    $url = get_post_meta( get_the_ID(), 'url', true );
    $use_individual_properties_template = get_field( 'use_individual_properties_template', 'option' );
    
    if ( $use_individual_properties_template !== false ) {
        
        // if we're using the permalink, then open in the same tab
        return '_self';
        
    } else {
        
        // if we're not using the permalink
        if ( $url ) {
            return '_blank';
        } else {
            return '_self';
        }
        
    }
    
   
    
}