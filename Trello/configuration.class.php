<?php
namespace Valenciohoffman\Service\Trello;

/**
 * @Created in PhpStorm 2017.1.
 * @author: Valencio Hoffman
 * @Date: 03-05-17
 * @license: Apache 2.0
 *
 * @version: 1.0.0
 *
 * Configuration class
 * This class is created to make an array where default options
 * and new options are merged in to each other for one array with
 * options that the user wanted
 */
class Configuration {

	private static $expirations = array(0 => '1hour', 1 => '1day', 2 => '30days', 3 => 'never');

	/**
	 * Parses give options against default options.
	 *
	 * The MIT License (MIT)
	 * Copyright (c) 2013 Steven Maguire
	 *
	 * @param  array $options
	 * @param  array $defaults
	 *
	 * @return array
	 */
	public static function parseDefaultOptions( $options = [], $defaults = [] ) {
		array_walk( $defaults, function ( $value, $key ) use ( &$options ) {
			if($key == "expiration") {
				$options[ $key ] = in_array($value, static::$expirations) ? $value : "never";
			} else if ( ! isset( $options[ $key ] ) ) {
				$options[ $key ] = $value;
			}
		});

		return $options;
	}

	/**
	 * Updates configuration settings with collection of key value pairs.
	 *
	 * The MIT License (MIT)
	 * Copyright (c) 2013 Steven Maguire
	 *
	 * @param array $settings
	 * @param array $defaultSettings
	 *
	 * @return array
	 */
	public static function setMany( $settings = [], $defaultSettings = [] ) {
		return static::parseDefaultOptions( $settings, $defaultSettings );
	}
}