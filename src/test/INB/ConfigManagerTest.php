<?php

require_once dirname(dirname(dirname(__FILE__))) . '/php/INB/ConfigManager.php';

class INB_ConfigManagerTest extends PHPUnit_Framework_TestCase {
	
	public function testMergeConfiguration() {
		$manager = new INB_ConfigManager('config.ini');
		
		$this->assertTrue(file_exists('config.ini'));
		
		$config = $manager->getConfig();
		
		$this->assertEquals($config->backup->command, "mysqldump -u root --password='temp' {{database_name}}");
	}
	
}