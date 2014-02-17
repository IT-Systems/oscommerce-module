<?php
/*
 * osCommerce 
 * Svea Part Payment
 * FI 5.0
 */

define('MODULE_PAYMENT_SWPPARTPAY_TEXT_TITLE','Svea Osamaksu');
define('MODULE_PAYMENT_SWPPARTPAY_TEXT_DESCRIPTION','Svea osamaksu - versio 4.0');

define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','Yksi tai useampi sallituista valuutoista ei ole määritelty. Nämä on määriteltävä käyttämään Svea Osamaksu. Kirjaudu admin paneeliin ja varmista että.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','Standardivaluuta ei ole sallitussa luettelossa. Kirjaudu admin paneeliin ja varmista että oletusvaluutta kuuluu niihin sallituihin maksu-moduulissa.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Maksu epäonnistui.');  

//Eu error codes
define('ERROR_CODE_20000','Tilaus suljettu ');
define('ERROR_CODE_20001','Tilaus hylätty ');
define('ERROR_CODE_20002','Virhe tilauksessa ');
define('ERROR_CODE_20003','Tilaus vanhentunut ');
define('ERROR_CODE_20004','Tilausta ei löydy ');
define('ERROR_CODE_20005','Tilaustyyppi virheellinen ');
define('ERROR_CODE_20006','Kaikkien tilausten rivit eivät voi olla summaltaan nolla tai negatiivisia ');
define('ERROR_CODE_20013','Avoinna oleva tilaus ');

define('ERROR_CODE_27000','Kampanjakoodi-summa yhdistelmä ei vastaa yhtäkään tämän asiakkaan');
define('ERROR_CODE_27001','Tilausta ei voi toimittaa, koska määritelty pdf lomake puuttuu. Ottakaa yhteyttä SveaWebPayn tukeen');
define('ERROR_CODE_27002','Osamaksusuunnitelmalle ei voi suorittaa osatoimitusta ');
define('ERROR_CODE_27003','Kampanjakoodia ei voi yhdistää kiinteään kuukausierään. ');
define('ERROR_CODE_27004','Sopivaa kampanjakoodia ei löydy kuukausierälle ');

define('ERROR_CODE_30000','Luotto hylätty');
define('ERROR_CODE_30001','Asiakas on estetty tai on esittänyt outoa tai epätavallista käyttäytymistä');
define('ERROR_CODE_30002','Luottotietojen tarkistukseen perustuen, pyyntö hylättiin');
define('ERROR_CODE_30003','Asiakasta ei löydy luottorekisteristä');

define('ERROR_CODE_40000','Asiakasta ei löydy');
define('ERROR_CODE_40001','Maatunnusta ei tueta');
define('ERROR_CODE_40002','Väärä asiakastieto');
define('ERROR_CODE_40004','Tälle asiakkaalle ei löydy osoitetta');

define('ERROR_CODE_50000','Asiakkaalla ei ole oikeutta käyttää tätä menetelmää.');

define('DD_NO_CAMPAIGN_ON_AMOUNT','Sopivaa kampanjakoodia ei löydy annetulle summalle.');

// used in payment credentials form
define('FORM_TEXT_PARTPAY_ADDRESS','Laskutusosoite:');
define('FORM_TEXT_PAYMENT_OPTIONS','Maksuvaihtoehdot:');

//define('FORM_TEXT_GET_PAY_OPTIONS','Maksuvaihtoehdot');
define('FORM_TEXT_SS_NO','Y-tunnus:');
define('FORM_TEXT_INITIALS','Tunnukset');                                
define('FORM_TEXT_BIRTHDATE','Syntymäaika');               
define('FORM_TEXT_VATNO','ALV'); 
define('FORM_TEXT_PARTPAY_FEE','Perustamiskulu lisätään');
define('FORM_TEXT_GET_PAYPLAN','Hae osoite');

define('ERROR_CODE_DEFAULT','Svea Error: ');

define('FORM_TEXT_GET_ADDRESS','Hae osoite ja maskuvaihtoehdot');
?>