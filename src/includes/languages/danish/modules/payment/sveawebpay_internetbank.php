<?php
/*
 * osCommerce 
 * Svea Internetbank
 * DK 5.0
 */

define('MODULE_PAYMENT_SWPINTERNETBANK_TEXT_TITLE','Svea Direktbank');
define('MODULE_PAYMENT_SWPINTERNETBANK_TEXT_DESCRIPTION','Svea Direktbank - version 5.0');

define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','En eller flere af de tilladte valutaer er ikke præciserede. Dette skal ske, førend Svea Direktbank kan benyttes. Login på dit admin panel og tjek, at alle valutaer er listede som tilladte i betalingsmodulet, og at de rigtige vekselkurser er indstillede korrekt.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','Standardvalutaen er ikke blandt de listede som tilladte. Login i dit admin panel og tjek, at standardvalutaen er på listen over tilladte valutaer i betalingsmodulet.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Betaling mislykkedes.');  

define('ERROR_CODE_100','Indre systemfejl såsom at databaser er nede, ressourcer er ikke tilgængelige etc. kontakt integrator');
define('ERROR_CODE_105','Ugyldig transaktionsstatus');
define('ERROR_CODE_106','Fejl ved tredjepart f.eks. ved banken');
define('ERROR_CODE_107','Transaktion afvist af bank');
define('ERROR_CODE_108','Transaktion afbrudt');
define('ERROR_CODE_109','Transaktion ikke fundet ved banken');
define('ERROR_CODE_110','Ugyldigt transaktions-id');
define('ERROR_CODE_113','Betalingsmetode ikke konfigureret til forhandleren');
define('ERROR_CODE_114','Timeout hos banken');
define('ERROR_CODE_121','Kortet er udløbet');
define('ERROR_CODE_124','Beløbet overstiger kreditlimit');
define('ERROR_CODE_143','Kredit afslået af banken');

define('ERROR_CODE_DEFAULT', 'Fejl i behandling af betalingen. Oplys venligst kode ved henvendelse kundeservice. Fejlkode: ');
?>