# Changelog for Svea WebPay payment module

## v 4.3b
+ Fixed more encoding issues 
+ Fixed handling fee bugs

## v 4.3a
+  Fixed encoding issues
+  Added jQuery include for partpay too
+  Added separate texts for partpay and invoice get address button

## v 4.3
+  Fixed bugs

## v 4.0
+  Added webservice for invoice and partpayment
+  Added connection to new card and direct bank payments via Certitrade

## v 3.03
+  Added Finnish language files
+  Fixed a bug occurring when tax rate is set to 0

##v 3.02 
+  Fixed handling fee being calculated incorrectly when set to a percentage
+  Fixed a minor currency conversion bug

## v 3.01
+  Added encoding parameter

## v 3.0
### Changes and additions

*  Changed HTTP method to POST
+  Added support for MS2 and 2.3
+  Added better handling of customer addresses returned from hosted
*  Allows for parameters in return urls to fix certain issues
*  Changed logos

### Fixes

No longer receives error messages from incorrect configuration if modules are disabled

## v 2.5

### Changes and additions

*  Major clean-up and restructuring of code
-  Removed handling of localization, as it is handled in the hosted integration already (and it was bugged)
*  Removed checkboxes for selecting supported currencies, as they complicated installation
+  Added optional images
+  Added ignore list for order total codes to increase compatibility with other order total modules

### Fixes

*  Fixed currency conversion
*  Fixed sending correct currency code
*  Fixed a bug where the handling fee would not be displayed right after changing billing adress
*  Fixed handling fee as percentage calculation
*  Fixed setting billing adress from return GET from hosted solution in case invoice or partpayment was used

## Credits

Original module developed by Botweb - http://www.botweb.se
Development paid for by SveaWebPay - http://www.sveawebpay.se

As of version 2.5, development is handled by SveaWebPay
