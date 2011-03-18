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

class HTML_baselayer
{
	function listBaseLayer($use_pagination, $rows, $pageNav, $option)
	{
		JToolBarHelper::title(JText::_("EASYSDI_LIST_baseLayer"));
		$ordering_field = JRequest::getVar ('order_field');
		?>
		<script>
		function tableOrder(task, orderField)
		{
			document.forms['adminForm'].elements['order_field'].value=orderField;
			document.forms['adminForm'].submit();
			return;
		
		}
		</script>
		<form action="index.php" method="GET" name="adminForm">
		<table class="adminlist">
			<thead>
				<tr>
					<th width="20" class='title'><?php echo JText::_("EASYSDI_baseLayer_SHARP"); ?></th>
					<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
					<th class='title'><a href="javascript:tableOrder('baseLayer', 'name');" title="Click to sort by this column"><?php echo JText::_("EASYSDI_baseLayer_NAME"); ?></th>
					<th class='title'><a><?php echo JText::_("EASYSDI_baseLayer_URL"); ?></a></th>
					<th class='title'><?php echo JText::_("EASYSDI_baseLayer_PROJECTION"); ?></th>
					<th class='title'><a href="javascript:tableOrder('baseLayer', 'ordering');" title="Click to sort by this column"><?php echo JText::_("EASYSDI_baseLayer_ORDER"); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			$k = 0;
			$i=0;
			//$id_base='';
			foreach ($rows as $row)
			{
				//$id_base=$row->id_base;
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
					<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
					<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editBaseLayer')"><?php echo stripcslashes($row->name); ?></a></td>
					<td><?php echo $row->url; ?></td>
					<td><?php echo $row->projection; ?></td>
					<td class="order" nowrap="nowrap"><?php
					$disabled = ($ordering_field == 'ordering') ? true : false;
		
					?> <span><?php echo $pageNav->orderUpIcon($i,  true, 'orderupbasemaplayer', 'Move Up', $disabled);  ?></span> <span><?php echo $pageNav->orderDownIcon($i,1,  true, 'orderdownbasemaplayer', 'Move Down', $disabled);   ?></span>
		
					<?php echo $row->ordering;?> <?php
		
		
					?></td>
				</tr>
				<?php
				$k = 1 - $k;
				$i++;
			}
		
			?>
			</tbody>
		
			<?php
		
			if ($use_pagination)
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
		<input type="hidden" name="order_field" value="<?php echo $ordering_field;?>" /> 
		<input type="hidden" name="task" value="baseLayer" /> 
		<input type="hidden" name="boxchecked" value="0" /> 
		<input type="hidden" name="hidemainmenu" value="0"></form>
		<?php
	}

