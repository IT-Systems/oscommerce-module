<?php //

define('SVEA_ORDERSTATUS_DELIVERED_ID', 3);     // need to be one of osC default to show up in history?
//define('SVEA_ORDERSTATUS_DELIVERED', 'Svea: Delivered');  // not used

/**
 * Class SveaOsCommerce contains various utility functions used by Svea osCommerce payment modules
 *
 * @author Kristian Grossman-Madsen
 */
class SveaOsCommerce {  
    
  /**
   *
   * @global type $currencies
   * @param float $value amount to convert
   * @param string $currency as three-letter $iso3166 country code
   * @param boolean $no_number_format if true, don't convert the to i.e. Swedish decimal indicator (",")
   *    Having a non-standard decimal may cause i.e. number conversion with floatval() to truncate fractions.
   * @return type
   */
    function convertToCurrency($value, $currency, $no_number_format = true) {
        global $currencies;

        // item price is ALWAYS given in internal price from the products DB, so just multiply by currency rate from currency table
        $rounded_value = tep_round($value * $currencies->currencies[$currency]['value'], $currencies->currencies[$currency]['decimal_places']);

        return $no_number_format ? $rounded_value : number_format(  $rounded_value,
                                                                    $currencies->currencies[$currency]['decimal_places'],
                                                                    $currencies->currencies[$currency]['decimal_point'],
                                                                    $currencies->currencies[$currency]['thousands_point']);
    }
   
    /**
     * get the customer country from 1) user billing address, if given, or 2) from shop country id in session
     */
    function getCountry() {
        global $order;       
        // try billing address country first
        if( isset( $order->billing['country']['iso_code_2'] ) ) {
            $user_country = $order->billing['country']['iso_code_2']; 
        }
        // no billing address set, fallback to session country_id
        else {
            $country = tep_get_countries_with_iso_codes( $_SESSION['customer_country_id'] );
            $user_country =  $country['countries_iso_code_2'];
        }
        return $user_country;
    }

    function getLanguage() {
        global $language;
        $user_language = tep_db_fetch_array(tep_db_query("select code from " . TABLE_LANGUAGES . " where directory = '" . $language . "'"));
        return $user_language['code'];
    }
    
