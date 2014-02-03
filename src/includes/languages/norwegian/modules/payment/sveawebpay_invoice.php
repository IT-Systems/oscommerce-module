<?php
/*
SVEAWEBPAY PAYMENT MODULE FOR osCOMMERCE 2.3
-----------------------------------------------
Version 5.0
*/
define('MODULE_PAYMENT_SWPINVOICE_TEXT_TITLE','SVEA faktura');
define('MODULE_PAYMENT_SWPINVOICE_TEXT_DESCRIPTION','SveaWebPay Webbservice Faktura - ver 5.0');
define('MODULE_PAYMENT_SWPINVOICE_HANDLING_APPLIES','En håndteringskostnad på % vil bli lagt til denne order ved checkout.');
define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','En eller fler av de tillatte valutaene er ikke definert. Dette må være aktivert for å kunne bruke SweaWebPay Hosted Solution. Logg inn på din admin panel, og sjekk at de valuta som er brukt i betalingen er listet som tillatt i betalingsmodulen, og at riktig valutakurs er innstilt.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','Den forinstilte valutaen er ikke inkludert på listen over tillatte valuta. Logg inn på din admin panel, og sjekk at forinstilt valuta er på listen over tillatte valuta i betalingsmodulen.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Betalingen misslyktes.');  

define('ERROR_CODE_1','Kan ikke få kredittopplysingsinformasjon');
define('ERROR_CODE_2','Butikken eller Sveas limit er overskredet');
define('ERROR_CODE_3','Denne kunden er blokkert eller har vist merkelig/uvanlig adferd');
define('ERROR_CODE_4','Orderen er for gammel og kan ikke lenger bli til en faktura');
define('ERROR_CODE_5','Orderen ville få kunden til å overskrede Sveas limit');
define('ERROR_CODE_6','Orderen overskreder det høyeste tillatt orderbeløpet tillatt hos Svea');
define('ERROR_CODE_7','Orderen overskreder din høyeste tillatte order beløp');
define('ERROR_CODE_8','Kunden har dårlig kreditthistorikk hos Svea');
define('ERROR_CODE_9','Kunden er ikke listet med kredittlimitleverantøren');
define('ERROR_CODE_DEFAULT', 'Feil ved prosessering av betaling. Intern feil');

//Form on checkout
define('FORM_TEXT_COMPANY_OR_PRIVATE','Velg foretak/privat:');
define('FORM_TEXT_COMPANY','Foretak');
define('FORM_TEXT_PRIVATE','Privat');
define('FORM_TEXT_SS_NO','SSN:');
define('FORM_TEXT_INVOICE_GET_ADDRESS','Hent adresse');
define('FORM_TEXT_INVOICE_ADDRESS','Faktura adresse:');
define('FORM_TEXT_INVOICE_FEE','Fakturagebyr:');
?>