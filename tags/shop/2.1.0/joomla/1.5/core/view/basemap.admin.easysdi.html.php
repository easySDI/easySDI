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

class HTML_basemap {

	function editBasemapContent( $rowBasemap,$basemap_name,$rowsAccount,$id, $option ){
		
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("SHOP_BASEMAP_CONTENT_TITLE")." : ".$basemap_name, 'generic.png' );
			
		?>				
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
		function ServiceFieldManagement(){
			if (document.forms['adminForm'].urltype.value != 'WMTS')
			{
				document.getElementById('minresolution').disabled = false;
				document.getElementById('maxresolution').disabled = false;
				document.getElementById('singletile').disabled = false;
				document.getElementById('matrixset').disabled = true;
				document.getElementById('matrixset').value = null;
				document.getElementById('matrixids').disabled = true;
				document.getElementById('matrixids').value = null;
				document.getElementById('wmts-info').style.visibility = 'hidden';
			}else{
				document.getElementById('minresolution').disabled = true;
				document.getElementById('minresolution').value = null;
				document.getElementById('maxresolution').disabled = true;
				document.getElementById('maxresolution').value = null;
				document.getElementById('singletile').disabled = true;
				document.getElementById('singletile').value = null;
				document.getElementById('matrixset').disabled = false;
				document.getElementById('matrixids').disabled = false;
				document.getElementById('wmts-info').style.visibility = 'visible';
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
								<td colspan="2"><?php echo $rowBasemap->id; ?></td>
							</tr>	
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_NAME"); ?> : </td>
								<td colspan="2" ><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $rowBasemap->name; ?>" /></td>							
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_DESCRIPTION"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="100" name="description" value="<?php echo $rowBasemap->description; ?>" /></td>							
							</tr>	
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_URL_TYPE"); ?> : </td>
								<td ><select class="inputbox" name="urltype" onChange="javascript:ServiceFieldManagement();" >
										<option value="WMS" <?php if($rowBasemap->urltype == 'WMS') echo "selected" ; ?>><?php echo JText::_("SHOP_WMS"); ?></option>
										<option value="WFS" <?php if($rowBasemap->urltype == 'WFS') echo "selected" ; ?>><?php echo JText::_("SHOP_WFS"); ?></option>
										<option value="WMTS" <?php if($rowBasemap->urltype == 'WMTS') echo "selected" ; ?>><?php echo JText::_("SHOP_WMTS"); ?></option>
								</select>
								</td>		
								<td  id="wmts-info" align="left">
									<div style="font-weight: bold" >
										<img src="<?php echo JURI::root(true);?>/includes/js/ThemeOffice/warning.png" style="vertical-align:top" alt="" /> 
										<?php echo JText::_("SHOP_BASEMAP_WMTS_INFO"); ?>
									</div>						
								</td>													
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_URL"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="100" name="url" value="<?php echo $rowBasemap->url; ?>" /></td>							
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_LAYERS"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="300" name="layers" value="<?php echo $rowBasemap->layers; ?>" /></td>							
							</tr>
							<tr>
								<td class="key">
									
										<?php echo JText::_("SHOP_IMG_FORMAT"); ?> : 
									
								</td>
								<td colspan="2" >
									<span class="editlinktip hasTip" title="<?php echo JText::_("SHOP_BASEMAP_EG_IMG_FORMAT"); ?>">
										<input class="inputbox" name="imgformat" type="text" size="30" maxlength="100" value="<?php echo $rowBasemap->imgformat; ?>" />
									</span>									
								</td>											
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PROJECTION"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="100" name="projection" value="<?php echo $rowBasemap->projection; ?>" /></td>
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_UNIT"); ?> : </td>
								<td colspan="2"><select class="inputbox" name="unit" >
								<option <?php if($rowBasemap->unit == 'm') echo "selected" ; ?> value="m"> <?php echo JText::_("SHOP_METERS"); ?></option>
								<option <?php if($rowBasemap->unit == 'degrees') echo "selected" ; ?> value="degrees"> <?php echo JText::_("SHOP_DEGREES"); ?></option>
								</select>
								</td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_STYLE"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="100" name="style" id="style" value="<?php echo $rowBasemap->style; ?>" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_MAXEXTENT"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="100" name="maxextent" value="<?php echo $rowBasemap->maxextent; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_MINRESOLUTION"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="100" name="minresolution" id="minresolution" value="<?php echo $rowBasemap->minresolution; ?>" <?php if ($rowBasemap->urltype == 'WMTS') echo 'disabled'; ?> /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_MAXRESOLUTION"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="100" name="maxresolution" id="maxresolution" value="<?php echo $rowBasemap->maxresolution; ?>" <?php if ($rowBasemap->urltype == 'WMTS') echo 'disabled'; ?> /> </td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_SINGLE_TILE"); ?> : </td>
								<td colspan="2"><select class="inputbox" name="singletile"   id="singletile"  <?php if ($rowBasemap->urltype == 'WMTS') echo 'disabled'; ?> >
										<option value="0" <?php if($rowBasemap->singletile == '0') echo "selected" ; ?>><?php echo JText::_("CORE_TRUE"); ?></option>
										<option value="1" <?php if($rowBasemap->singletile == '1') echo "selected" ; ?>><?php echo JText::_("CORE_FALSE"); ?></option>
								</select>
								</td>															
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_ATTRIBUTION"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="100" name="attribution" value="<?php echo $rowBasemap->attribution; ?>" /></td>							
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_MATRIXSET"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="100" name="matrixset" id="matrixset" value="<?php echo $rowBasemap->matrixset; ?>" <?php if ($rowBasemap->urltype != 'WMTS') echo 'disabled'; ?> /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_MATRIX"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="1000" name="matrixids" id="matrixids" value="<?php echo $rowBasemap->matrixids; ?>" <?php if ($rowBasemap->urltype != 'WMTS') echo 'disabled'; ?>/></td>								
							</tr>
						</table>
					</fieldset>
					<fieldset>
						<legend><?php echo JText::_("SHOP_AUTHENTICATION"); ?></legend>
							<table>
							<tr>
								<td >
									<input type="radio" name="service_type" value="via_proxy" onclick="javascript:displayAuthentication();" <?php if ($rowBasemap->account_id) echo "checked";?>>
								</td>
								<td  class="key" colspan="2">
									<?php echo JText::_("SHOP_AUTH_VIA_PROXY"); ?>
								</td>
							</tr>
							<tr>
								<td></td>
								<td><?php echo JText::_("SHOP_EASYSDI_ACCOUNT"); ?> : </td>
								<td><?php $enable = $rowBasemap->account_id? "" : "disabled"  ; echo JHTML::_("select.genericlist",$rowsAccount, 'account_id', 'size="1" class="inputbox" onChange="" '.$enable , 'value', 'text',$rowBasemap->account_id); ?></td>
							</tr>
							<tr>
								<td >
								 	<input type="radio" name="service_type" value="direct" onclick="javascript:displayAuthentication();" <?php if ($rowBasemap->user) echo "checked";?>> 
							 	</td>
							 	<td  class="key" colspan="2">
								 	 <?php echo JText::_("SHOP_AUTH_DIRECT"); ?>
							 	</td>
						 	<tr>
							<tr>
								<td></td>
								<td><?php echo JText::_("SHOP_AUTH_USER"); ?> : </td>
								<td><input <?php if (!$rowBasemap->user){echo "disabled";} ?> class="inputbox" type="text" size="50" maxlength="100" name="user" id="user" value="<?php echo $rowBasemap->user; ?>" /></td>							
							</tr>							
							<tr>
								<td></td>
								<td><?php echo JText::_("SHOP_AUTH_PASSWORD"); ?> : </td>
								<td><input <?php if (!$rowBasemap->user){echo "disabled";} ?> class="inputbox" type="password" size="50" maxlength="100" name="password" id="password" value="<?php echo $rowBasemap->password; ?>" /></td>							
							</tr>
							
							</table>
						</fieldset>	
				</td>
			</tr>
			
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $id;?>">
		<input type="hidden" name="basemap_id" value="<?php echo $rowBasemap->basemap_id;?>">
		<input type="hidden" name="guid" value="<?php echo $rowBasemap->guid; ?>" />
		<input type="hidden" name="created" value="<?php echo $rowBasemap->created; ?>" />
		<input type="hidden" name="createdby" value="<?php echo $rowBasemap->createdby; ?>" />
		<input type="hidden" name="checked_out" value="<?php echo $rowBasemap->checked_out; ?>" />
		<input type="hidden" name="checked_out_time" value="<?php echo $rowBasemap->checked_out_time; ?>" />
		</form>
	<?php
	}
	
	
	function listBasemapContent($basemap_id,$basemap_name,$rows, $pageNav,$option, $search){

		$database =& JFactory::getDBO();
		$user	=& JFactory::getUser();
		JToolBarHelper::title(JText::_("SHOP_BASEMAP_CONTENT_TITLE")." : ".$basemap_name);
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
	<form action="index.php" method="post" name="adminForm">
		
		<table  width="100%">
			<tr>
				<td class="key" width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchBaseMapContent" id="searchBaseMapContent" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "Go" ); ?></button>
					<button onclick="document.getElementById('searchBaseMapContent').value='';this.form.submit();"><?php echo JText::_( "Reset" ); ?></button>
				</td>
			</tr>
		</table>
		
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("CORE_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><a href="javascript:tableOrder('listBasemapContent', 'id');" title="Click to sort by this column"><?php echo JText::_("CORE_ID"); ?></a></th>
				<th class='title'><a href="javascript:tableOrder('listBasemapContent', 'name');" title="Click to sort by this column"><?php echo JText::_("CORE_NAME"); ?></a></th>				
				<th class='title'><a href="javascript:tableOrder('listBasemapContent', 'description');" title="Click to sort by this column"><?php echo JText::_("CORE_DESCRIPTION"); ?></a></th>
				<th class='title'><a href="javascript:tableOrder('listBasemapContent', 'url');" title="Click to sort by this column"><?php echo JText::_("SHOP_URL"); ?></a></th>
				<th class='title'><a href="javascript:tableOrder('listBasemapContent', 'ordering');" title="Click to sort by this column"><?php echo JText::_("SHOP_BASEMAPCONTENT_ORDER"); ?></a></th>
			</tr>
		</thead>
		<tbody>		
<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];	 
			$link = 'index.php?option='.$option.'&task=editBasemapContent&cid[]='.$row->id.'&basemap_id='.$basemap_id;	  				
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
				<td><?php echo $row->url; ?></td>
				<td class="order" nowrap="nowrap">
					<?php
					$disabled = ($order_field == 'ordering'||$order_field == "") ? true : false;
					?>
					<span><?php echo $pageNav->orderUpIcon($i,  true, 'orderupbasemapcontent', 'Move Up', $disabled);  ?></span>							
					<span><?php echo $pageNav->orderDownIcon($i,1,  true, 'orderdownbasemapcontent', 'Move Down', $disabled);   ?></span>
					<?php echo $row->ordering ;?>
				</td>
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
	  	<input type="hidden" name="order_field" value="" />
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listBasemapContent" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="basemap_id" value="<?php echo $basemap_id; ?>">
	  </form>
<?php
		
}
	
	
	
