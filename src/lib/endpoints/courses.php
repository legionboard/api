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
		$subjects = self::getFromPOST('subjects');
		if ($this->courses->checkByName($name)) {
			$this->api->setStatus(400);
			return Array('error' => Array(Array('code' => '2301', 'message' => 'A course with the given name already exists.')));
		}
		$identification = $this->courses->create($name, $subjects);
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
		$subjects = $params['subjects'];
		if (!empty($missing)) {
			$this->api->setStatus(400);
			return Array('missing' => $missing);
		}
		if ($this->courses->update($identification, $name, $subjects, $archived)) {
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

