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
namespace LegionBoard;

class Database
{

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

    // MySQL table for subjects
    public static $tableSubjects;

    /**
     * Connect with the database.
     */
    public function __construct()
    {
        require_once __DIR__ . '/Configuration.php';
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
        self::$tableSubjects = $tablePrefix . "_subjects";
        $this->database = new \mysqli($mysqlHost, $mysqlUser, $mysqlPW, $mysqlDB);
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
        if (!self::checkTable(self::$tableSubjects)) {
            self::createTableSubjects();
        }
        self::updateTables($mysqlDB);
    }

    /**
     * Get database connection.
     */
    public function get()
    {
        return $this->database;
    }

    /**
     * Check if table in database exists.
     */
    private function checkTable($table)
    {
        $sql = "SHOW TABLES LIKE '" . $table . "'";
        return $this->database->query($sql)->num_rows == 1;
    }

    /**
     * Create table for changes.
     */
    private function createTableChanges()
    {
        $sql = "CREATE TABLE " . self::$tableChanges . " (
		  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  teacher MEDIUMINT(8) DEFAULT 0,
		  course MEDIUMINT(8) DEFAULT 0,
		  startingDate DATE NOT NULL,
		  startingHour VARCHAR(2),
		  endingDate DATE NOT NULL,
		  endingHour VARCHAR(2),
		  type VARCHAR(1) NOT NULL,
		  coveringTeacher MEDIUMINT(8),
		  text LONGTEXT,
		  subject MEDIUMINT(8) DEFAULT 0,
		  reason VARCHAR(1) NOT NULL,
		  privateText LONGTEXT,
		  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (id)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        return $this->database->query($sql);
    }

    /**
     * Create table for teachers.
     */
    private function createTableTeachers()
    {
        $sql = "CREATE TABLE " . self::$tableTeachers . " (
		  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  name VARCHAR(255) NOT NULL UNIQUE,
		  subject MEDIUMINT(8) DEFAULT 0,
		  archived BOOLEAN DEFAULT 0,
		  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (id)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        return $this->database->query($sql);
    }

    /**
     * Create table for courses.
     */
    private function createTableCourses()
    {
        $sql = "CREATE TABLE " . self::$tableCourses . " (
		  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  name VARCHAR(255) NOT NULL UNIQUE,
		  subject MEDIUMINT(8) DEFAULT 0,
		  archived BOOLEAN DEFAULT 0,
		  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (id)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        return $this->database->query($sql);
    }

    /**
     * Create table for authentication.
     */
    private function createTableAuthentication()
    {
        $sql = "CREATE TABLE " . self::$tableAuthentication . " (
		  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  _key VARCHAR(128) NOT NULL UNIQUE,
		  groups VARCHAR(300) NOT NULL,
		  username VARCHAR(300) NOT NULL,
		  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (id)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        return $this->database->query($sql);
    }

