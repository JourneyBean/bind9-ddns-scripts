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


$input_key = $_POST['key'];
$input_action = $_POST['action'];

if ($input_key == getSecret()) {
    // Auth success
    
    switch($input_action) {

        case 'new_zone':
            if ($_POST['name']) {
                echo "[ddns-server] " . "[INFO] Adding zone [" . $_POST['name'] . "]\n";
                addZone($_POST['name'], $_POST['v4_addr'], $_POST['v6_addr'], $_POST['v6_prefix'], $_POST['v6_cidr']);
            };
            break;

        case 'new_client':
            if ($_POST['name']) {
                echo "[ddns-server] " . "[INFO] Adding client [" . $_POST['name'] . "]\n";
                addClient($_POST['name'], $_POST['v4_addr'], $_POST['v6_addr'], $_POST['v6_suffix']);
            };
            break;

        case 'mod_zone':
            if ($_POST['name'] && getZoneByName($_POST['name'])) {
                echo "[ddns-server] " . "[INFO] Modifying zone [" . $_POST['name'] .  "]\n";
                modifyZoneByName($_POST['name'], $_POST['name'], $_POST['v4_addr'], $_POST['v6_addr'], $_POST['v6_prefix'], $_POST['v6_cidr']);
            } else {
                echo "[ddns-server] " . "[ERR] Invalid parameters." . "\n";
            }
            break;

        case 'mod_client':
            if ($_POST['name'] && getClientByName($_POST['name'])) {
                echo "[ddns-server] " . "[INFO] Modifying client [" . $_POST['name'] . "]\n";
                modifyClientByName($_POST['name'], $_POST['name'], $_POST['v4_addr'], $_POST['v6_addr'], $_POST['v6_suffix']);
            } else {
                echo "[ddns-server] " . "[ERR] Invalid parameters." . "\n";
            }
            break;

        case 'update_zone':
            if ($_POST['name'] && getZoneByName($_POST['name'])) {
                echo "[ddns-server] " . "[INFO] Updating zone [" . $_POST['name'] . "]\n";
                updateZoneByName($_POST['name'], $_POST['name'], $_POST['v4_addr'], $_POST['v6_addr'], $_POST['v6_prefix'], $_POST['v6_cidr']);
            } else {
                echo "[ddns-server] " . "[ERR] Invalid parameters." . "\n";
            }
            break;

        case 'update_client':
            if ($_POST['name'] && getClientByName($_POST['name'])) {
                echo "[ddns-server] " . "[INFO] Updating client [" . $_POST['name'] . "]\n";
                updateClientByName($_POST['name'], $_POST['name'], $_POST['v4_addr'], $_POST['v6_addr'], $_POST['v6_suffix']);
            } else {
                echo "[ddns-server] " . "[ERR] Invalid parameters." . "\n";
            }
            break;

        case 'del_zone':
            if ($_POST['name']) {
                echo "[ddns-server] " . "[INFO] Deleting zone [" . $_POST['name'] . "]\n";
                deleteZoneByName($_POST['name']);
            } else {
                echo "[ddns-server] " . "[ERR] Invalid parameters." . "\n";
            }
            break;

        case 'del_client':
            if ($_POST['name']) {
                echo "[ddns-server] " . "[INFO] Deleting client [" . $_POST['name'] . "]\n";
                deleteClientByName($_POST['name']);
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