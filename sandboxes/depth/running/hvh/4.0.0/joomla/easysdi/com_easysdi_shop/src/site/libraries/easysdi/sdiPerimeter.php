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

    var $id;
    var $name;
    var $alias;
    var $wmsurl;
    var $wfsurl;
    var $allowedbuffer;

    function __construct($session_perimeter) {
        if (empty($session_perimeter))
            return;
        $this->id = $session_perimeter->perimeter_id;
        $this->loadData();
    }

    private function loadData() {
        try {
            $lang = JFactory::getLanguage();
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__sdi_perimeter p')
                    ->where('p.id = ' . $this->id)
            ;

            $db->setQuery($query);
            $item = $db->loadObject();

            $params = get_object_vars($item);
            foreach ($params as $key => $value) {
                $this->$key = $value;
            }

            if (!empty($this->wmsservice_id)):
                if ($this->wmsservicetype_id == 1):
                    $query = $db->getQuery(true)
                            ->select('p.*')
                            ->from('#__sdi_physicalservice p')
                            ->where('p.id = ' . $this->wmsservice_id);
                else :
                    $query = $db->getQuery(true)
                            ->select('p.*')
                            ->from('#__sdi_virtualservice p')
                            ->where('p.id = ' . $this->wmsservice_id);
                endif;

                $db->setQuery($query);
                $wmsservice = $db->loadObject();
                $this->wmsurl = $wmsservice->resourceurl;
            endif;

            if (!empty($this->wfsservice_id)):
                if ($this->wfsservicetype_id == 1):
                    $query = $db->getQuery(true)
                            ->select('p.*')
                            ->from('#__sdi_physicalservice p')
                            ->where('p.id = ' . $this->wfsservice_id);
                else :
                    $query = $db->getQuery(true)
                            ->select('p.*')
                            ->from('#__sdi_virtualservice p')
                            ->where('p.id = ' . $this->wfsservice_id);
                endif;

                $db->setQuery($query);
                $wfsservice = $db->loadObject();
                $this->wfsurl = $wfsservice->resourceurl;
            endif;
        } catch (JDatabaseException $e) {
            
        }
    }

    public function setAllowedBuffer($extractions) {
        $this->allowedbuffer = 1;
        
        if(empty($extractions)) return true;
        
        $incondition = '';        
        foreach ($extractions as $extraction):
            if(strlen($incondition) != 0) $incondition .= ',';
            $incondition .= $extraction->id;            
        endforeach;
        
        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
                ->select('*')
                ->from('#__sdi_diffusion_perimeter dp')
                ->where('dp.perimeter_id = ' . $this->id)
                ->where('dp.diffusion_id IN ( '. $incondition .')')
        ;

        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        foreach($items as $item):
            if($item->buffer == 0):
                $this->allowedbuffer = 0;
                return $this->allowedbuffer;
            endif;
        endforeach;
        
        return $this->allowedbuffer;
    }

}

?>
