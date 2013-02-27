<?php
/*
SVEAWEBPAY PAYMENT MODULE FOR osCOMMERCE 2.3
-----------------------------------------------
Version 4.0
*/
define('MODULE_PAYMENT_SWPPARTPAY_TEXT_TITLE','SVEA Osamaksu');
define('MODULE_PAYMENT_SWPPARTPAY_TEXT_DESCRIPTION','SveaWebpay osamaksu Webservice versio  4.0');
define('MODULE_PAYMENT_SWPPARTPAY_HANDLING_APPLIES','Tilaukseen lisätään %s palvelumaksusta');
define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','Yksi tai useampi sallituista valuutoista ei ole määritelty. Nämä on määriteltävä käyttämään SveaWebPay hosted solutionia. Kirjaudu admin paneeliin ja varmista että.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','Standardivaluuta ei ole sallitussa luettelossa. Kirjaudu admin paneeliin ja varmista että oletusvaluutta kuuluu niihin sallituihin maksu-moduulissa.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Maksu epäonnistui.');  

define('ERROR_CODE_1','Ei voinut hakea luottotietoja');
define('ERROR_CODE_2','Liikeen tai Svean luottoraja ylitetty');
define('ERROR_CODE_3','Asiakas tukittu tai on esiintynyt epätavallista käyttätymistä luotto sovittelijalla.');
define('ERROR_CODE_4','Osamaksu katkastui');
define('ERROR_CODE_5','Tämä tilaus aiheutaisi että luottoraja ylittyy');
define('ERROR_CODE_6','Lainojen luottoraja on ylitetty.');
define('ERROR_CODE_7','Kampanjakoodi ja summa ei täsmää');
define('ERROR_CODE_8','Asiakkaala on huono luotto historia Sveealla');
define('ERROR_CODE_9','Asiakas ei ole listassa');
define('ERROR_CODE_DEFAULT', 'Virhe maksussa, sisäinen virhe');

//Form on checkout
define('FORM_TEXT_SS_NO','Henkilötunnus:');
define('FORM_TEXT_GET_ADDRESS','Hae osoite ja maskuvaihtoehdot');
define('FORM_TEXT_GET_PAY_OPTIONS','Hae maksuvaihtoehdot');
define('FORM_TEXT_INVOICE_ADDRESS','Laskuosoite:');
define('FORM_TEXT_PAYMENT_OPTIONS','Osamaksuvaihtoehdot:');

define('DD_PARTPAY_IN','Maksa ');
define('DD_PAY_IN_THREE','Maksa 3 kuukauden päästä');
define('DD_MONTHS',' Kuukausia');
define('DD_CURRENY_PER_MONTH',' eur/kuukausi');
?>