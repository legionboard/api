<?php

namespace LegionBoard\tests;

class Utilities {

	public static function getMockFile() {
		$file = 'k=1234567890abcdef&teacher=123';
		return $file;
	}

	public static function getMockRequest() {
		$request = '/changes';
		return $request;
	}
}

// TODO: Mock this: http://docs.atoum.org/en/latest/mocking_systems.html 

namespace LegionBoard\Lib;

class mysqli {

	public function __construct($mysqlHost, $mysqlUser, $mysqlPW, $mysqlDB) {
		return true;
	}

	public function query() {
		return new mysqli_result();
	}
}

class mysqli_result {

	public $num_rows = 0;

	public function fetch_array() {
		return true;
	}
}
