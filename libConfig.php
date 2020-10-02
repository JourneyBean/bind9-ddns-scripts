<?php

/**
 *  Reads config from file and return as an array.
 */
function readConfig() {
    require('./config.php');
    return json_decode($config_json, true);
}

/**
 *  Reads config from $GLOBALS
 */
function getConfig() {
    return $GLOBALS['config'];
}

/**
 *  Write config file, input is config array
 */
function writeConfig( $config ) {
    $config_raw = json_encode($config);
    file_put_contents('./config.php', "<?php\n\n\$config_raw = '\n".$config_raw."\n\n';\n\n?>");
}

/**
 *  Returns client config array
 */
function getClientByName( $client_name ) {
    $clients = getConfig()['clients'];
    foreach ($clients as $current_client) {
        if ($current_client['name'] == $client_name) return $current_client;
    }
    return false;
}

/**
 *  Returns zone config array
 */
function getZoneByName( $zone_name ) {
    $clients = getConfig()['zones'];
    foreach ($zones as $current_zone) {
        if ($current_zone['name'] == $zone_name) return $current_zone;
    }
    return false;
}

function modifyZoneByName( $zone_name, $new_name, $new_v4_addr, $new_v6_addr, $new_v6_prefix, $new_v6_cidr ) {
    foreach ($GLOBALS['config']['zones'] as $current_zone) {
        if ($current_zone['name'] == $zone_name) {
            if ($new_name) $current_zone['name'] = $new_name;
            if ($new_v4_addr) $current_zone['v4_addr'] = $new_v4_addr;
            if ($new_v6_addr) $current_zone['v6_addr'] = $new_v6_addr;
            if ($new_v6_prefix) $current_zone['v6_prefix'] = $new_v6_prefix;
            if ($new_v6_cidr) $current_zone['v6_cidr'] = $new_v6_cidr;
        } else {
            return false;
        }
    }
}

function modifyClientByName( $client_name, $new_name, $new_v4_addr, $new_v6_addr, $new_v6_suffix ) {
    foreach ($GLOBALS['config']['clients'] as $current_client) {
        if ($current_client['name'] == $client_name) {
            if ($new_name) $current_client['name'] = $new_name;
            if ($new_v4_addr) $current_client['v4_addr'] = $new_v4_addr;
            if ($new_v6_addr) $current_client['v6_addr'] = $new_v6_addr;
            if ($new_v6_suffix) $current_client['v6_prefix'] = $new_v6_suffix;
        } else {
            return false;
        }
    }
}

function deleteZoneByName( $zone_name ) {
    foreach ($GLOBALS['config']['zones'] as $current_zone) {
        if ($current_zone['name'] == $zone_name) {
            unset($GLOBALS['config']['zones'][$current_zone]);
        }
    }
}

function deleteClientByName( $client_name ) {
    foreach($GLOBALS['config']['clients'] as $current_client) {
        if ($current_client['name'] == $client_name) {
            unset($GLOBALS['config']['zones'][$current_client]);
        }
    }
}

/**
 *  Returns secret key
 */
function getSecret() {
    return readConfig['secret'];
}

/**
 *  Returns config.php version
 */
function getConfigVersion() {
    return readConfig['version'];
}

?>