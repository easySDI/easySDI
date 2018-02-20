<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/controllers/sheet.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

class Easysdi_shopControllerRest extends Easysdi_shopController {

    //Address
    const CONTACT = '1';
    const BILLING = '2';
    const DELIVERY = '3';
    
    // Order productmining
    const PRODUCTMININGAUTO = 1;
    const PRODUCTMININGMANUAL = 2;

    /** @var string Possible values global or organism */
    private $userType = 'global';
    private $organism;
    // Namespace for response
    private $nsOws = 'http://www.opengis.net/ows/1.1';
    private $nsWps = 'http://www.opengis.net/wps/1.0.0';
    private $nsEasysdi = 'http://www.easysdi.org';

    /** @var JDatabaseDriver Description */
    private $db;

    /** @var DOMDocument */
    private $request;

    /** @var DOMDocument  */
    private $response;
    
    /** @var Easysdi_catalogControllerSheet */
    private $sheet;

    function __construct() {
        parent::__construct();

        $this->db = JFactory::getDbo();
        $this->request = new DOMDocument('1.0', 'utf-8');
        $this->response = new DOMDocument('1.0', 'utf-8');
        
        $this->sheet = new Easysdi_catalogControllerSheet();
    }

    /**
     * Main wps method
     */
    public function wps() {
        /* @var $response DOMElement */
        $response = NULL;

        if ($this->authentification()) {
            if (!empty($GLOBALS['HTTP_RAW_POST_DATA'])) {
                if ($this->request->loadXML($GLOBALS['HTTP_RAW_POST_DATA'], LIBXML_PARSEHUGE)) {
                    //if($this->request->schemaValidate(JPATH_COMPONENT.'/controllers/xsd/wpsAll.xsd')){
                    if (true) {
                        /* @var $identifier DOMElement */
                        $identifier = $this->request->getElementsByTagNameNS('http://www.opengis.net/ows/1.1', 'Identifier')->item(0);
                        switch ($identifier->nodeValue) {
                            case 'getOrders':
                                $response = $this->getOrders();
                                break;
                            case 'setOrder':
                                $response = $this->setOrder();
                                break;
                            default:
                                $response = $this->getException('NotSupportedOperation', 'This operation identifier is not supported.');
                                break;
                        }
                    } else {
                        $response = $this->getException('InvalideRequest', 'This request is not schema compliant.');
                    }
                } else {
                    $response = $this->getException('InvalideXml', 'This XML is not well formed.');
                }
            } else {
                $response = $this->getException('EmptyRequest', 'This request is empty.');
            }
        } else {
            $response = $this->getException('BadUserOrPassword', 'Bad username or password.');
        }

        $this->response->appendChild($response);
        echo $this->response->saveXML();
        JFactory::getApplication()->close();
    }

