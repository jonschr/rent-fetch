<?php

function rentfetch_get_property_images() {
    global $post;
    
    // bail if this isn't a property
    if ($post->post_type != 'properties' )
        return;
    
    $manual_images = rentfetch_get_property_images_manual();
    $yardi_images = rentfetch_get_property_images_yardi();
    $fallback_images = rentfetch_get_property_images_fallback();
    
    if ( $manual_images ) {
        return $manual_images;
    } elseif ( $yardi_images ) {
        return $yardi_images;
    } elseif ( $fallback_images) {
        return $fallback_images;
    } else {
        return;
    } 
}

function rentfetch_get_property_images_yardi() {
    global $post; 
        
    $yardi_images_string = get_post_meta( get_the_ID(), 'property_images', true );
    
    if ( !$yardi_images_string )
        return;
        
    $yardi_images_json = json_decode( $yardi_images_string );
    $yardi_images = array();
    
    foreach( $yardi_images_json as $yardi_image_json ) {
        $yardi_images[] = [
            'url' => $yardi_image_json->ImageURL,
            'title' => $yardi_image_json->Title,
            'alt' => $yardi_image_json->AltText,
            'caption' => $yardi_image_json->Caption
        ];
    }
    
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
        
        $manual_images[] = [
            'url' => wp_get_attachment_image_url($manual_image_id, 'large' ),
            'title' => get_the_title( $manual_image_id ),
            'alt' => get_post_meta( $manual_image_id, '_wp_attachment_image_alt', true ),
            'caption' => get_the_excerpt( $manual_image_id ),
        ];
    }
    
    return $manual_images;
    
}

function rentfetch_get_property_images_fallback() {
    
    $fallback_images[] = [
        'url' => apply_filters( 'rentfetch_sample_image', RENTFETCH_PATH . 'images/fallback-property.svg' ),
    ];
    
    return $fallback_images;
}