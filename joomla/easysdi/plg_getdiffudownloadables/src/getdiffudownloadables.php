<?php
/**
 * @version     4.4.4
 * @package     plg_easysdi_getdiffudownloadables
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('_JEXEC') or die;

class PlgEasysdi_admin_infoGetdiffudownloadables extends JPlugin {

    protected $autoloadLanguage = true;

    public function onGetAdminInfos($context) {
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*) as nbre');
        $query->from('#__sdi_diffusion');
        $query->where('hasdownload = 1');
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        //Convert the stdClass object in an array
        $values = get_object_vars($rows[0]);
        $values = $values['nbre'];
        //Create the return array with all the infos
        return array(
            'info' => $values,
            'text' => JText::plural('PLG_EASYSDI_ADMIN_INFO_GETDIFFUDOWNLOADABLES_DOWNLOADABLES',$values)
        );
    }
}
