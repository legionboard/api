<?php
/*
 * @author	Nico Alt
 * @date	27.06.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
abstract class AbstractEndpoint {

	/**
	 * Property: api
	 * API basing on abstractAPI.
	 */
	protected $api = null;

	public function __construct($api) {
		$this->api = $api;
		require_once __DIR__ . '/../changes.php';
		$this->changes = new Changes();
		require_once __DIR__ . '/../courses.php';
		$this->courses = new Courses();
		require_once __DIR__ . '/../teachers.php';
		$this->teachers = new Teachers();
	}

	/**
	 * Returns value from super-global array $_GET.
	 */
	protected function getFromGET($key) {
		return filter_input(INPUT_GET, $key);
	}

	/**
	 * Returns value from super-global array $_POST.
	 */
	protected function getFromPOST($key) {
		return filter_input(INPUT_POST, $key);
	}
}
?>
