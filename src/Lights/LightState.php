<?php
namespace Lights;

class LightState {
	public $brightness;
	public $x;
	public $y;

	public function __construct($brightness = null, $x = null, $y = null) {
		$this->brightness = $brightness;
		$this->x = $x;
		$this->y = $y;
	}

	public function lerp(LightState $destination, $alpha) {
		return new LightState(
			$this->lerpChannel($this->brightness, $destination->brightness, $alpha),
			$this->lerpChannel($this->x, $destination->x, $alpha),
			$this->lerpChannel($this->y, $destination->y, $alpha)
		);
	}

// Implementation details:
	private function lerpChannel($source, $destination, $alpha) {
		if(is_null($source)) {
			return $destination;
		}

		if(is_null($destination)) {
			return $source;
		}

		return $source + (($destination - $source) * $alpha);
	}
}
