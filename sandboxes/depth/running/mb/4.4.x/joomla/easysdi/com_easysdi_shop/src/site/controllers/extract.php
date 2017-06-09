<?php

/**
 * Extract - WebService designed to getOrders and setProduct
 * based on the old webservice (rest)
 * 
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_COMPONENT . '/helpers/easysdi_shop.php';

class Easysdi_shopControllerExtract extends Easysdi_shopController {

    // Address type
    const CONTACT = '1';
    const BILLING = '2';
    const DELIVERY = '3';

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

    // Namespace
    const nsSdi = 'http://www.easysdi.org/2011/sdi';
    const xmlnsXsi = 'http://www.w3.org/2001/XMLSchema-instance';
    //XSDs
    const xsdGetOrders = 'getorders.xsd';
    const xsdGetOrdersParameters = 'getordersparameters.xsd';
    const xsdSetProduct = 'setproduct.xsd';
    const xsdSetProductParameters = 'setproductparameters.xsd';
    const xsdException = 'exception.xsd';

    //XSDs local files
    private $xsdLocalGetOrders;
    private $xsdLocalGetOrdersParameters;
    private $xsdLocalSetProduct;
    private $xsdLocalSetProductParameters;
    private $xsdLocalException;
    
    //easisdi.org files
    private $xsdCentralGetOrders;
    private $xsdCentralGetOrdersParameters;
    private $xsdCentralSetProduct;
    private $xsdCentralSetProductParameters;
    private $xsdCentralException;    

    //XSIs
    const xsiBaseURL = 'http://www.easysdi.org/2011/sdi/';

    private $xsiGetOrders;
    private $xsiGetOrdersParameters;
    private $xsiSetProduct;
    private $xsiSetProductParameters;
    private $xsiException;

    /** @var JDatabaseDriver Description */
    private $db;
    private $transaction = false;

    /** @var DOMDocument */
    private $request;

    /** @var DOMDocument  */
    private $response;

    /** @var DOMElement * */
    private $product;
    private $states = array(Easysdi_shopHelper::PRODUCTSTATE_SENT);

    /**
     * __construct
     */
    function __construct() {
        parent::__construct();

        $this->db = JFactory::getDbo();
        $this->request = new DOMDocument('1.0', 'utf-8');
        $this->response = new DOMDocument('1.0', 'utf-8');

        $xsdLocalPathBase = JPATH_ROOT . '/components/com_easysdi_shop/controllers/xsd/extract/';

        //set LOCAL XSDs path
        $this->xsdLocalGetOrders = $xsdLocalPathBase . self::xsdGetOrders;
        $this->xsdLocalGetOrdersParameters = $xsdLocalPathBase . self::xsdGetOrdersParameters;
        $this->xsdLocalSetProduct = $xsdLocalPathBase . self::xsdSetProduct;
        $this->xsdLocalSetProductParameters = $xsdLocalPathBase . self::xsdSetProductParameters;
        $this->xsdLocalException = $xsdLocalPathBase . self::xsdException;

        //set CENTRAL XSDs path
        $xsdCentralPathBase = 'http://www.easysdi.org/schema/4.4.4/';
        $this->xsdCentralGetOrders = $xsdCentralPathBase . self::xsdGetOrders;
        $this->xsdCentralGetOrdersParameters = $xsdCentralPathBase . self::xsdGetOrdersParameters;
        $this->xsdCentralSetProduct = $xsdCentralPathBase . self::xsdSetProduct;
        $this->xsdCentralSetProductParameters = $xsdCentralPathBase . self::xsdSetProductParameters;
        $this->xsdCentralException = $xsdCentralPathBase . self::xsdException;        
        
        //set XSIs path
        $this->xsiGetOrders = self::xsiBaseURL . self::xsdGetOrders;
        $this->xsiGetOrdersParameters = self::xsiBaseURL . self::xsdGetOrdersParameters;
        $this->xsiSetProduct = self::xsiBaseURL . self::xsdSetProduct;
        $this->xsiSetProductParameters = self::xsiBaseURL . self::xsdSetProductParameters;
        $this->xsiException = self::xsiBaseURL . self::xsdException;
    }

    /**
     * authentication - authenticate the user with given credentials
     * 
     * @return void
     * @since 4.3.0
     * 
     * call getException if can't authenticate the user
     */
    private function authentication() {        
        if (!isset($_SERVER['PHP_AUTH_USER']) || !(
                $this->isOrderAccount($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) || $this->isOrganismAccount($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) )
        ) {
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
    private function isOrderAccount($username, $password) {
        $globalUserId = JComponentHelper::getParams('com_easysdi_shop')->get('orderaccount');

        $app = JFactory::getApplication();
        $credentials['username'] = $username; //user entered name
        $credentials['password'] = $password;

        $app->login($credentials);

        $user = JFactory::getUser();

        if ($user->get('id') == $globalUserId) {
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
                        ->where('o.username=' . $this->db->quote($username)));
        $organism = $this->db->loadObject();
        if ($organism !== null) {
            $passwordarray = explode(':', $organism->password);
            $pwdCryp = JUserHelper::getCryptedPassword($password, $passwordarray[1]) . ':' . $passwordarray[1];

            if ($organism->password == $pwdCryp) {
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
    private function sendResponse($code = 200, $response = null) {
        if ($response == null) {
            $response = $this->response;
        }

        //if code is not 200, set the HTTP code to the value of error code
        if ($code != 200) {
            header('HTTP/1.1 ' . $code . ' ' . $this->HTTPSTATUS[$code]);
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
    private function getException($code, $details = '') {

        // Rollback SQL Transaction
        if ($this->transaction) {
            $this->db->transactionRollback();
        }

        if (is_array($details)) {
            $details = implode('<br>', $details);
        }


        $response = new DOMDocument('1.0', 'utf-8');

        $root = $response->createElementNS(self::nsSdi, 'sdi:exception');
        $root->setAttributeNS(self::xmlnsXsi, 'xsi:schemaLocation', $this->xsdCentralException);

        $this->addTextChildNode($root, 'sdi:code', $code);
        $this->addTextChildNode($root, 'sdi:message', $this->HTTPSTATUS[$code]);
        $this->addTextChildNode($root, 'sdi:details', $details);

        $response->appendChild($root);

        $this->schemaValidation($this->xsdLocalException, 0, $response, false);

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
    private function schemaValidation($xsd, $IO, $xml = null, $throwException = true) {
        if ($xml === null) {
            $xml = $IO ? $this->request : $this->response;
        }

        $defaultUseErrors = libxml_use_internal_errors(true);

        if (!@$xml->schemaValidate($xsd)) {
            //throw an xml error exception
            $errors = libxml_get_errors();
            if ($throwException === false) {
                var_dump($errors);
                die();
            }

            if ($IO) { // Input
                $this->getException(400, 'The given XML is not valid. Please consult the XSD : ' . $xsd);
            } else { // Output
                $this->getException(500, print_r($errors, true) . "\n\nOrginal failed XML =" . $xml->saveXML());
            }
        }

        libxml_use_internal_errors($defaultUseErrors);
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
    private function loadXML($xml) {
        if (!$this->request->loadXML($xml, LIBXML_PARSEHUGE)) {
            $this->getException(500, 'Cannot load the XML');
        }
    }

    /*     * ************* */
    /** GET ORDERS * */
    /*     * ************* */

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
    private function addAttribute(&$parent, $attrName, $attrValue) {
        $attribute = $parent->ownerDocument->createAttribute($attrName);
        $attribute->value = $attrValue;
        $parent->appendChild($attribute);
    }

    /**
     * addTextNode - safe way to add a child node with text to a DOMNode to response document
     * this way, text content are correctly escaped for XML by libXML.
     * Nodes will have the default easySDI namespace defined by self::nsSdi
     * 
     * @param DOMNode $parent - the DOMNode which the new node + text node will be added to
     * @param string $nodeName - the node name
     * @param string $nodeText - the node text value
     * 
     * @return void
     * @since 4.4.0*
     */
    private function addTextChildNode(&$parent, $nodeName, $nodeText = null) {
        $newNode = $parent->ownerDocument->createElementNS(self::nsSdi, $nodeName);
        if (isset($nodeText)) {
            $newTextNode = $parent->ownerDocument->createTextNode($nodeText);
            $newNode->appendChild($newTextNode);
        }
        $parent->appendChild($newNode);
    }

    /**
     * addTextToNode - add a textnode to an existing node
     * 
     * @param DOMNode $node - the DOMNode which the attribute will be added to
     * @param string $text - the node text value
     * 
     * @return void
     * @since 4.4.0*
     */
    private function addTextToNode(&$node, $text) {
        $newTextNode = $node->ownerDocument->createTextNode($text);
        $node->appendChild($newTextNode);
    }

    /**
     * getOrdersParametersStates - load the given states
     * 
     * @return void
     * @since 4.3.0
     */
    private function getOrdersParametersStates() {
        $this->states = array(); // as states are given, we reset the default value
        foreach ($this->request->getElementsByTagNameNS(self::nsSdi, 'state') as $state) {
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
    private function getOrdersParametersRestrictionList($mode) {
        $restrictionList = array();

        switch ($mode) {
            case 'sdi:getOrdersByGuids': // get guids
                foreach ($this->request->getElementsByTagNameNS(self::nsSdi, 'guid') as $guid) {
                    array_push($restrictionList, "'" . $guid->nodeValue . "'");
                }
                break;

            case 'sdi:getOrdersByIds': // get ids
                foreach ($this->request->getElementsByTagNameNS(self::nsSdi, 'id') as $id) {
                    array_push($restrictionList, "'" . $id->nodeValue . "'");
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
    private function getOrdersParameters($xml) {
        $this->loadXML($xml);

        $this->schemaValidation($this->xsdLocalGetOrdersParameters, 1);

        $parameters = $this->request->getElementsByTagNameNS(self::nsSdi, 'parameters')->item(0);

        // get mode
        $mode = $parameters->getAttributeNS(self::xmlnsXsi, 'type');

        // get states
        if ($this->request->getElementsByTagNameNS(self::nsSdi, 'state')->length) {
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
    private function getOrdersObjectList($xml) {
        /* DEFAULT INPUTS */
        $mode = 'sdi:getOrders';

        $agg = 'o.id, o.guid, ' . $this->db->quoteName('o.name') . ', o.user_id, '
                . 'o.surface, o.level, u.guid , us.name , o.thirdparty_id, o.sent, ' . $this->db->quoteName('ot.value');
        $agg .= ', po.id , po.cfg_vat, po.cfg_currency, po.cfg_rounding, po.cfg_overall_default_fee_te,'
                . ' po.cfg_free_data_fee, po.cal_fee_ti, po.ind_lbl_category_order_fee';
        // retrieve all orders
        $query = $this->db->getQuery(true)
                ->select('o.id, o.guid, ' . $this->db->quoteName('o.name') . ', o.user_id, '
                        . 'o.surface, o.level, u.guid as user_guid, us.name as user_name, o.thirdparty_id,'
                        . ' o.sent, ' . $this->db->quoteName('ot.value') . ' as ordertype')
                ->select('po.id as pricing_order, po.cfg_vat, po.cfg_currency, '
                        . 'po.cfg_rounding, po.cfg_overall_default_fee_te, po.cfg_free_data_fee, '
                        . 'po.cal_fee_ti, po.ind_lbl_category_order_fee, po.cfg_fee_apply_vat')
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
            $query->where('r.organism_id = ' . (int) $this->organism->id);
        }
        if ($xml !== null) {
            list($mode, $restrictionList) = $this->getOrdersParameters($xml);
            // specify where clause depending on the getOrders mode
            if ($mode === 'sdi:getOrdersByGuids') { // by guids
                $query->where('d.guid IN (' . implode(',', $restrictionList) . ')');
            } elseif ($mode === 'sdi:getOrdersByIds') { // by ids
                $query->where('d.id IN (' . implode(',', $restrictionList) . ')');
            }
        }
        //only orders and drafts in automatic mining
        $query->where('od.productstate_id IN (' . implode(',', $this->states) . ')')
                ->where('d.productmining_id = ' . Easysdi_shopHelper::PRODUCTMININGAUTO)
                ->where('o.ordertype_id IN (' . Easysdi_shopHelper::ORDERTYPE_ORDER . ',' . Easysdi_shopHelper::ORDERTYPE_ESTIMATE . ')');
        
        //only order that are completely saved (avoid partial orders : https://forge.easysdi.org/issues/1252)
        $query->where('o.sent > \'0000-00-00 00:00:00\'');
        
        //group by selected items
        $query->group($agg);

        $this->db->setQuery($query);
        
        //$s = $query->__toString();
        
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
        try {
            $this->db->transactionStart();
        } catch (Exception $exc) {
            $this->db->connect();
            $driver_begin_transaction = $this->db->name . '_begin_transaction';
            $driver_begin_transaction($this->db->getConnection());
        }
        $this->transaction = true;

        $orders = $this->response->createElementNS(self::nsSdi, 'sdi:orders');
        $orders->setAttributeNS(self::xmlnsXsi, 'xsi:schemaLocation', $this->xsdCentralGetOrders);

        $platform = $this->response->createElementNS(self::nsSdi, 'sdi:platform');

        $this->addAttribute($platform, 'name', 'easySDI');
        $this->addAttribute($platform, 'version', sdiFactory::getSdiVersion());
        $this->addAttribute($platform, 'serviceversion', '4.4.4'); //easySDI version of the last modification

        $orders->appendChild($platform);

        foreach ($this->getOrdersObjectList($xml) as $order) {
            $orders->appendChild($this->getOrder($order));
        }

        $this->response->appendChild($orders);

        $this->schemaValidation($this->xsdLocalGetOrders, 0);

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
    private function getOrderBasket($order) {
        $basket = new sdiBasket();
        $basket->loadOrder($order->id);
        $basket->sdiUser = $sdiUser = sdiFactory::getSdiUser($order->user_id);
        if (!empty($order->thirdparty_id)) {
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

        $this->addTextChildNode($root, 'sdi:name', $order->name);

        $root->appendChild($this->getClient($order));
        $root->appendChild($this->getTierce($order->thirdparty_id));
        $root->appendChild($this->getPerimeter($order));

        $basket = $this->getOrderBasket($order);

        $root->appendChild($this->getSuppliers($order, $basket));

        if ($order->pricing_order !== null) {
            $root->appendChild($this->getOrderPricing($order));
        }

        Easysdi_shopHelper::changeOrderState($order->id);

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
    private function getOrderPricing($order) {
        $pricing = $this->response->createElementNS(self::nsSdi, 'sdi:pricing');
        $this->addTextChildNode($pricing, 'sdi:cfg_vat', $order->cfg_vat);
        $this->addTextChildNode($pricing, 'sdi:cfg_currency', $order->cfg_currency);
        $this->addTextChildNode($pricing, 'sdi:cfg_rounding', $order->cfg_rounding);
        $this->addTextChildNode($pricing, 'sdi:cfg_overall_default_fee_te', $order->cfg_overall_default_fee_te);
        $this->addTextChildNode($pricing, 'sdi:cfg_free_data_fee', (bool) $order->cfg_free_data_fee ? 'true' : 'false');
        $this->addTextChildNode($pricing, 'sdi:cfg_fee_apply_vat', (bool) $order->cfg_fee_apply_vat ? 'true' : 'false');        
        $this->addTextChildNode($pricing, 'sdi:cal_fee_ti', $order->cal_fee_ti);
        $this->addTextChildNode($pricing, 'sdi:ind_lbl_category_order_fee', $order->ind_lbl_category_order_fee);
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
    private function getSuppliers($order, $basket) {
        $suppliers = $this->response->createElementNS(self::nsSdi, 'sdi:suppliers');

        foreach (array_keys($basket->extractions) as $supplierId) {
            //skip other organisms if organismaccount is used
            if (!empty($this->organism) && $this->organism->id != $supplierId) {
                continue;
            }
            $supplier = $this->getSupplier($order, $supplierId);
            if($supplier != false)
            {
                $suppliers->appendChild($supplier);
            }
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
    private function getSupplier($order, $supplierId) {
        $products = $this->getProducts($order, $supplierId);        
        if($products == false)
        {
            //No product to extract
            return false;
        }
        $supplier = $this->response->createElementNS(self::nsSdi, 'sdi:supplier');

        $supplier->appendChild($this->getOrganism($supplierId));

        if ($order->pricing_order !== null) {
            $pricing = $this->response->createElementNS(self::nsSdi, 'sdi:pricing');

            $query = $this->db->getQuery(true)
                    ->select('pos.*')
                    ->from('#__sdi_pricing_order_supplier pos')
                    ->where('pos.pricing_order_id=' . (int) $order->pricing_order . ' AND pos.supplier_id=' . (int) $supplierId);
            $this->db->setQuery($query);
            $orderSupplier = $this->db->loadObject();

            $this->addTextChildNode($pricing, 'sdi:cfg_internal_free', (bool) $orderSupplier->cfg_internal_free ? 'true' : 'false');
            $this->addTextChildNode($pricing, 'sdi:cfg_fixed_fee_te', (float) $orderSupplier->cfg_fixed_fee_te);
            $this->addTextChildNode($pricing, 'sdi:cfg_fixed_fee_apply_vat', (bool) $orderSupplier->cfg_fixed_fee_apply_vat ? 'true' : 'false');
            $this->addTextChildNode($pricing, 'sdi:cfg_data_free_fixed_fee', (bool) $orderSupplier->cfg_data_free_fixed_fee ? 'true' : 'false');
            $this->addTextChildNode($pricing, 'sdi:cal_fee_ti', (float) $orderSupplier->cal_fee_ti);

            $supplier->appendChild($pricing);
        }

        $supplier->appendChild($products);

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
                ->select('o.acronym, ' . $this->db->quoteName('o.name'))
                ->select('c.iso2 country_iso')
                ->leftJoin('#__sdi_organism o on a.organism_id = o.id')
                ->leftJoin('#__sdi_sys_country c on c.id = a.country_id')
                ->from('#__sdi_address a ');
        switch ($for) {
            case 'client':
                $query->where('a.user_id = ' . (int) $recipientId);
                break;
            case 'organism':
                $query->where('a.organism_id = ' . (int) $recipientId);
                break;
        }
        $query->where('a.addresstype_id = ' . (int) $addressType);

        $this->db->setQuery($query);
        $addressdata = $this->db->loadObject();

        if (!empty($addressdata)) {
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
    private function addAddressData(&$address, $addressdata) {
        $this->addTextChildNode($address, 'sdi:organismacronym', $addressdata->acronym);
        $this->addTextChildNode($address, 'sdi:organismname', $addressdata->name);
        $this->addTextChildNode($address, 'sdi:agentfirstname', $addressdata->firstname);
        $this->addTextChildNode($address, 'sdi:agentlastname', $addressdata->lastname);
        $this->addTextChildNode($address, 'sdi:addressstreet1', $addressdata->address);
        $this->addTextChildNode($address, 'sdi:addressstreet2', $addressdata->addresscomplement);
        $this->addTextChildNode($address, 'sdi:zip', $addressdata->postalcode);
        $this->addTextChildNode($address, 'sdi:pobox', $addressdata->postalbox);
        $this->addTextChildNode($address, 'sdi:locality', $addressdata->locality);
        $this->addTextChildNode($address, 'sdi:country', $addressdata->country_iso);
        $this->addTextChildNode($address, 'sdi:email', $addressdata->email);
        $this->addTextChildNode($address, 'sdi:mobile', $addressdata->mobile);
        $this->addTextChildNode($address, 'sdi:phone', $addressdata->phone);
        $this->addTextChildNode($address, 'sdi:fax', $addressdata->fax);
    }

    /**
     * getOrganism - get an organism node
     * 
     * @param integer $id
     * 
     * @return DOMNode sdi:organism node
     * @since 4.3.0
     */
    private function getOrganism($id) {
        $this->db->setQuery($this->db->getQuery(true)
                        ->select('o.id, o.guid, o.acronym, o.website, o.name')
                        ->from('#__sdi_organism o')
                        ->where('o.id = ' . $id));
        $result = $this->db->loadObject();

        $organism = $this->response->createElementNS(self::nsSdi, 'sdi:organism');

        $this->addAttribute($organism, 'id', $result->id);
        $this->addAttribute($organism, 'guid', $result->guid);

        $this->addTextChildNode($organism, 'sdi:name', $result->name);
        $this->addTextChildNode($organism, 'sdi:acronym', $result->acronym);
        $this->addTextChildNode($organism, 'sdi:website', $result->website);

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
    private function getClientOrganism($userId) {
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
    private function getClient($order) {
        $client = $this->response->createElementNS(self::nsSdi, 'sdi:client');

        $this->addAttribute($client, 'id', $order->user_id);
        $this->addAttribute($client, 'guid', $order->user_guid);

        $this->addTextChildNode($client, 'sdi:name', $order->user_name);

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
    private function getTierce($id = 0) {
        $tierce = $this->response->createElementNS(self::nsSdi, 'sdi:tierce');
        if ($id != 0) {
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
    private function getCategories($organism_id = 0) {
        $categories = $this->response->createElementNS(self::nsSdi, 'sdi:categories');

        $query = $this->db->getQuery(true);
        $query->select('c.id, c.guid, c.name, c.alias')
                ->from('#__sdi_organism_category oc')
                ->join('LEFT', '#__sdi_category c ON c.id=oc.category_id')
                ->where('oc.organism_id=' . (int) $organism_id);
        $this->db->setQuery($query);

        foreach ($this->db->loadObjectList() as $cat) {
            $category = $this->response->createElementNS(self::nsSdi, 'sdi:category');

            $this->addAttribute($category, 'id', $cat->id);
            $this->addAttribute($category, 'guid', $cat->guid);
            $this->addAttribute($category, 'alias', $cat->alias);

            $this->addTextChildNode($category, 'sdi:name', $cat->name);

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
    private function getProducts($order, $supplierId) {
        $products = $this->response->createElementNS(self::nsSdi, 'sdi:products');

        $this->db->setQuery($this->db->getQuery(true)
                        ->select('d.id as product_id, d.guid, d.name')
                        ->select('m.id as metadata_id, m.guid as metadata_guid')
                        ->select('od.id as od_id, od.guid as od_guid')
                        ->select('posp.guid as posp_guid, posp.pricing_id, posp.cfg_pct_category_supplier_discount, posp.ind_lbl_category_supplier_discount, posp.cal_amount_data_te, posp.cal_total_amount_te, posp.cal_total_amount_ti, posp.cal_total_rebate_ti')
                        ->select('pospp.pricing_profile_id, pospp.pricing_profile_name, pospp.cfg_fixed_fee_te, pospp.cfg_apply_vat, pospp.cfg_surface_rate, pospp.cfg_min_fee, pospp.cfg_max_fee, pospp.cfg_pct_category_profile_discount, pospp.ind_lbl_category_profile_discount')
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
                        ->where('o.id=' . (int) $order->id)
                        ->where('r.organism_id=' . (int) $supplierId)
                        ->where('pos.supplier_id=' . (int) $supplierId)
                        ->where('d.productmining_id = ' . Easysdi_shopHelper::PRODUCTMININGAUTO)
                        ->where('od.productstate_id IN (' . implode(',', $this->states) . ')'));

        $orderProducts = $this->db->loadObjectList();

        if($orderProducts == null || count($orderProducts) == 0)
            return false;
        
        foreach ($orderProducts as $orderProduct) {
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
    private function getProduct($order, $orderProduct) {
        $root = $this->response->createElementNS(self::nsSdi, 'sdi:product');

        $this->addAttribute($root, 'id', $orderProduct->product_id);
        $this->addAttribute($root, 'guid', $orderProduct->guid);

        $this->addTextChildNode($root, 'sdi:name', $orderProduct->name);

        if ($order->pricing_order !== null) {
            $root->appendChild($this->getPricing($orderProduct));
        }

        $root->appendChild($this->getProductProperties($orderProduct));

        $root->appendChild($this->getProductMetadata($order, $orderProduct));

        $query = $this->db->getQuery(true);

        $query->update('#__sdi_order_diffusion ');
        $query->set('productstate_id = ' . (int) Easysdi_shopHelper::PRODUCTSTATE_AWAIT);
        $query->where('id = ' . (int) $orderProduct->od_id);

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
    private function getPricing($product) {
        $pricing = $this->response->createElementNS(self::nsSdi, 'sdi:pricing');

        switch ($product->pricing_id) {
            case 1: // free product
                $this->addTextChildNode($pricing, 'sdi:cfg_type', 'free');
                break;

            case 2: // fee without profile
                $this->addTextChildNode($pricing, 'sdi:cfg_type', 'fee');
                break;

            case 3: // fee with profile
                $this->addTextChildNode($pricing, 'sdi:cfg_type', 'profile');

                $pricing->appendChild($this->getPricingProfile($product));

                $this->addTextChildNode($pricing, 'sdi:cal_amount_data_te', $product->cal_amount_data_te);
                $this->addTextChildNode($pricing, 'sdi:cfg_pct_category_profile_discount', $product->cfg_pct_category_profile_discount);
                $this->addTextChildNode($pricing, 'sdi:ind_lbl_category_profile_discount', $product->ind_lbl_category_profile_discount);
                $this->addTextChildNode($pricing, 'sdi:cfg_pct_category_supplier_discount', $product->cfg_pct_category_supplier_discount);
                $this->addTextChildNode($pricing, 'sdi:ind_lbl_category_supplier_discount', $product->ind_lbl_category_supplier_discount);
                $this->addTextChildNode($pricing, 'sdi:cal_total_amount_te', $product->cal_total_amount_te);
                $this->addTextChildNode($pricing, 'sdi:cal_total_amount_ti', $product->cal_total_amount_ti);
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
    private function getPricingProfile($product) {
        $profile = $this->response->createElementNS(self::nsSdi, 'sdi:profile');

        $this->addAttribute($profile, 'id', $product->pricing_profile_id);
        $this->addAttribute($profile, 'guid', $product->pricing_profile_guid);

        $this->addTextChildNode($profile, 'sdi:name', $product->pricing_profile_name);
        $this->addTextChildNode($profile, 'sdi:cfg_fixed_fee_te', (float)$product->cfg_fixed_fee_te);

        $surfaceRate = $this->response->createElementNS(self::nsSdi, 'sdi:cfg_surface_rate');
        $this->addTextToNode($surfaceRate, $product->cfg_surface_rate);
        $this->addAttribute($surfaceRate, 'unit', 'currency per km2');
        $profile->appendChild($surfaceRate);

        $this->addTextChildNode($profile, 'sdi:cfg_min_fee', $product->cfg_min_fee);
        $this->addTextChildNode($profile, 'sdi:cfg_max_fee', $product->cfg_max_fee);
        $this->addTextChildNode($profile, 'sdi:cfg_apply_vat', (bool)$product->cfg_apply_vat ? 'true' : 'false');

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
    private function getProductMetadata($order, $product) {
        $metadata = $this->response->createElementNS(self::nsSdi, 'sdi:metadata');
        $this->addAttribute($metadata, 'id', $product->metadata_id);
        $this->addAttribute($metadata, 'guid', $product->metadata_guid);

        $requestFolder = JPATH_BASE . JComponentHelper::getParams('com_easysdi_shop')->get('orderrequestFolder') . '/' . $order->id;
        $xmlStr = file_exists($requestFolder . '/' . $product->product_id . '.xml') ? base64_encode(file_get_contents($requestFolder . '/' . $product->product_id . '.xml')) : '';
        $this->addTextChildNode($metadata, 'sdi:xml', $xmlStr);
        $pdfStr = file_exists($requestFolder . '/' . $product->product_id . '.pdf') ? base64_encode(file_get_contents($requestFolder . '/' . $product->product_id . '.pdf')) : '';
        $this->addTextChildNode($metadata, 'sdi:pdf', $pdfStr);
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
                ->where('opv.orderdiffusion_id=' . (int) $orderProduct->od_id);

        $this->db->setQuery($query);
        $propertiesdata = $this->db->loadObjectList();

        foreach ($propertiesdata as $propertydata) {
            $property = $this->response->createElementNS(self::nsSdi, 'sdi:property');

            $this->addAttribute($property, 'id', $propertydata->id);
            $this->addAttribute($property, 'alias', $propertydata->palias);
            $this->addAttribute($property, 'guid', $propertydata->guid);
            $this->addTextChildNode($property, 'sdi:value', $propertydata->pvalias);

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
    private function getPerimeter($order) {
        $perimeter = $this->response->createElementNS(self::nsSdi, 'sdi:perimeter');

        $query = $this->db->getQuery(true);

        $query->select('p.id, p.guid, ' . $this->db->quoteName('p.name') . ' as perimeter_name, p.alias, ' . $this->db->quoteName('op.value') . ' as perimeter_value');
        $query->from('#__sdi_order_perimeter op');
        $query->innerJoin('#__sdi_perimeter p on p.id = op.perimeter_id');
        $query->where('op.order_id = ' . (int) $order->id);

        $this->db->setQuery($query);
        $contents = $this->db->loadObjectList();

        $this->addAttribute($perimeter, 'type', $contents[0]->alias == 'freeperimeter' || $contents[0]->alias == 'myperimeter' ? 'coordinates' : 'values');
        $this->addAttribute($perimeter, 'id', $contents[0]->id);
        $this->addAttribute($perimeter, 'alias', $contents[0]->alias);
        $this->addAttribute($perimeter, 'guid', $contents[0]->guid);

        $surface = $this->response->createElementNS(self::nsSdi, 'sdi:surface');
        $this->addTextToNode($surface, $order->surface);
        $this->addAttribute($surface, 'unit', 'm2');
        $perimeter->appendChild($surface);

        if (!empty($order->level)) {
            $indoorlevel = $this->response->createElementNS(self::nsSdi, 'sdi:indoorlevel');
            $level = json_decode($order->level);
            $this->addAttribute($indoorlevel, 'code', $level->code);
            $this->addAttribute($indoorlevel, 'label', $level->label);
            $perimeter->appendChild($indoorlevel);
        }

        $contentsnode = $this->response->createElementNS(self::nsSdi, 'sdi:contents');
        foreach ($contents as $content) {
            $this->addTextChildNode($contentsnode, 'sdi:content', $content->perimeter_value);
        }
        $perimeter->appendChild($contentsnode);

        return $perimeter;
    }

    /*     * ************** */
    /** SET PRODUCT * */
    /*     * ************** */

    /**
     * setProduct - public method to update a product of an order
     * 
     * @return void
     * @since 4.3.0
     * 
     * call getException if the product isn't in the good state
     * call sendResponse to send the result
     */
    public function setProduct() {
        $this->authentication();

        $this->loadXML(JFactory::getApplication()->input->get('xml', null, 'raw'));

        $this->schemaValidation($this->xsdLocalSetProductParameters, 1);

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
        if ($this->userType == 'organism') {
            $this->checkOwnership($orderDiffusion->diffusion_id);
        }

        // check the state of the order_diffusion (should be equals to AWAIT to be updatable)
        if ($orderDiffusion->productstate_id != Easysdi_shopHelper::PRODUCTSTATE_AWAIT) {
            //throw a resource conflict exception
            $this->getException(409, 'The product you are trying to update has already been updated');
        }

        // try to get pricing data
        $po = Easysdi_shopHelper::getPricingOrder($order->id);
        // init other pricing var
        $pos = null;
        $posp = null;
        if ($po !== null) { // pricing order is defined, then load pricing order supplier and pricing order supplier product data
            $posp = Easysdi_shopHelper::getPricingOrderSupplierProduct($diffusion->id, $po->id);
            if ($posp == null) {
                $this->getException(500, 'Cannot load the requested pricing product');
            }
            $pos = Easysdi_shopHelper::getPricingOrderSupplier($posp->pricing_order_supplier_id);
            if ($pos == null) {
                $this->getException(500, 'Cannot load the requested pricing supplier');
            }
        }

        //all is fine
        $this->updateOrderDiffusion($orderDiffusion, $order, $po, $pos, $posp);

        //notify user if needed
        Easysdi_shopHelper::notifyCustomerOnOrderUpdate($order->id, true);

        // prepare response
        $this->setProductSucceeded($order, $diffusion);

        // schema validation
        $this->schemaValidation($this->xsdLocalSetProduct, 0);

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
    private function getDiffusionByGuid($guid) {
        $diffusionModel = $this->getModel('Diffusion', 'Easysdi_shopModel');
        $diffusion = $diffusionModel->getTable();
        if (!($diffusion->load(array('guid' => $guid)))) {
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
    private function getOrderByGuid($guid) {
        $orderModel = $this->getModel('Order', 'Easysdi_shopModel');
        $order = $orderModel->getTable();
        if (!($order->load(array('guid' => $guid)))) {
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
    private function getOrderDiffusion($oId, $dId) {
        $query = $this->db->getQuery(true)
                ->select('od.*')
                ->from('#__sdi_order_diffusion od')
                ->where('od.order_id=' . (int) $oId)
                ->where('od.diffusion_id=' . (int) $dId);
        $this->db->setQuery($query);
        $orderDiffusion = $this->db->loadObject();

        // check the existence of the order_diffusion
        if ($orderDiffusion == null) {
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
    private function checkOwnership($dId) {
        $query = $this->db->getQuery(true)
                ->select('r.organism_id')
                ->from('#__sdi_resource r')
                ->innerJoin('#__sdi_version v ON v.resource_id=r.id')
                ->innerJoin('#__sdi_diffusion d ON d.version_id=v.id')
                ->where('d.id=' . (int) $dId)
                ->where('r.organism_id=' . (int) $this->organism->id);
        $this->db->setQuery($query);
        $this->db->execute();

        if ($this->db->getNumRows() == 0) {
            //throw a product/organism integrity exception
            $this->getException(403, 'You cannot update this product');
        }
    }

    /**
     * convertSize - convert the given size to size in octet according to the given unit
     * 
     * @return string
     * @since 4.3.0
     * 
     * call getException if the unit size is not recognized
     */
    private function convertSize() {
        $sizeNode = $this->product->getElementsByTagNameNS(self::nsSdi, 'filesize')->item(0);
        $unit = $sizeNode->getAttribute('unit');
        $size = $sizeNode->nodeValue;

        switch ($unit) {
            case 'o':
                return $size;

            case 'Kio':
                return $size * 1024;

            case 'Mio':
                return $size * 1024 * 1024;

            case 'Gio':
                return $size * 1024 * 1024 * 1024;

            default:
                //throw an unpredictable case exception - should never fall in this case wich xsd validation
                $this->getException(409, 'Unit size not recognized');
        }
    }

    /**
     * storeFileLocally - save the file send by a supplier
     * 
     * @param string $d_id
     * @param string $o_id
     * 
     * @return array an array of the filename and filesize
     * @since 4.3.0
     * 
     * call getException if the folder cannot be created or if the file cannot be saved
     */
    private function storeFileLocally($d_id, $o_id) {
        if ($_FILES['file']['error'] > 0) {
            //throw an upload exception
            $this->getException(500, $_FILES['file']['error']);
        }

        $extractsFilesPath = JPATH_BASE . JComponentHelper::getParams('com_easysdi_shop')->get('orderresponseFolder') . '/' . $o_id . '/' . $d_id;
        if (!file_exists($extractsFilesPath)) {
            if (!mkdir($extractsFilesPath, 0755, true)) {
                //throw a folder creation exception
                $this->getException(500, 'Cannot create the required folder');
            }
        }

        $storeFileName = Easysdi_shopHelper::getCleanFilename($_FILES['file']['name']);

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $extractsFilesPath . '/' . $storeFileName)) {
            //throw an upload exception
            $this->getException(500, 'Cannot save uploaded file');
        }

        return array($storeFileName, $_FILES['file']['size']);
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
    private function updateOrderDiffusion($od, $order, $po = null, $pos = null, $posp = null) {
        $updatePricing = false;

        $query = $this->db->getQuery(true)
                ->update('#__sdi_order_diffusion')
                ->set('completed=' . $this->db->quote(date("Y-m-d H:i:s")))
                ->where('id=' . (int) $od->id);

        // set productstate
        $this->setProductState($query, $posp, $updatePricing);

        //set remark
        if ($this->product->getElementsByTagNameNS(self::nsSdi, 'remark')->length > 0) {
            $query->set('remark=' . $query->quote($this->product->getElementsByTagNameNS(self::nsSdi, 'remark')->item(0)->nodeValue));
        }

        //set fee
        if ($this->product->getElementsByTagNameNS(self::nsSdi, 'fee')->length > 0) {
            $this->setFee($posp, $updatePricing, (float) $this->product->getElementsByTagNameNS(self::nsSdi, 'fee')->item(0)->nodeValue);
        }

        //set storage
        if ($this->product->getElementsByTagNameNS(self::nsSdi, 'storage')->length > 0) {
            $this->setStorage($query, $od, $order);
        }

        $this->db->setQuery($query);

        if ($this->db->execute()) {
            if ($updatePricing) {
                $r = Easysdi_shopHelper::updatePricing($posp, $pos, $po, $this->db);
                if ($r !== true) {
                    $this->getException(500, $r);
                }
            }
            Easysdi_shopHelper::changeOrderState($od->order_id);
        }//else throw a db exception
        else {
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
    private function setProductSucceeded($order, $diffusion) {
        $this->response = new DOMDocument('1.0', 'utf-8');

        $root = $this->response->createElementNS(self::nsSdi, 'sdi:success');
        $root->setAttributeNS(self::xmlnsXsi, 'xsi:schemaLocation', $this->xsdCentralSetProduct);

        $code = 200;
        $this->addTextChildNode($root, 'sdi:code', $code);
        $this->addTextChildNode($root, 'sdi:message', $this->HTTPSTATUS[$code]);

        $orderNode = $this->response->createElementNS(self::nsSdi, 'sdi:order');
        $orderNode->setAttribute('guid', $order->guid);
        $this->addTextChildNode($orderNode, 'sdi:name', $order->name);
        $root->appendChild($orderNode);

        $productNode = $this->response->createElementNS(self::nsSdi, 'sdi:product');
        $productNode->setAttribute('guid', $diffusion->guid);
        $this->addTextChildNode($productNode, 'sdi:name', $diffusion->name);
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
    private function setProductState(&$query, &$posp, &$updatePricing) {
        switch ($this->product->getElementsByTagNameNS(self::nsSdi, 'state')->item(0)->nodeValue) {
            case 'available':
                $query->set('productstate_id=' . (int) Easysdi_shopHelper::PRODUCTSTATE_AVAILABLE);
                break;

            case 'rejected':
                $query->set('productstate_id=' . (int) Easysdi_shopHelper::PRODUCTSTATE_REJECTED_SUPPLIER);

                // update pricing data if exist
                if ($posp !== null) {
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
    private function setFee(&$posp, &$updatePricing, $fee) {
        // update pricing data if exist
        if ($posp !== null) {
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
    private function setStorage(&$query, $od, $order) {
        switch ($this->product->getElementsByTagNameNS(self::nsSdi, 'storage')->item(0)->nodeValue) {
            case 'local':
                //store the file and get back the file(path) and its size
                list($file, $size) = $this->storeFileLocally($od->diffusion_id, $order->id);

                $query->set('storage_id=' . (int) Easysdi_shopHelper::EXTRACTSTORAGE_LOCAL)
                        ->set('file=' . $query->quote($file))
                        ->set('size=' . $size);
                break;

            case 'remote':
                $query->set('storage_id=' . (int) Easysdi_shopHelper::EXTRACTSTORAGE_REMOTE)
                        ->set('file=' . $query->quote($this->product->getElementsByTagNameNS(self::nsSdi, 'fileurl')->item(0)->nodeValue))
                        ->set('size=' . (int) $this->convertSize());
                break;

            default:
                //throw an unpredictable case exception - should never fall in this case with xsd validation
                $this->getException(409, 'Product storage not recognized');
        }

        $query->set('displayName=' . $query->quote($this->product->getElementsByTagNameNS(self::nsSdi, 'filename')->item(0)->nodeValue));
    }

}
