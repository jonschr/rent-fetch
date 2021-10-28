<?php

/**
 * Check if the credentials exist
 *
 * @return  bool  true if creds exist, false if not
 */
function rentfetch_check_creds_realpage() {
    
    $realpage_integration_creds = get_field( 'realpage_integration_creds', 'option' );
    $realpage_user = $realpage_integration_creds['realpage_user'];
    $realpage_pass = $realpage_integration_creds['realpage_pass'];
    $realpage_pmc_id = $realpage_integration_creds['realpage_pmc_id'];
    $realpage_site_ids = $realpage_integration_creds['realpage_site_ids'];
    
    // return false if there's no api key set
    if ( !$realpage_user || $realpage_pass || $realpage_pmc_id || $realpage_site_ids )      
        return false;
    
    // return true if there's an api key
    return true;
    
}

/**
 * Echo the notice, for if user credentials are missing
 */
function rentfetch_realpage_missing_user_pass_notice() {
    echo '<div class="notice notice-warning is-dismissible">';
        echo '<p>Syncing of data with RealPage is enabled, but we\'re missing information for the integration.</p>';
    echo '</div>';
}