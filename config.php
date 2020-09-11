<?php

$config_raw = '

{
    "version": "v1.0",
    "secret": "xxxxxxxxxxxxx",
    "ns_serial": "2020091100",
    "zones": [
        {
            "name": "office",
            "v4_addr": "1.1.1.1",
            "v6_prefix": "2000:ffff:ffff:ffff::/64"
        }
    ],
    "clients": [
        {
            "name": "my_computer",
            "zone": "office"
            "v6_addr": "::1233:1222:1111:1111"
        }
    ]
}

';

?>