<?php

//* PROPERTY TITLE

function rentfetch_get_property_title() {
    $title = apply_filters( 'rentfetch_filter_property_title', get_the_title() );
    return esc_html( $title );
}

function rentfetch_property_title() {
    $title = rentfetch_get_property_title();
    if ( $title )
        echo $title;
}

// add_filter( 'rentfetch_filter_property_title', 'my_custom_title', 10, 1 );
// function my_custom_title( $title ) {
//     return 'My Custom Title';
// }

//* PROPERTY LOCATION

function rentfetch_get_property_location() {
        
    $address = get_post_meta( get_the_ID(), 'address', true );
    $city = get_post_meta( get_the_ID(), 'city', true );
    $state = get_post_meta( get_the_ID(), 'state', true );
    $zipcode = get_post_meta( get_the_ID(), 'zipcode', true );
    
    $location = '';

    // Concatenate address components with commas and spaces
    if (!empty($address)) {
        $location .= $address;
    }

    if (!empty($city)) {
        if (!empty($location)) {
            $location .= ', ';
        }
        $location .= $city;
    }

    if (!empty($state)) {
        if (!empty($location)) {
            $location .= ', ';
        }
        $location .= $state;
    }

    if (!empty($zipcode)) {
        if (!empty($location)) {
            $location .= ' ';
        }
        $location .= $zipcode;
    }
    
    $location = apply_filters( 'rentfetch_filter_property_location', $location );
    return esc_html( $location );
    
}

function rentfetch_property_location() {
    $location = rentfetch_get_property_location();
    
    if ( $location )
        echo $location;
}

// add_filter( 'rentfetch_filter_property_location', 'my_custom_location', 10, 1 );
// function my_custom_location( $location ) {
//     return 'My Custom Location';
// }

//* PROPERTY BEDROOMS

function rentfetch_get_property_bedrooms() {
    
    $property_id = esc_html( get_post_meta( get_the_ID(), 'property_id', true ) );
    
    $floorplan_data = rentfetch_get_floorplans( $property_id );
    $bedrooms = apply_filters( 'rentfetch_filter_property_bedrooms', $floorplan_data['bedsrange'] );
    return esc_html( $bedrooms );
}

function rentfetch_property_bedrooms() {
    $bedrooms = rentfetch_get_property_bedrooms();
    
    if ( $bedrooms )
        echo $bedrooms;
}

add_filter( 'rentfetch_filter_property_bedrooms', 'rentfetch_default_property_bedrooms_label', 10, 1 );
function rentfetch_default_property_bedrooms_label( $bedrooms ) {
    return $bedrooms . ' Bed';
}

//* PROPERTY BATHROOMS

function rentfetch_get_property_bathrooms() {
    
    $property_id = esc_html( get_post_meta( get_the_ID(), 'property_id', true ) );
    
    $floorplan_data = rentfetch_get_floorplans( $property_id );
    $bathrooms = apply_filters( 'rentfetch_filter_property_bathrooms', $floorplan_data['bathsrange'] );
    return esc_html( $bathrooms );
    
}

function rentfetch_property_bathrooms() {
    $bathrooms = rentfetch_get_property_bathrooms();
    
    if ( $bathrooms )
        echo $bathrooms;
}

add_filter( 'rentfetch_filter_property_bathrooms', 'rentfetch_default_property_bathrooms_label', 10, 1 );
function rentfetch_default_property_bathrooms_label( $bathrooms ) {
    return $bathrooms . ' Bath';
}

//* PROPERTY SQUARE FEET

function rentfetch_get_property_square_feet() {
    $property_id = esc_html( get_post_meta( get_the_ID(), 'property_id', true ) );
    
    $floorplan_data = rentfetch_get_floorplans( $property_id );
    $square_feet = apply_filters( 'rentfetch_filter_property_square_feet', $floorplan_data['sqftrange'] );
    return esc_html( $square_feet );
}

function rentfetch_property_square_feet() {
    $square_feet = rentfetch_get_property_square_feet();
    
    if ( $square_feet )
        echo $square_feet;
}

add_filter( 'rentfetch_filter_property_square_feet', 'rentfetch_default_property_square_feet_label', 10, 1 );
function rentfetch_default_property_square_feet_label( $square_feet ) {
    return $square_feet . ' Square Feet';
}

//* PROPERTY RENT

function rentfetch_get_property_rent() {
    $property_id = esc_html( get_post_meta( get_the_ID(), 'property_id', true ) );
    
    $floorplan_data = rentfetch_get_floorplans( $property_id );
    $rent = apply_filters( 'rentfetch_filter_property_rent', $floorplan_data['rentrange'] );
    return esc_html( $rent );
    
}

function rentfetch_property_rent() {
    $rent = rentfetch_get_property_rent();
    
    if ( $rent )
        echo $rent;
}

add_filter( 'rentfetch_filter_property_rent', 'rentfetch_default_property_rent_label', 10, 1 );
function rentfetch_default_property_rent_label( $rent ) {
    
    if ( $rent )
        return '$' . esc_html( $rent ) . '/mo';
        
    // This could return 'Call for Pricing' or 'Pricing unavailable' if pricing isn't available
    return null;
}