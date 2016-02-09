<?php

namespace App\Models;

class Comment extends AbstractModel {

	/**
	 * Email
	 *
	 * @var string
	 */
	public $email;

	/**
	 * Author name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Comment
	 *
	 * @var string
	 */
	public $comment;
	
	/**
	 * When was created?
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
		if (strlen($this->comment) < 3) {
			$ret['comment'] = 'Comment must have atleast 3 chars';
		} elseif (strlen($this->comment) > 65535) {
			$ret['comment'] = 'Name must have no more than 65535 chars';
		}
		return $ret;
	}
	
	/**
	 * Binds all data to PDO statement
	 */
	public function bind(\PDOStatement &$statement) {
		$statement->bindValue(':email', $this->email, \PDO::PARAM_STR);
		$statement->bindValue(':name', $this->name, \PDO::PARAM_STR);
		$statement->bindValue(':comment', $this->comment, \PDO::PARAM_STR);
		$statement->bindValue(':created_at', $this->created_at, \PDO::PARAM_STR);
	}	
	
	public function getGravatarHash() {
		return md5(strtolower(trim($this->email)));
	}

}
