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
		$search = $mainframe->getUserStateFromRequest( "searchProduct{$option}", 'searchProduct', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );

		$product = new product( $db );
		$total = $product->getObjectCount();
		$pageNav = new JPagination($total,$limitstart,$limit);

		$query = "SELECT p.*, t.label as treatment, m.guid as metadata_guid, v.title as version_title, o.name as object_name
					FROM $product->_tbl p 
					INNER JOIN #__sdi_list_treatmenttype t ON  p.treatmenttype_id=t.id 
					INNER JOIN #__sdi_objectversion v ON v.id=p.objectversion_id
					INNER JOIN #__sdi_object o ON o.id = v.object_id
					INNER JOIN #__sdi_metadata m ON v.metadata_id = m.id
					 ";
		
		$where="";
		if ($search)
		{
			$where = ' where LOWER(p.id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(p.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(p.description) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(o.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(v.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		$query .= $where;
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		if ($filter_order <> "id" and $filter_order <> "name" and $filter_order <> "description" and $filter_order <> "treatment"   and $filter_order <> "object_name" and $filter_order <> "version_title" and $filter_order <> "updated" )
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		if($filter_order == "object_name")
		{
			$orderby 	= ' order by o.name '. $filter_order_Dir;
		}
		else if($filter_order == "version_title")
		{
			$orderby 	= ' order by v.title '. $filter_order_Dir;
		}
		else if($filter_order == "treatment")
		{
			$orderby 	= ' order by t.label '. $filter_order_Dir;
		}
		else
		{
			$orderby 	= ' order by p.'. $filter_order .' '. $filter_order_Dir;
		}
		
		$query = $query.$orderby;
		$db->setQuery( $query ,$limitstart,$limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			exit();
		}

		HTML_product::listProduct( $rows,$filter_order_Dir, $filter_order, $search,$pageNav,$option);
	}

	function editProduct( $id, $option ) {
		global  $mainframe;
		$user = JFactory::getUser();
		$database =& JFactory::getDBO();
		if($id == '0') $id = JRequest::getVar('id', 0 );
		
		$product = new product( $database );
		$product->load( $id );
				
		$product->tryCheckOut($option,'listProduct');
		
		$version_id = JRequest::getVar('objectversion_id', 0 );
		if($version_id == 0)
		{
			$version_id = $product->objectversion_id;
		}
		$version = new objectversion($database);
		$version->load($version_id);
		
		$supplier = new account($database);
		$object = new object ($database);
		$object_id = JRequest::getVar('object_id', 0 );
		if($object_id==0)
		{
			$object_id=$version->object_id;
		}
		else
		{
			if($object_id <> $version->object_id)
			{
				$version_id=0;
				$version->load($version_id);
			}
		}
		if($object_id<>0)
		{
			
			$object->load($object_id);
			$supplier->load($object->account_id);
		}
		
		$objecttype_id = JRequest::getVar('objecttype_id', 0 );
		if($objecttype_id==0)
		{
			if($object_id<>0)
			{
				$objecttype_id=$object->objecttype_id;
			}
		}
		else
		{
			if($objecttype_id <>$object->objecttype_id)
			{
				$object_id=0;
				$object->load($object_id);
				$supplier->load(0);
				$version_id=0;
				$version->load($version_id);
			}
		}
				
		$catalogUrlBase = config_easysdi::getValue("catalog_url");

		//List of objecttype
		$objecttype_list = array();
		$objecttype_list[] = JHTML::_('select.option','0', JText::_("SHOP_OBJECT_LIST") );
		$database->setQuery("SELECT id AS value, name AS text 
							   FROM #__sdi_objecttype
							   WHERE predefined = 0 
							   ORDER BY name");
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		$objecttype_list = array_merge( $objecttype_list, $database->loadObjectList() );
		
		//List of object
		$object_list = array();
		$object_list[] = JHTML::_('select.option','0', JText::_("SHOP_OBJECT_LIST") );
		if($objecttype_id <>0)
			{
			$database->setQuery("SELECT id AS value, name AS text 
								   FROM #__sdi_object
								   WHERE published = 1 
								   AND objecttype_id = $objecttype_id
								   ORDER BY name");
			if ($database->getErrorNum()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$object_list = array_merge( $object_list, $database->loadObjectList() );
		}
		
		//List of version
		$version_list = array();
		$version_list[] = JHTML::_('select.option','0', JText::_("SHOP_VERSION_LIST") );
		if($object_id<>0)
		{
			$database->setQuery("SELECT id AS value, title AS text 
							   FROM #__sdi_objectversion
							   WHERE object_id = $object_id 
							   ORDER BY title");
			if ($database->getErrorNum()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$version_list = array_merge( $version_list, $database->loadObjectList() );
		}
		
		//List of partners with diffusion right
		$diffusion_list = array();
		$diffusion_list[] = JHTML::_('select.option','0', JText::_("SHOP_ACCOUNT_LIST") );
		if($object_id<>0)
		{
			$database->setQuery("SELECT  a.id AS value, b.name AS text 
								   FROM #__sdi_account a, #__users b 
								   WHERE a.user_id = b.id 
								   AND  ( a.root_id = $supplier->id OR a.id = $supplier->id )
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
		$basemap = new basemap( $database );
		$baseMap_list = array_merge( $baseMap_list,$basemap->getObjectListAsArray());
	
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
		
		//Product accessibility
		$accessibility_list = array();
		$accessibility_list [] = JHTML::_('select.option','0', JText::_("SHOP_PRODUCT_ACCESSIBILITY") );
		$database->setQuery( "SELECT id AS value,  label AS text FROM #__sdi_list_accessibility " );
		$accessibility_list = array_merge($accessibility_list,$database->loadObjectList()) ;
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		helper_easysdi::alter_array_value_with_JTEXT_($accessibility_list);

		//List of perimeters
		$perimeter_list = array();
		$perimeter = new perimeter( $database );
		$perimeter_list = $perimeter->getObjectListAsArray();
				
		$selected_perimeter = array();
		$query = "SELECT perimeter_id AS value FROM #__sdi_product_perimeter WHERE product_id=".$product->id;				
		$database->setQuery( $query );
		$selected_perimeter = $database->loadObjectList();
		if ($database->getErrorNum()) {						
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
		}
		
		//Select all available EasySDI Account
		$rowsAccount = array();
		$rowsAccount[] = JHTML::_('select.option','0', JText::_("SHOP_LIST_ACCOUNT_SELECT" ));
		$rowsAccount = array_merge($rowsAccount,account::getEasySDIAccountsList());
		
		$language =& JFactory::getLanguage();
		//Get  profiles
		$database->setQuery( "SELECT ap.code as value, t.label as text FROM #__sdi_language l, #__sdi_list_codelang cl, #__sdi_accountprofile ap LEFT OUTER JOIN #__sdi_translation t ON ap.guid=t.element_guid WHERE t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$language->_lang."'" );
		$rowsProfile = $database->loadObjectList();
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");		
		}
		
		
		//Get users
		$database->setQuery( "SELECT #__sdi_account.id as value, #__users.name as text 
								FROM #__users 
								INNER JOIN #__sdi_account 
								ON  #__users.id = #__sdi_account.user_id ORDER BY text
								
								" );
		$rowsUser = $database->loadObjectList();
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");		
		}
			
		$database->setQuery( "SELECT #__sdi_account.id as value 
								FROM #__sdi_product_account 
								INNER JOIN #__sdi_product 
								ON  #__sdi_product.id = #__sdi_product_account.product_id
								INNER JOIN #__sdi_account 
								ON  #__sdi_account.id = #__sdi_product_account.account_id
								WHERE #__sdi_product.id = ".$id." AND #__sdi_product_account.code='preview'" );
		$userPreviewSelected = $database->loadObjectList();
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");		
		}
		
		$database->setQuery( "SELECT #__sdi_account.id as value 
								FROM #__sdi_product_account 
								INNER JOIN #__sdi_product 
								ON  #__sdi_product.id = #__sdi_product_account.product_id
								INNER JOIN #__sdi_account 
								ON  #__sdi_account.id = #__sdi_product_account.account_id
								WHERE #__sdi_product.id = ".$id." AND #__sdi_product_account.code='download'" );
		$userDownloadSelected = $database->loadObjectList();
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");		
		}
		
		
		if (strlen($catalogUrlBase )==0)
		{
			$mainframe->enqueueMessage("NO VALID CATALOG URL IS DEFINED","ERROR");
		}
		else
		{
			HTML_product::editProduct( $product,$version,$object_id,$objecttype_id,$supplier,$objecttype_list, $object_list,$version_list,$diffusion_list,$baseMap_list,$treatmentType_list,$visibility_list,$accessibility_list,$perimeter_list,$selected_perimeter,$catalogUrlBase,$rowsAccount,$rowsUser,$userPreviewSelected,$userDownloadSelected,$id, $option );
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
		if($product->viewbasemap_id == '0')
		{
			$product->viewbasemap_id = null;
		}
		if($product->treatmenttype_id == null ||$product->treatmenttype_id == '')
		{
			$product->treatmenttype_id = sdilist::getIdByCode('#__sdi_list_treatmenttype','AUTO' );
		}
		if($product->viewaccessibility_id == '0')
		{
			$product->viewaccessibility_id = null;
		}
		if($product->loadaccessibility_id == '0')
		{
			$product->loadaccessibility_id = null;
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
			if($properties_id == -1)continue;
			$product_property = new product_property($database);
			$product_property->product_id=$product->id;
			$product_property->propertyvalue_id=$properties_id;
			if(!$product_property->store()){
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
				exit();
			}
		}
		
		$product_account = new product_account($database);
		if(!$product_account->delete($product->id)){
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}

		foreach( $_POST['userPreviewList'] as $accountpreview_id )
		{
			$product_account = new product_account($database);
			$product_account->product_id=$product->id;
			$product_account->account_id=$accountpreview_id;
			$product_account->code='preview';
			if(!$product_account->store()){
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
				exit();
			}
		}
		foreach( $_POST['userDownloadList'] as $accountdownload_id )
		{
			$product_account = new product_account($database);
			$product_account->product_id=$product->id;
			$product_account->account_id=$accountdownload_id;
			$product_account->code='download';
			if(!$product_account->store()){
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
				exit();
			}
		}
		
		$product->checkin();
		
		if ($returnList == true) {
			$mainframe->redirect("index.php?option=$option&task=listProduct");
		}

	}

	function deleteProduct($cid ,$option){
		global $mainframe;
		$database =& JFactory::getDBO();
		$limitstart = JRequest::getVar("limitstart");
		$limit = JRequest::getVar("limit");
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("SHOP_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listProduct&limitstart=$limitstart&limit=$limit" );
			exit;
		}
		
		foreach( $cid as $id )
		{
			$product = new product( $database );
			$product->load( $id );
			
			$query = "SELECT o.name as name FROM #__sdi_order_product p INNER JOIN #__sdi_order o ON o.id = p.order_id  WHERE p.product_id=$id ";
			$database->setQuery($query);
			$results = $database->loadObjectList();
			if(count($results)>0)
			{
				$mainframe->enqueueMessage(JText::sprintf("SHOP_PRODUCT_DELETE_ERROR",$product->name),"INFO");
				foreach($results as $result)
				{
					$mainframe->enqueueMessage(" - ".$result->name,"INFO");
				}
				$mainframe->redirect("index.php?option=$option&task=listProduct&limitstart=$limitstart&limit=$limit" );
			}
			
			if (!$product->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct&limitstart=$limitstart&limit=$limit" );
			}			
		}
		
		
		$mainframe->redirect("index.php?option=$option&task=listProduct&limitstart=$limitstart&limit=$limit" );
	}

	function downloadProduct(){

		$database =& JFactory::getDBO();
		$user = JFactory::getUser();
		$product_id = JRequest::getVar('product_id');
		echo $product_id;
		$query = "SELECT data,filename FROM #__sdi_product_file where product_id = $product_id ";
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
	
	function cancelProduct($option)
	{
		global $mainframe;
		$database = & JFactory::getDBO();
		$product = new product( $database );
		$product->bind(JRequest::get('post'));
		$product->checkin();

		$mainframe->redirect("index.php?option=$option&task=listProduct" );
	}
}

?>