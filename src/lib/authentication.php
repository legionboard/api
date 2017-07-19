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
namespace LegionBoard\Lib;

class Authentication {
	

	/**
	 * Connect with the database.
	 */
	public function __construct() {
		require_once __DIR__ . '/database.php';
		$database = new Database();
		$this->db = $database->get();
	}

	/**
	 * Verify a key.
	 */
	public function verifiy($key, $group) {
		if ($key == '') {
			return false;
		}
		// Generate SHA-512 hash of key
		$key = hash('sha512', $key);
		$sql = "SELECT * FROM " . Database::$tableAuthentication . " WHERE _key LIKE '$key' LIMIT 1";
		$query = $this->db->query($sql);
		if (!$query || $query->num_rows == 0) {
			return false;
		}
		$groups = $query->fetch_array()['groups'];
		// Check if key has admin privileges
		if ($groups == '%') {
			return true;
		}
		// Transform comma separated groups to array
		$groups = explode(",", $groups);
		return in_array($group, $groups);
	}

	/**
	 * Create a key.
	 */
	public function create($key, $groups, $username) {
		// Generate SHA-512 hash of key
		$key = hash('sha512', $key);
		$groups = $this->db->escape_string($groups);
		$username= $this->db->escape_string($username);
		$sql = "INSERT INTO " . Database::$tableAuthentication . " (_key, groups, username) VALUES ('$key', '$groups', '$username')";
		return $this->db->query($sql);
	}

	/**
	 * Returns the user ID of a given authentication key.
	 */
	public function getUserID($key) {
		$key = hash('sha512', $key);
		$sql = "SELECT id FROM " . Database::$tableAuthentication . " WHERE _key LIKE '$key' LIMIT 1";
		$query = $this->db->query($sql);
		if (!$query || $query->num_rows == 0) {
			return 0;
		}
		return $query->fetch_array()['id'];
	}

	/**
	 * Returns the username of a given ID.
	 */
	public function getUsername($id) {
		$sql = "SELECT username FROM " . Database::$tableAuthentication . " WHERE id LIKE '$id' LIMIT 1";
		$query = $this->db->query($sql);
		if (!$query || $query->num_rows == 0) {
			return 0;
		}
		return $query->fetch_array()['username'];
	}
}
?>
