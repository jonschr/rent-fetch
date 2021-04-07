<?php

add_shortcode( 'propertymap', 'apartmentsync_propertymap' );
function apartmentsync_propertymap( $atts ) {
    
    wp_enqueue_style( 'apartmentsync-search-properties-map' );
    wp_enqueue_script( 'apartmentsync-search-properties-ajax' );
    wp_enqueue_script( 'apartmentsync-search-properties-script' );
    
    ob_start();
    
    printf( '<form class="property-search-filters" action="%s/wp-admin/admin-ajax.php" method="POST" id="filter">', site_url() );
        
        //* Bedrooms
        $beds = apartentsync_get_meta_values( 'beds', 'floorplans' );
        $beds = array_unique( $beds );
        asort( $beds );
        
        echo '<div class="input-wrap input-wrap-beds">';
            echo '<div class="dropdown">';
                echo '<button type="button" class="dropdown-toggle" data-reset="Beds">Beds</button>';
                echo '<div class="dropdown-menu">';
                    echo '<div class="dropdown-menu-items">';
                        foreach( $beds as $bed ) {
                            printf( '<label><input type="checkbox" data-beds="%s" name="beds-%s">%s Bedroom</input></label>', $bed, $bed, $bed );
                        }
                    echo '</div>';
                    echo '<div class="filter-application">';
                        echo '<a class="clear" href="#">Clear</a>';
                        echo '<a class="apply" href="#">Apply</a>';
                    echo '</div>';
                echo '</div>';
            echo '</div>'; // .dropdown
        echo '</div>'; // .input-wrap
        
        //* Bathrooms
        $baths = apartentsync_get_meta_values( 'baths', 'floorplans' );
        $baths = array_unique( $baths );
        asort( $baths );
        
        echo '<div class="input-wrap input-wrap-baths">';
            echo '<div class="dropdown">';
                echo '<button type="button" class="dropdown-toggle" data-reset="Bathrooms">Bathrooms</button>';
                echo '<div class="dropdown-menu">';
                    echo '<div class="dropdown-menu-items">';
                        foreach( $baths as $bath ) {
                            printf( '<label><input type="checkbox" data-baths="%s" name="baths-%s">%s Bathroom</input></label>', $bath, $bath, $bath );
                        }
                    echo '</div>';
                    echo '<div class="filter-application">';
                        echo '<a class="clear" href="#">Clear</a>';
                        echo '<a class="apply" href="#">Apply</a>';
                    echo '</div>';
                echo '</div>';
            echo '</div>'; // .dropdown
        echo '</div>'; // .input-wrap
            
        echo '<button type="reset">Reset</button>';
        // echo '<button type="submit">Apply filter</button>';
        echo '<input type="hidden" name="action" value="propertysearch">';
    echo '</form>';
    echo '<div class="map-response-wrap">';
        echo '<div id="response"></div>';
        echo '<div id="map"></div>';
    echo '</div>';

    return ob_get_clean();
}

