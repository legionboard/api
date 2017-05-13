<?php
/*
 * Copyright (C) 2017 Nico Alt
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
 *
 * Endpoint: export
 * Accepts: GET
 */
require_once __DIR__ . '/abstractEndpoint.php';
class ExportEndpoint extends AbstractEndpoint {

	/**
	 * Handles GET requests.
	 */
	public function handleGET() {
		$startBy = self::getFromGET('startBy');
		if ($startBy == '') {
			$startBy = 'now';
		}
		if ($startBy != '') {
			if (self::replaceAlias($startBy) != null) {
				$startBy = self::replaceAlias($startBy);
			}
			if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $startBy)) {
				$error[] = Array('code' => '4100', 'message' => 'The starting date is formatted badly.');
			}
			else if (!checkdate(substr($startBy, 5, 2), substr($startBy, 8, 2), substr($startBy, 0, 4))) {
				$error[] = Array('code' => '4101', 'message' => 'The starting date does not exist.');
			}
		}
		$endBy = self::getFromGET('endBy');
		if ($endBy == '') {
			$endBy = 'i1w';
		}
		if ($endBy != '') {
			if (self::replaceAlias($endBy) != null) {
				$endBy = self::replaceAlias($endBy);
			}
			if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $endBy)) {
				$error[] = Array('code' => '4102', 'message' => 'The ending date is formatted badly.');
			}
			else if (!checkdate(substr($endBy, 5, 2), substr($endBy, 8, 2), substr($endBy, 0, 4))) {
				$error[] = Array('code' => '4103', 'message' => 'The ending date does not exist.');
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
				$error[] = Array('code' => '4104', 'message' => 'The ending date has to be after the starting date.');
			}
		}
		if (!empty($error)) {
			$this->api->setStatus(400);
			return Array('error' => $error);
		}
		// Get all resources and build export Array
		$changes = $this->changes->get(null, null, null, null, $startBy, $endBy, true, true, true);
		$courses = $this->courses->get(null, true);
		$subjects = $this->subjects->get(null, true);
		$teachers = $this->teachers->get(null, true);
		$this->api->setStatus(200);
		$export = Array(
					"resources" => Array(
						"changes" => $changes,
						"courses" => $courses,
						"subjects" => $subjects,
						"teachers" => $teachers
						)
					);
		return $export;
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
