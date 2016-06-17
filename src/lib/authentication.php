<?php
/*
 * @author	Nico Alt
 * @date	16.03.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
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
		$sql = "SELECT * FROM " . Database::$table_authentication . " WHERE _key LIKE '$key' LIMIT 1";
		$query = $this->db->query($sql);
		if (!$query || $query->num_rows == 0) {
			return false;
		}
		// Transform comma separated groups to array
		$groups = explode(",", $query->fetch_array()['groups']);
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
		$sql = "INSERT INTO " . Database::$table_authentication . " (_key, groups, username) VALUES ('$key', '$groups', '$username')";
		return $this->db->query($sql);
	}
}
?>
