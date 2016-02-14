<?php
class EcomCore_M2Ext_M2ExtController extends Mage_Adminhtml_Controller_Action
{
    public function reportAction()
    {

        $toDate   = '';
        $fromDate = '';

        $mktplace = $this->_request->getParam('mktplace');
        if ($mktplace != 'ebay' && $mktplace != 'amazon') {
            $mktplace = 'ebay';
        }

        $report = Mage::getModel('ecomcore_m2ext/report');
        $report->marketplace = $mktplace;

		$filterParams = array();
        $session = Mage::getSingleton('adminhtml/session')->getData();
        if (isset($session['ebayOrderGridfilter'])) {
            parse_str(base64_decode($session['ebayOrderGridfilter']), $filterParams);
        }
        if (isset($filterParams['purchase_create_date'])) {
            $dateParams = $filterParams['purchase_create_date'];
            if (isset($dateParams['from'])) {
                $fromDate = str_replace('/', '-', $dateParams['from']);
            }
            if (isset($datParams['to'])) {
                $toDate = str_replace('/', '-', $dateParams['to']);
            }
        }

        $filename = 'MarketplaceOrders';
        if ($fromDate) {
            $filename .= '-'.$fromDate;
            $report->startDate = $fromDate;
        }
        if ($toDate) {
            $filename .= '-'.$toDate;
            $report->stopDate = $toDate;
        }
        $filename .= '.csv';

        $exportFile = Mage::getBaseDir('var') . DS . 'export' . DS . $filename . $_SERVER['REQUEST_TIME'];
        $fp = fopen($exportFile, 'w');
        $data = $report->run();
        foreach ($data as $line) {
            fputcsv($fp, $line);
        }
        fclose($fp);
        $this->_prepareDownloadResponse($filename, array(
            'type'  => 'filename',
            'value' => $exportFile,
            'rm'    => true
        ));

    }
}
