<?php

/**
 *  Expand ipv6 address
 */
function ip6Expand($ip){
    $hex = unpack("H*hex", inet_pton($ip));         
    $ip = substr(preg_replace("/([A-f0-9]{4})/", "$1:", $hex['hex']), 0, -1);

    return $ip;
}

/**
 *  Pack ipv6 address
 */
function ip6Compress($ip) {
    return inet_ntop(inet_pton($ip));
}

/**
 *  Get ipv6 prefix
 */
function ip6GetPrefix($ip, $cidr) {
    $prefix = '';
    $ip_expanded = ip6Expand($ip);
    $ip_grouped = explode(':', $ip_expanded);
    $ip_grouped_save_num = $cidr/4;

    for ( $i=0; $i<intval($cidr/16); $i++ ) {
        $prefix = $prefix . $ip_grouped[$i];
        $prefix = $prefix . ':';
    }

    // if need to spilt 
    if ( $cidr%16 ) {
        for ( $i=0; $i<intval($cidr%16/4); $i++ ) {
            $prefix = $prefix . substr($ip_grouped[$cidr/16], $i, 1);
        }
        // if need to cut
        if ( $cidr%16%4 ) {
            $current_num = substr($ip_grouped[$cidr/16], $cidr%16/4, 1);
            $current_num = hexdec($current_num);

            if ( $cidr%16%4 == 3 ) $current_num = $current_num & 0x0E;
            else if ( $cidr%16%4 == 2 ) $current_num = $current_num & 0x0C;
            else if ( $cidr%16%4 == 1 ) $current_num = $current_num & 0x08;

            $current_num = dechex($current_num);

            $prefix = $prefix . $current_num;
        }
        if (strlen($prefix)<35) $prefix = $prefix . '::';
    } else {
        if (strlen($prefix)<36) $prefix = $prefix . ':';
        else $prefix = substr($prefix, 0, strlen($prefix)-1);
    }

    return ip6Compress($prefix);
}

/**
 *  Get ipv6 suffix
 */
function ip6GetSuffix(string $ip, int $cidr) {
    $suffix = '';
    $ip_expanded = ip6Expand($ip);
    $ip_grouped = explode(':', $ip_expanded);
    $cidr = 128-$cidr;

    for ( $i=7; $i>7-intval($cidr/16); $i-- ) {
        $suffix = $ip_grouped[$i] . ':' . $suffix;
    }
    $suffix = substr($suffix, 0, strlen($suffix)-1);

    // if need to split
    if ( $cidr%16 ) {
        $suffix = ':' . $suffix;
        for ( $i=3; $i>3-intval($cidr%16/4); $i-- ) {
            $suffix = substr($ip_grouped[7-intval($cidr/16)], $i, 1) . $suffix;
        }
        // if need to cut
        if ( $cidr%16%4 ) {
            $current_num = substr($ip_grouped[7-intval($cidr/16)], 3-$cidr%16/4, 1);
            $current_num = hexdec($current_num);

            if ( $cidr%16%4 == 3 ) $current_num = $current_num & 0x07;
            else if ( $cidr%16%4 == 2 ) $current_num = $current_num & 0x03;
            else if ( $cidr%16%4 == 1 ) $current_num = $current_num & 0x01;

            $current_num = dechex($current_num);

            $suffix = $current_num . $suffix;
        }
        if (strlen($suffix)<35) $suffix = '::' . $suffix;
    } else {
        if (strlen($suffix)<36) $suffix = '::' . $suffix;
    }

    return ip6Compress($suffix);
}

?>