<?php
/**
 * C'est la classe principale qui gérera l'ensemble des requetes effectuées dans la base
 * Il est possible d'utiliser autre chose que Mongo juste en héritant la class TwisterConnection
 */
class Twister extends TwisterObject
{
    protected $tc;
    protected $relations = array();
    protected $dustName = 'TwisterDust';
    protected $bagName = 'TwisterBag'; // objet des curseurs
    public function __construct(TwisterConnection $tc, $collectionName=null) 
    {
        $this->setConnection($tc);
        if($collectionName) $tc->setCollectionName($collectionName);
    }
    public function setDustName($name)
    {
        $this->dustName = $name;
    }
    public function getDust($data)
    {
        $name = $this->dustName;
        return new $name($this, $data);
    }
    public function setConnection($tc)
    {
        $this->tc = $tc;
        return $this;
    }
    
     public function setBagName($name)
    {
        $this->bagName = $name;
    }
    
    public function getBag($result)
    {
        $name = $this->bagName;
        return new $name($this, $result);
    }
    public function getConnection()
    {
        return $this->tc;
    }
    
    public function find($search=NULL)
    {
        $result = $this->getConnection()->find($search);
       
        return $this->getBag($result);
    }
    public function findOne($search=NULL)
    {
        return $this->getDust($this->getConnection()->findOne($search));
    }
    public function delete(TwisterDust $dust)
    {
        $this->getConnection()->remove(array('_id'=>$dust->getId()));
        return $this;
    }
    public function save(TwisterDust $dust)
    {
        $this->getConnection()->save($dust->getData());
        return $this;
    }
    public function insert(TwisterDust $dust)
    {
        $this->getConnection()->insert($dust->getData());
        return $this;
    }
    public function create($name)
    {
        $this->getConnection()->create($name);
        return $this;
    }
    public function setRelation($sourceField, Twister $relationTwister, $relationField)
    {
        $orel                           = new stdClass();
        $orel->field                    = $relationField;
        $orel->twister                  = $relationTwister;
        $this->relations[$sourceField]  = $orel;
        return $this;
    }
    public function getRelations()
    {
        return $this->relations;
    }
    public function __call($name, $arguments) {
        $t = explode('By', $name);
        switch ($t[0])
        {
            case 'findOne':
                return $this->findOne(array($t[1]=>$arguments[0]));
            case 'find':
                return $this->find(array($t[1]=>$arguments[0]));    
        }
    }
}
