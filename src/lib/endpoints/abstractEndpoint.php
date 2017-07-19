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
namespace LegionBoard\Endpoints;

abstract class AbstractEndpoint {

	/**
	 * Property: api
	 * API basing on abstractAPI.
	 */
	protected $api = null;

	public function __construct($api, $user) {
		$this->api = $api;
		require_once __DIR__ . '/../resources/activities.php';
		$this->activities = new ActivitiesResource($user);
		require_once __DIR__ . '/../resources/changes.php';
		$this->changes = new ChangesResource($user);
		require_once __DIR__ . '/../resources/courses.php';
		$this->courses = new CoursesResource($user);
		require_once __DIR__ . '/../resources/teachers.php';
		$this->teachers = new TeachersResource($user);
		require_once __DIR__ . '/../resources/subjects.php';
		$this->subjects = new SubjectsResource($user);
	}

	/**
	 * Returns value from super-global array $_GET.
	 */
	protected function getFromGET($key) {
		return filter_input(INPUT_GET, $key);
	}

	/**
	 * Returns value from super-global array $_POST.
	 */
	protected function getFromPOST($key) {
		return filter_input(INPUT_POST, $key);
	}
}
?>
