<?php
/*
 * osCommerce 
 * Svea Part Payment
 * SE 5.0
 */

define('MODULE_PAYMENT_SWPINVOICE_TEXT_TITLE','Svea Delbetalning');
define('MODULE_PAYMENT_SWPINVOICE_TEXT_DESCRIPTION','Svea Delbetalning - version 5.0');
define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','En eller flera av de tillåtna valutorna är ej definierade. Dessa måste vara definierade för att kunna använda Svea Delbetalning. Logga in till din admin-panel, och säkerställ att alla de tillåtna valutorna i payment-modulen existerar, och att de korrekta växelkursera är satta.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','Standardvalutan är ej med i listan av de tillåtna. Logga in till your admin-panel, och säkerställ att standardvalutan finns med bland de tillåtna i payment-modulen.');
define('ERROR_MESSAGE_PAYMENT_FAILED','Betalningen misslyckades.');  

//Eu error codes
define('ERROR_CODE_20000','Ordern är stängd');
define('ERROR_CODE_20001','Ordern är nekad');
define('ERROR_CODE_20002','Något gick fel med ordern');
define('ERROR_CODE_20003','Ordern har gått ut');
define('ERROR_CODE_20004','Ordern existerar ej');
define('ERROR_CODE_20005','Ordertyperna matchar ej');
define('ERROR_CODE_20006','Summan av orderraderna kan ej vara noll eller negativ');
define('ERROR_CODE_20013','Ordern väntar');

define('ERROR_CODE_27000','Delbetalningssumman matchar ej någon kampanj som är knuten till kontot.');
define('ERROR_CODE_27001','Det går ej att leverera order för en pdf saknas. Kontakta SveaWebPay’s support');
define('ERROR_CODE_27002','Det går ej att delleverera en delbetalning');
define('ERROR_CODE_27003','Det går ej att kombinera CampaignCode med en fast summa.');
define('ERROR_CODE_27004','Det går ej att hitta en passande kampanjkod för den angivna summan');

define('ERROR_CODE_30000','Kreditupplysningen avslogs');
define('ERROR_CODE_30001','Kunden är blockerad eller har uppvisat udda köpbeteende.');
define('ERROR_CODE_30002','Ordern nekades baserat på kreditupplysningen.');
define('ERROR_CODE_30003','Det går ej att hitta kunden i kreditupplysningen.');

define('ERROR_CODE_40000','Det går ej att hitta kunden');
define('ERROR_CODE_40001','Landskoden stöds ej');
define('ERROR_CODE_40002','Ogiltiga kunduppgifter');
define('ERROR_CODE_40004','Det gick ej att hitta några adresser för den här kunden');

define('ERROR_CODE_50000','Kunden är ej godkänd för denna metod');

define('DD_NO_CAMPAIGN_ON_AMOUNT','Det finns ej någon kampanj som överensstämmer med summan.');

// used in payment credentials form
define('FORM_TEXT_PARTPAY_ADDRESS','Fakturaadress:');
define('FORM_TEXT_PAYMENT_OPTIONS','Betalningsalternativ:');

//define('FORM_TEXT_GET_PAY_OPTIONS','Hämta kampanjer');
define('FORM_TEXT_SS_NO','Personnummer:');
define('FORM_TEXT_INITIALS','Initialer');
define('FORM_TEXT_BIRTHDATE','Födelsedatum');              
define('FORM_TEXT_VATNO','Organisationsnummer');
define('FORM_TEXT_PARTPAY_FEE','uppläggningsavgift tillkommer ');
//define('FORM_TEXT_GET_PAYPLAN','Hämta adress:');

define('ERROR_CODE_DEFAULT','Svea Error: ');

define('FORM_TEXT_GET_ADDRESS','Hämta adress');
?>