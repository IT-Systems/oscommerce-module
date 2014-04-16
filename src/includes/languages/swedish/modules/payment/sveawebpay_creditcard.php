<?php
/*
 * osCommerce 
 * Svea Card
 * SE 5.0
 */
define('MODULE_PAYMENT_SWPCREDITCARD_TEXT_TITLE','Svea Kort');
define('MODULE_PAYMENT_SWPCREDITCARD_TEXT_DESCRIPTION','Svea Kort - version 5.1.2');

define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','En eller flera av de tillåtna valutorna är ej definierade. Dessa måste vara definierade för att kunna använda Svea Kort. Logga in till din admin-panel, och säkerställ att alla de tillåtna valutorna i payment-modulen existerar, och att de korrekta växelkursera är satta.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','Standardvalutan är ej med i listan av de tillåtna. Logga in till your admin-panel, och säkerställ att standardvalutan finns med bland de tillåtna i payment-modulen.');
define('ERROR_MESSAGE_PAYMENT_FAILED','Betalningen misslyckades.');  

define('ERROR_CODE_100','Internt systemfel');
define('ERROR_CODE_105','Felaktig transaktionsstatus');
define('ERROR_CODE_106','Fel hos tredjepart ex hos bank');
define('ERROR_CODE_107','Transaktion avvisad hos bank');
define('ERROR_CODE_108','Transaktion avbruten');
define('ERROR_CODE_109','Transaktion ej hittad hos bank');
define('ERROR_CODE_110','Ogiltigt transaktionsid');
define('ERROR_CODE_113','Betalmetod ej konfigurerad för denna butik');
define('ERROR_CODE_114','Timeout hos bank');
define('ERROR_CODE_121','Utgångsdatum för kort passerat');
define('ERROR_CODE_124','Belppet överstiger maxgränsen för köp');
define('ERROR_CODE_143','Kredit avvisad av bank');

define('ERROR_CODE_DEFAULT', 'Ett okänt fel har uppstått vid behandling av din betalning. Var vänlig uppge denna kod när du kontaktar supporten. Felkod: ');
?>