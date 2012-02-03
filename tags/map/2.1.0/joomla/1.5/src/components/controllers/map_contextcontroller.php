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

defined('_JEXEC') or die ('Restricted access');
jimport('joomla.application.component.controller');
jimport( 'joomla.methods' );

class EasySDI_mapControllerMap_context extends JController {

  function getlist()
  {
    JRequest::setVar('layout', 'list');
    JRequest::setVar('view', 'map_context');
    parent::display();
  }

  function getitem()
  {
    JRequest::setVar('layout', 'item');
    JRequest::setVar('view', 'map_context');
    parent::display();
  }

  function save()
  {
    $model=$this->getModel('map_context');
    // Use $_POST directly as JRequest process the XML in WMC data.
    $array=array(
      'WMC_text'=>$_POST['WMC_text'],
      'user_id'=>JRequest::getVar('user_id')
    );
    if(get_magic_quotes_gpc())
      $array['WMC_text']=stripslashes($array['WMC_text']);

    if ($model->save($array)) {
      echo "OK";
    } else {
    	echo 'Problem occurred saving data';
    }
  }
  
  function delete()
  {
    $model=$this->getModel('map_context');
    $model->delete(JRequest::getVar('id'));
    echo "OK";
  }

}

?>
