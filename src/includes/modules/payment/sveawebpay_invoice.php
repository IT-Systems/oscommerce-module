<?php
/**
 * SVEAWEBPAY PAYMENT MODULE FOR osCommerce
 */

// Include Svea php integration package files
require_once(DIR_FS_CATALOG . 'ext/modules/payment/svea/Includes.php');         // use new php integration package for v4
require_once(DIR_FS_CATALOG . 'sveawebpay_config.php');     // sveaConfig implementation
require_once(DIR_FS_CATALOG . 'sveawebpay_common.php');     // osCommerce module common functions

class sveawebpay_invoice extends SveaOsCommerce {

  function sveawebpay_invoice() {
    global $order;

    $this->code = 'sveawebpay_invoice';
    $this->version = 5;                     // 2014, uses php integration package

    $this->title = MODULE_PAYMENT_SWPINVOICE_TEXT_TITLE;
    $this->description = MODULE_PAYMENT_SWPINVOICE_TEXT_DESCRIPTION;
    $this->enabled = ((MODULE_PAYMENT_SWPINVOICE_STATUS == 'True') ? true : false);
//    $this->sort_order = MODULE_PAYMENT_SWPINVOICE_SORT_ORDER;
//    //$this->sveawebpay_url = MODULE_PAYMENT_SWPINVOICE_URL;
//    $this->clientno_fakt = MODULE_PAYMENT_SWPINVOICE_CLIENTNO;
//    $this->handling_fee = MODULE_PAYMENT_SWPINVOICE_HANDLING_FEE;
    $this->default_currency = MODULE_PAYMENT_SWPINVOICE_DEFAULT_CURRENCY;
    $this->allowed_currencies = explode(',', MODULE_PAYMENT_SWPINVOICE_ALLOWED_CURRENCIES);
    $this->display_images = ((MODULE_PAYMENT_SWPINVOICE_IMAGES == 'True') ? true : false);
    $this->ignore_list = explode(',', MODULE_PAYMENT_SWPINVOICE_IGNORE);
//    if ((int)MODULE_PAYMENT_SWPINVOICE_ORDER_STATUS_ID > 0)
//      $this->order_status = MODULE_PAYMENT_SWPINVOICE_ORDER_STATUS_ID;
//    if (is_object($order)) $this->update_status();
  }

//  function update_status() {
//    global $order, $currencies, $messageStack;
//
//    // update internal currency
//    $this->default_currency = MODULE_PAYMENT_SWPINVOICE_DEFAULT_CURRENCY;
//    $this->allowed_currencies = explode(',', MODULE_PAYMENT_SWPINVOICE_ALLOWED_CURRENCIES);
//
//    // do not use this module if any of the allowed currencies are not set in osCommerce
//    foreach($this->allowed_currencies as $currency) {
//      if(!is_array($currencies->currencies[strtoupper($currency)]) && ($this->enabled == true)) {
//        $this->enabled = false;
//        $messageStack->add('header', ERROR_ALLOWED_CURRENCIES_NOT_DEFINED, 'error');
//      }
//    }
//
//    // do not use this module if the default currency is not among the allowed
//    if (!in_array($this->default_currency, $this->allowed_currencies) && ($this->enabled == true)) {
//      $this->enabled = false;
//      $messageStack->add('header', ERROR_DEFAULT_CURRENCY_NOT_ALLOWED, 'error');
//    }
//
//    // do not use this module if the geograhical zone is set and we are not in it
//    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_SWPINVOICE_ZONE > 0) ) {
//      $check_flag = false;
//      $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_SWPINVOICE_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
//
//      while ($check_fields = tep_db_fetch_array($check_query)) {
//        if ($check_fields['zone_id'] < 1) {
//          $check_flag = true;
//          break;
//        } elseif ($check_fields['zone_id'] == $order->billing['zone_id']) {
//          $check_flag = true;
//          break;
//        }
//      }
//
//      if ($check_flag == false)
//        $this->enabled = false;
//    }
//  }
//
  
  /**
   * called at start of checkout_payment.php
   * @return boolean
   */
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

        // TODO error handling

        // insert svea js
        $sveaJs =   '<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>' .
                    '<script type="text/javascript" src="' . $this->web_root . 'ext/jquery/svea/checkout/svea.js"></script>';
        $fields[] = array('title' => '', 'field' => $sveaJs);

        // get required fields depending on customer country and payment method

        // customer country is taken from customer settings
        $customer_country = $order->customer['country']['iso_code_2'];

        // fill in all fields as required by customer country and payment method
        $sveaAddressDD = $sveaInitialsDiv = $sveaBirthDateDiv  = '';

