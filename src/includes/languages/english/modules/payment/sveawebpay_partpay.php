<?php
/*
SVEAWEBPAY PAYMENT MODULE FOR osCOMMERCE 2.3
-----------------------------------------------
Version 5.0
*/
define('MODULE_PAYMENT_SWPPARTPAY_TEXT_TITLE','SVEA Part Payment');
define('MODULE_PAYMENT_SWPPARTPAY_TEXT_DESCRIPTION','SveaWebPay Webservice Part Payment - ver 5.0');
define('MODULE_PAYMENT_SWPPARTPAY_HANDLING_APPLIES','A handling fee of %s will be applied to this order on checkout.');
define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','One or more of the allowed currencies are not defined. This must be enabled in order to use the SweaWebPay Hosted Solution. Log in to your admin panel, and ensure that all currencies listed as allowed in the paymend module exists, and that the correct exchange rates are set.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','The default currency is not among those listed as allowed. Log in to your admin panel, and ensure that the default currency is in the allowed list in the payment module.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Payment Failed.');  

define('ERROR_CODE_1','Cannot get credit rating information');
define('ERROR_CODE_2','Store or Sveas credit limit overused');
define('ERROR_CODE_3','This customer is blocked or has shown strange/unusual behavior');
define('ERROR_CODE_4','Part Payment cancelled');
define('ERROR_CODE_5','The order would cause the client to exceed Sveas credit limit');
define('ERROR_CODE_6','The credit limit for occasional loans has been exceeded.');
define('ERROR_CODE_7','The combination of campaign code and amount is incorrect.');
define('ERROR_CODE_8','The customer has a poor credit history at Svea');
define('ERROR_CODE_9','The customer is not listed with the credit limit supplier');
define('ERROR_CODE_DEFAULT', 'Error processing payment. Internal error');

//Form on checkout
define('FORM_TEXT_SS_NO','SSN:');
define('FORM_TEXT_GET_ADDRESS','Get address and payment options');
define('FORM_TEXT_GET_PAY_OPTIONS','Get payment options');
define('FORM_TEXT_INVOICE_ADDRESS','Invoice address:');
define('FORM_TEXT_PAYMENT_OPTIONS','Payment options:');

define('DD_PARTPAY_IN','Partpay in ');
define('DD_PAY_IN_THREE','Pay within 3 months');
define('DD_MONTHS',' months');
define('DD_CURRENY_PER_MONTH',' kr/month');
?>