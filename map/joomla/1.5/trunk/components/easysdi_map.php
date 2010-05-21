<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.filesystem.file');

//echo ("<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAb_rWlMmsfxvoBseW9RqtUhTUcaP81GTesBL2xOc2rOtDaf2mohQvHDM-LtXmkLFqE2eQahvoQ5TRoQ'></script>");

$c=JRequest::getCmd('controller', '');

// Get the controller path
if ($c!='') {
	// use a specified controller
  $path = JPATH_COMPONENT.DS.'controllers'.DS.$c.'controller.php';	
} else {
	// use a default controller
	$path = JPATH_COMPONENT.DS.'controllers'.DS.'controller.php';	
}

if (JFile::exists($path))
{
	require_once($path);
}
else
{
  // Controller missing
  JError::raiseError('500', JText::_('Unknown controller'));
}
// instantiate the controller, building the classname dynamically
$c = 'EasySDI_mapController'.$c;
$controller = new $c();
// if accessing the proxy, use the raw format - can't be in URL because of clash with the FORMAT of WMS
if (JRequest::getCmd('view')=='getfeatureinfo' || JRequest::getCmd('view')=='proxy' || JRequest::getCmd('view')=='printMap') {	
  $doc = &JFactory::getDocument();
  $docRaw = &JDocument::getInstance('raw');
  $doc = $docRaw;  	
}

// run the task on the controller
$controller->execute(JRequest::getCmd('task', 'display'));
$controller->redirect();

?>