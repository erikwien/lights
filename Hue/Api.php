<?php
namespace Lights\Hue;

class Api {
	public $hash;
	public $baseUrl;
	public $appName;

	public function __construct($url, $hash, $applicationName) {
		$this->hash = $hash;
		$this->baseUrl = $url.'/api/'.$hash;
		$this->appName = $applicationName;
	}

	public function sendRequest($url, $method = HTTP_METH_GET, $data = null) {
		$request = new \HttpRequest(
			$this->baseUrl.$url,
			$method
		);

		if($data) {
			$request->setPutData($data);
			$request->setContentType('application/json');
		}

		$message = $request->send();
		$response = json_decode(utf8_encode(utf8_decode($message->getBody())), true);

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
