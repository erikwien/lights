<?php
namespace Lights\Hue;

class HttpMethod {
	const GET = 0;
	const PUT = 1;
}

class Api {
	public $hash;
	public $baseUrl;
	public $appName;

	public function __construct($url, $hash, $applicationName) {
		$this->hash = $hash;
		$this->baseUrl = $url.'/api/'.$hash;
		$this->appName = $applicationName;
	}

	public function sendRequest($url, $method = HttpMethod::GET, $data = null) {
		$request = curl_init($this->baseUrl.$url);

		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

		switch($method) {
			case HttpMethod::GET:
				break;

			case HttpMethod::PUT:
				$json = json_encode($data);
				curl_setopt($request, CURLOPT_HTTPHEADER, [
					'Content-Type: application/json',
					'Content-Length: ' . strlen($json)
				]);
				curl_setopt($request, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($request, CURLOPT_POSTFIELDS, $json);
				break;

			default:
				throw new \Exception('Unsupported method '.var_export($method, true).'.');
		}

		$result = curl_exec($request);
		curl_close($request);

		$response = json_decode($result, true);

		if(is_null($response)) {
			throw new \Exception('Got unparsable JSON back from API.');
		}

		if((count($response) === 1) && array_key_exists('error', $response[0])) {
			$error = $response[0]['error'];

			throw new ApiException(
				$error['type'],
				$error['address'],
				$error['description']
			);
		}

		return $response;
	}
}
