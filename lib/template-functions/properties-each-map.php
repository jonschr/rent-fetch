<?php

add_action( 'rentfetch_do_properties_each_map', 'rentfetch_properties_each_map' );
function rentfetch_properties_each_map() {
    
    $title = rentfetch_get_property_title();
    $property_location = rentfetch_get_property_location();
    $bedrooms = rentfetch_get_property_bedrooms();
    $bathrooms = rentfetch_get_property_bathrooms();
    $square_feet = rentfetch_get_property_square_feet();
    $rent = rentfetch_get_property_rent();
    
    do_action( 'rentfetch_do_property_images' );
    
    if ( $title )
        printf( '<h3>%s</h3>', $title );
    
    if ( $property_location )
        printf( '<p class="property-location">%s</p>', esc_html( $property_location ) );
        
    if ( $bedrooms )
        printf( '<p class="bedsrange">%s</p>', esc_html( $bedrooms ) );
        
    if ( $bathrooms )
        printf( '<p class="bathsrange">%s</p>', esc_html( $bathrooms ) );
        
    if ( $square_feet )
        printf( '<p class="square-feet">%s</p>', esc_html( $square_feet ) );
        
    if ( $rent )
        printf( '<p class="rent">%s</p>', esc_html( $rent ) );
        
    edit_post_link();
    
}
