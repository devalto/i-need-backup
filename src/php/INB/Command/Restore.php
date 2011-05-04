<?php

/**
 * <example>
 * Pop the backup of $database to stack and restore it
 * need-restore $database
 * 
 * Restore the backup from the top of the stack and keep it
 * need-restore --keep $database
 * 
 * Restore the backup with a specify id (it keeps it on the stack).
 * need-restore --id="$id_of_backup" $database
 * 
 * Options :
 * --id="id" 	Set an id to the backup for future reference
 * --keep 		Put the backup to the bottom of the stack
 * </example>
 */
class INB_Command_Restore extends INB_Command_CommandAbstract {
	
	public function execute() {
		$this->validateArguments();
		
		$config = Zend_Registry::get('config');
		
		$database_name = $this->_getDatabaseName();
		$id = is_null($this->getOption('id')) ? '' : $this->getOption('id');
		
		$adapter = INB_Database_Adapter_AdapterFactory::factory($config->stack->db->type, $config->stack->db->options->toArray());
		$database = $adapter->load();
		
		$stack = $database->getStack($database_name);
		
		$save = false;	// Flag to indicate if we have to save the stack and
						// remove file from storage.
		$element = null;
		if (!is_null($this->getOption('id'))) {
			$element = $stack->at($this->getOption('id'));
		} elseif ($this->getOption('keep')) {
			$element = $stack->top();
		} else {
			$element = $stack->pop();
			$save = true;
		}
		
		if (is_null($element)) {
			throw new INB_Command_ArgumentException('No backup found on the stack with given criteria');
		}
		
		$command = $config->restore->command . " < " . $element->getFile();
		
		$shell = new INB_Shell($command, array('database_name' => $database_name));
		$shell->execute();

		if ($save) {
			unlink($element->getFile());
			$adapter->save($database);
		}
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
	
}