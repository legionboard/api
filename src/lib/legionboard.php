<?php
/*
 * @author	Nico Alt
 * @date	16.03.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
require_once __DIR__ . '/abstractAPI.php';
class LegionBoard extends API {
	/*
	 * Index authentication keys
	 */
	const GROUP_SEE_CHANGES = 0;
	const GROUP_ADD_CHANGE = 1;
	const GROUP_UPDATE_CHANGE = 2;
	const GROUP_DELETE_CHANGE = 3;
	const GROUP_SEE_TEACHERS = 4;
	const GROUP_ADD_TEACHER = 5;
	const GROUP_UPDATE_TEACHER = 6;
	const GROUP_DELETE_TEACHER = 7;
	const GROUP_SEE_REASONS = 8;
	const GROUP_SEE_PRIVATE_TEXTS = 9;
	// Default teacher 'All'
	const DEFAULT_TEACHER_ALL = '1';
	/**
	 * Endpoint: teachers
	 * Accepts: GET, PUT, POST, DELETE
	 */
	protected function teachers() {
		self::checkTables();
		require_once __DIR__ . '/changes.php';
		$changes = new Changes();
		require_once __DIR__ . '/teachers.php';
		$teachers = new Teachers();
		require_once __DIR__ . '/authentication.php';
		$authentication = new Authentication();
		if ($this->method == 'GET') {
			$key = $_GET['k'];
			// Verify authentication key
			if (!$authentication->verifiy($key, self::GROUP_SEE_TEACHERS)) {
				$this->status = 401;
				return null;
			}
			$id = $this->args[0];
			$teachers = $teachers->get($id);
			if ($teachers != null) {
				return $teachers;
			}
			$this->status = 404;
			return null;			
		}
		if ($this->method == 'PUT') {
			parse_str($this->file, $params);
			$key = $params['k'];
			// Verify authentication key
			if (!$authentication->verifiy($key, self::GROUP_UPDATE_TEACHER)) {
				$this->status = 401;
				return null;
			}
			$id = $this->args[0];
			$missing = Array();
			if ($id == '') {
				$missing[] = 'id';
			}
			$name = $params['name'];
			if ($name == '') {
				$missing[] = 'name';
			}
			if (!empty($missing)) {
				$this->status = 400;
				return Array('missing' => $missing);
			}
			if ($teachers->update($id, $name)) {
				$this->status = 204;
				return null;
			}
			$this->status = 409;
			return Array('error' => Array(Array('code' => '200', 'message' => 'The teacher could not get updated.')));
		}
		if ($this->method == 'POST') {
			$key = $_POST['k'];
			// Verify authentication key
			if (!$authentication->verifiy($key, self::GROUP_ADD_TEACHER)) {
				$this->status = 401;
				return null;
			}
			$name = $_POST['name'];
			if ($name == '') {
				$this->status = 400;
				return Array('missing' => Array('name'));
			}
			if ($teachers->checkByName($name)) {
				$this->status = 400;
				return Array('error' => Array(Array('code' => '301', 'message' => 'A teacher with the given name already exists.')));
			}
			$id = $teachers->create($name);
			if (isset($id)) {
				$this->status = 201;
				return Array('id' => $id);
			}
			$this->status = 409;
			return Array('error' => Array(Array('code' => '300', 'message' => 'The teacher could not get created.')));
		}
		if ($this->method == 'DELETE') {
			$key = $_GET['k'];
			// Verify authentication key
			if (!$authentication->verifiy($key, self::GROUP_DELETE_TEACHER)) {
				$this->status = 401;
				return null;
			}
			$id = $this->args[0];
			if ($id == '') {
				$this->status = 400;
				return Array('missing' => Array('id'));
			}
			if ($id == self::DEFAULT_TEACHER_ALL) {
				$this->status = 400;
				return Array('error' => Array(Array('code' => '401', 'message' => 'Deleting the teacher with ID 1 is not allowed.')));				
			}
			if ($changes->get(null, Array($id)) != null || $changes->get(null, null, $id) != null) {
				$this->status = 400;
				return Array('error' => Array(Array('code' => '402', 'message' => 'The teacher is still linked to a change.')));
			}
			if ($teachers->delete($id)) {
				$this->status = 204;
				return null;
			}
			$this->status = 409;
			return Array('error' => Array(Array('code' => '400', 'message' => 'The teacher could not get deleted.')));
		}
		if ($this->method == 'OPTIONS') {
			$this->status = 200;
			return null;
		}
		$this->status = 405;
		return Array('error' => Array(Array('code' => '0', 'message' => "Only accepts GET, PUT, POST and DELETE requests.")));
	}
	/**
	 * Endpoint: changes
	 * Accepts: GET, PUT, POST, DELETE
	 */
	protected function changes() {
		self::checkTables();
		require_once __DIR__ . '/changes.php';
		$changes = new Changes();
		require_once __DIR__ . '/teachers.php';
		$teachers = new Teachers();
		require_once __DIR__ . '/authentication.php';
		$authentication = new Authentication();
		if ($this->method == 'GET') {
			$key = $_GET['k'];
			// Verify authentication key
			if (!$authentication->verifiy($key, self::GROUP_SEE_CHANGES)) {
				$this->status = 401;
				return null;
			}
			$seeReasons = $authentication->verifiy($key, self::GROUP_SEE_REASONS);
			$seePrivateTexts = $authentication->verifiy($key, self::GROUP_SEE_PRIVATE_TEXTS);
			$id = $this->args[0];
			$givenTeachers = explode(",", $_GET['teachers']);
			foreach ($givenTeachers as $teacher) {
				if ($teacher != '') {
					if (!ctype_digit($teacher)) {
						$error[] = Array('code' => '1100', 'message' => 'The teacher may only contain an integer.');
					}
					else if (!$teachers->checkById($teacher)) {
						$error[] = Array('code' => '1101', 'message' => 'The teacher does not exist.');
					}
				}
			}
			$coveringTeacher = $_GET['coveringTeacher'];
			if ($coveringTeacher != '') {
				if (!ctype_digit($coveringTeacher)) {
					$error[] = Array('code' => '1102', 'message' => 'The covering teacher may only contain an integer.');
				}
				else if (!$teachers->checkById($coveringTeacher)) {
					$error[] = Array('code' => '1103', 'message' => 'The covering teacher does not exist.');
				}
			}
			$startBy = $_GET['startBy'];
			// Replace alias with time
			if ($startBy != '') {
				switch ($startBy) {
					case 'now':
						$startBy = substr(date('c'), 0, 10);
						continue;
					case 'tom':
						$startBy = substr(date('c', time() + 86400), 0, 10);
						continue;
					case 'i3d':
						$startBy = substr(date('c', time() + 259200), 0, 10);
						continue;
					case 'i1w':
						$startBy = substr(date('c', time() + 604800), 0, 10);
						continue;
					case 'i1m':
						$startBy = substr(date('c', time() + 2419200), 0, 10);
						continue;
					case 'i1y':
						$startBy = substr(date('c', time() + 31536000), 0, 10);
						continue;
				}
				if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $startBy)) {
					$error[] = Array('code' => '1104', 'message' => 'The starting time is formatted badly.');
				}
				else if (!checkdate(substr($startBy, 5, 2), substr($startBy, 8, 2), substr($startBy, 0, 4))) {
					$error[] = Array('code' => '1105', 'message' => 'The starting time does not exist.');
				}
			}
			$endBy = $_GET['endBy'];
			// Replace alias with time
			if ($endBy != '') {
				switch ($endBy) {
					case 'now':
						$endBy = substr(date('c'), 0, 10);
						continue;
					case 'tom':
						$endBy = substr(date('c', time() + 86400), 0, 10);
						continue;
					case 'i3d':
						$endBy = substr(date('c', time() + 259200), 0, 10);
						continue;
					case 'i1w':
						$endBy = substr(date('c', time() + 604800), 0, 10);
						continue;
					case 'i1m':
						$endBy = substr(date('c', time() + 2419200), 0, 10);
						continue;
					case 'i1y':
						$endBy = substr(date('c', time() + 31536000), 0, 10);
						continue;
				}
				if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $endBy)) {
					$error[] = Array('code' => '1106', 'message' => 'The ending time is formatted badly.');
				}
				else if (!checkdate(substr($endBy, 5, 2), substr($endBy, 8, 2), substr($endBy, 0, 4))) {
					$error[] = Array('code' => '1107', 'message' => 'The ending time does not exist.');
				}
			}
			if (!empty($error)) {
				$this->status = 400;
				return Array('error' => $error);
			}
			if ($startBy != '' && $endBy != '') {
				$datetime1 = new DateTime(substr($startBy, 0, 10));
				$datetime2 = new DateTime(substr($endBy, 0, 10));
				if ($datetime1 > $datetime2) {
					$error[] = Array('code' => '1108', 'message' => 'The ending time has to be after the start time.');
				}
			}
			if (!empty($error)) {
				$this->status = 400;
				return Array('error' => $error);
			}
			$changes = $changes->get($id, $givenTeachers, $coveringTeacher, $startBy, $endBy, $seeReasons, $seePrivateTexts);
			$this->status = ($changes == null) ? 404 : 200;
			return $changes;
		}
		if ($this->method == 'PUT') {
			parse_str($this->file, $params);
			$key = $params['k'];
			// Verify authentication key
			if (!$authentication->verifiy($key, self::GROUP_UPDATE_CHANGE)) {
				$this->status = 401;
				return null;
			}
			$missing = Array();
			$id = $this->args[0];
			if ($id == '') {
				$missing[] = 'id';
			}
			$teacher = $params['teacher'];
			if ($teacher == '') {
				$missing[] = 'teacher';
			}
			$startBy = $params['startBy'];
			if ($startBy == '') {
				$missing[] = 'startBy';
			}
			$endBy = $params['endBy'];
			if ($endBy == '') {
				$missing[] = 'endBy';
			}
			$type = $params['type'];
			if ($type == '') {
				$missing[] = 'type';
			}
			$coveringTeacher = $params['coveringTeacher'];
			if ($type == 1 && $coveringTeacher == '') {
				$missing[] = 'coveringTeacher';
			}
			$text = $params['text'];
			if ($type == 2 && $text == '') {
				$missing[] = 'text';
			}
			$privateText = $params['privateText'];
			if (!empty($missing)) {
				$this->status = 400;
				return Array('missing' => $missing);
			}

			$error = Array();
			if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2})$/', $startBy)) {
				$error[] = Array('code' => '1201', 'message' => 'The starting time is formatted badly.');
			}
			else if (!checkdate(substr($startBy, 5, 2), substr($startBy, 8, 2), substr($startBy, 0, 4))) {
				$error[] = Array('code' => '1203', 'message' => 'The starting time does not exist.');
			}
			if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2})$/', $endBy)) {
				$error[] = Array('code' => '1202', 'message' => 'The ending time is formatted badly.');
			}
			else if (!checkdate(substr($endBy, 5, 2), substr($endBy, 8, 2), substr($endBy, 0, 4))) {
				$error[] = Array('code' => '1204', 'message' => 'The ending time does not exist.');
			}
			if (!ctype_digit($teacher)) {
				$error[] = Array('code' => '1205', 'message' => 'The teacher may only contain an integer.');
			}
			else if (!$teachers->checkById($teacher)) {
				$error[] = Array('code' => '1208', 'message' => 'The teacher does not exist.');
			}
			if ($coveringTeacher != '' && !ctype_digit($coveringTeacher)) {
				$error[] = Array('code' => '1206', 'message' => 'The covering teacher may only contain an integer.');
			}
			else if ($coveringTeacher != '' && !$teachers->checkById($coveringTeacher)) {
				$error[] = Array('code' => '1209', 'message' => 'The covering teacher does not exist.');
			}
			if ($type != '0' && $type != '1' && $type != '2') {
				$error[] = Array('code' => '1207', 'message' => 'The type is not allowed.');
			}
			$reason = $params['reason'];
			if ($reason != '' && $reason != '0' && $reason != '1' && $reason != '2') {
				$error[] = Array('code' => '1211', 'message' => 'The reason is not allowed.');
			}
			if (!empty($error)) {
				$this->status = 400;
				return Array('error' => $error);
			}
			$datetime1 = new DateTime(substr($startBy, 0, 10));
			$datetime2 = new DateTime(substr($endBy, 0, 10));
			if ($datetime1 > $datetime2) {
				$error[] = Array('code' => '1210', 'message' => 'The ending time has to be after the starting time.');
			}
			if (!empty($error)) {
				$this->status = 400;
				return Array('error' => $error);
			}
			if ($changes->update($id, $teacher, $coveringTeacher, $startBy, $endBy, $type, $text, $reason, $privateText)) {
				$this->status = 204;
				return null;
			}
			$this->status = 409;
			return Array('error' => Array(Array('code' => '1200', 'message' => 'The change could not get updated.')));
		}
		if ($this->method == 'POST') {
			$key = $_POST['k'];
			// Verify authentication key
			if (!$authentication->verifiy($key, self::GROUP_ADD_CHANGE)) {
				$this->status = 401;
				return null;
			}
			$missing = Array();
			$teacher = $_POST['teacher'];
			if ($teacher == '') {
				$missing[] = 'teacher';
			}
			$startBy = $_POST['startBy'];
			if ($startBy == '') {
				$missing[] = 'startBy';
			}
			$endBy = $_POST['endBy'];
			if ($endBy == '') {
				$missing[] = 'endBy';
			}
			$type = $_POST['type'];
			if ($type == '') {
				$missing[] = 'type';
			}
			$coveringTeacher = $_POST['coveringTeacher'];
			if ($type == 1 && $coveringTeacher == '') {
				$missing[] = 'coveringTeacher';
			}
			$text = $_POST['text'];
			if ($type == 2 && $text == '') {
				$missing[] = 'text';
			}
			$privateText = $_POST['privateText'];
			if (!empty($missing)) {
				$this->status = 400;
				return Array('missing' => $missing);
			}

			$error = Array();
			if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2})$/', $startBy)) {
				$error[] = Array('code' => '1301', 'message' => 'The starting time is formatted badly.');
			}
			else if (!checkdate(substr($startBy, 5, 2), substr($startBy, 8, 2), substr($startBy, 0, 4))) {
				$error[] = Array('code' => '1303', 'message' => 'The starting time does not exist.');
			}
			if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2})$/', $endBy)) {
				$error[] = Array('code' => '1302', 'message' => 'The ending time is formatted badly.');
			}
			else if (!checkdate(substr($endBy, 5, 2), substr($endBy, 8, 2), substr($endBy, 0, 4))) {
				$error[] = Array('code' => '1304', 'message' => 'The ending time does not exist.');
			}
			if (!ctype_digit($teacher)) {
				$error[] = Array('code' => '1305', 'message' => 'The teacher may only contain an integer.');
			}
			else if (!$teachers->checkById($teacher)) {
				$error[] = Array('code' => '1308', 'message' => 'The teacher does not exist.');
			}
			if ($coveringTeacher != '' && !ctype_digit($coveringTeacher)) {
				$error[] = Array('code' => '1306', 'message' => 'The covering teacher may only contain an integer.');
			}
			else if ($coveringTeacher != '' && !$teachers->checkById($coveringTeacher)) {
				$error[] = Array('code' => '1309', 'message' => 'The covering teacher does not exist.');
			}
			if ($type != '0' && $type != '1' && $type != '2') {
				$error[] = Array('code' => '1307', 'message' => 'The type is not allowed.');
			}
			$reason = $_POST['reason'];
			if ($reason != '' && $reason != '0' && $reason != '1' && $reason != '2') {
				$error[] = Array('code' => '1311', 'message' => 'The reason is not allowed.');
			}
			if (!empty($error)) {
				$this->status = 400;
				return Array('error' => $error);
			}
			$datetime1 = new DateTime(substr($startBy, 0, 10));
			$datetime2 = new DateTime(substr($endBy, 0, 10));
			if ($datetime1 > $datetime2) {
				$error[] = Array('code' => '1310', 'message' => 'The ending time has to be after the starting time.');
			}
			if (!empty($error)) {
				$this->status = 400;
				return Array('error' => $error);
			}
			$id = $changes->create($teacher, $coveringTeacher, $startBy, $endBy, $type, $text, $reason, $privateText);
			if (isset($id)) {
				$this->status = 201;
				return $id;
			}
			$this->status = 409;
			return Array('error' => Array(Array('code' => '1300', 'message' => 'The teacher could not get created.')));
		}
		if ($this->method == 'DELETE') {
			$key = $_GET['k'];
			// Verify authentication key
			if (!$authentication->verifiy($key, self::GROUP_DELETE_CHANGE)) {
				$this->status = 401;
				return null;
			}
			$id = $this->args[0];
			if ($id == '') {
				$this->status = 400;
				return Array('missing' => Array('id'));
			}
			if ($changes->delete($id)) {
				$this->status = 204;
				return null;
			}
			$this->status = 409;
			return Array('error' => Array(Array('code' => '1400', 'message' => 'The teacher could not get deleted.')));
		}
		if ($this->method == 'OPTIONS') {
			$this->status = 200;
			return null;
		}
		$this->status = 405;
		return Array('error' => Array(Array('code' => '1000', 'message' => "Only accepts GET, PUT, POST and DELETE requests.")));
	}

	/**
	 * This opens a MySQL connection to force a check of the tables.
	 */
	private function checkTables() {
		require_once __DIR__ . '/database.php';
		$database = new Database();
	}
}
?>
