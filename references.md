# References to bind9-ddns-scripts API

We use a zone to define a network zone; a client to difine an individual device.

## Structure

Briefly, we use an area to define a network that shares a same gateway. A client to define a network card.

### Areas

An area is commonly used as a network that shares one outbound routers. Some options to an area are:

- v4_addresses: IPv4 Addresses assigned to this network
- v6_prefixes: IPv6 Addresses assigned to this network

### Clients

A client is commonly used as a unique network devices that have a unique ip address. Options to clients are:

- zone: Assign a zone for this client to share the zone's IPv6 prefix.
- v4_addresses: Leave null unless you want to configure it.
- v6_prefix: Leave null unless you want to configure it.
- v6_addresses: Typically an EUI-64 address. This will automatically combined to related area's IPv6 prefix. 

Generally, the scripts will behave somehow like this to find a device's addesses:

IPv4:
1. Client's v4 address
2. Zone's v4 address

IPv6:
1. Client's address
2. Client's address with client's prefix
3. Client's address with area's prefix
4. Area's address with area's prefix


## API Usage

Generally, you can use this api by POSTing serialized data:

```
/api/index.php?action=add_client&name=router&zone=office&v6_addr=::333
```

### Adding

Adding zone or clients:
```
?action=add_zone&name=office&v4_addr=1.1.1.1&v6_prefix=1234::/64
?action=add_client&name=my-pc&zone=office&v6_addr=1233:2334:3445:4556
```

### Modifying

Modifying zone or clients:
```
?action=mod_zone&name=office&v4_addr=1.1.1.1
```

### Deleting

Deleting zone or clients:
```
?action=del_zone&name=office
```

## Typically usage

### Dynamic IPv6 Address

Here is an example of configuring dynamic IPv6 address on own DDNS server:

- First, adding a zone, configuring a v6 prefix;
- Second, adding a client, only configure a v6 address's suffix like "::3aa"
- Then you can only update your home's v6 address's prefix.

### Dynamic IPv4 Address

