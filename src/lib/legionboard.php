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
	const GROUP_SEE_COURSES = 10;
	const GROUP_ADD_COURSE = 11;
	const GROUP_UPDATE_COURSE = 12;
	const GROUP_DELETE_COURSE = 13;
	const GROUP_SEE_TIMES = 14;
	const GROUP_SEE_ACTIVITIES = 15;
	const GROUP_SEE_SUBJECTS = 16;
	const GROUP_ADD_SUBJECT = 17;
	const GROUP_UPDATE_SUBJECT = 18;
	const GROUP_DELETE_SUBJECT = 19;
	const GROUP_EXPORT_ALL_RESOURCES = 20;

	public function __construct($request) {
		$this->setVersionName('0.2.0');
		$this->setVersionCode('20099');
		parent::__construct($request);
		require_once __DIR__ . '/authentication.php';
		$this->authentication = new Authentication();
	}

	/**
	 * Endpoint: activities
	 * Accepts: GET
	 */
	protected function activities() {
		// Import endpoint "activities"
		require_once __DIR__ . '/endpoints/activities.php';
		if ($this->getMethod() == 'GET') {
			$key = self::getFromGET('k');
			// Verify user is allowed to see activities
			if (!$this->authentication->verifiy($key, self::GROUP_SEE_ACTIVITIES)) {
				$this->setStatus(401);
				return null;
			}
			$activitiesEndpoint = new ActivitiesEndpoint($this, $this->authentication->getUserID($key));
			return $activitiesEndpoint->handleGET();
		}
		if ($this->getMethod() == 'OPTIONS') {
			$this->setStatus(200);
			return null;
		}
		$this->setStatus(405);
		return Array('error' => Array(Array('code' => '0', 'message' => "Only accepts GET requests.")));
	}

	/**
	 * Endpoint: changes
	 * Accepts: GET, PUT, POST, DELETE, OPTIONS
	 */
	protected function changes() {
		// Import endpoint "changes"
		require_once __DIR__ . '/endpoints/changes.php';
		if ($this->getMethod() == 'GET') {
			$key = self::getFromGET('k');
			// Verifiy user is allowed to see changes
			if (!$this->authentication->verifiy($key, self::GROUP_SEE_CHANGES)) {
				$this->setStatus(401);
				return null;
			}
			$changesEndpoint = new ChangesEndpoint($this, $this->authentication->getUserID($key));
			// Verifiy user is allowed to see reasons
			$seeReasons = $this->authentication->verifiy($key, self::GROUP_SEE_REASONS);
			// Verifiy user is allowed to see private texts
			$seePrivateTexts = $this->authentication->verifiy($key, self::GROUP_SEE_PRIVATE_TEXTS);
			// Verifiy user is allowed to see times
			$seeTimes = $this->authentication->verifiy($key, self::GROUP_SEE_TIMES);
			return $changesEndpoint->handleGET($seeReasons, $seePrivateTexts, $seeTimes);
		}
		if ($this->getMethod() == 'POST') {
			$key = self::getFromPOST('k');
			// Verifiy user is allowed to add changes
			if (!$this->authentication->verifiy($key, self::GROUP_ADD_CHANGE)) {
				$this->setStatus(401);
				return null;
			}
			$changesEndpoint = new ChangesEndpoint($this, $this->authentication->getUserID($key));
			return $changesEndpoint->handlePOST();
		}
		if ($this->getMethod() == 'PUT') {
			parse_str($this->getFile(), $params);
			$key = $params['k'];
			// Verifiy user is allowed to update changes
			if (!$this->authentication->verifiy($key, self::GROUP_UPDATE_CHANGE)) {
				$this->setStatus(401);
				return null;
			}
			$changesEndpoint = new ChangesEndpoint($this, $this->authentication->getUserID($key));
			return $changesEndpoint->handlePUT($params);
		}
		if ($this->getMethod() == 'DELETE') {
			$key = self::getFromGET('k');
			// Verifiy user is allowed to delete changes
			if (!$this->authentication->verifiy($key, self::GROUP_DELETE_CHANGE)) {
				$this->setStatus(401);
				return null;
			}
			$changesEndpoint = new ChangesEndpoint($this, $this->authentication->getUserID($key));
			return $changesEndpoint->handleDELETE();
		}
		if ($this->getMethod() == 'OPTIONS') {
			$this->setStatus(200);
			return null;
		}
		$this->setStatus(405);
		return Array('error' => Array(Array('code' => '0', 'message' => "Only accepts GET, PUT, POST and DELETE requests.")));
	}

	/**
	 * Endpoint: courses
	 * Accepts: GET, PUT, POST, DELETE
	 */
	protected function courses() {
		// Import endpoint "courses"
		require_once __DIR__ . '/endpoints/courses.php';
		if ($this->getMethod() == 'GET') {
			$key = self::getFromGET('k');
			// Verify user is allowed to see courses
			if (!$this->authentication->verifiy($key, self::GROUP_SEE_COURSES)) {
				$this->setStatus(401);
				return null;
			}
			$coursesEndpoint = new CoursesEndpoint($this, $this->authentication->getUserID($key));
			// Verifiy user is allowed to see times
			$seeTimes = $this->authentication->verifiy($key, self::GROUP_SEE_TIMES);
			return $coursesEndpoint->handleGET($seeTimes);
		}
		if ($this->getMethod() == 'POST') {
			$key = self::getFromPOST('k');
			// Verify user is allowed to add courses
			if (!$this->authentication->verifiy($key, self::GROUP_ADD_COURSE)) {
				$this->setStatus(401);
				return null;
			}
			$coursesEndpoint = new CoursesEndpoint($this, $this->authentication->getUserID($key));
			return $coursesEndpoint->handlePOST();
		}
		if ($this->getMethod() == 'PUT') {
			parse_str($this->getFile(), $params);
			$key = $params['k'];
			// Verify user is allowed to update courses
			if (!$this->authentication->verifiy($key, self::GROUP_UPDATE_COURSE)) {
				$this->setStatus(401);
				return null;
			}
			$coursesEndpoint = new CoursesEndpoint($this, $this->authentication->getUserID($key));
			return $coursesEndpoint->handlePUT($params);
		}
		if ($this->getMethod() == 'DELETE') {
			$key = self::getFromGET('k');
			// Verify user is allowed to delete courses
			if (!$this->authentication->verifiy($key, self::GROUP_DELETE_COURSE)) {
				$this->setStatus(401);
				return null;
			}
			$coursesEndpoint = new CoursesEndpoint($this, $this->authentication->getUserID($key));
			return $coursesEndpoint->handleDELETE();
		}
		if ($this->getMethod() == 'OPTIONS') {
			$this->setStatus(200);
			return null;
		}
		$this->setStatus(405);
		return Array('error' => Array(Array('code' => '0', 'message' => "Only accepts GET, PUT, POST and DELETE requests.")));
	}

	/**
	 * Endpoint: export
	 * Accepts: GET
	 */
	protected function export() {
		// Import endpoint "export"
		require_once __DIR__ . '/endpoints/export.php';
		if ($this->getMethod() == 'GET') {
			$key = self::getFromGET('k');
			// Verifiy user is allowed to export all resources
			if (!$this->authentication->verifiy($key, self::GROUP_EXPORT_ALL_RESOURCES)) {
				$this->setStatus(401);
				return null;
			}
			$exportEndpoint = new ExportEndpoint($this, $this->authentication->getUserID($key));
			return $exportEndpoint->handleGET();
		}
		if ($this->getMethod() == 'OPTIONS') {
			$this->setStatus(200);
			return null;
		}
		$this->setStatus(405);
		return Array('error' => Array(Array('code' => '0', 'message' => "Only accepts GET requests.")));
	}

	/**
	 * Endpoint: subjects
	 * Accepts: GET, PUT, POST, DELETE
	 */
	protected function subjects () {
		// Import endpoint "courses"
		require_once __DIR__ . '/endpoints/subjects.php';
		if ($this->getMethod() == 'GET') {
			$key = self::getFromGET('k');
			// Verify user is allowed to see subjects
			if (!$this->authentication->verifiy($key, self::GROUP_SEE_SUBJECTS)) {
				$this->setStatus(401);
				return null;
			}
			$subjectsEndpoint = new SubjectsEndpoint($this, $this->authentication->getUserID($key));
			// Verifiy user is allowed to see times
			$seeTimes = $this->authentication->verifiy($key, self::GROUP_SEE_TIMES);
			return $subjectsEndpoint->handleGET($seeTimes);
		}
		if ($this->getMethod() == 'POST') {
			$key = self::getFromPOST('k');
			// Verify user is allowed to add subjects
			if (!$this->authentication->verifiy($key, self::GROUP_ADD_SUBJECT)) {
				$this->setStatus(401);
				return null;
			}
			$subjectsEndpoint = new SubjectsEndpoint($this, $this->authentication->getUserID($key));
			return $subjectsEndpoint->handlePOST();
		}
		if ($this->getMethod() == 'PUT') {
			parse_str($this->getFile(), $params);
			$key = $params['k'];
			// Verify user is allowed to update subjects
			if (!$this->authentication->verifiy($key, self::GROUP_UPDATE_SUBJECT)) {
				$this->setStatus(401);
				return null;
			}
			$subjectsEndpoint = new SubjectsEndpoint($this, $this->authentication->getUserID($key));
			return $subjectsEndpoint->handlePUT($params);
		}
		if ($this->getMethod() == 'DELETE') {
			$key = self::getFromGET('k');
			// Verify user is allowed to delete subjects
			if (!$this->authentication->verifiy($key, self::GROUP_DELETE_SUBJECT)) {
				$this->setStatus(401);
				return null;
			}
			$subjectsEndpoint = new SubjectsEndpoint($this, $this->authentication->getUserID($key));
			return $subjectsEndpoint->handleDELETE();
		}
		if ($this->getMethod() == 'OPTIONS') {
			$this->setStatus(200);
			return null;
		}
		$this->setStatus(405);
		return Array('error' => Array(Array('code' => '0', 'message' => "Only accepts GET, PUT, POST and DELETE requests.")));
	}

	/**
	 * Endpoint: teachers
	 * Accepts: GET, PUT, POST, DELETE
	 */
	protected function teachers() {
		// Import endpoint "teachers"
		require_once __DIR__ . '/endpoints/teachers.php';
		if ($this->getMethod() == 'GET') {
			$key = self::getFromGET('k');
			// Verify user is allowed to see teachers
			if (!$this->authentication->verifiy($key, self::GROUP_SEE_TEACHERS)) {
				$this->setStatus(401);
				return null;
			}
			$teachersEndpoint = new TeachersEndpoint($this, $this->authentication->getUserID($key));
			// Verifiy user is allowed to see times
			$seeTimes = $this->authentication->verifiy($key, self::GROUP_SEE_TIMES);
			return $teachersEndpoint->handleGET($seeTimes);
		}
		if ($this->getMethod() == 'POST') {
			$key = self::getFromPOST('k');
			// Verify user is allowed to add teachers
			if (!$this->authentication->verifiy($key, self::GROUP_ADD_TEACHER)) {
				$this->setStatus(401);
				return null;
			}
			$teachersEndpoint = new TeachersEndpoint($this, $this->authentication->getUserID($key));
			return $teachersEndpoint->handlePOST();
		}
		if ($this->getMethod() == 'PUT') {
			parse_str($this->getFile(), $params);
			$key = $params['k'];
			// Verify user is allowed to update teachers
			if (!$this->authentication->verifiy($key, self::GROUP_UPDATE_TEACHER)) {
				$this->setStatus(401);
				return null;
			}
			$teachersEndpoint = new TeachersEndpoint($this, $this->authentication->getUserID($key));
			return $teachersEndpoint->handlePUT($params);
		}
		if ($this->getMethod() == 'DELETE') {
			$key = self::getFromGET('k');
			// Verify user is allowed to delete teachers
			if (!$this->authentication->verifiy($key, self::GROUP_DELETE_TEACHER)) {
				$this->setStatus(401);
				return null;
			}
			$teachersEndpoint = new TeachersEndpoint($this, $this->authentication->getUserID($key));
			return $teachersEndpoint->handleDELETE();
		}
		if ($this->getMethod() == 'OPTIONS') {
			$this->setStatus(200);
			return null;
		}
		$this->setStatus(405);
		return Array('error' => Array(Array('code' => '0', 'message' => "Only accepts GET, PUT, POST and DELETE requests.")));
	}

	/**
	 * Returns value from super-global array $_GET.
	 */
	private function getFromGET($key) {
		return filter_input(INPUT_GET, $key);
	}

	/**
	 * Returns value from super-global array $_POST.
	 */
	private function getFromPOST($key) {
		return filter_input(INPUT_POST, $key);
	}
}
?>
