<?php
// Set flag that this is a parent file.
const _JEXEC = 1;

//error_reporting(E_ALL | E_NOTICE);
//ini_set('display_errors', 1);

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
    require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
    define('JPATH_BASE', dirname(__DIR__));
    require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_LIBRARIES . '/import.php';
require_once JPATH_LIBRARIES . '/import.legacy.php';
require_once JPATH_LIBRARIES . '/joomla/factory.php' ;
require_once JPATH_LIBRARIES . '/joomla/platform.php' ;
require_once JPATH_BASE . '/includes/framework.php' ;

//define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_BASE.'/administrator/components/com_easysdi_shop');
//define('JPATH_COMPONENT', JPATH_BASE.'/components/com_easysdi_shop');


// Load Library language
$lang = JFactory::getLanguage();

// Try the files_joomla file in the current language (without allowing the loading of the file in the default language)
$lang->load('files_joomla.sys', JPATH_SITE, null, false, false)
// Fallback to the files_joomla file in the default language
|| $lang->load('files_joomla.sys', JPATH_SITE, null, true);

// Load the configuration
require_once JPATH_CONFIGURATION . '/configuration.php';
require_once JPATH_ADMINISTRATOR.'/components/com_easysdi_core/helpers/curl.php';

/**
 * This script will fetch the update information for all extensions and store
 * them in the database, speeding up your administrator.
 *
 * @since  2.5
 */
class Easysdicron extends JApplicationCli
{
	/**
	 * Entry point for the script
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function doExecute()
	{
            $this->out('Start...');
            
/**
 * Input must be given like this :
 * D:\htdocs\geodbmeta\cli>php easysdi_cron.php --id=1233
 * 
 */
            $resource = $this->input->getInt('resource', null) ;
            $viral = $this->input->getBool('viral', null) ;
            $config = $this->input->getString('config',null) ;
            $user = $this->input->getString('user', null) ;
            $password = $this->input->getString('password', null) ;
                    
            $url = JUri::root();
            $this->out($url);
            
            $this->out($config);
            
            $this->out($user);
            
            $this->out($password);
            
            
            $url .= 'component/easysdi_core/service/newVersion?';
            if ($resource) $url .= '&resource='.$resource;
            if ($viral) $url .= '&viral='.$viral;
            if ($config) $url .= '&config='.urlencode($config);
            
            $this->out($url);
            
            $this->curlHelper = new CurlHelper();
            $this->curlHelper->simplified = true;
            $this->curlHelper->get(array('url' => $url , 'user' => $user, 'password' => $password));
            
            //$app = JFactory::getApplication('site');
           /* $db = JFactory::getDbo();            
            $q = "SELECT * from #__sdi_version";
            $db->setQuery($q);
            if( $rows = $db->loadObjectList() ) {
                foreach($rows as $row){
                    $this->out('Metadata '.$row->name);                    
                }
            }*/
            
            $this->out('...Finish.');
	}
}

JApplicationCli::getInstance('Easysdicron')->execute();
