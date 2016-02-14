<?php

class EcomCore_M2Ext_Model_Report extends Mage_Core_Model_Abstract
{

    public $startDate   = false;
    public $stopDate    = false;
    public $marketplace = false;

    public static $ebayHeaders = array(
        'purchase_date'            => 's.purchase_create_date',
        'bill_name'                => 'CONCAT_WS(" ", m.customer_firstname, m.customer_lastname)',
        'magento_order_number'     => 'm.increment_id',
        'marketplace'              => 'mkt.url',
        'marketplace_order_number' => 's.ebay_order_id',
        'marketplace_order_id'     => 's.selling_manager_id',
        'currency'                 => 's.currency',
        'payment_amount'           => 's.paid_amount',
        'status'                   => 'm.status',
    );


    protected function construct()
    {
        $this->init('ecomcore_m2ext/report');
    }

    public function run()
    {

        $headers = array();
        $fields = array();

        $res = Mage::getSingleton('core/resource');
        if ($this->marketplace == 'ebay') {
            $primaryTable  = $res->getTableName('m2epro_ebay_order');
            $paymentStatus = Ess_M2ePro_Model_Ebay_Order::PAYMENT_STATUS_COMPLETED;
            foreach (self::$ebayHeaders as $k => $v) {
                $headers[] = $k;
                $fields[] = $v.' AS '.$k;
            }
        } else {
            throw new Exception('Unsupported marketplace ['.$this->marketplace.']');
        }


        $sql = 'SELECT
            '.implode(', ',$fields).'
        FROM 
            '.$primaryTable.' s 
        JOIN
            '.$res->getTableName('m2epro_order').' o
             ON o.id = s.order_id
        JOIN
            '.$res->getTableName('sales/order').' m
             ON m.entity_id = o.magento_order_id
        JOIN
            '.$res->getTableName('m2epro_marketplace').' mkt
             ON mkt.id = o.marketplace_id
        WHERE
            s.payment_status = '.$paymentStatus;

        $params = array();
        if ($this->startDate) {
            $sql .= ' AND s.purchase_create_date > :startDate';
            $params['startDate'] = $this->startDate;
        }

        if ($this->stopDate) {
            $sql .= ' AND s.purchase_create_date <= :stopDate';
            $params['stopDate'] = $this->stopDate;
        }

        $dbres = $res->getConnection('core_read');
        $ptr   = $dbres->query($sql, $params);
        $data  = $ptr->fetchAll();
        array_unshift($data, $headers);
        return $data;

    }

}
