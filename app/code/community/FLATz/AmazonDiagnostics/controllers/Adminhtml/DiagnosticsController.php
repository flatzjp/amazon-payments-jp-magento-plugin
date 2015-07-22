<?php

/**
 * Amazon Diagnostics
 *
 * @category    Amazon
 * @package     Amazon_Diagnostics
 * @copyright   Copyright (c) 2015 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */
class Amazon_Diagnostics_Adminhtml_DiagnosticsController extends Mage_Adminhtml_Controller_Action {

    private $_basepath = "";
    private $_designpath = "";
    private $_layoutpath = "";
    private $_logpath = "";
    private $_apppath = "";
    private $_modules = array();
    private $_logs = array();
    private $_global_results = array();

    public function checkAction() {

        ini_set("auto_detect_line_endings", true);

        $this->_basepath = Mage::getBaseDir('base');
        $this->_apppath = Mage::getBaseDir('app');
        $this->_designpath = Mage::getBaseDir('design');
        $this->_logpath = Mage::getBaseDir('log');
        $this->_layoutpath = $this->_designpath . "/frontend/base/default/layout";

        /* do some diagnostics */
        $this->getMagento();
        $this->getPayments();
        $this->getLogin();
        $this->getModules();
        $this->getLogs();

        /* send the response */
        Mage::app()->getResponse()->setBody();
    }

    private function getMagento() {
        
        $this->log("===== MAGENTO =====");
        $this->log("version: ". Mage::getVersion());
        $this->log("edition: ". Mage::getEdition());
        $this->log("base_path: ". $this->_basepath);
        $this->log("secure_frontend: ". (Mage::getStoreConfig('web/secure/use_in_frontend') == 1 ? 'yes' : 'no'));
        $this->log("store_name: ". Mage::getStoreConfig('general/store_information/name'));
        if(defined('COMPILER_INCLUDE_PATH')) {
            $this->log("compilation: enabled");
        } else {
            $this->log("compilation: disabled");
        }
    }

    private function getPayments() {
        
        $this->log("\n===== PAYMENT SETTINGS =====");
        $payments_secret_key = Mage::getStoreConfig('payment/amazon_payments/access_secret');
        if (strlen($payments_secret_key) > 6) {
            $payments_secret_key = substr($payments_secret_key, 0, 3) . "..." . substr($payments_secret_key, strlen($payments_secret_key - 3), 3);
        }
        $payments_seller_id = Mage::getStoreConfig('payment/amazon_payments/seller_id');
        $payments_access_key = Mage::getStoreConfig('payment/amazon_payments/access_key');

        /* get checkout page type, make it clear which one is being used */
        $page_type = Mage::getStoreConfig('payment/amazon_payments/checkout_page');
        switch ($page_type) {
            case "amazon":
                $page_type = "amazon standalone";
                break;
            case "onepage":
                $page_type = "magento core onepage";
                break;
        }

        $enabled = Mage::getStoreConfig('payment/amazon_payments/enabled') == 1 ? 'yes' : 'no';
        
        $payments_seller_id = "'" . $payments_seller_id . "'";
        if (preg_match('/\s/', $payments_seller_id)) {
            $payments_seller_id .= " ** white space detected **";
        }
        $payments_access_key = "'" . $payments_access_key . "'";
        if (preg_match('/\s/', $payments_access_key)) {
            $payments_access_key .= " ** white space detected **";
        }
        $payments_secret_key = "'" . $payments_secret_key . "'";
        if (preg_match('/\s/', $payments_secret_key)) {
            $payments_secret_key .= "** white space detected **";
        }
        
        $payments_button_on_cart = (Mage::getStoreConfig('payment/amazon_payments/show_pay_cart') == 1 ? 'yes' : 'no');
        $payments_action = Mage::getStoreConfig('payment/amazon_payments/payment_action');
        $payments_secure_cart = (Mage::getSingleton('amazon_payments/config')->isSecureCart() == 1 ? 'yes' : 'no');
        $payments_payment_option = (Mage::getStoreConfig('payment/amazon_payments/use_in_checkout') == 1 ? 'yes' : 'no');
        $payments_async = (Mage::getStoreConfig('payment/amazon_payments/is_async') == 1 ? 'yes' : 'no');
        $payments_sandbox = (Mage::getStoreConfig('payment/amazon_payments/sandbox') == 1 ? 'yes' : 'no');
        
        $this->log("enabled: ". $enabled);
        $this->log("sandbox: ". $payments_sandbox);
        $this->log("seller_id: ". $payments_seller_id);
        $this->log("access_key: ". $payments_access_key);
        $this->log("secret_key: ". $payments_secret_key);
        $this->log("page_type: ". $page_type);
        $this->log("button_on_cart: ". $payments_button_on_cart);
        $this->log("action: ". $payments_action);
        $this->log("secure_cart: ". $payments_secure_cart);
        $this->log("payment_option: ". $payments_payment_option);
        $this->log("async: ". $payments_async);
    }

