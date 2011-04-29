<?php

/**
 * <example>
 * Push a backup of $database to stack
 * need-backup $database
 * 
 * Add a backup of $database at the bottom of the stack
 * need-backup --bottom $database
 * 
 * Push a backup of $database to the stack with an id for future reference
 * need-backup --id="$id_of_backup" $database
 * 
 * Options :
 * --comment="something"	Add a comment to the backup
 * --id="id"				Set an id to the backup for future reference
 * --bottom					Put the backup to the bottom of the stack
 * </example>
 */
class INB_Command_Backup extends INB_Command_CommandAbstract {
	
	public function execute() {
		$this->validateArguments();
		
		$config = Zend_Registry::get('config');
		$storage_dir = $config->stack->storage->directory;
		if (!is_dir($storage_dir)) {
			throw new INB_Command_ConfigurationException("The storage directory is invalid.");
		}
		
		if (!is_writable($storage_dir)) {
			throw new INB_Command_ConfigurationException("The storage directory is not writable.");
		}
		
		$database_name = $this->_getDatabaseName();
		$date = date(DATE_ATOM);
		$id = is_null($this->getOption('id')) ? '' : $this->getOption('id');
		
		$out_file = $storage_dir . "/" . $this->_createFilename($database_name, $date, $id);

		if (file_exists($out_file)) {
			throw new INB_Command_StorageException("The file '$out_file' already exists.");
		}
		
		$adapter = INB_Database_Adapter_AdapterFactory::factory($config->stack->db->type, $config->stack->db->options->toArray());
		$database = $adapter->load();
		
		$command = $config->backup->command . " > " . $out_file;
		
		$shell = new INB_Shell($command, array('database_name' => $database_name));
		$shell->execute();
		
		if (empty($id)) {
			$id = time();
		}
		
		$element = new INB_Database_Element($id, $out_file, $date);
		if (!is_null($this->getOption('comment'))) {
			$element->setComment($this->getOption('comment'));
		}
		
		$stack = $database->getStack($database_name);
		if (!$this->getOption('bottom')) {
			$stack->push($element);
		} else {
			$stack->unshift($element);
		}
		
		$adapter->save($database);
	}
	
	public function validateArguments() {
		$arguments = $this->getArguments();
		
		if (!isset($arguments[0])) {
			throw new INB_Command_ArgumentException('The first argument should be the database name.');
		}
		
		return true;
	}
	
	private function _getDatabaseName() {
		$arguments = $this->getArguments();
		
		return $arguments[0];
	}
	
	private function _createFilename($database, $date, $id = null) {
		$pattern = "%s_%s%s.sql";
		
		$print_id = "";
		if (!is_null($id) && $id != "") {
			$print_id = "_$id";
		}
		
		return sprintf($pattern, $database, $date, $print_id);
	}
	
}