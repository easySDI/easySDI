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

class HTML_grid {

	function editGrid( $rowGrid,$rowsAccount,$id, $option ){
		
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		jimport('joomla.html.pane');
		?>	
		<script>
		function submitbutton(pressbutton)
		{
			var form = document.adminForm;
			var text = '';
			var index = 0;
			if (pressbutton == "saveGrid" || pressbutton == "applyGrid")
			{
				if (form.elements['urlwms'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_URLWMS");?>";
					index = 1;	
				}	
				if (form.elements['layername'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_LAYERNAME");?>";
					index = 1;	
				}	
				if (form.elements['urlwfs'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_URLWFS");?>";
					index = 1;	
				}
				if (form.elements['featuretype'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_FEATURETYPE");?>";
					index = 1;	
				}
				if (form.elements['featureprefix'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_FEATUREPREFIX");?>";
					index = 1;	
				}
				if (form.elements['featureNS'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_FEATURENS");?>";
					index = 1;	
				}	
				if (form.elements['fieldgeom'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_FIELDGEOM");?>";
					index = 1;	
				}
				if (form.elements['fieldname'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_FIELDNAME");?>";
					index = 1;	
				}
				if (form.elements['fieldresource'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_FIELDRESOURCE");?>";
					index = 1;	
				}	
				if(index ==1)
				{
					text += ".";
					alert( "<?php echo JText::_("SHOP_MESSAGE_PROVIDE_VALUES");?> : "+text);
					return;
				}	
			}
			submitform( pressbutton );
		}
		
		function displayAuthentication(type)
		{
			if (document.forms['adminForm'].elements['service_type_'+type][0].checked)
			{
				document.getElementById(type+'password').disabled = true;
				document.getElementById(type+'password').value = "";
				document.getElementById(type+'user').disabled = true;
				document.getElementById(type+'user').value ="";
				document.getElementById(type+'account_id').disabled = false;
			}
			else
			{
				document.getElementById(type+'password').disabled = false;
				document.getElementById(type+'user').disabled = false;
				document.getElementById(type+'account_id').disabled = true;
				document.getElementById(type+'account_id').value = '0';
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
								<td><?php echo $rowGrid->id; ?></td>
								<input type="hidden" name="id" value="<?php echo $id;?>">								
							</tr>			
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_GRID_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $rowGrid->name; ?>" /></td>							
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_GRID_DESC"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="description" value="<?php echo $rowGrid->description; ?>" /></td>							
							</tr>
						</table>
					</fieldset>
					<fieldset>
						<legend><?php echo JText::_("SHOP_GRID_MAP"); ?></legend>
						<table>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_GRID_PROJECTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="projection" value="<?php echo $rowGrid->projection; ?>" /></td>
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_UNIT"); ?> : </td>
								<td colspan="2"><select class="inputbox" name="unit" >
								<option <?php if($rowGrid->unit == 'm') echo "selected" ; ?> value="m"> <?php echo JText::_("SHOP_METERS"); ?></option>
								<option <?php if($rowGrid->unit == 'degrees') echo "selected" ; ?> value="degrees"> <?php echo JText::_("SHOP_DEGREES"); ?></option>
								</select>
								</td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_GRID_EXTENT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="extent" value="<?php echo $rowGrid->extent; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_GRID_MIN_SCALE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="minscale" value="<?php echo $rowGrid->minscale; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_GRID_MAX_SCALE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxscale" value="<?php echo $rowGrid->maxscale; ?>" /></td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
						<legend><?php echo JText::_("SHOP_WMS"); ?></legend>
						<table>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_GRID_WMS_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="urlwms" value="<?php echo $rowGrid->urlwms; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_GRID_LAYER_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="layername" value="<?php echo $rowGrid->layername; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_GRID_IMG_FORMAT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="imgformat" value="<?php echo $rowGrid->imgformat; ?>" /></td>
							</tr>
							<tr>
								<td colspan="2">
									<?php 
									$pane = &JPane::getInstance('sliders', array('startOffset'=>-1, 'allowAllClose'=>true, 'opacityTransition'=>true, 'duration'=>600));
									echo $pane->startPane( 'panewms' ); 
									echo $pane->startPanel( JText::_("SHOP_AUTHENTICATION") );
									?>
									<table>
									<tr>
										<td >
											<input type="radio" name="service_type_wms" value="via_proxy" onclick="javascript:displayAuthentication('wms');" <?php if ($rowGrid->wmsaccount_id) echo "checked";?>>
										</td>
										<td colspan="2">
											<?php echo JText::_("SHOP_AUTH_VIA_PROXY"); ?>
										</td>
									</tr>
									<tr>
										<td></td>
										<td><?php echo JText::_("SHOP_EASYSDI_ACCOUNT"); ?> : </td>
										<td><?php 
											$enable = $rowGrid->wmsaccount_id? "" : "disabled"  ; 
											echo JHTML::_("select.genericlist",$rowsAccount, 'wmsaccount_id', 'size="1" class="inputbox" onChange="" '.$enable , 'value', 'text',$rowGrid->wmsaccount_id); 
											?>
										</td>
									</tr>
									<tr>
										<td >
										 	<input type="radio" name="service_type_wms" value="direct" onclick="javascript:displayAuthentication('wms');" <?php if ($rowGrid->wmsuser) echo "checked";?>> 
									 	</td>
									 	<td   colspan="2">
										 	 <?php echo JText::_("SHOP_AUTH_DIRECT"); ?>
									 	</td>
								 	<tr>
									<tr>
										<td></td>
										<td><?php echo JText::_("SHOP_AUTH_USER"); ?> : </td>
										<td><input <?php if (!$rowGrid->wmsuser){echo "disabled";} ?> class="inputbox" type="text" size="50" maxlength="100" name="wmsuser" id="wmsuser" value="<?php echo $rowGrid->wmsuser; ?>" /></td>							
									</tr>							
									<tr>
										<td></td>
										<td><?php echo JText::_("SHOP_AUTH_PASSWORD"); ?> : </td>
										<td><input <?php if (!$rowGrid->wmsuser){echo "disabled";} ?> class="inputbox" type="password" size="50" maxlength="100" name="wmspassword" id="wmspassword" value="<?php echo $rowGrid->wmspassword; ?>" /></td>							
									</tr>
									
									</table>
									<?php 
									echo $pane->endPanel();
									echo $pane->endPane();
									?>
								</td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
						<legend><?php echo JText::_("SHOP_WFS"); ?></legend>
						<table>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_GRID_WFS_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="urlwfs" value="<?php echo $rowGrid->urlwfs; ?>" /></td>
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_GRID_FEATURETYPE_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="400" name="featuretype" value="<?php echo $rowGrid->featuretype; ?>" /></td>
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_GRID_FEATURE_PREFIX"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="400" name="featureprefix" value="<?php echo $rowGrid->featureprefix; ?>" /></td>
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_FEATURE_NS"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="400" name="featureNS" value="<?php echo $rowGrid->featureNS; ?>" /></td>
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_GRID_FIELD_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="fieldname" value="<?php echo $rowGrid->fieldname; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_GRID_FIELD_DETAIL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="fielddetail" value="<?php echo $rowGrid->fielddetail; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_GRID_FIELD_GEOM"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="fieldgeom" value="<?php echo $rowGrid->fieldgeom; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_GRID_FIELD_RESOURCE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="fieldresource" value="<?php echo $rowGrid->fieldresource; ?>" /></td>							
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_GRID_DETAIL_TOOL_TIP"); ?> : </td>
								<td><?php echo JHTML::_('select.booleanlist', 'detailtooltip', '', $rowGrid->detailtooltip); ?> </td>
							</tr>		
							<tr>
								<td colspan="2">
									<?php 
									$pane = &JPane::getInstance('sliders', array('startOffset'=>-1, 'allowAllClose'=>true, 'opacityTransition'=>true, 'duration'=>600));
									echo $pane->startPane( 'panewms' ); 
									echo $pane->startPanel( JText::_("SHOP_AUTHENTICATION") );
									?>
									<table>
									<tr>
										<td >
											<input type="radio" name="service_type_wfs" value="via_proxy" onclick="javascript:displayAuthentication('wfs');" <?php if ($rowGrid->wfsaccount_id) echo "checked";?>>
										</td>
										<td colspan="2">
											<?php echo JText::_("SHOP_AUTH_VIA_PROXY"); ?>
										</td>
									</tr>
									<tr>
										<td></td>
										<td><?php echo JText::_("SHOP_EASYSDI_ACCOUNT"); ?> : </td>
										<td><?php 
											$enable = $rowGrid->wfsaccount_id? "" : "disabled"  ; 
											echo JHTML::_("select.genericlist",$rowsAccount, 'wfsaccount_id', 'size="1" class="inputbox" onChange="" '.$enable , 'value', 'text',$rowGrid->wfsaccount_id); 
											?>
										</td>
									</tr>
									<tr>
										<td >
										 	<input type="radio" name="service_type_wfs" value="direct" onclick="javascript:displayAuthentication('wfs');" <?php if ($rowGrid->wfsuser) echo "checked";?>> 
									 	</td>
									 	<td   colspan="2">
										 	 <?php echo JText::_("SHOP_AUTH_DIRECT"); ?>
									 	</td>
								 	<tr>
									<tr>
										<td></td>
										<td><?php echo JText::_("SHOP_AUTH_USER"); ?> : </td>
										<td><input <?php if (!$rowGrid->wfsuser){echo "disabled";} ?> class="inputbox" type="text" size="50" maxlength="100" name="wfsuser" id="wfsuser" value="<?php echo $rowGrid->wfsuser; ?>" /></td>							
									</tr>							
									<tr>
										<td></td>
										<td><?php echo JText::_("SHOP_AUTH_PASSWORD"); ?> : </td>
										<td><input <?php if (!$rowGrid->wfsuser){echo "disabled";} ?> class="inputbox" type="password" size="50" maxlength="100" name="wfspassword" id="wfspassword" value="<?php echo $rowGrid->wfspassword; ?>" /></td>							
									</tr>
									
									</table>
									<?php 
									echo $pane->endPanel();
									echo $pane->endPane();
									?>
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>			
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="guid" value="<?php echo $rowGrid->guid; ?>" />
		<input type="hidden" name="created" value="<?php echo $rowGrid->created; ?>" />
		<input type="hidden" name="createdby" value="<?php echo $rowGrid->createdby; ?>" />
		<input type="hidden" name="checked_out" value="<?php echo $rowGrid->checked_out; ?>" />
		<input type="hidden" name="checked_out_time" value="<?php echo $rowGrid->checked_out_time; ?>" />
		</form>
	<?php
	}
	
	
	
	function listGrid( $rows, $pageNav,$option,$filter_order_Dir, $filter_order,$search){
	
		$database =& JFactory::getDBO();
		$user	=& JFactory::getUser();
		JToolBarHelper::title(JText::_("SHOP_GRID_LIST"));
		
		?>
	<form action="index.php" method="post" name="adminForm">
		<table width="100%">
			<tr>
				<td class="key"  width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchGrid" id="searchGrid" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchGrid').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
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
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("SHOP_WFS_URL"), 'urlwfs', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("SHOP_GRID_FEATURE_TYPE"), 'featuretype', @$filter_order_Dir, @$filter_order); ?></th>
			</tr>
		</thead>
		<tbody>		
<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
			$link = 'index.php?option='.$option.'&task=editGrid&cid[]='.$row->id;	  				
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
				<td><?php echo $row->urlwfs; ?></td>				
				<td><?php echo $row->featuretype; ?></td>	
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
	  	<input type="hidden" name="task" value="listGrid" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
		
}	
}
?>