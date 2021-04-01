<?php

get_header();

//* General vars
$id = get_the_ID();
$title = get_the_title();

//* Post meta
// // for easy testing
// $meta = get_post_meta( $id );
// var_dump( $meta );

$address = get_post_meta( $id, 'address', true );
$city = get_post_meta( $id, 'city', true );
$state = get_post_meta( $id, 'state', true );
$zipcode = get_post_meta( $id, 'zipcode', true );
$url = get_post_meta( $id, 'url', true );
$description = get_post_meta( $id, 'description', true );
$email = get_post_meta( $id, 'email', true );
$phone = get_post_meta( $id, 'phone', true );
$latitude = get_post_meta( $id, 'latitude', true );
$longitude = get_post_meta( $id, 'longitude', true );
$property_code = get_post_meta( $id, 'property_code', true );
$voyager_property_code = get_post_meta( $id, 'voyager_property_code', true );
$property_id = get_post_meta( $id, 'property_id', true );

//* Prepare data

// prepare the city/state for the title
if ( $city && $state )
    $location = sprintf( '<span class="city-state">in %s, %s</span>', $city, $state );
    
if ( $city && !$state )
    $location = sprintf( '<span class="city-state">in %s</span>', $city );
    
if ( !$city && $state )
    $location = sprintf( '<span class="city-state">in %s</span>', $state );

    
//* Markup
    
if ( $title )
    printf( '<h1>%s %s</h1>', $title, $location );
    
//TODO add images

echo '<div class="basic-info">';
    echo '<div class="location">';
        
        if ( $address )
            printf( '<span class="address">%s</span>', $address );
            
        if ( $city )
            printf( '<span class="city">%s</span>', $city );
            
        if ( $state )
            printf( '<span class="state">%s</span>', $state );
            
        if ( $zipcode )
            printf( '<span class="zipcode">%s</span>', $zipcode );
            
    echo '</div>';
    echo '<div class="call">';
    
        printf( '<span class="calltoday">Call today</span>' );
        printf( '<span class="phone">%s</span>', $phone );
    
    echo '</div>';
    echo '<div class="property-website">';
    
        printf( '<a class="button property-link" target="_blank" href="%s">Visit property website</a>', $url );
    
    echo '</div>';
echo '</div>';

if ( $description ) {
    
    echo '<div class="description-wrap">';
    
        if ( $city )
            printf( '<h4 class="city">%s</h4>', $city );
            
        if ( $title )
            printf( '<h2>Welcome home to %s</h2>', $title );
            
        printf( '<div class="description">%s</div>', $description );
    
    echo '</div>';
    
}

$args = array(
    'post_type' => 'floorplans',
    'posts_per_page' => -1,
    'meta_query' => array(
        'key'   => 'voyager_property_code',
        'value' => $voyager_property_code,
    ),
);

$query = new WP_Query($args);
$floorplans = $query->posts;
foreach ($floorplans as $floorplan) {
    do_action( 'apartmentsync_do_floorplan_in_archive', $floorplan );
}

get_footer();