	function editBaseLayer( $baseLayer, $option )
	{
		global  $mainframe;
		JHTML::script('jquery-1.3.2.min.js', 'components/com_easysdi_map/externals/jquery/');
		if ($baseLayer->id != 0)
		{
			JToolBarHelper::title( JText::_("EASYSDI_baseLayer_EDIT"), 'generic.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("EASYSDI_baseLayer_NEW"), 'generic.png' );
		}
		?>
		<script>
		var $j = jQuery.noConflict();
		$j(document).ready(function() {
			!$j("input[name=resolutionOverScale]:radio").change(
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
			if(pressbutton == "saveBaseLayer")
			{
				
				if (document.getElementById('name').value == "")
				{
					alert ('<?php echo  JText::_( 'EASYSDI_BASE_CT_NAME_VALIDATION_ERROR');?>');	
					return;
				}
				
				else if (document.getElementById('url').value == "")
				{
					alert ('<?php echo  JText::_( 'EASYSDI_BASE_CT_URL_VALIDATION_ERROR');?>');	
					return;
				}
				else if (document.getElementById('layers').value == "")
				{
					alert ('<?php echo  JText::_( 'EASYSDI_BASE_CT_LAYER_VALIDATION_ERROR');?>');	
					return;
				}
				else if (document.getElementById('projection').value == "")
				{	
					alert ('<?php echo  JText::_( 'EASYSDI_BASE_CT_PROJECTION_VALIDATION_ERROR');?>');	
					return;
				}
				else if (false && document.getElementById('maxextent').value == "")
				{
					alert ('<?php echo  JText::_( 'EASYSDI_BASE_CT_MAX_EXTENT_VALIDATION_ERROR');?>');	
					return;
				}
				else if (false && $j('#resolutionoverscale0').attr('checked') && document.getElementById('minscale').value == "")
				{
					alert ('<?php echo  JText::_( 'EASYSDI_BASE_CT_MIN_SCALE_VALIDATION_ERROR');?>');	
					return;
				}
				else if (false && $j('#resolutionoverscale0').attr('checked') && document.getElementById('maxscale').value == "")
				{
					alert ('<?php echo  JText::_( 'EASYSDI_BASE_CT_MAX_SCALE_VALIDATION_ERROR');?>');	
					return;
				}
				else if ($j('#resolutionoverscale1').attr('checked') && document.getElementById('resolutions').value == "")
				{
					alert ('<?php echo  JText::_( 'EASYSDI_BASE_CT_RESOLUTION_VALIDATION_ERROR');?>');	
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
						<td class="key"><?php echo JText::_("EASYSDI_baseLayer_ID"); ?></td>
						<td><?php echo $baseLayer->id; ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("EASYSDI_baseLayer_NAME"); ?></td>
						<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" id="name" value="<?php echo stripslashes($baseLayer->name); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("EASYSDI_baseLayer_LAYERS"); ?></td>
						<td><input class="inputbox" type="text" size="50" maxlength="100" name="layers" id="layers" value="<?php echo $baseLayer->layers; ?>" /></td>
						<td><?php echo JText::_("EASYSDI_OVERLAY_LAYERS_SEPARATOR"); ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("EASYSDI_baseLayer_PROJECTION"); ?></td>
						<td><input class="inputbox" type="text" size="50" maxlength="100" name="projection" id="projection" value="<?php echo $baseLayer->projection; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("EASYSDI_baseLayer_IMG_FORMAT"); ?></td>
						<td><input class="inputbox" name="imgformat" id="imgformat" type="text" size="50" maxlength="100" value="<?php echo $baseLayer->imgformat; ?>" />
						</td>
						<td>ex : image/png</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("EASYSDI_baseLayer_UNIT"); ?></td>
						<td><select class="inputbox" name="unit">
							<option <?php if($baseLayer->unit == 'm') echo "selected" ; ?> value="m"><?php echo JText::_("EASYSDI_METERS"); ?></option>
							<option <?php if($baseLayer->unit == 'degrees') echo "selected" ; ?> value="degrees"><?php echo JText::_("EASYSDI_DEGREES"); ?></option>
						</select></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("EASYSDI_baseLayer_MAXEXTENT"); ?></td>
						<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxextent" id="maxextent" value="<?php echo $baseLayer->maxextent; ?>" /></td>
					</tr>
					<tr>
						<td colspan="2"><input type="radio" id="resolutionoverscale" name="resolutionoverscale" value="0"
						<?php if ($baseLayer->resolutionoverscale == 0) echo "checked=\"checked\""; ?> /> <?php echo JText::_("EASYSDI_BASE_SCALES"); ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("EASYSDI_BASE_MIN_SCALE"); ?></td>
						<td><input class="inputbox scales" type="text" size="50" maxlength="100" name="minscale" id="minscale"
						<?php if ($baseLayer->resolutionoverscale == 1) echo 'disabled' ?> value="<?php echo $baseLayer->minscale; ?>" /></td>
					</tr>
					<tr class="scales">
						<td class="key"><?php echo JText::_("EASYSDI_BASE_MAX_SCALE"); ?></td>
						<td><input class="inputbox scales" name="maxscale" id="maxscale" type="text" size="50" maxlength="100"
						<?php if ($baseLayer->resolutionoverscale == 1) echo 'disabled' ?> value="<?php echo $baseLayer->maxscale; ?>" /></td>
					</tr>
					<tr>
						<td colspan="2"><input type="radio" id="resolutionoverscale1" name="resolutionoverscale" value="1"
						<?php if ($baseLayer->resolutionoverscale == 1) echo "checked=\"checked\""; ?> /> <?php echo JText::_("EASYSDI_BASE_RESOLUTIONS"); ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("EASYSDI_BASE_RESOLUTIONS"); ?></td>
						<td style=""><textarea id="resolutions" class="textarea resolutions" style="height: 200px; width: 500px;" name="resolutions" size="50"
							maxlength="4000" <?php if ($baseLayer->resolutionOverScale == 0) echo 'disabled' ?>><?php echo $baseLayer->resolutions; ?></textarea></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("EASYSDI_BASE_CACHE"); ?></td>
						<td><input class="checkbox" name="cache" value="1" type="checkbox" <?php if ($baseLayer->cache == 1) echo "checked=\"checked\""; ?> /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("EASYSDI_baseLayer_URL"); ?></td>
						<td><input class="inputbox" type="text" size="50" maxlength="100" name="url" id="url" value="<?php echo $baseLayer->url; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("EASYSDI_baseLayer_TILE"); ?></td>
						<td><input class="checkbox" name="singletile" value="1" type="checkbox" <?php if ($baseLayer->singletile == 1) echo "checked=\"checked\""; ?> /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("EASYSDI_baseLayer_CUSTOM_STYLE_ENABLED"); ?></td>
						<td><input class="checkbox" name="customstyle" value="0" type="checkbox" <?php if ($baseLayer->customstyle == 1) echo "checked=\"checked\""; ?> /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("EASYSDI_baseLayer_VISIBILITY"); ?></td>
						<td><input class="checkbox" name="defaultvisibility" value="1" type="checkbox"
						<?php if ($baseLayer->defaultvisibility == 1) echo "checked=\"checked\""; ?> /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("EASYSDI_baseLayer_OPACITY"); ?></td>
						<td><input class="inputbox" type="text" size="50" maxlength="100" name="defaultopacity" id="defaultopacity"
							value="<?php echo $baseLayer->defaultopacity; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("EASYSDI_baseLayer_METADATA"); ?></td>
						<td><input class="inputbox" type="text" size="50" maxlength="500" name="metadataurl" id="metadataurl"
							value="<?php echo $baseLayer->metadataurl; ?>" /></td>
					</tr>
				</table>
				</fieldset>
				</td>
			</tr>
		</table>
		
		<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
		<input type="hidden" name="id" value="<?php echo $baseLayer->id;?>"> 
		<input type="hidden" name="ordering" value="<?php echo $baseLayer->ordering;?>">
		<input type="hidden" name="task" value="saveBaseLayer" />
		</form>
		<?php
	}
}
?>