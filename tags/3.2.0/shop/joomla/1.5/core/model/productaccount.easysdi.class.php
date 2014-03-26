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
defined('_JEXEC') or die('Restricted access');

class product_account extends sdiTable
{
	var $product_id=null;
	var $account_id=null;
		
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_product_account', 'id', $db ) ;    		
	}
	
	function loadById($product_id, $account_id)
	{
		$query = 'SELECT *'
		. ' FROM '.$this->_tbl
		. ' WHERE product_id = '.$product_id.' AND account_id = '.$account_id;
		$this->_db->setQuery( $query );

		if ($result = $this->_db->loadAssoc( )) {
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg() );
			return false;
		}
	}
	
	function delete ($product_id)
	{
		$this->_db->setQuery( "DELETE FROM  $this->_tbl WHERE PRODUCT_ID = ".$product_id );
		if (!$this->_db->query()) {
			return false;
		}
		return true;
	}
		
}

?>