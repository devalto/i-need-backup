<?php

class INB_ConfigManager {
	
	/**
	 * @var Zend_Config
	 */
	private $_config;
	
	public function __construct($config_file_name = null, array $override_config = array()) {
		$initial_config = $this->_getDefaultConfig();
		
		if (is_null($config_file_name)) {
			$config_file_name = $this->_getHomeDirectory() . '.need.ini';
		}
		
		if (file_exists($config_file_name)) {
			$config = new Zend_Config_Ini($config_file_name);
			$config_array = $config->toArray();
			
			$initial_config = $this->_mergeConfig($initial_config, $config_array);
		}
		
		$initial_config = $this->_mergeConfig($initial_config, $override_config);

		$this->_config = new Zend_Config($initial_config);
	}
	
	/**
	 * @return Zend_Config
	 */
	public function getConfig() {
		return $this->_config;
	}
	
	private function _getDefaultConfig() {
		$config = array(
			'stack' => array(
				'db' => array(
					'type' => 'json',
					'options' => array(
						'uri' => 'file://' . $this->_getHomeDirectory() . '.need.json'
					)
				),
				'storage' => array(
					'directory' => $this->_getHomeDirectory() . '.need-storage'
				)
			),
			'backup' => array(
				'command' => 'mysqldump -u root {{database_name}}'
			),
			'restore' => array(
				'command' => 'mysqladmin -u root --force drop {{database_name}} && mysqladmin -u root create {{database_name}} && mysql -u root {{database_name}}'
			)
		);
		
		return $config;
	}
	
	private function _getHomeDirectory() {
		$home_directory = false;
		$home_var = false;
		
		$check_vars = array('NEED_HOME', 'HOME');
		
		foreach ($check_vars as $var) {
			if (getenv($var)) {
				$home_var = $var;
				$home_directory = getenv($var);
				break;
			}
		}
		
		if (!$home_directory) {
			throw new RuntimeException("Can't find HOME directory");
		}
		
		if (!is_dir($home_directory)) {
			throw new RuntimeException("The $home_var environment variable is not a directory");
		}
		
		if ($home_directory[strlen($home_directory) - 1] != '/') {
			$home_directory .= '/';
		}
		
		return $home_directory;
	}
	
	/**
	 * Merge les configurations
	 * 
	 * @see http://www.php.net/manual/en/function.array-merge-recursive.php#93905
	 * @return array
	 */
	private function _mergeConfig() {
		// Holds all the arrays passed
		$params = &func_get_args();

		// First array is used as the base, everything else overwrites on it
		$return = array_shift($params);

		// Merge all arrays on the first array
		foreach ($params as $array) {
			foreach ($array as $key => $value) {
				// Numeric keyed values are added (unless already there)
				if (is_numeric($key) && (!in_array($value, $return))) {
					if (is_array($value)) {
						$return[] = $this->_mergeConfig($return [$$key], $value);
					} else {
						$return[] = $value;
					}

				// String keyed values are replaced
				} else {
					if (isset($return[$key]) && is_array($value) && is_array($return[$key])) {
						$return[$key] = $this->_mergeConfig($return [$$key], $value);
					} else {
						$return[$key] = $value;
					}
				}
			}
		}

		return $return;
	}
	
}