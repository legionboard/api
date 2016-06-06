<?php
/*
 * @author	Nico Alt
 * @date	16.03.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 */
class Database {
	
	// Default teacher 'All'
	const DEFAULT_TEACHER_ALL = '1';

	// Connection to database
	private $db;

	// MySQL table for changes
	public static $table_changes;

	// MySQL tabel for teachers
	public static $table_teachers;

	// MySQL table for courses
	public static $table_courses;
	
	// MySQL table for authentication
	public static $table_authentication;

	/**
	 * Connect with the database.
	 */
	public function __construct() {
		require_once __DIR__ . '/configuration.php';
		$mysqlhost = Configuration::get("MySQL", "Host");
		$mysqluser = Configuration::get("MySQL", "User");
		$mysqlpw = Configuration::get("MySQL", "Password");
		$mysqldb = Configuration::get("MySQL", "Database");
		$table_prefix = Configuration::get("MySQL", "Table_Prefix");
		if ($table_prefix != '') {
			self::$table_changes = $table_prefix . "_changes";
			self::$table_teachers = $table_prefix . "_teachers";
			self::$table_courses = $table_prefix . "_courses";
			self::$table_authentication = $table_prefix . "_authentication";
		}
		else {
			self::$table_changes = Configuration::get("MySQL", "Table_Changes");
			self::$table_teachers = Configuration::get("MySQL", "Table_Teachers");
			self::$table_authentication = Configuration::get("MySQL", "Table_Authentication");
			// Table courses didn't exist before prefix
			self::$table_courses = "lb_0_courses";
		}
		$this->db = new mysqli($mysqlhost, $mysqluser, $mysqlpw, $mysqldb);
		// Check if tables exist and create them if not
		if (!self::checkTable(self::$table_changes)) {
			self::createTable_Changes();
		}
		if (!self::checkTable(self::$table_teachers)) {
			self::createTable_Teachers();
		}
		if (!self::checkTable(self::$table_courses)) {
			self::createTable_Courses();
		}
		if (!self::checkTable(self::$table_authentication)) {
			self::createTable_Authentication();
		}
		self::updateTables($mysqldb);
	}

	/**
	 * Get database connection.
	 */
	public function get() {
		return $this->db;
	}

	/**
	 * Check if table in database exists.
	 */
	private function checkTable($table) {
		$sql = "SHOW TABLES LIKE '" . $table . "'";
		return $this->db->query($sql)->num_rows == 1;
	}

	/**
	 * Create table for changes.
	 */
	private function createTable_Changes() {
		$sql = "CREATE TABLE " . self::$table_changes . " (
		  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  teacher MEDIUMINT(8) NOT NULL,
		  course MEDIUMINT(8),
		  startBy VARCHAR(13) NOT NULL,
		  endBy VARCHAR(13) NOT NULL,
		  type VARCHAR(1) NOT NULL,
		  coveringTeacher MEDIUMINT(8),
		  text LONGTEXT,
		  reason VARCHAR(1) NOT NULL,
		  privateText LONGTEXT,
		  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (id)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
		if(!$this->db->query($sql)) {
			// Workaround for MySQL bug: http://stackoverflow.com/a/17498167
			$sql = "CREATE TABLE " . self::$table_changes . " (
			  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
			  teacher MEDIUMINT(8) NOT NULL,
			  course MEDIUMINT(8),
			  startBy VARCHAR(13) NOT NULL,
			  endBy VARCHAR(13) NOT NULL,
			  type VARCHAR(1) NOT NULL,
			  coveringTeacher MEDIUMINT(8),
			  text LONGTEXT,
			  reason VARCHAR(1) NOT NULL,
			  privateText LONGTEXT,
			  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  edited TIMESTAMP,
			  PRIMARY KEY (id)
			) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
			$this->db->query($sql);
		}
	}

	/**
	 * Create table for teachers.
	 */
	private function createTable_Teachers() {
		$sql = "CREATE TABLE " . self::$table_teachers . " (
		  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  name VARCHAR(255) NOT NULL UNIQUE,
		  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (id)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
		if(!$this->db->query($sql)) {
			// Workaround for MySQL bug: http://stackoverflow.com/a/17498167
			$sql = "CREATE TABLE " . self::$table_teachers . " (
			  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
			  name VARCHAR(255) NOT NULL UNIQUE,
			  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  edited TIMESTAMP,
			  PRIMARY KEY (id)
			) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
			$this->db->query($sql);
		}
		// Create default teacher "All"
		$name = 'Alle';
		$sql = "INSERT INTO " . self::$table_teachers . " (name, id) VALUES ('$name', '" . self::DEFAULT_TEACHER_ALL . "')";
		$this->db->query($sql);
	}

	/**
	 * Create table for courses.
	 */
	private function createTable_Courses() {
		$sql = "CREATE TABLE " . self::$table_courses . " (
		  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  name VARCHAR(255) NOT NULL UNIQUE,
		  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (id)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
		if(!$this->db->query($sql)) {
			// Workaround for MySQL bug: http://stackoverflow.com/a/17498167
			$sql = "CREATE TABLE " . self::$table_courses . " (
			  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
			  name VARCHAR(255) NOT NULL UNIQUE,
			  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  edited TIMESTAMP,
			  PRIMARY KEY (id)
			) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
			$this->db->query($sql);
		}
	}

	/**
	 * Create table for authentication.
	 */
	private function createTable_Authentication() {
		$sql = "CREATE TABLE " . self::$table_authentication . " (
		  _key VARCHAR(128) NOT NULL UNIQUE,
		  groups VARCHAR(300) NOT NULL,
		  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
		if(!$this->db->query($sql)) {
			// Workaround for MySQL bug: http://stackoverflow.com/a/17498167
			$sql = "CREATE TABLE " . self::$table_authentication . " (
			  _key VARCHAR(128) NOT NULL UNIQUE,
			  groups VARCHAR(300) NOT NULL,
			  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  edited TIMESTAMP
			) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
			$this->db->query($sql);
		}
	}

	/**
	 * Checks every table and if neccessary updates it.
	 */
	private function updateTables($db) {
		if (!self::checkColumn("course", self::$table_changes, $db)) {
			$sql = "ALTER TABLE " . self::$table_changes . " ADD course MEDIUMINT(8) AFTER teacher";
			$this->db->query($sql);
		}
	}

	/**
	 * Check if a column exists.
	 */
	private function checkColumn($column, $table, $db) {
		$sql = "SELECT *
					FROM information_schema.COLUMNS
					WHERE
						TABLE_SCHEMA = '" . $db . "' AND
						TABLE_NAME = '" . $table . "' AND
						COLUMN_NAME = '" . $column . "'";
		return $this->db->query($sql)->num_rows == 1;
	}
}
?>
