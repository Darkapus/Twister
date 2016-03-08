<?php
namespace Twister;
/**
 * @class TwisterObject
 * @brief basic object
 * @author prismadeath (Benjamin Baschet)
 */
class Object
{
    /**
     * @brief get string class name
     * @return type
     */
    public function __toString() {
        return get_class($this);
    }
}