    /**
     * Given iso 3166 country code, returns English country name.
     * 
     * @param string $iso3166
     * @return string english country name
     */
    function getCountryName( $iso3166 ) {

        // countrynames from https://github.com/johannesl/Internationalization, thanks!
        $countrynames = array(
            "AF"=>"Afghanistan",
            "AX"=>"\xc3\x85land Islands",
            "AL"=>"Albania",
            "DZ"=>"Algeria",
            "AS"=>"American Samoa",
            "AD"=>"Andorra",
            "AO"=>"Angola",
            "AI"=>"Anguilla",
            "AQ"=>"Antarctica",
            "AG"=>"Antigua and Barbuda",
            "AR"=>"Argentina",
            "AM"=>"Armenia",
            "AW"=>"Aruba",
            "AU"=>"Australia",
            "AT"=>"Austria",
            "AZ"=>"Azerbaijan",
            "BS"=>"Bahamas",
            "BH"=>"Bahrain",
            "BD"=>"Bangladesh",
            "BB"=>"Barbados",
            "BY"=>"Belarus",
            "BE"=>"Belgium",
            "BZ"=>"Belize",
            "BJ"=>"Benin",
            "BM"=>"Bermuda",
            "BT"=>"Bhutan",
            "BO"=>"Bolivia, Plurinational State of",
            "BQ"=>"Bonaire, Sint Eustatius and Saba",
            "BA"=>"Bosnia and Herzegovina",
            "BW"=>"Botswana",
            "BV"=>"Bouvet Island",
            "BR"=>"Brazil",
            "IO"=>"British Indian Ocean Territory",
            "BN"=>"Brunei Darussalam",
            "BG"=>"Bulgaria",
            "BF"=>"Burkina Faso",
            "BI"=>"Burundi",
            "KH"=>"Cambodia",
            "CM"=>"Cameroon",
            "CA"=>"Canada",
            "CV"=>"Cape Verde",
            "KY"=>"Cayman Islands",
            "CF"=>"Central African Republic",
            "TD"=>"Chad",
            "CL"=>"Chile",
            "CN"=>"China",
            "CX"=>"Christmas Island",
            "CC"=>"Cocos (Keeling) Islands",
            "CO"=>"Colombia",
            "KM"=>"Comoros",
            "CG"=>"Congo",
            "CD"=>"Congo, The Democratic Republic of the",
            "CK"=>"Cook Islands",
            "CR"=>"Costa Rica",
            "CI"=>"C\xc3\xb4te d'Ivoire",
            "HR"=>"Croatia",
            "CU"=>"Cuba",
            "CW"=>"Cura\xc3\xa7ao",
            "CY"=>"Cyprus",
            "CZ"=>"Czech Republic",
            "DK"=>"Denmark",
            "DJ"=>"Djibouti",
            "DM"=>"Dominica",
            "DO"=>"Dominican Republic",
            "EC"=>"Ecuador",
            "EG"=>"Egypt",
            "SV"=>"El Salvador",
            "GQ"=>"Equatorial Guinea",
            "ER"=>"Eritrea",
            "EE"=>"Estonia",
            "ET"=>"Ethiopia",
            "FK"=>"Falkland Islands (Malvinas)",
            "FO"=>"Faroe Islands",
            "FJ"=>"Fiji",
            "FI"=>"Finland",
            "FR"=>"France",
            "GF"=>"French Guiana",
            "PF"=>"French Polynesia",
            "TF"=>"French Southern Territories",
            "GA"=>"Gabon",
            "GM"=>"Gambia",
            "GE"=>"Georgia",
            "DE"=>"Germany",
            "GH"=>"Ghana",
            "GI"=>"Gibraltar",
            "GR"=>"Greece",
            "GL"=>"Greenland",
            "GD"=>"Grenada",
            "GP"=>"Guadeloupe",
            "GU"=>"Guam",
            "GT"=>"Guatemala",
            "GG"=>"Guernsey",
            "GN"=>"Guinea",
            "GW"=>"Guinea-Bissau",
            "GY"=>"Guyana",
            "HT"=>"Haiti",
            "HM"=>"Heard Island and McDonald Islands",
            "VA"=>"Holy See (Vatican City State)",
            "HN"=>"Honduras",
            "HK"=>"Hong Kong",
            "HU"=>"Hungary",
            "IS"=>"Iceland",
            "IN"=>"India",
            "ID"=>"Indonesia",
            "IR"=>"Iran, Islamic Republic of",
            "IQ"=>"Iraq",
            "IE"=>"Ireland",
            "IM"=>"Isle of Man",
            "IL"=>"Israel",
            "IT"=>"Italy",
            "JM"=>"Jamaica",
            "JP"=>"Japan",
            "JE"=>"Jersey",
            "JO"=>"Jordan",
            "KZ"=>"Kazakhstan",
            "KE"=>"Kenya",
            "KI"=>"Kiribati",
            "KP"=>"Korea, Democratic People's Republic of",
            "KR"=>"Korea, Republic of",
            "KW"=>"Kuwait",
            "KG"=>"Kyrgyzstan",
            "LA"=>"Lao People's Democratic Republic",
            "LV"=>"Latvia",
            "LB"=>"Lebanon",
            "LS"=>"Lesotho",
            "LR"=>"Liberia",
            "LY"=>"Libya",
            "LI"=>"Liechtenstein",
            "LT"=>"Lithuania",
            "LU"=>"Luxembourg",
            "MO"=>"Macao",
            "MK"=>"Macedonia, The Former Yugoslav Republic of",
            "MG"=>"Madagascar",
            "MW"=>"Malawi",
            "MY"=>"Malaysia",
            "MV"=>"Maldives",
            "ML"=>"Mali",
            "MT"=>"Malta",
            "MH"=>"Marshall Islands",
            "MQ"=>"Martinique",
            "MR"=>"Mauritania",
            "MU"=>"Mauritius",
            "YT"=>"Mayotte",
            "MX"=>"Mexico",
            "FM"=>"Micronesia, Federated States of",
            "MD"=>"Moldova, Republic of",
            "MC"=>"Monaco",
            "MN"=>"Mongolia",
            "ME"=>"Montenegro",
            "MS"=>"Montserrat",
            "MA"=>"Morocco",
            "MZ"=>"Mozambique",
            "MM"=>"Myanmar",
            "NA"=>"Namibia",
            "NR"=>"Nauru",
            "NP"=>"Nepal",
            "NL"=>"Netherlands",
            "NC"=>"New Caledonia",
            "NZ"=>"New Zealand",
            "NI"=>"Nicaragua",
            "NE"=>"Niger",
            "NG"=>"Nigeria",
            "NU"=>"Niue",
            "NF"=>"Norfolk Island",
            "MP"=>"Northern Mariana Islands",
            "NO"=>"Norway",
            "OM"=>"Oman",
            "PK"=>"Pakistan",
            "PW"=>"Palau",
            "PS"=>"Palestine, State of",
            "PA"=>"Panama",
            "PG"=>"Papua New Guinea",
            "PY"=>"Paraguay",
            "PE"=>"Peru",
            "PH"=>"Philippines",
            "PN"=>"Pitcairn",
            "PL"=>"Poland",
            "PT"=>"Portugal",
            "PR"=>"Puerto Rico",
            "QA"=>"Qatar",
            "RE"=>"R\xc3\xa9union",
            "RO"=>"Romania",
            "RU"=>"Russian Federation",
            "RW"=>"Rwanda",
            "BL"=>"Saint Barth\xc3\xa9lemy",
            "SH"=>"Saint Helena, Ascension and Tristan Da Cunha",
            "KN"=>"Saint Kitts and Nevis",
            "LC"=>"Saint Lucia",
            "MF"=>"Saint Martin (French part)",
            "PM"=>"Saint Pierre and Miquelon",
            "VC"=>"Saint Vincent and the Grenadines",
            "WS"=>"Samoa",
            "SM"=>"San Marino",
            "ST"=>"Sao Tome and Principe",
            "SA"=>"Saudi Arabia",
            "SN"=>"Senegal",
            "RS"=>"Serbia",
            "SC"=>"Seychelles",
            "SL"=>"Sierra Leone",
            "SG"=>"Singapore",
            "SX"=>"Sint Maarten (Dutch part)",
            "SK"=>"Slovakia",
            "SI"=>"Slovenia",
            "SB"=>"Solomon Islands",
            "SO"=>"Somalia",
            "ZA"=>"South Africa",
            "GS"=>"South Georgia and the South Sandwich Islands",
            "SS"=>"South Sudan",
            "ES"=>"Spain",
            "LK"=>"Sri Lanka",
            "SD"=>"Sudan",
            "SR"=>"Suriname",
            "SJ"=>"Svalbard and Jan Mayen",
            "SZ"=>"Swaziland",
            "SE"=>"Sweden",
            "CH"=>"Switzerland",
            "SY"=>"Syrian Arab Republic",
            "TW"=>"Taiwan, Province of China",
            "TJ"=>"Tajikistan",
            "TZ"=>"Tanzania, United Republic of",
            "TH"=>"Thailand",
            "TL"=>"Timor-Leste",
            "TG"=>"Togo",
            "TK"=>"Tokelau",
            "TO"=>"Tonga",
            "TT"=>"Trinidad and Tobago",
            "TN"=>"Tunisia",
            "TR"=>"Turkey",
            "TM"=>"Turkmenistan",
            "TC"=>"Turks and Caicos Islands",
            "TV"=>"Tuvalu",
            "UG"=>"Uganda",
            "UA"=>"Ukraine",
            "AE"=>"United Arab Emirates",
            "GB"=>"United Kingdom",
            "US"=>"United States",
            "UM"=>"United States Minor Outlying Islands",
            "UY"=>"Uruguay",
            "UZ"=>"Uzbekistan",
            "VU"=>"Vanuatu",
            "VE"=>"Venezuela, Bolivarian Republic of",
            "VN"=>"Viet Nam",
            "VG"=>"Virgin Islands, British",
            "VI"=>"Virgin Islands, U.S.",
            "WF"=>"Wallis and Futuna",
            "EH"=>"Western Sahara",
            "YE"=>"Yemen",
            "ZM"=>"Zambia",
            "ZW"=>"Zimbabwe"
        );
        return( array_key_exists( $iso3166, $countrynames) ? $countrynames[$iso3166] : "swp_error: getCountryCode: unknown country code" );
    }   
 
