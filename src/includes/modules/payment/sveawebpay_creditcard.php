<?php
/**
 * SVEAWEBPAY PAYMENT MODULE FOR osCommerce
 */

// Include Svea php integration package files
require_once(DIR_FS_CATALOG . 'ext/modules/payment/svea/Includes.php');         // use new php integration package
require_once(DIR_FS_CATALOG . 'sveawebpay_config.php');     // sveaConfig implementation
require_once(DIR_FS_CATALOG . 'sveawebpay_common.php');     // osCommerce module common functions

class sveawebpay_creditcard {

    function sveawebpay_creditcard() {
        global $order;

        $this->code = 'sveawebpay_creditcard';
        $this->version = "5";

//        $this->form_action_url = (MODULE_PAYMENT_SWPCREDITCARD_STATUS == 'True') ? 'https://test.sveaekonomi.se/webpay/payment' : 'https://webpay.sveaekonomi.se/webpay/payment';
        $this->title = MODULE_PAYMENT_SWPCREDITCARD_TEXT_TITLE;
        $this->description = MODULE_PAYMENT_SWPCREDITCARD_TEXT_DESCRIPTION;
        $this->enabled = ((MODULE_PAYMENT_SWPCREDITCARD_STATUS == 'True') ? true : false);
        $this->sort_order = MODULE_PAYMENT_SWPCREDITCARD_SORT_ORDER;
        $this->default_currency = MODULE_PAYMENT_SWPCREDITCARD_DEFAULT_CURRENCY;
        $this->allowed_currencies = explode(',', MODULE_PAYMENT_SWPCREDITCARD_ALLOWED_CURRENCIES);
        $this->display_images = ((MODULE_PAYMENT_SWPCREDITCARD_IMAGES == 'True') ? true : false);
        $this->ignore_list = explode(',', MODULE_PAYMENT_SWPCREDITCARD_IGNORE);
//        if ((int) MODULE_PAYMENT_SWPCREDITCARD_ORDER_STATUS_ID > 0)
//            $this->order_status = MODULE_PAYMENT_SWPCREDITCARD_ORDER_STATUS_ID;
//        if (is_object($order))
//            $this->update_status();
    }

//    function update_status() {
//        global $db, $order, $currencies, $messageStack;
//
//        // update internal currency
//        $this->default_currency = MODULE_PAYMENT_SWPCREDITCARD_DEFAULT_CURRENCY;
//        $this->allowed_currencies = explode(',', MODULE_PAYMENT_SWPCREDITCARD_ALLOWED_CURRENCIES);
//
//        // do not use this module if any of the allowed currencies are not set in osCommerce
//        foreach ($this->allowed_currencies as $currency) {
//            if (!is_array($currencies->currencies[strtoupper($currency)])) {
//                $this->enabled = false;
//                $messageStack->add('header', ERROR_ALLOWED_CURRENCIES_NOT_DEFINED, 'error');
//            }
//        }
//
//        // do not use this module if the default currency is not among the allowed
//        if (!in_array($this->default_currency, $this->allowed_currencies)) {
//            $this->enabled = false;
//            $messageStack->add('header', ERROR_DEFAULT_CURRENCY_NOT_ALLOWED, 'error');
//        }
//
//        // do not use this module if the geograhical zone is set and we are not in it
//        if (($this->enabled == true) && ((int) MODULE_PAYMENT_SWPCREDITCARD_ZONE > 0)) {
//            $check_flag = false;
//            $check_query = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_SWPCREDITCARD_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
//
//            while (!$check_query->EOF) {
//                if ($check_query->fields['zone_id'] < 1) {
//                    $check_flag = true;
//                    break;
//                } elseif ($check_query->fields['zone_id'] == $order->billing['zone_id']) {
//                    $check_flag = true;
//                    break;
//                }
//                $check_query->MoveNext();
//            }
//
//            if ($check_flag == false)
//                $this->enabled = false;
//        }
//    }

    function javascript_validation() {
        return false;
    }

