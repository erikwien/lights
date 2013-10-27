<?php namespace Lights;

class Layer extends Util\DataObject {
	protected static $properties = array(
		'name' => '',
		'active' => false,
		'state' => null,
		'overrides' => array()
	);

	public function __construct($name, $active, LightState $state, $overrides) {
		parent::__construct(array(
			'name' => $name,
			'active' => $active,
			'state' => $state,
			'overrides' =>  $overrides
		));
	}

	private function blendChannel($source, $destination) {
		if(is_null($source)) {
			return $destination;
		}

		if(is_null($destination)) {
			return $source;
		}

		return $destination;
	}

	private function blend(LightState $source, LightState $destination) {
		return new LightState(
			$this->blendChannel($source->brightness, $destination->brightness),
			$this->blendChannel($source->x, $destination->x),
			$this->blendChannel($source->y, $destination->y)
		);
	}

	public function apply($lightId, LightState $source) {
		if(!$this->active) {
			return $source;
		}

		$state = $this->blend($source, $this->state);

		if(array_key_exists($lightId, $this->overrides)) {
			// TODO: Or should be blend source with the override here?
			$state = $this->blend($state, $this->overrides[$lightId]);
		}

		return $state;
	}
}
