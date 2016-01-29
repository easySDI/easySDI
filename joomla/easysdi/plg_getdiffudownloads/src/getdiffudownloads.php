<?php
/**
 * @version		4.4.0
 * @package     plg_easysdi_getdiffudownloads
 * @copyright	
 * @license		
 * @author		
 */

defined('_JEXEC') or die;

class PlgEasysdi_admin_infoGetdiffudownloads extends JPlugin {

    protected $autoloadLanguage = true;

    public function onGetAdminInfos($context) {
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*) as nbre');
        $query->from('#__sdi_diffusion_download');
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        //Convert the stdClass object in an array
        $values = get_object_vars($rows[0]);
        $values = $values['nbre'];
        //Create the return array with all the infos
        return array(
            'info' => $values,
            'text' => JText::plural('PLG_EASYSDI_ADMIN_INFO_GETDIFFUDOWNLOADS_DOWNLOADS',$values)
        );
    }
}
