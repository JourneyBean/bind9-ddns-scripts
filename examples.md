# Examples

## IPv4 NAT, IPv6 shareing prefix

Assume you need to setup ddns for your office area. The network conditions are:

- All computers shares one IPv4 address. (NAT)

- All computers shares one IPv6 Prefix. They've got IPv6 addresses via SLAAC.

- Your network gateway (router) allows specific access from wan to your lan via IPv6.

Simply, you only need to setup one zone and some clients. Zone's IPv4 address and IPv6 prefix need to update frequently, but clients' suffix don't need to change. They are "static".

Here's the sample ```config.php``` file:

``` php
<?php

$config_json = '
{
    "version": "v1.0",
    "secret": "12345",
    "serial": "2020010110",
    "zonefile_path": "\/etc\/bind\/managed.zone",
    "ttl_default": 120,
    "zones": [
        {
            "name": "office",
            "v6_cidr": "64"
        }
    ],
    "clients": [
        {
            "name": "pc",
            "zone": "office",
            "v6_suffix": "::1233:1222:1111:1111"
        },
        {
            "name": "printer",
            "zone": "office",
            "v6_suffix": "::2232:3323:2232:1122"
        },
        {
            "name": "file_server",
            "zone": "office",
            "v6_suffix": "::123"
        }
    ]
}

';

?>
```

And your domain name is ```myexample.net``` , you want to resolve the following domains:

- ```www.myexample.net``` to your pc

- ```printer.myexamle.net```to your printer

- ```cloud.myexample.net``` to your file server

Then you can edit ```nsdata.php``` like this:

``` php
<?php

$nsdata = '
; This is a template file of NS records
; Scripts will read this varable and replace \xxxxx\
;
; Please avoid using "serial" as your computer/zone name
; because it is used for generate serial number.

$TTL 3600
$ORIGIN myexample.net.

@	IN	SOA	ns1	root (
	\serial\	; do not edit it unless you know
	3H			; refresh
	15M			; retry
	1D			; expiry
	1			; minimum ttl
);

@		3600	IN	NS		ns1         ; your dns server\'s secondary domain
ns1		3600	IN	A		1.2.3.4     ; your dns server\'s ip

www     120     IN  A       \pc4\       ; Maybe you need to configure DNAT on your router in order to forword IPv4 traffics
www     120     IN  AAAA    \pc6\
printer 120     IN  AAAA    \printer6\
cloud   120     IN  AAAA    \file_server6\

';

?>
```

Finally, you can create a script on your pc/router and make it run periodly:

``` sh
ip4=$(curl -s http://v4.ipv6-test.com/api/myip.php)
ip6=$(curl -s http://v6.ipv6-test.com/api/myip.php)
curl --location --request GET "https://ns1.myexample.net/ddns/api.php?key=12345&action=update_zone&v4_addr=${ip4}&v6_prefix=${ip6}&cidr=64"
```

It seems that it is pushing your pc's IPv6 address but not a prefix to ddns server. Don't worry, server scripts will extract the prefix.

For tips of getting your ip address online (or looking for other sites to get your ip address), you can also refer to https://openwrt.org/docs/guide-user/services/ddns/client for more information.
