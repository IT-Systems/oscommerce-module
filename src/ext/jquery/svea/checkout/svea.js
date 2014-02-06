/**
 * SVEAWEBPAY PAYMENT MODULE FOR osCommerce
 * javascript used to pass Svea WebPay payment method credentials et al
 */
var isReady; //global, undefined on first file inclusion

jQuery(document).ready(function (){

    if( !isReady ) { isReady = true;  // as svea.js is included w/both payplan & invoice, internetbank, only hook functions once.

    // store customerCountry as attribute in payment method selection radiobutton
    jQuery.ajax({
        type: "POST",
        url: "sveaAjax.php",
        data:
        {
            SveaAjaxGetCustomerCountry: true
        }
    }).done( function( msg ) {
        jQuery('#bodyContent').attr("sveaCustomerCountry",msg);
    });

    // first, uncheck all payment buttons
    jQuery("input[type=radio][name='payment']").prop('checked', false);

    // show fields depending on payment method selected
    jQuery("input[type=radio][name='payment']").click( function() {

        var checked_payment = jQuery("input:radio[name=payment]:checked").val();
        switch( checked_payment ) {

            // Svea invoice payment method selected
            case 'sveawebpay_invoice':

                // get customerCountry
                var customerCountry = jQuery('#bodyContent').attr("sveaCustomerCountry");

                // hide getAddress button
                if( customerCountry == "NO" ) {       // hide getAddress button for NO (default)
                    jQuery('#sveaSubmitGetAddress').hide();
                }

//                // hide addresses
//                hideBillingAndInvoiceAddress( customerCountry );

                // show input fields
                jQuery('#sveaInternetbankField').hide();
                jQuery('#sveaPartPayField').hide();
                jQuery('#sveaInvoiceField').show();

                // do getAddresses on button press
                jQuery("#sveaSubmitGetAddress").click( function(){
                    getAddresses(   jQuery('#sveaSSN').val(),
                                    "invoice",
                                    jQuery('#sveaInvoiceField input[type="radio"]:checked').val(),
                                    jQuery('#bodyContent').attr("sveaCustomerCountry"),
                                    "sveaAddressSelector"
                    );
                });

                // set zencart billing/shipping to match getAddresses selection
                jQuery('#sveaAddressSelector').change( function() {
                    jQuery.ajax({
                        type: "POST",
                        url: "sveaAjax.php",
                        data: {
                            SveaAjaxSetCustomerInvoiceAddress: true,
                            SveaAjaxAddressSelectorValue: jQuery('#sveaAddressSelector').val()
                        },
                        success: function(msg) { msg; }
                    });
                });
            break; //case 'sveawebpay_invoice':

            //
            // Svea partpay payment method selected
            case 'sveawebpay_partpay':

                // get customerCountry
                var customerCountry = jQuery('#bodyContent').attr("sveaCustomerCountry");

//                // hide addresses
//                hideBillingAndInvoiceAddress( customerCountry );

                // show input fields
                jQuery('#sveaInternetbankField').hide();
                jQuery('#sveaPartPayField').show();
                jQuery('#sveaInvoiceField').hide();

                // force getAddresses & show part payment options on ssn input
                jQuery("#sveaSubmitPaymentOptions").click( function(){
                    getAddresses(   jQuery('#sveaSSNPP').val(),
                                    "paymentplan",
                                    "false", // partpay not available to companies
                                    jQuery('#bodyContent').attr("sveaCustomerCountry"),
                                    "sveaAddressSelectorPP"
                    );
                });                
                
                // get & show getPaymentOptions
                getPartPaymentOptions( customerCountry );

                // set zencart billing/shipping to match getAddresses selection
                jQuery('#sveaAddressSelectorPP').change( function() {
                    jQuery.ajax({
                        type: "POST",
                        url: "sveaAjax.php",
                        data: {
                            SveaAjaxSetCustomerInvoiceAddress: true,
                            SveaAjaxAddressSelectorValue: jQuery('#sveaAddressSelectorPP').val()
                        },
                        success: function(msg) { msg; }
                    });
                });
            break; //case 'sveawebpay_partpay':

            //
            // Svea internetbank payment method selected
            case 'sveawebpay_internetbank':

                // get customerCountry
                var customerCountry = jQuery('#bodyContent').attr("sveaCustomerCountry");

                // show input fields
                jQuery('#sveaInternetbankField').show();
                jQuery('#sveaPartPayField').hide();
                jQuery('#sveaInvoiceField').hide();

                // get & show getBankPaymentOptions
                getBankPaymentOptions( customerCountry );

            break; //case 'sveawebpay_internetbank':

            //If other payment methods are selected, hide all svea related
            default:

//                // show billing address if hidden
//                showBillingAndInvoiceAddress();

                // hide svea payment methods
                jQuery('#sveaInternetbankField').hide();
                jQuery('#sveaInvoiceField').hide();
                jQuery('#sveaPartPayField').hide();
            break; //default:
        }
    });

    // show/hide private/company input fields depending on country
    jQuery("input[type=radio][name='sveaIsCompany'][value='false']").click( function() {    // show private
        jQuery('#sveaInitials_div').show();
        jQuery('#sveaBirthDate_div').show();
        jQuery('#sveaVatNo_div').hide();
                
        if( jQuery('#bodyContent').attr("sveaCustomerCountry") == "NO" ) {       // hide getAddress button
            jQuery('#sveaSubmitGetAddress').hide();
        }
    });

    jQuery("input[type=radio][name='sveaIsCompany'][value='true']").click( function() {     // company
        jQuery('#sveaInitials_div').hide();
        jQuery('#sveaBirthDate_div').hide();
        jQuery('#sveaVatNo_div').show();
                
        if( jQuery('#bodyContent').attr("sveaCustomerCountry") == "NO" ) {       // show getAddress button
            jQuery('#sveaSubmitGetAddress').show();
        }
        
    });

    } // isReady
});

