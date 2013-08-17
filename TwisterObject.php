<?php
/**
 * @class TwisterObject
 * @brief basic object
 * @author prismadeath (Benjamin Baschet)
 */
class TwisterObject
{
    /**
     * @brief get string class name
     * @return type
     */
    public function __toString() {
        return get_class($this);
    }
}