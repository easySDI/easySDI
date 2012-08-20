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

class HTML_objectversion {
	
	function listObjectVersion($pageNav, $rows, $object_id, $object_name, $option, $lists)
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
			var form = document.getElementById("objectversionListForm");
			
			form.filter_order.value 	= order;
			form.filter_order_Dir.value	= dir;
			form.submit( view );
		}
					
		</script>
		<div id="page">
		<h1 class="contentheading"><?php echo sprintf(JText::_("CATALOG_FE_LIST_OBJECTVERSION"), $object_name); ?></h1>
		<div class="contentin">
		
		<form action="index.php" method="GET" id="objectversionListForm" name="objectversionListForm">
		<div class="row">
			 <div class="row">
				<input type="submit" id="newobjectversion_button" name="newobjectversion_button" class="submit" value ="<?php echo JText::_("CATALOG_NEW_OBJECTVERSION"); ?>" onClick="document.getElementById('objectversionListForm').task.value='newObjectVersion';document.getElementById('objectversionListForm').submit();"/>
				<input type="submit" id="back_button" name="back_button" class="submit" value ="<?php echo JText::_("CORE_CANCEL"); ?>" onClick="document.getElementById('objectversionListForm').task.value='cancelObjectVersion';window.open('<?php echo JRoute::_(displayManager::buildUrl('index.php?task=cancelObjectVersion&object_id='.$object_id)); ?>', '_self')"/>
			</div>	 
		 </div>
	<script>
		function suppressObjectVersion_click(url, hasLinks){
			if (hasLinks == false)
				conf = confirm('<?php echo html_Metadata::cleanText(JText::_("CATALOG_CONFIRM_OBJECTVERSION_DELETE")); ?>');
			else
				conf = confirm('<?php echo html_Metadata::cleanText(JText::_("CATALOG_CONFIRM_OBJECTVERSION_WITHLINK_DELETE")); ?>');
			if(!conf)
				return false;
			window.open(url, '_self');
		}
	</script>
	<div class="ticker">
	<h2><?php echo JText::_("CORE_SEARCH_RESULTS_TITLE"); ?></h2>
	<?php
	if(count($rows) == 0){
		echo "<p><strong>".JText::_("CATALOG_OBJECTVERSION_NORESULTFOUND")."</strong>&nbsp;0&nbsp;</p>";
		
	}else{?>
	<table id="myObjectversions" class="box-table">
	<thead>
	<tr>
	<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_OBJECTVERSION_NAME"), 'title', $lists['order_Dir'], $lists['order']); ?></th>
	<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_OBJECTVERSION_DESCRIPTION"), 'description', $lists['order_Dir'], $lists['order']); ?></th>
	<th class='title'><?php echo JText::_('CATALOG_METADATA_ACTIONS'); ?></th>
	</tr>
	</thead>
	<?php } ?>
	<tbody>
	<?php
		$i=0;
		$param = array('size'=>array('x'=>800,'y'=>800) );
		JHTML::_("behavior.modal","a.modal",$param);
		foreach ($rows as $row)
		{	$i++;
			
			?>		
			<tr>
			<td ><a class="modal" title="<?php echo addslashes(JText::_("CATALOG_VIEW_MD")); ?>" href="./index.php?tmpl=component&option=com_easysdi_catalog&task=showMetadata&id=<?php echo $row->metadata_guid;  ?>" rel="{handler:'iframe',size:{x:650,y:600}}"> <?php echo $row->title ;?></a></td>
			<td ><?php echo $row->description; ?></td>
			<td class="metadataActions">
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
				<div class="logo" title="<?php echo addslashes(JText::_('CATALOG_OBJECTVERSION_EDIT')); ?>" id="editObject" onClick="window.open('./index.php?option=com_easysdi_catalog&task=editObjectVersion&object_id=<?php echo $object_id;?>&cid[]=<?php echo $row->id;?>&Itemid=<?php echo JRequest::getVar('Itemid');?>&lang=<?php echo JRequest::getVar('lang');?>', '_self');"></div>
				<?php
				if (count($rows)>1 and ($row->metadatastate_id == 2 or $row->metadatastate_id == 4)) // Impossible de supprimer si le statut n'est pas "ARCHIVED" ou "UNPUBLISHED"
				{
					$links = 0;
					$query = 'SELECT count(*)' .
							' FROM #__sdi_objectversionlink l
							  INNER JOIN #__sdi_objectversion child ON child.id=l.child_id
							  INNER JOIN #__sdi_objectversion parent ON parent.id=l.parent_id
							  INNER JOIN #__sdi_object o_parent ON o_parent.id=parent.object_id
							  INNER JOIN #__sdi_object o_child ON o_child.id=child.object_id' .
							' WHERE l.parent_id=' . $row->id.
							'		OR l.child_id=' . $row->id;
					$database->setQuery($query);
					//echo $database->getQuery();
					$links = $database->loadResult();
					//echo $links;
					
					if ($links > 0)
					{
						?> 
						<div class="logo" title="<?php echo addslashes(JText::_('CATALOG_OBJECTVERSION_DELETE')); ?>" id="deleteObject" onClick="return suppressObjectVersion_click('<?php echo JRoute::_(displayManager::buildUrl("index.php?option=com_easysdi_catalog&task=deleteObjectVersion&object_id=".$object_id."&cid[]=".$row->id)); ?>', true)" ></div>
						<?php 
					}
					else
					{
						?> 
						<div class="logo" title="<?php echo addslashes(JText::_('CATALOG_OBJECTVERSION_DELETE')); ?>" id="deleteObject" onClick="return suppressObjectVersion_click('<?php echo JRoute::_(displayManager::buildUrl("index.php?option=com_easysdi_catalog&task=deleteObjectVersion&object_id=".$object_id."&cid[]=".$row->id)); ?>', false);" ></div>
						<?php
					}
				}
				else {
				?>
				<div class="logo" id="emptyPicto"></div>
				<?php 
				}
			}
			?>
			<div class="logo" title="<?php echo addslashes(JText::_('CATALOG_OBJECTVERSION_VIEWLINK')); ?>" id="viewObjectVersionLink" onClick="window.open('<?php echo JRoute::_(displayManager::buildUrl("index.php?option=com_easysdi_catalog&task=viewObjectVersionLink&object_id=".$object_id."&cid[]=".$row->id)); ?>', '_self');" ></div>
			<div class="logo" title="<?php echo addslashes(JText::_('CATALOG_OBJECTVERSION_MANAGELINK')); ?>" id="manageObjectVersionLink" onClick="window.open('<?php echo JRoute::_(displayManager::buildUrl("index.php?option=com_easysdi_catalog&task=manageObjectVersionLink&object_id=".$object_id."&cid[]=".$row->id)); ?>', '_self');" ></div>
			</td>
			</tr>
			<?php		
		}
		
	?>
	</tbody>
	</table>
	
	</div>
	
	<div class="paging">
	    <h3 class="hidden"><?php JText::_('WEITERE TREFFER ANZEIGEN'); ?></h3>
	    <p class="info"><?php echo $pageNav->getPagesCounter(); ?></p>
		<p class="select"><?php echo $pageNav->getPagesLinks( ); ?></p>
	</div>

		<input type="hidden" name="option" value="<?php echo $option; ?>">
		<input type="hidden" name="object_id" value="<?php echo $object_id; ?>">
		<input type="hidden" id="task" name="task" value="listObjectVersion">
		<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
		<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		</form>
		</div>
		</div>
	<?php
	}
	
	function editObjectVersion($row, $object_id, $fieldsLength, $metadata_guid, $option)
	{
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
		$object = new object($database);
		$object->load($object_id);
		$objectversion_name = "\"".$row->title."\"";
		$object_name = "\"".$object->name."\"";
		?>
		<div id="page">
<?php 
if ($row->id == 0)
{
?>
			<h1 class="contentheading"><?php echo JText::_( 'CATALOG_EDIT_OBJECTVERSION_OBJECT' )." ".$object_name." " ?>[<?php echo JText::_( 'CATALOG_EDIT_OBJECTVERSION_NEW' ) ?>]</h1>
<?php 
}
else
{
?>
			<h1 class="contentheading"><?php echo JText::_( 'CATALOG_EDIT_OBJECTVERSION_OBJECT' )." ".$object_name." " ?><?php echo JText::_( 'CATALOG_EDIT_OBJECTVERSION_' )." ".$objectversion_name ?></h1>
<?php 
}
?>
		    <div id="contentin" class="contentin">
		    <form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<div class="row">
				 <div class="row">
					<input type="button" id="save_button" name="save_button" class="submit" value ="<?php echo JText::_("CORE_SAVE"); ?>" onClick="document.getElementById('adminForm').task.value='saveObjectVersion'; document.getElementById('adminForm').submit();"/>
					<input type="button" id="back_button" name="back_button" class="submit" value ="<?php echo JText::_("CORE_CANCEL"); ?>" onClick="document.getElementById('adminForm').task.value='backObjectVersion';window.open('<?php echo JRoute::_(displayManager::buildUrl('index.php?option=com_easysdi_catalog&task=backObjectVersion&object_id='.$object_id)); ?>', '_self')"/>
				</div>	 
			 </div>
				<div class="row">	
					<div class="row">
						<label for="metadata_guid"><?php echo JText::_("CORE_OBJECT_METADATAID_LABEL"); ?> : </label> 
						<input class="inputbox text full" type="text" size="50" name="metadata_guid" value="<?php echo $metadata_guid; ?>" disabled="disabled" />								
					</div>
					<div class="row">
						<label for="description"><?php echo JText::_("CORE_DESCRIPTION"); ?> : </label> 
						<textarea rows="4" cols="50" name ="description" class="inputbox text full" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php echo $row->description; ?></textarea>								
					</div>
				</div>
				
				<input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
				<input type="hidden" name="object_id" value="<?php echo $object_id?>" />
				<input type="hidden" name="objectversion_id" value="<?php echo $row->id?>" />
				<input type="hidden" name="metadata_guid" value="<?php echo $metadata_guid?>" />
				
				<input type="hidden" name="created" value="<?php echo ($row->created)? $row->created : date ('Y-m-d H:i:s');?>" />
				<input type="hidden" name="createdby" value="<?php echo ($row->createdby)? $row->createdby : $user->id; ?>" /> 
				<input type="hidden" name="updated" value="<?php echo ($row->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
				<input type="hidden" name="updatedby" value="<?php echo ($row->createdby)? $user->id : ''; ?>" /> 
				
				<input type="hidden" name="title" value="<?php echo ($row->title)? $row->title : date ('Y-m-d H:i:s');?>" />
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
	
	function viewObjectVersionLink($parent_objectlinks, $child_objectlinks, $objectversion_id, $object_id, $option)
	{
		$database =& JFactory::getDBO(); 
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		JHTML::script('ext-base.js', 'administrator/components/com_easysdi_catalog/ext/adapter/ext/');
		JHTML::script('ext-all.js', 'administrator/components/com_easysdi_catalog/ext/');

		$uri =& JUri::getInstance();
		$document =& JFactory::getDocument();
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/ext/resources/css/ext-all.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/form_layout_frontend.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/MultiSelect.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/fileuploadfield.css');
		
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/shCore.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/shThemeDefault.css');
		
		$language =& JFactory::getLanguage();
		
		if (file_exists($uri->base(true).'/components/com_easysdi_catalog/ext/src/locale/ext-lang-'.$language->_lang.'.js')) 
			JHTML::script('ext-lang-'.$language->_lang.'.js', 'administrator/components/com_easysdi_catalog/ext/src/locale/');
		else
			JHTML::script('ext-lang-'.substr($language->_lang, 0 ,2).'.js', 'administrator/components/com_easysdi_catalog/ext/src/locale/');
		
		
		$javascript = "";
	
		$objectversion = new objectversion($database);
		$objectversion->load($objectversion_id);
		
		$object = new object($database);
		$object->load($objectversion->object_id);
		
		$objectversion_name = "\"".$objectversion->title."\"";
		$object_name = "\"".$object->name."\"";
		?>
		<div id="page">
			<h2 class="contentheading"><?php echo JText::_("CATALOG_VIEW_OBJECTVERSIONLINK")." ".$object_name." ".JText::_("CATALOG_EDIT_OBJECTVERSION_")." ".$objectversion_name ?></h2>
		    <div id="contentin" class="contentin">
		   <form action="index.php" method="post" name="adminForm" id="adminForm"
			class="adminForm">
			<div class="row">
				 <div class="row">
					<input type="submit" id="back_button" name="back_button" class="submit" value ="<?php echo JText::_("CORE_CANCEL"); ?>" onClick="document.getElementById('adminForm').task.value='cancelObjectVersionLink';window.open('<?php echo JRoute::_(displayManager::buildUrl('index.php?task=cancelObjectVersionLink&object_id='.$object_id)); ?>', '_self')"/>
				</div>	 
			 </div>
			<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="object_id" value="<?php echo $object_id;?>" />
			<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
			<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
			</form>
			 <table width="100%">
			<tr>
				<td width="100%"><div id="viewLinksOutput"></div></td>
			</tr>
		   </table>
		   </div>
			</div>
		<?php
		
		$javascript .="
			//var domNode = Ext.DomQuery.selectNode('div#element-box div.m')
			//Ext.DomHelper.insertHtml('beforeEnd',domNode,'<div id=formContainer></div>');
			var domNode = Ext.DomQuery.selectNode('div#viewLinksOutput')
			Ext.DomHelper.insertHtml('afterBegin',domNode,'<div id=formContainer></div>');
				
			// Column Model shortcut array
			var cols = [
				{ id : 'value', hidden: true, dataIndex: 'value'},
				{ id : 'name', header: '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTVERSIONLINK_GRID_NAME_HEADER"))."', sortable: true, dataIndex: 'name', width:350}
			];
			
			var parentGridStore = new Ext.data.JsonStore({
		        fields : [{name: 'value', mapping : 'value'}, {name: 'name', mapping : 'name'}],
		        data   : ".HTML_metadata::array2json(array ("total"=>count($parent_objectlinks), "links"=>$parent_objectlinks)).",
				root   : 'links'
		    });
		    
			// declare the source Grid
		    var parentGrid = new Ext.grid.GridPanel({
				store            : parentGridStore,
		        width			 : 350,
		        columns          : cols,
				stripeRows       : true,
		        autoExpandColumn : 'name',
		        title            : '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTVERSIONLINK_PARENTGRID_TITLE"))."',
		        viewConfig: {
							 	forceFit: true,
								scrollOffset:0
							 }
		    });
		
		    var childGridStore = new Ext.data.JsonStore({
		        fields : [{name: 'value', mapping : 'value'}, {name: 'name', mapping : 'name'}],
		        data   : ".HTML_metadata::array2json(array ("total"=>count($child_objectlinks), "links"=>$child_objectlinks)).",
				root   : 'links'
		    });
		
		    // create the destination Grid
		    var childGrid = new Ext.grid.GridPanel({
				store            : childGridStore,
		        width			 : 350,
		        columns          : cols,
				stripeRows       : true,
		        autoExpandColumn : 'name',
		        title            : '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTVERSIONLINK_CHILDGRID_TITLE"))."',
		        viewConfig: {
							 	forceFit: true,
								scrollOffset:0
							 }
		    });
		    
			// Cr�er le formulaire qui va contenir la structure
			var form = new Ext.form.FormPanel(
				{
					id:'linksForm',
					url: 'index.php',
					border:false,
			        collapsed:false,
			        renderTo: document.getElementById('formContainer'),
			        items        : [
			        	{
			        		xtype		 : 'panel',
							width        : 700,
							height       : 300,
							layout       : 'hbox',
							defaults     : { flex : 1 }, //auto stretch
							layoutConfig : { align : 'stretch' },
							items        : [
								parentGrid,
								childGrid
							]
						}
					]
			    }
			);
	        
			// Affichage du formulaire
    		form.doLayout();
    	";
					
		print_r("<script type='text/javascript'>Ext.onReady(function(){".$javascript."});</script>");
	}
	
	function manageObjectVersionLink($objectlinks, $selected_objectlinks, $listObjecttypes, $listStatus, $listManagers, $listEditors, $objectversion_id, $object_id, $objecttypelink, $option)
	{
		$database =& JFactory::getDBO(); 
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		JHTML::script('ext-base.js', 'administrator/components/com_easysdi_catalog/ext/adapter/ext/');
		JHTML::script('ext-all.js', 'administrator/components/com_easysdi_catalog/ext/');
		JHTML::script('Components_extjs.js', 'administrator/components/com_easysdi_catalog/js/');
		
		$uri =& JUri::getInstance();
		$document =& JFactory::getDocument();
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/ext/resources/css/ext-all.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/form_layout_frontend.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/MultiSelect.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/fileuploadfield.css');
		
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/shCore.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/shThemeDefault.css');
		
		$language =& JFactory::getLanguage();
		
		if (file_exists($uri->base(true).'/components/com_easysdi_catalog/ext/src/locale/ext-lang-'.$language->_lang.'.js')) 
			JHTML::script('ext-lang-'.$language->_lang.'.js', 'administrator/components/com_easysdi_catalog/ext/src/locale/');
		else
			JHTML::script('ext-lang-'.substr($language->_lang, 0 ,2).'.js', 'administrator/components/com_easysdi_catalog/ext/src/locale/');
		
		
		$javascript = "";
	
		$objectversion = new objectversion($database);
		$objectversion->load($objectversion_id);
		
		$object = new object($database);
		$object->load($objectversion->object_id);
		
		$objectversion_name = "\"".$objectversion->title."\"";
		$object_name = "\"".$object->name."\"";
		?>
		<div id="page">
			<h2 class="contentheading"><?php echo JText::_("CATALOG_MANAGE_OBJECTVERSIONLINK")." ".$object_name." ".JText::_("CATALOG_EDIT_OBJECTVERSION_")." ".$objectversion_name ?></h2>
		    <div id="contentin" class="contentin">
		    <form action="index.php" method="post" name="adminForm" id="adminForm"
			class="adminForm">
			<div class="row">
				 <div class="row">
					<input type="submit" id="back_button" name="back_button" class="submit" value ="<?php echo JText::_("CORE_CANCEL"); ?>" onClick="document.getElementById('adminForm').task.value='cancelObjectVersionLink';window.open('<?php echo JRoute::_(displayManager::buildUrl('index.php?task=cancelObjectVersionLink&object_id='.$object_id)); ?>', '_self')"/>
				</div>	 
			 </div>
			<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="object_id" value="<?php echo $object_id;?>" />
			<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
			<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
			</form>
			<table width="auto">
			<tr>
				<td width="auto">
				<div id="viewLinksOutput"></div></td>
			</tr>
		   </table>
		   </div>
		</div>
		<?php
		
		//$pageSize=10;
		
		$javascript .="
			//var domNode = Ext.DomQuery.selectNode('div#element-box div.m')
			//Ext.DomHelper.insertHtml('beforeEnd',domNode,'<div id=formContainer></div>');
			var domNode = Ext.DomQuery.selectNode('div#viewLinksOutput')
			Ext.DomHelper.insertHtml('afterBegin',domNode,'<div id=formContainer></div>');
			
			// Column Model shortcut array
			var cols = [
				{ id : 'value', hidden: true, dataIndex: 'value', menuDisabled: true},
				{ id : 'objecttype_id', hidden: true, dataIndex: 'objecttype_id', menuDisabled: true},
				{ id : 'name', header: '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTVERSIONLINK_GRID_NAME_HEADER"))."', sortable: true, dataIndex: 'name', menuDisabled: true, width:350}
			];
			
			var unselectedGridStore = new Ext.data.JsonStore({
		        fields : [{name: 'value', mapping : 'value'}, {name: 'objecttype_id', mapping : 'objecttype_id'}, {name: 'name', mapping : 'name'}],
		        data   : ".HTML_metadata::array2json(array ("total"=>count($objectlinks), "links"=>$objectlinks)).",
				root   : 'links'
		    });

		   // declare the source Grid
		    var unselectedGrid = new Ext.grid.GridPanel({
		    	id				 : 'unselected',
				ddGroup          : 'selectedGridDDGroup',
		        ds				 : getObjectList(),
				width			 : 300,
				columns          : cols,
				enableDragDrop   : true,
		        stripeRows       : true,
		        autoExpandColumn : 'name',
		        flex			 : 5,
		        loadMask		 : true,
		        frame			 : false,
				title            : '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTVERSIONLINK_UNSELECTEDGRID_TITLE"))."',
				viewConfig: {
							 	forceFit: true,
								scrollOffset:0
							 }
		    });

		    /*
		    bbar			 : new Ext.PagingToolbar({
									pageSize: pagesize,
									store: getObjectList(),
									listeners: { 
											        change: function(data) { 
											          var unselected = Ext.getCmp('unselected');
											          lastOptions = this.lastOptions;
													  
						                			  //unselected.store.reload(lastOptions);
											        } 
											      } 							
									}),
				
		    */
		    
		    var selectedGridStore = new Ext.data.JsonStore({
		        fields : [{name: 'value', mapping : 'value'}, {name: 'objecttype_id', mapping : 'objecttype_id'}, {name: 'name', mapping : 'name'}],
		        data   : ".HTML_metadata::array2json(array ("total"=>count($selected_objectlinks), "links"=>$selected_objectlinks)).",
				root   : 'links'
		    });
				    
		    // create the destination Grid
		    var selectedGrid = new Ext.grid.GridPanel({
				id				 : 'selected',
				ddGroup          : 'unselectedGridDDGroup',
		        store            : selectedGridStore,
		        width			 : 300,
				columns          : cols,
				enableDragDrop   : true,
		        stripeRows       : true,
		        autoExpandColumn : 'name',
		        flex			 : 5,
		        loadMask		 : true,
		        frame			 : false,
				title            : '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTVERSIONLINK_SELECTEDGRID_TITLE"))."',
		        viewConfig: {
							 	forceFit: true,
								scrollOffset:0
							 }
		    });
		    
		    var htmlButtons = new Ext.Panel({
				id				 : 'htmlButtons',
				frame			 : false,
				border			 : false,
				layout      	 : 'vbox',
				flex			 : 1,
				layoutConfig	 : { align : 'center', pack:'center'},
				defaults		 : {margins:'0 0 5 0'},
                items			 : [
									{
										xtype: 'button',
										text: ' << ',
										width: 30,
										handler: function()
						                {
						                	var unselected = Ext.getCmp('unselected');
						                	var selected = Ext.getCmp('selected');                
			 								
											selected.store.removeAll();	
			 								unselected.store.reload({                    
			 									params: 
			 									{ 
				 									objecttype_id: Ext.getCmp('objecttype_id').getValue(),
				 									id:Ext.getCmp('id').getValue(),
				 									name:Ext.getCmp('name').getValue(),
				 									status:Ext.getCmp('status').getValue(),
				 									manager:Ext.getCmp('manager').getValue(),
				 									editor:Ext.getCmp('editor').getValue(),
				 									fromDate:Ext.getCmp('fromDate').getValue(),
				 									toDate:Ext.getCmp('toDate').getValue(),
				 									selectedObjects: ''
												}                
											});	
						                }
									},
									{
										xtype: 'button',
										text: ' < ',
										width: 30,
										handler: function()
						                {
						                	var unselected = Ext.getCmp('unselected');
						                	var selected = Ext.getCmp('selected');                
			 								
						                	var records = selected.selModel.getSelections();
			 								Ext.each(records, selected.store.remove, selected.store);
	                        				
			 								var selectedValues = new Array();
			 								var grid = Ext.getCmp('selected').store.data;
			 								for (var i = 0 ; i < grid.length ;i++) 
			 								{
			 									selectedValues.push(grid.get(i).get('value'));
											}
											
			 								unselected.store.reload({                    
			 									params: 
			 									{ 
				 									objecttype_id: Ext.getCmp('objecttype_id').getValue(),
				 									id:Ext.getCmp('id').getValue(),
				 									name:Ext.getCmp('name').getValue(),
				 									status:Ext.getCmp('status').getValue(),
				 									manager:Ext.getCmp('manager').getValue(),
				 									editor:Ext.getCmp('editor').getValue(),
				 									fromDate:Ext.getCmp('fromDate').getValue(),
				 									toDate:Ext.getCmp('toDate').getValue(),
				 									selectedObjects: selectedValues.join(', ')
												}                
											});	
						                }
									},
									{
										xtype: 'button',
										text: ' > ',
										width: 30,
										handler: function()
						                {
						                	var unselected = Ext.getCmp('unselected');
						                	var selected = Ext.getCmp('selected');                
			 								var records = unselected.selModel.getSelections();
			 								
                        					// Traiter chaque objet � ajouter
											for (i=0;i<records.length;i++)
											{
												//console.log(records[i]);
												if (!childbound_upper_reached(records[i]))
												{
													Ext.each(records[i], unselected.store.remove, unselected.store);
			                        				selected.store.add(records[i]);
		                        				}
											}
                        					selected.store.sort('name', 'ASC');
						                }
									},
									{
										xtype: 'button',
										text: ' >> ',
										width: 30,
										handler: function()
						                {
						                	var unselected = Ext.getCmp('unselected');
							                	var selected = Ext.getCmp('selected');                
				 								var records = unselected.store.getRange();
				 								
                        					// Traiter chaque objet � ajouter
											for (i=0;i<records.length;i++)
											{
												if (!childbound_upper_reached(records[i]))
												{
													Ext.each(records[i], unselected.store.remove, unselected.store);
			                        				selected.store.add(records[i]);
		                        				}
											}
                        					selected.store.sort('name', 'ASC');
						                }
									}
								   ]
		    });
		    
		    var objecttype = new Array();
		    objecttype['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTVERSIONLINK_OBJECTTYPE_LABEL'))."';
			objecttype['list'] = $listObjecttypes;
			
			var id = new Array();
		    id['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTVERSIONLINK_ID_LABEL'))."';
			
			var name = new Array();
		    name['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTVERSIONLINK_NAME_LABEL'))."';
			
		    var status = new Array();
		    status['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTVERSIONLINK_STATUS_LABEL'))."';
			status['list'] = $listStatus;
			
			var manager = new Array();
		    manager['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTVERSIONLINK_MANAGER_LABEL'))."';
			manager['list'] = $listManagers;
			
			var editor = new Array();
		    editor['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTVERSIONLINK_EDITOR_LABEL'))."';
			editor['list'] = $listEditors;
			
			var fromDate = new Array();
		    fromDate['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTVERSIONLINK_FROMDATE_LABEL'))."';
			
		    var toDate = new Array();
		    toDate['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTVERSIONLINK_TODATE_LABEL'))."';
			
		    // Cr�er le formulaire qui va contenir la structure
			var form = new Ext.form.FormPanel(
				{
					id:'linksForm',
					url: 'index.php',
					method: 'POST',
					border: false,
			        collapsed: false,
			        labelWidth: 200,
					renderTo: document.getElementById('formContainer'),
			        standardSubmit:true,
			        items        : [
			        	{
			        		xtype:'fieldset',
			        		title:'".html_Metadata::cleanText(JText::_('CATALOG_OBJECTVERSIONLINK_FILTERS_LABEL'))."',
			        		collapsible:false,
			        		items:[manageObjectLinkFilter(objecttype, id, name, status, manager, editor, fromDate, toDate)]
						},
			        	{
			        		id			: 'gridPanel',
			        		xtype		 : 'panel',
							width        : 660,
							height       : 300,
							layout       : 'hbox',
							border		 : false,
							layoutConfig : { align: 'stretch', pack : 'start', padding: '10 10 10 10'},
                            items        : [
								unselectedGrid,
								htmlButtons,
								selectedGrid
							]
						},
				       { 
				         id:'objectlinks', 
				         xtype: 'hidden',
				         value:'' 
				       },
				       { 
				         id:'task', 
				         xtype: 'hidden',
				         value:'saveObjectVersionLink' 
				       },
				       { 
				         id:'option', 
				         xtype: 'hidden',
				         value:'".$option."' 
				       },
				       { 
				         id:'object_id', 
				         xtype: 'hidden',
				         value:'".$object_id."' 
				       },
				       { 
				         id:'objectversion_id', 
				         xtype: 'hidden',
				         value:'".$objectversion_id."' 
				       }
					],
					buttons: [
						{
							text:'".html_Metadata::cleanText(JText::_('CORE_SAVE'))."',
		                    handler: function(){
		                    	var selectedValues = new Array();
					 			var grid = Ext.getCmp('selected').store.data;
							 	for (var i = 0 ; i < grid.length ;i++) 
					 			{
					 				selectedValues.push(grid.get(i).get('value'));
								}
								
		                    	form.getForm().setValues({objectlinks: selectedValues.join(', ')});
							    form.getForm().submit();
		                    	}
						}
					]
			    }
			);

			/****
	        * Setup Drop Targets
	        ***/
	        // This will make sure we only drop to the  view scroller element
	        var unselectedGridDropTargetEl =  unselectedGrid.getView().scroller.dom;
	        var unselectedGridDropTarget = new Ext.dd.DropTarget(unselectedGridDropTargetEl, {
	                ddGroup    : 'unselectedGridDDGroup',
	                notifyDrop : function(ddSource, e, data){
	                       var records =  ddSource.dragData.selections;
	                       Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
	                        //unselectedGrid.store.add(records);
	                        //unselectedGrid.store.sort('name', 'ASC');
	                        
	                        var selectedValues = new Array();
				 			var grid = Ext.getCmp('selected').store.data;
						 	for (var i = 0 ; i < grid.length ;i++) 
				 			{
				 				selectedValues.push(grid.get(i).get('value'));
							}
							
				 			unselectedGrid.store.reload({                    
				 			params: { 
				 				selectedObjects: selectedValues.join(', ')
				 				//start: unselectedGrid.bbar.get('start')
								}                
							});	
							
	                        return true
	                }
	        });
	
	
	        // This will make sure we only drop to the view scroller element
	        var selectedGridDropTargetEl = selectedGrid.getView().scroller.dom;
	        var selectedGridDropTarget = new Ext.dd.DropTarget(selectedGridDropTargetEl, {
	                ddGroup    : 'selectedGridDDGroup',
	                notifyDrop : function(ddSource, e, data){
	                		var records =  ddSource.dragData.selections;
	                        Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
	                        selectedGrid.store.add(records);
	                        selectedGrid.store.sort('name', 'ASC');
	                        return true
	                }
	        });
	        
			// Affichage du formulaire
    		form.doLayout();
    		
    		// Remplir une première fois les valeurs sélectionnées
    		var selectedValues = new Array();
 			var grid = Ext.getCmp('selected').store.data;
 			for (var i = 0 ; i < grid.length ;i++) 
 			{
 				selectedValues.push(grid.get(i).get('value'));
			}
			
			//unselectedGrid.store.load({params: {selectedObjects: selectedValues.join(', ')}});
    		//unselectedGrid.getBottomToolbar().bind(unselectedGrid.store);
    		
    		function getObjectList()
			{
				var ds = new Ext.data.Store({
					        proxy: new Ext.data.HttpProxy({
					            url: 'index.php?option=com_easysdi_catalog&task=getObjectVersionForLink'
					        }),
					        reader: new Ext.data.JsonReader({
					            root: 'links',
					            totalProperty: 'total',
					            id: 'value'
					        }, [
					            {name: 'value', mapping: 'value'},
					            {name: 'objecttype_id', mapping: 'objecttype_id'},
					           	{name: 'name', mapping: 'name'}
					        ]),
					        // turn on remote sorting
					        remoteSort: true,
					        baseParams: {dir:'ASC', sort:'name', objectversion_id:'".$objectversion_id."', object_id:'".$object_id."'}
					    });
				return ds;
				
			}
			
			function childbound_upper_reached(toAddChild)
			{
				// Nombre max d'objets autoris�s par type
				var objecttypelink;
				objecttypelink = ".HTML_metadata::array2json($objecttypelink).";
				//console.log(objecttypelink);
				
				// Traiter chaque objet à ajouter
				//var toAddChilds;
				//toAddChilds = Ext.getCmp('unselected').selModel.getSelections();
				//console.log(toAddChilds);
				
				//for (i=0;i<toAddChilds.length;i++)
				//{
					//var toAddChild = toAddChilds[i];
					//console.log(toAddChild.get('objecttype_id'));
					
					// Parcours des objets d�j� s�lectionn�s et r�cup�rer tous ceux qui sont du m�me type que 
					// l'objet qui va �tre ajout�
					var countSameType = 0;
					var selectedChilds;
					selectedChilds = Ext.getCmp('selected').store.data.items;
					for(j=0;j<selectedChilds.length;j++)
					{
						var selectedChild = selectedChilds[j];
						//console.log(selectedChild.get('objecttype_id'));
						if (selectedChild.get('objecttype_id') == toAddChild.get('objecttype_id'))
							countSameType++;	
					}
					//console.log(countSameType);
					
					// R�cup�rer le nombre max d'enfants de ce type autoris�
					var maxBound=0;
					for(j=0;j<objecttypelink.length;j++)
					{
						var link = objecttypelink[j];
						//console.log(link['objecttype_id']);	
						//console.log(link['childbound_upper']);
						//console.log(link['objecttype_id'] + ' - ' + toAddChild.get('objecttype_id'));	
					
						if (link['objecttype_id'] == toAddChild.get('objecttype_id'))
							maxBound = link['childbound_upper'];
					}
					
					// Si le nombre d'objets du m�me type est sup�rieur ou �gal au nombre d'objets autoris�s pour ce type,
					// emp�cher l'ajout
					//console.log(countSameType + ' - ' + maxBound);	
					if (countSameType >= maxBound)
					{
						//console.log('Borne max pour les enfants de ce type atteinte');
						Ext.MessageBox.alert('".html_Metadata::cleanText(JText::_('CATALOG_MANAGEOBJECTVERSION_MSG_MAXCHILDREACHED_TITLE'))."', 
                    						 toAddChild.get('name') + '".html_Metadata::cleanText(JText::_('CATALOG_MANAGEOBJECTVERSION_MSG_MAXCHILDREACHED_TEXT'))."');
						return true;
					}		
				//}
				return false;
			}
    	";
					
		print_r("<script type='text/javascript'>Ext.onReady(function(){".$javascript."});</script>");
	}
}
?>