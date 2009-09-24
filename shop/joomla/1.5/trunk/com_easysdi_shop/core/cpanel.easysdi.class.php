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

class order extends JTable
{	
	var $order_id=null;	
	var $remark=null;
	var $provider_id=null;
	var $name=null;
	var $type=null;
	var $status=null;
	var $order_update=null;
	var $third_party=null;
	var $archived=null;
	var $response_date=null;
	var $response_send=null;
	var $user_id=null;
	var $buffer=null;
	var $order_date=null;

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_order', 'order_id', $db ) ;    		
	}

}

class orderProductListByOrder extends JTable
{	
	var $id=null;	
	var $product_id=null;
	var $order_id=null;
	var $status=null;
	var $data=null;
	var $filename= null;
	var $remark=null;
	var $price=null;

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_order_product_list', 'order_id', $db ) ;    		
	}

}


class orderProductPerimeterByOrder extends JTable
{	
	var $id=null;	
	var $perimeter_id=null;
	var $order_id=null;
	var $value = null;
	var $text=null;
	

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_order_product_perimeters', 'order_id', $db ) ;    		
	}

}

class orderProductPropertiesByOrderList extends JTable
{	
	var $id=null;	
	var $order_product_list_id=null;
	var $property_id = null;
	var $property_value=null;
	var $code=null;

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_order_product_properties', 'order_product_list_id', $db ) ;    		
	}

}
?>