add_action( 'wp_ajax_propertysearch', 'apartmentsync_filter_properties' ); // wp_ajax_{ACTION HERE} 
add_action( 'wp_ajax_nopriv_propertysearch', 'apartmentsync_filter_properties' );
function apartmentsync_filter_properties(){
    
    //* start with floorplans
	$args = array(
        'post_type' => 'floorplans',
        'posts_per_page' => -1,
		'orderby' => 'date', // we will sort posts by date
		'order'	=> 'ASC' // ASC or DESC
	);
    
    //* bedrooms
    $beds = apartentsync_get_meta_values( 'beds', 'floorplans' );
    $beds = array_unique( $beds );
    asort( $beds );
    
    // loop through the checkboxes, and for each one that's checked, let's add that value to our meta query array
    foreach ( $beds as $bed ) {
        if ( isset( $_POST['beds-' . $bed ] ) && $_POST['beds-' . $bed ] == 'on' )
            $bedsarray[] = $bed;
    }
    
    // add the meta query array to our $args
    if ( isset( $bedsarray ) ) {
        $args['meta_query'][] = array(
            array(
                'key' => 'beds',
                'value' => $bedsarray,
            )
        );
    }
    
    //* bathrooms
    $baths = apartentsync_get_meta_values( 'baths', 'floorplans' );
    $baths = array_unique( $baths );
    asort( $baths );
    
    // loop through the checkboxes, and for each one that's checked, let's add that value to our meta query array
    foreach ( $baths as $bath ) {
        if ( isset( $_POST['baths-' . $bath ] ) && $_POST['baths-' . $bath ] == 'on' )
            $bathsarray[] = $bath;
    }
    
    // add the meta query array to our $args
    if ( isset( $bathsarray ) ) {
        $args['meta_query'][] = array(
            array(
                'key' => 'baths',
                'value' => $bathsarray,
            )
        );
    }
 
	// // for taxonomies / categories
	// if( isset( $_POST['categoryfilter'] ) )
	// 	$args['tax_query'] = array(
	// 		array(
	// 			'taxonomy' => 'category',
	// 			'field' => 'id',
	// 			'terms' => $_POST['categoryfilter']
	// 		)
	// 	);
 
	// // create $args['meta_query'] array if one of the following fields is filled
	// if( isset( $_POST['price_min'] ) && $_POST['price_min'] || isset( $_POST['price_max'] ) && $_POST['price_max'] || isset( $_POST['featured_image'] ) && $_POST['featured_image'] == 'on' )
	// 	$args['meta_query'] = array( 'relation'=>'AND' ); // AND means that all conditions of meta_query should be true
 
	// // if both minimum price and maximum price are specified we will use BETWEEN comparison
	// if( isset( $_POST['price_min'] ) && $_POST['price_min'] && isset( $_POST['price_max'] ) && $_POST['price_max'] ) {
	// 	$args['meta_query'][] = array(
	// 		'key' => '_price',
	// 		'value' => array( $_POST['price_min'], $_POST['price_max'] ),
	// 		'type' => 'numeric',
	// 		'compare' => 'between'
	// 	);
	// } else {
	// 	// if only min price is set
	// 	if( isset( $_POST['price_min'] ) && $_POST['price_min'] )
	// 		$args['meta_query'][] = array(
	// 			'key' => '_price',
	// 			'value' => $_POST['price_min'],
	// 			'type' => 'numeric',
	// 			'compare' => '>'
	// 		);
 
	// 	// if only max price is set
	// 	if( isset( $_POST['price_max'] ) && $_POST['price_max'] )
	// 		$args['meta_query'][] = array(
	// 			'key' => '_price',
	// 			'value' => $_POST['price_max'],
	// 			'type' => 'numeric',
	// 			'compare' => '<'
	// 		);
	// }
 
 
	// // if post thumbnail is set
	// if( isset( $_POST['featured_image'] ) && $_POST['featured_image'] == 'on' )
	// 	$args['meta_query'][] = array(
	// 		'key' => '_thumbnail_id',
	// 		'compare' => 'EXISTS'
	// 	);
	// // if you want to use multiple checkboxed, just duplicate the above 5 lines for each checkbox
 
    // echo '<pre style="font-size: 14px;">';
    // print_r( $args );
    // echo '</pre>';
    
	$query = new WP_Query( $args );
     
	if( $query->have_posts() ) :
        
        $numberofposts = $query->post_count;
        printf( '<div class="count"><h2 class="post-count"><span class="number">%s</span> results</h2><p>Note: Right now this is searching floorplans. Long-term, it will need to search the floorplans first, then do a secondary search of the associated properties.</p></div>', $numberofposts );
        
        echo '<div class="properties">';
            while( $query->have_posts() ): $query->the_post();
                do_action( 'apartmentsync_do_each_property', $query->post );
            endwhile;
        echo '</div>';
        
		wp_reset_postdata();
        
	else :
        
		echo 'No properties found matching the current search parameters.';
        
	endif;
 
	die();
}

add_action( 'apartmentsync_do_each_property', 'apartmentsync_each_property', 10, 1 );
function apartmentsync_each_property( $post ) {
    
    $id = $post->ID;
    $title = $post->post_title;
    $permalink = get_the_permalink();
    
    $class = get_post_class();
    $class = implode( ' ', $class );
    
    printf( '<div class="%s">', $class );
    
        if ( $title )
            printf( '<h3>%s</h3>', $title );
    
    echo '</div>';
    
    
    
}