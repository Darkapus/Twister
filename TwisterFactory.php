<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class TwisterFactory extends TwisterObject
{
    private $i=null;
    private $tc=null;
    static public function i()
    {
        if(is_null($this->i))
        {
            $this->i = new TwisterFactory();
        }
        return $this->i;
    }
    public function __construct() 
    {
        
    }
    public function getTwisterConnection()
    {
        return $this->tc;
    }
    public function setTwisterConnection(TwisterConnection $tc)
    {
        $this->tc = $tc;
        return $this;
    }
    public function add($o, $collection=null)
    {
        $data = method_exists($o, 'getData')?$o->getData():$o;
        $data->class = get_class($o);
        if(is_null($collection)) $collection = $data->class;
        $data->serialize = serialize($o);
        $twister = new Twister($this->getTwisterConnection(), $collection);
        $twister->getDust()->setData($data)->insert();
    }
    public function __call($name, $arguments) {
        preg_match_all("/(findOne|find)By_(.*)/", $name, $matches);
        $twister = new Twister($this->getTwisterConnection(),$arguments[1] );
        switch ($matches[1][0])
        {
            case 'findOne':
                return $twister->findOneByField($matches[2][0],$arguments[0]);
            case 'find':
                return $twister->findByField($matches[2][0],$arguments[0]);    
        }
    }
    
}