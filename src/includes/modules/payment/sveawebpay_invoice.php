<?php
/*
HOSTED SVEAWEBPAY PAYMENT MODULE FOR OSCommerce
-----------------------------------------------
Version 4.3b - OSCommerce
*/

class sveawebpay_invoice {

  function sveawebpay_invoice() {
    global $order;

    $this->code = 'sveawebpay_invoice';
    $this->version = 2;
    
    
    $_SESSION['SWP_CODE'] = $this->code;

    //$this->form_action_url = MODULE_PAYMENT_SWPINVOICE_URL;
    //$this->form_action_url = tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL', false);
    
    $this->title = MODULE_PAYMENT_SWPINVOICE_TEXT_TITLE;
    $this->description = MODULE_PAYMENT_SWPINVOICE_TEXT_DESCRIPTION;
    $this->enabled = ((MODULE_PAYMENT_SWPINVOICE_STATUS == 'True') ? true : false);
    $this->sort_order = MODULE_PAYMENT_SWPINVOICE_SORT_ORDER;
    //$this->sveawebpay_url = MODULE_PAYMENT_SWPINVOICE_URL;
    $this->clientno_fakt = MODULE_PAYMENT_SWPINVOICE_CLIENTNO;
    $this->handling_fee = MODULE_PAYMENT_SWPINVOICE_HANDLING_FEE;
    $this->default_currency = MODULE_PAYMENT_SWPINVOICE_DEFAULT_CURRENCY;
    $this->allowed_currencies = explode(',', MODULE_PAYMENT_SWPINVOICE_ALLOWED_CURRENCIES);
    $this->display_images = ((MODULE_PAYMENT_SWPINVOICE_IMAGES == 'True') ? true : false);
    $this->ignore_list = explode(',', MODULE_PAYMENT_SWPINVOICE_IGNORE);
    if ((int)MODULE_PAYMENT_SWPINVOICE_ORDER_STATUS_ID > 0)
      $this->order_status = MODULE_PAYMENT_SWPINVOICE_ORDER_STATUS_ID;
    if (is_object($order)) $this->update_status();
  }

