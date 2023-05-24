<?php

add_action( 'wp_footer', 'rentfetch_get_property_images' );
function rentfetch_get_property_images() {
    global $post;
    
    $yardi_images = rentfetch_get_property_images_yardi();
    $manual_images = rentfetch_get_property_images_manual();
    
    var_dump( $manual_images );
    
}

function rentfetch_get_property_images_yardi() {
    global $post; 
        
    $yardi_images = get_post_meta( get_the_ID(), 'property_images', true );
    
    if ( !$yardi_images )
        return;
        
    $yardi_images = json_decode( $yardi_images );
    
    return $yardi_images;
    
}

function rentfetch_get_property_images_manual() {
    global $post;
    
    $manual_image_ids = get_post_meta( get_the_ID(), 'images', true );
    
    // bail if we don't have any
    if ( !$manual_image_ids )
        return;
        
    $manual_images = array();
        
    foreach ( $manual_image_ids as $manual_image_id ) {
        $url = wp_get_attachment_image_url($manual_image_id, 'large' );
        
        $manual_images[] = $url;
    }
    
    return $manual_images;
    
}