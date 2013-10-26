<?php
namespace Lights\Hue;

class ApiException extends \Exception {
	private $type;
	private $address;
	private $description;

	public function __construct($type, $address, $description) {
		$this->type = $type;
		$this->address = $address;
		$this->description = $description;

		parent::__construct('Hue API error type '.$this->type.' at '.$this->address.'. "'.$this->description.'"');
	}
}
