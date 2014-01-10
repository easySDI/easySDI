<?php

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Description of rest
 *
 * @author Marc Battaglia <marc.battaglia@depth.ch>
 */
class Easysdi_shopControllerRest extends Easysdi_shopController {

    const CONTACT = '1';
    const BILLING = '2';
    const DELIVERY = '3';

    /** @var string Possible values global or organism */
    private $userType = 'global';
    private $organism;
    // Namespace for response
    private $nsOws = 'http://www.opengis.net/ows/1.1';
    private $nsWps = 'http://www.opengis.net/wps/1.0.0';
    private $nsEasysdi = 'http://www.easysdi.org';

    /** @var JDatabaseDriver Description */
    private $db;

    /** @var DOMDocument Description */
    private $request;

    /** @var DOMDocument Description */
    private $response;

    function __construct() {
        parent::__construct();

        $this->db = JFactory::getDbo();
        $this->request = new DOMDocument('1.0', 'utf-8');
        $this->response = new DOMDocument('1.0', 'utf-8');
    }

    /**
     * Main wps method
     */
    public function wps() {
        if ($this->authentification()) {
            if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
                if ($this->request->loadXML($GLOBALS['HTTP_RAW_POST_DATA'])) {
                    //if($this->request->schemaValidate(JPATH_COMPONENT.'/controllers/xsd/wpsAll.xsd')){
                    if (true) {
                        /* @var $identifier DOMElement */
                        $identifier = $this->request->getElementsByTagNameNS('http://www.opengis.net/ows/1.1', 'Identifier')->item(0);
                        switch ($identifier->nodeValue) {
                            case 'getOrders':
                                $this->response->appendChild($this->getOrders());
                                echo $this->response->saveXML();
                                break;
                            case 'setOrder':
                                $this->setOrder();
                                break;

                            default:
                                break;
                        }
                        die();
                    } else {
                        echo 'Requeste non valide';
                        die();
                    }
                } else {
                    echo 'xml non valide';
                    die();
                }
            }
        } else {
            echo 'Authentification OK :' . $this->userType;
            die();
        }
    }

    /**
     * 
     * @return DOMElement
     */
    private function getOrders() {
        $query = $this->db->getQuery(true);

        $query->select('o.id, o.`name`, o.user_id, ot.`value` ordertype');
        $query->from('#__sdi_order o');
        $query->innerJoin('#__sdi_sys_ordertype ot on ot.id = o.ordertype_id');

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

    private function setOrder() {
        
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

        $query->select('o.username, o.password');
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

        $client = $request->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:CLIENT'));
        $client->appendChild($request->appendChild($this->response->createElementNS($this->nsEasysdi, 'easysdi:ID', $order->user_id)));

        $root->appendChild($header);
        $root->appendChild($request);
        $root->appendChild($client);

        return $order;
    }

    private function getAdresse($addressType, $order) {
        switch ($addressType) {
            case self::CONTACT:
                $adresse = $this->response->createElementNS($this->nsEasysdi, 'easysdi:CONTACTADDRESS');
                break;
            case self::BILLING:
                $adresse = $this->response->createElementNS($this->nsEasysdi, 'easysdi:CONTACTADDRESS');
                break;
            case self::DELIVERY:
                $adresse = $this->response->createElementNS($this->nsEasysdi, 'easysdi:CONTACTADDRESS');
                break;
        }


        $query = $this->db->getQuery(true);

        $query->select('*');
        $query->from('#__sdi_address a ');
        $query->where('a.user_id = ' . $order->user_id);
        $query->where('a.addresstype_id = ' . $addressType);

        $this->db->setQuery($query);
        $addressedata = $this->db->loadObject();
        
        
        
    }

}
