<?php

require('includes/application_top.php'); // include osCommerce tep_ functions et al

require(DIR_FS_CATALOG . 'ext/modules/payment/svea/Includes.php');      // php integration package files
require_once(DIR_FS_CATALOG . 'sveawebpay_config.php');                 // sveaConfig implementation

// originally ported from ZC:
// 
/**
 *  get iso 3166 customerCountry from zencart customer settings
 */
if( isset($_POST['SveaAjaxGetCustomerCountry']) ) {
    $country = tep_get_countries_with_iso_codes( $_SESSION['customer_country_id'] );
    echo $country['countries_iso_code_2'];
}

/**
 * perform getPaymentPlanParams, paymentPlanPricePerMonth, return dropdown widget
 */
if( isset($_POST['SveaAjaxGetPartPaymentOptions']) ) {

    $price = isset( $_SESSION['sveaAjaxOrderTotal'] ) ? $_SESSION['sveaAjaxOrderTotal'] : "swp_not_set";        // from payment method
    $country = isset( $_SESSION['sveaAjaxCountryCode'] ) ? $_SESSION['sveaAjaxCountryCode'] : "swp_not_set";    // from svea.js

    sveaAjaxGetPartPaymentOptions( $price, $country );
    exit();
}

function sveaAjaxGetPartPaymentOptions( $price, $country ) {

    $sveaConfig = (MODULE_PAYMENT_SWPPARTPAY_MODE === 'Test' ) ? new OsCommerceSveaConfigTest() : new OsCommerceSveaConfigProd();

    $plansResponse = WebPay::getPaymentPlanParams( $sveaConfig )->setCountryCode($country)->doRequest();

    // error?
    if( $plansResponse->accepted == false) {
        echo( sprintf('<div><input type="radio" id="address_0" value="swp_not_set">%s</div>', $plansResponse->errormessage) );
    }
    // if not, show addresses and store response in session
    else {
        $priceResponse = WebPay::paymentPlanPricePerMonth( $price, $plansResponse );
        $counter = 0;
        foreach( $priceResponse->values as $cc) {
            $checked = $counter === 0 ? "checked" : "";
            echo sprintf( '<div><input type="radio" name="sveaPaymentOptionsPP" value="%s" '.$checked.'>%s (%.2f)</div>', $cc['campaignCode'], $cc['description'], $cc['pricePerMonth'] );
            $counter ++;
        }
    }
}

///**
// * Present the banks for the user in a friendly fashion, so that we can go directly there instead of landing on paypage
// */
//if( isset($_POST['SveaAjaxGetBankPaymentOptions']) ) {
//    $country = isset( $_SESSION['sveaAjaxCountryCode'] ) ? $_SESSION['sveaAjaxCountryCode'] : "swp_not_set";
//
//    sveaAjaxGetBankPaymentOptions( $country );
//    exit();
//}
//
//function sveaAjaxGetBankPaymentOptions( $country ) {
//
//    $sveaConfig = (MODULE_PAYMENT_SWPINTERNETBANK_MODE === 'Test') ? new ZenCartSveaConfigTest() : new ZenCartSveaConfigProd();
//
//    $banksResponse = WebPay::getPaymentMethods( $sveaConfig )->setContryCode( $country )->doRequest();
//
//    if( sizeof( $banksResponse ) == 0 ) {
//        return "Error: NO APPLICABLE BANKS FOR THIS PAYMENT METHOD";
//    }
//    else {
//        $logosPath = "images/logos/";
//        $counter = 0;
//        foreach( $banksResponse as $bank) {
//            if( preg_match( "/^DB/", substr( $bank,0,2 ) ) === 1 ) { // bank payment methods all start with "DB"
//                echo sprintf( '<input type="radio" name="BankPaymentOptions" id="%s" value="%s" %s/>', $bank, $bank, $counter++==0 ? "checked=true" : "" ); //selects 1st bank
//                echo sprintf( '<label for="%s"> <img src="%s%s.png" alt="bank %s" /> </label>', $bank, $logosPath, $bank, $bank);
//            }
//        }
//    }
//}

