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

class HTML_mailing {

	function listMailing($use_pagination, &$rows, &$pageNav, $search, $option, $type)
	{
		global $my;
		$database =& JFactory::getDBO();
		$types = array();
		
		$types[] = JHTML::_('select.option','', JText::_("EASYSDI_LIST_TYPE_DEFAULT" ));
		$types[] = JHTML::_('select.option','1', JText::_("EASYSDI_LIST_TYPE_VOTE") );
		$types[] = JHTML::_('select.option','2', JText::_("EASYSDI_LIST_TYPE_MEMBER") );
		$types[] = JHTML::_('select.option','3', JText::_("EASYSDI_LIST_TYPE_USER") );

		//mosCommonHTML::loadOverlib();
		
		//$lists['pagination_radio'] = mosHTML::yesnoRadioList('use_pagination','onchange="javascript:submitbutton(\'listMailing\');"',$use_pagination);
		$lists['pagination_radio'] = JHTML::_( "select.booleanlist",'use_pagination','onchange="javascript:submitbutton(\'listMailing\');"',$use_pagination);
		$lists['use_pagination'] = $use_pagination;

?>
	<form action="index.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th class="inbox"><?php echo JTEXT::_("EASYSDI_TITLE_MAILING"); ?>&nbsp;<?php echo JHTML::_("select.genericlist", $types, 'type', 'size="1" class="inputbox" onChange="javascript:submitbutton(\'listMailing\');"', 'value', 'text', $type ); ?></th>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td align="right">
					<b><?php echo JTEXT::_("EASYSDI_TEXT_FILTER"); ?></b>&nbsp;
					<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="javascript:submitbutton('listMailing');" />
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td>
<?php
		switch ($type) {
			case '1':
?>
					<b><?php echo JTEXT::_("EASYSDI_TEXT_GADATE"); ?></b><?php echo JHTML::_('calendar',$rowPartner->partner_entry, "mailing_date","mailing_date","%d-%m-%Y"); ?>&nbsp;<?php echo JText::_("ASITVD_TEXT_FORMATDATE"); ?>
					<b>&nbsp;-&nbsp;</b>
					<b><?php echo JTEXT::_("EASYSDI_TEXT_GALOCALITY"); ?></b>&nbsp;<input type="text" name="mailing_locality" value="<?php echo JRequest::getVar('mailing_locality',''); ?>" class="inputbox" />
<?php
				break;
			case '2':
?>
					<b><?php echo JTEXT::_("EASYSDI_TEXT_GADATE"); ?></b><?php echo JHTML::_('calendar',$rowPartner->partner_entry, "mailing_date","mailing_date","%d-%m-%Y"); ?>&nbsp;<?php echo JText::_("ASITVD_TEXT_FORMATDATE"); ?>
					<b>&nbsp;-&nbsp;</b>
					<b><?php echo JTEXT::_("EASYSDI_TEXT_SUBSCRIPTIONYEAR"); ?></b>&nbsp;<input type="text" name="mailing_year" value="<?php echo JRequest::getVar('mailing_year',date('Y')); ?>" size="4" maxlength="4" class="inputbox" />
					<b>&nbsp;-&nbsp;</b>
					<b><?php echo JTEXT::_("EASYSDI_TEXT_DISCOUNT"); ?></b>&nbsp;<input type="text" name="mailing_discount" value="<?php echo JRequest::getVar('mailing_discount','0'); ?>" size="3" maxlength="3" class="inputbox" />&nbsp;[%]
<?php
				break;
			case '3':
?>
					<b><?php echo JTEXT::_("EASYSDI_TEXT_GADATE"); ?></b><?php echo JHTML::_('calendar',$rowPartner->partner_entry, "mailing_date","mailing_date","%d-%m-%Y"); ?>&nbsp;<?php echo JText::_("ASITVD_TEXT_FORMATDATE"); ?>
					<b>&nbsp;-&nbsp;</b>
					<b><?php echo JTEXT::_("EASYSDI_TEXT_BILLINGYEAR"); ?></b>&nbsp;<input type="text" name="mailing_year" value="<?php echo JRequest::getVar('mailing_year',date('Y')); ?>" size="4" maxlength="4" class="inputbox" />
					<b>&nbsp;-&nbsp;</b>
					<b><?php echo JTEXT::_("EASYSDI_TEXT_DISCOUNT"); ?></b>&nbsp;<input type="text" name="mailing_discount" value="<?php echo JRequest::getVar('mailing_discount','0'); ?>" size="3" maxlength="3" class="inputbox" />&nbsp;[%]
<?php
				break;
			default :
				break;
		}
?>
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td align="left">
					<b><?php echo JTEXT::_("EASYSDI_TEXT_PAGINATE"); ?></b><?= $lists['pagination_radio']; ?>
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JTEXT::_("EASYSDI_TEXT_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JTEXT::_("EASYSDI_TEXT_ID"); ?></th>
				<th class='title'><?php echo JTEXT::_("EASYSDI_TEXT_USER"); ?></th>
				<th class='title'><?php echo JTEXT::_("EASYSDI_TEXT_ACCOUNT"); ?></th>
				<th class='title'><?php echo JTEXT::_("EASYSDI_TEXT_ACRONYM"); ?></th>
				<th class='title'><?php echo JTEXT::_("EASYSDI_TEXT_CODE"); ?></th>
				<th class='title'><?php echo JTEXT::_("EASYSDI_TEXT_LASTUPDATE"); ?></th>
			</tr>
		</thead>
		
<?php
		if ($type != '') {
			?><tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count($rows); $i < $n; $i++)
			{
				$row = $rows[$i];
	  
				//$checked = mosCommonHTML::CheckedOutProcessing( $row, $i );
?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td><?php echo $row->id; ?></td>
				<td><?php echo $row->account_username; ?></td>
				<td><?php echo $row->account_name; ?></td>
				<td><?php echo $row->acronym; ?></td>
				<td><?php echo $row->code; ?></td>
				<td><?php echo date('d.m.Y H:i:s',strtotime($row->updated)); ?></td>
			</tr>
<?php
				$k = 1 - $k;
			}
			?></tbody>
			
			<?php
			if ($lists['use_pagination'])
			{
				?>
				<tfoot>
					<tr>	
						<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<?php
			}
		}
?>
	  	</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listMailing" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  </form>
<?php
	}
	

}
	
?>
