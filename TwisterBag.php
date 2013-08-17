<?php
/**
 * @brief manage the cursor
 * @class TwisterBag
 * @author prismadeath (Benjamin Baschet)
 */
class TwisterBag extends TwisterObject implements Iterator
{
    private $twister; // la connection mongo
    private $bag; // le resultat
    private $currentDust=0;
    private $allDust=array();
    /**
     * 
     * @param Twister $t
     * @param type $result
     */
    public function __construct(Twister $t, $result) {
        $this->setTwister($t);
        
        $this->setBag($result);
    }
    /**
     * @brief set data needed
     * @param type $result
     * @return \TwisterBag
     */
    public function setBag($result)
    {
        $this->bag = $result;
        return $this;
    }
    /**
     * @brief get datas
     * @return \MongoCursor
     */
    public function getBag()
    {
        return $this->bag;
    }
    /**
     * twister needed 
     * @param Twister $t
     * @return \TwisterBag
     */
    public function setTwister(Twister $t){
        $this->twister = $t;
        return $this;
    }
    /**
     * @brief twister needed 
     * @return \Twister
     */
    public function getTwister()
    {
        return $this->twister;
    }
    /**
     * @brief go to the next entry
     * @return \TwisterBag
     */
    public function next()
    {
        ++$this->currentDust;
        return $this;
    }
    /**
     * @brief get current entry
     * @return type
     */
    public function current()
    {
        return $this->allDust[$this->currentDust];
    }
    /**
     * @brief get the key entry
     * @return type
     */
    public function key()
    {
        return $this->allDust[$this->currentDust]->data->_id;
    }
    /**
     * @brief valid the current entry
     * @return type
     */
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
    /**
     * @brief go to the first entry
     * @return \TwisterBag
     */
    public function rewind()
    {
        $this->currentDust = 0;
        return $this;
    }
}