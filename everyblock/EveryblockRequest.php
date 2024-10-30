<?php

class EveryblockLocalInfoRequest {
	private $api_key = "";
	private $url = "https://api.everyblock.com/";
	
	function EveryblockLocalInfoRequest() {
		require_once dirname( __FILE__ ) . '/settings.php';
		$settings = new EveryblockLocalInfoSettings();
		$this->api_key = $settings->getAPIKey();
	}
	
	public function getAllMetros($type='json', $processed=true) {
		return $this->returnData("content/", NULL, $this->sanitizeDatabaseInputs($type), $processed);
	}
	
	public function getLocationTimeline($metro, $location, $schemas = array(), $type='json', $processed=true) {
		$urlVars = "";
		if(count($schemas) > 0) {
			if(count($schemas) == 1 && $schemas[0] == "events") {
				return $this->returnData("content/" . $this->sanitizeDatabaseInputs($metro) . "/locations/" . $this->sanitizeDatabaseInputs($location) . "/timeline/events/", $urlVars, $this->sanitizeDatabaseInputs($type), $processed);
			} else {
				foreach($schemas as $key => $value) {
					$urlVars .= "schema=" . $this->sanitizeDatabaseInputs($value);
					if($key <= count($schemas) - 2) {
						$urlVars .= "&";
					}
				}
			}
		}
		return $this->returnData("content/" . $this->sanitizeDatabaseInputs($metro) . "/locations/" . $this->sanitizeDatabaseInputs($location) . "/timeline/", $urlVars, $this->sanitizeDatabaseInputs($type), $processed);
	}
	
	public function getNeighborhoods($metro, $type='json', $processed=true) {
		return $this->returnData("content/" . $this->sanitizeDatabaseInputs($metro) . "/neighborhoods/", NULL, $this->sanitizeDatabaseInputs($type), $processed);
	}
	
	public function getWards($metro, $type='json', $processed=true) {
		return $this->returnData("content/" . $this->sanitizeDatabaseInputs($metro) . "/wards/", NULL, $this->sanitizeDatabaseInputs($type), $processed);
	}
	
	public function getZippres($metro, $type='json', $processed=true) {
		return $this->returnData("content/" . $this->sanitizeDatabaseInputs($metro) . "/zippres/", NULL, $this->sanitizeDatabaseInputs($type), $processed);
	}
	
	public function getCustomLocations($metro, $type='json', $processed=true) {
		return $this->returnData("content/" . $this->sanitizeDatabaseInputs($metro) . "/custom-locations/", NULL, $this->sanitizeDatabaseInputs($type), $processed);
	}
	
	public function getMetro($metro, $type='json', $processed=true) {
		return $this->returnData("content/" . $this->sanitizeDatabaseInputs($metro) . "/", NULL, $this->sanitizeDatabaseInputs($type), $processed);
	}
	
	public function getTopNews($metro, $schemas = array(), $type='json', $processed=true) {
		$urlVars = "";
		if(count($schemas) > 0) {
			if(count($schemas) == 1 && $schemas[0] == "events") {
				return $this->returnData("content/" . $this->sanitizeDatabaseInputs($metro) . "/topnews/events/", $urlVars, $this->sanitizeDatabaseInputs($type), $processed);
			} else {
				foreach($schemas as $key => $value) {
					$urlVars .= "schema=" . $this->sanitizeDatabaseInputs($value);
					if($key <= count($schemas) - 2) $urlVars .= "&";
				}
			}
		}
		return $this->returnData("content/" . $this->sanitizeDatabaseInputs($metro) . "/topnews/", $urlVars, $this->sanitizeDatabaseInputs($type), $processed);
	}
	
	public function getSchema($metro, $type='json', $processed=true) {
		return $this->returnData("content/" . $this->sanitizeDatabaseInputs($metro) . "/schemas/", NULL, $this->sanitizeDatabaseInputs($type), $processed);
	}
	
	//--------------------------------------------------------------------------------------------
	
	private function createRequest($requestPath) {
	
		$requestPath = $this->sanitizeDatabaseInputs($requestPath);
	
		$headers = array('Authorization: Token ' . $this->api_key);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $this->url . $requestPath);    // get the url contents
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$data = curl_exec($ch); // execute curl request		
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if($code === 200) {
			return $data;
		}
		
		return false;
	}
	
	private function returnData($requestPath, $urlVars = NULL, $type="json", $processed=true) {
		
		if(strcmp($type, "json") !== 0 && strcmp($type, "jsonp") !== 0 && strcmp($type, "xml") !== 0) {
			$type = "json";
		}
		
		$data = $this->createRequest($requestPath . "." . $type . (isset($urlVars) ? "?" . $urlVars : ""));
		
		if($data === false) {
			return false;
		}
		
		if($processed === false) {
			echo $data;
			return false;
		}

		switch ($type) {
			case "jsonp": 
				$data = substr($data, strpos($data, '('));
			case "json":
				return json_decode($data);
				break;
			case "xml":
				return simplexml_load_string($data);
				break;
			default:
				return false;
		}
		
		return false;
	}
	
	private function sanitizeDatabaseInputs($input) {
		if(is_string($input)) $input = strip_tags($input);
		return $input;
	}
}

if(isset($_POST['process']) && strip_tags($_POST['process']) == "false") {
	$everyblockLocalInfoRequest = new EveryblockLocalInfoRequest();
	
	$type = "json";
	if(isset($_POST['type'])) $type = strip_tags($_POST['type']);
	
	if(isset($_POST['schemas'])) {
		$schemas = explode(",", $_POST['schemas']);
		
		foreach ($schemas as $key => $value) {
			$schemas[$key] = strip_tags($value);
		}
	}
	
	switch($_POST['call']) { 
		case "getAllMetros":
			$everyblockLocalInfoRequest->getAllMetros($type, false);
			break;
		case "getLocationTimeline":
			$everyblockLocalInfoRequest->getLocationTimeline($_POST['metro'], $_POST['location'], $schemas, $type, false);
			break;
		case "getNeighborhoods":
			$everyblockLocalInfoRequest->getNeighborhoods($_POST['metro'], $type, false);
			break;
		case "getWards":
			$everyblockLocalInfoRequest->getWards($_POST['metro'], $type, false);
			break;
		case "getZippres":
			$everyblockLocalInfoRequest->getZippres($_POST['metro'], $type, false);
			break;
		case "getCustomLocations":
			$everyblockLocalInfoRequest->getCustomLocations($_POST['metro'], $type, false);
			break;
		case "getMetro":
			$everyblockLocalInfoRequest->getMetro($_POST['metro'], $type, false);
			break;
		case "getTopNews":
			$everyblockLocalInfoRequest->getTopNews($_POST['metro'], $schemas, $type, false);
			break;
		case "getSchema":
			$everyblockLocalInfoRequest->getSchema($_POST['metro'], $type, false);
			break;
	}
}
?>