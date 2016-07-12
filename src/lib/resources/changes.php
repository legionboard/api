<?php
/*
 * @author	Nico Alt
 * @date	16.03.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
require_once __DIR__ . '/abstractResource.php';
class Changes extends AbstractResource {

	/**
	 * Connect with the database.
	 */
	public function __construct($user) {
		parent::__construct($user);
		$this->resource = 'changes';
	}

	/**
	 * Get one or more changes.
	 */
	public function get(
						$id = null,
						$teachers = null,
						$courses = null,
						$coveringTeacher = null,
						$startBy = null,
						$endBy = null,
						$seeReasons = false,
						$seePrivateTexts = false
					) {
		$changes = Array();
		// Filter by teachers
		$filteredTeachers = array_filter($teachers);
		if (!empty($filteredTeachers)) {
			sort($teachers);
			foreach ($teachers as $teacher) {
				// Add where clause for teacher
				$teacher = $this->database->escape_string($teacher);
				$sqlTeachers .= (empty($sqlTeachers) ? "" : " OR ") . "teacher LIKE '$teacher'";
			}
		}
		// Filter by courses
		$filteredCourses = array_filter($courses);
		if (!empty($filteredCourses)) {
			sort($courses);
			foreach ($courses as $course) {
				// Add where clause for course
				$course = $this->database->escape_string($course);
				$sqlCourses .= (empty($sqlCourses) ? "" : " OR ") . "course LIKE '$course'";
			}
		}
		// Filter by ID
		if (isset($id)) {
			$id = $this->database->escape_string($id);
			$sqlID = " id LIKE '$id'";
		}
		// Filter by covering teacher
		if (isset($coveringTeacher)) {
			$coveringTeacher = $this->database->escape_string($coveringTeacher);
			$sqlCoveringTeacher = " coveringTeacher LIKE '$coveringTeacher'";
		}
		// Build SELECT
		$sql = "SELECT * FROM " . Database::$tableChanges;
		if (!empty($sqlTeachers) || !empty($sqlCourses) || !empty($sqlID) || !empty($sqlCoveringTeacher)) {
			$sql .= " WHERE";
			if (!empty($sqlTeachers)) {
				$sql .= " (" . $sqlTeachers . ")";
			}
			if (!empty($sqlCourses)) {
				if (!empty($sqlTeachers)) {
					$sql .= " AND";
				}
				$sql .= " (" . $sqlCourses . ")";
			}
			if ((!empty($sqlTeachers) || !empty($sqlCourses)) && !empty($sqlID)) {
				$sql .= " AND";
			}
			$sql .= $sqlID;
			if ((!empty($sqlTeachers) || !empty($sqlCourses)) && !empty($sqlCoveringTeacher)) {
				$sql .= " AND";
			}
			$sql .= $sqlCoveringTeacher;

		}
		$query = $this->database->query($sql);
		if ($query && $query->num_rows != 0) {
			while($column = $query->fetch_array()) {
				// Filter out if end of change is before given start
				if ($startBy != null) {
					$dtColumnEnd = new DateTime(substr($column['endingDate'], 0, 10));
					$dtStart = new DateTime($startBy);
					if ($dtColumnEnd < $dtStart) {
						continue;
					}
				}
				// Filter out if start of change is after given end
				if ($endBy != null) {
					$dtColumnStart = new DateTime(substr($column['startingDate'], 0, 10));
					$dtEnd = new DateTime($endBy);
					if ($dtColumnStart > $dtEnd) {
						continue;
					}
				}
				$change = Array(
							'id' => $column['id'],
							'startingDate' => $column['startingDate'],
							'startingHour' => $column['startingHour'],
							'endingDate' => $column['endingDate'],
							'endingHour' => $column['endingHour'],
							'type' => $column['type'],
							'course' => $column['course'],
							'teacher' => $column['teacher'],
							'coveringTeacher' => $column['coveringTeacher'],
							'text' => $column['text'],
							'reason' => $seeReasons ? $column['reason'] : '-',
							'privateText' => $seePrivateTexts ? $column['privateText'] : '-',
							'added' => $column['added'],
							'edited' => $column['edited']
							);
				$changes[] = $change;
			}
		}
		$filteredChanges = array_filter($changes);
		if (empty($filteredChanges)) {
			return null;
		}
		return $changes;
	}

	/**
	 * Create a change.
	 */
	public function create(
						$teacher,
						$course,
						$coveringTeacher,
						$startingDate,
						$startingHour,
						$endingDate,
						$endingHour,
						$type,
						$text,
						$reason,
						$privateText
					) {
		// Escape strings
		$teacher = $this->database->escape_string($teacher);
		$course = $this->database->escape_string($course);
		$coveringTeacher = $this->database->escape_string($coveringTeacher);
		$startingDate = $this->database->escape_string($startingDate);
		$startingHour = $this->database->escape_string($startingHour);
		$endingDate = $this->database->escape_string($endingDate);
		$endingHour = $this->database->escape_string($endingHour);
		$type = $this->database->escape_string($type);
		$text = $this->database->escape_string($text);
		$reason = $this->database->escape_string($reason);
		$privateText = $this->database->escape_string($privateText);

		$sql = "INSERT INTO " . Database::$tableChanges .
				" (" .
					"teacher," .
					"course," .
					"coveringTeacher," .
					"startingDate," .
					"startingHour," .
					"endingDate," .
					"endingHour," .
					"type," .
					"text," .
					"reason," .
					"privateText" .
				")" .
				"VALUES (" .
					"'$teacher'," .
					"'$course'," .
					"'$coveringTeacher'," .
					"'$startingDate'," .
					"'$startingHour'," .
					"'$endingDate'," .
					"'$endingHour'," .
					"'$type'," .
					"'$text'," .
					"'$reason'," .
					"'$privateText'" .
				")";
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
	 * Update a change.
	 */
	public function update(
						$id,
						$teacher,
						$course,
						$coveringTeacher,
						$startingDate,
						$startingHour,
						$endingDate,
						$endingHour,
						$type,
						$text,
						$reason,
						$privateText
					) {
		// Escape strings
		$id = $this->database->escape_string($id);
		$teacher = $this->database->escape_string($teacher);
		$course = $this->database->escape_string($course);
		$coveringTeacher = $this->database->escape_string($coveringTeacher);
		$startingDate = $this->database->escape_string($startingDate);
		$startingHour = $this->database->escape_string($startingHour);
		$endingDate = $this->database->escape_string($endingDate);
		$endingHour = $this->database->escape_string($endingHour);
		$type = $this->database->escape_string($type);
		$text = $this->database->escape_string($text);
		$reason = $this->database->escape_string($reason);
		$privateText = $this->database->escape_string($privateText);

		$sql = "UPDATE " . Database::$tableChanges .
				" SET " .
					"teacher = '$teacher'," .
					"course = '$course'," .
					"coveringTeacher = '$coveringTeacher'," .
					"startingDate = '$startingDate'," .
					"startingHour = '$startingHour'," .
					"endingDate = '$endingDate'," .
					"endingHour = '$endingHour'," .
					"type = '$type'," .
					"text = '$text'," .
					"reason = '$reason'," .
					"privateText = '$privateText'" .
				"WHERE " .
					"id = '$id'";
		if ($this->database->query($sql)) {
			$this->activities->log($this->user, Activities::ACTION_UPDATE, $this->resource, $id);
			return true;
		}
		return false;
	}

	/**
	 * Delete a change.
	 */
	public function delete($id) {
		$id = $this->database->escape_string($id);
		$sql = "DELETE FROM " . Database::$tableChanges . " WHERE id = '$id'";
		if ($this->database->query($sql)) {
			$this->activities->log($this->user, Activities::ACTION_DELETE, $this->resource, $id);
			return true;
		}
		return false;
	}
}
?>
