<?php
/*
 * @author	Nico Alt
 * @date	27.06.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
require_once __DIR__ . '/abstractEndpoint.php';
class CoursesEndpoint extends AbstractEndpoint {
	
	public function handleGET($seeTimes = false) {
		$courses = $this->courses->get($this->api->getID(), $seeTimes);
		if ($courses != null) {
			return $courses;
		}
		$this->api->setStatus(404);
		return null;
	}
	
	public function handlePOST() {
		$name = self::getFromPOST('name');
		if ($name == '') {
			$this->api->setStatus(400);
			return Array('missing' => Array('name'));
		}
		if ($this->courses->checkByName($name)) {
			$this->api->setStatus(400);
			return Array('error' => Array(Array('code' => '2301', 'message' => 'A course with the given name already exists.')));
		}
		$identification = $this->courses->create($name);
		if (isset($identification)) {
			$this->api->setStatus(201);
			return Array('id' => $identification);
		}
		$this->api->setStatus(409);
		return Array('error' => Array(Array('code' => '2300', 'message' => 'The course could not get created.')));
	}
	
	public function handlePUT($params) {
		$identification = $this->api->getID();
		$missing = Array();
		if ($identification == '') {
			$missing[] = 'id';
		}
		$name = $params['name'];
		if ($name == '') {
			$missing[] = 'name';
		}
		$archived = $params['archived'];
		switch ($archived) {
			case 'false':
				$archived = '0';
				break;
			case 'true':
				$archived = '1';
				break;
			case '':
				$missing[] = 'archived';
				break;
			default:
				$this->api->setStatus(400);
				return Array('error' => Array(Array('code' => '2201', 'message' => 'The parameter archived may only contain true or false.')));
		}
		if (!empty($missing)) {
			$this->api->setStatus(400);
			return Array('missing' => $missing);
		}
		if ($this->courses->update($identification, $name, $archived)) {
			$this->api->setStatus(204);
			return null;
		}
		$this->api->setStatus(409);
		return Array('error' => Array(Array('code' => '2200', 'message' => 'The course could not get updated.')));
	}
	
	public function handleDELETE() {
		$identification = $this->api->getID();
		if ($identification == '') {
			$this->api->setStatus(400);
			return Array('missing' => Array('id'));
		}
		if ($this->changes->get(null, null, Array($identification)) != null) {
			$this->api->setStatus(400);
			return Array('error' => Array(Array('code' => '2401', 'message' => 'The course is still linked to a change.')));
		}
		if ($this->courses->delete($identification)) {
			$this->api->setStatus(204);
			return null;
		}
		$this->api->setStatus(409);
		return Array('error' => Array(Array('code' => '2400', 'message' => 'The course could not get deleted.')));
	}
}

