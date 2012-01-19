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

class HTML_geolocation 
{
	function listGeolocation( $rows, $pageNav, $search, $filter_order_Dir, $filter_order,$option)
	{
		JToolBarHelper::title(JText::_("MAP_GEOLOCATION_LIST"), 'map.png');
		?>
		<form action="index.php" method="GET" name="adminForm">
		<table width="100%">
			<tr>
				<td class="key"  width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchGeolocation" id="searchGeolocation" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchGeolocation').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("MAP_GEOLOCATION_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_GEOLOCATION_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_GEOLOCATION_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_GEOLOCATION_WFSURL"), 'wfsurl', @$filter_order_Dir, @$filter_order); ?></th>
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
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editGeolocation')"><?php echo $row->name; ?></a></td>
				<td><?php echo $row->description; ?></td>
				<td><?php echo $row->wfsurl; ?></td>								 
			</tr>
		<?php
			$k = 1 - $k;
			$i++;
		}
		?></tbody>
		<tfoot>
		<tr>	
		<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
	  	</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="geolocation" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  	</form>
		<?php		
	}
	
	function editGeolocation( $geolocation, $createUser, $updateUser,$fieldsLength,$option )
	{
		global  $mainframe;
		if ($geolocation->id != 0)
		{
			JToolBarHelper::title( JText::_("MAP_GEOLOCATION_EDIT").': <small><small>['. JText::_("CORE_EDIT").']</small></small>', 'addedit.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("MAP_GEOLOCATION_EDIT").': <small><small>['. JText::_("CORE_NEW").']</small></small>', 'addedit.png' );
		}
		?>		
		<script>	
	function submitbutton(pressbutton)
	{
		if(pressbutton == "saveGeolocation")
		{
			
			if (document.getElementById('wfsurl').value == "")
			{
				alert ('<?php echo  JText::_( 'MAP_GEOLOCATION_CT_WFS_URL_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('name').value == "")
			{
				alert ('<?php echo  JText::_( 'MAP_GEOLOCATION_CT_TITLE_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('areafield').value == "")
			{	
				alert ('<?php echo  JText::_( 'MAP_GEOLOCATION_CT_AREA_NAME_VALIDATION_ERROR');?>');	
				return;
			}else
			{	
				submitform(pressbutton);
			}
		}
		else
		{
			submitform(pressbutton);
		}
	}
	</script>				
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<table class="admintable">
							<tr>
								<td class="key"><?php echo JText::_("MAP_GEOLOCATION_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['name'];?>" name="name" id="name" value="<?php echo $geolocation->name; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_GEOLOCATION_DESCRIPTION"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['description'];?>" name="description" id="description" value="<?php echo $geolocation->description; ?>" /></td>
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("MAP_GEOLOCATION_WFS_URL"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['wfsurl'];?>" name="wfsurl" id="wfsurl" value="<?php echo $geolocation->wfsurl; ?>" /></td>							
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_GEOLOCATION_FEAT_TYPE_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['featuretypename'];?>" name="featuretypename" id="featuretypename" value="<?php echo $geolocation->featuretypename; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_GEOLOCATION_ID_FIELD_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['idfield'];?>" name="idfield" id="idfield" value="<?php echo $geolocation->idfield; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_GEOLOCATION_NAME_FIELD_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['namefield'];?>" name="namefield" id="namefield" value="<?php echo $geolocation->namefield; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_GEOLOCATION_AREA_FIELD_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['areafield'];?>" name="areafield" id="areafield" value="<?php echo $geolocation->areafield; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_GEOLOCATION_PARENT_ID"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['parentid'];?>" name="parentid" id="parentid" value="<?php echo $geolocation->parentid; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_GEOLOCATION_PARENT_FK_FIELD_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['parentfkfield'];?>" name="parentfkfield" id="parentfkfield" value="<?php echo $geolocation->parentfkfield; ?>" /></td>
							</tr>
							
							<tr>
								<td class="key"><?php echo JText::_("MAP_GEOLOCATION_MAX_FEATURE"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['maxfeatures'];?>" name="maxfeatures" id="maxfeatures" value="<?php echo $geolocation->maxfeatures; ?>" /></td>
							</tr>							
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<br></br>
		<table border="0" cellpadding="3" cellspacing="0">
		<?php
		if ($geolocation->created)
		{ 
		?>
			<tr>
				<td><?php echo JText::_("CORE_CREATED"); ?> : </td>
				<td><?php if ($geolocation->created) {echo date('d.m.Y h:i:s',strtotime($geolocation->created));} ?></td>
				<td>, </td>
				<td><?php echo $createUser; ?></td>
			</tr>
		<?php
		}
		if ($geolocation->updated and $geolocation->updated<> '0000-00-00 00:00:00')
		{ 
		?>
			<tr>
				<td><?php echo JText::_("CORE_UPDATED"); ?> : </td>
				<td><?php if ($geolocation->updated and $geolocation->updated<> 0) {echo date('d.m.Y h:i:s',strtotime($geolocation->updated));} ?></td>
				<td>, </td>
				<td><?php echo $updateUser; ?></td>
			</tr>
		<?php
		}
		?>		
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="saveGeolocation" />
		<input type="hidden" name="id" value="<?php echo $geolocation->id; ?>" />
		<input type="hidden" name="guid" value="<?php echo $geolocation->guid?>" />
		<input type="hidden" name="ordering" value="<?php echo $geolocation->ordering; ?>" />
		<input type="hidden" name="created" value="<?php echo $geolocation->created;?>" />
		<input type="hidden" name="createdby" value="<?php echo $geolocation->createdby; ?>" /> 
		<input type="hidden" name="updated" value="<?php echo $geolocation->created; ?>" />
		<input type="hidden" name="updatedby" value="<?php echo $geolocation->createdby; ?>" /> 
		</form>
	<?php
	}
}
?>