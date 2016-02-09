<?php

namespace App\Models;

abstract class AbstractModel {
	
	/**
	 * ID of user
	 *
	 * @var int
	 */
    public $id;	
	
	/**
	 * Constructor
	 * 
	 * @param array $data		Data to load
	 */
	public function __construct(array $data = []) {
		foreach ($data as $key => $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}
	}	
	
	/**
	 * validates model data
	 * 
	 * @return array
	 */
	public abstract function validate();
	
	/**
	 * Binds all data to PDO statement
	 */
	public abstract function bind(\PDOStatement &$statement);
	
	/**
	 * Gets table name
	 * 
	 * @staticvar array $names
	 * 
	 * @return string
	 */
	public static function getTable() {
		static $names = [];		
		$class = get_called_class();		
		if (!isset($names[$class])) {
			$parts = explode('\\', $class);
			$names[$class] = strtolower(end($parts));
		}
		return $names[$class];
	}
	
}