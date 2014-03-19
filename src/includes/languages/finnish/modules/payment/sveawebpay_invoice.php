<?php
/*
 * osCommerce 
 * Svea Invoice 
 * FI 5.0
*/
define('MODULE_PAYMENT_SWPINVOICE_TEXT_TITLE','Svea Lasku');
define('MODULE_PAYMENT_SWPINVOICE_TEXT_DESCRIPTION','Svea Lasku - versio 5.0');
define('MODULE_PAYMENT_SWPINVOICE_HANDLING_APPLIES','Tilaukseen lisätään %s %s palvelumaksusta');
define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','Yksi tai useampi sallituista valuutoista ei ole määritelty. Nämä on määriteltävä käyttämään SveaWebPay hosted solutionia. Kirjaudu admin paneeliin ja varmista että.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','Standardivaluuta ei ole sallitussa luettelossa. Kirjaudu admin paneeliin ja varmista että oletusvaluutta kuuluu niihin sallituihin maksu-moduulissa.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Maksu epäonnistui');  

//Eu error codes
define('ERROR_CODE_20000','Tilaus suljettu ');
define('ERROR_CODE_20001','Tilaus hylätty ');
define('ERROR_CODE_20002','Virhe tilauksessa ');
define('ERROR_CODE_20003','Tilaus vanhentunut ');
define('ERROR_CODE_20004','Tilausta ei löydy ');
define('ERROR_CODE_20005','Tilaustyyppi virheellinen ');
define('ERROR_CODE_20006','Kaikkien tilausten rivit eivät voi olla summaltaan nolla tai negatiivisia ');
define('ERROR_CODE_20013','Avoinna oleva tilaus ');

define('ERROR_CODE_30000','Luotto hylätty');
define('ERROR_CODE_30001','Asiakas on estetty tai on esittänyt outoa tai epätavallista käyttäytymistä');
define('ERROR_CODE_30002','Luottotietojen tarkistukseen perustuen, pyyntö hylättiin');
define('ERROR_CODE_30003','Asiakasta ei löydy luottorekisteristä');

define('ERROR_CODE_40000','Asiakasta ei löydy');
define('ERROR_CODE_40001','Maatunnusta ei tueta');
define('ERROR_CODE_40002','Väärä asiakastieto');
define('ERROR_CODE_40004','Tälle asiakkaalle ei löydy osoitetta');

define('ERROR_CODE_50000','Asiakkaalla ei ole oikeutta käyttää tätä menetelmää.');

//invoice specific
define('ERROR_CODE_24000','Laskun summa ylittää valtuutetun summan');

// used in payment credentials form
define('FORM_TEXT_INVOICE_ADDRESS','Laskutusosoite');
define('FORM_TEXT_INVOICE_FEE','Laskutus maksu:');
define('FORM_TEXT_COMPANY','Liiketoiminta');
define('FORM_TEXT_PRIVATE','Yksityinen');
define('FORM_TEXT_GET_ADDRESS','Hae osoite');

define('FORM_TEXT_SS_NO','Henkilötunnus:');
define('FORM_TEXT_INITIALS','Tunnukset');                                
define('FORM_TEXT_BIRTHDATE','Syntymäaika');               
define('FORM_TEXT_VATNO','ALV'); 

define('ERROR_CODE_DEFAULT','Svea Error: ');

// Tupas specific translations
define('FORM_TEXT_TUPAS_AUTHENTICATE', 'Tunnistaudu verkkopankissa');
define('ERROR_TAMPERED_PARAMETERS', 'Tunnistautumistapahtumassa on tapahtunut odottamaton virhe. Yritä uudelleen.');
define('ERROR_TUPAS_NOT_SET', 'Sinun täytyy ensin tunnistautua verkkopankissa jatkaaksesi.');
define('ERROR_TUPAS_MISMATCH', 'Henkilötunnus ei vastaa Tupas -palvelusta saatua. Yritä uudelleen, ja tunnistaudu toistamiseen, jos virhe toistuu.');
?>