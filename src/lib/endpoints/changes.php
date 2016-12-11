<?php
/*
 * @author	Nico Alt
 * @date	27.06.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 *
 * Endpoint: changes
 * Accepts: GET, PUT, POST, DELETE, OPTIONS
 */
require_once __DIR__ . '/abstractEndpoint.php';
class ChangesEndpoint extends AbstractEndpoint {

	/**
	 * Handles GET requests.
	 */
	public function handleGET($seeReasons = false, $seePrivateTexts = false, $seeTimes = false) {
		$givenTeachers = explode(",", self::getFromGET('teachers'));
		foreach ($givenTeachers as $teacher) {
			if ($teacher != '' && $teacher != 0) {
				if (!ctype_digit($teacher)) {
					$error[] = Array('code' => '1100', 'message' => 'The teacher may only contain an integer.');
					break;
				}
				else if (!$this->teachers->checkById($teacher)) {
					$error[] = Array('code' => '1101', 'message' => 'The teacher does not exist.');
					break;
				}
			}
		}
		$givenCourses = explode(",", self::getFromGET('courses'));
		foreach ($givenCourses as $course) {
			if ($course != '' && $course != 0) {
				if (!ctype_digit($course)) {
					$error[] = Array('code' => '1100', 'message' => 'The course may only contain an integer.');
					break;
				}
				else if (!$this->courses->checkById($course)) {
					$error[] = Array('code' => '1101', 'message' => 'The course does not exist.');
					break;
				}
			}
		}
		$coveringTeacher = self::getFromGET('coveringTeacher');
		if ($coveringTeacher != '') {
			if (!ctype_digit($coveringTeacher)) {
				$error[] = Array('code' => '1102', 'message' => 'The covering teacher may only contain an integer.');
			}
			else if (!$this->teachers->checkById($coveringTeacher)) {
				$error[] = Array('code' => '1103', 'message' => 'The covering teacher does not exist.');
			}
		}
		$startBy = self::getFromGET('startBy');
		// Replace alias with time
		if ($startBy != '') {
			if (self::replaceAlias($startBy) != null) {
				$startBy = self::replaceAlias($startBy);
			}
			if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $startBy)) {
				$error[] = Array('code' => '1104', 'message' => 'The starting date is formatted badly.');
			}
			else if (!checkdate(substr($startBy, 5, 2), substr($startBy, 8, 2), substr($startBy, 0, 4))) {
				$error[] = Array('code' => '1105', 'message' => 'The starting date does not exist.');
			}
		}
		$endBy = self::getFromGET('endBy');
		// Replace alias with time
		if ($endBy != '') {
			if (self::replaceAlias($endBy) != null) {
				$endBy = self::replaceAlias($endBy);
			}
			if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $endBy)) {
				$error[] = Array('code' => '1106', 'message' => 'The ending date is formatted badly.');
			}
			else if (!checkdate(substr($endBy, 5, 2), substr($endBy, 8, 2), substr($endBy, 0, 4))) {
				$error[] = Array('code' => '1107', 'message' => 'The ending date does not exist.');
			}
		}
		if (!empty($error)) {
			$this->api->setStatus(400);
			return Array('error' => $error);
		}
		if ($startBy != '' && $endBy != '') {
			$datetime1 = new DateTime(substr($startBy, 0, 10));
			$datetime2 = new DateTime(substr($endBy, 0, 10));
			if ($datetime1 > $datetime2) {
				$error[] = Array('code' => '1108', 'message' => 'The ending date has to be after the starting date.');
			}
		}
		if (!empty($error)) {
			$this->api->setStatus(400);
			return Array('error' => $error);
		}
		$changes = $this->changes->get($this->api->getID(), $givenTeachers, $givenCourses, $coveringTeacher, $startBy, $endBy, $seeReasons, $seePrivateTexts, $seeTimes);
		$this->api->setStatus(($changes == null) ? 404 : 200);
		return $changes;
	}

	/**
	 * Handles POST requests.
	 */	
	public function handlePOST() {
		$missing = Array();
		$teacher = self::getFromPOST('teacher');
		$startingDate = self::getFromPOST('startingDate');
		if ($startingDate == '') {
			$missing[] = 'startingDate';
		}
		$endingDate = self::getFromPOST('endingDate');
		if ($endingDate == '') {
			$missing[] = 'endingDate';
		}
		$type = self::getFromPOST('type');
		if ($type == '') {
			$missing[] = 'type';
		}
		$coveringTeacher = self::getFromPOST('coveringTeacher');
		if ($type == 1 && $coveringTeacher == '') {
			$missing[] = 'coveringTeacher';
		}
		$text = self::getFromPOST('text');
		if ($type == 2 && $text == '') {
			$missing[] = 'text';
		}
		$privateText = self::getFromPOST('privateText');
		$course = self::getFromPOST('course');
		$startingHour = self::getFromPOST('startingHour');
		$endingHour = self::getFromPOST('endingHour');
		if (!empty($missing)) {
			$this->api->setStatus(400);
			return Array('missing' => $missing);
		}

		$error = Array();
		if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $startingDate)) {
			$error[] = Array('code' => '1301', 'message' => 'The starting date is formatted badly.');
		}
		else if (!checkdate(substr($startingDate, 5, 2), substr($startingDate, 8, 2), substr($startingDate, 0, 4))) {
			$error[] = Array('code' => '1303', 'message' => 'The starting date does not exist.');
		}
		if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $endingDate)) {
			$error[] = Array('code' => '1302', 'message' => 'The ending date is formatted badly.');
		}
		else if (!checkdate(substr($endingDate, 5, 2), substr($endingDate, 8, 2), substr($endingDate, 0, 4))) {
			$error[] = Array('code' => '1304', 'message' => 'The ending date does not exist.');
		}
		if ($teacher != '' && !ctype_digit($teacher)) {
			$error[] = Array('code' => '1305', 'message' => 'The teacher may only contain an integer.');
		}
		else if ($teacher != '' && !$this->teachers->checkById($teacher)) {
			$error[] = Array('code' => '1308', 'message' => 'The teacher does not exist.');
		}
		if ($course != '' && !ctype_digit($course)) {
			$error[] = Array('code' => '1305', 'message' => 'The course may only contain an integer.');
		}
		else if ($course != '' && !$this->courses->checkById($course)) {
			$error[] = Array('code' => '1308', 'message' => 'The course does not exist.');
		}
		if ($coveringTeacher != '' && !ctype_digit($coveringTeacher)) {
			$error[] = Array('code' => '1306', 'message' => 'The covering teacher may only contain an integer.');
		}
		else if ($coveringTeacher != '' && !$this->teachers->checkById($coveringTeacher)) {
			$error[] = Array('code' => '1309', 'message' => 'The covering teacher does not exist.');
		}
		if ($type != '0' && $type != '1' && $type != '2' && $type != '3') {
			$error[] = Array('code' => '1307', 'message' => 'The type is not allowed.');
		}
		$reason = self::getFromPOST('reason');
		if ($reason != '' && $reason != '0' && $reason != '1' && $reason != '2') {
			$error[] = Array('code' => '1311', 'message' => 'The reason is not allowed.');
		}
		if (!empty($error)) {
			$this->api->setStatus(400);
			return Array('error' => $error);
		}
		$datetime1 = new DateTime($startingDate);
		$datetime2 = new DateTime($endingDate);
		if ($datetime1 > $datetime2) {
			$error[] = Array('code' => '1310', 'message' => 'The ending date has to be after the starting date.');
		}
		if (!empty($error)) {
			$this->api->setStatus(400);
			return Array('error' => $error);
		}
		$identification = $this->changes->create($teacher, $course, $coveringTeacher, $startingDate, $startingHour, $endingDate, $endingHour, $type, $text, $reason, $privateText);
		if (isset($identification)) {
			$this->api->setStatus(201);
			return Array('id' => $identification);
		}
		$this->api->setStatus(409);
		return Array('error' => Array(Array('code' => '1300', 'message' => 'The change could not get created.')));
	}
	
	/**
	 * Handles PUT requests.
	 */	
	public function handlePUT($params) {
		$missing = Array();
		$identification = $this->api->getID();
		if ($identification == '') {
			$missing[] = 'id';
		}
		$teacher = $params['teacher'];
		$startingDate = $params['startingDate'];
		if ($startingDate == '') {
			$missing[] = 'startingDate';
		}
		$endingDate = $params['endingDate'];
		if ($endingDate == '') {
			$missing[] = 'endingDate';
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
		$course = $params['course'];
		$startingHour = $params['startingHour'];
		$endingHour = $params['endingHour'];
		if (!empty($missing)) {
			$this->api->setStatus(400);
			return Array('missing' => $missing);
		}

		$error = Array();
		if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $startingDate)) {
			$error[] = Array('code' => '1201', 'message' => 'The starting date is formatted badly.');
		}
		else if (!checkdate(substr($startingDate, 5, 2), substr($startingDate, 8, 2), substr($startingDate, 0, 4))) {
			$error[] = Array('code' => '1203', 'message' => 'The starting date does not exist.');
		}
		if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $endingDate)) {
			$error[] = Array('code' => '1202', 'message' => 'The ending date is formatted badly.');
		}
		else if (!checkdate(substr($endingDate, 5, 2), substr($endingDate, 8, 2), substr($endingDate, 0, 4))) {
			$error[] = Array('code' => '1204', 'message' => 'The ending date does not exist.');
		}
		if ($teacher != '' && !ctype_digit($teacher)) {
			$error[] = Array('code' => '1205', 'message' => 'The teacher may only contain an integer.');
		}
		else if ($teacher != '' && !$this->teachers->checkById($teacher)) {
			$error[] = Array('code' => '1208', 'message' => 'The teacher does not exist.');
		}
		if ($course != '' && !ctype_digit($course)) {
			$error[] = Array('code' => '1205', 'message' => 'The course may only contain an integer.');
		}
		else if ($course != '' && !$this->courses->checkById($course)) {
			$error[] = Array('code' => '1208', 'message' => 'The course does not exist.');
		}
		if ($coveringTeacher != '' && !ctype_digit($coveringTeacher)) {
			$error[] = Array('code' => '1206', 'message' => 'The covering teacher may only contain an integer.');
		}
		else if ($coveringTeacher != '' && !$this->teachers->checkById($coveringTeacher)) {
			$error[] = Array('code' => '1209', 'message' => 'The covering teacher does not exist.');
		}
		if ($type != '0' && $type != '1' && $type != '2' && $type != '3') {
			$error[] = Array('code' => '1207', 'message' => 'The type is not allowed.');
		}
		$reason = $params['reason'];
		if ($reason != '' && $reason != '0' && $reason != '1' && $reason != '2') {
			$error[] = Array('code' => '1211', 'message' => 'The reason is not allowed.');
		}
		if (!empty($error)) {
			$this->api->setStatus(400);
			return Array('error' => $error);
		}
		$datetime1 = new DateTime($startingDate);
		$datetime2 = new DateTime($endingDate);
		if ($datetime1 > $datetime2) {
			$error[] = Array('code' => '1210', 'message' => 'The ending date has to be after the starting date.');
		}
		if (!empty($error)) {
			$this->api->setStatus(400);
			return Array('error' => $error);
		}
		if ($this->changes->update($identification, $teacher, $course, $coveringTeacher, $startingDate, $startingHour, $endingDate, $endingHour, $type, $text, $reason, $privateText)) {
			$this->api->setStatus(204);
			return null;
		}
		$this->api->setStatus(409);
		return Array('error' => Array(Array('code' => '1200', 'message' => 'The change could not get updated.')));
	}
	
	/**
	 * Handles DELETE requests.
	 */	
	public function handleDELETE() {
		if ($this->api->getID() == '') {
			$this->api->setStatus(400);
			return Array('missing' => Array('id'));
		}
		if ($this->changes->delete($this->api->getID())) {
			$this->api->setStatus(204);
			return null;
		}
		$this->api->setStatus(409);
		return Array('error' => Array(Array('code' => '1400', 'message' => 'The change could not get deleted.')));
	}
	
	/**
	 * Replaces aliases.
	 */
	private function replaceAlias($alias) {
		switch ($alias) {
			case 'now':
				return substr(date('c'), 0, 10);
			case 'tom':
				return substr(date('c', time() + 86400), 0, 10);
			case 'i3d':
				return substr(date('c', time() + 259200), 0, 10);
			case 'i1w':
				return substr(date('c', time() + 604800), 0, 10);
			case 'i1m':
				return substr(date('c', time() + 2419200), 0, 10);
			case 'i1y':
				return substr(date('c', time() + 31536000), 0, 10);
			default:
				return null;
		}
	}
}
