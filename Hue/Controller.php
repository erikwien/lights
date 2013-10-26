<?php
namespace Lights\Hue;

class Controller {
	private $api;

	public function __construct($url, $hash, $applicationName) {
		$this->api = new Api($url, $hash, $applicationName);
	}

	public function getLights() {
		$response = $this->api->sendRequest('/lights');
		$lights = [];

		foreach($response as $index => $light) {
			$lights[] = new Light(
				$this->api,
				$index,
				$light['name']
			);
		}

		return $lights;
	}
}
