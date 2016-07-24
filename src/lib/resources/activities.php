<?php
/*
 * @author	Nico Alt
 * @date	24.07.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
require_once __DIR__ . '/abstractResource.php';
class ActivitiesResource extends AbstractResource {

	public function __construct($user) {
		parent::__construct($user);
		$this->resource = 'activities';
	}

	/**
	 * Get one or more activities.
	 */
	public function get($id = null) {
		$sql = "SELECT * FROM " . Database::$tableActivities;
		// Add where clause for ID
		if (isset($id)) {
			$id = $this->database->escape_string($id);
			$sql .= " WHERE id LIKE '$id'";
		}
		$query = $this->database->query($sql);
		if (!$query || $query->num_rows == 0) {
			return null;
		}
		$activities = Array();
		while($column = $query->fetch_array()) {
			$activity = Array(
							'id' => $column['id'],
							'user' => $column['user'],
							'action' => $column['action'],
							'affectedResource' => $column['affectedResource'],
							'affectedID' => $column['affectedID'],
							'time' => $column['time']
							);
			$activities[] = $activity;
		}
		return $activities;
	}
}
?>
