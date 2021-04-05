<?php

//* Add a body class
add_filter( 'body_class', 'apartmentsync_add_properties_body_class' );
function apartmentsync_add_properties_body_class( $classes ) {
    global $post;
    
    if ( isset( $post ) )
        $classes[] = 'single-properties-template';
    
    return $classes;
    
}

get_header();

wp_enqueue_style( 'apartmentsync-single-properties' );

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
echo '<div class="single-properties-wrap">';

    ////////////////////
    // PROPERTY TITLE //
    ////////////////////

    echo '<div class="single-properties-entry-header">';

        if ( $title )
            printf( '<h1>%s %s</h1>', $title, $location );

    echo '</div>';
    
    /////////////////////
    // PROPERTY IMAGES //
    /////////////////////

    echo '<div class="images">';
    //TODO add images
    echo '</div>';
    
    /////////////////////////
    // PROPERTY BASIC INFO //
    /////////////////////////

    echo '<div class="basic-info">';
        echo '<div class="location">';
            echo '<p class="the-location">';
            
                if ( $address )
                    printf( '<span class="address">%s</span>', $address );
                    
                if ( $city )
                    printf( '<span class="city">%s</span>', $city );
                    
                if ( $state )
                    printf( '<span class="state">%s</span>', $state );
                    
                if ( $zipcode )
                    printf( '<span class="zipcode">%s</span>', $zipcode );
                
            echo '</p>';
        echo '</div>';
        echo '<div class="call">';
            echo '<p class="the-call">';
        
                printf( '<span class="calltoday">Call today</span>' );
                printf( '<span class="phone">%s</span>', $phone );
                
            echo '</p>';
        echo '</div>';
        echo '<div class="property-website">';
        
            printf( '<a class="button property-link" target="_blank" href="%s">Visit property website</a>', $url );
        
        echo '</div>';
    echo '</div>';
    
    //////////////////////////
    // PROPERTY DESCRIPTION //
    //////////////////////////

    if ( $description ) {
        
        echo '<div class="description-wrap">';
        
            if ( $city )
                printf( '<h4 class="city">%s</h4>', $city );
                
            if ( $title )
                printf( '<h2>Welcome home to %s</h2>', $title );
                
            printf( '<div class="description">%s</div>', $description );
        
        echo '</div>';
        
    }
    
    ////////////////
    // FLOORPLANS //
    ////////////////

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
    
    if ( $floorplans ) {
        echo '<div class="floorplans-wrap">';
            echo '<h2>Availability</h2>';
            echo '<div class="floorplans">';
                foreach ($floorplans as $floorplan) {
                    do_action( 'apartmentsync_do_floorplan_in_archive', $floorplan );
                }
            echo '</div>'; // .floorplans
        echo '</div>'; // .floorplans-wrap
    }
    
    
echo '</div>'; // .single-properties-wrap

get_footer();


