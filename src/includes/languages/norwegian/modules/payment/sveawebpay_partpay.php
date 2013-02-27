<?php
/*
SVEAWEBPAY PAYMENT MODULE FOR osCOMMERCE 2.3
-----------------------------------------------
Version 4.0
*/
define('MODULE_PAYMENT_SWPPARTPAY_TEXT_TITLE','SVEA Delbetaling');
define('MODULE_PAYMENT_SWPPARTPAY_TEXT_DESCRIPTION','SveaWebPay Webservice Delbetaling - ver 4.0');
define('MODULE_PAYMENT_SWPPARTPAY_HANDLING_APPLIES','En håndteringskostnad på % vil bli lagt til denne order ved checkout.');
define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','En eller fler av de tillatte valutaene er ikke definert. Dette må være aktivert for å kunne bruke SweaWebPay Hosted Solution. Logg inn på din admin panel, og sjekk at de valuta som er brukt i betalingen er listet som tillatt i betalingsmodulen, og at riktig valutakurs er innstilt.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','Den forinstilte valutaen er ikke inkludert på listen over tillatte valuta. Logg inn på din admin panel, og sjekk at forinstilt valuta er på listen over tillatte valuta i betalingsmodulen.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Betalingen misslyktes.');  

define('ERROR_CODE_1','Kan ikke få kredittopplysingsinformasjon');
define('ERROR_CODE_2','Butikken eller Sveas limit er overskredet');
define('ERROR_CODE_3','Denne kunden er blokkert eller har vist merkelig/uvanlig adferd');
define('ERROR_CODE_4','Delbetaling kansellert');
define('ERROR_CODE_5','Orderen ville få kunden til å overskrede Sveas limit');
define('ERROR_CODE_6','Kredittgrensen for tilfeldige lån er overskredet.');
define('ERROR_CODE_7','Kombinasjonen av kampanjekode og beløp er ikke korrekt.');
define('ERROR_CODE_8','Kunden har dårlig kreditthistorikk hos Svea');
define('ERROR_CODE_9','Kunden er ikke listet hos kredittlimitleverantøren');
define('ERROR_CODE_DEFAULT', 'Feil ved prosessering av betaling. Intern feil');

//Form on checkout
define('FORM_TEXT_SS_NO','SSN:');
define('FORM_TEXT_GET_ADDRESS','Hent adresse og betalingsalternativ');
define('FORM_TEXT_GET_PAY_OPTIONS','Hent betalingsalternativ');
define('FORM_TEXT_INVOICE_ADDRESS','Fakturaadresse:');
define('FORM_TEXT_PAYMENT_OPTIONS','Betalingsalternativ:');

define('DD_PARTPAY_IN','Delbetal på ');
define('DD_PAY_IN_THREE','Delbetal på 3 måneder');
define('DD_MONTHS',' måneder');
define('DD_CURRENY_PER_MONTH',' kr/måned');
?>