/**
 *  perform getAddresses() via php integration package, return dropdown html widget
 */
if( isset($_POST['SveaAjaxGetAddresses']) ) {

    $ssn = isset( $_POST['sveaSSN'] ) ? $_POST['sveaSSN'] : "swp_not_set";
    $country = isset( $_POST['sveaCountryCode'] ) ? $_POST['sveaCountryCode'] : "swp_not_set";
    $isCompany = isset( $_POST['sveaIsCompany'] ) ? $_POST['sveaIsCompany'] : "swp_not_set";
    $paymentType = isset( $_POST['paymentType'] ) ? $_POST['paymentType'] : "swp_not_set";

    sveaAjaxGetAddresses($ssn, $country, $isCompany, $paymentType );
    exit();
}

function sveaAjaxGetAddresses( $ssn, $country, $isCompany, $paymentType ) {

    $sveaConfig = (MODULE_PAYMENT_SWPINVOICE_MODE === 'Test' ||
                   MODULE_PAYMENT_SWPPARTPAY_MODE === 'Test' ) ? new OsCommerceSveaConfigTest() : new OsCommerceSveaConfigProd();

    $request = WebPay::getAddresses( $sveaConfig );
    // private individual
    if(  $isCompany === 'true' ) {
        $request = $request->setCompany( $ssn );
    }
    if( $isCompany === 'false' ) {
        $request = $request->setIndividual( $ssn );
    }
    // paymenttype
    switch( strtoupper($paymentType) ) {
        case "INVOICE":
            $request = $request->setOrderTypeInvoice();
            break;
        case "PAYMENTPLAN":
            $request = $request->setOrderTypePaymentPlan();
            break;
    }
    $response = $request->setCountryCode( $country )->doRequest();

    // error?
    if( $response->accepted == false) {
        echo( sprintf('<option id="address_0" value="swp_not_set">%s</option>', $response->errormessage) ); 
    }
    // if not, show addresses and store response in session
    else {
        // $getAddressResponse has type Svea\getAddressIdentity
        foreach( $response->customerIdentity as $key => $getAddressIdentity ) {

            $addressSelector = $getAddressIdentity->addressSelector;
            $fullName = $getAddressIdentity->fullName;  // also used for company name
            $street = $getAddressIdentity->street;
            $coAddress = $getAddressIdentity->coAddress;
            $zipCode = $getAddressIdentity->zipCode;
            $locality = $getAddressIdentity->locality;

            //Send back to user
            echo(   '<option id="address_' . $key .
                        '" value="' . $addressSelector .
                        '">' . $fullName .
                        ', ' . $street .
                        ', ' . $coAddress .
                        ', ' . $zipCode .
                        ' ' . $locality .
                    '</option>'
            );
        }
        
        $response->country = $country; // hack to pass country to session; inject country into object 
        
        $_SESSION['sveaGetAddressesResponse'] = serialize( $response );     // TODO make use of this...
    }
}

