<?php

namespace App\Models;

class User extends AbstractModel {	

	/**
	 * Email
	 *
	 * @var string
	 */
    public $email;
	
	/**
	 * Name
	 *
	 * @var string
	 */
    public $name;
	
	/**
	 * Password
	 *
	 * @var string
	 */
    public $password;
	
	/**
	 * When was created
	 *
	 * @var string
	 */
	public $created_at;
	
	/**
	 * validates model data
	 * 
	 * @return array
	 */
	public function validate() {
		$ret = [];
		if (empty($this->email)) {
			$ret['email'] = 'Email field must be not empty';
		} elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			$ret['email'] = 'Bad email supplied';
		}
		if (strlen($this->name) < 3) {
			$ret['name'] = 'Name must have atleast 3 chars';
		} elseif (strlen($this->name) > 255) {
			$ret['name'] = 'Name must have no more than 255 chars';
		}
		if (strlen($this->password) < 6) {
			$ret['password'] = 'Password must be atleast 6 chars length';
		} elseif (strlen($this->password) > 200) {
			$ret['password'] = 'Name must have no more than 200 chars';
		}
		return $ret;
	}	
	
	/**
	 * Binds all data to PDO statement
	 */
	public function bind(\PDOStatement &$statement) {
		$statement->bindValue(':email', $this->email, \PDO::PARAM_STR);
		$statement->bindValue(':name', $this->name, \PDO::PARAM_STR);
		$statement->bindValue(':password', sha1($this->password), \PDO::PARAM_STR);
		$statement->bindValue(':created_at', $this->created_at, \PDO::PARAM_STR);
	}	
	
}
