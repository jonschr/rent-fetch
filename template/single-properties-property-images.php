<?php

//* If the images come from Yardi
add_action( 'rentfetch_do_single_property_images_yardi', 'rentfetch_single_property_images_yardi', 10, 1 );
function rentfetch_single_property_images_yardi() {
    
    global $post;
    $id = get_the_ID();
    
    // these are images pulled from an API and stored as a JSON array
    $property_images = get_post_meta( $id, 'property_images', true );
    $property_images = json_decode( $property_images );
        
    if ( !$property_images )
        return;
        
    wp_enqueue_style( 'rentfetch-fancybox-style' );
    wp_enqueue_script( 'rentfetch-fancybox-script' );
            
    // bail if this is an object (errors will return objects here)
    if ( is_object( $property_images ) )
        return;
    
    $number_of_images = count( $property_images );
        
    if ( $number_of_images < 5 ) {
        
        echo '<div class="image-single">';
                            
            foreach( $property_images as $property_image ) {
                
                $title = $property_image->Title;
                $url = $property_image->ImageURL;
                $alt = $property_image->AltText;
                                
                // detect if there are special characters
                $regex = preg_match('[@_!#$%^&*()<>?/|}{~:]', $url);
                                    
                // bail on this slide if there are special characters in the image url
                if ( $regex )
                    break;
                    
                // bail if there is no image src
                if ( !$url )
                    break;
                
                printf( '<a data-fancybox="single-properties" href="%s"><img src="%s" alt="%s" title="%s" /></a>', $url, $url, $alt, $title );
                                
            }
        
            if ( $number_of_images > 1 )
                printf( '<a data-fancybox-trigger="single-properties" class="viewall" href="#">View %s images</a>', $number_of_images );
        
        echo '</div>';
        
    } else {
                
        echo '<div class="image-grid">';
             
            foreach( $property_images as $property_image ) {
                
                $title = $property_image->Title;
                $url = $property_image->ImageURL;
                $alt = $property_image->AltText;
                
                echo '<div class="image-grid-each">';
                    printf( '<a data-fancybox="single-properties" href="%s"><img src="%s" alt="%s" title="%s" /></a>', $url, $url, $alt, $title );
                echo '</div>';
                
            }
            
            if ( $number_of_images > 1 )
                printf( '<a data-fancybox-trigger="single-properties" class="viewall" href="#">View %s images</a>', $number_of_images );
            
        echo '</div>';
        
    }
}

//* If the images are manual
add_action( 'rentfetch_do_single_property_images_manual', 'rentfetch_single_property_images_manual', 10, 1 );
function rentfetch_single_property_images_manual() {
    
    global $post;
    $id = get_the_ID();
    
    // manually-added images
    $property_images = get_post_meta( $id, 'images', true );

    if ( !$property_images )
        return;
        
    wp_enqueue_style( 'rentfetch-fancybox-style' );
    wp_enqueue_script( 'rentfetch-fancybox-script' );
                
    $number_of_images = count( $property_images );
        
    if ( $number_of_images < 5 ) {
        
        echo '<div class="image-single">';
                            
            foreach( $property_images as $property_image ) {
                
                $url = wp_get_attachment_image_url( $property_image, 'large' );
                $alt = get_post_meta($property_image, '_wp_attachment_image_alt', TRUE);
                $title = get_the_title($property_image);
                    
                // bail if there is no image src
                if ( !$url )
                    break;
                
                printf( '<a data-fancybox="single-properties" href="%s"><img src="%s" alt="%s" title="%s" /></a>', $url, $url, $alt, $title );
                                
            }
        
            if ( $number_of_images > 1 )
                printf( '<a data-fancybox-trigger="single-properties" class="viewall" href="#">View %s images</a>', $number_of_images );
        
        echo '</div>';
        
    } else {
                
        echo '<div class="image-grid">';
             
            foreach( $property_images as $property_image ) {
                
                $url = wp_get_attachment_image_url( $property_image, 'large' );
                $alt = get_post_meta($property_image, '_wp_attachment_image_alt', TRUE);
                $title = get_the_title($property_image);
                                        
                echo '<div class="image-grid-each">';
                    printf( '<a data-fancybox="single-properties" href="%s"><img src="%s" alt="%s" title="%s" /></a>', $url, $url, $alt, $title );
                echo '</div>';
                
            }
            
            if ( $number_of_images > 1 )
                printf( '<a data-fancybox-trigger="single-properties" class="viewall" href="#">View %s images</a>', $number_of_images );
            
        echo '</div>';
        
    }
    
}