<?php

/**
 *  Convert IPv6 address to long int
 *  From Internet, source unknown..
 */
function ip2long6($ipv6) {
    $ip_n = inet_pton($ipv6);
    $bits = 15; // 16 x 8 bit = 128bit
    while ($bits >= 0) {
        $bin = sprintf("%08b",(ord($ip_n[$bits])));
        $ipv6long = $bin.$ipv6long;
        $bits--;
    }
    return gmp_strval(gmp_init($ipv6long,2),10);
}

/**
 *  Convert long int to IPv6 address
 *  From Internet, source unknown..
 */
function long2ip6($ipv6long) {
  
    $bin = gmp_strval(gmp_init($ipv6long,10),2);
    if (strlen($bin) < 128) {
        $pad = 128 - strlen($bin);
        for ($i = 1; $i <= $pad; $i++) {
        $bin = "0".$bin;
        }
    }
    $bits = 0;
    while ($bits <= 7) {
        $bin_part = substr($bin,($bits*16),16);
        $ipv6 .= dechex(bindec($bin_part)).":";
        $bits++;
    }
    // compress
  
    return inet_ntop(inet_pton(substr($ipv6,0,-1)));
}


function ip6GetPrefix( string $addr, int $cidr = 128 ) {
    return (($addr >> $cidr) & 0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF ) << $cidr;
}

function ip6GetSuffix( string $addr, int $cidr = 0 ) {
    $host_cidr = 128 - $cidr;
    return (($addr << $host_cidr) & 0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF) >> $host_cidr;
}

function ip6MergeAddr( string $prefix, string $suffix, $cidr = -1 ) {
    // Just merge
    if ( cidr == -1 ) {
        return $prefix | $suffix;
    }
    // Calculate CIDR then merge
    else {
        $prefix = ip6GetPrefix($prefix, $cidr);
        $suffix = ip6GetSuffix($suffix, $cidr);
        return $prefix | $suffix;
    }
}

?>