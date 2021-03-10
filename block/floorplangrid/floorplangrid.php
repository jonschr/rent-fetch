<?php


add_action('acf/init', 'apartmentsync_floorplangrid_block_register_block');
function apartmentsync_floorplangrid_block_register_block() {

    // Check function exists.
    if( function_exists( 'acf_register_block_type') ) {

        // register a testimonial block.
        acf_register_block_type(array(
            'name'              => 'floorplangrid',
            'title'             => __('Floorplan grid'),
            'description'       => __('A block to show a floorplan grid with simple filters'),
            'render_callback'   => 'apartmentsync_floorplangrid_block_render',
            'enqueue_assets'    => 'apartmentsync_floorplangrid_block_enqueue',
            'category'          => 'formatting',
            'icon'              => 'table-col-before',
            'keywords'          => array( 'floorplan', 'apartment', 'grid', 'availability' ),
            'mode'              => 'preview',
            'align'              => 'normal',
            'supports'          => array(
                'align' => array( 'full', 'wide', 'normal' ),
                'mode' => false,
                'jsx' => true
            ),
        ));
    }
}

function apartmentsync_floorplangrid_block_render( $block, $content = '', $is_preview = false, $post_id = 0 ) {
    
    //* Default class
    $className = 'floorplangrid';
    
    //* Default ID
    $id = 'floorplangrid-' . $block['id'];
    
    //* Get settings
    $floorplan_filter = get_field( 'floorplan_filter' );
    $columns = get_field( 'columns' );

    // Create id attribute allowing for custom "anchor" value.
    if( !empty($block['anchor']) ) 
        $id = $block['anchor'];

    // Create class attribute allowing for custom "className" and "align" values.
    if( !empty($block['className']) )
        $className .= ' ' . $block['className'];

    if( !empty($block['align']) )
        $className .= ' align' . $block['align'];

                    
    //* Render
    printf( '<div id="%s" class="%s">', $id, $className );
    
        $args = array(
            'post_type' => 'floorplans',
            'posts_per_page' => '-1'
        );

        // The Query
        $floorplans = get_posts( $args );
        
        printf( '<div class="floorplangrid-wrap columns-%s">', $columns );
            foreach ( $floorplans as $floorplan ) {
                                
                do_action( 'apartment_floorplangrid_do_inner', $floorplan->ID );
                
            }
        echo '</div>';
    
        
        if ( !empty($padding_top) || !empty($padding_bottom) || !empty($padding_left) || !empty($padding_right) ) {
            ?>
            <style>
                /* Padding */
                @media( min-width: 960px ) { 
                    #section-<?php echo $block['id']; ?> {
                        padding-top: <?php echo $padding_top; ?>% !important;
                        padding-bottom: <?php echo $padding_bottom; ?>% !important;
                        padding-left: <?php echo $padding_left; ?>% !important;
                        padding-right: <?php echo $padding_right; ?>% !important;
                    }
                }
            </style>
            <?php
        }
                
    echo '</div>';
   
}

function apartmentsync_floorplangrid_block_enqueue() {
    wp_enqueue_style( 'floorplangrid-style', APARTMENTSYNC_PATH . 'css/floorplangrid.css', array(), APARTMENTSYNC_VERSION, 'screen' );
}

