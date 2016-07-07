<?php
/*
 * @author	Nico Alt
 * @date	16.03.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
class Changes {	

	// Resource affected in this class
	const THIS_RESOURCE = 'changes';

	/**
	 * Connect with the database.
	 */
	public function __construct($user) {
		$this->user = $user;
		require_once __DIR__ . '/../database.php';
		$database = new Database();
		$this->database = $database->get();
		require_once __DIR__ . '/../activities.php';
		$this->activities = new Activities();
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
		if (!empty(array_filter($teachers))) {
			sort($teachers);
			foreach ($teachers as $teacher) {
				// Add where clause for teacher
				$teacher = $this->database->escape_string($teacher);
				$sqlTeachers .= (empty($sqlTeachers) ? "" : " OR ") . "teacher LIKE '$teacher'";
			}
		}
		// Filter by courses
		if (!empty(array_filter($courses))) {
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
					$dtColumnEnd = new DateTime(substr($column['endBy'], 0, 10));
					$dtStart = new DateTime($startBy);
					if ($dtColumnEnd < $dtStart) {
						continue;
					}
				}
				// Filter out if start of change is after given end
				if ($endBy != null) {
					$dtColumnStart = new DateTime(substr($column['startBy'], 0, 10));
					$dtEnd = new DateTime($endBy);
					if ($dtColumnStart > $dtEnd) {
						continue;
					}
				}
				$change = Array(
							'id' => $column['id'],
							'teacher' => $column['teacher'],
							'course' => $column['course'],
							'startBy' => $column['startBy'],
							'endBy' => $column['endBy'],
							'type' => $column['type'],
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
		if (empty(array_filter($changes))) {
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
						$startBy,
						$endBy,
						$type,
						$text,
						$reason,
						$privateText
					) {
		// Escape strings
		$teacher = $this->database->escape_string($teacher);
		$course = $this->database->escape_string($course);
		$coveringTeacher = $this->database->escape_string($coveringTeacher);
		$startBy = $this->database->escape_string($startBy);
		$endBy = $this->database->escape_string($endBy);
		$type = $this->database->escape_string($type);
		$text = $this->database->escape_string($text);
		$reason = $this->database->escape_string($reason);
		$privateText = $this->database->escape_string($privateText);

		$sql = "INSERT INTO " . Database::$tableChanges .
				" (" .
					"teacher," .
					"course," .
					"coveringTeacher," .
					"startBy," .
					"endBy," .
					"type," .
					"text," .
					"reason," .
					"privateText" .
				")" .
				"VALUES (" .
					"'$teacher'," .
					"'$course'," .
					"'$coveringTeacher'," .
					"'$startBy'," .
					"'$endBy'," .
					"'$type'," .
					"'$text'," .
					"'$reason'," .
					"'$privateText'" .
				")";
		if ($this->database->query($sql)) {
			$id = $this->database->insert_id;
			if ($this->activities->log($this->user, Activities::ACTION_CREATE, self::THIS_RESOURCE, $id)) {
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
						$startBy,
						$endBy,
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
		$startBy = $this->database->escape_string($startBy);
		$endBy = $this->database->escape_string($endBy);
		$type = $this->database->escape_string($type);
		$text = $this->database->escape_string($text);
		$reason = $this->database->escape_string($reason);
		$privateText = $this->database->escape_string($privateText);

		$sql = "UPDATE " . Database::$tableChanges .
				" SET " .
					"teacher = '$teacher'," .
					"course = '$course'," .
					"coveringTeacher = '$coveringTeacher'," .
					"startBy = '$startBy'," .
					"endBy = '$endBy'," .
					"type = '$type'," .
					"text = '$text'," .
					"reason = '$reason'," .
					"privateText = '$privateText'" .
				"WHERE " .
					"id = '$id'";
		if ($this->database->query($sql)) {
			$this->activities->log($this->user, Activities::ACTION_UPDATE, self::THIS_RESOURCE, $id);
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
			$this->activities->log($this->user, Activities::ACTION_DELETE, self::THIS_RESOURCE, $id);
			return true;
		}
		return false;
	}
}
?>