///**
// * store invoice address in customer address book, return zen address book id used for billing
// */
//if( isset($_POST['SveaAjaxSetCustomerInvoiceAddress']) ) {
//    sveaAjaxSetCustomerInvoiceAddress();
//    exit;
//}
//
//function sveaAjaxSetCustomerInvoiceAddress() {
//    global $db;
//
//    // have we got an address selector (i.e. a getAddresses country)?
//    if( isset($_POST['SveaAjaxAddressSelectorValue']) ) {
//
//        $addressSelector = $_POST['SveaAjaxAddressSelectorValue'];
//
//        $response = unserialize( $_SESSION['sveaGetAddressesResponse'] );
//
//        // find the address corresponding to chosen addressSelector
//        foreach( $response->customerIdentity as $getAddressIdentity ) {
//            if( $getAddressIdentity->addressSelector === $addressSelector ) {
//
//                // does the customer already have the invoice address in her address book?
//                $sqlGetInvoiceAddressBookId =
//                     "SELECT address_book_id FROM " . TABLE_ADDRESS_BOOK . " " .
//                     "WHERE customers_id = '" . intval($_SESSION['customer_id']) . "' " .
//                     "AND entry_firstname = '" . $getAddressIdentity->firstName . "' " .
//                     "AND entry_lastname = '" . $getAddressIdentity->lastName . "' " .
//                     "AND entry_company = '" . $getAddressIdentity->fullName . "' " .
//                     "AND entry_street_address = '" . $getAddressIdentity->street . "' " .
//                     "AND entry_postcode = '" . strval($getAddressIdentity->zipCode) . "' " .
//                     "AND entry_city = '" . $getAddressIdentity->locality . "' " .
//                     "AND entry_country_id ='" . intval($_SESSION['customer_country_id']) . "'"
//                ;
//                $queryFactoryResult = $db->execute($sqlGetInvoiceAddressBookId);
//
//                // invoice address not present, add it to address book for this customer
//                $foundInvoiceAddress = $queryFactoryResult->recordCount();
//
//                if( $foundInvoiceAddress == 0) {
//                    $sqlAddInvoiceAddress = array();
//                    $sqlAddInvoiceAddress['customers_id'] = intval($_SESSION['customer_id']);
//                    $sqlAddInvoiceAddress['entry_firstname'] = $getAddressIdentity->firstName;
//                    $sqlAddInvoiceAddress['entry_lastname'] = $getAddressIdentity->lastName;
//                    $sqlAddInvoiceAddress['entry_company'] = $getAddressIdentity->fullName; // check if company first, else empty string?
//                    $sqlAddInvoiceAddress['entry_street_address'] = $getAddressIdentity->street;
//                    $sqlAddInvoiceAddress['entry_postcode'] = strval($getAddressIdentity->zipCode);
//                    $sqlAddInvoiceAddress['entry_city'] = $getAddressIdentity->locality;
//                    $sqlAddInvoiceAddress['entry_country_id'] = intval($_SESSION['customer_country_id']);
//
//                    $sqlInsertInvoiceAddressResult = zen_db_perform(TABLE_ADDRESS_BOOK, $sqlAddInvoiceAddress); // returns true if insert succeeded
//
//                    // needed as zen_db_perform doesn't provide insert_id
//                    if( $sqlInsertInvoiceAddressResult ) {
//                        $queryFactoryResult = $db->execute($sqlGetInvoiceAddressBookId);
//                    }
//                }
//
//                // get latest address book entry for this customer, use as billing address
//                $invoiceAddressBookId = $queryFactoryResult->fields['address_book_id'];
//                $_SESSION['billto'] = $invoiceAddressBookId;
//
//                echo $invoiceAddressBookId;
//            }
//        }
//    }
//
//    // no addressSelector, so we respect the shipping/billing addresses for now.
//    else {
//        echo $_SESSION['billto'];
//    }
//}
//




//
// OLD osC
//
////require(DIR_WS_CLASSES . 'order.php');

