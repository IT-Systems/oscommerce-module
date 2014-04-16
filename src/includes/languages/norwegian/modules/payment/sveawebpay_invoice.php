<?php
/*
 * osCommerce 
 * Svea Invoice 
 * NO 5.0
*/
define('MODULE_PAYMENT_SWPINVOICE_TEXT_TITLE','Svea Faktura');
define('MODULE_PAYMENT_SWPINVOICE_TEXT_DESCRIPTION','Svea Faktura - version 5.1.2');
define('MODULE_PAYMENT_SWPINVOICE_HANDLING_APPLIES','En håndteringskostnad på % %s vil bli lagt til denne order ved checkout.');
define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','En eller fler av de tillatte valutaene er ikke definert. Dette må være aktivert for å kunne bruke SweaWebPay Hosted Solution. Logg inn på din admin panel, og sjekk at de valuta som er brukt i betalingen er listet som tillatt i betalingsmodulen, og at riktig valutakurs er innstilt.');
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

define('ERROR_CODE_30000','Kreditt rapporten ble avvist');
define('ERROR_CODE_30001','Kunden er blokkert eller har utvist uvanlig oppførsel');
define('ERROR_CODE_30002','Basert på den utførte kreditsjekk ble forespørselen avvist');
define('ERROR_CODE_30003','Kunden finnes ikke i kredittsjekk');

define('ERROR_CODE_40000','Ingen kunde funnet');
define('ERROR_CODE_40001','Det gitte postnummer støttes ikke');
define('ERROR_CODE_40002','Ugyldig kundeinformasjon');
define('ERROR_CODE_40004','Finner ingen adresse på denne kunden');

define('ERROR_CODE_50000','Klienten har ingen tillatelse for denne metoden');

//invoice specific
define('ERROR_CODE_24000','Fakturabeløpet overstiger det autoriserte beløpet');

// used in payment credentials form
define('FORM_TEXT_INVOICE_ADDRESS','Faktura adresse');
define('FORM_TEXT_INVOICE_FEE','Fakturagebyr:');
define('FORM_TEXT_COMPANY','Foretak');
define('FORM_TEXT_PRIVATE','Privat');
define('FORM_TEXT_GET_ADDRESS','Hent adresse');

define('FORM_TEXT_SS_NO','Fødselsnummer :');
define('FORM_TEXT_INITIALS','Initialer');                                
define('FORM_TEXT_BIRTHDATE','Fødselsdato ');              
define('FORM_TEXT_VATNO','Organisasjonsnummer');

define('ERROR_CODE_DEFAULT','Svea Error: ');

// Tupas API related messages
define('FORM_TEXT_TUPAS_AUTHENTICATE','Authenticate on online bank');
define('ERROR_TAMPERED_PARAMETERS', 'Unexpected error occurred during authentication. Please, try again.');
define('ERROR_TUPAS_NOT_SET', 'You have to authenticate yourself in online bank.');
define('ERROR_TUPAS_MISMATCH', 'The SSN doesn\'t match with the one that Tupas authentication sent. Please, try again.');
?>