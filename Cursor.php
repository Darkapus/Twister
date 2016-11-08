<?php
namespace Twister;
/**
 * @brief manage the cursor
 * @class TwisterBag
 * @author prismadeath (Benjamin Baschet)
 */
class Cursor extends Object implements \Iterator, \JsonSerializable
{
    private $documents=array();
    private $position=0;
    private $collection=null;
    /**
     * 
     * @param Twister $t
     * @param type $result
     */
    public function __construct(Collection $collection, $result) {
        $this->collection = $collection;
        $this->documents=$result;
        $this->position = 0;
    }
    /**
     * @brief go to the next entry
     * @return \TwisterBag
     */
    public function next()
    {
        $this->documents->next();
        return $this;
    }
    /**
     * @brief get current entry
     * @return type
     */
    public function current()
    {
        return $this->collection->cast((object)$this->documents->current());
    }
    /**
     * @brief get the key entry
     * @return type
     */
    public function key()
    {
        return $this->documents->key();
    }
    /**
     * @brief valid the current entry
     * @return type
     */
    public function valid()
    {
        return $this->documents->valid();
    }
    /**
     * @brief go to the first entry
     * @return \Twister\Cursor
     */
    public function rewind()
    {
        $this->documents->rewind();
        return $this;
    }
    /**
    * sort by a filter
    * @return \Twister\Cursor
    */
    public function sort(array $filter){
    	$this->documents->sort($filter);
    	return $this;
    }
    /**
    * skip number of
    * @return \Twister\Cursor
    */
    public function skip($number){
    	$this->documents->skip($number);
    	return $this;
    }
    /**
    * count the number of data
    * @return \Twister\Cursor
    */
    public function count($bool=false){
    	return $this->documents->count($bool);
    }
    /**
    * define a number of returned data
    * @return \Twister\Cursor
    */
    public function limit($number){
    	$this->documents->limit($number);
    	return $this;
    }
    /**
    * JsonSerialize implementation
    * @return array
    */
    public function jsonSerialize() {
        $tojson = [];
        
        foreach($this as $document){
        	$document->id = $document->getId().'';
        	$tojson[] = $document;
        }
        
        return $tojson;
    }
}
