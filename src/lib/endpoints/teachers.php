<?php
/*
 * @author	Nico Alt
 * @date	27.06.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
require_once __DIR__ . '/abstractEndpoint.php';
class TeachersEndpoint extends AbstractEndpoint {

	// Default teacher 'All'
	const DEFAULT_TEACHER_ALL = '1';
	
	public function handleGET() {
		$identification = $this->api->getID();
		$teachers = $this->teachers->get($identification);
		if ($teachers != null) {
			return $teachers;
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
		if ($this->teachers->checkByName($name)) {
			$this->api->setStatus(400);
			return Array('error' => Array(Array('code' => '301', 'message' => 'A teacher with the given name already exists.')));
		}
		$identification = $this->teachers->create($name);
		if (isset($identification)) {
			$this->api->setStatus(201);
			return Array('id' => $identification);
		}
		$this->api->setStatus(409);
		return Array('error' => Array(Array('code' => '300', 'message' => 'The teacher could not get created.')));
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
				return Array('error' => Array(Array('code' => '201', 'message' => 'The parameter archived may only contain true or false.')));
		}
		if (!empty($missing)) {
			$this->api->setStatus(400);
			return Array('missing' => $missing);
		}
		if ($this->teachers->update($identification, $name, $archived)) {
			$this->api->setStatus(204);
			return null;
		}
		$this->api->setStatus(409);
		return Array('error' => Array(Array('code' => '200', 'message' => 'The teacher could not get updated.')));
	}
	
	public function handleDELETE() {
		$identification = $this->api->getID();
		if ($identification == '') {
			$this->api->setStatus(400);
			return Array('missing' => Array('id'));
		}
		if ($identification == self::DEFAULT_TEACHER_ALL) {
			$this->api->setStatus(400);
			return Array('error' => Array(Array('code' => '401', 'message' => 'Deleting the teacher with ID 1 is not allowed.')));				
		}
		if ($this->changes->get(null, Array($identification)) != null || $this->changes->get(null, null, null, $identification) != null) {
			$this->api->setStatus(400);
			return Array('error' => Array(Array('code' => '402', 'message' => 'The teacher is still linked to a change.')));
		}
		if ($this->teachers->delete($identification)) {
			$this->api->setStatus(204);
			return null;
		}
		$this->api->setStatus(409);
		return Array('error' => Array(Array('code' => '400', 'message' => 'The teacher could not get deleted.')));
	}
}

