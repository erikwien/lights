<?php

namespace Lights;

class UnknownConfigException extends \Exception {
}

class Config {
	public $hue;
	public $states = array();
	public $profiles = array();
	public $layers = array();

	public function getProfile($name) {
		if(!array_key_exists($name, $this->profiles)) {
			throw new UnknownConfigException('No profile '.$name.' defined in config file.');
		}

		return $this->profiles[$name];
	}

	public function getState($name) {
		if(!array_key_exists($name, $this->states)) {
			throw new UnknownConfigException('No state '.$name.' defined in config file.');
		}

		return $this->states[$name];
	}

	static public function fromFile($filename) {
		$config = new Config();
		$file = json_decode(file_get_contents($filename), true);

		$config->hue = $file['hue'];

		if(array_key_exists('states', $file) && is_array($file['states'])) {
			foreach($file['states'] as $name => $state) {
				$config->states[$name] = new \Lights\LightState(
					$state['brightness'],
					$state['x'],
					$state['y']
				);
			}
		}

		if(array_key_exists('profiles', $file) && is_array($file['profiles'])) {
			foreach($file['profiles'] as $name => $entries) {
				$config->profiles[$name] = array();

				foreach($entries as $time => $state) {
					$config->profiles[$name][$time] = $config->getState($state);
				}
			}
		}

		if(array_key_exists('layers', $file) && is_array($file['layers'])) {
			foreach($file['layers'] as $index => $layer) {
				$overrides = array();
				if(array_key_exists('overrides', $layer) && is_array($layer['overrides'])) {
					foreach($layer['overrides'] as $lightId => $state) {
						$overrides[$lightId] = $config->getState($state);
					}
				}

				$config->layers[$index] = new Layer(
					(string)$layer['name'],
					(bool)$layer['active'],
					$config->getState($layer['state']),
					$overrides
				);
			}
		}

		return $config;
	}
}
