<?php
/*
 * @author	Nico Alt
 * @date	06.06.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
require_once __DIR__ . '/abstractResource.php';
class Courses extends AbstractResource {

	public function __construct($user) {
		parent::__construct($user);
		$this->resource = 'courses';
	}

	/**
	 * Get one or more courses.
	 */
	public function get($id = null) {
		$sql = "SELECT * FROM " . Database::$tableCourses;
		// Add where clause for ID
		if (isset($id)) {
			$id = $this->database->escape_string($id);
			$sql .= " WHERE id LIKE '$id'";
		}
		$query = $this->database->query($sql);
		if (!$query || $query->num_rows == 0) {
			return null;
		}
		$courses = Array();
		while($column = $query->fetch_array()) {
			$course = Array(
							'id' => $column['id'],
							'name' => $column['name'],
							'archived' => ($column['archived'] == '0') ? 'false' : 'true',
							'added' => $column['added'],
							'edited' => $column['edited']
							);
			$courses[] = $course;
		}
		return $courses;
	}

	/**
	 * Create a course.
	 */
	public function create($name) {
		$name = $this->database->escape_string($name);
		$sql = "INSERT INTO " . Database::$tableCourses . " (name) VALUES ('$name')";
		if ($this->database->query($sql)) {
			$id = $this->database->insert_id;
			if ($this->activities->log($this->user, Activities::ACTION_CREATE, $this->resource, $id)) {
				return $id;
			}
			$this->delete($id);
		}
		return null;
	}

	/**
	 * Update a course.
	 */
	public function update(
						$id,
						$name,
						$archived
					) {
		$id = $this->database->escape_string($id);
		$name = $this->database->escape_string($name);
		$archived = $this->database->escape_string($archived);
		$sql = "UPDATE " . Database::$tableCourses . " SET name = '$name', archived = '$archived' WHERE id = '$id'";
		if ($this->database->query($sql)) {
			$this->activities->log($this->user, Activities::ACTION_UPDATE, $this->resource, $id);
			return true;
		}
		return false;
	}

	/**
	 * Delete a course.
	 */
	public function delete($id) {
		$id = $this->database->escape_string($id);
		$sql = "DELETE FROM " . Database::$tableCourses . " WHERE id = '$id'";
		if ($this->database->query($sql)) {
			$this->activities->log($this->user, Activities::ACTION_DELETE, $this->resource, $id);
			return true;
		}
		return false;
	}

	/**
	 * Check if a course ID exists.
	 */
	public function checkById($id) {
		$id = $this->database->escape_string($id);
		$sql = 'SELECT id FROM ' . Database::$tableCourses . ' WHERE id = ' . $id . ' LIMIT 1';
		return $this->database->query($sql)->num_rows > 0;
	}

	/**
	 * Check if a course name exists.
	 */
	public function checkByName($name) {
		$name = $this->database->escape_string($name);
		$sql = "SELECT name FROM " . Database::$tableCourses . " WHERE name = '$name' LIMIT 1";
		return $this->database->query($sql)->num_rows > 0;
	}
}
?>
