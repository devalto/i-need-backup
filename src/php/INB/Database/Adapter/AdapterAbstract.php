<?php

abstract class INB_Database_Adapter_AdapterAbstract {
	
	private $_options = array();
	
	public function __construct(array $options = array()) {
		$this->_options = $options;
	}
	
	public function getOption($key) {
		if (!isset($this->_options[$key])) {
			return null;
		}
		
		return $this->_options[$key];
	}
	
	/**
	 * @return INB_Database_Database
	 */
	abstract public function load();
	
	abstract public function save(INB_Database_Database $db);
	
}