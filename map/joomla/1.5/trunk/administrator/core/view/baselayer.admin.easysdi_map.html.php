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
	function listBaseDefinition($use_pagination, $rows, $pageNav, $option)
	{
		JToolBarHelper::title(JText::_("EASYSDI_LIST_BASE_DEF"));
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
				<th width="20" class='title'><?php echo JText::_("EASYSDI_BASE_DEF_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><a href="javascript:tableOrder('baseLayer', 'url');" title="Click to sort by this column"><?php echo JText::_("EASYSDI_BASE_PROJECTION"); ?></a></th>
				<th class='title'><?php echo JText::_("EASYSDI_BASE_EXTENT"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_BASE_UNIT"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_BASE_DEFAULT"); ?></th>
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
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editBaseDefinition')"><?php echo $row->projection; ?></a></td>
				<td><?php echo $row->maxExtent; ?></td>
				<td><?php echo $row->unit; ?></td>	
				<td><?php if($row->def == 1){echo JText::_("EASYSDI_YES");}else{echo JText::_("EASYSDI_NO");} ?></td>				 
			</tr>
		<?php
			$k = 1 - $k;
			$i++;
		}
		
			?></tbody>
			
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
	  	<input type="hidden" name="order_field" value="" />
	  	<input type="hidden" name="task" value="baseDefinition" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	</form>
		<?php		
	}
	
	function editBaseDefinition( $base_definition, $option )
	{
		global  $mainframe;
		if ($base_definition->id != 0)
		{
			JToolBarHelper::title( JText::_("EASYSDI_BASE_DEF_EDIT"), 'generic.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("EASYSDI_BASE_DEF_NEW"), 'generic.png' );
		}
		?>		
		<script>	
	
	</script>				
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<table class="admintable">
							<tr>
								<td class="key" ><?php echo JText::_("EASYSDI_BASE_ID"); ?></td>
								<td><?php echo $base_definition->id; ?></td>																
							</tr>	
							<tr>							
								<td class="key"><?php echo JText::_("EASYSDI_BASE_PROJECTION"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="projection" id="projection" value="<?php echo $base_definition->projection; ?>" /></td>							
							</tr>	
							<tr>
							
								<td class="key"><?php echo JText::_("EASYSDI_BASE_MAX_EXTENT"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxExtent" id="maxExtent" value="<?php echo $base_definition->maxExtent; ?>" /></td>							
							</tr>		
							<tr>							
								<td  class="key"><?php echo JText::_("EASYSDI_BASE_UNIT"); ?> : </td>
								<td><select class="inputbox" name="unit" >								
									<option <?php if($base_definition->unit == 'm') echo "selected" ; ?> value="m"> <?php echo JText::_("EASYSDI_METERS"); ?></option>
									<option <?php if($base_definition->unit == 'degrees') echo "selected" ; ?> value="degrees"> <?php echo JText::_("EASYSDI_DEGREES"); ?></option>
								</select>
								</td>

							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_BASE_MIN_RESOLUTION"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="minResolution" id="minResolution" value="<?php echo $base_definition->minResolution; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_BASE_MAX_RESOLUTION"); ?></td>
								<td>
									<input class="inputbox"  name="maxResolution" id="maxResolution" type="text" size="50" maxlength="100" value="<?php echo $base_definition->maxResolution; ?>" />									
								</td>																							
							</tr>			
							<tr>
							
								<td class="key"><?php echo JText::_("EASYSDI_BASE_DEFAULT"); ?> : </td>
								<td><select class="inputbox" name="def" >
										<option value="0" <?php if($base_definition->def == '0') echo "selected" ; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option>
										<option value="1" <?php if($base_definition->def == '1') echo "selected" ; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option>
								</select>
								</td>															
							</tr>					
						</table>
					</fieldset>
				</td>
			</tr>
			
		</table>
		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $base_definition->id;?>">
		<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
	
	function listBaseLayer($use_pagination, $rows, $id_base, $pageNav, $option)
	{
		JToolBarHelper::title(JText::_("EASYSDI_LIST_BASE_LAYER"));
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
				<th width="20" class='title'><?php echo JText::_("EASYSDI_BASE_LAYER_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><a href="javascript:tableOrder('baseLayer', 'name');" title="Click to sort by this column"><?php echo JText::_("EASYSDI_BASE_LAYER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_BASE_LAYER_URL"); ?></a></th>
				<th class='title'><?php echo JText::_("EASYSDI_BASE_LAYER_PROJECTION"); ?></th>
				<th class='title'><a href="javascript:tableOrder('baseLayer', 'order');" title="Click to sort by this column"><?php echo JText::_("EASYSDI_BASE_LAYER_ORDER"); ?></th>
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
				<td class="order" nowrap="nowrap">
				<?php
				$disabled = ($order_field == 'order') ? true : false;
								
					?>
					
							<span><?php echo $pageNav->orderUpIcon($i,  true, 'orderupbasemaplayer', 'Move Up', $disabled);  ?></span>							
							<span><?php echo $pageNav->orderDownIcon($i,1,  true, 'orderdownbasemaplayer', 'Move Down', $disabled);   ?></span>
							
							<?php echo $row->order;?>
							
            		 
            		 <?php
				
				
				?>

            	</td>
			</tr>
		<?php
			$k = 1 - $k;
			$i++;
		}
		
			?></tbody>
			
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
	  	<input type="hidden" name="order_field" value="<?php echo $order_field;?>" />
	  	<input type="hidden" name="id_base" value="<?php echo $id_base;?>">
	  	<input type="hidden" name="task" value="baseLayer" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	</form>
		<?php		
	}
	
	function editBaseLayer( $base_layer, $option )
	{
		global  $mainframe;
		if ($base_layer->id != 0)
		{
			JToolBarHelper::title( JText::_("EASYSDI_BASE_LAYER_EDIT"), 'generic.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("EASYSDI_BASE_LAYER_NEW"), 'generic.png' );
		}
		?>		
		<script>	
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
			else if (document.getElementById('maxExtent').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_BASE_CT_MAX_EXTENT_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('minResolution').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_BASE_CT_MIN_RES_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('maxResolution').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_BASE_CT_MAX_RES_VALIDATION_ERROR');?>');	
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
								<td class="key" ><?php echo JText::_("EASYSDI_BASE_LAYER_ID"); ?></td>
								<td><?php echo $base_layer->id; ?></td>																
							</tr>	
							<tr>							
								<td class="key"><?php echo JText::_("EASYSDI_BASE_LAYER_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" id="name" value="<?php echo stripslashes($base_layer->name); ?>" /></td>							
							</tr>	
							<tr>
							
								<td class="key"><?php echo JText::_("EASYSDI_BASE_LAYER_URL"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="url" id="url" value="<?php echo $base_layer->url; ?>" /></td>							
							</tr>		
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_BASE_LAYER_LAYERS"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="layers" id="layers" value="<?php echo $base_layer->layers; ?>" /></td>
								<td><?php echo JText::_("EASYSDI_OVERLAY_LAYERS_SEPARATOR"); ?></td>									
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_BASE_LAYER_PROJECTION"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="projection" id="projection" value="<?php echo $base_layer->projection; ?>" /></td>
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("EASYSDI_BASE_LAYER_TILE"); ?></td>
								<td><select class="inputbox" name="singletile" >								
								<option <?php if($base_layer->singletile == 0) echo "selected" ; ?> value="0"> <?php echo JText::_("EASYSDI_NO"); ?></option>
								<option <?php if($base_layer->singletile == 1) echo "selected" ; ?> value="1"> <?php echo JText::_("EASYSDI_YES"); ?></option>
								</select>
								</td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_BASE_LAYER_IMG_FORMAT"); ?></td>
								<td>
									<input class="inputbox"  name="img_format" id="img_format" type="text" size="50" maxlength="100" value="<?php echo $base_layer->img_format; ?>" />									
								</td>
								<td>ex : image/png</td>															
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_BASE_LAYER_MAXEXTENT"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxExtent" id="maxExtent" value="<?php echo $base_layer->maxExtent; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("EASYSDI_BASE_LAYER_MINRESOLUTION"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="minResolution" id="minResolution" value="<?php echo $base_layer->minResolution; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("EASYSDI_BASE_LAYER_MAXRESOLUTION"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxResolution" id="maxResolution" value="<?php echo $base_layer->maxResolution; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("EASYSDI_BASE_LAYER_UNIT"); ?></td>
								<td><select class="inputbox" name="unit" >								
								<option <?php if($base_layer->unit == 'm') echo "selected" ; ?> value="m"> <?php echo JText::_("EASYSDI_METERS"); ?></option>
								<option <?php if($base_layer->unit == 'degrees') echo "selected" ; ?> value="degrees"> <?php echo JText::_("EASYSDI_DEGREES"); ?></option>
								</select>
								</td>
							</tr>
							
							<tr>							
								<td class="key"><?php echo JText::_("EASYSDI_BASE_LAYER_VISIBILITY"); ?></td>
								<td><select class="inputbox" name="default_visibility" >								
								<option <?php if($base_layer->default_visibility == 0) echo "selected" ; ?> value="0"> <?php echo JText::_("EASYSDI_NO"); ?></option>
								<option <?php if($base_layer->default_visibility == 1) echo "selected" ; ?> value="1"> <?php echo JText::_("EASYSDI_YES"); ?></option>
								</select>
								</td>
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("EASYSDI_BASE_LAYER_OPACITY"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="default_opacity" id="default_opacity" value="<?php echo $base_layer->default_opacity; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("EASYSDI_BASE_LAYER_METADATA"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="500" name="metadata_url" id="metadata_url" value="<?php echo $base_layer->metadata_url; ?>" /></td>							
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			
		</table>
		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $base_layer->id;?>">
		<input type="hidden" name="id_base" value="<?php echo $base_layer->id_base;?>">
		<input type="hidden" name="order" value="<?php echo $base_layer->order;?>">
		<input type="hidden" name="cid[]" value="<?php echo $base_layer->id_base;?>">
		<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
}
?>