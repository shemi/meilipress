<?php

if (class_exists('\Shemi\MeiliPress\MeiliPress')) {
    $plugin = new \Shemi\MeiliPress\MeiliPress(realpath(__DIR__ . '/../'));

    do_action('sp/mp/loaded');

    return $plugin;
}
