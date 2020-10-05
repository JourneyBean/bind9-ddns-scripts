<?php

/**
 *  Reads config from file and return as an array.
 */
function readConfig() {
    require(__DIR__ . '/config.php');
    return json_decode($config_json, true);
}

function loadConfig() {
    $GLOBALS['config'] = readConfig();
}

function getConfig() {
    return $GLOBALS['config'];
}

/**
 *  Write config file, input is config array
 */
function writeConfig( $config ) {
    $config_raw = json_encode($config, JSON_PRETTY_PRINT);
    file_put_contents( __DIR__ . '/config.php', "<?php\n\n\$config_json = '\n".$config_raw."\n\n';\n\n?>");
}

function saveConfig() {
    writeConfig(getConfig());
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
    $zones = getConfig()['zones'];
    foreach ($zones as $current_zone) {
        if ($current_zone['name'] == $zone_name) return $current_zone;
    }
    return false;
}

function addZone( $zone_name, $v4_addr, $v6_addr, $v6_prefix, $v6_cidr = 64 ) {
    foreach ($GLOBALS['config']['zones'] as $current_zone) {
        if ($current_zone['name'] == $zone_name) return;
    }
    $zone_new = [
        'name' => $zone_name,
        'v4_addr' => $v4_addr,
        'v6_addr' => $v6_addr,
        'v6_prefix' => $v6_prefix,
        'v6_cidr' => $v6_cidr
    ];
    array_push($GLOBALS['config']['zones'], $zone_new);
}

function addClient( $client_name, $v4_addr, $v6_addr, $v6_suffix ) {
    foreach ($GLOBALS['config']['clients'] as $current_client) {
        if ($current_client['name'] == $client_name) return;
    }
    $zone_new = [
        'name' => $client_name,
        'v4_addr' => $v4_addr,
        'v6_addr' => $v6_addr,
        'v6_suffix' => $v6_suffix
    ];
    array_push($GLOBALS['config']['clients'], $zone_new);
}

function updateZoneByName( $zone_name, $new_name, $new_v4_addr, $new_v6_addr, $new_v6_prefix, $new_v6_cidr ) {
    foreach ($GLOBALS['config']['zones'] as &$current_zone) {
        if ($current_zone['name'] == $zone_name) {
            if ($new_name) $current_zone['name'] = $new_name;
            if ($new_v4_addr) $current_zone['v4_addr'] = $new_v4_addr;
            if ($new_v6_addr) $current_zone['v6_addr'] = $new_v6_addr;
            if ($new_v6_prefix) $current_zone['v6_prefix'] = $new_v6_prefix;
            if ($new_v6_cidr) $current_zone['v6_cidr'] = $new_v6_cidr;
        } else {
        }
    }
}

function updateClientByName( $client_name, $new_name, $new_v4_addr, $new_v6_addr, $new_v6_suffix ) {
    foreach ($GLOBALS['config']['clients'] as &$current_client) {
        if ($current_client['name'] == $client_name) {
            if ($new_name) $current_client['name'] = $new_name;
            if ($new_v4_addr) $current_client['v4_addr'] = $new_v4_addr;
            if ($new_v6_addr) $current_client['v6_addr'] = $new_v6_addr;
            if ($new_v6_suffix) $current_client['v6_suffix'] = $new_v6_suffix;
        } else {
        }
    }
}

function modifyZoneByName( $zone_name, $new_name, $new_v4_addr, $new_v6_addr, $new_v6_prefix, $new_v6_cidr ) {
    foreach ($GLOBALS['config']['zones'] as &$current_zone) {
        if ($current_zone['name'] == $zone_name) {
            $current_zone['name'] = $new_name;
            $current_zone['v4_addr'] = $new_v4_addr;
            $current_zone['v6_addr'] = $new_v6_addr;
            $current_zone['v6_prefix'] = $new_v6_prefix;
            $current_zone['v6_cidr'] = $new_v6_cidr;
        } else {
        }
    }
}

function modifyClientByName( $client_name, $new_name, $new_v4_addr, $new_v6_addr, $new_v6_suffix ) {
    foreach ($GLOBALS['config']['clients'] as &$current_client) {
        if ($current_client['name'] == $client_name) {
            $current_client['name'] = $new_name;
            $current_client['v4_addr'] = $new_v4_addr;
            $current_client['v6_addr'] = $new_v6_addr;
            $current_client['v6_suffix'] = $new_v6_suffix;
        } else {
        }
    }
}

function deleteZoneByName( $zone_name ) {
    foreach ($GLOBALS['config']['zones'] as $key => $current_zone) {
        if ($current_zone['name'] == $zone_name) {
            unset($GLOBALS['config']['zones'][$key]);
        }
    }
}

function deleteClientByName( $client_name ) {
    foreach($GLOBALS['config']['clients'] as $key => $current_client) {
        if ($current_client['name'] == $client_name) {
            unset($GLOBALS['config']['clients'][$key]);
        }
    }
}

/**
 *  Returns secret key
 */
function getSecret() {
    return getConfig()['secret'];
}

/**
 *  Returns config.php version
 */
function getConfigVersion() {
    return getConfig()['version'];
}

function getSerialOld() {
    return getConfig()['serial'];
}

function getZonefilePath() {
    return getConfig()['zonefile_path'];
}

?>