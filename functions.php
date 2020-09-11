<?php

require_once('./config.php');

$config  = json_decode($config_raw, true);
$zones   = $config['zones'];
$clients = $config['clients'];

// get zone or client by name
function get_by_name( $from, $name ) {
    foreach ($from as $current_zone) {
        if ($current_zone['name'] == $name) return $current_zone;
    }
    return false;
}

function combine_v6( $prefix, $suffix ) {
    
}


function load_ip_pool() {
    $pool = [];

    // loda all zones's v4 addresses
    foreach ($zones as $current_zone) {

        // pool->zone_name->default->v4
        $pool[$current_zone['name']]['default']['v4'] = ($current_zone['v4_addr'])?$current_zone['v4_addr']:'not_found';

    }

    // load all client's v4 addresses
    foreach ($clients as $current_client) {

        // if has zone
        if ( $current_client['zone'] ) {

            // has zone
            $current_zone = get_by_name($current_client['zone']);

            // pool->zone_name->client_name->v4
            $pool[$current_zone['name']][$current_client['name']]['v4'] = $current_client['v4_addr'] ?
                                                                                $current_client['v4_addr'] :
                                                                                ($current_zone['v4_addr'] ?
                                                                                    $current_zone['v4_addr'] :
                                                                                    'not_found'
                                                                                );

        } else {

            // no zone
            // pool->client_name->v4
            $pool[$current_client['name']]['v4'] = $current_client['v4_addr'] ? $current_client['v4_addr'] : 'not_found';
        }

    }

    // load all v6 addresses
    foreach ($config['clients'] as $current_client) {

        if ($current_client['zone']) {

            // has zone
            $current_zone = get_by_name($current_client['zone']);

            // pool->zone_name->client_name->v6
            $pool[$current_zone['name']][$current_client['name']]['v6'] = 
            
        } else {
            // no zone

        }
    }

    return $pool;
}


?>