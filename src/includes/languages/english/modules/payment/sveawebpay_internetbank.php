<?php
/*
SVEAWEBPAY PAYMENT MODULE FOR osCOMMERCE 2.3
-----------------------------------------------
Version 5.0
*/
define('MODULE_PAYMENT_SWPINTERNETBANK_TEXT_TITLE','SVEA Direct Bank');
define('MODULE_PAYMENT_SWPINTERNETBANK_TEXT_DESCRIPTION','SveaWebPay Direct Bank Hosted - ver 5.0');
define('MODULE_PAYMENT_SWPINTERNETBANK_HANDLING_APPLIES','A handling fee of %s will be applied to this order on checkout.');
define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','One or more of the allowed currencies are not defined. This must be enabled in order to use the SweaWebPay Hosted Solution. Log in to your admin panel, and ensure that all currencies listed as allowed in the paymend module exists, and that the correct exchange rates are set.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','The default currency is not among those listed as allowed. Log in to your admin panel, and ensure that the default currency is in the allowed list in the payment module.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Payment Failed.');  

define('ERROR_CODE_100','Internal system error such as databases are down, resources not available etc. contact integrator');
define('ERROR_CODE_105','Invalid transaction status');
define('ERROR_CODE_106','Failure at third party e.g. failure at the bank');
define('ERROR_CODE_107','Transaction rejected by bank');
define('ERROR_CODE_108','Transaction cancelled');
define('ERROR_CODE_109','Transaction not found at the bank');
define('ERROR_CODE_110','Invalid transaction ID');
define('ERROR_CODE_113','Payment method not configured for merchant');
define('ERROR_CODE_114','Timeout at the bank');
define('ERROR_CODE_121','The card has expired');
define('ERROR_CODE_124','Amount exceeds the limit');
define('ERROR_CODE_143','Credit denied by bank');

define('ERROR_CODE_DEFAULT', 'Error processing payment. Please provide this code when contacting support. Error code: ');
?>