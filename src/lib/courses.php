<?php
/*
 * @author	Nico Alt
 * @date	06.06.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
class Courses {

	/**
	 * Connect with the database.
	 */
	public function __construct() {
		require_once __DIR__ . '/database.php';
		$database = new Database();
		$this->db = $database->get();
	}

	/**
	 * Get one or more courses.
	 */
	public function get($id = null) {
		// Add where clause for ID
		if (isset($id)) {
			$id = $this->db->escape_string($id);
			$sql_id = " WHERE id LIKE '$id'";
		}
		$sql = "SELECT * FROM " . Database::$table_courses . $sql_id;
		$query = $this->db->query($sql);
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
		$name = $this->db->escape_string($name);
		$sql = "INSERT INTO " . Database::$table_courses . " (name) VALUES ('$name')";
		if ($this->db->query($sql)) {
			return $this->db->insert_id;
		}
		return null;
	}

	/**
	 * Update a course.
	 */
	public function update($id, $name, $archived) {
		$id = $this->db->escape_string($id);
		$name = $this->db->escape_string($name);
		$archived = $this->db->escape_string($archived);
		$sql = "UPDATE " . Database::$table_courses . " SET name = '$name', archived = '$archived' WHERE id = '$id'";
		return $this->db->query($sql);
	}

	/**
	 * Delete a course.
	 */
	public function delete($id) {
		$id = $this->db->escape_string($id);
		$sql = "DELETE FROM " . Database::$table_courses . " WHERE id = '$id'";
		return $this->db->query($sql);
	}

	/**
	 * Check if a course ID exists.
	 */
	public function checkById($id) {
		$id = $this->db->escape_string($id);
		$sql = 'SELECT id FROM ' . Database::$table_courses . ' WHERE id = ' . $id . ' LIMIT 1';
		return $this->db->query($sql)->num_rows > 0;
	}

	/**
	 * Check if a course name exists.
	 */
	public function checkByName($name) {
		$name = $this->db->escape_string($name);
		$sql = "SELECT name FROM " . Database::$table_courses . " WHERE name = '$name' LIMIT 1";
		return $this->db->query($sql)->num_rows > 0;
	}
}
?>
