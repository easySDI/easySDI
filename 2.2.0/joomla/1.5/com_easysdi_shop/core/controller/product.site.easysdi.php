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

class SITE_product {

	function saveProduct($returnList, $option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		//Check user's rights
		$user = JFactory::getUser();
		if(!userManager::isUserAllowed($user,"PRODUCT")){
			return;
		}
		
		$account = new accountByUserId( $database );
		$account->load( $user->id );
		
		$product =& new product($database);
		$rowProductOld =& new product($database);
		$sendMail = false;
	
		
		$id = JRequest::getVar("id",0);
		if($id >0){
			$rowProductOld->load($id);
			if ($rowProductOld->published == 0 && JRequest::getVar("published",0) ==1){
				$sendMail = true;
			}
		}

		if (!$product->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listProduct'), false ));
			exit();
		}
		
		if(isset($_FILES['productfile']) && !empty($_FILES['productfile']['name'])) {
			if ($_FILES['productfile']['error']) {    
	          switch ($_FILES['productfile']['error']){    
                   case 1: // UPLOAD_ERR_INI_SIZE    
                   	$mainframe->enqueueMessage(JText::_("SHOP_PRODUCT_MAX_FILE_SIZE_UPLOAD_ERR_INI_SIZE"),"ERROR");
					$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=editProduct&id=$product->id" ),false));
					return;
                       
                   case 2: // UPLOAD_ERR_FORM_SIZE    
                  	//$mainframe->enqueueMessage(printf(JText::_("SHOP_PRODUCT_MAX_FILE_SIZE_UPLOAD_ERR_FORM_SIZE"),JRequest::getVar("MAX_FILE_SIZE")),"ERROR");
                  	$mainframe->enqueueMessage(JText::_("SHOP_PRODUCT_MAX_FILE_SIZE_UPLOAD_ERR_FORM_SIZE"),"ERROR");
					$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=editProduct&id=$product->id" ),false));
					return;
                   
                   case 3: // UPLOAD_ERR_PARTIAL    
                   	$mainframe->enqueueMessage(JText::_("SHOP_PRODUCT_MAX_FILE_SIZE_UPLOAD_ERR_PARTIAL"),"ERROR");
					$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=editProduct&id=$product->id" ), false));
					return;
                   
                   case 4: // UPLOAD_ERR_NO_FILE 
                   	$mainframe->enqueueMessage(JText::_("SHOP_PRODUCT_MAX_FILE_SIZE_UPLOAD_ERR_NO_FILE"),"ERROR");
					$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=editProduct&id=$product->id" ),false));
					return; 
                       
	          }    
			}    
			
			if(filesize($_FILES['productfile']['tmp_name']) > JRequest::getVar("MAX_FILE_SIZE")){
				//$mainframe->enqueueMessage(printf(JText::_("SHOP_PRODUCT_MAX_FILE_SIZE_ERROR"),JRequest::getVar("MAX_FILE_SIZE")),"ERROR");
				$mainframe->enqueueMessage(JText::_("SHOP_PRODUCT_MAX_FILE_SIZE_ERROR"),"ERROR");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=editProduct&id=$product->id" ),false));
				return;
			}
		}
		
		
		$service_type = JRequest::getVar('service_type');
		if($service_type == "via_proxy"){
			$product->viewuser = "";
			$product->viewpassword = "";
		}else{
			$product->viewaccount_id="";
		}
		if($product->viewbasemap_id == '0')
			$product->viewbasemap_id = null;
		if($product->treatmenttype_id == null ||$product->treatmenttype_id == '')
			$product->treatmenttype_id = sdilist::getIdByCode('#__sdi_list_treatmenttype','AUTO' );
		if($product->viewaccessibility_id == '0')
			$product->viewaccessibility_id = null;
		if($product->loadaccessibility_id == '0')
			$product->loadaccessibility_id = null;
		if($product->pathfile == '')
			$product->pathfile = null;
		
		if (!$product->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listProduct'), false ));
			exit();
		}
		
		$product_perimeter = new product_perimeter($database);
		if(!$product_perimeter->delete($product->id)){
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listProduct'), false ));
			exit();
		}

		foreach( $_POST['perimeter_id'] as $perimeter_id )
		{
			$product_perimeter = new product_perimeter($database);
			$product_perimeter->product_id=$product->id;
			$product_perimeter->perimeter_id=$perimeter_id;
			if(!$product_perimeter->store()){
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listProduct'), false ));
				exit();
			}
		}

		foreach( $_POST['buffer'] as $bufferPerimeterId )
		{
			$product_perimeter = new product_perimeter($database);
			if(!$product_perimeter->loadById($product->id,$bufferPerimeterId)){
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listProduct'), false ));
				exit();
			}
			$product_perimeter->buffer = 1;
			if(!$product_perimeter->store()){
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listProduct'), false ));
				exit();
			}
		}
		
		$product_property = new product_property($database);
		if(!$product_property->delete($product->id)){
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listProduct'), false ));
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
				$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listProduct'), false ));
				exit();
			}
		}
		
