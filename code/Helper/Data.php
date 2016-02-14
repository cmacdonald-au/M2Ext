<?php

class EcomCore_M2Ext_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function __()
    {
        $args = func_get_args();
        return $args[0];
    }

}
