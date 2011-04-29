<?php

class INB_Shell {
	
	private $_command;
	
	/**
	 * @var array
	 */
	private $_options = array();

	public function __construct($command, $options) {
		$this->_command = $command;
		$this->_options = $options;
	}
	
	public function getCommand() {
		$command = $this->_command;
		foreach($this->_options as $k => $v) {
			$command = str_replace("{{" . $k . "}}", $v, $command);
		}
		
		return $command;
	}
	
	public function execute() {
		$output = array();
		$return = 0;
		exec($this->getCommand(), $output, $return);
		
		if ($return > 0) {
			$message = "An error as occured during the execution of a shell command \"{$this->getCommand()}\" :\n" . join("\n", $output);
			throw new Exception($message);
		}
		
		return $output;
	}
	
}