<?php

class INB_Database_Database {
	
	private $_stacks_by_name = array();
	
	/**
	 * @param string $name
	 * @return INB_Database_Stack
	 */
	public function getStack($name = "default") {
		if (!isset($this->_stacks_by_name[$name])) {
			$this->_stacks_by_name[$name] = new INB_Database_Stack();
		}
		
		return $this->_stacks_by_name[$name];
	}
	
	public function getStackNames() {
		return array_keys($this->_stacks_by_name);
	}
	
	public function getStacks() {
		return $this->_stacks_by_name;
	}
	
	public function toArray() {
		$ret = array();
		
		foreach($this->_stacks_by_name as $name => $stack) {
			$ret[$name] = $stack->toArray();
		}
		
		return $ret;
	}
	
	public static function createFromArray(array $array) {
		$database = new INB_Database_Database();
		foreach ($array as $name => $elements) {
			$stack = $database->getStack($name);
			foreach ($elements as $element) {
				$stack->push(INB_Database_Element::createFromArray($element));
			}
		}
		
		return $database;
	}
	
}