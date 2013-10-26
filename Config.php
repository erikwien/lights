<?php

namespace Lights;

class Config {
	public $hue;
	public $states = array();
	public $profiles = array();


	static public function fromFile($filename) {
		$config = new Config();
		$file = json_decode(file_get_contents($filename), true);

		$config->hue = $file['hue'];

		foreach($file['states'] as $name => $state) {
			$config->states[$name] = new \Lights\Hue\LightState(
				$state['brightness'],
				$state['x'],
				$state['y']
			);
		}

		foreach($file['profiles'] as $name => $entries) {
			$config->profiles[$name] = array();

			foreach($entries as $time => $state) {
				$config->profiles[$name][$time] = $config->states[$state];
			}
		}

		return $config;
	}
}
