<?php

// add_action( 'rentfetch_do_get_floorplans_realpage', 'rentfetch_get_floorplans_realpage' );
add_action( 'wp_footer', 'rentfetch_get_floorplans_realpage' );
function rentfetch_get_floorplans_realpage() {
        
    // bail if credentials haven't been completed fully
    if ( rentfetch_check_creds_realpage() == false )
        return;
            
    $realpage_integration_creds = get_field( 'realpage_integration_creds', 'option' );
    $realpage_user = $realpage_integration_creds['realpage_user'];
    $realpage_pass = $realpage_integration_creds['realpage_pass'];
    $realpage_pmc_id = $realpage_integration_creds['realpage_pmc_id'];
    $realpage_site_ids = $realpage_integration_creds['realpage_site_ids'];
    
    // remove all whitespace from $realpage_site_ids
    $realpage_site_ids = preg_replace('/\s+/', '', $realpage_site_ids);
    $realpage_site_ids = explode( ',', $realpage_site_ids );
        
    foreach( $realpage_site_ids as $realpage_site_id ) {
                
        do_action( 'rentfetch_do_save_transient_realpage_floorplan', $realpage_site_id );
        
    }

}

add_action( 'rentfetch_do_save_transient_realpage_floorplan', 'rentfetch_save_transient_realpage_floorplan', $realpage_site_id );
function rentfetch_save_transient_realpage_floorplan( $realpage_site_id ) {
    
    $floorplans = get_transient( 'realpage_floorplans_site_id_' . $property );
    
    // bail if we have a transient already
    if ( $floorplans )
        return;
    
    $realpage_integration_creds = get_field( 'realpage_integration_creds', 'option' );
    $realpage_user = $realpage_integration_creds['realpage_user'];
    $realpage_pass = $realpage_integration_creds['realpage_pass'];
    $realpage_pmc_id = $realpage_integration_creds['realpage_pmc_id'];
            
    $curl = curl_init();
    
    $xml = sprintf(
        '<?xml version="1.0" encoding="utf-8"?>
        <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
            <soap12:Header>
                <UserAuthInfo xmlns="http://realpage.com/webservices">
                <UserName>%s</UserName>
                <Password>%s</Password>
                <SiteID>%s</SiteID>
                <PmcID>%s</PmcID>
                <InternalUser>1</InternalUser>
                </UserAuthInfo>
            </soap12:Header>
            <soap12:Body>
                <List xmlns="http://realpage.com/webservices">
                    <!-- removed information here from the sample request -->
                </List>
            </soap12:Body>
        </soap12:Envelope>',
        $realpage_user,
        $realpage_pass,
        $realpage_site_id,
        $realpage_pmc_id,
    );

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://Onesite.RealPage.com/WebServices/CrossFire/AvailabilityAndPricing/Floorplan.asmx',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $xml,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/soap+xml; charset=utf-8'
        ),
    ));

    $response = curl_exec($curl);
    
    // SimpleXML seems to have problems with the colon ":" in the <xxx:yyy> response tags, so take them out
    $xml = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2$3', $response);
    $xml = simplexml_load_string($xml);
    $json = json_encode($xml);
    $responseArray = json_decode($json,true);
    $floorplans = $responseArray['soapBody']['ListResponse']['ListResult']['FloorPlanObject'];
    
    set_transient( 'realpage_floorplans_site_id_' . $realpage_site_id, $floorplans, HOUR_IN_SECONDS );
    
}