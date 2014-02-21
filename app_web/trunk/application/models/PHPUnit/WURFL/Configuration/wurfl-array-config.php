<?php

$resourcesDir = dirname(__FILE__) . '/../../resources';

$wurfl['main-file'] = $resourcesDir . "/wurfl-regression.zip";
$wurfl['patches'] = array($resourcesDir . "/new_web_browsers_patch.xml", $resourcesDir."/spv_patch.xml");


$persistence['provider'] = "memcache";
$persistence['params'] = array(
	"dir" => "../cache"
);

$cache['provider'] = "null";


$configuration["wurfl"] = $wurfl;
$configuration["persistence"] = $persistence;
$configuration["cache"] = $cache;




?>