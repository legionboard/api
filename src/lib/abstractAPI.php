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
 *
 * Based on a tutorial found on
 * http://coreymaynard.com/blog/creating-a-restful-api-with-php/
 */
namespace LegionBoard\Lib;

abstract class API {

    /**
     * The version name of the API.
     */
    private $versionName = '0.0.0';

    private function getVersionName() {
		return $this->versionName;
	}

    protected function setVersionName($versionName) {
		$this->versionName = $versionName;
	}

    /**
     * The version code (integer) of the API.
     */
    private $versionCode = '1';

    private function getVersionCode() {
		return $this->versionCode;
	}

    protected function setVersionCode($versionCode) {
		$this->versionCode = $versionCode;
	}

    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE.
     */
    private $method = '';

    protected function getMethod() {
		return $this->method;
	}

    private function setMethod($method) {
		$this->method = $method;
	}

    /**
     * Property: endpoint
     * The Model requested in the URI. eg: /files.
     */
    private $endpoint = '';

    protected function getEndpoint() {
		return $this->endpoint;
	}

    private function setEndpoint($endpoint) {
		$this->endpoint = $endpoint;
	}

    /**
     * Property: identification
     * /<endpoint>/<identification>
     */
    private $identification = null;

    public function getID() {
		return $this->identification;
	}

    private function setID($identification) {
		$this->identification = $identification;
	}

    /**
     * Property: file
     * Stores the input of the PUT request.
     */
    private $file = Null;

    public function getFile() {
		return $this->file;
	}

    private function setFile($file) {
		$this->file = $file;
	}

    /**
     * Property: status
     * The status returned in the HTTP head.
     */
    private $status = 200;

    private function getStatus() {
		return $this->status;
	}

    public function setStatus($status) {
		$this->status = $status;
	}

	/**
	 * Property: previousHash
	 * The hash of the data the user already has.
	 */
	private $previousHash = "";

	private function getPreviousHash() {
		return $this->previousHash;
	}

	public function setPreviousHash($previousHash) {
		$this->previousHash = $previousHash;
	}

    /**
     * Allow for CORS, assemble and pre-process the data.
     */
    protected function __construct($request) {
        // Send CORS headers
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        // Output is always JSON
        header("Content-Type: application/json");
        // Send version header
        header("LegionBoard-Heart-Version-Name: " . $this->getVersionName());
        header("LegionBoard-Heart-Version-Code: " . $this->getVersionCode());

        $arguments = explode('/', rtrim($request, '/'));
        if (count($arguments) > 1) {
            $this->setID($arguments[1]);
        }
        $this->setEndpoint(array_shift($arguments));

        $this->setMethod(self::getServer('REQUEST_METHOD'));
        // Workaround where request is POST but with header PUT/DELETE
        if ($this->getMethod() == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
			switch (self::getServer('HTTP_X_HTTP_METHOD')) {
				case 'DELETE':
					$this->setMethod('DELETE');
					break;
				case 'PUT':
					$this->setMethod('PUT');
					break;
				default:
					throw new Exception("Unexpected Header");
			}
        }

        switch($this->getMethod()) {
			case 'DELETE':
			case 'POST':
				$this->request = $this->cleanInputs($_POST);
				break;
			case 'GET':
				$this->request = $this->cleanInputs($_GET);
				$this->setPreviousHash(filter_input(INPUT_GET, "dataHash"));
				break;
			case 'PUT':
				$this->request = $this->cleanInputs($_GET);
				$this->setFile(file_get_contents("php://input"));
				break;
			default:
				$this->response('Invalid Method', 405);
				break;
        }
    }

    public function processAPI() {
        if (method_exists($this, $this->endpoint)) {
            return $this->response($this->{$this->getEndpoint()}());
        }
        return $this->response(Array('error' => Array(Array('message' => 'This endpoint does not exist.'))), 404);
    }

    private function response($data, $status = null) {
        if (isset($data)) {
			$dataJson = json_encode($data);

			# Get SHA-512 hash of data
			$dataHash = hash('sha512', $dataJson);

			# If calculated hash and previous hash are equal
			if ($this->getPreviousHash() == $dataHash) {
				# Send status header "304 Not Modified"
				$status = 304;
				header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));

				return null;
			}

			# Send normal status header
			$status = isset($status) ? $status : $this->getStatus();
			header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));

			# Print out data hash in header if it's a GET request
			if ($this->getMethod() == 'GET') {
				header("LegionBoard-Heart-Data-Hash-SHA-512: " . $dataHash);
			}

			return $dataJson;
		}
		# Send normal status header
		$status = isset($status) ? $status : $this->getStatus();
		header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));

		return null;
    }

    private function cleanInputs($data) {
        $cleanInput = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $cleanInput[$k] = $this->cleanInputs($v);
            }
        } else {
            $cleanInput = trim(strip_tags($data));
        }
        return $cleanInput;
    }

    private function requestStatus($code) {
        $status = array(  
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            304 => 'Not Modified',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            409 => 'Conflict',
            500 => 'Internal Server Error',
        ); 
        return ($status[$code])?$status[$code]:$status[500]; 
    }

    /**
     * Returns value in super-global array _SERVER.
     */
    private function getServer($key) {
		return $_SERVER[$key];
	}
}
?>
