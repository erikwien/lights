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
		$data = [];

		if(!is_null($state->brightness)) {
			$data['on'] = ((int)$state->brightness) !== 0;
			$data['bri'] = (int)$state->brightness;
		}

		if(!is_null($state->x) && !is_null($state->y)) {
			$data['xy'] = [
				(float)$state->x,
				(float)$state->y
			];
		}

		if(count($data)) {
			$this->api->sendRequest(
				'/lights/'.$this->id.'/state',
				HTTP_METH_PUT,
				json_encode($data)
			);
		}
	}
}