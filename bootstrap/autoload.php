<?php

define( 'MEILIPRESS_START', microtime(true));

if(file_exists(__DIR__ . '/../vendor/autoload.php')) {
	require_once __DIR__ . '/../vendor/autoload.php';
}