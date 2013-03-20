<?php
/*
SVEAWEBPAY PAYMENT MODULE FOR osCOMMERCE 2.3
-----------------------------------------------
Version 4.0
*/
define('MODULE_PAYMENT_SWPCREDITCARD_TEXT_TITLE','SVEA kort betaling');
define('MODULE_PAYMENT_SWPCREDITCARD_TEXT_DESCRIPTION','SveaWebPay Card Payment Hosted - ver 4.0');
define('MODULE_PAYMENT_SWPCREDITCARD_HANDLING_APPLIES','En håndteringskostnad på % vil bli lagt til denne order ved checkout.');
define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','En eller fler av de tillatte valutaene er ikke definert. Dette må være aktivert for å kunne bruke SweaWebPay Hosted Solution. Logg inn på din admin panel, og sjekk at de valuta som er brukt i betalingen er listet som tillatt i betalingsmodulen, og at riktig valutakurs er innstilt.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','Den forinstilte valutaen er ikke inkludert på listen over tillatte valuta. Logg inn på din admin panel, og sjekk at forinstilt valuta er på listen over tillatte valuta i betalingsmodulen.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Betaling misslyktes.');  

define('ERROR_CODE_100','Intern systemfeil for eksempel at databasene ligger nede, resursene ikke er tilgjengelige osv. ta kontakt med integratør');
define('ERROR_CODE_105','Ikke gyldig transaksjonsstatus');
define('ERROR_CODE_106','Feil hos tredje part for eksempel feil hos banken');
define('ERROR_CODE_107','Transaksjon nektet av banken');
define('ERROR_CODE_108','Transaksjon kansellert');
define('ERROR_CODE_109','Transaksjon ikke funnet hos banken');
define('ERROR_CODE_110','Ikke gyldig transaksjons ID');
define('ERROR_CODE_113','Betalingsmåten er ikke konfigurert for handleren');
define('ERROR_CODE_114','Timeout hos banken');
define('ERROR_CODE_121','Kortet har gått ut');
define('ERROR_CODE_124','Beløp overskreder limiten');
define('ERROR_CODE_143','Kreditt nektet av bank');

define('ERROR_CODE_DEFAULT', 'Feil i betalingsprosessen. Vennligst oppgi denne koden når du tar kontakt med support. Feil kode: ');
?>