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

class CoursesResource extends AbstractResource {

	public function __construct($user) {
		parent::__construct($user);
		$this->resource = 'courses';
	}

	/**
	 * Get one or more courses.
	 */
	public function get($id = null, $seeTimes = false) {
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
							'subjects' => $column['subjects'],
							'archived' => ($column['archived'] == '0') ? false : true,
							'added' => $seeTimes ? $column['added'] : '-',
							'edited' => $seeTimes ? $column['edited'] : '-'
							);
			$courses[] = $course;
		}
		return $courses;
	}

	/**
	 * Create a course.
	 */
	public function create($name, $subjects) {
		$name = $this->database->escape_string($name);
		$subjects = $this->database->escape_string($subjects);
		$sql = "INSERT INTO " . Database::$tableCourses . " (name, subjects) VALUES ('$name', '$subjects')";
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
						$subjects,
						$archived
					) {
		$id = $this->database->escape_string($id);
		$name = $this->database->escape_string($name);
		$subjects = $this->database->escape_string($subjects);
		$archived = $this->database->escape_string($archived);
		$sql = "UPDATE " . Database::$tableCourses . " SET name = '$name', subjects = '$subjects', archived = '$archived' WHERE id = '$id'";
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

	/**
	 * Check if a course shortcut exists.
	 */
	public function checkByShortcut($shortcut) {
		$shortcut = $this->database->escape_string($shortcut);
		$sql = "SELECT name FROM " . Database::$tableCourses . " WHERE shortcut = '$shortcut' LIMIT 1";
		return $this->database->query($sql)->num_rows > 0;
	}
}
?>
