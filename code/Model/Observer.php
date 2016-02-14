<?php

class EcomCore_M2Ext_Model_Observer
{

    public static function layoutObserver(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Ess_M2ePro_Block_Adminhtml_Ebay_Order) {

            $message = Mage::helper('EcomCore_M2Ext')->__('Export sales data from the date range below?');
            $block->addButton('m2ext_report', array(
                'label'   => 'Export Sales Data',
                'onclick' => 'confirmSetLocation(\''.$message.'\', \''.Mage::Helper('adminhtml')->getUrl('adminhtml/m2ext/report/mktplace/ebay').'\')',
                'class'   => 'go'
            ));
        }
    }

}
