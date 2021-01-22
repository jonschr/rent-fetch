<?php

add_action( 'apartmentsync_do_check_creds_entrata', 'apartmentsync_check_creds_entrata' );
function apartmentsync_check_creds_entrata() {
    
    $entrata_integration_creds = get_field( 'entrata_integration_creds', 'option' );
    $entrata_user = $entrata_integration_creds['entrata_user'];
    $entrata_pass = $entrata_integration_creds['entrata_pass'];
    
    if ( !$entrata_user || !$entrata_pass ) {
        add_action( 'admin_notices', 'apartmentsync_entrata_missing_user_pass_notice');
        return;
    }
    
}

function apartmentsync_entrata_missing_user_pass_notice() {
    echo '<div class="notice notice-warning is-dismissible">';
        echo '<p>Syncing of data with Entrata is enabled, but we\'re missing a username or password for the integration. <a href="/wp-admin/edit.php?post_type=floorplans&page=acf-options-integration-settings">Fix that</a></p>';
    echo '</div>';
}