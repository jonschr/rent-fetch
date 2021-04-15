<?php

add_action( 'apartmentsync_do_single_property_images_yardi', 'apartmentsync_single_property_images_yardi', 10, 1 );
function apartmentsync_single_property_images_yardi( $property_images ) {
        
    if ( !$property_images )
        return;
        
    wp_enqueue_style( 'apartmentsync-fancybox-style' );
    wp_enqueue_script( 'apartmentsync-fancybox-script' );
            
    $number_of_images = count( $property_images );
        
    if ( $number_of_images <= 5 ) {
        
        echo '<div class="image-single">';
                    
            foreach( $property_images as $property_image ) {
                
                $title = $property_image->Title;
                $url = $property_image->ImageURL;
                $alt = $property_image->AltText;
                
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