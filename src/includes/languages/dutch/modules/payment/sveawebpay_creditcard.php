<?php
/*
 * osCommerce 
 * Svea Card
 * NL 5.0
 */
define('MODULE_PAYMENT_SWPCREDITCARD_TEXT_TITLE','Svea Creditcard');
define('MODULE_PAYMENT_SWPCREDITCARD_TEXT_DESCRIPTION','Svea Creditcard - version 5.0');

define('ERROR_ALLOWED_CURRENCIES_NOT_DEFINED','Een of meer van de toegestane valutas zijn niet gedefinieerd. Dit moet zijn ingeschakeld om de Svea Creditcard te kunnen gebruiken. Log in op je admin panel, en zorg ervoor dat u al onze valuta als toegestaan in de betaalmodule bestaan, en dat de ze juiste wisselkoersen hebben.');
define('ERROR_DEFAULT_CURRENCY_NOT_ALLOWED','De standaard valuta is niet onder die opgenomen lijst als toegestaan. Log in op je admin panel, en zorg ervoor dat de standaard valuta in de toegestande lijst staat  van  de betaalmodule.');  
define('ERROR_MESSAGE_PAYMENT_FAILED','Betaling mislukt.');  

define('ERROR_CODE_100','Ongeldig. Neem contact op met de integrator');
define('ERROR_CODE_105','Ongeldige transactiestatus');
define('ERROR_CODE_106','Fout bij derde partij');
define('ERROR_CODE_107','Transactie afgewezen door de bank');
define('ERROR_CODE_108','Transactie geannulleerd');
define('ERROR_CODE_109','Transactie niet gevonden bij de bank');
define('ERROR_CODE_110','Ongeldige transactie Identificatie');
define('ERROR_CODE_113','Betalingsmethode niet geconfigureerd voor de handelaar');
define('ERROR_CODE_114','Timeout bij de bank');
define('ERROR_CODE_121','De geldigheidsdatum van de pas is verlopen');
define('ERROR_CODE_124','Bedrag dat de limiet overschrijdt');
define('ERROR_CODE_143','Krediet geweigerd door de bank');

define('ERROR_CODE_DEFAULT', 'Er is een fout opgetreden tijdens de verwerking van de betaling. Geef deze code wanneer u contact opneemt de klanteservice. Fout code: ');
?>
