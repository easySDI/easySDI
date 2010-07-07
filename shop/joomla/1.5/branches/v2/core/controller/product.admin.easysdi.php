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

class ADMIN_product {

	function publish($cid,$published){
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		$product = new product( $db );
		$product->load( $cid[0] );
		if ($published){
			if (!$product->publish())$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
		}else{
			if(!$product->unpublish())$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
		}

	}

	function listProduct($option) {
		global  $mainframe;
		$db =& JFactory::getDBO();

		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',1);
		$search = $mainframe->getUserStateFromRequest( "searchProduct{$option}", 'searchProduct', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );

		$product = new product( $db );
		$total = $product->getObjectCount();
		$pageNav = new JPagination($total,$limitstart,$limit);


		// Recherche des enregistrements selon les limites
		$query = "SELECT p.*, t.label as treatment, a.name AS text
					FROM $product->_tbl p 
					INNER JOIN #__sdi_list_treatmenttype t ON  p.treatmenttype_id=t.id 
					INNER JOIN (SELECT c.id , u.name FROM #__sdi_account c, #__users  u WHERE c.user_id = u.id ) a ON p.manager_id = a.id ";
		if ($search !=null && strlen($search)>0 ) {$query = $query ." WHERE name like '%$search%' ";}

		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		if ($filter_order <> "id" and $filter_order <> "name" and $filter_order <> "description" and $filter_order <> "treatment" and $filter_order <> "created" and $filter_order <> "ordering")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		$query = $query.$orderby;
		
		if ($use_pagination) {
			$db->setQuery( $query ,$limitstart,$limit);
		}
		else {
			$db->setQuery( $query );
		}
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			exit();
		}

		HTML_product::listProduct($use_pagination, $rows, $search,$pageNav,$option);
	}

