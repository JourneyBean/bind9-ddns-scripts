<?php

$nsdata = '
; This is a template file of NS records
; Scripts will read this varable and replace \xxxxx\
;
; Please avoid using "serial" as your computer/zone name
; because it is used for generate serial number.

$TTL 3600
$ORIGIN example.com.

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

';

?>