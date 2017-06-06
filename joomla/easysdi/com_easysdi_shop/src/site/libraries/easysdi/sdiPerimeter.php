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

class sdiPerimeter {

    public $id;
    public $name;
    public $alias;
    public $wmsurl;
    public $wfsurl;
    
    function __construct($session_perimeter) {
        if (empty($session_perimeter))
            return;
        $this->id = $session_perimeter->perimeter_id;
        $this->loadData();
    }

    private function loadData() {
        try {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                    ->select('p.*, l.service_id, l.servicetype,l.layername as maplayername,l.istiled,l.opacity, l.isindoor,l.levelfield')
                    ->from('#__sdi_perimeter p')
                    ->leftJoin('#__sdi_maplayer l ON l.id=p.maplayer_id')
                    ->where('p.id = ' . (int) $this->id);

            $db->setQuery($query);
            $item = $db->loadObject();

            $params = get_object_vars($item);
            foreach ($params as $key => $value) {
                $this->$key = $value;
            }
            
            if (!empty($this->service_id)):
                switch ($this->servicetype):
                    case 'physical':
                        $query = $db->getQuery(true)
                                ->select('s.*')
                                ->from('#__sdi_physicalservice  s')
                                ->where('s.id = ' . (int) $this->service_id);
                        $db->setQuery($query);
                        $wmsservice = $db->loadObject();
                        $this->wmsurl = $wmsservice->resourceurl;
                        $this->server = $wmsservice->server_id;
                        break;
                    case 'virtual':
                        $query = $db->getQuery(true)
                                ->select('s.*')
                                ->from('#__sdi_virtualservice s')
                                ->where('s.id = ' . (int) $this->service_id);
                        $db->setQuery($query);
                        $wmsservice = $db->loadObject();
                        if (!empty($wmsservice->reflectedurl)):
                            $this->wmsurl = $wmsservice->reflectedurl;
                        else:
                            $this->wmsurl = $wmsservice->url;
                        endif;

                        //server type
                        $query = $db->getQuery(true);
                        $query->select('p.server_id');
                        $query->from('#__sdi_virtualservice AS v');
                        $query->join('LEFT', '#__sdi_virtual_physical AS vp ON vp.virtualservice_id=v.id');
                        $query->join('LEFT', '#__sdi_physicalservice AS p ON vp.physicalservice_id=p.id');
                        $query->group('p.server_id');
                        $query->where('v.id = ' .  (int) $this->service_id);
                        $db->setQuery($query);
                        $services = $db->loadColumn();
                        if (count($services) > 1) {
                            //virtual service aggregates more than one kind of physical services
                            $this->server = 3;
                        } else {
                            $this->server = $services[0];
                        }
                        break;
                endswitch;

                $this->source = $wmsservice->alias;
            endif;

            if (!empty($this->wfsservice_id)):
                if ($this->wfsservicetype_id == 1):
                    $query = $db->getQuery(true)
                            ->select('p.*')
                            ->from('#__sdi_physicalservice p')
                            ->where('p.id = ' . (int) $this->wfsservice_id);
                    $db->setQuery($query);
                    $wfsservice = $db->loadObject();
                    $this->wfsurl = $wfsservice->resourceurl;
                else :
                    $query = $db->getQuery(true)
                            ->select('p.*')
                            ->from('#__sdi_virtualservice p')
                            ->where('p.id = ' . (int) $this->wfsservice_id);
                    $db->setQuery($query);
                    $wfsservice = $db->loadObject();
                    $this->wfsurl = $wfsservice->reflectedurl;
                    if ($this->wfsurl == '')
                        $this->wfsurl = $wfsservice->url;
                endif;
            endif;
        } catch (JDatabaseException $e) {
            
        }
    }

//    public function setAllowedBuffer($extractions) {
//        if (empty($extractions))
//            return;
//
//        foreach ($extractions as $extraction):
//            foreach ($extraction->perimeters as $perimeter):
//                if ($perimeter->id == $this->id):
//                    if ($perimeter->allowedbuffer == 0):
//                        $this->allowedbuffer = 0;
//                        return $this->allowedbuffer;
//                    endif;
//                endif;
//            endforeach;
//        endforeach;
//    }

}

?>
