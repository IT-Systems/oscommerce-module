<?php
/**
 * SVEAWEBPAY PAYMENT MODULE FOR osCommerce
 */

// Include Svea php integration package files
require_once(DIR_FS_CATALOG . 'ext/modules/payment/svea/Includes.php');         // use new php integration package
require_once(DIR_FS_CATALOG . 'sveawebpay_config.php');     // sveaConfig implementation
require_once(DIR_FS_CATALOG . 'sveawebpay_common.php');     // osCommerce module common functions

class sveawebpay_internetbank extends SveaOsCommerce {
    function sveawebpay_internetbank() {
        global $order;

        $this->code = 'sveawebpay_internetbank';
        $this->version = "5";
   
        // used by card, directbank when posting form in checkout_confirmation.php
        $this->form_action_url = (MODULE_PAYMENT_SWPINTERNETBANK_MODE == 'Test') ? Svea\SveaConfig::SWP_TEST_URL : Svea\SveaConfig::SWP_PROD_URL;     
  
        $this->title = MODULE_PAYMENT_SWPINTERNETBANK_TEXT_TITLE;
        $this->description = MODULE_PAYMENT_SWPINTERNETBANK_TEXT_DESCRIPTION;
        $this->enabled = ((MODULE_PAYMENT_SWPINTERNETBANK_STATUS == 'True') ? true : false);
        $this->sort_order = MODULE_PAYMENT_SWPINTERNETBANK_SORT_ORDER;
        $this->display_images = ((MODULE_PAYMENT_SWPINTERNETBANK_IMAGES == 'True') ? true : false);
        $this->ignore_list = explode(',', MODULE_PAYMENT_SWPINTERNETBANK_IGNORE);
        if ((int)MODULE_PAYMENT_SWPINTERNETBANK_ORDER_STATUS_ID > 0)
            $this->order_status = MODULE_PAYMENT_SWPINTERNETBANK_ORDER_STATUS_ID;
        if (is_object($order)) $this->update_status();
    }

