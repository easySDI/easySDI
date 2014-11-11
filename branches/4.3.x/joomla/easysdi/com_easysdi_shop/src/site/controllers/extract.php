<?php

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_COMPONENT . '/helpers/easysdi_shop.php';


/**
 * Extract - WebService designed to getOrders and setProduct
 * based on the old webservice (rest)
 *
 * @author Jérôme VILLEMAGNE <jerome.villemagne@depth.ch>
 * @since 4.3.0
 */
class Easysdi_shopControllerExtract extends Easysdi_shopController {
    
    // Address type
    const CONTACT = '1';
    const BILLING = '2';
    const DELIVERY = '3';
    
    // Product state
    const PRODUCTSTATE_AVAILABLE = 1;
    const PRODUCTSTATE_AWAIT = 2;
    const PRODUCTSTATE_SENT = 3;
    const PRODUCTSTATE_VALIDATION = 4;
    const PRODUCTSTATE_REJECTED_TP = 5; //product rejected by third party
    const PRODUCTSTATE_REJECTED_SUPPLIER = 6; // product rejected by supplier
    
    // Order state
    const ORDERSTATE_ARCHIVED = 1;
    const ORDERSTATE_HISTORIZED = 2;
    const ORDERSTATE_FINISH = 3;
    const ORDERSTATE_AWAIT = 4;
    const ORDERSTATE_PROGRESS = 5;
    const ORDERSTATE_SENT = 6;
    const ORDERSTATE_SAVED = 7;
    const ORDERSTATE_VALIDATION = 8;
    const ORDERSTATE_REJECTED_TP = 9; //order rejected by third party
    const ORDERSTATE_REJECTED_SUPPLIER = 10; //order rejected by supplier
    
    // Extract storage
    const EXTRACTSTORAGE_LOCAL = 1;
    const EXTRACTSTORAGE_REMOTE = 2;

    /** @var string Possible values global or organism */
    private $userType = 'global';
    private $organism;
    
    // Namespace and XSD
    private $nsSdi = 'http://www.easysdi.org/2011/sdi';
    private $xmlnsXsi = 'http://www.w3.org/2001/XMLSchema-instance';
    private $xsiGetOrders = 'http://www.easysdi.org/2011/sdi/getorders.xsd';
    private $xsiSetProduct = 'http://www.easysdi.org/2011/sdi/setproduct.xsd';
    private $xsiException = 'http://www.easysdi.org/2011/sdi/exception.xsd';

    /** @var JDatabaseDriver Description */
    private $db;

    /** @var DOMDocument */
    private $request;

    /** @var DOMDocument  */
    private $response;
    
    /** @var DOMElement **/
    private $product;
    
    /**
     * __construct
     */
    function __construct() {
        parent::__construct();

        $this->db = JFactory::getDbo();
        $this->request = new DOMDocument('1.0', 'utf-8');
        $this->response = new DOMDocument('1.0', 'utf-8');
    }

