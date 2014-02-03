<?php
/*
SVEAWEBPAY PAYMENT MODULE FOR osCOMMERCE 2.3
-----------------------------------------------
Version 5.0
*/
define('MODULE_PAYMENT_SWPINVOICE_TEXT_TITLE','SVEA Invoice');
define('MODULE_PAYMENT_SWPINVOICE_TEXT_DESCRIPTION','SveaWebPay Webservice Invoice - ver 5.0');
define('MODULE_PAYMENT_SWPINVOICE_HANDLING_APPLIES','A handling fee of %s will be applied to this order on checkout.');
define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','One or more of the allowed currencies are not defined. This must be enabled in order to use the SweaWebPay Hosted Solution. Log in to your admin panel, and ensure that all currencies listed as allowed in the paymend module exists, and that the correct exchange rates are set.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','The default currency is not among those listed as allowed. Log in to your admin panel, and ensure that the default currency is in the allowed list in the payment module.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Payment Failed.');  

define('ERROR_CODE_1','Cannot get credit rating information');
define('ERROR_CODE_2','Store or Sveas credit limit overused');
define('ERROR_CODE_3','This customer is blocked or has shown strange/unusual behavior');
define('ERROR_CODE_4','The order is too old and can no longer be invoiced against');
define('ERROR_CODE_5','The order would cause the client to exceed Sveas credit limit');
define('ERROR_CODE_6','The order exceeds the highest order amount permitted at Svea');
define('ERROR_CODE_7','The order exceeds your highest order amount permitted');
define('ERROR_CODE_8','The customer has a poor credit history at Svea');
define('ERROR_CODE_9','The customer is not listed with the credit limit supplier');
define('ERROR_CODE_DEFAULT', 'Error processing payment. Internal error');

//Form on checkout
define('FORM_TEXT_COMPANY_OR_PRIVATE','Select Company/Private:');
define('FORM_TEXT_COMPANY','Company');
define('FORM_TEXT_PRIVATE','Private');
define('FORM_TEXT_SS_NO','SSN:');
define('FORM_TEXT_INVOICE_GET_ADDRESS','Get Address');
define('FORM_TEXT_INVOICE_ADDRESS','Invoice Address:');
define('FORM_TEXT_INVOICE_FEE','Invoice Fee:');
?>