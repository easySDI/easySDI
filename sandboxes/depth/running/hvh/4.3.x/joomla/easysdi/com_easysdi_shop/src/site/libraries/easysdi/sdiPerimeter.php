<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
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
    public $allowedbuffer;

    function __construct($session_perimeter) {
        if (empty($session_perimeter))
            return;
        $this->id = $session_perimeter->perimeter_id;
        $this->allowedbuffer = $session_perimeter->buffer;
        $this->loadData();
    }

    private function loadData() {
        try {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                    ->select('p.*, l.service_id, l.servicetype,l.layername as maplayername,l.istiled,l.opacity, l.isindoor,l.levelfield')
                    ->from('#__sdi_perimeter p')
                    ->innerJoin('#__sdi_maplayer l ON l.id=p.maplayer_id')
                    ->where('p.id = ' . (int) $this->id);

            $db->setQuery($query);
            $item = $db->loadObject();

            $params = get_object_vars($item);
            foreach ($params as $key => $value) {
                $this->$key = $value;
            }

            if(!empty($this->service_id)):
                switch ($this->servicetype):
                    case 'virtual':
                        $servicetable = '#__sdi_virtualservice';
                        break;
                    case 'physical':
                        $servicetable = '#__sdi_physicalservice';
                        break;                    
                endswitch;
                $query = $db->getQuery(true)
                            ->select('s.*')
                            ->from($servicetable.' s')
                            ->where('s.id = ' . (int) $this->service_id);
                $db->setQuery($query);
                $wmsservice = $db->loadObject();
                if(!empty($wmsservice->resourceurl)):
                    $this->wmsurl = $wmsservice->resourceurl;                
                elseif(!empty($wmsservice->reflectedurl)):
                    $this->wmsurl = $wmsservice->reflectedurl;
                else:
                    $this->wmsurl = $wmsservice->url;    
                endif;
            endif;
            
            //Old version
//            if (!empty($this->wmsservice_id)):  
//                if ($this->wmsservicetype_id == 1):
//                    $query = $db->getQuery(true)
//                            ->select('p.*')
//                            ->from('#__sdi_physicalservice p')
//                            ->where('p.id = ' . (int) $this->wmsservice_id);
//                    $db->setQuery($query);
//                    $wmsservice = $db->loadObject();
//                    $this->wmsurl = $wmsservice->resourceurl;
//                else :
//                    $query = $db->getQuery(true)
//                            ->select('p.*')
//                            ->from('#__sdi_virtualservice p')
//                            ->where('p.id = ' . (int) $this->wmsservice_id);
//                    $db->setQuery($query);
//                    $wmsservice = $db->loadObject();
//                    $this->wmsurl = $wmsservice->reflectedurl;
//                    if ($this->wmsurl == '')
//                        $this->wmsurl = $wmsservice->url;
//                endif;
//            endif;

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

    public function setAllowedBuffer($extractions) {
        if (empty($extractions))
            return;

        foreach ($extractions as $extraction):
            foreach ($extraction->perimeters as $perimeter):
                if ($perimeter->id == $this->id):
                    if ($perimeter->allowedbuffer == 0):
                        $this->allowedbuffer = 0;
                        return $this->allowedbuffer;
                    endif;
                endif;
            endforeach;
        endforeach;
    }

}

?>
