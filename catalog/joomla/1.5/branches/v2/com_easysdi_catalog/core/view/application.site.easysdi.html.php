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

class HTML_application {
	
	function listApplication($pageNav, $rows, $object_id, $object_name, $option, $lists)
	{
		$database =& JFactory::getDBO(); 
		$user	=& JFactory::getUser();
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
		?>	
		<script>
		function tableOrdering( order, dir, view )
		{
			var form = document.getElementById("applicationListForm");
			
			form.filter_order.value 	= order;
			form.filter_order_Dir.value	= dir;
			form.submit( view );
		}
					
		</script>
		<div id="page">
		<h1 class="contentheading"><?php echo sprintf(JText::_("CATALOG_FE_LIST_APPLICATION"), $object_name); ?></h1>
		<div class="contentin">
		
		<form action="index.php" method="GET" id="applicationListForm" name="applicationListForm">
		<div class="row">
			 <div class="row">
				<input type="submit" id="newapplication_button" name="newapplication_button" class="submit" value ="<?php echo JText::_("CATALOG_NEW_APPLICATION"); ?>" onClick="document.getElementById('applicationListForm').task.value='newApplication';document.getElementById('applicationListForm').submit();"/>
				<input type="submit" id="back_button" name="back_button" class="submit" value ="<?php echo JText::_("CORE_CANCEL"); ?>" onClick="document.getElementById('applicationListForm').task.value='backApplication';window.open('<?php echo JRoute::_('index.php?task=backApplication&object_id='.$object_id); ?>', '_self')"/>
			</div>	 
		 </div>
	<script>
		function suppressApplication_click(url, hasLinks){
			if (hasLinks == false)
				conf = confirm('<?php echo html_Metadata::cleanText(JText::_("CATALOG_CONFIRM_APPLICATION_DELETE")); ?>');
			else
				conf = confirm('<?php echo html_Metadata::cleanText(JText::_("CATALOG_CONFIRM_APPLICATION_WITHLINK_DELETE")); ?>');
			if(!conf)
				return false;
			window.open(url, '_self');
		}
	</script>
	<table id="myApplications" class="box-table">
	<thead>
	<tr>
	<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_APPLICATION_NAME"), 'name', $lists['order_Dir'], $lists['order']); ?></th>
	<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_APPLICATION_URL"), 'url', $lists['order_Dir'], $lists['order']); ?></th>
	<th class='title'><?php echo JText::_('CATALOG_APPLICATION_ACTIONS'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
		$i=0;
		$param = array('size'=>array('x'=>800,'y'=>800) );
		JHTML::_("behavior.modal","a.modal",$param);
		foreach ($rows as $row)
		{	$i++;
			
			?>		
			<tr>
			<td><?php echo $row->name; ?></td>
			<td><?php echo $row->url; ?></td>
			<td class="applicationActions">
			<?php 
			if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
			{
				?>
				<div class="logo" id="emptyPicto"></div>
				<div class="logo" id="emptyPicto"></div>
				<?php 
			} 
			else 
			{
				?>
				<div class="logo" title="<?php echo JText::_('CATALOG_APPLICATION_EDIT'); ?>" id="editApplication" onClick="window.open('./index.php?option=com_easysdi_catalog&task=editApplication&object_id=<?php echo $object_id;?>&cid[]=<?php echo $row->id;?>&Itemid=<?php echo JRequest::getVar('Itemid');?>&lang=<?php echo JRequest::getVar('lang');?>', '_self');"></div>
				<div class="logo" title="<?php echo JText::_('CATALOG_APPLICATION_DELETE'); ?>" id="deleteApplication" onClick="return suppressApplication_click('<?php echo JRoute::_("index.php?option=com_easysdi_catalog&task=deleteApplication&object_id=".$object_id."&cid[]=".$row->id); ?>', false);" ></div>
				<?php 
			}
			?>
			</td>
			</tr>
			<?php		
		}
		
	?>
	</tbody>
	</table>
	<?php echo $pageNav->getPagesCounter(); ?>&nbsp;<?php echo $pageNav->getPagesLinks(); ?>
	
		<input type="hidden" name="option" value="<?php echo $option; ?>">
		<input type="hidden" name="object_id" value="<?php echo $object_id; ?>">
		<input type="hidden" id="task" name="task" value="listApplication">
		<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
		<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		</form>
		</div>
		</div>
	<?php
	}
	
	function editApplication($row, $fieldsLength, $object_id, $option)
	{
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
		$object = new object($database);
		$object->load($object_id);
		$object_name = "\"".$object->name."\"";
		
		?>
		<div id="page">
<?php 
if ($row->id == 0)
{
?>
			<h1 class="contentheading"><?php echo JText::_( 'CATALOG_NEW_APPLICATION' )." ".$object_name ?></h1>
<?php 
}
else
{
?>
			<h1 class="contentheading"><?php echo JText::_( 'CATALOG_EDIT_APPLICATION' )." ".$row->name ?></h1>
<?php 
}
?>
		    <div id="contentin" class="contentin">
		    <form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<div class="row">
				 <div class="row">
					<input type="button" id="simple_search_button" name="simple_search_button" class="submit" value ="<?php echo JText::_("CORE_SAVE"); ?>" onClick="verify();"/>
					<input type="button" id="back_button" name="back_button" class="submit" value ="<?php echo JText::_("CORE_CANCEL"); ?>" onClick="document.getElementById('adminForm').task.value='cancelApplication';window.open('<?php echo JRoute::_('index.php?task=cancelApplication&object_id='.$object_id); ?>', '_self')"/>
				</div>	 
			 </div>
				<div class="row">	
					<div class="row">
						<label for="name"><?php echo JText::_("CATALOG_APPLICATION_NAME"); ?> : </label> 
						<input class="inputbox text full" type="text" size="50" name="name" value="<?php echo $row->name; ?>"/>								
					</div>
					<div class="row">
						<label for="windowname"><?php echo JText::_("CATALOG_APPLICATION_WINDOWNAME"); ?> : </label> 
						<input class="inputbox text full" type="text" size="50" name="windowname" value="<?php echo $row->windowname; ?>"/>								
					</div>
					<div class="row">
						<label for="description"><?php echo JText::_("CORE_DESCRIPTION"); ?> : </label> 
						<textarea rows="4" cols="50" name ="description" class="inputbox text full" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php echo $row->description; ?></textarea>								
					</div>
					<div class="row">
						<label for="url"><?php echo JText::_("CATALOG_APPLICATION_URL"); ?> : </label>
						<input class="inputbox text full" type="text" size="50" name="url" value="<?php echo $row->url; ?>"/>
					</div>
					<div class="row">
						<label for="options"><?php echo JText::_("CATALOG_APPLICATION_OPTIONS"); ?> : </label> 
						<textarea rows="4" cols="50" name ="options" class="inputbox text full" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['options'];?>);"><?php echo $row->options; ?></textarea>								
					</div>
				</div>
				
				<input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
				<input type="hidden" name="object_id" value="<?php echo $object_id?>" />
				
				<input type="hidden" name="created" value="<?php echo ($row->created)? $row->created : date ('Y-m-d H:i:s');?>" />
				<input type="hidden" name="createdby" value="<?php echo ($row->createdby)? $row->createdby : $user->id; ?>" /> 
				<input type="hidden" name="updated" value="<?php echo ($row->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
				<input type="hidden" name="updatedby" value="<?php echo ($row->createdby)? $user->id : ''; ?>" /> 
				
				<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
				<input type="hidden" name="guid" value="<?php echo $row->guid?>" />
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
				<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
			
			</form>
			</div>
		</div>
			<?php 	
	}
}
?>