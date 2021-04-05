<?php

add_action( 'apartmentsync_do_floorplan_in_archive', 'apartmentsync_floorplan_in_archive', 10, 1 );
function apartmentsync_floorplan_in_archive( $post ) {
    
    wp_enqueue_style( 'apartmentsync-floorplan-in-archive' );
    wp_enqueue_style( 'apartmentsync-fancybox-style' );
    wp_enqueue_script( 'apartmentsync-fancybox-script' );
    
    $post_id = $post->ID;
        
    //* Grab the data
    $title = get_the_title( $post_id );
    $availability_url = get_field( 'availability_url', $post_id );
    $available_units = get_field( 'available_units', $post_id );
    $numberofbaths = get_field( 'baths', $post_id );
    $numberofbeds = get_field( 'beds', $post_id );
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
    $beds = apartmentsync_floorplangrid_number_of_bedrooms_label( $numberofbeds, $settings );
        
    // thumb
    if ( $floorplan_image_url ) {
        $floorplan_image_url = explode( ',', $floorplan_image_url );
        $floorplan_image_url = $floorplan_image_url[0];
    }
    
    if ( !$floorplan_image_url )
        $floorplan_image_url = get_the_post_thumbnail_url( $post_id, 'large' );
    
    // rent
    $rent_range = null;
    if ( $minimum_rent && $maximum_rent ) {
        
        if ( $minimum_rent != $maximum_rent )
            $rent_range = sprintf( '<span class="dollars">$</span><span class="amount">%s</span> - <span class="amount">%s</span>', $minimum_rent, $maximum_rent );
            
        if ( $minimum_rent == $maximum_rent )
            $rent_range = sprintf( '<span class="dollars">$</span><span class="amount">%s</span>', $minimum_rent );
            
    }
    if ( $minimum_rent && !$maximum_rent ) $rent_range = sprintf( '<span class="dollars">$</span><span class="amount">%s</span>', $minimum_rent );
    if ( $maximum_rent && !$minimum_rent ) $rent_range = sprintf( '<span class="dollars">$</span><span class="amount">%s</span>', $maximum_rent );
    
    // sqft
    $sqft_range = null;
    $sqft_label = 'sq. ft.';
    
    if ( $minimum_sqft && $maximum_sqft ) {
        
        if ( $minimum_sqft != $maximum_sqft )
            $sqft_range = sprintf( '%s-%s %s', $minimum_sqft, $maximum_sqft, $sqft_label );
            
        if ( $minimum_sqft == $maximum_sqft )
            $sqft_range = sprintf( '%s %s', $minimum_sqft, $sqft_label );
        
    }
    
    if ( $minimum_sqft && !$maximum_sqft ) $sqft_range = sprintf( '%s %s', $minimum_sqft, $sqft_label );
    if ( !$minimum_sqft && $maximum_sqft ) $sqft_range = sprintf( '%s %s', $maximum_sqft, $sqft_label );
    
    //* Set up the classes
    $floorplanclass = get_post_class();
    
    if ( $available_units > 0 ) {
        $floorplanclass[] = 'units-available';
    } else {
        $floorplanclass[] = 'no-units-available';  
    } 
        
    $floorplanclass = implode( $floorplanclass, ' ' );
    
    // echo '<pre>';
    //     print_r( $block['data'] );
    // echo '</pre>';

    //* Do the markup
    printf( '<div class="%s beds-%s baths-%s">', $floorplanclass, $numberofbeds, $numberofbaths );
        echo '<div class="floorplan-inner">';
        
            if ( $floorplan_image_url ) {
                echo '<div class="floorplan-art-wrap">';
                    printf( '<div class="image-wrap"><a href="%s" data-fancybox="%s" class="image-link"><img height="200px" width="300px" class="image" src="%s" title="%s" alt="%s" /></a></div>', $floorplan_image_url, $post_id, $floorplan_image_url, $floorplan_image_name, $floorplan_image_alt_text );
                echo '</div>';
            }
                
            echo '<div class="floorplan-content">';
            
                if ( $title )
                    printf( '<h3 class="floorplan-title">%s</h3>', $title );
                    
                echo '<p class="info">';
                
                    if ( $beds )
                        printf( '<span class="floorplan-beds">%s</span>', $beds );
                        
                    if ( $baths )
                        printf( '<span class="floorplan-baths">%s</span>', $baths );
                        
                    if ( $sqft_range )
                        printf( '<span class="floorplan-sqft_range">%s</span>', $sqft_range );
                                        
                echo '</p>';
                
                edit_post_link( 'Edit', '', '', $post_id );
                
            echo '</div>';  
            
            echo '<div class="floorplan-rent-range">';
            
                if ( $rent_range )
                    printf( '<p class="rent_range">%s</p>', $rent_range );
                
                echo '<div class="floorplan-buttons">';
                
                echo '<a href="#" class="button">Sample button</a>';
                echo '<a href="#" class="button">Sample button</a>';
                
                    //* CONTACT BUTTON (FLOORPLAN AVAILABLE)
                    // we get the vars above, since these aren't layouts-specific
                    
                    if ( $settings['contactavailable_button_enabled'] == 1 ) {
                        
                        if ( $settings['contactavailable_button_type'] == 'link' )
                            printf( '<a href="%s" class="button contact-available-button" target="%s">%s</a>', $settings['contactavailable_link'], $settings['contactavailable_link_target'], $settings['contactavailable_customize_button_text'] );
                        
                        if ( $settings['contactavailable_button_type'] == 'gform' )
                            printf( '<a href="#" data-fancybox data-src="#contact-available-button-gform-%s" class="button contact-available-button">%s</a>', $settings['contactavailable_gravity_form_id'], $settings['contactavailable_customize_button_text'] );
                    }
                    
                    //* AVAILABILITY BUTTON
                    
                    // if there's an override, use that (or default to the setting)
                    $availability_url = get_field( 'availability_button_url_override' ) ?: $availability_url;
                
                    // availability button
                    if ( $availability_url && get_field( 'availability_button_enabled' ) == 1 ) {
                        
                        // get button info from settings                    
                        $availability_button_text = get_field( 'availability_button_text' ) ?: 'Vew availability';
                        $availability_button_target = get_field( 'availability_button_target' );
                                            
                        printf( '<a href="%s" class="button availability-button" target="%s">%s</a>', $availability_url, $availability_button_target, $availability_button_text );
                    }
                    
                echo '</div>'; // .buttons
            
            echo '</div>'; // .floorplan-rent-range
            
          echo '</div>'; // .floorplan-inner  
        
    echo '</div>';
    
}