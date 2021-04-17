<?php

add_action( 'apartmentsync_do_each_property', 'apartmentsync_each_property', 10, 2 );
function apartmentsync_each_property( $post, $floorplan ) {
    
    // properties in archive styles
    wp_enqueue_style( 'apartmentsync-properties-in-archive' );
    
    // slick
    wp_enqueue_script( 'apartmentsync-slick-main-script' );
    wp_enqueue_script( 'apartmentsync-property-images-slider-init' );
    wp_enqueue_style( 'apartmentsync-slick-main-styles' );
    wp_enqueue_style( 'apartmentsync-slick-main-theme' );
    
    // vars
    $id = $post->ID;
    $title = $post->post_title;
    $permalink = get_the_permalink();
    $property_id = get_post_meta( $id, 'property_id', true );
    $address = get_post_meta( $id, 'address', true );
    $city = get_post_meta( $id, 'city', true );
    $state = get_post_meta( $id, 'state', true );
    $zipcode = get_post_meta( $id, 'zipcode', true );
    $latitude = get_post_meta( $id, 'latitude', true );
    $longitude = get_post_meta( $id, 'longitude', true );
    $bedsrange = $floorplan['bedsrange'];
    $bathsrange = $floorplan['bathsrange'];    
    $rentrange = $floorplan['rentrange'];
    $sqftrange = $floorplan['sqftrange'];
    $permalink = get_the_permalink( $id );
    
    // class
    $class = get_post_class();
    $class = implode( ' ', $class );
    
    // markup
    printf( '<div id="%s" class="%s" data-id="%s" data-url="%s" data-latitude="%s" data-longitude="%s">', $id, $class, $id, $permalink, $latitude, $longitude );
    
        if ( $permalink )
            printf( '<a class="overlay" href="%s"></a>', $permalink );
            
        do_action( 'apartmentsync_do_each_property_images', $id );
    
        echo '<div class="property-content">';
        
            echo '<div class="property-info">';
    
                if ( $title )
                    printf( '<h3>%s</h3>', $title );
                                                        
                echo '<p class="the-address">';
                
                    if ( $address )
                        printf( '<span class="address">%s</span>', $address );
                    
                    if ( $city )
                        printf( '<span class="city">%s</span>', $city );
                        
                    if ( $state )
                        printf( '<span class="state">%s</span>', $state );
                        
                    if ( $zipcode )
                        printf( '<span class="zipcode">%s</span>', $zipcode );
                    
                echo '</p>';
                                
                if ( $rentrange )
                    printf( '<span class="rentrange">%s</span>', $rentrange );
                
            echo '</div>';
            echo '<div class="floorplan-info">';
            
                if ( $bedsrange !== null )
                    printf( '<span class="bedsrange">%s</span>', $bedsrange );
                    
                if ( $bathsrange !== null )
                    printf( '<span class="bathsrange">%s</span>', $bathsrange );
                    
                if ( $sqftrange !== null )
                    printf( '<span class="sqftrange">%s</span>', $sqftrange );
            
            echo '</div>';
        echo '</div>';
                                        
    echo '</div>';
    
    
    
}

add_action( 'apartmentsync_do_each_property_images', 'apartmentsync_each_property_images', 10, 1 );
function apartmentsync_each_property_images( $post_id ) {
        
    // these are images pulled from an API and stored as a JSON array
    $property_images = get_post_meta( $post_id, 'property_images', true );
    $property_images = json_decode( $property_images );
        
    if ( !$property_images )
        return;
        
    // grab the first image url for use in the map
    $firsturl = $property_images[0]->ImageURL;
    
    if ( !$firsturl )
        $firsturl = apply_filters( 'apartmentsync_sample_image', $image );
        
    printf( '<div class="property-images-wrap" data-image-url="%s">', $firsturl );
        echo '<div class="property-slider">';
        
            $count = 1;
            
            
            foreach( $property_images as $property_image ) {
                                
                // max 3 slides
                if ( $count > 3 )
                    break;
                          
                $title = null;
                if ( isset( $property_image->Title ) )
                    $title = $property_image->Title;
                
                $url = null;
                if ( isset( $property_image->ImageURL ) )
                    $url = $property_image->ImageURL;
                
                $alt = null;
                if ( isset( $property_image->AltText ) )
                    $alt = $property_image->AltText;
                    
                // bail if there is no image src
                if ( $url == null )
                    break;
                                
                // detect if there are special characters
                $regex = preg_match('[@_!#$%^&*()<>?/|}{~:]', $url);
                                    
                // bail on this slide if there are special characters in the image url
                if ( $regex )
                    break;
                                    
                echo '<div class="property-slide">';
                    printf( '<img loading=lazy src="%s" alt="%s" title="%s" />', $url, $alt, $title );
                echo '</div>';
                
                $count++;
                
            }
        
        echo '</div>';
                
    echo '</div>';
    
}