    /**
     * Execute de Identifier getOrders
     * 
     * @return DOMElement
     */
    private function getOrders() {
        $query = $this->db->getQuery(true);

        $query->select('o.id, ' . $this->db->quoteName('o.name') .', o.user_id, o.surface, o.thirdparty_id, ' . $this->db->quoteName('ot.value') .' as ordertype');
        $query->from('#__sdi_order o');
        $query->innerJoin('#__sdi_sys_ordertype ot on ot.id = o.ordertype_id');
        $query->innerJoin('#__sdi_order_diffusion od on o.id = od.order_id');
        $query->innerJoin('#__sdi_diffusion d on d.id = od.diffusion_id');
        $query->innerJoin('#__sdi_version v on d.version_id = v.id');
        $query->innerJoin('#__sdi_resource r on r.id = v.resource_id');
        if (!empty($this->organism)) {
            $query->where('r.organism_id = ' . (int)$this->organism->id);
        }
        //only items in sent state
        $query->where('od.productstate_id = ' . Easysdi_shopHelper::PRODUCTSTATE_SENT);
        //only automatic mining products
        $query->where('d.productmining_id = ' . self::PRODUCTMININGAUTO);
        //only order that are completely saved (avoid partial orders : https://forge.easysdi.org/issues/1252)
        $query->where('o.sent > \'0000-00-00 00:00:00\'');
        //group by order id
        $query->group('o.id');

        $this->db->setQuery($query);

        $results = $this->db->loadObjectList();

        $executeResponse = $this->response->createElementNS($this->nsWps, 'wps:ExecuteResponse');
        $status = $this->response->createElementNS($this->nsWps, 'wps:Status');
        $processSucceeded = $this->response->createElementNS($this->nsWps, 'wps:ProcessSucceeded', 'processSucceeded');
        $processOutputs = $this->response->createElementNS($this->nsWps, 'wps:ProcessOutputs');
        $output = $this->response->createElementNS($this->nsWps, 'wps:Output');
        $identifier = $this->response->createElementNS($this->nsOws, 'ows:Identifier', 'getOrders');
        $data = $this->response->createElementNS($this->nsWps, 'wps:Data');
        $complexData = $this->response->createElementNS($this->nsWps, 'wps:ComplexData');
        $orders = $this->response->createElementNS($this->nsEasysdi, 'easysdi:orders');

        foreach ($results as $order) {
            $orders->appendChild($this->getOrder($order));
        }

        $complexData->appendChild($orders);
        $data->appendChild($complexData);
        $output->appendChild($identifier);
        $output->appendChild($data);
        $processOutputs->appendChild($output);
        $status->appendChild($processSucceeded);
        $executeResponse->appendChild($status);
        $executeResponse->appendChild($processOutputs);

        return $executeResponse;
    }

    /**
     * Execute de Identifier setOrder
     * 
     * @return DOMElement
     */
    private function setOrder() {
        $orderId = '';
        $diffusionId = '';
        $remark = '';
        $amount = '';
        $filename = '';
        $data = '';

        ini_set('memory_limit','4096M');
        
        $inputs = $this->request->getElementsByTagNameNS($this->nsWps, 'Input');

        /* @var $input DOMElement */
        foreach ($inputs as $input) {
            switch ($input->getElementsByTagNameNS($this->nsOws, 'Identifier')->item(0)->nodeValue) {
                case 'orderID':
                    $orderId = $input->getElementsByTagNameNS($this->nsWps, 'LiteralData')->item(0)->nodeValue;
                    break;
                case 'productID':
                    $diffusionId = $input->getElementsByTagNameNS($this->nsWps, 'LiteralData')->item(0)->nodeValue;
                    break;
                case 'notice':
                    $remark = $input->getElementsByTagNameNS($this->nsWps, 'LiteralData')->item(0)->nodeValue;
                    break;
                case 'amount':
                    $amount = $input->getElementsByTagNameNS($this->nsWps, 'LiteralData')->item(0)->nodeValue;
                    break;
                case 'filename':
                    $filename = $input->getElementsByTagNameNS($this->nsWps, 'LiteralData')->item(0)->nodeValue;
                    break;
                case 'data':
                    $data = $input->getElementsByTagNameNS($this->nsWps, 'ComplexData')->item(0)->nodeValue;
                    break;
            }
        }

        $query = $this->db->getQuery(true);

        $query->select('od.id');
        $query->from('#__sdi_order_diffusion od');
        $query->innerJoin('#__sdi_diffusion d on d.id = od.diffusion_id');
        $query->innerJoin('#__sdi_version v on d.version_id = v.id');
        $query->innerJoin('#__sdi_resource r on r.id = v.resource_id');
        if (!empty($this->organism)) {
            $query->where('r.organism_id = ' . (int)$this->organism->id);
        }
        $query->where('od.order_id = ' . (int)$orderId);
        $query->where('od.diffusion_id = ' . (int)$diffusionId);

        $this->db->setQuery($query);

        if ($product = $this->db->loadObject()) {
            return $this->sendProduct($product->id, $orderId, $diffusionId, $remark, $amount, $filename, $data);
        } else {
            return $this->getException('ProductNotFound', 'Couple userid and productid not found for this user.');
        }
    }

