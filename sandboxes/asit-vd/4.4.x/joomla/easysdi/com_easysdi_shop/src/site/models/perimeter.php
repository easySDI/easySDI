<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

/**
 * Easysdi_shop model.
 */
class Easysdi_shopModelPerimeter extends JModelLegacy {

    var $_item = null;

    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($id = null) {
        if ($this->_item === null) {
            $this->_item = false;

            try {
                $db = JFactory::getDbo();

                $query = $db->getQuery(true)
                        ->select('p.*, l.service_id, l.servicetype,l.layername as maplayername,l.istiled,l.opacity, l.isindoor,l.levelfield')
                        ->from('#__sdi_perimeter p')
                        ->leftJoin('#__sdi_maplayer l ON l.id=p.maplayer_id')
                        ->where('p.id = ' . (int) $id);

                $db->setQuery($query);
                $item = $db->loadObject();

                $params = get_object_vars($item);
                $perimeter = new stdClass();
                foreach ($params as $key => $value) {
                    $perimeter->$key = $value;
                }

                if (!empty($perimeter->service_id)):
                    switch ($perimeter->servicetype):
                        case 'physical':
                            $query = $db->getQuery(true)
                                    ->select('s.*')
                                    ->from('#__sdi_physicalservice  s')
                                    ->where('s.id = ' . (int) $perimeter->service_id);
                            $db->setQuery($query);
                            $wmsservice = $db->loadObject();
                            $perimeter->wmsurl = $wmsservice->resourceurl;
                            $perimeter->server = $wmsservice->server_id;
                            break;
                        case 'virtual':
                            $query = $db->getQuery(true)
                                    ->select('s.*')
                                    ->from('#__sdi_virtualservice s')
                                    ->where('s.id = ' . (int) $perimeter->service_id);
                            $db->setQuery($query);
                            $wmsservice = $db->loadObject();
                            if (!empty($wmsservice->reflectedurl)):
                                $perimeter->wmsurl = $wmsservice->reflectedurl;
                            else:
                                $perimeter->wmsurl = $wmsservice->url;
                            endif;

                            //server type
                            $query = $db->getQuery(true);
                            $query->select('p.server_id');
                            $query->from('#__sdi_virtualservice AS v');
                            $query->join('LEFT', '#__sdi_virtual_physical AS vp ON vp.virtualservice_id=v.id');
                            $query->join('LEFT', '#__sdi_physicalservice AS p ON vp.physicalservice_id=p.id');
                            $query->group('p.server_id');
                            $query->where('v.id = ' . (int) $perimeter->service_id);
                            $db->setQuery($query);
                            $services = $db->loadColumn();
                            if (count($services) > 1) {
                                //virtual service aggregates more than one kind of physical services
                                $perimeter->server = 3;
                            } else {
                                $perimeter->server = $services[0];
                            }
                            break;
                    endswitch;

                    $perimeter->source = $wmsservice->alias;
                endif;

                if (!empty($perimeter->wfsservice_id)):
                    if ($perimeter->wfsservicetype_id == 1):
                        $query = $db->getQuery(true)
                                ->select('p.*')
                                ->from('#__sdi_physicalservice p')
                                ->where('p.id = ' . (int) $perimeter->wfsservice_id);
                        $db->setQuery($query);
                        $wfsservice = $db->loadObject();
                        $perimeter->wfsurl = $wfsservice->resourceurl;
                    else :
                        $query = $db->getQuery(true)
                                ->select('p.*')
                                ->from('#__sdi_virtualservice p')
                                ->where('p.id = ' . (int) $perimeter->wfsservice_id);
                        $db->setQuery($query);
                        $wfsservice = $db->loadObject();
                        $perimeter->wfsurl = $wfsservice->reflectedurl;
                        if ($perimeter->wfsurl == '')
                            $perimeter->wfsurl = $wfsservice->url;
                    endif;
                endif;
            } catch (JDatabaseException $e) {
                
            }

            $this->_item = $perimeter;
        }

        return $this->_item;
    }

}
