<?php
/*
 * @author	Nico Alt
 * @date	16.03.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
class Teachers {	

	/**
	 * Connect with the database.
	 */
	public function __construct() {
		require_once __DIR__ . '/database.php';
		$database = new Database();
		$this->db = $database->get();
	}

	/**
	 * Get one or more teachers.
	 */
	public function get($id = null) {
		// Add where clause for ID
		if (isset($id)) {
			$id = $this->db->escape_string($id);
			$sql_id = " WHERE id LIKE '$id'";
		}
		$sql = "SELECT * FROM " . Database::$tableTeachers . $sql_id;
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
			return $this->db->insert_id;
		}
		return null;
	}

	/**
	 * Update a teacher.
	 */
	public function update($id, $name, $archived) {
		$id = $this->db->escape_string($id);
		$name = $this->db->escape_string($name);
		$archived = $this->db->escape_string($archived);
		$sql = "UPDATE " . Database::$tableTeachers . " SET name = '$name', archived = '$archived' WHERE id = '$id'";
		return $this->db->query($sql);
	}

	/**
	 * Delete a teacher.
	 */
	public function delete($id) {
		$id = $this->db->escape_string($id);
		$sql = "DELETE FROM " . Database::$tableTeachers . " WHERE id = '$id'";
		return $this->db->query($sql);
	}

	/**
	 * Check if a teacher ID exists.
	 */
	public function checkById($id) {
		$id = $this->db->escape_string($id);
		$sql = 'SELECT id FROM ' . Database::$tableTeachers . ' WHERE id = ' . $id . ' LIMIT 1';
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
