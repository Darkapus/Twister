<?php
namespace Twister;
/**
 * @brief manage the connection language with mongodb
 * @class \Twister\Connection
 * @author Darkapus (Benjamin Baschet)
 */
class Connection extends Object
{
    protected $collection;
    protected $conn;
    protected $dbname;
    protected $autocommit = true;
    protected $bulk;
    
    /**
     * 
     * @param type $server
     * @param type $dbname
     * @param type $args
     */
    function __construct($server, $dbname=NULL, $args=NULL) {
	$this->dbname = $dbname;
		
	if(!class_exists('\MongoDB\Driver\Manager')) {
		throw new Exception('Please install mongodb; check peck mongodb;'); // twister exception
	}
		
	$manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017/$dbname");
	$this->conn = $manager;
	$this->bulk = new \MongoDB\Driver\BulkWrite;
    }
    /**
     * if autocommit = false, need to do : commit()
     * it is preferable to set autocommit at false when there are a lot of operations
     * return \Twister\Connection
     */
    public function commit(){
    	$this->conn->executeBulkWrite($this->dbname.'.'.$this->collection, $this->bulk);
    	$this->bulk = new \MongoDB\Driver\BulkWrite;
    	return $this;
    }
    
    public function setAutoCommit($bool=true){
    	$this->autocommit = $bool;
    	return $this;
    }
    public function isAutoCommit(){
    	return $this->autocommit;
    }
    
    function getManager(){
	return $this->conn;
    }
    
    public function getDbName(){
	return $this->dbname;
    }
    /**
     * @brief set collection name
     * @param type $name
     * @return \Twister\Connection
     */
    public function setCollectionName($name){
        $this->collection = $name;
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
    public function find($search=NULL, $options)
    {
		$query = new \MongoDB\Driver\Query($search, $options);
		$cursor = $this->conn->executeQuery($this->dbname.'.'.$this->collection, $query);
		return $cursor;
    }
    /**
     * @brief get the first result of search
     * @param type $search
     * @return array
     */
    public function findOne($search=NULL)
    {
	$query = new \MongoDB\Driver\Query($search);
        $cursor = $this->conn->executeQuery($this->dbname.'.'.$this->collection, $query);
        foreach($cursor as $row) return $row;    
    }
    /**
     * @brief delete data from search
     * @param type $search
     * @return \Twister\Connection
     */
    public function delete($search)
    {
	$this->bulk->delete($search);
	if($this->isAutoCommit()) $this->commit();
	return $this;
    }
    /**
     * @brief set data
     * @param type $MongoObject
     * @return \Twister\Connection
     */
    public function save($MongoObject)
    {
        $this->bulk->update(['_id'=>$MongoObject->_id], ['$set'=>$MongoObject]);
        if($this->isAutoCommit()) $this->commit();
	return $this;
    }
    /**
     * @brief pull data on field multiple
     * @param type $mongoObject
     * @param type $field
     * @param type $value
     * @return \Twister\Connection
     */
    public function pull($mongoObject, $field, $value)
    {
	$this->bulk->update(array('_id'=>$mongoObject->_id), array('$pull', array($field=>$value)));
	if($this->isAutoCommit()) $this->commit();
        return $this;
    }
    /**
     * @brief insert data on field multiple
     * @param type $mongoObject
     * @param type $field
     * @param type $value
     * @return \Twister\Connection
     */
    public function push($mongoObject, $field, $value)
    {
        $this->bulk->update(array('_id'=>$mongoObject->_id), array('$push', array($field=>$value)));
        if($this->isAutoCommit()) $this->commit();
        return $this;
    }
    /**
     * @brief create du data on mongodb
     * @param type $MongoObject
     * @return \Twister\Connection
     */
    public function insert($MongoObject)
    {
	$mongoid = $this->bulk->insert($MongoObject);
	if($this->isAutoCommit()) $this->commit();
        return $mongoid;
    }
    /**
     * return array
     */
    public function aggregate($query){
	$command = new \MongoDB\Driver\Command(['aggregate' => $this->collection,'pipeline'=>$query,'cursor'=>new \stdClass]);
	$result = $this->getManager()->executeCommand($this->dbname, $command);
	return $result->toArray();
    }
}
