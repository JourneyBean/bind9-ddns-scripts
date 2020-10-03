<?php

require_once('./libEnv.php');
require_once('./libConfig.php');
require_once('./libNsdata.php');
require_once('./libIp6Addr.php');

echo ip6GetPrefix("FE00::1234", 64);

?>