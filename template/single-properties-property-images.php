<?php

add_action( 'apartmentsync_do_single_property_images_yardi', 'apartmentsync_single_property_images_yardi', 10, 1 );
function apartmentsync_single_property_images_yardi( $property_images ) {
        
    if ( !$property_images )
        return;
        
    wp_enqueue_style( 'apartmentsync-fancybox-style' );
    wp_enqueue_script( 'apartmentsync-fancybox-script' );
            
    // bail if this is an object (errors will return objects here)
    if ( is_object( $property_images ) )
        return;
    
    $number_of_images = count( $property_images );
        
    if ( $number_of_images <= 5 ) {
        
        echo '<div class="image-single">';
        
            $count = 1;
                    
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
                
                $count++;
                
            }
        
            if ( $number_of_images > 1 )
                printf( '<a data-fancybox-trigger="single-properties" class="viewall" href="#">View %s images</a>', $count );
        
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