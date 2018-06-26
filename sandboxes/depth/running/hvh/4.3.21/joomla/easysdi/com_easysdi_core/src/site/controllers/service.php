<?php

/**
 * @version     4.3.21
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2018. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
/**
 * Easysdi_coreControllerService
 *
 * @author hvanhoecke
 */
require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/factory/sdifactory.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/user/sdiuser.php';
require_once JPATH_COMPONENT . '/controllers/version.php';


class Easysdi_coreControllerService extends Easysdi_coreController {

    /** @var JDatabaseDriver Description */
    private $db;

    /** @var DOMDocument */
    private $request;

    /** @var DOMDocument  */
    private $response;
    private $rootNode;
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

    /**
     * __construct
     */
    function __construct() {
        parent::__construct();
        $this->db = JFactory::getDbo();
        $this->request = new DOMDocument('1.0', 'utf-8');
        $this->response = new DOMDocument('1.0', 'utf-8');
        $this->rootNode = $this->response->createElementNS(self::nsSdi, 'sdi:coreservice');
        $this->addAttribute($this->rootNode, 'version', '4.3.21');
        $this->response->appendChild($this->rootNode);
    }

    /**
     * Authentication into Joomla application 
     * 
     * @return boolean
     */
    public function authenticate() {
        $app = JFactory::getApplication();
        $credentials['username'] = $_SERVER['PHP_AUTH_USER'];
        $credentials['password'] = $_SERVER['PHP_AUTH_PW'];
        if (!$app->login($credentials)) {
            return false;
        }
        return true;
    }

    private function checkRights($resource_id) {
        //$model = $this->getModel('Resource', 'Easysdi_coreModel');
        //$resource = $model->getData($resource_id);
        
        $sdiUser = sdiFactory::getSdiUser();        
        if (!$sdiUser->authorize($resource_id, sdiUser::resourcemanager) && !$sdiUser->isOrganismManager($resource_id)) {
            return false;
        }
        return true;
        /*$table = JTable::getInstance('Resource', 'Easysdi_coreTable');
        if ($table->load($resource_id)) {
            $properties = $table->getProperties(1);
            $this->_resource = JArrayHelper::toObject($properties, 'JObject');
        }*/        
    }

    public function newVersion() {
        if (!$this->authenticate()) {
            $this->sendException('401', 'Vous devez être identifié pour utiliser cette fonctionnalité.');
        }

        $id = JFactory::getApplication()->input->getInt('resource', null);
        if(!$this->checkRights($id)){
            $this->sendException('403', 'Vous netes pas autorisé à accéder à cette ressource.');
        }

        $versionController = new Easysdi_coreControllerVersion();
        if(!$versionController->remoteNewVersion())
        {
            $this->sendException('400', 'Une nouvelle version ne peut être créé.');
        }
        
        
        $responseNode = $this->response->createElementNS(self::nsSdi, 'sdi:coreserviceresponse','Nouvelle version créée.');
        $this->rootNode->appendChild($responseNode);
        $this->sendResponse();
        
       /* $viral = JFactory::getApplication()->input->getBool('viral', false);
        $config = JFactory::getApplication()->input->getString('config', null);*/

        
        
        
        if ((file_exists("./images/Dokumenten_AGI/replacing.txt"))) {
            $str_data = file_get_contents("./images/Dokumenten_AGI/replacing.txt");
            $data = json_decode($str_data, true);            
        };
    }

    /**
     * Send response 
     * 
     * @param type $code
     * @param type $response
     * 
     * @return void
     */
    private function sendResponse($code = 200, $response = null) {
        if ($response == null) {
            $response = $this->response;
        }
        header('Content-Type: application/xml; charset=utf-8');
        if ($code != 200) {  //if code is not 200, set the HTTP code to the value of error code
            header('HTTP/1.1 ' . $code . ' ' . $this->HTTPSTATUS[$code]);
        }
        echo $response->saveXML();
        JFactory::getApplication()->close();
    }

    /**
     * sendException - build an xml which describes an error
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
    private function sendException($code, $details = '') {
        $exception = $this->response->createElementNS(self::nsSdi, 'sdi:exception');
        $exception->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:code', $code));
        $exception->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:message', $this->HTTPSTATUS[$code]));
        $exception->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:details', $details));
        $this->rootNode->appendChild($exception);
        $this->sendResponse($code, $this->response);
    }

    /**
     * addAttribute - add an attribute to a DOMNode
     * 
     * @param DOMNode $parent - the DOMNode to which add the attribute
     * @param string $attrName - the attribute name
     * @param mixed $attrValue - the attribute value
     * 
     * @return void
     */
    private function addAttribute(&$parent, $attrName, $attrValue) {
        $attribute = $this->response->createAttribute($attrName);
        $attribute->value = $attrValue;
        $parent->appendChild($attribute);
    }

}
