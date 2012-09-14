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

class HTML_productextent {

	function editProductExtent( $rowProductExtent,$rowsAccount,$id, $option ){
		
		global  $mainframe;
		$database =& JFactory::getDBO(); 
			
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
								<td><?php echo $rowProductExtent->id; ?></td>
								<input type="hidden" name="id" value="<?php echo $id;?>">								
							</tr>			
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_PRODUCTEXTENT_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $rowProductExtent->name; ?>" /></td>							
							</tr>
							<tr>
							
								<td class="key"><?php echo JText::_("SHOP_PRODUCTEXTENT_DESC"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="description" value="<?php echo $rowProductExtent->description; ?>" /></td>							
							</tr>
						</table>
					</fieldset>
					<fieldset>
						<legend><?php echo JText::_("SHOP_WMS"); ?></legend>
						<table>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_WMS_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="urlwms" value="<?php echo $rowProductExtent->urlwms; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCTEXTENT_LAYER_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="layername" value="<?php echo $rowProductExtent->layername; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCTEXTENT_IMG_FORMAT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="imgformat" value="<?php echo $rowProductExtent->imgformat; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCTEXTENT_MIN_SCALE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="minscale" value="<?php echo $rowProductExtent->minscale; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCTEXTENT_MAX_SCALE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxscale" value="<?php echo $rowProductExtent->maxscale; ?>" /></td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
						<legend><?php echo JText::_("SHOP_WFS"); ?></legend>
						<table>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_WFS_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="urlwfs" value="<?php echo $rowProductExtent->urlwfs; ?>" /></td>
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_FEATURETYPE_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="featuretype" value="<?php echo $rowProductExtent->featuretype; ?>" /></td>
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_PRODUCTEXTENT_FIELD_ID"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="fieldid" value="<?php echo $rowProductExtent->fieldid; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_PRODUCTEXTENT_FIELD_RESOURCE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="fieldresource" value="<?php echo $rowProductExtent->fieldresource; ?>" /></td>							
							</tr>
						</table>
					</fieldset>
					<fieldset>
							<legend><?php echo JText::_("SHOP_AUTHENTICATION"); ?></legend>
								<table>
								<tr>
									<td >
										<input type="radio" name="service_type" value="via_proxy" onclick="javascript:displayAuthentication();" <?php if ($rowProductExtent->account_id) echo "checked";?>>
									</td>
									<td  class="key" colspan="2">
										<?php echo JText::_("SHOP_AUTH_VIA_PROXY"); ?>
									</td>
								</tr>
								<tr>
									<td></td>
									<td><?php echo JText::_("SHOP_EASYSDI_ACCOUNT"); ?> : </td>
									<td><?php $enable = $rowProductExtent->account_id? "" : "disabled"  ; echo JHTML::_("select.genericlist",$rowsAccount, 'account_id', 'size="1" class="inputbox" onChange="" '.$enable , 'value', 'text',$rowProductExtent->account_id); ?></td>
								</tr>
								<tr>
									<td >
									 	<input type="radio" name="service_type" value="direct" onclick="javascript:displayAuthentication();" <?php if ($rowProductExtent->user) echo "checked";?>> 
								 	</td>
								 	<td  class="key" colspan="2">
									 	 <?php echo JText::_("SHOP_AUTH_DIRECT"); ?>
								 	</td>
							 	<tr>
								<tr>
									<td></td>
									<td><?php echo JText::_("SHOP_AUTH_USER"); ?> : </td>
									<td><input <?php if (!$rowProductExtent->user){echo "disabled";} ?> class="inputbox" type="text" size="50" maxlength="100" name="user" id="user" value="<?php echo $rowProductExtent->user; ?>" /></td>							
								</tr>							
								<tr>
									<td></td>
									<td><?php echo JText::_("SHOP_AUTH_PASSWORD"); ?> : </td>
									<td><input <?php if (!$rowProductExtent->user){echo "disabled";} ?> class="inputbox" type="password" size="50" maxlength="100" name="password" id="password" value="<?php echo $rowProductExtent->password; ?>" /></td>							
								</tr>
								
								</table>
							</fieldset>
				</td>
			</tr>			
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="guid" value="<?php echo $rowProductExtent->guid; ?>" />
		<input type="hidden" name="created" value="<?php echo $rowProductExtent->created; ?>" />
		<input type="hidden" name="createdby" value="<?php echo $rowProductExtent->createdby; ?>" />
		<input type="hidden" name="checked_out" value="<?php echo $rowProductExtent->checked_out; ?>" />
		<input type="hidden" name="checked_out_time" value="<?php echo $rowProductExtent->checked_out_time; ?>" />
		</form>
	<?php
	}
	
	
	
	function listProductExtent( $rows, $pageNav,$option,$filter_order_Dir, $filter_order,$search){
	
		$database =& JFactory::getDBO();
		$user	=& JFactory::getUser();
		JToolBarHelper::title(JText::_("SHOP_PRODUCTEXTENT_LIST"));
		
		?>
	<form action="index.php" method="post" name="adminForm">
		<table width="100%">
			<tr>
				<td class="key"  width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchProductExtent" id="searchProductExtent" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchProductExtent').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
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
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("SHOP_PRODUCTEXTENT_FEATURE_TYPE"), 'featuretype', @$filter_order_Dir, @$filter_order); ?></th>
			</tr>
		</thead>
		<tbody>		
<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
			$link = 'index.php?option='.$option.'&task=editProductExtent&cid[]='.$row->id;	  				
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
	  	<input type="hidden" name="task" value="listProductExtent" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
		
}	
}
?>