		$product_account = new product_account($database);
		if(!$product_account->delete($product->id)){
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listProduct'), false ));
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
				$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listProduct'), false ));
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
				$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listProduct'), false ));
				exit();
			}
		}
		
		if ($sendMail){
			$query = "SELECT count(*) FROM #__users,#__sdi_account 
						WHERE #__users.id=#__sdi_account.user_id 
						AND (#__users.usertype='Administrator' OR #__users.usertype='Super Administrator') 
						AND #__sdi_account.notify_distribution=1";
			$database->setQuery( $query );
			$total = $database->loadResult();
			if($total >0){
			$query = "SELECT * FROM #__users,#__sdi_account 
							WHERE #__users.id=#__sdi_account.user_id 
							AND (#__users.usertype='Administrator' OR #__users.usertype='Super Administrator') 
							AND #__sdi_account.notify_distribution=1";
			$database->setQuery( $query );

			$rows = $database->loadObjectList();
			$mailer =& JFactory::getMailer();
			$user = JFactory::getUser();
			SITE_product::sendMail($rows,JText::_("SHOP_NEW_DISTRIBUTION_MAIL_SUBJECT"),JText::sprintf("SHOP_NEW_DISTIBUTION_MAIL_BODY",$rowProduct->data_title,$user->username));																
			}
		}	
			
		$product->checkin();
		
		if ($returnList == true) {
			if ($product->id == 0)$mainframe->enqueueMessage(JText::_("SHOP_MESSAGE_PRODUCT_CREATION_SUCCESS"),"INFO");
			$limitstart = JRequest::getVar("limitstart");
			$limit = JRequest::getVar("limit");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listProduct&limitstart='.$limitstart.'&limit='.$limit), false ));
		}	
	}
	
	function editProduct( $option,$isNew = false) {
		global  $mainframe;
		$database =& JFactory::getDBO();
		$user = JFactory::getUser();
		$account = new accountByUserId($database);
		$account->load($user->id);		
		if(!$account->root_id)$account->root_id ='0';
		
		//Check user's rights
		if(!userManager::isUserAllowed($user,"PRODUCT")){
			return;
		}
		
		//Check task
		if (!$isNew){
			$id = JRequest::getVar('id');
		}
		else {
			$id=0;
		}
		
		//Product
		$product = new product( $database );
		$product->load( $id );
		
		$product->tryCheckOut($option,'listProduct');
		
		//Version
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
		$database->setQuery("SELECT id AS value, name AS text 
							   FROM #__sdi_object
							   WHERE published = 1 
							    AND objecttype_id = $objecttype_id
							   AND id IN (SELECT object_id FROM #__sdi_manager_object WHERE account_id = $account->id)
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
			$database->setQuery("SELECT id AS value, title AS text 
							   FROM #__sdi_objectversion
							   WHERE object_id = $object_id 
							   ORDER BY title");
			if ($database->getErrorNum()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$version_list = array_merge( $version_list, $database->loadObjectList() );
		}
		
		if($object_id<>0)
		{
			//Build queries
			$select_query = "SELECT a.id AS value, b.name AS text 
										 FROM #__sdi_account a, #__users b 
										 WHERE ( a.root_id = $supplier->id OR a.id = $supplier->id )  
										 AND a.user_id = b.id 
										 AND a.id IN  (SELECT account_id FROM #__sdi_actor WHERE role_id = (SELECT id FROM #__sdi_list_role WHERE code =";
			$order_query = ")) ORDER BY b.name";
			
			//List of partner with the same root as current logged user
			$accounts = array();
			$accounts[] = JHTML::_('select.option','0', JText::_("SHOP_ACCOUNT_LIST") );
			$query = $select_query."'PRODUCT' ".$order_query;
			$database->setQuery($query);
			$accounts = array_merge( $accounts, $database->loadObjectList() );
			if ($database->getErrorNum()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			
			//List of partner with DIFFUSION right
			$diffusion_list = array();
			$diffusion_list[] = JHTML::_('select.option','0', JText::_("SHOP_ACCOUNT_LIST") );
			$query = $select_query."'DIFFUSION' ".$order_query;
			$database->setQuery($query);
			$diffusion_list = array_merge($diffusion_list, $database->loadObjectList());
			if ($database->getErrorNum()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
		}
		
		//List of basemap
		$baseMap_list = array();		
		$baseMap_list [] = JHTML::_('select.option','0', JText::_("SHOP_BASEMAP_LIST") );
		$basemap = new basemap( $database );
		$baseMap_list = array_merge( $baseMap_list,$basemap->getObjectListAsArray());
			
		//Product treatment
		$treatmentType_list = array();		
		$database->setQuery( "SELECT id AS value, label AS text FROM #__sdi_list_treatmenttype " );
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
		
		//List of available perimeters
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
		
		//Select all available easysdi Account
		$rowsAccount = array();
		$rowsAccount[] = JHTML::_('select.option','0', JText::_("SHOP_LIST_ACCOUNT_SELECT" ));
		$database->setQuery( "SELECT a.id as value, u.name as text FROM #__users u INNER JOIN #__sdi_account a ON u.id = a.user_id 
							WHERE ( a.root_id = $account->id OR a.id = $account->id )  " );
		$rowsAccount = array_merge($rowsAccount,$database->loadObjectList());
		
		//Get users
		$database->setQuery( "SELECT #__sdi_account.id as value, #__users.name as text 
								FROM #__users 
								INNER JOIN #__sdi_account 
								ON  #__users.id = #__sdi_account.user_id 
								WHERE (#__sdi_account.root_id IS NULL
								OR 
								 #__sdi_account.root_id = $account->id
								 OR #__sdi_account.id =  $account->id)
								ORDER BY text
								
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
		
		//Max file size to upload
		$database->setQuery( "SELECT value FROM #__sdi_configuration WHERE code = 'SHOP_CONFIGURATION_MAX_FILE_SIZE'");
		$product->maxFileSize = $database->loadResult();
		if ($database->getErrorNum()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		
		$grid_list=array();
		$database->setQuery( "SELECT NULL AS value, '".JText::_("SHOP_PRODUCT_GRID_SELECTION_MESSAGE")."' AS text UNION SELECT  id as value, name as text
				FROM #__sdi_grid
				" );
		$grid_list = $database->loadObjectList();
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		
		if (strlen($catalogUrlBase )==0){
				$mainframe->enqueueMessage("NO VALID CATALOG URL IS DEFINED","ERROR");
		}else{
			HTML_product::editProduct( $account,$product,$version,$supplier,$id,$accounts,$object_id, $objecttype_id,$objecttype_list,$object_list,$version_list,$diffusion_list,$baseMap_list,$treatmentType_list,$visibility_list,$accessibility_list,$perimeter_list,$rowsAccount,$rowsUser,$userPreviewSelected,$userDownloadSelected,$grid_list, $option );
		}
	}
	
	function suppressProduct($cid,$option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		$user = JFactory::getUser();
		//Check user's rights
		if(!userManager::isUserAllowed($user,"PRODUCT"))
		{
			return;
		}
		
		$rowProduct = new product( $database );
		$rowProduct->load( $cid[0] );	
		
		$query = "SELECT o.name as name FROM #__sdi_order_product p INNER JOIN #__sdi_order o ON o.id = p.order_id  WHERE p.product_id=$cid[0] ";
		$database->setQuery($query);
		$results = $database->loadObjectList();
		if(count($results)>0)
		{
			$mainframe->enqueueMessage(JText::_("SHOP_PRODUCT_DELETE_ERROR"),"INFO");
			foreach($results as $result)
			{
				$mainframe->enqueueMessage(" - ".$result->name,"INFO");
			}
			return;
		}

		if(!$rowProduct->delete())
		{
			$mainframe->enqueueMessage("SHOP_MESSAGE_ERROR_SUPPRESS_PRODUCT","ERROR");
			return;
		}
     }
	
	function listProduct(){
		global  $mainframe;

		$option=JRequest::getVar("option");
		$limit = JRequest::getVar('limit', 20 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$database =& JFactory::getDBO();		 	
		$user = JFactory::getUser();
		
		//Check user's rights
		if(!userManager::isUserAllowed($user,"PRODUCT"))
		{
			return;
		}
		
		$account = new accountByUserId($database);
		$account->load($user->id);		
		
		$search = $mainframe->getUserStateFromRequest( "searchProduct{$option}", 'searchProduct', '' );
		$filter = "";
		if ( $search )
		{
			if(strripos ($search,'"') != FALSE)
			{
				$searchcontent = substr($search, 1,strlen($search)-2 );
				$searchcontent = $database->getEscaped( trim( strtolower( $searchcontent ) ) );
				$filter .= " AND (o.name = '$searchcontent')";
			}
			else
			{
				$searchcontent = $database->getEscaped( trim( strtolower( $search ) ) );
				$filter .= " AND (o.name LIKE '%".$searchcontent."%')";
			}
		}
		
		
		
		//List only the products belonging to the current user
		$query = " SELECT p.*, v.metadata_id, y.code as visibility, m.account_id, md.guid as metadata_guid, v.title as version_title, o.name as object_name
							FROM #__sdi_product p 
							INNER JOIN #__sdi_objectversion v ON p.objectversion_id = v.id
							INNER JOIN #__sdi_object o ON o.id = v.object_id
							INNER JOIN #__sdi_metadata md ON md.id = v.metadata_id
							INNER JOIN #__sdi_manager_object m ON m.object_id = o.id 
							INNER JOIN #__sdi_list_visibility y ON  y.id = p.visibility_id 
							WHERE m.account_id = $account->id " ;
		
		$query .= $filter;
		$query .= " order by p.name";
		
		$database->setQuery($query);
		$total = count($database->loadObjectList() );
		
		$database->setQuery($query,$limitstart,$limit);		
		$rows = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		jimport('joomla.html.pagination'); 
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		HTML_product::listProduct($pageNav,$rows,$option,$account,$search);
		
}
	
	function sendMail ($rows,$subject,$body)
	{
			$mailer =& JFactory::getMailer();
			foreach ($rows as $row){
					//$mailer->addRecipient($row->email);
					$mailer->addBCC($row->email);																
				}
				$mailer->setSubject($subject);
				$user = JFactory::getUser();
				$mailer->setBody($body);
				if ($mailer->send() !==true){
				}
	}
	
	function deleteProductFile($option ) {
		global  $mainframe;
		$db =& JFactory::getDBO();
		$user = JFactory::getUser();
		$account = new accountByUserId($db);
		$account->load($user->id);
		
		//Check user's rights
		if(!userManager::isUserAllowed($user,"PRODUCT"))
		{
			return;
		}
		
		$id = JRequest::getVar('id', 0 );
		
		$product = new product( $db );
		$product->load( $id );
				
		//$product->tryCheckOut($option,'listProduct');
		
		if($product){
			$db->setQuery("DELETE FROM #__sdi_product_file WHERE product_id=".$id);
			$db->query();
			if ($db->getErrorNum()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				exit();
			}
			
			$db->setQuery("UPDATE #__sdi_product SET pathfile=null WHERE id=".$id);
			$db->query();
			if ($db->getErrorNum()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				exit();
			}
		}
		
		//$product->checkin();
	}
	
	function downloadFinalProduct(){
		global $mainframe;
		$db =& JFactory::getDBO();
		$product_id = JRequest::getVar('product_id');
		
		$user = JFactory::getUser();
		$account = new accountByUserId($db);
		$account->load($user->id);		
		
		//Check user's rights
		if(!userManager::isUserAllowed($user,"PRODUCT")){
			$mainframe->enqueueMessage(JText::_("SHOP_MSG_NOT_ALLOWED_TO_MANAGE"),"ERROR");
			return;
		}
		//Check if the current user is manager of the product
		$query = "SELECT COUNT(*) FROM #__sdi_manager_object 
						WHERE object_id IN (SELECT v.object_id FROM #__sdi_objectversion v 
																INNER JOIN #__sdi_product p ON p.objectversion_id = v.id 
																WHERE p.id = $product_id )
						AND account_id = $account->id";
		$db->setQuery($query);
		$result = $db->loadResult();
		if($result < 1){
			$mainframe->enqueueMessage(JText::_("SHOP_MSG_NOT_ALLOWED_TO_MANAGE"),"ERROR");
			return;
		}
		
		//Return the product
		$product = new product($db);
		$product->load($product_id);
		$file = $product->getFile();
		$fileName = $product->getFileName();

		error_reporting(0);

		ini_set('zlib.output_compression', 0);
		header('Pragma: public');
		header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
		header('Content-Transfer-Encoding: none');
		header("Content-Length: ".strlen($file));
		header('Content-Type: application/octetstream; name="'.$product->getFileExtension().'"');
		header('Content-Disposition: attachement; filename="'.$fileName.'"');

		echo $file;
		die();
	}

	function cancelProduct($option)
	{
		global $mainframe;
		$database = & JFactory::getDBO();
		$product = new product( $database );
		$product->bind(JRequest::get('post'));
		$product->checkin();

		$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listProduct'), false ));
	}
}
?>