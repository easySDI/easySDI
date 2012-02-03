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

class SITE_favorite 
{
	
	function manageFavoriteProduct ()
	{
		global $mainframe;
		$db =& JFactory::getDBO();
			
		$user = JFactory::getUser();		
		if(!userManager::isUserAllowed($user,"FAVORITE"))
		{
			return;
		}
		
		//Public
		$public = sdilist::getIdByCode('#__sdi_list_visibility','public' );
		
		//Private
		$private = sdilist::getIdByCode('#__sdi_list_visibility','private' );
		
		//Published
		$published = sdilist::getIdByCode('#__sdi_list_metadatastate','published' );
		
		//Get Var
		$account = new accountByUserId($db);
		$account->load($user->id);
		if ($user->guest)
		{
			$account->id = 0;
		}
		
		$language=&JFactory::getLanguage();
		$language->load('com_easysdi');
		$limitstart = JRequest::getVar('limitstart',0);
		$limit = JRequest::getVar('limit',20);
		if($limit == "")
			$limit = 20;
		if($limit == 0)
			$limitstart = 0;
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$simpleSearchCriteria  	= JRequest::getVar('simpleSearchCriteria','');
		
		//load notification product_id
		$query = "SELECT metadata_id FROM #__sdi_favorite WHERE account_id = $account->id  AND  enable_notification=1";
		$db->setQuery( $query);
		$notificationList = $db->loadResultArray();
		
		//Free text filter
		$filter = "";
		$display_internal_orderable = false;
		
		//Load products count, only favorites
		$query  = "SELECT COUNT(*) FROM #__sdi_favorite WHERE account_id = $account->id";
		$query  = $query .$filter ;
		$db->setQuery( $query);
		$total = $db->loadResult();
		
		$myFavRows = "";
		$simpleSearchFilter = "";
	
		//Search criteria filter
		$simpleSearchFilter  = " order by ";
//		if ($simpleSearchCriteria == "moreConsultedMD"){
//					$simpleSearchFilter  = $simpleSearchFilter ."p.weight DESC";
//		}
		if ($simpleSearchCriteria == "lastAddedMD"){
					$simpleSearchFilter  = $simpleSearchFilter ."ov.created DESC";
		}
		if ($simpleSearchCriteria == "lastUpdatedMD"){
					$simpleSearchFilter  = $simpleSearchFilter ."ov.updated DESC";
		}
		if ($simpleSearchCriteria == ""){
					$simpleSearchFilter  = $simpleSearchFilter ."ov.title ASC";
		}
		$query = "SELECT f.*, m.guid as metadata_guid, o.account_id as provider_id, a.name as provider_name, o.name as title
				   FROM #__sdi_favorite f
				   INNER JOIN #__sdi_metadata m on m.id = f.metadata_id
				   INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
				   INNER JOIN #__sdi_object o ON o.id = ov.object_id
				   INNER JOIN #__sdi_account a ON a.id = o.account_id
				   WHERE m.id IN (SELECT metadata_id FROM #__sdi_favorite WHERE account_id = $account->id)";
//		$query  = "SELECT ov.*, o.account_id, m.guid as metadata_guid FROM #__sdi_objectversion ov 
//							   INNER JOIN #__sdi_metadata m ON ov.metadata_id = m.id 
//							   INNER JOIN #__sdi_product p ON p.objectversion_id = ov.id
//							   INNER JOIN #__sdi_object o ON o.id = ov.object_id
//							   INNER JOIN #__sdi_account a ON a.id = o.account_id
//							   WHERE ov.id IN (SELECT objectversion_id FROM #__sdi_favorite WHERE account_id = $account->id)";
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
		
		//TODO : replace with some generic code -----------------------------
		$db->setQuery("SELECT * FROM #__menu where name='GEOCommande'");
		$shopitemId = $db->loadResult();
		if ($db->getErrorNum()) 
		{
			echo "<div class='alert'>";
			echo 	$db->getErrorMsg();
			echo "</div>";
		}
		//-------------------------------------------------------------------
		
		//define an array of orderable associated product for the current user
		$orderableProductsMd = null;
		$filter = "";
		$query = "";
		if($account->id == 0)
		{
			$query  = "SELECT m.id FROM #__sdi_metadata m 
									  INNER JOIN #__sdi_objectversion ov ON m.id = ov.metadata_id 
									  INNER JOIN #__sdi_product p ON p.objectversion_id = ov.id
									  INNER JOIN #__sdi_object o ON o.id = ov.object_id
									  WHERE m.metadatastate_id=$published 
									  AND p.published=1
									  AND p.visibility = $public
									  AND o.visibility_id = $public";
		}
		else
		{
			//User logged, display products according to users's rights
			if(userManager::hasRight($account->id,"REQUEST_EXTERNAL"))
			{
				if(userManager::hasRight($account->id,"REQUEST_INTERNAL"))
				{
					$query  = "SELECT m.id FROM #__sdi_metadata m 
									  INNER JOIN #__sdi_objectversion ov ON m.id = ov.metadata_id 
									  INNER JOIN #__sdi_product p ON p.objectversion_id = ov.id
									  INNER JOIN #__sdi_object o ON o.id = ov.object_id
									  WHERE m.metadatastate_id=$published 
									  AND p.published=1
									  AND (o.visibility_id = $public 
									  		OR (o.visibility_id = $private AND o.id IN (SELECT mo.object_id FROM #__sdi_manager_object mo WHERE mo.account_id = $account->id )
									  										OR o.id IN (SELECT mo.object_id FROM #__sdi_manager_object mo WHERE mo.account_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id ))
									  										OR o.account_id IN (SELECT id FROM #__sdi_account WHERE root_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id ))
									  										OR o.account_id  IN (SELECT id FROM #__sdi_account WHERE root_id = $account->id ) 
									  										OR o.account_id =  $account->id
									  										OR o.account_id IN  (SELECT id FROM #__sdi_account WHERE root_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id ))
									  										))";
//					$query  = "SELECT ov.id FROM #__sdi_objectversion ov 
//									  INNER JOIN #__sdi_product p ON p.objectversion_id = ov.id
//									  INNER JOIN #__sdi_metadata m ON m.id = ov.metadata_id 
//									  INNER JOIN #__sdi_object o ON o.id = ov.object_id
//									  WHERE m.metadatastate_id=$published 
//									  AND p.published=1
//									  AND (o.visibility_id = $public 
//									  		OR (o.visibility_id = $private
//									  			AND (o.id IN (SELECT mo.object_id FROM #__sdi_manager_object mo WHERE mo.account_id = $account->id )
//									  				 OR o.id IN (SELECT mo.object_id FROM #__sdi_manager_object mo WHERE mo.account_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id ))
//									  				 OR o.account_id =  $account->id
////									  				 OR o.account_id IN  (SELECT id FROM #__sdi_account WHERE root_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id ))) ) )";

				}
				else
				{
					$query  = "SELECT m.id FROM #__sdi_metadata m 
									  INNER JOIN #__sdi_objectversion ov ON m.id = ov.metadata_id 
									  INNER JOIN #__sdi_product p ON p.objectversion_id = ov.id
									  INNER JOIN #__sdi_object o ON o.id = ov.object_id
									  WHERE m.metadatastate_id=$published 
									  AND p.published=1
									  AND o.visibility_id = $public";
				}
			}
			else
			{
				if(userManager::hasRight($account->id,"REQUEST_INTERNAL"))
				{
					$query  = "SELECT m.id FROM #__sdi_metadata m 
									  INNER JOIN #__sdi_objectversion ov ON m.id = ov.metadata_id 
									  INNER JOIN #__sdi_product p ON p.objectversion_id = ov.id				  
									  INNER JOIN #__sdi_object o ON o.id = ov.object_id
									  WHERE m.metadatastate_id=$published 
									  AND p.published=1
									  AND (o.visibility_id = $private 
									  		AND (
									  				o.id IN (SELECT mo.object_id FROM #__sdi_manager_object mo WHERE mo.account_id = $account->id )
									  				OR o.id IN (SELECT mo.object_id FROM #__sdi_manager_object mo WHERE mo.account_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id ))
									  				OR o.account_id IN (SELECT id FROM #__sdi_account WHERE root_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id ))
									  				OR o.account_id  IN (SELECT id FROM #__sdi_account WHERE root_id = $account->id )
									  				OR o.account_id =  $account->id
									  				OR o.account_id IN  (SELECT id FROM #__sdi_account WHERE root_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id ))
									  			)
									  	  )";
									
				}
				else
				{
					$query  = "SELECT m.id FROM #__sdi_metadata m 
									  INNER JOIN #__sdi_objectversion ov ON m.id = ov.metadata_id 
									  INNER JOIN #__sdi_product p ON p.objectversion_id = ov.id
									  INNER JOIN #__sdi_object o ON o.id = ov.object_id
									  WHERE m.metadatastate_id=$published AND o.visibility_id = 150";
				}
			}
		}
		$db->setQuery($query);
		$orderableProductsMd = $db->loadResultArray();
		if ($db->getErrorNum()) {						
					echo "<div class='alert'>";			
					echo 			$db->getErrorMsg();
					echo "</div>";
		}
		
		HTML_favorite::manageFavoriteProduct($option,$countMD,$rows,$orderableProductsMd,$notificationList,$total,$limitstart,$limit);
	}
	
	function metadataNotification($is_notify = 0)
	{
		global  $mainframe;
		$database=& JFactory::getDBO(); 
	
		$user = JFactory::getUser();	
		if(!userManager::isUserAllowed($user,"FAVORITE"))
		{
			return;
		}	
		
		$account = new accountByUserId($database);
		$account->load($user->id);
		$favorite_id = JRequest::getVar("favorite_id",0);
		if ($favorite_id == 0)
		{
			echo "<div class='alert'>";			
			echo JText::_("EASYSDI_ERROR_NO_PRODUCT_ID");
			echo "</div>";	
			return;
		}
		
//		$query = "SELECT COUNT(*) FROM  #__sdi_favorite WHERE objectversion_id = $objectversion_id AND account_id  = $account->id";
//		$database->setQuery( $query);
//		$total = $database->loadResult();
//		if($total == 0)
//		{
//			SITE_favorite::favoriteProduct(1);
//		}

		$query = "UPDATE  #__sdi_favorite set  enable_notification = $is_notify WHERE id = $favorite_id AND account_id  = $account->id";
		 
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<div class='alert'>";			
			echo JText::_($database->getErrorMsg());
			echo "</div>";	
			
			break;									
		}
		
		//TODO : do the notification...
	}

	function favoriteProduct($is_favorite = 0)
	{
		global  $mainframe;
		$database=& JFactory::getDBO();
		
		$user = JFactory::getUser();	
	
		if(!userManager::isUserAllowed($user,"FAVORITE"))
		{
			return;
		}
		$account = new accountByUserId($database);
		$account->load($user->id);
	
		$metadata_guid = JRequest::getVar("metadata_guid",0);
		$query = "SELECT id FROM #__sdi_metadata WHERE guid ='$metadata_guid'";
		$database->setQuery( $query );
		$metadata_id = $database->loadResult();
		
		if ($metadata_id == 0)
		{
			echo "<div class='alert'>";			
			echo JText::_("EASYSDI_ERROR_NO_PRODUCT_ID");
			echo "</div>";	
			return;
		}
		if ($is_favorite == 0)
		{
			$query = "DELETE FROM  #__sdi_favorite WHERE metadata_id = $metadata_id AND account_id  = $account->id";
		}
		else
		{
			$query = "INSERT INTO  #__sdi_favorite (metadata_id,account_id) VALUES ($metadata_id,$account->id)";
		}
			 
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<div class='alert'>";			
			echo JText::_($database->getErrorMsg());
			echo "</div>";	
			
			break;									
		}
	}


function searchProducts($orderable = 1){
	$user = JFactory::getUser();		
	
if ($user->guest){
		?>
			<div class="alert"><?php echo JText::_("EASYSDI_ACCOUNT_NOT_CONNECTED");  ?></div>
		<?php
		return;
	}
	if(!usermanager::isEasySDIUser($user))
	{
		?>
			<div class="alert"><?php echo JText::_("EASYSDI_NOT_CONNECTED_AS_EASYSDI_USER");  ?></div>
		<?php
		return;
	}

		global $mainframe;
		$db =& JFactory::getDBO();
		
		$partner = new partnerByUserId($db);
		$partner->load($user->id);
			
	$language=&JFactory::getLanguage();
	$language->load('com_easysdi');
	$limitstart = JRequest::getVar('limitstart',0);
	$limit = JRequest::getVar('limit',5);
	if($limit == "")
		$limit = 5;
	if($limit == 0)
			$limitstart = 0;
	$option = JRequest::getVar('option');
	$task = JRequest::getVar('task');
	$step = JRequest::getVar('step',"1");
	$countMD = JRequest::getVar('countMD');		
	$simpleSearchCriteria  	= JRequest::getVar('simpleSearchCriteria','');
	$freetextcriteria = JRequest::getVar('freetextcriteria','');
	$freetextcriteria = $db->getEscaped( trim( strtolower( $freetextcriteria ) ) );
	$furnisher_id = JRequest::getVar('furnisher_id');
	
	
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
	
	if ($furnisher_id != ""){
		$filter = $filter." AND (p.PARTNER_ID = $furnisher_id)";
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
	if ($simpleSearchCriteria == ""){
				$query  = $query." p.data_title ASC";
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
	<!--<span class="searchCriteria">
	--><table width="100%">
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
				<td><span class="mdtitle" ><a class="modal" title="<?php echo JText::_("EASYSDI_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" rel="{handler:'iframe',size:{x:650,y:600}}"> <?php echo $row->data_title; ?></a>
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
	<!--</span>	
	--></form>
</div>	
	<?php


}}
?>