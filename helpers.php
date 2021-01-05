<?php

if(! function_exists('mp_dd')) {
	function mp_dd(...$args) {
		foreach ($args as $arg) {
			echo '<pre>', var_dump($arg), '</pre><hr>';
		}

		die;
	}
}

if(! function_exists('mp_array_equal')) {
	function mp_array_equal($a, $b) {
		return (
			is_array($a)
			&& is_array($b)
			&& count($a) == count($b)
			&& array_diff($a, $b) === array_diff($b, $a)
		);
	}
}

if(! function_exists('mp_array_equal_and_same_order')) {
	function mp_array_equal_and_same_order($a, $b) {
		if(! mp_array_equal($a, $b)) {
			return false;
		}

		return $a === $b;
	}
}

if(! function_exists('mp_date')) {
	function mp_date($date) {
		\Carbon\Carbon::setLocale(explode('_', get_locale())[0]);

		if(! $date) {
			return null;
		}

		if(is_string($date) || $date instanceof \DateTime) {
			$date = new \Carbon\Carbon($date);
		}

		if(is_numeric($date)) {
			$date = \Carbon\Carbon::createFromTimestamp($date, get_option('timezone_string'));
		}

		return $date;
	}
}

if(! function_exists('mp_date_format')) {
	function mp_date_format($date, $time = false) {
		$date = mp_date($date);
		$format = get_option('date_format') . ($time ? ' '.get_option('time_format') : '');

		return $date ? $date->format($format) : '';
	}
}