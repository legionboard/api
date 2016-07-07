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
	private $database;

	// MySQL table for changes
	public static $tableChanges;

	// MySQL tabel for teachers
	public static $tableTeachers;

	// MySQL table for courses
	public static $tableCourses;
	
	// MySQL table for authentication
	public static $tableAuthentication;
	
	// MySQL table for activities
	public static $tableActivities;

	/**
	 * Connect with the database.
	 */
	public function __construct() {
		require_once __DIR__ . '/configuration.php';
		$config = new Configuration();
		$mysqlHost = $config->get("MySQL", "Host");
		$mysqlUser = $config->get("MySQL", "User");
		$mysqlPW = $config->get("MySQL", "Password");
		$mysqlDB = $config->get("MySQL", "Database");
		$tablePrefix = $config->get("MySQL", "Table_Prefix");

		self::$tableChanges = $tablePrefix . "_changes";
		self::$tableTeachers = $tablePrefix . "_teachers";
		self::$tableCourses = $tablePrefix . "_courses";
		self::$tableAuthentication = $tablePrefix . "_authentication";
		self::$tableActivities = $tablePrefix . "_activities";
		// If table prefix does not exist
		if ($tablePrefix == '') {
			self::$tableChanges = $config->get("MySQL", "Table_Changes");
			self::$tableTeachers = $config->get("MySQL", "Table_Teachers");
			self::$tableAuthentication = $config->get("MySQL", "Table_Authentication");
			// Table courses didn't exist before prefix
			self::$tableCourses = "lb_0_courses";
			self::$tableActivities = "lb_0_activities";
		}
		$this->database = new mysqli($mysqlHost, $mysqlUser, $mysqlPW, $mysqlDB);
		// Check if tables exist and create them if not
		if (!self::checkTable(self::$tableChanges)) {
			self::createTableChanges();
		}
		if (!self::checkTable(self::$tableTeachers)) {
			self::createTableTeachers();
		}
		if (!self::checkTable(self::$tableCourses)) {
			self::createTableCourses();
		}
		if (!self::checkTable(self::$tableAuthentication)) {
			self::createTableAuthentication();
		}
		if (!self::checkTable(self::$tableActivities)) {
			self::createTableActivities();
		}
		self::updateTables($mysqlDB);
	}

	/**
	 * Get database connection.
	 */
	public function get() {
		return $this->database;
	}

	/**
	 * Check if table in database exists.
	 */
	private function checkTable($table) {
		$sql = "SHOW TABLES LIKE '" . $table . "'";
		return $this->database->query($sql)->num_rows == 1;
	}

	/**
	 * Create table for changes.
	 */
	private function createTableChanges() {
		$sql = "CREATE TABLE " . self::$tableChanges . " (
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
		if(!$this->database->query($sql)) {
			// Workaround for MySQL bug: http://stackoverflow.com/a/17498167
			$sql = "CREATE TABLE " . self::$tableChanges . " (
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
			$this->database->query($sql);
		}
	}

	/**
	 * Create table for teachers.
	 */
	private function createTableTeachers() {
		$sql = "CREATE TABLE " . self::$tableTeachers . " (
		  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  name VARCHAR(255) NOT NULL UNIQUE,
		  archived BOOLEAN DEFAULT 0,
		  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (id)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
		if(!$this->database->query($sql)) {
			// Workaround for MySQL bug: http://stackoverflow.com/a/17498167
			$sql = "CREATE TABLE " . self::$tableTeachers . " (
			  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
			  name VARCHAR(255) NOT NULL UNIQUE,
			  archived BOOLEAN DEFAULT 0,
			  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  edited TIMESTAMP,
			  PRIMARY KEY (id)
			) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
			$this->database->query($sql);
		}
		// Create default teacher "All"
		$name = 'Alle';
		$sql = "INSERT INTO " . self::$tableTeachers . " (name, id) VALUES ('$name', '" . self::DEFAULT_TEACHER_ALL . "')";
		$this->database->query($sql);
	}

	/**
	 * Create table for courses.
	 */
	private function createTableCourses() {
		$sql = "CREATE TABLE " . self::$tableCourses . " (
		  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  name VARCHAR(255) NOT NULL UNIQUE,
		  archived BOOLEAN DEFAULT 0,
		  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (id)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
		if(!$this->database->query($sql)) {
			// Workaround for MySQL bug: http://stackoverflow.com/a/17498167
			$sql = "CREATE TABLE " . self::$tableCourses . " (
			  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
			  name VARCHAR(255) NOT NULL UNIQUE,
			  archived BOOLEAN DEFAULT 0,
			  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  edited TIMESTAMP,
			  PRIMARY KEY (id)
			) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
			$this->database->query($sql);
		}
	}

	/**
	 * Create table for authentication.
	 */
	private function createTableAuthentication() {
		$sql = "CREATE TABLE " . self::$tableAuthentication . " (
		  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  _key VARCHAR(128) NOT NULL UNIQUE,
		  groups VARCHAR(300) NOT NULL,
		  username VARCHAR(300) NOT NULL,
		  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (id)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
		if(!$this->database->query($sql)) {
			// Workaround for MySQL bug: http://stackoverflow.com/a/17498167
			$sql = "CREATE TABLE " . self::$tableAuthentication . " (
			  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
			  _key VARCHAR(128) NOT NULL UNIQUE,
			  groups VARCHAR(300) NOT NULL,
			  username VARCHAR(300) NOT NULL,
			  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  edited TIMESTAMP,
			  PRIMARY KEY (id)
			) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
			$this->database->query($sql);
		}
	}

	/**
	 * Create table for activities.
	 */
	private function createTableActivities() {
		$sql = "CREATE TABLE " . self::$tableActivities . " (
		  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  user MEDIUMINT(8) UNSIGNED NOT NULL,
		  action TINYINT(2) UNSIGNED NOT NULL,
		  affectedResource VARCHAR(255) NOT NULL,
		  affectedID MEDIUMINT(8) UNSIGNED NOT NULL,
		  time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  PRIMARY KEY (id)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
		$this->database->query($sql);
	}

	/**
	 * Checks every table and if neccessary updates it.
	 */
	private function updateTables($dbName) {
		if (!self::checkColumn("course", self::$tableChanges, $dbName)) {
			$sql = "ALTER TABLE " . self::$tableChanges . " ADD course MEDIUMINT(8) AFTER teacher";
			$this->database->query($sql);
		}
		if (!self::checkColumn("archived", self::$tableCourses, $dbName)) {
			$sql = "ALTER TABLE " . self::$tableCourses. " ADD archived BOOLEAN DEFAULT 0 AFTER name";
			$this->database->query($sql);
		}
		if (!self::checkColumn("archived", self::$tableTeachers, $dbName)) {
			$sql = "ALTER TABLE " . self::$tableTeachers . " ADD archived BOOLEAN DEFAULT 0 AFTER name";
			$this->database->query($sql);
		}
		if (!self::checkColumn("username", self::$tableAuthentication, $dbName)) {
			$sql = "ALTER TABLE " . self::$tableAuthentication. " ADD username VARCHAR(300) NOT NULL AFTER groups";
			$this->database->query($sql);
		}
		if (!self::checkColumn("id", self::$tableAuthentication, $dbName)) {
			$sql = "ALTER TABLE " . self::$tableAuthentication. " ADD id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
			$this->database->query($sql);
		}
	}

	/**
	 * Check if a column exists.
	 */
	private function checkColumn($column, $table, $dbName) {
		$sql = "SELECT *
					FROM information_schema.COLUMNS
					WHERE
						TABLE_SCHEMA = '" . $dbName . "' AND
						TABLE_NAME = '" . $table . "' AND
						COLUMN_NAME = '" . $column . "'";
		return $this->database->query($sql)->num_rows == 1;
	}
}
?>