    /**
     * Given English country name, returns iso 3166 country code.
     * 
     * @param string $country
     * @return string iso 3166 country code
    */
    function getCountryCode( $country ) {

        // countrynames from https://github.com/johannesl/Internationalization, thanks!
        $countrynames = array(
            "AF"=>"Afghanistan",
            "AX"=>"\xc3\x85land Islands",
            "AL"=>"Albania",
            "DZ"=>"Algeria",
            "AS"=>"American Samoa",
            "AD"=>"Andorra",
            "AO"=>"Angola",
            "AI"=>"Anguilla",
            "AQ"=>"Antarctica",
            "AG"=>"Antigua and Barbuda",
            "AR"=>"Argentina",
            "AM"=>"Armenia",
            "AW"=>"Aruba",
            "AU"=>"Australia",
            "AT"=>"Austria",
            "AZ"=>"Azerbaijan",
            "BS"=>"Bahamas",
            "BH"=>"Bahrain",
            "BD"=>"Bangladesh",
            "BB"=>"Barbados",
            "BY"=>"Belarus",
            "BE"=>"Belgium",
            "BZ"=>"Belize",
            "BJ"=>"Benin",
            "BM"=>"Bermuda",
            "BT"=>"Bhutan",
            "BO"=>"Bolivia, Plurinational State of",
            "BQ"=>"Bonaire, Sint Eustatius and Saba",
            "BA"=>"Bosnia and Herzegovina",
            "BW"=>"Botswana",
            "BV"=>"Bouvet Island",
            "BR"=>"Brazil",
            "IO"=>"British Indian Ocean Territory",
            "BN"=>"Brunei Darussalam",
            "BG"=>"Bulgaria",
            "BF"=>"Burkina Faso",
            "BI"=>"Burundi",
            "KH"=>"Cambodia",
            "CM"=>"Cameroon",
            "CA"=>"Canada",
            "CV"=>"Cape Verde",
            "KY"=>"Cayman Islands",
            "CF"=>"Central African Republic",
            "TD"=>"Chad",
            "CL"=>"Chile",
            "CN"=>"China",
            "CX"=>"Christmas Island",
            "CC"=>"Cocos (Keeling) Islands",
            "CO"=>"Colombia",
            "KM"=>"Comoros",
            "CG"=>"Congo",
            "CD"=>"Congo, The Democratic Republic of the",
            "CK"=>"Cook Islands",
            "CR"=>"Costa Rica",
            "CI"=>"C\xc3\xb4te d'Ivoire",
            "HR"=>"Croatia",
            "CU"=>"Cuba",
            "CW"=>"Cura\xc3\xa7ao",
            "CY"=>"Cyprus",
            "CZ"=>"Czech Republic",
            "DK"=>"Denmark",
            "DJ"=>"Djibouti",
            "DM"=>"Dominica",
            "DO"=>"Dominican Republic",
            "EC"=>"Ecuador",
            "EG"=>"Egypt",
            "SV"=>"El Salvador",
            "GQ"=>"Equatorial Guinea",
            "ER"=>"Eritrea",
            "EE"=>"Estonia",
            "ET"=>"Ethiopia",
            "FK"=>"Falkland Islands (Malvinas)",
            "FO"=>"Faroe Islands",
            "FJ"=>"Fiji",
            "FI"=>"Finland",
            "FR"=>"France",
            "GF"=>"French Guiana",
            "PF"=>"French Polynesia",
            "TF"=>"French Southern Territories",
            "GA"=>"Gabon",
            "GM"=>"Gambia",
            "GE"=>"Georgia",
            "DE"=>"Germany",
            "GH"=>"Ghana",
            "GI"=>"Gibraltar",
            "GR"=>"Greece",
            "GL"=>"Greenland",
            "GD"=>"Grenada",
            "GP"=>"Guadeloupe",
            "GU"=>"Guam",
            "GT"=>"Guatemala",
            "GG"=>"Guernsey",
            "GN"=>"Guinea",
            "GW"=>"Guinea-Bissau",
            "GY"=>"Guyana",
            "HT"=>"Haiti",
            "HM"=>"Heard Island and McDonald Islands",
            "VA"=>"Holy See (Vatican City State)",
            "HN"=>"Honduras",
            "HK"=>"Hong Kong",
            "HU"=>"Hungary",
            "IS"=>"Iceland",
            "IN"=>"India",
            "ID"=>"Indonesia",
            "IR"=>"Iran, Islamic Republic of",
            "IQ"=>"Iraq",
            "IE"=>"Ireland",
            "IM"=>"Isle of Man",
            "IL"=>"Israel",
            "IT"=>"Italy",
            "JM"=>"Jamaica",
            "JP"=>"Japan",
            "JE"=>"Jersey",
            "JO"=>"Jordan",
            "KZ"=>"Kazakhstan",
            "KE"=>"Kenya",
            "KI"=>"Kiribati",
            "KP"=>"Korea, Democratic People's Republic of",
            "KR"=>"Korea, Republic of",
            "KW"=>"Kuwait",
            "KG"=>"Kyrgyzstan",
            "LA"=>"Lao People's Democratic Republic",
            "LV"=>"Latvia",
            "LB"=>"Lebanon",
            "LS"=>"Lesotho",
            "LR"=>"Liberia",
            "LY"=>"Libya",
            "LI"=>"Liechtenstein",
            "LT"=>"Lithuania",
            "LU"=>"Luxembourg",
            "MO"=>"Macao",
            "MK"=>"Macedonia, The Former Yugoslav Republic of",
            "MG"=>"Madagascar",
            "MW"=>"Malawi",
            "MY"=>"Malaysia",
            "MV"=>"Maldives",
            "ML"=>"Mali",
            "MT"=>"Malta",
            "MH"=>"Marshall Islands",
            "MQ"=>"Martinique",
            "MR"=>"Mauritania",
            "MU"=>"Mauritius",
            "YT"=>"Mayotte",
            "MX"=>"Mexico",
            "FM"=>"Micronesia, Federated States of",
            "MD"=>"Moldova, Republic of",
            "MC"=>"Monaco",
            "MN"=>"Mongolia",
            "ME"=>"Montenegro",
            "MS"=>"Montserrat",
            "MA"=>"Morocco",
            "MZ"=>"Mozambique",
            "MM"=>"Myanmar",
            "NA"=>"Namibia",
            "NR"=>"Nauru",
            "NP"=>"Nepal",
            "NL"=>"Netherlands",
            "NC"=>"New Caledonia",
            "NZ"=>"New Zealand",
            "NI"=>"Nicaragua",
            "NE"=>"Niger",
            "NG"=>"Nigeria",
            "NU"=>"Niue",
            "NF"=>"Norfolk Island",
            "MP"=>"Northern Mariana Islands",
            "NO"=>"Norway",
            "OM"=>"Oman",
            "PK"=>"Pakistan",
            "PW"=>"Palau",
            "PS"=>"Palestine, State of",
            "PA"=>"Panama",
            "PG"=>"Papua New Guinea",
            "PY"=>"Paraguay",
            "PE"=>"Peru",
            "PH"=>"Philippines",
            "PN"=>"Pitcairn",
            "PL"=>"Poland",
            "PT"=>"Portugal",
            "PR"=>"Puerto Rico",
            "QA"=>"Qatar",
            "RE"=>"R\xc3\xa9union",
            "RO"=>"Romania",
            "RU"=>"Russian Federation",
            "RW"=>"Rwanda",
            "BL"=>"Saint Barth\xc3\xa9lemy",
            "SH"=>"Saint Helena, Ascension and Tristan Da Cunha",
            "KN"=>"Saint Kitts and Nevis",
            "LC"=>"Saint Lucia",
            "MF"=>"Saint Martin (French part)",
            "PM"=>"Saint Pierre and Miquelon",
            "VC"=>"Saint Vincent and the Grenadines",
            "WS"=>"Samoa",
            "SM"=>"San Marino",
            "ST"=>"Sao Tome and Principe",
            "SA"=>"Saudi Arabia",
            "SN"=>"Senegal",
            "RS"=>"Serbia",
            "SC"=>"Seychelles",
            "SL"=>"Sierra Leone",
            "SG"=>"Singapore",
            "SX"=>"Sint Maarten (Dutch part)",
            "SK"=>"Slovakia",
            "SI"=>"Slovenia",
            "SB"=>"Solomon Islands",
            "SO"=>"Somalia",
            "ZA"=>"South Africa",
            "GS"=>"South Georgia and the South Sandwich Islands",
            "SS"=>"South Sudan",
            "ES"=>"Spain",
            "LK"=>"Sri Lanka",
            "SD"=>"Sudan",
            "SR"=>"Suriname",
            "SJ"=>"Svalbard and Jan Mayen",
            "SZ"=>"Swaziland",
            "SE"=>"Sweden",
            "CH"=>"Switzerland",
            "SY"=>"Syrian Arab Republic",
            "TW"=>"Taiwan, Province of China",
            "TJ"=>"Tajikistan",
            "TZ"=>"Tanzania, United Republic of",
            "TH"=>"Thailand",
            "TL"=>"Timor-Leste",
            "TG"=>"Togo",
            "TK"=>"Tokelau",
            "TO"=>"Tonga",
            "TT"=>"Trinidad and Tobago",
            "TN"=>"Tunisia",
            "TR"=>"Turkey",
            "TM"=>"Turkmenistan",
            "TC"=>"Turks and Caicos Islands",
            "TV"=>"Tuvalu",
            "UG"=>"Uganda",
            "UA"=>"Ukraine",
            "AE"=>"United Arab Emirates",
            "GB"=>"United Kingdom",
            "US"=>"United States",
            "UM"=>"United States Minor Outlying Islands",
            "UY"=>"Uruguay",
            "UZ"=>"Uzbekistan",
            "VU"=>"Vanuatu",
            "VE"=>"Venezuela, Bolivarian Republic of",
            "VN"=>"Viet Nam",
            "VG"=>"Virgin Islands, British",
            "VI"=>"Virgin Islands, U.S.",
            "WF"=>"Wallis and Futuna",
            "EH"=>"Western Sahara",
            "YE"=>"Yemen",
            "ZM"=>"Zambia",
            "ZW"=>"Zimbabwe"
        );
        return( array_key_exists( $country, array_flip($countrynames) ) ? 
                array_flip($countrynames[$country]) : "swp_error: getCountryCode: unknown country name" );
    }    
            
