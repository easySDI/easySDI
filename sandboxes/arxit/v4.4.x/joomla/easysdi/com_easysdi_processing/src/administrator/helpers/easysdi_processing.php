<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Cadastre component helper.
 */
abstract class Easysdi_processingHelper
{
    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '', $itemName = '') {
        require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/easysdi_core.php';
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_core');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_user');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_catalog');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_shop');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_processing');
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_PROCESSING_TITLE_PROCESSINGS'), 'index.php?option=com_easysdi_processing&view=processings', $vName == 'processings'
        );
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_PROCESSING_TITLE_ORDERS'), 'index.php?option=com_easysdi_processing&view=orders', $vName == 'orders'
        );
        if ($vName == 'propertyvalues') {
            JHtmlSidebar::addEntry(
                    Easysdi_coreHelper::getMenuSpacer(3) . $itemName, '#', $vName == 'propertyvalues'
            );
        };
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_service');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_map');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_monitor');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_dashboard');
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActions() {
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_easysdi_processing';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

    public static function getProcessTypes(){

      //!TODO use accessscope

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('pt.*')
          ->from('#__processing AS pt');

       $query->select("GROUP_CONCAT( user_id SEPARATOR ',' ) AS processing_type_obs")
          ->join('LEFT', '#__sdi_processing_obs AS pto on pt.id = pto.processing_id')
          ->group('pt.id');

        $db->setQuery($query);

        $data = $db->loadObjectList();
        return $data;

  }

  /**
     * Creates a list of range options used in filter select list
     * used in com_users on users view
     *
     * @return  array
     *
     * @since   2.5
     */
    public static function getRangeOptions() {
        $options = array(
            JHtml::_('select.option', 'today', JText::_('COM_EASYSDI_PROCESSING_OPTION_RANGE_TODAY')),
            JHtml::_('select.option', 'past_week', JText::_('COM_EASYSDI_PROCESSING_OPTION_RANGE_PAST_WEEK')),
            JHtml::_('select.option', 'past_1month', JText::_('COM_EASYSDI_PROCESSING_OPTION_RANGE_PAST_1MONTH')),
            JHtml::_('select.option', 'past_3month', JText::_('COM_EASYSDI_PROCESSING_OPTION_RANGE_PAST_3MONTH')),
            JHtml::_('select.option', 'past_6month', JText::_('COM_EASYSDI_PROCESSING_OPTION_RANGE_PAST_6MONTH')),
            JHtml::_('select.option', 'past_year', JText::_('COM_EASYSDI_PROCESSING_OPTION_RANGE_PAST_YEAR')),
            JHtml::_('select.option', 'post_year', JText::_('COM_EASYSDI_PROCESSING_OPTION_RANGE_POST_YEAR')),
        );
        return $options;
    }


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
    ->from('#__sdi_processing AS p')
    ->where('p.state = 1');


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

public static function getProcessById($id){

    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    $query->select('p.*')
    ->from('#__sdi_processing AS p')
    ->where('p.id = "'.$id.'"');

    $query->select("GROUP_CONCAT( sdi_user_id SEPARATOR ',' ) AS processing_obs")
    ->join('LEFT', '#__sdi_processing_obs AS pobs on p.id = pobs.processing_id');


    $query->select("count( po.id ) AS t_count")
    ->join('LEFT', '#__sdi_processing_order AS po on p.id = po.processing_id');

    $query->group('p.id');

    $db->setQuery($query);

    $data = $db->loadObject();
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

    //$roles=[];
    $user=sdiFactory::getSdiUser();

    if ( $user->juser->authorise('core.admin')) $roles[]='superuser';
    if ( $data->user_id == $user->id) $roles[]='creator';
    if ( $data->processing_contact_id == $user->id) $roles[]='contact';
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

/**
     * Get a phrase like "2 hours ago" from a date. Units are: Year, Month, day, hour, minute, seconds
     * @param DateTime $date
     * @return String "xx timeUnit ago"
     */
    public static function getRelativeTimeString(DateTime $date) {
        $current = new DateTime;
        $diff = $current->diff($date);
        $units = array("YEAR" => $diff->format("%y"),
            "MONTH" => $diff->format("%m"),
            "DAY" => $diff->format("%d"),
            "HOUR" => $diff->format("%h"),
            "MINUTE" => $diff->format("%i"),
            "SECOND" => $diff->format("%s"),
        );
        $out = JText::_('COM_EASYSDI_PROCESSING_TIME_NOW');
        foreach ($units as $unit => $amount) {
            if (empty($amount)) {
                continue;
            }
            $out = JText::plural('COM_EASYSDI_PROCESSING_TIME_' . $unit . '_AGO', $amount);
            break;
        }
        return $out;
    }

    /**
     *
     * Rebuild url from its different parts - check http_build_url() PHP Manual
     * http_build_url() is part of pecl_http
     * unparse_url() does the work with or without http_build_url()
     *
     * @param mixed $url | string or array as returned by parse_url()
     * @param mixed $parts | same as $url
     * @return string
     */
    public static function unparse_url($url, $parts = array()) {
        if (function_exists('http_build_url'))
            return http_build_url($url, $parts);

        $parsed_url = array_merge($url, $parts);

        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

}
?>