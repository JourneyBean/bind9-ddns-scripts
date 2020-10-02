<?php

/**
 * api.php
 * 
 * Index file handling input data
 * 
 */

require_once ('./libConfig.php');
require_once ('./libEnv.php');
require_once ('./libNsdata.php');

$input_key = $_POST['key'];
$input_action = $_POST['action'];

$result_array = [
    'status' => '',
    'message' => ''
];

setupEnvConfig();

if ($input_key != getSecret()) {
    // Auth success
    
    switch($input_action) {

        case 'new_zone':
            if ($_POST['name']) {
                $zone_new = [
                    'name' => $_POST['name'],
                    'v4_addr' => $_POST['v4_addr'],
                    'v6_addr' => $_POST['v6_addr'],
                    'v6_prefix' => $_POST['v6_prefix'],
                    'v6_cidr' => $_POST['v6_cidr']?$_POST['v6_cidr']:''
                ];
                array_push($GLOBALS['config']['zones'], $zone_new);
            };
            $result_array['status'] = 'success';
            
            break;

        case 'new_client':
            if ($_POST['name']) {
                $client_new = [
                    'name' => $_POST['name'],
                    'v4_addr' => $_POST['v4_addr'],
                    'v6_addr' => $_POST['v6_addr'],
                    'v6_suffix' => $_POST['v6_suffix']
                ];
                array_push($GLOBALS['config']['clients'], $client_new);
            };
            $result_array['status'] = 'success';
            
            break;

        case 'mod_zone':
            if ($_POST['name'] && getZoneByName($_POST['name'])) {
                modifyZoneByName($_POST['name'], $_POST['name'], $_POST['v4_addr'], $_POST['v6_addr'], $_POST['v6_prefix'], $_POST['v6_cidr']);
            }
            $result_array['status'] = 'success';

            break;

        case 'mod_client':
            if ($_POST['name'] && getClientByName($_POST['name'])) {
                modifyClientByName($_POST['name'], $_POST['name'], $_POST['v4_addr'], $_POST['v6_addr'], $_POST['v6_suffix']);
            }
            $result_array['status'] = 'success';

            break;

        case 'del_zone':
            deleteZoneByName($_POST['name']);
            $result_array['status'] = 'success';
            
            break;

        case 'del_client':
            deleteClientByName($_POST['name']);
            $result_array['status'] = 'success';
            
            break;
        
        default:
            $result_array['status'] = 'failed';
            $result_array['message'] = 'Action not supported.';
    }

} else {
    // Auth failed
    $result_array['status'] = 'failed';
    $result_array['message'] = 'Auth failed.';
    echo json_encode($result_array);
    exit;
}

// Setup ip pool
$_ = setupEnvIpPool();
// Generate Serial
$_['meta']['serial'] = $GLOBALS['config']['serial'];
$_['meta']['ttl_default'] = $GLOBALS['config']['ttl_default'];

// generate nsdata
$nsdata = readNsdata();

// write nsdata (compare first)
$nsdata_old = file_get_contents($GLOBALS['config']['bind9_zone_filepath']);
if ($nsdata != $nsdata_old) {
    $_['meta']['serial'] = date('YmdH');
    $nsdata = readNsdata();
    file_put_contents($GLOBALS['config']['bind9_zone_filepath'], $nsdata);
}


// inform bind9 to reload
system('rndc reload');

// write config.php
writeConfig( $config );

echo json_encode($result_array);

?>