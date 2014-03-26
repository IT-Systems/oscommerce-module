# osCommerce - Svea payment module
## Version 5.1.1
* Tested with osCommerce 3.3.3.4
* Requires PHP 5.3 or later (namespace support)

## Release history
For release history, see [**github release tags**](https://github.com/sveawebpay/oscommerce-module/releases)

## Introduction
This module supports Svea invoice and payment plan payments in Sweden, Finland, Norway, Denmark, Netherlands and Germany, as well as creditcard and direct bank payments from all countries. 

The module has been tested with osCommerce and pre-installed order total modules, including the Svea invoice fee module. The module has been updated to make use of the latest payment systems at Svea, and builds upon the included Svea php integration package.

As always, we strongly recommend that you have a test environment set up, and make a backup of your existing site, database and settings before upgrading.

If you experience technical issues with this module, or if you have feature suggestions, please submit an issue on the Github issue list.

#Installation instructions

## Upgrading from a previous version of the module

* From module version 4.3.x or earlier: The module has been rewritten from scratch. If you are upgrading from the previous version of this module, all payment methods should be uninstalled and then re-installed when upgrading (please make note of your previous configuration settings). This ensures that all settings are initialised correctly. 



##Basic installation example using the Svea Invoice payment method

The following example assumes that you have already downloaded and installed osCommerce as described in the [osCommerce documentation](http://library.oscommerce.com/).

This guide covers how to install the Svea osCommerce module and install various payment methods in your osCommerce shop, as well as the various localisation and other settings you need to make to ensure that the payment modules works properly.

### Install the Zen Cart Svea payment module files

* Download or clone the contents of [this repository from github](https://github.com/sveawebpay/oscommerce-module). Unless instructed otherwise by Svea support, we recommend that you use only the default master branch of the repository, which contain the latest stable and tested module release.

* Copy the contents of the src folder to your osCommerce root folder.

* Make sure to merge the files and folders from the module with the ones in your osCommerce installation, replacing any previously installed files with updated versions.

* This module depends on the Svea php integration package, which is included under the "ext/modules/payment/svea" folder. (There should be no need to upgrade the integration package separately from the osCommerce module, unless instructed to do so by Svea support.)

### Configure the payment modules in the osCommerce admin panel
In this example we'll first configure the Svea invoice payment method, instructions for other payment methods then follows below.

#### Svea Invoice configuration

* Log in to your osCommerce admin panel.

* Browse to _Modules -> Payment_ in the osCommerce admin left-hand menu. Any installed payment methods should then appear in a list. 

* Press the _Install Module_ button in the screen upper right to view the uninstalled payment methods. Locate the Svea payment method you wish to install.

* Select the payment method and press the _Install Module_ button in the right-hand pane. For now, select and install the Svea Invoice payment method.

* You will now see the choosen method in the list of installed payment methods. Select the _edit_ button to modify the payment method settings. 

![Invoice payment settings] (https://github.com/sveawebpay/oscommerce-module/raw/develop/docs/image/invoice_settings_1.PNG "Svea Invoice settings 1")

* _Enable Svea Invoice Module_: if set to false, the module is disabled and won't show up in the customer list of available payment methods on checkout.

* _Svea Username XX_, _Svea Password XX_ and _Svea Client no XX_: enter the username and password that corresponds to your client number for the country(XX) in question. You can only accept invoice payments from countries for which you have entered credentials, other country fields should be left empty. Test credentials will be provided to you by your Svea integration manager upon request. 

![Invoice payment settings] (https://github.com/sveawebpay/oscommerce-module/raw/develop/docs/image/invoice_settings_2.PNG "Svea Invoice settings 2")

* _Transaction mode_: Determines whether payments using this method go to Svea's test or production servers. Until you have been giving the go ahead by your Svea integration manager, this should be set to Test. Then, in order to receive payments for production orders, this setting should be switched over to Production.

* _Set Order Status_: The osCommerce order status given to orders after the customer has completed checkout.

* _Auto Deliver Order_: Order invoices will be delivered (sent out) to the customer by Svea if an order's status is set to this status. If Set Order Status (above) matches this setting the order will be autodelivered upon creation, thus avoiding the need to manually deliver the order in the Svea Admin interface. Ask your Svea integration manager if unsure about this setting.

* _Invoice Distribution Type_: If _Auto Deliver Order_ (above) is set to true, this setting must match the corresponding setting in Svea's admin interface. Ask your Svea integration manager if unsure.

* _Display SveaWebPay Images_: Set to true if you wish to display the Svea payment method logos during checkout payment method selection.

* _Ignore OT list_: if you experience problems with i.e. incompatible order total modules, the module name(s) may be entered here and will then be ignored by the invoice payment module when writing the request to Svea.

* _Payment Zone_: if a zone is selected here, invoice payments will only be accepted from within that zone. See "Localisation and additional osCommerce configuration requirements" below.

* _Sort order of display_: determines the order in which payment methods are presented to the customer on checkout. The method are listed in ascending order on the payment method selection page.

* Finally, remember to _save_ your settings.

Also, make sure you have defined all relevant currencies for countries you accept invoice payments from. See "Localisation and additional osCommerce configuration requirements" below.
 
#### Setting up the Svea Invoice handling fee order total module
The Svea Invoice handling fee order total module is used to add an invoice fee to the order total when the Svea Invoice payment method is selected during checkout.

* Browse to _Modules -> Order Total_ in the osCommerce admin left-hand menu. Install the Svea Invoice handling fee module, following the same procedure as detailed for the Svea Invoice payment method above.

* Select _Svea Invoice handling fee_ from the list of installed order total modules and select the _edit_ button:

![Invoice fee settings] (https://github.com/sveawebpay/oscommerce-module/raw/develop/docs/image/invoice_fee_settings.PNG "Invoice fee settings")

* _Enable Svea Invoice Fee_: If set to false, no invoice fee will be applied to invoice payments in any country. (If you wish to temporarily disable a single country invoice fee, set its fee entry to 0 below and it will not show up in the order total.)

* _Sort order_ determines where in the order total stack the invoice fee will be displayed upon checkout. See recommendations under "Order Total settings" below.

The invoice fee and tax class need to be specified for each country from which you accept invoice payments. (Note that you also need to have the invoice payment method set up to accept customers from these countries. Please contact your Svea integration manager if you have further questions.

* _Fee_: Specify the amount excluding tax in the respective country currency. Note that the invoice fee always should be specified excluding tax and in the country currency, not the shop default currency. Also, make sure to use the correct decimal point notation, i.e. a dot (.) when specifying the fee.

* _Tax class_: Select the tax class that will be applied to the invoice fee.

* Finally, remember to _save_ your settings.

### Other payment methods
For the other Svea payment methods (payment plan, card payment and direct bank payment), see below.

#### Svea Payment Plan configuration

* Browse to _Modules -> Payment_ in the osCommerce admin left-hand menu. Install the Svea Payment Plan module, following the same procedure as detailed for the Svea Invoice payment method above.

* Select and then _edit_ the _Svea Payment Plan module_ from the list of installed modules:

* _Enable Svea Payment Plan Module_: if set to false, the module is disabled and won't show up in the customer list of available payment methods on checkout.

* _Svea Username XX_, _Svea Password XX_ and _Svea Client no XX_: enter the username and password that corresponds to your client number for the country(XX) in question. You can only accept payment plan payments from countries for which you have entered credentials, other country fields should be left empty. Test credentials will be provided to you by your Svea integration manager upon request. 

* _Min amount for <Country> in <Currency>_ and _Max amount for <Country> in <Currency>_: The minimum and maximum amount for the various campaigns. Use the minimum and maximum value over the set of all active campaigns. Ask your Svea integration manager if unsure.

* _Transaction mode_: Determines whether payments using this method go to Svea's test or production servers. Until you have been giving the go ahead by Svea, this should be set to Test. Then, in order to receive payments for production orders, this should be switched over to its Production setting.

* _Set Order Status_: The osCommerce order status given to orders after the customer has completed checkout.

* _Auto Deliver Order_: Order payment plan will be delivered (sent out) to the customer by Svea if an order's status is set to this status. If Set Order Status (above) matches this setting the order will be autodelivered upon creation, thus avoiding the need to manually deliver the order in the Svea Admin interface. Ask your Svea integration manager if unsure about this setting.

* _Ignore OT list_: if you experience problems with i.e. incompatible order total modules, the module name(s) may be entered here and will then be ignored by the invoice payment module.

* _Payment Zone_: if a zone is selected here, invoice payments will only be accepted from within that zone. See "Localisation and additional Zen Cart configuration requirements" below.

* _Sort order of display_: determines the order in which payment methods are presented to the customer on checkout. The method are listed in ascending order on the payment method selection page.

* Finally, remember to _save_ your settings.

Also, make sure you have defined all relevant currencies for countries you accept payment plan payments from. See "Localisation and additional osCommerce configuration requirements" below.

#### Svea Card configuration

* Browse to _Modules -> Payment_ in the osCommerce admin left-hand menu. Install the Svea Card module, following the same procedure as detailed for the Svea Invoice payment method above.

* Select and then _edit_ the _Svea Card module_ from the list of installed modules:

* _Enable Svea Card Payment Module_: if set to false, the module is disabled and won't show up in the customer list of available payment methods on checkout

* _Svea Card Merchant ID_ and _Svea Card Secret Word_: enter your provided merchant ID and secret word. These are provided to you by your Svea integration manager.

* _Svea Card Test Merchant ID_ and _Svea Card Test Secret Word_: enter your provided test merchant ID and secret word. Test credentials will be provided to you by Svea upon request.

* _Transaction mode_: Determines whether payments using this method go to Svea's test or production servers. Until you have been giving the go ahead by Svea, this should be set to Test. Then, in order to receive payments for production orders, this should be switched over to its Production setting.

* _Set Order Status_: The osCommerce order status given to orders after the customer has completed checkout.

* _Ignore OT list_: if you experience problems with i.e. incompatible order total modules, the module name(s) may be entered here and will then be ignored by the invoice payment module.

* _Payment Zone_: if a zone is selected here, invoice payments will only be accepted from within that zone. See "Localisation and additional Zen Cart configuration requirements" below.

* _Sort order of display_: determines the order in which payment methods are presented to the customer on checkout. The method are listed in ascending order on the payment method selection page.

* Finally, remember to _save_ your settings.

#### Svea Direct Bank configuration
* Browse to _Modules -> Payment_ in the osCommerce admin left-hand menu. Install the Svea Direct Bank module, following the same procedure as detailed for the Svea Invoice payment method above.

* Select and then _edit_ the _Svea Direct Bank module_ from the list of installed modules:

* _Enable Svea Direct Bank Payment Module_: if set to false, the module is disabled and won't show up in the customer list of available payment methods on checkout

* _Svea Direct Bank Merchant ID_ and _Svea Direct Bank Secret Word_: enter your provided merchant ID and secret word. These are provided to you by your Svea integration manager. 

* _Svea Direct Bank Test Merchant ID_ and _Svea Direct Bank Test Secret Word_: enter your provided test merchant ID and secret word. Test credentials will be provided to you by Svea upon request.

* _Transaction mode_: Determines whether payments using this method go to Svea's test or production servers. Until you have been giving the go ahead by Svea, this should be set to Test. Then, in order to receive payments for production orders, this should be switched over to its Production setting.

* _Set Order Status_: The osCommerce order status given to orders after the customer has completed checkout.

* _Display SveaWebPay Images_: Set to true if you wish to display the available bank logos during checkout payment method selection.

* _Ignore OT list_: if you experience problems with i.e. incompatible order total modules, the module name(s) may be entered here and will then be ignored by the invoice payment module.

* _Payment Zone_: if a zone is selected here, invoice payments will only be accepted from within that zone. See "Localisation and additional osCommerce configuration requirements" below.

* _Sort order of display_: determines the order in which payment methods are presented to the customer on checkout. The method are listed in ascending order on the payment method selection page.

* Finally, remember to _save_ your settings.

##Localisation and additional osCommerce configuration requirements

### Country specific requirements
* In NL and GE stores, the postal code needs to be set to required for customer registrations. It is used by the invoice and payment plan modules for credit check information et al.

### Currencies settings
* Under _Localisation -> Currencies_, all currencies used in countries where you accept invoice or payment plan payments must be defined or the customer will see warning message and your module will not work. The following is a list of countries and their respective currencies: SE (SEK), DK (DKK), NO (NOK), FI (EUR), DE (EUR), NL (EUR).

* Under _Localisation -> Currencies_, the _Decimal Places_ setting should be set to two (2) for _Euro_.

### Order Total settings
* The recommended order total modules sort order is: sub-total (lowest), svea invoice fee, shipping, coupon, taxes, store credit, voucher and total.

##Troubleshooting and recommendations
Always check that you have set up your osCommerce settings correctly before posting issues or contacting Svea support. Specifically, the following settings must all be in place for the payment modules to work correctly in the various countries:

### Check your Svea customer credentials
* Your _username, password_, and _client no_ for Invoice and Part Payment, and the country in question, are set to the correct values.

* Your _secret word_ and _merchant id_ for Card and Direct bank payments are correct.

### Check correlated osCommerce settings and localisations
* Under _Locations/Taxes_ and _Localisation_, the correlating _Tax classes, Tax rates_, _Currencies_, _Zone_ and _Zone Definitions_ settings are correct.

* Under _Modules -> Order Totals_, double check that the sort order et al is correct.

* You are using test case credentials when conducting test purchases.

### General FAQ

(intentionally left blank)