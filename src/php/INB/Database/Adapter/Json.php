<?php

class INB_Database_Adapter_Json extends INB_Database_Adapter_AdapterAbstract {
	
	public function __construct(array $options = array()) {
		parent::__construct($options);
		
		if (is_null($this->getUri())) {
			throw new InvalidArgumentException("You must specify a filename that contains the JSON string");
		}
	}
	
	public function load() {
		if (!file_exists($this->getUri())) {
			return new INB_Database_Database();
		}
		
		$content = file_get_contents($this->getUri());
		$json = json_decode($content, true);
		
		return INB_Database_Database::createFromArray($json);
	}
	
	public function save(INB_Database_Database $database) {
		file_put_contents($this->getUri(), json_encode($database->toArray()));
	}
	
	public function getUri() {
		return $this->getOption('uri');
	}
	
}