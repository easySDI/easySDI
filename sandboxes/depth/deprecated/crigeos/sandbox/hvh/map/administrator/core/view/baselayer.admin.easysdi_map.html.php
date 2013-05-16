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
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.jsLoaderUtil.php');


class HTML_baselayer
{
	function listBaseLayer( $rows, $pageNav,$search, $filter_order_Dir, $filter_order, $option)
	{
		
		JToolBarHelper::title(JText::_("MAP_LIST_BASELAYER"), 'map.png');
		?>
		<script>
			function baselayer_overview(id, task){
			
				if(!isNaN(id)&& task!= "" ){
					document.getElementById("overviewLayerId").value = id;
					if(task =="setoverview")
						document.getElementById("overviewLayerMode").value = 1;
					if(task =="unsetoverview")
						document.getElementById("overviewLayerMode").value = 0;
					document.adminForm.submit();
					
				}
				else
					return
				
				
			}
		</script>
		<form action="index.php" method="GET" name="adminForm">
		<table width="100%">
			<tr>
				<td class="key"  width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchBaseLayer" id="searchBaseLayer" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchBaseLayer').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
		<table class="adminlist">
			<thead>
				<tr>
					<th width="20" class='title'><?php echo JText::_("MAP_BASELAYER_SHARP"); ?></th>
					<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_BASELAYER_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_BASELAYER_ISOVERVIEW"), 'published', @$filter_order_Dir, @$filter_order); ?></th>		
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_PUBLISHED"), 'published', @$filter_order_Dir, @$filter_order); ?></th>					
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_BASELAYER_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_BASELAYER_URL"), 'url', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_BASELAYER_LAYERS"), 'layers', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_BASELAYER_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
					<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderBaseMapLayer' ); ?></th>
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
					<td> <?php echo self::getOverviewSetting($row,$i, 'tick.png', 'publish_x.png', 'baselayer_'); ?></td>
					<td> <?php echo JHTML::_('grid.published',$row,$i, 'tick.png', 'publish_x.png', 'baselayer_'); ?></td>
					<td><?php echo $row->description; ?></td>
					<td><?php echo $row->url; ?></td>
					<td><?php echo $row->layers; ?></td>
					<td width="100px" align="right" >
					<?php  $ordering = ($filter_order == 'ordering')  ? true : false;
					if ($filter_order_Dir=="asc"){						
					?>
						<span><?php echo $pageNav->orderUpIcon($i, true, 'orderupbasemaplayer', 'Move Up', $ordering ); ?></span>
				        <span><?php echo $pageNav->orderDownIcon($i, count($rows), true, 'orderdownbasemaplayer', 'Move Down', $ordering ); ?></span>
					<?php		
					}
					else{		
					?>
						 <span><?php echo $pageNav->orderUpIcon($i, true, 'orderdownbasemaplayer', 'Move Down', $ordering ); ?></span>
	 		             <span><?php echo $pageNav->orderDownIcon($i, count($rows), true, 'orderupbasemaplayer', 'Move Up', $ordering); ?></span>
					<?php
					}
					$disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
					</td>
				</tr>
				<?php
				$k = 1 - $k;
				$i++;
			}
		
			?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="9"><?php echo $pageNav->getListFooter(); ?></td>
				</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
		<input type="hidden" name="task" value="baseLayer" /> 
		<input type="hidden" name="overviewLayerId" id="overviewLayerId"/> 
		<input type="hidden" name="overviewLayerMode" id="overviewLayerMode"/> 
		<input type="hidden" name="boxchecked" value="0" /> 
		<input type="hidden" name="hidemainmenu" value="0">
		<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  	</form>
		<?php
	}

	function editBaseLayer( $baseLayer,$createUser, $updateUser,$fieldsLength, $option )
	{
		global  $mainframe;
		$jsLoader =JSLOADER_UTIL::getInstance();
		
		JHTML::script('jquery-1.3.2.min.js', $jsLoader->getPath("map","jquery" ));//'components/com_easysdi_map/externals/jquery/');
		if ($baseLayer->id != 0){
			JToolBarHelper::title( JText::_("MAP_BASELAYER_EDIT").': <small><small>['. JText::_("CORE_EDIT").']</small></small>', 'addedit.png' );
		}else{
			JToolBarHelper::title( JText::_("MAP_BASELAYER_EDIT").': <small><small>['. JText::_("CORE_NEW").']</small></small>', 'addedit.png' );
		}
		?>
		<script>
		var $j = jQuery.noConflict();
		$j(document).ready(function() {
			!$j("input[name=resolutionoverscale]:radio").change(
					function(e){
						if (e.target.value==0)
						{
							  $j(".scales").removeAttr('disabled'); 
							  $j(".resolutions").attr("disabled","disabled");
						}
						else if (e.target.value==1)  {
							$j(".resolutions").removeAttr('disabled');
							$j(".scales").attr("disabled","disabled");
						}
						}
					);
			!$j('.servicetype').change(
					function(e){
						if(e.target.value == "WMS"){
							$j(".WMS").removeAttr('style');
							$j(".WMTS").removeAttr('style');
							$j('.WMS').attr("style","display:block");
							$j('.WMTS').attr("style","display:none");

							var select = document.getElementById('version');
							select.options.length = 0; // clear out existing items
							select.options.add(new Option("1.1.0", "1.1.0"));
							select.options.add(new Option("1.1.1", "1.1.1"));
							select.options.add(new Option("1.3.0", "1.3.0"));
						}
						else if (e.target.value =="WMTS"){
							$j(".WMTS").removeAttr('style');
							$j(".WMS").removeAttr('style');
							$j('.WMTS').attr("style","display:block");
							$j('.WMS').attr("style","display:none");

							var select = document.getElementById('version');
							select.options.length = 0; // clear out existing items
							select.options.add(new Option("1.0.0", "1.0.0"));
						}
					}
				);
		});
		function submitbutton(pressbutton){
			if(pressbutton == "saveBaseLayer"){
				if (document.getElementById('type').value == "WMS"){
					document.getElementById('matrixset').value = null;
					document.getElementById('matrixids').value = null;
					document.getElementById('style').value = null;
				}else{
					document.getElementById('cache').checked = 0;
					document.getElementById('singletile').checked = 0;
					document.getElementById('customstyle').checked = 0;
					
				}
				if (document.getElementById('name').value == ""){
					alert ('<?php echo  JText::_( 'MAP_BASE_CT_NAME_VALIDATION_ERROR');?>');	
					return;
				}else if (document.getElementById('url').value == ""){
					alert ('<?php echo  JText::_( 'MAP_BASE_CT_URL_VALIDATION_ERROR');?>');	
					return;
				}else if (document.getElementById('layers').value == ""){
					alert ('<?php echo  JText::_( 'MAP_BASE_CT_LAYER_VALIDATION_ERROR');?>');	
					return;
				}else{	
					submitform(pressbutton);
				}
			}else{
				submitform(pressbutton);
			}
		}
		</script>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
		<table>
		<tr>
			<td>
			<fieldset><legend><?php echo JText::_("MAP_BASELAYER_GENERAL"); ?></legend>
		
				<table class="admintable" >
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASELAYER_NAME"); ?></td>
						<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['name'];?>" name="name" id="name" value="<?php echo stripslashes($baseLayer->name); ?>" /></td>
					</tr>
						<tr>
							<td class="key"><?php echo JText::_("MAP_BASELAYER_ISOVERVIEW"); ?> : </td>
							<td colspan="2"><?php echo JHTML::_('select.booleanlist', 'isoverviewlayer', '',  $baseLayer->isoverviewlayer); ?> </td>																
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("CORE_PUBLISHED"); ?> : </td>
							<td colspan="2"><?php echo JHTML::_('select.booleanlist', 'published', '',  $baseLayer->published); ?> </td>																
						</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASELAYER_DESCRIPTION"); ?></td>
						<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['description'];?>" name="description" id="description" value="<?php echo stripslashes($baseLayer->description); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASELAYER_URL"); ?></td>
						<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['url'];?>" name="url" id="url" value="<?php echo $baseLayer->url; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASELAYER_TYPE_VERSION"); ?></td>
						<td align="left">
							<select class="inputbox servicetype" name="type" id="type">
								<option <?php if($baseLayer->type == 'WMS') echo "selected" ; ?> value="WMS"><?php echo "WMS"; ?></option>
								<option <?php if($baseLayer->type == 'WMTS') echo "selected" ; ?> value="WMTS"><?php echo "WMTS"; ?></option>
							</select>
						</td>
						<td align="left" >
							<select class="inputbox" id="version" name="version"  >
							 <?php if($baseLayer->type != 'WMTS'){
							 	?>
							 	<option <?php if($baseLayer->version == '1.1.0') echo "selected" ; ?> value="1.1.0"><?php echo "1.1.0"; ?></option>
								<option <?php if($baseLayer->version == '1.1.1') echo "selected" ; ?> value="1.1.1"><?php echo "1.1.1"; ?></option>
								<option <?php if($baseLayer->version == '1.3.0') echo "selected" ; ?> value="1.3.0"><?php echo "1.3.0"; ?></option>
							 	<?php 
							 }else{
							 	?>
							 	<option <?php if($baseLayer->version == '1.0.0') echo "selected" ; ?> value="1.0.0"><?php echo "1.0.0"; ?></option>
							 	<?php
							 }
							 ?>
								
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASELAYER_LAYERS"); ?></td>
						<td  colspan="2">
						<span class="editlinktip hasTip" title="<?php echo JText::_("MAP_OVERLAY_LAYERS_SEPARATOR"); ?>">
							<input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['layers'];?>" name="layers" id="layers" value="<?php echo $baseLayer->layers; ?>" />
						</span>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASELAYER_IMG_FORMAT"); ?></td>
						<td  colspan="2">
						<span class="editlinktip hasTip" title="ex : image/png">
							<input class="inputbox" name="imgformat" id="imgformat" type="text" size="100" maxlength="<?php echo $fieldsLength['imgformat'];?>" value="<?php echo $baseLayer->imgformat; ?>" />
						</span>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASELAYER_VISIBILITY"); ?></td>
						<td colspan="2"><input class="checkbox" name="defaultvisibility" value="1" type="checkbox" <?php if ($baseLayer->defaultvisibility == 1) echo "checked=\"checked\""; ?> /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASELAYER_OPACITY"); ?></td>
						<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['defaultopacity'];?>" name="defaultopacity" id="defaultopacity" value="<?php echo $baseLayer->defaultopacity; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASELAYER_METADATA"); ?></td>
						<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['metadataurl'];?>" name="metadataurl" id="metadataurl" value="<?php echo $baseLayer->metadataurl; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASELAYER_PROJECTION"); ?></td>
						<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['projection'];?>" name="projection" id="projection" value="<?php echo $baseLayer->projection; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASELAYER_UNIT"); ?></td>
						<td colspan="2"><select class="inputbox" name="unit">
							<option <?php if($baseLayer->unit == 'm') echo "selected" ; ?> value="m"><?php echo JText::_("MAP_METERS"); ?></option>
							<option <?php if($baseLayer->unit == 'degrees') echo "selected" ; ?> value="degrees"><?php echo JText::_("MAP_DEGREES"); ?></option>
						</select></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASELAYER_MAXEXTENT"); ?></td>
						<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['maxextent'];?>" name="maxextent" id="maxextent" value="<?php echo $baseLayer->maxextent; ?>" /></td>
					</tr>
					<tr>
						<td colspan="3"><input type="radio" id="resolutionoverscale" name="resolutionoverscale" value="0" <?php if ($baseLayer->resolutionoverscale == 0) echo "checked=\"checked\""; ?> /> <?php echo JText::_("MAP_BASE_SCALES"); ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASE_MIN_SCALE"); ?></td>
						<td colspan="2" ><input class="inputbox scales" type="text" size="100" maxlength="<?php echo $fieldsLength['minscale'];?>" name="minscale" id="minscale" <?php if ($baseLayer->resolutionoverscale == 1) echo 'disabled' ?> value="<?php echo $baseLayer->minscale; ?>" /></td>
					</tr>
					<tr class="scales">
						<td class="key"><?php echo JText::_("MAP_BASE_MAX_SCALE"); ?></td>
						<td colspan="2"><input class="inputbox scales" name="maxscale" id="maxscale" type="text" size="100" maxlength="<?php echo $fieldsLength['maxscale'];?>" <?php if ($baseLayer->resolutionoverscale == 1) echo 'disabled' ?> value="<?php echo $baseLayer->maxscale; ?>" /></td>
					</tr>
					<tr>
						<td colspan="3"><input type="radio" id="resolutionoverscale1" name="resolutionoverscale" value="1" <?php if ($baseLayer->resolutionoverscale == 1) echo "checked=\"checked\""; ?> /> <?php echo JText::_("MAP_BASE_RESOLUTIONS"); ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_( 'MAP_BASE_MIN_RESOLUTION' ); ?></td>
						<td colspan="2"><input class="inputbox resolutions" type="text" size="100" maxlength="<?php echo $fieldsLength['minresolution'];?>" name="minresolution" <?php if ($baseLayer->resolutionoverscale == 0) echo 'disabled' ?> value="<?php echo $baseLayer->minresolution; ?>"  /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_( 'MAP_BASE_MAX_RESOLUTION' ); ?></td>
						<td colspan="2"><input class="inputbox resolutions" type="text" size="100" maxlength="<?php echo $fieldsLength['maxresolution'];?>"  name="maxresolution" <?php if ($baseLayer->resolutionoverscale == 0) echo 'disabled' ?> value="<?php echo $baseLayer->maxresolution; ?>"  /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("MAP_BASE_RESOLUTIONS"); ?></td>
						<td colspan="2" style="">
							<textarea id="resolutions" class="textarea resolutions" style="height: 100px; width: 400px;" name="resolutions" <?php if ($baseLayer->resolutionoverscale == 0) echo 'disabled' ?>><?php echo $baseLayer->resolutions; ?></textarea>
						</td>
					</tr>
				</table>
				
		</fieldset>
	</td>
	<td valign="top">
		<fieldset class="WMS" <?php if($baseLayer->type != 'WMTS') echo 'style="display:block"' ; else  echo 'style="display:none"'; ?>><legend><?php echo JText::_("MAP_BASELAYER_SPECIFIC_WMS"); ?></legend>
			<table class="admintable">
				<tr>
					<td class="key"><?php echo JText::_("MAP_BASELAYER_CUSTOM_STYLE_ENABLED"); ?></td>
					<td colspan="2"><input class="checkbox" id="customstyle" name="customstyle" value="1" type="checkbox" <?php if ($baseLayer->customstyle == 1) echo "checked=\"checked\""; ?> /></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("MAP_BASE_CACHE"); ?></td>
					<td colspan="2"><input class="checkbox" id="cache" name="cache" value="1" type="checkbox" <?php if ($baseLayer->cache == 1) echo "checked=\"checked\""; ?> /></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("MAP_BASELAYER_TILE"); ?></td>
					<td colspan="2"><input class="checkbox" id="singletile" name="singletile" value="1" type="checkbox" <?php if ($baseLayer->singletile == 1) echo "checked=\"checked\""; ?> /></td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="WMTS" <?php if($baseLayer->type == 'WMTS') echo 'style="display:block"' ; else  echo 'style="display:none"'; ?>><legend><?php echo JText::_("MAP_BASELAYER_SPECIFIC_WMTS"); ?></legend>
			<table class="admintable">
				<tr>
					<td class="key"><?php echo JText::_("MAP_BASELAYER_MATRIX_SET"); ?></td>
					<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['matrixset'];?>" name="matrixset" id="matrixset" value="<?php echo $baseLayer->matrixset; ?>" /></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("MAP_BASELAYER_MATRIX_IDS"); ?></td>
					<td colspan="2">
					<span class="editlinktip hasTip" title="<?php echo JText::_("MAP_MATRIX_IDS_TOOLTIP"); ?>">
					<input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['matrixids'];?>" name="matrixids" id="matrixids" value="<?php echo $baseLayer->matrixids; ?>" />
					</span>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("MAP_BASELAYER_STYLE"); ?></td>
					<td colspan="2">
					<span class="editlinktip hasTip" title="<?php echo JText::_("MAP_BASELAYER_STYLE_TOOLTIP"); ?>">
					<input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['style'];?>" name="style" id="style" value="<?php echo $baseLayer->style; ?>" />
					</span>
					</td>
				</tr>
			</table>
		</fieldset>
	</td>
</tr>
</table>
		
		
		<br></br>
		<table border="0" cellpadding="3" cellspacing="0">
		<?php
		if ($baseLayer->created)
		{ 
		?>
			<tr>
				<td><?php echo JText::_("CORE_CREATED"); ?> : </td>
				<td><?php if ($baseLayer->created) {echo date('d.m.Y h:i:s',strtotime($baseLayer->created));} ?></td>
				<td>, </td>
				<td><?php echo $createUser; ?></td>
			</tr>
		<?php
		}
		if ($baseLayer->updated and $baseLayer->updated<> '0000-00-00 00:00:00')
		{ 
		?>
			<tr>
				<td><?php echo JText::_("CORE_UPDATED"); ?> : </td>
				<td><?php if ($baseLayer->updated and $baseLayer->updated<> 0) {echo date('d.m.Y h:i:s',strtotime($baseLayer->updated));} ?></td>
				<td>, </td>
				<td><?php echo $updateUser; ?></td>
			</tr>
		<?php
		}
		?>		
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="editBaseLayer" /> 
		<input type="hidden" name="id" value="<?php echo $baseLayer->id; ?>" />
		<input type="hidden" name="guid" value="<?php echo $baseLayer->guid?>" />
		<input type="hidden" name="ordering" value="<?php echo $baseLayer->ordering; ?>" />
		<input type="hidden" name="created" value="<?php echo $baseLayer->created;?>" />
		<input type="hidden" name="createdby" value="<?php echo $baseLayer->createdby; ?>" /> 
		<input type="hidden" name="updated" value="<?php echo $baseLayer->created; ?>" />
		<input type="hidden" name="updatedby" value="<?php echo $baseLayer->createdby; ?>" /> 
		</form>
		<?php
	}
	
	function getOverviewSetting( &$row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{
		$img 	= $row->isoverviewlayer ? $imgY : $imgX;
		$task 	= $row->isoverviewlayer ? 'unsetoverview' : 'setoverview';
		$alt 	= $row->isoverviewlayer ? JText::_( 'MAP_OVERVIEWSET' ) : JText::_( 'MAP_OVERVIEWUNSET' );
		$action = $row->isoverviewlayer ? JText::_( 'MAP_UNSETOVERVIEW' ) : JText::_( 'MAP_SETOVERVIEW' );

		$href = '
		<a href="javascript:void(0);" onclick="return baselayer_overview('. $row->id .',\''.$task .'\')" title="'. $action .'">
		<img src="images/'. $img .'" border="0" alt="'. $alt .'" /></a>'
		;

		return $href;
	}
	
	
}
?>