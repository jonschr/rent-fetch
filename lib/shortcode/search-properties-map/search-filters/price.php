<?php

function rentfetch_search_properties_map_filters_price() {
    
    // check whether beds search is enabled
    $map_search_components = get_option( 'options_map_search_components' );
        
    // bail if beds search is not enabled
    if ( !in_array( 'price_search', $map_search_components ) )
        return;
        
    // figure out our min/max values
    $price_settings = get_option( 'options_price_filter' );
    $valueSmall = isset( $price_settings['minimum'] ) ? $price_settings['minimum'] : 0;
    $valueBig = isset( $price_settings['maximum'] ) ? $price_settings['maximum'] : 5000;
    $step = isset( $price_settings['step'] ) ? $price_settings['step'] : 50;        
    
    // enqueue the noui slider
    wp_enqueue_style( 'rentfetch-nouislider-style' );
    wp_enqueue_script( 'rentfetch-nouislider-script' );
    wp_localize_script( 'rentfetch-nouislider-init-script', 'settings', 
        array(
            'valueSmall' => $valueSmall,
            'valueBig' => $valueBig,
            'step' => $step,
        )
    );
    wp_enqueue_script( 'rentfetch-nouislider-init-script' );
    
    //* build the price search
    echo '<div class="input-wrap input-wrap-prices">';
        echo '<div class="dropdown">';
            echo '<button type="button" class="dropdown-toggle" data-reset="Price">Price</button>';
            echo '<div class="dropdown-menu dropdown-menu-prices">';
                echo '<div class="dropdown-menu-items">';
                    echo '<div class="price-slider-wrap"><div id="price-slider" style="width:100%;"></div></div>';
                    echo '<div class="inputs-prices">';
                        printf( '<input type="number" name="pricesmall" id="pricesmall" value="%s" />', $valueSmall );
                        printf( '<input type="number" name="pricebig" id="pricebig" value="%s" />', $valueBig );
                    echo '</div>';
                echo '</div>';
                echo '<div class="filter-application">';
                    echo '<a class="clear" href="#">Clear</a>';
                    echo '<a class="apply" href="#">Apply</a>';
                echo '</div>';
            echo '</div>';
        echo '</div>'; // .dropdown
    echo '</div>'; // .input-wrap
        
}