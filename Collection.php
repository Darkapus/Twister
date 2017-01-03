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
    public function getTableName(){
	return $this->tableName;
    }
    public function setTableName($tableName){
        $this->tableName = $tableName;
        return $this;
    }
    public function getTable(){
        $name = $this->tableName;
        return $this->getConnection()->setCollectionName($name);
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
		$documentName = $this->documentName;
        	return $documentName::cast((object)$data);
        }
        else{
            // create empty document
            return false;
        }
    }
    public function getDocumentName(){
    	return $this->documentName;
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
    public function getCursor($result, $search=[])
    {
        $name = $this->cursorName;
        return new $name($this, $result, $search);
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
    public function find($search=array(), $limit=25, $skip=0, $sort=[])
    {
        return $this->getCursor($this->getTable()->find($search, ['limit'=>$limit, 'skip'=>$skip, 'sort'=>$sort]), $search);
    }
    public function aggregate($query){
		return $this->getTable()->aggregate($query);	
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
        $this->getTable()->delete(array('_id'=>$document->getMongoId()));
        return $this;
    }
    /**
     * @brief save a dust
     * @param TwisterDust $dust
     * @return \Twister\Collection
     */
    public function save($document)
    {
        $this->getTable()->save($document->getData());
        return $this;
    }
    
    public function update($document, $query){
    	$this->getTable()->update(array('_id'=>$document->getMongoId()), $query);
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

        $this->getTable()->update(array('_id'=>$document->getMongoId()), array('$push'=>array($field=>$value)));
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

        $this->getTable()->update(array('_id'=>$document->getMongoId()), array('$pull'=>array($field=>$value)));
        return $this;
    }
    /**
     * @brief insert a dust
     * @param Dust $dust
     * @return \Twister\Collection
     */
    public function insert($document)
    {
        $ident = $this->getTable()->insert($document->getData());
	$document->setMongoId($ident); // push the mongo id to the document
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
    public function cast($sourceObject)
    {
        $destination = $this->documentName;
        return $destination::cast($sourceObject);
    }
    public function getDataFromDocument($document){
    	return $document->getData();
    }
}
