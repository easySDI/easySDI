<?php
/*------------------------------------------------------------------------
# easysdi_processing.php - Easysdi_processing Component
# ------------------------------------------------------------------------
# author    Thomas Portier
# copyright Copyright (C) 2015. All Rights Reserved
# license   Depth France
# website   www.depth.fr
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


abstract class Easysdi_processingHelper
{



  public static function getUserProcessOrders(){

    $user=sdiFactory::getSdiUser();

    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    $query->select('po.*')
    ->from('#__sdi_processing_order AS po')
    ->where('po.created_by = ' . (int) $user->id);

    $query->select('p.name as processing_name, p.contact_id, p.parameters as processing_params, p.access, p.access_id')
    ->join('LEFT', '#__sdi_processing AS p on p.id = po.processing_id');

    $query->select('u.name as contact_name')
    ->join('LEFT', '#__sdi_user AS su on su.id = p.contact_id')
    ->join('LEFT', '#__users AS u on su.user_id = u.id');

    $db->setQuery($query);

    $data = $db->loadObjectList();
    return $data;
}


public static function getOrders($alias){


    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    $query->select('po.*')
    ->from('#__sdi_processing_order AS po');



    $query->select('p.name as processing_name, p.contact_id, p.parameters as processing_params, p.access, p.access_id')
    ->where('p.alias = "'.$alias.'"')
    ->join('LEFT', '#__sdi_processing AS p on p.id = po.processing_id');

    $query->select('u.name as created_by_name')
    ->join('LEFT', '#__sdi_user AS su on su.id = po.created_by')
    ->join('LEFT', '#__users AS u on su.user_id = u.id');

    $db->setQuery($query);

    $data = $db->loadObjectList();
    return $data;
}


public static function getUserProcessOrdersCount(){

    $user=sdiFactory::getSdiUser();

    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    $query->select('count(id) as count')
    ->from('#__sdi_processing_order AS po')
    ->where('po.created_by = ' . (int) $user->id);

    $db->setQuery($query);

    $data = $db->loadObject();
    return $data->count;
}



public static function getUserProcesses(){
    // !todo access level
    $user=sdiFactory::getSdiUser();

    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    $query->select('p.*')
    ->from('#__sdi_processing AS p');


    $query->select("GROUP_CONCAT( sdi_user_id SEPARATOR ',' ) AS processing_obs")
    ->join('LEFT', '#__sdi_processing_obs AS pobs on p.id = pobs.processing_id');

    $query->select("count( po.id ) AS t_count")
    ->join('LEFT', '#__sdi_processing_order AS po on p.id = po.processing_id');


    $query->group('p.id');

    $db->setQuery($query);

    $data = $db->loadObjectList();
    return $data;

}



public static function getContactProcesses(){

    $user=sdiFactory::getSdiUser();

    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    $query->select('p.*')
    ->from('#__sdi_processing AS p')
    ->where('p.contact_id = ' . (int) $user->id);

    $query->select("count( po.id ) AS t_new_count")
    ->join('LEFT', '#__sdi_processing_order AS po on p.id = po.processing_id AND po.status = "new"');

    $query->select("count( po2.id ) AS t_active_count")
    ->join('LEFT', '#__sdi_processing_order AS po2 on p.id = po2.processing_id AND po2.status = "active"');

    $query->group('p.id');
    $db->setQuery($query);

    $data = $db->loadObjectList();
    return $data;

}



public static function getProcess($alias){

    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    $query->select('p.*')
    ->from('#__sdi_processing AS p')
    ->where('p.alias = "'.$alias.'"');

    $query->select("GROUP_CONCAT( sdi_user_id SEPARATOR ',' ) AS processing_obs")
    ->join('LEFT', '#__sdi_processing_obs AS pobs on p.id = pobs.processing_id');


    $query->select("count( po.id ) AS t_count")
    ->join('LEFT', '#__sdi_processing_order AS po on p.id = po.processing_id');

    $query->group('p.id');

    $db->setQuery($query);

    $data = $db->loadObject();
    return $data;

}


public static function getOrder($id){

    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    $query->select('po.*')
    ->from('#__sdi_processing_order AS po')
    ->where('po.id = '.$id);


    $query->select("p.name as processing_name, p.contact_id, p.parameters as processing_parameters, p.access, p.access_id")
    ->join('LEFT', '#__sdi_processing AS p on p.id = po.processing_id');

    $query->select("GROUP_CONCAT( sdi_user_id SEPARATOR ',' ) AS processing_obs")
    ->join('LEFT', '#__sdi_processing_obs AS pobs on p.id = pobs.processing_id');


    $query->select('u.name as created_by_name')
    ->join('LEFT', '#__sdi_user AS su on su.id = po.created_by')
    ->join('LEFT', '#__users AS u on su.user_id = u.id');

    $query->select('u2.name as contact_name')
    ->join('LEFT', '#__sdi_user AS su2 on su2.id = p.contact_id')
    ->join('LEFT', '#__users AS u2 on su2.user_id = u2.id');

    $query->group('po.id');

    $db->setQuery($query);

    $data = $db->loadObject();
    return $data;

}



public static function getCurrentUserRolesOnData($data){

    $roles=[];
    $user=sdiFactory::getSdiUser();

    if ( $user->juser->authorise('core.admin')) $roles[]='superuser';
    if ( $data->created_by == $user->id) $roles[]='creator';
    if ( $data->contact_id == $user->id) $roles[]='contact';
    if ( in_array($user->id, explode(',', $data->processing_obs)) ) $roles[]='obs';
    return $roles;
}




public static function getFileInfo($file){
    $res=pathinfo($file);
    unset($res['dirname']);
    return array_merge($res,
        array(
            "size" => filesize($file),
            "mime_type" => finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file),
            "charset" => finfo_file(finfo_open(FILEINFO_MIME_ENCODING), $file)
            )
        );

}


}