    /**
     * Create table for activities.
     */
    private function createTableActivities()
    {
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
     * Create table for subjects.
     */
    private function createTableSubjects()
    {
        $sql = "CREATE TABLE " . self::$tableSubjects . " (
		  id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  name VARCHAR(255) NOT NULL UNIQUE,
		  shortcut VARCHAR(255) NOT NULL UNIQUE,
		  archived BOOLEAN DEFAULT 0,
		  added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (id)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        return $this->database->query($sql);
    }

    /**
     * Checks every table and if neccessary updates it.
     */
    private function updateTables($dbName)
    {
        // Add column course in table changes
        if (!self::checkColumn("course", self::$tableChanges, $dbName)) {
            $sql = "ALTER TABLE " . self::$tableChanges . " ADD course MEDIUMINT(8) DEFAULT 0 AFTER teacher";
            $this->database->query($sql);
        }
        // Add column archived in table courses
        if (!self::checkColumn("archived", self::$tableCourses, $dbName)) {
            $sql = "ALTER TABLE " . self::$tableCourses. " ADD archived BOOLEAN DEFAULT 0 AFTER name";
            $this->database->query($sql);
        }
        // Add column archived in table teachers
        if (!self::checkColumn("archived", self::$tableTeachers, $dbName)) {
            $sql = "ALTER TABLE " . self::$tableTeachers . " ADD archived BOOLEAN DEFAULT 0 AFTER name";
            $this->database->query($sql);
        }
        // Add column username in table authentication
        if (!self::checkColumn("username", self::$tableAuthentication, $dbName)) {
            $sql = "ALTER TABLE " . self::$tableAuthentication. " ADD username VARCHAR(300) NOT NULL AFTER groups";
            $this->database->query($sql);
        }
        // Add column id in table authentication
        if (!self::checkColumn("id", self::$tableAuthentication, $dbName)) {
            $sql = "ALTER TABLE " . self::$tableAuthentication. " ADD id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
            $this->database->query($sql);
        }
        // Allow column teacher in table changes to be null
        if (!self::checkColumnAllowsNull("teacher", self::$tableChanges, $dbName)) {
            $sql = "ALTER TABLE " . self::$tableChanges. " MODIFY teacher MEDIUMINT(8) DEFAULT 0";
            $this->database->query($sql);
        }
        // Add column subject in table changes
        if (!self::checkColumn("subject", self::$tableChanges, $dbName)) {
            $sql = "ALTER TABLE " . self::$tableChanges . " ADD subject MEDIUMINT(8) DEFAULT 0 AFTER text";
            $this->database->query($sql);
        }
        // Add column subjects in table courses
        if (!self::checkColumn("subjects", self::$tableCourses, $dbName)) {
            $sql = "ALTER TABLE " . self::$tableCourses . " ADD subject MEDIUMINT(8) DEFAULT 0 AFTER name";
            $this->database->query($sql);
        }
        // Add column subjects in table teachers
        if (!self::checkColumn("subjects", self::$tableTeachers, $dbName)) {
            $sql = "ALTER TABLE " . self::$tableTeachers . " ADD subject MEDIUMINT(8) DEFAULT 0 AFTER name";
            $this->database->query($sql);
        }
        self::checkSplitUpTimes($dbName);
    }

    /**
     * Check if a column exists.
     */
    private function checkColumn($column, $table, $dbName)
    {
        $sql = "SELECT *
					FROM information_schema.COLUMNS
					WHERE
						TABLE_SCHEMA = '" . $dbName . "' AND
						TABLE_NAME = '" . $table . "' AND
						COLUMN_NAME = '" . $column . "'";
        return $this->database->query($sql)->num_rows == 1;
    }

    /**
     * Check if a column allows null.
     */
    private function checkColumnAllowsNull($column, $table, $dbName)
    {
        $sql = "SELECT " .
                    "IS_NULLABLE " .
                "FROM INFORMATION_SCHEMA.COLUMNS " .
                "WHERE " .
                    "TABLE_SCHEMA='" . $dbName . "' AND " .
                    "TABLE_NAME='" . $table . "' AND " .
                    "COLUMN_NAME='" . $column . "'";
        return $this->database->query($sql)->fetch_array()['IS_NULLABLE'] == 'YES';
    }

    /**
     * Check if times in changes are split up.
     */
    private function checkSplitUpTimes($dbName)
    {
        // Add column startingDate in table changes
        if (!self::checkColumn("startingDate", self::$tableChanges, $dbName)) {
            $sql = "ALTER TABLE " . self::$tableChanges. " ADD startingDate DATE NOT NULL AFTER startBy";
            $this->database->query($sql);
        }
        // Add column startingHour in table changes
        if (!self::checkColumn("startingHour", self::$tableChanges, $dbName)) {
            $sql = "ALTER TABLE " . self::$tableChanges. " ADD startingHour VARCHAR(2) AFTER startingDate";
            $this->database->query($sql);
        }
        // Add column endingDate in table changes
        if (!self::checkColumn("endingDate", self::$tableChanges, $dbName)) {
            $sql = "ALTER TABLE " . self::$tableChanges. " ADD endingDate DATE NOT NULL AFTER endBy";
            $this->database->query($sql);
        }
        // Add column endingHour in table changes
        if (!self::checkColumn("endingHour", self::$tableChanges, $dbName)) {
            $sql = "ALTER TABLE " . self::$tableChanges. " ADD endingHour VARCHAR(2) AFTER endingDate";
            $this->database->query($sql);
        }
        // Transform times from startBy to startingDate/-Hour
        if (self::checkColumn("startBy", self::$tableChanges, $dbName)) {
            $sql = "SELECT * FROM " . self::$tableChanges;
            $query = $this->database->query($sql);
            if ($query && $query->num_rows != 0) {
                while ($column = $query->fetch_array()) {
                    if ($column['startingDate'] != '0000-00-00') {
                        break;
                    }
                    $startingDate = substr($column['startBy'], 0, 10);
                    $startingHour = substr($column['startBy'], 11, 2);
                    if ($startingHour == '00') {
                        $startingHour = '';
                    }
                    $sql = "UPDATE " . self::$tableChanges . " SET startingDate = '" . $startingDate . "', startingHour = '" . $startingHour . "' WHERE id = '" . $column['id'] . "'";
                    $this->database->query($sql);
                }
                $sql = "ALTER TABLE " . self::$tableChanges. " DROP COLUMN startBy";
                $this->database->query($sql);
            }
        }
        // Transform times from endBy to endingDate/-Hour
        if (self::checkColumn("endBy", self::$tableChanges, $dbName)) {
            $sql = "SELECT * FROM " . self::$tableChanges;
            $query = $this->database->query($sql);
            if ($query && $query->num_rows != 0) {
                while ($column = $query->fetch_array()) {
                    if ($column['endingDate'] != '0000-00-00') {
                        break;
                    }
                    $endingDate = substr($column['endBy'], 0, 10);
                    $endingHour = substr($column['endBy'], 11, 2);
                    if ($endingHour == '20') {
                        $endingHour = '';
                    }
                    $sql = "UPDATE " . self::$tableChanges . " SET endingDate = '" . $endingDate . "', endingHour = '" . $endingHour . "' WHERE id = '" . $column['id'] . "'";
                    $this->database->query($sql);
                }
                $sql = "ALTER TABLE " . self::$tableChanges. " DROP COLUMN endBy";
                $this->database->query($sql);
            }
        }
    }
}
