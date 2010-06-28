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

class HTML_overlay
{
	function listOverlayContent($use_pagination, $rows, $pageNav, $option)
	{
		JToolBarHelper::title(JText::_("EASYSDI_LIST_OVERLAY_CONTENT"));
		$order_field = JRequest::getVar ('order_field');
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
			<th width="20" class='title'><?php echo JText::_("EASYSDI_OVERLAY_CONTENT_SHARP"); ?></th>
			<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class='title'><a href="javascript:tableOrder('overlayContent', 'url');" title="Click to sort by this column"><?php echo JText::_("EASYSDI_OVERLAY_CONTENT_URL"); ?></a></th>
			<th class='title'><a href="javascript:tableOrder('overlayContent', 'name');" title="Click to sort by this column"><?php echo JText::_("EASYSDI_OVERLAY_CONTENT_NAME"); ?></th>
			<th class='title'><?php echo JText::_("EASYSDI_OVERLAY_CONTENT_PROJECTION"); ?></th>
			<th class='title'><a href="javascript:tableOrder('overlayContent', 'order');" title="Click to sort by this column"><?php echo JText::_("EASYSDI_OVERLAY_ORDER"); ?></th>
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
			<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editOverlayContent')"><?php echo $row->url; ?></a></td>
			<td><?php echo stripcslashes($row->name); ?></td>
			<td><?php echo $row->projection; ?></td>
			<td class="order" nowrap="nowrap"><?php
			$disabled = ($order_field == 'order') ? true : false;
			?> <span><?php echo $pageNav->orderUpIcon($i,  true, 'orderupoverlay', 'Move Up', $disabled);  ?></span> <span><?php echo $pageNav->orderDownIcon($i,1,  true, 'orderdownoverlay', 'Move Down', $disabled);   ?></span>
			<?php echo $row->order;?> <?php
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
<input type="hidden" name="option" value="<?php echo $option; ?>" /> <input type="hidden" name="order_field" value="<?php echo $order_field;?>" /> <input
	type="hidden" name="task" value="overlayContent" /> <input type="hidden" name="boxchecked" value="0" /> <input type="hidden" name="hidemainmenu"
	value="0"></form>
	<?php
	}

