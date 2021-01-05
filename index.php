<?php

/**
 * Plugin Name: MeiliPress
 * Plugin URI: https://github.com/shemi/meilipress
 * Description: MeiliSearch for wordpress
 * Version: dev-master
 * Author: Shemi Perez
 * Author URI: https://github.com/shemi
 * Text Domain: meilipress
 * Domain Path: localization
 *
 */

require_once __DIR__.'/helpers.php';
require_once __DIR__ . '/bootstrap/autoload.php';

define('MP_TD', 'meilipress');

$GLOBALS['MEILIPRESS'] = require_once __DIR__ . '/bootstrap/plugin.php';

if (! function_exists('MP')) {

    /**
     * Return the instance of plugin.
     *
     * @return \Shemi\MeiliPress\MeiliPress
     */
    function MP()
    {
        return $GLOBALS['MEILIPRESS'];
    }
}
