# bind9-ddns-scripts

Provides a simple API set to update bind9 NS records.

## Installation

First you need PHP(>=7.2.0 recommended) installed on your server. Then simply copy these files to your webroot directory.

## Configuration

You have to edit some files to get DDNS sever to work. Generally, you need to edit configuration file ```config.php``` and zone file ```nsdata.php```.

### ```config.php``` file: 

This file contains a php string variable which stores formatted json-type configurations. We recommend you to change ```secret```, ```zonefile_path```, ```zones``` and ```clients``` to satisfy your needs.

- ```secret``` saves the passkey to your ddns server.

- ```zonefile_path``` saves the path to bind9 zone file. Typically ```/etc/bind/managed.zone```. If this file doesn't exists, the scripts will create one. Please make sure pointing to the right path.

- ```ttl_default``` is not used. (Will be added in future version.)

- ```zones```
    
    ```zones``` saves information to a zone. It can describe a group of clients. You must define ```name``` for it, and you can optional define ```v4_addr```, ```v6_addr```, ```v6_prefix``` and ```v6_cidr```.

    Here is an example of ```zone``` section:

    ``` json
    ...
    "zones": [
        {
            "name": "office",
            "v4_addr": "1.2.3.4",
            "v6_prefix": "2000:1234:2233:5600::",
            "v6_cidr": 56
        },
        {
            ...
        }
    ],
    ...
    ```

- ```clients```

    ```clients``` saves information to an individual client. You must define ```name``` for it, and the following are optional: ```zone```, ```v4_addr```, ```v6_addr```, ```v6_suffix``` .

    Here is an example of ```client``` section:

    ``` json
    ...
    "clients": [
        {
            "name": "pc",
            "zone": "office",
            "v6_suffix": "::567"
        },
        {
            ...
        }
    ],
    ...
    ```

#### How scripts selecting client's address

The scripts will look for a client's ip address from ```config.php``` obeying the following orders:

For IPv4 address:

1. Look for client's ```v4_addr```, if null then:

2. Look for client's ```zone```'s ```v4_addr```, if have no zone or null, then:

3. Address is null.

For IPv6 address:

1. Look for client's ```v6_addr```, if null then:

2. Look for client's ```v6_suffix``` , ```zone```'s ```v6_prefix``` and ```v6_cidr```. ```v6_cidr``` will be set to ```64``` if null. Then scripts will merge this prefix with suffix as returning address. If ```zone```'s ```v6_prefix``` not found, then:

3. Look for ```zone```'s ```v6_addr```. If null then:

4. Address is null.

### ```nsdata.php``` file:

- First you have to set ```$ORIGIN``` to your domain.

- Then add your records.

Here's an example:

```
$TTL 3600
$ORIGIN example.com

@	IN	SOA	ns1	root (
	\serial\	; do not edit it unless you know
	3H			; refresh
	15M			; retry
	1D			; expiry
	1			; minimum ttl
);

@		3600	IN	NS		ns1
ns1		3600	IN	A		1.2.3.4
ns2		3600	IN	A		3.4.5.6
ns1v6	3600	IN	AAAA	2000:1000::1
ns2v6	3600	IN	AAAA	2000:1111::1

; Static Addresses like these:
www		3600	IN	A		2.3.4.5
www		3600	IN	AAAA	2000:1234::1

; Dynamic Addresses like these:
cloud	30		IN	A		\office4\
cloud	30		IN	AAAA	\pc6\
cloud	30		IN	A		\pc4\
```

This file will be generated into bind9 zone file. Scripts will replace all the labels with "\\" surrounded into ip address or other parameters like serial. You can use both ```zone's name + 4/6``` and ```client's name + 4/6```. When a label is not found, scripts will remove this line.

Some special label is reserved for controlling dynamic paremeters. Now we only have ```\serial\``` used. ```\serial\``` will be generated into datetime formatted to ```YYYYmmddHH``` . 

This file will be generated into this:

```
$TTL 3600
$ORIGIN example.com

@	IN	SOA	ns1	root (
	2020101010	; do not edit it unless you know
	3H			; refresh
	15M			; retry
	1D			; expiry
	1			; minimum ttl
);

@		3600	IN	NS		ns1
ns1		3600	IN	A		1.2.3.4
ns2		3600	IN	A		3.4.5.6
ns1v6	3600	IN	AAAA	2000:1000::1
ns2v6	3600	IN	AAAA	2000:1111::1

; Static Addresses like these:
www		3600	IN	A		2.3.4.5
www		3600	IN	AAAA	2000:1234::1

; Dynamic Addresses like these:
cloud	30		IN	A		1.2.3.4
cloud	30		IN	AAAA	2000:1234:2233:5600::567
cloud	30		IN	A		1.2.3.4
```

You can refer to https://www.cloudflare.com/learning/dns/glossary/dns-zone/ , http://www.steves-internet-guide.com/dns-zones-explained/ for NS zone file details.

### Permissions Setup

You need to allow scripts to modify ```config.php``` and write file to your ```bind9 zone directory```. You also need to grant access to ```rndc``` . Otherwise, your server can't load new zonefile in time. 

How to allow user ```www-data``` to access ```rndc```:

``` sh
setfacl -m www-data:r-x /etc/bind/rndc.key 
```

## API Usage

Simply using ```GET``` method to manage your server.

Here's an example of updating a zone:

``` sh
curl --location --request GET 'https://nic.example.com/api.php?key=12345&action=update_zone&name=office&v4_addr=1.2.3.4&v6_prefix=2000:1222:2222:2222:222::
```

### ```key```

Just fill in your secret.

### ```action```

Scripts support these actions:

|        | Add   | Update | Modify | Delete |
|--------|-------|--------|--------|--------|
| Zone   | ```new_zone``` | ```update_zone``` | ```mod_zone``` | ```del_zone``` | 
| Client | ```new_client``` | ```update_client``` | ```mod_client``` | ```del_client``` |

Of all these actions, only ```name``` parameter is required. Other parameters like ```v4_addr```, ```v6_addr```, ```v6_prefix``` are all optional.

For more examples of API Usage, Please refer to ```./examples.md``` .

#### Differences between update and modify

Action ```update``` will ignore null parameters but ```modify``` will write null parameters into your config. When performing a ip change shot, it's recommend to use ```update``` action.

## Testing Server

When everything is ready, we recommend you test your zone file first. Just using the following command:

``` sh
bind_testzone example.com /path/to/zonefile.zone
```

If no error found, then you can enjoy it.