        // get ssn & selects private/company for SE, NO, DK, FI
        if( ($customer_country == 'SE') ||     // e.g. == 'SE'
            ($customer_country == 'NO') ||
            ($customer_country == 'DK') )
        {
            // input text field for individual/company SSN
            $sveaSSN =          FORM_TEXT_SS_NO . '<br /><input type="text" name="sveaSSN" id="sveaSSN" maxlength="11" /><br />';
        }

        if( ($customer_country == 'FI') )
        {
           // input text field for individual/company SSN, without getAddresses hook
            $sveaSSNFI =        FORM_TEXT_SS_NO . '<br /><input type="text" name="sveaSSNFI" id="sveaSSNFI" maxlength="11" /><br />';
        }

        // radiobutton for choosing individual or organization
        $sveaIsCompanyField =
                            '<label><input type="radio" name="sveaIsCompany" value="false" checked>' . FORM_TEXT_PRIVATE . '</label>' .
                            '<label><input type="radio" name="sveaIsCompany" value="true">' . FORM_TEXT_COMPANY . '</label><br />';

        // these are the countries we support getAddress in (getAddress also depends on sveaSSN being present)
        if( ($customer_country == 'SE') ||
            ($customer_country == 'NO') ||
            ($customer_country == 'DK') )
        {
            $sveaAddressDD =    '<br /><label for ="sveaAddressSelector" style="display:none">' . FORM_TEXT_INVOICE_ADDRESS . '</label><br />' .
                                '<select name="sveaAddressSelector" id="sveaAddressSelector" style="display:none"></select><br />';
        }

        // if customer is located in Netherlands, get initials
        if( $customer_country == 'NL' ) {

            $sveaInitialsDiv =  '<div id="sveaInitials_div" >' .
                                    '<label for="sveaInitials">' . FORM_TEXT_INITIALS . '</label><br />' .
                                    '<input type="text" name="sveaInitials" id="sveaInitials" maxlength="5" />' .
                                '</div><br />';
        }

        // if customer is located in Netherlands or DE, get birth date
        if( ($customer_country == 'NL') ||
            ($customer_country == 'DE') )
        {
            //Years from 1913 to date('Y')
            $years = '';
            for($y = 1913; $y <= date('Y'); $y++){
                $selected = "";
                if( $y == (date('Y')-30) )      // selected is backdated 30 years
                    $selected = "selected";

                $years .= "<option value='$y' $selected>$y</option>";
            }
            $birthYear = "<select name='sveaBirthYear' id='sveaBirthYear'>$years</select>";

            //Months to 12
            $months = "";
            for($m = 1; $m <= 12; $m++){
                $val = $m;
                if($m < 10)
                    $val = "$m";

                $months .= "<option value='$val'>$m</option>";
            }
            $birthMonth = "<select name='sveaBirthMonth' id='sveaBirthMonth'>$months</select>";
            
            //Days, to 31
            $days = "";
            for($d = 1; $d <= 31; $d++){

                $val = $d;
                if($d < 10)
                    $val = "$d";

                $days .= "<option value='$val'>$d</option>";
            }
            $birthDay = "<select name='sveaBirthDay' id='sveaBirthDay'>$days</select>";

            
            $sveaBirthDateDiv = '<div id="sveaBirthDate_div" >' .
                                    //'<label for="sveaBirthDate">' . FORM_TEXT_BIRTHDATE . '</label><br />' .
                                    //'<input type="text" name="sveaBirthDate" id="sveaBirthDate" maxlength="8" />' .
                                    '<label for="sveaBirthYear">' . FORM_TEXT_BIRTHDATE . '</label><br />' .
                                    $birthYear . $birthMonth . $birthDay .
                                '</div><br />';

            $sveaVatNoDiv = '<div id="sveaVatNo_div" hidden="true">' .
                                    '<label for="sveaVatNo" >' . FORM_TEXT_VATNO . '</label><br />' .
                                    '<input type="text" name="sveaVatNo" id="sveaVatNo" maxlength="14" />' .
                                '</div><br />';
        }
        
        // TODO add handling fee here
        
        $sveaError = '<br /><span id="sveaSSN_error_invoice" style="color:red"></span>';

        // create and add the field to be shown by our js when we select SveaInvoice payment method
        $sveaField =    '<div id="sveaInvoiceField" style="display:none">' .
                            $sveaIsCompanyField .   //  SE, DK, NO
                            $sveaSSN .              //  SE, DK, NO
                            $sveaSSNFI .            //  FI, no getAddresses
                            $sveaSubmitAddress.
                            $sveaAddressDD .        //  SE, Dk, NO
                            $sveaInitialsDiv .      //  NL
                            $sveaBirthDateDiv .     //  NL, DE
                            $sveaVatNoDiv .         // NL, DE
                            $sveaHandlingFee .
                            // FI, NL, DE also uses customer address data from osCommerce
                        '</div>';