    /**
     * Creates an orders_status_history and then updates an order status entry in table order, orders_status_history
     * 
     * @param int $oID  -- order id to change orders_status for
     * @param int $status -- updated status you want to set
     * @param string $comment -- updated comment you want to set
     */
    function insertOrdersStatus( $oID, $status, $comment ) {
        $sql_data_array = array(
                'orders_id' => $oID,
                'orders_status_id' => $status,                           
                'date_added' => 'now()',
                'customer_notified' => 0,  // 0 for "no email" (open lock symbol) in order status history
                'comments' => $comment
        );
        tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

        $this->updateOrdersStatus( $oID, $status, $comment );
    }
    
    /**
     * Return the Svea sveaOrderId corresponding to an order, or false if order not found in svea_order table
     * 
     * @param int $oID -- order id
     * @return int -- svea order id, or false if order not found in svea_order table
     */
    function getSveaOrderId( $oID ) {
        
        $sveaResult = tep_db_query("SELECT * FROM svea_order WHERE orders_id = " . (int)$oID );
        $fields = $sveaResult->fetch_assoc();
        
        return isset( $fields["sveaorderid"] ) ? $fields["sveaorderid"] : false;
    }
    
    /**
     * Return the Svea create order object corresponding to an order
     * 
     * @param int $oID -- order id
     * @return Svea\CreateOrderBuilder
     */
    function getSveaCreateOrderObject( $oID ) {
        
        $sveaResult = tep_db_query("SELECT * FROM svea_order WHERE orders_id = " . (int)$oID );
        $fields = $sveaResult->fetch_assoc();        
        
        return unserialize( $fields["createorder_object"] );
    }

