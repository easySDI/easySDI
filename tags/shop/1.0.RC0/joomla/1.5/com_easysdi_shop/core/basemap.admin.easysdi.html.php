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

class HTML_basemap {

	function editBasemapContent( $rowBasemap,$id, $option ){
		
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("EASYSDI_TITLE_EDIT_BASEMAP_CONTENT"), 'generic.png' );
			
		?>				
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
<?php
		echo $tabs->startPane("BasemapPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"BasemapPane");

		?>		
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_TEXT_JOOMLA"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td width="100p"><?php echo JText::_("EASYSDI_CONTENT_ID"); ?> : </td>
								<td><?php echo $rowBasemap->id; ?></td>
								<input type="hidden" name="id" value="<?php echo $id;?>">								
							</tr>			
							<tr>
								<td width="100p"><?php echo JText::_("EASYSDI_BASEMAP_CONTENT_ID"); ?> : </td>
								<td><?php echo $rowBasemap->basemap_def_id; ?></td>
								<input type="hidden" name="basemap_def_id" value="<?php echo $rowBasemap->basemap_def_id;?>">								
							</tr>
							
							<tr>
								<td><?php echo JText::_("EASYSDI_BASEMAP_PROJECTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="projection" value="<?php echo $rowBasemap->projection; ?>" /></td>
							</tr>
							
							<tr>							
								<td><?php echo JText::_("EASYSDI_BASEMAP_UNIT"); ?> : </td>
								<td><select class="inputbox" name="unit" >
								
								<option <?php if($rowBasemap->unit == 'm') echo "selected" ; ?> value="m"> <?php echo JText::_("EASYSDI_METERS"); ?></option>
								<option <?php if($rowBasemap->unit == 'degrees') echo "selected" ; ?> value="degrees"> <?php echo JText::_("EASYSDI_DEGREES"); ?></option>
								</select>
								</td>
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_BASEMAP_MINRESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="minResolution" value="<?php echo $rowBasemap->minResolution; ?>" /></td>							
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_BASEMAP_MAXRESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxResolution" value="<?php echo $rowBasemap->maxResolution; ?>" /></td>							
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_BASEMAP_MAXEXTENT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxExtent" value="<?php echo $rowBasemap->maxExtent; ?>" /></td>							
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_BASEMAP_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="url" value="<?php echo $rowBasemap->url; ?>" /></td>							
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_BASEMAP_URL_TYPE"); ?> : </td>
								<td><select class="inputbox" name="url_type" >
										<option value="WMS" <?php if($rowBasemap->url_type == 'WMS') echo "selected" ; ?>><?php echo JText::_("EASYSDI_WMS"); ?></option>
										<option value="WMS" <?php if($rowBasemap->url_type == 'WFS') echo "selected" ; ?>><?php echo JText::_("EASYSDI_WFS"); ?></option>
								</select>
								</td>															
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_BASEMAP_IMG_FORMAT"); ?> : </td>
								<td>
									<input class="inputbox" name="img_format" type="text" size="50" maxlength="100" value="<?php echo $rowBasemap->img_format; ?>" />									
								</td>
								<td>ex : image/png</td>															
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_BASEMAP_SINGLE_TILE"); ?> : </td>
								<td><select class="inputbox" name="singletile" >
										<option value="0" <?php if($rowBasemap->singletile == '0') echo "selected" ; ?>><?php echo JText::_("EASYSDI__TRUE"); ?></option>
										<option value="1" <?php if($rowBasemap->singletile == '1') echo "selected" ; ?>><?php echo JText::_("EASYSDI__FALSE"); ?></option>
								</select>
								</td>															
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_BASEMAP_LAYERS"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="layers" value="<?php echo $rowBasemap->layers; ?>" /></td>							
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_BASEMAP_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $rowBasemap->name; ?>" /></td>							
							</tr>
							
							
								<tr>
							
								<td><?php echo JText::_("EASYSDI_BASEMAP_USER"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="user" value="<?php echo $rowBasemap->user; ?>" /></td>							
							</tr>
							
								<tr>
							
								<td><?php echo JText::_("EASYSDI_BASEMAP_PASSWORD"); ?> : </td>
								<td><input class="inputbox" type="password" size="50" maxlength="100" name="password" value="<?php echo $rowBasemap->password; ?>" /></td>							
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
	
	
	
	function listBasemapContent($basemap_id,$use_pagination, $rows, $pageNav,$option){

		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_BASEMAPCONTENT"));
		$order_field = JRequest::getVar ('order_field');
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>
				<td align="right">
					<b><?php echo JText::_("EASYSDI_FILTER");?></b>&nbsp;
					<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="javascript:submitbutton(\'listBasemap\');" />			
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listBasemap\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_BASEMAPCONTENT_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("EASYSDI_BASEMAPCONTENT_ID"); ?></th>
				<th class='title'><a href="javascript:tableOrder('listBasemapContent', 'url');" title="Click to sort by this column"><?php echo JText::_("EASYSDI_BASEMAPCONTENT_URL"); ?></a></th>
				<th class='title'><?php echo JText::_("EASYSDI_BASEMAPCONTENT_LAYER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_BASEMAPCONTENT_PROJECTION"); ?></th>				
				<th class='title'><a href="javascript:tableOrder('listBasemapContent', 'ordering');" title="Click to sort by this column"><?php echo JText::_("EASYSDI_BASEMAPCONTENT_ORDER"); ?></th>
			</tr>
		</thead>
		<tbody>		
<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];	 
			$link = 'index.php?option='.$option.'&task=editBasemapContent&cid[]='.$row->id.'&basemap_def_id='.$basemap_id;	  				
?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
								
				<td><?php echo $row->id; ?></td>
				<td><a href="<?php echo $link ?>"><?php echo $row->url; ?></a></td>
				<td><?php echo $row->layers; ?></td>
				<td><?php echo $row->projection; ?></td>
				<td class="order" nowrap="nowrap">
				<?php
				$disabled = ($order_field == 'ordering'||$order_field == "") ? true : false;
								
					?>
					
							<span><?php echo $pageNav->orderUpIcon($i,  true, 'orderupbasemapcontent', 'Move Up', $disabled);  ?></span>							
							<span><?php echo $pageNav->orderDownIcon($i,1,  true, 'orderdownbasemapcontent', 'Move Down', $disabled);   ?></span>
							
							<?php echo $row->ordering ;?>
							
            		 
            		 <?php
				
				
				?>

            	</td> 
            				
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
	  	<input type="hidden" name="order_field" value="" />
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listBasemapContent" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="basemap_def_id" value="<?php echo $basemap_id; ?>">
	  </form>
<?php
		
}
	
	
	
	
	
	
	
	
	
	
	
	
	function editBasemap( $rowBasemap,$id, $option ){
		
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("EASYSDI_TITLE_EDIT_BASEMAP"), 'generic.png' );
			
		?>				
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
<?php
		echo $tabs->startPane("BasemapPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"BasemapPane");

		?>		
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_TEXT_JOOMLA"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td width="100p"><?php echo JText::_("EASYSDI_Basemap_ID"); ?> : </td>
								<td><?php echo $rowBasemap->id; ?></td>
								<input type="hidden" name="id" value="<?php echo $id;?>">								
							</tr>			

							<tr>
								<td><?php echo JText::_("EASYSDI_BASEMAP_ALIAS"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="alias" value="<?php echo $rowBasemap->alias; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_BASEMAP_PROJECTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="projection" value="<?php echo $rowBasemap->projection; ?>" /></td>
							</tr>
							
							<tr>							
								<td><?php echo JText::_("EASYSDI_BASEMAP_UNIT"); ?> : </td>
								<td><select class="inputbox" name="unit" >								
									<option <?php if($rowBasemap->unit == 'm') echo "selected" ; ?> value="m"> <?php echo JText::_("EASYSDI_METERS"); ?></option>
									<option <?php if($rowBasemap->unit == 'degrees') echo "selected" ; ?> value="degrees"> <?php echo JText::_("EASYSDI_DEGREES"); ?></option>
								</select>
								</td>

							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_BASEMAP_MINRESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="minResolution" value="<?php echo $rowBasemap->minResolution; ?>" /></td>							
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_BASEMAP_MAXRESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxResolution" value="<?php echo $rowBasemap->maxResolution; ?>" /></td>							
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_BASEMAP_MAXEXTENT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxExtent" value="<?php echo $rowBasemap->maxExtent; ?>" /></td>							
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_BASEMAP_IS_DEFAULT"); ?> : </td>
								<td><select class="inputbox" name="def" >
										<option value="0" <?php if($rowBasemap->def == '0') echo "selected" ; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option>
										<option value="1" <?php if($rowBasemap->def == '1') echo "selected" ; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option>
								</select>
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
	
	
	
	function listBasemap($use_pagination, $rows, $pageNav,$option){
	
		
		
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_BASEMAP"));
		
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>
				<td align="right">
					<b><?php echo JText::_("EASYSDI_FILTER");?></b>&nbsp;
					<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="javascript:submitbutton(\'listBasemap\');" />			
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listBasemap\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_BASEMAP_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("EASYSDI_BASEMAP_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_BASEMAP_ALIAS"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_BASEMAP_PROJECTION"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_BASEMAP_UNIT"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_BASEMAP_MAXEXTENT"); ?></th>				
			</tr>
		</thead>
		<tbody>		
<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
			$link = 'index.php?option='.$option.'&task=editBasemap&cid[]='.$row->id;	  				
?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
								
				<td><?php echo $row->id; ?></td>
				<td><a href="<?php echo $link ?>"><?php echo $row->alias; ?></a></td>
				<td><?php echo $row->projection; ?></td>				
				<td><?php echo $row->unit; ?></td>				
				<td><?php echo $row->maxExtent; ?></td>
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
	  	<input type="hidden" name="task" value="listBasemap" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  </form>
<?php
		
}	
}
?>