    function update_status() {
        global $db, $order, $currencies, $messageStack;

        // do not use this module if the geograhical zone is set and we are not in it
        if (($this->enabled == true) && ((int) MODULE_PAYMENT_SWPINTERNETBANK_ZONE > 0)) {
            $check_flag = false;
            $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . 
                " where geo_zone_id = '" . MODULE_PAYMENT_SWPINTERNETBANK_ZONE . "' and zone_country_id = '" . 
                $order->billing['country']['id'] . "' order by zone_id");

            while( $row = mysqli_fetch_assoc( $check_query ) ) {                
                if ($row['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($row['zone_id'] == $order->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
            }

            if ($check_flag == false)
                $this->enabled = false;
        }
    }

    function javascript_validation() {
        return false;
    }

    // sets information displayed when choosing between payment options
    function selection() {
        global $order, $currencies;

        // get & store country code
        if( isset($order) ) {
            $_SESSION['sveaAjaxOrderTotal'] = $order->info['total'];
            $_SESSION['sveaAjaxCountryCode'] = $order->customer['country']['iso_code_2'];
        }

        $fields = array();

        // show bank logo
        if($order->customer['country']['iso_code_2'] == "SE"){
             $fields[] = array('title' => '<img src=images/Svea/SVEADIRECTBANK_SE.png />', 'field' => '');
        }  
        else {
            $fields[] = array('title' => '<img src=images/Svea/SVEADIRECTBANK.png />', 'field' => '');
        }

        if (isset($_REQUEST['payment_error']) && $_REQUEST['payment_error'] == 'sveawebpay_internetbank') { // is set in before_process() on failed payment
            $fields[] = array('title' => '<span style="color:red">' . $_SESSION['SWP_ERROR'] . '</span>', 'field' => '');
        }

        // insert svea js
        $sveaJs =   '<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>' .
                    '<script type="text/javascript" src="' . $this->web_root . 'ext/jquery/svea/checkout/svea.js"></script>';
        $fields[] = array('title' => '', 'field' => $sveaJs);

        // customer country is taken from customer settings
        $customer_country = $order->customer['country']['iso_code_2'];

        // fill in all fields as required to show available bank payment methods for selection
        $sveaBankPaymentOptions = '<div name="sveaBankPaymentOptions" id="sveaBankPaymentOptions"></div>';

        // create and add the field to be shown by our js when we select SveaInvoice payment method
        $sveaField =    '<div id="sveaInternetbankField" >' . //style="display:none">' .
                            $sveaBankPaymentOptions .
                        '</div>';

        $fields[] = array('title' => '', 'field' => '<br />' . $sveaField);

        return array( 'id'      => $this->code,
                      'module'  => $this->title,
                      'fields'  => $fields);
    }

    function pre_confirmation_check() {
        return false;
    }

    function confirmation() {
        return false;
    }

    function process_button() {

        global $db, $order, $order_totals, $language;

        // calculate the order number
        $new_order_rs = tep_db_query("select orders_id from ".TABLE_ORDERS." order by orders_id desc limit 1");
        $new_order_field = tep_db_fetch_array($new_order_rs);
        $client_order_number = ($new_order_field['orders_id'] + 1);

        // localization parameters
        $user_country = $this->getCountry();
        $user_language = $this->getLanguage();      
        $currency = $this->getCurrency();

        // Create and initialize order object, using either test or production configuration
        $sveaConfig = (MODULE_PAYMENT_SWPINTERNETBANK_MODE === 'Test') ? new OsCommerceSveaConfigTest() : new OsCommerceSveaConfigProd();

        $swp_order = WebPay::createOrder( $sveaConfig )
            ->setCountryCode( $user_country )
            ->setCurrency($currency)                       
            ->setClientOrderNumber($client_order_number)
            ->setOrderDate(date('c'))                      
        ;

        // we use the same code as in invoice/payment plan for order totals, as coupons isn't integral to osCommerce

        // create product order rows from each item in cart
        $swp_order = $this->parseOrderProducts( $order->products, $swp_order, $this->getCurrency() );

        // creates non-item order rows from Order Total entries
        $swp_order = $this->parseOrderTotals( $this->getOrderTotals(), $swp_order );

        // localization parameters
        if( isset( $order->billing['country']['iso_code_2'] ) ) {
            $user_country = $order->billing['country']['iso_code_2']; 
        }
        // no billing address set, fallback to session country_id
        else {
            $country = tep_get_countries_with_iso_codes( $_SESSION['customer_country_id'] );
            $user_country =  $country['countries_iso_code_2'];
        }

        $payPageLanguage = "";
        switch ($user_country) {
        case "DE":
            $payPageLanguage = "de";
            break;
        case "NL":
            $payPageLanguage = "nl";
            break;
        case "SE":
            $payPageLanguage = "sv";
            break;
        case "NO":
            $payPageLanguage = "no";
            break;
        case "DK":
            $payPageLanguage = "da";
            break;
        case "FI":
            $payPageLanguage = "fi";
            break;
        default:
            $payPageLanguage = "en";
            break;
        }

        // go directly to selected bank
        $swp_form = $swp_order->usePaymentMethod( $_REQUEST['BankPaymentOptions'] )
            ->setCancelUrl( tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true) )
            ->setReturnUrl( tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL') )
            ->getPaymentForm();

        //return $process_button_string;
        return  $swp_form->htmlFormFieldsAsArray['input_merchantId'] .
                $swp_form->htmlFormFieldsAsArray['input_message'] .
                $swp_form->htmlFormFieldsAsArray['input_mac'];

    }

    function before_process() {
        global $order;

        if ($_REQUEST['response']) {

            // localization parameters
            if (isset($order->billing['country']['iso_code_2'])) {
                $user_country = $order->billing['country']['iso_code_2'];
            }
            // no billing address set, fallback to session country_id
            else {
                $country = tep_get_countries_with_iso_codes($_SESSION['customer_country_id']);
                $user_country = $country['countries_iso_code_2'];
            }

            // Create and initialize order object, using either test or production configuration
            $sveaConfig = (MODULE_PAYMENT_SWPINTERNETBANK_MODE === 'Test') ? new OsCommerceSveaConfigTest() : new OsCommerceSveaConfigProd();

            $swp_respObj = new SveaResponse($_REQUEST, $user_country, $sveaConfig); // returns HostedPaymentResponse
            $swp_response = $swp_respObj->response;

            // check for bad response
            if ($swp_response->resultcode === 0) {
                die('Response failed authorization. AC not valid or Response is not recognized');
            }

            // response ok, check if payment accepted
            else {
                
                // handle failed payments
                if ( $swp_response->accepted === 0) {
                    switch ($swp_response->resultcode) { // will autoconvert from string, matching initial numeric part
                        case 100:
                            $_SESSION['SWP_ERROR'] = sprintf("Svea error %s: %s", $swp_response->resultcode, ERROR_CODE_100);
                            break;
                        case 105:
                            $_SESSION['SWP_ERROR'] = sprintf("Svea error %s: %s", $swp_response->resultcode, ERROR_CODE_105);
                            break;
                        case 106:
                            $_SESSION['SWP_ERROR'] = sprintf("Svea error %s: %s", $swp_response->resultcode, ERROR_CODE_106);
                            break;
                        case 107:
                            $_SESSION['SWP_ERROR'] = sprintf("Svea error %s: %s", $swp_response->resultcode, ERROR_CODE_107);
                            break;
                        case 108:
                            $_SESSION['SWP_ERROR'] = sprintf("Svea error %s: %s", $swp_response->resultcode, ERROR_CODE_108);
                            break;
                        case 109:
                            $_SESSION['SWP_ERROR'] = sprintf("Svea error %s: %s", $swp_response->resultcode, ERROR_CODE_109);
                            break;
                        case 110:
                            $_SESSION['SWP_ERROR'] = sprintf("Svea error %s: %s", $swp_response->resultcode, ERROR_CODE_110);
                            break;
                        case 113:
                            $_SESSION['SWP_ERROR'] = sprintf("Svea error %s: %s", $swp_response->resultcode, ERROR_CODE_113);
                            break;
                        case 114:
                            $_SESSION['SWP_ERROR'] = sprintf("Svea error %s: %s", $swp_response->resultcode, ERROR_CODE_114);
                            break;
                        case 121:
                            $_SESSION['SWP_ERROR'] = sprintf("Svea error %s: %s", $swp_response->resultcode, ERROR_CODE_121);
                            break;
                        case 124:
                            $_SESSION['SWP_ERROR'] = sprintf("Svea error %s: %s", $swp_response->resultcode, ERROR_CODE_124);
                            break;
                        case 143:
                            $_SESSION['SWP_ERROR'] = sprintf("Svea error %s: %s", $swp_response->resultcode, ERROR_CODE_143);
                            break;
                        default:
                            $_SESSION['SWP_ERROR'] = sprintf("Svea error %s: %s", $swp_response->resultcode, ERROR_CODE_DEFAULT . $swp_response->resultcode);
                            break;
                    }
                    if (isset($_SESSION['payment_attempt'])) {  // prevents repeated payment attempts interpreted by zencart as slam attack
                        unset($_SESSION['payment_attempt']);
                    }
                    $payment_error_return = 'payment_error=sveawebpay_internetbank'; // used in conjunction w/SWP_ERROR to avoid reason showing up in url

                    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return));
                }
                // handle successful payments
                else {

                    // payment request succeded, store response in session
                    if( $swp_response->accepted === 1 ) {

                        if (isset($_SESSION['SWP_ERROR'])) {
                            unset($_SESSION['SWP_ERROR']);
                        }

                        // (with creditcard payments, shipping and billing addresses are unchanged from customer entries)
                        // save the response object
                        $_SESSION["swp_response"] = serialize($swp_response);
                    }
                }
            }
        }
    }

    // if payment accepted, insert order into database
    function after_process() {
        global $insert_id, $order;

        // retrieve response object from before_process()
        $swp_response = unserialize($_SESSION["swp_response"]);
        
        // insert  order into database
         $customer_notification = (SEND_EMAILS == 'true') ? '1' : '0';
         $sql_data_array = array(
            'orders_id' => $insert_id,
            'orders_status_id' => $order->info['order_status'], 
            'date_added' => 'now()', 
            'customer_notified' => $customer_notification,
            'comments' => 
                'Accepted by Svea ' . date("Y-m-d G:i:s") . ' Security Number #: '. $swp_response->transactionId .
                " ". $order->info['comments']
         );
         tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);


        // clean up our session variables set during checkout   //$SESSION[swp_*
        unset($_SESSION['swp_order']);
        unset($_SESSION['swp_response']);

        return false;
    }

    // sets error message to the GET error value
    function get_error() {
        return array(
            'title' => ERROR_MESSAGE_PAYMENT_FAILED,
            'error' => stripslashes(urldecode($_GET['error']))
        );
    }

    // standard check if installed function
    function check() {
        if (!isset($this->_check)) {
            $check_rs = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_SWPINTERNETBANK_STATUS'");
            $this->_check = (tep_db_num_rows($check_rs) > 0);
        }
        return $this->_check;
    }

    // insert configuration keys here
    function install() {

        $common = "insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added";
        tep_db_query($common . ", set_function) values ('Enable Svea Direct Bank Payment Module', 'MODULE_PAYMENT_SWPINTERNETBANK_STATUS', 'True', '', '6', '0', now(), 'tep_cfg_select_option(array(\'True\', \'False\'), ')");
        tep_db_query($common . ") values ('Svea Direct Bank Merchant ID', 'MODULE_PAYMENT_SWPINTERNETBANK_MERCHANT_ID', '', 'The Merchant ID', '6', '0', now())");
        tep_db_query($common . ") values ('Svea Direct Bank Secret Word', 'MODULE_PAYMENT_SWPINTERNETBANK_SW', '', 'The Secret word', '6', '0', now())");
        tep_db_query($common . ") values ('Svea Direct Bank Test Merchant ID', 'MODULE_PAYMENT_SWPINTERNETBANK_MERCHANT_ID_TEST', '', 'The Merchant ID', '6', '0', now())");
        tep_db_query($common . ") values ('Svea Direct Bank Test Secret Word', 'MODULE_PAYMENT_SWPINTERNETBANK_SW_TEST', '', 'The Secret word', '6', '0', now())");
        tep_db_query($common . ", set_function) values ('Transaction Mode', 'MODULE_PAYMENT_SWPINTERNETBANK_MODE', 'Test', 'Transaction mode used for processing orders. Production should be used for a live working cart. Test for testing.', '6', '0', now(), 'tep_cfg_select_option(array(\'Production\', \'Test\'), ')");
    tep_db_query($common . ", set_function, use_function) values ('Set Order Status', 'MODULE_PAYMENT_SWPINTERNETBANK_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', now(), 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name')");
        tep_db_query($common . ", set_function) values ('Display SveaWebPay Images', 'MODULE_PAYMENT_SWPINTERNETBANK_IMAGES', 'True', 'Do you want to display SveaWebPay images when choosing between payment options?', '6', '0', now(), 'tep_cfg_select_option(array(\'True\', \'False\'), ')");

        tep_db_query($common . ") values ('Ignore OT list', 'MODULE_PAYMENT_SWPINTERNETBANK_IGNORE','ot_pretotal', 'Ignore the following order total codes, separated by commas.','6','0',now())");
        tep_db_query($common . ", set_function, use_function) values ('Payment Zone', 'MODULE_PAYMENT_SWPINTERNETBANK_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', now(), 'tep_cfg_pull_down_zone_classes(', 'tep_get_zone_class_title')");
        tep_db_query($common . ") values ('Sort order of display.', 'MODULE_PAYMENT_SWPINTERNETBANK_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    // standard uninstall function
    function remove() {
        tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    // must perfectly match keys inserted in install function
    function keys() {
        return array(
            'MODULE_PAYMENT_SWPINTERNETBANK_STATUS',
            'MODULE_PAYMENT_SWPINTERNETBANK_MERCHANT_ID',
            'MODULE_PAYMENT_SWPINTERNETBANK_SW',
            'MODULE_PAYMENT_SWPINTERNETBANK_MERCHANT_ID_TEST',
            'MODULE_PAYMENT_SWPINTERNETBANK_SW_TEST',
            'MODULE_PAYMENT_SWPINTERNETBANK_MODE',
            'MODULE_PAYMENT_SWPINTERNETBANK_ORDER_STATUS_ID',
            'MODULE_PAYMENT_SWPINTERNETBANK_IMAGES',
            'MODULE_PAYMENT_SWPINTERNETBANK_IGNORE',
            'MODULE_PAYMENT_SWPINTERNETBANK_ZONE',
            'MODULE_PAYMENT_SWPINTERNETBANK_SORT_ORDER'
        );
    }
}
?>