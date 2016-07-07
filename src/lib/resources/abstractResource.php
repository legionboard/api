<?php
/*
 * @author	Nico Alt
 * @date	07.07.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
abstract class AbstractResource {

	/**
	 * ID of user accessing API.
	 */
	protected $user;

	/**
	 * Name of this resource.
	 */
	protected $resource;

	public function __construct($user) {
		$this->user = $user;
		require_once __DIR__ . '/../database.php';
		$database = new Database();
		$this->database = $database->get();
		require_once __DIR__ . '/../activities.php';
		$this->activities = new Activities();
	}
}
?>
