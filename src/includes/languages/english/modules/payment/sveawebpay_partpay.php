<?php
/*
SVEAWEBPAY PAYMENT MODULE FOR osCommerce
-----------------------------------------------
Version 5.0
English
*/

// ZC
define('MODULE_PAYMENT_SWPPARTPAY_TEXT_TITLE','Svea Payment Plan');
define('MODULE_PAYMENT_SWPPARTPAY_TEXT_DESCRIPTION','Svea Payment Plan - version 5.0');

define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','One or more of the allowed currencies are not defined. This must be enabled in order to use the SweaWebPay Hosted Solution. Log in to your admin panel, and ensure that all currencies listed as allowed in the payment module exists, and that the correct exchange rates are set.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','The default currency is not among those listed as allowed. Log in to your admin panel, and ensure that the default currency is in the allowed list in the payment module.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Payment Failed.');  

//Eu error codes
define('ERROR_CODE_20000','Order closed');
define('ERROR_CODE_20001','Order is denied ');
define('ERROR_CODE_20002','Something is wrong with the order  ');
define('ERROR_CODE_20003','Order has expired  ');
define('ERROR_CODE_20004','Order does not exist  ');
define('ERROR_CODE_20005','Wrong Order Type  ');
define('ERROR_CODE_20006','InvalidAmount ');
define('ERROR_CODE_20013',' ');

define('ERROR_CODE_27000','The provided campaigncode-amount combination does not match any campaign code attached to this client ');
define('ERROR_CODE_27001','Can not deliver order since the specified pdf template is missing. Contact SveaWebPay�s support ');
define('ERROR_CODE_27002','Can not partial deliver a PaymentPlan ');
define('ERROR_CODE_27003','Can not mix CampaignCode with a fixed Monthly Amount. ');
define('ERROR_CODE_27004','Can not find a suitable CampaignCode for the Monthly Amount ');

define('ERROR_CODE_30000','The credit report was rejected');
define('ERROR_CODE_30001','This customer is blocked or has shown strange/unusual behavior');
define('ERROR_CODE_30002','Based upon the performed credit check the request was rejected');
define('ERROR_CODE_30003','Customer cannot be found by credit check ');

define('ERROR_CODE_40000','No customer found');
define('ERROR_CODE_40001','The provided CountryCode is not supported');
define('ERROR_CODE_40002','Invalid Customer information');
define('ERROR_CODE_40004','Could not find any addresses for this customer ');

define('ERROR_CODE_50000','Client is not authorized for this method');

define('DD_NO_CAMPAIGN_ON_AMOUNT','Can not find a suitable CampaignCode for the given amount');

// used in payment credentials form
define('FORM_TEXT_PARTPAY_ADDRESS','Invoice address:');
define('FORM_TEXT_PAYMENT_OPTIONS','Payment options:');

define('FORM_TEXT_GET_PAY_OPTIONS','Get payment options');
define('FORM_TEXT_SS_NO','Social Security No (YYYYMMDD-XXXX):');
define('FORM_TEXT_INITIALS','Initials');                                
define('FORM_TEXT_BIRTHDATE','Date of Birth');              
define('FORM_TEXT_VATNO','Vat Number'); 
define('FORM_TEXT_PARTPAY_FEE','Initial fee will be added.');
define('FORM_TEXT_GET_PAYPLAN','Get address:');

define('ERROR_CODE_DEFAULT','Svea Error: ');

