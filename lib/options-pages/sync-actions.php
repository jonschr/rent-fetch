<?php

add_action('admin_menu', 'register_my_custom_submenu_page', 99 );

function register_my_custom_submenu_page() {
    add_submenu_page( 
        'edit.php?post_type=floorplans', 
        'Data Sync', 
        'Data Sync', 
        'manage_options', 
        'api-sync', 
        'apartment_sync_syncactions_callback' ); 
}

function apartment_sync_syncactions_callback() {
	echo '<div class="wrap">';
		echo '<h2>Pull API data into site</h2>';
	echo '</div>';
}