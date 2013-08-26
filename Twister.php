<?php
/**
 * @brief main class, mongo request manager
 * @class Twister
 * @author prismadeath (Benjamin Baschet)
 */
class Twister extends TwisterObject
{
    protected $tc;
    protected $relations = array(); 
    protected $dustName = 'TwisterDust';
    protected $bagName = 'TwisterBag'; // objet des curseurs
    /**
     * 
     * @param TwisterConnection $tc
     * @param type $collectionName
     */
    public function __construct(TwisterConnection $tc, $collectionName=null) 
    {
        $this->setConnection($tc);
        if($collectionName) $tc->setCollectionName($collectionName);
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
     * @return \TwisterDust
     */
    public function getDust($data=array())
    {
        $name = $this->dustName;
        return new $name($this, $data);
    }
    /**
     * @brief set connection
     * @param type $tc
     * @return \Twister
     */
    public function setConnection($tc)
    {
        $this->tc = $tc;
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
    public function delete(TwisterDust $dust)
    {
        $this->getConnection()->remove(array('_id'=>$dust->getId()));
        return $this;
    }
    /**
     * @brief save a dust
     * @param TwisterDust $dust
     * @return \Twister
     */
    public function save(TwisterDust $dust)
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
    public function push(TwisterDust $dust, $field, $value)
    {
        $this->getConnection()->push($dust->getData(), $field, $value);
        return $this;
    }
    /**
     * @brief insert a dust
     * @param TwisterDust $dust
     * @return \Twister
     */
    public function insert(TwisterDust $dust)
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
    public function addRelation($sourceField, Twister $relationTwister, $relationField, $type='simple')
    {
        $orel1                              = new stdClass();
        $orel1->field                       = $relationField;
        $orel1->twister                     = $relationTwister;
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
        $t = explode('By_', $name);
        switch ($t[0])
        {
            case 'findOne':
                return $this->findOneByField($t[1],$arguments[0]);
            case 'find':
                return $this->findByField($t[1],$arguments[0]);    
        }
    }
}
