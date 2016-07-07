<?php
/*
 * @author	Nico Alt
 * @date	16.03.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
class Teachers {

	// Resource affected in this class
	const THIS_RESOURCE = 'teachers';

	public function __construct($user) {
		$this->user = $user;
		require_once __DIR__ . '/../database.php';
		$database = new Database();
		$this->db = $database->get();
		require_once __DIR__ . '/../activities.php';
		$this->activities = new Activities();
	}

	/**
	 * Get one or more teachers.
	 */
	public function get($teacherID = null) {
		$sql = "SELECT * FROM " . Database::$tableTeachers;
		// Add where clause for ID
		if (isset($teacherID)) {
			$teacherID = $this->db->escape_string($teacherID);
			$sql .= " WHERE id LIKE '$teacherID'";
		}
		$query = $this->db->query($sql);
		if (!$query || $query->num_rows == 0) {
			return null;
		}
		$teachers = Array();
		while($column = $query->fetch_array()) {
			$teacher = Array(
							'id' => $column['id'],
							'name' => $column['name'],
							'archived' => ($column['archived'] == '0') ? 'false' : 'true',
							'added' => $column['added'],
							'edited' => $column['edited']
							);
			$teachers[] = $teacher;
		}
		return $teachers;
	}

	/**
	 * Create a teacher.
	 */
	public function create($name) {
		$name = $this->db->escape_string($name);
		$sql = "INSERT INTO " . Database::$tableTeachers . " (name) VALUES ('$name')";
		if ($this->db->query($sql)) {
			$id = $this->db->insert_id;
			if ($this->activities->log($this->user, Activities::ACTION_CREATE, self::THIS_RESOURCE, $id)) {
				return $id;
			}
			$this->delete($id);
		}
		return null;
	}

	/**
	 * Update a teacher.
	 */
	public function update($teacherID, $name, $archived) {
		$teacherID = $this->db->escape_string($teacherID);
		$name = $this->db->escape_string($name);
		$archived = $this->db->escape_string($archived);
		$sql = "UPDATE " . Database::$tableTeachers . " SET name = '$name', archived = '$archived' WHERE id = '$teacherID'";
		if ($this->db->query($sql)) {
			$this->activities->log($this->user, Activities::ACTION_UPDATE, self::THIS_RESOURCE, $teacherID);
			return true;
		}
		return false;
	}

	/**
	 * Delete a teacher.
	 */
	public function delete($teacherID) {
		$teacherID = $this->db->escape_string($teacherID);
		$sql = "DELETE FROM " . Database::$tableTeachers . " WHERE id = '$teacherID'";
		if ($this->db->query($sql)) {
			$this->activities->log($this->user, Activities::ACTION_DELETE, self::THIS_RESOURCE, $teacherID);
			return true;
		}
		return false;
	}

	/**
	 * Check if a teacher ID exists.
	 */
	public function checkById($teacherID) {
		$teacherID = $this->db->escape_string($teacherID);
		$sql = 'SELECT id FROM ' . Database::$tableTeachers . ' WHERE id = ' . $teacherID . ' LIMIT 1';
		return $this->db->query($sql)->num_rows > 0;
	}

	/**
	 * Check if a teacher name exists.
	 */
	public function checkByName($name) {
		$name = $this->db->escape_string($name);
		$sql = "SELECT name FROM " . Database::$tableTeachers . " WHERE name = '$name' LIMIT 1";
		return $this->db->query($sql)->num_rows > 0;
	}
}
?>
