<?php //

/**
 * Class SveaOsCommerce contains various utility functions used by Svea osCommerce payment modules
 *
 * @author Kristian Grossman-Madsen
 */
class SveaOsCommerce {  
//    
//  /**
//   *
//   * @global type $currencies
//   * @param float $value amount to convert
//   * @param string $currency as three-letter $iso3166 country code
//   * @param boolean $no_number_format if true, don't convert the to i.e. Swedish decimal indicator (",")
//   *    Having a non-standard decimal may cause i.e. number conversion with floatval() to truncate fractions.
//   * @return type
//   */
//    function convertToCurrency($value, $currency, $no_number_format = true) {
//        global $currencies;
//
//        // item price is ALWAYS given in internal price from the products DB, so just multiply by currency rate from currency table
//        $rounded_value = zen_round($value * $currencies->currencies[$currency]['value'], $currencies->currencies[$currency]['decimal_places']);
//
//        return $no_number_format ? $rounded_value : number_format(  $rounded_value,
//                                                                    $currencies->currencies[$currency]['decimal_places'],
//                                                                    $currencies->currencies[$currency]['decimal_point'],
//                                                                    $currencies->currencies[$currency]['thousands_point']);
//    }
//   
    /**
     *  switch to default currency if the customers currency is not supported
     * 
     * @return type -- currency to use
     */
    function getCurrency( $customerCurrency ) {
        return in_array($customerCurrency, $this->allowed_currencies) ? $customerCurrency : $this->default_currency;
    }
        
//    /**
//     * Given iso 3166 country code, returns English country name.
//     * 
//     * @param string $iso3166
//     * @return string english country name
//     */
//    function getCountryName( $iso3166 ) {
//
//        // countrynames from https://github.com/johannesl/Internationalization, thanks!
//        $countrynames = array(
//            "AF"=>"Afghanistan",
//            "AX"=>"\xc3\x85land Islands",
//            "AL"=>"Albania",
//            "DZ"=>"Algeria",
//            "AS"=>"American Samoa",
//            "AD"=>"Andorra",
//            "AO"=>"Angola",
//            "AI"=>"Anguilla",
//            "AQ"=>"Antarctica",
//            "AG"=>"Antigua and Barbuda",
//            "AR"=>"Argentina",
//            "AM"=>"Armenia",
//            "AW"=>"Aruba",
//            "AU"=>"Australia",
//            "AT"=>"Austria",
//            "AZ"=>"Azerbaijan",
//            "BS"=>"Bahamas",
//            "BH"=>"Bahrain",
//            "BD"=>"Bangladesh",
//            "BB"=>"Barbados",
//            "BY"=>"Belarus",
//            "BE"=>"Belgium",
//            "BZ"=>"Belize",
//            "BJ"=>"Benin",
//            "BM"=>"Bermuda",
//            "BT"=>"Bhutan",
//            "BO"=>"Bolivia, Plurinational State of",
//            "BQ"=>"Bonaire, Sint Eustatius and Saba",
//            "BA"=>"Bosnia and Herzegovina",
//            "BW"=>"Botswana",
//            "BV"=>"Bouvet Island",
//            "BR"=>"Brazil",
//            "IO"=>"British Indian Ocean Territory",
//            "BN"=>"Brunei Darussalam",
//            "BG"=>"Bulgaria",
//            "BF"=>"Burkina Faso",
//            "BI"=>"Burundi",
//            "KH"=>"Cambodia",
//            "CM"=>"Cameroon",
//            "CA"=>"Canada",
//            "CV"=>"Cape Verde",
//            "KY"=>"Cayman Islands",
//            "CF"=>"Central African Republic",
//            "TD"=>"Chad",
//            "CL"=>"Chile",
//            "CN"=>"China",
//            "CX"=>"Christmas Island",
//            "CC"=>"Cocos (Keeling) Islands",
//            "CO"=>"Colombia",
//            "KM"=>"Comoros",
//            "CG"=>"Congo",
//            "CD"=>"Congo, The Democratic Republic of the",
//            "CK"=>"Cook Islands",
//            "CR"=>"Costa Rica",
//            "CI"=>"C\xc3\xb4te d'Ivoire",
//            "HR"=>"Croatia",
//            "CU"=>"Cuba",
//            "CW"=>"Cura\xc3\xa7ao",
//            "CY"=>"Cyprus",
//            "CZ"=>"Czech Republic",
//            "DK"=>"Denmark",
//            "DJ"=>"Djibouti",
//            "DM"=>"Dominica",
//            "DO"=>"Dominican Republic",
//            "EC"=>"Ecuador",
//            "EG"=>"Egypt",
//            "SV"=>"El Salvador",
//            "GQ"=>"Equatorial Guinea",
//            "ER"=>"Eritrea",
//            "EE"=>"Estonia",
//            "ET"=>"Ethiopia",
//            "FK"=>"Falkland Islands (Malvinas)",
//            "FO"=>"Faroe Islands",
//            "FJ"=>"Fiji",
//            "FI"=>"Finland",
//            "FR"=>"France",
//            "GF"=>"French Guiana",
//            "PF"=>"French Polynesia",
//            "TF"=>"French Southern Territories",
//            "GA"=>"Gabon",
//            "GM"=>"Gambia",
//            "GE"=>"Georgia",
//            "DE"=>"Germany",
//            "GH"=>"Ghana",
//            "GI"=>"Gibraltar",
//            "GR"=>"Greece",
//            "GL"=>"Greenland",
//            "GD"=>"Grenada",
//            "GP"=>"Guadeloupe",
//            "GU"=>"Guam",
//            "GT"=>"Guatemala",
//            "GG"=>"Guernsey",
//            "GN"=>"Guinea",
//            "GW"=>"Guinea-Bissau",
//            "GY"=>"Guyana",
//            "HT"=>"Haiti",
//            "HM"=>"Heard Island and McDonald Islands",
//            "VA"=>"Holy See (Vatican City State)",
//            "HN"=>"Honduras",
//            "HK"=>"Hong Kong",
//            "HU"=>"Hungary",
//            "IS"=>"Iceland",
//            "IN"=>"India",
//            "ID"=>"Indonesia",
//            "IR"=>"Iran, Islamic Republic of",
//            "IQ"=>"Iraq",
//            "IE"=>"Ireland",
//            "IM"=>"Isle of Man",
//            "IL"=>"Israel",
//            "IT"=>"Italy",
//            "JM"=>"Jamaica",
//            "JP"=>"Japan",
//            "JE"=>"Jersey",
//            "JO"=>"Jordan",
//            "KZ"=>"Kazakhstan",
//            "KE"=>"Kenya",
//            "KI"=>"Kiribati",
//            "KP"=>"Korea, Democratic People's Republic of",
//            "KR"=>"Korea, Republic of",
//            "KW"=>"Kuwait",
//            "KG"=>"Kyrgyzstan",
//            "LA"=>"Lao People's Democratic Republic",
//            "LV"=>"Latvia",
//            "LB"=>"Lebanon",
//            "LS"=>"Lesotho",
//            "LR"=>"Liberia",
//            "LY"=>"Libya",
//            "LI"=>"Liechtenstein",
//            "LT"=>"Lithuania",
//            "LU"=>"Luxembourg",
//            "MO"=>"Macao",
//            "MK"=>"Macedonia, The Former Yugoslav Republic of",
//            "MG"=>"Madagascar",
//            "MW"=>"Malawi",
//            "MY"=>"Malaysia",
//            "MV"=>"Maldives",
//            "ML"=>"Mali",
//            "MT"=>"Malta",
//            "MH"=>"Marshall Islands",
//            "MQ"=>"Martinique",
//            "MR"=>"Mauritania",
//            "MU"=>"Mauritius",
//            "YT"=>"Mayotte",
//            "MX"=>"Mexico",
//            "FM"=>"Micronesia, Federated States of",
//            "MD"=>"Moldova, Republic of",
//            "MC"=>"Monaco",
//            "MN"=>"Mongolia",
//            "ME"=>"Montenegro",
//            "MS"=>"Montserrat",
//            "MA"=>"Morocco",
//            "MZ"=>"Mozambique",
//            "MM"=>"Myanmar",
//            "NA"=>"Namibia",
//            "NR"=>"Nauru",
//            "NP"=>"Nepal",
//            "NL"=>"Netherlands",
//            "NC"=>"New Caledonia",
//            "NZ"=>"New Zealand",
//            "NI"=>"Nicaragua",
//            "NE"=>"Niger",
//            "NG"=>"Nigeria",
//            "NU"=>"Niue",
//            "NF"=>"Norfolk Island",
//            "MP"=>"Northern Mariana Islands",
//            "NO"=>"Norway",
//            "OM"=>"Oman",
//            "PK"=>"Pakistan",
//            "PW"=>"Palau",
//            "PS"=>"Palestine, State of",
//            "PA"=>"Panama",
//            "PG"=>"Papua New Guinea",
//            "PY"=>"Paraguay",
//            "PE"=>"Peru",
//            "PH"=>"Philippines",
//            "PN"=>"Pitcairn",
//            "PL"=>"Poland",
//            "PT"=>"Portugal",
//            "PR"=>"Puerto Rico",
//            "QA"=>"Qatar",
//            "RE"=>"R\xc3\xa9union",
//            "RO"=>"Romania",
//            "RU"=>"Russian Federation",
//            "RW"=>"Rwanda",
//            "BL"=>"Saint Barth\xc3\xa9lemy",
//            "SH"=>"Saint Helena, Ascension and Tristan Da Cunha",
//            "KN"=>"Saint Kitts and Nevis",
//            "LC"=>"Saint Lucia",
//            "MF"=>"Saint Martin (French part)",
//            "PM"=>"Saint Pierre and Miquelon",
//            "VC"=>"Saint Vincent and the Grenadines",
//            "WS"=>"Samoa",
//            "SM"=>"San Marino",
//            "ST"=>"Sao Tome and Principe",
//            "SA"=>"Saudi Arabia",
//            "SN"=>"Senegal",
//            "RS"=>"Serbia",
//            "SC"=>"Seychelles",
//            "SL"=>"Sierra Leone",
//            "SG"=>"Singapore",
//            "SX"=>"Sint Maarten (Dutch part)",
//            "SK"=>"Slovakia",
//            "SI"=>"Slovenia",
//            "SB"=>"Solomon Islands",
//            "SO"=>"Somalia",
//            "ZA"=>"South Africa",
//            "GS"=>"South Georgia and the South Sandwich Islands",
//            "SS"=>"South Sudan",
//            "ES"=>"Spain",
//            "LK"=>"Sri Lanka",
//            "SD"=>"Sudan",
//            "SR"=>"Suriname",
//            "SJ"=>"Svalbard and Jan Mayen",
//            "SZ"=>"Swaziland",
//            "SE"=>"Sweden",
//            "CH"=>"Switzerland",
//            "SY"=>"Syrian Arab Republic",
//            "TW"=>"Taiwan, Province of China",
//            "TJ"=>"Tajikistan",
//            "TZ"=>"Tanzania, United Republic of",
//            "TH"=>"Thailand",
//            "TL"=>"Timor-Leste",
//            "TG"=>"Togo",
//            "TK"=>"Tokelau",
//            "TO"=>"Tonga",
//            "TT"=>"Trinidad and Tobago",
//            "TN"=>"Tunisia",
//            "TR"=>"Turkey",
//            "TM"=>"Turkmenistan",
//            "TC"=>"Turks and Caicos Islands",
//            "TV"=>"Tuvalu",
//            "UG"=>"Uganda",
//            "UA"=>"Ukraine",
//            "AE"=>"United Arab Emirates",
//            "GB"=>"United Kingdom",
//            "US"=>"United States",
//            "UM"=>"United States Minor Outlying Islands",
//            "UY"=>"Uruguay",
//            "UZ"=>"Uzbekistan",
//            "VU"=>"Vanuatu",
//            "VE"=>"Venezuela, Bolivarian Republic of",
//            "VN"=>"Viet Nam",
//            "VG"=>"Virgin Islands, British",
//            "VI"=>"Virgin Islands, U.S.",
//            "WF"=>"Wallis and Futuna",
//            "EH"=>"Western Sahara",
//            "YE"=>"Yemen",
//            "ZM"=>"Zambia",
//            "ZW"=>"Zimbabwe"
//        );
//        return( array_key_exists( $iso3166, $countrynames) ? $countrynames[$iso3166] : "swp_error: getCountryCode: unknown country code" );
//    }   
// 
//    /**
//     * Given English country name, returns iso 3166 country code.
//     * 
//     * @param string $country
//     * @return string iso 3166 country code
//    */
//    function getCountryCode( $country ) {
//
//        // countrynames from https://github.com/johannesl/Internationalization, thanks!
//        $countrynames = array(
//            "AF"=>"Afghanistan",
//            "AX"=>"\xc3\x85land Islands",
//            "AL"=>"Albania",
//            "DZ"=>"Algeria",
//            "AS"=>"American Samoa",
//            "AD"=>"Andorra",
//            "AO"=>"Angola",
//            "AI"=>"Anguilla",
//            "AQ"=>"Antarctica",
//            "AG"=>"Antigua and Barbuda",
//            "AR"=>"Argentina",
//            "AM"=>"Armenia",
//            "AW"=>"Aruba",
//            "AU"=>"Australia",
//            "AT"=>"Austria",
//            "AZ"=>"Azerbaijan",
//            "BS"=>"Bahamas",
//            "BH"=>"Bahrain",
//            "BD"=>"Bangladesh",
//            "BB"=>"Barbados",
//            "BY"=>"Belarus",
//            "BE"=>"Belgium",
//            "BZ"=>"Belize",
//            "BJ"=>"Benin",
//            "BM"=>"Bermuda",
//            "BT"=>"Bhutan",
//            "BO"=>"Bolivia, Plurinational State of",
//            "BQ"=>"Bonaire, Sint Eustatius and Saba",
//            "BA"=>"Bosnia and Herzegovina",
//            "BW"=>"Botswana",
//            "BV"=>"Bouvet Island",
//            "BR"=>"Brazil",
//            "IO"=>"British Indian Ocean Territory",
//            "BN"=>"Brunei Darussalam",
//            "BG"=>"Bulgaria",
//            "BF"=>"Burkina Faso",
//            "BI"=>"Burundi",
//            "KH"=>"Cambodia",
//            "CM"=>"Cameroon",
//            "CA"=>"Canada",
//            "CV"=>"Cape Verde",
//            "KY"=>"Cayman Islands",
//            "CF"=>"Central African Republic",
//            "TD"=>"Chad",
//            "CL"=>"Chile",
//            "CN"=>"China",
//            "CX"=>"Christmas Island",
//            "CC"=>"Cocos (Keeling) Islands",
//            "CO"=>"Colombia",
//            "KM"=>"Comoros",
//            "CG"=>"Congo",
//            "CD"=>"Congo, The Democratic Republic of the",
//            "CK"=>"Cook Islands",
//            "CR"=>"Costa Rica",
//            "CI"=>"C\xc3\xb4te d'Ivoire",
//            "HR"=>"Croatia",
//            "CU"=>"Cuba",
//            "CW"=>"Cura\xc3\xa7ao",
//            "CY"=>"Cyprus",
//            "CZ"=>"Czech Republic",
//            "DK"=>"Denmark",
//            "DJ"=>"Djibouti",
//            "DM"=>"Dominica",
//            "DO"=>"Dominican Republic",
//            "EC"=>"Ecuador",
//            "EG"=>"Egypt",
//            "SV"=>"El Salvador",
//            "GQ"=>"Equatorial Guinea",
//            "ER"=>"Eritrea",
//            "EE"=>"Estonia",
//            "ET"=>"Ethiopia",
//            "FK"=>"Falkland Islands (Malvinas)",
//            "FO"=>"Faroe Islands",
//            "FJ"=>"Fiji",
//            "FI"=>"Finland",
//            "FR"=>"France",
//            "GF"=>"French Guiana",
//            "PF"=>"French Polynesia",
//            "TF"=>"French Southern Territories",
//            "GA"=>"Gabon",
//            "GM"=>"Gambia",
//            "GE"=>"Georgia",
//            "DE"=>"Germany",
//            "GH"=>"Ghana",
//            "GI"=>"Gibraltar",
//            "GR"=>"Greece",
//            "GL"=>"Greenland",
//            "GD"=>"Grenada",
//            "GP"=>"Guadeloupe",
//            "GU"=>"Guam",
//            "GT"=>"Guatemala",
//            "GG"=>"Guernsey",
//            "GN"=>"Guinea",
//            "GW"=>"Guinea-Bissau",
//            "GY"=>"Guyana",
//            "HT"=>"Haiti",
//            "HM"=>"Heard Island and McDonald Islands",
//            "VA"=>"Holy See (Vatican City State)",
//            "HN"=>"Honduras",
//            "HK"=>"Hong Kong",
//            "HU"=>"Hungary",
//            "IS"=>"Iceland",
//            "IN"=>"India",
//            "ID"=>"Indonesia",
//            "IR"=>"Iran, Islamic Republic of",
//            "IQ"=>"Iraq",
//            "IE"=>"Ireland",
//            "IM"=>"Isle of Man",
//            "IL"=>"Israel",
//            "IT"=>"Italy",
//            "JM"=>"Jamaica",
//            "JP"=>"Japan",
//            "JE"=>"Jersey",
//            "JO"=>"Jordan",
//            "KZ"=>"Kazakhstan",
//            "KE"=>"Kenya",
//            "KI"=>"Kiribati",
//            "KP"=>"Korea, Democratic People's Republic of",
//            "KR"=>"Korea, Republic of",
//            "KW"=>"Kuwait",
//            "KG"=>"Kyrgyzstan",
//            "LA"=>"Lao People's Democratic Republic",
//            "LV"=>"Latvia",
//            "LB"=>"Lebanon",
//            "LS"=>"Lesotho",
//            "LR"=>"Liberia",
//            "LY"=>"Libya",
//            "LI"=>"Liechtenstein",
//            "LT"=>"Lithuania",
//            "LU"=>"Luxembourg",
//            "MO"=>"Macao",
//            "MK"=>"Macedonia, The Former Yugoslav Republic of",
//            "MG"=>"Madagascar",
//            "MW"=>"Malawi",
//            "MY"=>"Malaysia",
//            "MV"=>"Maldives",
//            "ML"=>"Mali",
//            "MT"=>"Malta",
//            "MH"=>"Marshall Islands",
//            "MQ"=>"Martinique",
//            "MR"=>"Mauritania",
//            "MU"=>"Mauritius",
//            "YT"=>"Mayotte",
//            "MX"=>"Mexico",
//            "FM"=>"Micronesia, Federated States of",
//            "MD"=>"Moldova, Republic of",
//            "MC"=>"Monaco",
//            "MN"=>"Mongolia",
//            "ME"=>"Montenegro",
//            "MS"=>"Montserrat",
//            "MA"=>"Morocco",
//            "MZ"=>"Mozambique",
//            "MM"=>"Myanmar",
//            "NA"=>"Namibia",
//            "NR"=>"Nauru",
//            "NP"=>"Nepal",
//            "NL"=>"Netherlands",
//            "NC"=>"New Caledonia",
//            "NZ"=>"New Zealand",
//            "NI"=>"Nicaragua",
//            "NE"=>"Niger",
//            "NG"=>"Nigeria",
//            "NU"=>"Niue",
//            "NF"=>"Norfolk Island",
//            "MP"=>"Northern Mariana Islands",
//            "NO"=>"Norway",
//            "OM"=>"Oman",
//            "PK"=>"Pakistan",
//            "PW"=>"Palau",
//            "PS"=>"Palestine, State of",
//            "PA"=>"Panama",
//            "PG"=>"Papua New Guinea",
//            "PY"=>"Paraguay",
//            "PE"=>"Peru",
//            "PH"=>"Philippines",
//            "PN"=>"Pitcairn",
//            "PL"=>"Poland",
//            "PT"=>"Portugal",
//            "PR"=>"Puerto Rico",
//            "QA"=>"Qatar",
//            "RE"=>"R\xc3\xa9union",
//            "RO"=>"Romania",
//            "RU"=>"Russian Federation",
//            "RW"=>"Rwanda",
//            "BL"=>"Saint Barth\xc3\xa9lemy",
//            "SH"=>"Saint Helena, Ascension and Tristan Da Cunha",
//            "KN"=>"Saint Kitts and Nevis",
//            "LC"=>"Saint Lucia",
//            "MF"=>"Saint Martin (French part)",
//            "PM"=>"Saint Pierre and Miquelon",
//            "VC"=>"Saint Vincent and the Grenadines",
//            "WS"=>"Samoa",
//            "SM"=>"San Marino",
//            "ST"=>"Sao Tome and Principe",
//            "SA"=>"Saudi Arabia",
//            "SN"=>"Senegal",
//            "RS"=>"Serbia",
//            "SC"=>"Seychelles",
//            "SL"=>"Sierra Leone",
//            "SG"=>"Singapore",
//            "SX"=>"Sint Maarten (Dutch part)",
//            "SK"=>"Slovakia",
//            "SI"=>"Slovenia",
//            "SB"=>"Solomon Islands",
//            "SO"=>"Somalia",
//            "ZA"=>"South Africa",
//            "GS"=>"South Georgia and the South Sandwich Islands",
//            "SS"=>"South Sudan",
//            "ES"=>"Spain",
//            "LK"=>"Sri Lanka",
//            "SD"=>"Sudan",
//            "SR"=>"Suriname",
//            "SJ"=>"Svalbard and Jan Mayen",
//            "SZ"=>"Swaziland",
//            "SE"=>"Sweden",
//            "CH"=>"Switzerland",
//            "SY"=>"Syrian Arab Republic",
//            "TW"=>"Taiwan, Province of China",
//            "TJ"=>"Tajikistan",
//            "TZ"=>"Tanzania, United Republic of",
//            "TH"=>"Thailand",
//            "TL"=>"Timor-Leste",
//            "TG"=>"Togo",
//            "TK"=>"Tokelau",
//            "TO"=>"Tonga",
//            "TT"=>"Trinidad and Tobago",
//            "TN"=>"Tunisia",
//            "TR"=>"Turkey",
//            "TM"=>"Turkmenistan",
//            "TC"=>"Turks and Caicos Islands",
//            "TV"=>"Tuvalu",
//            "UG"=>"Uganda",
//            "UA"=>"Ukraine",
//            "AE"=>"United Arab Emirates",
//            "GB"=>"United Kingdom",
//            "US"=>"United States",
//            "UM"=>"United States Minor Outlying Islands",
//            "UY"=>"Uruguay",
//            "UZ"=>"Uzbekistan",
//            "VU"=>"Vanuatu",
//            "VE"=>"Venezuela, Bolivarian Republic of",
//            "VN"=>"Viet Nam",
//            "VG"=>"Virgin Islands, British",
//            "VI"=>"Virgin Islands, U.S.",
//            "WF"=>"Wallis and Futuna",
//            "EH"=>"Western Sahara",
//            "YE"=>"Yemen",
//            "ZM"=>"Zambia",
//            "ZW"=>"Zimbabwe"
//        );
//        return( array_key_exists( $country, array_flip($countrynames) ) ? 
//                array_flip($countrynames[$country]) : "swp_error: getCountryCode: unknown country name" );
//    }    
//          
//    /**
//     * Updates latest order_status entry in table order, orders_status_history
//     * 
//     * @param int $oID  -- order id to change orders_status for
//     * @param int $status -- updated status you want to set
//     * @param string $comment -- updated comment you want to set
//     */
//    function updateOrdersStatus( $oID, $status, $comment ) {
//        global $db;
//
//        $historyResult = $db->Execute(  "select * from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = ". (int)$oID .
//                                        " order by date_added DESC LIMIT 1");
//        $oshID = $historyResult->fields["orders_status_history_id"];
//        $comments = $historyResult->fields["comments"];
//
//        $db->Execute(   "update " . TABLE_ORDERS_STATUS_HISTORY . " " .
//                        "set comments = '" . $comments . "\n " . $comment . "', " .
//                        "orders_status_id = " . (int)$status . ", " .
//                        "customer_notified = " . 0 . " " .         // 0 for "no email" (open lock symbol) in order status history
//                        "where orders_status_history_id = " . (int)$oshID)
//        ;
//        
//        $db->Execute(   "update " . TABLE_ORDERS . " " .
//                        "set orders_status = " . (int)$status . " " .
//                        "where orders_id = " . (int)$oID );
//    }
//     
//    /**
//     * Creates an orders_status_history and then updates an order status entry in table order, orders_status_history
//     * 
//     * @param int $oID  -- order id to change orders_status for
//     * @param int $status -- updated status you want to set
//     * @param string $comment -- updated comment you want to set
//     */
//    function insertOrdersStatus( $oID, $status, $comment ) {
//        $sql_data_array = array(
//                'orders_id' => $oID,
//                'orders_status_id' => $status,                           
//                'date_added' => 'now()',
//                'customer_notified' => 0,  // 0 for "no email" (open lock symbol) in order status history
//                'comments' => $comment
//        );
//        zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
//
//        $this->updateOrdersStatus( $oID, $status, $comment );
//    }
//    
//    /**
//     * Return the Svea sveaOrderId corresponding to an order, or false if order not found in svea_order table
//     * 
//     * @param int $oID -- order id
//     * @return int -- svea order id, or false if order not found in svea_order table
//     */
//    function getSveaOrderId( $oID ) {
//        global $db;
//        
//        $sveaResult = $db->Execute("SELECT * FROM svea_order WHERE orders_id = " . (int)$oID );
//        return isset( $sveaResult->fields["sveaorderid"] ) ? $sveaResult->fields["sveaorderid"] : false;
//    }
//    
//    /**
//     * Return the Svea invoiceId corresponding to an order
//     * 
//     * @param int $oID -- order id
//     * @return int -- svea invoice id
//     */
//    function getSveaInvoiceId( $oID ) {
//        global $db;
//        
//        $sveaResult = $db->Execute("SELECT * FROM svea_order WHERE orders_id = " . (int)$oID );
//        return $sveaResult->fields["invoice_id"];
//    }
//    
//    /**
//     * Return the Svea create order object corresponding to an order
//     * 
//     * @param int $oID -- order id
//     * @return Svea\CreateOrderBuilder
//     */
//    function getSveaCreateOrderObject( $oID ) {
//        global $db;
//        
//        $sveaResult = $db->Execute("SELECT * FROM svea_order WHERE orders_id = " . (int)$oID );
//        return unserialize( $sveaResult->fields["createorder_object"] );
//    }
//
//    /**
//     * get current order status for order from orders table
//     * 
//     * @param int $oID -- order id
//     * @return int -- current order status 
//     */
//    function getCurrentOrderStatus( $oID ) {
//        global $db;
//        
//        $historyResult = $db->Execute(  "select * from orders_status_history where orders_id = ". (int)$oID .
//                                        " order by date_added DESC LIMIT 1");
//        return $historyResult->fields["orders_status_id"];
//    }
//   
//    /**
//     * parseOrderTotals() goes through the zencart order order_totals for diverse non-product
//     * order rows and updates the svea order object with the appropriate shipping, handling
//     * and discount rows.
//     * 
//     * @param array $order_totals
//     * @param createOrderBuilder or deliverOrderBuilder $svea_order
//     * @return createOrderBuilder or deliverOrderBuilder -- the updated $svea_order object
//     */
//    function parseOrderTotals( $order_totals, &$svea_order ) {
//        global $db, $order;
//        
//        $currency = $this->getCurrency($order->info['currency']);
//        
//        foreach ($order_totals as $ot_id => $order_total) {
//
//            switch ($order_total['code']) {
//
//                // ignore these order_total codes
//                case in_array( $order_total['code'], $this->ignore_list):
//                case 'ot_subtotal':
//                case 'ot_total':
//                case 'ot_tax':
//                    // do nothing
//                    break;
//
//                // if shipping fee, create WebPayItem::shippingFee object and add to order
//                case 'ot_shipping':
//
//                    // makes use of zencart $order-info[] shipping information to populate object
//                    // shop shows prices including tax, take this into accord when calculating tax
//                    if (DISPLAY_PRICE_WITH_TAX == 'false') {
//                        $amountExVat = $order->info['shipping_cost'];
//                        $amountIncVat = $order->info['shipping_cost'] + $order->info['shipping_tax'];
//                    }
//                    else {
//                        $amountExVat = $order->info['shipping_cost'] - $order->info['shipping_tax'];
//                        $amountIncVat = $order->info['shipping_cost'] ;
//                    }
//
//                    // add WebPayItem::shippingFee to swp_order object
//                    $svea_order->addFee(
//                            WebPayItem::shippingFee()
//                                    ->setDescription($order->info['shipping_method'])
//                                    ->setAmountExVat( $amountExVat )
//                                    ->setAmountIncVat( $amountIncVat )
//                    );
//                    break;
//
//                // Svea handling (i.e. invoice) fee applies, create WebPayItem::invoiceFee object and add to order
//                case 'sveawebpay_handling_fee' :
//
//                    // we need both the fee and the tax rate, but $order_total['value'] only contains the compounded amount 
//                    // we bypass it and go to the configured cost and tax rate for the country.                     
//                    $fee_cost = constant("MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_".$order->delivery['country']['iso_code_2']);
//                    $tax_class = constant("MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_".$order->delivery['country']['iso_code_2']);
//                    $tax_rate = zen_get_tax_rate($tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
//               
//                    // add WebPayItem::invoiceFee to swp_order object
//                    $svea_order->addFee(
//                            WebPayItem::invoiceFee()
//                                    ->setName($order_total['title'])
//                                    ->setDescription($order_total['text']." (".$order->delivery['country']['iso_code_2'].")")
//                                    ->setAmountExVat($fee_cost)
//                                    ->setVatPercent($tax_rate)
//                    );                  
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
//
//                    // if display price with tax is not set, svea's package calculations match zencart's and we can use value right away
//                    if (DISPLAY_PRICE_WITH_TAX == 'false') {
//                        $svea_order->addDiscount(
//                            WebPayItem::fixedDiscount()
//                                ->setAmountExVat( $order_total['value'] ) // $amountExVat works iff display prices with tax = false in shop
//                                ->setDescription( $order_total['title'] )
//                        );
//                    }
//                    
//                    // if display prices with tax is set to true, the ot_coupon module calculate_deductions() method returns a value including tax.
//                    elseif (DISPLAY_PRICE_WITH_TAX == 'true')
//                    {                  
//                        // get the discount coupon type
//                        $coupon = $db->Execute("select * from " . TABLE_COUPONS . " where coupon_id = '" . (int)$_SESSION['cc_id'] . "'");
//                        
//                        // if coupon specified as a percentage, Sveas fixedDiscount w/setAmountIncVat match zencart's calculation
//                        if( $coupon->fields['coupon_type'] == 'P' )
//                        {
//                            $svea_order->addDiscount(
//                                WebPayItem::fixedDiscount()
//                                    ->setAmountIncVat( $order_total['value'] )
//                                    ->setDescription( $order_total['title'] )
//                            );
//                        }
//                        
//                        // if coupon specified as a fixed amount, ZenCart's vat calculation does not fit Svea's, so we just pass on shop values as is 
//                        elseif( $coupon->fields['coupon_type'] == 'F')
//                        {                          
//                            $discountExVat = (int)$coupon->fields['coupon_amount'];
//                            $discountIncVat = $order_total['value'];
//                            
//                            // calculate the vatpercent from zencart's amount: discount vat/discount amount ex vat
//                            $zencartDiscountVatPercent = ($discountIncVat-$discountExVat)/$discountExVat *100;
//                            
//                            // split $zencartDiscountVatPercent into allowed values
//                            $taxRates = Svea\Helper::getTaxRatesInOrder($svea_order);
//                            $discountRows = Svea\Helper::splitMeanToTwoTaxRates( $coupon->fields['coupon_amount'],
//                                    $zencartDiscountVatPercent, $order_total['title'], $order_total['title'], $taxRates );
//                            
//                            // add the discount split over the valid taxrates
//                            foreach($discountRows as $row) {
//                                $svea_order = $svea_order->addDiscount( $row );
//                            }                             
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
//                    if( $order_total['value'] < 0 ) {
//                        $svea_order->addDiscount(
//                            WebPayItem::fixedDiscount()
//                                ->setAmountExVat( -1* $this->convertToCurrency(strip_tags($order_total['value']), $currency)) // given as positive amount
//                                ->setVatPercent($tax_rate)  //Optional, see info above
//                                ->setDescription($order_total['title'])        //Optional
//                        );
//                    }
//                    else {
//                        $svea_order->addFee(
//                            WebPayItem::invoiceFee()
//                                ->setAmountExVat($this->convertToCurrency(strip_tags($order_total['value']), $currency))
//                                ->setVatPercent($tax_rate)  //Optional, see info above
//                                ->setDescription($order_total['title'])        //Optional
//                        );
//                    }
//                    break;
//            }
//        }
//        
//        return $svea_order;
//    }
}
?>

