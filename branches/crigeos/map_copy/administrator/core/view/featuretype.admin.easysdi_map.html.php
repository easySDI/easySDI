<?php
/**
 *  EasySDI, a solution to implement easily any spatial data infrastructure
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

class HTML_featuretype 
{
	function listFeatureType( $rows, $pageNav,$search, $filter_order_Dir, $filter_order, $option)
	{
		JToolBarHelper::title(JText::_("MAP_LIST_FEATURE_TYPE"), 'map.png');
		?>
		<form action="index.php" method="GET" name="adminForm">
		<table width="100%">
			<tr>
				<td class="key"  width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchFeatureType" id="searchFeatureType" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchFeatureType').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("MAP_FEATURETYPE_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_FEATURETYPE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_FEATURETYPE_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
			</tr>
		</thead>
		<tbody>		
		<?php
		$k = 0;
		$i=0;
		foreach ($rows as $row)
		{		
		?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editFeatureType')"><?php echo $row->name; ?></a></td>				
				<td><?php echo $row->description; ?></a></td>
			</tr>
		<?php
			$k = 1 - $k;
			$i++;
		}
		
			?>
		</tbody>
		<tfoot>
		<tr>	
		<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
		</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="featureType" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  	</form>
		<?php		
	}
	
	function editFeatureType ($feature_type, $rowsUses,$rowsSelectedUses,$rowsAttributes,$rowsProfiles,$rowsAttributeProfiles,$createUser,$updateUser,$fieldsLength,$option)
	{
		if ($feature_type->id != 0)
		{
			JToolBarHelper::title( JText::_("MAP_EDIT_FEATURETYPE").': <small><small>['. JText::_("CORE_EDIT").']</small></small>', 'addedit.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("MAP_EDIT_FEATURETYPE").': <small><small>['. JText::_("CORE_NEW").']</small></small>', 'addedit.png' );
		}
		
		HTML_featuretype::alter_array_value_with_Jtext($rowsUses);
		HTML_featuretype::alter_array_value_with_Jtext($rowsProfiles);
	?>	
	<script type="text/javascript">
	window.onload=function()
	{
		checkUses();
	}
	function checkUses()
	{	
		var selection = document.getElementById('uses_id').options ;
		var result = true;
		for(i=0;i<selection.length;i++) 
		{
			if(selection[i].selected)
			{
				if(selection[i].value == 3)
				{
					result = false;
				}				
			}
  		} 
			document.getElementById('geometry').value = "";			
  			document.getElementById('geometry').disabled = result;
  			if(result )
  			{
  				document.getElementById('geometry').style.backgroundColor = "#FFFFFF";
  			}
  			else
  			{
  				document.getElementById('geometry').style.backgroundColor = document.getElementById('name').style.backgroundColor;
  			}
 	}
 	
 	function enableVisibility (i)
 	{
 		document.getElementById('VISIBILITY_'+i).disabled = !document.getElementById('VISIBLE_'+i).checked ;
 		document.getElementById('WIDTH_'+i).disabled = !document.getElementById('VISIBLE_'+i).checked ;
 		if(	document.getElementById('VISIBILITY_'+i).disabled )
 		{
 			document.getElementById('VISIBILITY_'+i).checked = false ;
 			document.getElementById('WIDTH_'+i).value = "";
 		}
 	}
 	</script>			
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">

		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
					<legend><?php echo  JText::_("MAP_LEGEND_FEATURETYPE")?></legend>						
						<table class="admintable">
							<tr>
								<td class="key" width="100p"><?php echo JText::_("MAP_FEATURETYPE_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['name'];?>" name="name" id="name" value="<?php echo $feature_type->name; ?>" /></td>								
							</tr>
							<tr>
								<td class="key" width="100p"><?php echo JText::_("MAP_FEATURETYPE_DESCRIPTION"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['description'];?>" name="description" id="description" value="<?php echo $feature_type->description; ?>" /></td>								
							</tr>
							<tr>
								<td class="key" width="100p"><?php echo JText::_("MAP_FEATURETYPE_FEATURETYPENAME"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['featuretypename'];?>" name="featuretypename" id="featuretypename" value="<?php echo $feature_type->featuretypename; ?>" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_FEATURETYPE_USE"); ?></td>
								<td><?php echo JHTML::_("select.genericlist",$rowsUses, 'uses_id[]', 'size="5" multiple="true" class="selectbox" onChange="javascript:checkUses()"', 'value', 'text', $rowsSelectedUses ); ?></td>
							</tr>
							<tr id="geom">
								<td class="key" width="100p"><?php echo JText::_("MAP_FEATURETYPE_GEOMETRY"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['geometry'];?>" name="geometry" id="geometry" value="<?php echo $feature_type->geometry; ?>" /></td>								
							</tr>
						</table>
					</fieldset>
					<fieldset>		
					<legend><?php echo  JText::_("MAP_LEGEND_ATTRIBUTE")?>
						<a href="#" onclick="javascript: submitbutton('addNewAttribute')"> 
									<img class="helpTemplate" 
										 src="../templates/easysdi/icons/silk/add.png" 
										 alt="<?php echo JText::_("MAP_ADD_NEW_ATTRIBUTE") ?>" 
										 />
								</a></legend>				
						<table class="admintable">
						<tr>
							<th width="310"><?php echo JText::_( 'MAP_ATTR_NAME'); ?></th>
							<th width="110"><?php echo JText::_( 'MAP_ATTR_DATA_TYPE'); ?></th>
							<th width="150"><?php echo JText::_( 'MAP_ATTR_PROFILE'); ?></th>
							<th width="50"><?php echo JText::_( 'MAP_ATTR_VISIBLE'); ?></th>
							<th width="110"><?php echo JText::_( 'MAP_ATTR_WIDTH'); ?></th>
							<th width="50"><?php echo JText::_( 'MAP_ATTR_VISIBILITY'); ?></th>							
						</tr>
							<tbody id="attributeTable" >
							<?php $iAttribute = 0; 
							foreach ($rowsAttributes as $attribute)
							{
							?>
								<input type="hidden" name="ID_<?php echo $iAttribute;?>" id="ID_<?php echo $iAttribute;?>" value="<?php echo $attribute->id; ?>" />
								<tr id="TR_<?php echo $iAttribute; ?>" >
									<td class="key"><input class="inputbox" type="text" name="NAME_<?php echo $iAttribute;?>" id="NAME_<?php echo $iAttribute;?>" value="<?php echo $attribute->name; ?>" size=70></td>
									<td><input class="inputbox" type="text" name="DATATYPE_<?php echo $iAttribute;?>" id="DATATYPE_<?php echo $iAttribute;?>"  value="<?php echo $attribute->datatype; ?>"></td>
									<td><?php echo JHTML::_("select.genericlist",$rowsProfiles, 'PROFILE_'.$iAttribute.'[]', 'size="5" multiple="true" class="selectbox"', 'value', 'text', $rowsAttributeProfiles[$attribute->id] ); ?></td>
									<td><input type="checkbox"  name="VISIBLE_<?php echo $iAttribute;?>" id="VISIBLE_<?php echo $iAttribute;?>"  value="1" onChange="javascript:enableVisibility(<?php echo $iAttribute;?>)" <?php if ($attribute->visible) echo "checked"; ?>"></td>
									<td><input class="inputbox" type="text" name="WIDTH_<?php echo $iAttribute;?>" id="WIDTH_<?php echo $iAttribute;?>"  <?php if (!$attribute->visible) echo "disabled"; ?> value="<?php if ($attribute->visible) echo $attribute->width; ?>"></td>				
									<td><input type="checkbox" name="VISIBILITY_<?php echo $iAttribute;?>" id="VISIBILITY_<?php echo $iAttribute;?>" value="1" <?php if (!$attribute->visible) echo "disabled"; if ($attribute->initialvisibility && $attribute->visible) echo "checked"; ?>"></td>
									<td><input type="button" onClick="document.getElementById('selectAttr').value=<?php echo $iAttribute; ?>;javascript:submitbutton('removeAttribute');" value="<?php echo JText::_( 'MAP_REMOVE_ATTRIBUTE' ); ?>"></td>													
								</tr>
							<?php 
								$iAttribute += 1;
							}
							?>
							</tbody>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<br></br>
		<table border="0" cellpadding="3" cellspacing="0">
		<?php
		if ($feature_type->created)
		{ 
		?>
			<tr>
				<td><?php echo JText::_("CORE_CREATED"); ?> : </td>
				<td><?php if ($feature_type->created) {echo date('d.m.Y h:i:s',strtotime($feature_type->created));} ?></td>
				<td>, </td>
				<td><?php echo $createUser; ?></td>
			</tr>
		<?php
		}
		if ($feature_type->updated and $feature_type->updated<> '0000-00-00 00:00:00')
		{ 
		?>
			<tr>
				<td><?php echo JText::_("CORE_UPDATED"); ?> : </td>
				<td><?php if ($feature_type->updated and $feature_type->updated<> 0) {echo date('d.m.Y h:i:s',strtotime($feature_type->updated));} ?></td>
				<td>, </td>
				<td><?php echo $updateUser; ?></td>
			</tr>
		<?php
		}
		?>		
		</table>
		<input type="hidden" name="selectAttr" id="selectAttr" value="" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="iAttribute" id="iAttribute" value="<?php echo $iAttribute; ?>" />
		<input type="hidden" name="toRemoveAttrList" id="toRemoveAttrList" value="" />
		<input type="hidden" name="id" value="<?php echo $feature_type->id; ?>" />
		<input type="hidden" name="guid" value="<?php echo $feature_type->guid?>" />
		<input type="hidden" name="ordering" value="<?php echo $feature_type->ordering; ?>" />
		<input type="hidden" name="created" value="<?php echo $feature_type->created;?>" />
		<input type="hidden" name="createdby" value="<?php echo $feature_type->createdby; ?>" /> 
		<input type="hidden" name="updated" value="<?php echo $feature_type->created; ?>" />
		<input type="hidden" name="updatedby" value="<?php echo $feature_type->createdby; ?>" /> 
	</form>
	<script >
	var nbAttribute = <?php echo $iAttribute;?>;
	var nbToDelete = 0;
	var toRemoveAttrArray = new Array();
	
	function submitbutton(pressbutton)
	{
		if(pressbutton == "saveFeatureType")
		{
			if (document.getElementById('featuretypename').value == "")
			{
				alert ('<?php echo  JText::_( 'MAP_FEATURETYPENAME_VALIDATION_ERROR');?>');	
				return;
			}
			else
			{	
				submitform(pressbutton);
			}
		}
		
		if (pressbutton=="addNewAttribute")
		{	
			addNewAttribute();
		}
		else if (pressbutton=="removeAttribute")
		{	var attr = document.getElementById('selectAttr').value;
			if(document.getElementById('ID_'+attr))
			{
				toRemoveAttrArray[nbToDelete] = document.getElementById('ID_'+attr).value;
				nbToDelete = nbToDelete +1;
			}
			removeAttribute(document.getElementById('selectAttr').value);
		}
		else
		{	
			var arv = toRemoveAttrArray.toString();
			document.getElementById("toRemoveAttrList").value=arv; 
			submitform(pressbutton);
		}
	}
	function addNewAttribute()
	{
		var idEl = document.createElement ('input');
		idEl.type = "hidden";
		idEl.name = "ID_"+nbAttribute;
		idEl.value = "";
		document.getElementById("attributeTable").appendChild(idEl);
		
		var tr = document.createElement('tr');	
		tr.name  = "TR_"+nbAttribute;
		tr.id = "TR_"+nbAttribute;
			
		
		var tdName = document.createElement('td');
		tdName.className="key";
		
		
		var tdDataType = document.createElement('td');
		var tdWidth = document.createElement('td');		
		var tdInitialVisibility = document.createElement('td');
		var tdVisible = document.createElement('td');		
		var tdProfile = document.createElement('td');		
				
		var inputName = document.createElement('input');
		inputName.size=70;
		inputName.type="text";
		inputName.name="NAME_"+nbAttribute;
		inputName.id="NAME_"+nbAttribute;
		
		var inputDataType = document.createElement('input');
		inputDataType.type="text";
		inputDataType.name="DATATYPE_"+nbAttribute;
		inputDataType.id="DATATYPE_"+nbAttribute;
		
		var inputWidth = document.createElement('input');
		inputWidth.type="text";
		inputWidth.name="WIDTH_"+nbAttribute;
		inputWidth.id="WIDTH_"+nbAttribute;
				
		var inputProfile = document.createElement('select');
		inputProfile.class="selectbox";
		inputProfile.size="5";
		inputProfile.multiple="true";
		inputProfile.name="PROFILE_"+nbAttribute+"[]";
		inputProfile.id="PROFILE_"+nbAttribute+"[]";
		
		<?php
		foreach ($rowsProfiles as $profile)
		{
		?>
				var optionProfile = document.createElement('option');
				optionProfile.value = "<?php echo $profile->value; ?>";
				optionProfile.text =  "<?php echo $profile->text; ?>";
				inputProfile.appendChild(optionProfile);
		<?php
		}
		?>
		
		var inputInitialVisibility = document.createElement('input');
		inputInitialVisibility.type="checkbox";
		inputInitialVisibility.value="1";
		inputInitialVisibility.name="VISIBILITY_"+nbAttribute;
		inputInitialVisibility.id="VISIBILITY_"+nbAttribute;
				
		var inputVisible = document.createElement('input');
		inputVisible.type="checkbox";
		inputVisible.value="1";
		inputVisible.setAttribute ("onChange", "javascript:enableVisibility("+nbAttribute+")");
		inputVisible.name="VISIBLE_"+nbAttribute;
		inputVisible.id="VISIBLE_"+nbAttribute;
		
		tdName.appendChild(inputName);
		tr.appendChild(tdName);
		tdDataType.appendChild(inputDataType);
		tr.appendChild(tdDataType);		
		tdProfile.appendChild(inputProfile);
		tr.appendChild(tdProfile);
		tdVisible.appendChild(inputVisible);
		tr.appendChild(tdVisible);
		tdWidth.appendChild(inputWidth);
		tr.appendChild(tdWidth);		
		tdInitialVisibility.appendChild(inputInitialVisibility);
		tr.appendChild(tdInitialVisibility);
		
		var aButton = document.createElement('input');
		aButton.type="button";
		aButton.value="<?php echo JText::_( 'MAP_REMOVE_ATTRIBUTE' ); ?>";
		aButton.setAttribute("onClick","document.getElementById('selectAttr').value="+nbAttribute+" ;javascript:submitbutton('removeAttribute');");
		
		var tdButton = document.createElement('td');
		tdButton.appendChild(aButton);
				
		tr.appendChild(tdButton);
		
		document.getElementById("attributeTable").appendChild(tr);
		
		document.getElementById('VISIBILITY_'+nbAttribute).disabled = true ;
 		document.getElementById('WIDTH_'+nbAttribute).disabled = true;
		
		nbAttribute = nbAttribute + 1;
		document.getElementById("iAttribute").value = nbAttribute;
	}
	
	function removeAttribute(attrNo)
	{		
		noeud = document.getElementById("attributeTable");
		fils = document.getElementById("TR_"+attrNo);
		
		noeud.removeChild(fils);
		
		nbAttribute = nbAttribute - 1;
		document.getElementById("iAttribute").value = nbAttribute;
	}
	</script>
<?php
	}
	
	function alter_array_value_with_Jtext(&$rows)
	{		
		if (count($rows)>0){
		  foreach($rows as $key => $row) {		  	
       		$rows[$key]->text = JText::_($rows[$key]->text);
  		}			    
		}
	}
}
?>