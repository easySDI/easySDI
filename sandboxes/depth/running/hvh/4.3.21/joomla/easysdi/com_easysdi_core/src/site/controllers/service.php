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
require_once JPATH_COMPONENT . '/controllers/version.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/factory/sdifactory.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/user/sdiuser.php';
require_once JPATH_ADMINISTRATOR.'/components/com_easysdi_core/helpers/curl.php';

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

    /**
     * 
     * 
     * @param type $resource_id
     * @return boolean
     */
    private function checkRights($resource_id) {
        $sdiUser = sdiFactory::getSdiUser();
        if (!$sdiUser->authorize($resource_id, sdiUser::resourcemanager) && !$sdiUser->isOrganismManager($resource_id)) {
            return false;
        }
        return true;
    }

    public function testVersion ()
    {
            $resource = $this->input->getInt('resource', null) ;
            $viral = $this->input->getBool('viral', null) ;
            $config = $this->input->getString('config',null) ;
            $user = $this->input->getString('user', null) ;
            $password = $this->input->getString('password', null) ;
                    
            $url = JUri::root();
           
            
            $url .= 'component/easysdi_core/service/newVersion?';
            if ($resource) $url .= '&resource='.$resource;
            if ($viral) $url .= '&viral='.$viral;
            if ($config) $url .= '&config='.urlencode($config);
            
            $this->curlHelper = new CurlHelper();
            $this->curlHelper->withreturn = true;
            $result = $this->curlHelper->get(array('url' => $url , 'user' => $user, 'password' => $password, 'authtype' => 'BASIC'));
            
            echo $result;
            
            die();
    }
    
    /**
     * Create a new version for the given resource.
     *      * 
     * @return void, XML response is sent.
     */
    public function newVersion() {
        try {
            if (!$this->authenticate()) {
                $this->sendException('401', 'A valid authentication is required.');
            }

            //The resource id. 
            $id = JFactory::getApplication()->input->getInt('resource', null);
            if ($id == null) {
                $this->sendException('404', 'A resource must be specified.');
            }
            if (!$this->checkRights($id)) {
                $this->sendException('403', 'You are not authorized to execute this action on the resource.');
            }

            //If viral boolean parameter is provided, its value will overwrite the viral attribute of the resourcetypelink
            //If not provided, the resourcetypelink viral attribute value will be used.
            $viral = JFactory::getApplication()->input->getBool('viral', null);

            //The config parameter can be null.
            //If it's provided, the target resource(file or web service) must be reachable.
            $config = JFactory::getApplication()->input->getString('config', null);
            if ($config) {
                $str_data = file_get_contents($config);
                if($str_data === FALSE)
                {
                    $this->sendException('404', 'The specified configuration resource cannot be reached.', $config);
                }
                $data = json_decode($str_data, true);
            }

            $versionController = new Easysdi_coreControllerVersion();
            $result = $versionController->remoteNewVersion($data, $viral);
            if ($result == false) {
                $this->sendException('500', 'The server encountered an error while creating the new version.');
            }

            $responseNode = $this->response->createElementNS(self::nsSdi, 'sdi:response');
            $this->rootNode->appendChild($responseNode);

            $replacements = $result['replacement'];
            unset($result['replacement']);

            foreach ($result as $version) {
                $versionNode = $this->response->createElementNS(self::nsSdi, 'sdi:version');
                $this->addAttribute($versionNode, 'state', 'created');
                $responseNode->appendChild($versionNode);
                $versionNode->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:resource', $version['resource_id']));
                $versionNode->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:name', $version['name']));

                $this->buildReplacementResult($replacements,$version['resource_id'], $versionNode);

                if ($version['children']) {
                    $childrenNode = $this->response->createElementNS(self::nsSdi, 'sdi:children');
                    $versionNode->appendChild($childrenNode);
                }
                foreach ($version['children'] as $child) {
                    $childNode = $this->response->createElementNS(self::nsSdi, 'sdi:version');
                    $this->addAttribute($childNode, 'state', 'created');
                    $childNode->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:resource', $child['resource_id']));
                    $childNode->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:name', $child['name']));
                    
                    $this->buildReplacementResult($replacements, $child['resource_id'], $childNode);
                    
                    $childrenNode->appendChild($childNode);
                }
            }

            $this->sendResponse();
        } catch (Exception $exc) {
            $this->sendException('500', $exc->getMessage());
        }
    }

    /**
     * Send HTTP response 
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
    private function sendException($code, $details = '', $target= '') {
        $exception = $this->response->createElementNS(self::nsSdi, 'sdi:exception');
        $exception->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:code', $code));
        $exception->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:message', $this->HTTPSTATUS[$code]));
        $exception->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:details', $details));
        $exception->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:target', $target));
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

    private function buildReplacementResult($replacements,$resource_id, $node) {
        foreach ($replacements as $replacement) {
            if ($replacement['resource_id'] == $resource_id) {
                $replacements = $this->response->createElementNS(self::nsSdi, 'sdi:replacements');
                $node->appendChild($replacements);
                foreach ($replacement['success'] as $success) {
                    $replacementsucces = $this->response->createElementNS(self::nsSdi, 'sdi:replacement');
                    $this->addAttribute($replacementsucces, 'state', 'success');
                    $replacements->appendChild($replacementsucces);
                    $replacementsucces->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:xpath', $success['xpath']));
                    $replacementsucces->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:value', $success['value']));
                }
                foreach ($replacement['failed'] as $failed) {
                    $replacementfailed = $this->response->createElementNS(self::nsSdi, 'sdi:replacement');
                    $this->addAttribute($replacementfailed, 'state', 'failed');
                    $replacements->appendChild($replacementfailed);
                    $replacementfailed->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:xpath', $failed['xpath']));
                    $replacementfailed->appendChild($this->response->createElementNS(self::nsSdi, 'sdi:value', $failed['value']));
                }
            }
        }
    }

}
