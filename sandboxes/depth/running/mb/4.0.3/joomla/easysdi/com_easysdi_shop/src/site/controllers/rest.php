<?php

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/controllers/sheet.php';


/**
 * Description of rest
 *
 * @author Marc Battaglia <marc.battaglia@depth.ch>
 */
class Easysdi_shopControllerRest extends Easysdi_shopController {

    const CONTACT = '1';
    const BILLING = '2';
    const DELIVERY = '3';
    // Product state
    const PRODUCTSTATEAWAIT = 2;
    const PRODUCTSTATEAVAILABLE = 1;
    const PRODUCTSTATESENT = 3;
    // Order state
    const ORDERSTATESENT = 6;
    const ORDERSTATEAWAIT = 4;
    const ORDERSTATEPROGRESS = 5;
    const ORDERSTATEFINISH = 3;

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

        $query->select('o.id, o.`name`, o.user_id, o.thirdparty_id, ot.`value` ordertype');
        $query->from('#__sdi_order o');
        $query->innerJoin('#__sdi_sys_ordertype ot on ot.id = o.ordertype_id');
        $query->innerJoin('#__sdi_order_diffusion od on o.id = od.order_id');
        $query->innerJoin('#__sdi_diffusion d on d.id = od.diffusion_id');
        $query->innerJoin('#__sdi_version v on d.version_id = v.id');
        $query->innerJoin('#__sdi_resource r on r.id = v.resource_id');
        if (!empty($this->organism)) {
            $query->where('r.organism_id = ' . $this->organism->id);
        }
        $query->where('od.productstate_id = ' . self::PRODUCTSTATESENT);
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
            $query->where('r.organism_id = ' . $this->organism->id);
        }
        $query->where('od.order_id = ' . $orderId);
        $query->where('od.diffusion_id = ' . $diffusionId);

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
                        $this->changeState($orderdiffusionId, self::PRODUCTSTATEAVAILABLE);
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
        $query->set('remark = \'' . $remark . '\'');
        $query->set('fee = \''.$amount.'\'');
        $query->set('completed = \'' . $now . '\'');
        $query->set('file = \'' . $filename . '\'');
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
        $orderaccount = JFactory::getUser($globalUserId);
        $passwordarray = explode(':', $orderaccount->password);

        $pwdCryp = JUserHelper::getCryptedPassword($password, $passwordarray[1]) . ':' . $passwordarray[1];

        if ($username == $orderaccount->username && $orderaccount->password == $pwdCryp) {
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
        $query->where('o.username = \'' . $username . '\'');

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
     * @param stdClass $order
     * @return DOMElement address node
     */
    private function getAdresse($addressType, $order, $for = 'client') {
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
        $query->select('o.acronym name1, o.`name` name2');
        $query->select('c.iso2 country_iso');
        $query->leftJoin('#__sdi_organism o on a.organism_id = o.id');
        $query->leftJoin('#__sdi_sys_country c on c.id = a.country_id');
        $query->from('#__sdi_address a ');
        switch ($for) {
            case 'client':
                $query->where('a.user_id = ' . $order->user_id);
                break;
            case 'tierce':
                $query->where('a.user_id = ' . $order->thirdparty_id);
                break;
        }
        $query->where('a.addresstype_id = ' . $addressType);

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

    /**
     * get a CLIENT node with many ADDRESS child
     * 
     * @param stdClass $order
     * @return DOMElement
     */
    private function getClient($order) {
        $client = $this->response->createElementNS($this->nsEasysdi, 'easysdi:CLIENT');
        $client->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:ID', $order->user_id));
        $client->appendChild($this->getAdresse(self::CONTACT, $order));
        $client->appendChild($this->getAdresse(self::BILLING, $order));
        $client->appendChild($this->getAdresse(self::DELIVERY, $order));

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
            $tierce->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:ID', $order->thirdparty_id));
            $tierce->appendChild($this->getAdresse(self::CONTACT, $order, 'tierce'));
            $tierce->appendChild($this->getAdresse(self::BILLING, $order, 'tierce'));
            $tierce->appendChild($this->getAdresse(self::DELIVERY, $order, 'tierce'));
        }
        return $tierce;
    }

    private function getBuffer() {
        $buffer = $this->response->createElementNS($this->nsEasysdi, 'easysdi:BUFFER');

        return $buffer;
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

        $query->select('d.id, m.guid, d.`name`, od.id orderdiffusion_id');
        $query->from('#__sdi_order o');
        $query->innerJoin('#__sdi_sys_ordertype ot on ot.id = o.ordertype_id');
        $query->innerJoin('#__sdi_order_diffusion od on o.id = od.order_id');
        $query->innerJoin('#__sdi_diffusion d on d.id = od.diffusion_id');
        $query->innerJoin('#__sdi_version v on d.version_id = v.id');
        $query->innerJoin('#__sdi_resource r on r.id = v.resource_id');
        $query->innerJoin('#__sdi_metadata m on m.version_id = v.id');
        if (!empty($this->organism)) {
            $query->where('r.organism_id = ' . $this->organism->id);
        }
        $query->where('od.productstate_id = ' . self::PRODUCTSTATESENT);
        $query->where('od.order_id = ' . $order->id);


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

        $this->changeState($product->orderdiffusion_id, self::PRODUCTSTATEAWAIT);
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

        $query->select('p.`alias` as palias, pv.`alias` as pvalias');
        $query->from('#__sdi_order_diffusion od');
        $query->innerJoin('#__sdi_order_propertyvalue opv on opv.orderdiffusion_id = od.id');
        $query->innerJoin('#__sdi_propertyvalue pv on pv.id = opv.propertyvalue_id');
        $query->innerJoin('#__sdi_property p on p.id = pv.property_id');
        $query->where('od.id = ' . $product->orderdiffusion_id);

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

        $query = $this->db->getQuery(true);

        $query->select('p.`name` perimeter_name, p.alias, op.`value` perimeter_value');
        $query->from('#__sdi_order_perimeter op');
        $query->innerJoin('#__sdi_perimeter p on p.id = op.perimeter_id');
        $query->where('op.order_id = ' . $order->id);

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
        $query->set('od.productstate_id = ' . $to);
        $query->where('od.id = ' . $orderdiffusion_id);

        $this->db->setQuery($query);
        $this->db->execute();

        $query = $this->db->getQuery(true);

        $query->select('*');
        $query->from('#__sdi_order_diffusion');
        $query->where('id = ' . $orderdiffusion_id);

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
        $query->where('order_id = ' . $orderId);

        $this->db->setQuery($query);
        $total = $this->db->getNumRows($this->db->execute());

        $query = $this->db->getQuery(true);

        $query->select('id');
        $query->from('#__sdi_order_diffusion');
        $query->where('order_id = ' . $orderId);
        $query->where('productstate_id = ' . self::PRODUCTSTATEAWAIT);

        $this->db->setQuery($query);
        $await = $this->db->getNumRows($this->db->execute());

        $query = $this->db->getQuery(true);

        $query->select('id');
        $query->from('#__sdi_order_diffusion');
        $query->where('order_id = ' . $orderId);
        $query->where('productstate_id = ' . self::PRODUCTSTATEAVAILABLE);

        $this->db->setQuery($query);
        $available = $this->db->getNumRows($this->db->execute());

        $orderstate = $this->chooseOrderState($total, $await, $available);

        if ($orderstate > 0) {
            $now = date("Y-m-d H:i:s");
            
            $query = $this->db->getQuery(true);

            $query->update('#__sdi_order');
            $query->set('orderstate_id = ' . $orderstate);
            if($orderstate == self::ORDERSTATEFINISH){
                $query->set('completed = \'' . $now . '\'');
            }
            $query->where('id = ' . $orderId);

            $this->db->setQuery($query);
            $this->db->execute();
        }
    }
    
    private function chooseOrderState($total, $await, $available){
        if($available == $total){
            return self::ORDERSTATEFINISH;
        }
        
        if($available>0){
            return self::ORDERSTATEPROGRESS;
        }
        
        if($await>0){
            return self::ORDERSTATEAWAIT;
        }
        
        return 0;
    }

}
