<?php

/*
 * @brief need to be tested
 * @class TwisterFactory
 * @author prismadeath (Benjamin Baschet)
 */
class TwisterFactory extends TwisterObject
{
    private static $i=null;
    private $tc=null;
    /**
     * 
     * @return TwisterFactory
     */
    static public function i()
    {
        if(is_null(self::$i))
        {
            self::$i = new TwisterFactory();
        }
        return self::$i;
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
        if(!($o instanceof IDust)) throw new TwisterException('Object need to be an instance of TwisterInterface.');
        $data = method_exists($o, 'getData')?$o->getData():$o;
        $data->class = get_class($o);
        if(is_null($collection)) $collection = $data->class;
        
        $twister = new Twister($this->getTwisterConnection(), $collection);
        $twister->setDustName(get_class($o));
        $dust   = $twister->getDust()->setData($data)->insert();        
        return $dust;
    }
    public function __call($name, $arguments) {
        preg_match_all("/(findOne|find)By_(.*)/", $name, $matches);
        $twister = new Twister($this->getTwisterConnection(),$arguments[0] );
        switch ($matches[1][0])
        {
            case 'findOne':
                return $twister->findOneByField($matches[2][0],$arguments[1]);
            case 'find':
                return $twister->findByField($matches[2][0],$arguments[1]);    
        }
    }
    
}
