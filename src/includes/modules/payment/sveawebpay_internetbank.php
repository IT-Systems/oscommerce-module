<?php
/*
HOSTED SVEAWEBPAY PAYMENT MODULE FOR OSCommerce
-----------------------------------------------
Version 4.0 - OSCommerce
*/

class sveawebpay_internetbank {

  function sveawebpay_internetbank() {
    global $order;

    $this->code = 'sveawebpay_internetbank';
    $this->version = 2;

    $_SESSION['SWP_CODE'] = $this->code;

    $this->form_action_url = (MODULE_PAYMENT_SWPINTERNETBANK_STATUS == 'True') ? 'https://test.sveaekonomi.se/webpay/payment' : 'https://webpay.sveaekonomi.se/webpay/payment';
    $this->title = MODULE_PAYMENT_SWPINTERNETBANK_TEXT_TITLE;
    $this->description = MODULE_PAYMENT_SWPINTERNETBANK_TEXT_DESCRIPTION;
    $this->enabled = ((MODULE_PAYMENT_SWPINTERNETBANK_STATUS == 'True') ? true : false);
    $this->sort_order = MODULE_PAYMENT_SWPINTERNETBANK_SORT_ORDER;
    $this->sveawebpay_url = MODULE_PAYMENT_SWPINTERNETBANK_URL;
    //$this->handling_fee = MODULE_PAYMENT_SWPINTERNETBANK_HANDLING_FEE;
    $this->default_currency = MODULE_PAYMENT_SWPINTERNETBANK_DEFAULT_CURRENCY;
    $this->allowed_currencies = explode(',', MODULE_PAYMENT_SWPINTERNETBANK_ALLOWED_CURRENCIES);
    $this->display_images = ((MODULE_PAYMENT_SWPINTERNETBANK_IMAGES == 'True') ? true : false);
    $this->ignore_list = explode(',', MODULE_PAYMENT_SWPINTERNETBANK_IGNORE);
    if ((int)MODULE_PAYMENT_SWPINTERNETBANK_ORDER_STATUS_ID > 0)
      $this->order_status = MODULE_PAYMENT_SWPINTERNETBANK_ORDER_STATUS_ID;
    if (is_object($order)) $this->update_status();
  }

