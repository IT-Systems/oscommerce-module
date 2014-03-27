<?php
/*
 * osCommerce 
 * Svea Part Payment
 * NO 5.0
 */

define('MODULE_PAYMENT_SWPPARTPAY_TEXT_TITLE','Svea Delbetaling');
define('MODULE_PAYMENT_SWPPARTPAY_TEXT_DESCRIPTION','Svea Delbetaling - version 5.1.2');

define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','En eller fler av de tillatte valutaene er ikke definert. Dette må være aktivert for å kunne bruke Svea Delbetaling. Logg inn på din admin panel, og sjekk at de valuta som er brukt i betalingen er listet som tillatt i betalingsmodulen, og at riktig valutakurs er innstilt.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','Den forinstilte valutaen er ikke inkludert på listen over tillatte valuta. Logg inn på din admin panel, og sjekk at forinstilt valuta er på listen over tillatte valuta i betalingsmodulen.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Betalingen misslyktes.');  

//Eu error codes
define('ERROR_CODE_20000','Ordren er stengt');
define('ERROR_CODE_20001','Ordren nektes');
define('ERROR_CODE_20002','Noe er galt med ordren');
define('ERROR_CODE_20003','Ordren er utgått');
define('ERROR_CODE_20004','Ordren finnes ikke');
define('ERROR_CODE_20005','Ordre type passer ikke sammen');
define('ERROR_CODE_20006','Summen av alle ordrelinjer kan ikke være null eller negativt');
define('ERROR_CODE_20013','Ordren er på vent');

define('ERROR_CODE_27000','Den medfølgende kampanjekode beløpets kombinasjon samsvarer ikke med noen kampanje kode knyttet til denne klienten');
define('ERROR_CODE_27001','Kan ikke levere ordren siden den angitte pdf malen mangler, Kontakt Svea WebPay for mer informasjon');
define('ERROR_CODE_27002','Kan ikke delvis levere en betalingsplan');
define('ERROR_CODE_27003','Kan ikke blande en kampanjekode med ett fast månedlig beløp.');
define('ERROR_CODE_27004','Kan ikke finne en passende kampanjekode for det månedlige beløpet');

define('ERROR_CODE_30000','Kreditt rapporten ble avvist');
define('ERROR_CODE_30001','Kunden er blokkert eller har utvist uvanlig oppførsel');
define('ERROR_CODE_30002','Basert på den utførte kreditsjekk ble forespørselen avvist');
define('ERROR_CODE_30003','Kunden finnes ikke i kredittsjekk');

define('ERROR_CODE_40000','Ingen kunde funnet');
define('ERROR_CODE_40001','Det gitte postnummer støttes ikke');
define('ERROR_CODE_40002','Ugyldig kundeinformasjon');
define('ERROR_CODE_40004','Finner ingen adresse på denne kunden');

define('ERROR_CODE_50000','Klienten har ingen tillatelse for denne metoden');

define('DD_NO_CAMPAIGN_ON_AMOUNT','Kan ikke finne en passende kampanjekode for beløpet');

// used in payment credentials form
define('FORM_TEXT_PARTPAY_ADDRESS','Faktura adresse:');
define('FORM_TEXT_PAYMENT_OPTIONS','Betalingsalternativer:');

//define('FORM_TEXT_GET_PAY_OPTIONS','Hent betalingsalternativ');
define('FORM_TEXT_SS_NO','Fødselsnummer:');
define('FORM_TEXT_INITIALS','Initialer');    
define('FORM_TEXT_BIRTHDATE','Fødselsdato');              
define('FORM_TEXT_VATNO','Organisasjonsnummer'); 
define('FORM_TEXT_PARTPAY_FEE','Innledende avgift vil bli lagt til');
//define('FORM_TEXT_GET_PAYPLAN','Hent adresse');

define('ERROR_CODE_DEFAULT','Svea Error: ');
define('FORM_TEXT_GET_ADDRESS','Hent adresse');
?>