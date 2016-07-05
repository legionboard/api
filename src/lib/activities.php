<?php
/*
 * @author	Nico Alt
 * @date	05.07.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
class Activities {

	/**
	 * Index keys for actions.
	 */
	const ACTION_CREATE = 0;
	const ACTION_UPDATE = 1;
	const ACTION_DELETE = 2;
	

	/**
	 * Connect with the database.
	 */
	public function __construct() {
		require_once __DIR__ . '/database.php';
		$database = new Database();
		$this->db = $database->get();
	}

	/**
	 * Log an action.
	 */
	public function log($user, $action, $affectedResource, $affectedID) {
		$user = $this->db->escape_string($user);
		$action= $this->db->escape_string($action);
		$affectedResource= $this->db->escape_string($affectedResource);
		$affectedID= $this->db->escape_string($affectedID);
		$sql = "INSERT INTO " . Database::$tableActivities .
				" (" .
					"user," .
					"action," .
					"affectedResource," .
					"affectedID" .
				") " .
				"VALUES (" .
					"'$user'," .
					"'$action'," .
					"'$affectedResource'," .
					"'$affectedID'" .
				")";
		return $this->db->query($sql);
	}
}
?>
