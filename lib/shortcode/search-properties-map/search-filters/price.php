<?php

function rentfetch_search_properties_map_filters_price() {
	
	// check whether beds search is enabled
	$map_search_components = get_option( 'options_map_search_components' );
	
	// this needs to be set to an array even if the option isn't set
	if ( !is_array( $map_search_components ) )
		$map_search_components = array();
		
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
	echo '<fieldset>';
		echo '<legend>Price Range</legend>';
		echo '<div class="slider filter-wrap-price">';
			echo '<div class="price-slider-wrap"><div id="price-slider" style="width:100%;"></div></div>';
			echo '<div class="inputs-prices">';
				printf( '<input type="number" name="pricesmall" id="pricesmall" value="%s" />', $valueSmall );
				printf( '<input type="number" name="pricebig" id="pricebig" value="%s" />', $valueBig );
			echo '</div>';
		echo '</div>'; // .slider
	echo '</fieldset>';		
}

add_filter( 'rentfetch_search_property_map_floorplans_query_args', 'rentfetch_search_property_map_floorplans_args_price', 10, 1 );
function rentfetch_search_property_map_floorplans_args_price( $floorplans_args ) {
	
	// bail if we don't have a price search
	if ( !isset( $_POST['pricesmall'] ) && !isset( $_POST['pricebig'] ) )
		return $floorplans_args;
	
	$defaultpricesmall = 100;
	$defaultpricebig = 5000;
	
	// get the small value
	if ( isset( $_POST['pricesmall'] ) )
		$pricesmall = intval( sanitize_text_field( $_POST['pricesmall'] ) );
		
	// if it's not a good value, then change it to something sensible
	if ( $pricesmall < 100 )
		$pricesmall = $defaultpricesmall;
	
	// get the big value
	if ( isset( $_POST['pricebig'] ) )
		$pricebig = intval( sanitize_text_field( $_POST['pricebig'] ) );
		
	// if there's isn't one, then use the default instead
	if ( empty( $pricebig ) )
		$pricebig = $defaultpricebig;
			
	
	// if we're showing all properties, then by default we need to ignore pricing
	$property_availability_display = $price_settings = get_option( 'options_property_availability_display', 'options' );
	if ( $property_availability_display == 'all' ) {
		
		// but if pricing parameters are actually being manually set, then we need that search to work
		if ( $pricesmall > 100 || $pricebig < 5000 ) {
								
			$floorplans_args['meta_query'][] = array(
				array(
					'key' => 'minimum_rent',
					'value' => array( $pricesmall, $pricebig ),
					'type' => 'numeric',
					'compare' => 'BETWEEN',
				)
			);
		}            
	} else {
		// if this is an availability search, then always take pricing into account
		$floorplans_args['meta_query'][] = array(
				array(
					'key' => 'minimum_rent',
					'value' => array( $pricesmall, $pricebig ),
					'type' => 'numeric',
					'compare' => 'BETWEEN',
				)
			);
	}
	
	return $floorplans_args;
	
}