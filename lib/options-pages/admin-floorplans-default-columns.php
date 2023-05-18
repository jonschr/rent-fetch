<?php

add_filter( 'manage_floorplans_posts_columns', 'rentfetch_default_floorplans_admin_columns' );
function rentfetch_default_floorplans_admin_columns( $columns ) {
    
    ?>
    
    <style>
        
            /* .wrap {
                overflow-x: scroll;
                max-width: 98%;
            } */
            
            .row-actions {
                opacity: 1;
                /* left: auto; */
            }
            
            .wp-list-table {
                display: inline-block;
                width: auto;
                margin-bottom: -4px !important;
                max-width: 100%;
                overflow: -moz-scrollbars-none;
                -ms-overflow-style: none;
                overflow-x: auto;
                position: relative;
                box-sizing: border-box;
            }
            
            td {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            
            th {
                width: 100px;
                min-width: 100px !important;
            }
            
            td#cb,
            th.check-column {
                width: 30px !important;
                min-width: 30px !important;
            }
            
            th#title {
                min-width: 300px !important;
            }
            
            th#property_id {
                min-width: 80px !important;
            }
            
            th#floorplan_source {
                min-width: 120px !important;
            }
            
            th#manual_images {
                min-width: 150px !important;
            }
            
            th#floorplan_images {
                min-width: 150px !important;
            }
            
            th#floorplan_description {
                min-width: 150px !important;
            }
            
            th#minimum_deposit {
                min-width: 150px !important;
            }
            
            th#maximum_deposit {
                min-width: 150px !important;
            }
            
            th#availability_date {
                min-width: 150px !important;
            }
            
            th#show_specials {
                min-width: 150px !important;
            }
            
            th#has_specials {
                min-width: 150px !important;
            }
            
            th#floorplan_id {
                min-width: 100px !important;
            }
            
            th#availability_url,
            td.availability_url
             {
                max-width: 150px !important;
            }
                    
        </style>
        
        <?php
    
    $columns = array(
        'cb' =>                         '<input type="checkbox" />',
        'title' =>                      __( 'Title', 'rentfetch' ),
        'floorplan_source' =>           __( 'Floorplan Source', 'rentfetch' ),
        'property_id' =>                __( 'Property ID', 'rentfetch' ),
        'floorplan_id' =>               __( 'Floorplan ID', 'rentfetch' ),
        'unit_type_mapping' =>          __( 'Unit Type', 'rentfetch' ),
        'manual_images' =>              __( 'Manual Images', 'rentfetch' ),
        'floorplan_images' =>           __( 'Synced Images', 'rentfetch' ),
        'floorplan_description' =>      __( 'Floorplan Description', 'rentfetch' ),
        'floorplan_video_or_tour' =>    __( 'Video/Tour', 'rentfetch' ),
        'beds' =>                       __( 'Beds', 'rentfetch' ),
        'baths' =>                      __( 'Baths', 'rentfetch' ),
        'minimum_deposit' =>            __( 'Min Deposit', 'rentfetch' ),
        'maximum_deposit' =>            __( 'Max Deposit', 'rentfetch' ),
        'minimum_rent' =>               __( 'Min Rent', 'rentfetch' ),
        'maximum_rent' =>               __( 'Max Rent', 'rentfetch' ),
        'minimum_sqft' =>               __( 'Min Sqrft', 'rentfetch' ),
        'maximum_sqft' =>               __( 'Max Sqrft', 'rentfetch' ),
        'availability_date' =>          __( 'Availability Date', 'rentfetch' ),
        'property_show_specials' =>     __( 'Show Specials', 'rentfetch' ),
        'has_specials' =>               __( 'Has Specials', 'rentfetch' ),
        'availability_url' =>           __( 'Availability URL', 'rentfetch' ),
        'available_units' =>            __( 'Availiable Units', 'rentfetch' ),
    );
    
    return $columns;
    
}

add_action( 'manage_floorplans_posts_custom_column', 'rentfetch_floorplans_default_column_content', 10, 2);
function rentfetch_floorplans_default_column_content( $column, $post_id ) {
        
    if ( 'title' === $column )
        echo esc_attr( get_the_title( $post_id ) );
        
    if ( 'floorplan_source' === $column )
        echo esc_attr( get_post_meta( $post_id, 'floorplan_source', true ) );        
    
    if ( 'property_id' === $column )
        echo esc_attr( get_post_meta( $post_id, 'property_id', true ) );        
    
    if ( 'floorplan_id' === $column )
        echo esc_attr( get_post_meta( $post_id, 'floorplan_id', true ) );        
    
    if ( 'unit_type_mapping' === $column )
        echo esc_attr( get_post_meta( $post_id, 'unit_type_mapping', true ) );        
    
    // if ( 'manual_images' === $column )
    //     echo esc_attr( get_post_meta( $post_id, 'manual_images', true ) );   
        
    if ( 'manual_images' === $column ) {
        $images = get_post_meta( $post_id, 'manual_images', true );
        
        if ( is_array( $images ) ) {            
            foreach ( $images as $image ) {
                $image = wp_get_attachment_image_url( $image, 'thumbnail' );
                echo '<img src="' . esc_attr( $image ) . '" style="width: 40px; height: 40px;" />';
            }
        }
    }
    
    if ( 'floorplan_images' === $column )
        echo esc_attr( get_post_meta( $post_id, 'floorplan_images', true ) );        
    
    if ( 'floorplan_description' === $column )
        echo esc_attr( get_post_meta( $post_id, 'floorplan_description', true ) );        
    
    if ( 'floorplan_video_or_tour' === $column )
        echo esc_attr( get_post_meta( $post_id, 'floorplan_video_or_tour', true ) );        
    
    if ( 'beds' === $column )
        echo esc_attr( get_post_meta( $post_id, 'beds', true ) );        
    
    if ( 'baths' === $column )
        echo esc_attr( get_post_meta( $post_id, 'baths', true ) );        
    
    if ( 'minimum_deposit' === $column )
        echo esc_attr( get_post_meta( $post_id, 'minimum_deposit', true ) );        
    
    if ( 'maximum_deposit' === $column )
        echo esc_attr( get_post_meta( $post_id, 'maximum_deposit', true ) );        
    
    if ( 'minimum_rent' === $column )
        echo esc_attr( get_post_meta( $post_id, 'minimum_rent', true ) );        
    
    if ( 'maximum_rent' === $column )
        echo esc_attr( get_post_meta( $post_id, 'maximum_rent', true ) );        
    
    if ( 'minimum_sqft' === $column )
        echo esc_attr( get_post_meta( $post_id, 'minimum_sqft', true ) );        
    
    if ( 'maximum_sqft' === $column )
        echo esc_attr( get_post_meta( $post_id, 'maximum_sqft', true ) );        
    
    if ( 'availability_date' === $column )
        echo esc_attr( get_post_meta( $post_id, 'availability_date', true ) );        
    
    if ( 'property_show_specials' === $column ) {
        $property_show_specials = get_post_meta( $post_id, 'property_show_specials', true );
        
        if ( $property_show_specials ) {
            echo 'Yes';
        } else {
            echo 'No';
        }
    }
            
    if ( 'has_specials' === $column ) {
        $has_specials = get_post_meta( $post_id, 'has_specials', true );
        
        if ( $has_specials ) {
            echo 'Yes';
        } else {
            echo 'No';
        }
    }
        
    if ( 'availability_url' === $column )
        echo esc_attr( get_post_meta( $post_id, 'availability_url', true ) );        
    
    if ( 'available_units' === $column )
        echo esc_attr( get_post_meta( $post_id, 'available_units', true ) );        
    
}