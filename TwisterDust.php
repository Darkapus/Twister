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
        if(property_exists($this->data, '_id'))
        {        
            return $this->data->_id;
        }    
        else 
        {
            return false;
        }
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
    /**
     * @brief get the relation field data
     * @todo put logic in Twister class
     * @param string $field
     * @param mixte $value // can be a string / array / object
     * @return TwisterBag or TwisterDust
     */
    public function getRelation($field, $value)
    {
        $relations      = $this->getTwister()->getRelations();
        if(key_exists($field, $relations))
        {
            $relation   = $relations[$field];
            switch($relation->type)
            {
                case 'simple':
                case 'single':
                    $find       = 'findOneBy_'.$relation->field;
                    break;
                case 'multiple':
                    $find       = 'findBy_'.$relation->field;
                    break;
            }
            
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
        if(property_exists($this->data, $name))
        {
            return $this->data->$name ;
        }
        else 
        {
            return false;
        }
    }
    public function __call($name, $arguments) {
        
        preg_match_all("/(push|pull|add|set|get)(.*)/", $name, $matches);
        
        switch($matches[1][0])
        {
            case 'add':
                $get                = $matches[2][0];
                //@todo put logic in Twister class
                if($arguments[0] instanceof TwisterDust)
                {
                    $relations      = $this->getTwister()->getRelations();
                    $field          = $relations[$name]->field;
                    // push a data (Multiple Relation)
                    $this->getTwister()->push($this, $get, $arguments[0]->$field);
                }
                else
                {
                    // push a value
                    $this->getTwister()->push($this, $get, $arguments[0]);
                }
                return $this;
                break;
            case 'get':
                $get                = $matches[2][0];
                //@todo put logic in Twister class
                if(property_exists($this->data, $get))
                return $this->getRelation($get, $this->data->$get);
                else
                    return '';
                break;
            case 'set':
                $set                = $matches[2][0];
                $value              = $arguments[0];
                //@todo put logic in Twister class
                if($arguments[0] instanceof TwisterDust)
                {
                    $relations      = $this->getTwister()->getRelations();
                    $o              = $relations['simple'][$set];
                    if((string)$o->twister == (string)$value->getTwister())
                    {
                        $field      = 'get'.$o->field;
                        $value      = $value->$field();
                    }
                }
                 $this->data->$set  = $arguments[0];
                 return $this;
            default:
                throw new Exception($name.' dont exist');
        }
    }
}