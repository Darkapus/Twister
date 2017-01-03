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
    private $cur=0;
    private $search;
    /**
     * 
     * @param Twister $t
     * @param type $result
     */
    public function __construct(Collection $collection, $result, $search=[]) {
        $this->collection = $collection;
        if(is_object($result)){
        	$this->documents = $result->toArray();
        }
        
        $this->position = 0;
	$this->search = $search;
    }
    /**
     * @brief go to the next entry
     * @return \TwisterBag
     */
    public function next()
    {
        return ++$this->cur;
    }
    /**
     * @brief get current entry
     * @return type
     */
    public function current()
    {
    	$documentName = $this->collection->getDocumentName();
    	return $documentName::cast((object)$this->documents[$this->cur]);
    }
    /**
     * @brief get the key entry
     * @return type
     */
    public function key()
    {
        return $this->cur;
    }
    /**
     * @brief valid the current entry
     * @return type
     */
    public function valid()
    {
        return isset($this->documents[$this->cur]);
    }
    /**
     * @brief go to the first entry
     * @return \Twister\Cursor
     */
    public function rewind()
    {
        $this->cur = 0;
        return $this;
    }
    /**
    * count the number of data
    * @return \Twister\Cursor
    */
    public function count(){
	$cmd = new \MongoDB\Driver\Command( [ 'count' => $this->collection->getTableName(), 'query' => $this->search ] );
        $result = $this->collection->getConnection()->getManager()->executeCommand( $this->collection->getConnection()->getDbName(), $cmd )->toArray();

    	return $result[0]->n;
    }
    /**
    * JsonSerialize implementation
    * @return array
    */
    public function jsonSerialize() {
        $tojson = [];
        
        foreach($this as $document){
        	$tojson[] = $document;
        	
        }
        
        return $tojson;
    }
}
