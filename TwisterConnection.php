<?php
class TwisterConnection extends TwisterObject
{
    private $collection;
    private $conn;
    private $db;
    function __construct($server, $dbname=NULL, $args=NULL) {
        $class = 'MongoClient'; 
  
        if(!class_exists($class)){ 
            $class = 'Mongo'; 
        } 
        
        $this->conn = new $class($server); 
        if(!is_null($dbname)) $this->db = $this->conn->selectDB($dbname);
    }
    public function getDb()
    {
        return $this->db;
    }
    public function setCollectionName($name){
        $this->collection = $this->db->$name;
    }
    public function getCollection(){
        return $this->collection;
    }
    public function find($search=NULL)
    {
        if($search)
            return $this->result = $this->getCollection()->find($search);
        else {
            return $this->result = $this->getCollection()->find();
        }
    }
    public function findOne($search=NULL)
    {
        if($search)
        return $this->getCollection()->findOne($search);
        else
            return $this->getCollection()->findOne();
    }
    public function delete($search)
    {
        $this->getCollection()->remove($search);
        return $this;
    }
    public function save($MongoObject)
    {
        $this->getCollection()->save($MongoObject);
        return $this;
    }
    public function insert($MongoObject)
    {
        $this->getCollection()->insert($MongoObject);
    }
    public function __get($name) {
        return $this->db->$name;
    }
    public function __call($name, $arguments) {
        return $this->db->$name(implode(',', $arguments));
    }
    public function create($name)
    {
        return $this->db->createCollection($name);
    }
}