        $fields[] = array('title' => '', 'field' => '<br />' . $sveaField . $sveaError);

        // TODO check this -- $_SESSION["swp_order_info_pre_coupon"]  = serialize($order->info);  // store order info needed to reconstruct amount pre coupon later
                
        if(     $order->billing['country']['iso_code_2'] == "SE" ||
                $order->billing['country']['iso_code_2'] == "DK" ||
                $order->billing['country']['iso_code_2'] == "NO" )      // but don't show button/do getAddress unless customer is company!
        {
             $sveaSubmitAddress = '<button id="sveaSubmitGetAddress" type="button">'.FORM_TEXT_GET_ADDRESS.'</button>';
        }

        return array( 'id'      => $this->code,
                      'module'  => $this->title,
                      'fields'  => $fields
        );
    }

    /**
     * called at start of checkout_confirmation.php
     * @return boolean
     */
  function pre_confirmation_check() {
//      print_r( $_POST );      
    return false;
  }

  function confirmation() {
    return false;
  }
  
  
    /** process_button() is called from tpl_checkout_confirmation.php in
     *  includes/templates/template_default/templates when we press the
     *  continue checkout button after having selected payment method and
     *  entered required payment method input.
     *
     *  Here we prepare to populate the order object by creating the
     *  WebPayItem::orderRow objects that make up the order.
     */
    function process_button() {

        global $db, $order, $order_totals, $language;

        // handle postback of payment method info fields, if present
        $post_sveaSSN = isset($_POST['sveaSSN']) ? $_POST['sveaSSN'] : "swp_not_set" ;
        $post_sveaSSNFI = isset($_POST['sveaSSNFI']) ? $_POST['sveaSSNFI'] : "swp_not_set" ;
        $post_sveaIsCompany = isset($_POST['sveaIsCompany']) ? $_POST['sveaIsCompany'] : "swp_not_set" ;
        $post_sveaAddressSelector = isset($_POST['sveaAddressSelector']) ? $_POST['sveaAddressSelector'] : "swp_not_set";
        $post_sveaVatNo = isset($_POST['sveaVatNo']) ? $_POST['sveaVatNo'] : "swp_not_set";
        $post_sveaBirthDay = isset($_POST['sveaBirthDay']) ? $_POST['sveaBirthDay'] : "swp_not_set";
        $post_sveaBirthMonth = isset($_POST['sveaBirthMonth']) ? $_POST['sveaBirthMonth'] : "swp_not_set";
        $post_sveaBirthYear = isset($_POST['sveaBirthYear']) ? $_POST['sveaBirthYear'] : "swp_not_set";
        $post_sveaInitials = isset($_POST['sveaInitials']) ? $_POST['sveaInitials'] : "swp_not_set" ;

        // calculate the order number
        $new_order_rs = tep_db_query("select orders_id from ".TABLE_ORDERS." order by orders_id desc limit 1");
        $new_order_field = tep_db_fetch_array($new_order_rs);
        $client_order_number = ($new_order_field['orders_id'] + 1);

//print_r( $client_order_number); die;
        
        // localization parameters
        if( isset( $order->billing['country']['iso_code_2'] ) ) {
            $user_country = $order->billing['country']['iso_code_2']; 
        }
        // no billing address set, fallback to session country_id
        else {
            $country = zen_get_countries_with_iso_codes( $_SESSION['customer_country_id'] );
            $user_country =  $country['countries_iso_code_2'];
        }
//print_r( $user_country ); die;
       
    $user_language = tep_db_fetch_array(tep_db_query("select code from " . TABLE_LANGUAGES . " where directory = '" . $language . "'"));
    $user_language = $user_language['code'];
//print_r( $user_language );
      
        $currency = $this->getCurrency($order->info['currency']);
//print_r( $currency ); die;
        
        // Create and initialize order object, using either test or production configuration
        $sveaConfig = (MODULE_PAYMENT_SWPINVOICE_MODE === 'Test') ? new OsCommerceSveaConfigTest() : new OsCommerceSveaConfigProd();

        $swp_order = WebPay::createOrder( $sveaConfig )
            ->setCountryCode( $user_country )
            ->setCurrency($currency)                       //Required for card & direct payment and PayPage payment.
            ->setClientOrderNumber($client_order_number)   //Required for card & direct payment, PaymentMethod payment and PayPage payments
            ->setOrderDate(date('c'))                      //Required for synchronous payments
        ;
//print_r( $swp_order ); die;

//print_r( $order); die;

        // for each item in cart, create WebPayItem::orderRow objects and add to order
        foreach ($order->products as $productId => $product) {

            $amount_ex_vat = floatval( /*$this->convertToCurrency(*/round($product['final_price'], 2)/*, $currency)*/ );
         
            $swp_order->addOrderRow(
                    WebPayItem::orderRow()
                            ->setQuantity($product['qty'])          //Required
                            ->setAmountExVat($amount_ex_vat)          //Optional, see info above
                            ->setVatPercent(intval($product['tax']))  //Optional, see info above
                            ->setDescription($product['name'])        //Optional
           );
        }

        // get order totals in parseable format
        $order_totals = $this->getOrderTotals();
    
        // creates non-item order rows from Order Total entries
        $swp_order = $this->parseOrderTotals( $order_totals, $swp_order );

        // Check if customer is company
        if( $post_sveaIsCompany === 'true')
        {
            // create company customer object
            $swp_customer = WebPayItem::companyCustomer();
           
            // set company name
            $swp_customer->setCompanyName( $order->billing['company'] );

            // set company SSN
            if( ($user_country == 'SE') ||
                ($user_country == 'NO') ||
                ($user_country == 'DK') )
            {
                $swp_customer->setNationalIdNumber( $post_sveaSSN );
            }
            if( ($user_country == 'FI') )
            {
                $swp_customer->setNationalIdNumber( $post_sveaSSNFI );
            }

            // set addressSelector from getAddresses
            if( ($user_country == 'SE') ||
                ($user_country == 'NO') ||
                ($user_country == 'DK') )
            {
                $swp_customer->setAddressSelector( $post_sveaAddressSelector );
            }

            // set vatNo
            if( ($user_country == 'NL') ||
                ($user_country == 'DE') )
            {
                $swp_customer->setVatNumber( $post_sveaVatNo );
            }

            // set housenumber
            if( ($user_country == 'NL') ||
                ($user_country == 'DE') )
            {
                $myStreetAddress = Svea\Helper::splitStreetAddress( $order->billing['street_address'] ); // Split street address and house no
            }
            else // other countries disregard housenumber field, so put entire address in streetname field     
            {
                $myStreetAddress[0] = $order->billing['street_address'];
                $myStreetAddress[1] = $order->billing['street_address'];
                $myStreetAddress[2] = "";
            }
            
            // set common fields
            $swp_customer
                ->setStreetAddress( $myStreetAddress[1], $myStreetAddress[2] )
                ->setZipCode($order->billing['postcode'])
                ->setLocality($order->billing['city'])
                ->setEmail($order->customer['email_address'])
                ->setIpAddress($_SERVER['REMOTE_ADDR'])
                ->setCoAddress($order->billing['suburb'])                       // c/o address
                ->setPhoneNumber($order->customer['telephone']);

            // add customer to order
            $swp_order->addCustomerDetails($swp_customer);
        }
        else    // customer is private individual
        {
            // create individual customer object
            $swp_customer = WebPayItem::individualCustomer();

            // set individual customer name
            $swp_customer->setName( $order->billing['firstname'], $order->billing['lastname'] );

            // set individual customer SSN
            if( ($user_country == 'SE') ||
                ($user_country == 'NO') ||
                ($user_country == 'DK') )
            {
                $swp_customer->setNationalIdNumber( $post_sveaSSN );
            }
            if( ($user_country == 'FI') )
            {
                $swp_customer->setNationalIdNumber( $post_sveaSSNFI );
            }

            // set BirthDate if required
            if( ($user_country == 'NL') ||
                ($user_country == 'DE') )
            {
                $swp_customer->setBirthDate(intval($post_sveaBirthYear), intval($post_sveaBirthMonth), intval($post_sveaBirthDay));
            }

            // set initials if required
            if( ($user_country == 'NL') )
            {
                $swp_customer->setInitials($post_sveaInitials);
            }

            // set housenumber
            if( ($user_country == 'NL') ||
                ($user_country == 'DE') )
            {
                $myStreetAddress = Svea\Helper::splitStreetAddress( $order->billing['street_address'] ); // Split street address and house no
            }
            else // other countries disregard housenumber field, so put entire address in streetname field     
            {
                $myStreetAddress[0] = $order->billing['street_address'];
                $myStreetAddress[1] = $order->billing['street_address'];
                $myStreetAddress[2] = "";
            }

            // set common fields
            $swp_customer
                ->setStreetAddress( $myStreetAddress[1], $myStreetAddress[2] )  // street, housenumber
                ->setZipCode($order->billing['postcode'])
                ->setLocality($order->billing['city'])
                ->setEmail($order->customer['email_address'])
                ->setIpAddress($_SERVER['REMOTE_ADDR'])
                ->setCoAddress($order->billing['suburb'])                       // c/o address
                ->setPhoneNumber($order->customer['telephone'])
            ;

            // add customer to order
            $swp_order->addCustomerDetails($swp_customer);
        }

        // store our order object in session, to be retrieved in before_process()
        $_SESSION["swp_order"] = serialize($swp_order);

          
//print_r( $swp_order ); die;
        
        // we're done here
        return false;
    }
  
