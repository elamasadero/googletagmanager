<?php
/**
* NOTICE OF LICENSE
**
*  @author    Rodrigo Varela Tabuyo <rodrigo@centolaecebola.com>
*  @copyright 2017 Rodrigo Varela Tabuyo
*  @license   ……
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class GoogleTagManager extends Module
{
    // add custom error messages
    protected $errors = array();

    public function __construct()
    {
        $this->name = 'googletagmanager';
        $this->tab = 'analytics_stats';
        $this->version = '0.1';
        $this->author = 'Rodrigo Varela Tabuyo';
        $this->module_key = '5cb794a64177c47254bef97263fe8lbc';
        $this->bootstrap = false;
        $this->ps_versions_compliancy = array('min' => '1.6');

        parent::__construct();

        $this->displayName = $this->l('Google Tag Manager');
        $this->description = $this->l('Añade tags y no mires atrás');

        $this->confirmUninstall = $this->l('¿Está seguro de quieres desinstalar este módulo?');
    }

    public function install()
    {        
        return (
            parent::install()
            // Custom hook to add tag right after <head>
            && $this->registerHook('GoogleTagManagerOnTop')

            // Custom hook to add iframe right after <body>
            && $this->registerHook('GoogleTagManagerAfterBody')

            // Use to set common dataLayer vars
            && $this->registerHook('displayHeader')

            // Use to set Homepage dataLayer vars
            && $this->registerHook('displayHome')

            // Use to set Product page dataLayer vars
            && $this->registerHook('displayFooterProduct')

            // Use to set product listings (categories and search) page dataLayer vars
            && $this->registerHook('listingPage')

            && $this->registerHook('actionProductListOverride')

            && $this->registerHook('actionSearch')

            // Use to set shopping cart dataLayer vars
            && $this->registerHook('displayShoppingCart')
            
            // Use to set order confirmation dataLayer vars
            && $this->registerHook('displayOrderConfirmation')
            );
    }

    public function uninstall() {

        if (!parent::uninstall()) {
            return false;
        }

        return parent::uninstall();
    }

    public function hookGoogleTagManagerOnTop($params) {
        //Custom hook to add tag right after <head>
        return $this->display(__FILE__, 'views/templates/hooks/googletagmanagerontop.tpl');
    }
    public function hookGoogleTagManagerAfterBody() {
        // Custom hook to add iframe right after <body>
        return $this->display(__FILE__, 'views/templates/hooks/googletagmanagerafterbody.tpl');
    }
    
    public function hookDisplayHeader($params) {
        //Set up common Criteo One Tag vars
        $customer = $this->context->customer; //id_customer = $params['cart']->id_customer;
        if( $customer->id ) {
            $customer_email = $customer->email;
            $processed_address = strtolower($customer_email); //conversion to lower case 
            $processed_address = trim($processed_address); //trimming
            $processed_address = mb_convert_encoding($processed_address, "UTF-8", mb_detect_encoding($customer_email)); //conversion from ISO-8859-1 to UTF-8 (replace "ISO-8859-1" by the source encoding of your string)
            $processed_address = md5($processed_address); //hash with MD5 algorithm
            $hashedEmail = $processed_address;
            $this->context->smarty->assign("hashedEmail",$hashedEmail);
        }
        else
          $hashedEmail = '';

    }
    public function hookDisplayHome($params) {
        //Homepage DataLayer value for Criteo One Tag
        
        $this->context->smarty->assign("PageType", "HomePage");
    }

    public function hookDisplayFooterProduct($params) {
        //DataLayer value for Criteo One Tag
        
        $id_product = Tools::getValue('id_product');
        
        $this->context->smarty->assign("ProductID", $id_product);
        $this->context->smarty->assign("PageType", "ProductPage");

    }

    public function hookActionProductListOverride($params) {
        //Get first three products in category page
        
        $order_by = $this->context->controller->orderBy;
        $order_way = $this->context->controller->orderWay;
        $id_category = Tools::getValue('id_category');
        $category = new Category($id_category);
        $three_products = $category->getProducts($this->context->language->id, 1, 3, $order_by, $order_way);
        
        $this->context->smarty->assign("three_products", $three_products);
        $this->context->smarty->assign("PageType", "ListingPage");
    }

    public function hookActionSearch($params) {
        //TODO: similar to previous function, but using search results
    }
    
    public function hookDisplayShoppingCart($params) {
        //DataLayer value for Criteo One Tag
        $step_in_checkout_process= $this->context->controller->step;
        if( $step_in_checkout_process == 0) { //show shopping cart
            $this->context->smarty->assign("transactionProducts", $params['products']);
            $this->context->smarty->assign("PageType", "BasketPage");
        } //do not assign dataLayer vars in payment and delivery options

    }

    public function hookOrderConfirmation($params) {
        $this->context->smarty->assign("PageType", "TransactionPage");

        $obj_order = $params['objOrder'];
        $ids_payment_error = array(6, 8); //cancelled, payment error, refunded

        $new_Cart = new Cart($params['objOrder']->id_cart);
        $customer = new Customer($new_Cart->id_customer);
        
        //if first order, we have a new customer
        if( count(Order::getCustomerOrders($customer->id)) == 1 )
            $this->smarty->assign("type_of_customer", "new_customer");
        else
            $this->smarty->assign("type_of_customer", "returning_customer");

        $products_in_cart = $new_Cart->getProducts(true);
        
        if (Validate::isLoadedObject($obj_order)) {
            if (!in_array($obj_order->current_state, $ids_payment_error)) {
                // Validate all orders except payment error status
                $order_is_valid = true;
            }

            if ($order_is_valid) {
                // convert object to array
                $order = get_object_vars($obj_order);
                $order_id = $order['id'];
                $id_shop = $order['id_shop'];
                $id_lang = $order['id_lang'];

                $this->smarty->assign("transactionId", $order_id); //Transaction ID
                $this->smarty->assign("transactionTotal", $order['total_paid']); //
                $this->smarty->assign("transactionShipping", $order['total_shipping']); //
                $this->smarty->assign("transactionProducts", $products_in_cart); //
                $this->smarty->assign("dataLayer", $order);
            }
        }
    }
}