    // sets information displayed when choosing between payment options
    function selection() {
        global $order, $currencies;

        $fields = array();


        if ($order->customer['country']['iso_code_2'] == "SE") {
            $fields[] = array('title' => '<img src=images/Svea/SVEACARD_SE.png />', 'field' => '<img src=images/Svea/KORTCERT.png /><img src=images/Svea/AMEX.png /><img src=images/Svea/DINERS.png />');
        } else {
            $fields[] = array('title' => '<img src=images/Svea/SVEACARD.png />', 'field' => '<img src=images/Svea/KORTCERT.png /><img src=images/Svea/AMEX.png /><img src=images/Svea/DINERS.png />');
        }

        if (isset($_REQUEST['payment_error']) && $_REQUEST['payment_error'] == 'sveawebpay_creditcard') { // is set in before_process() on failed payment
            $fields[] = array('title' => '<span style="color:red">' . $_SESSION['SWP_ERROR'] . '</span>', 'field' => '');
        }

        // handling fee
        if (isset($this->handling_fee) && $this->handling_fee > 0) {
            $paymentfee_cost = $this->handling_fee;
            if (substr($paymentfee_cost, -1) == '%')
                $fields[] = array('title' => sprintf(MODULE_PAYMENT_SWPCREDITCARD_HANDLING_APPLIES, $paymentfee_cost), 'field' => '');
            else {
                $tax_class = MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS;
                if (DISPLAY_PRICE_WITH_TAX == "true" && $tax_class > 0)
                    $paymentfee_tax = $paymentfee_cost * zen_get_tax_rate($tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']) / 100;
                $fields[] = array('title' => sprintf(MODULE_PAYMENT_SWPCREDITCARD_HANDLING_APPLIES, $currencies->format($paymentfee_cost + $paymentfee_tax)), 'field' => '');
            }
        }

        $_SESSION["swp_order_info_pre_coupon"] = serialize($order->info);  // store order info needed to reconstruct amount pre coupon later

        return array('id' => $this->code,
            'module' => $this->title,
            'fields' => $fields);
    }

    function pre_confirmation_check() {
        return false;
    }

    function confirmation() {
        return false;
    }

//    function process_button() {
//
//        global $db, $order, $order_totals, $language;
//
//        // calculate the order number
//        $new_order_rs = $db->Execute("select orders_id from " . TABLE_ORDERS . " order by orders_id desc limit 1");
//        $new_order_field = $new_order_rs->fields;
//        $client_order_number = ($new_order_field['orders_id'] + 1);
//
//        // localization parameters
//        if (isset($order->billing['country']['iso_code_2'])) {
//            $user_country = $order->billing['country']['iso_code_2'];
//        }
//        // no billing address set, fallback to session country_id
//        else {
//            $country = zen_get_countries_with_iso_codes($_SESSION['customer_country_id']);
//            $user_country = $country['countries_iso_code_2'];
//        }
//
//        $user_language = $db->Execute("select code from " . TABLE_LANGUAGES . " where directory = '" . $language . "'");
//        $user_language = $user_language->fields['code'];
//
//
//        // switch to default currency if the customers currency is not supported
//        $currency = $order->info['currency'];
//        if (!in_array($currency, $this->allowed_currencies)) {
//            $currency = $this->default_currency;
//        }
//
//        $sveaConfig = (MODULE_PAYMENT_SWPCREDITCARD_MODE === 'Test') ? new ZenCartSveaConfigTest() : new ZenCartSveaConfigProd();
//
//        // Create and initialize order object, using either test or production configuration
//        $swp_order = WebPay::createOrder($sveaConfig)
//                ->setCountryCode($user_country)
//                ->setCurrency($currency)                       //Required for card & direct payment and PayPage payment.
//                ->setClientOrderNumber($client_order_number)   //Required for card & direct payment, PaymentMethod payment and PayPage payments
//                ->setOrderDate(date('c'))                      //Required for synchronous payments
//        ;
//
//
//        //
//        // for each item in cart, create WebPayItem::orderRow objects and add to order
//        foreach ($order->products as $productId => $product) {
//
//            // convert_to_currency
//            $amount_ex_vat = floatval($this->convert_to_currency(round($product['final_price'], 2), $currency));
//            $swp_order->addOrderRow(
//                    WebPayItem::orderRow()
//                            ->setQuantity($product['qty'])          //Required
//                            ->setAmountExVat($amount_ex_vat)          //Optional, see info above
//                            ->setVatPercent(intval($product['tax']))  //Optional, see info above
//                            ->setDescription($product['name'])        //Optional
//            );
//        }
//
//        //
//        // handle order total modules
//        // i.e shipping fee, handling fee items
//        foreach ($order_totals as $ot_id => $order_total) {
//
//            switch ($order_total['code']) {
//                case in_array($order_total['code'], $this->ignore_list):
//                case 'ot_subtotal':
//                case 'ot_total':
//                case 'ot_tax':
//                    // do nothing
//                    break;
//
//                //
//                // if shipping fee, create WebPayItem::shippingFee object and add to order
//                case 'ot_shipping':
//
//                    // makes use of zencart $order-info[] shipping information to populate object
//                    // shop shows prices including tax, take this into accord when calculating tax
//                    if (DISPLAY_PRICE_WITH_TAX == 'false') {
//                        $amountExVat = $order->info['shipping_cost'];
//                        $amountIncVat = $order->info['shipping_cost'] + $order->info['shipping_tax'];
//                    } else {
//                        $amountExVat = $order->info['shipping_cost'] - $order->info['shipping_tax'];
//                        $amountIncVat = $order->info['shipping_cost'];
//                    }
//
//                    // add WebPayItem::shippingFee to swp_order object
//                    $swp_order->addFee(
//                            WebPayItem::shippingFee()
//                                    ->setDescription($order->info['shipping_method'])
//                                    ->setAmountExVat($amountExVat)
//                                    ->setAmountIncVat($amountIncVat)
//                    );
//                    break;
//
//                //
//                // if handling fee applies, create WebPayItem::invoiceFee object and add to order
//                case 'sveawebpay_handling_fee' :
//
//                    // is the handling_fee module activated?
//                    if (isset($this->handling_fee) && $this->handling_fee > 0) {
//
//                        // handlingfee expressed as percentage?
//                        if (substr($this->handling_fee, -1) == '%') {
//
//                            // sum of products + shipping * handling_fee as percentage
//                            $hf_percentage = floatval(substr($this->handling_fee, 0, -1));
//
//                            $hf_price = ($order->info['subtotal'] + $order->info['shipping_cost']) * ($hf_percentage / 100.0);
//                        }
//                        // handlingfee expressed as absolute amount (incl. tax)
//                        else {
//                            $hf_price = $this->convert_to_currency(floatval($this->handling_fee), $currency);
//                        }
//                        $hf_taxrate = zen_get_tax_rate(MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
//
//                        // add WebPayItem::invoiceFee to swp_order object
//                        $swp_order->addFee(
//                                WebPayItem::invoiceFee()
//                                        ->setDescription()
//                                        ->setAmountExVat($hf_price)
//                                        ->setVatPercent($hf_taxrate)
//                        );
//                    }
//                    break;
//
//                case 'ot_coupon':
//                    // zencart coupons are made out as either amount x.xx or a percentage y%.
//                    // Both of these are calculated by zencart via the order total module ot_coupon.php and show up in the
//                    // corresponding $order_totals[...]['value'] field.
//                    //
//                    // Depending on the module settings the value may differ, Svea assumes that the (zc 1.5.1) default settings
//                    // are being used:
//                    //
//                    // admin/ot_coupon module setting -- include shipping: false, include tax: false, re-calculate tax: standard
//                    //
//                    // The value contains the total discount amount including tax iff configuration display prices with tax is set to true:
//                    //
//                    // admin/configuration setting -- display prices with tax: true => ot_coupon['value'] includes vat, if false, excludes vat
//                    //
//                    // Example:
//                    // zc adds an ot_coupon with value of 20 for i.e. a 10% discount on an order of 100 +(25%) + 100 (+6%).
//                    // This discount seems to be split in equal parts over the two order item vat rates:
//                    // 90*1,25 + 90*1,06 = 112,5 + 95,4 = 207,90, to which the shipping fee of 4 (+25%) is added. The total is 212,90
//                    // ot_coupon['value'] is 23,10 iff display prices incuding tax = true, else ot_coupon['value'] = 20
//                    //
//                    // We handle the coupons by adding a FixedDiscountRow for the amount, specified ex vat. The package
//                    // handles the vat calculations.
//                    // if display price with tax is not set, svea's package calculations match zencart's and we can use value right away
//                    if (DISPLAY_PRICE_WITH_TAX == 'false') {
//                        $swp_order->addDiscount(
//                                WebPayItem::fixedDiscount()
//                                        ->setAmountExVat($order_total['value']) // $amountExVat works iff display prices with tax = false in shop
//                                        ->setDescription($order_total['title'])
//                        );
//                    }
//                    // we need to determine the order discount ex. vat if display prices with tax is set to true,
//                    // the ot_coupon module calculate_deductions() method returns a value including tax. We try to
//                    // reconstruct the amount using the stored order info and the order_totals entries
//                    else {
//                        $swp_order_info_pre_coupon = unserialize($_SESSION["swp_order_info_pre_coupon"]);
//                        $pre_coupon_subtotal_ex_tax = $swp_order_info_pre_coupon['subtotal'] - $swp_order_info_pre_coupon['tax'];
//
//                        foreach ($order_totals as $key => $ot) {
//                            if ($ot['code'] === 'ot_subtotal') {
//                                $order_totals_subtotal_ex_tax = $ot['value'];
//                            }
//                        }
//                        foreach ($order_totals as $key => $ot) {
//                            if ($ot['code'] === 'ot_tax') {
//                                $order_totals_subtotal_ex_tax -= $ot['value'];
//                            }
//                        }
//                        foreach ($order_totals as $key => $ot) {
//                            if ($ot['code'] === 'ot_coupon') {
//                                $order_totals_subtotal_ex_tax -= $ot['value'];
//                            }
//                        }
//
//                        $value_from_subtotals = isset($order_totals_subtotal_ex_tax) ?
//                                ($pre_coupon_subtotal_ex_tax - $order_totals_subtotal_ex_tax) : $order_total['value']; // 'value' fallback
//                        // if display_price_with tax is set to true && the coupon was specified as a fixed amount
//                        // zencart's math doesn't match svea's, so we force the discount to use the the shop's vat
//                        $coupon = $db->Execute("select * from " . TABLE_COUPONS . " where coupon_id = '" . (int) $_SESSION['cc_id'] . "'");
//
//                        // coupon_type is F for coupons specified with a fixed amount
//                        if ($coupon->fields['coupon_type'] == 'F') {
//
//                            // calculate the vatpercent from zencart's amount: discount vat/discount amount ex vat
//                            $zencartDiscountVatPercent =
//                                    ($order_total['value'] - $coupon->fields['coupon_amount']) / $coupon->fields['coupon_amount'] * 100;
//
//
//                            // split $zencartDiscountVatPercent into allowed values
//                            $taxRates = Svea\Helper::getTaxRatesInOrder($swp_order);
//                            $discountRows = Svea\Helper::splitMeanToTwoTaxRates($coupon->fields['coupon_amount'], $zencartDiscountVatPercent, $order_total['title'], $order_total['title'], $taxRates);
//
//                            foreach ($discountRows as $row) {
//                                $swp_order = $swp_order->addDiscount($row);
//                            }
//                        }
//                        // if coupon specified as a percentage, or as a fixed amount and prices are ex vat.
//                        else {
//                            $swp_order->addDiscount(
//                                    WebPayItem::fixedDiscount()
//                                            ->setAmountExVat($value_from_subtotals)
//                                            ->setDescription($order_total['title'])
//                            );
//                        }
//                    }
//                    break;
//
//                // default case attempt to handle 'unknown' items from other plugins, treating negatives as discount rows, positives as fees
//                default:
//                    $order_total_obj = $GLOBALS[$order_total['code']];
//                    $tax_rate = zen_get_tax_rate($order_total_obj->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
//
//                    // if displayed WITH tax, REDUCE the value since it includes tax
//                    if (DISPLAY_PRICE_WITH_TAX == 'true') {
//                        $order_total['value'] = (strip_tags($order_total['value']) / ((100 + $tax_rate) / 100));
//                    }
//
//                    // write negative amounts as FixedDiscount with the given tax rate, write positive amounts as HandlingFee
//                    if ($order_total['value'] < 0) {
//                        $swp_order->addDiscount(
//                                WebPayItem::fixedDiscount()
//                                        ->setAmountExVat(-1 * $this->convert_to_currency(strip_tags($order_total['value']), $currency)) // given as positive amount
//                                        ->setVatPercent($tax_rate)  //Optional, see info above
//                                        ->setDescription($order_total['title'])        //Optional
//                        );
//                    } else {
//                        $swp_order->addFee(
//                                WebPayItem::invoiceFee()
//                                        ->setAmountExVat($this->convert_to_currency(strip_tags($order_total['value']), $currency))
//                                        ->setVatPercent($tax_rate)  //Optional, see info above
//                                        ->setDescription($order_total['title'])        //Optional
//                        );
//                    }
//                    break;
//            }
//        }
//
//        $swp_form = $swp_order->usePaymentMethod(PaymentMethod::KORTCERT)
//                ->setCancelUrl(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true))
//                ->setReturnUrl(zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL'))
//                ->getPaymentForm();
//
//        //return $process_button_string;
//        return $swp_form->htmlFormFieldsAsArray['input_merchantId'] .
//                $swp_form->htmlFormFieldsAsArray['input_message'] .
//                $swp_form->htmlFormFieldsAsArray['input_mac'];
//    }
//
//    function before_process() {
//        global $order;
//
//        if ($_REQUEST['response']) {
//
//            // Include Svea php integration package files
//            require_once(DIR_FS_CATALOG . 'svea/Includes.php');
//
//            // localization parameters
//            if (isset($order->billing['country']['iso_code_2'])) {
//                $user_country = $order->billing['country']['iso_code_2'];
//            }
//            // no billing address set, fallback to session country_id
//            else {
//                $country = zen_get_countries_with_iso_codes($_SESSION['customer_country_id']);
//                $user_country = $country['countries_iso_code_2'];
//            }
//
//            // put response into responsehandler
//            $sveaConfig = (MODULE_PAYMENT_SWPCREDITCARD_MODE === 'Test') ? new ZenCartSveaConfigTest() : new ZenCartSveaConfigProd();
//
//            $swp_respObj = new SveaResponse($_REQUEST, $user_country, $sveaConfig); // returns HostedPaymentResponse
//            $swp_response = $swp_respObj->response;
//            // check for bad response
//            if ($swp_response->resultcode == '0') {
//                die('Response failed authorization. AC not valid or Response is not recognized');
//            }
//
//            // response ok, check if payment accepted
//            else {
//
//                // handle failed payments
//                if (!$swp_response->accepted === true) {
//
//                    switch ($swp_response->resultcode) {
//                        case 100:
//                            $_SESSION['SWP_ERROR'] = ERROR_CODE_100;
//                            break;
//                        case 105:
//                            $_SESSION['SWP_ERROR'] = ERROR_CODE_105;
//                            break;
//                        case 106:
//                            $_SESSION['SWP_ERROR'] = ERROR_CODE_106;
//                            break;
//                        case 107:
//                            $_SESSION['SWP_ERROR'] = ERROR_CODE_107;
//                            break;
//                        case 108:
//                            $_SESSION['SWP_ERROR'] = ERROR_CODE_108;
//                            break;
//                        case 109:
//                            $_SESSION['SWP_ERROR'] = ERROR_CODE_109;
//                            break;
//                        case 110:
//                            $_SESSION['SWP_ERROR'] = ERROR_CODE_110;
//                            break;
//                        case 113:
//                            $_SESSION['SWP_ERROR'] = ERROR_CODE_113;
//                            break;
//                        case 114:
//                            $_SESSION['SWP_ERROR'] = ERROR_CODE_114;
//                            break;
//                        case 121:
//                            $_SESSION['SWP_ERROR'] = ERROR_CODE_121;
//                            break;
//                        case 124:
//                            $_SESSION['SWP_ERROR'] = ERROR_CODE_124;
//                            break;
//                        case 143:
//                            $_SESSION['SWP_ERROR'] = ERROR_CODE_143;
//                            break;
//                        default:
//                            $_SESSION['SWP_ERROR'] =
//                                    ERROR_CODE_DEFAULT . $swp_response->resultcode;
//                            break;
//                    }
//
//                    if (isset($_SESSION['payment_attempt'])) {  // prevents repeated payment attempts interpreted by zencart as slam attack
//                        unset($_SESSION['payment_attempt']);
//                    }
//
//                    $payment_error_return = 'payment_error=sveawebpay_creditcard'; // used in conjunction w/SWP_ERROR to avoid reason showing up in url
//                    zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return));
//                }
//
//                // handle successful payments
//                else {
//
//                    // payment request succeded, store response in session
//                    if ($swp_response->accepted === true) {
//
//                        if (isset($_SESSION['SWP_ERROR'])) {
//                            unset($_SESSION['SWP_ERROR']);
//                        }
//
//                        // (with creditcard payments, shipping and billing addresses are unchanged from customer entries)
//                        // save the response object
//                        $_SESSION["swp_response"] = serialize($swp_response);
//                    }
//                }
//            }
//        }
//    }
//
//    // if payment accepted, insert order into database
//    function after_process() {
//        global $insert_id, $order;
//
//        // retrieve response object from before_process()
//        require_once(DIR_FS_CATALOG . 'svea/Includes.php');
//        $swp_response = unserialize($_SESSION["swp_response"]);
//
//        // insert zencart order into database
//        $sql_data_array = array('orders_id' => $insert_id,
//            'orders_status_id' => $order->info['order_status'],
//            'date_added' => 'now()',
//            'customer_notified' => 0,
//            'comments' => 'Accepted by Svea ' . date("Y-m-d G:i:s") . ' Security Number #: ' .
//            isset($swp_response->sveaOrderId) ?
//                    $swp_response->sveaOrderId : $swp_response->transactionId //if request to webservice, use sveaOrderId, if hosted use transactionId
//        );
//        zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
//
//        //
//        // clean up our session variables set during checkout   //$SESSION[swp_*
//        unset($_SESSION['swp_order']);
//        unset($_SESSION['swp_response']);
//
//        return false;
//    }
//
//    // sets error message to the GET error value
//    function get_error() {
//        return array('title' => ERROR_MESSAGE_PAYMENT_FAILED,
//            'error' => stripslashes(urldecode($_GET['error'])));
//    }
//
    
    // standard check if installed function
    function check() {
        if (!isset($this->_check)) {
            $check_rs = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_SWPCREDITCARD_STATUS'");
            $this->_check = (tep_db_num_rows($check_rs) > 0);
        }
        return $this->_check;
    }
    

    // insert configuration keys here
    function install() {
        $common = "insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added";
        tep_db_query($common . ", set_function) values ('Enable Svea Card Payment Module', 'MODULE_PAYMENT_SWPCREDITCARD_STATUS', 'True', 'Do you want to accept Svea payments?', '6', '0', now(), 'tep_cfg_select_option(array(\'True\', \'False\'), ')");
        tep_db_query($common . ") values ('Svea Card Merchant ID', 'MODULE_PAYMENT_SWPCREDITCARD_MERCHANT_ID', '1130', 'The Merchant ID', '6', '0', now())");
        tep_db_query($common . ") values ('Svea Card Secret Word', 'MODULE_PAYMENT_SWPCREDITCARD_SW', '8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3', 'The Secret word', '6', '0', now())");
        tep_db_query($common . ") values ('Svea Card Test Merchant ID', 'MODULE_PAYMENT_SWPCREDITCARD_MERCHANT_ID_TEST', '1130', 'The Merchant ID', '6', '0', now())");
        tep_db_query($common . ") values ('Svea Card Test Secret Word', 'MODULE_PAYMENT_SWPCREDITCARD_SW_TEST', '8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3', 'The Secret word', '6', '0', now())");
        tep_db_query($common . ", set_function) values ('Transaction Mode', 'MODULE_PAYMENT_SWPCREDITCARD_MODE', 'Test', 'Transaction mode used for processing orders. Production should be used for a live working cart. Test for testing.', '6', '0', now(), 'tep_cfg_select_option(array(\'Production\', \'Test\'), ')");
        tep_db_query($common . ") values ('Accepted Currencies', 'MODULE_PAYMENT_SWPCREDITCARD_ALLOWED_CURRENCIES','SEK,NOK,DKK,EUR', 'The accepted currencies, separated by commas.  These <b>MUST</b> exist within your currencies table, along with the correct exchange rates.','6','0',now())");
        tep_db_query($common . ", set_function) values ('Default Currency', 'MODULE_PAYMENT_SWPCREDITCARD_DEFAULT_CURRENCY', 'SEK', 'Default currency used, if the customer uses an unsupported currency it will be converted to this. This should also be in the supported currencies list.', '6', '0', now(), 'tep_cfg_select_option(array(\'SEK\',\'NOK\',\'DKK\',\'EUR\'), ')");
//        tep_db_query($common . ", set_function, use_function) values ('Set Order Status', 'MODULE_PAYMENT_SWPCREDITCARD_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', now(), 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name')");
        tep_db_query($common . ", set_function) values ('Display SveaWebPay Images', 'MODULE_PAYMENT_SWPCREDITCARD_IMAGES', 'True', 'Do you want to display SveaWebPay images when choosing between payment options?', '6', '0', now(), 'tep_cfg_select_option(array(\'True\', \'False\'), ')");
        tep_db_query($common . ") values ('Ignore OT list', 'MODULE_PAYMENT_SWPCREDITCARD_IGNORE','ot_pretotal', 'Ignore the following order total codes, separated by commas.','6','0',now())");
        tep_db_query($common . ", set_function, use_function) values ('Payment Zone', 'MODULE_PAYMENT_SWPCREDITCARD_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', now(), 'tep_cfg_pull_down_zone_classes(', 'tep_get_zone_class_title')");
        tep_db_query($common . ") values ('Sort order of display.', 'MODULE_PAYMENT_SWPCREDITCARD_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    // standard uninstall function
    function remove() {
        tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    // must perfectly match keys inserted in install function
    function keys() {
        return array(
            'MODULE_PAYMENT_SWPCREDITCARD_STATUS',
            'MODULE_PAYMENT_SWPCREDITCARD_MERCHANT_ID',
            'MODULE_PAYMENT_SWPCREDITCARD_SW',
            'MODULE_PAYMENT_SWPCREDITCARD_MERCHANT_ID_TEST',
            'MODULE_PAYMENT_SWPCREDITCARD_SW_TEST',
            'MODULE_PAYMENT_SWPCREDITCARD_MODE',
            'MODULE_PAYMENT_SWPCREDITCARD_ALLOWED_CURRENCIES',
            'MODULE_PAYMENT_SWPCREDITCARD_DEFAULT_CURRENCY',
//            'MODULE_PAYMENT_SWPCREDITCARD_ORDER_STATUS_ID',
            'MODULE_PAYMENT_SWPCREDITCARD_IMAGES',
            'MODULE_PAYMENT_SWPCREDITCARD_IGNORE',
            'MODULE_PAYMENT_SWPCREDITCARD_ZONE',
            'MODULE_PAYMENT_SWPCREDITCARD_SORT_ORDER'
        );
    }
//
//    /**
//     *
//     * @global type $currencies
//     * @param float $value amount to convert
//     * @param string $currency as three-letter $iso3166 country code
//     * @param boolean $no_number_format if true, don't convert the to i.e. Swedish decimal indicator (",")
//     *    Having a non-standard decimal may cause i.e. number conversion with floatval() to truncate fractions.
//     * @return type
//     */
//    function convert_to_currency($value, $currency, $no_number_format = true) {
//        global $currencies;
//
//        // item price is ALWAYS given in internal price from the products DB, so just multiply by currency rate from currency table
//        $rounded_value = zen_round($value * $currencies->currencies[$currency]['value'], $currencies->currencies[$currency]['decimal_places']);
//
//        return $no_number_format ? $rounded_value : number_format($rounded_value, $currencies->currencies[$currency]['decimal_places'], $currencies->currencies[$currency]['decimal_point'], $currencies->currencies[$currency]['thousands_point']);
//    }
//
//    function getCountryName($iso3166) {
//
//        // countrynames from https://github.com/johannesl/Internationalization, thanks!
//        $countrynames = array(
//            "AF" => "Afghanistan",
//            "AX" => "\xc3\x85land Islands",
//            "AL" => "Albania",
//            "DZ" => "Algeria",
//            "AS" => "American Samoa",
//            "AD" => "Andorra",
//            "AO" => "Angola",
//            "AI" => "Anguilla",
//            "AQ" => "Antarctica",
//            "AG" => "Antigua and Barbuda",
//            "AR" => "Argentina",
//            "AM" => "Armenia",
//            "AW" => "Aruba",
//            "AU" => "Australia",
//            "AT" => "Austria",
//            "AZ" => "Azerbaijan",
//            "BS" => "Bahamas",
//            "BH" => "Bahrain",
//            "BD" => "Bangladesh",
//            "BB" => "Barbados",
//            "BY" => "Belarus",
//            "BE" => "Belgium",
//            "BZ" => "Belize",
//            "BJ" => "Benin",
//            "BM" => "Bermuda",
//            "BT" => "Bhutan",
//            "BO" => "Bolivia, Plurinational State of",
//            "BQ" => "Bonaire, Sint Eustatius and Saba",
//            "BA" => "Bosnia and Herzegovina",
//            "BW" => "Botswana",
//            "BV" => "Bouvet Island",
//            "BR" => "Brazil",
//            "IO" => "British Indian Ocean Territory",
//            "BN" => "Brunei Darussalam",
//            "BG" => "Bulgaria",
//            "BF" => "Burkina Faso",
//            "BI" => "Burundi",
//            "KH" => "Cambodia",
//            "CM" => "Cameroon",
//            "CA" => "Canada",
//            "CV" => "Cape Verde",
//            "KY" => "Cayman Islands",
//            "CF" => "Central African Republic",
//            "TD" => "Chad",
//            "CL" => "Chile",
//            "CN" => "China",
//            "CX" => "Christmas Island",
//            "CC" => "Cocos (Keeling) Islands",
//            "CO" => "Colombia",
//            "KM" => "Comoros",
//            "CG" => "Congo",
//            "CD" => "Congo, The Democratic Republic of the",
//            "CK" => "Cook Islands",
//            "CR" => "Costa Rica",
//            "CI" => "C\xc3\xb4te d'Ivoire",
//            "HR" => "Croatia",
//            "CU" => "Cuba",
//            "CW" => "Cura\xc3\xa7ao",
//            "CY" => "Cyprus",
//            "CZ" => "Czech Republic",
//            "DK" => "Denmark",
//            "DJ" => "Djibouti",
//            "DM" => "Dominica",
//            "DO" => "Dominican Republic",
//            "EC" => "Ecuador",
//            "EG" => "Egypt",
//            "SV" => "El Salvador",
//            "GQ" => "Equatorial Guinea",
//            "ER" => "Eritrea",
//            "EE" => "Estonia",
//            "ET" => "Ethiopia",
//            "FK" => "Falkland Islands (Malvinas)",
//            "FO" => "Faroe Islands",
//            "FJ" => "Fiji",
//            "FI" => "Finland",
//            "FR" => "France",
//            "GF" => "French Guiana",
//            "PF" => "French Polynesia",
//            "TF" => "French Southern Territories",
//            "GA" => "Gabon",
//            "GM" => "Gambia",
//            "GE" => "Georgia",
//            "DE" => "Germany",
//            "GH" => "Ghana",
//            "GI" => "Gibraltar",
//            "GR" => "Greece",
//            "GL" => "Greenland",
//            "GD" => "Grenada",
//            "GP" => "Guadeloupe",
//            "GU" => "Guam",
//            "GT" => "Guatemala",
//            "GG" => "Guernsey",
//            "GN" => "Guinea",
//            "GW" => "Guinea-Bissau",
//            "GY" => "Guyana",
//            "HT" => "Haiti",
//            "HM" => "Heard Island and McDonald Islands",
//            "VA" => "Holy See (Vatican City State)",
//            "HN" => "Honduras",
//            "HK" => "Hong Kong",
//            "HU" => "Hungary",
//            "IS" => "Iceland",
//            "IN" => "India",
//            "ID" => "Indonesia",
//            "IR" => "Iran, Islamic Republic of",
//            "IQ" => "Iraq",
//            "IE" => "Ireland",
//            "IM" => "Isle of Man",
//            "IL" => "Israel",
//            "IT" => "Italy",
//            "JM" => "Jamaica",
//            "JP" => "Japan",
//            "JE" => "Jersey",
//            "JO" => "Jordan",
//            "KZ" => "Kazakhstan",
//            "KE" => "Kenya",
//            "KI" => "Kiribati",
//            "KP" => "Korea, Democratic People's Republic of",
//            "KR" => "Korea, Republic of",
//            "KW" => "Kuwait",
//            "KG" => "Kyrgyzstan",
//            "LA" => "Lao People's Democratic Republic",
//            "LV" => "Latvia",
//            "LB" => "Lebanon",
//            "LS" => "Lesotho",
//            "LR" => "Liberia",
//            "LY" => "Libya",
//            "LI" => "Liechtenstein",
//            "LT" => "Lithuania",
//            "LU" => "Luxembourg",
//            "MO" => "Macao",
//            "MK" => "Macedonia, The Former Yugoslav Republic of",
//            "MG" => "Madagascar",
//            "MW" => "Malawi",
//            "MY" => "Malaysia",
//            "MV" => "Maldives",
//            "ML" => "Mali",
//            "MT" => "Malta",
//            "MH" => "Marshall Islands",
//            "MQ" => "Martinique",
//            "MR" => "Mauritania",
//            "MU" => "Mauritius",
//            "YT" => "Mayotte",
//            "MX" => "Mexico",
//            "FM" => "Micronesia, Federated States of",
//            "MD" => "Moldova, Republic of",
//            "MC" => "Monaco",
//            "MN" => "Mongolia",
//            "ME" => "Montenegro",
//            "MS" => "Montserrat",
//            "MA" => "Morocco",
//            "MZ" => "Mozambique",
//            "MM" => "Myanmar",
//            "NA" => "Namibia",
//            "NR" => "Nauru",
//            "NP" => "Nepal",
//            "NL" => "Netherlands",
//            "NC" => "New Caledonia",
//            "NZ" => "New Zealand",
//            "NI" => "Nicaragua",
//            "NE" => "Niger",
//            "NG" => "Nigeria",
//            "NU" => "Niue",
//            "NF" => "Norfolk Island",
//            "MP" => "Northern Mariana Islands",
//            "NO" => "Norway",
//            "OM" => "Oman",
//            "PK" => "Pakistan",
//            "PW" => "Palau",
//            "PS" => "Palestine, State of",
//            "PA" => "Panama",
//            "PG" => "Papua New Guinea",
//            "PY" => "Paraguay",
//            "PE" => "Peru",
//            "PH" => "Philippines",
//            "PN" => "Pitcairn",
//            "PL" => "Poland",
//            "PT" => "Portugal",
//            "PR" => "Puerto Rico",
//            "QA" => "Qatar",
//            "RE" => "R\xc3\xa9union",
//            "RO" => "Romania",
//            "RU" => "Russian Federation",
//            "RW" => "Rwanda",
//            "BL" => "Saint Barth\xc3\xa9lemy",
//            "SH" => "Saint Helena, Ascension and Tristan Da Cunha",
//            "KN" => "Saint Kitts and Nevis",
//            "LC" => "Saint Lucia",
//            "MF" => "Saint Martin (French part)",
//            "PM" => "Saint Pierre and Miquelon",
//            "VC" => "Saint Vincent and the Grenadines",
//            "WS" => "Samoa",
//            "SM" => "San Marino",
//            "ST" => "Sao Tome and Principe",
//            "SA" => "Saudi Arabia",
//            "SN" => "Senegal",
//            "RS" => "Serbia",
//            "SC" => "Seychelles",
//            "SL" => "Sierra Leone",
//            "SG" => "Singapore",
//            "SX" => "Sint Maarten (Dutch part)",
//            "SK" => "Slovakia",
//            "SI" => "Slovenia",
//            "SB" => "Solomon Islands",
//            "SO" => "Somalia",
//            "ZA" => "South Africa",
//            "GS" => "South Georgia and the South Sandwich Islands",
//            "SS" => "South Sudan",
//            "ES" => "Spain",
//            "LK" => "Sri Lanka",
//            "SD" => "Sudan",
//            "SR" => "Suriname",
//            "SJ" => "Svalbard and Jan Mayen",
//            "SZ" => "Swaziland",
//            "SE" => "Sweden",
//            "CH" => "Switzerland",
//            "SY" => "Syrian Arab Republic",
//            "TW" => "Taiwan, Province of China",
//            "TJ" => "Tajikistan",
//            "TZ" => "Tanzania, United Republic of",
//            "TH" => "Thailand",
//            "TL" => "Timor-Leste",
//            "TG" => "Togo",
//            "TK" => "Tokelau",
//            "TO" => "Tonga",
//            "TT" => "Trinidad and Tobago",
//            "TN" => "Tunisia",
//            "TR" => "Turkey",
//            "TM" => "Turkmenistan",
//            "TC" => "Turks and Caicos Islands",
//            "TV" => "Tuvalu",
//            "UG" => "Uganda",
//            "UA" => "Ukraine",
//            "AE" => "United Arab Emirates",
//            "GB" => "United Kingdom",
//            "US" => "United States",
//            "UM" => "United States Minor Outlying Islands",
//            "UY" => "Uruguay",
//            "UZ" => "Uzbekistan",
//            "VU" => "Vanuatu",
//            "VE" => "Venezuela, Bolivarian Republic of",
//            "VN" => "Viet Nam",
//            "VG" => "Virgin Islands, British",
//            "VI" => "Virgin Islands, U.S.",
//            "WF" => "Wallis and Futuna",
//            "EH" => "Western Sahara",
//            "YE" => "Yemen",
//            "ZM" => "Zambia",
//            "ZW" => "Zimbabwe"
//        );
//
//        return( array_key_exists($iso3166, $countrynames) ? $countrynames[$iso3166] : $iso3166 );
//    }
}
?>
