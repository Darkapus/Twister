<?php
class TwisterDust extends TwisterObject
{
    public $data;
    private $twister;
    public function __construct(Twister $twister, $data)
    {
        if(!is_object($data)) $data = (object) $data;
        $this->setData($data);
        $this->setTwister($twister);
    }
    public function setTwister(Twister $twister)
    {
        $this->twister = $twister;
        return $this;
    }
    public function getTwister()
    {
        return $this->twister;
    }
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
    public function getData()
    {
        return $this->data;
    }
    public function getId()
    {
        return $this->data->_id;
    }
    public function newId()
    {
        return $this->data->_id=new MongoId();
    }
    public function insert()
    {
        $this->newId();
        $this->getTwister()->insert($this);
    }
    public function delete()
    {
        $this->getTwister()->delete($this);
    }
    public function save()
    {
        $this->getTwister()->save($this);
    }
    public function getRelation($field, $value)
    {
        $relations      = $this->getTwister()->getRelations();
        if(key_exists($field, $relations))
        {
            $relation   = $relations[$field];
            $find       = 'findOneBy'.$relation->field;
            return $relation->twister->$find($value);
        }
        return $value;
    }
    public function __get($name) {
       return $this->data->$name ;
    }
    public function __call($name, $arguments) {
        switch(true)
        {
            case substr($name,0,3)=='get':
                $get    = substr($name,3);
                return $this->getRelation($get, $this->data->$get);
                break;
            case substr($name,0,3)=='set':
                $set    = substr($name,3);
                return $this->data->$set = $arguments[0];
            default:
                throw new Exception($name.' dont exist');
        }
    }
}