<?php

/**
 * api.php
 * 
 * Index file handling input data
 * 
 * @author Johnson Liu
 * @version v1.1
 * 
 */

// ini_set('display_errors', 'on');

require_once(__DIR__ . '/libNsdata.php');
require_once(__DIR__ . '/libIpPool.php');
require_once(__DIR__ . '/libConfig.php');

loadConfig();


$input_key = $_GET['key'];
$input_action = $_GET['action'];

if ($input_key == getSecret()) {
    // Auth success
    
    switch($input_action) {

        case 'new_zone':
            if ($_GET['name']) {
                echo "[ddns-server] " . "[INFO] Adding zone [" . $_GET['name'] . "]\n";
                addZone($_GET['name'], $_GET['v4_addr'], $_GET['v6_addr'], $_GET['v6_prefix'], $_GET['v6_cidr']);
            };
            break;

        case 'new_client':
            if ($_GET['name']) {
                echo "[ddns-server] " . "[INFO] Adding client [" . $_GET['name'] . "]\n";
                addClient($_GET['name'], $_GET['v4_addr'], $_GET['v6_addr'], $_GET['v6_suffix']);
            };
            break;

        case 'mod_zone':
            if ($_GET['name'] && getZoneByName($_GET['name'])) {
                echo "[ddns-server] " . "[INFO] Modifying zone [" . $_GET['name'] .  "]\n";
                modifyZoneByName($_GET['name'], $_GET['name'], $_GET['v4_addr'], $_GET['v6_addr'], $_GET['v6_prefix'], $_GET['v6_cidr']);
            } else {
                echo "[ddns-server] " . "[ERR] Invalid parameters." . "\n";
            }
            break;

        case 'mod_client':
            if ($_GET['name'] && getClientByName($_GET['name'])) {
                echo "[ddns-server] " . "[INFO] Modifying client [" . $_GET['name'] . "]\n";
                modifyClientByName($_GET['name'], $_GET['name'], $_GET['v4_addr'], $_GET['v6_addr'], $_GET['v6_suffix']);
            } else {
                echo "[ddns-server] " . "[ERR] Invalid parameters." . "\n";
            }
            break;

        case 'update_zone':
            if ($_GET['name'] && getZoneByName($_GET['name'])) {
                echo "[ddns-server] " . "[INFO] Updating zone [" . $_GET['name'] . "]\n";
                updateZoneByName($_GET['name'], $_GET['name'], $_GET['v4_addr'], $_GET['v6_addr'], $_GET['v6_prefix'], $_GET['v6_cidr']);
            } else {
                echo "[ddns-server] " . "[ERR] Invalid parameters." . "\n";
            }
            break;

        case 'update_client':
            if ($_GET['name'] && getClientByName($_GET['name'])) {
                echo "[ddns-server] " . "[INFO] Updating client [" . $_GET['name'] . "]\n";
                updateClientByName($_GET['name'], $_GET['name'], $_GET['v4_addr'], $_GET['v6_addr'], $_GET['v6_suffix']);
            } else {
                echo "[ddns-server] " . "[ERR] Invalid parameters." . "\n";
            }
            break;

        case 'del_zone':
            if ($_GET['name']) {
                echo "[ddns-server] " . "[INFO] Deleting zone [" . $_GET['name'] . "]\n";
                deleteZoneByName($_GET['name']);
            } else {
                echo "[ddns-server] " . "[ERR] Invalid parameters." . "\n";
            }
            break;

        case 'del_client':
            if ($_GET['name']) {
                echo "[ddns-server] " . "[INFO] Deleting client [" . $_GET['name'] . "]\n";
                deleteClientByName($_GET['name']);
            } else {
                echo "[ddns-server] " . "[ERR] Invalid parameters." . "\n";
            }
            break;
        
        default:
            echo "[ddns-server] " . "[ERR] Operation not supported." . "\n";
            exit;
    }

} else {
    // Auth failed
    echo "[ddns-server] " . "[ERR] Authentication failed." . "\n";
    exit;
}

// write config.php
saveConfig();

// old nsdata
if (file_exists(getZonefilePath())) {
    $nsdata_old = file_get_contents(getZonefilePath());
} else {
    $nsdata_old = '';
}

// new nsdata generated with old serial
$ip_pool = getIpPool();
$nsdata_raw = readNsdata();
$serial_old = getSerialOld();

$nsdata = translateNsdata($ip_pool, $nsdata_raw, $serial_old);

// compare nsdata
if ($nsdata_old == $nsdata) {
    echo "[ddns-server] " . "[INFO] Same profile. Ignoring update request." . "\n";
    exit;
}

// write nsdata
$serial = date('YmdH');
$nsdata = translateNsdata($ip_pool, $nsdata_raw, $serial);
$GLOBALS['config']['serial'] = $serial;
file_put_contents(getZonefilePath(), $nsdata);

// inform bind9 to reload
echo "[ddns-server] " . "[INFO] ";
system('rndc reload');

// write config.php
saveConfig();

echo "[ddns-server] " . "[OK] Script exec success." . "\n";

?>