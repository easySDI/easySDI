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

class order extends sdiTable
{	
	var $type_id=null;
	var $status_id=null;
	var $user_id=null;
	var $thirdparty_id=null;
	var $buffer=null;
	var $surface = null;
	var $remark=null;
	var $response=null;
	var $responsesent=null;
	var $sent=null;	

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_order', 'id', $db ) ;    		
	}
	
	function updateStatus ($status_id)
	{
		$this->status_id = $status_id;
		return $this->store();
	}

}

class orderProductListByOrder extends sdiTable
{	
	var $order_id=null;
	var $product_id=null;
	var $status_id=null;
	var $filename=null;
	var $remark=null;
	var $price=null;

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_order_product', 'order_id', $db ) ;    		
	}
	
	function delete ()
	{
		$this->_db->setQuery( "DELETE FROM #__sdi_order_property  
								WHERE orderproduct_id 
								 = $this->id");
		$this->_db->query();
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return parent::delete();
	}

}

class orderProduct extends sdiTable
{	
	var $order_id=null;
	var $product_id=null;
	var $status_id=null;
	var $filename=null;
	var $remark=null;
	var $price=null;

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_order_product', 'id', $db ) ;    		
	}
}

class orderProductPerimeterByOrder extends sdiTable
{	
	var $order_id=null;
	var $perimeter_id=null;
	var $value = null;
	var $text=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_order_perimeter', 'order_id', $db ) ;    		
	}

}

class orderPerimeter extends sdiTable
{	
	var $order_id=null;
	var $perimeter_id=null;
	var $value = null;
	var $text=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_order_perimeter', 'id', $db ) ;    		
	}

}

class orderProductPropertiesByOrderList extends sdiTable
{	
	var $orderproduct_id=null;
	var $property_id = null;
	var $propertyvalue_id=null;
	var $propertyvalue=null;

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_order_property', 'orderproduct_id', $db ) ;    		
	}

}

class orderProductProperty extends sdiTable
{	
	var $orderproduct_id=null;
	var $property_id = null;
	var $propertyvalue_id=null;
	var $propertyvalue=null;

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_order_property', 'id', $db ) ;    		
	}

}
?>