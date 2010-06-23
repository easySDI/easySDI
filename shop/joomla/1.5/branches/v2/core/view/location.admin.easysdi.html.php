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

class HTML_location {

	function editLocation( $rowLocation,$rowsAccount,$id, $option ){
		
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		
		//Get if the current location is used by an other in the field "id_location_filter".
		//This means that a location depends on this one for a filter so
		// the current location can not be "shown in location"
		$queryIsFilter = "select * from  #__easysdi_location_definition  where id_location_filter=$rowLocation->id ";
		$database->setQuery( $queryIsFilter );
		$result = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		$is_filter = false;
		if(count($result) >0)
		{
			$is_filter = true;	
		}
		//
		
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("EASYSDI_TITLE_EDIT_LOCATION"), 'generic.png' );
			
		?>	
		<script>
		function chooseLocationAsFilter(locationId)
		{
			<?php
			$query = "SELECT * FROM #__easysdi_location_definition WHERE is_localisation = 1 ";
			$database->setQuery($query);
			$rows = $database->loadObjectList();
			if ($database->getErrorNum()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			foreach ($rows as $row)
			{
				?>
				if(locationId == '<?php echo $row->id ;?>')
				{
					alert( "<?php echo JText::_("EASYSDI_LOCATION_DEF_VALIDATION");?> ");
					return;
				}
				<?php 
			}
			?>
		}
		</script>	
		<script>
		function displayAuthentication()
		{
			if (document.forms['adminForm'].service_type[0].checked)
			{
				document.getElementById('password').disabled = true;
				document.getElementById('password').value = "";
				document.getElementById('user').disabled = true;
				document.getElementById('user').value ="";
				document.getElementById('easysdi_account_id').disabled = false;
			}
			else
			{
				document.getElementById('password').disabled = false;
				document.getElementById('user').disabled = false;
				document.getElementById('easysdi_account_id').disabled = true;
				document.getElementById('easysdi_account_id').value = '0';
			}
		}		
		</script>		
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
<?php
		echo $tabs->startPane("LocationPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"LocationPane");

		?>		
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_TEXT_JOOMLA"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td ><?php echo JText::_("EASYSDI_LOCATION_ID"); ?> : </td>
								<td><?php echo $rowLocation->id; ?></td>
								<input type="hidden" name="id" value="<?php echo $id;?>">								
							</tr>			

							<tr>
								<td><?php echo JText::_("EASYSDI_WFS_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="wfs_url" value="<?php echo $rowLocation->wfs_url; ?>" /></td>
							</tr>
							
							<tr>							
								<td><?php echo JText::_("EASYSDI_FEATURETYPE_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="feature_type_name" value="<?php echo $rowLocation->feature_type_name; ?>" /></td>
							</tr>
						
							<tr>
							
								<td><?php echo JText::_("EASYSDI_LOCATION_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="location_name" value="<?php echo $rowLocation->location_name; ?>" /></td>							
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_LOCATION_DESC"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="location_desc" value="<?php echo $rowLocation->location_desc; ?>" /></td>							
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_LOCATION_NAME_FIELD_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name_field_name" value="<?php echo $rowLocation->name_field_name; ?>" /></td>							
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_LOCATION_ID_FIELD_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="id_field_name" value="<?php echo $rowLocation->id_field_name; ?>" /></td>							
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_LOCATION_FILTER_FIELD_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="filter_field_name" value="<?php echo $rowLocation->filter_field_name; ?>" /></td>
								<?php
									$perimList = array();
									$perimList [] = JHTML::_('select.option','-1', JText::_("EASYSDI_PERIM_LIST") );
									$database->setQuery( "SELECT id AS value, location_name AS text FROM #__easysdi_location_definition order by location_name" );
									$perimList = array_merge($perimList, $database->loadObjectList());
		
		
															?>
								<td><?php echo JText::_("EASYSDI_LOCATION_FILTER_FIELD_NAME_DEPENDS_OF"); ?></td>
								
								<td><?php echo JHTML::_("select.genericlist",$perimList, 'id_location_filter', 'size="1" class="inputbox" onChange="javascript:chooseLocationAsFilter(this.value);"', 'value', 'text', $rowLocation->id_location_filter ); ?></td>
								<td>
								
								
								</td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_LOCATION_LOCALISATION"); ?> : </td>
							<?php
							if($is_filter == false)
							{
							?>
							<td><select name="is_localisation" > <option value="1" <?php if($rowLocation->is_localisation == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
								<option value="0" <?php if($rowLocation->is_localisation == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select>
							</td>
							<?php
							}
							else
							{
							?>
							<td colspan="2"><select name="is_localisation" disabled >  
								<option value="0" selected ><?php echo JText::_("EASYSDI_FALSE"); ?></option></select>
								<?php echo JText::_("EASYSDI_LOCATION_DISABLE_VISIBLE_REASON"); ?>
							</td>
							<?php
							}
							?>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_LOCATION_SEARCHBOX"); ?> : </td>
							<td><select name="searchbox" > <option value="1" <?php if($rowLocation->searchbox == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
								<option value="0" <?php if($rowLocation->searchbox == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select>
							</td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_PERIMETER_SEARCHBOX_ALLOW_MULTIPLE_OCCURENCES"); ?> : </td>
							<td><select name="allowMultipleSelection" > <option value="1" <?php if($rowLocation->allowMultipleSelection == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
								<option value="0" <?php if($rowLocation->allowMultipleSelection == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select>
							</td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_LOCATION_MAXFEATURES"); ?> : </td>
							<td><input type name="maxfeatures"  value="<?php echo $rowLocation->maxfeatures ?>"> 
							</td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_LOCATION_SORT"); ?> : </td>
							<td><select name="sort" > <option value="1" <?php if($rowLocation->sort == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
								<option value="0" <?php if($rowLocation->sort == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select>
							</td>
							</tr>
							
							<tr>
							<td colspan ="3">
							<fieldset>
							<legend><?php echo JText::_("EASYSDI_BASE_MAP_AUTHENTICATION"); ?></legend>
								<table>
								<tr>
									<td >
										<input type="radio" name="service_type" value="via_proxy" onclick="javascript:displayAuthentication();" <?php if ($rowLocation->easysdi_account_id) echo "checked";?>>
									</td>
									<td colspan="2">
										<?php echo JText::_("EASYSDI_BASEMAP_VIA_PROXY"); ?>
									</td>
								</tr>
								<tr>
									<td></td>
									<td><?php echo JText::_("EASYSDI_BASEMAP_EASYSDI_ACCOUNT"); ?> : </td>
									<td><?php $enable = $rowLocation->easysdi_account_id? "" : "disabled"  ; echo JHTML::_("select.genericlist",$rowsAccount, 'easysdi_account_id', 'size="1" class="inputbox" onChange="" '.$enable , 'value', 'text',$rowLocation->easysdi_account_id); ?></td>
								</tr>
								<tr>
									<td >
									 	<input type="radio" name="service_type" value="direct" onclick="javascript:displayAuthentication();" <?php if ($rowLocation->user) echo "checked";?>> 
								 	</td>
								 	<td colspan="2">
									 	 <?php echo JText::_("EASYSDI_BASEMAP_DIRECT"); ?>
								 	</td>
							 	<tr>
								<tr>
									<td></td>
									<td><?php echo JText::_("EASYSDI_BASEMAP_USER"); ?> : </td>
									<td><input <?php if (!$rowLocation->user){echo "disabled";} ?> class="inputbox" type="text" size="50" maxlength="100" name="user" id="user" value="<?php echo $rowLocation->user; ?>" /></td>							
								</tr>							
								<tr>
									<td></td>
									<td><?php echo JText::_("EASYSDI_BASEMAP_PASSWORD"); ?> : </td>
									<td><input <?php if (!$rowLocation->user){echo "disabled";} ?> class="inputbox" type="password" size="50" maxlength="100" name="password" id="password" value="<?php echo $rowLocation->password; ?>" /></td>							
								</tr>
								
								</table>
							</fieldset>	
							<td>	
							</tr>			
						</table>
					</fieldset>
				</td>
			</tr>
			
		</table>
		
		
		<?php
		echo $tabs->endPanel();
		echo $tabs->endPane();		
		?>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
	
	
	
	function listLocation($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_LOCATION"));
				  
		$search = JRequest::getVar("search","");
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>
				<td align="right">
					<b><?php echo JText::_("EASYSDI_FILTER");?></b>&nbsp;
					<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="javascript:submitbutton('listLocation');" />			
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listLocation\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_LOCATION_DEF"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("EASYSDI_LOCATION_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_LOCATION_WFS_URL"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_LOCATION_LOCATION_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_LOCATION_LOCATION_DESC"); ?></th>
			</tr>
		</thead>
		<tbody>		
<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];	  				
?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
								
				<td><?php echo $row->id; ?></td>
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editLocation')"><?php echo $row->wfs_url; ?></td>				
				<td><?php echo $row->location_name; ?></td>
				<td><?php echo $row->location_desc; ?></td>
			</tr>
<?php
			$k = 1 - $k;
		}
		
			?></tbody>
			
		<?php			
		
		if (JRequest::getVar('use_pagination',0))
		{?>
		<tfoot>
		<tr>	
		<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
		<?php
		}
?>
	  	</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listLocation" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  </form>
<?php
		
}	
}
?>