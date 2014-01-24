<?php
namespace Lights\Hue;

use \Lights\Util\DataObject;
use \Lights\LightState;

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
		parent::__construct($data, true);
		$this->api = $api;
	}

	public function setName($name) {
		$response = $this->api->sendRequest(
			'/lights/'.$this->id,
			HTTP_METH_PUT,
			json_encode([
				'name' => $name
			])
		);

		// TODO: Check response...

		$this->setRawProperty('name', $name);
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
		$data = array();

		if(!is_null($state->brightness)) {
			$data['on'] = ((int)$state->brightness) !== 0;
			$data['bri'] = (int)$state->brightness;
		}

		if(!is_null($state->x) && !is_null($state->y)) {
			$data['xy'] = array(
				(float)$state->x,
				(float)$state->y
			);
		}


		if(count($data)) {
			$response = $this->api->sendRequest(
				'/lights/'.$this->id.'/state',
				HTTP_METH_PUT,
				json_encode($data)
			);
		}
	}
}