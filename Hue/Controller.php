<?php
namespace Lights\Hue;

class Controller {
	private $api;

	public function __construct($config) {
		$this->api = new Api(
			$config->hue['url'],
			$config->hue['hash'],
			$config->hue['applicationName']
		);
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
