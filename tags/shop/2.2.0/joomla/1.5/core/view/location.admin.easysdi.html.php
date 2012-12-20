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
		$queryIsFilter = "select * from  #__sdi_location  where filterlocation_id=$rowLocation->id ";
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
		JToolBarHelper::title( JText::_("SHOP_LOCATION_TITLE_EDIT"), 'generic.png' );
			
		?>	
		<script>
		function chooseLocationAsFilter(locationId)
		{
			<?php
			$query = "SELECT * FROM #__sdi_location WHERE islocalisation = 1 ";
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
					alert( "<?php echo JText::_("SHOP_LOCATION_DEF_VALIDATION");?> ");
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
				document.getElementById('account_id').disabled = false;
			}
			else
			{
				document.getElementById('password').disabled = false;
				document.getElementById('user').disabled = false;
				document.getElementById('account_id').disabled = true;
				document.getElementById('account_id').value = '0';
			}
		}		
		</script>		
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
		
		<table class="admintable" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
					<legend><?php echo JText::_("SHOP_GENERAL"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="key"><?php echo JText::_("CORE_ID"); ?> : </td>
								<td><?php echo $rowLocation->id; ?></td>
								<input type="hidden" name="id" value="<?php echo $id;?>">								
							</tr>			
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_LOCATION_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $rowLocation->name; ?>" /></td>							
							</tr>
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_LOCATION_DESC"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="description" value="<?php echo $rowLocation->description; ?>" /></td>							
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_WFS_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="urlwfs" value="<?php echo $rowLocation->urlwfs; ?>" /></td>
							</tr>
							
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_FEATURETYPE_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="featuretype" value="<?php echo $rowLocation->featuretype; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_LOCATION_NAME_FIELD_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="fieldname" value="<?php echo $rowLocation->fieldname; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_LOCATION_ID_FIELD_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="fieldid" value="<?php echo $rowLocation->fieldid; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_LOCATION_FILTER_FIELD_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="fieldfilter" value="<?php echo $rowLocation->fieldfilter; ?>" /></td>
								<?php
									$perimList = array();
									$perimList [] = JHTML::_('select.option','-1', JText::_("SHOP_LOCATION_LIST") );
									$database->setQuery( "SELECT id AS value, name AS text FROM #__sdi_location order by name" );
									$perimList = array_merge($perimList, $database->loadObjectList());
		
		
															?>
								<td ><?php echo JText::_("SHOP_LOCATION_FILTER_FIELD_NAME_DEPENDS_OF"); ?></td>
								
								<td><?php echo JHTML::_("select.genericlist",$perimList, 'filterlocation_id', 'size="1" class="inputbox" onChange="javascript:chooseLocationAsFilter(this.value);"', 'value', 'text', $rowLocation->filterlocation_id ); ?></td>
								<td>
								
								
								</td>
							</tr>
							<tr>
							<td  class="key"><?php echo JText::_("SHOP_LOCATION_LOCALISATION"); ?> : </td>
							<?php
							if($is_filter == false)
							{
							?>
							<td><select name="islocalisation" > <option value="1" <?php if($rowLocation->islocalisation == 1) echo "selected"; ?>><?php echo JText::_("CORE_TRUE"); ?></option> 
								<option value="0" <?php if($rowLocation->islocalisation == 0) echo "selected"; ?>><?php echo JText::_("CORE_FALSE"); ?></option></select>
							</td>
							<?php
							}
							else
							{
							?>
							<td colspan="2"><select name="islocalisation" disabled >  
								<option value="0" selected ><?php echo JText::_("CORE_FALSE"); ?></option></select>
								<?php echo JText::_("SHOP_LOCATION_DISABLE_VISIBLE_REASON"); ?>
							</td>
							<?php
							}
							?>
							</tr>
							<tr>
							<td class="key"><?php echo JText::_("SHOP_LOCATION_SEARCHBOX"); ?> : </td>
							<td><select name="searchbox" > <option value="1" <?php if($rowLocation->searchbox == 1) echo "selected"; ?>><?php echo JText::_("CORE_TRUE"); ?></option> 
								<option value="0" <?php if($rowLocation->searchbox == 0) echo "selected"; ?>><?php echo JText::_("CORE_FALSE"); ?></option></select>
							</td>
							</tr>
							<tr>
							<td class="key"><?php echo JText::_("SHOP_LOCATION_SEARCHBOX_ALLOW_MULTIPLE_OCCURENCES"); ?> : </td>
							<td><select name="allowMultipleSelection" > <option value="1" <?php if($rowLocation->MultipleSelection == 1) echo "selected"; ?>><?php echo JText::_("CORE_TRUE"); ?></option> 
								<option value="0" <?php if($rowLocation->MultipleSelection == 0) echo "selected"; ?>><?php echo JText::_("CORE_FALSE"); ?></option></select>
							</td>
							</tr>
							<tr>
							<td class="key"><?php echo JText::_("SHOP_LOCATION_MAXFEATURES"); ?> : </td>
							<td><input type name="maxfeatures"  value="<?php echo $rowLocation->maxfeatures ?>"> 
							</td>
							</tr>
							<tr>
							<td class="key"><?php echo JText::_("SHOP_LOCATION_SORT"); ?> : </td>
							<td><select name="sort" > <option value="1" <?php if($rowLocation->sort == 1) echo "selected"; ?>><?php echo JText::_("CORE_TRUE"); ?></option> 
								<option value="0" <?php if($rowLocation->sort == 0) echo "selected"; ?>><?php echo JText::_("CORE_FALSE"); ?></option></select>
							</td>
							</tr>
										
						</table>
					</fieldset>
					<fieldset>
							<legend><?php echo JText::_("SHOP_AUTHENTICATION"); ?></legend>
								<table>
								<tr>
									<td >
										<input type="radio" name="service_type" value="via_proxy" onclick="javascript:displayAuthentication();" <?php if ($rowLocation->account_id) echo "checked";?>>
									</td>
									<td  class="key" colspan="2">
										<?php echo JText::_("SHOP_AUTH_VIA_PROXY"); ?>
									</td>
								</tr>
								<tr>
									<td></td>
									<td><?php echo JText::_("SHOP_EASYSDI_ACCOUNT"); ?> : </td>
									<td><?php $enable = $rowLocation->account_id? "" : "disabled"  ; echo JHTML::_("select.genericlist",$rowsAccount, 'account_id', 'size="1" class="inputbox" onChange="" '.$enable , 'value', 'text',$rowLocation->account_id); ?></td>
								</tr>
								<tr>
									<td >
									 	<input type="radio" name="service_type" value="direct" onclick="javascript:displayAuthentication();" <?php if ($rowLocation->user) echo "checked";?>> 
								 	</td>
								 	<td  class="key" colspan="2">
									 	 <?php echo JText::_("SHOP_AUTH_DIRECT"); ?>
								 	</td>
							 	<tr>
								<tr>
									<td></td>
									<td><?php echo JText::_("SHOP_AUTH_USER"); ?> : </td>
									<td><input <?php if (!$rowLocation->user){echo "disabled";} ?> class="inputbox" type="text" size="50" maxlength="100" name="user" id="user" value="<?php echo $rowLocation->user; ?>" /></td>							
								</tr>							
								<tr>
									<td></td>
									<td><?php echo JText::_("SHOP_AUTH_PASSWORD"); ?> : </td>
									<td><input <?php if (!$rowLocation->user){echo "disabled";} ?> class="inputbox" type="password" size="50" maxlength="100" name="password" id="password" value="<?php echo $rowLocation->password; ?>" /></td>							
								</tr>
								
								</table>
							</fieldset>
				</td>
			</tr>			
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="guid" value="<?php echo $rowLocation->guid; ?>" />
		<input type="hidden" name="created" value="<?php echo $rowLocation->created; ?>" />
		<input type="hidden" name="createdby" value="<?php echo $rowLocation->createdby; ?>" />
		<input type="hidden" name="checked_out" value="<?php echo $rowLocation->checked_out; ?>" />
		<input type="hidden" name="checked_out_time" value="<?php echo $rowLocation->checked_out_time; ?>" />
		</form>
	<?php
	}
	
	
	
	function listLocation( $rows, $pageNav,$option,$filter_order_Dir, $filter_order,$search){
	
		$database =& JFactory::getDBO();
		$user	=& JFactory::getUser();
		JToolBarHelper::title(JText::_("SHOP_LOCATION_LIST_LOCATION"));
		
		?>
	<form action="index.php" method="post" name="adminForm">
		<table width="100%">
			<tr>
				<td class="key"  width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchLocation" id="searchLocation" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchLocation').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title' width="10px"><?php echo JText::_("CORE_SHARP"); ?></th>
				<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("SHOP_WFS_URL"), 'urlwfs', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'updated', @$filter_order_Dir, @$filter_order); ?></th>
			</tr>
		</thead>
		<tbody>		
<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
			$link = 'index.php?option='.$option.'&task=editLocation&cid[]='.$row->id;	  				
?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td><?php echo $row->id; ?></td>
				<td>
				<?php 
				if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
				{
					echo $row->name;
				} 
				else 
				{
					?>
					<a href="<?php echo $link;?>"><?php echo $row->name; ?></a>
					<?php
				}
				?>
				</td>
				<td><?php echo $row->description; ?></td>
				<td><?php echo $row->urlwfs; ?></td>				
				<td><?php echo $row->updated; ?></td>	
			</tr>
<?php
			$k = 1 - $k;
		}
		
			?></tbody>
			
		<tfoot>
		<tr>	
		<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
		</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listLocation" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
		
}	
}
?>