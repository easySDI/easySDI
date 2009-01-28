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
/*foreach($_POST as $key => $val) 
echo '$_POST["'.$key.'"]='.$val.'<br />';*/

defined('_JEXEC') or die('Restricted access');

class SITE_favorite {
	
function manageFavoriteProduct ($orderable = 1)
{
	$user = JFactory::getUser();		
	
	if (!$user->guest){
	
		global $mainframe;
		$db =& JFactory::getDBO();
		
		//Get Var
		$partner = new partnerByUserId($db);
		$partner->load($user->id);
		$language=&JFactory::getLanguage();
		$language->load('com_easysdi');
		$limitstart = JRequest::getVar('limitstart',0);
		$limit = JRequest::getVar('limit',5);		
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$simpleSearchCriteria  	= JRequest::getVar('simpleSearchCriteria','lastAddedMD');
		$freetextcriteria = JRequest::getVar('freetextcriteria','');
		$freetextcriteria = $db->getEscaped( trim( strtolower( $freetextcriteria ) ) );
		$myFavoritesFirst = JRequest::getVar('myFavoritesFirst','');		
		
		//load favorite product_id
		$query = "SELECT product_id FROM #__easysdi_user_product_favorite WHERE partner_id = $partner->partner_id ";
		$db->setQuery( $query);
		$productList = $db->loadResultArray();
	
		//load notification product_id
		$query = "SELECT product_id FROM #__easysdi_user_product_favorite WHERE partner_id = $partner->partner_id AND  notify_metadata_modification=1";
		$db->setQuery( $query);
		$notificationList = $db->loadResultArray();
		
		//Free text filter
		$filter = "";	
		if ($freetextcriteria){
			$filter = $filter." AND (p.DATA_TITLE like '%".$freetextcriteria."%' ";
			$filter = $filter." OR p.METADATA_ID = '$freetextcriteria')";
		}			                                           
		$filter .= " AND (p.EXTERNAL=1 OR (p.INTERNAL =1 AND p.PARTNER_ID IN (SELECT PARTNER_ID FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id OR root_id = $partner->partner_id))) ";
				
		//Load products count
		$query  = "SELECT COUNT(*) FROM #__easysdi_product p where published=1 and orderable = ".$orderable;
		$query  = $query .$filter ;
		$db->setQuery( $query);
		$total = $db->loadResult();
		
		$myFavRows = "";
		$simpleSearchFilter = "";
	
		//Search criteria filter
		if($myFavoritesFirst == 1)
		{
			$simpleSearchFilter  = " order by f.partner_id DESC, ";
		}
		else
		{
			$simpleSearchFilter  = " order by ";
		}
		if ($simpleSearchCriteria == "moreConsultedMD"){
					$simpleSearchFilter  = $simpleSearchFilter ."p.weight DESC";
		}
		if ($simpleSearchCriteria == "lastAddedMD"){
					$simpleSearchFilter  = $simpleSearchFilter ."p.creation_date DESC";
		}
		if ($simpleSearchCriteria == "lastUpdatedMD"){
					$simpleSearchFilter  = $simpleSearchFilter ."p.update_date DESC";
		}
			
		$query  = "SELECT * FROM #__easysdi_product p LEFT OUTER JOIN (SELECT partner_id, product_id FROM #__easysdi_user_product_favorite WHERE partner_id = $partner->partner_id) f  ON p.id = f.product_id  where  p.published=1 and  p.orderable = ".$orderable;
		$query  = $query .$filter ;
		$query = $query .$simpleSearchFilter;		
		$db->setQuery( $query,$limitstart,$limit);
		$rows = $db->loadObjectList();
		
		if ($db->getErrorNum()) 
		{
			echo "<div class='alert'>"; 										
			echo 	$db->getErrorMsg();
			echo "</div>";
		}	
		
		?>
		<div class="contentin">	
			<script>
		 		function submitOrderForm()
		 		{
		 			document.getElementById('orderForm').submit();
		 		}
		 	</script>		
			<form name="orderForm" id="orderForm" action='<?php echo JRoute::_("index.php") ?>' method='POST'>
				<h2 class="contentheading"><?php echo JText::_("EASYSDI_FAVORITE_TITLE"); ?></h2>	
				<h3> <?php echo JText::_("EASYSDI_SEARCH_CRITERIA_TITLE"); ?></h3>
				<br>
				<span class="searchCriteria">
					<input name="freetextcriteria" type="text" value="<?php echo $freetextcriteria; ?>"><br>
				 	<input type="radio" name="simpleSearchCriteria" value="lastAddedMD" <?php if ($simpleSearchCriteria == "lastAddedMD") echo "checked";?> /> <?php echo JText::_("EASYSDI_LAST_ADDED_MD"); ?><br>
				 	<input type="radio" name="simpleSearchCriteria" value="moreConsultedMD" <?php if ($simpleSearchCriteria == "moreConsultedMD") echo "checked";?> /> <?php echo JText::_("EASYSDI_MORECONSULTED_MD"); ?><br>
					<input type="radio" name="simpleSearchCriteria" value="lastUpdatedMD" <?php if ($simpleSearchCriteria == "lastUpdatedMD") echo "checked";?> /> <?php echo JText::_("EASYSDI_LAST_UPDATED_MD"); ?><br>				 	
			 	</span>
			 	<span class="myFavoritesFirst">					
					<input type="checkbox" name="myFavoritesFirst" value="1" <?php if ($myFavoritesFirst == "1") echo "checked";?> /> <?php echo JText::_("EASYSDI_PRODUCT_MY_FAVORITE_FIRST"); ?><br>
			 	</span>
			 	<br>
			 	<button type="submit" class="searchButton" onClick="document.getElementById('task').value='manageFavoriteProduct';submitOrderForm();"> <?php echo JText::_("EASYSDI_SEARCH_BUTTON"); ?></button>
			 	<br>
			 	<br>
			 	<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>
						 			 
				<input type='hidden' name='option' value='<?php echo $option;?>'>
				<input type='hidden' id ="task" name='task' value='<?php echo $task; ?>'>
				<input type='hidden'  name='limitstart' value="<?php echo  $limitstart; ?>">
				
				<?php $pageNav = new JPagination($total,$limitstart,$limit); ?>
				<span class="searchCriteria">
					<table width="100%">
						<tr>
							<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
							<td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
						</tr>
					</table>
					<table width="100%">
						<tr>
							<td align="left" width="80%"><?php echo JText::_("EASYSDI_PRODUCT_TITLE"); ?></td>
							<td align="left" width="10%"><?php echo JText::_("EASYSDI_PRODUCT_MY_FAVORITE"); ?></td>
							<td align="left" width="10%"><?php echo JText::_("EASYSDI_PRODUCT_FAVORITE_NOTIFICATION"); ?></td>
						</tr>
					</table>
					<?php
					$param = array('size'=>array('x'=>800,'y'=>800) );
					JHTML::_("behavior.modal","a.modal",$param);	
					$i=0;	
					
				
					//Display all the products 
					foreach ($rows  as $row)
					{		
					?>
						<hr>		
						<table width="100%"  >		
							<tr>		
								<td width="45" > 
									<img src="./img.gif" width="40" height="40"> 
								</td>
								<td width="60%">
									<span class="mdtitle" >
									<a class="modal" title="<?php echo JText::_("EASYSDI_VIEW_MD"); ?>" 
										href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" rel="{handler:'iframe',size:{x:500,y:500}}"> <?php echo $row->data_title; ?></a>
									</span>
									<br>
									<span class="mdsupplier" ><?php echo $row->supplier_name;?>
									</span>
									<br>																
								</td>
								<td width="10%">
									<input type="checkbox" <?php if ( in_array($row->id,$productList)){ echo "checked";};?> onClick="document.getElementById('productId').value='<?php echo $row->id; ?>';if (this.checked){document.getElementById('task').value='addFavorite';} else{document.getElementById('task').value='removeFavorite';}; submitOrderForm();" />
								</td>
								<td width="10%"> 			
									<input type="checkbox" <?php if ( in_array($row->id,$notificationList)){ echo "checked";};?> onClick="document.getElementById('productId').value='<?php echo $row->id; ?>';if (this.checked){document.getElementById('task').value='addMetadataNotification';} else{document.getElementById('task').value='removeMetadataNotification';}; submitOrderForm();" />
								
								</td>
							
							</tr>				
						</table>		
					<?php
						$i=$i+1;
					}	
					?>	
					<input type="hidden" name="countMD" value="<?php echo $countMD;?>">		
					<input type="hidden" id="productId" name="productId" value="">
										
				</span>
			</form>
		</div>	
		<?php
	
	}
	else
	{
		echo "<div class='alert'>";			
		echo JText::_("EASYSDI_ACTION_NOT_ALLOWED");
		echo "</div>";
	}
}
	
function metadataNotification($is_notify = 0){
	
		global  $mainframe;
		$database=& JFactory::getDBO(); 
	
		$user = JFactory::getUser();		
		$partner = new partnerByUserId($database);
		if (!$user->guest){
			$partner->load($user->id);
		
			$productId = JRequest::getVar("productId",0);
			if ($productId == 0){
				
				echo "<div class='alert'>";			
				echo JText::_("EASYSDI_ERROR_NO_PRODUCT_ID");
				echo "</div>";	
				return;
			}
			$query = "SELECT COUNT(*) FROM  #__easysdi_user_product_favorite WHERE product_id = $productId AND partner_id  = $partner->partner_id";
			$database->setQuery( $query);
			$total = $database->loadResult();
			if($total == 0)
			{
				SITE_favorite::favoriteProduct(1);
			}
	
			$query = "UPDATE  #__easysdi_user_product_favorite set  notify_metadata_modification = $is_notify WHERE product_id = $productId AND partner_id  = $partner->partner_id";
			 
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<div class='alert'>";			
				echo JText::_($database->getErrorMsg());
				echo "</div>";	
				
				break;									
			}
		
		}
	
}

