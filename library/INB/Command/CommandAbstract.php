<?php

abstract class INB_Command_CommandAbstract {
	
	private $_arguments = array();
	
	private $_options;
	
	public function __construct() {
		$this->_options = new stdClass();
	}
	
	public function setArguments(array $args) {
		$this->_arguments = $args;
		
		return $this;
	}
	
	public function getArguments() {
		return $this->_arguments;
	}
	
	public function setOptions($options) {
		$this->_options = $options;
		
		return $this;
	}
	
	public function getOptions() {
		return $this->_options;
	}
	
	public function getOption($name) {
		if (!isset($this->_options->{$name})) {
			return null;
		}
		
		return $this->_options->{$name};
	}
	
}