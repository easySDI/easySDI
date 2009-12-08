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

		$query = "select count(*) from #__easysdi_order where order_id = $order_id AND user_id = $user->id";
		$database->setQuery($query);
		$total = $database->loadResult();
		if ($total == 0) die;

		$query = "SELECT data,filename FROM #__easysdi_order_product_list where product_id = $product_id AND order_id = $order_id";
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

	function listOrdersForProvider(){

		global  $mainframe;

		$option=JRequest::getVar("option");
		

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
		$orderStatus = JRequest::getVar("orderStatus","");
		if($orderStatus != "")
		{
			$orderStatusQuery = " AND o.status='$orderStatus' ";
		}
		else
		{
			//All except SAVED, FINISH, ARCHIVED and HISTORIZED
			$queryOrderStatus = "select id from #__easysdi_order_status_list where code IN ('SAVED','FINISH','ARCHIVED','HISTORIZED')";
			$database->setQuery($queryOrderStatus);
			$orderStatusList = $database->loadObjectList();
			$orderStatusQuery = " AND o.status NOT IN (";
			foreach ($orderStatusList as $status)
			{
				$orderStatusQuery .= " '".$status->id."' ,";
			}
			$orderStatusQuery = substr ($orderStatusQuery,0,-1);
			$orderStatusQuery .= ")";
		}
		
		//Get the id of product status AWAIT to get only order with product not already AVAILAIBLE to costumer
		$queryStatus = "select id from #__easysdi_order_product_status_list where code ='AWAIT'";
		$database->setQuery($queryStatus);
		$productOrderStatus = $database->loadResult();
		
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
			
		$queryStatus = "select * from #__easysdi_order_status_list where code NOT IN ('SAVED','FINISH','ARCHIVED','HISTORIZED')";
		$database->setQuery($queryStatus);
		$productStatusFilter = $database->loadObjectList();

		$queryType = "select * from #__easysdi_order_type_list ";
		$database->setQuery($queryType);
		$productTypeFilter = $database->loadObjectList();

		$queryTreatment = "SELECT * FROM #__easysdi_product_treatment_type";
		$database->setQuery($queryTreatment);
		$treatmentList = $database->loadObjectList();
		
		
		// Ne montre pas dans la liste les devis dont le prix est gratuit. Ils sont automatiquement traité par le système.
		$query = "SELECT o.order_id as order_id, 
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
					     o.status as status, 
					     osl.code as code, 
					     osl.translation as status_translation, 
					     tl.translation as type_translation
				  FROM  #__easysdi_order o, 
				  		#__easysdi_order_product_list opl, 
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
				  and p.diffusion_partner_id = pa.partner_id 
				  and pa.user_id =".$user->id." 
				  and o.user_id = uClient.id
				  and tl.id = o.type
				  and opl.status = $productOrderStatus 
				  $orderStatusQuery 
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
					         #__easysdi_order_status_list osl 
					   WHERE pa.user_id = u.id 
					   AND  o.status=osl.id 
					   AND  o.order_id = opl.order_id 
					   AND  opl.product_id = p.id 
					   AND  p.diffusion_partner_id = pa.partner_id 
					   AND  pa.user_id =".$user->id." 
		 			   and opl.status = $productOrderStatus 
					   $orderStatusQuery  
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
		HTML_cpanel::listOrdersForProvider($pageNav,$rows,$option,$ordertype,$search,$orderStatus,$productOrderStatus, $productStatusFilter, $productTypeFilter, $treatmentList, $treatmentType);

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
			$price = JRequest::getVar("price".$product_id,"0");
			if (strlen($price)!=0)
			{

				$queryStatus = "select id from #__easysdi_order_product_status_list where code ='AVAILABLE'";
				$database->setQuery($queryStatus);
				$status_id = $database->loadResult();

				$query = "UPDATE #__easysdi_order_product_list SET status = ".$status_id.", remark= ".$remark.",price = $price ";
					
				 $fileName = $_FILES['file'.$product_id]["name"];
			 	if (strlen($fileName)>0)
			 	{
				 	$tmpName =  $_FILES['file'.$product_id]["tmp_name"];
	
				 	$fp      = fopen($tmpName, 'r');
				 	$content = fread($fp, filesize($tmpName));
				 	$content = addslashes($content);
				 	fclose($fp);
				 	$query .= ", filename = '$fileName' , data = '$content' ";
				 }
				 $query .= "WHERE order_id=".$order_id." AND product_id = ".$product_id;
	
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
		 SITE_cpanel::setOrderStatus($order_id,1);
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
						 otl.translation as type_translation 
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

		$query = "SELECT * FROM  #__users WHERE id=".$user->id;
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
		$archive_delay = config_easysdi::getValue("ARCHIVE_DELAY");
		$history_delay = config_easysdi::getValue("HISTORY_DELAY") - $archive_delay;

		if ($archive_delay == null) $archive_delay=30;
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

		if ($history_delay == null) $history_delay=60- $archive_delay;
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
			$query = "update #__easysdi_order_product_list set data=NULL where order_id = ".$field;
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
		$query = "SELECT *, sl.translation as slT, tl.translation as tlT, a.name as order_name  FROM  #__easysdi_order a ,  #__easysdi_order_product_perimeters b, #__easysdi_order_status_list sl,#__easysdi_order_type_list tl where a.order_id = b.order_id and a.order_id = $id and tl.id = a.type and sl.id = a.status";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}

		//Customer name
		$user =$rows[0]->user_id;
		
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
		$third = $rows[0]->third_party; 
		if( $third != 0)
		{
			$queryUser = "SELECT name FROM #__users WHERE id =(SELECT user_id FROM #__easysdi_community_partner where partner_id= $third)";
			$db->setQuery($queryUser );
			$third_name =  $db->loadResult();
		}
		
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
			echo 			$database->getErrorMsg();
			echo "</div>";
		}
		if(count($rowsProduct) == 0)
		{
			//The connected user does not have any product to provide in this order
			//Do not display any information and quit with error message
			$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_ALLOWED_TO_ACCESS_ORDER_REPORT") ,"INFO");
			return;
		}
		
	
		HTML_cpanel::orderReportRecap($id,$isfrontEnd, $isForProvider, $rows, $user_name, $third_name,$rowsProduct);
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
		
		$query = "SELECT status FROM #__easysdi_order WHERE order_id=$order_id";
		$db->setQuery($query);
		$status_id = $db->loadResult();
		
		$query = "SELECT COUNT(*) FROM #__easysdi_order_product_list p, #__easysdi_order_product_status_list sl WHERE p.status=sl.id and p.order_id=$order_id AND sl.code = 'AWAIT' ";
		$db->setQuery($query);
		$total = $db->loadResult();
		
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
			
		$db->setQuery($query);
		if (!$db->query()) {
			echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";
		}
		
		$db->setQuery("SELECT o.name as order_name, u.email as email FROM #__easysdi_order o, #__users u where o.user_id = u.id and order_id=".$order_id);
		$results = $db->loadObjectList();
		$order_name = $results[0]->order_name;
		$email = $results[0]->email;
		
		if ($total ==0)
		{
			SITE_product::sendMailByEmail($email,JText::sprintf("EASYSDI_CMD_READY_MAIL_SUBJECT", $order_name, $order_id),JText::sprintf("EASYSDI_CMD_READY_MAIL_BODY",$order_name,$order_id));
		}
		else if ($total == $totalProduct -1)
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

		$orderQuery = "SELECT distinct product_id FROM #__easysdi_order_product_list WHERE order_id = $order_id";
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
