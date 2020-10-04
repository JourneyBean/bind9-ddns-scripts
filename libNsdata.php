<?php

/**
 *  Read nsdata file and return raw string
 *  @author Johnson Liu
 *  @version v1.0
 */
function readNsdata() {

    require( __DIR__ . '/nsdata.php');

    return $nsdata;

}

/**
 *  Replace all labels in nsdata file
 *  @author Johnson Liu
 *  @version v1.0
 */
function translateNsdata( $ip_pool, $nsdata, $serial = false ) {

    $output_data = '';
    
    if ($serial) $ip_pool['serial'] = $serial;
    else $ip_pool['serial'] = date('YmdH');

    $nsdata_lines = explode(PHP_EOL, $nsdata);

    // process by line
    foreach ( $nsdata_lines as $line ) {

        $start = strpos($line, '\\');
        $end = strrpos($line, '\\');

        // need label
        if ( $start && $end ) {
            $label = substr($line, $start+1, $end-$start-1);
            // label exists
            if ( $ip_pool[$label] ) 
                $output_data = $output_data . substr($line, 0, $start) . $ip_pool[$label] . substr($line, $end+1, strlen($line)-$end) . PHP_EOL;
        
        // no label needed
        } else {
            $output_data = $output_data . $line . PHP_EOL;
        }
    }

    return $output_data;
}

?>