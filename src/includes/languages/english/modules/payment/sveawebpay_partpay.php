<?php
/*
 * osCommerce 
 * Svea Part Payment
 * EN 5.0
 */

define('MODULE_PAYMENT_SWPPARTPAY_TEXT_TITLE','Svea Payment Plan');
define('MODULE_PAYMENT_SWPPARTPAY_TEXT_DESCRIPTION','Svea Payment Plan - version 5.0');

define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','One or more of the allowed currencies are not defined. This must be enabled in order to use Svea Payment Plan. Log in to your admin panel, and ensure that all currencies listed as allowed in the payment module exists, and that the correct exchange rates are set.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','The default currency is not among those listed as allowed. Log in to your admin panel, and ensure that the default currency is in the allowed list in the payment module.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Payment Failed.');  

//Eu error codes
define('ERROR_CODE_20000','Order closed');
define('ERROR_CODE_20001','Order is denied');
define('ERROR_CODE_20002','Something is wrong with the order  ');
define('ERROR_CODE_20003','Order has expired');
define('ERROR_CODE_20004','Order does not exist');
define('ERROR_CODE_20005','OrderType mismatch');
define('ERROR_CODE_20006','The sum of all order rows cannot be zero or negative');
define('ERROR_CODE_20013','Order is pending');
define('ERROR_CODE_20014','OrderAlternateDeliveryAddressNotAllowed');
define('ERROR_CODE_20019','ClientOrderNumber
AlreadyExists');
define('ERROR_CODE_20021','NoOrderRows');
define('ERROR_CODE_20023','DiscountPercentNotAllowed');
define('ERROR_CODE_20024','InvalidVatPercent');

define('ERROR_CODE_27000','The provided campaigncode-amount combination does not match any campaign code attached to this client ');
define('ERROR_CODE_27001','Can not deliver order since the specified pdf template is missing. Contact SveaWebPay\'s support ');
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
define('ERROR_CODE_40003','Invalid Co-Customer information');define('ERROR_CODE_40004','Could not find any addresses for this customer');
define('ERROR_CODE_40005','CustomerIsNotPreApproved');

define('ERROR_CODE_50000','Client is not authorized for this method');
define('ERROR_CODE_50001','OrderType is required');
define('ERROR_CODE_50002','AddressSelector is not valid for this CountryCode');
define('ERROR_CODE_50003','CreatePaymentPlanDetails must be null when OrderType is Invoice');
define('ERROR_CODE_50004','CreatePaymentPlanDetails must not be null when OrderType is PaymentPlan');
define('ERROR_CODE_50005','Missing Identification Value');
define('ERROR_CODE_50006','No order amount limits exists for this client');
define('ERROR_CODE_50007','Invalid applicant CountryCode');
define('ERROR_CODE_50008','InvoiceDistributionType is required');
define('ERROR_CODE_50009','DeliverInvoiceDetails must not be null');
define('ERROR_CODE_50010','InvoiceToCredit must be null');
define('ERROR_CODE_50011','InvoiceToCredit must not be null');
define('ERROR_CODE_50012','OrderInformation is required');
define('ERROR_CODE_50013','CustomerIdentity is required');
define('ERROR_CODE_50014','Invalid input value. See ErrorMessage for description');
define('ERROR_CODE_50015','The specified OrderType is invalid');
define('ERROR_CODE_50016','The order does not belong to this
client');
define('ERROR_CODE_50017','Missing applicant CountryCode');
define('ERROR_CODE_50018','The OrderDate is invalid');
define('ERROR_CODE_50019','An Individual-type CustomerIdentity cannot have a CompanyIdentity-structure');
define('ERROR_CODE_50020','A Company-type CustomerIdentity cannot have an IndividualIdentity-structure');
define('ERROR_CODE_50021','IndividualIdentity must be null for Individuals for this country');
define('ERROR_CODE_50022','CompanyIdentity must be null for Companies for this country');
define('ERROR_CODE_50023','The provided AddressSelector is invalid');
define('ERROR_CODE_50024','AddressProvider is not supported for this CustomerType');
define('ERROR_CODE_50025','The zip code is not valid for this country');
define('ERROR_CODE_50028','CloseOrderInformationIsRequired');
define('ERROR_CODE_50029','AddressInformationIsRequired');
define('ERROR_CODE_50030','InvalidCountryCodeGetAddresses');
define('ERROR_CODE_50031','ZipCodeShouldBeNullForCompanies');
define('ERROR_CODE_50032','InvalidVatAmount');

define('DD_NO_CAMPAIGN_ON_AMOUNT','Can not find a suitable CampaignCode for the given amount');

// used in payment credentials form
define('FORM_TEXT_PARTPAY_ADDRESS','Invoice address:');
define('FORM_TEXT_PAYMENT_OPTIONS','Payment options:');

//define('FORM_TEXT_GET_PAY_OPTIONS','Get payment options');
define('FORM_TEXT_SS_NO','Social Security No (YYYYMMDD-XXXX):');
define('FORM_TEXT_INITIALS','Initials');       
define('FORM_TEXT_BIRTHDATE','Date of Birth');              
define('FORM_TEXT_VATNO','Vat Number'); 
define('FORM_TEXT_PARTPAY_FEE','Initial fee will be added to your order.');
//define('FORM_TEXT_GET_PAYPLAN','Get address:');

define('ERROR_CODE_DEFAULT','Svea Error: ');

define('FORM_TEXT_GET_ADDRESS','Get Address');  // replaces FORM_TEXT_GET_PAYPLAN

// Tupas API related messages
define('FORM_TEXT_TUPAS_AUTHENTICATE','Authenticate on online bank');
define('ERROR_TAMPERED_PARAMETERS', 'Unexpected error occurred during authentication. Please, try again.');
define('ERROR_TUPAS_NOT_SET', 'You have to authenticate yourself in online bank.');
define('ERROR_TUPAS_MISMATCH', 'The SSN doesn\'t match with the one that Tupas authentication sent. Please, try again.');
?>