	function editOverlayContent( $overlay_content,$rowsGroup, $option )
	{
		global  $mainframe;
		JHTML::script('jquery-1.3.2.min.js', 'components/com_easysdi_map/externals/jquery/');

		if ($overlay_content->id != 0)
		{
			JToolBarHelper::title( JText::_("EASYSDI_OVERLAY_CONTENT_EDIT"), 'generic.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("EASYSDI_OVERLAY_CONTENT_NEW"), 'generic.png' );
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
		if(pressbutton == "saveOverlayContent")
		{
			if (document.getElementById('projection').value == "")
			{	
				alert ('<?php echo  JText::_( 'EASYSDI_OVL_CT_PROJECTION_VALIDATION_ERROR');?>');	
				return;
			}
			else if (false && $j('#resolutionOverScale0').attr('checked') && document.getElementById('minScale').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_OVL_CT_MIN_SCALE_VALIDATION_ERROR');?>');	
				return;
			}
			else if (false && $j('#resolutionOverScale0').attr('checked') && document.getElementById('maxScale').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_OVL_CT_MAX_SCALE_VALIDATION_ERROR');?>');	
				return;
			}
			else if ($j('#resolutionOverScale1').attr('checked') && document.getElementById('resolutions').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_OVL_CT_RESOLUTION_VALIDATION_ERROR');?>');	
				return;
			}
			else if (false && document.getElementById('maxExtent').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_OVL_CT_MAX_EXTENT_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('url').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_OVL_CT_URL_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('layers').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_OVL_CT_LAYER_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('name').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_OVL_CT_NAME_VALIDATION_ERROR');?>');	
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
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_CONTENT_ID"); ?></td>
				<td><?php echo $overlay_content->id; ?></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_NAME"); ?></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" id="name"
					value="<?php echo stripcslashes($overlay_content->name); ?>" /></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_LAYERS"); ?></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="layers" id="layers" value="<?php echo $overlay_content->layers; ?>" /></td>
				<td><?php echo JText::_("EASYSDI_OVERLAY_LAYERS_SEPARATOR"); ?></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_CONTENT_GROUP"); ?></td>
				<td><?php echo JHTML::_("select.genericlist",$rowsGroup, 'overlay_group_id', 'size="1" class="inputbox" ', 'value', 'text',$overlay_content->overlay_group_id); ?>
			
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_PROJECTION"); ?></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="projection" id="projection"
					value="<?php echo $overlay_content->projection; ?>" /></td>
			</tr>

			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_UNIT"); ?></td>
				<td><select class="inputbox" name="unit">
					<option <?php if($overlay_content->unit == 'm') echo "selected" ; ?> value="m"><?php echo JText::_("EASYSDI_METERS"); ?></option>
					<option <?php if($overlay_content->unit == 'degrees') echo "selected" ; ?> value="degrees"><?php echo JText::_("EASYSDI_DEGREES"); ?></option>
				</select></td>
			</tr>

			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_MAXEXTENT"); ?></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxExtent" id="maxExtent"
					value="<?php echo $overlay_content->maxExtent; ?>" /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="radio" id="resolutionOverScale0" name="resolutionOverScale" value="0"
				<?php if ($overlay_content->resolutionOverScale == 0) echo "checked=\"checked\""; ?> /> <?php echo JText::_("EASYSDI_BASE_SCALES"); ?></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_BASE_MIN_SCALE"); ?></td>
				<td><input class="inputbox scales" type="text" size="50" maxlength="100" name="minScale" id="minScale"
				<?php if ($overlay_content->resolutionOverScale == 1) echo 'disabled' ?> value="<?php echo $overlay_content->minScale; ?>" /></td>
			</tr>
			<tr class="scales">
				<td class="key"><?php echo JText::_("EASYSDI_BASE_MAX_SCALE"); ?></td>
				<td><input class="inputbox scales" name="maxScale" id="maxScale" type="text" size="50" maxlength="100"
				<?php if ($overlay_content->resolutionOverScale == 1) echo 'disabled' ?> value="<?php echo $overlay_content->maxScale; ?>" /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="radio" id="resolutionOverScale1" name="resolutionOverScale" value="1"
				<?php if ($overlay_content->resolutionOverScale == 1) echo "checked=\"checked\""; ?> /> <?php echo JText::_("EASYSDI_BASE_RESOLUTIONS"); ?></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_BASE_RESOLUTIONS"); ?></td>
				<td style=""><textarea class="textarea resolutions" style="height: 200px; width: 500px;" id="resolutions" name="resolutions" size="50"
					maxlength="4000" <?php if ($overlay_content->resolutionOverScale == 0) echo 'disabled' ?>><?php echo $overlay_content->resolutions; ?></textarea></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_BASE_CACHE"); ?></td>
				<td><input class="checkbox" name="cache" value="1" type="checkbox" <?php if ($overlay_content->cache == 1) echo "checked=\"checked\""; ?> /></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_URL"); ?></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="url" id="url" value="<?php echo $overlay_content->url; ?>" /></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_URL_TYPE"); ?></td>
				<td><select class="inputbox" name="url_type">
					<option value="WMS" <?php if($overlay_content->url_type == 'WMS') echo "selected" ; ?>><?php echo JText::_("EASYSDI_WMS"); ?></option>
					<option value="WFS" <?php if($overlay_content->url_type == 'WFS') echo "selected" ; ?>><?php echo JText::_("EASYSDI_WFS"); ?></option>
				</select></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_TILE"); ?></td>
				<td><input class="checkbox" name="singletile" value="1" type="checkbox"
				<?php if ($overlay_content->singletile == 1) echo "checked=\"checked\""; ?> /></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_IMG_FORMAT"); ?></td>
				<td><input class="inputbox" name="img_format" id="img_format" type="text" size="50" maxlength="100"
					value="<?php echo $overlay_content->img_format; ?>" /></td>
				<td>ex : image/png</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_BASE_LAYER_CUSTOM_STYLE_ENABLED"); ?></td>
				<td><input class="checkbox" name="customStyle" value="1" type="checkbox"
				<?php if ($overlay_content->customStyle == 1) echo "checked=\"checked\""; ?> /></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_VISIBILITY"); ?></td>
				<td><input class="checkbox" name="default_visibility" value="1" type="checkbox"
				<?php if ($overlay_content->default_visibility == 1) echo "checked=\"checked\""; ?> /></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_OPACITY"); ?></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="default_opacity" id="default_opacity"
					value="<?php echo $overlay_content->default_opacity; ?>" /></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_METADATA"); ?></td>
				<td><input class="inputbox" type="text" size="50" maxlength="500" name="metadata_url" id="metadata_url"
					value="<?php echo $overlay_content->metadata_url; ?>" /></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>

</table>

<input type="hidden" name="option" value="<?php echo $option; ?>" /> <input type="hidden" name="id" value="<?php echo $overlay_content->id;?>"> <input
	type="hidden" name="order" value="<?php echo $overlay_content->order;?>"> <input type="hidden" name="task" value="saveOverlayContent" /></form>
				<?php
	}

	function listOverlayGroup($use_pagination, $rows, $pageNav, $option)
	{
		JToolBarHelper::title(JText::_("EASYSDI_LIST_OVERLAY_GROUP"));
		$order_field = JRequest::getVar ('order_field');
		?>
<form action="index.php" method="GET" name="adminForm"><script>
		function tableOrder(task, orderField)
		{
			document.forms['adminForm'].elements['order_field'].value=orderField;
			document.forms['adminForm'].submit();
			return;
		
		}
		</script>
<table class="adminlist">
	<thead>
		<tr>
			<th width="20" class='title'><?php echo JText::_("EASYSDI_OVERLAY_GROUP_SHARP"); ?></th>
			<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class='title'><a href="javascript:tableOrder('overlayGroup', 'name');" title="Click to sort by this column"><?php echo JText::_("EASYSDI_OVERLAY_GROUP_NAME"); ?></th>
			<th class='title'><a href="javascript:tableOrder('overlayGroup', 'open');" title="Click to sort by this column"><?php echo JText::_("EASYSDI_OVERLAY_GROUP_OPEN"); ?></th>
			<th class='title'><a href="javascript:tableOrder('overlayGroup', 'order');" title="Click to sort by this column"><?php echo JText::_("EASYSDI_OVERLAY_GROUP_ORDER"); ?></th>
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
			<td><?php if($row->open == 1){echo JText::_("EASYSDI_YES");}else{echo JText::_("EASYSDI_NO");} ?></td>
			<td width="10%" class="order" nowrap="nowrap"><?php
			$disabled = ($order_field == 'order') ? true : false;
			?> <span><?php echo $pageNav->orderUpIcon($i,  true, 'orderupoverlaygroup', 'Move Up', $disabled);  ?></span> <span><?php echo $pageNav->orderDownIcon($i,1,  true, 'orderdownoverlaygroup', 'Move Down', $disabled);   ?></span>
			<?php echo $row->order;?> <?php
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
<input type="hidden" name="option" value="<?php echo $option; ?>" /> <input type="hidden" name="task" value="overlayGroup" /> <input type="hidden"
	name="boxchecked" value="0" /> <input type="hidden" name="hidemainmenu" value="0"> <input type="hidden" name="order_field"
	value="<?php echo $order_field;?>" /></form>
	<?php
	}

	function editOverlayGroup ($overlay_group, $option)
	{
		if ($overlay_group->id != 0)
		{
			JToolBarHelper::title( JText::_("EASYSDI_OVERLAY_GROUP_EDIT"), 'generic.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("EASYSDI_OVERLAY_GROUP_NEW"), 'generic.png' );
		}


		?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">

<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<fieldset>
		<table class="admintable">
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_GROUP_ID"); ?> :</td>
				<td><?php echo $overlay_group->id; ?></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_GROUP_NAME"); ?> :</td>
				<td><input class="inputbox" type="text" size="100" maxlength="400" name="name" value="<?php echo $overlay_group->name; ?>" /></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_("EASYSDI_OVERLAY_GROUP_OPEN"); ?></td>
				<td><input class="checkbox" name="open" value="1" type="checkbox" <?php if ($overlay_group->open == 1) echo "checked=\"checked\""; ?> /></td>
			</tr>

		</table>
		</fieldset>
		</td>
	</tr>
</table>
<input type="hidden" name="id" value="<?php echo $overlay_group->id; ?>" /> <input type="hidden" name="option" value="<?php echo $option; ?>" /> <input
	type="hidden" name="task" value="saveOverlayGroup" /></form>

		<?php
	}
}
?>