	function editProduct( $id, $option ) {
		global  $mainframe;
		$user = JFactory::getUser();
		$database =& JFactory::getDBO();
		if($id == '0') $id = JRequest::getVar('id', 0 );
		
		$product = new product( $database );
		$product->load( $id );
				
		$version_id = JRequest::getVar('objectversion_id', 0 );
		if($version_id == 0)
		{
			$version_id = $product->objectversion_id;
		}
		$version = new objectversion($database);
		$version->load($version_id);
		
		$supplier = new account($database);
		$object_id = JRequest::getVar('object_id', 0 );
		if($object_id==0)
		{
			$object_id=$version->object_id;
		}
		if($object_id<>0)
		{
			$object = new object ($database);
			$object->load($object_id);
			$supplier->load($object->account_id);
		}
				
		$catalogUrlBase = config_easysdi::getValue("catalog_url");

		//List of object
		$object_list = array();
		$object_list[] = JHTML::_('select.option','0', JText::_("SHOP_OBJECT_LIST") );
		$database->setQuery("SELECT id AS value, name AS text 
							   FROM #__sdi_object
							   WHERE published = 1 
							   ORDER BY name");
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		$object_list = array_merge( $object_list, $database->loadObjectList() );
		
		//List of version
		$version_list = array();
		$version_list[] = JHTML::_('select.option','0', JText::_("SHOP_VERSION_LIST") );
		if($object_id<>0)
		{
			$database->setQuery("SELECT id AS value, name AS text 
							   FROM #__sdi_object_version
							   WHERE orderable = 1 
							   AND object_id = $object_id 
							   ORDER BY name");
			if ($database->getErrorNum()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$version_list = array_merge( $version_list, $database->loadObjectList() );
		}
		
		//List of partners with ADMIN right
		$manager_list = array();
		$manager_list[] = JHTML::_('select.option','0', JText::_("SHOP_ACCOUNT_LIST") );
		if($object_id<>0)
		{
			$database->setQuery("SELECT a.id AS value, b.name AS text 
								   FROM #__sdi_account a, #__users b  
								   WHERE a.user_id = b.id 
								   AND  (a.root_id = $supplier->root_id OR a.root_id = $supplier->id OR a.id = $supplier->id )
								   AND a.id IN (SELECT account_id FROM #__sdi_actor WHERE role_id = (SELECT id FROM #__sdi_list_role WHERE code ='PRODUCT'))
								   ORDER BY b.name");
			if ($database->getErrorNum()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$manager_list = array_merge( $manager_list, $database->loadObjectList() );
		}

		//List of partners with diffusion right
		$diffusion_list = array();
		$diffusion_list[] = JHTML::_('select.option','0', JText::_("SHOP_ACCOUNT_LIST") );
		if($object_id<>0)
		{
			$database->setQuery("SELECT a.id AS value, b.name AS text 
								   FROM #__sdi_account a, #__users b 
								   WHERE a.user_id = b.id 
								   AND  (a.root_id = $supplier->root_id OR a.root_id = $supplier->id OR a.id = $supplier->id )
								   AND a.id IN (SELECT account_id FROM #__sdi_actor WHERE role_id = (SELECT id FROM #__sdi_list_role WHERE code ='DIFFUSION'))
								   ORDER BY b.name");	
			if ($database->getErrorNum()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}	
			$diffusion_list = array_merge($diffusion_list, $database->loadObjectList());
		}
		
		//List of available BaseMap
		$baseMap_list = array();		
		$baseMap_list [] = JHTML::_('select.option','0', JText::_("SHOP_BASEMAP_LIST") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__sdi_basemap " );
		$baseMap_list = array_merge($baseMap_list, $database->loadObjectList()) ;
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
	
		//Product treatment
		$treatmentType_list = array();		
		$database->setQuery( "SELECT id AS value,  label AS text FROM #__sdi_list_treatmenttype " );
		$treatmentType_list = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		helper_easysdi::alter_array_value_with_JTEXT_($treatmentType_list);
		
		//Product visibility
		$visibility_list = array();		
		$database->setQuery( "SELECT id AS value,  label AS text FROM #__sdi_list_visibility " );
		$visibility_list = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		helper_easysdi::alter_array_value_with_JTEXT_($visibility_list);

		//List of perimeters
		$perimeter_list = array();
		$query = "SELECT id AS value, name AS text FROM #__sdi_perimeter ";
		$database->setQuery( $query );
		$perimeter_list = $database->loadObjectList() ;
		if ($database->getErrorNum()) {						
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
		}		
				
		$selected_perimeter = array();
		$query = "SELECT perimeter_id AS value FROM #__sdi_product_perimeter WHERE product_id=".$product->id;				
		$database->setQuery( $query );
		$selected_perimeter = $database->loadObjectList();
		if ($database->getErrorNum()) {						
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
		}
		
		//Select all available easysdi Account
		$rowsAccount = array();
		$rowsAccount[] = JHTML::_('select.option','0', JText::_("SHOP_LIST_ACCOUNT_SELECT" ));
		$rowsAccount = array_merge($rowsAccount,account::getEasySDIAccountsList());
			
		if (strlen($catalogUrlBase )==0)
		{
			$mainframe->enqueueMessage("NO VALID CATALOG URL IS DEFINED","ERROR");
		}
		else
		{
			HTML_product::editProduct( $product,$version,$object_id,$supplier, $object_list,$version_list,$manager_list,$diffusion_list,$baseMap_list,$treatmentType_list,$visibility_list,$perimeter_list,$selected_perimeter,$catalogUrlBase,$rowsAccount,$id, $option );
		}
	}
	
	function saveProduct($returnList ,$option){
		global  $mainframe;
		$database=& JFactory::getDBO();
		$option =  JRequest::getVar("option");
		$product =&	 new product($database);

		if (!$product->bind( $_POST )) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}
		
		$service_type = JRequest::getVar('service_type');
		if($service_type == "via_proxy")
		{
			$product->viewuser = "";
			$product->viewpassword = "";
		}
		else
		{
			$product->viewaccount_id="";
		}
		if($product->viewbasemap_id == 0)
		{
			$product->viewbasemap_id = NULL;
		}
		
		if (!$product->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}

		$product_perimeter = new product_perimeter($database);
		if(!$product_perimeter->delete($product->id)){
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}

		foreach( $_POST['perimeter_id'] as $perimeter_id )
		{
			$product_perimeter = new product_perimeter($database);
			$product_perimeter->product_id=$product->id;
			$product_perimeter->perimeter_id=$perimeter_id;
			if(!$product_perimeter->store()){
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
				exit();
			}
		}

		foreach( $_POST['buffer'] as $bufferPerimeterId )
		{
			$product_perimeter = new product_perimeter($database);
			if(!$product_perimeter->loadById($product->id,$bufferPerimeterId)){
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
				exit();
			}
			$product_perimeter->buffer = 1;
			if(!$product_perimeter->store()){
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
				exit();
			}
		}

		$product_property = new product_property($database);
		if(!$product_property->delete($product->id)){
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}

		foreach( $_POST['property_id'] as $properties_id )
		{
			$product_property = new product_property($database);
			$product_property->product_id=$product->id;
			$product_property->propertyvalue_id=$properties_id;
			if(!$product_property->store()){
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
				exit();
			}
		}
		
		if ($returnList == true) {
			$mainframe->redirect("index.php?option=$option&task=listProduct");
		}

	}

	function deleteProduct($cid ,$option){
		global $mainframe;
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("SHOP_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit;
		}
		
		foreach( $cid as $id )
		{
			$product = new product( $database );
			$product->load( $id );
			//if(!$product->deleteProduct())$mainframe->enqueueMessage("ERROR SUPPRESS PRODUCT","ERROR");
			if (!$product->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
			}			
		}
		
		$limitstart = JRequest::getVar("limitstart");
		$limit = JRequest::getVar("limit");
		$mainframe->redirect("index.php?option=$option&task=listProduct&limitstart=$limitstart&limit=$limit" );
	}

	function downloadProduct(){

		$database =& JFactory::getDBO();
		$user = JFactory::getUser();
		$product_id = JRequest::getVar('product_id');
		
		$query = "SELECT data,filename FROM #__sdi_productfile where product_id = $product_id ";
		$database->setQuery($query);
		$row = $database->loadObject();

		error_reporting(0);

		ini_set('zlib.output_compression', 0);
		header('Pragma: public');
		header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
		header('Content-Transfer-Encoding: none');
		header("Content-Length: ".strlen($row->data));
		header('Content-Type: application/octetstream; name="'.$row->filename.'"');
		header('Content-Disposition: attachement; filename="'.$row->filename.'"');

		echo $row->data;
		die();
	}
	
}

?>