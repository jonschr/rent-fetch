<?php

add_action( 'apartmentsync_do_single_properties', 'apartmentsync_single_property_title', 10 );
add_action( 'apartmentsync_do_single_properties', 'apartmentsync_single_property_images', 20 );
add_action( 'apartmentsync_do_single_properties', 'apartmentsync_single_property_basic_info', 30 );
add_action( 'apartmentsync_do_single_properties', 'apartmentsync_single_property_description', 40 );
add_action( 'apartmentsync_do_single_properties', 'apartmentsync_single_property_floorplans', 50 );
add_action( 'apartmentsync_do_single_properties', 'apartmentsync_single_property_amenities', 60 );
add_action( 'apartmentsync_do_single_properties', 'apartmentsync_single_property_lease_details', 70 );
add_action( 'apartmentsync_do_single_properties', 'apartmentsync_single_property_neighborhood', 80 );
add_action( 'apartmentsync_do_single_properties', 'apartmentsync_single_property_nearby_properties', 90 );

function apartmentsync_single_property_title() {
        
    global $post;
    
    $id = get_the_ID();
    $title = get_the_title();
    $city = get_post_meta( $id, 'city', true );
    $state = get_post_meta( $id, 'state', true );
    
    // prepare the location
    if ( $city && $state )
        $location = sprintf( '<span class="city-state">in %s, %s</span>', $city, $state );
        
    if ( $city && !$state )
        $location = sprintf( '<span class="city-state">in %s</span>', $city );
        
    if ( !$city && $state )
        $location = sprintf( '<span class="city-state">in %s</span>', $state );
    
    echo '<div class="single-properties-entry-header">';

        if ( $title )
            printf( '<h1>%s %s</h1>', $title, $location );

    echo '</div>';
}

function apartmentsync_single_property_images() {
    
    global $post;
    $id = get_the_ID();
    
    echo '<div class="images">';
        
        // these are images pulled from an API and stored as a JSON array
        $property_images = get_post_meta( $id, 'property_images', true );
        $property_images = json_decode( $property_images );
                
        if ( $property_images )
            do_action( 'apartmentsync_do_single_property_images_yardi', $property_images );
        
    echo '</div>';
    
}

function apartmentsync_single_property_basic_info() {
    
    global $post;
    $id = get_the_ID();
    
    $address = get_post_meta( $id, 'address', true );
    $city = get_post_meta( $id, 'city', true );
    $state = get_post_meta( $id, 'state', true );
    $zipcode = get_post_meta( $id, 'zipcode', true );
    $url = get_post_meta( $id, 'url', true );
    $phone = get_post_meta( $id, 'phone', true );

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
        
        // prepare the property url
        $url = apply_filters( 'apartmentsync_filter_property_url', $url );
        
        if ( $url ) {
            echo '<div class="property-website">';
            
                printf( '<a class="button property-link" target="_blank" href="%s">Visit property website</a>', $url );
            
            echo '</div>';
        }
    echo '</div>';
}

function apartmentsync_single_property_description() {
    
    global $post;
    $id = get_the_ID();
    
    $title = get_the_title();
    $city = get_post_meta( $id, 'city', true );
    $description = get_post_meta( $id, 'description', true );

    if ( $description ) {
        
        echo '<div class="description-wrap">';
        
            if ( $city )
                printf( '<h4 class="city">%s</h4>', $city );
                
            if ( $title )
                printf( '<h2>Welcome home to %s</h2>', $title );
                
            printf( '<div class="description">%s</div>', $description );
        
        echo '</div>';
        
    }
}

function apartmentsync_single_property_floorplans() {
    
    global $post;
    $id = get_the_ID();
    $property_id = get_post_meta( $id, 'property_id', true );
    
    // grab the gravity forms lightbox, if enabled on this page
    do_action( 'apartmentsync_do_gform_lightbox' );
    
    // get the possible values for the beds
    $beds = apartentsync_get_meta_values( 'beds', 'floorplans' );
    $beds = array_unique( $beds );
    asort( $beds );
    
    echo '<div class="floorplans-wrap">';
    
    // loop through each of the possible values, so that we can do markup around that
    foreach( $beds as $bed ) {
        
        $args = array(
            'post_type' => 'floorplans',
            'posts_per_page' => -1,
            'orderby' => 'meta_value_num',
            'meta_key' => 'beds',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key'   => 'property_id',
                    'value' => $property_id,
                ),
                array(
                    'key' => 'beds',
                    'value' => $bed,
                ),
            ),
        );
        
        $floorplans_query = new WP_Query( $args );
            
        if ( $floorplans_query->have_posts() ) {
            echo '<details open>';
                echo '<summary><h3>';
                    if ( $bed == '0' ) {
                        echo 'Studio';
                    } else {
                        echo $bed . ' Bedroom';
                    }
                echo '</h3></summary>';
                echo '<div class="floorplan-in-archive">';
                    while ( $floorplans_query->have_posts() ) : $floorplans_query->the_post(); 
                        do_action( 'apartmentsync_do_floorplan_in_archive', $post->ID );                    
                    endwhile;
                echo '</div>'; // .floorplans
            echo '</details>';
            
        }
        
        wp_reset_postdata();
    }
    
    echo '</div>'; // .floorplans-wrap
    
}

function apartmentsync_single_property_amenities() {
    
    global $post;
    
    $terms = get_the_terms( get_the_ID(), 'amenities' );
    if ( $terms ) {
        echo '<div class="amenities-wrap">';
            echo '<h2>Amenities</h2>';
            echo '<ul class="amenities">';
                foreach( $terms as $term ) {                
                    printf( '<li>%s</li>', $term->name );
                }
            echo '</ul>';
        echo '</div>';
    }
}

function apartmentsync_single_property_lease_details() {
    
    global $post;
    
    $content_area = get_post_meta( get_the_ID(), 'content_area', true );
    if ( !empty( $content_area ) ) {
        $content_area = apply_filters( 'the_content', $content_area );
        
        echo '<div class="content-area-wrap">';
            echo $content_area;
        echo '</div>';
    }
}

function apartmentsync_single_property_neighborhood() {
    
    global $post;
    
    $neighborhoods = MB_Relationships_API::get_connected( [
        'id'   => 'properties_to_neighborhoods',
        'to' => get_the_ID(),
    ] );
        
    if ( !empty( $neighborhoods ) ) {
        
        $neighborhood = $neighborhoods[0];   
        $neighborhood_id = $neighborhood->ID;
        $permalink = get_the_permalink( $neighborhood_id );
        $thumb = get_the_post_thumbnail_url( $neighborhood_id, 'large' );
        $title = get_the_title( $neighborhood_id );
        $excerpt = apply_filters( 'the_content', get_the_excerpt( $neighborhood_id ) );
        
        echo '<div class="neighborhoods-wrap">';
        
            printf( '<div class="neighborhood-photo-wrap"><a href="%s" class="neighborhood-photo" style="background-image:url(%s);"></a></div>', $permalink, $thumb );
            
            echo '<div class="neighborhood-content">';
            
                echo '<h4>The neighborhood</h4>';
                printf( '<h2>Life in %s</h2>', $title );
                
                if ( $excerpt )
                    printf( '<div class="excerpt">%s</div>', $excerpt );
                    
                printf( '<a href="%s" class="button">Explore the neighborhood</a>', $permalink );
                
            echo '</div>';
        echo '</div>';
    }
}

function apartmentsync_single_property_nearby_properties() {
    
    global $post;
    
    do_action( 'apartmentsync_single_properties_nearby_properties' );
    
}