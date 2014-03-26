<?php
/*
 * osCommerce 
 * Svea Invoice 
 * NL 5.0
*/
define('MODULE_PAYMENT_SWPINVOICE_TEXT_TITLE','Svea Factuur');
define('MODULE_PAYMENT_SWPINVOICE_TEXT_DESCRIPTION','Svea Factuur - version 5.1.1');
define('MODULE_PAYMENT_SWPINVOICE_HANDLING_APPLIES','De Factuurkosten %s zullen worden toegevoegd');

define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','One or more of the allowed currencies are not defined. This must be enabled in order to use the SweaWebPay Hosted Solution. Log in to your admin panel, and ensure that all currencies listed as allowed in the payment module exists, and that the correct exchange rates are set.');
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

define('ERROR_CODE_30000','Het kredietrapport is afgewezen');
define('ERROR_CODE_30001','De klant is geblokkeerd of heeft vreemd/ ongewoon gedrag vertoond');
define('ERROR_CODE_30002','Op basis van de uitgevoerde kredietcontrole, is het verzoek afgewezen');
define('ERROR_CODE_30003','De klant kan niet gevonden worden door de kredietcontrole');

define('ERROR_CODE_40000','Geen klant gevonden');
define('ERROR_CODE_40001','De verstrekte landencode wordt niet ondersteund');
define('ERROR_CODE_40002','Ongeldige klantinformatie');
define('ERROR_CODE_40004','Er kan geen adres gevonden worden voor deze klant');

define('ERROR_CODE_50000','De klant is niet bevoegd voor deze methode');

//invoice specific
define('ERROR_CODE_24000','Het factuurbedrag is hoger dan de toegestane limiet');

// used in payment credentials form
define('FORM_TEXT_INVOICE_ADDRESS','Factuuradres');
define('FORM_TEXT_INVOICE_FEE','Factuurkosten:');
define('FORM_TEXT_COMPANY','Bedrijf');
define('FORM_TEXT_PRIVATE','Privé');
define('FORM_TEXT_GET_ADDRESS','Adresgegevens ophalen');

define('FORM_TEXT_SS_NO','Sofi-nummer:');
define('FORM_TEXT_INITIALS','Initialen');                                
define('FORM_TEXT_BIRTHDATE','Geboortedatum (YYYYMMDD)');              
define('FORM_TEXT_VATNO','BTW nr');

define('ERROR_CODE_DEFAULT','Svea Error: ');
?>