  function update_status() {
    global $order, $currencies, $messageStack;

    // update internal currency
    $this->default_currency = MODULE_PAYMENT_SWPINVOICE_DEFAULT_CURRENCY;
    $this->allowed_currencies = explode(',', MODULE_PAYMENT_SWPINVOICE_ALLOWED_CURRENCIES);

    // do not use this module if any of the allowed currencies are not set in osCommerce
    foreach($this->allowed_currencies as $currency) {
      if(!is_array($currencies->currencies[strtoupper($currency)]) && ($this->enabled == true)) {
        $this->enabled = false;
        $messageStack->add('header', ERROR_ALLOWED_CURRENCIES_NOT_DEFINED, 'error');
      }
    }

    // do not use this module if the default currency is not among the allowed
    if (!in_array($this->default_currency, $this->allowed_currencies) && ($this->enabled == true)) {
      $this->enabled = false;
      $messageStack->add('header', ERROR_DEFAULT_CURRENCY_NOT_ALLOWED, 'error');
    }

    // do not use this module if the geograhical zone is set and we are not in it
    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_SWPINVOICE_ZONE > 0) ) {
      $check_flag = false;
      $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_SWPINVOICE_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");

      while ($check_fields = tep_db_fetch_array($check_query)) {
        if ($check_fields['zone_id'] < 1) {
          $check_flag = true;
          break;
        } elseif ($check_fields['zone_id'] == $order->billing['zone_id']) {
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

    $fields = array();
  
    // image
    if ($this->display_images)
    
    $fields[] = array('title' => '<img src=images/SveaWebPay-Faktura-100px.png />', 'field' => '');
    
    //Return error
    if (isset($_REQUEST['sveaError'])){
        $fields[] = array('title' => '<span style="color:red">'.$this->responseCodes($_REQUEST['sveaError']).'</span>', 'field' => '');
    }
    
    //jQuery Fix for osCommerce MS2.2
    if (PROJECT_VERSION == 'osCommerce 2.2-MS2' || PROJECT_VERSION == 'osCommerce Online Merchant v2.2 RC2a'){
        $jqueryJs = '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>';
        $fields[] = array('title' => '', 'field' => $jqueryJs);   
    }
    
    //Fields to insert/show when SWP is chosen
    $sveaJs =  '<script type="text/javascript" src="'.$this->web_root . 'ext/jquery/svea/checkout/svea.js"></script>';
    
    $fields[] = array('title' => '', 'field' => $sveaJs);
    
    $sveaIsCompany    = FORM_TEXT_COMPANY_OR_PRIVATE.' <br /><select name="sveaIsCompany" id="sveaIsCompany">
                        <option value="" selected="selected">'.FORM_TEXT_PRIVATE.'</option>
                        <option value="true">'.FORM_TEXT_COMPANY.'</option>
                        </select><br />'; 
    $sveaPnr          = FORM_TEXT_SS_NO.'<br /><input type="text" name="sveaPnr" id="sveaPnr" maxlength="11" value=""><br />';

    //For finland there is no getAdress
    if ($order->info['currency'] == 'EUR'){
        $sveaGetAdressBtn = '';
    }else{
        $sveaGetAdressBtn = '<button type="button" id="getSveaAdressInvoice" onclick="getAdress()">'.FORM_TEXT_INVOICE_GET_ADDRESS.'</button><br />'; 
    }
           
    $sveaAdressDD     = FORM_TEXT_INVOICE_ADDRESS.'<br /><select name="adressSelector_fakt" id="adressSelector_fakt" style="display:none"></select><br />';
    
    $sveaField        = '<div id="sveaFaktField" style="display:none">'.$sveaIsCompany.$sveaPnr.$sveaAdressDD.$sveaGetAdressBtn.'</div>';
             
    $fields[] = array('title' => $sveaField, 'field' => '<span id="pers_nr_error_fakt" style="color:red"></span>');
    
    
    // handling fee
    if (isset($this->handling_fee) && $this->handling_fee > 0) {
      $paymentfee_cost = $this->handling_fee;
      if (substr($paymentfee_cost, -1) == '%')
        $fields[] = array('title' => sprintf(MODULE_PAYMENT_SWPINVOICE_HANDLING_APPLIES, $paymentfee_cost), 'field' => '');
      else
      {
        $tax_class = MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS;
        if (DISPLAY_PRICE_WITH_TAX == "true" && $tax_class > 0)
          $paymentfee_tax = $paymentfee_cost * tep_get_tax_rate($tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']) / 100;
        $fields[] = array('title' => sprintf(MODULE_PAYMENT_SWPINVOICE_HANDLING_APPLIES, $currencies->format($paymentfee_cost+$paymentfee_tax)), 'field' => '');
      }
    }
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
    require('ext/modules/payment/svea/svea.php');
    
    global $order, $language;
    
    //Get the order
    $new_order_rs = tep_db_query("select orders_id from ".TABLE_ORDERS." order by orders_id desc limit 1");
    $new_order_field = tep_db_fetch_array($new_order_rs);

    // localization parameters
    $user_country = $order->billing['country']['iso_code_2'];
    $user_language = tep_db_fetch_array(tep_db_query("select code from " . TABLE_LANGUAGES . " where directory = '" . $language . "'"));
    $user_language = $user_language['code'];
    
    // switch to default currency if the customers currency is not supported
    $currency = $order->info['currency'];
    if(!in_array($currency, $this->allowed_currencies))
      $currency = $this->default_currency;
  
    
    // we'll store the generated orderid in a session variable so we can check
    // it when returning from payment gateway for security reasons:
    // Set up SSN and company
    $_SESSION['swp_orderid'] = $hosted_params['OrderId'];

    
    /*** Set up The request Array ***/
    
    // Order rows
    foreach($order->products as $productId => $product) {
    
        $orderRows = Array(
              "Description" => utf8_encode($product['name']),
              "PricePerUnit" => $this->convert_to_currency(round($product['final_price'],2),$currency),
              "NrOfUnits" => $product['qty'],
              "Unit" => "st",
              "VatPercent" => $product['tax'],
              "DiscountPercent" => 0
            );
            
        if (isset($clientInvoiceRows)){
    
            $clientInvoiceRows[$productId] = $orderRows;
        }else{
            $clientInvoiceRows[] = $orderRows;
        }

    }

    
    // handle order totals

    global $order_total_modules;
    // ugly hack to accomodate ms2.2
    foreach ($order_total_modules->modules as $phpfile) {
      $class = substr($phpfile, 0, strrpos($phpfile, '.'));
      $GLOBALS[$class]->output = array();
    }
    $order_totals = $order_total_modules->process();

    foreach($order_totals as $ot_id => $order_total) {
      $current_row++;
      switch($order_total['code']) {
        case 'ot_subtotal':
        case 'ot_total':
        case 'ot_tax':
        case in_array($order_total['code'],$this->ignore_list):
          // do nothing for these
          $current_row--;
          break;
        case 'ot_shipping':
          $shipping_code = explode('_', $_SESSION['shipping']['id']);
          $shipping = $GLOBALS[$shipping_code[0]];
          if (isset($shipping->description))
            $shipping_description = $shipping->title . ' [' . $shipping->description . ']';
          else
            $shipping_description = $shipping->title;

            $clientInvoiceRows[] = Array(
              "Description" => utf8_encode($shipping_description),
              "PricePerUnit" => $this->convert_to_currency($_SESSION['shipping']['cost'],$currency),
              "NrOfUnits" => 1,
              "VatPercent" => (string) tep_get_tax_rate($shipping->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']),
              "DiscountPercent" => 0
            );
          break;
        case 'ot_coupon':

          $clientInvoiceRows[] = Array(
              "Description" => utf8_encode(strip_tags($order_total['title'])),
              "PricePerUnit" => -$this->convert_to_currency(strip_tags($order_total['value']),$currency),
              "NrOfUnits" => 1,
              "VatPercent" => 0,
              "DiscountPercent" => 0
            );      
        break;
        // default case handles order totals like handling fee, but also
        // 'unknown' items from other plugins. Might cause problems.
        default:
          $order_total_obj = $GLOBALS[$order_total['code']];
          $tax_rate = (string) tep_get_tax_rate($order_total_obj->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          // if displayed WITH tax, REDUCE the value since it includes tax
          if (DISPLAY_PRICE_WITH_TAX == 'true')
            $order_total['value'] = (strip_tags($order_total['value']) / ((100 + $tax_rate) / 100));

            $clientInvoiceRows[] = Array(
              "Description" => utf8_encode(strip_tags($order_total['title'])),
              "PricePerUnit" => $this->convert_to_currency(strip_tags($order_total['value']),$currency),
              "NrOfUnits" => 1,
              "VatPercent" => $tax_rate,
              "DiscountPercent" => 0
            );
        break;
      }
      $i++;
    }
    
    
    //IsCompany
    $company = ($_POST['sveaIsCompany'] == 'true') ? True: false;
    
    //Get svea configuration for each country based on currency
    $sveaConf = getCountryConfigInvoice($order->info['currency']) ;
    
    //The createOrder Data
    $request = Array(
          "Auth" => Array(
            "Username" => $sveaConf['username'],
            "Password" => $sveaConf['password'],
            "ClientNumber" => $sveaConf['clientno']
           ),
          "Order" => Array(
    		"ClientOrderNr" => ($new_order_field['orders_id'] + 1).'-'.time(),
            "CountryCode" => $sveaConf['countryCode'],
            "SecurityNumber" => $_POST['sveaPnr'],
            "IsCompany" => $company,
            "OrderDate" => date(c),
    		"AddressSelector" => $_POST['adressSelector_fakt'],
            "PreApprovedCustomerId" => 0
          ),
          
          "InvoiceRows" => array('ClientInvoiceRowInfo' => $clientInvoiceRows)
        );
     
     $_SESSION['swp_fakt_request'] = $request;
     if ($this->handling_fee > 0){
        echo '
        <script type="text/javascript">
            $(".contentContainer .contentText:eq(1) table:eq(1) > tbody").append("<tr><td>'.FORM_TEXT_INVOICE_FEE.' '.$this->handling_fee.' '.$order->info['currency'].'</td></tr>");
        </script>
        ';
     }
     
    
  }

     //Error Responses
    function responseCodes($err){      
        switch ($err){
            case "CusomterCreditRejected" :
                return ERROR_CODE_1;
                break;
            case "CustomerOverCreditLimit" :
                return ERROR_CODE_2;
                break;
            case "CustomerAbuseBlock" :
                return ERROR_CODE_3;
                break;
            case "OrderExpired" :
                return ERROR_CODE_4;
                break;
            case "ClientOverCreditLimit" :
                return ERROR_CODE_5;
                break;
            case "OrderOverSveaLimit" :
                return ERROR_CODE_6;
                break;
            case "OrderOverClientLimit" :
                return ERROR_CODE_7;
                break;
            case "CustomerSveaRejected" :
                return ERROR_CODE_8;
                break;
            case "CustomerCreditNoSuchEntity" :
                return ERROR_CODE_9;
                break;
            default :
                return ERROR_CODE_DEFAULT;
                break;
            
        }
    }

  function before_process() {
            
    global $order, $order_totals, $language, $billto, $sendto;
    
    
    //Put all the data in request tag
    $data['request'] = $_SESSION['swp_fakt_request'];

   	$svea_server = (MODULE_PAYMENT_SWPINVOICE_MODE == 'Test') ? 'https://webservices.sveaekonomi.se/webpay_test/SveaWebPay.asmx?WSDL' : 'https://webservices.sveaekonomi.se/webpay/SveaWebPay.asmx?WSDL';
    
    //Call Soap
    $client = new SoapClient( $svea_server );

     //Make soap call to below method using above data
    $svea_req = $client->CreateOrder( $data );

    /*****
    Responsehandling
    ******/
     
    $response = $svea_req->CreateOrderResult->RejectionCode;
    
    
    
    // handle failed payments
    if ($response != 'Accepted') {
      $_SESSION['SWP_ERROR'] = $this->responseCodes($response);
      
      $payment_error_return = 'payment_error=' . $this->code;
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return));
    }
    
    
    // handle successful payments
    if($response == 'Accepted'){
        unset($_SESSION['swp_fakt_request']);
        $order->info['securityNumber']     = $svea_req->CreateOrderResult->SecurityNumber;
 
    }
      
    if (isset($svea_req->CreateOrderResult->LegalName)) {
      $name = explode(',',$svea_req->CreateOrderResult->LegalName); 
        
      $order->billing['firstname']       = $name[1];
      $order->billing['lastname']        = $name[0];
      $order->billing['street_address']  = $svea_req->CreateOrderResult->AddressLine1;
      $order->billing['suburb']          = $svea_req->CreateOrderResult->AddressLine2;
      $order->billing['city']            = $svea_req->CreateOrderResult->Postarea;
      $order->billing['state']           = '';                    // "state" is not applicable in SWP countries
      $order->billing['postcode']        = $svea_req->CreateOrderResult->Postcode;
    
      $order->delivery['firstname']      = $name[1];
      $order->delivery['lastname']       = $name[0];
      $order->delivery['street_address'] = $svea_req->CreateOrderResult->AddressLine1;
      $order->delivery['suburb']         = $svea_req->CreateOrderResult->AddressLine2;
      $order->delivery['city']           = $svea_req->CreateOrderResult->Postarea;
      $order->delivery['state']          = '';                    // "state" is not applicable in SWP countries
      $order->delivery['postcode']       = $svea_req->CreateOrderResult->Postcode;
    }

    $table = array (
                'INVOICE'       => MODULE_PAYMENT_SWPINVOICE_TEXT_TITLE,
                'INVOICESE'     => MODULE_PAYMENT_SWPINVOICE_TEXT_TITLE);

    if(array_key_exists($_GET['PaymentMethod'], $table))
      $order->info['payment_method'] = $table[$_GET['PaymentMethod']];
    
    // set billing and shipping address to the one fetched from Svea hosted page instead of the local OsCommerce account
    $firstname      =     $order->billing['firstname'];
    $lastname       =     $order->billing['lastname'];    
    $street_address =     $order->billing['street_address'];
    $suburb         =     $order->billing['suburb'];
    $city           =     $order->billing['city'];        
    $postcode       =     $order->billing['postcode'];
    $country        =     $order->billing['country']['id'];
    
    // let's check if the address is already stored in the OsCommerce address book (not a first time user)
    $customer_id = $_SESSION['customer_id'];
    $query = tep_db_query("select address_book_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customer_id . "' and entry_firstname = '$firstname' and entry_lastname = '$lastname' and entry_street_address = '$street_address' and entry_postcode = '$postcode'");
    $address = tep_db_fetch_array($query);
     
    // first time user; address wasn't found, let's insert it into the database for future use
    if (!$address) {
      $query = mysql_query("insert into " . TABLE_ADDRESS_BOOK . " values(NULL, '$customer_id', 'm', ' ', '$firstname', '$lastname', '$street_address', '$suburb', '$postcode', '$city', NULL, '$country', '0')");
      $billto = mysql_insert_id();
      $sendto = mysql_insert_id();
    }
    // address was found in address book, let's use it for shipping/billing for this order.
    else {
      $billto = $address['address_book_id'];
      $sendto = $address['address_book_id'];
    }
    
  }

  // if payment accepted, insert order into database
  function after_process() {
    global $insert_id, $order;

    $sql_data_array = array(  'orders_id'         => $insert_id,
                              'orders_status_id'  => $order->info['order_status'],
                              'date_added'        => 'now()',
                              'customer_notified' => 0,
                              'comments'          => 'Accepted by SveaWebPay '.date("Y-m-d G:i:s") .' Security Number #: '.$order->info['securityNumber']);
    tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
    
   
    return false;
  }

  // sets error message to the session error value
  function get_error() {
    $error_text['title'] = ERROR_MESSAGE_PAYMENT_FAILED;
    
    if($_SESSION['SWP_ERROR'])
      $error_text['error'] = $_SESSION['SWP_ERROR'];
    else
      $error_text['error'] = "Unexpected error during payment"; // if session variable was not found, normally this shouldn't happen 
      
    return $error_text;
                 
  }

  // standard check if installed function
  function check() {
    global $db;
    if (!isset($this->_check)) {
      $check_rs = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_SWPINVOICE_STATUS'");
      $this->_check = (tep_db_num_rows($check_rs) > 0);
    }
    return $this->_check;
  }

  // insert configuration keys here
  function install() {
    $common = "insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added";
    tep_db_query($common . ", set_function) values ('Enable SveaWebPay Invoice Module', 'MODULE_PAYMENT_SWPINVOICE_STATUS', 'True', 'Do you want to accept SveaWebPay payments?', '6', '0', now(), 'tep_cfg_select_option(array(\'True\', \'False\'), ')");
    tep_db_query($common . ") values ('SveaWebPay Username SV', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_SV', 'Testinstallation', 'Username for SveaWebPay Invoice Sweden', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Password SV', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_SV', 'Testinstallation', 'Password for SveaWebPay Invoice Sweden', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Username NO', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_NO', 'webpay_test_no', 'Username for SveaWebPay Invoice Norway', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Password NO', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_NO', 'dvn349hvs9+29hvs', 'Password for SveaWebPay Invoice Norway', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Username FI', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_FI', 'finlandtest', 'Username for SveaWebPay Invoice Finland', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Password FI', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_FI', 'finlandtest', 'Password for SveaWebPay Invoice Finland', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Username DK', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_DK', 'danmarktest', 'Username for SveaWebPay Invoice Denmark', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Password DK', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_DK', 'danmarktest', 'Password for SveaWebPay Invoice Denmark', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Client no SV', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_SV', '75021', '', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Client no NO', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_NO', '32666', '', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Client no FI', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_FI', '29995', '', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Client no DK', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_DK', '60006', '', '6', '0', now())");
    tep_db_query($common . ", set_function) values ('Transaction Mode', 'MODULE_PAYMENT_SWPINVOICE_MODE', 'Test', 'Transaction mode used for processing orders. Production should be used for a live working cart. Test for testing.', '6', '0', now(), 'tep_cfg_select_option(array(\'Production\', \'Test\'), ')");
    tep_db_query($common . ") values ('Handling Fee', 'MODULE_PAYMENT_SWPINVOICE_HANDLING_FEE', '', 'This handling fee will be applied to all orders using this payment method.  The figure can either be set to a specific amount eg <b>5.00</b>, or set to a percentage of the order total, by ensuring the last character is a \'%\' eg <b>5.00%</b>.', '6', '0', now())");
    tep_db_query($common . ") values ('Accepted Currencies', 'MODULE_PAYMENT_SWPINVOICE_ALLOWED_CURRENCIES','SEK,NOK,DKK,EUR', 'The accepted currencies, separated by commas.  These <b>MUST</b> exist within your currencies table, along with the correct exchange rates.','6','0',now())");
    tep_db_query($common . ", set_function) values ('Default Currency', 'MODULE_PAYMENT_SWPINVOICE_DEFAULT_CURRENCY', 'SEK', 'Default currency used, if the customer uses an unsupported currency it will be converted to this. This should also be in the supported currencies list.', '6', '0', now(), 'tep_cfg_select_option(array(\'SEK\',\'NOK\',\'DKK\',\'EUR\'), ')");
    tep_db_query($common . ", set_function, use_function) values ('Set Order Status', 'MODULE_PAYMENT_SWPINVOICE_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', now(), 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name')");
    tep_db_query($common . ", set_function) values ('Display SveaWebPay Images', 'MODULE_PAYMENT_SWPINVOICE_IMAGES', 'True', 'Do you want to display SveaWebPay images when choosing between payment options?', '6', '0', now(), 'tep_cfg_select_option(array(\'True\', \'False\'), ')");
    tep_db_query($common . ") values ('Ignore OT list', 'MODULE_PAYMENT_SWPINVOICE_IGNORE','ot_pretotal', 'Ignore the following order total codes, separated by commas.','6','0',now())");
    tep_db_query($common . ", set_function, use_function) values ('Payment Zone', 'MODULE_PAYMENT_SWPINVOICE_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', now(), 'tep_cfg_pull_down_zone_classes(', 'tep_get_zone_class_title')");
    tep_db_query($common . ") values ('Sort order of display.', 'MODULE_PAYMENT_SWPINVOICE_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
  }

  // standard uninstall function
  function remove() {
    tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
  }

  // must perfectly match keys inserted in install function
  function keys() {
    return array( 'MODULE_PAYMENT_SWPINVOICE_STATUS',
                  'MODULE_PAYMENT_SWPINVOICE_USERNAME_SV',
                  'MODULE_PAYMENT_SWPINVOICE_PASSWORD_SV',
                  'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_SV',
                  'MODULE_PAYMENT_SWPINVOICE_USERNAME_NO',
                  'MODULE_PAYMENT_SWPINVOICE_PASSWORD_NO',
                  'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_NO',
                  'MODULE_PAYMENT_SWPINVOICE_USERNAME_FI',
                  'MODULE_PAYMENT_SWPINVOICE_PASSWORD_FI',
                  'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_FI',
                  'MODULE_PAYMENT_SWPINVOICE_USERNAME_DK',
                  'MODULE_PAYMENT_SWPINVOICE_PASSWORD_DK',
                  'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_DK',
                  'MODULE_PAYMENT_SWPINVOICE_MODE',
                  'MODULE_PAYMENT_SWPINVOICE_HANDLING_FEE',
                  'MODULE_PAYMENT_SWPINVOICE_ALLOWED_CURRENCIES',
                  'MODULE_PAYMENT_SWPINVOICE_DEFAULT_CURRENCY',
                  'MODULE_PAYMENT_SWPINVOICE_ORDER_STATUS_ID',
                  'MODULE_PAYMENT_SWPINVOICE_IMAGES',
                  'MODULE_PAYMENT_SWPINVOICE_IGNORE',
                  'MODULE_PAYMENT_SWPINVOICE_ZONE',
                  'MODULE_PAYMENT_SWPINVOICE_SORT_ORDER');
  }

  function convert_to_currency($value, $currency) {
    global $currencies;
    
    $length = strlen($value);
    $decimal_pos = strpos($value, ".");
    $decimal_places = ($length - $decimal_pos) -1;    
    $decimal_symbol = $currencies->currencies[$currency]['decimal_point'];
    
    // item price is ALWAYS given in internal price from the products DB, so just multiply by currency rate from currency table
    return number_format(tep_round($value * $currencies->currencies[$currency]['value'], $decimal_places), 2, $decimal_symbol, '');
  }
}
?>
