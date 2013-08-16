<?php
class TwisterBag extends TwisterObject implements Iterator
{
    private $twister; // la connection mongo
    private $bag; // le resultat
    private $currentDust=0;
    private $allDust=array();
    public function __construct(Twister $t, $result) {
        $this->setTwister($t);
        
        $this->setBag($result);
    }
    public function setBag($result)
    {
        $this->bag = $result;
        return $this;
    }
    public function getBag()
    {
        return $this->bag;
    }
    public function setTwister(Twister $t){
        $this->twister = $t;
        return $this;
    }
    public function getTwister()
    {
        return $this->twister;
    }
    
    public function next()
    {
        ++$this->currentDust;
    }
    public function current()
    {
        return $this->allDust[$this->currentDust];
    }
    public function key()
    {
        return $this->allDust[$this->currentDust]->data->_id;
    }
    public function valid()
    {
        if(!key_exists($this->currentDust, $this->allDust))
        {
            if($dd = $this->getBag()->getNext())
            {
                $data = $this->getTwister()->getDust($dd);
                $this->allDust[$this->currentDust] = $data;
            }
        }
        return isset($this->allDust[$this->currentDust]);
    }
    public function rewind()
    {
        $this->currentDust = 0;
    }
}