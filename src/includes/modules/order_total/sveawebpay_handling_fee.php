<?php

class sveawebpay_handling_fee 
{
    /**
     * constructor, initialises object from config settings values (in uppercase)
     */
    function sveawebpay_handling_fee()  {

        // include currencies class and create an instance
        require_once(DIR_WS_CLASSES . 'currencies.php');
        $currencies = new currencies(); // no global currencies available at i.e. module installation
        
        $this->code = 'sveawebpay_handling_fee';
        $this->title = MODULE_ORDER_TOTAL_SWPHANDLING_NAME;
        $this->description = MODULE_ORDER_TOTAL_SWPHANDLING_DESCRIPTION;

        //common
        $this->enabled = MODULE_ORDER_TOTAL_SWPHANDLING_STATUS == 'True' ? true : false;
        $this->sort_order = MODULE_ORDER_TOTAL_SWPHANDLING_SORT_ORDER;
        
        //country specific
        $this->tax_class['SE'] = MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_SE;      
        $this->tax_class['NO'] = MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_NO;      
        $this->tax_class['DK'] = MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_DK;      
        $this->tax_class['FI'] = MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_FI;      
        $this->tax_class['NL'] = MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_NL;      
        $this->tax_class['DE'] = MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_DE;      

        // normally, all values should be configured using the shop default, and
        // are converted to the order currency when displayed to the user. This
        // approach leads to non-even handling fee amounts, so we instead wish
        // to specify the exact amount as it should appear on the invoice. 
        // 
        // Thus the "reverse conversion" of the configured handling fee to the
        // default currency (which always has conversion ratio of 1, hence 1/x).        
        $this->handling_fee['SE'] = defined('MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_SE') ? 
           MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_SE * (1/$currencies->currencies['SEK']['value']): 0.0; 
        $this->handling_fee['NO'] = defined('MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_NO') ? 
            MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_NO * (1/$currencies->currencies['NOK']['value']): 0.0;  
        $this->handling_fee['DK'] = defined('MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_DK') ? 
                MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_DK * (1/$currencies->currencies['DKK']['value']): 0.0;    
        $this->handling_fee['FI'] = defined('MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_FI') ? 
                MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_FI * (1/$currencies->currencies['EUR']['value']): 0.0;    
        $this->handling_fee['NL'] = defined('MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_NL') ? 
                MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_NL * (1/$currencies->currencies['EUR']['value']): 0.0;    
        $this->handling_fee['DE'] = defined('MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_DE') ? 
                MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_DE * (1/$currencies->currencies['EUR']['value']): 0.0;    
        
        $this->output = array();
    }

    /**
     * process() populates $this->output[] array with order total row information
     */
    function process()  {
        global $order, $currencies;
   
        // only add to order total rows if the invoice payment method has been selected
        if( $_SESSION['payment'] != "sveawebpay_invoice" ) {
            return;
        }

        // only add to order total rows if the invoice fee is enabled
        if( $this->enabled != "true" ) {
            return;
        }
        
        // get customer country from order
        $countryCode = $order->customer['country']['iso_code_2'];

        // calculate handling fee total
        $fee_cost = $this->handling_fee[$countryCode];
        $tax_class = $this->tax_class[$countryCode];
              
        // calculate tax and add cost to order total and tax
        if ($fee_cost) {

            $fee_tax = 0;
            if ($tax_class > 0) {
                $fee_tax = $fee_cost * tep_get_tax_rate($tax_class, $order->billing['country']['id'], $order->billing['zone_id']) / 100;
                if ($fee_tax > 0) {
                    $order->info['tax'] += $fee_tax;
                    $fee_taxgroup = tep_get_tax_description($tax_class, $order->billing['country']['id'], $order->billing['zone_id']);
                    $order->info['tax_groups'][$fee_taxgroup] += $fee_tax;
                }
            }
            $order->info['total'] += $fee_cost + $fee_tax;
            
            // $this->output[] contains fields presented in order total 
            if (DISPLAY_PRICE_WITH_TAX == 'true') // include tax in value
            {
                $this->output[] = array(
                    'title' => MODULE_ORDER_TOTAL_SWPHANDLING_NAME.":",
                    'text' => $currencies->format($fee_cost + $fee_tax, true, $order->info['currency'], $order->info['currency_value']),
                    //'tax' => $fee_tax, // unfortunately doesn't seem to get passed along to the order total
                    'value' => $fee_cost + $fee_tax);
            } 
            else // don't include tax in value 
            {
                $this->output[] = array(
                    'title' => MODULE_ORDER_TOTAL_SWPHANDLING_NAME.":",
                    'text' => $currencies->format($fee_cost, true, $order->info['currency'], $order->info['currency_value']),
                    //'tax' => $fee_tax, // unfortunately doesn't seem to get passed along to the order total
                    'value' => $fee_cost);
            }
        }       
    }
    
    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_SWPHANDLING_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }
    
    function install() 
    {
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function) values ('Enable Svea Invoice Fee', 'MODULE_ORDER_TOTAL_SWPHANDLING_STATUS', 'True', 'Do you want to apply the Svea invoice fee?', '6', '0', now(), 'tep_cfg_select_option(array(\'True\', \'False\'), ')");        
       
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_SWPHANDLING_SORT_ORDER', '0', 'Sort order of display.', '6', '3', now())");
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Fee (SE)', 'MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_SE', '20', 'Invoice fee will be applied to orders using the invoice payment method. Specify amount excluding tax, in shop default currency.', '6', '0', now())");
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class (SE)', 'MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_SE', '0', 'Tax class for invoice fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");     
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Fee (NO)', 'MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_NO', '0.0', '', '6', '0', now())");
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class (NO)', 'MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_NO', '0', '', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");

        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Fee (DK)', 'MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_DK', '0.0', '', '6', '0', now())");
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class (DK)', 'MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_DK', '0', '', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");        

        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Fee (FI)', 'MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_FI', '0.0', '', '6', '0', now())");
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class (FI)', 'MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_FI', '0', '', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");        

        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Fee (NL)', 'MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_NL', '0.0', '', '6', '0', now())");
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class (NL)', 'MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_NL', '0', '', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");        

        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Fee (DE)', 'MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_DE', '0.0', '', '6', '0', now())");
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class (DE)', 'MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_DE', '0', '', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");        
    }

    function remove() {
        tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
        return array(
            'MODULE_ORDER_TOTAL_SWPHANDLING_STATUS',    
            'MODULE_ORDER_TOTAL_SWPHANDLING_SORT_ORDER',

            'MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_SE',
            'MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_SE',
            
            'MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_NO',
            'MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_NO',

            'MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_DK',
            'MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_DK',

            'MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_FI',
            'MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_FI',
            
            'MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_NL',
            'MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_NL',
            
            'MODULE_ORDER_TOTAL_SWPHANDLING_HANDLING_FEE_DE',
            'MODULE_ORDER_TOTAL_SWPHANDLING_TAX_CLASS_DE'
        );
    }
}
?>