<?php
/*
SVEAWEBPAY PAYMENT MODULE FOR osCOMMERCE 2.3
-----------------------------------------------
Version 5.0
*/
define('MODULE_PAYMENT_SWPINVOICE_TEXT_TITLE','Svea Lasku');
define('MODULE_PAYMENT_SWPINVOICE_TEXT_DESCRIPTION','SveaWebPay Lasku Webservice  - versio 5.0');
define('MODULE_PAYMENT_SWPINVOICE_HANDLING_APPLIES','Tilaukseen lisätään %s %s palvelumaksusta');
define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','Yksi tai useampi sallituista valuutoista ei ole määritelty. Nämä on määriteltävä käyttämään SveaWebPay hosted solutionia. Kirjaudu admin paneeliin ja varmista että.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','Standardivaluuta ei ole sallitussa luettelossa. Kirjaudu admin paneeliin ja varmista että oletusvaluutta kuuluu niihin sallituihin maksu-moduulissa.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Maksu epäonnistui');  

define('ERROR_CODE_1','Ei voinut hakea luottotietoja');
define('ERROR_CODE_2','Liikeen tai Svean luottoraja ylitetty');
define('ERROR_CODE_3','Asiakas tukittu tai on esiintynyt epätavallista käyttätymistä luotto sovittelijalla.');
define('ERROR_CODE_4','Tilaus on mennyt umpeen');
define('ERROR_CODE_5','Tämä tilaus aiheutaisi että luottoraja ylittyy');
define('ERROR_CODE_6','Tilaus ylittää sallitun  summan Sveealla');
define('ERROR_CODE_7','The order exceeds your highest order amount permitted');
define('ERROR_CODE_8','Asiakkaala on huono luotto historia Sveealla');
define('ERROR_CODE_9','Asiakas ei ole listassa');
define('ERROR_CODE_DEFAULT', 'Virhe maksussa, sisäinen virhe');

//Form on checkout
define('FORM_TEXT_COMPANY_OR_PRIVATE','Valitse liiketoiminta/yksityinen:');
define('FORM_TEXT_COMPANY','Liiketoiminta');
define('FORM_TEXT_PRIVATE','Yksityinen');
define('FORM_TEXT_SS_NO','Henkilötunnus/Y-tunnus::');
define('FORM_TEXT_INVOICE_GET_ADDRESS','Hae osoite');
define('FORM_TEXT_INVOICE_ADDRESS','Laskuosoite:');
define('FORM_TEXT_INVOICE_FEE','Laskutus maksu:');
?>