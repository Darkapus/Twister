<?php
namespace Twister;
class Document{
	protected $_id;
	public function __construct(){
		$this->_id = new \MongoId();
	}
	public function getId(){
		return $this->_id;
	}
}