//if (isset($_POST['paymentOptions'])){
//    $svea_server = (MODULE_PAYMENT_SWPPARTPAY_MODE == 'Test') ? 'https://webservices.sveaekonomi.se/webpay_test/SveaWebPay.asmx?WSDL' : 'https://webservices.sveaekonomi.se/webpay/SveaWebPay.asmx?WSDL';
//}else{
//    $svea_server = (MODULE_PAYMENT_SWPINVOICE_MODE == 'Test') ? 'https://webservices.sveaekonomi.se/webpay_test/SveaWebPay.asmx?WSDL' : 'https://webservices.sveaekonomi.se/webpay/SveaWebPay.asmx?WSDL';
//}
//
//
//$order = new order;
//
//
///***    Validation  ***/
//class Validate{
//
//	private function luhn($ssn){
//		$sum = 0;
//		for ($i = 0; $i < strlen($ssn)-1; $i++){
//			$tmp = substr($ssn, $i, 1) * (2 - ($i & 1)); //växla mellan 212121212
//			if ($tmp > 9) $tmp -= 9;
//			$sum += $tmp;
//		}
//
//		//extrahera en-talet
//		$sum = (10 - ($sum % 10)) % 10;
//		return substr($ssn, -1, 1) == $sum;
//	}
//
//	private function only_numbers($ssn){
//		if (is_numeric($ssn)){
//			return true;
//		}else{
//			return false;
//		}
//	}
//
//
//	public function check($ssn){
//
//		$error_msg = null;
//
//		if ($this->only_numbers($ssn) == false){
//			$error_msg = "Persnr/orgnr får endast bestå av siffror";
//		}elseif ($this->luhn($ssn) == false){
//			$error_msg = "Persnr/orgnr har felaktig kontrollsiffra, vänligen ange ett giltigt nr";
//		}
//
//		$returns = array("error_msg" => $error_msg);
//
//		return $returns;
//	}
//
//}
//
///*
// *
// * GET address
// *
// */
// 
//if (isset($_POST['sveapnr'])):
//
//    if ($_POST['f'] != '1'){
//    $PP = (isset($_POST['paymentOptions'])) ? true : false ;
//
//    $v = new validate();
//    $validation = $v->check($_POST['sveapnr']);
//
//    //Get svea configuration for each country based on currency
//    $sveaConf = ($PP == true) ? getCountryConfigPP($order->info['currency']) : getCountryConfigInvoice($order->info['currency']) ;
//
//    $company = ($PP == true) ? false : $_POST['is_company'] ;
//
//    if ($order->info['currency'] == 'SEK'){
//        $error_msg = $validation['error_msg'];
//    }else{
//        $error_msg = null;
//    }
//
//
//    if ($PP == true){
//        $pnef = "#pers_nr_error_delbet";
//        $as   = "#adressSelector_delbet";
//        $petf = "#persnr_error_tr_delbet";
//    }else{
//        $pnef = "#pers_nr_error_fakt";
//        $as   = "#adressSelector_fakt";
//        $petf = "#persnr_error_tr_fakt";
//    }
//
//
//    if ($error_msg == '' || $error_msg == null){
//
//
//    $request = Array(
//    	"request" => Array(
//          "Auth" => Array(
//            "Username" => $sveaConf['username'],
//            "Password" => $sveaConf['password'],
//            "ClientNumber" => $sveaConf['clientno']
//           ),
//    	  "IsCompany" => $company,
//    	  "CountryCode" => $sveaConf['countryCode'],
//    	  "SecurityNumber" => $_POST['sveapnr']
//    	)
//      );
//
//    //Call Soap and set up data
//    $client = new SoapClient( $svea_server );
//
//    //Handle response
//    $response =  $client->GetAddresses( $request );
//
//
//    echo '$("'.$as.'").empty();';
//
//    if (isset($response->GetAddressesResult->ErrorMessage)){
//    	echo 'jQuery("'.$pnef.'").html("'.$response->GetAddressesResult->ErrorMessage.'");
//    			jQuery("'.$pnef.'").show();';
//    }elseif(is_array($response->GetAddressesResult->Addresses->CustomerAddress)){
//    		foreach ($response->GetAddressesResult->Addresses->CustomerAddress as $key => $info){
//
//    			$firstName = $info->FirstName;
//    			$lastName = $info->LastName;
//                $LegalName = $info->LegalName;
//    			$address = $info->AddressLine1." ".$info->AddressLine2;
//    			$postCode = $info->Postcode;
//    			$city = $info->Postarea;
//    			$addressSelector = $info->AddressSelector;
//
//            //Send back to user
//    		echo '
//            jQuery("'.$as.'").show();
//    		jQuery("'.$as.'").append("<option id=\"adress_'.$key.'\" value=\"'.$addressSelector.'\">'.$LegalName.', '.$address.', '.$postCode.' '.$city.'</option>");
//            ';
//
//            }
//
//            echo '
//            jQuery("button[type=submit]").removeAttr("disabled");
//            jQuery("'.$petf.'").hide();';
//    }else{
//    		$firstName = $response->GetAddressesResult->Addresses->CustomerAddress->FirstName;
//    		$lastName = $response->GetAddressesResult->Addresses->CustomerAddress->LastName;
//            $LegalName = $response->GetAddressesResult->Addresses->CustomerAddress->LegalName;
//    		$address_1 = (!empty($response->GetAddressesResult->Addresses->CustomerAddress->AddressLine1)) ? $response->GetAddressesResult->Addresses->CustomerAddress->AddressLine1 : '';
//            $address_2 = (!empty($response->GetAddressesResult->Addresses->CustomerAddress->AddressLine2)) ? $response->GetAddressesResult->Addresses->CustomerAddress->AddressLine2 : '';
//    		$address = $address_1." ".$address_2;
//    		$postCode = $response->GetAddressesResult->Addresses->CustomerAddress->Postcode;
//    		$city = $response->GetAddressesResult->Addresses->CustomerAddress->Postarea;
//    		$addressSelector = $response->GetAddressesResult->Addresses->CustomerAddress->AddressSelector;
//
//            //Send back to user
//    		echo '
//            jQuery("'.$as.'").show();
//    		jQuery("'.$as.'").append("<option id=\"adress_'.$key.'\" value=\"'.$addressSelector.'\">'.$LegalName.', '.$address.', '.$postCode.' '.$city.'</option>");
//            jQuery("button[type=submit]").removeAttr("disabled");
//            jQuery("'.$petf.'").hide();';
//    }
//
//
//
//    }else{
//    	echo 'jQuery("'.$pnef.'").html("'.$error_msg.'");
//              jQuery("'.$petf.'").show();';
//    }
//
//    }
//endif;
//
//
///*
// *
// * PartPayment
// *
// */
//
//if (isset($_POST['paymentOptions'])):
//
//    require DIR_WS_LANGUAGES. $language .'/modules/payment/sveawebpay_partpay.php';
//
//    $sveaConf = getCountryConfigPP($order->info['currency']) ;
//
//    //Order rows
//    foreach($order->products as $i => $Item) {
//
//    $orderRowArr = Array(
//              "ArticleNr" => $i,
//              "Description" => utf8_encode($Item['name']),
//              "PricePerUnit" => $Item['price'],
//              "NrOfUnits" => $Item['qty'],
//              "Unit" => "st",
//              "VatPercent" => $Item['tax'],
//              "DiscountPercent" => 0
//            );
//
//    if (isset($clientInvoiceRows)){
//        $clientInvoiceRows[$i] = $orderRowArr;
//    }else{
//        $clientInvoiceRows[] = $orderRowArr;
//    }
//    }
//
//    //The createOrder Data
//    $request = Array(
//    	"request" => Array(
//          "Auth" => Array(
//            "Username" => $sveaConf['username'],
//            "Password" => $sveaConf['password'],
//            "ClientNumber" => $sveaConf['clientno']
//           ),
//          "Amount" => 0,
//          "InvoiceRows" => array('ClientInvoiceRowInfo' => $clientInvoiceRows)
//        )
//    );
//
//    //Call Soap
//    $client = new SoapClient( $svea_server );
//
//     //Make soap call to below method using above data
//    $svea_req = $client->GetPaymentPlanOptions( $request);
//
//    $response = $svea_req->GetPaymentPlanOptionsResult->PaymentPlanOptions;
//
//    echo 'jQuery("#paymentOptions").empty();';
//
//    foreach ($svea_req->GetPaymentPlanOptionsResult->PaymentPlanOptions->PaymentPlanOption as $key => $ss){
//
//    	echo 'jQuery("#paymentOptions").append("<option id=\"paymentOption'.$key.'\" value=\"'.$ss->CampainCode.'\">'.$ss->Description.'</option>");';
//    }
//
//    echo 'jQuery("#paymentOptions").show();';
//
//endif;
?>