    /**
     * Decode the file and upload in the folder configured in the shop.
     * 
     * @param int $orderdiffusionId
     * @param int $orderId
     * @param int $diffusionId
     * @param string $remark
     * @param string $filename
     * @param string $data Base64 value of file
     * @return DOMElement
     */
    private function sendProduct($orderdiffusionId, $orderId, $diffusionId, $remark, $amount, $filename, $data) {
        $folder = JComponentHelper::getParams('com_easysdi_shop')->get('orderresponseFolder');

        if ($content = base64_decode($data)) {
            if (!file_exists(JPATH_BASE . $folder . '/' . $orderId . '/' . $diffusionId)) {
                $mkdirOk = mkdir(JPATH_BASE . $folder . '/' . $orderId . '/' . $diffusionId, 0755, true);
            } else {
                $mkdirOk = true;
            }

            if ($mkdirOk) {
                if ($size = file_put_contents(JPATH_BASE . $folder . '/' . $orderId . '/' . $diffusionId . '/' . $filename, $content)) {
                    if ($this->updateOrderDiffusion($orderdiffusionId, $remark, $amount, $filename, $size)) {
                        $this->changeState($orderdiffusionId, Easysdi_shopHelper::PRODUCTSTATE_AVAILABLE);
                        //notify user if needed
                        Easysdi_shopHelper::notifyCustomerOnOrderUpdate($orderId, true);
                        return $this->getSuccess('File sended');
                    } else {
                        return $this->getException('UnableToUpdateTable', 'Unable to update values into database table.');
                    }
                } else {
                    return $this->getException('UnableToWriteFile', 'Unable to write file.');
                }
            } else {
                return $this->getException('UnableToWriteFolder', 'Unable to write folder tree.');
            }
        } else {
            return $this->getException('UnableToDecode', 'Unable to decode data.');
        }
    }

    /**
     * Upgrade to the order element.
     * 
     * @param int $orderId
     * @param int $diffusionId
     * @param string $remark
     * @param string $filename
     * @param int $size
     * @return mixed A database cursor resource on success, boolean false on failure.
     */
    private function updateOrderDiffusion($orderdiffusionId, $remark, $amount, $filename, $size) {
        $now = date("Y-m-d H:i:s");

        $query = $this->db->getQuery(true);

        $query->update('#__sdi_order_diffusion');
        $query->set('remark = ' . $query->quote($remark));
        $query->set('completed = ' . $query->quote($now));
        $query->set('storage_id = 1');
        $query->set('file = ' . $query->quote($filename));
        $query->set('size = ' . $size);
        $query->where('id = ' . $orderdiffusionId);

        $this->db->setQuery($query);

        return $this->db->execute();
    }

