<?php
/**
 * @brief manage the data from mongodb
 * @class TwisterDust
 * @author prismadeath (Benjamin Baschet)
 */
class TwisterDust extends TwisterObject
{
    /**
     * @brief here, stocked data from mongodb
     * @var type 
     */
    public $data;
    /**
     * @brief class Twister
     * @var type 
     */
    private $twister;
    public function __construct(Twister $twister, $data)
    {
        if(!is_object($data)) $data = (object) $data;
        $this->setData($data);
        $this->setTwister($twister);
    }
    /**
     * @brief set twister, Needed for insert / update / delete
     * @param Twister $twister
     * @return \TwisterDust
     */
    public function setTwister(Twister $twister)
    {
        $this->twister = $twister;
        return $this;
    }
    /**
     * @brief get twister. Needed for insert / update / delete
     * @return \Twister
     */
    public function getTwister()
    {
        return $this->twister;
    }
    /**
     * @brief set data
     * @param type $data
     * @return \TwisterDust
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
    /**
     * @brief return the data
     * @return object
     */
    public function getData()
    {
        return $this->data;
    }
    /**
     * @brief return data id
     * @return string
     */
    public function getId()
    {
        return $this->data->_id;
    }
    /**
     * @brief generate a mongo id object
     * @return MongoId
     */
    public function newId()
    {
        return $this->data->_id=new MongoId();
    }
    /**
     * @brief insert a new entry on mongodb
     * @return \TwisterDust
     */
    public function insert()
    {
        $this->newId();
        $this->getTwister()->insert($this);
        return $this;
    }
    /**
     * @brief delete the current data
     * @return \TwisterDust
     */
    public function delete()
    {
        $this->getTwister()->delete($this);
        return $this;
    }
    /**
     * @brief data save action
     * @return TwisterDust
     */
    public function save()
    {
        $this->getTwister()->save($this);
        return $this;
    }
    /*
     * @brief get the relation field data
     * @return string or MongoId
     */
    public function getRelation($field, $value)
    {
        $relations      = $this->getTwister()->getRelations();
        if(key_exists($field, $relations))
        {
            $relation   = $relations[$field];
            $find       = 'findOneBy_'.$relation->field;
            return $relation->twister->$find($value);
        }
        return $value;
    }
    /**
     * @brief get a data from the dust
     * @param type $name
     * @return string
     */
    public function __get($name) {
       return $this->data->$name ;
    }
    public function __call($name, $arguments) {
        switch(substr($name,0,3))
        {
            case 'get':
                $get    = substr($name,3);
                return $this->getRelation($get, $this->data->$get);
                break;
            case 'set':
                $set        = substr($name,3);
                $value = $arguments[0];
                if($arguments[0] instanceof TwisterDust)
                {
                    $relations  = $this->getTwister()->getRelations();
                    $o          = $relations[$set];
                    if((string)$o->twister == (string)$value->getTwister())
                    {
                        $field      = 'get'.$o->field;
                        $value      = $value->$field();
                    }
                }
                return $this->data->$set = $arguments[0];
            default:
                throw new Exception($name.' dont exist');
        }
    }
}