    /**
     * Authenticate user
     * 
     * @return boolean
     */
    private function authentication() {
        $success = FALSE;

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Texte utilisé si le visiteur utilise le bouton d\'annulation';
            exit;
        } else {
            /**
             * @Todo
             * [jvi/mba - 2014-06-13]:
             * Note: Joomla password generation change ;  isOrderAccount use new type of pwd while isOrganismAccount use
             * old one - should be change...
             */
            
            if ($this->isOrderAccount($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
                $success = TRUE;
            } elseif ($this->isOrganismAccount($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
                $success = TRUE;
                $this->userType = 'organism';
            } else {
                header('WWW-Authenticate: Basic realm="My Realm"');
                header('HTTP/1.0 401 Unauthorized');
                echo "Nom d'utilisateur ou mot de passe invalide.";
            }
        }

        return $success;
    }

    /**
     * Check if user is the orderaccount user
     * 
     * @param string $username
     * @param string $password
     * @return boolean
     */
    private function isOrderAccount($username, $password) {
        $globalUserId = JComponentHelper::getParams('com_easysdi_shop')->get('orderaccount');
        
        $user = JFactory::getUser();
        
        $app = JFactory::getApplication();
        $credentials['username'] = $username; //user entered name
        $credentials['password'] = $password;
        
        $error = $app->login($credentials);
        
        $user = JFactory::getUser();
        
        if ($user->get('id')==$globalUserId) {
            $app->logout();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Check if user is an organism
     * 
     * @param string $username
     * @param string $password
     * @return boolean
     */
    private function isOrganismAccount($username, $password) {
        $query = $this->db->getQuery(true);

        $query->select('o.id, o.username, o.password');
        $query->from('#__sdi_organism o');
        $query->where('o.username = ' . $query->quote($username));

        $this->db->setQuery($query);

        if ($organism = $this->db->loadObject()) {
            $passwordarray = explode(':', $organism->password);
            $pwdCryp = JUserHelper::getCryptedPassword($password, $passwordarray[1]) . ':' . $passwordarray[1];

            if ($organism->password == $pwdCryp) {
                $this->organism = $organism;
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    
    /**
     * getException - return an xml response which describes an error
     * 
     * @param integer $code
     * @param string $message
     * @param string $details
     * @return void
     */
    private function getException($code, $message, $details = ''){
        
        // Rollback SQL Transaction
        $this->db->transactionRollback();
        
        if(is_array($details))
            $details = implode('<br>', $details);
        
        $response = new DOMDocument('1.0', 'utf-8');
        
        $root = $response->createElementNS($this->xsiException,'exception');
        
        $root->appendChild($response->createElementNS($this->xsiException,'code', $code));
        $root->appendChild($response->createElementNS($this->xsiException,'message', $message));
        $root->appendChild($response->createElementNS($this->xsiException,'details', $details));
        
        $response->appendChild($root);
        
        if(!$response->schemaValidate($this->xsiException)){
            var_dump(libxml_get_errors());
            die();
        }
        
        echo $response->saveXML();
        JFactory::getApplication()->close($code);
    }
    
    /****************/
    /** GET ORDERS **/
    /****************/
    
    /**
     * addAttribute - add an attribute to a DOMNode
     * 
     * @param DOMNode $parent
     * @param string $attrName
     * @param mixed $attrValue
     * @return void
     * @since 4.3.0
     */
    private function addAttribute(&$parent, $attrName, $attrValue){
        $attribute = $this->response->createAttribute($attrName);
        $attribute->value = $attrValue;
        $parent->appendChild($attribute);
    }

    /**
     * getOrders - public method to get list of availables orders
     * 
     * @return type
     */
    public function getOrders() {
        if($this->authentication()){
            $orders = $this->response->createElementNS($this->nsSdi, 'sdi:orders');
            
            $this->addAttribute($orders, 'xmlns:xsi', $this->xmlnsXsi);
            $this->addAttribute($orders, 'xsi:schemaLocation', $this->nsSdi.' '.$this->xsiGetOrders);
            
            $platform = $this->response->createElementNS($this->nsSdi, 'sdi:platform');
            
            $this->addAttribute($platform, 'name', 'easySDI');
            $this->addAttribute($platform, 'version', '4.3.0-beta-1');
            $this->addAttribute($platform, 'serviceversion', '4.0');
            
            $orders->appendChild($platform);
            
            // retrieve all orders
            $query = $this->db->getQuery(true);

            $query
                    ->select('o.id, o.guid, ' . $this->db->quoteName('o.name') . ', o.user_id, o.surface, u.guid as user_guid, us.name as user_name, o.thirdparty_id, o.sent, ' . $this->db->quoteName('ot.value') . ' as ordertype')
                    ->select('po.id as pricing_order, po.cfg_vat, po.cfg_currency, po.cfg_rounding, po.cfg_overall_default_fee, po.cfg_free_data_fee, po.cal_fee_ti, po.ind_lbl_category_order_fee')
                    ->from('#__sdi_order o')
                    ->innerJoin('#__sdi_pricing_order po ON po.order_id=o.id')
                    ->innerJoin('#__sdi_sys_ordertype ot on ot.id = o.ordertype_id')
                    ->innerJoin('#__sdi_order_diffusion od on o.id = od.order_id')
                    ->innerJoin('#__sdi_diffusion d on d.id = od.diffusion_id')
                    ->innerJoin('#__sdi_version v on d.version_id = v.id')
                    ->innerJoin('#__sdi_resource r on r.id = v.resource_id')
                    ->innerJoin('#__sdi_user u on u.id = o.user_id')
                    ->innerJoin('#__users us ON us.id=u.user_id');
            if (!empty($this->organism)) {
                $query->where('r.organism_id = ' . (int)$this->organism->id);
            }
            $query
                    ->where('od.productstate_id = ' . self::PRODUCTSTATE_SENT)
                    ->group('o.id');

            $this->db->setQuery($query);

            $results = $this->db->loadObjectList();
            foreach ($results as $order)
                $orders->appendChild($this->getOrder($order));
            
            
            $this->response->appendChild($orders);
        
            if(!$this->response->schemaValidate($this->xsiGetOrders)){
                //throw an xml error exception
                $errors = libxml_get_errors();
                $this->getException(500, 'Internal Server Error', $errors);
            }
            
            echo $this->response->saveXML();
            JFactory::getApplication()->close();
        }//else throw an authentication exception
        else $this->getException(401, 'Unauthorized');
    }

    /**
     * getOrder - Get one ORDER node
     * 
     * @param stdClass $order
     * @return DOMElement ORDER node
     */
    private function getOrder($order) {
        // Open an SQL Transaction
        $this->db->transactionStart();

        $root = $this->response->createElementNS($this->nsSdi, 'sdi:order');
        
        $this->addAttribute($root, 'id', $order->id);
        $this->addAttribute($root, 'type', $order->ordertype);
        $this->addAttribute($root, 'guid', $order->guid);
        $this->addAttribute($root, 'datetimesent', implode('T', explode(' ', $order->sent)));
        
        $root->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:name', $order->name));
        
        $root->appendChild($this->getClient($order));
        $root->appendChild($this->getTierce($order->thirdparty_id));
        $root->appendChild($this->getPerimeter($order));
        
        
        $basket = new sdiBasket();
        $basket->loadOrder($order->id);
        $basket->sdiUser = $sdiUser = sdiFactory::getSdiUser($order->user_id);
        if(!empty($order->thirdparty_id)){
            $basket->thirdparty = $order->thirdparty_id;
        }
        Easysdi_shopHelper::extractionsBySupplierGrouping($basket);
        Easysdi_shopHelper::basketPriceCalculation($basket);
        $order->pricing = $basket->pricing;
        
        $root->appendChild($this->getSuppliers($order));
        
        $pricing = $this->response->createElementNS($this->nsSdi, 'sdi:pricing');
        $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_vat', $order->cfg_vat));
        $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_currency', $order->cfg_currency));
        $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_rounding', $order->cfg_rounding));
        $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_overall_default_fee', $order->cfg_overall_default_fee));
        $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_free_data_fee', (bool)$order->cfg_free_data_fee ? 'true' : 'false'));
        $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cal_fee_ti', $order->cal_fee_ti));
        $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:ind_lbl_category_order_fee', $order->ind_lbl_category_order_fee));
        
        $root->appendChild($pricing);
        
        $this->changeOrderState($order->id);
        
        return $root;
    }
    
    /**
     * getSuppliers - get suppliers node for a given order
     * 
     * @param stdClass $order
     * @return DOMElement SUPPLIERS node
     */
    private function getSuppliers($order){
        $suppliers = $this->response->createElementNS($this->nsSdi, 'sdi:suppliers');
        
        // retrieve all suppliers
        $query = $this->db->getQuery(true)
                ->select('pos.*')
                ->from('#__sdi_pricing_order_supplier pos')
                ->where('pos.pricing_order_id='.(int)$order->pricing_order);
        if(!empty($this->organism))
                $query->where('pos.supplier_id='.(int)$this->organism->id);
        
        $this->db->setQuery($query);
        $orderSuppliers = $this->db->loadObjectList();
        
        foreach($orderSuppliers as $orderSupplier)
            $suppliers->appendChild($this->getSupplier($order->id, $orderSupplier));
        
        return $suppliers;
    }
    
    /**
     * getSupplier - get one SUPPLIER node
     * 
     * @param integer $orderId
     * @param stdClass $orderSupplier
     * @return DOMElement SUPPLIER node
     */
    private function getSupplier($orderId, $orderSupplier){
        $supplier = $this->response->createElementNS($this->nsSdi, 'sdi:supplier');

        $supplier->appendChild($this->getOrganism($orderSupplier->supplier_id));

        $pricing = $this->response->createElementNS($this->nsSdi, 'sdi:pricing');
        $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_internal_free', (bool)$orderSupplier->cfg_internal_free ? 'true' : 'false'));
        $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_fixed_fee_ti', $orderSupplier->cfg_fixed_fee_ti));
        $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_data_free_fixed_fee', (bool)$orderSupplier->cfg_data_free_fixed_fee ? 'true' : 'false'));
        $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cal_fee_ti', $orderSupplier->cal_fee_ti));

        $supplier->appendChild($pricing);
        $supplier->appendChild($this->getProducts($orderSupplier));
        
        return $supplier;
    }

    /**
     * getAddress - Get an address node from the specific type
     * 
     * @param integer $addressType type of address 
     * @param integer $recipientId
     * @param string $for
     * @return DOMElement address node
     */
    private function getAddress($addressType, $recipientId, $for = 'client') {
        switch ($addressType) {
            case self::CONTACT:
                $addressContainer = $this->response->createElementNS($this->nsSdi, 'sdi:contact');
                break;
            case self::BILLING:
                $addressContainer = $this->response->createElementNS($this->nsSdi, 'sdi:invoice');
                break;
            case self::DELIVERY:
                $addressContainer = $this->response->createElementNS($this->nsSdi, 'sdi:delivery');
                break;
        }
        
        $address = $this->response->createElementNS($this->nsSdi, 'sdi:address');

        $query = $this->db->getQuery(true);

        $query->select('a.firstname, a.lastname, a.address, a.addresscomplement, a.postalcode, a.postalbox, a.locality, a.email, a.phone, a.mobile, a.fax');
        $query->select('o.acronym, ' . $this->db->quoteName('o.name') );
        $query->select('c.iso2 country_iso');
        $query->leftJoin('#__sdi_organism o on a.organism_id = o.id');
        $query->leftJoin('#__sdi_sys_country c on c.id = a.country_id');
        $query->from('#__sdi_address a ');
        switch ($for) {
            case 'client':
                $query->where('a.user_id = ' . (int)$recipientId);
                break;
            case 'organism':
                $query->where('a.organism_id = ' . (int)$recipientId);
                break;
        }
        $query->where('a.addresstype_id = ' . (int)$addressType);

        $this->db->setQuery($query);
        $addressdata = $this->db->loadObject();

        if (!empty($addressdata)) {
            $address->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:organismacronym', $addressdata->acronym));
            $address->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:organismname', $addressdata->name));
            $address->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:agentfirstname', $addressdata->firstname));
            $address->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:agentlastname', $addressdata->lastname));
            $address->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:addressstreet1', $addressdata->address));
            $address->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:addressstreet2', $addressdata->addresscomplement));
            $address->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:zip', $addressdata->postalcode));
            $address->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:pobox', $addressdata->postalbox));
            $address->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:locality', $addressdata->locality));
            $address->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:country', $addressdata->country_iso));
            $address->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:email', $addressdata->email));
            $address->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:mobile', $addressdata->mobile));
            $address->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:phone', $addressdata->phone));
            $address->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:fax', $addressdata->fax));
        }
        
        $addressContainer->appendChild($address);

        return $addressContainer;
    }
    
    /**
     * getOrganism - get an ORGANISM node
     * 
     * @param integer $id
     * @return DOMElement ORGANISM node
     */
    private function getOrganism($id){
        $query = $this->db->getQuery(true);

        $query->select('o.id, o.guid, o.acronym, o.website, o.name')
                ->from('#__sdi_organism o')
                ->where('o.id = ' . $id);

        $this->db->setQuery($query);
        $result = $this->db->loadObject();
        
        $organism = $this->response->createElementNS($this->nsSdi, 'sdi:organism');
        
        $this->addAttribute($organism, 'id', $result->id);
        $this->addAttribute($organism, 'guid', $result->guid);
        
        $organism->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:name', $result->name));
        $organism->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:acronym', $result->acronym));
        $organism->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:website', $result->website));
        
        $organism->appendChild($this->getCategories($result->id));
        
        $organism->appendChild($this->getAddress(self::CONTACT, $clientOrganism->id, 'tierce'));
        $organism->appendChild($this->getAddress(self::BILLING, $clientOrganism->id, 'tierce'));
        $organism->appendChild($this->getAddress(self::DELIVERY, $clientOrganism->id, 'tierce'));
        
        return $organism;
    }
    
    /**
     * getClientOrganism - get an ORGANISM node from user id
     * 
     * @param integer $userId
     * @return DOMElement ORGANISM node
     */
    private function getClientOrganism($userId){
        $query = $this->db->getQuery(true);

        $query->select('uro.organism_id')
                ->from('#__sdi_user_role_organism uro')
                ->where('uro.user_id = ' . $userId)
                ->where('uro.role_id=1');

        $this->db->setQuery($query);
        $id = $this->db->loadResult();
        return $this->getOrganism($id);
    }

    /**
     * getClient - get a CLIENT node with many ADDRESS child
     * 
     * @param stdClass $order
     * @return DOMElement
     */
    private function getClient($order) {
        
        $client = $this->response->createElementNS($this->nsSdi, 'sdi:client');
        
        $this->addAttribute($client, 'id', $order->user_id);
        $this->addAttribute($client, 'guid', $order->user_guid);
        
        $client->appendChild($this->response->createElementNS($this->nsSdi, 'name', $order->user_name));
        
        $client->appendChild($this->getAddress(self::CONTACT, $order->user_id));
        $client->appendChild($this->getAddress(self::BILLING, $order->user_id));
        $client->appendChild($this->getAddress(self::DELIVERY, $order->user_id));
        
        $client->appendChild($this->getClientOrganism($order->user_id));
        
        return $client;
    }

    /**
     * getTierce - get a TIERCE node with many ADDRESS child
     * 
     * @param integer $id
     * @return DOMElement
     */
    private function getTierce($id = 0) {
        $tierce = $this->response->createElementNS($this->nsSdi, 'sdi:tierce');
        if($id != 0)
            $tierce->appendChild($this->getOrganism($id));
            
        return $tierce;
    }

    /**
     * getCategories - get an organism's categorie's list
     * called from getClient and getTierce
     * 
     * @param integer $organism_id
     * @return DOMElement
     */
    private function getCategories($organism_id = 0){
        $categories = $this->response->createElementNS($this->nsSdi, 'sdi:categories');
        
        $query = $this->db->getQuery(true);
        $query->select('c.id, c.guid, c.name, c.alias')
                ->from('#__sdi_organism_category oc')
                ->join('LEFT', '#__sdi_category c ON c.id=oc.category_id')
                ->where('oc.organism_id='.(int) $organism_id);
        $this->db->setQuery($query);

        foreach($this->db->loadObjectList() as $cat){
            $category = $this->response->createElementNS($this->nsSdi, 'sdi:category');
            
            $this->addAttribute($category, 'id', $cat->id);
            $this->addAttribute($category, 'guid', $cat->guid);
            $this->addAttribute($category, 'alias', $cat->alias);
            
            $category->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:name', $cat->name));

            $categories->appendChild($category);
        }
        
        return $categories;
    }

    /**
     * getProducts - get a PRODUCTS node with many PRODUCT child
     * 
     * @param stdClass $orderSupplier
     * @return DOMElement a PRODUCTS node
     */
    private function getProducts($orderSupplier) {
        $products = $this->response->createElementNS($this->nsSdi, 'sdi:products');
        
        $query = $this->db->getQuery(true)
                ->select('posp.guid as posp_guid, posp.product_id, posp.pricing_id, posp.cfg_pct_category_supplier_discount, posp.ind_lbl_category_supplier_discount, posp.cal_amount_data_te, posp.cal_total_amount_te, posp.cal_total_amount_ti, posp.cal_total_rebate_ti')
                ->select('d.guid, d.name')
                ->select('od.id as od_id')
                ->select('pospp.pricing_profile_id, pospp.pricing_profile_name, pospp.cfg_fixed_fee, pospp.cfg_surface_rate, pospp.cfg_min_fee, pospp.cfg_max_fee, pospp.cfg_pct_category_profile_discount, pospp.ind_lbl_category_profile_discount')
                ->select('pp.guid as pricing_profile_guid')
                ->select('m.id as metadata_id, m.guid as metadata_guid')
                ->from('#__sdi_pricing_order_supplier_product posp')
                ->join('LEFT', '#__sdi_diffusion d ON d.id=posp.product_id')
                ->innerJoin('#__sdi_version v on d.version_id = v.id')
                ->innerJoin('#__sdi_resource r on r.id = v.resource_id')
                ->innerJoin('#__sdi_metadata m on m.version_id = v.id')
                ->join('LEFT', '#__sdi_pricing_order_supplier_product_profile pospp ON pospp.pricing_order_supplier_product_id=posp.id')
                ->join('LEFT', '#__sdi_pricing_profile pp ON pp.id=pospp.pricing_profile_id')
                
                ->join('LEFT', '#__sdi_pricing_order_supplier pos ON pos.id='.(int)$orderSupplier->id)
                ->join('LEFT', '#__sdi_pricing_order po ON po.id=pos.pricing_order_id')
                ->join('LEFT', '#__sdi_order_diffusion od ON od.order_id=po.order_id AND od.diffusion_id=posp.product_id')
                
                ->where('posp.pricing_order_supplier_id='.(int)$orderSupplier->id)
                ->where('od.productstate_id = ' . self::PRODUCTSTATE_SENT)
                ;
        
        $this->db->setQuery($query);
        
        $orderProducts = $this->db->loadObjectList();
        
        foreach($orderProducts as $orderProduct)
            $products->appendChild($this->getProduct($orderProduct));
        
        return $products;
    }

    /**
     * getProduct - get a PRODUCT node
     * 
     * @param stdClass $orderProduct
     * @return DOMElement PRODUCT node
     */
    private function getProduct($orderProduct) {
        $root = $this->response->createElementNS($this->nsSdi, 'sdi:product');
        
        $this->addAttribute($root, 'id', $orderProduct->product_id);
        $this->addAttribute($root, 'guid', $orderProduct->guid);
        
        $root->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:name', $orderProduct->name));
        
        $pricing = $this->response->createElementNS($this->nsSdi, 'sdi:pricing');
        
        switch($orderProduct->pricing_id){
            case 1: // free product
                $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_type', 'free'));
                break;
            
            case 2: // fee without profile
                $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_type', 'fee'));
                break;
            
            case 3: // fee with profile
                $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_type', 'profile'));
                
                $profile = $this->response->createElementNS($this->nsSdi, 'sdi:profile');

                $this->addAttribute($profile, 'id', $orderProduct->pricing_profile_id);
                $this->addAttribute($profile, 'guid', $orderProduct->pricing_profile_guid);

                $profile->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:name', $orderProduct->pricing_profile_name));
                $profile->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_fixed_fee', $orderProduct->cfg_fixed_fee));
                $surfaceRate = $this->response->createElementNS($this->nsSdi, 'sdi:cfg_surface_rate', $orderProduct->cfg_surface_rate);
                $this->addAttribute($surfaceRate, 'unit', 'currency per km2');
                $profile->appendChild($surfaceRate);
                $profile->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_min_fee', $orderProduct->cfg_min_fee));
                $profile->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_max_fee', $orderProduct->cfg_max_fee));

                $pricing->appendChild($profile);

                $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cal_amount_data_te', $orderProduct->cal_amount_data_te));
                $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_pct_category_profile_discount', $orderProduct->cfg_pct_category_profile_discount));
                $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:ind_lbl_category_profile_discount', $orderProduct->ind_lbl_category_profile_discount));
                $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cfg_pct_category_supplier_discount', $orderProduct->cfg_pct_category_supplier_discount));
                $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:ind_lbl_category_supplier_discount', $orderProduct->ind_lbl_category_supplier_discount));
                $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cal_total_amount_te', $orderProduct->cal_total_amount_te));
                $pricing->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:cal_total_amount_ti', $orderProduct->cal_total_amount_ti));
                
                break;
        }
        
        $root->appendChild($pricing);
        
        $root->appendChild($this->getProductProperties($orderProduct));
        
        $metadata = $this->response->createElementNS($this->nsSdi, 'sdi:metadata');
        $this->addAttribute($metadata, 'id', $orderProduct->metadata_id);
        $this->addAttribute($metadata, 'guid', $orderProduct->metadata_guid);
        $metadata->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:xml', base64_encode(file_get_contents(JPATH_BASE.'/orderproductsfiles/'.$orderProduct->posp_guid.'.xml'))));
        $metadata->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:pdf', base64_encode(file_get_contents(JPATH_BASE.'/orderproductsfiles/'.$orderProduct->posp_guid.'.pdf'))));
        $root->appendChild($metadata);
        
        $query = $this->db->getQuery(true);

        $query->update('#__sdi_order_diffusion od');
        $query->set('od.productstate_id = ' . (int)self::PRODUCTSTATE_AWAIT);
        $query->where('od.id = ' . (int)$orderProduct->od_id);

        $this->db->setQuery($query);
        $this->db->execute();
        
        return $root;
    }

    /**
     * getProductProperties - get a PROPERTIES node with many PROPERTY node
     * 
     * @param stdClass $orderProduct
     * @return DOMElement PROPERTIES node
     */
    private function getProductProperties($orderProduct) {
        $properties = $this->response->createElementNS($this->nsSdi, 'sdi:properties');
        
        $query = $this->db->getQuery(true)
                ->select('p.id, p.guid, ' . $this->db->quoteName('p.alias') . ' as palias, ' . $this->db->quoteName('pv.alias') . ' as pvalias')
                ->from('#__sdi_pricing_order_supplier_product posp')
                ->join('LEFT', '#__sdi_pricing_order_supplier pos ON pos.id=posp.pricing_order_supplier_id')
                ->join('LEFT', '#__sdi_pricing_order po ON po.id=pos.pricing_order_id')
                ->join('LEFT', '#__sdi_order_diffusion od ON od.order_id=po.order_id AND od.diffusion_id=posp.product_id')
                ->innerJoin('#__sdi_order_propertyvalue opv on opv.orderdiffusion_id = od.id')
                ->innerJoin('#__sdi_propertyvalue pv on pv.id = opv.propertyvalue_id')
                ->innerJoin('#__sdi_property p on p.id = pv.property_id');

        $this->db->setQuery($query);
        $propertiesdata = $this->db->loadObjectList();

        foreach ($propertiesdata as $propertydata) {
            $property = $this->response->createElementNS($this->nsSdi, 'sdi:property');
            
            $this->addAttribute($property, 'id', $propertydata->id);
            $this->addAttribute($property, 'alias', $propertydata->palias);
            $this->addAttribute($property, 'guid', $propertydata->guid);
            $property->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:value', $propertydata->pvalias));

            $properties->appendChild($property);
        }

        return $properties;
    }

    /**
     * getPerimeter - Get a PERIMETER node with many CONTENT node
     * 
     * @param stdClass $order
     * @return DOMElement
     */
    private function getPerimeter($order){
        $perimeter = $this->response->createElementNS($this->nsSdi, 'sdi:perimeter');
        
        $query = $this->db->getQuery(true);

        $query->select('p.id, p.guid, ' . $this->db->quoteName('p.name') . ' as perimeter_name, p.alias, ' . $this->db->quoteName('op.value') . ' as perimeter_value');
        $query->from('#__sdi_order_perimeter op');
        $query->innerJoin('#__sdi_perimeter p on p.id = op.perimeter_id');
        $query->where('op.order_id = ' . (int)$order->id);

        $this->db->setQuery($query);
        $contents = $this->db->loadObjectList();
        
        $this->addAttribute($perimeter, 'type', $contents[0]->alias=='freeperimeter' || $contents[0]->alias=='myperimeter' ? 'coordinates' : 'values');
        $this->addAttribute($perimeter, 'id', $contents[0]->id);
        $this->addAttribute($perimeter, 'alias', $contents[0]->alias);
        $this->addAttribute($perimeter, 'guid', $contents[0]->guid);
        
        $surface = $this->response->createElementNS($this->nsSdi, 'sdi:surface', $order->surface);
        $this->addAttribute($surface, 'unit', 'm2');
        $perimeter->appendChild($surface);

        foreach($contents as $content)
            $perimeter->appendChild($this->response->createElementNS($this->nsSdi, 'sdi:content', $content->perimeter_value));
        
        return $perimeter;
    }
    
    /*****************/
    /** SET PRODUCT **/
    /*****************/


    /**
     * setProduct - public method to update a product of an order
     * 
     * @return void
     * @since 4.3.0
     */
    public function setProduct(){
        if($this->authentication()){
            if($this->request->loadXML(JFactory::getApplication()->input->get('xml', null, 'raw'), LIBXML_PARSEHUGE)){
                if(@$this->request->schemaValidate($this->xsiSetProduct)){
                    // Open an SQL Transaction
                    $this->db->transactionStart();
                    
                    $this->product = $this->request->getElementsByTagNameNS($this->nsSdi, 'product')->item(0);
                    
                    $diffusionModel = $this->getModel('Diffusion', 'Easysdi_shopModel');
                    $diffusion = $diffusionModel->getTable();
                    if(!($diffusion->load(array('guid' => $this->product->getAttribute('guid')))))
                        $this->getException(400, 'Bad Request', 'Cannot load the requested product');
                    
                    $pospModel = $this->getModel('PricingOrderSupplierProduct', 'Easysdi_shopModel');
                    $posp = $pospModel->getTable();
                    if(!($posp->load(array('product_id' => $diffusion->id))))
                        $this->getException(400, 'Bad Request', 'Cannot load the requested pricing product');
                    
                    //check the product/order integrity
                    $posModel = $this->getModel('PricingOrderSupplier', 'Easysdi_shopModel');
                    $pos = $posModel->getTable();
                    if(!($pos->load($posp->pricing_order_supplier_id)))
                        $this->getException(400, 'Bad Request', 'Cannot load the requested supplier');
                    
                    $orderModel = $this->getModel('Order', 'Easysdi_shopModel');
                    $order = $orderModel->getTable();
                    if(!($order->load(array('guid' => $this->product->getElementsByTagNameNS($this->nsSdi, 'order')->item(0)->nodeValue))))
                        $this->getException(400, 'Bad Request', 'Cannot load the requested order');
                    
                    $poModel = $this->getModel('PricingOrder', 'Easysdi_shopModel');
                    $po = $poModel->getTable();
                    if(!($po->load(array('order_id' => $order->id))))
                        $this->getException(400, 'Bad Request', 'Cannot load the requested pricing order');
                    
                    if($po->id != $pos->pricing_order_id)
                        //throw a product/order integrity exception
                        $this->getException(400, 'Bad Request', 'Couple product/order integrity violation');
                    
                    
                    // check that order_diffusion is open for update (means at AWAIT state)
                    $query = $this->db->getQuery(true)
                            ->select('od.id')
                            ->from('#__sdi_order_diffusion od')
                            ->where('od.order_id='.(int)$order->id)
                            ->where('od.diffusion_id='.(int)$diffusion->id)
                            ->where('od.productstate_id='.self::PRODUCTSTATE_AWAIT);
                    $this->db->setQuery($query);
                    $this->db->execute();
                    
                    if($this->db->getNumRows()==0)
                        //throw a resource conflict exception
                        $this->getException(409, 'Resource Conflict', 'The product you are trying to update has already been updated');
                    
                    //check if the current organism own the given product, if an organism account is used
                    if($this->userType == 'organism'){
                        $query = $this->db->getQuery(true)
                                ->select('r.organism_id')
                                ->from('#__sdi_resource r')
                                ->innerJoin('#__sdi_version v ON v.resource_id=r.id')
                                ->innerJoin('#__sdi_diffusion d ON d.version_id=v.id')
                                ->innerJoin('#__sdi_order_diffusion od ON od.diffusion_id=d.id')
                                ->where('d.id='.(int)$posp->product_id)
                                ;
                        $this->db->setQuery($query);
                        
                        if($this->organism->id != $this->db->loadResult()){
                            //throw a product/organism integrity exception
                            $this->getException(403, 'Forbidden', 'You cannot update this product');
                        }
                    }
                    
                    //all is fine
                    $this->updateOrderDiffusion($posp, $pos, $po);
                    
                }//else throw xml validating exception
                else $this->getException(400, 'Bad Request', 'The given XML is not valid. Please consult the XSD');
            }//else throw xml loading exception
            else $this->getException(400, 'Bad Request', 'Cannot load the XML');
        }//else throw authentication exception
        else $this->getException(401, 'Unauthorized');
    }
    
    /**
     * convertSize - convert the given size to size in octet according to the given unit
     * 
     * @return string
     */
    private function convertSize(){
        $size = $this->product->getElementsByTagNameNS($this->nsSdi, 'size')->item(0);
        $unit = $size->getAttribute('unit');
        $size = $size->nodeValue;
        
        switch($unit){
            case 'o':
                return $size;
            
            case 'Kio':
                return $size*1024;
            
            case 'Mio':
                return $size*1024*1024;
            
            case 'Gio':
                return $size*1024*1024*1024;
            
            default:
                //throw an unpredictable case exception - should never fall in this case wich xsd validation
                $this->getException(400, 'Bad Request', 'Unit size not recognized');
        }
    }
    
    /**
     * storeFileLocally - save the file send by a supplier
     * 
     * @param stdClass $posp
     * @param stdClass $po
     * @return array
     */
    private function storeFileLocally($posp, $po){
        if($_FILES['file']['error']>0){
            //throw an upload exception
            $this->getException(500, 'Internal Server Error', $_FILES['file']['error']);
        }
        
        $extractsFilesPath = JPATH_BASE . JComponentHelper::getParams('com_easysdi_shop')->get('orderresponseFolder') . '/' . $po['guid'];
        if(!file_exists($extractsFilesPath)){
            if(!mkdir($extractsFilesPath, 0755, true)){
                //throw a folder creation exception
                $this->getException(500, 'Internal Server Error', 'Cannot create the required folder');
            }
        }
        
        $file = $extractsFilesPath.$posp['guid'].'.zip';
        if(!move_uploaded_file($_FILES['file']['tmp_name'], $file)){
            //throw an upload exception
            $this->getException(500, 'Internal Server Error', 'Cannot save uploaded file');
        }
        
        return array(
            'file' => $file,
            'size' => $_FILES['file']['size']
        );
    }
    
    /**
     * updateOrderDiffusion - update the datatable #__sdi_order_diffusion
     * 
     * @param stdClass $posp
     * @param stdClass $pos
     * @param stdClass $po
     * 
     * @return void
     * @since 4.3.0
     */
    private function updateOrderDiffusion($posp, $pos, $po){
        $updatePricing = false;
        $now = date("Y-m-d H:i:s");
        
        $query = $this->db->getQuery(true)
                ->update('#__sdi_order_diffusion')
                ->where('order_id='.(int)$po->order_id)
                ->where('diffusion_id='.(int)$posp->product_id)
                ->set("completed='".$now."'")
                ;
        
        // set productstate
        switch($this->product->getElementsByTagNameNS($this->nsSdi, 'state')->item(0)->nodeValue){
            case 'available':
                $query->set('productstate_id='.(int)self::PRODUCTSTATE_AVAILABLE);
                break;
            
            case 'rejected':
                $query->set('productstate_id='.(int)self::PRODUCTSTATE_REJECTED_SUPPLIER);
                $updatePricing = true;
                $posp->cal_total_amount_ti = 0;
                break;
            
            default:
                //throw an unpredictable case exception - should never fall in this case with xsd validation
                $this->getException(400, 'Bad Request', 'Product state not recognized');
        }
        
        //set remark
        if($this->product->getElementsByTagNameNS($this->nsSdi, 'remark')->length>0)
            $query->set('remark='.$query->quote($this->product->getElementsByTagNameNS($this->nsSdi, 'remark')->item(0)->nodeValue));
        
        //set fee
        if($this->product->getElementsByTagNameNS($this->nsSdi, 'fee')->length>0){
            $fee = (float)$this->product->getElementsByTagNameNS($this->nsSdi, 'fee')->item(0)->nodeValue;
            $query->set('fee='.$fee);
            $updatePricing = true;
            $posp->cal_total_amount_ti = $fee;
        }
        
        //set storage
        if($this->product->getElementsByTagNameNS($this->nsSdi, 'storage')->length>0){
            switch($this->product->getElementsByTagNameNS($this->nsSdi, 'storage')->item(0)->nodeValue){
                case 'local':
                    //store the file and get back the file(path) and its size
                    extract($this->storeFileLocally($posp, $po));
                    
                    $query->set('storage_id='.(int)self::EXTRACTSTORAGE_LOCAL)
                        ->set('file='.$query->quote($file))
                        ->set('size='.$size)
                        ;
                    break;
                
                case 'remote':
                    $query->set('storage_id='.(int)self::EXTRACTSTORAGE_REMOTE)
                        ->set('file='.$query->quote($this->product->getElementsByTagNameNS($this->nsSdi, 'fileurl')->item(0)->nodeValue))
                        ->set('size='.(int)$this->convertSize())
                        ;
                    break;
                
                default:
                    //throw an unpredictable case exception - should never fall in this case with xsd validation
                    $this->getException(400, 'Bad Request', 'Product storage not recognized');
            }
            
            $query->set('displayName='.$query->quote($this->product->getElementsByTagNameNS($this->nsSdi, 'filename')->item(0)->nodeValue));
        }
        
        $this->db->setQuery($query);
        
        if($this->db->execute()){
            $updatePricing ? $this->updatePricing($posp, $pos, $po) : $this->changeOrderState($po->order_id);
            
            $response = new DOMDocument('1.0', 'utf-8');
            
            $root = $response->createElement('success');
            $root->appendChild($response->createElement('message', 'Product updated'));
            
            $response->appendChild($root);
            
            echo $response->saveXML();
            JFactory::getApplication()->close();
        }//else throw a db exception
        else $this->getException (500, 'Internal Server Error', 'Cannot update order diffusion');
    }
    
    /**
     * updatePricing - update the pricing schema branch
     * 
     * @param stdClass $posp
     * @param stdClass $pos
     * @param stdClass $po
     * 
     * @return void
     * @since 4.3.0
     */
    private function updatePricing($posp, $pos, $po){
        
        if($posp->save(array())){
            $query = $this->db->getQuery(true)
                    ->select('SUM(posp.cal_total_amount_ti) ctat')
                    ->from('#__sdi_pricing_order_supplier_product posp')
                    ->innerJoin('#__sdi_pricing_order_supplier pos ON pos.id=posp.pricing_order_supplier_id')
                    ->where('posp.pricing_order_supplier_id='.(int)$pos->id)
                    ;
            
            $this->db->setQuery($query);
            
            $pos->cal_total_amount_ti = $this->db->loadResult();
            
            if($pos->save(array())){
                $query = $this->db->getQuery(true)
                        ->select('SUM(posp.cal_total_amount_ti) ctat')
                        ->from('#__sdi_pricing_order_supplier_product posp')
                        ->innerJoin('#__sdi_pricing_order_supplier pos ON pos.id=posp.pricing_order_supplier_id')
                        ->where('pos.pricing_order_id='.(int)$po->id)
                        ;

                $this->db->setQuery($query);

                $po->cal_total_amount_ti = $this->db->loadResult();

                if($po->save(array()))
                    $this->changeOrderState($po->order_id);
                //else throw a po update exception
                else $this->getException (500, 'Internal Server Error', 'Cannot update pricing order');
            }
            //else throw a po update exception
            else $this->getException (500, 'Internal Server Error', 'Cannot update pricing order supplier');
        }//else throw a posp update exception
        else $this->getException (500, 'Internal Server Error', 'Cannot update pricing order supplier product');
        
    }

    /**
     * changeOrderState - Dynamically changes the statue of the order.
     * 
     * @param integer $orderId Id of the order.
     */
    private function changeOrderState($orderId) {
        $query = $this->db->getQuery(true);

        $query->select('id');
        $query->from('#__sdi_order_diffusion');
        $query->where('order_id = ' . (int)$orderId);

        $this->db->setQuery($query);
        $total = $this->db->getNumRows($this->db->execute());

        $query = $this->db->getQuery(true);

        $query->select('id');
        $query->from('#__sdi_order_diffusion');
        $query->where('order_id = ' . (int)$orderId);
        $query->where('productstate_id = ' . self::PRODUCTSTATE_AWAIT);

        $this->db->setQuery($query);
        $await = $this->db->getNumRows($this->db->execute());

        $query = $this->db->getQuery(true);

        $query->select('id');
        $query->from('#__sdi_order_diffusion');
        $query->where('order_id = ' . (int)$orderId);
        $query->where('productstate_id = ' . self::PRODUCTSTATE_AVAILABLE);

        $this->db->setQuery($query);
        $available = $this->db->getNumRows($this->db->execute());

        $query = $this->db->getQuery(true);

        $query->select('id');
        $query->from('#__sdi_order_diffusion');
        $query->where('order_id = ' . (int)$orderId);
        $query->where('productstate_id = ' . self::PRODUCTSTATE_REJECTED_SUPPLIER);

        $this->db->setQuery($query);
        $rejected = $this->db->getNumRows($this->db->execute());

        $orderstate = $this->chooseOrderState($total, $await, $available, $rejected);

        if ($orderstate > 0) {
            $now = date("Y-m-d H:i:s");
            
            $query = $this->db->getQuery(true);

            $query->update('#__sdi_order');
            $query->set('orderstate_id = ' . $orderstate);
            if($orderstate == self::ORDERSTATE_FINISH){
                $query->set('completed = ' . $query->quote($now) );
            }
            $query->where('id = ' . (int)$orderId);

            $this->db->setQuery($query);
            $this->db->execute();
        }
        
        // Commit the SQL Transaction
        $this->db->transactionCommit();
    }
    
    /**
     * chooseOrderState - return the correct orderState according to the given params
     * 
     * @param integer $total
     * @param integer $await
     * @param integer $available
     * @param integer $rejected
     * @return integer
     */
    private function chooseOrderState($total, $await, $available, $rejected){
        
        if($total == $rejected)
            return self::ORDERSTATE_REJECTED_SUPPLIER;
        
        if($available == $total || $available+$rejected==$total)
            return self::ORDERSTATE_FINISH;
        
        if($available>0 || $rejected>0)
            return self::ORDERSTATE_PROGRESS;
        
        if($await>0)
            return self::ORDERSTATE_AWAIT;
        
        return 0;
    }

}
