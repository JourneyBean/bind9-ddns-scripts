<?php

/**
 * api.php
 * 
 * Index file handling input data
 * 
 */

require_once(__DIR__ . '/libNsdata.php');
require_once(__DIR__ . '/libIpPool.php');
require_once(__DIR__ . '/libConfig.php');

loadConfig();





$input_key = $_POST['key'];
$input_action = $_POST['action'];

if ($input_key != getSecret()) {
    // Auth success
    
    switch($input_action) {

        case 'new_zone':
            if ($_POST['name']) {
                addZone($_POST['name'], $_POST['v4_addr'], $_POST['v6_addr'], $_POST['v6_prefix'], $_POST['v6_cidr']);
            };
            break;

        case 'new_client':
            if ($_POST['name']) {
                addClient($_POST['name'], $_POST['v4_addr'], $_POST['v6_addr'], $_POST['v6_suffix']);
            };
            break;

        case 'mod_zone':
            if ($_POST['name'] && getZoneByName($_POST['name'])) {
                modifyZoneByName($_POST['name'], $_POST['name'], $_POST['v4_addr'], $_POST['v6_addr'], $_POST['v6_prefix'], $_POST['v6_cidr']);
            }
            break;

        case 'mod_client':
            if ($_POST['name'] && getClientByName($_POST['name'])) {
                modifyClientByName($_POST['name'], $_POST['name'], $_POST['v4_addr'], $_POST['v6_addr'], $_POST['v6_suffix']);
            }
            break;

        case 'del_zone':
            if ($_POST['name']) {
                deleteZoneByName($_POST['name']);
            }
            break;

        case 'del_client':
            if ($_POST['name']) {
                deleteClientByName($_POST['name']);
            }
            break;
        
        default:
            echo "[ddns-server] " . "[ERR] Operation not supported." . PHP_EOF;
            exit;
    }

} else {
    // Auth failed
    echo "[ddns-server] " . "[ERR] Authentication failed." . PHP_EOF;
    exit;
}

// old nsdata
$nsdata_old = file_get_contents(getZonefilePath());

// new nsdata generated with old serial
$ip_pool = getIpPool();
$nsdata_raw = readNsdata();
$serial_old = getSerial();

$nsdata = translateNsdata($ip_pool, $nsdata_raw, $serial_old);

// compare nsdata
if ($nsdata_old == $nsdata) {
    echo "[ddns-server] " . "[INFO] Same profile. Ignoring update request." . PHP_EOF;
    exit;
}

// write nsdata
$serial = date('YmdH');
$nsdata = translateNsdata($ip_pool, $nsdata_raw, $serial);
$GLOBALS['config']['serial'] = $serial;
file_put_contents(getZonefilePath, $nsdata);

// inform bind9 to reload
echo "[ddns-server] " . "[INF] " . system('rndc reload') . PHP_EOF;

// write config.php
saveConfig();

echo "[ddns-server] " . "[OK] Script exec success." . PHP_EOF;

?>