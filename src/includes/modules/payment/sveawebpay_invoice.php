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
        $this->version = "5.0.0";

        $this->title = MODULE_PAYMENT_SWPINVOICE_TEXT_TITLE;
        $this->description = MODULE_PAYMENT_SWPINVOICE_TEXT_DESCRIPTION;
        $this->enabled = ((MODULE_PAYMENT_SWPINVOICE_STATUS == 'True') ? true : false);
        $this->sort_order = MODULE_PAYMENT_SWPINVOICE_SORT_ORDER;
        $this->display_images = ((MODULE_PAYMENT_SWPINVOICE_IMAGES == 'True') ? true : false);
        $this->ignore_list = explode(',', MODULE_PAYMENT_SWPINVOICE_IGNORE);
        if ((int)MODULE_PAYMENT_SWPINVOICE_ORDER_STATUS_ID > 0)
            $this->order_status = MODULE_PAYMENT_SWPINVOICE_ORDER_STATUS_ID;
        if (is_object($order)) $this->update_status();
    }

    function update_status() {
        global $db, $order, $currencies, $messageStack;

        // do not use this module if any of the allowed currencies are not set in osCommerce
        foreach($this->getInvoiceCurrencies() as $currency ) {
            if( !is_array($currencies->currencies[strtoupper($currency)]) ) {
                $this->enabled = false;
                $messageStack->add('header', ERROR_ALLOWED_CURRENCIES_NOT_DEFINED, 'error');
            }
        }

        // do not use this module if the geograhical zone is set and we are not in it
        if (($this->enabled == true) && ((int) MODULE_PAYMENT_SWPINVOICE_ZONE > 0)) {
            $check_flag = false;
            $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . 
                " where geo_zone_id = '" . MODULE_PAYMENT_SWPINVOICE_ZONE . "' and zone_country_id = '" . 
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

        // add svea invoice image file
        if ($this->display_images) {
             $fields[] = array('title' => '<img src=images/Svea/SVEAINVOICEEU_'.$order->customer['country']['iso_code_2'].'.png />', 'field' => '');
        }
             
        // catch and display error messages raised when i.e. payment request from before_process() below turns out not accepted
        if (isset($_REQUEST['payment_error']) && $_REQUEST['payment_error'] == 'sveawebpay_invoice') {
            $fields[] = array('title' => '<span style="color:red">' . $_SESSION['SWP_ERROR'] . '</span>', 'field' => '');
        }

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
        
        // add information about invoice if invoice fee module enabled
        if ( constant( MODULE_ORDER_TOTAL_SWPHANDLING_STATUS ) == 'True' ) {
            $paymentfee_cost = constant( "MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_".$customer_country );

            $tax_class = constant( "MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_".$customer_country );
            if (DISPLAY_PRICE_WITH_TAX == "true" && $tax_class > 0) {  
                $paymentfee_tax = $paymentfee_cost * tep_get_tax_rate($tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']) /100;
            }
        
            $sveaInvoiceFee =
                '<br /><div>' . sprintf( MODULE_PAYMENT_SWPINVOICE_HANDLING_APPLIES, $currencies->format($paymentfee_cost + $paymentfee_tax), "");
        }
      
        if(     $order->billing['country']['iso_code_2'] == "SE" ||
                $order->billing['country']['iso_code_2'] == "DK" ||
                $order->billing['country']['iso_code_2'] == "NO" )      // but don't show button/do getAddress unless customer is company!
        {
             $sveaSubmitAddress = '<button id="sveaSubmitGetAddress" type="button">'.FORM_TEXT_GET_ADDRESS.'</button>';
        }

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
                            $sveaInvoiceFee .
                            // FI, NL, DE also uses customer address data from osCommerce
                        '</div>';

        $fields[] = array('title' => '', 'field' => '<br />' . $sveaField . $sveaError);

        return array( 'id'      => $this->code,
                      'module'  => $this->title,
                      'fields'  => $fields
        );
        
    }

    /**
     * called at start of checkout_confirmation.php
     * @return boolean
     */
    function pre_confirmation_check() 
    {
        global $order, $currency;

        // check if we've performed a getAddress lookup
        if( isset( $_SESSION['sveaGetAddressesResponse'] ) )
        {
            $getAddressesResponse = unserialize($_SESSION['sveaGetAddressesResponse']);
            unset($_SESSION['sveaGetAddressesResponse']);

            // set zencart billing address to invoice address from getAddresses response
            foreach($getAddressesResponse->customerIdentity as $asAddress ) // all GetAddressIdentity objects
            {
                
                if( $asAddress->addressSelector == $_POST['sveaAddressSelector'] ) // write the selected GetAddressIdentity
                {
                    if( $_POST['sveaIsCompany'] == 'false' ) // is individual?
                    {    
                        $order->billing['firstname'] = $asAddress->firstName;
                        $order->billing['lastname'] = $asAddress->lastName;
                        $order->billing['company'] = "";
                    }
                    elseif( $_POST['sveaIsCompany'] == 'true' ) // is company?
                    {       
                        $order->billing['company'] = $asAddress->fullName;
                        $order->billing['firstname'] = $asAddress->firstName;
                        $order->billing['lastname'] = $asAddress->lastName;
                    }

                    $order->billing['street_address'] = $asAddress->street;
                    $order->billing['suburb'] =  $asAddress->coAddress;
                    $order->billing['city'] = $asAddress->locality;
                    $order->billing['postcode'] = $asAddress->zipCode;

                    $order->billing['street_address'] = $asAddress->street;
                    $order->billing['suburb'] =  $asAddress->coAddress;
                    $order->billing['city'] = $asAddress->locality;
                    $order->billing['postcode'] = $asAddress->zipCode;
                           
                    $order->billing['country']['title'] = $this->getCountryName( $getAddressesResponse->country );  // other fields not set
                }
            }
        }    

        // make sure we use the correct invoice currency corresponding to the customer country here!
        $customer_country = $order->customer['country']['iso_code_2'];
        
        // did the customer have a different currency selected than the invoice country currency?
        if( $_SESSION['currency'] != $this->getInvoiceCurrency( $customer_country ) )
        {         
            // set shop currency to the selected payment method currency
            $order->info['currency'] = $this->getInvoiceCurrency( $customer_country );
            $_SESSION['currency'] = $order->info['currency'];

            // redirect to update order_totals to new currency, making sure to preserve post data
            $_SESSION['sveapostdata'] = $_POST; 
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_CONFIRMATION));    // redirect to update order_totals to new currency               
        }
        
        if( isset($_SESSION['sveapostdata']) )
        {
            $_POST = array_merge( $_POST, $_SESSION['sveapostdata'] );
            unset( $_SESSION['sveapostdata'] );
        }

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
        
        // localization parameters
        $user_country = $this->getCountry();
        $user_language = $this->getLanguage();
        
        $currency = $order->info['currency'];
        
        // Create and initialize order object, using either test or production configuration
        $sveaConfig = (MODULE_PAYMENT_SWPINVOICE_MODE === 'Test') ? new OsCommerceSveaConfigTest() : new OsCommerceSveaConfigProd();

        $swp_order = WebPay::createOrder( $sveaConfig )
            ->setCountryCode( $user_country )
            ->setCurrency($currency)                       
            ->setClientOrderNumber($client_order_number)  
            ->setOrderDate(date('c'))                  
        ;

        // create product order rows from each item in cart
        $swp_order = $this->parseOrderProducts( $order->products, $swp_order );
        
        // creates non-item order rows from Order Total entries
        $swp_order = $this->parseOrderTotals( $this->getOrderTotals(), $swp_order );

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

        // we're done here
        return false;
    }
  
    /**
     * before_process is called from modules/checkout_process.
     * It instantiates and populates a WebPay::createOrder object
     * as well as sends the actual payment request
     */
    function before_process() {
        global $order, $order_totals, $language, $billto, $sendto;

        // retrieve order object set in process_button()
        $swp_order = unserialize($_SESSION["swp_order"]);

        // send payment request to svea, receive response       
        try {
            $swp_response = $swp_order->useInvoicePayment()->doRequest();
            }
        catch (Exception $e){  
            // hack together a fake response object containing the error & errormessage
            $swp_response = (object) array( "accepted" => false, "resultcode" => 1000, "errormessage" => $e->getMessage() ); //new "error" 1000
        }

        // payment request failed; handle this by redirecting w/result code as error message
        if ($swp_response->accepted === false) {
            $_SESSION['SWP_ERROR'] = $this->responseCodes($swp_response->resultcode,$swp_response->errormessage);
            $payment_error_return = 'payment_error=sveawebpay_invoice';
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return)); // error handled in selection() above
        }
     
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
        }
    }

    // if payment accepted, set addresses based on response, insert order into database
    function after_process() {
        global $insert_id, $order;

        $new_order_id = $insert_id;  // $insert_id contains the new order orders_id

        // retrieve response object from before_process()
        $createOrderResponse = unserialize($_SESSION["swp_response"]);

        // store create order object along with response sveaOrderId in db
        $sql_data_array = array(
            'orders_id' => $new_order_id,
            'sveaorderid' => $createOrderResponse->sveaOrderId,
            'createorder_object' => $_SESSION["swp_order"]      // session data is already serialized
        );
        tep_db_perform("svea_order", $sql_data_array);
        
        // if autodeliver order status matches the new order status, deliver the order
        if( $this->getCurrentOrderStatus( $new_order_id ) == MODULE_PAYMENT_SWPINVOICE_AUTODELIVER )
        {
            $deliverResponse = $this->doDeliverOrderInvoice($new_order_id);
            if( $deliverResponse->accepted == true )
            {

                $comment = 'Order AutoDelivered. (SveaOrderId: ' .$this->getSveaOrderId( $new_order_id ). ')';
                //$this->insertOrdersStatus( $new_order_id, SVEA_ORDERSTATUS_DELIVERED_ID, $comment );
                $sql_data_array = array(
                    'orders_id' => $new_order_id,
                    'orders_status_id' => SVEA_ORDERSTATUS_DELIVERED_ID,
                    'date_added' => 'now()',
                    'customer_notified' => 0,  // 0 for "no email" (open lock symbol) in order status history   //TODO use card SEND_MAIL behaviour
                    'comments' => $comment
                );
                tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

                tep_db_query(   "update " . TABLE_ORDERS . " " .
                                "set orders_status = " . SVEA_ORDERSTATUS_DELIVERED_ID . " " .
                                "where orders_id = " . $new_order_id
                );
            }
            else
            {
                $comment =  'WARNING: AutoDeliver failed, status not changed. ' .
                            'Error: ' . $deliverResponse->errormessage . ' (SveaOrderId: ' .$this->getSveaOrderId( $new_order_id ). ')';
                $this->insertOrdersStatus( $new_order_id, $this->getCurrentOrderStatus( $new_order_id ), $comment );
            }
        }

        // clean up our session variables set during checkout   //$SESSION[swp_*
        unset($_SESSION['swp_order']);
        unset($_SESSION['swp_response']);

        return false;
    }
    
    // sets error message to the GET error value
    function get_error() {
        return array('title' => ERROR_MESSAGE_PAYMENT_FAILED, 
            'error' => stripslashes(urldecode($_GET['swperror']))
        );
    }
    
    // standard check if installed function
    function check() {
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
        tep_db_query($common . ") values ('SveaWebPay Username SE', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_SE', '', 'Username for SveaWebPay Invoice Sweden', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Password SE', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_SE', '', 'Password for SveaWebPay Invoice Sweden', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Username NO', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_NO', '', 'Username for SveaWebPay Invoice Norway', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Password NO', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_NO', '', 'Password for SveaWebPay Invoice Norway', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Username FI', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_FI', '', 'Username for SveaWebPay Invoice Finland', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Password FI', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_FI', '', 'Password for SveaWebPay Invoice Finland', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Username DK', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_DK', '', 'Username for SveaWebPay Invoice Denmark', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Password DK', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_DK', '', 'Password for SveaWebPay Invoice Denmark', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Username NL', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_NL', '', 'Username for SveaWebPay Invoice Netherlands', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Password NL', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_NL', '', 'Password for SveaWebPay Invoice Netherlands', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Username DE', 'MODULE_PAYMENT_SWPINVOICE_USERNAME_DE', '', 'Username for SveaWebPay Invoice Germany', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Password DE', 'MODULE_PAYMENT_SWPINVOICE_PASSWORD_DE', '', 'Password for SveaWebPay Invoice Germany', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Client no SE', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_SE', '', '', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Client no NO', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_NO', '', '', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Client no FI', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_FI', '', '', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Client no DK', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_DK', '', '', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Client no NL', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_NL', '', '', '6', '0', now())");
        tep_db_query($common . ") values ('SveaWebPay Client no DE', 'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_DE', '', '', '6', '0', now())");
        tep_db_query($common . ", set_function) values ('Transaction Mode', 'MODULE_PAYMENT_SWPINVOICE_MODE', 'Test', 'Transaction mode used for processing orders. Production should be used for a live working cart. Test for testing.', '6', '0', now(), 'tep_cfg_select_option(array(\'Production\', \'Test\'), ')");
        tep_db_query($common . ", set_function, use_function) values ('Set Order Status', 'MODULE_PAYMENT_SWPINVOICE_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', now(), 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name')");
        
        tep_db_query($common . ", set_function) values ('Auto Deliver Order', 'MODULE_PAYMENT_SWPINVOICE_AUTODELIVER', '3', 'AutoDeliver: When the order status of an order is set to this value, it will be delivered to Svea. Use in conjunction with Set Order Status above to autodeliver orders.', '6', '0', now(), 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name')");
        tep_db_query($common . ", set_function) values ('Invoice Distribution type', 'MODULE_PAYMENT_SWPINVOICE_DISTRIBUTIONTYPE', 'Post', 'Deliver orders per Post or Email? NOTE: This must match your Svea admin settings or invoices may be non-delivered. Ask your Svea integration manager if unsure.', '6', '0', now(), 'tep_cfg_select_option(array(\'Post\', \'Email\'), ')");
        
        tep_db_query($common . ", set_function) values ('Display SveaWebPay Images', 'MODULE_PAYMENT_SWPINVOICE_IMAGES', 'True', 'Do you want to display SveaWebPay images when choosing between payment options?', '6', '0', now(), 'tep_cfg_select_option(array(\'True\', \'False\'), ')");
        tep_db_query($common . ") values ('Ignore OT list', 'MODULE_PAYMENT_SWPINVOICE_IGNORE','ot_pretotal', 'Ignore the following order total codes, separated by commas.','6','0',now())");
        tep_db_query($common . ", set_function, use_function) values ('Payment Zone', 'MODULE_PAYMENT_SWPINVOICE_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', now(), 'tep_cfg_pull_down_zone_classes(', 'tep_get_zone_class_title')");
        tep_db_query($common . ") values ('Sort order of display.', 'MODULE_PAYMENT_SWPINVOICE_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");

        // insert svea order table if not exists already
        $res = tep_db_query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '". DB_DATABASE ."' AND table_name = 'svea_order';");
        $fields = $res->fetch_assoc();
        if( $fields["COUNT(*)"] != 1 ) {
            $sql = "CREATE TABLE svea_order (orders_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, sveaorderid INT NOT NULL, createorder_object BLOB, invoice_id INT )";
            tep_db_query( $sql );
        }

        // insert svea order statuses into table order_status, if not exists already
        $res = tep_db_query('SELECT COUNT(*) FROM ' . TABLE_ORDERS_STATUS . ' WHERE orders_status_name = "'. SVEA_ORDERSTATUS_CLOSED .'"');
        $fields = $res->fetch_assoc();
        if( $fields["COUNT(*)"] == 0 ) {
            $sql =  'INSERT INTO ' . TABLE_ORDERS_STATUS . ' (`orders_status_id`, `language_id`, `orders_status_name`) VALUES ' .
                    '(' . SVEA_ORDERSTATUS_DELIVERED_ID . ', 1, "' . SVEA_ORDERSTATUS_DELIVERED . '")'
            ;
            tep_db_query( $sql );
        }        
        
    }

    // standard uninstall function
    function remove() {
        tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    // must perfectly match keys inserted in install function
    function keys() {
        return array( 
            'MODULE_PAYMENT_SWPINVOICE_STATUS',
            'MODULE_PAYMENT_SWPINVOICE_USERNAME_SE',
            'MODULE_PAYMENT_SWPINVOICE_PASSWORD_SE',
            'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_SE',
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
            'MODULE_PAYMENT_SWPINVOICE_ORDER_STATUS_ID',
            
            'MODULE_PAYMENT_SWPINVOICE_AUTODELIVER',
            'MODULE_PAYMENT_SWPINVOICE_DISTRIBUTIONTYPE',
            
            'MODULE_PAYMENT_SWPINVOICE_IMAGES',
            'MODULE_PAYMENT_SWPINVOICE_IGNORE',
            'MODULE_PAYMENT_SWPINVOICE_ZONE',
            'MODULE_PAYMENT_SWPINVOICE_SORT_ORDER'
        );
    }

    // Localize Error Responses
    function responseCodes($err,$msg = NULL) {
        switch ($err) {

            // EU error codes
            case "20000" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_20000);
                break;
            case "20001" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_20001);
                break;
            case "20002" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_20002);
                break;
            case "20003" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_20003);
                break;
            case "20004" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_20004);
                break;
            case "20005" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_20005);
                break;
            case "20006" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_20006);
                break;
            case "20013" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_20013);
                break;

            case "24000" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_24000);
                break;

            case "30000" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_30000);
                break;
            case "30001" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_30001);
                break;
            case "30002" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_30002);
                break;
            case "30003" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_30003);
                break;

            case "40000" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_40000);
                break;
            case "40001" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_40001);
                break;
            case "40002" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_40002);
                break;
            case "40004" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_40004);
                break;

            case "50000" :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_50000);
                break;

            default :
                return sprintf("Svea error %s: %s", $err, ERROR_CODE_DEFAULT . " " . $err . " - " . $msg);     // $err here is the response->resultcode
                break;
        }
    } 


    /**
     * Given an orderID, reconstruct the svea order object and send deliver order request, return response
     *
     * @param int $oID -- $oID is the order id
     * @return Svea\DeliverOrderResult
     */
    function doDeliverOrderInvoice($oID) {

        // get osCommerce order from db
        $order = new order($oID);
        
        // get svea order id reference returned in createOrder request result
        $sveaOrderId = $this->getSveaOrderId( $oID );
        $swp_order = $this->getSveaCreateOrderObject( $oID );

        // Create and initialize order object, using either test or production configuration
        $sveaConfig = (MODULE_PAYMENT_SWPINVOICE_MODE === 'Test') ? new OsCommerceSveaConfigTest() : new OsCommerceSveaConfigProd();

        $swp_deliverOrder = WebPay::deliverOrder( $sveaConfig )
            ->setInvoiceDistributionType( MODULE_PAYMENT_SWPINVOICE_DISTRIBUTIONTYPE )
            ->setOrderId($sveaOrderId)
        ;

        // TODO create helper functions in integration package that transforms createOrder -> deliverOrder -> closeOrder etc. (see INTG-324)

        // this really exploits CreateOrderRow objects having public properties...
        // ~hack
        $swp_deliverOrder->orderRows = $swp_order->orderRows;
        $swp_deliverOrder->shippingFeeRows = $swp_order->shippingFeeRows;
        $swp_deliverOrder->invoiceFeeRows = $swp_order->invoiceFeeRows;
        $swp_deliverOrder->fixedDiscountRows = $swp_order->fixedDiscountRows;
        $swp_deliverOrder->relativeDiscountRows = $swp_order->relativeDiscountRows;
        $swp_deliverOrder->countryCode = $swp_order->countryCode;
        // /hack
   
        $swp_deliverResponse = $swp_deliverOrder->deliverInvoiceOrder()->doRequest();

        // if deliverorder accepted, update svea_order table with Svea invoiceId
        if( $swp_deliverResponse->accepted == true )
        {
            tep_db_query(
                "update svea_order " .
                "set invoice_id = " . $swp_deliverResponse->invoiceId . " " .
                "where orders_id = " . (int)$oID )
            ;
        }
        // return deliver order response
        return $swp_deliverResponse;
    }    
    
    /**
     * Returns the currency used for an invoice country. 
     */
    function getInvoiceCurrency( $country ) 
    {
        $country_currencies = array(
            'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_SE' => 'SEK',
            'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_NO' => 'NOK',
            'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_FI' => 'EUR',
            'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_DK' => 'DKK',
            'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_NL' => 'EUR',
            'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_DE' => 'EUR'
        );
 
        $method = "MODULE_PAYMENT_SWPINVOICE_CLIENTNO_" . $country;
        
        return $country_currencies[$method];
    } 
    
    /**
     * Returns the currencies used in all countries where an invoice payment 
     * method has been configured (i.e. clientno is set for country in config). 
     * Used in invoice to determine currencies which must be set.
     * 
     * @return array - currencies for countries with ug clientno set in config 
     */
    function getInvoiceCurrencies() 
    {
        $country_currencies = array(
            'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_SE' => 'SEK',
            'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_NO' => 'NOK',
            'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_FI' => 'EUR',
            'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_DK' => 'DKK',
            'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_NL' => 'EUR',
            'MODULE_PAYMENT_SWPINVOICE_CLIENTNO_DE' => 'EUR'
        );

        $currencies = array();
        foreach( $country_currencies as $country => $currency )
        {
            if( constant($country)!=NULL ) $currencies[] = $currency;
        }
        
        return array_unique( $currencies );
    }   
}
?>
