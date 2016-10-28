<?php
namespace Twister;
/**
 * @brief main class, mongo request manager
 * @class Twister
 * @author prismadeath (Benjamin Baschet)
 */
class Collection extends Object
{
    protected $tc;
    /**
     * Relation descriptor
     */
    protected $relations = array(); 
    
    /**
     * document name, mongo data will be cast on
     */
    protected $documentName = '\Twister\Document';
    /**
     * Cursor, for reading more than one data
     */
    protected $cursorName = '\Twister\Cursor';

    protected $tableName = null;
    /**
     * 
     * @param Connection $tc
     * @param type $collectionName
     */
    public function __construct(Connection $connection, $collectionName=null) 
    {
        $this->setConnection($connection);
        $this->setTableName($collectionName);
    }
    /**
     * @brief set dust name needed to generate work class
     * @param type $name
     * @return \Twister\Collection
     */
    public function setDocumentName($name)
    {
        $this->documentName = $name;
        return $this;
    }
    public function setTableName($tableName){
        $this->tableName = $tableName;
        return $this;
    }
    public function getTable(){
        $name = $this->tableName;
        return $this->getConnection()->getDb()->$name;
    }
    /**
     * extends Document or implement IDocument
     * if not define \Twister\Document by default
     * document maker with current data
     * @return \Twister\Document
     */
    public function getDocument($data=array())
    {
        if($data){

            // cast data on new documentName()
            $document = $this->cast((object)$data);
            
            return $document;
        }
        else{
            // create empty document
            return false;
        }
    }
    /**
     * create an empty document. Called before insert.
     */ 
    public function getEmptyDocument(){
        $document = new $this->documentName;
        return $document;
    }
    /**
     * @brief set connection
     * @param Connection $connection
     * @return \Twister\Collection
     */
    public function setConnection(Connection $connection)
    {
        $this->tc = $connection;
        return $this;
    }
    /**
     * @brief set bag name, needed
     * @param type $name
     * @return \Twister
     */
     public function setCursorName($name)
    {
        $this->cursorName = $name;
        return $this;
    }
    /**
     * @brief generate bag class
     * @param type $result
     * @return \Twister\Cursor
     */
    public function getCursor($result)
    {
        $name = $this->cursorName;
        return new $name($this, $result);
    }
    /**
     * @brief get twister connection
     * @return \Twister\Connection
     */
    public function getConnection()
    {
        return $this->tc;
    }
    /**
     * @brief launch a search and put it on bag
     * @param type $search
     * @return \Twister\Cursor
     */
    public function find($search=array())
    {
        return $this->getCursor($this->getTable()->find($search));
    }
    /**
     * @brief lauch a search and put it on dust.
     * return false if dont find
     * @param type $search
     * @return false | \Twister\Document
     */
    public function findOne($search=array())
    {
        return $this->getDocument($this->getTable()->findOne($search));
    }
    /**
     * @brief delete a dust
     * @param TwisterDust $dust
     * @return \Twister\Collection
     */
    public function delete($document)
    {
        $this->getTable()->remove(array('_id'=>$document->getId()));
        return $this;
    }
    /**
     * @brief save a dust
     * @param TwisterDust $dust
     * @return \Twister\Collection
     */
    public function save($document)
    {
        $this->getTable()->save($this->getDataFromDocument($document));
        return $this;
    }
    /**
     * @brief insert data on array
     * @param Document $document
     * @param type $field
     * @param type $value
     * @return \Twister\Collection
     */
    public function push($document, $field, $value) {

        $this->getTable()->update(array('_id'=>$document->getId()), array('$push'=>array($field=>$value)));
        return $this;
    }
    /**
     * @brief pull data on array
     * @param Document $document
     * @param type $field
     * @param type $value
     * @return \Twister\Collection
     */
    public function pull($document, $field, $value) {

        $this->getTable()->update(array('_id'=>$document->getId()), array('$pull'=>array($field=>$value)));
        return $this;
    }
    /**
     * @brief insert a dust
     * @param Dust $dust
     * @return \Twister\Collection
     */
    public function insert($document)
    {
        $this->getTable()->insert($this->getDataFromDocument($document));
        return $this;
    }
    /**
     * @brief create collection
     * @param type $name
     * @return \Twister\Collection
     */
    public function create($name)
    {
        $this->getTable()->create($name);
        return $this;
    }
    /**
     * @brief launch a search by field and value
     * @param type $field
     * @param type $value
     * @return \Twister\Document
     */
    public function findOneByField($field, $value)
    {
        return $this->findOne(array($field=>$value));
    }
    /**
     * @brief launch a search by field and value
     * @param type $field
     * @param type $value
     * @return \Twister\Cursor
     */
    public function findByField($field, $value)
    {
        return $this->find(array($field=>$value));
    }
    /**
     * @brief get relations of twister
     * @return type
     */
    public function getRelations()
    {
        return $this->relations;
    }
    public function __call($name, $arguments) {
        preg_match_all("/(findOne|find)By_(.*)/", $name, $matches);
        
        switch ($matches[1][0])
        {
            case 'findOne':
                return $this->findOneByField($matches[2][0],$arguments[0]);
            case 'find':
                return $this->findByField($matches[2][0],$arguments[0]);    
        }
    }
    /**
     * Class casting
     *
     * @param object $sourceObject
     * @return object
     */
    function cast($sourceObject)
    {
        $destination = $this->documentName;
        if (is_string($destination)) {
            $destination = new $destination();
        }
        $sourceReflection = new \ReflectionObject($sourceObject);
        $destinationReflection = new \ReflectionObject($destination);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
            if($value instanceof \MongoDate){
                $value = $value->toDateTime();
            }
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($destination,$value);
            } else {
                $destination->$name = $value;
            }
        }
        return $destination;
    }
    public function getDataFromDocument($document){
    	$std = array();
    	$documentReflection = new \ReflectionObject($document);
    	$documentProperties = $documentReflection->getProperties();
    	foreach ($documentProperties as $documentPropertie) {
    		$documentPropertie->setAccessible(true);
    		$name = $documentPropertie->getName();
            $value = $documentPropertie->getValue($document);
            if($name == '_id' && is_null($value)) continue;
            if(is_object($value)){
    			if($value instanceof \MongoId){
    				$std[$name] = $value;
    			}
                elseif($value instanceof \DateTime){
                    $std[$name] = new \MongoDate($value->getTimestamp());
                }
                elseif($value instanceof \MongoDate){
                    $std[$name] = $value;
                }
    			else{
                    $std[$name] = $this->getDataFromDocument($value);
    			}
    		}
    		elseif(is_array($value)){
    		    $std[$name] = array();
    		    foreach($value as $v){
    		        if(is_object($v)){
            			if($value instanceof \MongoId){
            				$std[$name][] = $v;
            			}
            			else{
            				$std[$name][] = $this->getDataFromDocument($v);
            			}
            		}
            		else{
            		    $std[$name][] = $v;
            		}
    		    }
    		}
    		else{
    			$std[$name] = $value;
    		}
    	}
    	return $std;	
    }
}
