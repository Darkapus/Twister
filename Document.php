<?php
namespace Twister;
class Document{
	protected $_id;
	public function __construct(){
		//$this->_id = new \MongoId();
		//_id is managed by mongo automaticaly
	}
	public function getId(){
		return $this->_id;
	}
}
