<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch
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
			
			$query = "update #__easysdi_order set status = ".$status_id." where user_id = ".$user->id." AND ORDER_ID =".$order_id;
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
		
		/**
		 * Allow Pathway with mod_menu_easysdi
		 */
		 // Get the menu item object
        $menus = &JSite::getMenu();
        $menu  = $menus->getActive();
         $params = &$mainframe->getParams();
 	 	//Handle the breadcrumbs
        if(!$menu)
        {
        	$params->set('page_title',	JText::_("EASYSDI_MENU_ITEM_MYTREATMENT"));
			//Add item in pathway		
			$breadcrumbs = & $mainframe->getPathWay();
		    $breadcrumbs->addItem( JText::_("EASYSDI_MENU_ITEM_MYTREATMENT"), '' );
		    $document	= &JFactory::getDocument();
			$document->setTitle( $params->get( 'page_title' ) );
        }
		/**/
        
		/*
			$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 5 );
			$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0);
		*/
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		
		$database =& JFactory::getDBO();
		$user = JFactory::getUser();
		$rootPartner = new partnerByUserId($database);
		$rootPartner->load($user->id);

		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $database->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";

		$productorderstatus = JRequest::getVar("productorderstatus","");
		$orderStatus=" (osl.code='SENT' or osl.code='PROGRESS' or osl.code='AWAIT') ";
		if ($productorderstatus == ""){
			$queryStatus = "select id from #__easysdi_order_product_status_list where code ='AWAIT'";
			$database->setQuery($queryStatus);
			$productorderstatus = $database->loadResult();
				
		}

		// Ne montre que les commandes traitées ou partiellement traitées.
		$queryStatus = "select id from #__easysdi_order_product_status_list where code ='AVAILABLE'";
		$database->setQuery($queryStatus);
		$status_id = $database->loadObjectList();
		
		if ($productorderstatus == $status_id){
			$orderStatus=" (osl.code='FINISH' OR osl.code='PROGRESS' ) ";
				
		}

		$ordertype= JRequest::getVar("ordertype","");
		if ($ordertype !=""){
			$filterList[] = "(o.type ='$ordertype')";
		}

		if ( $search ) {
			$filterList[] = "(o.name LIKE '%$search%')";
		}

		if (count($filterList) > 0)
			$filter .= implode(" AND ", $filterList);
		if (count($filterList)==1)
			$filter = " AND ".$filterList[0];
			
		$queryStatus = "select * from #__easysdi_order_status_list ";
		$database->setQuery($queryStatus);
		$productStatusFilter = $database->loadObjectList();
		
		$queryType = "select * from #__easysdi_order_type_list ";
		$database->setQuery($queryType);
		$productTypeFilter = $database->loadObjectList();
		
		// Ne montre pas dans la liste les devis dont le prix est gratuit. Ils sont automatiquement traité par le système.
		$query = "SELECT o.order_id as order_id, u.name as username,p.data_title as data_title,o.name as name,o.type as type, o.status as status, osl.code as code, osl.translation as status_translation, tl.translation as type_translation FROM  #__easysdi_order o, #__easysdi_order_product_list opl, #__easysdi_product p, #__easysdi_community_partner pa, #__users u, #__easysdi_order_status_list osl, #__easysdi_order_type_list tl WHERE o.status=osl.id and pa.user_id = u.id and o.order_id = opl.order_id and opl.product_id = p.id and p.partner_id = pa.partner_id and pa.user_id =".$user->id." and opl.status='$productorderstatus' and $orderStatus AND o.order_id NOT IN (SELECT o.order_id FROM  #__easysdi_order o, #__easysdi_order_product_list opl, #__easysdi_product p,#__easysdi_community_partner pa, #__users u, #__easysdi_order_status_list osl , #__easysdi_order_product_status_list psl, #__easysdi_order_type_list tl WHERE o.type=tl.id and o.status=osl.id and pa.user_id = u.id and o.order_id = opl.order_id and opl.product_id = p.id and p.partner_id = pa.partner_id and pa.user_id =".$user->id." and opl.status=osl.id and psl.code='AWAIT' and $orderStatus and o.type = tl.id and tl.code ='D' and p.is_free = 1) and p.diffusion_partner_id = $rootPartner->partner_id";
		
		$query .= $filter;
		$query .= " order by o.order_id";
		
		$queryCount = "SELECT count(*) FROM  #__easysdi_order o, #__easysdi_order_product_list opl,#__easysdi_product p,#__easysdi_community_partner pa , #__users u, #__easysdi_order_status_list osl WHERE pa.user_id = u.id and  o.status=osl.id and o.order_id = opl.order_id and opl.product_id = p.id and p.partner_id = pa.partner_id and pa.user_id =".$user->id." and opl.status='$productorderstatus' and $orderStatus  AND o.order_id NOT IN (SELECT o.order_id FROM  #__easysdi_order o, #__easysdi_order_product_list opl,#__easysdi_product p,#__easysdi_community_partner pa, #__users u, #__easysdi_order_product_status_list psl , #__easysdi_order_type_list tl WHERE opl.status=psl.id and pa.user_id = u.id and o.order_id = opl.order_id and opl.product_id = p.id and p.partner_id = pa.partner_id and pa.user_id =".$user->id." and psl.code='AWAIT' and $orderStatus and  o.type = tl.id and tl.code ='D' and p.is_free = 1) and p.diffusion_partner_id = $rootPartner->partner_id";
		//$and p.diffusion_partner_id = $rootPartner->partner_id
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

		HTML_cpanel::listOrdersForProvider($pageNav,$rows,$option,$ordertype,$search,$productorderstatus, $productStatusFilter, $productTypeFilter);

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

	function saveOrdersForProvider(){

		global  $mainframe;
		$database =& JFactory::getDBO();

		$products_id = JRequest::getVar("product_id");

		$order_id =  JRequest::getVar("order_id");
		foreach ($products_id as $product_id){

			$remark = JRequest::getVar("remark".$product_id);
			$price = JRequest::getVar("price".$product_id,"0");
			if (strlen($price)!=0){

				$queryStatus = "select id from #__easysdi_order_product_status_list where code ='AVAILABLE'";
				$database->setQuery($queryStatus);
				$status_id = $database->loadResult();
				
				$query = "UPDATE #__easysdi_order_product_list SET status = ".$status_id.", remark= '.$remark.',price = $price ";
					
			 $fileName = $_FILES['file'.$product_id]["name"];
			 if (strlen($fileName)>0){
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
			
			 $query = "SELECT COUNT(*) FROM #__easysdi_order_product_list p, #__easysdi_order_product_status_list sl WHERE p.status=sl.id and p.order_id=".$order_id." AND sl.code = 'AWAIT' ";
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
			$query = "UPDATE   #__easysdi_order  SET status =".$status_id." ,response_date ='". $date->toMySQL()."'  WHERE order_id=$order_id ";

			 //Mise à jour du statut de la commande

				$database->setQuery( $query );
			 if (!$database->query()) {
			 	echo "<div class='alert'>";
			 	echo JText::_($database->getErrorMsg());
			 	echo "</div>";

			 	break;
			 }

			 if ($total ==0){
			 	SITE_cpanel::notifyUserByEmail($order_id);
			 }
			}
		}
	}

	function notifyUserByEmail($order_id){
		/*
		 * Envois un mail à l'utilisateur pour le prévenir que la commande est traitée.
		 */

		$database =& JFactory::getDBO();
			

		$query = "SELECT o.user_id as user_id,u.email as email,o.name as data_title FROM  #__easysdi_order o,#__users u WHERE order_id=$order_id and o.user_id = u.id";
		$database->setQuery($query);
		$row = $database->loadObject();

		$partner = new partnerByUserId($database);
		$partner->load($row->user_id);
		echo $partner->notify_order_ready;

		if ($partner->notify_order_ready == 1) {
			 

			SITE_product::sendMailByEmail($row->email,JText::_("EASYSDI_CMD_READY_MAIL_SUBJECT"),JText::sprintf("EASYSDI_CMD_READY_MAIL_BODY",$row->data_title));
				
		}
	}

	function processOrder(){

		global  $mainframe;
		$database =& JFactory::getDBO();
			
		$option=JRequest::getVar("option");
		$order_id=JRequest::getVar("order_id","0");
		$user = JFactory::getUser();


		$query = "SELECT p.id as product_id, o.order_id as order_id, u.name as username,p.metadata_id as metadata_id, p.data_title as data_title,o.name as name,o.type as type, opl.status as status FROM  #__easysdi_order o, #__easysdi_order_status_list osl, #__easysdi_order_product_list opl, #__easysdi_order_product_status_list psl, #__easysdi_product p,#__easysdi_community_partner pa, #__users u WHERE o.status=osl.id and opl.status=psl.id and pa.user_id = u.id and o.order_id = opl.order_id and opl.product_id = p.id and p.partner_id = pa.partner_id and pa.user_id =".$user->id." and psl.code='AWAIT' and osl.code <> 'ARCHIVED'  and o.order_id=".$order_id;
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


		HTML_cpanel::processOrder($rows,$option,$rowOrder,$partner);

	}


	function listOrders(){

		global  $mainframe;
		/**
		 * Allow Pathway with mod_menu_easysdi
		 */
		 // Get the menu item object
	     $menus = &JSite::getMenu();
         $menu  = $menus->getActive();
         $params = &$mainframe->getParams();
 		 //Handle the breadcrumbs
        if(!$menu)
        {
        	$params->set('page_title',	JText::_("EASYSDI_MENU_ITEM_MYORDERS"));
			//Add item in pathway		
			$breadcrumbs = & $mainframe->getPathWay();
		    $breadcrumbs->addItem( JText::_("EASYSDI_MENU_ITEM_MYORDERS"), '' );
		    $document	= &JFactory::getDocument();
			$document->setTitle( $params->get( 'page_title' ) );
        }
		/**/
        
		$option=JRequest::getVar("option");
		/*$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 5 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		*/
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		

		$database =& JFactory::getDBO();
		$user = JFactory::getUser();
		
		if(!userManager::isUserAllowed($user, "REQUEST_INTERNAL") && !userManager::isUserAllowed($user, "REQUEST_EXTERNAL") )
		{
			return;
		}
		
		$rootPartner = new partnerByUserId($database);
		$rootPartner->load($user->id);
		
		
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
		
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $database->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";

		$queryType = "select * from #__easysdi_order_type_list ";
		$database->setQuery($queryType);
		$typeFilter = $database->loadObjectList();
		
		$queryStatus = "select * from #__easysdi_order_status_list where code<>'ARCHIVED' and code<>'HISTORIZED'";
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
			$filterList[]= "(o.name LIKE '%$search%')";
		}

		if (count($filterList) > 1)
			$filter .= " AND ".implode(" AND ", $filterList);
		elseif (count($filterList) == 1)
			$filter .= " AND ".$filterList[0];	
		
		$query = "select o.*, osl.code, osl.translation as status_translation, tl.translation as type_translation from #__easysdi_order o inner join #__easysdi_order_status_list osl on o.status=osl.id inner join #__easysdi_order_type_list tl on o.type=tl.id ";
		$query .= $filter;
		$query .= " and o.status <> ".$archive." and o.status <> ".$history;
		
		$queryCount = "select count(*) from #__easysdi_order o where o.status <> ".$archive." and o.status <> ".$history." AND  o.user_id = ".$user->id;
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

		HTML_cpanel::listOrders($pageNav,$rows,$option,$orderstatus,$ordertype,$search, $statusFilter, $typeFilter);

	}


	function orderReport($id){
		global $mainframe;
		$option = JRequest::getVar('option');

		$db =& JFactory::getDBO();
		
		$query = "SELECT * FROM  #__easysdi_order a ,  #__easysdi_order_product_perimeters b where a.order_id = b.order_id and a.order_id = $id";


		$db->setQuery($query );

		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}

		$query = "SELECT * FROM #__easysdi_order_product_list  a, #__easysdi_product b where a.product_id  = b.id and order_id = $id";
		$db->setQuery($query );
		$rowsProduct = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}

		?>
