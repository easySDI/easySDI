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
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.jsLoaderUtil.php');


class HTML_overlay
{
	function listOverlay( $rows,$lists, $pageNav,$search, $filter_order_Dir, $filter_order,  $option)
	{
		global  $mainframe;
		$filter_group_id = $mainframe->getUserStateFromRequest( $option.$overlay.'filter_group_id',	'filter_group_id',	-1,	'int' );
		JToolBarHelper::title(JText::_("MAP_LIST_OVERLAY_CONTENT"), 'map.png');
		?>
		<form action="index.php" method="GET" name="adminForm">
		<table width="100%">
			<tr>
				<td class="key"  width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchOverlay" id="searchOverlay" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchOverlay').value=''; this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
				<td align="right" width="100%" nowrap="nowrap">
					<?php
					echo $lists['group_id'];
					?>
				</td>
			</tr>
		</table>
		<table class="adminlist">
			<thead>
				<tr>
					<th width="20" class='title'><?php echo JText::_("MAP_OVERLAY_CONTENT_SHARP"); ?></th>
					<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
					<th width="190" class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_OVERLAY_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
					<th width="180" class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_OVERLAY_GROUP"), 'group_id', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_PUBLISHED"), 'published', @$filter_order_Dir, @$filter_order); ?></th>					
					
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_OVERLAY_LAYERS"), 'layers', @$filter_order_Dir, @$filter_order); ?></th>
					<?php 
					if ( $filter_group_id != -1){?>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_OVERLAY_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
					<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderOverlay' ); ?></th>
					<?php 
					} else {?>
					<th class='title'><?php echo JText::_("MAP_OVERLAY_ORDER"); ?></th>
					<?php }?>
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
					<td align="center"><?php echo $i+ $pageNav->limitstart+1;?></td>
					<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
					<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editOverlay')"><?php echo stripcslashes($row->name); ?></a></td>
					<td><?php echo $row->group_name; ?></td>
					<td> <?php echo JHTML::_('grid.published',$row,$i, 'tick.png', 'publish_x.png', 'overlay_'); ?></td>
					<td><?php echo $row->layers; ?></td>
					
					
					
					<td width="100px" align="right" >
					<?php  $ordering = ($filter_order == 'ordering')  ? true : false;
						   $byGroup = ($filter_group_id == -1)? false: true;
						   $disabled = ($ordering && $filter_group_id != -1) ?  '' : 'disabled="disabled"';
					if ($byGroup &&  $filter_order_Dir=="asc"){	
						?>
							<span><?php echo $pageNav->orderUpIcon($i, true, 'orderupoverlay', 'Move Up', $ordering ); ?></span>
					        <span><?php echo $pageNav->orderDownIcon($i, count($rows), true, 'orderdownoverlay', 'Move Down', $ordering ); ?></span>
						<?php
					}
					elseif ($byGroup  ){
						?>
							 <span><?php echo $pageNav->orderUpIcon($i, true, 'orderdownoverlay', 'Move Down', $ordering ); ?></span>
			 		         <span><?php echo $pageNav->orderDownIcon($i, count($rows), true, 'orderupoverlay', 'Move Up', $ordering); ?></span>
						<?php
					}
					 ?>
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
					</td>
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
		<input type="hidden" name="task" value="overlay" /> 
		<input type="hidden" name="boxchecked" value="0" /> 
		<input type="hidden" name="hidemainmenu" value="0">
		<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
		</form>
		<?php
	}

	function editOverlay( $overlay_content,$createUser, $updateUser,$rowsGroup,$fieldsLength, $option )
	{
		global  $mainframe;
		$jsLoader =JSLOADER_UTIL::getInstance();
		
		JHTML::script('jquery-1.3.2.min.js',  $jsLoader->getPath("map","jquery"));//'components/com_easysdi_map/externals/jquery/');
		if ($overlay_content->id != 0)
		{
			JToolBarHelper::title( JText::_("MAP_OVERLAY_CONTENT_EDIT").': <small><small>['. JText::_("CORE_EDIT").']</small></small>', 'addedit.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("MAP_OVERLAY_CONTENT_EDIT").': <small><small>['. JText::_("CORE_NEW").']</small></small>', 'addedit.png' );
		}
		?>
		<script>
		var $j = jQuery.noConflict();
		$j(document).ready(function() {
			!$j("input[name=resolutionoverscale]:radio").change(
					function(e){
						if (e.target.value==0)
						{
							  $j(".scales").removeAttr('disabled'); 
							  $j(".resolutions").attr("disabled","disabled");
						}
						else if (e.target.value==1)  {
							$j(".resolutions").removeAttr('disabled');
							$j(".scales").attr("disabled","disabled");
						}
						}
					);
		});
		function submitbutton(pressbutton)
		{
			if(pressbutton == "saveOverlay")
			{
				if (document.getElementById('projection').value == "")
				{	
					alert ('<?php echo  JText::_( 'MAP_OVL_CT_PROJECTION_VALIDATION_ERROR');?>');	
					return;
				}
				else if (false && $j('#resolutionoverscale0').attr('checked') && document.getElementById('minscale').value == "")
				{
					alert ('<?php echo  JText::_( 'MAP_OVL_CT_MIN_SCALE_VALIDATION_ERROR');?>');	
					return;
				}
				else if (false && $j('#resolutionoverscale0').attr('checked') && document.getElementById('maxscale').value == "")
				{
					alert ('<?php echo  JText::_( 'MAP_OVL_CT_MAX_SCALE_VALIDATION_ERROR');?>');	
					return;
				}
				else if ($j('#resolutionoverscale1').attr('checked') && document.getElementById('resolutions').value == "")
				{
					alert ('<?php echo  JText::_( 'MAP_OVL_CT_RESOLUTION_VALIDATION_ERROR');?>');	
					return;
				}
				else if (false && document.getElementById('maxextent').value == "")
				{
					alert ('<?php echo  JText::_( 'MAP_OVL_CT_MAX_EXTENT_VALIDATION_ERROR');?>');	
					return;
				}
				else if (document.getElementById('url').value == "")
				{
					alert ('<?php echo  JText::_( 'MAP_OVL_CT_URL_VALIDATION_ERROR');?>');	
					return;
				}
				else if (document.getElementById('layers').value == "")
				{
					alert ('<?php echo  JText::_( 'MAP_OVL_CT_LAYER_VALIDATION_ERROR');?>');	
					return;
				}
				else if (document.getElementById('name').value == "")
				{
					alert ('<?php echo  JText::_( 'MAP_OVL_CT_NAME_VALIDATION_ERROR');?>');	
					return;
				}			
				else
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
						<td class="key"><?php echo JText::_("MAP_OVERLAY_NAME"); ?></td>
						<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['name'];?>" name="name" id="name"
							value="<?php echo stripcslashes($overlay_content->name); ?>" /></td>
					</tr>
					
					<tr>
						<td class="key"><?php echo JText::_("CORE_PUBLISHED"); ?> : </td>
						<td><?php echo JHTML::_('select.booleanlist', 'published', '',  $overlay_content->published); ?> </td>																
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_CONTENT_GROUP"); ?></td>
						<td><?php echo JHTML::_("select.genericlist",$rowsGroup, 'group_id', 'size="1" class="inputbox" ', 'value', 'text',$overlay_content->group_id); ?>
					
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_URL"); ?></td>
						<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['url'];?>" name="url" id="url" value="<?php echo $overlay_content->url; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_URL_TYPE"); ?></td>
						<td><select class="inputbox" name="type">
							<option value="WMS" <?php if($overlay_content->type == 'WMS') echo "selected" ; ?>><?php echo JText::_("MAP_WMS"); ?></option>
							<option value="WFS" <?php if($overlay_content->type == 'WFS') echo "selected" ; ?>><?php echo JText::_("MAP_WFS"); ?></option>
						</select></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_URL_VERSION"); ?></td>
						<td>
							<select class="inputbox" name="version" id="version">
								<option <?php if($overlay_content->version == '1.0.0') echo "selected" ; ?> value="1.0.0"><?php echo "1.0.0"; ?></option>
								<option <?php if($overlay_content->version == '1.1.0') echo "selected" ; ?> value="1.1.0"><?php echo "1.1.0"; ?></option>
								<option <?php if($overlay_content->version == '1.1.1') echo "selected" ; ?> value="1.1.1"><?php echo "1.1.1"; ?></option>
								<option <?php if($overlay_content->version == '1.3.0') echo "selected" ; ?> value="1.3.0"><?php echo "1.3.0"; ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_LAYERS"); ?></td>
						<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['layers'];?>" name="layers" id="layers" value="<?php echo $overlay_content->layers; ?>" /></td>
						<td><?php echo JText::_("MAP_OVERLAY_LAYERS_SEPARATOR"); ?></td>
					</tr>
					
					
					
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_IMG_FORMAT"); ?></td>
						<td><input class="inputbox" name="imgformat" id="imgformat" type="text" size="100" maxlength="<?php echo $fieldsLength['imgformat'];?>"
							value="<?php echo $overlay_content->imgformat; ?>" /></td>
						<td>ex : image/png</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASE_CACHE"); ?></td>
						<td><input class="checkbox" name="cache" value="1" type="checkbox" <?php if ($overlay_content->cache == 1) echo "checked=\"checked\""; ?> /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_TILE"); ?></td>
						<td><input class="checkbox" name="singletile" value="1" type="checkbox"
						<?php if ($overlay_content->singletile == 1) echo "checked=\"checked\""; ?> /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASELAYER_CUSTOM_STYLE_ENABLED"); ?></td>
						<td><input class="checkbox" name="customstyle" value="1" type="checkbox"
						<?php if ($overlay_content->customstyle == 1) echo "checked=\"checked\""; ?> /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_VISIBILITY"); ?></td>
						<td><input class="checkbox" name="defaultvisibility" value="1" type="checkbox"
						<?php if ($overlay_content->defaultvisibility == 1) echo "checked=\"checked\""; ?> /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_OPACITY"); ?></td>
						<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['defaultopacity'];?>" name="defaultopacity" id="defaultopacity"
							value="<?php echo $overlay_content->defaultopacity; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_METADATA"); ?></td>
						<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['metadataurl'];?>" name="metadataurl" id="metadataurl"
							value="<?php echo $overlay_content->metadataurl; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_PROJECTION"); ?></td>
						<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['projection'];?>" name="projection" id="projection"
							value="<?php echo $overlay_content->projection; ?>" /></td>
					</tr>
		
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_UNIT"); ?></td>
						<td><select class="inputbox" name="unit">
							<option <?php if($overlay_content->unit == 'm') echo "selected" ; ?> value="m"><?php echo JText::_("MAP_METERS"); ?></option>
							<option <?php if($overlay_content->unit == 'degrees') echo "selected" ; ?> value="degrees"><?php echo JText::_("MAP_DEGREES"); ?></option>
						</select></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_MAXEXTENT"); ?></td>
						<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['maxextent'];?>" name="maxextent" id="maxextent"
							value="<?php echo $overlay_content->maxextent; ?>" /></td>
					</tr>
					<tr>
						<td colspan="2"><input type="radio" id="resolutionoverscale0" name="resolutionoverscale" value="0"
						<?php if ($overlay_content->resolutionoverscale == 0) echo "checked=\"checked\""; ?> /> <?php echo JText::_("MAP_BASE_SCALES"); ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASE_MIN_SCALE"); ?></td>
						<td><input class="inputbox scales" type="text" size="100" maxlength="<?php echo $fieldsLength['minscale'];?>" name="minscale" id="minscale"
						<?php if ($overlay_content->resolutionoverscale == 1) echo 'disabled' ?> value="<?php echo $overlay_content->minscale; ?>" /></td>
					</tr>
					<tr class="scales">
						<td class="key"><?php echo JText::_("MAP_BASE_MAX_SCALE"); ?></td>
						<td><input class="inputbox scales" name="maxscale" id="maxscale" type="text" size="100" maxlength="<?php echo $fieldsLength['maxscale'];?>"
						<?php if ($overlay_content->resolutionoverscale == 1) echo 'disabled' ?> value="<?php echo $overlay_content->maxscale; ?>" /></td>
					</tr>
					<tr>
						<td colspan="2"><input type="radio" id="resolutionoverscale1" name="resolutionoverscale" value="1"
						<?php if ($overlay_content->resolutionoverscale == 1) echo "checked=\"checked\""; ?> /> <?php echo JText::_("MAP_BASE_RESOLUTIONS"); ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASE_RESOLUTIONS"); ?></td>
						<td style=""><textarea class="textarea resolutions" style="height: 200px; width: 500px;" id="resolutions" name="resolutions" size="100"
							maxlength="4000" <?php if ($overlay_content->resolutionoverscale == 0) echo 'disabled' ?>><?php echo $overlay_content->resolutions; ?></textarea></td>
					</tr>
					
				</table>
				</fieldset>
				</td>
			</tr>
		</table>
		<br></br>
		<table border="0" cellpadding="3" cellspacing="0">
		<?php
		if ($overlay_content->created)
		{ 
		?>
			<tr>
				<td><?php echo JText::_("CORE_CREATED"); ?> : </td>
				<td><?php if ($overlay_content->created) {echo date('d.m.Y h:i:s',strtotime($overlay_content->created));} ?></td>
				<td>, </td>
				<td><?php echo $createUser; ?></td>
			</tr>
		<?php
		}
		if ($overlay_content->updated and $overlay_content->updated<> '0000-00-00 00:00:00')
		{ 
		?>
			<tr>
				<td><?php echo JText::_("CORE_UPDATED"); ?> : </td>
				<td><?php if ($overlay_content->updated and $overlay_content->updated<> 0) {echo date('d.m.Y h:i:s',strtotime($overlay_content->updated));} ?></td>
				<td>, </td>
				<td><?php echo $updateUser; ?></td>
			</tr>
		<?php
		}
		?>		
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
		<input type="hidden" name="task" value="saveOverlay" />
		<input type="hidden" name="id" value="<?php echo $overlay_content->id; ?>" />
		<input type="hidden" name="guid" value="<?php echo $overlay_content->guid?>" />
		<input type="hidden" name="ordering" value="<?php echo $overlay_content->ordering; ?>" />
		<input type="hidden" name="created" value="<?php echo $overlay_content->created;?>" />
		<input type="hidden" name="createdby" value="<?php echo $overlay_content->createdby; ?>" /> 
		<input type="hidden" name="updated" value="<?php echo $overlay_content->created; ?>" />
		<input type="hidden" name="updatedby" value="<?php echo $overlay_content->createdby; ?>" /> 
		</form>
		<?php
	}

	function listOverlayGroup( $rows, $pageNav,$search, $filter_order_Dir, $filter_order, $option)
	{
		JToolBarHelper::title(JText::_("MAP_LIST_OVERLAY_GROUP"), 'map.png');
		?>
		<form action="index.php" method="GET" name="adminForm">
		<table width="100%">
			<tr>
				<td class="key"  width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchOverlayGroup" id="searchOverlayGroup" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchOverlayGroup').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
		<table class="adminlist">
			<thead>
				<tr>
					<th width="20" class='title'><?php echo JText::_("MAP_OVERLAY_GROUP_SHARP"); ?></th>
					<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_OVERLAY_GROUP_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_OVERLAY_GROUP_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_PUBLISHED"), 'published', @$filter_order_Dir, @$filter_order); ?></th>					
					
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_OVERLAY_GROUP_OPEN"), 'open', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_OVERLAY_GROUP_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
					<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderOverlayGroup' ); ?></th>
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
					<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editOverlayGroup')"><?php echo $row->name; ?></a></td>
					<td><?php echo $row->description; ?></a></td>
					<td> <?php echo JHTML::_('grid.published',$row,$i, 'tick.png', 'publish_x.png', 'overlaygroup_'); ?></td>
					
					
					<td><?php if($row->open == 1){echo JText::_("MAP_YES");}else{echo JText::_("MAP_NO");} ?></td>
					<td width="100px" align="right" >
					<?php  $ordering = ($filter_order == 'ordering')  ? true : false;
					if ($filter_order_Dir=="asc"){						
					?>
						<span><?php echo $pageNav->orderUpIcon($i, true, 'orderupoverlaygroup', 'Move Up', $ordering ); ?></span>
				        <span><?php echo $pageNav->orderDownIcon($i, count($rows), true, 'orderdownoverlaygroup', 'Move Down', $ordering ); ?></span>
					<?php		
					}
					else{		
					?>
						 <span><?php echo $pageNav->orderUpIcon($i, true, 'orderdownoverlaygroup', 'Move Down', $ordering ); ?></span>
	 		             <span><?php echo $pageNav->orderDownIcon($i, count($rows), true, 'orderupoverlaygroup', 'Move Up', $ordering); ?></span>
					<?php
					}
					$disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
					</td>
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
		<input type="hidden" name="task" value="overlayGroup" /> 
		<input type="hidden" name="boxchecked" value="0" /> 
		<input type="hidden" name="hidemainmenu" value="0"> 
		<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
		</form>
		<?php
	}

	function editOverlayGroup ($overlay_group,$createUser, $updateUser,$fieldsLength, $option)
	{
		if ($overlay_group->id != 0)
		{
			JToolBarHelper::title( JText::_("MAP_OVERLAY_GROUP_EDIT").': <small><small>['. JText::_("CORE_EDIT").']</small></small>', 'addedit.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("MAP_OVERLAY_GROUP_EDIT").': <small><small>['. JText::_("CORE_NEW").']</small></small>', 'addedit.png' );
		}
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
				<fieldset>
				<table class="admintable">
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_GROUP_NAME"); ?> :</td>
						<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['name'];?>" name="name" value="<?php echo $overlay_group->name; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("CORE_PUBLISHED"); ?> : </td>
						<td><?php echo JHTML::_('select.booleanlist', 'published', '',  $overlay_group->published); ?> </td>																
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_GROUP_DESCRIPTION"); ?> :</td>
						<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['description'];?>" name="description" value="<?php echo $overlay_group->description; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_OVERLAY_GROUP_OPEN"); ?></td>
						<td><input class="checkbox" name="open" value="1" type="checkbox" <?php if ($overlay_group->open == 1) echo "checked=\"checked\""; ?> /></td>
					</tr>
		
				</table>
				</fieldset>
				</td>
			</tr>
		</table>
		<br></br>
		<table border="0" cellpadding="3" cellspacing="0">
		<?php
		if ($overlay_group->created)
		{ 
		?>
			<tr>
				<td><?php echo JText::_("CORE_CREATED"); ?> : </td>
				<td><?php if ($overlay_group->created) {echo date('d.m.Y h:i:s',strtotime($overlay_group->created));} ?></td>
				<td>, </td>
				<td><?php echo $createUser; ?></td>
			</tr>
		<?php
		}
		if ($overlay_group->updated and $overlay_group->updated<> '0000-00-00 00:00:00')
		{ 
		?>
			<tr>
				<td><?php echo JText::_("CORE_UPDATED"); ?> : </td>
				<td><?php if ($overlay_group->updated and $overlay_group->updated<> 0) {echo date('d.m.Y h:i:s',strtotime($overlay_group->updated));} ?></td>
				<td>, </td>
				<td><?php echo $updateUser; ?></td>
			</tr>
		<?php
		}
		?>		
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
		<input type="hidden" name="task" value="saveOverlayGroup" />
		<input type="hidden" name="id" value="<?php echo $overlay_group->id; ?>" />
		<input type="hidden" name="guid" value="<?php echo $overlay_group->guid?>" />
		<input type="hidden" name="ordering" value="<?php echo $overlay_group->ordering; ?>" />
		<input type="hidden" name="created" value="<?php echo $overlay_group->created;?>" />
		<input type="hidden" name="createdby" value="<?php echo $overlay_group->createdby; ?>" /> 
		<input type="hidden" name="updated" value="<?php echo $overlay_group->created; ?>" />
		<input type="hidden" name="updatedby" value="<?php echo $overlay_group->createdby; ?>" /> 
		</form>
		<?php
	}
}
?>