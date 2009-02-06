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

class HTML_location {

	function editLocation( $rowLocation,$id, $option ){
		
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("EASYSDI_TITLE_EDIT_LOCATION"), 'generic.png' );
			
		?>				
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
								<td width="100p"><?php echo JText::_("EASYSDI_LOCATION_ID"); ?> : </td>
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
								
								<td><?php echo JHTML::_("select.genericlist",$perimList, 'id_location_filter', 'size="1" class="inputbox"', 'value', 'text', $rowLocation->id_location_filter ); ?></td>
								<td>
								
								
								</td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_LOCATION_LOCALISATION"); ?> : </td>
							<td><select name="is_localisation" > <option value="1" <?php if($rowLocation->is_localisation == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
								<option value="0" <?php if($rowLocation->is_localisation == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select>
							</td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_LOCATION_SEARCHBOX"); ?> : </td>
							<td><select name="searchbox" > <option value="1" <?php if($rowLocation->searchbox == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
								<option value="0" <?php if($rowLocation->searchbox == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select>
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
					<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="javascript:submitbutton(\'listLocation\');" />			
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
				<td><?php echo $row->wfs_url; ?></td>				
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