<h1><?php echo JText::_("EASYSDI_ORDERED_PRODUCT_LIST") ?></h1>
<table>
<?php
$i=0;

$queryStatus = "select id from #__easysdi_order_product_status_list where code ='AVAILABLE'";
$db->setQuery($queryStatus);
$status_id = $db->loadResult();
		
foreach ($rowsProduct as $row){ ?>
	<tr>
		<td><?php echo ++$i; ?></td>
		<td><?php echo $row->data_title?><?php if ($row->is_free)  {echo " (".JText::_("EASYSDI_FREE_PRODUCT").")" ; }?></td>
		<?php
		
		if ($row->status == $status_id){
			$queryType = "select id from #__easysdi_order_type_list where code='O'";
			$db->setQuery($queryType);
			$type = $db->loadResult();
		
			if($rows[0]->type==$type){?>

		<td><a target="RAW"
			href="./index.php?format=raw&option=<?php echo $option; ?>&task=downloadProduct&order_id=<?php echo $row->order_id?>&product_id=<?php echo $row->product_id?>">
			<?php echo JText::_("EASYSDI_DOWNLOAD_PRODUCT");?></a></td>
			<?php
			}
			
		}
		?>
	</tr>
	<?php }?>


</table>
	<?php

	$query = "SELECT * FROM  #__easysdi_perimeter_definition where id = ".$rows[0]->perimeter_id;
	$db->setQuery($query );
	$rowsPerimeter = $db->loadObjectList();
	if ($db->getErrorNum()) {
		echo "<div class='alert'>";
		echo 			$db->getErrorMsg();
		echo "</div>";
	}
	if ($rows[0]->perimeter_id > 0){
		?>
<h1><?php echo $rowsPerimeter[0]->perimeter_name; ?> (<?php echo $rowsPerimeter[0]->perimeter_desc; ?>)</h1>
		<?php }else{
			echo "<h1>".JText::_("EASYSDI_GEOMETRY_TEXT")."</h1>";

		} ?>
