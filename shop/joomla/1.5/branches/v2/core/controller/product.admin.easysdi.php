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
		if ($published){
			$query = "update #__easysdi_product  set published = 1  where id=$cid[0]";

		}else{
			$query = "update #__easysdi_product  set published = 0  where id=$cid[0]";
		}
		$db->setQuery( $query ,$limitstart,$limit);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

	}

	function listProduct($option) {
		global  $mainframe;
		$db =& JFactory::getDBO();

		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',1);
//		$profile = $mainframe->getUserStateFromRequest( "profile{$option}", 'profile', '' );
//		$category = $mainframe->getUserStateFromRequest( "category{$option}", 'category', '' );
//		$payment = $mainframe->getUserStateFromRequest( "payment{$option}", 'payment', '' );
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );

		$query = "SELECT COUNT(*) FROM #__easysdi_product ";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT p.*, t.translation , a.name AS text
					FROM #__easysdi_product p 
					INNER JOIN #__easysdi_product_treatment_type t ON  p.treatment_type=t.id 
					INNER JOIN (SELECT c.id , u.name FROM #__sdi_account c, #__users  u WHERE c.user_id = u.id ) a ON p.partner_id = a.id ";
		if ($search !=null && strlen($search)>0 ) {$query = $query ." WHERE data_title like '%$search%' ";}	
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
		$rowProduct = new product( $database );

		if($id == '0')
		{
			$id = JRequest::getVar('id', 0 );
		}
		if ($id == '0' ){
			$rowProduct->creation_date =date('d.m.Y H:i:s');
			$rowProduct->metadata_id = helper_easysdi::getUniqueId();
			$partner = new accountByUserId($database);
			$partner->load($user->id);
			$rowProduct->partner_id = $partner->id;
			//Default metadata manager set to current product manager. 
//			if(userManager::hasRight($partner->id,"METADATA"))
//			{
//				$rowProduct->metadata_partner_id = $partner->id;
//			}
		}
		else
		{
			$rowProduct->load( $id );
		}

		$rowProduct->update_date = date('d.m.Y H:i:s');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");

		//Select all available easysdi Account
		$rowsAccount = array();
		$rowsAccount[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_ACCOUNT_SELECT" ));
		$rowsAccount = array_merge($rowsAccount,userManager::getEasySDIAccountsList());
		
		//List of partners with PRODUCT right
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.id AS value, b.name AS text 
										FROM #__sdi_account a,#__users b 
										WHERE a.user_id = b.id 
										AND a.root_id IS NULL
										AND a.id IN (SELECT account_id FROM #__sdi_actor WHERE role_id = (SELECT id FROM #__sdi_list_role WHERE code ='PRODUCT'))									
										ORDER BY b.name" );
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		//List of partners with METADATA right
		$metadata_partner = array();
		$metadata_partner[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$current_manager_partner = JRequest::getVar('partner_id', 0 );	
		if ($current_manager_partner == '0') $current_manager_partner = $rowProduct->partner_id;	
		$rowPartner = new account( $database );
		$rowPartner->load( $current_manager_partner );
		if($rowPartner->root_id == "") $rowPartner->root_id = '0';
		$database->setQuery("SELECT a.id AS value, b.name AS text 
							   FROM #__sdi_account a, #__users b  
							   WHERE a.user_id = b.id
							   AND  (a.root_id = $rowPartner->root_id OR a.root_id = $rowPartner->id OR a.id = $rowPartner->id )
							   AND a.id IN (SELECT account_id FROM #__sdi_actor WHERE role_id = (SELECT id FROM #__sdi_list_role WHERE code ='METADATA'))
							   ORDER BY b.name");	
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		$metadata_partner = array_merge( $metadata_partner, $database->loadObjectList() );
		
		//List of partners with ADMIN right
		$admin_partner = array();
		$admin_partner[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery("SELECT a.id AS value, b.name AS text 
							   FROM #__sdi_account a, #__users b  
							   WHERE a.user_id = b.id 
							   AND  (a.root_id = $rowPartner->root_id OR a.root_id = $rowPartner->id OR a.id = $rowPartner->id )
							   AND a.id IN (SELECT account_id FROM #__sdi_actor WHERE role_id = (SELECT id FROM #__sdi_list_role WHERE code ='PRODUCT'))
							   ORDER BY b.name");
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		$admin_partner = array_merge( $admin_partner, $database->loadObjectList() );

		//List of partners with diffusion right
		$diffusion_partner = array();
		$diffusion_partner[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery("SELECT a.id AS value, b.name AS text 
							   FROM #__sdi_account a, #__users b 
							   WHERE a.user_id = b.id 
							   AND  (a.root_id = $rowPartner->root_id OR a.root_id = $rowPartner->id OR a.id = $rowPartner->id )
							   AND a.id IN (SELECT account_id FROM #__sdi_actor WHERE role_id = (SELECT id FROM #__sdi_list_role WHERE code ='DIFFUSION'))
							   ORDER BY b.name");	
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}	
		$diffusion_partner = array_merge($diffusion_partner, $database->loadObjectList());

		//List of available BaseMap
		$baseMaplist = array();		
		$baseMaplist [] = JHTML::_('select.option','0', JText::_("EASYSDI_BASEMAP_LIST") );
		$database->setQuery( "SELECT id AS value,  alias AS text FROM #__easysdi_basemap_definition " );
		$baseMaplist = array_merge($baseMaplist, $database->loadObjectList()) ;
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
	
		//Product treatment
		$treatmentTypeList = array();		
		$database->setQuery( "SELECT id AS value,  translation AS text FROM #__easysdi_product_treatment_type " );
		$treatmentTypeList = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		HTML_product::alter_array_value_with_JTEXT_($treatmentTypeList);

		$standardlist = array();
		$standardlist[] = JHTML::_('select.option','0', JText::_("EASYSDI_TABS_LIST") );
//		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_standard  WHERE is_deleted =0 " );
//		$standardlist= $database->loadObjectList() ;		
//		if ($database->getErrorNum()) {
//			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
//		}
		
		//List of perimeters
		$perimeterList = array();
		$query = "SELECT id AS value, perimeter_name AS text FROM #__easysdi_perimeter_definition ";
		$database->setQuery( $query );
		$perimeterList = $database->loadObjectList() ;
		if ($database->getErrorNum()) {						
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
		}		
				
		$selected_perimeter = array();
		$query = "SELECT perimeter_id AS value FROM #__easysdi_product_perimeter WHERE product_id=".$rowProduct->id;				
		$database->setQuery( $query );
		$selected_perimeter = $database->loadObjectList();
		if ($database->getErrorNum()) {						
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
		}
			
		if (strlen($catalogUrlBase )==0)
		{
			$mainframe->enqueueMessage("NO VALID CATALOG URL IS DEFINED","ERROR");
		}
		else
		{
			HTML_product::editProduct( $rowProduct,$current_manager_partner,$rowsAccount,$partners,$metadata_partner,$admin_partner,$diffusion_partner,$baseMaplist,$treatmentTypeList,$standardlist,$perimeterList,$selected_perimeter,$catalogUrlBase,$id, $option );
		}
	}
	
	function saveProduct($returnList ,$option){
		global  $mainframe;
		$database=& JFactory::getDBO();
		$option =  JRequest::getVar("option");
		$rowProduct =&	 new Product($database);

		if (!$rowProduct->bind( $_POST )) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}
		
		$service_type = JRequest::getVar('service_type');
		if($service_type == "via_proxy")
		{
			$rowProduct->previewUser = "";
			$rowProduct->previewpassword = "";
		}
		else
		{
			$rowProduct->easysdi_account_id="";
		}

		if (!$rowProduct->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}

		$query = "DELETE FROM  #__easysdi_product_perimeter WHERE PRODUCT_ID = ".$rowProduct->id;
		$database->setQuery( $query );
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}

		foreach( $_POST['perimeter_id'] as $perimeter_id )
		{
			$query = "INSERT INTO #__easysdi_product_perimeter VALUES (0,$rowProduct->id,$perimeter_id,0)";
			$database->setQuery( $query );
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
				exit();
			}
		}

		foreach( $_POST['buffer'] as $bufferPerimeterId )
		{
			$query = "UPDATE #__easysdi_product_perimeter SET isBufferAllowed=1 WHERE product_id = $rowProduct->id AND perimeter_id = $bufferPerimeterId";
			
			$database->setQuery( $query );
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				//$mainframe->redirect("index.php?option=$option&task=listProduct" );	
				exit();			
			}
		}

		$query = "DELETE FROM  #__easysdi_product_property WHERE PRODUCT_ID = ".$rowProduct->id;
		$database->setQuery( $query );
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}

		foreach( $_POST['properties_id'] as $properties_id )
		{
			$query = "INSERT INTO #__easysdi_product_property VALUES (0,".$rowProduct->id.",".$properties_id.")";

			$database->setQuery( $query );
			if (!$database->query()) {
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
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit;
		}
		
		foreach( $cid as $id )
		{
			$product = new product( $database );
			$product->load( $id );
			if(!$rowProduct->deleteProduct())$mainframe->enqueueMessage("ERROR SUPPRESS PRODUCT","ERROR");
//			if (!$product->delete()) {
//				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
//				$mainframe->redirect("index.php?option=$option&task=listProduct" );
//			}
//
//			$query = "DELETE FROM  #__easysdi_product_perimeter WHERE PRODUCT_ID = ".$id;
//			$database->setQuery( $query );
//			if (!$database->query()) {
//				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
//				$mainframe->redirect("index.php?option=$option&task=listProduct" );
//				exit();
//			}
//
//			$query = "DELETE FROM  #__easysdi_product_property WHERE PRODUCT_ID = ".$id;
//			$database->setQuery( $query );
//			if (!$database->query()) {
//				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
//				$mainframe->redirect("index.php?option=$option&task=listProduct" );
//				exit();
//			}
		}
		
		$limitstart = JRequest::getVar("limitstart");
		$limit = JRequest::getVar("limit");
		$mainframe->redirect("index.php?option=$option&task=listProduct&limitstart=$limitstart&limit=$limit" );
	}

	
	
}

?>