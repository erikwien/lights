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

	public function apply($lightId, LightState $source) {
		if(!$this->active) {
			return $source;
		}

		$state = $source->lerp($this->state, 1);

		if(array_key_exists($lightId, $this->overrides)) {
			// TODO: Or should be lerp source with the override here?
			$state = $state->lerp($this->overrides[$lightId], 1);
		}

		return $state;
	}
}
