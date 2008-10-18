<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */

defined('_JEXEC') or die('Restricted access');

jimport("joomla.html.pagination");

jimport("joomla.html.pane");

require_once(JPATH_COMPONENT.DS.'core'.DS.'easysdi.config.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'partner.site.easysdi.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'partner.site.easysdi.html.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'partner.site.easysdi.class.php');
require_once(JPATH_COMPONENT.DS.'js'.DS.'partner.site.easysdi.php');



include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');


$language=&JFactory::getLanguage();
$language->load('com_easysdi_partner');

?>
<?php
$task = JRequest::getVar('task');


switch($task){	
	case "createAffiliate":
		SITE_partner::editAffiliatePartner(0);
		break;
	case "editAffiliateById":
		$affiliate_id = JRequest::getVar('affiliate_id');
		SITE_partner::editAffiliatePartner($affiliate_id);
		break;
	case "listAffiliatePartner":
		SITE_partner::listPartner();
		break;
	case "saveAffiliatePartner":
		SITE_partner::savePartner();
		break;
	case "editAffiliatePartner":
		SITE_partner::editAffiliatePartner();
		break;	
	case "savePartner":
		SITE_partner::savePartner();				
		break;	
	case "editPartner":	
			
		SITE_partner::editPartner();
		break;
	
	case "showPartner":
	default:	
		SITE_partner::showPartner();
		break;
		
				
}
 ?>