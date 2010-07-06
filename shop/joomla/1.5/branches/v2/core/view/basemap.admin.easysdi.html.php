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

	function editBasemapContent( $rowBasemap,$rowsAccount,$id, $option ){
		
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("SHOP_TITLE_BASEMAP_CONTENT"), 'generic.png' );
			
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
								<td><?php echo $rowBasemap->id; ?></td>
								<input type="hidden" name="id" value="<?php echo $id;?>">								
							</tr>			
							<tr>
								<td class="key"><?php echo JText::_("CORE_ID"); ?> : </td>
								<td><?php echo $rowBasemap->basemap_id; ?></td>
								<input type="hidden" name="basemap_id" value="<?php echo $rowBasemap->basemap_id;?>">								
							</tr>
							
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PROJECTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="projection" value="<?php echo $rowBasemap->projection; ?>" /></td>
							</tr>
							
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_UNIT"); ?> : </td>
								<td><select class="inputbox" name="unit" >
								
								<option <?php if($rowBasemap->unit == 'm') echo "selected" ; ?> value="m"> <?php echo JText::_("EASYSDI_METERS"); ?></option>
								<option <?php if($rowBasemap->unit == 'degrees') echo "selected" ; ?> value="degrees"> <?php echo JText::_("EASYSDI_DEGREES"); ?></option>
								</select>
								</td>
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_MINRESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="minResolution" value="<?php echo $rowBasemap->minResolution; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_MAXRESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxResolution" value="<?php echo $rowBasemap->maxResolution; ?>" /></td>							
							</tr>
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_MAXEXTENT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxExtent" value="<?php echo $rowBasemap->maxExtent; ?>" /></td>							
							</tr>
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="url" value="<?php echo $rowBasemap->url; ?>" /></td>							
							</tr>
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_URL_TYPE"); ?> : </td>
								<td><select class="inputbox" name="urltype" >
										<option value="WMS" <?php if($rowBasemap->urltype == 'WMS') echo "selected" ; ?>><?php echo JText::_("EASYSDI_WMS"); ?></option>
										<option value="WMS" <?php if($rowBasemap->urltype == 'WFS') echo "selected" ; ?>><?php echo JText::_("EASYSDI_WFS"); ?></option>
								</select>
								</td>															
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_IMG_FORMAT"); ?> : </td>
								<td>
									<input class="inputbox" name="imgformat" type="text" size="50" maxlength="100" value="<?php echo $rowBasemap->imgformat; ?>" />									
								</td>
								<td>ex : image/png</td>															
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_SINGLE_TILE"); ?> : </td>
								<td><select class="inputbox" name="singletile" >
										<option value="0" <?php if($rowBasemap->singletile == '0') echo "selected" ; ?>><?php echo JText::_("EASYSDI__TRUE"); ?></option>
										<option value="1" <?php if($rowBasemap->singletile == '1') echo "selected" ; ?>><?php echo JText::_("EASYSDI__FALSE"); ?></option>
								</select>
								</td>															
							</tr>
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_LAYERS"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="layers" value="<?php echo $rowBasemap->layers; ?>" /></td>							
							</tr>
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $rowBasemap->name; ?>" /></td>							
							</tr>
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_ATTRIBUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="attribution" value="<?php echo $rowBasemap->attribution; ?>" /></td>							
							</tr>
							<br>
										
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
		<input type="hidden" name="guid" value="<?php echo $rowBasemap->guid; ?>" />
		</form>
	<?php
	}
	
	
	function listBasemapContent($basemap_id,$use_pagination, $rows, $pageNav,$option, $search){

		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("SHOP_TITLE_BASEMAP_CONTENT"));
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
		
		<table width="100%">
			<tr>
				<td align="right" width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchBaseMapContent" id="searchBaseMapContent" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchBaseMapContent').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("CORE_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listBasemap\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("CORE_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><a href="javascript:tableOrder('listBasemapContent', 'id');" title="Click to sort by this column"><?php echo JText::_("CORE_ID"); ?></th>
				<th class='title'><a href="javascript:tableOrder('listBasemapContent', 'name');" title="Click to sort by this column"><?php echo JText::_("CORE_NAME"); ?></th>				
				<th class='title'><a href="javascript:tableOrder('listBasemapContent', 'description');" title="Click to sort by this column"><?php echo JText::_("CORE_DESCRIPTION"); ?></th>
				<th class='title'><a href="javascript:tableOrder('listBasemapContent', 'url');" title="Click to sort by this column"><?php echo JText::_("SHOP_URL"); ?></a></th>
				<th class='title'><a href="javascript:tableOrder('listBasemapContent', 'ordering');" title="Click to sort by this column"><?php echo JText::_("SHOP_BASEMAPCONTENT_ORDER"); ?></th>
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
				<td><a href="<?php echo $link ?>"><?php echo $row->name; ?></a></td>
				<td><?php echo $row->description; ?></a></td>
				<td><?php echo $row->url; ?></a></td>
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
	  	<input type="hidden" name="basemap_id" value="<?php echo $basemap_id; ?>">
	  </form>
<?php
		
}
	
	
	
	function editBasemap( $rowBasemap,$id, $option ){
		
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("SHOP_TITLE_BASEMAP"), 'generic.png' );
			
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
								<input type="hidden" name="id" value="<?php echo $id;?>">								
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
								<td class="key"><?php echo JText::_("SHOP_MINRESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="minResolution" value="<?php echo $rowBasemap->minResolution; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_MAXRESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxResolution" value="<?php echo $rowBasemap->maxResolution; ?>" /></td>							
							</tr>
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_MAXEXTENT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxExtent" value="<?php echo $rowBasemap->maxExtent; ?>" /></td>							
							</tr>
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_DECIMAL_PRECISION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="50" name="decimalPrecision" value="<?php echo $rowBasemap->decimalPrecision; ?>" /></td>							
							</tr>
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_MAXEXTEND_IS_RESTRICTIVE"); ?> : </td>
								<td><select class="inputbox" name="restrictedExtend" >
										<option value="0" <?php if($rowBasemap->restrictedExtend == '0') echo "selected" ; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option>
										<option value="1" <?php if($rowBasemap->restrictedExtend == '1') echo "selected" ; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option>
								</select>
								</td>															
							</tr>	
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_RESTRICTEDSCALES"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="restrictedScales" value="<?php echo $rowBasemap->restrictedScales; ?>" /></td>							
							</tr>
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_BASEMAP_IS_DEFAULT"); ?> : </td>
								<td><select class="inputbox" name="default" >
										<option value="0" <?php if($rowBasemap->default == '0') echo "selected" ; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option>
										<option value="1" <?php if($rowBasemap->default == '1') echo "selected" ; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option>
								</select>
								</td>															
							</tr>	
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("SHOP_TEXT_BASEMAP_STYLE"); ?></legend>
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
		<input type="hidden" name="guid" value="<?php echo $rowBasemap->guid; ?>" />
		</form>
	<?php
	}
	
	
	
	function listBasemap($use_pagination, $rows, $pageNav,$option, $search){
		global  $mainframe;
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("SHOP_LIST_BASEMAP"));
		?>
		<form action="index.php" method="post" name="adminForm">
			
			<table width="100%">
			<tr>
				<td align="right" width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchBaseMap" id="searchBaseMap" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchBaseMap').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
			<table width="100%">
				<tr>																																			
					<td align="left"><b><?php echo JText::_("CORE_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listBasemap\');"',$use_pagination); ?></td>
				</tr>
			</table>
			<table class="adminlist">
			<thead>
				<tr>					 			
					<th class='title'><?php echo JText::_("CORE_SHARP"); ?></th>
					<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
					<th class='title'><?php echo JText::_("CORE_ID"); ?></th>
					<th class='title'><?php echo JText::_("CORE_NAME"); ?></th>
					<th class='title'><?php echo JText::_("SHOP_PROJECTION"); ?></th>
					<th class='title'><?php echo JText::_("SHOP_UNIT"); ?></th>
					<th class='title'><?php echo JText::_("SHOP_MAXEXTENT"); ?></th>				
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
				<td><a href="<?php echo $link ?>"><?php echo $row->name; ?></a></td>
				<td><?php echo $row->projection; ?></td>				
				<td><?php echo $row->unit; ?></td>				
				<td><?php echo $row->maxExtent; ?></td>
			</tr>
		<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
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