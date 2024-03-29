<?php

//////////////
// ENQUEUES //
//////////////

add_action( 'wp_enqueue_scripts', 'rentfetch_enqueue_scripts_stylesheets' );
function rentfetch_enqueue_scripts_stylesheets() {
    
    // Enqueue dashicons, since we use them on the frontend
    wp_enqueue_style( 'dashicons' );
	
	// Plugin styles
    wp_register_style( 'rentfetch-single-properties', RENTFETCH_PATH . 'css/single-properties.css', array(), RENTFETCH_VERSION, 'screen' );
    
    // NoUISlider (for dropdown double range slider)
    wp_register_style( 'rentfetch-nouislider-style', RENTFETCH_PATH . 'vendor/nouislider/nouislider.min.css', array(), RENTFETCH_VERSION, 'screen' );
    wp_register_script( 'rentfetch-nouislider-script', RENTFETCH_PATH . 'vendor/nouislider/nouislider.min.js', array( 'jquery' ), RENTFETCH_VERSION, true );
    wp_register_script( 'rentfetch-nouislider-init-script', RENTFETCH_PATH . 'js/rentfetch-search-map-nouislider-init.js', array( 'jquery' ), RENTFETCH_VERSION, true );
    
    // Flatpickr
    wp_register_style( 'rentfetch-flatpickr-style', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), RENTFETCH_VERSION, 'screen' );
    wp_register_script( 'rentfetch-flatpickr-script', 'https://cdn.jsdelivr.net/npm/flatpickr', array( 'jquery' ), RENTFETCH_VERSION, true );
    wp_register_script( 'rentfetch-flatpickr-script-init', RENTFETCH_PATH . 'js/rentfetch-search-map-flatpickr-init.js', array( 'rentfetch-flatpickr-script' ), RENTFETCH_VERSION, true );
    
    // Properties map search
    wp_register_style( 'rentfetch-search-properties-map', RENTFETCH_PATH . 'css/search-properties-map.css', array(), RENTFETCH_VERSION, 'screen' );
    wp_register_script( 'rentfetch-search-properties-ajax', RENTFETCH_PATH . 'js/rentfetch-search-properties-ajax.js', array( 'jquery' ), RENTFETCH_VERSION, true );
    wp_register_script( 'rentfetch-search-properties-script', RENTFETCH_PATH . 'js/rentfetch-search-properties-script.js', array( 'jquery' ), RENTFETCH_VERSION, true );
    wp_register_script( 'rentfetch-toggle-map', RENTFETCH_PATH . 'js/rentfetch-toggle-map.js', array( 'jquery' ), RENTFETCH_VERSION, true );
    
    // Properties map (the map itself)
    wp_register_script( 'rentfetch-property-map', RENTFETCH_PATH . 'js/rentfetch-property-map.js', array( 'jquery', 'rentfetch-google-maps' ), RENTFETCH_VERSION, true );
    wp_register_script( 'rentfetch-single-property-map', RENTFETCH_PATH . 'js/rentfetch-single-property-map.js', array( 'jquery', 'rentfetch-google-maps' ), RENTFETCH_VERSION, true );
    
    // Properties searchbar
    wp_register_script( 'rentfetch-search-filters-general', RENTFETCH_PATH . 'js/rentfetch-search-filters-general.js', array( 'jquery' ), RENTFETCH_VERSION, true );
        
    // Properties in archive
    wp_register_style( 'rentfetch-properties-in-archive', RENTFETCH_PATH . 'css/properties-in-archive.css', array(), RENTFETCH_VERSION, 'screen' );
    wp_register_script( 'rentfetch-property-images-slider-init', RENTFETCH_PATH . 'js/rentfetch-property-images-slider-init.js', array( 'jquery' ), RENTFETCH_VERSION, true );
    
    // Favorite properties
    wp_register_script( 'rentfetch-property-favorites-cookies', 'https://cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js', array( 'jquery' ), RENTFETCH_VERSION, true );
    wp_register_script( 'rentfetch-property-favorites', RENTFETCH_PATH . 'js/rentfetch-property-favorites.js', array( 'rentfetch-property-favorites-cookies' ), RENTFETCH_VERSION, true );
    
    // Floorplans in archive
    wp_register_style( 'rentfetch-floorplan-in-archive', RENTFETCH_PATH . 'css/floorplan-in-archive.css', array(), RENTFETCH_VERSION, 'screen' );
    wp_register_script( 'rentfetch-floorplan-images-slider-init', RENTFETCH_PATH . 'js/rentfetch-floorplan-images-slider-init.js', array( 'jquery' ), RENTFETCH_VERSION, true );
    
    // Fancybox
    wp_register_style( 'rentfetch-fancybox-style', RENTFETCH_PATH . 'vendor/fancybox/jquery.fancybox.min.css', array(), RENTFETCH_VERSION, 'screen' );
    wp_register_script( 'rentfetch-fancybox-script', RENTFETCH_PATH . 'vendor/fancybox/jquery.fancybox.min.js', array( 'jquery' ), RENTFETCH_VERSION, true );
        
    // Slick
    wp_register_script( 'rentfetch-slick-main-script', RENTFETCH_PATH . 'vendor/slick/slick.min.js', array('jquery'), RENTFETCH_VERSION, true );
    wp_register_style( 'rentfetch-slick-main-styles', RENTFETCH_PATH . 'vendor/slick/slick.css', array(), RENTFETCH_VERSION );
    wp_register_style( 'rentfetch-slick-main-theme', RENTFETCH_PATH . 'vendor/slick/slick-theme.css', array(), RENTFETCH_VERSION );
    
    // Google reCAPTCHA
    wp_register_script( 'rentfetch-google-recaptcha', 'https://www.google.com/recaptcha/api.js', array('jquery'), RENTFETCH_VERSION, true );
    
    	
}