//// ZC
//define('MODULE_PAYMENT_SWPPARTPAY_TEXT_TITLE','Svea Payment Plan');
//define('MODULE_PAYMENT_SWPPARTPAY_TEXT_DESCRIPTION','Svea Payment Plan Solution - version 4.0');
//
//define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','One or more of the allowed currencies are not defined. This must be enabled in order to use the SweaWebPay Hosted Solution. Log in to your admin panel, and ensure that all currencies listed as allowed in the payment module exists, and that the correct exchange rates are set.');
//define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','The default currency is not among those listed as allowed. Log in to your admin panel, and ensure that the default currency is in the allowed list in the payment module.');  
//define('ERROR_MESSAGE_PAYMENT_FAILED','Payment Failed.');  
//
////Eu error codes
//define('ERROR_CODE_20000','Order closed');
//define('ERROR_CODE_20001','Order is denied ');
//define('ERROR_CODE_20002','Something is wrong with the order  ');
//define('ERROR_CODE_20003','Order has expired  ');
//define('ERROR_CODE_20004','Order does not exist  ');
//define('ERROR_CODE_20005','Wrong Order Type  ');
//define('ERROR_CODE_20006','InvalidAmount ');
//define('ERROR_CODE_20013',' ');
//
//define('ERROR_CODE_27000','The provided campaigncode-amount combination does not match any campaign code attached to this client ');
//define('ERROR_CODE_27001','Can not deliver order since the specified pdf template is missing. Contact SveaWebPay�s support ');
//define('ERROR_CODE_27002','Can not partial deliver a PaymentPlan ');
//define('ERROR_CODE_27003','Can not mix CampaignCode with a fixed Monthly Amount. ');
//define('ERROR_CODE_27004','Can not find a suitable CampaignCode for the Monthly Amount ');
//
//define('ERROR_CODE_30000','The credit report was rejected');
//define('ERROR_CODE_30001','This customer is blocked or has shown strange/unusual behavior');
//define('ERROR_CODE_30002','Based upon the performed credit check the request was rejected');
//define('ERROR_CODE_30003','Customer cannot be found by credit check ');
//
//define('ERROR_CODE_40000','No customer found');
//define('ERROR_CODE_40001','The provided CountryCode is not supported');
//define('ERROR_CODE_40002','Invalid Customer information');
//define('ERROR_CODE_40004','Could not find any addresses for this customer ');
//
//define('ERROR_CODE_50000','Client is not authorized for this method');
//
//define('DD_NO_CAMPAIGN_ON_AMOUNT','Can not find a suitable CampaignCode for the given amount');
//
//// used in payment credentials form
//define('FORM_TEXT_PARTPAY_ADDRESS','Invoice address:');
//define('FORM_TEXT_PAYMENT_OPTIONS','Payment options:');
//
//define('FORM_TEXT_GET_PAY_OPTIONS','Get payment options');
//define('FORM_TEXT_SS_NO','Social Security No (YYYYMMDD-XXXX):');
//define('FORM_TEXT_INITIALS','Initials');                                
//define('FORM_TEXT_BIRTHDATE','Date of Birth');              
//define('FORM_TEXT_VATNO','Vat Number'); 
//define('FORM_TEXT_PARTPAY_FEE','initial fee will be added ');
//define('FORM_TEXT_GET_PAYPLAN','Get address:');
//
//define('ERROR_CODE_DEFAULT','Svea Error: ');


// OLD osC
//define('MODULE_PAYMENT_SWPPARTPAY_TEXT_TITLE','SVEA Part Payment');
//define('MODULE_PAYMENT_SWPPARTPAY_TEXT_DESCRIPTION','SveaWebPay Webservice Part Payment - ver 5.0');
//define('MODULE_PAYMENT_SWPPARTPAY_HANDLING_APPLIES','A handling fee of %s will be applied to this order on checkout.');
//define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','One or more of the allowed currencies are not defined. This must be enabled in order to use the SweaWebPay Hosted Solution. Log in to your admin panel, and ensure that all currencies listed as allowed in the paymend module exists, and that the correct exchange rates are set.');
//define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','The default currency is not among those listed as allowed. Log in to your admin panel, and ensure that the default currency is in the allowed list in the payment module.');  
//define('ERROR_MESSAGE_PAYMENT_FAILED','Payment Failed.');  
//
//define('ERROR_CODE_1','Cannot get credit rating information');
//define('ERROR_CODE_2','Store or Sveas credit limit overused');
//define('ERROR_CODE_3','This customer is blocked or has shown strange/unusual behavior');
//define('ERROR_CODE_4','Part Payment cancelled');
//define('ERROR_CODE_5','The order would cause the client to exceed Sveas credit limit');
//define('ERROR_CODE_6','The credit limit for occasional loans has been exceeded.');
//define('ERROR_CODE_7','The combination of campaign code and amount is incorrect.');
//define('ERROR_CODE_8','The customer has a poor credit history at Svea');
//define('ERROR_CODE_9','The customer is not listed with the credit limit supplier');
//define('ERROR_CODE_DEFAULT', 'Error processing payment. Internal error');
//
////Form on checkout
//define('FORM_TEXT_SS_NO','SSN:');
//define('FORM_TEXT_GET_ADDRESS','Get address and payment options');
//define('FORM_TEXT_GET_PAY_OPTIONS','Get payment options');
//define('FORM_TEXT_INVOICE_ADDRESS','Invoice address:');
//define('FORM_TEXT_PAYMENT_OPTIONS','Payment options:');
//
//define('DD_PARTPAY_IN','Partpay in ');
//define('DD_PAY_IN_THREE','Pay within 3 months');
//define('DD_MONTHS',' months');
//define('DD_CURRENY_PER_MONTH',' kr/month');
?>