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
 



class HTMLadmin_statistic {
	
	function listStatistic($pageNav,$statistics,$option,$statisticType,$DateFrom,$DateTo,$filter_order_Dir, $filter_order,$search)
	{
		JToolBarHelper::title( JText::_("AGI_STATISTIC"), 'generic.png' );
		$database =& JFactory::getDBO();
		?>	
		<form action="index.php" method="GET" id="adminForm" name="adminForm">		
		<script>
		function fieldReset()
		{
			document.getElementById('DateFrom').value='';
			document.getElementById('DateTo').value='';
			document.getElementById('searchStatistic').value='';
			document.getElementById('filter_order').value='';
			document.getElementById('filter_order_Dir').value='';
			this.form.submit();
		}
		</script>
			<table width="100%" class="adminlist">
				<tr>
					<td>
						<b><?php echo JText::_("AGI_STATISTIC_TYPE");?></b>&nbsp;
						<select name="statisticType" id="statisticType"  >
							<option value="#__sdi_stat_performance" <?php if ($statisticType=="#__sdi_stat_performance"){?>selected="selected"<?php }?>><?php echo JText::_("AGI_STATISTIC_TYPE_PERFORMANCE"); ?></option>
							<option value="#__sdi_stat_attribute" <?php if ($statisticType=="#__sdi_stat_attribute"){?>selected="selected"<?php }?>><?php echo JText::_("AGI_STATISTIC_TYPE_ATTRIBUTE"); ?></option>
							<option value="#__sdi_stat_metadata" <?php if ($statisticType=="#__sdi_stat_metadata"){?>selected="selected"<?php }?>><?php echo JText::_("AGI_STATISTIC_TYPE_METADATA"); ?></option>
						</select>
					</td>
					<td colspan=3>
						<b><?php echo JText::_( 'AGI_STATISTIC_FILTER_DATE'); ?> </b>: 
						<br>
						<?php JHTML::_('behavior.calendar'); ?>
						<b><?php echo JText::_( 'AGI_STATISTIC_FILTER_DATE_FROM'); ?></b><?php echo JHTML::_('calendar',$DateFrom, "DateFrom","DateFrom","%d-%m-%Y"); ?>
						<b><?php echo JText::_( 'AGI_STATISTIC_FILTER_DATE_TO'); ?></b><?php echo JHTML::_('calendar',$DateTo, "DateTo","DateTo","%d-%m-%Y"); ?>
						<input name="dateFormat" type="hidden" value="%d-%m-%Y">
					</td>
					<td align="left">
						<b><?php echo JText::_("AGI_STATISTIC_FILTER");?></b>&nbsp;
						<input type="text" name="searchStatistic" id="searchStatistic" class="inputbox" value="<?php echo $search;?>" />			
					</td>
				</tr>
			</table>
			<br>
			<button type="submit" class="searchButton" > <?php echo JText::_("SEARCH"); ?></button>
			<button onclick="javascript:fieldReset();"><?php echo JText::_( "RESET" ); ?></button>
			<br>		
			
			<h3><?php echo JText::_("AGI_STATISTIC_SEARCH_RESULTS_TITLE"); ?></h3>
			<?php
				switch($statisticType)
				{
					case "#__sdi_stat_performance":
						HTMLadmin_statistic::listPerformance($statistics,$filter_order_Dir, $filter_order,$pageNav); 
						break;
					case "#__sdi_stat_attribute":
						HTMLadmin_statistic::listAttribute($statistics,$filter_order_Dir, $filter_order,$pageNav);
						break; 
					case "#__sdi_stat_metadata":
						HTMLadmin_statistic::listMetadata($statistics,$filter_order_Dir, $filter_order,$pageNav);
						break;
				}
			?>
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task" name="task" value="listStatistic">
			<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  		<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
		</form>
	<?php	
	}
	
	function listPerformance($statistics,$filter_order_Dir, $filter_order,$pageNav)
	{
		?>
		<table class="adminlist">
			<thead>
			<tr>
				<th class='title'><?php echo JText::_('AGI_STATISTIC_SHARP'); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("AGI_STATISTIC_SERVICE"), 'service', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("AGI_STATISTIC_OPERATION"), 'operation', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("AGI_STATISTIC_DATE"), 'date', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("AGI_STATISTIC_MIN_TIME"), 'min_time', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("AGI_STATISTIC_MAX_TIME"), 'max_time', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("AGI_STATISTIC_AVERAGE_TIME"), 'average_time', @$filter_order_Dir, @$filter_order); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
				$i=0;
				foreach ($statistics as $statistic)
				{	$i++;
					?>		
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $statistic->service; ?></td>
						<td><?php echo $statistic->operation ;?></td>
						<td><?php echo $statistic->date ;?></td>
						<td><?php echo $statistic->min_time ;?></td>
						<td><?php echo $statistic->max_time ;?></td>
						<td><?php echo $statistic->average_time ;?></td>
					</tr>
						<?php		
				}
			?>
			</tbody>
			<tfoot>
				<tr>	
					<td colspan="11"><?php echo $pageNav->getListFooter(); ?></td>
				</tr>
			</tfoot>
			</table>
		<?php 
	}
	
	function listAttribute ($statistics,$filter_order_Dir, $filter_order,$pageNav)
	{
		?>
		<table class="adminlist">
			<thead>
			<tr>
				<th class='title'><?php echo JText::_('AGI_STATISTIC_SHARP'); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("AGI_STATISTIC_ATTRIBUTE_NAME"), 'attribute_name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("AGI_STATISTIC_DATE"), 'date', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("AGI_STATISTIC_COUNT"), 'count', @$filter_order_Dir, @$filter_order); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
				$i=0;
				foreach ($statistics as $statistic)
				{	$i++;
					?>		
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $statistic->attribute_name ;?></td>
						<td><?php echo $statistic->date ;?></td>
						<td><?php echo $statistic->count ;?></td>
					</tr>
						<?php		
				}
			?>
			</tbody>
			<tfoot>
				<tr>	
					<td colspan="11"><?php echo $pageNav->getListFooter(); ?></td>
				</tr>
			</tfoot>
			</table>
		<?php 
	}
	
	function listMetadata($statistics,$filter_order_Dir, $filter_order,$pageNav)
	{
		?>
		<table class="adminlist">
			<thead>
			<tr>
				<th class='title'><?php echo JText::_('AGI_STATISTIC_SHARP'); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("AGI_STATISTIC_METADATA"), 'metadata_id', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("AGI_STATISTIC_DATE"), 'date', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("AGI_STATISTIC_COUNT"), 'count', @$filter_order_Dir, @$filter_order); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
				$i=0;
				foreach ($statistics as $statistic)
				{	$i++;
					?>		
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $statistic->metadata_id ;?></td>
						<td><?php echo $statistic->date ;?></td>
						<td><?php echo $statistic->count ;?></td>
					</tr>
						<?php		
				}
			?>
			</tbody>
			<tfoot>
				<tr>	
					<td colspan="11"><?php echo $pageNav->getListFooter(); ?></td>
				</tr>
			</tfoot>
			</table>
		<?php 
	}
}
?>