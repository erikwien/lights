<?php
namespace Lights\Hue;

use \Lights\Util\DataObject;

class Light extends DataObject {
	private $api;

	static $properties = [
		'id' => null,
		'state' => null,
		'type' => null,
		'name' => null,
		'modelid' => null,
		'swversion' => null
	];

	public function __construct(Api $api, $data) {
		parent::__construct($data);
		$this->api = $api;
	}

	public function getState() {
		$response = $this->api->sendRequest('/lights/'.$this->id);
		$state = $response['state'];

		return new LightState(
			$state['bri'],
			$state['xy'][0],
			$state['xy'][1]
		);
	}

	public function setState(LightState $state) {
		$this->api->sendRequest(
			'/lights/'.$this->id.'/state',
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