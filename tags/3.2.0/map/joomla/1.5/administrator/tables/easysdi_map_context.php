<?php

/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community * For more information : www.easysdi.org
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

// No direct access
defined('_JEXEC') or die('Restricted access');
 
/**
 * Map context Table class
 */
class TableEasysdi_map_context extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    var $id = null;
     
    /**
     * User id
     *
     * @var int
     */
    var $user_id = null;
    
    /**
     * Plaintext Map Context - text version of WMC XML
     *
     * @var string
     */
    var $WMC_text = null;
 
    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function TableEasysdi_map_context( &$db ) {
        parent::__construct('#__easysdi_map_context', 'id', $db);
    }
}
?>