//// hide billing, invoice address fields in getAddress countries
//function hideBillingAndInvoiceAddress( country ) {
//    if( (country === 'SE') ||
//        (country === 'NO') ||
//        (country === 'DK') )
//    {
//        jQuery('#checkoutPaymentHeadingAddress').hide();
//        jQuery('#checkoutBillto').hide();
//        jQuery('#checkoutPayment .floatingBox').hide();
//    }
//}

//// show billing address if currently hidden
//function showBillingAndInvoiceAddress() {
//    jQuery('#checkoutPaymentHeadingAddress').show();
//    jQuery('#checkoutBillto').show();
//    jQuery('#checkoutPayment .floatingBox').show();
//}

//
// new getAddresses() that uses the integration package
function getAddresses( ssn, paymentType, isCompany, countryCode, addressSelectorName ) {

    // Show loader
    jQuery('#sveaSSN').after('<img src="images/svea_indicator.gif" id="SveaInvoiceLoader" />');

    // Do getAddresses call
    jQuery.ajax({
        type: "POST",
        url: "sveaAjax.php",
        data: { SveaAjaxGetAddresses: true,
                sveaSSN: ssn,
                paymentType: paymentType,
                sveaIsCompany: isCompany,
                sveaCountryCode: countryCode
        },

        success: function(msg){
            jQuery('#SveaInvoiceLoader').remove();
            jQuery( "#" + addressSelectorName ).empty();
            jQuery( "#" + addressSelectorName ).append(msg);
            jQuery( 'label[for="' + addressSelectorName + '"]' ).show();
            jQuery( "#" + addressSelectorName ).show();

            // update billing/shipping addresses in db for display on checkout_confirmation page
            jQuery.ajax({
                type: "POST",
                url: "sveaAjax.php",
                data: {
                    SveaAjaxSetCustomerInvoiceAddress: true,
                    SveaAjaxAddressSelectorValue: jQuery( "#" + addressSelectorName ).val()
                },
                success: function(msg) { msg; }
           });
        }
    });
}


