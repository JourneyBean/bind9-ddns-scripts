<?php

$config_json = '
{
    "version": "v1.0",
    "secret": "xxxxxxxxxxxxx",
    "serial": "2020010110",
    "zonefile_path": ".\/managed.zone",
    "ttl_default": 120,
    "zones": [
        {
            "name": "office",
            "v4_addr": "1.1.1.1",
            "v6_prefix": "2000:ffff:ffff:fff0::",
            "v6_cidr": "60"
        }
    ],
    "clients": [
        {
            "name": "pc",
            "zone": "office",
            "v6_suffix": "::1233:1222:1111:1111"
        },
        {
            "name": "testclient",
            "v4_addr": "10.0.0.1",
            "v6_suffix": "::234"
        }
    ]
}

';

?>