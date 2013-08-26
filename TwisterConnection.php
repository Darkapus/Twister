<?php
/**
 * @brief manage the connection language with mongodb
 * @class TwisterConnection
 * @author prismadeath (Benjamin Baschet)
 */
class TwisterConnection extends TwisterObject
{
    private $collection;
    private $conn;
    private $db;
    /**
     * 
     * @param type $server
     * @param type $dbname
     * @param type $args
     */
    function __construct($server, $dbname=NULL, $args=NULL) {
        $class = 'MongoClient'; 
  
        if(!class_exists($class)){ 
            $class = 'Mongo'; 
        } 
        
        $this->conn = new $class($server); 
        if(!is_null($dbname)) $this->db = $this->conn->selectDB($dbname);
    }
    /**
     * @brief get mongo db
     * @return \MongoDb
     */
    public function getDb()
    {
        return $this->db;
    }
    /**
     * @brief set collection name
     * @param type $name
     * @return \TwisterConnection
     */
    public function setCollectionName($name){
        $this->collection = $this->db->$name;
        return $this;
    }
    /**
     * @brief get the collection, future : make a class
     * @return type
     */
    public function getCollection(){
        return $this->collection;
    }
    /**
     * @brief get the query result 
     * @param type $search
     * @return \MongoCursor
     */
    public function find($search=NULL)
    {
        if($search)
            return $this->result = $this->getCollection()->find($search);
        else {
            return $this->result = $this->getCollection()->find();
        }
    }
    /**
     * @brief get the first result of search
     * @param type $search
     * @return array
     */
    public function findOne($search=NULL)
    {
        if($search)
        return $this->getCollection()->findOne($search);
        else
            return $this->getCollection()->findOne();
    }
    /**
     * @brief delete data from search
     * @param type $search
     * @return \TwisterConnection
     */
    public function delete($search)
    {
        $this->getCollection()->remove($search);
        return $this;
    }
    /**
     * @brief set data
     * @param type $MongoObject
     * @return \TwisterConnection
     */
    public function save($MongoObject)
    {
        $this->getCollection()->save($MongoObject);
        return $this;
    }
    /**
     * @brief insert data on field multiple
     * @param type $mongoObject
     * @param type $field
     * @param type $value
     * @return \TwisterConnection
     */
    public function push($mongoObject, $field, $value)
    {
        $this->getCollection()->update(array('_id'=>$mongoObject->_id), array('$push', array($field=>$value)));
        return $this;
    }
    /**
     * @brief create du data on mongodb
     * @param type $MongoObject
     * @return \TwisterConnection
     */
    public function insert($MongoObject)
    {
        $this->getCollection()->insert($MongoObject);
        return $this;
    }
    /**
     * @brief get on db
     * @param type $name
     * @return type
     */
    public function __get($name) {
        return $this->db->$name;
    }
    /**
     * @brief call method on db
     * @param type $name
     * @param type $arguments
     * @return type
     */
    public function __call($name, $arguments) {
        return $this->db->$name(implode(',', $arguments));
    }
    /**
     * @brief create new collection
     * @param type $name
     * @return type
     */
    public function create($name)
    {
        return $this->db->createCollection($name);
    }
}