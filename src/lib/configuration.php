<?php
/*
 * @author	Nico Alt
 * @date	16.03.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
class Configuration {

	/*
	 * Get a key in a group from the configuration.
	 */
	public static function get($group, $key) {
		if (file_exists(__DIR__ . "/configuration.ini")) {
			$configuration = parse_ini_file(__DIR__ . "/configuration.ini", true);
			return $configuration[$group][$key];
		}
		return '';
	}
}
?>
