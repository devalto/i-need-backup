<?php

class INB_Database_Stack {
	
	private $_concrete_stack = array();
	
	private $_ids = array();
	
	public function pop() {
		$element = array_pop($this->_concrete_stack);
		unset($this->_ids[array_search($element->getId(), $this->_ids)]);
		
		return $element;
	}
	
	public function push(INB_Database_Element $element) {
		if (in_array($element->getId(), $this->_ids)) {
			throw new OutOfBoundsException("The element '{$element->getId()}' already exist in the stack.");
		}
		
		array_push($this->_concrete_stack, $element);
		
		$this->_ids[] = $element->getId();
		
		return $this;
	}
	
	public function unshift(INB_Database_Element $element) {
		if (in_array($element->getId(), $this->_ids)) {
			throw new OutOfBoundsException("The element '{$element->getId()}' already exist in the stack.");
		}
		
		array_unshift($this->_concrete_stack, $element);
		
		$this->_ids[] = $element->getId();
		
		return $this;
	}
	
	public function top() {
		$top_index = count($this->_concrete_stack) - 1;
		if ($top_index > 0) {
			return $this->_concrete_stack[$top_index];
		}
		
		return null;
	}
	
	public function at($id) {
		foreach ($this->_concrete_stack as $element) {
			if ($id == $element->getId()) {
				return $element;
			}
		}
		
		return null;
	}
	
	public function getList() {
		return $this->_concrete_stack;
	}
	
	public function toArray() {
		$ret = array();
		
		foreach($this->_concrete_stack as $stack) {
			$ret[] = $stack->toArray();
		}
		
		return $ret;
	}
	
}