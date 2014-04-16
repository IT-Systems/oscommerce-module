<?php
/*
 * osCommerce 
 * Svea Card
 * EN 5.0
 */
define('MODULE_PAYMENT_SWPCREDITCARD_TEXT_TITLE','Svea Card');
define('MODULE_PAYMENT_SWPCREDITCARD_TEXT_DESCRIPTION','Svea Card - version 5.1.2');

define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','One or more of the allowed currencies are not defined. This must be enabled in order to use Svea Card. Log in to your admin panel, and ensure that all currencies listed as allowed in the payment module exists, and that the correct exchange rates are set.');
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