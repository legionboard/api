<?php
/*
 * @author	Nico Alt
 * @date	24.07.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
require_once __DIR__ . '/abstractEndpoint.php';
class ActivitiesEndpoint extends AbstractEndpoint {
	
	public function handleGET() {
		$activities = $this->activities->get($this->api->getID());
		if ($activities != null) {
			return $activities;
		}
		$this->api->setStatus(404);
		return null;
	}
}

