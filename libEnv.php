<?php

require_once( './libConfig.php' );
require_once( './libIp6Addr.php' );

/**
 *  Generate all v4 addresses
 */
function getV4Pool() {

    $config = getConfig();
    $clients = $config['clients'];
    $zones = $config['zones'];

    $v4_pool = [];

    // import zones' addresses
    foreach ( $zones as $current_zone ) {
        // v4_pool[zone_name]['v4'] = zone_v4_addr | null
        if ($current_zone['v4_addr']) $v4_pool[$current_zone['name']]['v4'] = $current_zone['v4_addr'];
    }

    // import clients' addresses
    foreach ( $clients as $current_client ) {
        // v4_pool[client_name]['v4'] = client_v4_addr | zone_v4_addr | null
        if ($current_client['v4_addr']) {
            $v4_pool[$current_client['name']]['v4'] = $current_client['v4_addr'];
        } 
        else if ($getZoneByName([$current_client['zone']])['v4_addr']) {
            $v4_pool[$current_client['name']]['v4'] = $getZoneByName([$current_client['zone']])['v4_addr'];
        } 
    }

    return $v4_pool;
}

/**
 *  Generate all v6 addresses
 */
function getV6Pool() {

    $config = getConfig();
    $clients = $config['clients'];
    $zones = $config['zones'];

    $v6_pool = [];

    // import clients' addresses
    foreach ( $zones as $current_zone ) {
        // v6_pool[zone_name]['v6'] = zone_v6_addr | null
        if ($current_zone['v6_addr']) $v6_pool[$current_zone['name']]['v6'] = $current_zone['v6_addr'];
    }

    foreach ( $clients as $current_client ) {
        // v6_pool[cient_name]['v6'] = client_v6_addr | zone_v6_prefix&client_v6_suffix | zone_v6_addr | null
        if ( $current_client['v6_address'] ) {
            $v6_pool[$current_client['name']] = $current_client['v6_address'];
        } else if ( $current_client['v6_suffix'] && $getZoneByName($current_client['name'])['v6_prefix'] ) {
            $suffix = $current_client['v6_suffix'];
            $prefix = $getZoneByName($current_client['name'])['v6_prefix'];
            $cidr = $getZoneByName($current_client['name'])['v6_cidr']?$getZoneByName($current_client['name'])['v6_cidr']:-1;
            $v6_pool[$current_client['name']] = ip6MergeAddr($prefix, $suffix, $cidr);
        } else if ( $getZoneByName($current_client['name'])['v6_addr'] ) {
            $v6_pool[$current_client['name']] = $getZoneByName($current_client['name'])['v6_addr'];
        }
    }

    return $v6_pool;
}

/**
 *  Environment setup
 *  This function do these things:
 *  - Setup IP pool to GLOBAL array.
 */
function setupEnvConfig() {

    // load config to memory
    $GLOBALS['config'] = readConfig();

}

function setupEnvIpPool() {

    $v4_pool = getV4Pool();
    $v6_pool = getV6Pool();

    return array_merge_recursive($v4_pool, $v6_pool);
}

?>