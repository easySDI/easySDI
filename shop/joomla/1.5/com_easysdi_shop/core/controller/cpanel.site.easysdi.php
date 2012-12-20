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

	function downloadProduct($order_id){

		$database =& JFactory::getDBO();
		$user = JFactory::getUser();
		$product_id = JRequest::getVar('product_id');
		
		//retrieve granted user to download product: owner of the order and furnisher
		$query = "select user_id from #__sdi_order where id = ".$order_id;
		$database->setQuery($query);
		$orderOwner = $database->loadResult();
		
		$query = "SELECT u.id FROM #__users u, 
								   #__sdi_product p
								   INNER JOIN #__sdi_objectversion v ON v.id = p.objectversion:id
								   INNER JOIN #__sdi_object o ON o.id = v.object_id, 
								   #__sdi_account a 
							  WHERE u.id=a.user_id 
							  AND a.id=o.account_id 
							  AND p.id=".$product_id;
		$database->setQuery($query);
		$productFurnisher = $database->loadResult();
		
		//restrict acces to order's owner and product's diffuser
		if($user->id != $orderOwner && $user->id != $productFurnisher)
			die();

		$query = "SELECT opf.data,opf.filename 
					FROM #__sdi_order_product op 
					INNER JOIN #__sdi_orderproduct_file opf on opf.orderproduct_id=op.id
				 	WHERE op.product_id = $product_id AND op.order_id = $order_id";
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
	
	function archiveOrder($order_id){
		global  $mainframe;
		$option=JRequest::getVar("option");
		
		if ($order_id == 0)
		{
			echo "<div class='alert'>";
			echo JText::_("SHOP_ERROR_NO_ORDER_ID");
			echo "</div>";
		}
		else 
		{
			$database =& JFactory::getDBO();
			$user = JFactory::getUser();
				
			$status_id = sdilist::getIdByCode('#__sdi_list_orderstatus','ARCHIVED' );
			
			$order = new order($database);
			$order->load($order_id);
			if (!$order->setStatus($status_id))
			{
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
		$account_id=JRequest::getVar("id",0);
		if ($account_id == 0)
		{
			echo "<div class='alert'>";
			echo JText::_("SHOP_ORDER_MESSAGE_NO_PRODUCT_ID");
			echo "</div>";
		}
		else 
		{
			$database =& JFactory::getDBO();				
			$catalogUrlBase = config_easysdi::getValue("catalog_url");
			
			$query = "SELECT p.* FROM #__sdi_product p 
							INNER JOIN #__sdi_objectversion v ON v.id = p.objectversion_id
							INNER JOIN #__sdi_object o ON o.id = v.object_id
					 		WHERE o.account_id =".$account_id;
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
	
	function orderDraft ($order_id)
	{
		global $mainframe;
		$database =& JFactory::getDBO();
		$option = JRequest::getVar('option');
		$devis_to_order = JRequest::getVar('devis_to_order',0);
		//Order
		$order = new order ($database);
		$order->load($order_id);
		
		$mainframe->setUserState('order_name',$order->name);
		$mainframe->setUserState('third_party',$order->thirdparty_id);
		$mainframe->setUserState('bufferValue',$order->buffer);
		$mainframe->setUserState('totalArea',$order->surface);
		
		//Order ID
		$mainframe->setUserState('order_id',$order->id);
				
		//Order type
		$queryType = "SELECT * FROM #__sdi_list_ordertype WHERE id=$order->type_id";
		$database->setQuery($queryType);
		$type = $database->loadObject();
		
		if($devis_to_order == 1)
			$mainframe->setUserState('order_type','O');
		else
			$mainframe->setUserState('order_type',$type->code );
		
		//Products
		$queryProducts = "SELECT * FROM #__sdi_order_product WHERE order_id=$order_id";
		$database->setQuery($queryProducts);
		$productList = $database->loadObjectList();
		$productArray = array ();
		foreach($productList as $product)
		{
			$productArray[]=$product->product_id;
		}
		$mainframe->setUserState('productList',$productArray);
		
		//Selected surfaces
		$queryPerimeters = "SELECT * FROM #__sdi_order_perimeter WHERE order_id=$order_id ORDER BY id";
		$database->setQuery($queryPerimeters);
		$perimeterList = $database->loadObjectList();
		$selectedSurfaces = array ();
		$selectedSurfacesName = array();
		foreach ($perimeterList as $perimeter)
		{
			$selectedSurfaces[]=$perimeter->value;
			$selectedSurfacesName[]=$perimeter->text;	
		}
		$mainframe->setUserState('selectedSurfaces',$selectedSurfaces);
		$mainframe->setUserState('selectedSurfacesName',$selectedSurfacesName);
		$mainframe->setUserState('perimeter_id',$perimeterList[0]->perimeter_id);
		//Properties
		$queryProducts = "SELECT * FROM #__sdi_order_product WHERE order_id=$order_id";
		$database->setQuery($queryProducts);
		$productsList = $database->loadObjectList();
		foreach($productsList as $productItem)
		{
			$queryPropertyCode = "SELECT * FROM #__sdi_order_property WHERE orderproduct_id = $productItem->id";
			$database->setQuery($queryPropertyCode);
			$orderProperties = $database->loadObjectList();
			$mlistArray = array();
			$cboxArray = array();
			foreach($orderProperties as $orderProperty)
			{
				$queryPropertyDefintion = "SELECT * FROM #__sdi_property WHERE id='$orderProperty->property_id'";
				$database->setQuery($queryPropertyDefintion);
				$propertyDefinition = $database->loadObject();
				switch($propertyDefinition->type)
				{
					case "message":
						$mainframe->setUserState($orderProperty->property_id."_text_property_".$productItem->product_id,$orderProperty->propertyvalue_id);
						break;
					case "list":
						$a = array();
						$a[] = $orderProperty->propertyvalue_id;
						$mainframe->setUserState($orderProperty->property_id."_list_property_".$productItem->product_id,$a);
						break;
					case "text":
						$mainframe->setUserState($orderProperty->property_id."_text_property_".$productItem->product_id,$orderProperty->propertyvalue);
						break;
					case "textarea":
						$a = array();
						$a[] = $orderProperty->propertyvalue;
						$mainframe->setUserState($orderProperty->property_id."_textarea_property_".$productItem->product_id,$a);
						break;
					case "cbox":
						$cboxArray[] = $orderProperty->propertyvalue_id;
						$mainframe->setUserState($orderProperty->property_id."_cbox_property_".$productItem->product_id,$cboxArray);
						break;
					case "mlist":
						$mlistArray[] = $orderProperty->propertyvalue_id;
						$mainframe->setUserState($orderProperty->property_id."_mlist_property_".$productItem->product_id,$mlistArray);
						break;
				}
			}
		
		}
		//Get the url for the "order" entry of the menu
		$queryURL = "SELECT id FROM #__menu WHERE link = 'index.php?option=com_easysdi_shop&view=shop' ";
		$database->setQuery($queryURL);
		$redirectURL = $database->loadResult();
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&view=shop&Itemid=$redirectURL&step=5"), false));
	}
	
	function suppressOrder(){
		global  $mainframe;
		$option=JRequest::getVar("option");
		$order_id=JRequest::getVar("order_id",0);
		if ($order_id == 0)
		{
			echo "<div class='alert'>";
			echo JText::_("SHOP_ERROR_NO_ORDER_ID");
			echo "</div>";
		}
		else 
		{
			$database =& JFactory::getDBO();
			$user = JFactory::getUser();

			$status_id = sdilist::getIdByCode('#__sdi_list_orderstatus','SAVED' );
			
			$query_order_status = "select status_id from #__sdi_order where user_id = ".$user->id." AND id =".$order_id;
			$database->setQuery($query_order_status);
			$order_status = $database->loadResult();
			
			//User must own this order to delete it
			if ($order_status == ""){
				echo "<div class='alert'>";
				echo JText::_("SHOP_ORDER_MESSAGE_NO_SUCH_ORDER_FOR_USER");
				echo "</div>";
				return;
			}
			
			//Only draft are permitted for deletion
			if ($order_status <> $status_id){
				echo "<div class='alert'>";
				echo JText::_("SHOP_ORDER_MESSAGE_TRY_DELETE_ORDER_OTHER_THAN_DRAFT");
				echo "</div>";
				return;
			}
			
			$order = new order ($database);
			$order->load($order_id);
			
			if (!$order->delete())
			{
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}
		}
	}
	
	function copyOrder($order_id){
		global  $mainframe;
		$option=JRequest::getVar("option");
		$order_copy_id="";
		if ($order_id == 0)
		{
			echo "<div class='alert'>";
			echo JText::_("SHOP_ERROR_NO_ORDER_ID");
			echo "</div>";
		}
		else 
		{
			$database =& JFactory::getDBO();
			$user = JFactory::getUser();

			$await = sdilist::getIdByCode('#__sdi_list_productstatus','AWAIT' );
			$saved = sdilist::getIdByCode('#__sdi_list_orderstatus','SAVED' );
			$finish = sdilist::getIdByCode('#__sdi_list_orderstatus','FINISH' );
			$archived = sdilist::getIdByCode('#__sdi_list_orderstatus','ARCHIVED' );
			$historized = sdilist::getIdByCode('#__sdi_list_orderstatus','HISTORIZED' );
			
			$query_order_status = "select status_id from #__sdi_order where user_id = ".$user->id." AND id =".$order_id;
			$database->setQuery($query_order_status);
			$order_status = $database->loadResult();
			
			if ($order_status == ""){
				echo "<div class='alert'>";
				echo JText::_("SHOP_ORDER_MESSAGE_NO_SUCH_ORDER_FOR_USER");
				echo "</div>";
			}
			
			//Only finish, historized and archived are permitted for copy
			if ($order_status != $finish && $order_status != $archived && $order_status != $historized){
				echo "<div class='alert'>";
				echo JText::_("SHOP_ORDER_MESSAGE_TRY_COPY_ORDER_WITH_UNALLOWEDSTATUS");
				echo "</div>";
			}
			
			//Do the copy
			$currentOrder = new order ($database);
			$currentOrder->load($order_id);
			//Do not give the same name twice and limit the name to 40 characters
			$order_name="";
			$query_count_name = "select status_id from #__sdi_order where user_id = ".$user->id." AND name ='".addslashes($order_name)."'";
			$database->setQuery($query_count_name);
			$order_occ = $database->loadResult();
			$l = 1;
			do
			{    
				//truncate the name to have max 40 characters:
				if(strlen($currentOrder->name) <= 31)
					$order_name=$currentOrder->name.JText::_("SHOP_ORDER_COPY").$l;
				else
					$order_name=substr($currentOrder->name,0,31).JText::_("SHOP_ORDER_COPY").$l;
				$query_count_name = "select status_id from #__sdi_order where user_id = ".$user->id." AND name ='".addslashes($order_name)."'";
				$database->setQuery($query_count_name);
				$order_occ = $database->loadResult();
				$l++;
				if($l == 11)
					break;
			}
			while ($order_occ > 0);
			
			//insert new order
			$currentOrder->id = 0;
			$currentOrder->guid = 0;
			$currentOrder->name = addslashes($order_name);
			$currentOrder->status_id = $saved;
			$currentOrder->created = date('Y-m-d H:i:s');
			$currentOrder->response=NULL;
			$currentOrder->responsesent=0;
			$currentOrder->sent=NULL;
			if(!$currentOrder->store())
			{
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}
			
			$order_copy_id = $database->insertid();
			
			//fill in dependency tables
			$query = "SELECT * FROM #__sdi_order_product where order_id=".$order_id;
			$database->setQuery($query);
			$rows = $database->loadObjectList();
			foreach ($rows as $row)
			{
				$orderProduct = new orderProduct($database);
				$orderProduct->product_id=$row->product_id;
				$orderProduct->order_id=$order_copy_id;
				$orderProduct->status_id=$await;
				if(!$orderProduct->store())
				{
					echo "<div class='alert'>";
					echo $database->getErrorMsg();
					echo "</div>";
					exit;
				}
				$list_copy_id = $database->insertid();
				
				$query = "SELECT * FROM #__sdi_order_property where orderproduct_id=".$row->id;
				$database->setQuery($query);
				$rows1 = $database->loadObjectList();
				foreach ($rows1 as $row1)
				{
					$orderProductProperty = new orderProductProperty($database);
					$orderProductProperty->orderproduct_id=$list_copy_id;
					$orderProductProperty->property_id=$row1->property_id;
					$orderProductProperty->propertyvalue=$row1->propertyvalue;
					$orderProductProperty->propertyvalue_id=$row1->propertyvalue_id;
					$orderProductProperty->code=$row1->code;
					if(!$orderProductProperty->store())
					{
						echo "<div class='alert'>";
						echo $database->getErrorMsg();
						echo "</div>";
						exit;
					}
				}
			}
			
			$query = "SELECT * FROM #__sdi_order_perimeter where order_id=".$order_id." order by id";
			$database->setQuery($query);
			$rows = $database->loadObjectList();
			foreach ($rows as $row)
			{
				$orderPerimeter = new orderPerimeter($database);
				$orderPerimeter->perimeter_id=$row->perimeter_id;
				$orderPerimeter->order_id=$order_copy_id;
				$orderPerimeter->value=$row->value;
				$orderPerimeter->text=addslashes($row->text);
				if(!$orderPerimeter->store())
				{
					echo "<div class='alert'>";
					echo $database->getErrorMsg();
					echo "</div>";
					exit;
				}
			}
		}		
	}

	function listOrdersForProvider(){
		global  $mainframe;
		$option=JRequest::getVar("option");
			
		$limit = JRequest::getVar('limit', 20 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		
		$database =& JFactory::getDBO();
		$user = JFactory::getUser();
		$account = new accountByUserId($database);
		$account->load($user->id);

		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
// 		$search = $database->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";

		//Build the query on product treatment type
		$treatmentTypeQuery = "";
		$treatmentType = JRequest::getVar("treatmentType");
		if($treatmentType == "")
			$treatmentType =1;
		if($treatmentType != "" and $treatmentType != "-1")
		{
			$treatmentTypeQuery = " AND p.treatmenttype_id = $treatmentType ";
		}
		
		//Build the query on Order status
		$orderStatusQuery = "";
		
		$dfltStatus = sdilist::getIdByCode('#__sdi_list_productstatus','AWAIT' );
		
		$orderStatus = JRequest::getVar("orderStatus",$dfltStatus);
		if($orderStatus != "")
		{
			$orderStatusQuery = " AND psl.id='$orderStatus' ";
		}
		else
		{
			//All except SAVED, FINISH, ARCHIVED and HISTORIZED
			$queryOrderStatus = "select id from #__sdi_list_productstatus;";
			$database->setQuery($queryOrderStatus);
			$orderStatusList = $database->loadObjectList();
		}
			
		$productOrderStatus = sdilist::getIdByCode('#__sdi_list_productstatus','AWAIT' );
		
		//convenience var to list all request
		$allorders=JRequest::getVar("allorders", 0);
		if($allorders == 1){
			$productOrderStatus=2;
			$orderStatusQuery = " ";
		}
		
		$ordertype= JRequest::getVar("ordertype","");
		if ($ordertype !=""){
			$filterList[] = "(o.type_id ='$ordertype')";
		}

		if ( $search )
		{
			if(strripos ($search,'"') != FALSE)
			{
				$searchcontent = substr($search, 1,strlen($search)-2 );
				$searchcontent = $database->getEscaped( trim( strtolower( $searchcontent ) ) );
				$filterList[]= " (o.name LIKE '%".$searchcontent."%' OR o.id LIKE '%".$searchcontent."%')";
			}
			else
			{
				$search = $database->getEscaped( trim( strtolower( $search ) ) );
				$filterList[]= " (o.name = '$search' OR o.id = '$search')";
			}
		}

		if (count($filterList) > 0)
		$filter .= implode(" AND ", $filterList);
		if (count($filterList)==1)
		$filter = " AND ".$filterList[0];
			
		$queryStatus = "select * from #__sdi_list_productstatus";
		$database->setQuery($queryStatus);
		$productStatusFilter = $database->loadObjectList();

		$queryType = "select * from #__sdi_list_ordertype ";
		$database->setQuery($queryType);
		$productTypeFilter = $database->loadObjectList();

		$queryTreatment = "SELECT * FROM #__sdi_list_treatmenttype ";
		$database->setQuery($queryTreatment);
		$treatmentList = $database->loadObjectList();
		
		$status_saved = sdilist::getIdByCode('#__sdi_list_orderstatus','SAVED' );
		
		// Ne montre pas dans la liste les devis dont le prix est gratuit. Ils sont automatiquement traité par le système.
		// Ni les requêtes de type brouillon
		$query = "SELECT o.id as order_id, 
		                                 o.thirdparty_id as third_party,
						 v.metadata_id as metadata_id,
						 m.guid as metadata_guid,
						 p.name as productName,
						 opl.id as product_list_id,
					     uClient.name as username,
					     uClient.id as client_id,
					     p.name as data_title,
					     o.name as name,
					     o.created as order_date,
					     o.sent as order_send_date,
					     o.responsesent as RESPONSE_SEND,
					     o.sent as RESPONSE_DATE,
					     o.type_id as type, 
					     opl.status_id as status, 
					     osl.code as code, 
					     psl.label as status_translation, 
					     tl.label as type_translation
				  FROM  #__sdi_order o, 
				  		#__sdi_order_product opl, 
						#__sdi_list_productstatus psl,
				  		#__sdi_product p,
				  		#__sdi_objectversion v ,
				  		#__sdi_metadata m,
				  		#__sdi_account a, 
				  		#__users u, 
				  		#__users uClient,
				  		#__sdi_list_orderstatus osl, 
				  		#__sdi_list_ordertype tl 
				  WHERE o.status_id=osl.id 
				  and a.user_id = u.id 
				  and o.id = opl.order_id 
				  and opl.product_id = p.id 
				  and psl.id = opl.status_id 
				  and p.diffusion_id = a.id 
				  and a.user_id =".$user->id." 
				  and o.user_id = uClient.id
				  and tl.id = o.type_id
				  AND v.id = p.objectversion_id
				  AND m.id = v.metadata_id
				  and o.status_id <> $status_saved
				  and opl.status_id = $productOrderStatus 
				  $orderStatusQuery 
				  $treatmentTypeQuery
				  AND o.id 
				  NOT IN (SELECT o.id 
				  		  FROM  #__sdi_order o, 
				  		  		#__sdi_order_product opl, 
				  		  		#__sdi_product p,
				  		  		#__sdi_list_orderstatus osl , 
				  		  		#__sdi_list_ordertype tl 
				  		  WHERE o.type_id=tl.id 
				  		  AND o.id = opl.order_id 
				  		  AND opl.product_id = p.id 
				  		  AND o.status_id = osl.id
				  		  AND tl.code ='D' 
				  		  AND p.free = 1) 
				  ";

		$query .= $filter;
		$query .= " order by o.id";

		$queryCount ="SELECT COUNT(o.id)
				  FROM  #__sdi_order o, 
				  		#__sdi_order_product opl, 
						#__sdi_list_productstatus psl,
				  		#__sdi_product p,
				  		#__sdi_objectversion v ,
				  		#__sdi_account a, 
				  		#__users u, 
				  		#__users uClient,
				  		#__sdi_list_orderstatus osl, 
				  		#__sdi_list_ordertype tl 
				  WHERE o.status_id=osl.id 
				  and a.user_id = u.id 
				  and o.id = opl.order_id 
				  and opl.product_id = p.id 
				  and psl.id = opl.status_id 
				  and p.diffusion_id = a.id 
				  and a.user_id =".$user->id." 
				  and o.user_id = uClient.id
				  and tl.id = o.type_id
				  AND v.id = p.objectversion_id
				  and o.status_id <> $status_saved
				  and opl.status_id = $productOrderStatus 
				  $orderStatusQuery 
				  $treatmentTypeQuery
				  AND o.id 
				  NOT IN (SELECT o.id 
				  		  FROM  #__sdi_order o, 
				  		  		#__sdi_order_product opl, 
				  		  		#__sdi_product p,
				  		  		#__sdi_list_orderstatus osl , 
				  		  		#__sdi_list_ordertype tl 
				  		  WHERE o.type_id=tl.id 
				  		  AND o.id = opl.order_id 
				  		  AND opl.product_id = p.id 
				  		  AND o.status_id = osl.id
				  		  AND tl.code ='D' 
				  		  AND p.free = 1) 
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
	
	function saveOrdersForProvider($order_id)
	{
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
		global  $mainframe;
		$database =& JFactory::getDBO();
		$products_id = JRequest::getVar("product_id");
		$isOneElemTreated = false;
		foreach ($products_id as $product_id)
		{
			$remark = JRequest::getVar("remark".$product_id);
			$remark = $database->quote( $database->getEscaped($remark), false );
			$price = JRequest::getVar("price".$product_id,"0");
			$fileName = addslashes($_FILES['file'.$product_id]["name"]);
			if ((strlen($remark)!=0 || strlen($fileName)!=0) && strlen($price)!=0)
			{
				$isOneElemTreated = true;
				$status_id = sdilist::getIdByCode('#__sdi_list_productstatus','AVAILABLE' );
				
				$query = "SELECT id FROM #__sdi_order_product WHERE order_id=".$order_id." AND product_id = ".$product_id;
				$database->setQuery( $query );
				$orderproduct_id =  $database->loadResult();
				
				$orderProduct = new orderProduct($database);
				$orderProduct->load($orderproduct_id);
				$orderProduct->status_id=$status_id;
				$orderProduct->remark = $remark;
				$orderProduct->price=$price;
				if (!$orderProduct->store())
				{
					echo "<div class='alert'>";
				 	echo JText::_($database->getErrorMsg());
				 	echo "</div>";
				 	break;
				}
				
			 	if (strlen($fileName)>0)
			 	{
				 	$tmpName =  $_FILES['file'.$product_id]["tmp_name"];
				 	$fp      = fopen($tmpName, 'r');
				 	$content = fread($fp, filesize($tmpName));
				 	$content = addslashes($content);
				 	fclose($fp);
					if (!$orderProduct->setFile($fileName,$content))
					{
						echo "<div class='alert'>";
					 	echo JText::_($database->getErrorMsg());
					 	echo "</div>";
					 	break;
					}
				 }
			}
		}
		if($isOneElemTreated)
		    SITE_cpanel::setOrderStatus($order_id,1);
	}

	function notifyUserByEmail($order_id, $subject, $body)
	{
		// Envois un mail à l'utilisateur pour le prévenir que la commande est traitée.
		$database =& JFactory::getDBO();
		$query = "SELECT o.user_id as user_id,
						 u.email as email,
						 o.name as data_title, 
						 o.id as order_id 
				  FROM  #__sdi_order o,#__users u 
				  WHERE order_id=$order_id and o.user_id = u.id";
		$database->setQuery($query);
		$row = $database->loadObject();

		$account = new accountByUserId($database);
		$account->load($row->user_id);
		//echo $account->notify_order_ready;

		if ($account->notify_order_ready == 1) {
			SITE_cpanel::sendMailByEmail($row->email,JText::sprintf($subject, $row->data_title, $row->order_id),JText::sprintf($body,$row->data_title, $row->order_id));
		}
	}

	function processOrder(){
		global  $mainframe;
		$database =& JFactory::getDBO();
		$option=JRequest::getVar("option");
		
		$product_list_id=JRequest::getVar("product_list_id","0");
		if($product_list_id == 0)
		{
			$mainframe->enqueueMessage(JText::_("SHOP_ORDER_MESSAGE_NO_SELECTION"),'info');						
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listOrdersForProvider" ), false));
			exit();
		}
		$queryOrder = "SELECT * FROM #__sdi_order_product WHERE id = $product_list_id";
		$database->setQuery($queryOrder);
		$result = $database->loadObject();
		$order_id = $result->order_id;
		$product_id = $result->product_id;
	
		$user = JFactory::getUser();

		//Build the query on product treatment type
		$treatmentTypeQuery = "";
		$database->setQuery("SELECT t.code as code, p.treatmenttype_id as treatment_type 
								FROM #__sdi_list_treatmenttype t, #__sdi_product p 
								WHERE p.treatmenttype_id=t.id AND p.id = $product_id");
		$result = $database->loadObject();
		$treatmentType = $result->treatment_type;
		$treatmentCode = $result->code;
		$treatmentTranslation = "";
		if($treatmentType != "")
		{
			$treatmentTypeQuery = " AND p.treatmenttype_id = $treatmentType ";
			$queryTreatment = "SELECT label FROM #__sdi_list_treatmenttype WHERE id = $treatmentType";
			$database->setQuery($queryTreatment);
			$treatmentTranslation = $database->loadResult();
		}

		$query = "SELECT p.id as product_id, 
						 o.id as order_id, 
						 u.name as username,
						 v.metadata_id as metadata_id, 
						 m.guid as metadata_guid,
						 p.name as data_title,
						 o.name as name,
						 o.type_id as type, 
						 opl.status_id as status,
						 otl.label as type_translation,
						 o.sent as order_send_date
				  FROM  #__sdi_order o, 
				  		#__sdi_list_orderstatus osl, 
				  		#__sdi_order_product opl, 
				  		#__sdi_list_productstatus psl, 
				  		#__sdi_product p,
				  		#__sdi_objectversion v ,
				  		#__sdi_metadata m,
				  		#__sdi_account a, 
				  		#__sdi_list_ordertype otl,
				  		#__users u 
				  WHERE o.status_id=osl.id 
				  AND o.type_id = otl.id  
				  AND opl.status_id=psl.id 
				  AND a.user_id = u.id 
				  AND  v.id = p.objectversion_id
				  AND  v.metadata_id = m.id
				  AND o.id = opl.order_id 
				  AND opl.product_id = p.id 
				  AND p.diffusion_id = a.id 
				  AND a.user_id =".$user->id." 
				  AND psl.code='AWAIT' 
				  AND osl.code <> 'ARCHIVED' 
				  $treatmentTypeQuery
				  AND o.id=".$order_id;
					
		$query .= " order by o.id";

		$database->setQuery($query);
		$rows = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}

		$query = "SELECT * FROM  #__sdi_order WHERE id=".$order_id;
		$database->setQuery($query);
		$rowOrder = $database->loadObject();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}
		
		if($rowOrder->thirdparty_id != 0){
			$query = "SELECT user_id FROM #__sdi_account where id = ".$rowOrder->thirdparty_id;
			$database->setQuery($query);
			$res = $database->loadResult();
			$query = "SELECT * FROM  #__users WHERE id=".$res;
			$database->setQuery($query);
			$third_party = $database->loadObject();
		}

		$query = "SELECT label FROM #__sdi_list_orderstatus where id = ".$rowOrder->status_id;
		$database->setQuery($query);
		$status = $database->loadResult();
		
		$query = "SELECT * FROM  #__users WHERE id=".$rowOrder->user_id;
		$database->setQuery($query);
		$partner = $database->loadObject();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}

		HTML_cpanel::processOrder($rows,$option,$rowOrder,$third_party,$status,$partner,$product_id, $treatmentTranslation, $treatmentCode);
	}

	function listOrders(){
		global  $mainframe;
		$database =& JFactory::getDBO();
		$user = JFactory::getUser();
		$rootAccount = new accountByUserId($database);
		$rootAccount->load($user->id);

		//Check the use rights
		if(!userManager::hasRight($rootAccount->id,"REQUEST_INTERNAL") &&
		!userManager::hasRight($rootAccount->id,"REQUEST_EXTERNAL"))
		{
			$mainframe->enqueueMessage(JText::_("SHOP_MSG_NOT_ALLOWED_TO_MANAGE")." :  ".JText::_("SHOP_MSG_NOT_ALLOWED_TO_MANAGE_REQUEST"),"INFO");
			return;
		}

		$option=JRequest::getVar("option");
		$limit = JRequest::getVar('limit', 20 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		
		//Automatic Archive and/or Historize of the orders
		//Get the delays in days unit
		$archive_delay = config_easysdi::getValue("SHOP_CONFIGURATION_ARCHIVE_DELAY", 30);
		$history_delay = config_easysdi::getValue("SHOP_CONFIGURATION_HISTORY_DELAY", 60);
		
		if($history_delay <= $archive_delay){
			echo "<div class='alert'>";
			echo JText::_("SHOP_ORDER_MESSAGE_HISTORY_ARCHIVE_DELAYS_ERROR");	
			echo "</div>";
		}
		
		//Archive
		$saved = sdilist::getIdByCode('#__sdi_list_orderstatus','SAVED' );
		$finish = sdilist::getIdByCode('#__sdi_list_orderstatus','FINISH' );
		$archived = sdilist::getIdByCode('#__sdi_list_orderstatus','ARCHIVED' );
		$historized = sdilist::getIdByCode('#__sdi_list_orderstatus','HISTORIZED' );
			
		$query = "UPDATE #__sdi_order SET status_id=".$archived.", 
					updated = NOW(), updatedby =".$rootAccount->id."  
					WHERE user_id = ".$user->id." 
					AND DATEDIFF(NOW() ,updated) > $archive_delay 
					AND DATEDIFF(NOW() ,updated) < $history_delay 
					AND status_id = $finish";
		$database->setQuery($query);
		if (!$database->query()) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}
		
		//Historize
		$query = "SELECT id FROM #__sdi_order 
						WHERE user_id = ".$user->id." 
						AND DATEDIFF(NOW() ,updated) > $history_delay 
						AND (status_id = ".$archived." OR status_id = ".$finish.")";
		$database->setQuery($query);
		$toUpdate = $database->loadResultArray();

		$query = "UPDATE #__sdi_order 
					SET status_id=".$historized.", updated = NOW() , updatedby =".$rootAccount->id." 
					WHERE user_id = ".$user->id." 
					AND DATEDIFF(NOW() ,updated) > $history_delay 
					AND (status_id = ".$archived." OR status_id = ".$finish.")";
		$database->setQuery($query);
		if (!$database->query()) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}

		//Delete files for historized order
		foreach ($toUpdate as $field)
		{
			$query = "DELETE FROM #__sdi_orderproduct_file  f
						INNER JOIN #__sdi_order_product o ON o.id=f.orderproduct_id
					   WHERE o.order_id = ".$field;
			$database->setQuery($query);
			if (!$database->query()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}
		}

		$search = $mainframe->getUserStateFromRequest( "searchOrder{$option}", 'searchOrder', '' );
// 		$search = $database->getEscaped( trim( strtolower( $search ) ) );
		$filter = "";
		$queryType = "select * from #__sdi_list_ordertype";
		$database->setQuery($queryType);
		$typeFilter = $database->loadObjectList();

		$queryStatus = "select * from #__sdi_list_orderstatus";
		$database->setQuery($queryStatus);
		$statusFilter = $database->loadObjectList();
		
		$orderstatus=JRequest::getVar("orderstatus","");
		if ($orderstatus !=""){
			$filterList[]= "(o.status_id ='$orderstatus')";
		}

		$ordertype= JRequest::getVar("ordertype","");
		if ($ordertype !=""){
			$filterList[]= "(o.type_id ='$ordertype')";
		}

		if ( $search )
		{
			if(strripos ($search,'"') != FALSE)
			{
				$searchcontent = substr($search, 1,strlen($search)-2 );
				$searchcontent = $database->getEscaped( trim( strtolower( $searchcontent ) ) );
				$filterList[]= " (o.name LIKE '%".$searchcontent."%' OR o.id LIKE '%".$searchcontent."%')";
			}
			else
			{
				$search = $database->getEscaped( trim( strtolower( $search ) ) );
				$filterList[]= " (o.name = '$search' OR o.id = '$search')";
			}
		}

		if (count($filterList) > 1)
		$filter .= " AND ".implode(" AND ", $filterList);
		elseif (count($filterList) == 1)
		$filter .= " AND ".$filterList[0];

		$query = "SELECT o.*, 
						 osl.code, 
						 osl.label as status_label, 
						 tl.label as type_label 
				 FROM #__sdi_order o 
				 INNER JOIN #__sdi_list_orderstatus osl ON o.status_id=osl.id 
				 INNER JOIN #__sdi_list_ordertype tl ON o.type_id=tl.id 
				 WHERE  o.user_id = ".$user->id;
		$query .= $filter;
		
		if ($orderstatus ==""){
			$query .= " and o.status_id <> ".$archived." and o.status_id <> ".$historized;
		}
		
		$query .= " order by o.created";
		
		$queryCount = "select count(*) from #__sdi_order o where";
		if ($orderstatus ==""){
			$queryCount .= " o.status_id <> ".$archived." and o.status_id <> ".$historized." AND";
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
		
		HTML_cpanel::listOrders($pageNav,$rows,$option,$orderstatus,$ordertype,$search, $statusFilter, $typeFilter,$redirectURL, $saved,$finish,$archived,$historized);
	}

	function orderReport($id,$isfrontEnd, $isForProvider){
		global $mainframe;
		$database =& JFactory::getDBO();
		
		$isInMemory = false;
		if($id == 0)
		{
			$isInMemory = true;
		}
		
		if($isForProvider == '')
		{
			$isForProvider == false;
		}
		
		//Get the current logged user
		$u = JFactory::getUser();
		$account = new accountByUserId($database);
		$account->load($u->id);
		
		if($isfrontEnd == true)
		{
			//Check if a user is logged
			if ($u->guest)
			{
				$mainframe->enqueueMessage(JText::_("SHOP_ACCOUNT_NOT_CONNECTED"),"INFO");
				return;
			}
			if($isForProvider == false)
			{
				//Check the current user rights
				if(!userManager::hasRight($account->id,"REQUEST_INTERNAL") &&
					!userManager::hasRight($account->id,"REQUEST_EXTERNAL"))
				{
					$mainframe->enqueueMessage(JText::_("SHOP_MSG_NOT_ALLOWED_TO_MANAGE")." :  ".JText::_("SHOP_MSG_NOT_ALLOWED_TO_MANAGE_REQUEST"),"INFO");
					return;
				}
			}
		}
		else
		{
			echo "usertype : ".$u->name." - ".$u->usertype;
			if($u->usertype!= 'Super Administrator' && $u->usertype!= 'Administrator'   )
			{
				$mainframe->enqueueMessage(JText::_("SHOP_MSG_NOT_ALLOWED_TO_MANAGE")." :  ".JText::_("SHOP_MSG_NOT_ALLOWED_TO_MANAGE_REQUEST"),"INFO");
				return;
			}
		}
		
		$db =& JFactory::getDBO();
		
		$rowOrder = null;
		$perimeterRows = null;
		
		if(!$isInMemory){
			//fetch order in database			
			$query = "SELECT a.*, 
							 sl.label as slT, 
							 tl.label as tlT, 
							 a.name as order_name  
					  FROM  #__sdi_order a ,  
					  		#__sdi_list_orderstatus sl, 
					  		#__sdi_list_ordertype tl 
					  WHERE a.id = $id 
					  AND tl.id = a.type_id 
					  AND sl.id = a.status_id";
			$db->setQuery($query);
			$rowOrder = $db->loadObject();
			
			$query = "SELECT b.perimeter_id, 
							 b.text, 
							 b.value 
					  FROM  #__sdi_order a, 
					  		#__sdi_order_perimeter b 
					  WHERE a.id = b.order_id 
					  AND a.id = $id order by b.id";
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
			$queryTranslation = "SELECT label from #__sdi_list_ordertype WHERE code = '".$mainframe->getUserState('order_type')."'";
			$db->setQuery($queryTranslation );
			$translation = $db->loadResult();
			$rowOrder = array (  
				'name' => $mainframe->getUserState('order_name'),
				'type_id' => $mainframe->getUserState('order_type'),
				'thirdparty_id' => $mainframe->getUserState('third_party'),
				'user_id' => $u->id,
				'sent' => '',
				'buffer' => $mainframe->getUserState('bufferValue'),
				'surface' =>  $mainframe->getUserState('totalArea'),
				'tlT' => $translation,
				'slT' => "",
				'status_id' => ""
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
				$mainframe->enqueueMessage(JText::_("SHOP_ORDER_MESSAGE_NOT_ALLOWED_TO_ACCESS_ORDER_REPORT").$user,"INFO");
				return;
			}
		}
		
		//user name
		$queryUser = "SELECT name FROM #__users WHERE id = $user";
		$db->setQuery($queryUser );
		$user_name =  $db->loadResult();
		
		//root name
		$root_name = "";
		if($account->root_id != null)
		{
			$queryUser = "SELECT name FROM #__users WHERE id =(SELECT user_id FROM #__sdi_account where id= $account->root_id)";
			$db->setQuery($queryUser);
			$root_name =  $db->loadResult();
		}
		
		$third_name ='';
		//Third name
		$third = $rowOrder->thirdparty_id; 
		if( $third != 0)
		{
			$queryUser = "SELECT name FROM #__users WHERE id =(SELECT user_id FROM #__sdi_account where id= $third)";
			$db->setQuery($queryUser );
			$third_name =  $db->loadResult();
		}
		
		//Load the products
		if(!$isInMemory)
		{
			$query = '';
			if($isForProvider)
			{
				$query = "SELECT *, a.id as plId 
					  FROM #__sdi_order_product  a, 
					  	   #__sdi_product b 
					  WHERE a.product_id  = b.id 
					  AND a.order_id = $id 
					  AND b.diffusion_id = $account->id";
			}
			else
			{
				$query = "SELECT *, a.id as plId , opf.filename as filename
					  FROM #__sdi_order_product  a LEFT OUTER JOIN #__sdi_orderproduct_file opf ON opf.orderproduct_id = a.id, 
					       #__sdi_product b
					  WHERE a.product_id  = b.id 
					  AND a.order_id = $id";
			}
			$db->setQuery($query );
			$rowsProduct = $db->loadObjectList();
			if ($db->getErrorNum()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
			}
		}
		else
		{
			//Load product list in memory
			$cid = $mainframe->getUserState('productList');
			$rowsProduct = Array();
			if (count($cid)>0){
				for ($i = 0; $i < count($cid); $i ++){
					$query = "SELECT * FROM #__sdi_product WHERE id =".$cid[$i];
					$db->setQuery($query );
					$rowsProduct[] = $db->loadObject();
				}
			}
		}
		
		if(count($rowsProduct) == 0)
		{
			//The connected user does not have any product to provide in this order
			//Do not display any information and quit with error message
			$mainframe->enqueueMessage(JText::_("SHOP_ORDER_MESSAGE_NOT_ALLOWED_TO_ACCESS_ORDER_REPORT") ,"INFO");
			return;
		}
		
		HTML_cpanel::orderReportRecap($id, $isfrontEnd, $isForProvider, $rowOrder, $perimeterRows, $user_name, $root_name, $third_name, $rowsProduct, $isInMemory);
	}

	
	function sendOrder($order_id){
		global $mainframe;
		$db =& JFactory::getDBO();
		$user = JFactory::getUser();
		$account = new accountByUserId($db);
		$account->load($user->id);

		//$date = new JDate();


		$await_type = sdilist::getIdByCode('#__sdi_list_productstatus','AWAIT' );
		$available_type = sdilist::getIdByCode('#__sdi_list_productstatus','AVAILABLE' );
		$status_id = sdilist::getIdByCode('#__sdi_list_orderstatus','SENT' );

		$order = new order ($db);
		$order->load($order_id);
		$order->setStatus($status_id);
		$order->sent=date('Y-m-d H:i:s');
		$order->store();
		
			
		SITE_cpanel::notifyOrderToDiffusion($order_id);
			
		$query = "SELECT o.name as cmd_name,
						 u.email as email , 
						 p.id as product_id, 
						 p.name as data_title , 
						 ob.account_id as partner_id   
				  FROM #__users u,
				  	   #__sdi_account pa, 
				  	   #__sdi_order_product opl , 
				  	   #__sdi_product p
				  	   INNER JOIN #__sdi_objectversion v ON v.id = p.objectversion_id
				  	   INNER JOIN #__sdi_object ob ON ob.id=v.object_id,
				  	   #__sdi_order o, 
				  	   #__sdi_list_productstatus psl, 
				  	   #__sdi_list_orderstatus osl, 
				  	   #__sdi_list_ordertype tl 
				  WHERE opl.status_id=psl.id 
				  AND o.status_id=osl.id 
				  AND opl.order_id= $order_id 
				  AND p.id = opl.product_id 
				  AND p.free = 1 
				  AND psl.code='AWAIT' 
				  AND o.type_id=tl.id 
				  AND tl.code='D' 
				  AND ob.account_id = pa.id 
				  AND pa.user_id = u.id 
				  AND o.id=opl.order_id 
				  AND osl.code='SENT' ";
			
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$db->getErrorMsg();
			echo "</div>";
		}

		$response_send = 0;
		foreach ($rows as $row){
			//$response_send = 1;
			$query = "UPDATE   #__sdi_order_product opl set status_id = ".$available_type." WHERE opl.order_id= $order_id AND opl.product_id = $row->product_id";
			$db->setQuery( $query );
			if (!$db->query()) {
				echo "<div class='alert'>";
				echo $db->getErrorMsg();
				echo "</div>";
			}
			$user = JFactory::getUser();
				
			SITE_cpanel::sendMailByEmail($row->email,JText::_("SHOP_SHOP_MAIL_SUBJECT_REQUEST_FREE_PRODUCT"),JText::sprintf("SHOP_SHOP_MAIL_BODY_REQUEST_FREE_PROUCT",$row->data_title,$row->cmd_name,$user->username));
		}
		SITE_cpanel::setOrderStatus($order_id,$response_send);
	}

	
	function sendMailByEmail($email,$subject,$body)
	{
				$mailer =& JFactory::getMailer();		
				$mailer->addBCC($email);																				
				$mailer->setSubject($subject);
				$user = JFactory::getUser();
				$mailer->setBody($body);				
				if ($mailer->send() !==true){
				}		
	}
	
	//Update order status after user validation or after provider has made a product available
	function setOrderStatus ($order_id, $response_send)
	{
		 /*
		 * Mise à jour du statut de la commande.
		 * Si il n'y a plus rien à traiter, on la marque comme terminée
		 * dans les autres cas on la marque comme en cours de traitement
		 */
		global $mainframe;
		$db =& JFactory::getDBO();
		
		$order = new order ($db);
		$order->load($order_id);
		$status_id =$order->status_id;
		
		$query = "SELECT COUNT(*) 
				  FROM #__sdi_order_product p, 
					   #__sdi_list_productstatus sl 
				  WHERE p.status_id=sl.id 
				  AND p.order_id=$order_id 
				  AND sl.code = 'AWAIT' ";
		$db->setQuery($query);
		$total = $db->loadResult();
		
		$query = "SELECT COUNT(*) 
				  FROM #__sdi_order_product p
				  WHERE p.order_id=$order_id  ";
		$db->setQuery($query);
		$totalProduct = $db->loadResult();
	
		
		if ( $total == 0)
		{
			$status_id = sdilist::getIdByCode('#__sdi_list_orderstatus','FINISH' );
		}
		else if ($total == $totalProduct)
		{
			//Do nothing, keep the current status
		}
		else
		{
			$status_id = sdilist::getIdByCode('#__sdi_list_orderstatus','PROGRESS' );
		}

		$order->status_id = $status_id;
		if($response_send)
		{
			$order->response = date('Y-m-d H:i:s');
			$order->responsesent = $response_send;
		}

		if(!$order->store())
		{
			echo "<div class='alert'>";
		 	echo JText::_($db->getErrorMsg());
		 	echo "</div>";
		 	break;
		}
		
		$db->setQuery("SELECT o.name as order_name, 
							  u.id as user_id, 
							  u.email as email 
					   FROM #__sdi_order o, 
					   		#__users u 
					   WHERE o.user_id = u.id 
					   AND o.id=".$order_id);
		$results = $db->loadObjectList();
		$order_name = $results[0]->order_name;
		$email = $results[0]->email;
		$usr_id = $results[0]->user_id;
		
		//verify the notification is active.
		$queryNot = "SELECT a.notify_order_ready FROM #__sdi_account a, #__users u WHERE u.id = a.user_id and a.user_id = $usr_id";
		$db->setQuery($queryNot);
		$not = $db->loadResult();
		if ($total ==0 && $not == 1)
		{
			SITE_cpanel::sendMailByEmail($email,JText::sprintf("SHOP_ORDER_CMD_READY_MAIL_SUBJECT", $order_name, $order_id),JText::sprintf("SHOP_ORDER_CMD_READY_MAIL_BODY",$order_name,$order_id));
		}
		else if ($total == $totalProduct -1 && $not == 1)
		{
			SITE_cpanel::sendMailByEmail($email,JText::sprintf("SHOP_ORDER_CMD_READY_MAIL_SUBJECT", $order_name, $order_id),JText::sprintf("SHOP_ORDER_CMD_READY_MAIL_BODY",$order_name,$order_id));
		}
	}
	
	//Send a command notification to the email address specified in the product definition
	//Only if the treatment of the product is defined "manual"
	function notifyOrderToDiffusion($order_id)
	{
		$db =& JFactory::getDBO();

		$queryOrderName = "SELECT name FROM #__sdi_order WHERE id = $order_id";
		$db->setQuery($queryOrderName);
		$order_name = $db->loadResult();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";
		}
		
		$queryOrderType = "SELECT l.code FROM #__sdi_order o, #__sdi_list_ordertype l WHERE o.type_id = l.id and id = $order_id";
		$db->setQuery($queryOrderType);
		$order_type = $db->loadResult();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";
		}
		
		$orderQuery = "SELECT distinct l.product_id 
					   FROM #__sdi_product p, 
					   		#__sdi_order_product l 
					   WHERE l.product_id = p.id 
					   AND l.order_id = $order_id";
		if($order_type == "D")
		{
			//Filter out the products that are free if it is a devis, they should not be notificated
			$orderQuery = "SELECT distinct l.product_id 
						   FROM #__sdi_product p, 
						   		#__sdi_order_product l 
						   WHERE l.product_id = p.id 
						   AND p.free=0 
						   AND l.order_id = $order_id";
		}
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

		$queryNotitification = "SELECT DISTINCT diffusion_id ,
												notification 
								FROM #__sdi_product 
								WHERE id IN ($productList)
								AND treatmenttype_id = (SELECT id from #__sdi_list_treatmenttype WHERE code = 'MANU')";
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
				$queryDiffusionPartnerEmail = "SELECT email FROM #__users WHERE id = (SELECT user_id from #__sdi_account WHERE id = $result->diffusion_id)";
				$db->setQuery($queryDiffusionPartnerEmail);
				$diffusionEmail = $db->loadResult();
			}
			SITE_cpanel::getEmailNotificationList($result->notification, $list);
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
		$mailer->setSubject(JText::_("SHOP_ORDER_CMD_NOTIFICATION_SUBJECT"));
		$mailer->setBody(JText::sprintf("SHOP_ORDER_CMD_NOTIFICATION_BODY",$order_id,$order_name));
		if ($mailer->send() !==true)
		{
			//
		}
	}
	
	function showSummaryForAccount(){
		$summaryForId = JRequest::getVar('SummaryForId');
		$print = JRequest::getVar('print');
		$toolbar = JRequest::getVar('toolbar');
		HTML_cpanel_account::showSummaryForAccount($summaryForId, $print, $toolbar);
	}
}
?>
