<?php

add_action( 'rentfetch_do_each_property', 'rentfetch_each_property', 10, 2 );
function rentfetch_each_property( $id, $floorplan ) {
    
    // properties in archive styles
    wp_enqueue_style( 'rentfetch-properties-in-archive' );
    
    // slick
    wp_enqueue_script( 'rentfetch-slick-main-script' );
    wp_enqueue_script( 'rentfetch-property-images-slider-init' );
    wp_enqueue_style( 'rentfetch-slick-main-styles' );
    wp_enqueue_style( 'rentfetch-slick-main-theme' );
    
    // vars
    $title = get_the_title( $id );
    $permalink = get_the_permalink( $id );
    $property_id = get_post_meta( $id, 'property_id', true );
    $voyager_property_code = get_post_meta( $id, 'voyager_property_code', true );
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
    $has_specials = $floorplan['property_has_specials'];
    
    $permalink = get_the_permalink( $id );    
    
    // class
    $class = get_post_class();
        
    if ( $has_specials == true )
        $class[] = 'has-specials';
                
    $class = implode( ' ', $class );
    
    // markup
    printf( '<div id="%s" class="%s" data-id="%s" data-url="%s" data-latitude="%s" data-longitude="%s">', $id, $class, $id, $permalink, $latitude, $longitude );
    
        if ( $permalink )
            printf( '<a class="overlay" href="%s"></a>', $permalink );
                                
        do_action( 'rentfetch_do_each_property_images', $id );
    
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
                             
                do_action( 'rentfetch_do_each_properties_rent_range', $floorplan );
                
                do_action( 'rentfetch_do_each_property_specials', $floorplan );
                                                    
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
        
        if ( current_user_can( 'edit_posts' ) ) {
            echo '<p class="admin-data">';
            
            if ( $voyager_property_code )
                printf( '<span><strong>Voyager Code:</strong> %s</span>', $voyager_property_code );
                
            if ( $property_id )
                printf( '<span><strong>Property ID:</strong> %s</span>', $property_id );
            
            echo '</p>';
        }
                                                
    echo '</div>';
    
}

//* add the specials markup
add_action( 'rentfetch_do_each_property_specials', 'rentfetch_each_property_specials', 10, 1 );
function rentfetch_each_property_specials( $floorplan ) {
    
    $has_specials = $floorplan['property_has_specials'];
    
    if ( $has_specials == true ) {
        $specials_text = 'Specials available';
        $specials_text = apply_filters( 'rentfetch_has_specials_text', $specials_text );
        printf( '<div class="has-specials-property">%s</div>', $specials_text );
    }
    
}

//* select our data source for images (manual images is preferred, then yardi)
add_action( 'rentfetch_do_each_property_images', 'rentfetch_each_property_images', 10, 1 );
function rentfetch_each_property_images( $post_id ) {
    
    // manually-added images
    $property_images_manual = get_post_meta( $post_id, 'images', true );
                
    // these are images pulled from an API and stored as a JSON array
    $property_images_yardi = get_post_meta( $post_id, 'property_images', true );
    $property_images_yardi = json_decode( $property_images_yardi );
                    
    if ( $property_images_manual ) {
        do_action( 'rentfetch_do_each_property_images_manual', $post_id );
    } elseif ( $property_images_yardi ) {
        do_action( 'rentfetch_do_each_property_images_yardi', $post_id );
    }
    
}

//* add markup for when we're adding images from WordPress (manual entry)
add_action( 'rentfetch_do_each_property_images_manual', 'rentfetch_each_property_images_manual', 10, 1 );
function rentfetch_each_property_images_manual( $post_id ) {
    
    // these are images pulled from an API and stored as a JSON array
    $property_images = get_post_meta( $post_id, 'images', true );
            
    if ( !$property_images )
        return;
        
    $firsturl = null;
            
    // grab the first image url for use in the map
    $firsturl = wp_get_attachment_image_url( $property_images[0], 'large' );
        
    if ( !$firsturl )
        $firsturl = apply_filters( 'rentfetch_sample_image', RENTFETCH_PATH . 'images/fallback-property.svg' );
        
    printf( '<div class="property-images-wrap" data-image-url="%s">', $firsturl );
    
        do_action( 'rentfetch_properties_archive_before_images' );
                
        echo '<div class="property-slider">';
        
            if ( $property_images ) {
                
                // limit this location to 3 images
                $property_images = array_slice( $property_images, 0, 3 );
                            
                foreach( $property_images as $property_image ) {
                            
                    $url = wp_get_attachment_image_url( $property_image, 'large' );
                    $alt = get_post_meta($property_image, '_wp_attachment_image_alt', TRUE);
                    $title = get_the_title($property_image);
                                        
                    echo '<div class="property-slide">';
                        printf( '<img loading=lazy src="%s" alt="%s" title="%s" />', $url, $alt, $title );
                    echo '</div>';
                                    
                }
                
            } else {
                
                $property_image = RENTFETCH_PATH . 'images/fallback-property.svg';
        
                echo '<div class="property-slide">';
                    printf( '<img loading=lazy src="%s" />', $property_image );
                echo '</div>';
            }
                    
            
        
        echo '</div>';
        
        do_action( 'rentfetch_properties_archive_after_images' );
                
    echo '</div>';
    
}