add_action( 'apartment_floorplangrid_do_inner', 'apartment_floorplangrid_inner_default', 10, 1 );
function apartment_floorplangrid_inner_default( $floorplanID ) {
    
    $post_id = $floorplanID;
    
    //* Grab the data
    $title = get_the_title( $post_id );
    $availability_url = get_field( 'availability_url', $post_id );
    $available_units = get_field( 'available_units', $post_id );
    $baths = get_field( 'baths', $post_id );
    $beds = get_field( 'beds', $post_id );
    $has_specials = get_field( 'has_specials', $post_id );
    $floorplan_id = get_field( 'floorplan_id', $post_id );
    $floorplan_image_url = get_field( 'floorplan_image_url', $post_id );
    $floorplan_image_name = get_field( 'floorplan_image_name', $post_id );
    $floorplan_image_alt_text = get_field( 'floorplan_image_alt_text', $post_id );
    $maximum_deposit = get_field( 'maximum_deposit', $post_id );
    $maximum_rent = get_field( 'maximum_rent', $post_id );
    $maximum_sqft = get_field( 'maximum_sqft', $post_id );
    $minimum_deposit = get_field( 'minimum_deposit', $post_id );
    $minimum_rent = get_field( 'minimum_rent', $post_id );
    $minimum_sqft = get_field( 'minimum_sqft', $post_id );
    $property_id = get_field( 'property_id', $post_id );
    $property_show_specials = get_field( 'property_show_specials', $post_id );
    $unit_type_mapping = get_field( 'unit_type_mapping', $post_id );
    $floorplan_source = get_post_meta( $post_id, 'floorplan_source', true );
        
    //* Figure things out
    
    // beds
    if ( $beds == 0 ) $beds = 'Studio';
    if ( $beds == 1 ) $beds = '1 Bedroom';
    if ( $beds == 2 ) $beds = '2 Bedroom';
    if ( $beds == 3 ) $beds = '3 Bedroom';
    if ( $beds == 4 ) $beds = '4 Bedroom';
    if ( $beds == 5 ) $beds = '5 Bedroom';
    
    // baths
    if ( $baths == 0 ) $baths = '0 Bath';
    if ( $baths == 1 ) $baths = '1 Bath';
    if ( $baths == 2 ) $baths = '2 Bath';
    if ( $baths == 3 ) $baths = '3 Bath';
    if ( $baths == 4 ) $baths = '4 Bath';
    if ( $baths == 5 ) $baths = '5 Bath';
    
    // rent
    $rent_range = null;
    if ( $minimum_rent && $maximum_rent ) $rent_range = sprintf( '<span class="dollars">$</span><span class="amount">%s</span> - <span class="amount">%s</span> ', $minimum_rent, $maximum_rent );
    if ( $minimum_rent && !$maximum_rent ) $rent_range = sprintf( '<span class="dollars">$</span><span class="amount">%s</span>', $minimum_rent );
    if ( $maximum_rent && !$minimum_rent ) $rent_range = sprintf( '<span class="dollars">$</span><span class="amount">%s</span>', $maximum_rent );
    
    // sqrft
    $sqrft_range = null;
    $sqrft_label = 'sq. ft.';
    if ( $minimum_sqft && $maximum_sqft ) {
        
        if ( $minimum_sqrft != $maximum_sqrft )
            $sqrft_range = sprintf( '%s-%s %s', $minimum_sqft, $maximum_sqft, $sqrft_label );
            
        if ( $minimum_sqrft == $maximum_sqrft )
            $sqrft_range = sprintf( '%s %s', $minimum_sqft, $sqrft_label );
        
    }
    
    if ( $minimum_sqft && !$maximum_sqft ) $sqrft_range = sprintf( '%s %s', $minimum_sqft, $sqrft_label );
    if ( !$minimum_sqft && $maximum_sqft ) $sqrft_range = sprintf( '%s %s', $maximum_sqft, $sqrft_label );
    
    //* Set up the classes
    $floorplanclass = array( 'floorplan' );
    
    if ( $available_units > 0 ) {
        $floorplanclass[] = 'units-available';
    } else {
        $floorplanclass[] = 'no-units-available';  
    } 
        
    $floorplanclass = implode( $floorplanclass, ' ' );

    //* Do the markup
    printf( '<div class="%s">', $floorplanclass );
            
        echo '<div class="floorplangrid__art-wrap">';
        
            if ( $floorplan_image_url ) 
                printf( '<div class="floorplangrid__image-wrap"><a href="#" class="floorplangrid__image-link"><img class="floorplangrid__image" src="%s" title="%s" alt="%s" /></a></div>', $floorplan_image_url, $floorplan_image_name, $floorplan_image_alt_text );
                
        echo '</div>';
            
        echo '<div class="floorplangrid__content">';
        
            if ( $title )
                printf( '<h3 class="floorplangrid__title">%s</h3>', $title );
                
            echo '<p class="floorplangrid__info">';
            
                if ( $beds )
                    printf( '<span class="floorplangrid__beds">%s</span>', $beds );
                    
                if ( $baths )
                    printf( '<span class="floorplangrid__baths">%s</span>', $baths );
                    
                if ( $sqrft_range )
                    printf( '<span class="floorplangrid__sqrft_range">%s</span>', $sqrft_range );
                                    
            echo '</p>';
            
            if ( $rent_range )
                printf( '<p class="floorplangrid__rent_range">%s</p>', $rent_range );
            
            edit_post_link( 'Edit', '', '', $post_id );
                            
        echo '</div>';  
        
    echo '</div>';
    
}