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
	
	function listObjectVersion($rows, $object_id, $page, $filter_order_Dir, $filter_order, $option)
	{
		$database =& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$ordering = ($filter_order == 'ordering');
		
		$partners =	array(); ?>
		<form action="index.php" method="post" name="adminForm">
			
			<table class="adminlist">
			<thead>
				<tr>					 			
					<th class='title' width="10px"><?php echo JText::_("CORE_SHARP"); ?></th>
					<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
					<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
					<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderObject' ); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_METADATA_STATE"), 'state', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'updated', @$filter_order_Dir, @$filter_order); ?></th>
				</tr>
			</thead>
			<tbody>		
	<?php
			$k = 0;
			$i=0;
			foreach ($rows as $row)
			{			
				$checked 	= JHTML::_('grid.checkedout',   $row, $i );
					  				
	?>
				<tr> <!-- class="<?php //echo "row$k"; ?>" -->
					<td align="center" width="10px"><?php echo $page->getRowOffset( $i );//echo $i+$page->limitstart+1;?></td>
					<td align="center">
					<?php echo $checked; ?>
				</td>	
					<td width="30px" align="center"><?php echo $row->id; ?></td>
					<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
					<td width="100px" align="right">
						<?php
						if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
							if ($disabled){
						?>
								 <?php echo $page->orderUpIcon($i, true, 'orderupObject', '', false ); ?>
					             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownObject', '', false ); ?>
			            <?php
							}
							else {
						?>
								 <?php echo $page->orderUpIcon($i, true, 'orderupObject', 'Move Up', isset($rows[$i-1]) ); ?>
					             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownObject', 'Move Down', isset($rows[$i+1]) ); ?>
						<?php
							}		
						}
						else{ 
							if ($disabled){
						?>
								 <?php echo $page->orderUpIcon($i, true, 'orderdownObject', '', false ); ?>
					             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupObject', '', false ); ?>
			            <?php
							}
							else {
						?>
								 <?php echo $page->orderUpIcon($i, true, 'orderdownObject', 'Move Down', isset($rows[$i-1]) ); ?>
			 		             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupObject', 'Move Up', isset($rows[$i+1]) ); ?>
						<?php
							}
						}?>
						<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
		            </td>
					<?php
					$link = "index.php?option=$option&amp;task=editObjectVersion&cid[]=$row->id&object_id=$object_id";
					?>								
					
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
					<td><?php echo JText::_($row->description); ?></td>		
					<td><?php echo JText::_($row->state); ?></td>
					<td width="100px"><?php if ($row->updated and $row->updated<> '0000-00-00 00:00:00') {echo date('d.m.Y h:i:s',strtotime($row->updated));} ?></td>
				</tr>
	<?php
				$k = 1 - $k;
				$i ++;
			}
			
				?>
			</tbody>
			<tfoot>
			<tr>	
			<td colspan="9"><?php echo $page->getListFooter(); ?></td>
			</tr>
			</tfoot>
			</table>
		  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
		  	<input type="hidden" name="task" value="listObject" />
		  	<input type="hidden" name="boxchecked" value="0" />
		  	<input type="hidden" name="hidemainmenu" value="0">
		  	<input type="hidden" name="object_id" value="<?php echo $object_id; ?>">
		  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
		  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
		  </form>
	<?php
			
	}	

	function newObjectVersion($object_id, $fieldsLength, $metadata_guid, $listVersionNames, $option)
	{
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td width=150><?php echo JText::_("CORE_OBJECT_METADATAID_LABEL"); ?> : </td>
					<td><input class="inputbox" type="text" size="50" name="metadata_guid" value="<?php echo $metadata_guid; ?>" disabled="disabled" /></td>								
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_NAME"); ?> : </td>
					<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['name'];?>" name="name"/></td>								
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
					<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"></textarea></td>								
				</tr>
			</table>
			
			<input type="hidden" name="object_id" value="<?php echo $object_id?>" />
			<input type="hidden" name="metadata_guid" value="<?php echo $metadata_guid?>" />
			<input type="hidden" name="created" value="<?php echo date ('Y-m-d H:i:s');?>" />
			<input type="hidden" name="createdby" value="<?php echo $user->id; ?>" /> 
			
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="" />
			
			<input type="hidden" name="versionNames" value="<?php echo implode(", ", $listVersionNames);?>" />
		</form>
			<?php 	
	}
	
	function editObjectVersion($row, $object_id, $fieldsLength, $listVersionNames, $option)
	{
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td width=150><?php echo JText::_("CORE_OBJECT_METADATAID_LABEL"); ?> : </td>
					<td><input class="inputbox" type="text" size="50" name="metadata_guid" value="<?php echo $row->guid; ?>" disabled="disabled" /></td>								
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_NAME"); ?> : </td>
					<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['name'];?>" name="name" value="<?php echo $row->name; ?>"/></td>								
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
					<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php echo $row->description; ?></textarea></td>								
				</tr>
			</table>
			
			<input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
			<input type="hidden" name="object_id" value="<?php echo $object_id?>" />
			<input type="hidden" name="metadata_guid" value="<?php echo $row->guid?>" />
			<input type="hidden" name="created" value="<?php echo date ('Y-m-d H:i:s');?>" />
			<input type="hidden" name="createdby" value="<?php echo $user->id; ?>" /> 
			
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="" />
			
			<input type="hidden" name="versionNames" value="<?php echo implode(", ", $listVersionNames);?>" />
		</form>
			<?php 	
	}
	
