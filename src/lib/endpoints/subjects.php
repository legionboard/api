<?php
/*
 * @author	Jan Weber
 * @date	13.11.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
require_once __DIR__ . '/abstractEndpoint.php';
class SubjectsEndpoint extends AbstractEndpoint {
	
	public function handleGET($seeTimes = false) {
		$subjects = $this->subjects->get($this->api->getID(), $seeTimes);
		if ($subjects != null) {
			return $subjects;
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
		$shortcut = self::getFromPOST('shortcut');
		if ($shortcut == '') {
			$this->api->setStatus(400);
			return Array('missing' => Array('shortcut'));
		}
		if ($this->subjects->checkByName($name)) {
			$this->api->setStatus(400);
			return Array('error' => Array(Array('code' => '3301', 'message' => 'A subject with the given name already exists.')));
		}
		if ($this->subjects->checkByShortcut($shortcut)) {
			$this->api->setStatus(400);
			return Array('error' => Array(Array('code' => '3302', 'message' => 'A subject with the given shortcut already exists.')));
		}
		$identification = $this->subjects->create($name);
		if (isset($identification)) {
			$this->api->setStatus(201);
			return Array('id' => $identification);
		}
		$this->api->setStatus(409);
		return Array('error' => Array(Array('code' => '3300', 'message' => 'The subject could not get created.')));
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
		$shortcut = $params['shortcut'];
		if ($shortcut == '') {
			$missing[] = 'shortcut';
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
				return Array('error' => Array(Array('code' => '3201', 'message' => 'The parameter archived may only contain true or false.')));
		}
		if (!empty($missing)) {
			$this->api->setStatus(400);
			return Array('missing' => $missing);
		}
		if ($this->subjects->update($identification, $name, $shortcut, $archived)) {
			$this->api->setStatus(204);
			return null;
		}
		$this->api->setStatus(409);
		return Array('error' => Array(Array('code' => '3200', 'message' => 'The subject could not get updated.')));
	}
	
	public function handleDELETE() {
		$identification = $this->api->getID();
		if ($identification == '') {
			$this->api->setStatus(400);
			return Array('missing' => Array('id'));
		}
		if ($this->changes->get(null, null, null, null, null, null, null, null, null, Array($identification)) != null) {
			$this->api->setStatus(400);
			return Array('error' => Array(Array('code' => '3401', 'message' => 'The subject is still linked to a change.')));
		}
		if ($this->courses->delete($identification)) {
			$this->api->setStatus(204);
			return null;
		}
		$this->api->setStatus(409);
		return Array('error' => Array(Array('code' => '3400', 'message' => 'The subject could not get deleted.')));
	}
}

