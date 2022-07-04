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
add_filter( 'rentfetch_filter_property_permalink', 'rentfetch_filter_property_permalink', 10, 1 );
function rentfetch_filter_property_permalink( $url ) {
    global $post;
    
    $permalink = get_the_permalink( get_the_ID() );
    $url = get_post_meta( get_the_ID(), 'url', true );
    
    if ( $url ) {
        return esc_url( $url );
    } else {
        return $permalink;
    }
    
}

/**
 * Filter the property URL target
 */
add_filter( 'rentfetch_filter_property_permalink_target', 'rentfetch_filter_property_permalink_target', 10, 1 );
function rentfetch_filter_property_permalink_target( $target ) {
    
    global $post;
    
    $url = get_post_meta( get_the_ID(), 'url', true );
    
    if ( $url ) {
        return '_blank';
    } else {
        return '_self';
    }
    
}