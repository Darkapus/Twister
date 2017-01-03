<?php
namespace Twister;
class Document implements \JsonSerializable {
	protected $_id;
	public function __construct(){
		//$this->_id = new \MongoId();
	}
	public function getMongoId(){
		return $this->_id;
	}
	public function setMongoId($id){
		$this->_id = $id;
		return $this;
	}
	/**
     * Class casting
     *
     * @param object $sourceObject
     * @return Document
     */
    public static function cast($sourceObject)
    {
        $destination = static::class;
        if (is_string($destination)) {
            $destination = new $destination();
        }
        $sourceReflection = new \ReflectionObject($sourceObject);
        $destinationReflection = new \ReflectionObject($destination);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
            
            if($value instanceof \MongoDB\BSON\UTCDatetime){
                $value = $value->toDateTime();
            }
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($destination,$value);
            } else {
                $destination->$name = $value;
            }
        }
        return $destination;
    }
    /**
     * get a standart object
     * return \stdClass
     */
	public function getData(){
		$document = $this;
    	$std = array();
    	$documentReflection = new \ReflectionObject($document);
    	$documentProperties = $documentReflection->getProperties();
    	foreach ($documentProperties as $documentPropertie) {
    		$documentPropertie->setAccessible(true);
    		$name = $documentPropertie->getName();
            $value = $documentPropertie->getValue($document);
            if($name == '_id' && is_null($value)) continue;
            if(is_object($value)){
    			if($value instanceof \MongoDB\BSON\ObjectID){
    				$std[$name] = $value;
    			}
                elseif($value instanceof \DateTime){
                    $std[$name] = new \MongoDB\BSON\UTCDatetime($value->getTimestamp() * 1000);
                }
                elseif($value instanceof \MongoDB\BSON\UTCDatetime){
                    $std[$name] = $value;
                }
    			else{
    				if(method_exists($value, 'getData')){
    					$std[$name] = $value->getData();	
    				}
                    else{
                    	$std[$name] = $value;
                    }
    			}
    		}
    		elseif(is_array($value)){
    		    $std[$name] = array();
    		    foreach($value as $v){
    		        if(is_object($v)){
            			if($value instanceof \MongoDB\BSON\ObjectID){
            				$std[$name][] = $v;
            			}
            			else{
            				if(method_exists($value, 'getData')){
            					$std[$name][] = $v->getData();
            				}
            				else{
		                    	$std[$name] = $value;
		                    }
            			}
            		}
            		else{
            		    $std[$name][] = $v;
            		}
    		    }
    		}
    		else{
    			$std[$name] = $value;
    		}
    	}
    	return $std;	
    }
    
    /**
    * JsonSerialize implementation
    * @return array
    */
    public function jsonSerialize() {
        return $this->getData();
    }
}