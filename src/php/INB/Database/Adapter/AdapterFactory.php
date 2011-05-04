<?php

class INB_Database_Adapter_AdapterFactory {

	/**
	 *
	 * @param string $type
	 * @param array $options
	 * @return INB_Database_Adapter_AdapterAbstract
	 */
	public static function factory($type, array $options) {
		$class = "INB_Database_Adapter_" . ucfirst($type);
		return new $class($options);
	}
	
}