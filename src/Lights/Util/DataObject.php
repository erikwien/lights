<?php

namespace Lights\Util;

class DataObject {
	public function __construct($propertyValues = null, $setRawProperties = false) {
		// Set default values for all properties:
		foreach($this->getDefaultValues() as $property => $defaultValue) {
			$this->values[$property] = $defaultValue;
		}

		// Assign any incoming property values:
		if($propertyValues && !$setRawProperties) {
			$this->setProperties($propertyValues);
		} else if($propertyValues) {
			$this->setRawProperties($propertyValues);
		}
	}

	/**
	 * Fetch the default values of all properties as an array.
	 *
	 * @return array Associative array of property values on the form:
	 *
	 *         array(
	 *             '<property name>' => '<property value>'
	 *         )
	 */
	public function getDefaultValues() {
		return self::mergeStaticArrayHierarchy(get_class($this), 'properties');
	}

	/**
	 * Set property values from an associative array.
	 *
	 * @param array $values Associative array of property values on the form:
	 *
	 *        array(
	 *            '<property name>' => '<property value>'
	 *        )
	 */
	public function setProperties($values) {
		foreach($this->getPropertyNames() as $property) {
			if(array_key_exists($property, $values)) {
				$this->$property = $values[$property];
			}
		}
	}

	/**
	 * Fetch properties as associative array.
	 *
	 * @return array Properties on the form:
	 *
	 *         array(
	 *             '<property name>' => '<property value>'
	 *         )
	 */
	public function getProperties() {
		$properties = array();

		// Note that we're building a new array instead of returning the
		// $this->values array directly. This is to make sure properties go
		// through any overridden getters that the derived class may have
		// defined.
		foreach($this->getPropertyNames() as $property) {
			$properties[$property] = $this->$property;
		}

		return $properties;
	}

	/**
	 * Fetch all valid property names as an array.
	 *
	 * @return array Array with the names of all properties in the object.
	 */
	public function getPropertyNames() {
		return array_keys(self::mergeStaticArrayHierarchy(get_class($this), 'properties'));
	}

	/**
	 * Fetch the value of a named property.
	 *
	 * If the object has a getter function defined for the property, that
	 * function is called and its returnvalue returned. Otherwise the raw
	 * property value is retuned.
	 *
	 * @param string $name The name of the property to fetch.
	 * @return mixed The value of the named property.
	 */
	public function __get($name) {
		// If the object has a method on the form "getPropertyName", call that
		// instead of getting the property directly:
		$getterMethod = 'get'.ucfirst($name);
		if(method_exists($this, $getterMethod)) {
			return $this->$getterMethod();
		}

		return $this->getRawProperty($name);
	}

	/**
	 * Returns true if the object has a names property with the name passed in.
	 *
	 * @param string $name The name of the property to check.
	 * @return boolean True if the property exists.
	 */
	public function __isset($name) {
		return in_array($name, $this->getPropertyNames());
	}

	/**
	 * Set the value of a named property.
	 *
	 * @param string $name Name of the the property to set.
	 * @param mixed $value The value to set the property to.
	 * @return mixed Return value of the defined setter for the propery (if any).
	 */
	public function __set($name, $value) {
		// If the object has a method on the form "setPropertyName", call that
		// instead of setting the value directly:
		$setterMethod = 'set'.ucfirst($name);
		if(method_exists($this, $setterMethod)) {
			return $this->$setterMethod($value);
		}

		return $this->setRawProperty($name, $value);
	}

// Protected interface:
	protected function getRawProperty($name) {
		if(!in_array($name, $this->getPropertyNames())) {
			throw new \Exception('No such property.');
		}

		return $this->values[$name];
	}

	protected function setRawProperty($name, $value) {
		if(!in_array($name, $this->getPropertyNames())) {
			throw new \Exception('No such property.');
		}

		return $this->values[$name] = $value;
	}

	protected function getRawProperties() {
		$properties = array();

		foreach($this->getPropertyNames() as $property) {
			$properties[$property] = $this->getRawProperty($property);
		}

		return $properties;
	}

	protected function setRawProperties($values) {
		foreach($this->getPropertyNames() as $property) {
			if(array_key_exists($property, $values)) {
				$this->setRawProperty($property, $values[$property]);
			}
		}
	}

// Private interface:
	private static $definedProperties = array();

	private static function mergeStaticArrayHierarchy($class, $name) {
		if(!array_key_exists($class, self::$definedProperties)) {
			self::$definedProperties[$class] = array();

			if(!array_key_exists($name, self::$definedProperties)) {
				$result = array();
				$classIter = $class;

				do {
					if(isset($classIter::$$name) && is_array($classIter::$$name)) {
						$result = array_merge($result, $classIter::$$name);
					}
				}
				while($classIter = get_parent_class($classIter));

				self::$definedProperties[$class][$name] = $result;
			}
		}

		return self::$definedProperties[$class][$name];
	}

// Properties:
	private $values = array();
}