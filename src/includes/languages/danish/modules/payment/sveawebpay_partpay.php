<?php
/*
SVEAWEBPAY PAYMENT MODULE FOR osCOMMERCE 2.3
-----------------------------------------------
Version 5.0
*/
define('MODULE_PAYMENT_SWPPARTPAY_TEXT_TITLE','Svea Delbetaling');
define('MODULE_PAYMENT_SWPPARTPAY_TEXT_DESCRIPTION','SveaWebPay Webservice Delbetaling - vers. 5.0');
define('MODULE_PAYMENT_SWPPARTPAY_HANDLING_APPLIES','Et admininistrationsgebyr på %s vil blive føjet til ordren ved kassen.');
define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','En eller flere af de tilladte valutaer er ikke præciserede. Dette skal ske, førend SweaWebPay Hosted Solution kan benyttes. Login på dit admin panel og tjek, at alle valutaer er listede som tilladt i betalingsmodulet, og at de rigtige vekselkurser er indstillede korrekt.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','Standardvalutaen er ikke blandt de listede som tilladte. Login i dit admin panel og tjek, at standardvalutaen er på listen over tilladte valutaer i betalingsmodulet.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Betaling mislykkedes.');  

define('ERROR_CODE_1','Kreditoplysninger kan ikke findes');
define('ERROR_CODE_2','Butikkens eller Sveas kreditlimit er overskredet');
define('ERROR_CODE_3','Denne kunde er blokeret eller har udvist mærkværdig adfærd');
define('ERROR_CODE_4','Delbetaling afbrudt');
define('ERROR_CODE_5','Med denne ordre vil kunden overskride sin kreditlimit hos Svea');
define('ERROR_CODE_6','Kreditgrænsen for lejlighedsvise lån er overskredet.');
define('ERROR_CODE_7','Kombinationen af kampagnekoder og beløb er ukorrekt.');
define('ERROR_CODE_8','Kunden har en dårlig kredithistorik hos Svea');
define('ERROR_CODE_9','Kunden er ikke listet hos kreditlimit-udbyderen');
define('ERROR_CODE_DEFAULT', 'Fejl i behandling af betaling. Indre fejl');

//Form on checkout
define('FORM_TEXT_SS_NO','SSN:');
define('FORM_TEXT_GET_ADDRESS','Få adresse og betalingsmuligheder');
define('FORM_TEXT_GET_PAY_OPTIONS','Få betalingsmuligheder');
define('FORM_TEXT_INVOICE_ADDRESS','Faktureringsadresse:');
define('FORM_TEXT_PAYMENT_OPTIONS','Betalingsmuligheder:');

define('DD_PARTPAY_IN','Delbetal i ');
define('DD_PAY_IN_THREE','Betal over 3 måneder');
define('DD_MONTHS',' måneder');
define('DD_CURRENY_PER_MONTH',' kr./måned');
?>