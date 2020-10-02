<?php

$config_json = '

{
    "version": "v1.0",
    "secret": "xxxxxxxxxxxxx",
    "serial": "2020010110",
    "bind9_zone_filepath": "/etc/bind/managed.zone"
    "ttl_default": 120,
    "zones": [
        {
            "name": "office",
            "v4_addr": "1.1.1.1",
            "v6_prefix": "2000:ffff:ffff:ffff::"
        }
    ],
    "clients": [
        {
            "name": "my_computer",
            "zone": "office"
            "v6_suffix": "::1233:1222:1111:1111"
        }
    ]
}

';

?>