    /**
     * Authenticate user
     * 
     * @return boolean
     */
    private function authentification() {
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
     * Get one ORDER node
     * 
     * @param stdClass $order
     * @return DOMElement ORDER node
     */
    private function getOrder($order) {

        $root = $this->response->createElementNS($this->nsEasysdi, 'easysdi:ORDER');

        $header = $this->response->createElementNS($this->nsEasysdi, 'easysdi:HEADER');
        $header->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:VERSION', '2.0'));
        $header->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:PLTFORM', 'EASYSDI'));
        $header->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:SERVER', '36'));

        $request = $this->response->createElementNS($this->nsEasysdi, 'easysdi:REQUEST');
        $request->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:ID', $order->id));
        $request->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:TYPE', $order->ordertype));
        $request->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:NAME', $order->name));

        $root->appendChild($header);
        $root->appendChild($request);
        $root->appendChild($this->getClient($order));
        $root->appendChild($this->getTierce($order));
        $root->appendChild($this->getPerimeter($order));
        $root->appendChild($this->getProducts($order));

        return $root;
    }

    /**
     * Get an address node from the specific type
     * 
     * @param int $addressType type of address 
     * @param int $recipientId
     * @return DOMElement address node
     */
    private function getAdresse($addressType, $recipientId, $for = 'client') {
        switch ($addressType) {
            case self::CONTACT:
                $address = $this->response->createElementNS($this->nsEasysdi, 'easysdi:CONTACTADDRESS');
                break;
            case self::BILLING:
                $address = $this->response->createElementNS($this->nsEasysdi, 'easysdi:INVOICEADDRESS');
                break;
            case self::DELIVERY:
                $address = $this->response->createElementNS($this->nsEasysdi, 'easysdi:DELIVERYADDRESS');
                break;
        }


        $query = $this->db->getQuery(true);

        $query->select('a.firstname, a.lastname, a.address, a.addresscomplement, a.postalcode, a.postalbox, a.locality, a.email, a.phone, a.mobile, a.fax');
        $query->select('o.acronym name1, o.name name2');
        $query->select('c.iso2 country_iso');
        $query->leftJoin('#__sdi_organism o on a.organism_id = o.id');
        $query->leftJoin('#__sdi_sys_country c on c.id = a.country_id');
        $query->from('#__sdi_address a ');
        switch ($for) {
            case 'client':
                $query->where('a.user_id = ' . (int)$recipientId);
                break;
            case 'tierce':
                $query->where('a.organism_id = ' . (int)$recipientId);
                break;
            /*case 'organism':
                $query->where('a.organism_id = ' . (int)$recipientId);
                break;*/
        }
        $query->where('a.addresstype_id = ' . (int)$addressType);

        $this->db->setQuery($query);
        $addressdata = $this->db->loadObject();

        if (!empty($addressdata)) {
            $address->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:NAME1', $addressdata->name1));
            $address->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:NAME2', $addressdata->name2));
            $address->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:AGENTFIRSTNAME', $addressdata->firstname));
            $address->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:AGENTLASTNAME', $addressdata->lastname));
            $address->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:ADDRESSSTREET1', $addressdata->address));
            $address->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:ADDRESSSTREET2', $addressdata->addresscomplement));
            $address->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:ZIP', $addressdata->postalcode));
            $address->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:POBOX', $addressdata->postalbox));
            $address->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:LOCALITY', $addressdata->locality));
            $address->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:COUNTRY', $addressdata->country_iso));
            $address->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:EMAIL', $addressdata->email));
            $address->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:MOBILE', $addressdata->mobile));
            $address->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:PHONE', $addressdata->phone));
            $address->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:FAX', $addressdata->fax));
        }

        return $address;
    }
    
    private function getClientOrganism($userId){
        $query = $this->db->getQuery(true);

        $query->select('o.id, o.acronym, o.website, o.name')
                ->from('#__sdi_user_role_organism uro')
                ->join('LEFT', '#__sdi_organism o ON o.id=uro.organism_id')
                ->where('uro.user_id = ' . $userId)
                ->where('uro.role_id=1');

        $this->db->setQuery($query);

        return $this->db->loadObject();
    }

    /**
     * get a CLIENT node with many ADDRESS child
     * 
     * @param stdClass $order
     * @return DOMElement
     */
    private function getClient($order) {
        $client = $this->response->createElementNS($this->nsEasysdi, 'easysdi:CLIENT');
        $client->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:ID', $order->user_id));
        $client->appendChild($this->getAdresse(self::CONTACT, $order->user_id));
        $client->appendChild($this->getAdresse(self::BILLING, $order->user_id));
        $client->appendChild($this->getAdresse(self::DELIVERY, $order->user_id));
        
        $clientOrganism = $this->getClientOrganism($order->user_id);
        
        $organism = $this->response->createElementNS($this->nsEasysdi, 'easysdi:ORGANISM');

        $organism->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:ID', $clientOrganism->id));
        $organism->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:NAME', $clientOrganism->name));
        $organism->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:ACRONYM', $clientOrganism->acronym));
        $organism->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:WEBSITE', $clientOrganism->website));

        $organism->appendChild($this->getCategories($clientOrganism->id));

        $organism->appendChild($this->getAdresse(self::CONTACT, $clientOrganism->id, 'tierce'));
        $organism->appendChild($this->getAdresse(self::BILLING, $clientOrganism->id, 'tierce'));
        $organism->appendChild($this->getAdresse(self::DELIVERY, $clientOrganism->id, 'tierce'));

        $client->appendChild($organism);

        return $client;
    }

    /**
     * get a TIERCE node with many ADDRESS child
     * 
     * @param stdClass $order
     * @return DOMElement
     */
    private function getTierce($order) {
        $tierce = $this->response->createElementNS($this->nsEasysdi, 'easysdi:TIERCE');
        if (!empty($order->thirdparty_id)) {
            $query = $this->db->getQuery(true);
            
            $query->select('o.name, o.acronym, o.website')
                    ->from('#__sdi_organism o')
                    ->where('o.id='.(int)$order->thirdparty_id);
            
            $this->db->setQuery($query);
            
            $thirdparty = $this->db->loadObject();
            
            $tierce->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:ID', $order->thirdparty_id));
            
            $tierce->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:NAME', $thirdparty->name));
            $tierce->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:ACRONYM', $thirdparty->acronym));
            $tierce->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:WEBSITE', $thirdparty->website));
            
            $tierce->appendChild($this->getCategories($order->thirdparty_id));
            
            $tierce->appendChild($this->getAdresse(self::CONTACT, $order->thirdparty_id, 'tierce'));
            $tierce->appendChild($this->getAdresse(self::BILLING, $order->thirdparty_id, 'tierce'));
            $tierce->appendChild($this->getAdresse(self::DELIVERY, $order->thirdparty_id, 'tierce'));
        }
        return $tierce;
    }

    /**
     * get an organism's categorie's list
     * called from getClient and getTierce
     * 
     * @param int $organism_id
     * @return DOMElement
     */
    private function getCategories($organism_id = 0){
        $categories = $this->response->createElementNS($this->nsEasysdi, 'easysdi:CATEGORIES');
        
        $query = $this->db->getQuery(true);
        $query->select('c.id, c.name')
                ->from('#__sdi_organism_category oc')
                ->join('LEFT', '#__sdi_category c ON c.id=oc.category_id')
                ->where('oc.organism_id='.(int) $organism_id);
        $this->db->setQuery($query);

        foreach($this->db->loadObjectList() as $cat){
            $category = $this->response->createElementNS($this->nsEasysdi, 'easysdi:CATEGORY');
            $category->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:ID', $cat->id));
            $category->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:NAME', $cat->name));

            $categories->appendChild($category);
        }
        
        return $categories;
    }

    /**
     * get a PRODUCTS node with many PRODUCT child
     * 
     * @param stdClass $order
     * @return DOMElement a PRODUCTS node
     */
    private function getProducts($order) {
        $products = $this->response->createElementNS($this->nsEasysdi, 'easysdi:PRODUCTS');

        $query = $this->db->getQuery(true);

        $query->select('d.id, m.guid, d.name, od.id orderdiffusion_id');
        $query->from('#__sdi_order o');
        $query->innerJoin('#__sdi_sys_ordertype ot on ot.id = o.ordertype_id');
        $query->innerJoin('#__sdi_order_diffusion od on o.id = od.order_id');
        $query->innerJoin('#__sdi_diffusion d on d.id = od.diffusion_id');
        $query->innerJoin('#__sdi_version v on d.version_id = v.id');
        $query->innerJoin('#__sdi_resource r on r.id = v.resource_id');
        $query->innerJoin('#__sdi_metadata m on m.version_id = v.id');
        if (!empty($this->organism)) {
            $query->where('r.organism_id = ' . (int)$this->organism->id);
        }
        $query->where('od.productstate_id = ' . Easysdi_shopHelper::PRODUCTSTATE_SENT);
        $query->where('od.order_id = ' . (int)$order->id);


        $this->db->setQuery($query);
        $productsdata = $this->db->loadObjectList();

        foreach ($productsdata as $product) {
            $products->appendChild($this->getProduct($order, $product));
        }

        return $products;
    }

    /**
     * get a PRODUCT node
     * 
     * @param stdClass $product
     * @return DOMElement PRODUCT node
     */
    private function getProduct($order, $product) {
        $xml = $this->sheet->exportXML($product->guid, FALSE);
        $pdf = $this->sheet->exportPDF($product->guid, FALSE);
        
        $root = $this->response->createElementNS($this->nsEasysdi, 'easysdi:PRODUCT');
        $root->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:METADATA_ID', $product->guid));
        $root->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:ID', $product->id));
        $root->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:NAME', $product->name));
        $root->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:XML', base64_encode($xml)));
        $root->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:PDF', base64_encode($pdf)));
        
        $root->appendChild($this->getProductProperties($product));

        $this->changeState($product->orderdiffusion_id, Easysdi_shopHelper::PRODUCTSTATE_AWAIT);
        $this->changeOrderState($order->id);

        return $root;
    }

    /**
     * get a PROPERTIES node with many PROPERTY node
     * 
     * @param stdClass $product
     * @return DOMElement PROPERTIES node
     */
    private function getProductProperties($product) {
        $properties = $this->response->createElementNS($this->nsEasysdi, 'easysdi:PROPERTIES');

        $query = $this->db->getQuery(true);

        $query->select('p.alias as palias, pv.alias as pvalias');
        $query->from('#__sdi_order_diffusion od');
        $query->innerJoin('#__sdi_order_propertyvalue opv on opv.orderdiffusion_id = od.id');
        $query->innerJoin('#__sdi_propertyvalue pv on pv.id = opv.propertyvalue_id');
        $query->innerJoin('#__sdi_property p on p.id = pv.property_id');
        $query->where('od.id = ' . (int)$product->orderdiffusion_id);

        $this->db->setQuery($query);
        $propertiesdata = $this->db->loadObjectList();

        foreach ($propertiesdata as $propertydata) {
            $property = $this->response->createElementNS($this->nsEasysdi, 'easysdi:PROPERTY');
            $property->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:CODE', $propertydata->palias));
            $property->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:VALUE', $propertydata->pvalias));

            $properties->appendChild($property);
        }

        return $properties;
    }

    /**
     * Get a PERIMETER node with many CONTENT node
     * 
     * @param type $order
     * @return DOMElement
     */
    private function getPerimeter($order) {
        $perimeter = $this->response->createElementNS($this->nsEasysdi, 'easysdi:PERIMETER');
        
        $perimeter->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:SURFACE', $order->surface));

        $query = $this->db->getQuery(true);

        $query->select('p.name perimeter_name, p.alias, op.value perimeter_value');
        $query->from('#__sdi_order_perimeter op');
        $query->innerJoin('#__sdi_perimeter p on p.id = op.perimeter_id');
        $query->where('op.order_id = ' . (int)$order->id);

        $this->db->setQuery($query);
        $contents = $this->db->loadObjectList();

        $first = $contents[0];

        switch ($first->alias) {
            case 'freeperimeter':
            case 'myperimeter':
                $perimeter->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:TYPE', 'COORDINATES'));
                break;
            default :
                $perimeter->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:TYPE', 'VALUES'));
                break;
        }

        $perimeter->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:CODE', $first->alias));

        foreach ($contents as $content) {
            $perimeter->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:CONTENT', $content->perimeter_value));
        }

        return $perimeter;
    }

    /**
     * Show a generic error XML block.
     * 
     * @param string $exceptionCode
     * @param string $message
     */
    private function getException($exceptionCode, $message) {
        $exceptionReport = $this->response->createElementNS($this->nsOws, 'ows:ExceptionReport');
        $exception = $this->response->createElementNS($this->nsOws, 'ows:Exception');
        $exception->setAttribute('exceptionCode', $exceptionCode);

        $exceptionText = $this->response->createElementNS($this->nsOws, 'ows:ExceptionText', $message);

        $exception->appendChild($exceptionText);
        $exceptionReport->appendChild($exception);

        $this->response->appendChild($exceptionReport);

        echo $this->response->saveXML();
        JFactory::getApplication()->close();
    }

    /**
     * Returns a XML block of generic success response.
     * 
     * @param string $message
     * @return DOMElement
     */
    private function getSuccess($message) {
        $executeResponse = $this->response->createElementNS($this->nsWps, 'wps:ExecuteResponse');
        $status = $this->response->createElementNS($this->nsWps, 'wps:Status');
        $processSucceeded = $this->response->createElementNS($this->nsWps, 'wps:ProcessSucceeded', $message);

        $status->appendChild($processSucceeded);
        $executeResponse->appendChild($status);

        return $executeResponse;
    }

    /**
     * Change the status of a order element.
     * 
     * @param int $orderdiffusion_id Id of the element to change.
     * @param int $to Status of the element.
     */
    private function changeState($orderdiffusion_id, $to) {
        $query = $this->db->getQuery(true);

        $query->update('#__sdi_order_diffusion od');
        $query->set('od.productstate_id = ' . (int)$to);
        $query->where('od.id = ' . (int)$orderdiffusion_id);

        $this->db->setQuery($query);
        $this->db->execute();

        $query = $this->db->getQuery(true);

        $query->select('*');
        $query->from('#__sdi_order_diffusion');
        $query->where('id = ' . (int)$orderdiffusion_id);

        $this->db->setQuery($query);
        $orderdiffusion = $this->db->loadObject();

        $this->changeOrderState($orderdiffusion->order_id);
    }

    /**
     * Dynamically changes the statue of the order.
     * 
     * @param int $orderId Id of the order.
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
        $query->where('productstate_id = ' . Easysdi_shopHelper::PRODUCTSTATE_AWAIT);

        $this->db->setQuery($query);
        $await = $this->db->getNumRows($this->db->execute());

        $query = $this->db->getQuery(true);

        $query->select('id');
        $query->from('#__sdi_order_diffusion');
        $query->where('order_id = ' . (int)$orderId);
        $query->where('productstate_id = ' . Easysdi_shopHelper::PRODUCTSTATE_AVAILABLE);

        $this->db->setQuery($query);
        $available = $this->db->getNumRows($this->db->execute());

        $orderstate = $this->chooseOrderState($total, $await, $available);

        if ($orderstate > 0) {
            $now = date("Y-m-d H:i:s");
            
            $query = $this->db->getQuery(true);

            $query->update('#__sdi_order');
            $query->set('orderstate_id = ' . $orderstate);
            if($orderstate == Easysdi_shopHelper::ORDERSTATE_FINISH){
                $query->set('completed = ' . $query->quote($now) );
            }
            $query->where('id = ' . (int)$orderId);

            $this->db->setQuery($query);
            $this->db->execute();
        }
    }
    
    private function chooseOrderState($total, $await, $available){
        if($available == $total){
            return Easysdi_shopHelper::ORDERSTATE_FINISH;
        }
        
        if($available>0){
            return Easysdi_shopHelper::ORDERSTATE_PROGRESS;
        }
        
        if($await>0){
            return Easysdi_shopHelper::ORDERSTATE_AWAIT;
        }
        
        return 0;
    }

}
