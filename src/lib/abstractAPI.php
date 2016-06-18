<?php
/*
 * @author	Nico Alt
 * @date	16.03.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 *
 * Based on a tutorial found on
 * http://coreymaynard.com/blog/creating-a-restful-api-with-php/
 *
 */
abstract class API {
    /**
     * The version name of the API.
     */
    protected $versionName = '0.1.2';
    /**
     * The version code (integer) of the API.
     */
    protected $versionCode = '1';
    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     */
    protected $method = '';
    /**
     * Property: endpoint
     * The Model requested in the URI. eg: /files
     */
    protected $endpoint = '';
    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    protected $args = Array();
    /**
     * Property: file
     * Stores the input of the PUT request
     */
     protected $file = Null;
    /**
     * Property: status
     * The status returned in the HTTP head.
     */
    protected $status = 200;

    /**
     * Constructor: __construct
     * Allow for CORS, assemble and pre-process the data
     */
    public function __construct($request) {
        // Send CORS headers
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        // Output is always JSON
        header("Content-Type: application/json");
        // Send version header
        header("LegionBoard-Heart-Version-Name: " . $this->versionName);
        header("LegionBoard-Heart-Version-Code: " . $this->versionCode);

        $this->args = explode('/', rtrim($request, '/'));
        $this->endpoint = array_shift($this->args);

        $this->method = self::getServer('REQUEST_METHOD');
        // Workaround where request is POST but with header PUT/DELETE
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
			switch (self::getServer('HTTP_X_HTTP_METHOD')) {
				case 'DELETE':
					$this->method = 'DELETE';
					break;
				case 'PUT':
					$this->method = 'PUT';
					break;
				default:
					throw new Exception("Unexpected Header");
			}
        }

        switch($this->method) {
			case 'DELETE':
			case 'POST':
				$this->request = $this->cleanInputs($_POST);
				break;
			case 'GET':
				$this->request = $this->cleanInputs($_GET);
				break;
			case 'PUT':
				$this->request = $this->cleanInputs($_GET);
				$this->file = file_get_contents("php://input");
				break;
			default:
				$this->response('Invalid Method', 405);
				break;
        }
    }

    public function processAPI() {
        if (method_exists($this, $this->endpoint)) {
            return $this->response($this->{$this->endpoint}($this->args));
        }
        return $this->response(Array('error' => Array(Array('message' => 'This endpoint does not exist.'))), 404);
    }

    private function response($data, $status = null) {
        $status = isset($status) ? $status : $this->status;
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        if (isset($data)) {
			return json_encode($data);
		}
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