//* add markup for when we're adding images from yardi
add_action( 'rentfetch_do_each_property_images_yardi', 'rentfetch_each_property_images_yardi', 10, 1 );
function rentfetch_each_property_images_yardi( $post_id ) {
    
    // these are images pulled from an API and stored as a JSON array
    $property_images = get_post_meta( $post_id, 'property_images', true );
    $property_images = json_decode( $property_images );
        
    if ( !$property_images )
        return;
        
    $firsturl = null;
        
    // grab the first image url for use in the map
    if ( isset( $property_images[0]->ImageURL ) )
        $firsturl = $property_images[0]->ImageURL;
    
    if ( !$firsturl )
        $firsturl = apply_filters( 'rentfetch_sample_image', RENTFETCH_PATH . 'images/fallback-property.svg' );
        
    printf( '<div class="property-images-wrap" data-image-url="%s">', $firsturl );
    
        do_action( 'rentfetch_properties_archive_before_images' );
                
        echo '<div class="property-slider">';
        
            if ( $property_images ) {
                
                $property_images = array_slice( $property_images, 0, 3 );
            
                foreach( $property_images as $property_image ) {
                            
                    $title = null;
                    if ( isset( $property_image->Title ) )
                        $title = $property_image->Title;
                    
                    $url = null;
                    if ( isset( $property_image->ImageURL ) )
                        $url = $property_image->ImageURL;
                        
                    // if we don't have an image, use a placeholder
                    if ( empty( $url ) )
                        $url = RENTFETCH_PATH . 'images/fallback-property.svg';
                                            
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
                                    
                }
                
            } else {
                
                $property_image = RENTFETCH_PATH . 'images/fallback-property.svg';
        
                echo '<div class="property-slide">';
                    printf( '<img loading=lazy src="%s" />', $property_image );
                echo '</div>';
            }
                    
            
        
        echo '</div>';
        
        do_action( 'rentfetch_properties_archive_after_images' );
                
    echo '</div>';
}

//* favorite link
add_action( 'rentfetch_properties_archive_before_images', 'rentfetch_favorite_property_link' );
function rentfetch_favorite_property_link() {
    
    wp_enqueue_script( 'rentfetch-property-favorites-cookies' );
    wp_enqueue_script( 'rentfetch-property-favorites' );
    
    $post_id = get_the_ID();
    
    if ( $post_id )
        printf( '<a href="#" class="favorite-heart" data-property-id="%s" data-favorite="1"></a>', $post_id );
        
} 

//* rent range figuring out how to display it
add_action( 'rentfetch_do_each_properties_rent_range', 'rentfetch_each_properties_rent_range', 10, 1 );
function rentfetch_each_properties_rent_range( $floorplan ) {
    
    $rent_range_display_type = get_field( 'property_pricing_display', 'option' );
    
    if ( $rent_range_display_type == 'range' || ( !$rent_range_display_type ) ) {
        // if there's no option set or if it's set to range...
        do_action( 'rentfetch_do_each_properties_rent_range_display_as_range', $floorplan );        
    } elseif ( $rent_range_display_type == 'minimum' ) {
        // if it's set to minimum...
        do_action( 'rentfetch_do_each_properties_rent_range_display_as_minimum', $floorplan );
    } 
}

//* rent range (displaying as range)
add_action( 'rentfetch_do_each_properties_rent_range_display_as_range', 'rentfetch_each_properties_rent_range_display_as_range' );
function rentfetch_each_properties_rent_range_display_as_range( $floorplan ) {
    
    $rentrange = $floorplan['rentrange'];
    
    if ( $rentrange )
        printf( '<span class="rentrange">%s</span>', $rentrange );
}

//* rent range (displaying as minimum)
add_action( 'rentfetch_do_each_properties_rent_range_display_as_minimum', 'rentfetch_each_properties_rent_range_display_as_minimum' );
function rentfetch_each_properties_rent_range_display_as_minimum( $floorplan ) {
    
    $minimums = $floorplan['minimum_rent'];
    $maximums = $floorplan['maximum_rent'];
        
    $rents = array_merge( $minimums, $maximums );
    
    $rent = min( $rents );
    
    if ( $rent > 100 )
        printf( '<span class="rentrange">From $%s</span>', $rent );
}