    private function getLogin() {
        
        $this->log("\n===== LOGIN SETTINGS =====");
        $login_client_id = Mage::getStoreConfig('amazon_login/settings/client_id');
        $login_client_secret = Mage::getStoreConfig('amazon_login/settings/client_secret');
        if (strlen($login_client_secret) > 6) {
            $login_client_secret = substr($login_client_secret, 0, 3) . "..." . substr($login_client_secret, strlen($login_client_secret - 3), 3);
        }

        $login_enabled = (Mage::getStoreConfig('amazon_login/settings/enabled') == 1 ? 'yes' : 'no');
        $login_button_type = Mage::getStoreConfig('amazon_login/settings/button_type');
        $login_popup = (Mage::getStoreConfig('amazon_login/settings/popup') == 1 ? 'yes' : 'no');
        $login_client_id = "'" . $login_client_id . "'";
        if (preg_match('/\s/', $login_client_id)) {
            $login_client_id = " ** white space detected **";
        }
        $login_client_secret = "'" . $login_client_secret . "'";
        if (preg_match('/\s/', $login_client_secret)) {
            $login_client_secret .= "** white space detected **";
        }
        
        $this->log("enabled: ". $login_enabled);
        $this->log("button_type: ". $login_button_type);
        $this->log("popup: ". $login_popup);
        $this->log("client_id: ". $login_client_id);
        $this->log("client_secret: ". $login_client_secret);
    }

    private function getModules() {

        $this->log("\n===== MODULES =====");
        $modules_folder = $this->_apppath . "/etc/modules";

        try {
            /* get list of modules */
            if ($h = opendir($modules_folder)) {

                /* loop through the modules */
                while (false !== ($entry = readdir($h))) {

                    /* we don't want . and .. */
                    if ($entry !== "." && $entry !== "..") {

                        /* get file extension */
                        $ext = pathinfo($modules_folder . "/" . $entry, PATHINFO_EXTENSION);

                        /* make sure it's xml */
                        if ($ext == "xml") {

                            /* load the module xml */
                            $xml = simplexml_load_file($modules_folder . "/" . $entry);

                            /* convert xml to associative array */
                            $xml = json_encode($xml);
                            $xml = json_decode($xml, true);

                            foreach ($xml['modules'] as $k => $v) {

                                /* filter out core modules and */
                                if ($v['codePool'] !== 'core') {

                                    $this->log("[". $k ."]");
                                    /* get status */
                                    $this->log("    active: ". $v['active']);

                                    /* get codepool */
                                    $this->log("    pool: ". $v['codePool']);

                                    /* parse the module config.xml */
                                    $modulepath = implode("/", explode("_", $k));
                                    $mxml = simplexml_load_file($this->_apppath . "/code/" . $v['codePool'] . "/" . $modulepath . "/etc/config.xml");

                                    /* convert to associative array */
                                    $mxml = json_encode($mxml);
                                    $mxml = json_decode($mxml, true);

                                    /* get version */
                                    $this->log("    version: ". $mxml['modules'][$k]['version']);

                                    /* get global blocks */
                                    $this->log("    [blocks]");
                                    if (isset($mxml['global']['blocks'])) {
                                        foreach ($mxml['global']['blocks'] as $mk => $mv) {
                                            $this->log("        ". $mk);
                                        }
                                    }
                                    $this->log("");
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            /* log any errors */
            $this->log("Error in module list: ". $e->getMessage());
        }
        @closedir($h);
    }

    private function getLogs() {

        try {
            echo "\n===== EXCEPTION LOG =====\n";
            /* get list of log files */
            if ($h = opendir($this->_logpath)) {

                /* loop through the files */
                while (false !== ($entry = readdir($h))) {

                    /* we don't want . and .. 
                     * modified to only check exception.log for now. will remove if needed
                     * but it felt extaneous.
                     */
                    if ($entry !== "." && $entry !== ".." && $entry == "exception.log") {

                        $amazon_lines = array(); // store occurrences of all lines containing 'amazon'
                        $tail_amount = 25;       //
                        $cnt = 0;                // keep track of current line number
                        $lines_before = 10;       // number of lines before 'amazon' to keep
                        $lines_after = 10;        // number of lines after 'amazon' to keep

                        /* get relevant portions of all log files. this is a list of all occurrences of 'amazon'
                         * including 'lines_before'/'lines_after' both before and after the line.
                         */
                        $lh = @fopen($this->_logpath . "/" . $entry, "r");
                        if ($lh) {
                            $logname = pathinfo($this->_logpath . "/" . $entry, PATHINFO_FILENAME);
                            while (($buffer = fgets($lh, 8192)) !== false) {
                                $buffer = trim($buffer);
                                if($buffer !== "") {
                                    if(stristr($buffer, 'amazon')) {
                                        if(($cnt - $lines_before) >= 0) {
                                            $amazon_lines[] = array($cnt - $lines_before, $cnt + $lines_after);
                                        } else {
                                            $amazon_lines[] = array(0, $cnt + $lines_after);
                                        }
                                    }
                                    $cnt++;
                                }
                            }
                        } else {
                            /* couldn't read the file */
                            echo "Could not read ". $logname ." log.\n";
                        }

                        $newa = array(); // new temporary array to store lines
                        foreach($amazon_lines as $k => $v) {
                            foreach(range($v[0], $v[1]) as $r) {
                                if(!in_array($r, $newa)) {
                                    $newa[$r] = 1;
                                }
                                if($r == $v[1]) {
                                    $newa[] = 0; // where to snip
                                }
                            }
                        }

                        rewind($lh);
                        
                        $cnt = 0; // reset line number
                        $relevant_lines = array();
                        while (($buffer = fgets($lh, 8192)) !== false) {
                            $buffer = trim($buffer);
                            if($buffer !== "") {
                                foreach($newa as $k => $v) {
                                    if($cnt == $k) {
                                        echo $buffer ."\n";
                                        if($v == 0) {
                                            echo "\n---------- snip ----------\n\n";
                                        }
                                    }
                                }
                                $cnt++;
                            }
                        }
                        
                        @fclose($lh);
                    }
                }
            }
        } catch (Exception $e) {
            /* log any errors */
            $this->log("Error getting logs: ". $e->getMessage());
        }
        @closedir($h);
    }
    
    private function log($s) {
        echo $s ."\n";
    }
}
