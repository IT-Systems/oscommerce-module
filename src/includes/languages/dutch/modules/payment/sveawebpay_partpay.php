<?php
/*
 * osCommerce 
 * Svea Part Payment 
 * NL 5.0
*/

define('MODULE_PAYMENT_SWPPARTPAY_TEXT_TITLE','Svea Deelbetalen');
define('MODULE_PAYMENT_SWPPARTPAY_TEXT_DESCRIPTION','Svea Deelbetalen - version 5.1.2');

define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','One or more of the allowed currencies are not defined. This must be enabled in order to use Svea Payment Plan. Log in to your admin panel, and ensure that all currencies listed as allowed in the payment module exists, and that the correct exchange rates are set.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','The default currency is not among those listed as allowed. Log in to your admin panel, and ensure that the default currency is in the allowed list in the payment module.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Payment Failed.');  

//Eu error codes
define('ERROR_CODE_20000','De order is gesloten');
define('ERROR_CODE_20001','De order is afgewezen');
define('ERROR_CODE_20002','De order heeft een fout veroorzaakt');
define('ERROR_CODE_20003','De order is verlopen');
define('ERROR_CODE_20004','De order bestaat niet');
define('ERROR_CODE_20005','Het ordertype komt niet overeen');
define('ERROR_CODE_20006','De som van alle orderrijen kan geen nul of negatief zijn ');
define('ERROR_CODE_20013','De order is in afwachting');

define('ERROR_CODE_27000','The provided campaigncode-amount combination does not match any campaign code attached to this client ');
define('ERROR_CODE_27001','Can not deliver order since the specified pdf template is missing. Contact SveaWebPay´s support ');
define('ERROR_CODE_27002','Can not partial deliver a PaymentPlan ');
define('ERROR_CODE_27003','Can not mix CampaignCode with a fixed Monthly Amount. ');
define('ERROR_CODE_27004','Can not find a suitable CampaignCode for the Monthly Amount ');

define('ERROR_CODE_30000','Het kredietrapport is afgewezen');
define('ERROR_CODE_30001','De klant is geblokkeerd of heeft vreemd/ ongewoon gedrag vertoond');
define('ERROR_CODE_30002','Op basis van de uitgevoerde kredietcontrole, is het verzoek afgewezen');
define('ERROR_CODE_30003','De klant kan niet gevonden worden door de kredietcontrole');

define('ERROR_CODE_40000','Geen klant gevonden');
define('ERROR_CODE_40001','De verstrekte landencode wordt niet ondersteund');
define('ERROR_CODE_40002','Ongeldige klantinformatie');
define('ERROR_CODE_40004','Er kan geen adres gevonden worden voor deze klant');

define('ERROR_CODE_50000','De klant is niet bevoegd voor deze methode');

define('DD_NO_CAMPAIGN_ON_AMOUNT','Kan geen geschikte actie code vinden voor de bepaalde bedrag');

// used in payment credentials form
define('FORM_TEXT_PARTPAY_ADDRESS','Factuuradres:');
define('FORM_TEXT_PAYMENT_OPTIONS','Betaalopties:');

//define('FORM_TEXT_GET_PAY_OPTIONS','Betalingsmogelijkheden ontvangen');
define('FORM_TEXT_SS_NO','Sofi-nummer:');
define('FORM_TEXT_INITIALS','Initialen');
define('FORM_TEXT_BIRTHDATE','Geboortedatum (YYYYMMDD)'); 
define('FORM_TEXT_VATNO','BTW nr');  
define('FORM_TEXT_PARTPAY_FEE','Initial fee will be added to your order.');
//define('FORM_TEXT_GET_PAYPLAN','Adresgegevens ophalen');

define('ERROR_CODE_DEFAULT','Svea Error: ');

define('FORM_TEXT_GET_ADDRESS','Adresgegevens ophalen');

// Tupas API related messages
define('FORM_TEXT_TUPAS_AUTHENTICATE','Authenticate on online bank');
define('ERROR_TAMPERED_PARAMETERS', 'Unexpected error occurred during authentication. Please, try again.');
define('ERROR_TUPAS_NOT_SET', 'You have to authenticate yourself in online bank.');
define('ERROR_TUPAS_MISMATCH', 'The SSN doesn\'t match with the one that Tupas authentication sent. Please, try again.');
?>
