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
	const GROUP_SEE_COURSES = 10;
	const GROUP_ADD_COURSE = 11;
	const GROUP_UPDATE_COURSE = 12;
	const GROUP_DELETE_COURSE = 13;

	public function __construct($request) {
		$this->setVersionName('0.1.2');
		$this->setVersionCode('1');
		parent::__construct($request);
		require_once __DIR__ . '/authentication.php';
		$this->authentication = new Authentication();
	}

	/**
	 * Endpoint: changes
	 * Accepts: GET, PUT, POST, DELETE, OPTIONS
	 */
	protected function changes() {
		// Import endpoint "changes"
		require_once __DIR__ . '/endpoints/changes.php';
		$changesEndpoint = new ChangesEndpoint($this);
		if ($this->getMethod() == 'GET') {
			$key = self::getFromGET('k');
			// Verifiy user is allowed to see changes
			if (!$this->authentication->verifiy($key, self::GROUP_SEE_CHANGES)) {
				$this->setStatus(401);
				return null;
			}
			// Verifiy user is allowed to see reasons
			$seeReasons = $this->authentication->verifiy($key, self::GROUP_SEE_REASONS);
			// Verifiy user is allowed to see private texts
			$seePrivateTexts = $this->authentication->verifiy($key, self::GROUP_SEE_PRIVATE_TEXTS);
			return $changesEndpoint->handleGET($seeReasons, $seePrivateTexts);
		}
		if ($this->getMethod() == 'POST') {
			$key = self::getFromPOST('k');
			// Verifiy user is allowed to add changes
			if (!$this->authentication->verifiy($key, self::GROUP_ADD_CHANGE)) {
				$this->setStatus(401);
				return null;
			}
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
			return $changesEndpoint->handlePUT($params);
		}
		if ($this->getMethod() == 'DELETE') {
			$key = self::getFromGET('k');
			// Verifiy user is allowed to delete changes
			if (!$this->authentication->verifiy($key, self::GROUP_DELETE_CHANGE)) {
				$this->setStatus(401);
				return null;
			}
			return $changesEndpoint->handleDELETE();
		}
		if ($this->getMethod() == 'OPTIONS') {
			$this->setStatus(200);
			return null;
		}
		$this->setStatus(405);
		return Array('error' => Array(Array('code' => '1000', 'message' => "Only accepts GET, PUT, POST and DELETE requests.")));
	}

	/**
	 * Endpoint: courses
	 * Accepts: GET, PUT, POST, DELETE
	 */
	protected function courses() {
		// Import endpoint "courses"
		require_once __DIR__ . '/endpoints/courses.php';
		$coursesEndpoint = new CoursesEndpoint($this);
		if ($this->getMethod() == 'GET') {
			$key = self::getFromGET('k');
			// Verify user is allowed to see changes
			if (!$this->authentication->verifiy($key, self::GROUP_SEE_COURSES)) {
				$this->setStatus(401);
				return null;
			}
			return $coursesEndpoint->handleGET();
		}
		if ($this->getMethod() == 'POST') {
			$key = self::getFromPOST('k');
			// Verify user is allowed to add changes
			if (!$this->authentication->verifiy($key, self::GROUP_ADD_COURSE)) {
				$this->setStatus(401);
				return null;
			}
			return $coursesEndpoint->handlePOST();
		}
		if ($this->getMethod() == 'PUT') {
			parse_str($this->getFile(), $params);
			$key = $params['k'];
			// Verify user is allowed to update changes
			if (!$this->authentication->verifiy($key, self::GROUP_UPDATE_COURSE)) {
				$this->setStatus(401);
				return null;
			}
			return $coursesEndpoint->handlePUT($params);
		}
		if ($this->getMethod() == 'DELETE') {
			$key = self::getFromGET('k');
			// Verify user is allowed to delete changes
			if (!$this->authentication->verifiy($key, self::GROUP_DELETE_COURSE)) {
				$this->setStatus(401);
				return null;
			}
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
	 * Endpoint: teachers
	 * Accepts: GET, PUT, POST, DELETE
	 */
	protected function teachers() {
		// Import endpoint "teachers"
		require_once __DIR__ . '/endpoints/teachers.php';
		$teachersEndpoint = new TeachersEndpoint($this);
		if ($this->getMethod() == 'GET') {
			$key = self::getFromGET('k');
			// Verify user is allowed to see teachers
			if (!$this->authentication->verifiy($key, self::GROUP_SEE_TEACHERS)) {
				$this->setStatus(401);
				return null;
			}
			return $teachersEndpoint->handleGET();
		}
		if ($this->getMethod() == 'POST') {
			$key = self::getFromPOST('k');
			// Verify user is allowed to add teachers
			if (!$this->authentication->verifiy($key, self::GROUP_ADD_TEACHER)) {
				$this->setStatus(401);
				return null;
			}
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
			return $teachersEndpoint->handlePUT($params);
		}
		if ($this->getMethod() == 'DELETE') {
			$key = self::getFromGET('k');
			// Verify user is allowed to delete teachers
			if (!$this->authentication->verifiy($key, self::GROUP_DELETE_TEACHER)) {
				$this->setStatus(401);
				return null;
			}
			return $teachersEndpoint->handleDELETE();
		}
		if ($this->method == 'OPTIONS') {
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