    /**
     * get current order status for order from orders table
     * 
     * @param int $oID -- order id
     * @return int -- current order status 
     */
    function getCurrentOrderStatus( $oID ) {        
        $historyResult = tep_db_query(  "select * from orders_status_history where orders_id = ". (int)$oID .
                                        " order by date_added DESC LIMIT 1");
        $fields = $historyResult->fetch_assoc();
        return $fields["orders_status_id"];
    }
     
    /**
     * for each item in cart, create WebPayItem::orderRow objects and add to order
     * @param type $order_totals
     * @param type $svea_order
     */
    function parseOrderProducts( $order_products, &$svea_order )
    {
        global $order;
        
        $currency = $order->info['currency'];          
        
        foreach( $order_products as $productId => $product ) 
        {
            $amount_ex_vat = floatval( $this->convertToCurrency(round($product['final_price'], 2), $currency) );

            $svea_order->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(intval($product['qty']))
                    ->setAmountExVat($amount_ex_vat)      
                    ->setVatPercent(intval($product['tax']))
                    ->setDescription($product['name'])
            );
        }
        return $svea_order;
    }
    
    /**
     * get order totals from globals and return them in a format akin to that of the
     * order_total::process() (which accumulates values in output array, so we can't
     * just call it again, but need to look at what the order total presentation left us
     * 
     * @return array $order_totals
     */
    function getOrderTotals()
    {    
       global $order_total_modules;
        
        $order_totals = array();
        foreach( $order_total_modules->modules as $n => $module_filename )
        {
            $module = substr($module_filename, 0, strrpos($module_filename, '.'));

            for( $i=0; $i<count($GLOBALS[$module]->output); $i++)
            {
                $order_totals[] = array(
                    'code' => $GLOBALS[$module]->code, 
                    'title' => $GLOBALS[$module]->output[$i]['title'],
                    'text' => $GLOBALS[$module]->output[$i]['text'],
                    'value' => $GLOBALS[$module]->output[$i]['value'],
                    'sort_order' => $GLOBALS[$module]->sort_order,
                );
            }
       }
       
       return $order_totals ;
    }    
    
    /**
     * parseOrderTotals() goes through the order order_totals for diverse non-product
     * order rows and updates the svea order object with the appropriate shipping, handling
     * and discount rows.
     * 
     * @param array $order_totals
     * @param createOrderBuilder or deliverOrderBuilder $svea_order
     * @return createOrderBuilder or deliverOrderBuilder -- the updated $svea_order object
     */
    function parseOrderTotals( $order_totals, &$svea_order ) {
        global $db, $order;
      
        $currency = $order->info['currency'];
            
        foreach ($order_totals as $ot_id => $order_total) {
                                         
            switch ($order_total['code']) {

                // ignore these order_total codes
                case in_array( $order_total['code'], $this->ignore_list):
                case 'ot_subtotal':
                case 'ot_total':
                case 'ot_tax':
                    // do nothing
                    break;

                // if shipping fee, create WebPayItem::shippingFee object and add to order
                case 'ot_shipping':
                                                    
                    // get shipping tax rate from session
                    $shipping_module_name = substr($_SESSION['shipping']['id'], 0, strrpos($_SESSION['shipping']['id'], '_'));
                    $shipping_tax_class = constant( 'MODULE_SHIPPING_'.strtoupper($shipping_module_name)."_TAX_CLASS");
                    $shipping_tax_rate = tep_get_tax_rate( $shipping_tax_class );
                    
                    // get shipping cost ex vat from globals
                    $shipping_cost_ex_vat = floatval( $this->convertToCurrency( round($GLOBALS['shipping']['cost'],2), $currency) );

                    // add WebPayItem::shippingFee to swp_order object
                    $svea_order->addFee(
                            WebPayItem::shippingFee()
                                    ->setDescription($order->info['shipping_method'])
                                    ->setAmountExVat( $shipping_cost_ex_vat )
                                    ->setVatPercent($shipping_tax_rate)
                    );
                    break;

                // Svea handling (i.e. invoice) fee applies, create WebPayItem::invoiceFee object and add to order
                case 'sveawebpay_handling_fee' :

                    // we need both the fee and the tax rate, but $order_total['value'] only contains the compounded amount 
                    // we bypass it and go to the configured cost and tax rate for the country (as defined by delivery address)                     
                    $fee_cost = constant("MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_".$order->delivery['country']['iso_code_2']);
                    $tax_class = constant("MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_".$order->delivery['country']['iso_code_2']);
                    $tax_rate = tep_get_tax_rate($tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
               
                    // add WebPayItem::invoiceFee to swp_order object
                    $svea_order->addFee(
                            WebPayItem::invoiceFee()
                                    ->setName($order_total['title'])
                                    ->setDescription($order_total['text']." (".$order->delivery['country']['iso_code_2'].")")
                                    ->setAmountExVat($fee_cost)
                                    ->setVatPercent($tax_rate)
                    );                  
                    break;

                // default case attempts to handle 'unknown' items from other plugins, treating negatives as discount rows, positives as fees
                default:
                                      
                    if( $order_total['value'] < 0 ) // write negative amounts as FixedDiscount
                    {    
                        // order_total_object doesn't include tax, so we trust that the integration package calculation to handle it
                        if (DISPLAY_PRICE_WITH_TAX == 'true') // if prices are displayed WITH tax in shop, use setAmountIncVat
                        {                        
                            $svea_order->addDiscount(
                                WebPayItem::fixedDiscount()
                                    ->setAmountIncVat( -1* $this->convertToCurrency(strip_tags($order_total['value']), $currency)) // given as positive amount
                                    ->setDescription($order_total['title'])
                            );
                        }
                        else // if prices are displayed WITHOUT tax in shop, use setAmountExVat
                        {
                            $svea_order->addDiscount(
                                WebPayItem::fixedDiscount()
                                    ->setAmountExVat( -1* $this->convertToCurrency(strip_tags($order_total['value']), $currency)) // given as positive amount
                                    ->setDescription($order_total['title'])
                            );
                        }
                    }
                    else //write positive amounts as HandlingFee
                    {                         
                        $svea_order->addFee(
                            WebPayItem::invoiceFee()
                                ->setAmountExVat($this->convertToCurrency(strip_tags($order_total['value']), $currency)) // needs package support!
                                ->setDescription($order_total['title'])
                        );
                    }
                    break;
            }
        }
        
        return $svea_order;
    }
}
?>

