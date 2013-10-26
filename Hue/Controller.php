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

	public function getLightById($id) {
		$response = $this->api->sendRequest('/lights/'.$id);

		$light = new Light(
			$this->api,
			array(
				'id' =>	$id,
				'name' => $response['name'],
				'type' => $response['type'],
				'modelid' => $response['modelid'],
				'swversion' => $response['swversion'],
			)
		);

		return $light;
	}

	public function getLights() {
		$response = $this->api->sendRequest('/lights');
		$lights = [];

		foreach($response as $id => $light) {
			$lights[] = $this->getLightById($id);
		}

		return $lights;
	}
}