function favoriteProduct($is_favorite = 0){
	
		global  $mainframe;
		$database=& JFactory::getDBO(); 
	
		$user = JFactory::getUser();		
		$partner = new partnerByUserId($database);
		if (!$user->guest){
			$partner->load($user->id);
		
			$productId = JRequest::getVar("productId",0);
			if ($productId == 0){
				
				echo "<div class='alert'>";			
				echo JText::_("EASYSDI_ERROR_NO_PRODUCT_ID");
				echo "</div>";	
				return;
			}
			if ($is_favorite == 0)
			{
				$query = "DELETE FROM  #__easysdi_user_product_favorite WHERE product_id = $productId AND partner_id  = $partner->partner_id";
			}
			else
			{
				$query = "INSERT INTO  #__easysdi_user_product_favorite (product_id,partner_id) VALUES ($productId,$partner->partner_id)";
			}
				
			
			 
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<div class='alert'>";			
				echo JText::_($database->getErrorMsg());
				echo "</div>";	
				
				break;									
			}
		
		}
	
}
/*
function deleteFavoriteProduct($cid){
	
		global  $mainframe;
		$database=& JFactory::getDBO(); 
	
		$user = JFactory::getUser();		
		$partner = new partnerByUserId($database);
		if (!$user->guest){
			$partner->load($user->id);
		
		
		
	foreach ($cid as $id){
		
		$query = "DELETE FROM #__easysdi_user_product_favorite WHERE partner_id = $partner->partner_id AND PRODUCT_ID = $id";
					
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<div class='alert'>";			
				echo JText::_($database->getErrorMsg());
				echo "</div>";	
				
				break;									
			}
	}
	
		}else{
			echo "<div class='alert'>";			
				echo JText::_("EASYSDI_ACTION_NOT_ALLOWED");
			echo "</div>";
		
		}
	
	
}

function addFavoriteProduct($cid){
	
	global  $mainframe;
	$database=& JFactory::getDBO(); 

	$user = JFactory::getUser();		
	$partner = new partnerByUserId($database);
	
	if (!$user->guest)
	{
		$partner->load($user->id);
				
		foreach ($cid as $id)
		{
			$query = "INSERT INTO #__easysdi_user_product_favorite VALUES (0,".$partner->partner_id.",".$id.",0)";
						
			$database->setQuery( $query );
			if (!$database->query()) 
			{
				echo "<div class='alert'>";			
				echo JText::_($database->getErrorMsg());
				echo "</div>";	
				
				break;									
			}
		}
	}
	else
	{
		echo "<div class='alert'>";			
			echo JText::_("EASYSDI_ACTION_NOT_ALLOWED");
		echo "</div>";
	}
}	
*/