// from ZC
    /**
     * before_process is called from modules/checkout_process.
     * It instantiates and populates a WebPay::createOrder object
     * as well as sends the actual payment request
     */
    function before_process() {
        global $order, $order_totals, $language, $billto, $sendto;

        // retrieve order object set in process_button()
        $swp_order = unserialize($_SESSION["swp_order"]);

//        print_r( $swp_order->useInvoicePayment()->prepareRequest() ); die;
//        
        // send payment request to svea, receive response       
        $swp_response = $swp_order->useInvoicePayment()->doRequest();

        // payment request failed; handle this by redirecting w/result code as error message
        if ($swp_response->accepted === false) {
            $_SESSION['SWP_ERROR'] = $this->responseCodes($swp_response->resultcode,$swp_response->errormessage);
            $payment_error_return = 'payment_error=sveawebpay_invoice';
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return)); // error handled in selection() above
        }
     
        //
        // payment request succeded, store response in session
        if ($swp_response->accepted == true) {

            if (isset($_SESSION['SWP_ERROR'])) {
                unset($_SESSION['SWP_ERROR']);
            }

            // set zencart billing address to invoice address from payment request response

            // is private individual?
            if( $swp_response->customerIdentity->customerType == "Individual") {
                $order->billing['firstname'] = $swp_response->customerIdentity->fullName; // workaround for zen_address_format not showing 'name' in order information view/
                $order->billing['lastname'] = "";
                $order->billing['company'] = "";
            }
            else {
                $order->billing['company'] = $swp_response->customerIdentity->fullName;
            }
            $order->billing['street_address'] =
                    $swp_response->customerIdentity->street . " " . $swp_response->customerIdentity->houseNumber;
            $order->billing['suburb'] =  $swp_response->customerIdentity->coAddress;
            $order->billing['city'] = $swp_response->customerIdentity->locality;
            $order->billing['postcode'] = $swp_response->customerIdentity->zipCode;
            $order->billing['state'] = '';  // "state" is not applicable in SWP countries

            $order->billing['country']['title'] =                                           // country name only needed for address
                    $this->getCountryName( $swp_response->customerIdentity->countryCode );

            // save the response object
            $_SESSION["swp_response"] = serialize($swp_response);
            
//print_r( $swp_response ); die;           
        }
    }
    
    
