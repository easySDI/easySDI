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

Jimport('joomla.application.component.view');

class EasySDI_mapViewMap_context extends JView {

  /**
   * Display method for the map_filter view.
   */
  function display()
  {   
    $id = JRequest::getCmd('id', null);    	
    $user_id = JRequest::getCmd('user_id', null);    	
    $model = $this->getModel('map_context');
    $model->setId($id);  	
    $model->setUserId($user_id);
    // dynamically build the method to call on the model (e.g. getList or getItem).
    $method='get'.JRequest::getVar('layout');
    $map_contexts=$model->$method();
    echo json_encode(array(
      'map_contexts'=>$map_contexts,
      'totalCount'=>count($map_contexts)
    ));
  }
}
?>
