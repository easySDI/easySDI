<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

abstract class Easysdi_catalogHelper {

    
    /**
     * Get the preview version of a metadata
     * 
     * @param int $metadata_id
     * @return stdClass
     */
    public static function getPreviewMetadata(Easysdi_catalogTablemetadata $metadata) {
        $db = JFactory::getDbo();
        
        // resource
        $query = $db->getQuery(true);
        $query->select('r.id');
        $query->from('#__sdi_resource r');
        $query->innerJoin('#__sdi_version v ON v.resource_id = r.id');
        $query->innerJoin('#__sdi_metadata m ON m.version_id = v.id');
        $query->where('m.id = '.(int)$metadata->id);
        
        $db->setQuery($query);
        $resource = $db->loadObject();
        
        // preview metadata
        $query = $db->getQuery(true);
        $query->select('m.*');
        $query->from('#__sdi_metadata m');
        $query->innerJoin('#__sdi_version v ON v.id = m.version_id');
        $query->where('v.resource_id = ' . (int) $resource->id);
        $query->where('m.created < ' .  $query->quote($metadata->created));
        $query->order('m.created DESC');

        $db->setQuery($query, 0, 1);
        $preview = $db->loadObject();

        return $preview;
    }
    
    /**
     * Get the last version of a metatdata
     */
    public static function getLastVersion($metadata_guid){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('v.resource_id as id');
        $query->from('#__sdi_metadata m');
        $query->innerJoin('#__sdi_version v ON v.id = m.version_id');
        $query->where('m.guid = '.$query->quote($metadata_guid));
        
        $db->setQuery($query);
        $resource = $db->loadObject();
        
        $query = $db->getQuery(true);
        
        $query->select('m.guid');
        $query->from('#__sdi_metadata m');
        $query->innerJoin('#__sdi_version v ON v.id = m.version_id');
        $query->where('v.resource_id = '.(int)$resource->id);
        $query->where('m.endpublished = '.$query->quote('0000-00-00 00:00:00'));
        
        $db->setQuery($query);
        $lastMetadata = $db->loadObject();
        
        return $lastMetadata->guid;
    }

}
