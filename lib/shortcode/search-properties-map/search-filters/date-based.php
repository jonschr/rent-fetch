<?php

function rentfetch_search_properties_map_filters_date() {
    
    // check whether beds search is enabled
    $map_search_components = get_option( 'options_map_search_components' );
        
    // bail if beds search is not enabled
    if ( !in_array( 'date_search', $map_search_components ) )
        return;
        
    // enqueue date picker scripts
    wp_enqueue_style( 'rentfetch-flatpickr-style' );
    wp_enqueue_script( 'rentfetch-flatpickr-script' );
    wp_enqueue_script( 'rentfetch-flatpickr-script-init' );
    
    // build the date-based search
    echo '<div class="input-wrap input-wrap-date-available">';
        echo '<div class="dropdown">';
            echo '<div class="flatpickr">';
                echo '<input type="text" name="dates" placeholder="Available date" style="width:auto;" data-input>';
            echo '</div>';
        echo '</div>'; // .dropdown
    echo '</div>'; // .input-wrap
        
    
}