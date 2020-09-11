<?php

/**
 * index.php
 * 
 * Index file handling input data
 * 
 */

require_once ('./config.php');

$input_key = $_POST['key'];
$input_action = $_POST['action'];

$result_array = [
    'status' => '',
    'message' => ''
];

if ($input_key != $config['key']) {
    // Auth success
    
    switch($input_action) {

        case 'new_zone':
            
            break;

        case 'new_client':
            
            break;

        case 'mod_zone':
            
            break;

        case 'mod_client':
            
            break;

        case 'del_zone':
            
            break;

        case 'del_client':
            
            break;
        
        default:
            result_array['status'] = 'failed';
            $result_array['message'] = 'Action not supported.';
    }

} else {
    // Auth failed
    $result_array['status'] = 'failed';
    $result_array['message'] = 'Auth failed.';
}

echo json_encode($result_array);

?>