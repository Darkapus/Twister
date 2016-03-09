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
    
    protected $dustName = '\Twister\Dust';
    /**
     * document name, mongo data will be cast on
     */
    protected $documentName = '\Twister\Document';
    protected $bagName = '\Twister\Bag'; // objet des curseurs
    /**
     * Cursor, for reading more than one data
     */
    protected $cursorName = '\Twister\Cursor';
    /**
     * 
     * @param Connection $tc
     * @param type $collectionName
     */
    public function __construct(Connection $connection, $collectionName=null) 
    {
        $this->setConnection($connection);
        if($collectionName) 
        {
            $tc->setCollectionName($collectionName);
        }
    }
    /**
     * @brief set dust name needed to generate work class
     * @param type $name
     * @return \Twister
     */
    public function setDustName($name)
    {
        $this->dustName = $name;
        return $this;
    }
    /**
     * @brief generate dust class with data
     * @param type $data
     * @return \Dust
     */
    public function getDust($data=array())
    {
        $name = $this->dustName;
        $dust = new $name();
        $dust->setCollection($this);
        $dust->setData($data);
        return $dust;
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
        }
        else{
            // create empty document
            $document = new $this->documentName;
        }
        // set current collection to easy use for insert/add/save/delete
        $document->setCollection($this);
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
     public function setBagName($name)
    {
        $this->bagName = $name;
        return $this;
    }
    /**
     * @brief generate bag class
     * @param type $result
     * @return \name
     */
    public function getBag($result)
    {
        $name = $this->bagName;
        return new $name($this, $result);
    }
    /**
     * @brief get twister connection
     * @return \TwisterConnection
     */
    public function getConnection()
    {
        return $this->tc;
    }
    /**
     * @brief launch a search and put it on bag
     * @param type $search
     * @return \TwisterBag
     */
    public function find($search=NULL)
    {
        $result = $this->getConnection()->find($search);
       
        return $this->getBag($result);
    }
    /**
     * @brief lauch a search and put it on dust
     * @param type $search
     * @return \TwisterDust
     */
    public function findOne($search=NULL)
    {
        return $this->getDust($this->getConnection()->findOne($search));
    }
    /**
     * @brief delete a dust
     * @param TwisterDust $dust
     * @return \Twister
     */
    public function delete(Dust $dust)
    {
        $this->getConnection()->delete(array('_id'=>$dust->getId()));
        return $this;
    }
    /**
     * @brief save a dust
     * @param TwisterDust $dust
     * @return \Twister
     */
    public function save(Dust $dust)
    {
        $this->getConnection()->save($dust->getData());
        return $this;
    }
    /**
     * @brief insert data on array
     * @param TwisterDust $dust
     * @param type $field
     * @param type $value
     * @return \Twister
     */
    public function push(Dust $dust, $field, $value)
    {
        $this->getConnection()->push($dust->getData(), $field, $value);
        return $this;
    }
    /**
     * @brief insert a dust
     * @param Dust $dust
     * @return \Twister
     */
    public function insert(Dust $dust)
    {
        $this->getConnection()->insert($dust->getData());
        return $this;
    }
    /**
     * @brief create collection
     * @param type $name
     * @return \Twister
     */
    public function create($name)
    {
        $this->getConnection()->create($name);
        return $this;
    }
    /**
     * @brief launch a search by field and value
     * @param type $field
     * @param type $value
     * @return \TwisterDust
     */
    public function findOneByField($field, $value)
    {
        return $this->findOne(array($field=>$value));
    }
    /**
     * @brief launch a search by field and value
     * @param type $field
     * @param type $value
     * @return \TwisterBag
     */
    public function findByField($field, $value)
    {
        return $this->find(array($field=>$value));
    }
    /**
     * @brief set a relation betweend two twisters
     * @param type $sourceField
     * @param Twister $relationTwister
     * @param type $relationField
     * @return \Twister
     */
    public function addRelation($sourceField, Collection $collection, $relationField, $type='simple')
    {
        $orel1                              = new \stdClass();
        $orel1->field                       = $relationField;
        $orel1->twister                     = $collection;
        $orel1->type                        = $type;
        $this->relations[$sourceField]      = $orel1;
        
        return $this;
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
        $sourceReflection = new ReflectionObject($sourceObject);
        $destinationReflection = new ReflectionObject($destination);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
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
}
