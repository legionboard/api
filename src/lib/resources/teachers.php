<?php
/*
 * Copyright (C) 2016 - 2017 Nico Alt
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * See the file "LICENSE.md" for the full license governing this code.
 */
namespace LegionBoard\Resources;

require_once __DIR__ . '/abstractResource.php';

class TeachersResource extends AbstractResource {

	public function __construct($user) {
		parent::__construct($user);
		$this->resource = 'teachers';
	}

	/**
	 * Get one or more teachers.
	 */
	public function get($teacherID = null, $seeTimes = false) {
		$sql = "SELECT * FROM " . Database::$tableTeachers;
		// Add where clause for ID
		if (isset($teacherID)) {
			$teacherID = $this->database->escape_string($teacherID);
			$sql .= " WHERE id LIKE '$teacherID'";
		}
		$query = $this->database->query($sql);
		if (!$query || $query->num_rows == 0) {
			return null;
		}
		$teachers = Array();
		while($column = $query->fetch_array()) {
			$teacher = Array(
							'id' => $column['id'],
							'name' => $column['name'],
							'subjects' => $column['subjects'],
							'archived' => ($column['archived'] == '0') ? false : true,
							'added' => $seeTimes ? $column['added'] : '-',
							'edited' => $seeTimes ? $column['edited'] : '-'
							);
			$teachers[] = $teacher;
		}
		return $teachers;
	}

	/**
	 * Create a teacher.
	 */
	public function create($name, $subjects) {
		$name = $this->database->escape_string($name);
		$subjects = $this->database->escape_string($subjects);
		$sql = "INSERT INTO " . Database::$tableTeachers . " (name, subjects) VALUES ('$name', '$subjects')";
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
	 * Update a teacher.
	 */
	public function update($teacherID, $name, $subjects, $archived) {
		$teacherID = $this->database->escape_string($teacherID);
		$name = $this->database->escape_string($name);
		$subjects = $this->database->escape_string($subjects);
		$archived = $this->database->escape_string($archived);
		$sql = "UPDATE " . Database::$tableTeachers . " SET name = '$name', subjects = '$subjects', archived = '$archived' WHERE id = '$teacherID'";
		if ($this->database->query($sql)) {
			$this->activities->log($this->user, Activities::ACTION_UPDATE, $this->resource, $teacherID);
			return true;
		}
		return false;
	}

	/**
	 * Delete a teacher.
	 */
	public function delete($teacherID) {
		$teacherID = $this->database->escape_string($teacherID);
		$sql = "DELETE FROM " . Database::$tableTeachers . " WHERE id = '$teacherID'";
		if ($this->database->query($sql)) {
			$this->activities->log($this->user, Activities::ACTION_DELETE, $this->resource, $teacherID);
			return true;
		}
		return false;
	}

	/**
	 * Check if a teacher ID exists.
	 */
	public function checkById($teacherID) {
		$teacherID = $this->database->escape_string($teacherID);
		$sql = 'SELECT id FROM ' . Database::$tableTeachers . ' WHERE id = ' . $teacherID . ' LIMIT 1';
		return $this->database->query($sql)->num_rows > 0;
	}

	/**
	 * Check if a teacher name exists.
	 */
	public function checkByName($name) {
		$name = $this->database->escape_string($name);
		$sql = "SELECT name FROM " . Database::$tableTeachers . " WHERE name = '$name' LIMIT 1";
		return $this->database->query($sql)->num_rows > 0;
	}
}
?>