//  OLD osC    
//  function before_process() {
//
//    global $order, $order_totals, $language, $billto, $sendto;
//
//
//
//    /*****
//    Responsehandling
//    ******/
//        
//    // handle failed payments
//    if ($response != 'Accepted') {
//
//      $_SESSION['SWP_ERROR'] = $this->responseCodes($response,$errormessage);        
//      $payment_error_return = 'payment_error=' . $this->code;
//      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return));
//    }
//
//
//    // handle successful payments
//    if($response == 'Accepted'){
//        unset($_SESSION['swp_fakt_request']);
//        $order->info['securityNumber']     = $svea_req->CreateOrderResult->SecurityNumber;
//
//    }
//
//    if (isset($svea_req->CreateOrderResult->LegalName)) {
//      $name = explode(',',$svea_req->CreateOrderResult->LegalName);
//
//      $order->billing['firstname']       = $name[1];
//      $order->billing['lastname']        = $name[0];
//      $order->billing['street_address']  = $svea_req->CreateOrderResult->AddressLine1;
//      $order->billing['suburb']          = $svea_req->CreateOrderResult->AddressLine2;
//      $order->billing['city']            = $svea_req->CreateOrderResult->Postarea;
//      $order->billing['state']           = '';                    // "state" is not applicable in SWP countries
//      $order->billing['postcode']        = $svea_req->CreateOrderResult->Postcode;
//
//      $order->delivery['firstname']      = $name[1];
//      $order->delivery['lastname']       = $name[0];
//      $order->delivery['street_address'] = $svea_req->CreateOrderResult->AddressLine1;
//      $order->delivery['suburb']         = $svea_req->CreateOrderResult->AddressLine2;
//      $order->delivery['city']           = $svea_req->CreateOrderResult->Postarea;
//      $order->delivery['state']          = '';                    // "state" is not applicable in SWP countries
//      $order->delivery['postcode']       = $svea_req->CreateOrderResult->Postcode;
//    }
//
//    $table = array (
//                'INVOICE'       => MODULE_PAYMENT_SWPINVOICE_TEXT_TITLE,
//                'INVOICESE'     => MODULE_PAYMENT_SWPINVOICE_TEXT_TITLE);
//
//    if(array_key_exists($_GET['PaymentMethod'], $table))
//      $order->info['payment_method'] = $table[$_GET['PaymentMethod']];
//
//    // set billing and shipping address to the one fetched from Svea hosted page instead of the local OsCommerce account
//    $firstname      =     $order->billing['firstname'];
//    $lastname       =     $order->billing['lastname'];
//    $street_address =     $order->billing['street_address'];
//    $suburb         =     $order->billing['suburb'];
//    $city           =     $order->billing['city'];
//    $postcode       =     $order->billing['postcode'];
//    $country        =     $order->billing['country']['id'];
//
//    // let's check if the address is already stored in the OsCommerce address book (not a first time user)
//    $customer_id = $_SESSION['customer_id'];
//    $query = tep_db_query("select address_book_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customer_id . "' and entry_firstname = '$firstname' and entry_lastname = '$lastname' and entry_street_address = '$street_address' and entry_postcode = '$postcode'");
//    $address = tep_db_fetch_array($query);
//
//    // first time user; address wasn't found, let's insert it into the database for future use
//    if (!$address) {
//      $query = mysql_query("insert into " . TABLE_ADDRESS_BOOK . " values(NULL, '$customer_id', 'm', ' ', '$firstname', '$lastname', '$street_address', '$suburb', '$postcode', '$city', NULL, '$country', '0')");
//      $billto = mysql_insert_id();
//      $sendto = mysql_insert_id();
//    }
//    // address was found in address book, let's use it for shipping/billing for this order.
//    else {
//      $billto = $address['address_book_id'];
//      $sendto = $address['address_book_id'];
//    }
//
//  }

