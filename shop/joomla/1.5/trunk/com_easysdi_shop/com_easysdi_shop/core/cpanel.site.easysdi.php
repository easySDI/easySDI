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
class SITE_cpanel {

	function downloadProduct(){

		$database =& JFactory::getDBO();
		$user = JFactory::getUser();
		$order_id = JRequest::getVar('order_id');
		$product_id = JRequest::getVar('product_id');
		
		//retrieve granted user to download product: owner of the order and furnisher
		$query = "select user_id from #__easysdi_order where order_id = ".$order_id;
		$database->setQuery($query);
		$orderOwner = $database->loadResult();
		
		$query = "SELECT u.id FROM #__users u, #__easysdi_product p, #__easysdi_community_partner cp where u.id=cp.user_id and cp.partner_id=p.diffusion_partner_id and p.id=".$product_id;
		$database->setQuery($query);
		$productFurnisher = $database->loadResult();
		
		//restrict acces to order's owner and product's diffuser
		if($user->id != $orderOwner && $user->id != $productFurnisher)
			die();

		$query = "SELECT oplb.data as data, opl.filename as filename FROM #__easysdi_order_product_list opl, #__easysdi_order_product_list_blob oplb where opl.id=oplb.order_product_list_id AND opl.product_id = $product_id AND opl.order_id = $order_id";
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
	
	function archiveOrder(){
		global  $mainframe;
		$option=JRequest::getVar("option");
		$order_id=JRequest::getVar("order_id",0);
		if ($order_id == 0){
			echo "<div class='alert'>";
			echo JText::_("EASYSDI_ERROR_NO_ORDER_ID");
			echo "</div>";
		}else {
			$database =& JFactory::getDBO();
			$user = JFactory::getUser();

			$rootPartner = new partnerByUserId($database);
			$rootPartner->load($user->id);
				
			$queryStatus = "select id from #__easysdi_order_status_list where code ='ARCHIVED'";
			$database->setQuery($queryStatus);
			$status_id = $database->loadResult();
			
			jimport("joomla.utilities.date");
			$date = new JDate();
			
			$query = "update #__easysdi_order set status = ".$status_id.", order_update = '".$date->toMySQL()."' where user_id = ".$user->id." AND ORDER_ID =".$order_id;
			$database->setQuery($query);
			if (!$database->query()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}
		}
	}
	
	function listProductsForPartnerId(){
		global  $mainframe;
		$option=JRequest::getVar("option");
		$partner_id=JRequest::getVar("id",0);
		if ($partner_id == 0){
			echo "<div class='alert'>";
			echo JText::_("EASYSDI_ERROR_NO_PRODUCT_ID");
			echo "</div>";
		}else {
			$database =& JFactory::getDBO();				
			$catalogUrlBase = config_easysdi::getValue("catalog_url");
			
			$query = "select * from #__easysdi_product where partner_id =".$partner_id;
			//echo $query;
			$database->setQuery($query);
			$rows = $database->loadObjectList();
			
			$csv = "id;titre;responsable;gestionnaire\r\n";
			foreach ($rows as $row)
			{
				$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&outputSchema=csw:IsoRecord&elementSetName=full&id=".$row->metadata_id;
				$cswResults = DOMDocument::load($catalogUrlGetRecordById);
				
				//echo var_dump($cswResults)."<br>";
				$doc = new DOMDocument('1.0', 'UTF-8');
				
				if ($cswResults <> false)
				$xpathResults = new DOMXPath($cswResults);
				else
				$xpathResults = new DOMXPath($doc);
				$xpathResults->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.1');
				$xpathResults->registerNamespace('dc','http://purl.org/dc/elements/1.1/');
				$xpathResults->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
				$xpathResults->registerNamespace('gco','http://www.isotc211.org/2005/gco');
				$xpathResults->registerNamespace('srv','http://www.isotc211.org/2005/srv');
				$xpathResults->registerNamespace('ext','http://www.depth.ch/2008/ext');
				$xpathResults->registerNamespace('xlink','http://www.w3.org/1999/xlink');		
				
				$node = $xpathResults->query("//gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString");
				$mid = $node->item(0)->nodeValue;
				
				$node = $xpathResults->query("//gmd:MD_Metadata/gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString");
				$resp = $node->item(0)->nodeValue;
				//echo "responsable:".$resp;
				
				$node = $xpathResults->query("//gmd:MD_Metadata/gmd:contact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString");
				$gest = $node->item(0)->nodeValue;
				//echo "gestionnaire:".$gest;
				
				$node = $xpathResults->query("//gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString");
				$title = $node->item(0)->nodeValue;
				//echo "title:".$title;
				
				$csv .= utf8_decode($mid.";".$title.";".$resp.";".$gest."\r\n");
			}
			
			error_reporting(0);
			ini_set('zlib.output_compression', 0);
			header('Pragma: public');
			header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
			header('Content-Transfer-Encoding: none');
			header('Content-Type: application/octetstream; name="export.csv"');
			header('Content-Disposition: attachement; filename="export.csv"');

			echo $csv;	

			die();
			
		}
	}
	
	function suppressOrder(){
		global  $mainframe;
		$option=JRequest::getVar("option");
		$order_id=JRequest::getVar("order_id",0);
		if ($order_id == 0){
			echo "<div class='alert'>";
			echo JText::_("EASYSDI_ERROR_NO_ORDER_ID");
			echo "</div>";
		}else {
			$database =& JFactory::getDBO();
			$user = JFactory::getUser();

			$rootPartner = new partnerByUserId($database);
			$rootPartner->load($user->id);
				
			$queryStatus = "select id from #__easysdi_order_status_list where code ='SAVED'";
			$database->setQuery($queryStatus);
			$status_id = $database->loadResult();
			
			jimport("joomla.utilities.date");
			$date = new JDate();
			
			$query_order_status = "select status from #__easysdi_order where user_id = ".$user->id." AND ORDER_ID =".$order_id;
			$database->setQuery($query_order_status);
			$order_status = $database->loadResult();
			
			//User must own this order to delete it
			if ($order_status == ""){
				echo "<div class='alert'>";
				echo JText::_("EASYSDI_ERROR_NO_SUCH_ORDER_FOR_USER");
				echo "</div>";
			}
			
			//Only draft are permitted for deletion
			if ($order_status <> $status_id){
				echo "<div class='alert'>";
				echo JText::_("EASYSDI_ERROR_TRY_DELETE_ORDER_OTHER_THAN_DRAFT");
				echo "</div>";
			}
			
			$query = "delete from #__easysdi_order where user_id = ".$user->id." AND ORDER_ID =".$order_id;
			$database->setQuery($query);
			if (!$database->query()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}
		}
	}
	
	function copyOrder(){
		global  $mainframe;
		$option=JRequest::getVar("option");
		$order_id=JRequest::getVar("order_id",0);
		$order_copy_id="";
		if ($order_id == 0){
			echo "<div class='alert'>";
			echo JText::_("EASYSDI_ERROR_NO_ORDER_ID");
			echo "</div>";
		}else {
			$database =& JFactory::getDBO();
			$user = JFactory::getUser();

			$rootPartner = new partnerByUserId($database);
			$rootPartner->load($user->id);
			
			$queryStatus = "select id from #__easysdi_order_product_status_list where code ='AWAIT'";
			$database->setQuery($queryStatus);
			$await = $database->loadResult();
			
			$queryStatus = "select id from #__easysdi_order_status_list where code ='SAVED'";
			$database->setQuery($queryStatus);
			$saved = $database->loadResult();
			
			$queryStatus = "select id from #__easysdi_order_status_list where code ='FINISH'";
			$database->setQuery($queryStatus);
			$finish = $database->loadResult();
			
			$queryStatus = "select id from #__easysdi_order_status_list where code ='ARCHIVED'";
			$database->setQuery($queryStatus);
			$archived = $database->loadResult();
			
			$queryStatus = "select id from #__easysdi_order_status_list where code ='HISTORIZED'";
			$database->setQuery($queryStatus);
			$historized = $database->loadResult();
			
			jimport("joomla.utilities.date");
			$date = new JDate();
			
			$query_order_status = "select status from #__easysdi_order where user_id = ".$user->id." AND ORDER_ID =".$order_id;
			$database->setQuery($query_order_status);
			$order_status = $database->loadResult();
			
			//User must own this order to copy it
			if ($order_status == ""){
				echo "<div class='alert'>";
				echo JText::_("EASYSDI_ERROR_NO_SUCH_ORDER_FOR_USER");
				echo "</div>";
			}
			
			//Only finish, historized and archived are permitted for copy
			if ($order_status != $finish && $order_status != $archived && $order_status != $historized){
				echo "<div class='alert'>";
				echo JText::_("EASYSDI_ERROR_TRY_COPY_ORDER_WITH_UNALLOWEDSTATUS");
				echo "</div>";
			}
			
			//Do the copy
			$query = "SELECT * FROM jos_easysdi_order where order_id=".$order_id;
			$database->setQuery($query);
			$currentOrder = $database->loadObjectList();
			$currentOrder = $currentOrder[0];
			//Do not give the same name twice and limit the name to 40 characters
			$order_name="";
			$query_count_name = "select status from #__easysdi_order where user_id = ".$user->id." AND name ='".addslashes($order_name)."'";
			$database->setQuery($query_count_name);
			$order_occ = $database->loadResult();
			$l = 1;
			do{    
				//truncate the name to have max 40 characters:
				if(strlen($currentOrder->name) <= 31)
					$order_name=$currentOrder->name.JText::_("EASYSDI_TEXT_COPY").$l;
				else
					$order_name=substr($currentOrder->name,0,31).JText::_("EASYSDI_TEXT_COPY").$l;
				$query_count_name = "select status from #__easysdi_order where user_id = ".$user->id." AND name ='".addslashes($order_name)."'";
				$database->setQuery($query_count_name);
				$order_occ = $database->loadResult();
				$l++;
				if($l == 11)
					break;
			}
			while ($order_occ > 0);
			
			//insert new order
			$query = "insert into #__easysdi_order (remark, provider_id, name, type, status, third_party, user_id, buffer, order_date, surface) ";
			$query .= "values('$currentOrder->remark', $currentOrder->provider_id, '".addslashes($order_name)."', $currentOrder->type, $saved, $currentOrder->third_party, $currentOrder->user_id, $currentOrder->buffer, now(), $currentOrder->surface)";
			$database->setQuery($query);
			if (!$database->query()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}			
			$order_copy_id = mysql_insert_id();
			
			//fill in dependency tables
			$query = "SELECT * FROM #__easysdi_order_product_list where order_id=".$order_id;
			$database->setQuery($query);
			$rows = $database->loadObjectList();
			foreach ($rows as $row)
			{
				$query = "insert into #__easysdi_order_product_list (product_id, order_id, status) ";
				$query .= "values($row->product_id, $order_copy_id, $await)";
				$database->setQuery($query);
				if (!$database->query()) {
					echo "<div class='alert'>";
					echo $database->getErrorMsg();
					echo "</div>";
					exit;
				}
				$list_copy_id = mysql_insert_id();
				
				$query = "INSERT INTO #__easysdi_order_product_list_blob(order_product_list_id,data) 
								VALUES (".$list_copy_id.",null)";
				$database->setQuery($query);
				if (!$database->query()) {
					echo "<div class='alert'>";
					echo $database->getErrorMsg();
					echo "</div>";
					exit;
				}
				
				$query = "SELECT * FROM #__easysdi_order_product_properties where order_product_list_id=".$row->id;
				$database->setQuery($query);
				$rows1 = $database->loadObjectList();
				foreach ($rows1 as $row1)
				{
					$query = "insert into #__easysdi_order_product_properties (order_product_list_id, property_id, property_value, code) ";
					$query .= "values($list_copy_id, $row1->property_id, '$row1->property_value', '$row1->code')";
					$database->setQuery($query);
					if (!$database->query()) {
						echo "<div class='alert'>";
						echo $database->getErrorMsg();
						echo "</div>";
						exit;
					}
				}
			}
			
			$query = "SELECT * FROM #__easysdi_order_product_perimeters where order_id=".$order_id." order by id";
			$database->setQuery($query);
			$rows = $database->loadObjectList();
			foreach ($rows as $row)
			{
				$query = "insert into #__easysdi_order_product_perimeters (perimeter_id, order_id, value, text) ";
				$query .= "values($row->perimeter_id, $order_copy_id, '$row->value', '$row->text')";
				$database->setQuery($query);
				if (!$database->query()) {
					echo "<div class='alert'>";
					echo $database->getErrorMsg();
					echo "</div>";
					exit;
				}
			}
			
			
		}
	}
        
	function getIsoDate($dateText){
		     $database =& JFactory::getDBO();
	             $date = JRequest::getVar($dateText, "");
		     $date = $database->getEscaped(trim(strtolower($date)));
		     $array = explode(".", $date);
		     $return = $array[2]."-".$array[1]."-".$array[0];
		     if($return != "" && $return != "--")
			     return $return;
		     else
		             return "";
	}
	
	function listOrdersForProvider(){

		global  $mainframe;

		$option=JRequest::getVar("option");
		$currentPartner = -1;
		$currentProduct = -1;

		//Allows Pathway with mod_menu_easysdi
		breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_MYTREATMENT");
		
		//$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		//$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		
		$limit = JRequest::getVar('limit', 20 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		
		$database =& JFactory::getDBO();
		$user = JFactory::getUser();
		$rootPartner = new partnerByUserId($database);
		$rootPartner->load($user->id);

		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $database->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";

		//Build the query on product treatment type
		$treatmentTypeQuery = "";
		$treatmentType = JRequest::getVar("treatmentType");
		if($treatmentType == "")
			$treatmentType =1;
		if($treatmentType != "" and $treatmentType != "-1")
		{
			$treatmentTypeQuery = " AND p.treatment_type = $treatmentType ";
		}
		
		//Build the query on Order status
		$orderStatusQuery = "";
		$advFilter = "";
		
		$q = "select code from #__easysdi_order_product_status_list where code ='AWAIT'";
		$database->setQuery($q);
		$dfltStatus = $database->loadResult();
		
		$orderStatus = JRequest::getVar("orderStatus",$dfltStatus);
		
		//Get the id of product status AWAIT to get only order with product not already AVAILAIBLE to costumer
		$queryStatus = "select id from #__easysdi_order_product_status_list where code ='AWAIT'";
		$database->setQuery($queryStatus);
		$productOrderStatus = $database->loadResult();
		
		//all request
		if($orderStatus == "")
		{
			
		}
		//Only await
		else if($orderStatus == "AWAIT"){
		   $orderStatusQuery = " AND psl.code='$orderStatus' ";
		   //no adv filter
		   $advFilter = "";
		}
		//Only available
		else if($orderStatus == "AVAILABLE"){
		   $orderStatusQuery = " AND psl.code='$orderStatus' ";
		}

		//advanced filter
		if($orderStatus == "" || $orderStatus == "AVAILABLE"){
		     $received_cal_min_iso = SITE_cpanel::getIsoDate('received_cal_min');
		     $received_cal_max_iso = SITE_cpanel::getIsoDate('received_cal_max');
	             $treated_cal_min_iso = SITE_cpanel::getIsoDate('treated_cal_min');
		     $treated_cal_max_iso = SITE_cpanel::getIsoDate('treated_cal_max');
		     
                     if($received_cal_min_iso != ''){
			$advFilter .= " and o.order_send_date >= '".$received_cal_min_iso." 00:00:00'";

		     }
		     
		     if($received_cal_max_iso != ''){
			$advFilter .= " and o.order_send_date <= '".$received_cal_max_iso." 23:59:59'";

		     }
		     
		     if($treated_cal_min_iso != ''){
			$advFilter .= " and o.RESPONSE_DATE >= '".$treated_cal_min_iso." 00:00:00'";
		     }
		     
		     if($treated_cal_max_iso != ''){
			$advFilter .= " and o.RESPONSE_DATE <= '".$treated_cal_max_iso." 23:59:59'";
		     }
		     
		     //partner
		     $currentPartner = JRequest::getVar('easysdi_user_id');
                     if($currentPartner != -1)
			     $advFilter .= " and o.user_id = ".$currentPartner;
		     
		     //product
		     $currentProduct = JRequest::getVar('product_id');
                     if($currentProduct != -1)
			     $advFilter .= " and p.id = ".$currentProduct;
		     
		}
		
		$ordertype= JRequest::getVar("ordertype","");
		if ($ordertype !=""){
			$filterList[] = "(o.type ='$ordertype')";
		}

		if ( $search ) {
			$filterList[]= "(o.name LIKE '%$search%' OR o.order_id LIKE '%$search%')";
		}

		if (count($filterList) > 0)
		$filter .= implode(" AND ", $filterList);
		if (count($filterList)==1)
		$filter = " AND ".$filterList[0];
			
		$queryStatus = "select * from #__easysdi_order_product_status_list";
		$database->setQuery($queryStatus);
		$productStatusFilter = $database->loadObjectList();

		$queryType = "select * from #__easysdi_order_type_list ";
		$database->setQuery($queryType);
		$productTypeFilter = $database->loadObjectList();

		$queryTreatment = "SELECT * FROM #__easysdi_product_treatment_type";
		$database->setQuery($queryTreatment);
		$treatmentList = $database->loadObjectList();
		
		$queryStatusSaved = "select id from #__easysdi_order_status_list where code ='SAVED'";
		$database->setQuery($queryStatusSaved);
		$status_saved = $database->loadResult();
		
		// Ne montre pas dans la liste les devis dont le prix est gratuit. Ils sont automatiquement traité par le système.
		// Ni les requêtes de type brouillon
		$query = "SELECT o.order_id as order_id, 
						 p.metadata_id as metadata_id,
						 p.data_title as productName,
						 opl.id as product_list_id,
					     uClient.name as username,
					     uClient.id as client_id,
					     p.data_title as data_title,
					     o.name as name,
					     o.order_date as order_date,
					     o.order_send_date as order_send_date,
					     o.RESPONSE_SEND as RESPONSE_SEND,
					     o.RESPONSE_DATE as RESPONSE_DATE,
					     o.type as type, 
					     opl.status as status, 
					     osl.code as code, 
					     psl.translation as status_translation,
					     psl.code as status_code,
					     tl.translation as type_translation
				  FROM  #__easysdi_order o, 
				  		#__easysdi_order_product_list opl, 
						#__easysdi_order_product_status_list psl, 
				  		#__easysdi_product p, 
				  		#__easysdi_community_partner pa, 
				  		#__users u, 
				  		#__users uClient,
				  		#__easysdi_order_status_list osl, 
				  		#__easysdi_order_type_list tl 
				  WHERE o.status=osl.id 
				  and pa.user_id = u.id 
				  and o.order_id = opl.order_id 
				  and opl.product_id = p.id 
				  and psl.id = opl.status 
				  and p.diffusion_partner_id = pa.partner_id 
				  and pa.user_id =".$user->id." 
				  and o.user_id = uClient.id
				  and tl.id = o.type
				  and o.status <> $status_saved
				  $orderStatusQuery 
				  $advFilter 
				  $treatmentTypeQuery
				  AND o.order_id 
				  NOT IN (SELECT o.order_id 
				  		  FROM  #__easysdi_order o, 
				  		  		#__easysdi_order_product_list opl, 
				  		  		#__easysdi_product p,
				  		  		#__easysdi_community_partner pa, 
				  		  		#__users u, 
				  		  		#__easysdi_order_status_list osl , 
				  		  		#__easysdi_order_product_status_list psl, 
				  		  		#__easysdi_order_type_list tl 
				  		  WHERE o.type=tl.id 
				  		  AND o.status=osl.id 
				  		  AND pa.user_id = u.id 
				  		  AND o.order_id = opl.order_id 
				  		  AND opl.product_id = p.id 
				  		  AND p.partner_id = pa.partner_id 
				  		  AND pa.user_id =".$user->id." 
				  		  AND opl.status=osl.id 
				  		  AND psl.code='AWAIT' 
				  		  $orderStatusQuery 
				  		  AND o.type = tl.id 
				  		  AND tl.code ='D' 
				  		  AND p.is_free = 1) 
				  ";

		$query .= $filter;
		$query .= " order by o.order_id";
	
		$queryCount = "SELECT count(opl.product_id) 
					   FROM  #__easysdi_order o, 
					         #__easysdi_order_product_list opl,
					         #__easysdi_product p,
					         #__easysdi_community_partner pa , 
					         #__users u, 
					         #__easysdi_order_status_list osl, 
						 #__easysdi_order_product_status_list psl 
					   WHERE opl.status=psl.id 
					   AND pa.user_id = u.id 
					   AND  o.status=osl.id 
					   AND  o.order_id = opl.order_id 
					   AND  opl.product_id = p.id 
					   AND  p.diffusion_partner_id = pa.partner_id 
					   AND  pa.user_id =".$user->id." 
					   and o.status <> $status_saved
					   $orderStatusQuery  
					   $advFilter 
					   $treatmentTypeQuery
					   AND  o.order_id 
					   NOT IN (SELECT o.order_id 
					   		   FROM  #__easysdi_order o, 
					   		   		 #__easysdi_order_product_list opl,
					   		   		 #__easysdi_product p,
					   		   		 #__easysdi_community_partner pa, 
					   		   		 #__users u, 
					   		   		 #__easysdi_order_product_status_list psl , 
					   		   		 #__easysdi_order_type_list tl 
					   		   WHERE opl.status=psl.id 
					   		   AND pa.user_id = u.id 
					   		   AND o.order_id = opl.order_id 
					   		   AND opl.product_id = p.id 
					   		   AND p.partner_id = pa.partner_id 
					   		   AND pa.user_id =".$user->id." 
					   		   AND psl.code='AWAIT' 
					   		   $orderStatusQuery
					   		   AND  o.type = tl.id 
					   		   AND tl.code ='D' 
					   		   AND p.is_free = 1) 
					 ";
		
		$queryCount .= $filter;
	
		$database->setQuery($queryCount);
		$total = $database->loadResult();

		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}
                		
		$pageNav = new JPagination($total,$limitstart,$limit);
		$database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
		$rows = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}
		
		//load partner list
		$database->setQuery( "SELECT j.id as value, j.name as text FROM #__easysdi_community_partner p, #__users j where p.user_id = j.id order by j.name" );
		$partner_list = $database->loadObjectList();
	        array_unshift($partner_list, JHTML::_('select.option','-1', JText::_("EASYSDI_SHOP_ALL")));  

		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}
		$database->setQuery( "SELECT p.id as value, p.data_title as text FROM #__easysdi_product p, #__easysdi_community_partner par, #__users j where par.user_id = j.id and par.partner_id = p.diffusion_partner_id and par.user_id =".$user->id." order by p.data_title" );
		$product_list = $database->loadObjectList();
	        array_unshift($product_list, JHTML::_('select.option','-1', JText::_("EASYSDI_SHOP_ALL")));  
		
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}
		
		HTML_cpanel::listOrdersForProvider($pageNav,$rows,$option,$ordertype,$search,$orderStatus,$productOrderStatus, $productStatusFilter, $productTypeFilter, $treatmentList, $treatmentType, $partner_list, $product_list, $currentPartner, $currentProduct);

	}

	/*
	 * Statuts de la commande
	 * SENT => A traiter
	 * SAVED => Sauvée et ne dois pas être traitée par le fournisseur
	 * AWAIT => En cours de traitement chez le fournisseur
	 * PROGESS => Partiellement traitée par le fournisseur
	 * FINISH => Complètement traitée
	 * ARCHIVED => Archivée
	 * HISTORIZED => Archivée et BLOB de donn�es vid�
	 */
	function saveOrdersForProvider()
	{

		global  $mainframe;
		$database =& JFactory::getDBO();

		$products_id = JRequest::getVar("product_id");

		$order_id =  JRequest::getVar("order_id");
		foreach ($products_id as $product_id)
		{
			$remark = JRequest::getVar("remark".$product_id);
			$remark = $database->quote( $database->getEscaped($remark), false );
			$price = JRequest::getVar("price".$product_id,"0");
			if (strlen($price)!=0)
			{

				$queryStatus = "select id from #__easysdi_order_product_status_list where code ='AVAILABLE'";
				$database->setQuery($queryStatus);
				$status_id = $database->loadResult();
				
				$query = "UPDATE #__easysdi_order_product_list SET status = ".$status_id.", remark= ".$remark.",price = $price ";
				$fileName = $_FILES['file'.$product_id]["name"];
				$content = null;
			 	if (strlen($fileName)>0)
			 	{
				 	$tmpName =  $_FILES['file'.$product_id]["tmp_name"];
				 	$fp      = fopen($tmpName, 'r');
					$content = fread($fp, filesize($tmpName));
				 	$content = addslashes($content);
				 	fclose($fp);
				 	$query .= ", filename = '$fileName' ";
				 }
				 $query .= "WHERE order_id=".$order_id." AND product_id = ".$product_id;
	
				 $database->setQuery( $query );
				 if (!$database->query()) {
				 	echo "<div class='alert'>";
				 	echo JText::_($database->getErrorMsg());
				 	echo "</div>";
	
				 	break;
				 }
				 
				$query = "SELECT id FROM #__easysdi_order_product_list WHERE order_id=".$order_id." AND product_id = ".$product_id;
				$database->setQuery($query);
				$opl_id = $database->loadResult();
				
				$query = "UPDATE #__easysdi_order_product_list_blob SET data = '$content' WHERE order_product_list_id=".$opl_id;
				$database->setQuery( $query );
				if (!$database->query()) {
					echo "<div class='alert'>";
					echo JText::_($database->getErrorMsg());
					echo "</div>";
	
					break;
				}
			 //Mise à jour du statut de la commande
		/*	 $query = "SELECT COUNT(*) FROM #__easysdi_order_product_list p, #__easysdi_order_product_status_list sl WHERE p.status=sl.id and p.order_id=".$order_id." AND sl.code = 'AWAIT' ";
			 $database->setQuery($query);
			 $total = $database->loadResult();
			 jimport("joomla.utilities.date");
			 $date = new JDate();
			 if ( $total == 0){
			 	$queryStatus = "select id from #__easysdi_order_status_list where code ='FINISH'";
			 	$database->setQuery($queryStatus);
			 	$status_id = $database->loadResult();
			 }else{
			 	$queryStatus = "select id from #__easysdi_order_status_list where code ='PROGRESS'";
			 	$database->setQuery($queryStatus);
			 	$status_id = $database->loadResult();
			 }
			 $query = "UPDATE   #__easysdi_order  SET status =".$status_id." ,response_send = 1, response_date ='". $date->toMySQL()."',order_update ='". $date->toMySQL()."'  WHERE order_id=$order_id ";

			 $database->setQuery( $query );
			 if (!$database->query()) {
			 	echo "<div class='alert'>";
			 	echo JText::_($database->getErrorMsg());
			 	echo "</div>";

			 	break;
			 }

			 if ($total ==0){
			 	SITE_cpanel::notifyUserByEmail($order_id);
			 }*/
			}
		}
		 SITE_cpanel::setOrderStatus($order_id,true);
	}

	function notifyUserByEmail($order_id, $subject, $body){
		/*
		 * Envois un mail à l'utilisateur pour le prévenir que la commande est traitée.
		 */

		$database =& JFactory::getDBO();
			

		$query = "SELECT o.user_id as user_id,u.email as email,o.name as data_title, o.order_id as order_id FROM  #__easysdi_order o,#__users u WHERE order_id=$order_id and o.user_id = u.id";
		$database->setQuery($query);
		$row = $database->loadObject();

		$partner = new partnerByUserId($database);
		$partner->load($row->user_id);
		echo $partner->notify_order_ready;

		if ($partner->notify_order_ready == 1) {
			SITE_product::sendMailByEmail($row->email,JText::sprintf($subject, $row->data_title, $row->order_id),JText::sprintf($body,$row->data_title, $row->order_id));
			//SITE_product::sendMailByEmail($row->email,JText::_("EASYSDI_CMD_READY_MAIL_SUBJECT"),JText::sprintf("EASYSDI_CMD_READY_MAIL_BODY",$row->data_title));

		}
	}
	

	function processOrder(){

		global  $mainframe;
		$database =& JFactory::getDBO();
			
		$option=JRequest::getVar("option");
		//Allows Pathway with mod_menu_easysdi
		breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_PROCESS_TREATMENT",
											"EASYSDI_MENU_ITEM_MYTREATMENT",  
											"index.php?option=$option&task=listOrdersForProvider");
		
		$product_list_id=JRequest::getVar("product_list_id","0");
		if($product_list_id == 0)
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_PROCESS_ORDER_ERROR_NO_SELECTION"),'info');						
			$mainframe->redirect("index.php?option=$option&task=listOrdersForProvider" );
			exit();
		}
		$queryOrder = "SELECT * FROM #__easysdi_order_product_list WHERE id = $product_list_id";
		$database->setQuery($queryOrder);
		$result = $database->loadObject();
		$order_id = $result->order_id;
		$product_id = $result->product_id;
		
		/*$order_id=JRequest::getVar("order_id","0");
		if($order_id == 0)
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_PROCESS_ORDER_ERROR_NO_SELECTION"),'info');						
			$mainframe->redirect("index.php?option=$option&task=listOrdersForProvider" );
			exit();
		}*/
		$user = JFactory::getUser();

		//Build the query on product treatment type
		$treatmentTypeQuery = "";
		
		$database->setQuery("SELECT t.code as code, p.treatment_type as treatment_type FROM #__easysdi_product_treatment_type t, #__easysdi_product p WHERE p.treatment_type=t.id AND p.id = $product_id");
		$result = $database->loadObject();
		$treatmentType = $result->treatment_type;
		$treatmentCode = $result->code;
		$treatmentTranslation = "";
		if($treatmentType != "")
		{
			$treatmentTypeQuery = " AND p.treatment_type = $treatmentType ";
			$queryTreatment = "SELECT translation FROM #__easysdi_product_treatment_type WHERE id = $treatmentType";
			$database->setQuery($queryTreatment);
			$treatmentTranslation = $database->loadResult();
		}

		$query = "SELECT p.id as product_id, 
						 o.order_id as order_id, 
						 u.name as username,
						 p.metadata_id as metadata_id, 
						 p.data_title as data_title,
						 o.name as name,
						 o.type as type, 
						 opl.status as status,
						 otl.translation as type_translation,
						 o.order_send_date as order_send_date
				  FROM  #__easysdi_order o, 
				  		#__easysdi_order_status_list osl, 
				  		#__easysdi_order_product_list opl, 
				  		#__easysdi_order_product_status_list psl, 
				  		#__easysdi_product p,
				  		#__easysdi_community_partner pa, 
				  		#__easysdi_order_type_list otl,
				  		#__users u 
				  WHERE o.status=osl.id 
				  AND o.type = otl.id  
				  AND opl.status=psl.id 
				  AND pa.user_id = u.id 
				  AND o.order_id = opl.order_id 
				  AND opl.product_id = p.id 
				  AND p.diffusion_partner_id = pa.partner_id 
				  AND pa.user_id =".$user->id." 
				  AND psl.code='AWAIT' 
				  AND osl.code <> 'ARCHIVED' 
				  $treatmentTypeQuery
				  AND o.order_id=".$order_id;
					
		$query .= " order by o.order_id";

		$database->setQuery($query);
		$rows = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}

		$query = "SELECT * FROM  #__easysdi_order WHERE order_id=".$order_id;
		$database->setQuery($query);
		$rowOrder = $database->loadObject();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}
				
		$query = "SELECT * FROM  #__users WHERE id=".$rowOrder->user_id;
		$database->setQuery($query);
		$partner = $database->loadObject();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}


		HTML_cpanel::processOrder($rows,$option,$rowOrder,$partner,$product_id, $treatmentTranslation, $treatmentCode);

	}

	function listOrders(){

		global  $mainframe;
		
		//Allows Pathway with mod_menu_easysdi
		breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_MYORDERS");
		
		$database =& JFactory::getDBO();
		$user = JFactory::getUser();
		$rootPartner = new partnerByUserId($database);
		$rootPartner->load($user->id);

		//Check the use rights
		if(!userManager::hasRight($rootPartner->partner_id,"REQUEST_INTERNAL") &&
		!userManager::hasRight($rootPartner->partner_id,"REQUEST_EXTERNAL"))
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_ALLOWED_TO_MANAGE")." :  ".JText::_("EASYSDI_NOT_ALLOWED_TO_MANAGE_REQUEST"),"INFO");
			return;
		}

		$option=JRequest::getVar("option");
		/*$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 5 );
		 $limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		 */
		//$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		//$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		$limit = JRequest::getVar('limit', 20 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		//Automatic Archive and/or Historize of the orders
		//Get the delays in days unit
		$archive_delay = config_easysdi::getValue("ARCHIVE_DELAY", 30);
		$history_delay = config_easysdi::getValue("HISTORY_DELAY", 60);
		
		if($history_delay <= $archive_delay){
			echo "<div class='alert'>";
			echo JText::_("EASYSDI_HISTORY_ARCHIVE_DELAYS_ERROR");
			echo "</div>";
		}
		
		//Archive
		$queryStatus = "select id from #__easysdi_order_status_list where code ='ARCHIVED'";
		$database->setQuery($queryStatus);
		$status_id = $database->loadResult();
			
		$query = "update #__easysdi_order set status=".$status_id.", ORDER_UPDATE = NOW() where user_id = ".$user->id." AND DATEDIFF(NOW() ,ORDER_UPDATE) > $archive_delay AND DATEDIFF(NOW() ,ORDER_UPDATE) < $history_delay ";

		$queryStatus = "select id from #__easysdi_order_status_list where code ='FINISH'";
		$database->setQuery($queryStatus);
		$status_id = $database->loadResult();

		$query .= " AND STATUS = ".$status_id;
		$database->setQuery($query);
		if (!$database->query()) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}
		
		//History
		$queryStatus = "select id from #__easysdi_order_status_list where code ='HISTORIZED'";
		$database->setQuery($queryStatus);
		$history = $database->loadResult();

		$queryStatus = "select id from #__easysdi_order_status_list where code ='ARCHIVED'";
		$database->setQuery($queryStatus);
		$archive = $database->loadResult();

		$query = "select order_id from #__easysdi_order where user_id = ".$user->id." AND DATEDIFF(NOW() ,ORDER_UPDATE) > $history_delay AND (STATUS = ".$archive." OR STATUS = ".$status_id.")";
		$database->setQuery($query);
		$toUpdate = $database->loadResultArray();

		$query = "update #__easysdi_order set status=".$history.", ORDER_UPDATE = NOW() where user_id = ".$user->id." AND DATEDIFF(NOW() ,ORDER_UPDATE) > $history_delay AND (STATUS = ".$archive." OR STATUS = ".$status_id.")";
		
		$database->setQuery($query);

		if (!$database->query()) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}

		foreach ($toUpdate as $field)
		{
			$query = "update #__easysdi_order_product_list_blob set data=NULL where order_product_list_id IN(SELECT id FROM #__easysdi_order_product_list WHERE order_id = ".$field.")";
			$database->setQuery($query);
				
			if (!$database->query()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}
		}

		$search = $mainframe->getUserStateFromRequest( "searchOrder{$option}", 'searchOrder', '' );
		$search = $database->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";

		$queryType = "select * from #__easysdi_order_type_list ";
		$database->setQuery($queryType);
		$typeFilter = $database->loadObjectList();

		$queryStatus = "select * from #__easysdi_order_status_list";
		$database->setQuery($queryStatus);
		$statusFilter = $database->loadObjectList();
		
		$orderstatus=JRequest::getVar("orderstatus","");
		if ($orderstatus !=""){
			$filterList[]= "(o.status ='$orderstatus')";
		}


		$ordertype= JRequest::getVar("ordertype","");
		if ($ordertype !=""){
			$filterList[]= "(o.type ='$ordertype')";
		}

		if ( $search ) {
			$filterList[]= "(o.name LIKE '%$search%' OR o.order_id LIKE '%$search%')";
		}

		if (count($filterList) > 1)
		$filter .= " AND ".implode(" AND ", $filterList);
		elseif (count($filterList) == 1)
		$filter .= " AND ".$filterList[0];

		$query = "select o.*, osl.code, osl.translation as status_translation, tl.translation as type_translation from #__easysdi_order o inner join #__easysdi_order_status_list osl on o.status=osl.id inner join #__easysdi_order_type_list tl on o.type=tl.id AND  o.user_id = ".$user->id;
		$query .= $filter;
		
		if ($orderstatus ==""){
			$query .= " and o.status <> ".$archive." and o.status <> ".$history;
		}
		
		$query .= " order by o.order_date";
		
		$queryCount = "select count(*) from #__easysdi_order o where";
		if ($orderstatus ==""){
			$queryCount .= " o.status <> ".$archive." and o.status <> ".$history." AND";
		}
		$queryCount .= " o.user_id = ".$user->id;
		
		$queryCount .= $filter;

		$database->setQuery($queryCount);
		$total = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}

		$pageNav = new JPagination($total,$limitstart,$limit);

		$database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
		$rows = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}

		//Get the url for the "order" entry of the menu
		$queryURL = "SELECT id FROM #__menu WHERE link = 'index.php?option=com_easysdi_shop&view=shop' ";
		$database->setQuery($queryURL);
		$redirectURL = $database->loadResult();
		
		//$redirectURL = "index.php?option=com_easysdi_shop&view=shop&ItemId=".$ItemId;
		
		HTML_cpanel::listOrders($pageNav,$rows,$option,$orderstatus,$ordertype,$search, $statusFilter, $typeFilter,$redirectURL);

	}

	/*function orderReport($id){
		ADMIN_cpanel::orderReport($id, false, false);
	}*/
	
	function orderReport($id,$isfrontEnd, $isForProvider){
	
		global $mainframe;
		
		$isInMemory = false;
		if($id == 0){
			$isInMemory = true;
		}
		
		if($isForProvider == '')
		{
			$isForProvider == false;
		}
		
		$database =& JFactory::getDBO();
		
		//Get the current logged user
		$u = JFactory::getUser();
		$rootPartner = new partnerByUserId($database);
		$rootPartner->load($u->id);
		if($isfrontEnd == true)
		{
			//Check if a user is logged
			if ($u->guest)
			{
				$mainframe->enqueueMessage(JText::_("EASYSDI_ACCOUNT_NOT_CONNECTED"),"INFO");
				return;
			}
			if($isForProvider == false)
			{
				//Check the current user rights
				if(!userManager::hasRight($rootPartner->partner_id,"REQUEST_INTERNAL") &&
					!userManager::hasRight($rootPartner->partner_id,"REQUEST_EXTERNAL"))
				{
					$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_ALLOWED_TO_MANAGE")." :  ".JText::_("EASYSDI_NOT_ALLOWED_TO_MANAGE_REQUEST"),"INFO");
					return;
				}
			}
		}
		
		$db =& JFactory::getDBO();
		
		$rowOrder = null;
		$perimeterRows = null;
		
		if(!$isInMemory){
			//fetch order in database			
			$query = "SELECT *, sl.translation as slT, tl.translation as tlT, a.name as order_name  FROM  #__easysdi_order a ,  #__easysdi_order_status_list sl, #__easysdi_order_type_list tl where a.order_id = $id and tl.id = a.type and sl.id = a.status";
			$db->setQuery($query);
			$rowOrder = $db->loadObject();
			
			$query = "SELECT b.perimeter_id, b.text, b.value FROM  #__easysdi_order a, #__easysdi_order_product_perimeters b where a.order_id = b.order_id and a.order_id = $id order by b.id";
			$db->setQuery($query);
			$perimeterRows = $db->loadObjectList();
			
			if ($db->getErrorNum()) {
				echo "<div class='alert'>";
				echo 			$database->getErrorMsg();
				echo "</div>";
			}
		}
		else
		{
			//fetch order in memory
			$queryTranslation = "SELECT translation from #__easysdi_order_type_list where code = '".$mainframe->getUserState('order_type')."'";
			$db->setQuery($queryTranslation );
			$translation = $db->loadResult();
			$rowOrder = array (  
				'order_name' => $mainframe->getUserState('order_name'),
				'type' => $mainframe->getUserState('order_type'),
				'third_party' => $mainframe->getUserState('third_party'),
				'user_id' => $u->id,
				'order_send_date' => '',
				'buffer' => $mainframe->getUserState('bufferValue'),
				'surface' =>  $mainframe->getUserState('totalArea'),
				'tlT' => $translation,
				'slT' => "",
				'status' => ""
			);
			$rowOrder = (object) $rowOrder;  
			
			$perimeter_id = $mainframe->getUserState('perimeter_id');
			$selSurfaceListValue = $mainframe->getUserState('selectedSurfaces');
			$selSurfaceListName = $mainframe->getUserState('selectedSurfacesName');
			
			$perimeterRows = Array();
			if ($selSurfaceListName!=null){
				for ($i = 0; $i < count($selSurfaceListName); $i ++){
					$perimeterRows[] = (object)array (  
						'perimeter_id' => $perimeter_id,
						'text' => $selSurfaceListName[$i],
						'value' => $selSurfaceListValue[$i]
					);
				}
			}
		}

		//Customer name
		$user =$rowOrder->user_id;
		
		if($isfrontEnd == true && $isForProvider == false)
		{
			//Check if the current order belongs to the current logged user
			if($user != $u->id)
			{
				$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_ALLOWED_TO_ACCESS_ORDER_REPORT") ,"INFO");
				return;
			}
		}
		
		
		$queryUser = "SELECT name FROM #__users WHERE id = $user";
		$db->setQuery($queryUser );
		$user_name =  $db->loadResult();
		
		$third_name ='';
		//Third name
		$third = $rowOrder->third_party; 
		if( $third != 0)
		{
			$queryUser = "SELECT name FROM #__users WHERE id =(SELECT user_id FROM #__easysdi_community_partner where partner_id= $third)";
			$db->setQuery($queryUser );
			$third_name =  $db->loadResult();
		}
		
		//Load the products
		if(!$isInMemory){
			$query = '';
			if($isForProvider)
			{
				$query = "SELECT *, a.id as plId 
					  FROM #__easysdi_order_product_list  a, 
					  	   #__easysdi_product b 
					  WHERE a.product_id  = b.id 
					  AND order_id = $id 
					  AND b.diffusion_partner_id = $rootPartner->partner_id";
			}
			else
			{
				$query = "SELECT *, a.id as plId 
					  FROM #__easysdi_order_product_list  a, 
					       #__easysdi_product b 
					  WHERE a.product_id  = b.id 
					  AND order_id = $id";
			}
			
			$db->setQuery($query );
			$rowsProduct = $db->loadObjectList();
			if ($db->getErrorNum()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
			}
		}else{
			//Load product list in memory
			$cid = $mainframe->getUserState('productList');
			$rowsProduct = Array();
			if (count($cid)>0){
				for ($i = 0; $i < count($cid); $i ++){
					$query = "SELECT * FROM #__easysdi_product WHERE id =".$cid[$i];
					$db->setQuery($query );
					$rowsProduct[] = $db->loadObject();
				}
			}
		}
		
		if(count($rowsProduct) == 0)
		{
			//The connected user does not have any product to provide in this order
			//Do not display any information and quit with error message
			$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_ALLOWED_TO_ACCESS_ORDER_REPORT") ,"INFO");
			return;
		}
		
		HTML_cpanel::orderReportRecap($id, $isfrontEnd, $isForProvider, $rowOrder, $perimeterRows, $user_name, $third_name, $rowsProduct, $isInMemory);
	}

	/*
	function orderReportForProvider($id){
		SITE_cpanel::orderReport($id, true, true);
	}
*/
	function sendOrder(){
		global $mainframe;
		$db =& JFactory::getDBO();

		jimport("joomla.utilities.date");
		$date = new JDate();

		$order_id=JRequest::getVar("order_id",0);
			
		$queryType = "SELECT id from #__easysdi_order_product_status_list where code = 'AWAIT'";
		$db->setQuery($queryType );
		$await_type = $db->loadResult();
		$queryType = "SELECT id from #__easysdi_order_product_status_list where code = 'AVAILABLE'";
		$db->setQuery($queryType );
		$available_type = $db->loadResult();

		$queryStatus = "select id from #__easysdi_order_status_list where code ='SENT'";
		$db->setQuery($queryStatus);
		$status_id = $db->loadResult();

		$query = "UPDATE  #__easysdi_order set status = ".$status_id.", order_update ='". $date->toMySQL()."',order_send_date='". $date->toMySQL()."' WHERE order_id = ".$order_id;

		$db->setQuery($query );

		if (!$db->query()) {
			echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";
		}

			
		SITE_cpanel::notifyOrderToDiffusion($order_id);
			
		$query = "SELECT o.name as cmd_name,u.email as email , p.id as product_id, p.data_title as data_title , p.partner_id as partner_id   FROM #__users u,#__easysdi_community_partner pa, #__easysdi_order_product_list opl , #__easysdi_product p,#__easysdi_order o, #__easysdi_order_product_status_list psl, #__easysdi_order_status_list osl, #__easysdi_order_type_list tl WHERE opl.status=psl.id and o.status=osl.id and opl.order_id= $order_id AND p.id = opl.product_id and p.is_free = 1 and psl.code='AWAIT' and o.type=tl.id and tl.code='D' AND p.partner_id = pa.partner_id and pa.user_id = u.id and o.order_id=opl.order_id and osl.code='SENT' ";
			
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$db->getErrorMsg();
			echo "</div>";
		}

		$response_send = 0;
		foreach ($rows as $row){
			$response_send = 1;
			$query = "UPDATE   #__easysdi_order_product_list opl set status = ".$available_type." WHERE opl.order_id= $order_id AND opl.product_id = $row->product_id";
			$db->setQuery( $query );
			if (!$db->query()) {
				echo "<div class='alert'>";
				echo $db->getErrorMsg();
				echo "</div>";
			}
			$user = JFactory::getUser();
				
			SITE_product::sendMailByEmail($row->email,JText::_("EASYSDI_REQUEST_FREE_PRODUCT_SUBJECT"),JText::sprintf("EASYSDI_REQEUST_FREE_PROUCT_MAIL_BODY",$row->data_title,$row->cmd_name,$user->username));
		}
		SITE_cpanel::setOrderStatus($order_id,$response_send);
		
		/*
		 * Mise à jour du statut de la commande.
		 * Si il n'y a plus rien à traiter, on la marque comme terminée
		 * dans les autres cas on la marque comme en cours de traitement
		 */
	/*	$query = "SELECT COUNT(*) FROM #__easysdi_order_product_list p, #__easysdi_order_product_status_list sl WHERE p.status=sl.id and p.order_id=$order_id AND sl.code = 'AWAIT' ";
		$db->setQuery($query);
		$total = $db->loadResult();
		
		$query = "SELECT COUNT(*) FROM #__easysdi_order_product_list p, #__easysdi_order_product_status_list sl WHERE p.status=sl.id and p.order_id=$order_id  ";
		$db->setQuery($query);
		$totalProduct = $db->loadResult();
			
		jimport("joomla.utilities.date");
		$date = new JDate();
		if ( $total == 0){
			$queryStatus = "select id from #__easysdi_order_status_list where code ='FINISH'";
			$db->setQuery($queryStatus);
			$status_id = $db->loadResult();
		}else if ($total == $totalProduct){
			$queryStatus = "select id from #__easysdi_order_status_list where code ='AWAIT'";
			$db->setQuery($queryStatus);
			$status_id = $db->loadResult();
		}else
		{
			$queryStatus = "select id from #__easysdi_order_status_list where code ='PROGRESS'";
			$db->setQuery($queryStatus);
			$status_id = $db->loadResult();
		}
			
		$queryStatus = "select id from #__easysdi_order_status_list where code ='SENT'";
		$db->setQuery($queryStatus);
		$sent = $db->loadResult();
			
		$query = "UPDATE   #__easysdi_order  SET status =".$status_id." ,response_date ='". $date->toMySQL()."'  WHERE order_id=$order_id and status=".$sent;
			
		$db->setQuery($query);
		if (!$db->query()) {
			echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";
		}

		if ($total ==0){
			SITE_cpanel::notifyUserByEmail($order_id);
		}*/
	}

	//Update order status after user validation or after provider made a product available
	function setOrderStatus ($order_id, $response_send)
	{
		 /*
		 * Mise à jour du statut de la commande.
		 * Si il n'y a plus rien à traiter, on la marque comme terminée
		 * dans les autres cas on la marque comme en cours de traitement
		 */
		global $mainframe;
		$db =& JFactory::getDBO();
		jimport("joomla.utilities.date");
		$date = new JDate();
		
		//current order status
		$query = "SELECT status FROM #__easysdi_order WHERE order_id=$order_id";
		$db->setQuery($query);
		$status_id = $db->loadResult();
		
		//amount of non-treated order elements
		$query = "SELECT COUNT(*) FROM #__easysdi_order_product_list p, #__easysdi_order_product_status_list sl WHERE p.status=sl.id and p.order_id=$order_id AND sl.code = 'AWAIT' ";
		$db->setQuery($query);
		$total = $db->loadResult();
		
		//total order element
		$query = "SELECT COUNT(*) FROM #__easysdi_order_product_list p, #__easysdi_order_product_status_list sl WHERE p.status=sl.id and p.order_id=$order_id  ";
		$db->setQuery($query);
		$totalProduct = $db->loadResult();
		jimport("joomla.utilities.date");
		$date = new JDate();
		if ( $total == 0)
		{
			$queryStatus = "select id from #__easysdi_order_status_list where code ='FINISH'";
			$db->setQuery($queryStatus);
			$status_id = $db->loadResult();
		}
		else if ($total == $totalProduct)
		{
			//Do nothing, keep the current status
			
		}
		else
		{
			$queryStatus = "select id from #__easysdi_order_status_list where code ='PROGRESS'";
			$db->setQuery($queryStatus);
			$status_id = $db->loadResult();
		}

		if($response_send)
		{
			$query = "UPDATE   #__easysdi_order  SET 
							status =".$status_id." , 
							order_update ='".$date->toMySQL()."' ,
							response_date ='".$date->toMySQL()."' ,
							response_send =".$response_send."  
					WHERE order_id=$order_id ";
		}
		else
		{
			$query = "UPDATE   #__easysdi_order  SET 
							status =".$status_id." , 
							order_update ='".$date->toMySQL()."' ,
							response_date ='".$date->toMySQL()."' 
					WHERE order_id=$order_id ";
		}
			
		//Update new status
		$db->setQuery($query);
		if (!$db->query()) {
			echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";
		}
		
		$db->setQuery("SELECT o.name as order_name, u.id as user_id, u.email as email FROM #__easysdi_order o, #__users u where o.user_id = u.id and order_id=".$order_id);
		$results = $db->loadObjectList();
		$order_name = $results[0]->order_name;
		$email = $results[0]->email;
		$usr_id = $results[0]->user_id;
		
		//verify the notification is active.
		$queryNot = "SELECT p.notify_order_ready FROM #__easysdi_community_partner p, #__users u WHERE u.id = p.user_id and p.user_id = $usr_id";
		$db->setQuery($queryNot);
		$not = $db->loadResult();
		if ($total ==0 && $not == 1)
		{
			SITE_product::sendMailByEmail($email,JText::sprintf("EASYSDI_CMD_READY_MAIL_SUBJECT", $order_name, $order_id),JText::sprintf("EASYSDI_CMD_READY_MAIL_BODY",$order_name,$order_id));
		}
		else if ($total == $totalProduct -1 && $not == 1)
		{
			SITE_product::sendMailByEmail($email,JText::sprintf("EASYSDI_CMD_READY_MAIL_SUBJECT", $order_name, $order_id),JText::sprintf("EASYSDI_CMD_READY_MAIL_BODY",$order_name,$order_id));
		}
	}
	
	//Send a command notification to the specified email in the product definition
	//Only if the product treatment is manual
	function notifyOrderToDiffusion($order_id)
	{
		$db =& JFactory::getDBO();

		$queryOrderName = "SELECT name FROM #__easysdi_order WHERE order_id = $order_id";
		$db->setQuery($queryOrderName);
		$order_name = $db->loadResult();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";
		}
		
		$queryOrderType = "SELECT l.code FROM #__easysdi_order o, #__easysdi_order_type_list l WHERE o.type = l.id and order_id = $order_id";
		$db->setQuery($queryOrderType);
		$order_type = $db->loadResult();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";
		}
		
		$orderQuery = "SELECT distinct l.product_id FROM #__easysdi_product p, #__easysdi_order_product_list l WHERE l.product_id = p.id AND order_id = $order_id";
		if($order_type == "D")
			//Filter out the products that are free if it is a devis, they should not be notificated
			$orderQuery = "SELECT distinct l.product_id FROM #__easysdi_product p, #__easysdi_order_product_list l WHERE l.product_id = p.id AND p.is_free=0 AND order_id = $order_id";
			
		$db->setQuery($orderQuery);
		$cid = $db->loadResultArray();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";
		}
		

		$productList = "";
		foreach ($cid as $product_id )
		{
			$productList = $productList.$product_id.",";
		}
		$productList = substr ($productList,0,strlen($productList)-1);

		$queryNotitification = "SELECT DISTINCT diffusion_partner_id ,notification_email FROM #__easysdi_product WHERE id IN ($productList)AND treatment_type = (SELECT id from #__easysdi_product_treatment_type WHERE code = 'MANU')";
		$db->setQuery($queryNotitification);
		$results = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";
		}

		foreach ($results as $result)
		{
			$list = array();
			$diffusionEmail = "";
			if($result->diffusion_partner_id)
			{
				$queryDiffusionPartnerEmail = "SELECT email FROM #__users WHERE id = (SELECT user_id from #__easysdi_community_partner WHERE partner_id = $result->diffusion_partner_id)";
				$db->setQuery($queryDiffusionPartnerEmail);
				$diffusionEmail = $db->loadResult();
			}
			SITE_cpanel::getEmailNotificationList($result->notification_email, $list);
			SITE_cpanel::sendEmailToNotificationList($diffusionEmail,$list, $order_id, $order_name);
		}
	}

	function getEmailNotificationList($emails, &$emailArray)
	{
		if($emails)
		{
			$index = strpos($emails,',');
			if($index)
			{
				$emailArray[] = substr ($emails,0,$index);
				$em = substr($emails,$index + 1);
				SITE_cpanel::getEmailNotificationList($em, &$emailArray);
			}
			else
			{
				$emailArray[] = $emails;
			}
		}
	}

	function sendEmailToNotificationList($diffusionEmail, $emailList, $order_id,$order_name)
	{
		$mailer =& JFactory::getMailer();
		$mailer->AddRecipient($diffusionEmail);
		foreach ($emailList as $email)
		{
			$mailer->AddAddress($email);
		}
		$mailer->setSubject(JText::_("EASYSDI_ORDER_NOTIFICATION_SUBJECT"));
		$mailer->setBody(JText::sprintf("EASYSDI_ORDER_NOTIFICATION_BODY",$order_id,$order_name));
		if ($mailer->send() !==true)
		{
			//
		}
	}
	
	function showSummaryForPartner(){
		$summaryForId = JRequest::getVar('SummaryForId');
		$print = JRequest::getVar('print');
		$toolbar = JRequest::getVar('toolbar');
		HTML_cpanel_partner::showSummaryForPartner($summaryForId, $print, $toolbar);
	}
}
?>
