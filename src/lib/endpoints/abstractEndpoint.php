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

	public function __construct($api, $user) {
		$this->api = $api;
		require_once __DIR__ . '/../resources/activities.php';
		$this->activities = new ActivitiesResource($user);
		require_once __DIR__ . '/../resources/changes.php';
		$this->changes = new ChangesResource($user);
		require_once __DIR__ . '/../resources/courses.php';
		$this->courses = new CoursesResource($user);
		require_once __DIR__ . '/../resources/teachers.php';
		$this->teachers = new TeachersResource($user);
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
