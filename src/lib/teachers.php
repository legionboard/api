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
		$sql = "SELECT * FROM " . Database::$table_teachers . $sql_id;
		$query = $this->db->query($sql);
		if (!$query || $query->num_rows == 0) {
			return null;
		}
		$teachers = Array();
		while($column = $query->fetch_array()) {
			$teacher = Array(
							'id' => $column['id'],
							'name' => $column['name'],
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
		$sql = "INSERT INTO " . Database::$table_teachers . " (name) VALUES ('$name')";
		if ($this->db->query($sql)) {
			return $this->db->insert_id;
		}
		return null;
	}

	/**
	 * Update a teacher.
	 */
	public function update($id, $name) {
		$id = $this->db->escape_string($id);
		$name = $this->db->escape_string($name);
		$sql = "UPDATE " . Database::$table_teachers . " SET name = '$name' WHERE id = '$id'";
		return $this->db->query($sql);
	}

	/**
	 * Delete a teacher.
	 */
	public function delete($id) {
		$id = $this->db->escape_string($id);
		$sql = "DELETE FROM " . Database::$table_teachers . " WHERE id = '$id'";
		return $this->db->query($sql);
	}

	/**
	 * Check if a teacher ID exists.
	 */
	public function checkById($id) {
		$id = $this->db->escape_string($id);
		$sql = 'SELECT id FROM ' . Database::$table_teachers . ' WHERE id = ' . $id . ' LIMIT 1';
		if ($this->db->query($sql)->num_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * Check if a teacher name exists.
	 */
	public function checkByName($name) {
		$name = $this->db->escape_string($name);
		$sql = "SELECT name FROM " . Database::$table_teachers . " WHERE name = '$name' LIMIT 1";
		if ($this->db->query($sql)->num_rows > 0) {
			return true;
		}
		return false;
	}
}
?>