function searchProducts($orderable = 1){
	$user = JFactory::getUser();		
	
	if (!$user->guest){

		global $mainframe;
		$db =& JFactory::getDBO();
		
		$partner = new partnerByUserId($db);
		$partner->load($user->id);
			
	$language=&JFactory::getLanguage();
	$language->load('com_easysdi');
	$limitstart = JRequest::getVar('limitstart',0);
	$limit = JRequest::getVar('limit',5);
	
	$option = JRequest::getVar('option');
	$task = JRequest::getVar('task');
	$step = JRequest::getVar('step',"1");
	$countMD = JRequest::getVar('countMD');		
	$simpleSearchCriteria  	= JRequest::getVar('simpleSearchCriteria','lastAddedMD');
	$freetextcriteria = JRequest::getVar('freetextcriteria','');
	$freetextcriteria = $db->getEscaped( trim( strtolower( $freetextcriteria ) ) );
	
	
	$cid = JRequest::getVar ('cid', array(0) );
	
	$filter = "";	
	
	//$productList = $mainframe->getUserState('productList');
	$query = "SELECT product_id FROM #__easysdi_user_product_favorite WHERE partner_id = $partner->partner_id ";
	$db->setQuery( $query,$limitstart,$limit);
	$productList = $db->loadResultArray();
					
	
	
	
	if (count($productList)>0){
		$filter = " AND ID NOT IN (";
		foreach( $productList as $id){
		$filter = $filter.$id.",";
		}
		$filter = substr($filter , 0, -1);
		$filter = $filter.")";
	}
	
	if ($freetextcriteria){
		$filter = $filter." AND (DATA_TITLE like '%".$freetextcriteria."%' ";
		$filter = $filter." OR METADATA_ID = '$freetextcriteria')";
	}
		
		$user = JFactory::getUser();
		
		$partner = new partnerByUserId($db);
		if (!$user->guest){
			$partner->load($user->id);
		}else{
			$partner->partner_id = 0;
		}
	
	$filter .= " AND (EXTERNAL=1 OR (INTERNAL =1 AND PARTNER_ID IN (SELECT PARTNER_ID FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id OR root_id = $partner->partner_id))) ";
	$query  = "SELECT COUNT(*) FROM #__easysdi_product p where published=1 and orderable = ".$orderable;
	$query  = $query .$filter ;
	$db->setQuery( $query);
	$total = $db->loadResult();
		
	$query  = "SELECT * FROM #__easysdi_product p where published=1 and  orderable = ".$orderable;			
	$query  = $query .$filter ;
		
	if ($simpleSearchCriteria == "moreConsultedMD"){
				$query  = $query." order by weight";
	}
	if ($simpleSearchCriteria == "lastAddedMD"){
				$query  = $query." order by creation_date";
	}
	if ($simpleSearchCriteria == "lastUpdatedMD"){
				$query  = $query." order by update_date";
	}
		
	
	$db->setQuery( $query,$limitstart,$limit);
	$rows = $db->loadObjectList();
			
	if ($db->getErrorNum()) {
		echo "<div class='alert'>"; 										
		echo 	$db->getErrorMsg();
		echo "</div>";
	}			 
	?>	
	
	<div class="contentin">	
<script>
 function submitOrderForm(){
 	document.getElementById('orderForm').submit();
 }
 </script>		
	<form name="orderForm" id="orderForm" action='<?php echo JRoute::_("index.php") ?>' method='POST'>
	<h2 class="contentheading"><?php echo JText::_("EASYSDI_FAVORITE_TITLE"); ?></h2>
	
	<h3> <?php echo JText::_("EASYSDI_SEARCH_CRITERIA_TITLE"); ?></h3>
		<br>
		<span class="searchCriteria">
			<input name="freetextcriteria" type="text" value="<?php echo $freetextcriteria; ?>"><br>
		 	<input type="radio" name="simpleSearchCriteria" value="lastAddedMD" <?php if ($simpleSearchCriteria == "lastAddedMD") echo "checked";?>> <?php echo JText::_("EASYSDI_LAST_ADDED_MD"); ?><br>
		 	<input type="radio" name="simpleSearchCriteria" value="moreConsultedMD" <?php if ($simpleSearchCriteria == "moreConsultedMD") echo "checked";?>> <?php echo JText::_("EASYSDI_MORECONSULTED_MD"); ?><br>
		 	<input type="radio" name="simpleSearchCriteria" value="lastUpdatedMD" <?php if ($simpleSearchCriteria == "lastUpdatedMD") echo "checked";?> > <?php echo JText::_("EASYSDI_LAST_UPDATED_MD"); ?><br>
	 	</span>
	 	<br>
	 	<button type="submit" class="searchButton" onClick="document.getElementById('task').value='manageFavoriteProduct';submitOrderForm();"> <?php echo JText::_("EASYSDI_SEARCH_BUTTON"); ?></button>
	 	<button type="submit" class="searchButton" onClick="document.getElementById('task').value='addFavoriteProduct';submitOrderForm();"> <?php echo JText::_("EASYSDI_ADD_TO_FAVORITE"); ?></button>
	 	<br>
	 	<br>
	<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>
			 			 
	<input type='hidden' name='option' value='<?php echo $option;?>'>
	<input type='hidden' id ="task" name='task' value='<?php echo $task; ?>'>
	<input type='hidden' id ="fromStep" name='fromStep' value='1'>
	<input type='hidden' id ="step" name='step' value='<?php echo $step; ?>'>
	<input type='hidden'  name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>">
	
	
	<?php $pageNav = new JPagination($total,$limitstart,$limit); ?>
	<span class="searchCriteria">
	<table width="100%">
		<tr>
			<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
			<td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
		</tr>
	</table>
	
	<?php
	$param = array('size'=>array('x'=>800,'y'=>800) );
	JHTML::_("behavior.modal","a.modal",$param);	
	$i=0;	
	
	foreach ($rows  as $row){		
		?>
	<hr>		
	<table width="100%">		
		<tr>		
			<td><img src="./img.gif" width="40" height="40"> </td>
				<td><span class="mdtitle" ><a class="modal" title="<?php echo JText::_("EASYSDI_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" rel="{handler:'iframe',size:{x:500,y:500}}"> <?php echo $row->data_title; ?></a>
					</span>
					<br>
					<span class="mdsupplier" ><?php echo $row->supplier_name;?>
					</span>
					<br>																
				</td>
			<td> 				
			<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" <?php if (in_array($row->id,$cid)) { echo "checked";};?>/></td>				
		</tr>				
	</table>		
		<?php
		$i=$i+1;
	}	
	?>	
		<input type="hidden" name="countMD" value="<?php echo $countMD;?>">				
	</span>	
	</form>
</div>	
	<?php

}else{
	
	echo "<div class='alert'>";			
				echo JText::_("EASYSDI_ACTION_NOT_ALLOWED");
			echo "</div>";
}
}