function viewObjectVersionLink($parent_objectlinks, $child_objectlinks, $objectversion_id, $object_id, $option)
	{
		JHTML::script('ext-base-debug.js', 'administrator/components/com_easysdi_catalog/ext/adapter/ext/');
		JHTML::script('ext-all-debug.js', 'administrator/components/com_easysdi_catalog/ext/');

		$uri =& JUri::getInstance();
		$document =& JFactory::getDocument();
		$document->addStyleSheet($uri->base() . 'components/com_easysdi_catalog/ext/resources/css/ext-all.css');
		
		$javascript = "";
	
		?>
			<form action="index.php" method="post" name="adminForm" id="adminForm"
			class="adminForm">
			<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="object_id" value="<?php echo $object_id;?>" />
			</form>
		<?php
		
		$javascript .="
			var domNode = Ext.DomQuery.selectNode('div#element-box div.m')
			Ext.DomHelper.insertHtml('beforeEnd',domNode,'<div id=formContainer></div>');
			
			// Column Model shortcut array
			var cols = [
				{ id : 'value', hidden: true, dataIndex: 'value'},
				{ id : 'name', header: '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTVERSIONLINK_GRID_NAME_HEADER"))."', sortable: true, dataIndex: 'name'}
			];
			
			var parentGridStore = new Ext.data.JsonStore({
		        fields : [{name: 'value', mapping : 'value'}, {name: 'name', mapping : 'name'}],
		        data   : ".HTML_metadata::array2json(array ("total"=>count($parent_objectlinks), "links"=>$parent_objectlinks)).",
				root   : 'links'
		    });
		    
			// declare the source Grid
		    var parentGrid = new Ext.grid.GridPanel({
				store            : parentGridStore,
		        columns          : cols,
				stripeRows       : true,
		        autoExpandColumn : 'name',
		        title            : '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTVERSIONLINK_PARENTGRID_TITLE"))."'
		    });
		
		    var childGridStore = new Ext.data.JsonStore({
		        fields : [{name: 'value', mapping : 'value'}, {name: 'name', mapping : 'name'}],
		        data   : ".HTML_metadata::array2json(array ("total"=>count($child_objectlinks), "links"=>$child_objectlinks)).",
				root   : 'links'
		    });
		
		    // create the destination Grid
		    var childGrid = new Ext.grid.GridPanel({
				store            : childGridStore,
		        columns          : cols,
				stripeRows       : true,
		        autoExpandColumn : 'name',
		        title            : '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTVERSIONLINK_CHILDGRID_TITLE"))."'
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
							width        : 650,
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
	
	function manageObjectVersionLink($objectlinks, $selected_objectlinks, $listObjecttypes, $listStatus, $listManagers, $listEditors, $object_id, $option)
	{
		JHTML::script('ext-base-debug.js', 'administrator/components/com_easysdi_catalog/ext/adapter/ext/');
		JHTML::script('ext-all-debug.js', 'administrator/components/com_easysdi_catalog/ext/');
		JHTML::script('Components_extjs.js', 'administrator/components/com_easysdi_catalog/js/');
		
		$uri =& JUri::getInstance();
		$document =& JFactory::getDocument();
		$document->addStyleSheet($uri->base() . 'components/com_easysdi_catalog/ext/resources/css/ext-all.css');
		
		$javascript = "";
	
		?>
			<form action="index.php" method="post" name="adminForm" id="adminForm"
			class="adminForm">
			<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="object_id" value="<?php echo $object_id;?>" />
			</form>
		<?php
		
		$javascript .="
			var domNode = Ext.DomQuery.selectNode('div#element-box div.m')
			Ext.DomHelper.insertHtml('beforeEnd',domNode,'<div id=formContainer></div>');
			
			// Column Model shortcut array
			var cols = [
				{ id : 'value', hidden: true, dataIndex: 'value', menuDisabled: true},
				{ id : 'name', header: '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTVERSIONLINK_GRID_NAME_HEADER"))."', sortable: true, dataIndex: 'name', menuDisabled: true}
			];
			
			var unselectedGridStore = new Ext.data.JsonStore({
		        fields : [{name: 'value', mapping : 'value'}, {name: 'name', mapping : 'name'}],
		        data   : ".HTML_metadata::array2json(array ("total"=>count($objectlinks), "links"=>$objectlinks)).",
				root   : 'links'
		    });
		    
			// declare the source Grid
		    var unselectedGrid = new Ext.grid.GridPanel({
		    	id				 : 'unselected',
				ddGroup          : 'selectedGridDDGroup',
		        ds				 : getObjectList(),
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
			    
		    var selectedGridStore = new Ext.data.JsonStore({
		        fields : [{name: 'value', mapping : 'value'}, {name: 'name', mapping : 'name'}],
		        data   : ".HTML_metadata::array2json(array ("total"=>count($selected_objectlinks), "links"=>$selected_objectlinks)).",
				root   : 'links'
		    });
				    
		    // create the destination Grid
		    var selectedGrid = new Ext.grid.GridPanel({
				id				 : 'selected',
				ddGroup          : 'unselectedGridDDGroup',
		        store            : selectedGridStore,
		        columns          : cols,
				enableDragDrop   : true,
		        stripeRows       : true,
		        autoExpandColumn : 'name',
		        flex			 : 5,
		        loadMask		 : true,
		        frame			 : false,
				title            : '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTVERSIONLINK_SELECTEDGRID_TITLE"))."'
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
										text: '<<',
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
										text: '<',
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
										text: '>',
										handler: function()
						                {
						                	var unselected = Ext.getCmp('unselected');
						                	var selected = Ext.getCmp('selected');                
			 								var records = unselected.selModel.getSelections();
			 								Ext.each(records, unselected.store.remove, unselected.store);
	                        				selected.store.add(records);
                        					selected.store.sort('name', 'ASC');
						                }
									},
									{
										xtype: 'button',
										text: '>>',
										handler: function()
						                {
						                	var unselected = Ext.getCmp('unselected');
						                	var selected = Ext.getCmp('selected');                
			 								var records = unselected.store.getRange();
			 								Ext.each(records, unselected.store.remove, unselected.store);
	                        				selected.store.add(records);
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
			        	manageObjectLinkFilter(objecttype, id, name, status, manager, editor, fromDate, toDate),
			        	{
			        		id			: 'gridPanel',
			        		xtype		 : 'panel',
							width        : 650,
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
				         value:'saveObjectLink' 
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
    		
    		// Remplir une premi�re fois les valeurs s�lectionn�es
    		var selectedValues = new Array();
 			var grid = Ext.getCmp('selected').store.data;
 			for (var i = 0 ; i < grid.length ;i++) 
 			{
 				selectedValues.push(grid.get(i).get('value'));
			}
			
			unselectedGrid.store.load({params: {selectedObjects: selectedValues.join(', ')}});
    		
    		function getObjectList()
			{
				var ds = new Ext.data.Store({
					        proxy: new Ext.data.HttpProxy({
					            url: 'index.php?option=com_easysdi_catalog&task=getObjectForLink'
					        }),
					        reader: new Ext.data.JsonReader({
					            root: 'links',
					            totalProperty: 'total',
					            id: 'value'
					        }, [
					            {name: 'value', mapping: 'value'},
					            {name: 'name', mapping: 'name'}
					        ]),
					        // turn on remote sorting
					        remoteSort: true,
					        baseParams: {limit:100, dir:'ASC', sort:'name', object_id:'".$object_id."'}
					    });
				return ds;
			}
    	";
					
		print_r("<script type='text/javascript'>Ext.onReady(function(){".$javascript."});</script>");
	}
}
?>