	function editBasemap( $rowBasemap,$id, $option ){
		
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("SHOP_BASEMAP_TITLE_EDIT"), 'generic.png' );
			
		?>				
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
		<table class="admintable"  border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("SHOP_GENERAL"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="key"><?php echo JText::_("CORE_ID"); ?> : </td>
								<td><?php echo $rowBasemap->id; ?></td>
							</tr>			

							<tr>
								<td class="key"><?php echo JText::_("CORE_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $rowBasemap->name; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PROJECTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="projection" value="<?php echo $rowBasemap->projection; ?>" /></td>
							</tr>
							
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_UNIT"); ?> : </td>
								<td><select class="inputbox" name="unit" >								
									<option <?php if($rowBasemap->unit == 'm') echo "selected" ; ?> value="m"> <?php echo JText::_("SHOP_METERS"); ?></option>
									<option <?php if($rowBasemap->unit == 'degrees') echo "selected" ; ?> value="degrees"> <?php echo JText::_("SHOP_DEGREES"); ?></option>
								</select>
								</td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_MAXEXTENT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxextent" value="<?php echo $rowBasemap->maxextent; ?>" /></td>							
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_MAXEXTEND_IS_RESTRICTIVE"); ?> : </td>
								<td><select class="inputbox" name="restrictedextent" >
										<option value="0" <?php if($rowBasemap->restrictedextent == '0') echo "selected" ; ?>><?php echo JText::_("CORE_FALSE"); ?></option>
										<option value="1" <?php if($rowBasemap->restrictedextent == '1') echo "selected" ; ?>><?php echo JText::_("CORE_TRUE"); ?></option>
								</select>
								</td>															
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_DECIMAL_PRECISION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="50" name="decimalprecision" value="<?php echo $rowBasemap->decimalprecision; ?>" /></td>							
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_IS_DEFAULT"); ?> : </td>
								<td><select class="inputbox" name="default" >
										<option value="0" <?php if($rowBasemap->default == '0') echo "selected" ; ?>><?php echo JText::_("CORE_FALSE"); ?></option>
										<option value="1" <?php if($rowBasemap->default == '1') echo "selected" ; ?>><?php echo JText::_("CORE_TRUE"); ?></option>
								</select>
								</td>															
							</tr>
							<tr>
								<td   colspan="2">
								<?php
								$isScale = true;	 
								if ($rowBasemap->minresol != null || $rowBasemap->maxresol != null || $rowBasemap->restrictedresol != null )
									$isScale = false;
									 
								?>
									<input type="radio" id="mapRadioScale" name="mapResolutionOverScale" value="0" <?php if ($isScale) echo 'checked="checked"'; ?>   onClick="javascript:enableScale(true);"/> <?php echo JText::_("SHOP_TITLE_SCALE"); ?>	
								</td>
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_MINSCALE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="minresolution" id="minresolution" <?php if(!$isScale) echo 'disabled'; ?> value="<?php echo $rowBasemap->minresolution; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_MAXSCALE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxresolution" id="maxresolution" <?php if(!$isScale) echo 'disabled'; ?> value="<?php echo $rowBasemap->maxresolution; ?>" /></td>							
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_RESTRICTEDSCALES"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="restrictedscales" id="restrictedscales" <?php if(!$isScale) echo 'disabled'; ?> value="<?php echo $rowBasemap->restrictedscales; ?>" /></td>							
							</tr>
							<tr>
								<td   colspan="2">
									<input type="radio" id="mapRadioResolution" name="mapResolutionOverScale" value="1" <?php if(!$isScale) echo 'checked="checked"'; ?> onClick="javascript:enableScale(false);" /> <?php echo JText::_("SHOP_TITLE_RESOLUTION"); ?>	
																		(<img src="<?php echo JURI::root(true);?>/includes/js/ThemeOffice/warning.png" style="vertical-align:top" alt="" /> 
										<i><?php echo JText::_("SHOP_BASEMAP_RESOLUTION_INFO"); ?></i>)
															
								</td>
								</td>
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_MINRESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="minresol" id="minresol" <?php if($isScale) echo 'disabled'; ?> value="<?php echo $rowBasemap->minresol; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_MAXRESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxresol" id="maxresol"   <?php if($isScale) echo 'disabled'; ?> value="<?php echo $rowBasemap->maxresol; ?>" /></td>							
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_RESTRICTEDRESOL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="500"  name="restrictedresol" id="restrictedresol"  <?php if($isScale) echo 'disabled'; ?> value="<?php echo $rowBasemap->restrictedresol; ?>" /></td>							
							</tr>
								
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("SHOP_BASEMAP_TEXT_STYLE"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_OPENLAYERS_DEFAULT_FILLCOLOR"); ?> : </td>
								<td><input class="inputbox" type="text" size="10" maxlength="10" name="dfltfillcolor" value="<?php echo $rowBasemap->dfltfillcolor; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_OPENLAYERS_DEFAULT_STROKECOLOR"); ?> : </td>
								<td><input class="inputbox" type="text" size="10" maxlength="10" name="dfltstrkcolor" value="<?php echo $rowBasemap->dfltstrkcolor; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_OPENLAYERS_DEFAULT_STROKEWIDTH"); ?> : </td>
								<td><input class="inputbox" type="text" size="10" maxlength="10" name="dfltstrkwidth" value="<?php echo $rowBasemap->dfltstrkwidth; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_OPENLAYERS_SELECT_FILLCOLOR"); ?> : </td>
								<td><input class="inputbox" type="text" size="10" maxlength="10" name="selectfillcolor" value="<?php echo $rowBasemap->selectfillcolor; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_OPENLAYERS_SELECT_STROKECOLOR"); ?> : </td>
								<td><input class="inputbox" type="text" size="10" maxlength="10" name="selectstrkcolor" value="<?php echo $rowBasemap->selectstrkcolor; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_OPENLAYERS_TEMP_FILLCOLOR"); ?> : </td>
								<td><input class="inputbox" type="text" size="10" maxlength="10" name="tempfillcolor" value="<?php echo $rowBasemap->tempfillcolor; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_OPENLAYERS_TEMP_STROKECOLOR"); ?> : </td>
								<td><input class="inputbox" type="text" size="10" maxlength="10" name="tempstrkcolor" value="<?php echo $rowBasemap->tempstrkcolor; ?>" /></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $id;?>">
		<input type="hidden" name="guid" value="<?php echo $rowBasemap->guid; ?>" />
		<input type="hidden" name="created" value="<?php echo $rowBasemap->created; ?>" />
		<input type="hidden" name="createdby" value="<?php echo $rowBasemap->createdby; ?>" />
		<input type="hidden" name="checked_out" value="<?php echo $rowBasemap->checked_out; ?>" />
		<input type="hidden" name="checked_out_time" value="<?php echo $rowBasemap->checked_out_time; ?>" />
		</form>
	<?php
	}
	
	
	
	function listBasemap( $rows, $pageNav,$option,$filter_order_Dir, $filter_order, $search){
		global  $mainframe;
		$database =& JFactory::getDBO();
		$user	=& JFactory::getUser();
		JToolBarHelper::title(JText::_("SHOP_LIST_BASEMAP"));
		?>
		<form action="index.php" method="post" name="adminForm">
			
			<table width="100%">
			<tr>
				<td class="key"  width="100%">
					<?php echo JText::_("Filter"); ?>:
					<input type="text" name="searchBaseMap" id="searchBaseMap" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "Go" ); ?></button>
					<button onclick="document.getElementById('searchBaseMap').value='';this.form.submit();"><?php echo JText::_( "Reset" ); ?></button>
				</td>
			</tr>
		</table>
			
			<table class="adminlist">
			<thead>
				<tr>					 			
					<th class='title'><?php echo JText::_("CORE_SHARP"); ?></th>
					<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("SHOP_PROJECTION"), 'projection', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("SHOP_UNIT"), 'unit', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("SHOP_MAXEXTENT"), 'maxextent', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'updated', @$filter_order_Dir, @$filter_order); ?></th>
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
<!--				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);if(this.checked){document.getElementById('countRelatedBasemapContent').value = '<?php echo $row->count;?>';};" /></td>-->
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
				<td><?php echo $row->projection; ?></td>				
				<td><?php echo $row->unit; ?></td>				
				<td><?php echo $row->maxExtent; ?></td>
				<td><?php echo $row->updated; ?></td>
			</tr>
		<?php
			$k = 1 - $k;
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
	  	<input type="hidden" name="task" value="listBasemap" />
	  	<input type="hidden" name="boxchecked" value="0" />
<!--	  	<input type="hidden" id="countRelatedBasemapContent"  name="countRelatedBasemapContent" value="0" />-->
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
		
}	
}
?>