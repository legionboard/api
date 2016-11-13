<?php
/*
 * @author	Jan Weber
 * @date	13.11.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
require_once __DIR__ . '/abstractResource.php';
class SubjectsResource extends AbstractResource {

	public function __construct($user) {
		parent::__construct($user);
		$this->resource = 'subjects';
	}

	/**
	 * Get one or more subjects.
	 */
	public function get($id = null, $seeTimes = false) {
		$sql = "SELECT * FROM " . Database::$tableSubjects;
		// Add where clause for ID
		if (isset($id)) {
			$id = $this->database->escape_string($id);
			$sql .= " WHERE id LIKE '$id'";
		}
		$query = $this->database->query($sql);
		if (!$query || $query->num_rows == 0) {
			return null;
		}
		$subjects = Array();
		while($column = $query->fetch_array()) {
			$subject = Array(
							'id' => $column['id'],
							'name' => $column['name'],
							'shortcut' => $column['shortcut'],
							'archived' => ($column['archived'] == '0') ? false : true,
							'added' => $seeTimes ? $column['added'] : '-',
							'edited' => $seeTimes ? $column['edited'] : '-'
							);
			$subjects[] = $subject;
		}
		return $subjects;
	}

	/**
	 * Create a subject.
	 */
	public function create($name, $shortcut) {
		$name = $this->database->escape_string($name);
		$shortcut = $this->database->escape_string($shortcut);
		$sql = "INSERT INTO " . Database::$tableSubjects . " (name, shorrtcut) VALUES ('$name', '$shortcut')";
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
	 * Update a subject.
	 */
	public function update(
						$id,
						$name,
						$shortcut,
						$archived
					) {
		$id = $this->database->escape_string($id);
		$name = $this->database->escape_string($name);
		$shortcut = $this->database->escape_string($shortcut);
		$archived = $this->database->escape_string($archived);
		$sql = "UPDATE " . Database::$tableSubjects . " SET name = '$name', shortcut = '$shortcut', archived = '$archived' WHERE id = '$id'";
		if ($this->database->query($sql)) {
			$this->activities->log($this->user, Activities::ACTION_UPDATE, $this->resource, $id);
			return true;
		}
		return false;
	}

	/**
	 * Delete a subjects.
	 */
	public function delete($id) {
		$id = $this->database->escape_string($id);
		$sql = "DELETE FROM " . Database::$tableSubjects . " WHERE id = '$id'";
		if ($this->database->query($sql)) {
			$this->activities->log($this->user, Activities::ACTION_DELETE, $this->resource, $id);
			return true;
		}
		return false;
	}

	/**
	 * Check if a subject ID exists.
	 */
	public function checkById($id) {
		$id = $this->database->escape_string($id);
		$sql = 'SELECT id FROM ' . Database::$tableSubjects . ' WHERE id = ' . $id . ' LIMIT 1';
		return $this->database->query($sql)->num_rows > 0;
	}

	/**
	 * Check if a subject shortcut exists.
	 */
	public function checkByShortcut($shortcut) {
		$shortcut = $this->database->escape_string($shortcut);
		$sql = "SELECT name FROM " . Database::$tableSubjects . " WHERE shortcut = '$shortcut' LIMIT 1";
		return $this->database->query($sql)->num_rows > 0;
	}
}
?>