// from ZC
    // if payment accepted, set addresses based on response, insert order into database
    function after_process() {
        global $insert_id, $order, $db;

        $new_order_id = $insert_id;  // $insert_id contains the new order orders_id
        
        // retrieve response object from before_process()
        $createOrderResponse = unserialize($_SESSION["swp_response"]);

//        // store create order object along with response sveaOrderId in db
//        $sql_data_array = array(
//            'orders_id' => $new_order_id,
//            'sveaorderid' => $createOrderResponse->sveaOrderId,
//            'createorder_object' => $_SESSION["swp_order"]      // session data is already serialized
//        );
//        zen_db_perform("svea_order", $sql_data_array);
//        
//        // if autodeliver order status matches the new order status, deliver the order
//        if( $this->getCurrentOrderStatus( $new_order_id ) == MODULE_PAYMENT_SWPINVOICE_AUTODELIVER ) 
//        {
//            $deliverResponse = $this->doDeliverOrderInvoice($new_order_id);
//            if( $deliverResponse->accepted == true )
//            {                    
//                $comment = 'Order AutoDelivered. (SveaOrderId: ' .$this->getSveaOrderId( $new_order_id ). ')';                
//                //$this->insertOrdersStatus( $new_order_id, SVEA_ORDERSTATUS_DELIVERED_ID, $comment );
//                $sql_data_array = array(
//                    'orders_id' => $new_order_id,
//                    'orders_status_id' => SVEA_ORDERSTATUS_DELIVERED_ID,              
//                    'date_added' => 'now()',
//                    'customer_notified' => 0,  // 0 for "no email" (open lock symbol) in order status history
//                    'comments' => $comment
//                );
//                zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
//
//                $db->Execute(   "update " . TABLE_ORDERS . " " .
//                                "set orders_status = " . SVEA_ORDERSTATUS_DELIVERED_ID . " " .
//                                "where orders_id = " . $new_order_id 
//                );
//            }
//            else 
//            {
//                $comment =  'WARNING: AutoDeliver failed, status not changed. ' .
//                            'Error: ' . $deliverResponse->errormessage . ' (SveaOrderId: ' .$this->getSveaOrderId( $new_order_id ). ')';
//                $this->insertOrdersStatus( $new_order_id, $this->getCurrentOrderStatus( $new_order_id ), $comment );
//            }          
//        }
         
        // clean up our session variables set during checkout   //$SESSION[swp_*
        unset($_SESSION['swp_order']);
        unset($_SESSION['swp_response']);

        return false;
    }
    
