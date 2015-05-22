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
    
    const ORDERTYPE_ORDER = 1;
    const ORDERTYPE_ESTIMATE = 2;
    const ORDERTYPE_DRAFT = 3;
    
    // Order productmining
    const PRODUCTMININGAUTO = 1;
    const PRODUCTMININGMANUAL = 2;
    
    // Extract storage
    const EXTRACTSTORAGE_LOCAL = 1;
    const EXTRACTSTORAGE_REMOTE = 2;

    /** @var string Possible values global or organism */
    private $userType = 'global';
    private $organism;
    
    // HTTP STATUS used
    private $HTTPSTATUS = array(
        200 => 'OK',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        409 => 'Conflict',
        500 => 'Internal Server Error'
    );
    
    // Namespace and XSD
    const nsSdi = 'http://www.easysdi.org/2011/sdi';
    const xmlnsXsi = 'http://www.w3.org/2001/XMLSchema-instance';
    const xsiGetOrders = 'http://www.easysdi.org/2011/sdi/getorders.xsd';
    const xsiGetOrdersParameters = 'http://www.easysdi.org/2011/sdi/getordersparameters.xsd';
    const xsiSetProduct = 'http://www.easysdi.org/2011/sdi/setproduct.xsd';
    const xsiSetProductParameters = 'http://www.easysdi.org/2011/sdi/setproductparameters.xsd';
    const xsiException = 'http://www.easysdi.org/2011/sdi/exception.xsd';

    /** @var JDatabaseDriver Description */
    private $db;
    private $transaction = false;

    /** @var DOMDocument */
    private $request;

    /** @var DOMDocument  */
    private $response;
    
    /** @var DOMElement **/
    private $product;
    
    private $states = array(self::PRODUCTSTATE_SENT);
    
    /**
     * __construct
     */
    function __construct(){
        parent::__construct();

        $this->db = JFactory::getDbo();
        $this->request = new DOMDocument('1.0', 'utf-8');
        $this->response = new DOMDocument('1.0', 'utf-8');
    }

    /**
     * authentication - authenticate the user with given credentials
     * 
     * @return void
     * @since 4.3.0
     * 
     * call getException if can't authenticate the user
     */
    private function authentication(){
        if( !isset($_SERVER['PHP_AUTH_USER']) 
            || !(   
                    $this->isOrderAccount($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) || $this->isOrganismAccount($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) )
            ){
            $this->getException(401);
        }
    }

    /**
     * Check if user is the orderaccount user
     * 
     * @param string $username
     * @param string $password
     * 
     * @return boolean
     * @since 4.3.0
     */
    private function isOrderAccount($username, $password){
        $globalUserId = JComponentHelper::getParams('com_easysdi_shop')->get('orderaccount');
        
        $app = JFactory::getApplication();
        $credentials['username'] = $username; //user entered name
        $credentials['password'] = $password;
        
        $app->login($credentials);
        
        $user = JFactory::getUser();
        
        if($user->get('id')==$globalUserId){
            $app->logout();
            return true;
        }
        return false;
    }

    /**
     * Check if user is an organism
     * 
     * @param string $username
     * @param string $password
     * 
     * @return boolean
     * @since 4.3.0
     */
    private function isOrganismAccount($username, $password) {
        $this->db->setQuery($this->db->getQuery(true)
                ->select('o.id, o.username, o.password')
                ->from('#__sdi_organism o')
                ->where('o.username='.$this->db->quote($username)));
        $organism = $this->db->loadObject();
        if($organism !== null){
            $passwordarray = explode(':', $organism->password);
            $pwdCryp = JUserHelper::getCryptedPassword($password, $passwordarray[1]) . ':' . $passwordarray[1];

            if($organism->password == $pwdCryp){
                $this->organism = $organism;
                $this->userType = 'organism';
                return true;
            }
        }
        return false;
    }
    
    /**
     * sendResponse - send the response to the client
     * close the Application
     * 
     * @param integer $code - the HTTP response code
     * @param mixed $response - the xml to respond
     * 
     * @return void
     * @since 4.3.0
     */
    private function sendResponse($code = 200, $response = null){
        if($response == null){
            $response = $this->response;
        }
        
        echo $response->saveXML();
        JFactory::getApplication()->close($code);
    }
    
    /**
     * getException - build an xml which describes an error
     * 
     * @param integer $code - the exception code
     * @param string $message - the exception message
     * @param string $details - the exception details
     * 
     * @return void
     * @since 4.3.0
     * 
     * call sendResponse to return the exception
     */
    private function getException($code, $details = ''){
        // Rollback SQL Transaction
        if($this->transaction){
            $this->db->transactionRollback();
        }
        
        if(is_array($details)){
            $details = implode('<br>', $details);
        }
        
        $response = new DOMDocument('1.0', 'utf-8');
        
        $root = $response->createElementNS(self::nsSdi, 'sdi:exception');
        $root->setAttributeNS(self::xmlnsXsi, 'xsi:schemaLocation', self::xsiException);
        
        $root->appendChild($response->createElementNS(self::nsSdi,'sdi:code', $code));
        $root->appendChild($response->createElementNS(self::nsSdi,'sdi:message', $this->HTTPSTATUS[$code]));
        $root->appendChild($response->createElementNS(self::nsSdi,'sdi:details', $details));
        
        $response->appendChild($root);
        
        $this->schemaValidation(self::xsiException, 0, $response, false);
        
        $this->sendResponse($code, $response);
    }
    
    /**
     * schemaValidation - a generic method to validate I/O xml against xsd
     * if the xml cannot be validated, call getException according to the IO parameter
     * 
     * @param string $xsd - the xsd URI
     * @param mixed $IO - define if the xml to validate is in Input or Output
     * @param mixed $xml - (optional) the xml to validate ; default to $this->request or $this->response, according to IO parameter
     * @param boolean $throwException - wheter the method should call getException or dump the error
     * 
     * @return void
     * @since 4.3.0
     * 
     * call getException if validation failed (or dump the errors according to throwException parameter)
     */
    private function schemaValidation($xsd, $IO, $xml = null, $throwException = true){
        if($xml === null){
            $xml = $IO ? $this->request : $this->response;
        }
        
        if(!@$xml->schemaValidate($xsd)){
            //throw an xml error exception
            $errors = libxml_get_errors();
            if($throwException === false){
                var_dump($errors);
                die();
            }
            
            if($IO){ // Input
                $this->getException(400, 'The given XML is not valid. Please consult the XSD : '.$xsd);
            }
            else{ // Output
                $this->getException(500, $errors);
            }
        }
    }
    
    /**
     * loadXML - try to load an xml, call getException if failed
     * 
     * @param string $xml - the xml to load
     * 
     * @return void
     * @since 4.3.0
     * 
     * call getException if the given xml cannot be loaded
     */
    private function loadXML($xml){
        if(!$this->request->loadXML($xml, LIBXML_PARSEHUGE)){
            $this->getException(500, 'Cannot load the XML');
        }
    }
    
    /****************/
    /** GET ORDERS **/
    /****************/
    
    /**
     * addAttribute - add an attribute to a DOMNode
     * 
     * @param DOMNode $parent - the DOMNode to which add the attribute
     * @param string $attrName - the attribute name
     * @param mixed $attrValue - the attribute value
     * 
     * @return void
     * @since 4.3.0
     */
    private function addAttribute(&$parent, $attrName, $attrValue){
        $attribute = $this->response->createAttribute($attrName);
        $attribute->value = $attrValue;
        $parent->appendChild($attribute);
    }
    
    /**
     * getOrdersParametersStates - load the given states
     * 
     * @return void
     * @since 4.3.0
     */
    private function getOrdersParametersStates(){
        $this->states = array(); // as states are given, we reset the default value
        foreach($this->request->getElementsByTagNameNS(self::nsSdi, 'state') as $state){
            array_push($this->states, $state->nodeValue);
        }
    }
    
    /**
     * getOrdersParametersRestrictionList - retrieve the restriction list (of ids or guids, depending on the mode)
     * 
     * @param string $mode - determines the restriction list items type
     * 
     * @return array - array of ids or guids
     * @since 4.3.0
     */
    private function getOrdersParametersRestrictionList($mode){
        $restrictionList = array();
        
        switch($mode){
            case 'sdi:getOrdersByGuids': // get guids
                foreach($this->request->getElementsByTagNameNS(self::nsSdi, 'guid') as $guid){
                    array_push($restrictionList, "'".$guid->nodeValue."'");
                }
                break;
            
            case 'sdi:getOrdersByIds': // get ids
                foreach($this->request->getElementsByTagNameNS(self::nsSdi, 'id') as $id){
                    array_push($restrictionList, "'".$id->nodeValue."'");
                }
                break;
            
            case 'sdi:getOrders':
            default:
                // nothing todo
        }
        
        return $restrictionList;
    }
    
    /**
     * getOrdersParameters - retrieve the parameters from the given xml
     * 
     * @param string $xml - the xml which gives parameters for getOrders
     * 
     * @return array - return the mode and the restrictionList
     * @since 4.3.0
     */
    private function getOrdersParameters($xml){
        $this->loadXML($xml);
                
        $this->schemaValidation(self::xsiGetOrdersParameters, 1);

        $parameters = $this->request->getElementsByTagNameNS(self::nsSdi, 'parameters')->item(0);

        // get mode
        $mode = $parameters->getAttributeNS(self::xmlnsXsi, 'type');

        // get states
        if($this->request->getElementsByTagNameNS(self::nsSdi, 'state')->length){
            $this->getOrdersParametersStates();
        }
        
        return array($mode, $this->getOrdersParametersRestrictionList($mode));
    }
    
    /**
     * getOrdersObjectList - build and perform the request to load all the orders to be treated
     * 
     * @param mixed $xml - the xml string or null
     * 
     * @return mixed - an ObjectList or null
     * @since 4.3.0
     */
    private function getOrdersObjectList($xml){
        /* DEFAULT INPUTS */
        $mode = 'sdi:getOrders';

        $agg = 'o.id, o.guid, ' . $this->db->quoteName('o.name') . ', o.user_id, o.surface, o.level, u.guid , us.name , o.thirdparty_id, o.sent, ' . $this->db->quoteName('ot.value') ;
        $agg .= ', po.id , po.cfg_vat, po.cfg_currency, po.cfg_rounding, po.cfg_overall_default_fee, po.cfg_free_data_fee, po.cal_fee_ti, po.ind_lbl_category_order_fee';
        // retrieve all orders
        $query = $this->db->getQuery(true)
                ->select('o.id, o.guid, ' . $this->db->quoteName('o.name') . ', o.user_id, o.surface, o.level, u.guid as user_guid, us.name as user_name, o.thirdparty_id, o.sent, ' . $this->db->quoteName('ot.value') . ' as ordertype')
                ->select('po.id as pricing_order, po.cfg_vat, po.cfg_currency, po.cfg_rounding, po.cfg_overall_default_fee, po.cfg_free_data_fee, po.cal_fee_ti, po.ind_lbl_category_order_fee')
                ->from('#__sdi_order o')
                ->leftJoin('#__sdi_pricing_order po ON po.order_id=o.id')
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
        if($xml !== null){
            list($mode, $restrictionList) = $this->getOrdersParameters($xml);
            // specify where clause depending on the getOrders mode
            if($mode === 'sdi:getOrdersByGuids'){ // by guids
                $query->where('d.guid IN ('.implode(',', $restrictionList).')');
            }
            elseif($mode === 'sdi:getOrdersByIds'){ // by ids
                $query->where('d.id IN ('.implode(',', $restrictionList).')');
            }
        }
        $query->where('od.productstate_id IN ('.implode(',', $this->states).')')
                ->where('d.productmining_id = ' . self::PRODUCTMININGAUTO)
                ->where('o.ordertype_id IN ('.self::ORDERTYPE_ORDER.','.self::ORDERTYPE_ESTIMATE.')')
                ->group($agg);
        
        $this->db->setQuery($query);
        
        return $this->db->loadObjectList();
    }

    /**
     * getOrders - public method to get the list of availables orders according to the given xml
     * 
     * @return void
     * @since 4.3.0
     * 
     * call sendResponse to return the result
     */
    public function getOrders() {
        $this->authentication();
        
        // get xml if given
        $xml = JFactory::getApplication()->input->get('xml', null, 'raw');

        // Open an SQL Transaction
        //$this->db->transactionStart();
        try {
            $this->db->transactionStart();
        } catch (Exception $exc) {
            $this->db->connect();
            $driver_begin_transaction = $this->db->name . '_begin_transaction';
            $driver_begin_transaction($this->db->getConnection());
        }
        $this->transaction = true;

        $orders = $this->response->createElementNS(self::nsSdi, 'sdi:orders');
        $orders->setAttributeNS(self::xmlnsXsi, 'xsi:schemaLocation', self::xsiGetOrders);

        $platform = $this->response->createElementNS(self::nsSdi, 'sdi:platform');

        $this->addAttribute($platform, 'name', 'easySDI');
        $this->addAttribute($platform, 'version', '4.3.0');
        $this->addAttribute($platform, 'serviceversion', '4.0');

        $orders->appendChild($platform);

        foreach($this->getOrdersObjectList($xml) as $order){
            $orders->appendChild($this->getOrder($order));
        }

        $this->response->appendChild($orders);

        $this->schemaValidation(self::xsiGetOrders, 0);

        // Commit the SQL Transaction
        $this->db->transactionCommit();

        $this->sendResponse();
    }
    
    /**
     * getOrderBasket - return the sdiBasket for a given order
     * 
     * @param stdClass $order - an object which represent the current order
     * 
     * @return \sdiBasket
     * @since 4.3.0
     */
    private function getOrderBasket($order){
        $basket = new sdiBasket();
        $basket->loadOrder($order->id);
        $basket->sdiUser = $sdiUser = sdiFactory::getSdiUser($order->user_id);
        if(!empty($order->thirdparty_id)){
            $basket->thirdparty = $order->thirdparty_id;
        }
        Easysdi_shopHelper::extractionsBySupplierGrouping($basket);
        return $basket;
    }

    /**
     * getOrder - build and return an order DOMNode
     * 
     * @param stdClass $order
     * 
     * @return DOMNode sdi:order node
     * @since 4.3.0
     */
    private function getOrder($order) {
        $root = $this->response->createElementNS(self::nsSdi, 'sdi:order');
        
        $this->addAttribute($root, 'id', $order->id);
        $this->addAttribute($root, 'type', $order->ordertype);
        $this->addAttribute($root, 'guid', $order->guid);
        $this->addAttribute($root, 'datetimesent', implode('T', explode(' ', $order->sent)));
        
        $root->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:name', $order->name));
        
        $root->appendChild($this->getClient($order));
        $root->appendChild($this->getTierce($order->thirdparty_id));
        $root->appendChild($this->getPerimeter($order));
        
        $basket = $this->getOrderBasket($order);
        
        $root->appendChild($this->getSuppliers($order, $basket));
        
        if($order->pricing_order !== null){
            $root->appendChild($this->getOrderPricing($order));
        }
        
        $this->changeOrderState($order->id);
        
        return $root;
    }
    
    /**
     * getOrderPricing - get the pricing data for the given order
     * 
     * @param stdClass $order
     * 
     * @return DOMNode sdi:pricing node
     * @since 4.3.0
     */
    private function getOrderPricing($order){
        $pricing = $this->response->createElementNS(self::nsSdi, 'sdi:pricing');
        $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_vat', $order->cfg_vat));
        $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_currency', $order->cfg_currency));
        $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_rounding', $order->cfg_rounding));
        $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_overall_default_fee', $order->cfg_overall_default_fee));
        $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_free_data_fee', (bool)$order->cfg_free_data_fee ? 'true' : 'false'));
        $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cal_fee_ti', $order->cal_fee_ti));
        $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:ind_lbl_category_order_fee', $order->ind_lbl_category_order_fee));
        return $pricing;
    }
    
    /**
     * getSuppliers - get suppliers node for a given order
     * 
     * @param stdClass $order
     * 
     * @return DOMNode sdi:suppliers node
     * @since 4.3.0
     */
    private function getSuppliers($order, $basket){
        $suppliers = $this->response->createElementNS(self::nsSdi, 'sdi:suppliers');
        
        foreach(array_keys($basket->extractions) as $supplierId){
            $suppliers->appendChild($this->getSupplier($order, $supplierId));
        }
        
        return $suppliers;
    }
    
    /**
     * getSupplier - get one SUPPLIER node
     * 
     * @param stdClass $order
     * @param integer $supplierId
     * 
     * @return DOMNode sdi:supplier node
     * @since 4.3.0
     */
    private function getSupplier($order, $supplierId){
        $supplier = $this->response->createElementNS(self::nsSdi, 'sdi:supplier');

        $supplier->appendChild($this->getOrganism($supplierId));
        
        if($order->pricing_order !== null){
            $pricing = $this->response->createElementNS(self::nsSdi, 'sdi:pricing');
            
            $query = $this->db->getQuery(true)
                    ->select('pos.*')
                    ->from('#__sdi_pricing_order_supplier pos')
                    ->where('pos.pricing_order_id='.(int)$order->pricing_order.' AND pos.supplier_id='.(int)$supplierId);
            $this->db->setQuery($query);
            $orderSupplier = $this->db->loadObject();
            
            $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_internal_free', (bool)$orderSupplier->cfg_internal_free ? 'true' : 'false'));
            $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_fixed_fee_ti', (float)$orderSupplier->cfg_fixed_fee_ti));
            $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_data_free_fixed_fee', (bool)$orderSupplier->cfg_data_free_fixed_fee ? 'true' : 'false'));
            $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cal_fee_ti', (float)$orderSupplier->cal_fee_ti));

            $supplier->appendChild($pricing);
        }
            
        $supplier->appendChild($this->getProducts($order, $supplierId));
        
        return $supplier;
    }

    /**
     * getAddress - Get an address node of specific type
     * 
     * @param integer $addressType type of address 
     * @param integer $recipientId
     * @param string $for
     * 
     * @return DOMNode sdi:contact|sdi:invoice|sdi:delivery node
     * @since 4.3.0
     */
    private function getAddress($addressType, $recipientId, $for = 'client') {
        switch ($addressType) {
            case self::CONTACT:
                $addressContainer = $this->response->createElementNS(self::nsSdi, 'sdi:contact');
                break;
            case self::BILLING:
                $addressContainer = $this->response->createElementNS(self::nsSdi, 'sdi:invoice');
                break;
            case self::DELIVERY:
                $addressContainer = $this->response->createElementNS(self::nsSdi, 'sdi:delivery');
                break;
        }
        
        $address = $this->response->createElementNS(self::nsSdi, 'sdi:address');

        $query = $this->db->getQuery(true)
                ->select('a.firstname, a.lastname, a.address, a.addresscomplement, a.postalcode, a.postalbox, a.locality, a.email, a.phone, a.mobile, a.fax')
                ->select('o.acronym, ' . $this->db->quoteName('o.name') )
                ->select('c.iso2 country_iso')
                ->leftJoin('#__sdi_organism o on a.organism_id = o.id')
                ->leftJoin('#__sdi_sys_country c on c.id = a.country_id')
                ->from('#__sdi_address a ');
        switch($for){
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

        if(!empty($addressdata)){
            $this->addAddressData($address, $addressdata);
        }
        
        $addressContainer->appendChild($address);

        return $addressContainer;
    }
    
    /**
     * addAddressData - populate the sdi:address node
     * 
     * @param DOMNode $address
     * @param stdClass $addressdata
     * 
     * @return void
     * @since 4.3.0
     */
    private function addAddressData(&$address, $addressdata){
        $address->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:organismacronym', $addressdata->acronym));
        $address->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:organismname', $addressdata->name));
        $address->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:agentfirstname', $addressdata->firstname));
        $address->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:agentlastname', $addressdata->lastname));
        $address->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:addressstreet1', $addressdata->address));
        $address->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:addressstreet2', $addressdata->addresscomplement));
        $address->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:zip', $addressdata->postalcode));
        $address->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:pobox', $addressdata->postalbox));
        $address->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:locality', $addressdata->locality));
        $address->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:country', $addressdata->country_iso));
        $address->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:email', $addressdata->email));
        $address->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:mobile', $addressdata->mobile));
        $address->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:phone', $addressdata->phone));
        $address->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:fax', $addressdata->fax));
    }
    
    /**
     * getOrganism - get an organism node
     * 
     * @param integer $id
     * 
     * @return DOMNode sdi:organism node
     * @since 4.3.0
     */
    private function getOrganism($id){
        $this->db->setQuery($this->db->getQuery(true)
                ->select('o.id, o.guid, o.acronym, o.website, o.name')
                ->from('#__sdi_organism o')
                ->where('o.id = ' . $id));
        $result = $this->db->loadObject();
        
        $organism = $this->response->createElementNS(self::nsSdi, 'sdi:organism');
        
        $this->addAttribute($organism, 'id', $result->id);
        $this->addAttribute($organism, 'guid', $result->guid);
        
        $organism->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:name', $result->name));
        $organism->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:acronym', $result->acronym));
        $organism->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:website', $result->website));
        
        $organism->appendChild($this->getCategories($result->id));
        
        $organism->appendChild($this->getAddress(self::CONTACT, $id, 'organism'));
        $organism->appendChild($this->getAddress(self::BILLING, $id, 'organism'));
        $organism->appendChild($this->getAddress(self::DELIVERY, $id, 'organism'));
        
        return $organism;
    }
    
    /**
     * getClientOrganism - get an organism node from user id
     * 
     * @param integer $userId
     * 
     * @return DOMNode ORGANISM node
     * @since 4.3.0
     */
    private function getClientOrganism($userId){
        $this->db->setQuery($this->db->getQuery(true)
                ->select('uro.organism_id')
                ->from('#__sdi_user_role_organism uro')
                ->where('uro.user_id = ' . $userId)
                ->where('uro.role_id=1'));
        $id = $this->db->loadResult();
        return $this->getOrganism($id);
    }

    /**
     * getClient - get the client node from an order object
     * 
     * @param stdClass $order
     * 
     * @return DOMNode sdi:client node
     * @since 4.3.0
     */
    private function getClient($order){
        $client = $this->response->createElementNS(self::nsSdi, 'sdi:client');
        
        $this->addAttribute($client, 'id', $order->user_id);
        $this->addAttribute($client, 'guid', $order->user_guid);
        
        $client->appendChild($this->response->createElementNS(self::nsSdi, 'name', $order->user_name));
        
        $client->appendChild($this->getAddress(self::CONTACT, $order->user_id));
        $client->appendChild($this->getAddress(self::BILLING, $order->user_id));
        $client->appendChild($this->getAddress(self::DELIVERY, $order->user_id));
        
        $client->appendChild($this->getClientOrganism($order->user_id));
        
        return $client;
    }

    /**
     * getTierce - get a tierce node from its id
     * 
     * @param integer $id
     * 
     * @return DOMNode sdi:tierce node
     * @since 4.3.0
     */
    private function getTierce($id = 0){
        $tierce = $this->response->createElementNS(self::nsSdi, 'sdi:tierce');
        if($id != 0){
            $tierce->appendChild($this->getOrganism($id));
        }
            
        return $tierce;
    }

    /**
     * getCategories - get an organism's categorie's list
     * called from getClient and getTierce
     * 
     * @param integer $organism_id
     * 
     * @return DOMNode sdi:categories node
     * @since 4.3.0
     */
    private function getCategories($organism_id = 0){
        $categories = $this->response->createElementNS(self::nsSdi, 'sdi:categories');
        
        $query = $this->db->getQuery(true);
        $query->select('c.id, c.guid, c.name, c.alias')
                ->from('#__sdi_organism_category oc')
                ->join('LEFT', '#__sdi_category c ON c.id=oc.category_id')
                ->where('oc.organism_id='.(int) $organism_id);
        $this->db->setQuery($query);

        foreach($this->db->loadObjectList() as $cat){
            $category = $this->response->createElementNS(self::nsSdi, 'sdi:category');
            
            $this->addAttribute($category, 'id', $cat->id);
            $this->addAttribute($category, 'guid', $cat->guid);
            $this->addAttribute($category, 'alias', $cat->alias);
            
            $category->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:name', $cat->name));

            $categories->appendChild($category);
        }
        
        return $categories;
    }

    /**
     * getProducts - get a products node
     * 
     * @param stdClass $order
     * @param integer $supplierId
     * 
     * @return DOMNode sdi:products node
     * @since 4.3.0
     */
    private function getProducts($order, $supplierId){
        $products = $this->response->createElementNS(self::nsSdi, 'sdi:products');
        
        $this->db->setQuery($this->db->getQuery(true)
                ->select('d.id as product_id, d.guid, d.name')
                ->select('m.id as metadata_id, m.guid as metadata_guid')
                ->select('od.id as od_id, od.guid as od_guid')
                ->select('posp.guid as posp_guid, posp.pricing_id, posp.cfg_pct_category_supplier_discount, posp.ind_lbl_category_supplier_discount, posp.cal_amount_data_te, posp.cal_total_amount_te, posp.cal_total_amount_ti, posp.cal_total_rebate_ti')
                ->select('pospp.pricing_profile_id, pospp.pricing_profile_name, pospp.cfg_fixed_fee, pospp.cfg_surface_rate, pospp.cfg_min_fee, pospp.cfg_max_fee, pospp.cfg_pct_category_profile_discount, pospp.ind_lbl_category_profile_discount')
                ->select('pp.guid as pricing_profile_guid')
                ->from('#__sdi_order_diffusion od')
                ->innerJoin('#__sdi_order o ON o.id=od.order_id')
                ->innerJoin('#__sdi_sys_ordertype ot ON ot.id=o.ordertype_id')
                ->innerJoin('#__sdi_diffusion d ON d.id=od.diffusion_id')
                ->innerJoin('#__sdi_version v ON v.id=d.version_id')
                ->innerJoin('#__sdi_resource r ON r.id=v.resource_id')
                ->innerJoin('#__sdi_metadata m ON m.version_id=v.id')
                ->leftJoin('#__sdi_pricing_order po ON po.order_id=od.order_id')
                ->leftJoin('#__sdi_pricing_order_supplier pos ON pos.pricing_order_id=po.id')
                ->leftJoin('#__sdi_pricing_order_supplier_product posp ON posp.product_id=d.id AND posp.pricing_order_supplier_id=pos.id')
                ->leftJoin('#__sdi_pricing_order_supplier_product_profile pospp ON pospp.pricing_order_supplier_product_id=posp.id')
                ->leftJoin('#__sdi_pricing_profile pp ON pp.id=pospp.pricing_profile_id')
                ->where('o.id='.(int)$order->id)
                ->where('r.organism_id='.(int)$supplierId)
                ->where('od.productstate_id IN ('.implode(',', $this->states).')'));
        
        $orderProducts = $this->db->loadObjectList();
        
        foreach($orderProducts as $orderProduct){
            $products->appendChild($this->getProduct($order, $orderProduct));
        }
        
        return $products;
    }

    /**
     * getProduct - get a product node
     * 
     * @param stdClass $order
     * @param stdClass $orderProduct
     * 
     * @return DOMNode sdi:product node
     * @since 4.3.0
     */
    private function getProduct($order, $orderProduct){
        $root = $this->response->createElementNS(self::nsSdi, 'sdi:product');
        
        $this->addAttribute($root, 'id', $orderProduct->product_id);
        $this->addAttribute($root, 'guid', $orderProduct->guid);
        
        $root->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:name', $orderProduct->name));
        
        if($order->pricing_order !== null){
            $root->appendChild($this->getPricing($orderProduct));
        }
        
        $root->appendChild($this->getProductProperties($orderProduct));
        
        $root->appendChild($this->getProductMetadata($orderProduct));
        
        $query = $this->db->getQuery(true);

        $query->update('#__sdi_order_diffusion ');
        $query->set('productstate_id = ' . (int)self::PRODUCTSTATE_AWAIT);
        $query->where('id = ' . (int)$orderProduct->od_id);

        $this->db->setQuery($query);
        $this->db->execute();
        
        return $root;
    }
    
    /**
     * getPricing - get a pricing node
     * 
     * @param stdClass $product
     * 
     * @return DOMNode sdi:pricing node
     * @since 4.3.0
     */
    private function getPricing($product){
        $pricing = $this->response->createElementNS(self::nsSdi, 'sdi:pricing');
        
        switch($product->pricing_id){
            case 1: // free product
                $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_type', 'free'));
                break;
            
            case 2: // fee without profile
                $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_type', 'fee'));
                break;
            
            case 3: // fee with profile
                $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_type', 'profile'));
                
                $pricing->appendChild($this->getPricingProfile($product));
                
                $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cal_amount_data_te', $product->cal_amount_data_te));
                $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_pct_category_profile_discount', $product->cfg_pct_category_profile_discount));
                $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:ind_lbl_category_profile_discount', $product->ind_lbl_category_profile_discount));
                $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_pct_category_supplier_discount', $product->cfg_pct_category_supplier_discount));
                $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:ind_lbl_category_supplier_discount', $product->ind_lbl_category_supplier_discount));
                $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cal_total_amount_te', $product->cal_total_amount_te));
                $pricing->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cal_total_amount_ti', $product->cal_total_amount_ti));
                break;
        }
        return $pricing;
    }
    
    /**
     * getPricingProfile - get the pricing profile
     * 
     * @param stdClass $product
     * 
     * @return DOMNode sdi:profile node
     * @since 4.3.0
     */
    private function getPricingProfile($product){
        $profile = $this->response->createElementNS(self::nsSdi, 'sdi:profile');
        
        $this->addAttribute($profile, 'id', $product->pricing_profile_id);
        $this->addAttribute($profile, 'guid', $product->pricing_profile_guid);
        
        $profile->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:name', $product->pricing_profile_name));
        $profile->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_fixed_fee', $product->cfg_fixed_fee));
        $surfaceRate = $this->response->createElementNS(self::nsSdi, 'sdi:cfg_surface_rate', $product->cfg_surface_rate);
        $this->addAttribute($surfaceRate, 'unit', 'currency per km2');
        $profile->appendChild($surfaceRate);
        $profile->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_min_fee', $product->cfg_min_fee));
        $profile->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:cfg_max_fee', $product->cfg_max_fee));
        
        return $profile;
    }
    
    /**
     * getProductMetadata - get the metadata
     * 
     * @param stdClass $product
     * 
     * @return DOMNode sdi:metadata node
     * @since 4.3.0
     */
    private function getProductMetadata($product){
        $metadata = $this->response->createElementNS(self::nsSdi, 'sdi:metadata');
        $this->addAttribute($metadata, 'id', $product->metadata_id);
        $this->addAttribute($metadata, 'guid', $product->metadata_guid);
        
        $requestFolder = JPATH_BASE . JComponentHelper::getParams('com_easysdi_shop')->get('orderrequestFolder').'/';
        
        $xmlStr = file_exists($requestFolder.$product->od_guid.'.xml') ? base64_encode(file_get_contents($requestFolder.$product->od_guid.'.xml')) : '';
        $metadata->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:xml', $xmlStr));
        $pdfStr = file_exists($requestFolder.$product->od_guid.'.pdf') ? base64_encode(file_get_contents($requestFolder.$product->od_guid.'.pdf')) : '';
        $metadata->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:pdf', $pdfStr));
        return $metadata;
    }

    /**
     * getProductProperties - get a properties node
     * 
     * @param stdClass $orderProduct
     * 
     * @return DOMNode sdi:properties node
     * @since 4.3.0
     */
    private function getProductProperties($orderProduct) {
        $properties = $this->response->createElementNS(self::nsSdi, 'sdi:properties');
        
        $query = $this->db->getQuery(true)
                ->select('p.id, p.guid, ' . $this->db->quoteName('p.alias') . ' as palias, ' . $this->db->quoteName('pv.alias') . ' as pvalias')
                ->from('#__sdi_property p')
                ->innerJoin('#__sdi_propertyvalue pv ON pv.property_id=p.id')
                ->innerJoin('#__sdi_order_propertyvalue opv ON opv.propertyvalue_id=pv.id')
                ->where('opv.orderdiffusion_id='.(int)$orderProduct->od_id);

        $this->db->setQuery($query);
        $propertiesdata = $this->db->loadObjectList();

        foreach($propertiesdata as $propertydata){
            $property = $this->response->createElementNS(self::nsSdi, 'sdi:property');
            
            $this->addAttribute($property, 'id', $propertydata->id);
            $this->addAttribute($property, 'alias', $propertydata->palias);
            $this->addAttribute($property, 'guid', $propertydata->guid);
            $property->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:value', $propertydata->pvalias));

            $properties->appendChild($property);
        }

        return $properties;
    }

    /**
     * getPerimeter - Get a perimeter node
     * 
     * @param stdClass $order
     * 
     * @return DOMNode sdi:perimeter node
     * @since 4.3.0
     */
    private function getPerimeter($order){
        $perimeter = $this->response->createElementNS(self::nsSdi, 'sdi:perimeter');
        
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
        
        $surface = $this->response->createElementNS(self::nsSdi, 'sdi:surface', $order->surface);
        $this->addAttribute($surface, 'unit', 'm2');
        $perimeter->appendChild($surface);
        
        if(!empty($order->level)){
            $indoorlevel = $this->response->createElementNS(self::nsSdi, 'sdi:indoorlevel');
            $level = json_decode($order->level);
            $this->addAttribute($indoorlevel, 'code', $level->code);
            $this->addAttribute($indoorlevel, 'label', $level->label);
            $perimeter->appendChild($indoorlevel);
        }

        $contentsnode = $this->response->createElementNS(self::nsSdi, 'sdi:contents');
        foreach($contents as $content){
            $contentsnode->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:content', $content->perimeter_value));
        }
        $perimeter->appendChild($contentsnode);
        
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
     * 
     * call getException if the product isn't in the good state
     * call sendResponse to send the result
     */
    public function setProduct(){
        $this->authentication();
            
        $this->loadXML(JFactory::getApplication()->input->get('xml', null, 'raw'));

        $this->schemaValidation(self::xsiSetProductParameters, 1);

        // Open an SQL Transaction
        //$this->db->transactionStart();
        try {
            $this->db->transactionStart();
        } catch (Exception $exc) {
            $this->db->connect();
            $driver_begin_transaction = $this->db->name . '_begin_transaction';
            $driver_begin_transaction($this->db->getConnection());
        }
        $this->transaction = true;

        $this->product = $this->request->getElementsByTagNameNS(self::nsSdi, 'product')->item(0);

        // get the diffusion object
        $diffusion = $this->getDiffusionByGuid($this->product->getAttribute('guid'));

        // get the order object
        $order = $this->getOrderByGuid($this->product->getElementsByTagNameNS(self::nsSdi, 'order')->item(0)->nodeValue);

        // get the order_diffusion object
        $orderDiffusion = $this->getOrderDiffusion($order->id, $diffusion->id);

        // check if the current organism own the given product, if an organism account is used
        if($this->userType == 'organism'){
            $this->checkOwnership($orderDiffusion->diffusion_id);
        }

        // check the state of the order_diffusion (should be equals to AWAIT to be updatable)
        if($orderDiffusion->productstate_id != self::PRODUCTSTATE_AWAIT){
            //throw a resource conflict exception
            $this->getException(409, 'The product you are trying to update has already been updated');
        }

        // try to get pricing data
        $po = $this->getPricingOrder($order->id);
        // init other pricing var
        $pos = null;
        $posp = null;
        if($po !== null){ // pricing order is defined, then load pricing order supplier and pricing order supplier product data
            $posp = $this->getPricingOrderSupplierProduct($diffusion->id, $po->id);
            $pos = $this->getPricingOrderSupplier($posp->pricing_order_supplier_id);
        }

        //all is fine
        $this->updateOrderDiffusion($orderDiffusion, $order, $po, $pos, $posp);
        
        // prepare response
        $this->setProductSucceeded($order, $diffusion);

        // schema validation
        $this->schemaValidation(self::xsiSetProduct, 0);
        
        // Commit the SQL Transaction
        $this->db->transactionCommit();
        
        $this->sendResponse();
    }
    
    /**
     * getDiffusionByGuid - retrieve a diffusion object from its guid
     * 
     * @param string $guid
     * 
     * @return stdClass diffusion object
     * @since 4.3.0
     * 
     * call getException if the diffusion cannot be loaded
     */
    private function getDiffusionByGuid($guid){
        $diffusionModel = $this->getModel('Diffusion', 'Easysdi_shopModel');
        $diffusion = $diffusionModel->getTable();
        if(!($diffusion->load(array('guid' => $guid)))){
            $this->getException(500, 'Cannot load the requested product');
        }
        return $diffusion;
    }
    
    /**
     * getOrderByGuid - retrieve an order object from its guid
     * 
     * @param string $guid
     * 
     * @return stdClass order object
     * @since 4.3.0
     * 
     * call getException if the order cannot be loaded
     */
    private function getOrderByGuid($guid){
        $orderModel = $this->getModel('Order', 'Easysdi_shopModel');
        $order = $orderModel->getTable();
        if(!($order->load(array('guid' => $guid)))){
            $this->getException(500, 'Cannot load the requested order');
        }
        return $order;
    }
    
    /**
     * getOrderDiffusion - retrieve an orderdiffusion object from the order and diffusion ids
     * 
     * @param integer $oId
     * @param integer $dId
     * 
     * @return stdClass orderdiffusion object
     * @since 4.3.0
     * 
     * call getException the couple product/order is not valid
     */
    private function getOrderDiffusion($oId, $dId){
        $query = $this->db->getQuery(true)
                ->select('od.*')
                ->from('#__sdi_order_diffusion od')
                ->where('od.order_id='.(int)$oId)
                ->where('od.diffusion_id='.(int)$dId);
        $this->db->setQuery($query);
        $orderDiffusion = $this->db->loadObject();

        // check the existence of the order_diffusion
        if($orderDiffusion == null){
            //throw a product/order integrity exception
            $this->getException(409, 'Couple product/order integrity violation');
        }
        return $orderDiffusion;
    }
    
    /**
     * checkOwnerShip - check if the ownership
     * 
     * @param integer $dId
     * 
     * @return void
     * @since 4.3.0
     * 
     * call getException if the product isn't owned by the current organism
     */
    private function checkOwnership($dId){
        $query = $this->db->getQuery(true)
                ->select('r.organism_id')
                ->from('#__sdi_resource r')
                ->innerJoin('#__sdi_version v ON v.resource_id=r.id')
                ->innerJoin('#__sdi_diffusion d ON d.version_id=v.id')
                ->where('d.id='.(int)$dId)
                ->where('r.organism_id='.(int)$this->organism->id);
        $this->db->setQuery($query);
        $this->db->execute();

        if($this->db->getNumRows() == 0){
            //throw a product/organism integrity exception
            $this->getException(403, 'You cannot update this product');
        }
    }
    
    /**
     * getPricingOrder - retrieve a pricingorder object from the order's id
     * 
     * @param integer $oId
     * 
     * @return stdClass pricingorder object
     * @since 4.3.0
     */
    private function getPricingOrder($oId){
        $poModel = $this->getModel('PricingOrder', 'Easysdi_shopModel');
        $po = $poModel->getTable();
        $po->load(array('order_id' => $oId));
        return $po;
    }
    
    /**
     * getPricingOrderSupplierProduct - retrieve a pricingordersupplierproduct object
     * 
     * @param integer $dId
     * @param integer $poId
     * 
     * @return stdClass pricingordersupplierproduct object
     * @since 4.3.0
     * 
     * call getException if the pricingordersupplierproduct cannot be loaded
     */
    private function getPricingOrderSupplierProduct($dId, $poId){
        $pospModel = $this->getModel('PricingOrderSupplierProduct', 'Easysdi_shopModel');
        $posp = $pospModel->getTable();
        
        $this->db->setQuery($this->db->getQuery(true)
                ->select('posp.id')
                ->from('#__sdi_pricing_order_supplier_product posp')
                ->innerJoin('#__sdi_pricing_order_supplier pos ON pos.id=posp.pricing_order_supplier_id')
                ->where('posp.product_id='.(int)$dId)
                ->where('pos.pricing_order_id='.(int)$poId));
        
        $pospId = $this->db->loadResult();
        if($pospId == null || !($posp->load(array('id' => $pospId)))){
            $this->getException(500, 'Cannot load the requested pricing product');
        }
        return $posp;
    }
    
    /**
     * getPricingOrderSupplier - retrieve a pricingordersupplier from its id
     * 
     * @param integer $posId
     * 
     * @return stdClass pricingordersupplier object
     * @since 4.3.0
     * 
     * call getException if the pricingordersupplier cannot be loaded
     */
    private function getPricingOrderSupplier($posId){
        $posModel = $this->getModel('PricingOrderSupplier', 'Easysdi_shopModel');
        $pos = $posModel->getTable();
        if(!($pos->load(array('id' => $posId)))){
            $this->getException(500, 'Cannot load the requested pricing supplier');
        }
        return $pos;
    }
    
    /**
     * convertSize - convert the given size to size in octet according to the given unit
     * 
     * @return string
     * @since 4.3.0
     * 
     * call getException if the unit size is not recognized
     */
    private function convertSize(){
        $sizeNode = $this->product->getElementsByTagNameNS(self::nsSdi, 'size')->item(0);
        $unit = $sizeNode->getAttribute('unit');
        $size = $sizeNode->nodeValue;
        
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
                $this->getException(409, 'Unit size not recognized');
        }
    }
    
    /**
     * storeFileLocally - save the file send by a supplier
     * 
     * @param string $od_guid
     * @param string $o_guid
     * 
     * @return array an array of the filename and filesize
     * @since 4.3.0
     * 
     * call getException if the folder cannot be created or if the file cannot be saved
     */
    private function storeFileLocally($od_guid, $o_guid){
        if($_FILES['file']['error']>0){
            //throw an upload exception
            $this->getException(500, $_FILES['file']['error']);
        }
        
        $extractsFilesPath = JPATH_BASE . JComponentHelper::getParams('com_easysdi_shop')->get('orderresponseFolder') . '/' . $o_guid;
        if(!file_exists($extractsFilesPath)){
            if(!mkdir($extractsFilesPath, 0755, true)){
                //throw a folder creation exception
                $this->getException(500, 'Cannot create the required folder');
            }
        }
        
        $file = $extractsFilesPath.$od_guid.'.zip';
        if(!move_uploaded_file($_FILES['file']['tmp_name'], $file)){
            //throw an upload exception
            $this->getException(500, 'Cannot save uploaded file');
        }
        
        return array($file, $_FILES['file']['size']);
    }
    
    /**
     * updateOrderDiffusion - update the datatable #__sdi_order_diffusion
     * 
     * @param stdClass $od
     * @param stdClass $order
     * @param stdClass $po
     * @param stdClass $pos
     * @param stdClass $posp
     * 
     * @return void
     * @since 4.3.0
     * 
     * call getException if the orderdiffusion cannot be updated
     */
    private function  updateOrderDiffusion($od, $order, $po = null, $pos = null, $posp = null){
        $updatePricing = false;
        
        $query = $this->db->getQuery(true)
                ->update('#__sdi_order_diffusion')
                ->set('completed='.$this->db->quote(date("Y-m-d H:i:s")))
                ->where('id='.(int)$od->id);
        
        // set productstate
        $this->setProductState($query, $posp, $updatePricing);
        
        //set remark
        if($this->product->getElementsByTagNameNS(self::nsSdi, 'remark')->length>0){
            $query->set('remark='.$query->quote($this->product->getElementsByTagNameNS(self::nsSdi, 'remark')->item(0)->nodeValue));
        }
        
        //set fee
        if($this->product->getElementsByTagNameNS(self::nsSdi, 'fee')->length>0){
            $this->setFee($query, $posp, $updatePricing, (float)$this->product->getElementsByTagNameNS(self::nsSdi, 'fee')->item(0)->nodeValue);
        }
        
        //set storage
        if($this->product->getElementsByTagNameNS(self::nsSdi, 'storage')->length>0){
            $this->setStorage($query, $od, $order);
        }
        
        $this->db->setQuery($query);
        
        if($this->db->execute()){
            $updatePricing ? $this->updatePricing($posp, $pos, $po) : $this->changeOrderState($od->order_id);
        }//else throw a db exception
        else{
            $this->getException(500, 'Cannot update order diffusion');
        }
    }
    
    /**
     * setProductSucceeded - build the response for a setProduct success
     * 
     * @param stdClass $order
     * @param stdClass $diffusion
     * 
     * @return void
     * @since 4.3.0
     */
    private function setProductSucceeded($order, $diffusion){
        $this->response = new DOMDocument('1.0', 'utf-8');

        $root = $this->response->createElementNS(self::nsSdi, 'sdi:success');
        $root->setAttributeNS(self::xmlnsXsi, 'xsi:schemaLocation', self::xsiSetProduct);
        
        $code = 200;
        $root->appendChild($this->response->createElementNS(self::nsSdi,'sdi:code', $code));
        $root->appendChild($this->response->createElementNS(self::nsSdi,'sdi:message', $this->HTTPSTATUS[$code]));
        
        $orderNode = $this->response->createElementNS(self::nsSdi,'sdi:order');
        $orderNode->setAttribute('guid', $order->guid);
        $orderNode->appendChild($this->response->createElementNS(self::nsSdi,'sdi:name', $order->name));
        $root->appendChild($orderNode);
        
        $productNode = $this->response->createElementNS(self::nsSdi,'sdi:product');
        $productNode->setAttribute('guid', $diffusion->guid);
        $productNode->appendChild($this->response->createElementNS(self::nsSdi,'sdi:name', $diffusion->name));
        $root->appendChild($productNode);

        $this->response->appendChild($root);
    }
    
    /**
     * setProductState - specify the product's update's query
     * 
     * @param JDatabaseQuery $query
     * @param stdClass $posp
     * @param boolean $updatePricing
     * 
     * @return void
     * @since 4.3.0
     * 
     * call getException if the productstate is not recognized
     */
    private function setProductState(&$query, &$posp, &$updatePricing){
        switch($this->product->getElementsByTagNameNS(self::nsSdi, 'state')->item(0)->nodeValue){
            case 'available':
                $query->set('productstate_id='.(int)self::PRODUCTSTATE_AVAILABLE);
                break;
            
            case 'rejected':
                $query->set('productstate_id='.(int)self::PRODUCTSTATE_REJECTED_SUPPLIER);
                
                // update pricing data if exist
                if($posp !== null){
                    $updatePricing = true;
                    $posp->cal_total_amount_ti = 0;
                }
                break;
            
            default:
                //throw an unpredictable case exception - should never fall in this case with xsd validation
                $this->getException(409, 'Product state not recognized');
        }
    }
    
    /**
     * setFee - specify the product's update's query
     * 
     * @param JDatabaseQuery $query
     * @param stdClass $posp
     * @param boolean $updatePricing
     * @param float $fee
     * 
     * @return void
     * @since 4.3.0
     */
    private function setFee(&$query, &$posp, &$updatePricing, $fee){
        $query->set('fee='.$fee);

        // update pricing data if exist
        if($posp !== null){
            $updatePricing = true;
            $posp->cal_total_amount_ti = $fee;
        }
    }
    
    /**
     * setStorage - specify the product's update's query
     * 
     * @param JDatabaseQuery $query
     * @param stdClass $od
     * @param stdClass $order
     * 
     * @return void
     * @since 4.3.0
     * 
     * call getException if the productstorage is not recognized
     */
    private function setStorage(&$query, $od, $order){
        switch($this->product->getElementsByTagNameNS(self::nsSdi, 'storage')->item(0)->nodeValue){
            case 'local':
                //store the file and get back the file(path) and its size
                list($file, $size) = $this->storeFileLocally($od->guid, $order->guid);

                $query->set('storage_id='.(int)self::EXTRACTSTORAGE_LOCAL)
                    ->set('file='.$query->quote($file))
                    ->set('size='.$size);
                break;

            case 'remote':
                $query->set('storage_id='.(int)self::EXTRACTSTORAGE_REMOTE)
                    ->set('file='.$query->quote($this->product->getElementsByTagNameNS(self::nsSdi, 'fileurl')->item(0)->nodeValue))
                    ->set('size='.(int)$this->convertSize());
                break;

            default:
                //throw an unpredictable case exception - should never fall in this case with xsd validation
                $this->getException(409, 'Product storage not recognized');
        }

        $query->set('displayName='.$query->quote($this->product->getElementsByTagNameNS(self::nsSdi, 'filename')->item(0)->nodeValue));
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
     * 
     * call getException if pricingordersupplierproduct, pricingordersupplier or pricingorder object cannot be updated
     */
    private function updatePricing($posp, $pos, $po){
        if(!$posp->save(array())){
            $this->getException(500, 'Cannot update pricing order supplier product');
        }
        
        $this->db->setQuery($this->db->getQuery(true)
                ->select('SUM(posp.cal_total_amount_ti) ctat')
                ->from('#__sdi_pricing_order_supplier_product posp')
                ->innerJoin('#__sdi_pricing_order_supplier pos ON pos.id=posp.pricing_order_supplier_id')
                ->where('posp.pricing_order_supplier_id='.(int)$pos->id));

        $pos->cal_total_amount_ti = $this->db->loadResult();
        
        if(!$pos->save(array())){
            $this->getException(500, 'Cannot update pricing order supplier');
        }
        
        $this->db->setQuery($this->db->getQuery(true)
                ->select('SUM(posp.cal_total_amount_ti) ctat')
                ->from('#__sdi_pricing_order_supplier_product posp')
                ->innerJoin('#__sdi_pricing_order_supplier pos ON pos.id=posp.pricing_order_supplier_id')
                ->where('pos.pricing_order_id='.(int)$po->id));

        $po->cal_total_amount_ti = $this->db->loadResult();
        
        if(!$po->save(array())){
            $this->getException(500, 'Cannot update pricing order');
        }
        
        $this->changeOrderState($po->order_id);
    }

    /**
     * changeOrderState - Dynamically changes the statue of the order.
     * 
     * @param integer $orderId Id of the order.
     * 
     * @return void
     * @since 4.3.0
     */
    private function changeOrderState($orderId) {
        $this->db->setQuery($this->db->getQuery(true)
                ->select('id')->from('#__sdi_order_diffusion')->where('order_id='.(int)$orderId));
        $total = $this->db->getNumRows($this->db->execute());
        
        $this->db->setQuery($this->db->getQuery(true)
                ->select('id')->from('#__sdi_order_diffusion')->where('order_id='.(int)$orderId)->where('productstate_id='.self::PRODUCTSTATE_AWAIT));
        $await = $this->db->getNumRows($this->db->execute());

        $this->db->setQuery($this->db->getQuery(true)
                ->select('id')->from('#__sdi_order_diffusion')->where('order_id='.(int)$orderId)->where('productstate_id='.self::PRODUCTSTATE_AVAILABLE));
        $available = $this->db->getNumRows($this->db->execute());

        $this->db->setQuery($this->db->getQuery(true)
                ->select('id')->from('#__sdi_order_diffusion')->where('order_id='.(int)$orderId)->where('productstate_id='.self::PRODUCTSTATE_REJECTED_SUPPLIER));
        $rejected = $this->db->getNumRows($this->db->execute());

        $orderstate = $this->chooseOrderState($total, $await, $available, $rejected);

        if($orderstate > 0){
            $query = $this->db->getQuery(true)
                    ->update('#__sdi_order')->set('orderstate_id='.$orderstate);
            if($orderstate == self::ORDERSTATE_FINISH){
                $query->set('completed='.$query->quote(date("Y-m-d H:i:s")));
            }
            $query->where('id='.(int)$orderId);

            $this->db->setQuery($query);
            $this->db->execute();
        }
    }
    
    /**
     * chooseOrderState - return the correct orderState according to the given params
     * 
     * @param integer $total
     * @param integer $await
     * @param integer $available
     * @param integer $rejected
     * 
     * @return integer
     * @since 4.3.0
     */
    private function chooseOrderState($total, $await, $available, $rejected){
        if($total == $rejected){
            return self::ORDERSTATE_REJECTED_SUPPLIER;
        }
        
        if($available == $total || $available+$rejected==$total){
            return self::ORDERSTATE_FINISH;
        }
        
        if($available>0 || $rejected>0){
            return self::ORDERSTATE_PROGRESS;
        }
        
        if($await>0){
            return self::ORDERSTATE_AWAIT;
        }
        
        return 0;
    }

}
