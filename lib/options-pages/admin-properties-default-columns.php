<?php

add_filter( 'manage_properties_posts_columns', 'rentfetch_default_properties_admin_columns' );
function rentfetch_default_properties_admin_columns( $columns ) {
    
    ?>
    
    <style>
        
            .wrap {
                overflow-x: scroll;
                max-width: 98%;
            }
            
            .wp-list-table {
                position: relative;
                min-width: 2000px !important; 
            }
            
            td {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            
            th {
                width: 100px;
                min-width: 70px !important;
            }
            
            th#title {
                width: 300px !important;
            }
            
            th#property_id {
                width: 80px !important;
            }
            
            th#property_code {
                width: 100px !important;
            }
            
            th#address {
                width: 150px !important;
            }
            
            th#city {
                width: 70px !important;
            }
            
            th#state {
                width: 50px !important;
            }
            
            th#zipcode {
                width: 75px !important;
            }
            
            th#latitude {
                width: 100px !important;
            }
            
            th#longitude {
                width: 100px !important;
            }
            
            th#email {
                width: 150px !important;
            }
            
            th#phone {
                width: 100px !important;
            }
            
            th#url {
                width: 150px !important;
            }
            
            th#images {
                width: 200px !important;
            }
            
            th#property_source {
                width: 120px !important;
            }
            
            th#description {
                width: 200px !important;
            }
            
            th#matterport {
                width: 80px !important;
            }
            
            th#video {
                width: 80px !important;
            }
            
            th#pets {
                width: 100px !important;
            }
            
            th#content_area {
                width: 200px !important;
            }
            
            th#property_images {
                width: 200px !important;
            }
        
        </style>
        
        <?php
    
    $columns = array(
        'cb' =>              '<input type="checkbox" />',
        'title' =>           __( 'Title', 'rentfetch' ),
        'property_id' =>     __( 'Property ID', 'rentfetch' ),
        'property_code' =>   __( 'Property Code', 'rentfetch' ),
        'address' =>         __( 'Address', 'rentfetch' ),
        'city' =>            __( 'City', 'rentfetch' ),
        'state' =>           __( 'State', 'rentfetch' ),
        'zipcode' =>         __( 'Zipcode', 'rentfetch' ),
        'latitude' =>        __( 'Latitude', 'rentfetch' ),
        'longitude' =>       __( 'Longitude', 'rentfetch' ),
        'email' =>           __( 'Email', 'rentfetch' ),
        'phone' =>           __( 'Phone', 'rentfetch' ),
        'url' =>             __( 'URL', 'rentfetch' ),
        'images' =>          __( 'Images', 'rentfetch' ),
        'property_source' => __( 'Property Source', 'rentfetch' ),
        'description' =>     __( 'Description', 'rentfetch' ),
        'matterport' =>      __( 'Matterport', 'rentfetch' ),
        'video' =>           __( 'Video', 'rentfetch' ),
        'pets' =>            __( 'Pets', 'rentfetch' ),
        'content_area' =>    __( 'Content Area', 'rentfetch' ),
        'property_images' => __( 'Yardi Property Images', 'rentfetch' ),
    );
    
    return $columns;
    
}

add_action( 'manage_properties_posts_custom_column', 'rentfetch_properties_default_column_content', 10, 2);
function rentfetch_properties_default_column_content( $column, $post_id ) {
        
    if ( 'title' === $column )
        echo esc_attr( get_the_title( $post_id ) );
        
    if ( 'property_id' === $column )
        echo esc_attr( get_post_meta( $post_id, 'property_id', true ) );
        
    if ( 'property_code' === $column )
        echo esc_attr( get_post_meta( $post_id, 'property_code', true ) );
        
    if ( 'address' === $column )
        echo esc_attr( get_post_meta( $post_id, 'address', true ) );
        
    if ( 'city' === $column )
        echo esc_attr( get_post_meta( $post_id, 'city', true ) );
        
    if ( 'state' === $column )
        echo esc_attr( get_post_meta( $post_id, 'state', true ) );
        
    if ( 'zipcode' === $column )
        echo esc_attr( get_post_meta( $post_id, 'zipcode', true ) );
        
    if ( 'latitude' === $column )
        echo esc_attr( get_post_meta( $post_id, 'latitude', true ) );
        
    if ( 'longitude' === $column )
        echo esc_attr( get_post_meta( $post_id, 'longitude', true ) );
        
    if ( 'email' === $column )
        echo esc_attr( get_post_meta( $post_id, 'email', true ) );
        
    if ( 'phone' === $column )
        echo esc_attr( get_post_meta( $post_id, 'phone', true ) );
        
    if ( 'url' === $column )
        echo esc_attr( get_post_meta( $post_id, 'url', true ) );
        
    if ( 'images' === $column ) {
        $images = get_post_meta( $post_id, 'images', true );
        
        if ( is_array( $images ) ) {            
            foreach ( $images as $image ) {
                $image = wp_get_attachment_image_url( $image, 'thumbnail' );
                echo '<img src="' . esc_attr( $image ) . '" style="width: 40px; height: 40px;" />';
            }
        }
    }
        
    if ( 'property_source' === $column )
        echo esc_attr( get_post_meta( $post_id, 'property_source', true ) );
        
    if ( 'description' === $column )
        echo esc_attr( get_post_meta( $post_id, 'description', true ) );
        
    if ( 'matterport' === $column )
        echo esc_attr( get_post_meta( $post_id, 'matterport', true ) );
        
    if ( 'video' === $column )
        echo esc_attr( get_post_meta( $post_id, 'video', true ) );
        
    if ( 'pets' === $column )
        echo esc_attr( get_post_meta( $post_id, 'pets', true ) );
        
    if ( 'content_area' === $column )
        echo esc_attr( get_post_meta( $post_id, 'content_area', true ) );
        
    if ( 'property_images' === $column )
        echo esc_attr( get_post_meta( $post_id, 'property_images', true ) );
    
    if ( 'attraction_type' === $column ) {
        $terms = get_the_terms( $post_id, 'attractiontypes' );
        $count = 0;
        
        if ( $terms ) {
            foreach( $terms as $term ) {
                if ( $count != 0 )
                    echo ', ';
                    
                echo $term->name;
                $count++;
            }
        }            
    }
        
    if ( 'na_attractions_always_show' === $column ) {
        $always_show = get_post_meta( $post_id, 'na_attractions_always_show', true );
        
        if ( $always_show ) {
            echo 'Yes';
        } else {
            echo 'No';
        }
    }
        
    
    
}