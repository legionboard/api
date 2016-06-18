<?php
/*
 * @author	Nico Alt
 * @date	16.03.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
class Changes {	

	/**
	 * Connect with the database.
	 */
	public function __construct() {
		require_once __DIR__ . '/database.php';
		$database = new Database();
		$this->db = $database->get();
	}

	/**
	 * Get one or more changes.
	 */
	public function get($id = null, $teachers = null, $courses = null, $coveringTeacher = null, $startBy = null, $endBy = null, $seeReasons = false, $seePrivateTexts = false) {
		$changes = Array();
		// Filter by teachers
		if (!empty(array_filter($teachers))) {
			sort($teachers);
			foreach ($teachers as $teacher) {
				// Add where clause for teacher
				$teacher = $this->db->escape_string($teacher);
				$sql_teachers .= (empty($sql_teachers) ? "" : " OR ") . "teacher LIKE '$teacher'";
			}
		}
		// Filter by courses
		if (!empty(array_filter($courses))) {
			sort($courses);
			foreach ($courses as $course) {
				// Add where clause for course
				$course = $this->db->escape_string($course);
				$sql_courses .= (empty($sql_courses) ? "" : " OR ") . "course LIKE '$course'";
			}
		}
		// Filter by ID
		if (isset($id)) {
			$id = $this->db->escape_string($id);
			$sql_id = " id LIKE '$id'";
		}
		// Filter by covering teacher
		if (isset($coveringTeacher)) {
			$coveringTeacher = $this->db->escape_string($coveringTeacher);
			$sql_coveringTeacher = " coveringTeacher LIKE '$coveringTeacher'";
		}
		// Build SELECT
		$sql = "SELECT * FROM " . Database::$tableChanges;
		if (!empty($sql_teachers) || !empty($sql_courses) || !empty($sql_id) || !empty($sql_coveringTeacher)) {
			$sql .= " WHERE";
			if (!empty($sql_teachers)) {
				$sql .= " (" . $sql_teachers . ")";
			}
			if (!empty($sql_courses)) {
				if (!empty($sql_teachers)) {
					$sql .= " AND";
				}
				$sql .= " (" . $sql_courses . ")";
			}
			if ((!empty($sql_teachers) || !empty($sql_courses)) && !empty($sql_id)) {
				$sql .= " AND";
			}
			$sql .= $sql_id;
			if ((!empty($sql_teachers) || !empty($sql_courses)) && !empty($sql_coveringTeacher)) {
				$sql .= " AND";
			}
			$sql .= $sql_coveringTeacher;

		}
		$query = $this->db->query($sql);
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
		$changesCon = array_filter($changes);
		if (empty($changesCon)) {
			return null;
		}
		return $changes;
	}

	/**
	 * Create a change.
	 */
	public function create($teacher, $course, $coveringTeacher, $startBy, $endBy, $type, $text, $reason, $privateText) {
		$teacher = $this->db->escape_string($teacher);
		$course = $this->db->escape_string($course);
		$coveringTeacher = $this->db->escape_string($coveringTeacher);
		$startBy = $this->db->escape_string($startBy);
		$endBy = $this->db->escape_string($endBy);
		$type = $this->db->escape_string($type);
		$text = $this->db->escape_string($text);
		$reason = $this->db->escape_string($reason);
		$privateText = $this->db->escape_string($privateText);
		$sql = "INSERT INTO " . Database::$tableChanges . " (teacher, course, coveringTeacher, startBy, endBy, type, text, reason, privateText) VALUES ('$teacher', '$course', '$coveringTeacher', '$startBy', '$endBy', '$type', '$text', '$reason', '$privateText')";
		if ($this->db->query($sql)) {
			return $this->db->insert_id;
		}
		return null;
	}

	/**
	 * Update a change.
	 */
	public function update($id, $teacher, $course, $coveringTeacher, $startBy, $endBy, $type, $text, $reason, $privateText) {
		$id = $this->db->escape_string($id);
		$teacher = $this->db->escape_string($teacher);
		$course = $this->db->escape_string($course);
		$coveringTeacher = $this->db->escape_string($coveringTeacher);
		$startBy = $this->db->escape_string($startBy);
		$endBy = $this->db->escape_string($endBy);
		$type = $this->db->escape_string($type);
		$text = $this->db->escape_string($text);
		$reason = $this->db->escape_string($reason);
		$privateText = $this->db->escape_string($privateText);
		$sql = "UPDATE " . Database::$tableChanges . " SET teacher = '$teacher', course = '$course', coveringTeacher = '$coveringTeacher', startBy = '$startBy', endBy = '$endBy', type = '$type', text = '$text', reason = '$reason', privateText = '$privateText' WHERE id = '$id'";
		return $this->db->query($sql);
	}

	/**
	 * Delete a change.
	 */
	public function delete($id) {
		$id = $this->db->escape_string($id);
		$sql = "DELETE FROM " . Database::$tableChanges . " WHERE id = '$id'";
		return $this->db->query($sql);
	}
}
?>
