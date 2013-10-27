<?php

namespace Lights;

class BadConfigException extends \Exception {
}

class Config {
	public $hue;
	public $states = array();
	public $profiles = array();
	public $layers = array();

	public function getProfile($name) {
		if(!array_key_exists($name, $this->profiles)) {
			throw new BadConfigException('No profile '.$name.' defined in config file.');
		}

		return $this->profiles[$name];
	}

	public function getState($name) {
		if(!array_key_exists($name, $this->states)) {
			throw new BadConfigException('No state '.$name.' defined in config file.');
		}

		return $this->states[$name];
	}

	static private function stateFromDescription($config, $description) {
		$state = null;

		if(is_string($description)) {
			$state = $config->getState($description);
		} else if(is_array($description)) {
			$state = new \Lights\LightState(
				array_key_exists('brightness', $description) ? $description['brightness'] : null,
				array_key_exists('x', $description) ? $description['x'] : null,
				array_key_exists('y', $description) ? $description['y'] : null
			);
		} else {
			throw new BadConfigException('Unable to read light state from description '.var_export($description, true).'.');
		}

		return $state;
	}

	static public function fromFile($filename) {
		$config = new Config();
		$file = json_decode(file_get_contents($filename), true);

		if(!is_array($file)) {
			throw new BadConfigException('Unable to parse config file.');
		}

		if(!array_key_exists('hue', $file) || !is_array($file['hue'])) {		
			throw new BadConfigException('No Hue config found in config file.');
		}

		$config->hue = $file['hue'];

		if(array_key_exists('states', $file) && is_array($file['states'])) {
			foreach($file['states'] as $name => $state) {
				$config->states[$name] = self::stateFromDescription($config, $state);
			}
		}

		if(array_key_exists('profiles', $file) && is_array($file['profiles'])) {
			foreach($file['profiles'] as $name => $entries) {
				$config->profiles[$name] = array();

				foreach($entries as $time => $state) {
					$config->profiles[$name][$time] = self::stateFromDescription($config, $state);
				}
			}
		}

		if(array_key_exists('layers', $file) && is_array($file['layers'])) {
			foreach($file['layers'] as $index => $layer) {
				$overrides = array();
				if(array_key_exists('overrides', $layer) && is_array($layer['overrides'])) {
					foreach($layer['overrides'] as $lightId => $state) {
						$overrides[$lightId] = self::stateFromDescription($config, $state);
					}
				}

				$config->layers[$index] = new Layer(
					(string)$layer['name'],
					(bool)$layer['active'],
					self::stateFromDescription($config, $layer['state']),
					$overrides
				);
			}
		}

		return $config;
	}
}
