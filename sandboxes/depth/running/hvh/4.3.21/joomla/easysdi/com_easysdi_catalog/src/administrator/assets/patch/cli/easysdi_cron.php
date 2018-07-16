<?php

// Set flag that this is a parent file.
const _JEXEC = 1;

//error_reporting(E_ALL | E_NOTICE);
//ini_set('display_errors', 1);
// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php')) {
    require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(__DIR__));
    require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_LIBRARIES . '/import.php';
require_once JPATH_LIBRARIES . '/import.legacy.php';
require_once JPATH_LIBRARIES . '/joomla/factory.php';
require_once JPATH_LIBRARIES . '/joomla/platform.php';
require_once JPATH_BASE . '/includes/framework.php';

// Load Library language
$lang = JFactory::getLanguage();

// Try the files_joomla file in the current language (without allowing the loading of the file in the default language)
$lang->load('files_joomla.sys', JPATH_SITE, null, false, false)
// Fallback to the files_joomla file in the default language
        || $lang->load('files_joomla.sys', JPATH_SITE, null, true);

// Load the configuration
require_once JPATH_CONFIGURATION . '/configuration.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/curl.php';

/**
 */
class Easysdicron extends JApplicationCli {

    /**
     * Entry point for the script	 
     */
    public function doExecute() {
        // Fool the system to avoid Notice
        $_SERVER['HTTP_HOST'] = 'domain.com';

        $this->out(date("Y-m-d H:i:s") . ' | START...');

        /**
         * Input must be given like this :
         * C:\[PHP Installation Folder]>php D:\htdocs\geobackend\cli\easysdi_cron.php --resource=1233 --user=foo --password=bar --config=http%3A%2F%2Flocalhost%2Fgeobackend%2Fmedia%2Feasysdi%2Fconfig.json
         * 
         */
        $resource = $this->input->getInt('resource', null);
        $viral = $this->input->getBool('viral', null);
        $config = $this->input->getString('config', null);
        $user = $this->input->getString('user', null);
        $password = $this->input->getString('password', null);

        $url = JUri::root();
        $url .= 'component/easysdi_core/service/newVersion?';
        if ($resource)
            $url .= '&resource=' . $resource;
        if ($viral)
            $url .= '&viral=' . $viral;
        if ($config)
            $url .= '&config=' . urlencode($config);

        $this->curlHelper = new CurlHelper();
        $this->curlHelper->withreturn = true;
        $result = $this->curlHelper->get(array('url' => $url, 'user' => $user, 'password' => $password, 'authtype' => 'BASIC'));
        $doc = new DOMDocument();
        $doc->loadXML($result);
        if ($doc == false) {
            $this->out(date("Y-m-d H:i:s") . ' | SERVER ERROR : The remote server returned an unexpected response.');
        }
        else
        {
            if ($doc->getElementsByTagName("exception")->length > 0) {
                $code = $doc->getElementsByTagName("code")->item(0)->nodeValue;
                $message = $doc->getElementsByTagName("message")->item(0)->nodeValue;
                $details = $doc->getElementsByTagName("details")->item(0)->nodeValue;
                $this->out(date("Y-m-d H:i:s") . ' | SERVICE ERROR : ' . $code . ' | ' . $message . ' | ' . $details );
            }else{
               $this->out(date("Y-m-d H:i:s") . ' | SERVICE RESPONSE : ');
               $this->out($result); 
            }
        }
        $this->out(date("Y-m-d H:i:s") . ' | ...FINISH');        
    }

}
JApplicationCli::getInstance('Easysdicron')->execute();
