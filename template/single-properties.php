<?php

//* Add a body class
add_filter( 'body_class', 'apartmentsync_add_properties_body_class' );
function apartmentsync_add_properties_body_class( $classes ) {
    global $post;
    
    if ( isset( $post ) )
        $classes[] = 'single-properties-template';
    
    return $classes;
    
}

////////////
// MARKUP //
////////////

get_header();

//* Markup
echo '<div class="single-properties-wrap">';

    // $property_components = get_option( 'options_single_property_components' );    
    // var_dump( $property_components );

    echo '<div id="images" class="single-properties-section no-padding full-width">';
        echo '<div class="wrap">';
        
        rentfetch_property_images_grid();
        
        echo '</div>';
    echo '</div>';
    
    echo '<div id="subnav" class="single-properties-section no-padding">';
        echo '<div class="wrap">';
        
        wp_enqueue_script( 'properties-single-collapse-subnav' );
        
        echo '<a class="toggle-subnav" href="#">Quick links <span class="dashicons dashicons-arrow-down-alt2"></span></a>';
        
            echo '<ul class="subnav">';
            
                $property_components = get_option( 'options_single_property_components' );
                
                if ( is_array( $property_components ) ) {
                    
                    foreach ( $property_components as $component ) {
                        
                        if ( $component == 'property_images' ) {
                            $label = apply_filters( 'rentfetch_property_images_subnav_label', 'Images' );
                            printf( '<li><a href="#images">%s</a></li>', $label );
                        }
                        
                        if ( $component == 'property_details' ) {
                            $label = apply_filters( 'rentfetch_property_details_subnav_label', 'Details' );
                            printf( '<li><a href="#images">%s</a></li>', $label );
                        }
                                        
                        if ( $component == 'floorplans_display' ) {
                            $label = apply_filters( 'rentfetch_floorplans_display_subnav_label', 'Floorplans' );
                            printf( '<li><a href="#floorplans">%s</a></li>', $label );
                        }
                        
                        if ( $component == 'amenities_display' ) {
                            $label = apply_filters( 'rentfetch_amenities_display_subnav_label', 'Amenities' );
                            printf( '<li><a href="#amenities">%s</a></li>', $label );
                        }
                        
                        if ( $component == 'property_map' ) {
                            $label = apply_filters( 'rentfetch_property_map_subnav_label', 'Map' );
                            printf( '<li><a href="#googlemaps">%s</a></li>', $label );
                        }
                        
                        if ( $component == 'nearby_properties' ) {
                            $label = apply_filters( 'rentfetch_nearby_properties_subnav_label', 'Nearby Properties' );
                            printf( '<li><a href="#moreproperties">%s</a></li>', $label );
                        }
                                        
                    }
                    
                }
            
            echo '</ul>';
            
        echo '</div>';
    echo '</div>';
    
    echo '<div id="details" class="single-properties-section">';
        echo '<div class="wrap">';
                
            $location = rentfetch_get_property_location();
            $title = rentfetch_get_property_title();
            
            if ( $title )
                printf( '<h1 class="title">%s</h1>', $title );
                
            if ( $location )
                printf( '<p class="location">%s</p>', $location );
                
            echo '<div class="buttons-details">';
            
                echo '<div class="buttons-wrap">';
                
                
            
                echo '</div>';
                
                echo '<div class="details-wrap">';
                
                echo '</div>';
            
            echo '</div>';
        
        echo '</div>';
    echo '</div>';
    
    echo '<div id="floorplans" class="single-properties-section">';
        echo '<div class="wrap">';
        
        echo '<h2>Floorplans</h2>';
        
        echo '</div>';
    echo '</div>';
    
    echo '<div id="amenities" class="single-properties-section">';
        echo '<div class="wrap">';
        
        echo '<h2>Amenities</h2>';
        
        echo '</div>';
    echo '</div>';
    
    echo '<div id="googlemaps" class="single-properties-section no-padding full-width">';
        echo '<div class="wrap">';
        
            $id = esc_attr( get_the_ID() );

            $latitude = floatval( get_post_meta( $id, 'latitude', true ) );
            $longitude = floatval( get_post_meta( $id, 'longitude', true ) );
            
            //* bail if there's not a lat or longitude
            if ( empty( $latitude ) || empty( $longitude) )
                return;
                
            $title = esc_attr( rentfetch_get_property_title() );
            $phone = esc_attr( rentfetch_get_property_phone() );
            $location = esc_attr( rentfetch_get_property_location() );
            
            $content = sprintf( '<div class="map-marker"><p class="title">%s</p><p class="location">%s</p></div>', $title, $location );
            $content = esc_attr( apply_filters( 'rentfetch_property_single_map_marker_content', $content ) );
            
            // the map itself
            $key = apply_filters( 'rentfetch_get_google_maps_api_key', null );
            wp_enqueue_script( 'rentfetch-google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $key, array(), null, true );

            // Localize the google maps script, then enqueue that
            $maps_options = array(
                'json_style' => json_decode( get_option( 'options_google_maps_styles' ) ),
                'marker_url' => get_option( 'options_google_map_marker' ),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'content' => $content,
            );
            wp_localize_script( 'rentfetch-single-property-map', 'options', $maps_options );
            wp_enqueue_script( 'rentfetch-single-property-map');
            
            echo '<div id="single-property-map"></div>';
        
        echo '</div>';
    echo '</div>';
    
    echo '<div id="moreproperties" class="single-properties-section">';
        echo '<div class="wrap">';
        
        echo '<h2>More Properties</h2>';
        
        echo '</div>';
    echo '</div>';
    
    
    
    // do_action( 'rentfetch_do_single_properties' );
    
    
    
echo '</div>'; // .single-properties-wrap

get_footer();