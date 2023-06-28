<?php

function rentfetch_get_property_images( $args = null ) {
    global $post;
    
    // bail if this isn't a property
    if ($post->post_type != 'properties' )
        return;
    
    $manual_images = rentfetch_get_property_images_manual( $args );
    $yardi_images = rentfetch_get_property_images_yardi( $args );
    $fallback_images = rentfetch_get_property_images_fallback( $args );
        
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

function rentfetch_get_property_images_manual( $args ) {
    global $post;
    
    if ( isset( $args['size'] ) ) {
        $size = $args['size'];
    } else {
        $size = 'large';
    }
    
    $manual_image_ids = get_post_meta( get_the_ID(), 'images', true );
                
    // bail if we don't have any
    if ( !$manual_image_ids )
        return;
        
    // bail if we only have one empty string in the manual images array
    if (count($manual_image_ids) === 1 && $manual_image_ids[0] === "" )
        return;
                        
    $manual_images = array();
        
    foreach ( $manual_image_ids as $manual_image_id ) {
        
        $manual_images[] = [
            'url' => wp_get_attachment_image_url( $manual_image_id, $size ),
            'title' => get_the_title( $manual_image_id ),
            'alt' => get_post_meta( $manual_image_id, '_wp_attachment_image_alt', true ),
            'caption' => get_the_excerpt( $manual_image_id ),
        ];
    }
    
    return $manual_images;
    
}

function rentfetch_get_property_images_yardi( $args ) {
    global $post; 
            
    $yardi_images_string = get_post_meta( get_the_ID(), 'property_images', true );
    
    // bail if there's no yardi images
    if ( !$yardi_images_string )
        return;
        
    // rarely, an error might get saved here. if so, bail.
    if ( $yardi_images_string == '[{"Error":"1050"}]' )
        return;
        
    $yardi_images_json = json_decode( $yardi_images_string );
    $yardi_images = array();
    
    foreach( $yardi_images_json as $yardi_image_json ) {
        $yardi_images[] = [
            'url' => esc_url( $yardi_image_json->ImageURL ),
            'title' => $yardi_image_json->Title,
            'alt' => $yardi_image_json->AltText,
            'caption' => $yardi_image_json->Caption
        ];
    }
    
    return $yardi_images;
    
}

function rentfetch_get_property_images_fallback( $args ) {
    
    $fallback_images[] = [
        'url' => apply_filters( 'rentfetch_sample_image', RENTFETCH_PATH . 'images/fallback-property.svg' ),
        'title' => 'Sample image',
        'alt' => 'Sample image',
        'caption' => null,
    ];
    
    return $fallback_images;
}