//// OLD osC    
//  // if payment accepted, insert order into database
//  function after_process() {
//    global $insert_id, $order;
//
//    $sql_data_array = array(  'orders_id'         => $insert_id,
//                              'orders_status_id'  => $order->info['order_status'],
//                              'date_added'        => 'now()',
//                              'customer_notified' => 0,
//                              'comments'          => 'Accepted by SveaWebPay '.date("Y-m-d G:i:s") .' Security Number #: '.$order->info['securityNumber']);
//    tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
//
//    if ($this->handling_fee > 0){
//
//    switch (MODULE_PAYMENT_SWPINVOICE_DEFAULT_CURRENCY){
//            case 'SEK':
//                $currency = 'kr';
//            break;
//            case 'EUR':
//                $currency = 'â‚¬';
//            break;
//    }
//
//    $handlingFee   = $this->convert_to_currency($this->handling_fee,$order->info['currency']);
//    $invoiceFeeVAT = $handlingFee * 0.2;
//    $invoicePrice  = $handlingFee * 0.8;
//
//    tep_db_query("UPDATE ".TABLE_ORDERS_TOTAL." set value = value+".$handlingFee.", text = CONCAT(FORMAT(value,0), '".$currency."') WHERE orders_id = ".$insert_id." AND class = 'ot_total'");
//    tep_db_query("UPDATE ".TABLE_ORDERS_TOTAL." set value = value+".$invoiceFeeVAT.", text = CONCAT(FORMAT(value,0), '".$currency."') WHERE orders_id = ".$insert_id." AND class = 'ot_tax'");
//    tep_db_query("UPDATE ".TABLE_ORDERS_TOTAL." set value = value+".$handlingFee.", text = CONCAT(FORMAT(value,0), '".$currency."') WHERE orders_id = ".$insert_id." AND class = 'ot_subtotal'");
//    tep_db_query("INSERT INTO ".TABLE_ORDERS_PRODUCTS." (orders_products_id, orders_id, products_id, products_model, products_name, products_price, final_price, products_tax, products_quantity)
//          VALUES ('','".$insert_id."','','','Faktureringsavgift','".$invoicePrice."','".$invoicePrice."','25.00','1')");
//     }
//
//    return false;
//  }
//

    // sets error message to the GET error value
    function get_error() {
        return array('title' => ERROR_MESSAGE_PAYMENT_FAILED, 'error' => stripslashes(urldecode($_GET['swperror'])));
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
    tep_db_query($common . ") values ('SveaWebPay Username SV', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_SV', 'sverigetest', 'Username for SveaWebPay Invoice Sweden', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Password SV', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_SV', 'sverigetest', 'Password for SveaWebPay Invoice Sweden', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Username NO', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_NO', 'norgetest2', 'Username for SveaWebPay Invoice Norway', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Password NO', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_NO', 'norgetest2', 'Password for SveaWebPay Invoice Norway', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Username FI', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_FI', 'finlandtest2', 'Username for SveaWebPay Invoice Finland', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Password FI', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_FI', 'finlandtest2', 'Password for SveaWebPay Invoice Finland', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Username DK', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_DK', 'danmarktest2', 'Username for SveaWebPay Invoice Denmark', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Password DK', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_DK', 'danmarktest2', 'Password for SveaWebPay Invoice Denmark', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Username NL', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_NL', 'hollandtest', 'Username for SveaWebPay Invoice Netherlands', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Password NL', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_NL', 'hollandtest', 'Password for SveaWebPay Invoice Netherlands', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Username DE', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_DE', 'germanytest', 'Username for SveaWebPay Invoice Germany', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Password DE', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_DE', 'germanytest', 'Password for SveaWebPay Invoice Germany', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Client no SV', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_SV', '79021', '', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Client no NO', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_NO', '33308', '', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Client no FI', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_FI', '26136', '', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Client no DK', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_DK', '62008', '', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Client no NL', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_NL', '85997', '', '6', '0', now())");
    tep_db_query($common . ") values ('SveaWebPay Client no DE', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_DE', '14997', '', '6', '0', now())");
    tep_db_query($common . ", set_function) values ('Transaction Mode', 'MODULE_PAYMENT_SWPINVOICE_MODE', 'Test', 'Transaction mode used for processing orders. Production should be used for a live working cart. Test for testing.', '6', '0', now(), 'tep_cfg_select_option(array(\'Production\', \'Test\'), ')");
//    tep_db_query($common . ") values ('Handling Fee', 'MODULE_PAYMENT_SWPINVOICE_HANDLING_FEE', '', 'This handling fee will be applied to all orders using this payment method.  The figure can either be set to a specific amount eg <b>5.00</b>, or set to a percentage of the order total, by ensuring the last character is a \'%\' eg <b>5.00%</b>.', '6', '0', now())");
    tep_db_query($common . ") values ('Accepted Currencies', 'MODULE_PAYMENT_SWPINVOICE_ALLOWED_CURRENCIES','SEK,NOK,DKK,EUR', 'The accepted currencies, separated by commas.  These <b>MUST</b> exist within your currencies table, along with the correct exchange rates.','6','0',now())");
    tep_db_query($common . ", set_function) values ('Default Currency', 'MODULE_PAYMENT_SWPINVOICE_DEFAULT_CURRENCY', 'SEK', 'Default currency used, if the customer uses an unsupported currency it will be converted to this. This should also be in the supported currencies list.', '6', '0', now(), 'tep_cfg_select_option(array(\'SEK\',\'NOK\',\'DKK\',\'EUR\'), ')");
//    tep_db_query($common . ", set_function, use_function) values ('Set Order Status', 'MODULE_PAYMENT_SWPINVOICE_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', now(), 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name')");
    tep_db_query($common . ", set_function) values ('Display SveaWebPay Images', 'MODULE_PAYMENT_SWPINVOICE_IMAGES', 'True', 'Do you want to display SveaWebPay images when choosing between payment options?', '6', '0', now(), 'tep_cfg_select_option(array(\'True\', \'False\'), ')");
    tep_db_query($common . ") values ('Ignore OT list', 'MODULE_PAYMENT_SWPINVOICE_IGNORE','ot_pretotal', 'Ignore the following order total codes, separated by commas.','6','0',now())");
//    tep_db_query($common . ", set_function, use_function) values ('Payment Zone', 'MODULE_PAYMENT_SWPINVOICE_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', now(), 'tep_cfg_pull_down_zone_classes(', 'tep_get_zone_class_title')");
//    tep_db_query($common . ") values ('Sort order of display.', 'MODULE_PAYMENT_SWPINVOICE_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
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
                  'MODULE_PAYMENT_SWPINVOICE_USERNAME_NL',
                  'MODULE_PAYMENT_SWPINVOICE_PASSWORD_NL',
                  'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_NL',
                  'MODULE_PAYMENT_SWPINVOICE_USERNAME_DE',
                  'MODULE_PAYMENT_SWPINVOICE_PASSWORD_DE',
                  'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_DE',
                  'MODULE_PAYMENT_SWPINVOICE_MODE',
//                  'MODULE_PAYMENT_SWPINVOICE_HANDLING_FEE',
                  'MODULE_PAYMENT_SWPINVOICE_ALLOWED_CURRENCIES',
                  'MODULE_PAYMENT_SWPINVOICE_DEFAULT_CURRENCY',
//                  'MODULE_PAYMENT_SWPINVOICE_ORDER_STATUS_ID',
                  'MODULE_PAYMENT_SWPINVOICE_IMAGES',
                  'MODULE_PAYMENT_SWPINVOICE_IGNORE',
//                  'MODULE_PAYMENT_SWPINVOICE_ZONE',
//                  'MODULE_PAYMENT_SWPINVOICE_SORT_ORDER'
    );
  }

    // Localize Error Responses
    function responseCodes($err,$msg = NULL) {
        switch ($err) {

            // EU error codes
            case "20000" :
                return ERROR_CODE_20000;
                break;
            case "20001" :
                return ERROR_CODE_20001;
                break;
            case "20002" :
                return ERROR_CODE_20002;
                break;
            case "20003" :
                return ERROR_CODE_20003;
                break;
            case "20004" :
                return ERROR_CODE_20004;
                break;
            case "20005" :
                return ERROR_CODE_20005;
                break;
            case "20006" :
                return ERROR_CODE_20006;
                break;
            case "20013" :
                return ERROR_CODE_20013;
                break;

            case "24000" :
                return ERROR_CODE_24000;
                break;

            case "30000" :
                return ERROR_CODE_30000;
                break;
            case "30001" :
                return ERROR_CODE_30001;
                break;
            case "30002" :
                return ERROR_CODE_30002;
                break;
            case "30003" :
                return ERROR_CODE_30003;
                break;

            case "40000" :
                return ERROR_CODE_40000;
                break;
            case "40001" :
                return ERROR_CODE_40001;
                break;
            case "40002" :
                return ERROR_CODE_40002;
                break;
            case "40004" :
                return ERROR_CODE_40004;
                break;

            case "50000" :
                return ERROR_CODE_50000;
                break;

            default :
                return ERROR_CODE_DEFAULT . " " . $err . " - " . $msg;     // $err here is the response->resultcode
                break;
        }
    } 

//
//  function convert_to_currency($value, $currency) {
//    global $currencies;
//
//    $length = strlen($value);
//    $decimal_pos = strpos($value, ".");
//    $decimal_places = ($length - $decimal_pos) -1;
//    $decimal_symbol = $currencies->currencies[$currency]['decimal_point'];
//    // item price is ALWAYS given in internal price from the products DB, so just multiply by currency rate from currency table
//    return tep_round($value * $currencies->currencies[$currency]['value'], $decimal_places);
//  }
}
?>
