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
/*foreach($_POST as $key => $val) 
echo '$_POST["'.$key.'"]='.$val.'<br />';*/

defined('_JEXEC') or die('Restricted access');


class SITE_shop {

	function deleteProduct()
	{
		global  $mainframe;
		$db =& JFactory::getDBO();
		$id = JRequest::getVar('prodId');
		$option = JRequest::getVar('option');
		$itemId = JRequest::getVar('Itemid');
		$lang = JRequest::getVar('lang');
		$step = JRequest::getVar('step');
		
		$productList = $mainframe->getUserState('productList');
		$newProductList = array ();
		if (is_array($productList))
		{
			foreach ($productList as $key => $value)
			{
				if ($value != $id)
				{
					$newProductList[]= $value;
				}
				if ($value == $id)
				{
						$query = "SELECT  pd.id as property_id 
							  FROM #__sdi_product_property p, 
							  	   #__sdi_property  as pd,
							  	   #__sdi_property_value as pv   
							  WHERE pv.id = p.propertyvalue_id
							  and   pv.property_id = pd.id
							  and p.product_id = ".$id." group by pd.property_id order by pd.ordering";
					$db->setQuery( $query );
					$rows = $db->loadObjectList();
					
					foreach($rows as $row){
					$property = $mainframe->getUserState($row->property_id.'_text_property_'.$id);
					unset ($property);
					$mainframe->setUserState($row->property_id.'_text_property_'.$id, '');
					
					$property = $mainframe->getUserState($row->property_id.'_textarea_property_'.$id);
					unset ($property);
					$mainframe->setUserState($row->property_id.'_textarea_property_'.$id,'');
					
					$property = $mainframe->getUserState($row->property_id.'_list_property_'.$id);
					unset ($property);
					$mainframe->setUserState($row->property_id.'_list_property_'.$id,'');
					
					$property = $mainframe->getUserState($row->property_id.'_mlist_property_'.$id);
					unset ($property);
					$mainframe->setUserState($row->property_id.'_mlist_property_'.$id,'');
					
					$property = $mainframe->getUserState($row->property_id.'_cbox_property_'.$id);
					unset ($property);
					$mainframe->setUserState($row->property_id.'_cbox_property_'.$id,'');
					}
				}
			}
			
		}
		if(count($newProductList)== 0)
		{
			$mainframe->setUserState('bufferValue',0);
			$mainframe->setUserState('totalArea',0);
			$mainframe->setUserState('perimeter_id','');
			$mainframe->setUserState('order_name','');
			$mainframe->setUserState('third_party','');
			$mainframe->setUserState('order_type','');
			$mainframe->setUserState('previousExtent','');
			
			//Unset the perimeter if no product left in caddy
			$mainframe->setUserState('perimeter_id','');
		}
		$mainframe->setUserState('productList',$newProductList);
		if(count($newProductList)!= 0)
			$mainframe->redirect("index.php?option=$option&view=shop&Itemid=$itemId&step=$step&lang=$lang");
	}

	function orderPerimeter ($cid)
	{
		global  $mainframe;
		$step = JRequest::getVar('step',"2");
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
	
		$db =& JFactory::getDBO(); 
		$query = "select * from #__sdi_basemap where `default` = 1"; 
		$db->setQuery( $query);
		$basemap = $db->loadObjectList();		  
		if ($db->getErrorNum()) {						
					echo "<div class='alert'>";			
					echo 			$db->getErrorMsg();
					echo "</div>";
		}
		
		$query = "select * from #__sdi_basemapcontent where basemap_id = ".$basemap[0]->id." order by ordering"; 
		$db->setQuery( $query);
		$basemap_contents = $db->loadObjectList();
		if ($db->getErrorNum()) {						
					echo "<div class='alert'>";			
					echo 			$db->getErrorMsg();
					echo "</div>";
		}
		
		HTML_shop::orderPerimeter($cid, $basemap, $basemap_contents, $option,$task,$step);
	}