function getPartPaymentOptions( countryCode ) {

    jQuery.ajax({
        type: "POST",
        url: "sveaAjax.php",
        data: {
            SveaAjaxGetPartPaymentOptions: true,
            sveaCountryCode: countryCode
        },
        success: function(msg){
            jQuery( "#sveaPaymentOptionsPP" ).empty();
            jQuery( "#sveaPaymentOptionsPP" ).append(msg);
            jQuery( 'label[for="sveaPaymentOptionsPP"]' ).show();
            jQuery( "#sveaPaymentOptionsPP" ).show();
        }
    });
}

function getBankPaymentOptions( countryCode ) {

    // getBankPaymentOptions to display as radio buttons, w/preselected default
    jQuery.ajax({
        type: "POST",
        url: "sveaAjax.php",
        data: {
            SveaAjaxGetBankPaymentOptions: true,
            sveaAjaxCountryCode : countryCode
        },
        success: function( msg ){
            jQuery( "#sveaBankPaymentOptions" ).empty();
            jQuery( "#sveaBankPaymentOptions" ).append(msg);
            jQuery( "#sveaBankPaymentOptions" ).show();
        }
    });
}


// OLD osC
//
////jQuery(document).ready(function (){
//    
//    //Function to get which payment method selected
//    jQuery("input[type=radio][name='payment']").click( function() {
//    
//    var checked_payment = jQuery("input:radio[name=payment]:checked").val();
//    
//    //If Svea invoice is selected
//    if (checked_payment == 'sveawebpay_invoice'){
//        jQuery('#sveaDelbetField').hide();
//        jQuery('#sveaFaktField').show();
//        //jQuery('button[type=submit]').attr('disabled','true');
//
//    //If Svea Part payment
//    }else if (checked_payment == 'sveawebpay_partpay'){    
//        jQuery('#sveaFaktField').hide();
//        jQuery('#sveaDelbetField').show();
//        //jQuery('button[type=submit]').attr('disabled','true');
//
//        
//    //If other payment methods are selected, hide all svea related    
//    }else{
//        jQuery('#sveaFaktField').hide();
//        jQuery('#sveaDelbetField').hide();
//    }
//    
//    });
//});
//
////Get adress, invoice
//function getAdress(){
// 
//    var sveaPnr = jQuery('#sveaPnr').val();
//    var company = jQuery('#sveaIsCompany').val();
//
//    if (sveaPnr == ''){
//        jQuery('#pers_nr_error_fakt').html('Personnr/Orgnr måste fyllas i');
//    }else{
//        
//        //Show loader
//        $('#sveaPnr').after('<img src="images/svea_indicator.gif" id="SveaInvoiceLoader" />');
//        
//        jQuery.ajax({
//    	  type: "POST",
//    	  url: "sveaAjax.php",
//    	  data: {sveapnr: sveaPnr, is_company: company},
//    	  success: function(msg){
//    	      jQuery('#pers_nr_error_fakt').empty();
//              eval(msg);
//              $('#SveaInvoiceLoader').remove();
//    		}
//    	});
//    }
//    
//}
//
////Get adress, PartPay
//function getAdressPP(fin){
//    
//    var sveaPnr = jQuery('#sveaPnr_delbet').val();
//    
//    if (sveaPnr == ''){
//        jQuery('#pers_nr_error_delbet').html('Personnr måste fyllas i');
//    }else{
//        
//        //Show loader
//        $('#sveaPnr_delbet').after('<img src="images/svea_indicator.gif" id="SveaInvoiceLoader" />');
//        
//        jQuery.ajax({
//    	  type: "POST",
//    	  url: "sveaAjax.php",
//    	  data: {sveapnr: sveaPnr, paymentOptions: '1', f: fin},
//    	  success: function(msg){
//    	      jQuery('#pers_nr_error_delbet').empty();
//              eval(msg);
//              $('#SveaInvoiceLoader').remove();
//    		}
//    	});
//    }
//}