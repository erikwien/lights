<?php
namespace Lights\Hue;

class LightState {
	public $brightness;
	public $x;
	public $y;

	public function __construct($brightness, $x, $y) {
		$this->brightness = $brightness;
		$this->x = $x;
		$this->y = $y;
	}
}