	function orderRecap ($cid,$option){

		global  $mainframe;
		$db =& JFactory::getDBO();

		$query= "SELECT p.*, v.metadata_id as metadata_id , a.name as  supplier_name
					FROM #__easysdi_product p 
					INNER JOIN #__sdi_objectversion v ON v.id = p.objectversion_id
					INNER JOIN #__sdi_object o ON v.object_id = o.id 
					INNER JOIN #__sdi_account a ON o.account_id = a.id
					WHERE p.id in (";
		foreach( $cid as $id )
		{
			$query = $query.$id."," ;
		}
		$query  = substr($query , 0, -1);
		$query = $query.")";
		//$query =  $query . " and orderable = 1";
		$db->setQuery( $query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$db->getErrorMsg();
			echo "</div>";
		}
		?>
<table>
	<thead class='contentheading'>
		<tr>
			<td></td>
			<td><?php echo JText::_("SHOP_SHOP_DATA_IDENTIFICATION");?></td>
			<td><?php echo JText::_("SHOP_SHOP_ORGANISATION_NAME");?></td>
		</tr>
	</thead>
	<tbody>


	<?php
	$i = 0;
	foreach( $rows as $product ){
		?>
		<tr>
			<td><input type="hidden" id="cb<?php echo $i;?>" name="cid[]"
				value="<?php echo $product->id; ?>" /></td>
			<td><a
				href='index.php?option=com_easysdi_core&task=showMetadata&id=<?php echo $product->metadata_id;?>'><?php echo $product->name; ?></a>
			</td>
			<td><a
				href='index.php?option=com_easysdi_core&task=showMetadata&id=<?php echo $product->metadata_id;?>'><?php echo $product->supplier_name; ?></a>
			</td>
		</tr>
		<?php
		$i = $i +1;
	}
	?>
	</tbody>
</table>
	<?php
	}

	function orderProperties($cid){

		global  $mainframe;
		$db =& JFactory::getDBO();

		$step = JRequest::getVar('step',"2");
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$cid = $mainframe->getUserState('productList');
		?>


<script>

var initFields = false;

window.addEvent('domready', function() {
 // select automatically properties that have only one choice
 if(initFields == false){
	var aSel = $('orderForm').getElementsByTagName("select");
	for(var i=0;i<aSel.length;i++){
		if(aSel[i].options.length == 3){
			aSel[i].options[2].selected = true;
		}
	}
	initFields = true;
 }
});


 function submitOrderForm(){
 	document.getElementById('orderForm').submit();
 }
 </script>
<form name="orderForm" id="orderForm"
	action='<?php echo JRoute::_("index.php") ?>' method='POST'><?php


	$query= "SELECT p.*, o.account_id as supplier_id FROM #__sdi_product p 
						INNER JOIN #__sdi_objectversion v ON v.id = p.objectversion_id
						INNER JOIN #__sdi_object o ON o.id = v.object_id
						WHERE p.id in (";
	foreach( $cid as $id ) {
		$query = $query.$id."," ;
	}
	$query  = substr($query , 0, -1);
	$query = $query.")";
	$db->setQuery( $query);
	$products = $db->loadObjectList();
	if ($db->getErrorNum()) {
		echo "<div class='alert'>";
		echo 			$db->getErrorMsg();
		echo "</div>";
	}


	$language =& JFactory::getLanguage();
	foreach ($products as $product  ){
		$query = "SELECT DISTINCT pd.id as id, 
							pd.ordering as property_order, 
							pd.mandatory as mandatory, 
							t.label as property_text, 
							pd.type as type, 
							pd.id as property_id							
				  FROM #__sdi_language l, 
				  		#__sdi_list_codelang cl,
				  		#__sdi_product_property p 
				  INNER JOIN #__sdi_property_value pvd ON p.propertyvalue_id=pvd.id 
				  INNER JOIN #__sdi_property pd ON pvd.property_id=pd.id 
				  LEFT OUTER JOIN #__sdi_translation t ON pd.guid=t.element_guid 
				  WHERE p.product_id=".$product->id." 
				  AND t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$language->_lang."' 
				  AND pd.published=1 
				  AND (pd.account_id = 0 OR pd.account_id = ".$product->supplier_id." ) 
				  ORDER by property_order";
?>

<br/>
<fieldset id="fieldset_properties" class="fieldset_properties"><legend><?php echo JText::_($product->name);?></legend>
<?php
$db->setQuery( $query );
$rows = $db->loadObjectList();
		
?>
<ol class="product_proprety_row_list">

<?php

if (count($rows)>0){
	foreach ($rows as $row){
		echo "<li>";
		$query="SELECT pd.mandatory as mandatory, 
								pvd.ordering as value_order, 
								pvd.name as value_text,
								t.label as value_trans, 
								pvd.id as value
							FROM #__sdi_language l, 
			  					#__sdi_list_codelang cl,
			  					#__sdi_product_property p 
							INNER JOIN #__sdi_property_value pvd on p.propertyvalue_id=pvd.id 
							INNER JOIN #__sdi_property pd on pvd.property_id=pd.id 
							LEFT OUTER JOIN #__sdi_translation t ON pvd.guid=t.element_guid 
							WHERE p.product_id=".$product->id." 
							AND t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$language->_lang."' 
							AND pd.published=1 
							AND pd.id=".$row->id." 
							ORDER BY value_order";
		
		switch($row->type){
			case "list":
				$db->setQuery( $query );
				$rowsValue = $db->loadObjectList();
				$isMandatoryClass = $row->mandatory == 1 ?  " mdtryElem" : "";
				echo "<select class=\"product_proprety_row_list$isMandatoryClass\" 
							  name='".$row->property_id."_list_property_".$product->id."[]' 
							  id='".$row->property_id."_list_property_".$product->id."[]'>";
				//add a default option that replaces the text before the list, value = -1 to be sure to not interfer with pvd.id.
				echo "<option value='-1'>-". JText::_($row->property_text)."-</option>";
				//echo "<option></option>";
				foreach ($rowsValue as $rowValue){
					$selProduct = $mainframe->getUserState($row->property_id.'_list_property_'.$product->id);
					$selected = "";
					if ( is_array($selProduct)){
						if (in_array($rowValue->value,$selProduct)) $selected ="selected";
					}
					echo "<option ".$selected." value='".$rowValue->value."'>". JText::_($rowValue->value_trans)."</option>";
				}
				echo "</select>";
				if($rowValue->mandatory == 1) echo "<span class=\"mdtyProperty\">*</span>"; else echo "&nbsp;";
				break;
			case "mlist":
				echo "<div id='".$row->property_id."_mlist_property_".$product->id."[]_label'>".JText::_($row->property_text).":</div>";
//				$query="SELECT pd.mandatory as mandatory, 
//								pvd.order as value_order, 
//								pvd.text as value_text, 
//								pvd.id as value, 
//								pvd.translation as val_trans 
//							FROM #__easysdi_product_property p 
//							inner join #__easysdi_product_properties_values_definition pvd on p.property_value_id=pvd.id 
//							inner join #__easysdi_product_properties_definition pd on pvd.properties_id=pd.id 
//							where p.product_id=".$product->id." 
//							and pd.published=1 
//							and pd.id=".$row->id." 
//							order by value_order";
				$db->setQuery( $query );
				$rowsValue = $db->loadObjectList();
				$isMandatoryClass = $row->mandatory == 1 ?  " mdtryElem" : "";
				echo "<table><tr><td>";
				echo "<select class=\"product_proprety_row_list$isMandatoryClass\" 
						      multiple size='".count($rowsValue)."' 
						      name='".$row->property_id."_mlist_property_".$product->id."[]' 
						      id='".$row->property_id."_mlist_property_".$product->id."[]'>";
				foreach ($rowsValue as $rowValue){
					$selProduct = $mainframe->getUserState($row->property_id.'_mlist_property_'.$product->id);
					$selected = "";
					if ( is_array($selProduct)){
						if (in_array($rowValue->value,$selProduct)) $selected ="selected";
					}
					echo "<option ".$selected." value='".$rowValue->value."'>". JText::_($rowValue->value_trans)."</option>";
				}
				echo "</select>";
				echo "</td><td>";
				if($rowValue->mandatory == 1) echo "<div class=\"mdtyProperty\">*</div>"; else echo "&nbsp;";
				echo "</td></tr></table>";
				break;
			case "cbox":
				$html = "";
//				$query="SELECT pd.mandatory as mandatory, 
//								pvd.order as value_order, 
//								pvd.text as value_text, 
//								pvd.id as value, 
//								pvd.translation as val_trans 
//						FROM #__easysdi_product_property p 
//						inner join #__easysdi_product_properties_values_definition pvd on p.property_value_id=pvd.id 
//						inner join #__easysdi_product_properties_definition pd on pvd.properties_id=pd.id 
//						where p.product_id=".$product->id." 
//						and pd.published=1 
//						and pd.id=".$row->id." 
//						order by value_order";
				$db->setQuery( $query );
				$rowsValue = $db->loadObjectList();
				$isMandatoryClass = $row->mandatory == 1 ?  "mdtryElem" : "";
				$html .= "<div class=\"product_proprety_cbox_group\">";
				foreach ($rowsValue as $rowValue){
					$selProduct = $mainframe->getUserState($row->property_id.'_cbox_property_'.$product->id);
					$selected = "";
					if ( is_array($selProduct)){
						if (in_array($rowValue->value,$selProduct)) $selected ="checked";
					}
					
					$html .= "&nbsp;&nbsp;<input type='checkbox' 
											class='".$isMandatoryClass."' 
											name='".$row->property_id."_cbox_property_".$product->id."[]' 
											id='".$row->property_id."_cbox_property_".$product->id."[]' ".$selected." 
											value='".$rowValue->value."'/>&nbsp;".JText::_($rowValue->value_trans)."&nbsp;";
					$html .="<br/>";
				}
				$html .= "</div>";
				echo "<span id='".$row->property_id."_cbox_property_".$product->id."[]_label'>".JText::_($row->property_text).": "."</span>";
				if($rowValue->mandatory == 1) 
					echo "<span id='".$row->property_id."_cbox_property_".$product->id."[]_group' class=\"mdtyProperty\">*</span><br/>";
				echo $html;
				break;
			case "text":
				echo "<div id='".$row->property_id."_text_property_".$product->id."_label'>".JText::_($row->property_text).":</div>";
//				$query="SELECT pd.mandatory as mandatory, 
//							pvd.order as value_order, 
//							pvd.text as value_text, 
//							pvd.id as value, 
//							pvd.translation as val_trans 
//						FROM #__easysdi_product_property p
//						 inner join #__easysdi_product_properties_values_definition pvd on p.property_value_id=pvd.id 
//						 inner join #__easysdi_product_properties_definition pd on pvd.properties_id=pd.id 
//						 where p.product_id=".$product->id." 
//						 and pd.published=1 
//						 and pd.id=".$row->id." 
//						 order by value_order";
				$db->setQuery( $query );
				$rowsValue = $db->loadObjectList();
				$isMandatoryClass = $row->mandatory == 1 ?  " mdtryElem" : "";
				foreach ($rowsValue as $rowValue){
					$valueText = $mainframe->getUserState($row->property_id.'_text_property_'.$product->id);

					if ($valueText){
						$selected = $valueText;
					}else{
						$selected = "";
						//$selected = $rowValue->val_trans;
					}
					echo "<table><tr><td>";
					//value: 
					echo "<input class=\"msgProperty$isMandatoryClass\" 
								 type='text' 
								 name='".$row->property_id."_text_property_".$product->id."' 
								 id='".$row->property_id."_text_property_".$product->id."' 
								 value='".JText::_($selected)."' />";
					echo "</td><td>";
					if($rowValue->mandatory == 1) echo "<div class=\"mdtyProperty\">*</div>"; else echo "&nbsp;";
					echo "</td></tr></table>";
					break;
				}
				break;
				
				
			case "textarea":
				echo "<div id='".$row->property_id."_textarea_property_".$product->id."[]_label'>".JText::_($row->property_text).":</div>";
//				$query="SELECT pd.mandatory as mandatory, 
//								pvd.order as value_order, 
//								pvd.text as value_text, 
//								pvd.id as value, 
//								pvd.translation as val_trans 
//							FROM #__easysdi_product_property p 
//							inner join #__easysdi_product_properties_values_definition pvd on p.property_value_id=pvd.id 
//							inner join #__easysdi_product_properties_definition pd on pvd.properties_id=pd.id 
//							where p.product_id=".$product->id." 
//							and pd.published=1 
//							and pd.id=".$row->id." 
//							order by value_order";
				$db->setQuery( $query );
				$rowsValue = $db->loadObjectList();
				$isMandatoryClass = $row->mandatory == 1 ?  "mdtryElem" : "";
				
			foreach ($rowsValue as $rowValue){
					$selProduct = $mainframe->getUserState($row->property_id.'_textarea_property_'.$product->id);
									
					if (count($selProduct)>0){
						$selected = $selProduct[0];
					}else{
						$selected = "";
						//$selected = $rowValue->val_trans;
					}
					echo "<table><tr><td>";
					echo "<TEXTAREA class='".$isMandatoryClass."' 
							rows=3 COLS=62 
							name='".$row->property_id."_textarea_property_".$product->id."[]' 
							id='".$row->property_id."_textarea_property_".$product->id."[]'>".JText::_($selected)."</textarea>";
					echo "</td><td>";
					if($rowValue->mandatory == 1) echo "<div class=\"mdtyProperty\">*</div>"; else echo "&nbsp;";
					echo "</td></tr></table>";
					break;
				}
				break;
			case "message":
				echo JText::_(trim($row->property_text));
//				$query="SELECT pd.mandatory as mandatory, 
//							   pvd.order as value_order, 
//							   pvd.text as value_text, 
//							   pvd.id as value, 
//							   pvd.translation as val_trans 
//					    FROM #__easysdi_product_property p 
//						inner join #__easysdi_product_properties_values_definition pvd on p.property_value_id=pvd.id 
//						inner join #__easysdi_product_properties_definition pd on pvd.properties_id=pd.id 
//					    where p.product_id=".$product->id." 
//						and pd.published=1 
//						and pd.id=".$row->id." 
//						order by value_order";
				
				$db->setQuery( $query );
				$rowsValue = $db->loadObjectList();
				foreach ($rowsValue as $rowValue){
					$selected = $rowValue->val_trans;
					?>
					<div class="product_proprety_message_title">
					<?php 
					echo JText::_($selected);
					?>
					<input type='hidden' name='<?php echo $row->property_id; ?>_message_property_<?php echo $product->id; ?>' id='<?php echo $row->property_id; ?>_message_property_<?php echo $product->id; ?>' value='<?php echo $selected;?>'></input>
					</div>
					<?php 
					break;
				}

				break;
		}
		echo "</li>";
	}

	//echo "</select>";
	//echo "</fieldset>";

}
?>
</ol>
</fieldset>
<?php

	}
	?> 
	<input type='hidden' id="fromStep" name='fromStep' value='3'> 
	<input type='hidden' id="step" name='step' value='<?php echo $step; ?>'> 
	<input type='hidden' id="option" name='option' value='<?php echo $option; ?>'>
	<input type='hidden' id="task" name='task' value='<?php echo $task; ?>'>
	<input type='hidden' id="view" name='view' value='<?php echo JRequest::getVar('view'); ?>'>
	<input type='hidden' id="totalArea" name='totalArea' value='<?php echo JRequest::getVar('totalArea'); ?>'> 
	<input type='hidden' name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>">
	
	
</form>
	<?php

	}

	function orderDefinition($cid){
		global  $mainframe;
		$db =& JFactory::getDBO();
		$step = JRequest::getVar ('step',4 );
		$option = JRequest::getVar ('option' );
		$task = JRequest::getVar ('task' );
		$user = JFactory::getUser();
		
		if (!$user->guest){
			?>
			<div class="info"><?php echo JText::_("SHOP_SHOP_MESSAGE_CONNECTED_WITH_USER").$user->name;  ?></div>
			<?php
		}
		?>
<script>
 function submitOrderForm(){
	 if(document.getElementById('step').value <= document.getElementById('fromStep').value)
	 {
	 	//Step back in the tab, don't need to check the values filled in the form
	 	
	 }
	 else
	 {
		 if (!document.getElementById('order_type_d').checked && !document.getElementById('order_type_o').checked){
		 
		 	alert("<?php echo JText::_("SHOP_SHOP_MESSAGE_ORDER_TYPE_NOT_FILL") ?>");
		 	return;
		 }
		 if (document.getElementById('order_name').value.length == 0){
		 
		 	alert("<?php echo JText::_("SHOP_SHOP_MESSAGE_ORDER_NAME_NOT_FILL") ?>");
		 	return;
		 }
		 //Limit order name to 40 characters
		 if(document.getElementById('order_name').value.length > 40){
			alert('<?php echo JText::_("SHOP_SHOP_MESSAGE_ORDER_NAME_TOO_LONG"); ?>');
			return;
		}
	 }
 	document.getElementById('orderForm').submit();
 }
 </script>
 <form name="orderForm" id="orderForm" action='<?php echo JRoute::_("index.php") ?>' method='GET'>
	<input type='hidden' id="fromStep" name='fromStep' value='4'> 
	<input type='hidden' id="step" name='step' value='<?php echo $step; ?>'> 
	<input type='hidden' id="option" name='option' value='<?php echo $option; ?>'>
	<input type='hidden' id="task" name='task' value='order'> 
	<input type='hidden' name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>">
	<table class="ordersteps">
		<tr>
			<td class="orderstepsTitle"><?php echo JText::_("SHOP_ORDER_NAME"); ?>:</td>
			<td><input type="text" class="infoClient" name="order_name" id="order_name" value="<?php echo $mainframe->getUserState('order_name'); ?>"></td>
		</tr>
		<tr>
			<td colspan="2"><div class="separator" /></td>
		</tr>
		<tr>
			<td class="orderstepsTitle"><?php echo JText::_("SHOP_ORDER_TYPE_D"); ?>:</td>
			<td><input type="radio" name="order_type" id="order_type_d" value="D" <?php if ("D" == $mainframe->getUserState('order_type')) echo "checked"; ?>></td>
		</tr>
		<tr>
			<td class="orderstepsTitle"><?php echo JText::_("SHOP_ORDER_TYPE_O"); ?>:</td>
			<td><input type="radio" name="order_type" id="order_type_o" value="O" <?php if ("O" == $mainframe->getUserState('order_type')) echo "checked"; ?>></td>
		</tr>
		<tr>
			<td colspan="2"><div class="separator" /></td>
		</tr>
	
	<?php

	if ($user->guest ){
		?>
		<tr>
			<td class="orderstepsTitle"><?php echo JText::_("SHOP_SHOP_MESSAGE_USER_NOT_CONNECTED"); ?>:</td>
		</tr>
		<tr>
			<td class="orderstepsTitle"><?php echo JText::_("SHOP_AUTH_USER"); ?>:</td>
			<td><input type="text" class="infoClient" name="user" value=""></td>
		</tr>
		<tr>
			<td class="orderstepsTitle"><?php echo JText::_("SHOP_AUTH_PASSWORD"); ?>:</td>
			<td><input type="password" class="infoClient" name="password" value=""></td>
		</tr>
		<?php
	}
	
	
		$query = "select a.id as account_id, j.name as name 
					from #__sdi_account a, #__sdi_actor b, #__sdi_list_role c, #__users as j 
					where c.code = 'TIERCE' and c.id = b.role_id AND a.id = b.account_id and a.user_id = j.id and a.root_id is null ORDER BY name";
		$db->setQuery( $query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";
     ?>  
		<tr>
			<td colspan="2"><div class="separator" /></td>
		</tr>
    <?php
		}
    ?>	

		<tr>
			<td class="orderstepsTitle"><?php echo JText::_("SHOP_SHOP_ORDER_THIRD_PARTY"); ?>:</td>
			<td>
				<select class="thirdPartySelect" name="third_party">
					<option value="0"><?php echo JText::_("SHOP_SHOP_ORDER_FOR_NOBODY"); ?></option>
					<?php
					$third_party = $mainframe->getUserState('third_party');
					echo $third_party;
					foreach ($rows as $row){
						$selected="";
						if ($third_party == $row->account_id) $selected="selected";
						echo "<option ".$selected." value=\"".$row->account_id."\">".$row->name."</option>";
					}
					?>
				</select>
			</td>
		</tr>
	</table>
</form>

			<!--
      //Call here the include content item plugin, or a specific article.
			//Insert into the EasySDI config a key SHOP_ARTICLE_STEP4 with
			//the value like {include_content_item 148} refering to the plugin and
			//article you would like to call.
			//-->       
		
			<table width="100%" id="infoStep4">
			<?php

			$row->text = config_easysdi::getValue("SHOP_ARTICLE_STEP4");
			$args = array( 1,&$row,&$params);
			JPluginHelper::importPlugin( 'content' );
			$dispatcher =& JDispatcher::getInstance();
			//$params = & new JParameter('');
			$results = $dispatcher->trigger('onPrepareContent', 
			array(&$row,&$params,0));
			
			echo $row->text;
			
			
			?>
		        </table>



<?php


	}

	function orderSend($cid){

		global $mainframe;
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$step = JRequest::getVar('step',5);
		$db =& JFactory::getDBO();

		$user = JFactory::getUser();
		$account = new accountByUserId( $db );
		$account->load( $user->id );


		?>
<div class="contentin">
<br>
<br>
<script>
 function submitOrderForm(){
 	document.getElementById('orderForm').submit();
 }
 </script>
<form id="orderForm" name="orderForm"
	action="<?php echo JRoute::_("index.php"); ?>">
	<input type='hidden'
	id="fromStep" name='fromStep' value='5'> <input type="hidden"
	name="task" id="taskOrderForm" value="order"> <input type="hidden"
	name="option" value="<?php echo JRequest::getVar('option'); ?>"> <input
	type='hidden' id="step" name='step' value='<?php echo $step; ?>'> 
	<input
	type='hidden' name='Itemid'
	value="<?php echo  JRequest::getVar ('Itemid' );?>"></form>

		<?php
		if (!$user->guest){
			
			//Check the user rights and the product accessibilitty
			$isProductAllowed = true;
			$hasExternal = false;
			$hasInternal = false;
			
			//Public
			$queryVisibility = "select id from #__sdi_list_visibility where code ='public'";
			$db->setQuery($queryVisibility);
			$public = $db->loadResult();
			//Private
			$queryVisibility = "select id from #__sdi_list_visibility where code ='private'";
			$db->setQuery($queryVisibility);
			$private = $db->loadResult();
			
			if(userManager::hasRight($account->id,"REQUEST_EXTERNAL"))
			{
				$hasExternal = true;
			}
			if(userManager::hasRight($account->id,"REQUEST_INTERNAL"))
			{
				$hasInternal = true;
			}
			
			$cid = $mainframe->getUserState('productList');
			$listId = '';
			foreach ($cid as $productId)
			{
				$listId = $listId.$productId.',';
			}
			$listId = substr($listId,0,strlen($listId) -1);
			
			$query = "SELECT * from #__sdi_product WHERE id IN($listId) ";
			$db->setQuery( $query);
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row)
			{
				if($row->published == '0' ||  ($row->visibility_id <> $public && $row->visibility_id <> $private))
				{
					SITE_shop::displayErrorMessage("SHOP_SHOP_MESSAGE_ORDER_PROBLEM_PRODUCT_NOT_ORDERABLE", $row->name );
					$isProductAllowed = false;
					break;
				}
				
				$query = "SELECT COUNT(*) FROM #__sdi_product p 
								INNER JOIN #__sdi_objectversion v ON v.id = p.objectversion_id
								INNER JOIN #__sdi_object o ON o.id = v.object_id
								INNER JOIN #__sdi_metadata m ON m.id = v.metadata_id
								WHERE p.published=1 AND o.published = 1
								AND p.id = $row->id
								AND
								 (o.account_id =  $account->id
								OR
								o.account_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id )
								OR 
								o.account_id IN (SELECT id FROM #__sdi_account WHERE root_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id ))
								OR
								o.account_id IN (SELECT id FROM #__sdi_account WHERE root_id = $account->id ))" ;
						$db->SetQuery($query);
						$countProduct = $db->loadResult();
				if($row->visibility_id == $private)
				{	
					//User needs to belong to the product's partner group
					if($countProduct == 1 && $hasInternal == true )
					{
						//The product belongs to the current user's group and the user has the internal right
					}
					else
					{
						//The product does not belong to the current user's group, or the user does not have the internal right
						SITE_shop::displayErrorMessage("SHOP_SHOP_MESSAGE_ORDER_PROBLEM_USER_RIGHT", $row->data_title );
						$isProductAllowed = false;
						break;
					}		
					
				}
				else if($row->visibility_id == $public)
				{
					if($hasExternal == false)
					{
						//User does not have the right to order external product
						SITE_shop::displayErrorMessage("SHOP_SHOP_MESSAGE_ORDER_PROBLEM_USER_RIGHT", $row->data_title );
						$isProductAllowed = false;
						break;
					}
				}
				else
				{
					//Product is not orderable
					SITE_shop::displayErrorMessage("SHOP_SHOP_MESSAGE_ORDER_PROBLEM_USER_RIGHT", $row->data_title );
					$isProductAllowed = false;
					break;
				}
				
			}
			
			
			if($isProductAllowed == true)
			{
			//	
			//Call here the include content item plugin, or a specific article.
			//Insert into the EasySDI config a key SHOP_ARTICLE_STEP5 with
			//the value like {include_content_item 148} refering to the plugin and
			//article you would like to call.
			//
			?>
			<table width="100%" id="generalConditions">
			<?php
			$row->text = config_easysdi::getValue("SHOP_ARTICLE_STEP5");
			$args = array( 1,&$row,&$params);
			JPluginHelper::importPlugin( 'content' );
			$dispatcher =& JDispatcher::getInstance();
			//$params = & new JParameter('');
			$results = $dispatcher->trigger('onPrepareContent', 
			array(&$row,&$params,0));
			
			echo $row->text;
			
			?>
		        </table>
				<input
				onClick="document.getElementById('taskOrderForm').value = 'saveOrder';submitOrderForm();"
				type="button"
				class="button"
				value='<?php echo JText::_("SHOP_SHOP_ORDER_SAVE_BUTTON"); ?>'> <input
				onClick="document.getElementById('taskOrderForm').value = 'sendOrder';submitOrderForm();"
				type="button"
				class="button"
				value='<?php echo JText::_("SHOP_SHOP_ORDER_SEND_BUTTON"); ?>'> <?php
			}
		}
		else
		{
			//User not connected
			?>
			<div class="alert"><?php echo JText::_("SHOP_SHOP_MESSAGE_NOT_CONNECTED");?></div>
			<?php
		}
		?></div>
		<?php
	}

	function displayErrorMessage ($error, $product)
	{
		?>
		<div class="alert">
			<span class="alertheader"><?php echo JText::_("SHOP_SHOP_MESSAGE_ORDER_PROBLEM_IN_ORDER");?><br></span>
			<?php echo JText::_($error);?>
			<span class="alerthighlight"><?php echo $product;?><br></span>
			<?php echo JText::_("SHOP_SHOP_MESSAGE_ORDER_PROBLEM_ACTION");?><br>
		</div>
		<?php 
	}
	
	function saveOrder($orderStatus){
		global $mainframe;
		$db =& JFactory::getDBO();
		$user = JFactory::getUser();
		$account = new accountByUserId( $db );
		$account->load( $user->id );
		
		if (!$user->guest)
		{
			$order_id = $mainframe->getUserState('order_id');
			if($order_id)
			{
				//If order_id exists, this is an update of an existing draft order
				//Delete existing order and then insert the new one
				$Order = new order( $db );
				$Order->load( $order_id);
				if ($Order->id == 0)
				{
					echo "<div class='alert'>";			
					echo JText::_("SHOP_SHOP_MESSAGE_DELETE_ORDER").$Order->id;
					echo "</div>";
				}
				else 
				{
					if (!$Order->delete()) 
					{
						$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
						return;
					}
				}									
			}
			
			$cid = $mainframe->getUserState('productList');
			$order_status_value = $orderStatus;
			$option = JRequest::getVar('option');
			$task = JRequest::getVar('task');
			$order_type = $mainframe->getUserState('order_type');
			$order_type_code = $mainframe->getUserState('order_type');
			$order_name = $mainframe->getUserState('order_name');
			$third_party = $mainframe->getUserState('third_party');
			$bufferValue = $mainframe->getUserState('bufferValue');
			$totalArea = $mainframe->getUserState('totalArea');
	
			$orderStatus = sdilist::getIdByCode('#__sdi_list_orderstatus',$orderStatus);
			$order_type = sdilist::getIdByCode('#__sdi_list_ordertype',$order_type);
			$await_type = sdilist::getIdByCode('#__sdi_list_productstatus','AWAIT');
			$available_type = sdilist::getIdByCode('#__sdi_list_productstatus','AVAILABLE');
			
			if( $bufferValue == '')
			{
				$bufferValue = 0;
			}
			if( $totalArea == '')
			{
				$totalArea = 0;
			}

			$order = new order ($db);
			$order->type_id=$order_type;
			$order->status_id=$orderStatus;
			$order->user_id=$user->id;
			$order->thirdparty_id=$third_party;
			$order->buffer=$bufferValue;
			$order->surface = $totalArea;
			$order->name=addslashes($order_name);
			//If the order is "SENT" update the 'sent' value
			if($order_status_value == "SENT")
			{
				$order->sent =date('Y-m-d H:i:s');
				//$order->responsesent =1;
			}
			if(!$order->store())
			{
				echo "<div class='alert'>";
				echo $db->getErrorMsg();
				echo "</div>";
				return;
			}
			$order_id	= $db->insertid();
			
			$perimeter_id = $mainframe->getUserState('perimeter_id');
			$selSurfaceList = $mainframe->getUserState('selectedSurfaces');
			$selSurfaceListName = $mainframe->getUserState('selectedSurfacesName');

			$i=0;
			foreach ($selSurfaceList as $sel)
			{
				$order_perimeter = new orderPerimeter($db);
				$order_perimeter->order_id=$order_id;
				$order_perimeter->perimeter_id=$perimeter_id;
				$order_perimeter->value=$sel;
				$order_perimeter->text=addslashes($selSurfaceListName[$i]);
				if(!$order_perimeter->store())
				{
					echo "<div class='alert'>";
					echo $db->getErrorMsg();
					echo "</div>";
					return;
				}
				$i++;
			}

			foreach ($cid as $product_id)
			{
				if ($product_id != "0")
				{				
					$order_product = new orderProduct($db)	;
					$order_product->product_id=$product_id;
					$order_product->order_id=$order_id;
					$order_product->status_id=$await_type;
					if(!$order_product->store())
					{
						echo "<div class='alert'>";
						echo $db->getErrorMsg();
						echo "</div>";
						return;
					}

					$order_product_list_id = $db->insertid();
					$query = "SELECT DISTINCT a.code as code, 
											  a.id as property_id 
								FROM #__sdi_product_property b, 
									 #__sdi_property  as a ,
									 #__sdi_property_value as c  
								WHERE a.id = c.property_id 
								and b.propertyvalue_id = c.id 
								and b.product_id = ". $product_id." 
								order by a.ordering";
					$db->setQuery( $query );
					
					$rows = $db->loadObjectList();
					
					foreach($rows as $row)
					{
						$productProperties  = $mainframe->getUserState($row->property_id."_list_property_".$product_id);
						if (count($productProperties)>0)
						{
							$mainframe->setUserState($row->property_id.'_list_property_'.$product_id,null);
							foreach ($productProperties as $propertyvalue_id)
							{
								$orderProductProperty = new orderProductProperty($db)	;
								$orderProductProperty->orderproduct_id=$order_product_list_id;
								$orderProductProperty->propertyvalue_id=$propertyvalue_id;
								$orderProductProperty->property_id=$row->property_id;
								if(!$orderProductProperty->store())
								{
									echo "<div class='alert'>";
									echo $db->getErrorMsg();
									echo "</div>";
									return;
								}
							}
						}
	
						$productProperties  = $mainframe->getUserState($row->property_id."_mlist_property_".$product_id);
						if (count($productProperties)>0)
						{
							$mainframe->setUserState($row->property_id.'_mlist_property_'.$product_id,null);
							foreach ($productProperties as $propertyvalue_id)
							{
								$orderProductProperty = new orderProductProperty($db)	;
								$orderProductProperty->orderproduct_id=$order_product_list_id;
								$orderProductProperty->propertyvalue_id=$propertyvalue_id;
								$orderProductProperty->property_id=$row->property_id;
								if(!$orderProductProperty->store())
								{
									echo "<div class='alert'>";
									echo $db->getErrorMsg();
									echo "</div>";
									return;
								}
							}
						}
						
						$productProperties  = $mainframe->getUserState($row->property_id."_cbox_property_".$product_id);
						if (count($productProperties)>0)
						{
							$mainframe->setUserState($row->property_id.'_cbox_property_'.$product_id,null);
							foreach ($productProperties as $propertyvalue_id)
							{
								$orderProductProperty = new orderProductProperty($db)	;
								$orderProductProperty->orderproduct_id=$order_product_list_id;
								$orderProductProperty->propertyvalue_id=$propertyvalue_id;
								$orderProductProperty->property_id=$row->property_id;
								if(!$orderProductProperty->store())
								{
									echo "<div class='alert'>";
									echo $db->getErrorMsg();
									echo "</div>";
									return;
								}
							}
						}
						
						$productProperties  = $mainframe->getUserState($row->property_id."_text_property_".$product_id);
						if ($productProperties != '')
						{
							$mainframe->setUserState($row->property_id.'_text_property_'.$product_id,null);
								$orderProductProperty = new orderProductProperty($db)	;
								$orderProductProperty->orderproduct_id=$order_product_list_id;
								$orderProductProperty->propertyvalue=$productProperties;
								$orderProductProperty->property_id=$row->property_id;
								if(!$orderProductProperty->store())
								{
									echo "<div class='alert'>";
									echo $db->getErrorMsg();
									echo "</div>";
									return;
								}
						}
						
						$productProperties  = $mainframe->getUserState($row->property_id."_message_property_".$product_id);
						if ($productProperties != '')
						{
							$mainframe->setUserState($row->property_id.'_message_property_'.$product_id,null);
								$orderProductProperty = new orderProductProperty($db)	;
								$orderProductProperty->orderproduct_id=$order_product_list_id;
								$orderProductProperty->propertyvalue=$productProperties;
								$orderProductProperty->property_id=$row->property_id;
								if(!$orderProductProperty->store())
								{
									echo "<div class='alert'>";
									echo $db->getErrorMsg();
									echo "</div>";
									exit;
								}
						}
						
						$productProperties  = $mainframe->getUserState($row->property_id."_textarea_property_".$product_id);
						if (count($productProperties)>0)
						{
							$mainframe->setUserState($row->property_id.'_textarea_property_'.$product_id,null);
						
							foreach ($productProperties as $propertyvalue_id)
							{
								$orderProductProperty = new orderProductProperty($db)	;
								$orderProductProperty->orderproduct_id=$order_product_list_id;
								$orderProductProperty->propertyvalue=$propertyvalue_id;
								$orderProductProperty->property_id=$row->property_id;
								if(!$orderProductProperty->store())
								{
									echo "<div class='alert'>";
									echo $db->getErrorMsg();
									echo "</div>";
									exit;
								}
							}
						}					
					}
				}
			}
			
			
			//If the order status is "SENT", notify the distribution manager
			// that a new query exists 
			if($order_status_value == "SENT")
			{
				SITE_cpanel::notifyOrderToDiffusion($order_id);
			}

			$sent = sdilist::getIdByCode('#__sdi_list_orderstatus','SENT' );
			
			/* Met à jour le status pour un devis dont le prix est connu comme étant gratuit 
				et envoi un mail pour dire qu'un devis sur la donnée gratuite à été demandé*/
			$query = "SELECT o.name as cmd_name,
							 u.email as email , 
							 p.id as product_id, 
							 p.name as data_title   
					  FROM #__users u,
					  	   #__sdi_account pa, 
					  	   #__sdi_order_product opl , 
					  	   #__sdi_product p,
					  	   #__sdi_order o, 
					  	   #__sdi_list_ordertype otl 
					  WHERE opl.order_id= $order_id 
					  AND p.id = opl.product_id 
					  and p.free = 1 
					  and opl.status_id='".$await_type."' 
					  and o.type_id=otl.id 
					  and otl.code='D' 
					  and pa.user_id = u.id 
					  and o.id=opl.order_id 
					  and o.status_id='".$sent."' ";
			$db->setQuery( $query );
			$rows = $db->loadObjectList();
			if ($db->getErrorNum()) {
				echo "<div class='alert'>";
				echo 			$db->getErrorMsg();
				echo "</div>";
			}

			foreach ($rows as $row){
				$query = "UPDATE   #__sdi_order_product opl set status_id = ".$available_type." WHERE opl.order_id= $order_id AND opl.product_id = $row->product_id";
				$db->setQuery( $query );
				if (!$db->query()) {
					echo "<div class='alert'>";
					echo $db->getErrorMsg();
					echo "</div>";
					exit;
				}
				$user = JFactory::getUser();

				SITE_cpanel::sendMailByEmail($row->email,JText::_("SHOP_SHOP_MAIL_SUBJECT_REQUEST_FREE_PRODUCT"),JText::sprintf("SHOP_SHOP_MAIL_BODY_REQUEST_FREE_PROUCT",$row->data_title,$row->cmd_name,$user->name));
					
			}
			
			//Send an email to the customer to inform that his order has been sent
			//only if status is SENT
			if($order_status_value == "SENT")
			{
				//verify the notification is active.
				$queryNot = "SELECT p.notify_order_ready FROM #__sdi_account p, #__users u WHERE u.id = p.user_id and p.user_id = $user->id";
				$db->setQuery($queryNot);
				$not = $db->loadResult();
				if($not == 1)
					SITE_cpanel::sendMailByEmail($user->email,JText::sprintf("SHOP_SHOP_MAIL_SUBJECT_ORDER_NOTIFICATION_CUSTOMER", $order_name, $order_id),JText::sprintf("EASYSDI_ORDER_NOTIFICATION_CUSTOMER_BODY",$order_name,$order_id));
			}
			SITE_cpanel::setOrderStatus($order_id,$response_send);
			
			$mainframe->setUserState('productList',null);
			$mainframe->setUserState('order_type',null);
			$mainframe->setUserState('order_name',null);
			$mainframe->setUserState('third_party',null);
			$mainframe->setUserState('selectedSurfacesName',null);
			$mainframe->setUserState('selectedSurfaces',null);
			$mainframe->setUserState('totalArea',null);
			$mainframe->setUserState('perimeter_id',null);
			$mainframe->setUserState('bufferValue',null);
			$mainframe->setUserState('previousExtent',null);
			$mainframe->setUserState('order_id',null);


		}
		else
		{
			?>
			<div class="alert"><?php echo JText::_("SHOP_SHOP_MESSAGE_NOT_ALLOWED"); ?></div>
			<?php
		}
		
	}

	function manageSession(){

		global $mainframe;
		$fromStep = JRequest::getVar('fromStep',0);

		/**
		 * Comming from nowhere. Save nothing!
		 */
		if ($fromStep == 0) return ;

		if ($fromStep == 1) {
			
			$cid = JRequest::getVar ('cid',  array());
			/*
			 * Save the product list from the step 1
			 */
			$productList = $mainframe->getUserState('productList');
			if (is_array($productList))
			{
				foreach ($cid as $key => $value)
				{
					if(in_array($value , $productList))
					{
						
					}
					else
					{
						$productList[]=$value;
					}
				}
				$mainframe->setUserState('productList',$productList);
			}
			else 
			{
				$mainframe->setUserState('productList',$cid);
			}
			/*if (is_array($mainframe->getUserState('productList'))){
				$cid = array_merge($cid,$mainframe->getUserState('productList'));
			}
			$mainframe->setUserState('productList',$cid);*/
		}

		if ($fromStep == 2) {
			/*
			 * Save the perimeter from the step 2
			 */
			$selSurfaceList = JRequest::getVar ('replicSelectedSurface', array() );
			$mainframe->setUserState('selectedSurfaces',$selSurfaceList);
			 
			$selSurfaceListName = JRequest::getVar ('replicSelectedSurfaceName', array(0) );
			$mainframe->setUserState('selectedSurfacesName',$selSurfaceListName);
				
			$bufferValue = JRequest::getVar ('bufferValue2', 0 );
			$mainframe->setUserState('bufferValue',$bufferValue);
				
			$totalArea = JRequest::getVar ('totalArea', 0 );
			$mainframe->setUserState('totalArea',$totalArea);

			$perimeter_id = JRequest::getVar ('perimeter_id', 0 );
			$mainframe->setUserState('perimeter_id',$perimeter_id);
			
			$previousExtent = JRequest::getVar ('previousExtent', '' );
			$mainframe->setUserState('previousExtent',$previousExtent);

		}

		if ($fromStep == 3) {
			/*
			 * Save the properties from the step 3
			 */
			
			$cid = $mainframe->getUserState('productList');
			$db =& JFactory::getDBO();
			
			foreach ($cid as $key =>  $id)
			{
				$query = "SELECT DISTINCT a.id as property_id FROM #__sdi_product_property b, 
														#__sdi_property  as a ,
														#__sdi_property_value as c  
							WHERE a.id = c.property_id and b.propertyvalue_id = c.id and b.product_id = ". $id." order by a.ordering";
				
				$db->setQuery( $query );
				$rows = $db->loadObjectList();
					
				foreach($rows as $row)
				{
					$property=	JRequest::getVar($row->property_id."_text_property_$id", '' );
					$mainframe->setUserState($row->property_id.'_text_property_'.$id,$property);
					
					$property=	JRequest::getVar($row->property_id."_message_property_$id", '' );
					$mainframe->setUserState($row->property_id.'_message_property_'.$id,$property);
					
					$property=	JRequest::getVar($row->property_id."_textarea_property_$id", array() );
					$mainframe->setUserState($row->property_id.'_textarea_property_'.$id,$property);
	
					$property=	JRequest::getVar($row->property_id."_list_property_$id", array() );
					$mainframe->setUserState($row->property_id.'_list_property_'.$id,$property);
	
					$property=	JRequest::getVar($row->property_id."_cbox_property_$id", array() );
					$mainframe->setUserState($row->property_id.'_cbox_property_'.$id,$property);
	
					$property=	JRequest::getVar($row->property_id."_mlist_property_$id", array() );
					$mainframe->setUserState($row->property_id.'_mlist_property_'.$id,$property);
				}				 
				 
			}

		}

		if ($fromStep == 4) {
			/*
			 * Save the user's information from the step 4
			 */

			$third_party = JRequest::getVar("third_party");
			$order_name = JRequest::getVar("order_name");
			$order_type = JRequest::getVar("order_type");

			$mainframe->setUserState('third_party',$third_party);
			$mainframe->setUserState('order_name',$order_name);
			$mainframe->setUserState('order_type',$order_type );

			$user = JFactory::getUser();
			if ($user->guest && JRequest::getVar('step') > 4)
			{
				$options=array();
				$credentials = array();
				$credentials['username'] = JRequest::getVar('user');
				$credentials['password'] = JRequest::getString('password');
				$error = $mainframe->login($credentials, $options);

			 if(JError::isError($error))
			 {
			 	$step = JRequest::getVar('step',4);
					echo "<div class='alert'>";
					echo $error->getMessage();
					echo "</div>";
			 }
			}
		}
		
		$productList = $mainframe->getUserState('productList');
		if ( !is_array($productList) || count($productList) == 0){
			JRequest::setVar('step',1);
			JRequest::setVar('fromStep',0);
		}
		
		
	}

	function order(){
		global $mainframe;

		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$cid = JRequest::getVar ('cid', array() );
		$step = JRequest::getVar('step',1);
		
		SITE_shop::manageSession();
		$productList = $mainframe->getUserState('productList');
		if ( !is_array($productList) || count($productList) == 0)
		{
			$step = 1;
			$curStep = '';
			$fromStep = '';
		}
		?>
<h2 class="contentheading"><?php echo JText::_("SHOP_SHOP_TITLE"); ?></h2>
<script>
var tries = 1;

function validateForm(toStep, fromStep){
	
	//Do not let order if products do not have at least 1 common perimeter
	if(toStep == 2 && fromStep == 1){
		if($('commonPerimCount').value < 1)
			return false;
	}
	
	//Do not allow to go before the perimeter are loaded.
	//Causes bug (selected perimeter not saved)
	if(toStep == 3 && fromStep == 2){
		if(loadingpanel.maximized && tries == 1){
			//register an event to the loading panel to do this clear
			tries--;
			return false;
		}
	}
	
	//check that all properties were filled in
	if(toStep == 4 && fromStep == 3){
		
		var errorMsg = "";
		var errorNum = 0;
		//
		//select
		//
		
		var aSel = $('orderForm').getElementsByTagName("select");
		for(var i=0;i<aSel.length;i++){
			
			//Is the property mandatory?
			if(aSel[i].className.indexOf("mdtryElem",0) != -1){			
				selected = new Array(); 
				for (var j = 0; j < aSel[i].options.length; j++){
					if (aSel[i].options[j].selected && (aSel[i].options[j].value != -1 || aSel[i].options[j].value != ""))
					selected.push(aSel[i].options[j].value);
				}
				
				//Select multiple
				if(aSel[i].multiple){
					if(selected.length < 1){
						label = aSel[i].id+'_label';
						errorMsg += "\r\n"+$(label).innerHTML.innerHTML.replace(":","");
						errorNum++;
					}
				}else{
				//select box normal
					if(aSel[i].options[aSel[i].selectedIndex].value == -1 || aSel[i].options[aSel[i].selectedIndex].value == ""){
						errorMsg += "\r\n"+aSel[i].options[0].text;
						errorNum++;
					}
				}
			}
		}
		
		//
		//input
		//
		var aSel = $('orderForm').getElementsByTagName("input");
		for(var i=0;i<aSel.length;i++){
			if(aSel[i].className.indexOf("mdtryElem",0) != -1){
				//text
				if(aSel[i].type == 'text'){
					if(aSel[i].value.length < 1){
						label = aSel[i].id+'_label';
						errorMsg += "\r\n"+$(label).innerHTML.replace(":","");
					}
				}
			}
		}
		
		
		//checkbox
		var aSel = $('orderForm').getElementsByTagName("div");
		for(var i=0;i<aSel.length;i++){
			//Browse group
			if(aSel[i].className == "product_proprety_cbox_group"){
				var aCbox = aSel[i].getElementsByTagName("input");
				var cont=false;
				var label = "";
				
				for(var j=0;j<aCbox.length;j++){
					//cont if at least one is checked
					label = aCbox[j].id+'_label';
					if(aCbox[j].checked)
						cont = true;
				}
				if(!cont){
					errorMsg += "\r\n"+"-"+$(label).innerHTML.replace(":","")+"-";
				}
			}
		}
		
		//
		//textArea
		//
		var aSel = $('orderForm').getElementsByTagName("textarea");
		for(var i=0;i<aSel.length;i++){
			if(aSel[i].className.indexOf("mdtryElem",0) != -1){
				if(aSel[i].value.length < 1){
					label = aSel[i].id+'_label';
					errorMsg += "\r\n"+$(label).innerHTML.replace(":","");
				}
			}
		}
		
		if(errorMsg != ""){
			if(errorNum > 1)
				msgHeader = '<?php echo JText::_("SHOP_SHOP_PROPERTIES_ERRORS"); ?>';
			else
				msgHeader = '<?php echo JText::_("SHOP_SHOP_PROPERTIES_ERROR"); ?>';
			alert(msgHeader+errorMsg);
			return false;
		}
	}

	//All checks are ok
	document.getElementById('step').value=toStep;
	submitOrderForm();
}
</script>
<table>
	<tr>
		<td>
		<div class="headerShop"><?php $curStep = 1; if(count($productList)>0&& ($curStep<$step-1 || $curStep==$step+1)) { ?>
		<div
			onClick="return validateForm(<?php echo $curStep; ?>,<?php echo $step; ?>);"
			class="selectableStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }elseif(count($productList)>0 && ($curStep==$step-1)){ ?>
		<div
			onClick="return validateForm(<?php echo $curStep; ?>,<?php echo $step; ?>);"
			class="previousStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }else {?>
		<div
			class="<?php if($curStep==$step) {echo "currentStep";} else{echo "unselectableStep";}?>">
			<table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
			<?php } ?> <?php $curStep = 2; if(count($productList)>0&& ($curStep<$step-1 || $curStep==$step+1)) { ?>
		<div
			onClick="return validateForm(<?php echo $curStep; ?>,<?php echo $step; ?>);"
			class="selectableStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }elseif(count($productList)>0 && ($curStep==$step-1)){ ?>
		<div
			onClick="return validateForm(<?php echo $curStep; ?>,<?php echo $step; ?>);"
			class="previousStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }else {?>
		<div
			class="<?php if($curStep==$step) {echo "currentStep";} else{echo "unselectableStep";}?>"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php } ?> <?php $curStep = 3; if(count($productList)>0&& ($curStep<$step-1 || $curStep==$step+1)) { ?>
		<div
			onClick="return validateForm(<?php echo $curStep; ?>,<?php echo $step; ?>);"
			class="selectableStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }elseif(count($productList)>0 && ($curStep==$step-1)){ ?>
		<div
			onClick="return validateForm(<?php echo $curStep; ?>,<?php echo $step; ?>);"
			class="previousStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }else {?>
		<div
			class="<?php if($curStep==$step) {echo "currentStep";} else{echo "unselectableStep";}?>"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php } ?> <?php $curStep = 4; if(count($productList)>0&& ($curStep<$step-1 || $curStep==$step+1)) { ?>
		<div
			onClick="return validateForm(<?php echo $curStep; ?>,<?php echo $step; ?>);"
			class="selectableStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }elseif(count($productList)>0 && ($curStep==$step-1)){ ?>
		<div
			onClick="return validateForm(<?php echo $curStep; ?>,<?php echo $step; ?>);"
			class="previousStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }else {?>
		<div
			class="<?php if($curStep==$step) {echo "currentStep";} else{echo "unselectableStep";}?>"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php } ?> <?php $curStep = 5; if(count($productList)>0&& ($curStep<$step-1 || $curStep==$step+1)) { ?>
		<div
			onClick="return validateForm(<?php echo $curStep; ?>,<?php echo $step; ?>);"
			class="selectableStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }elseif(count($productList)>0 && ($curStep==$step-1)){ ?>
		<div
			onClick="return validateForm(<?php echo $curStep; ?>,<?php echo $step; ?>);"
			class="previousStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }else {?>
		<div
			class="<?php if($curStep==$step) {echo "currentStep";} else{echo "unselectableStep";}?>"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php } ?></div>
		</td>
	</tr>
	<tr>
		<td>
		<div class="bodyShop">
		<?php if ($step ==1) SITE_shop::searchProducts();?>
		<?php if ($step ==2) SITE_shop::orderPerimeter($cid,$option);?> 
		<?php if ($step ==3) SITE_shop::orderProperties($cid,$option);?>
		<?php if ($step ==4) SITE_shop::orderDefinition($cid);?> 
		<?php if ($step ==5) SITE_shop::orderSend($cid);?>
		</div>
		</td>
	</tr>
</table>

	<?php
	}

	function searchProducts($orderable = 1){
		global $mainframe;
		$db =& JFactory::getDBO();
		$limitstart = JRequest::getVar('limitstart',0);
		$limit = JRequest::getVar('limit',5);
		
		//reinit limistart to 0 if we want all records
		if($limit == 0)
			$limitstart=0;
		
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$view = JRequest::getVar('view');
		$step = JRequest::getVar('step',"1");
		$countMD = JRequest::getVar('countMD');
		$simpleSearchCriteria  	= JRequest::getVar('simpleSearchCriteria','');
		$freetextcriteria = JRequest::getVar('freetextcriteria','');
		$freetextcriteria = $db->getEscaped( trim( strtolower( $freetextcriteria ) ) );
		$account_id = JRequest::getVar('partner_id');
		$account_id = $db->getEscaped( trim( strtolower( $account_id ) ) );		
		$filter_visible=JRequest::getVar('filter_visible');
		$filter_date = JRequest::getVar('update_cal');
		$filter_date = $db->getEscaped( trim( strtolower( $filter_date ) ) );
		$filter_date_comparator = JRequest::getVar('update_select');
		
		//Public
		$queryVisibility = "select id from #__sdi_list_visibility where code ='public'";
		$db->setQuery($queryVisibility);
		$public = $db->loadResult();
		//Private
		$queryVisibility = "select id from #__sdi_list_visibility where code ='private'";
		$db->setQuery($queryVisibility);
		$private = $db->loadResult();
		
		/* Todo, push the date format in EasySDI config and
		set it here accordingly */
		if($filter_date){
			$temp = explode(".", $filter_date);
			$filter_date = $temp[2]."-".$temp[1]."-".$temp[0];
		}
		
		//partner select box
		$suppliers = array();
		$suppliers[0]='';
		
		//Do not display a furnisher without product	
		$query = "SELECT  #__sdi_account.id as value, #__users.name as text 
		          FROM #__users, `#__sdi_account` 
			  	  INNER JOIN `#__sdi_object` ON #__sdi_account.id = #__sdi_object.account_id 
			  	  WHERE #__users.id = #__sdi_account.user_id AND 
			      #__sdi_account.id IN (Select o.account_id from #__sdi_object o 
			      												INNER JOIN #__sdi_objectversion v ON o.id = v.object_id 
			      												INNER JOIN #__sdi_product p ON p.objectversion_id =  v.id  
			      												WHERE  p.published=1) 
			      AND #__sdi_object.published = 1
			      GROUP BY #__sdi_account.id 
			      ORDER BY #__users.name";
		$db->setQuery( $query);
		$suppliers = array_merge( $suppliers, $db->loadObjectList() );
		if ($db->getErrorNum()) 
		{
			echo "<div class='alert'>";
			echo 	$db->getErrorMsg();
			echo "</div>";
		}
		
		$cid = JRequest::getVar ('cid', array() );

		$filter = "";

		$productList = $mainframe->getUserState('productList');
		if (count($productList)>0){
			$filter = " AND p.ID NOT IN (";
			foreach( $productList as $id){
				$filter = $filter.$id.",";
			}
			$filter = substr($filter , 0, -1);
			$filter = $filter.")";
		}

		if ($freetextcriteria){
			//replace space with wildcard for one character
			$freetextcriteria = str_replace(" ", "_", $freetextcriteria);
			$filter = $filter." AND (p.name like '%".$freetextcriteria."%' ";
			$filter = $filter." OR v.metadata_id = '$freetextcriteria')";
		}
		
		if ($account_id){
			$filter = $filter." and o.account_id = '".$account_id."'";
		}
		
		if ($filter_visible){
			$filter = $filter."  and viewurlwms != ''";
		}
		
		if ($filter_date && $filter_date_comparator){
			$filter_date_esc = $db->quote( $db->getEscaped( $filter_date."%" ), false );
			if($filter_date_comparator == "equal")
				$filter = $filter." AND m.updated like ".$filter_date_esc;
			if($filter_date_comparator == "different")
				$filter = $filter." AND m.updated not like ".$filter_date_esc;
			if($filter_date_comparator == "greaterorequal")
				$filter = $filter." AND (m.updated >= ".$filter_date_esc." OR m.updated like ".$filter_date_esc.") "; 
			if($filter_date_comparator == "smallerorequal")
				$filter = $filter." AND (m.updated <= ".$filter_date_esc." OR m.updated like ".$filter_date_esc.") "; 
		}
		
		$user = JFactory::getUser();
		$account = new accountByUserId($db);
		if (!$user->guest)
		{
			$account->load($user->id);
		}else
		{
			$account->id = 0;
		}

		if($account->id == 0)
		{
			//No user logged, display only external products
			$filter .= " AND (p.visibility_id=".$public.") ";
		}
		else
		{
			//User logged, display products according to users's rights
			if(userManager::hasRight($account->id,"REQUEST_EXTERNAL"))
			{
				if(userManager::hasRight($account->id,"REQUEST_INTERNAL"))
				{
					$filter .= " AND (p.visibility_id=$public
					OR
					(p.visibility_id =$private AND
					(o.account_id =  $account->id
					OR
					o.account_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id )
					OR 
					o.account_id IN (SELECT id FROM #__sdi_account WHERE root_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id ))
					OR
					o.account_id  IN (SELECT id FROM #__sdi_account WHERE root_id = $account->id ) 
					
					))) ";
				}
				else
				{
					$filter .= " AND (p.visibility_id=$public) ";
				}
			}
			else
			{
				if(userManager::hasRight($account->id,"REQUEST_INTERNAL"))
				{
					$filter .= " AND (p.visibility_id =$private AND
					(o.account_id =  $account->id
					OR
					o.account_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id )
					OR 
					o.account_id IN (SELECT id FROM #__sdi_account WHERE root_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id ))
					OR
					o.account_id  IN (SELECT id FROM #__sdi_account WHERE root_id = $account->id ) 
					)) ";
									
				}
				else
				{
					//no command right
					$filter .= " AND (p.visibility=2000) ";
				}
			}
		}

		$query  = "SELECT COUNT(*)FROM #__sdi_product p 
							INNER JOIN #__sdi_objectversion v ON v.id = p.objectversion_id
							INNER JOIN #__sdi_object o ON o.id = v.object_id
							INNER JOIN #__sdi_metadata m ON m.id = v.metadata_id
							WHERE p.published=1 AND o.published = 1";
		$query  = $query .$filter;
		$db->setQuery( $query);
		$total = $db->loadResult();

		$query  = "SELECT p.*, v.metadata_id as metadata_id, o.account_id as supplier_id, a.name as supplier_name , a.logo as supplier_logo, m.visibility_id as md_visibility_id FROM #__sdi_product p 
							INNER JOIN #__sdi_objectversion v ON v.id = p.objectversion_id
							INNER JOIN #__sdi_object o ON o.id = v.object_id
							INNER JOIN #__sdi_account a ON a.id = o.account_id
							INNER JOIN #__sdi_metadata m ON m.id = v.metadata_id
							WHERE p.published=1 AND o.published = 1";
		$query  = $query .$filter;
		if ($simpleSearchCriteria == "lastAddedMD"){
			$query  = $query." order by p.created";
		}
		else if ($simpleSearchCriteria == "lastUpdatedMD"){
			$query  = $query." order by p.updated";
		}
		else
		{
			$query  = $query ." order by p.name";
		}
	//	echo $query;
		$db->setQuery($query,$limitstart,$limit);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 	$db->getErrorMsg();
			echo "</div>";
		}

		HTML_shop::searchProducts ($suppliers, $account_id, $user,$rows,$countMD,$total, $limitstart, $limit,$option,$task,$view,$step);	
	}
}
	?>