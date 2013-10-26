<?php
namespace Lights\Hue;

class Light {
	private $api;
	private $index;

	public $name;

	public function __construct(Api $api, $index, $name) {
		$this->api = $api;
		$this->index = $index;
		$this->name = $name;
	}

	public function getState() {
		$response = $this->api->sendRequest('/lights/'.$this->index);
		$state = $response['state'];

		// TODO: Check light mode to know which color state to look at

		return new LightState(
			$state['bri'],
			$state['x'],
			$state['y']
		);
	}

	public function setState(LightState $state) {
		$this->api->sendRequest(
			'/lights/'.$this->index.'/state',
			HTTP_METH_PUT,
			json_encode([
				'on' => ((int)$state->brightness) !== 0,
				'bri' => (int)$state->brightness,
				'xy' => array(
					(float)$state->x,
					(float)$state->y
				),
				'alert' => 'none'
			])
		);
	}
}