/*
function listFavoriteProduct($orderable = 1){
	$user = JFactory::getUser();		
	
	if (!$user->guest){

		global $mainframe;
		$db =& JFactory::getDBO();
		
		$partner = new partnerByUserId($db);
		$partner->load($user->id);
			
			
			
	
	$language=&JFactory::getLanguage();
	$language->load('com_easysdi');
	$limitstart = JRequest::getVar('limitstart',0);
	$limit = JRequest::getVar('limit',5);
	
	$option = JRequest::getVar('option');
	$task = JRequest::getVar('task');
	$step = JRequest::getVar('step',"1");
	$countMD = JRequest::getVar('countMD');		
	$simpleSearchCriteria  	= JRequest::getVar('simpleSearchCriteria','lastAddedMD');
	$freetextcriteria = JRequest::getVar('freetextcriteria','');
	$freetextcriteria = $db->getEscaped( trim( strtolower( $freetextcriteria ) ) );
	
	
	$cid = JRequest::getVar ('cid', array(0) );
	
	$filter = "";	
	
	//$productList = $mainframe->getUserState('productList');
	$query = "SELECT product_id FROM #__easysdi_user_product_favorite WHERE partner_id = $partner->partner_id ";
	$db->setQuery( $query,$limitstart,$limit);
	$productList = $db->loadResultArray();
							
	if (count($productList)>0){
		$filter = " AND p.ID IN (";
		foreach( $productList as $id){
		$filter = $filter.$id.",";
		}
		$filter = substr($filter , 0, -1);
		$filter = $filter.")";
	}else $filter = " AND 1=0";
	
	if ($freetextcriteria){
		$filter = $filter." AND (p.DATA_TITLE like '%".$freetextcriteria."%' ";
		$filter = $filter." OR p.METADATA_ID = '$freetextcriteria')";
	}
		
		
	
	$filter .= " AND (p.EXTERNAL=1 OR (p.INTERNAL =1 AND p.PARTNER_ID IN (SELECT PARTNER_ID FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id OR root_id = $partner->partner_id))) ";
	$query  = "SELECT COUNT(*) FROM #__easysdi_product p, #__easysdi_user_product_favorite  f where f.product_id= p.id AND f.partner_id =  $partner->partner_id AND published=1 and orderable = ".$orderable;
	$query  = $query .$filter ;
	$db->setQuery( $query);
	$total = $db->loadResult();
		
	$query  = "SELECT * FROM #__easysdi_product p , #__easysdi_user_product_favorite  f where f.product_id= p.id AND f.partner_id =  $partner->partner_id AND published=1 and  orderable = ".$orderable;			
	$query  = $query .$filter ;
		
	if ($simpleSearchCriteria == "moreConsultedMD"){
				$query  = $query." order by weight";
	}
	if ($simpleSearchCriteria == "lastAddedMD"){
				$query  = $query." order by creation_date";
	}
	if ($simpleSearchCriteria == "lastUpdatedMD"){
				$query  = $query." order by update_date";
	}
		
	
	$db->setQuery( $query,$limitstart,$limit);
	$rows = $db->loadObjectList();
			
	if ($db->getErrorNum()) {
		echo "<div class='alert'>"; 										
		echo 	$db->getErrorMsg();
		echo "</div>";
	}			 
	?>	
	
	<div class="contentin">	
<script>
 function submitOrderForm(){
 	document.getElementById('orderForm').submit();
 }
 </script>		
	<form name="orderForm" id="orderForm" action='<?php echo JRoute::_("index.php") ?>' method='POST'>
	<h2 class="contentheading"><?php echo JText::_("EASYSDI_FAVORITE_TITLE"); ?></h2>
	
	<h3> <?php echo JText::_("EASYSDI_SEARCH_CRITERIA_TITLE"); ?></h3>
		<br>
		<span class="searchCriteria">
			<input name="freetextcriteria" type="text" value="<?php echo $freetextcriteria; ?>"><br>
		 	<input type="radio" name="simpleSearchCriteria" value="lastAddedMD" <?php if ($simpleSearchCriteria == "lastAddedMD") echo "checked";?>> <?php echo JText::_("EASYSDI_LAST_ADDED_MD"); ?><br>
		 	<input type="radio" name="simpleSearchCriteria" value="moreConsultedMD" <?php if ($simpleSearchCriteria == "moreConsultedMD") echo "checked";?>> <?php echo JText::_("EASYSDI_MORECONSULTED_MD"); ?><br>
		 	<input type="radio" name="simpleSearchCriteria" value="lastUpdatedMD" <?php if ($simpleSearchCriteria == "lastUpdatedMD") echo "checked";?> > <?php echo JText::_("EASYSDI_LAST_UPDATED_MD"); ?><br>
	 	</span>
	 	<br>
	 	<button type="submit" class="searchButton" onClick="document.getElementById('task').value='listFavoriteProduct';submitOrderForm();"> <?php echo JText::_("EASYSDI_SEARCH_BUTTON"); ?></button>
	 	<button type="submit" class="searchButton" onClick="document.getElementById('task').value='deleteFavoriteProduct';submitOrderForm();"> <?php echo JText::_("EASYSDI_REMOVE_FROM_FAVORITE"); ?></button>		
	 	
	 	<br>
	 	<br>
	<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>
			 			 
	<input type='hidden' name='option' value='<?php echo $option;?>'>
	<input type='hidden' id ="task" name='task' value='<?php echo $task; ?>'>
	<input type='hidden' id ="fromStep" name='fromStep' value='1'>
	<input type='hidden' id ="step" name='step' value='<?php echo $step; ?>'>
	<input type='hidden'  name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>">
	
	
	<?php $pageNav = new JPagination($total,$limitstart,$limit); ?>
	<span class="searchCriteria">
	<table width="100%">
	<tr><td align="left"><?php echo $pageNav->getPagesCounter(); ?></td><td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
	</tr>
	</table>
	
	<?php
	$param = array('size'=>array('x'=>800,'y'=>800) );
	JHTML::_("behavior.modal","a.modal",$param);	
	$i=0;	
	
	foreach ($rows  as $row){		
		?>
<hr>		
		<table width="100%">
		
		<tr>		
		<td><img src="./img.gif" width="40" height="40"> </td>
		<td>
		<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->product_id; ?>" <?php if (in_array($row->id,$cid)) { echo "checked";};?>/>
		<span class="mdtitle" ><a class="modal" title="<?php echo JText::_("EASYSDI_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" rel="{handler:'iframe',size:{x:500,y:500}}"> <?php echo $row->data_title; ?></a></span><br>
			<span class="mdsupplier" ><?php echo $row->supplier_name;?></span><br>																
		</td>
		<td> 								
		<span title = "<?php echo JText::_("EASYSDI_NOTIFY_WHEN_METADATA_CHANGE")?>" >
		<input type="checkbox" <?php if ( $row->notify_metadata_modification) { echo "checked";};?>  onClick="document.getElementById('productId').value='<?php echo $row->product_id; ?>';if (this.checked){document.getElementById('task').value='addMetadataNotification';} else{document.getElementById('task').value='removeMetadataNotification';}; submitOrderForm();" /></span>
		
		</td>				
		</tr>				
		</table>		
		<?php
		$i=$i+1;
	}	
	?>	
		<input type="hidden" id="productId" name="productId" value="">
		<input type="hidden" name="countMD" value="<?php echo $countMD;?>">				
	</span>	
	</form>
</div>	
	<?php

}else{
	
	echo "<div class='alert'>";			
				echo JText::_("EASYSDI_ACTION_NOT_ALLOWED");
			echo "</div>";
}
}

*/


}
?>