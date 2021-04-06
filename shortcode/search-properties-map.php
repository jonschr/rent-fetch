<?php

add_shortcode( 'propertymap', 'apartmentsync_propertymap' );
function apartmentsync_propertymap( $atts ) {
    
    wp_enqueue_style( 'apartmentsync-search-properties-map' );
    
    
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
                    echo '<select name="bedsfilter">';
                        echo '<option value="">Bedrooms</option>';
                        foreach( $beds as $bed ) {
                            printf( '<option value="%s">%s Bedroom</option>', $bed, $bed );
                        }
                    echo '</select>'; // .bedsfilter
                echo '</div>';
            echo '</div>'; // .dropdown
        echo '</div>'; // .input-wrap
        
        //* Bathrooms
        $baths = apartentsync_get_meta_values( 'baths', 'floorplans' );
        $baths = array_unique( $baths );
        asort( $baths );
        
        echo '<div class="input-wrap input-wrap-baths">';
            echo '<select name="bathsfilter">';
                echo '<option value="">Bathrooms</option>';
                foreach( $baths as $bath ) {
                    printf( '<option value="%s">%s Bathrooms</option>', $bath, $bath );
                }
            echo '</select>';
        echo '</div>';
    
        echo '<button type="reset">Reset</button>';
        echo '<button type="submit">Apply filter</button>';
        echo '<input type="hidden" name="action" value="propertysearch">';
    echo '</form>';
    echo '<div id="response"></div>';
    
    ?>
    
    
    <script>
    
    jQuery(function($){
        
        $('#filter').submit(function(){
            var filter = $('#filter');
            $.ajax({
                url:filter.attr('action'),
                data:filter.serialize(), // form data
                type:filter.attr('method'), // POST
                beforeSend:function(xhr){
                    filter.find('button[type="submit"]').text('Processing...'); // changing the button label
                },
                success:function(data){
                    filter.find('button[type="submit"]').text('Apply filter'); // changing the button label back
                    $('#response').html(data); // insert data
                }
            });
            return false;
        });
        
        window.onload = function(){
            $('#filter').submit();
        }
    });
    
    </script>
    <?php

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
    
    // bedrooms
	if( isset( $_POST['bedsfilter'] ) && $_POST['bedsfilter'] != null ) {
		$args['meta_query'][] = array(
			array(
				'key' => 'beds',
				'value' => $_POST['bedsfilter']
			)
		);
    }
    
    // bathrooms
	if( isset( $_POST['bathsfilter'] ) && $_POST['bathsfilter'] != null ) {
		$args['meta_query'][] = array(
			array(
				'key' => 'baths',
				'value' => $_POST['bathsfilter']
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
 
	$query = new WP_Query( $args );
 
	if( $query->have_posts() ) :
        
		while( $query->have_posts() ): $query->the_post();
			echo '<h3>' . $query->post->post_title . '</h3>';
		endwhile;
		wp_reset_postdata();
	else :
		echo 'No properties found matching the current search parameters.';
	endif;
 
	die();
}