<table>

<?php
$i=0;
foreach ($rows as $row){?>
	<tr>
		<td><?php echo ++$i; ?></td>
		<td><?php echo $row->text?><?php if ($rows[0]->perimeter_id > 0) {echo "($row->value)";}?></td>

	</tr>
	<?php }?>
</table>


	<?php

	}

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

	 $query = "UPDATE  #__easysdi_order set status = ".$status_id.", order_update ='". $date->toMySQL()."' WHERE order_id = ".$order_id;

	 $db->setQuery($query );

		if (!$db->query()) {
	 	echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";
		}

		$query = "SELECT o.name as cmd_name,u.email as email , p.id as product_id, p.data_title as data_title , p.partner_id as partner_id   FROM #__users u,#__easysdi_community_partner pa, #__easysdi_order_product_list opl , #__easysdi_product p,#__easysdi_order o, #__easysdi_order_product_status_list psl, #__easysdi_order_status_list osl, #__easysdi_order_type_list tl WHERE opl.status=psl.id and o.status=osl.id and opl.order_id= $order_id AND p.id = opl.product_id and p.is_free = 1 and psl.code='AWAIT' and o.type=tl.id and tl.code='D' AND p.partner_id = pa.partner_id and pa.user_id = u.id and o.order_id=opl.order_id and osl.code='SENT' ";
				
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$db->getErrorMsg();
			echo "</div>";
		}

		foreach ($rows as $row){
				
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

		/*
		 * Mise à jour du statut de la commande.
		 * Si il n'y a plus rien à traiter, on la marque comme terminée
		 * dans les autres cas on la marque comme en cours de traitement
		 */
		$query = "SELECT COUNT(*) FROM #__easysdi_order_product_list p, #__easysdi_order_product_status_list sl WHERE p.status=sl.id and p.order_id=$order_id AND sl.code = 'AWAIT' ";
		$db->setQuery($query);
		$total = $db->loadResult();
		
		jimport("joomla.utilities.date");
		$date = new JDate();
		if ( $total == 0){
			$queryStatus = "select id from #__easysdi_order_status_list where code ='FINISH'";
			$db->setQuery($queryStatus);
			$status_id = $db->loadResult();
		}else{
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
		}
	}
}
?>
