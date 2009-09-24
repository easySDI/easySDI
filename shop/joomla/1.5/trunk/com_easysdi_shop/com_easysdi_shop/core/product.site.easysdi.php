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
	
	function saveProductMetadata(){
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		global  $mainframe;
		$database =& JFactory::getDBO();

		$user = JFactory::getUser();
		//Check user's rights
		if(!userManager::isUserAllowed($user,"METADATA"))
		{
			return;
		}
		 
		$metadata_standard_id = JRequest::getVar("standard_id");
		$metadata_id = JRequest::getVar("metadata_id");
		
		/* Liste des onglets � traiter */
		$query = "SELECT b.text as text,a.tab_id as tab_id 
				  FROM #__easysdi_metadata_standard_classes a, 
				  	   #__easysdi_metadata_tabs b 
				  WHERE a.tab_id =b.id 
				  		AND (a.standard_id = $metadata_standard_id 
				  			 OR a.standard_id IN (SELECT inherited 
				  			 					  FROM #__easysdi_metadata_standard 
				  			 					  WHERE is_deleted =0 
				  			 					  	    AND id = $metadata_standard_id
				  			 					  )
							) 
				  GROUP BY a.tab_id";
				  
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}
		$doc="<gmd:MD_Metadata xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" xmlns:gco=\"http://www.isotc211.org/2005/gco\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:gml=\"http://www.opengis.net/gml\" xmlns:gts=\"http://www.isotc211.org/2005/gts\" xmlns:ext=\"http://www.depth.ch/2008/ext\">";
		foreach ($rows as $row){
			/* Pour chaque onglets, liste des classes � traiter */
			$query = "SELECT * 
					  FROM #__easysdi_metadata_standard_classes a, 
					  	   #__easysdi_metadata_classes b 
					  WHERE a.class_id =b.id 
					  		AND a.tab_id = $row->tab_id 
					  		AND (a.standard_id = $metadata_standard_id 
					  			 OR a.standard_id IN (SELECT inherited 
					  			 					  FROM #__easysdi_metadata_standard 
					  			 					  WHERE is_deleted =0 
					  			 					  		AND id = $metadata_standard_id
					  			 					  )
					  			)
					  ORDER BY position";
			$database->setQuery($query);
			$rowsClasses = $database->loadObjectList();
			if ($database->getErrorNum()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
				
			/* Traitement de chaque classe */
			foreach ($rowsClasses as $rowClasses){
				$doc=$doc."<$rowClasses->iso_key>";
				$count = helper_easysdi::searchForLastEntry($rowClasses,$metadata_standard_id);
				echo "<hr>SearchForLastEntry: ".$count."<br>";
				for ($i=0;$i<helper_easysdi::searchForLastEntry($rowClasses,$metadata_standard_id);$i++){										
					echo "rowClasses ".$rowClasses->id." : ".$rowClasses->iso_key."<br>";
					helper_easysdi::generateMetadata($rowClasses,$row->tab_id,$metadata_standard_id,$rowClasses->iso_key,&$doc,$i);							
				}
				echo "<hr>";
				//helper_easysdi::generateMetadata($rowClasses,$row->tab_id,$metadata_standard_id,$rowClasses->iso_key,&$doc);
				$doc=$doc."</$rowClasses->iso_key>";
			}


		}
		$doc=$doc."</gmd:MD_Metadata>";

		$f = fopen('c:\\doc.xml', 'w');
		fwrite($f, $doc);
		fclose($f);
		
		$xmlstr = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
		<csw:Transaction service=\"CSW\"
		version=\"2.0.0\"
		xmlns:csw=\"http://www.opengis.net/cat/csw\" >
		<csw:Insert>
		$doc
		</csw:Insert>
		</csw:Transaction>";
		
		$xmlstrToDelete = "<csw:Transaction service=\"CSW\" version=\"2.0.0\" 
						   xmlns:csw=\"http://www.opengis.net/cat/csw\" 
						   xmlns:dc=\"http://www.purl.org/dc/elements/1.1/\"
						   xmlns:ogc=\"http://www.opengis.net/ogc\">
						  <csw:Delete typeName=\"csw:Record\">
						    <csw:Constraint version=\"2.0.0\">
						      <ogc:Filter>
						        <ogc:PropertyIsEqualTo>        
						            <ogc:PropertyName>//gmd:fileIdentifier/gco:CharacterString</ogc:PropertyName>
						            <ogc:Literal>$metadata_id</ogc:Literal>
						        </ogc:PropertyIsEqualTo>
						      </ogc:Filter>
						    </csw:Constraint>
						  </csw:Delete>
						</csw:Transaction>";
		
		//Try to discover if a metadata already exists. 
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");				
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.1&elementSetName=full&id=".$metadata_id;				
		$cswResults = DOMDocument::load($catalogUrlGetRecordById); 
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');		
		$geoMD = new geoMetadata($cswResults);				 
		
		SITE_product::SaveMetadata($xmlstrToDelete);
		SITE_product::SaveMetadata($xmlstr);
			
		$query = "UPDATE #__easysdi_product SET hasMetadata = 1 WHERE id = ".$metadata_standard_id = JRequest::getVar("id");
		$database->setQuery( $query );
		if (!$database->query()){
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProductMetadata" );
			exit();
		}
		
		//Send a Mail to notify the administrator that a new product is created 
		if ( strlen($geoMD->getFileIdentifier()) == 0){
			//find all the users interrested to know if a new metadata is created.
			$query = "SELECT count(*) FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND (#__users.usertype='Administrator' OR #__users.usertype='Super Administrator') AND #__easysdi_community_partner.notify_new_metadata=1";
			$database->setQuery( $query );
			$total = $database->loadResult();
			if($total >0){
			$query = "SELECT * FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND (#__users.usertype='Administrator' OR #__users.usertype='Super Administrator') AND #__easysdi_community_partner.notify_new_metadata=1";
			$database->setQuery( $query );

			$rows = $database->loadObjectList();
			$mailer =& JFactory::getMailer();
			$user = JFactory::getUser();
			SITE_product::sendMail($rows,JText::_("EASYSDI_NEW_METADATA_MAIL_SUBJECT"),JText::sprintf("EASYSDI_NEW_METADATA_MAIL_BODY",$metadata_id,$user->username));																
			}
		}
		
		//Send a Mail to the users that have requested to be notified when the metadata has just been changed
		$product_id = JRequest::getVar("product_id",0);
		$query = "SELECT email,data_title FROM #__easysdi_user_product_favorite f, #__easysdi_community_partner p,#__users u,#__easysdi_product pr  where f.partner_id = p.partner_id  AND p.user_id = u.id and pr.id = f.product_id AND f.product_id = $product_id AND notify_metadata_modification = 1";
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		$mailer =& JFactory::getMailer();
		$user = JFactory::getUser();
		if (count($rows) >0){
			SITE_product::sendMail($rows,JText::_("EASYSDI_METADATA_HAS_CHANGED_MAIL_SUBJECT"),JText::sprintf("EASYSDI_METADATA_HAS_CHANGED_MAIL_BODY",$rows[0]->data_title));
		}
	}
		
	function sendMailByEmail($email,$subject,$body){

		
				$mailer =& JFactory::getMailer();		
				$mailer->addBCC($email);																				
				$mailer->setSubject($subject);
				$user = JFactory::getUser();
				$mailer->setBody($body);				
				if ($mailer->send() !==true){
					
				}		
						
	}
	
	function sendMail ($rows,$subject,$body){
					 
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
		
	
	function SaveMetadata($xmlstr){
				
		$content_length = strlen($xmlstr);

		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");

		$session = curl_init($catalogUrlBase);



		curl_setopt ($session, CURLOPT_POST, true);
		curl_setopt ($session, CURLOPT_POSTFIELDS, $xmlstr);


		// Don't return HTTP headers. Do return the contents of the call
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

		// Make the call
		$xml = curl_exec($session);
		

		curl_close($session);
		return $xml;

	}

	
	function saveProduct($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$user = JFactory::getUser();
		//Check user's rights
		if(!userManager::isUserAllowed($user,"PRODUCT"))
		{
			return;
		}
		$rowPartner = new partnerByUserId( $database );
		$rowPartner->load( $user->id );
		
		
		$rowProduct =& new Product($database);
		$rowProductOld =& new Product($database);
		$sendMail = false;
		
		$id = JRequest::getVar("id",0);
		if($id >0){
			$rowProductOld->load($id);
			if ($rowProductOld->published == 0 && JRequest::getVar("published",0) ==1){
				$sendMail = true;
			}
			
		}
		if (!$rowProduct->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}
				
		$rowProduct->admin_partner_id = $rowPartner->partner_id;
		if($rowPartner->root_id)
		{
			$rowProduct->partner_id = $rowPartner->root_id;
		}
		else
		{
			$rowProduct->partner_id = $rowPartner->partner_id;
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
				//echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				//$mainframe->redirect("index.php?option=$option&task=listProduct" );	
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
		
		
		if ($sendMail){
			$query = "SELECT count(*) FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND (#__users.usertype='Administrator' OR #__users.usertype='Super Administrator') AND #__easysdi_community_partner.notify_distribution=1";
			$database->setQuery( $query );
			$total = $database->loadResult();
			if($total >0){
			$query = "SELECT * FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND (#__users.usertype='Administrator' OR #__users.usertype='Super Administrator') AND #__easysdi_community_partner.notify_distribution=1";
			$database->setQuery( $query );

			$rows = $database->loadObjectList();
			$mailer =& JFactory::getMailer();
			$user = JFactory::getUser();
			SITE_product::sendMail($rows,JText::_("EASYSDI_NEW_DISTRIBUTION_MAIL_SUBJECT"),JText::sprintf("EASYSDI_NEW_DISTIBUTION_MAIL_BODY",$rowProduct->data_title,$user->username));																
			}
		}	
			
		 //SITE_product::SaveMetadata();
		 
	
		
	}
	
	function editProduct( $isNew = false) {
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		global  $mainframe;
		$user = JFactory::getUser();
		//Check user's rights
		if(!userManager::isUserAllowed($user,"PRODUCT"))
		{
			return;
		}
		
		if (!$isNew){
		$id = JRequest::getVar('id');
		}else {
			$id=0;
		}
		
		$option = JRequest::getVar('option');
		
		//Allows Pathway with mod_menu_easysdi
		if($isNew)
		{
			breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_PRODUCT_CREATE",
										   "EASYSDI_MENU_ITEM_PRODUCTS",
										   "index.php?option=$option&task=listProduct");
		}
		else
		{
			breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_PRODUCT_EDIT",
										   "EASYSDI_MENU_ITEM_PRODUCTS",
										   "index.php?option=$option&task=listProduct");
		}

		$database =& JFactory::getDBO(); 
		$rowProduct = new product( $database );
		$rowProduct->load( $id );					
		
		if ($id ==0){
			$rowProduct->creation_date =date('d.m.Y H:i:s');
			$rowProduct->metadata_id = helper_easysdi::getUniqueId();
			
			$partner = new partnerByUserId($database);
			$partner->load($user->id);
			$rowProduct->partner_id = $partner->partner_id;			
			if(userManager::hasRight($partner->partner_id,"METADATA"))
			{
				$rowProduct->metadata_partner_id = $partner->partner_id;
			}
		}
		
		$rowProduct->update_date = date('d.m.Y H:i:s'); 
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		if (strlen($catalogUrlBase )==0){
				$mainframe->enqueueMessage("NO VALID CATALOG URL IS DEFINED","ERROR");
		}else{
			HTML_product::editProduct( $rowProduct,$id, $option );
		}
	}
	
	
	function editMetadata($isNew = false) {
		global  $mainframe;
		if (!$isNew){
		$id = JRequest::getVar('id');
		}else {
			$id=0;
		}
		
		$user = JFactory::getUser();
		//Check user's rights
		if(!usermanager::isUserAllowed($user,"METADATA"))
		{
			return;
		}
		
		$option = JRequest::getVar('option');
		//Allows Pathway with mod_menu_easysdi
		breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_METADATA_EDIT",
										   "EASYSDI_MENU_ITEM_METADATA",
										   "index.php?option=$option&task=listProductMetadata"); 
		
		$database =& JFactory::getDBO(); 
		$rowProduct = new product( $database );
		$rowProduct->load( $id );					
	
		if ($id ==0){
			$rowProduct->creation_date =date('d.m.Y H:i:s');
			$rowProduct->metadata_id = uniqid();
			 			
		}
		$rowProduct->update_date = date('d.m.Y H:i:s'); 
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		if (strlen($catalogUrlBase )==0){
				$mainframe->enqueueMessage("NO VALID CATALOG URL IS DEFINED","ERROR");
		}else{
		HTML_product::editMetadata( $rowProduct,$id, $option );
		}
	}
	
	
	function editMetadata2($isNew = false) {
		global  $mainframe;
		
		if (!$isNew){
		$id = JRequest::getVar('id');
		}else {
			$id=0;
		}
		
		$user = JFactory::getUser();
		//Check user's rights
		if(!usermanager::isUserAllowed($user,"METADATA"))
		{
			return;
		}
		
		$option = JRequest::getVar('option');
		  
		$database =& JFactory::getDBO(); 
		$rowProduct = new product( $database );
		$rowProduct->load( $id );					
	
		if ($id ==0){
			$rowProduct->creation_date =date('d.m.Y H:i:s');
			$rowProduct->metadata_id = uniqid();
			 			
		}
		$rowProduct->update_date = date('d.m.Y H:i:s'); 
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		if (strlen($catalogUrlBase )==0){
				$mainframe->enqueueMessage("NO VALID CATALOG URL IS DEFINED","ERROR");
		}else{
		HTML_product::editMetadata2( $rowProduct,$id, $option );
		}
	}
	
	
	function listProduct(){
		global  $mainframe;

		//Allows Pathway with mod_menu_easysdi
		breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_PRODUCTS");
        
		$option=JRequest::getVar("option");
		$limit = JRequest::getVar('limit', 5 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		
		$database =& JFactory::getDBO();		 	
		$user = JFactory::getUser();
		//Check user's rights
		if(!userManager::isUserAllowed($user,"PRODUCT"))
		{
			return;
		}
		
		$rootPartner = new partnerByUserId($database);
		$rootPartner->load($user->id);		
		
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $database->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";
		if ( $search ) {
			$filter .= " AND (data_title LIKE '%$search%')";			
		}
		$partner = new partnerByUserId($database);
		$partner->load($user->id);

		/*$queryCount = "select count(*) from #__easysdi_product where 
											(partner_id in 
												(SELECT partner_id FROM #__easysdi_community_partner 
												where root_id = 
												( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id) 
												OR  partner_id = 
												( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id)  
												OR root_id = $partner->partner_id OR  partner_id = $partner->partner_id)
											) ";*/
		
		//List only the products belonging to the current user
		$queryCount = " SELECT COUNT(*) FROM #__easysdi_product where admin_partner_id = $partner->partner_id " ;
		$queryCount .= $filter;
		$database->setQuery($queryCount);
		$total = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		$pageNav = new JPagination($total,$limitstart,$limit);
		/*$query = "select * from #__easysdi_product
						   where (partner_id in 
						   		(SELECT partner_id FROM #__easysdi_community_partner 
						   							where  
						   							root_id = ( SELECT root_id FROM #__easysdi_community_partner 
						   														where partner_id=$partner->partner_id) 
						   							OR  
						   							partner_id = ( SELECT root_id FROM #__easysdi_community_partner 
						   															where partner_id=$partner->partner_id)  
						   							OR 
						   							root_id = $partner->partner_id 
						   							OR  
						   							partner_id = $partner->partner_id
						   							)) ";*/
		//List only the products belonging to the current user
		$query = " SELECT * FROM #__easysdi_product where admin_partner_id = $partner->partner_id " ;
		$query .= $filter;
		$database->setQuery($query,$limitstart,$limit);		
		$rows = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		HTML_product::listProduct($pageNav,$rows,$option,$rootPartner);
		
/*		if (helper_easysdi::hasRight($rootPartner->partner_id,"INTERNAL")){
		HTML_product::listProduct($pageNav,$rows,$option,$rootPartner);
		}else{
			$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_ALLOWED_TO_MANAGE_PRODUCT"),"INFO");
		}
*/		
		
}
	
	
	function listProductMetadata(){
		global  $mainframe;
		
		breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_METADATA");
		
		
		$option=JRequest::getVar("option");
		$limit = JRequest::getVar('limit', 5 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		
		$database =& JFactory::getDBO();		 	
		$user = JFactory::getUser();
		
		//Check user's rights
		if(!userManager::isUserAllowed($user,"METADATA"))
		{
			return;
		}
		
		$rootPartner = new partnerByUserId($database);
		$rootPartner->load($user->id);		
		
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $database->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";
		if ( $search ) {
			$filter .= " AND (data_title LIKE '%$search%')";			
		}
		$partner = new partnerByUserId($database);
		$partner->load($user->id);

		//$queryCount = "select count(*) from #__easysdi_product where (partner_id in (SELECT partner_id FROM #__easysdi_community_partner where  root_id = ( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id) OR  partner_id = ( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id)  OR root_id = $partner->partner_id OR  partner_id = $partner->partner_id)) ";
		
		//List only the products for which metadata manager is the current user
		$queryCount = " SELECT COUNT(*) FROM #__easysdi_product where metadata_partner_id = $partner->partner_id " ;
		$queryCount .= $filter;
		
		$database->setQuery($queryCount);
		$total = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		$pageNav = new JPagination($total,$limitstart,$limit);
		//$query = "select * from #__easysdi_product where (partner_id in (SELECT partner_id FROM #__easysdi_community_partner where  root_id = ( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id) OR  partner_id = ( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id)  OR root_id = $partner->partner_id OR  partner_id = $partner->partner_id)) ";
		//List only the products for which metadata manager is the current user
		$query = " SELECT * FROM #__easysdi_product where metadata_partner_id = $partner->partner_id " ;
		$query .= $filter;

		$database->setQuery($query,$limitstart,$limit);		
		$rows = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		HTML_product::listProductMetadata($pageNav,$rows,$option,$rootPartner);	
		
	/*if (helper_easysdi::hasRight($rootPartner->partner_id,"INTERNAL")){
		HTML_product::listProductMetadata($pageNav,$rows,$option,$rootPartner);		
	}else{
		$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_ALLOWED_TO_MANAGE_METADATA"),"INFO");
	}*/
		
		
	
}


}
?>