  function update_status() {
    global $order, $currencies, $messageStack;

    // update internal currency
    $this->default_currency = MODULE_PAYMENT_SWPINTERNETBANK_DEFAULT_CURRENCY;
    $this->allowed_currencies = explode(',', MODULE_PAYMENT_SWPINTERNETBANK_ALLOWED_CURRENCIES);

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
    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_SWPINTERNETBANK_ZONE > 0) ) {
      $check_flag = false;
      $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_SWPINTERNETBANK_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");

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
      $fields[] = array('title' => '<img src=images/SveaWebPay-Direktbank-100px.png />', 'field' => '');

    // handling fee
    if (isset($this->handling_fee) && $this->handling_fee > 0) {
      $paymentfee_cost = $this->handling_fee;
      if (substr($paymentfee_cost, -1) == '%')
        $fields[] = array('title' => sprintf(MODULE_PAYMENT_SWPINTERNETBANK_HANDLING_APPLIES, $paymentfee_cost), 'field' => '');
      else
      {
        $tax_class = MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS;
        if (DISPLAY_PRICE_WITH_TAX == "true" && $tax_class > 0)
          $paymentfee_tax = $paymentfee_cost * tep_get_tax_rate($tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']) / 100;
        $fields[] = array('title' => sprintf(MODULE_PAYMENT_SWPINTERNETBANK_HANDLING_APPLIES, $currencies->format($paymentfee_cost+$paymentfee_tax)), 'field' => '');
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
    global $order, $language;

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


    //Import SVEA files
    include('svea/SveaConfig.php');

    //SVEA config settings
    $configSvea = SveaConfig::getConfig();
    $configSvea->merchantId = MODULE_PAYMENT_SWPINTERNETBANK_MERCHANT_ID;
    $configSvea->secret = MODULE_PAYMENT_SWPINTERNETBANK_SW;

    //Build Order rows
    $totalPrice = 0;
    $totalTax = 0;

    $paymentRequest = new SveaPaymentRequest();
    $orderSvea = new SveaOrder();
    $paymentRequest->order = $orderSvea;;

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
            //Nya rader här
            $shippingPriceExVat   = $this->convert_to_currency($_SESSION['shipping']['cost'],$currency);
            $shippingTaxRate = (string) tep_get_tax_rate($shipping->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
            $shippingTax     = ($shippingTaxRate / 100) * $shippingPriceExVat;

            $orderRow = new SveaOrderRow();
            $orderRow->amount = number_format(round($shippingPriceExVat+$shippingTax,2),2,'','');
            $orderRow->vat = number_format(round($shippingTax,2),2,'','');
            $orderRow->name = $shipping_description;
            $orderRow->quantity = 1;
            $orderRow->unit = "st";

            //Add the order rows to your order
            $orderSvea->addOrderRow($orderRow);

            //Add to totals
            $totalPrice = $totalPrice+$shippingPriceExVat+$shippingTax;
            $totalTax = $totalTax + $shippingTax;

          break;
        case 'ot_coupon':
          //Nya rader här
          $discountPrice = -$this->convert_to_currency(strip_tags($order_total['value']),$currency);

          $orderRow = new SveaOrderRow();
          $orderRow->amount = number_format(round($discountPrice,2),2,'','');
          $orderRow->vat = 0;
          $orderRow->name = strip_tags($order_total['title']);
          $orderRow->quantity = 1;
          $orderRow->unit = "st";

          //Add the order rows to your order
          $orderSvea->addOrderRow($orderRow);

          //Add to totals
          $totalPrice = $totalPrice+$discountPrice;

        break;
        // default case handles order totals like handling fee, but also
        // 'unknown' items from other plugins. Might cause problems.
        default:
          $order_total_obj = $GLOBALS[$order_total['code']];
          $tax_rate = (string) tep_get_tax_rate($order_total_obj->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          // if displayed WITH tax, REDUCE the value since it includes tax
          if (DISPLAY_PRICE_WITH_TAX == 'true')
            $order_total['value'] = (strip_tags($order_total['value']) / ((100 + $tax_rate) / 100));
            $otherTax     = ($tax_rate / 100) * $order_total['value'];

            $orderRow = new SveaOrderRow();
            $orderRow->amount = number_format(round($order_total['value']+$otherTax,2),2,'','');
            $orderRow->vat = number_format(round($otherTax,2),2,'','');
            $orderRow->name = strip_tags($order_total['title']);
            $orderRow->quantity = 1;
            $orderRow->unit = "st";

            //Add the order rows to your order
            $orderSvea->addOrderRow($orderRow);

            //Add to totals
            $totalPrice = $totalPrice+$otherPriceExVat+$otherTax;
            $totalTax = $totalTax + $otherTax;

        break;
      }
    }

    // Ordered Products
    foreach($order->products as $i => $Item) {
        //fix for using attributes and therfore using "final_price" and not "price". Add attributes on description
        $tax = ($Item['tax'] / 100) * $this->convert_to_currency($Item['final_price'],$currency);
        $price = $this->convert_to_currency($Item['final_price'],$currency) + $tax;

        $totalPrice = $totalPrice+($price * $Item['qty']);
        $totalTax = $totalTax + ($tax * $Item['qty']);

        $orderRow = new SveaOrderRow();
        $orderRow->amount = number_format(round($price,2),2,'','');
        $orderRow->vat = number_format(round($tax,2),2,'','');
        $orderRow->name = $Item['name'];
        $orderRow->quantity = $Item['qty'];
        $orderRow->sku = $Item['sku'];
        $orderRow->unit = "st";


        //Add the order rows to your order
        $orderSvea->addOrderRow($orderRow);
    }

    //Set base data for the order
    $orderSvea->amount = number_format(round($totalPrice,2),2,'','');
    $orderSvea->customerRefno = ($new_order_field['orders_id'] + 1).'-'.time();
    $orderSvea->returnUrl = tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
    $orderSvea->vat = number_format(round($totalTax,2),2,'','');
    $orderSvea->currency = $currency;
    $orderSvea->cancelurl = tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL');

    //Exclude other payments
    $paymethods = array ('CARD','SVEASPLITSE','SVEAINVOICESE');

    foreach($paymethods as $method){
        $orderSvea->excludePaymentMethod($method);
    }


    $paymentRequest->createPaymentMessage();


    $formString  = "<input type='hidden' name='merchantid' value='{$paymentRequest->merchantid}'/>";
    $formString .= "<input type='hidden' name='message' value='{$paymentRequest->payment}'/>";
    $formString .= "<input type='hidden' name='mac' value='{$paymentRequest->mac}'/>";

    //return $process_button_string;
    return $formString;
  }

  function before_process() {
    global $order, $order_totals, $language, $billto, $sendto;

    if ($_REQUEST['response']){

        //REQUESTS
        $responseSvea   = $_REQUEST['response'];
        $macSvea        = $_REQUEST['mac'];
        $merchantidSvea = $_REQUEST['merchantid'];

        //Import SVEA files
        include('svea/SveaConfig.php');

        $resp = new SveaPaymentResponse($responseSvea);

        if($resp->validateMac($macSvea,MODULE_PAYMENT_SWPINTERNETBANK_SW) == true){

            //SUCCESS
            if ($resp->statuscode == '0'){

                    $table = array (
                            'KORTABSE'      => MODULE_PAYMENT_SWPINTERNETBANK_TEXT_TITLE,
                            'KORTINDK'      => MODULE_PAYMENT_SWPINTERNETBANK_TEXT_TITLE,
                            'KORTINFI'      => MODULE_PAYMENT_SWPINTERNETBANK_TEXT_TITLE,
                            'KORTINNO'      => MODULE_PAYMENT_SWPINTERNETBANK_TEXT_TITLE,
                            'KORTINSE'      => MODULE_PAYMENT_SWPINTERNETBANK_TEXT_TITLE,
                            'NETELLER'      => MODULE_PAYMENT_SWPINTERNETBANK_TEXT_TITLE,
                            'PAYSON'        => MODULE_PAYMENT_SWPINTERNETBANK_TEXT_TITLE);

                    if(array_key_exists($_GET['PaymentMethod'], $table))
                      $order->info['payment_method'] = $table[$_GET['PaymentMethod']] . ' - ' . $_GET['PaymentMethod'];

                    // set billing and shipping address to the one of the local OsCommerce account
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

            }else{
                    //FAIL
                    $payment_error_return = 'payment_error=' . $this->code;
                      switch ($_GET['ErrorCode']) {
                          case 1:
                            $_SESSION['SWP_ERROR'] = ERROR_CODE_1;
                            break;
                          case 2:
                            $_SESSION['SWP_ERROR'] = ERROR_CODE_2;
                            break;
                          case 3:
                            $_SESSION['SWP_ERROR'] = ERROR_CODE_3;
                            break;
                          case 4:
                            $_SESSION['SWP_ERROR'] = ERROR_CODE_4;
                            break;
                          case 5:
                            $_SESSION['SWP_ERROR'] = ERROR_CODE_5;
                            break;
                          default:
                            $_SESSION['SWP_ERROR'] = ERROR_CODE_DEFAULT . $_GET['ErrorCode'];
                            break;
                      }
                      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return));
                    }


        }else{
            //MAC NOT VALID
            die('nej');
        }
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
      $check_rs = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_SWPINTERNETBANK_STATUS'");
      $this->_check = (tep_db_num_rows($check_rs) > 0);
    }
    return $this->_check;
  }

  // insert configuration keys here
  function install() {
    $common = "insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added";
    tep_db_query($common . ", set_function) values ('Enable SveaWebPay Direct Bank Module', 'MODULE_PAYMENT_SWPINTERNETBANK_STATUS', 'True', 'Do you want to accept SveaWebPay payments?', '6', '0', now(), 'tep_cfg_select_option(array(\'True\', \'False\'), ')");
    tep_db_query($common . ") values ('SveaWebPay Merchant ID', 'MODULE_PAYMENT_SWPINTERNETBANK_MERCHANT_ID', '', 'The merchant ID obtained from Svea', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Secret word', 'MODULE_PAYMENT_SWPINTERNETBANK_SW', '', 'The secret word used for hosted payment', '6', '0', now())");
    tep_db_query($common . ", set_function) values ('Transaction Mode', 'MODULE_PAYMENT_SWPINTERNETBANK_MODE', 'Test', 'Transaction mode used for processing orders. Production should be used for a live working cart. Test for testing.', '6', '0', now(), 'tep_cfg_select_option(array(\'Production\', \'Test\'), ')");
    tep_db_query($common . ") values ('Accepted Currencies', 'MODULE_PAYMENT_SWPINTERNETBANK_ALLOWED_CURRENCIES','SEK,NOK,DKK,EUR', 'The accepted currencies, separated by commas.  These <b>MUST</b> exist within your currencies table, along with the correct exchange rates.','6','0',now())");
    tep_db_query($common . ", set_function) values ('Default Currency', 'MODULE_PAYMENT_SWPINTERNETBANK_DEFAULT_CURRENCY', 'SEK', 'Default currency used, if the customer uses an unsupported currency it will be converted to this. This should also be in the supported currencies list.', '6', '0', now(), 'tep_cfg_select_option(array(\'SEK\',\'NOK\',\'DKK\',\'EUR\'), ')");
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
    return array( 'MODULE_PAYMENT_SWPINTERNETBANK_STATUS',
                  'MODULE_PAYMENT_SWPINTERNETBANK_MERCHANT_ID',
                  'MODULE_PAYMENT_SWPINTERNETBANK_SW',
                  'MODULE_PAYMENT_SWPINTERNETBANK_MODE',
                  'MODULE_PAYMENT_SWPINTERNETBANK_ALLOWED_CURRENCIES',
                  'MODULE_PAYMENT_SWPINTERNETBANK_DEFAULT_CURRENCY',
                  'MODULE_PAYMENT_SWPINTERNETBANK_ORDER_STATUS_ID',
                  'MODULE_PAYMENT_SWPINTERNETBANK_IMAGES',
                  'MODULE_PAYMENT_SWPINTERNETBANK_IGNORE',
                  'MODULE_PAYMENT_SWPINTERNETBANK_ZONE',
                  'MODULE_PAYMENT_SWPINTERNETBANK_SORT_ORDER');
  }

  function convert_to_currency($value, $currency) {
    global $currencies;

    $length = strlen($value);
    $decimal_pos = strpos($value, ".");
    $decimal_places = ($length - $decimal_pos) -1;
    $decimal_symbol = $currencies->currencies[$currency]['decimal_point'];

    // item price is ALWAYS given in internal price from the products DB, so just multiply by currency rate from currency table
    return tep_round($value * $currencies->currencies[$currency]['value'], $decimal_places);
  }
}
?>
