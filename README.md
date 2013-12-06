# osCommerce - Svea WebPay payment module

These modules are only tested in OsCommerce 2.2 MS2, 2.2 RC2a and 2.3.1. 
Problems can occur with other versions of OsCommerce.

Copy all files in the src folder to the root directory for your installation. The folders should merge with the folders of the same name.

##Important info when using card and direct bank payments
The request made from this module to SVEAs systems is made through a redirected form. 
The response of the payment is then sent back to the module via POST or GET (selectable in our admin).

###When using GET
Have in mind that a long response string sent via GET could get cut off in some browsers and especially in some servers due to server limitations. 
Our recommendation to solve this is to check the PHP configuration of the server and set it to accept at LEAST 512 characters.


###When using POST
As our servers are using SSL certificates and when using POST to get the response from a payment the users browser propmts the user with a question whether to continue or not, if the receiving site does not have a certificate.
Would the customer then click cancel, the process does not continue.  This does not occur if your server holds a certicifate. To solve this we recommend that you purchase a SSL certificate from your provider.

We can recommend the following certificate providers:
* InfraSec:  infrasec.se
* VeriSign : verisign.com

## History

4.3.3 (131206) Better handling of SoapFault exceptions in invoice, part payment payment methods.