<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2009 Antoine Elbel & R�my Baud (aelbel@solnet.ch remy.baud@asitvd.ch)
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

class HTML_site {
	
	function gettingStarted($featureSources, $layers, $pageNav, $userRights, $wpsPublish, $remainingLayers, $maxLayerForUser, $user_diffusor_url){
		global  $mainframe;
		$db =& JFactory::getDBO();
		$index = JRequest::getVar('tabIndex',0);
		if(!$userRights["GEOSERVICE_DATA_MANA"] || !$userRights["GEOSERVICE_MANAGER"])
			$index = 0;	
		$tabs =& JPANE::getInstance('Tabs', array('startOffset' => $index));
		$base_url = substr($_SERVER['PHP_SELF'], 0, -9);
		JHTML::script('mainapp.js', 'components/com_easysdi_publish/js/');
		JHTML::script('htmlEncode.js', 'components/com_easysdi_publish/js/');
		if($index != 2){
			JHTML::script('ext-base.js', 'components/com_easysdi_publish/js/extuploader/');
			JHTML::script('ext-all.js', 'components/com_easysdi_publish/js/extuploader/');
		}
		?>
		
		<div id="page">
		  <h2 class="contentheading"><?php echo JText::_("EASYSDI_PUBLISH_PUBLISH_TITLE"); ?></h2>
			<div class="contentin">
			<div class="publish">	
			<form action="index.php" method="get" name="adminForm" id="adminForm" class="adminForm">
			<input type="hidden" name="option" id="option" value="<?php echo JRequest::getVar('option' );?>" />
			<input type="hidden" name="task" id="task" value="<?php echo JRequest::getVar('task' );?>" />
			<input type="hidden" name="tabIndex" id="tabIndex" value="<?php echo JRequest::getVar('tabIndex' );?>" />
			<input type="hidden" name="wpsPublish" id="wpsPublish" value="<?php echo $wpsPublish;?>" />
			<input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo $base_url; ?>" />
			<input type="hidden" name="featureSourceGuid" id="featureSourceGuid" value="" />
			<input type="hidden" name="layerGuid" id="layerGuid" value="" />
			<input type='hidden'  name='limitstart' value="">

			
			<!-- Help -->
			<table width="100%">
						<tr>
							<td>&nbsp;</td>
							<td align="right"><a target="_blank" href="http://www.dailymotion.com/video/xa44fk_easysdi-publish-demo-eng_tech"><?php echo JText::_("EASYSDI_PUBLISH_HELP"); ?></a></td>
						</tr>
			</table>
		<?php
		   //show only if joomla user has the rights
		echo $tabs->startPane("publishPane");
		if($userRights['GEOSERVICE_DATA_MANA'])
		{
			echo $tabs->startPanel(JText::_("EASYSDI_PUBLISH_FIND_DATA"),"findDataPane");
			
			echo "<!-- start featuresource section -->";
			
			
			
			
			
			if($index == 0){
			echo "<br/>";
			$param = array('size'=>array('x'=>800,'y'=>800) );
			JHTML::_("behavior.modal","a.modal",$param);
					
		
			//Get the fs status from the server and update the featuresource object
			//
			$wpsConfig = $wpsPublish."/services/config";
			$fsList = "";
			$fsInProgress = Array();
			
			foreach ($featureSources as $row)
							$fsList .= $row->featureGUID.",";
			$url = $wpsConfig."?operation=ListFeatureSources&list=".$fsList;
  			$doc = SITE_proxy::fetch($url, false);
  			$xml = simplexml_load_string($doc);
			//$xml = simplexml_load_file($url,'SimpleXMLElement', LIBXML_NOCDATA);
			//echo "<pre>";  print_r($xml);  echo "</pre>";

			$i=0;
			foreach ($featureSources as $row){
				$srvFs = $xml->xpath("//featuresource[@guid='$row->featureGUID']");
				if(count($srvFs) > 0){
					$srvFs = $srvFs[0];
					//echo "<pre>";  print_r($srvFs);  echo "</pre>";
					$status = (string)$srvFs->status;
					$featureSources[$i]->status = $status;
					$featureSources[$i]->excmessage = (string)$srvFs->excmessage;
	  			$featureSources[$i]->excdetail = (string)$srvFs->excdetail;
				}else{
					$featureSources[$i]->status = "OUT_OF_SYNC";
				}
				$i++;
			}
			//echo "<pre>";  print_r($featureSources[8]);  echo "</pre>";
			?>	
		
			<script type="text/javascript">
	        	
			function paginateOnChange(button, tabIndex){
				$('tabIndex').value = tabIndex;
				submitbutton(button);
			}
			
			</script>
		
		<table width="100%">
			<tr>
				<td width="100%">
					<table width="100%">
						<tr>
							<td align="left"><span><?php echo JText::_("EASYSDI_PUBLISH_FIND_DATA_TITLE");?></span></td>
						        <td align="right" width="30px">
								<img id="loadingImg" width="25px" height="25px" src="components/com_easysdi_publish/img/loading.gif"></img>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			
			<tr>
				<td>
						<fieldset>
							<legend><?php echo JText::_("EASYSDI_PUBLISH_FEATURESOURCE_LIST"); ?></legend>
							<table width="100%">
								<tr>
									<td>&nbsp;</td>
									<td align="right"><a href="index.php?option=com_easysdi_publish&task=createFeatureSource"><?php echo JText::_("EASYSDI_PUBLISH_CREATE_FEATURESOURCE"); ?></a></td>
								</tr>
							</table>
							<br/>
							<table class="box-table" width="100%" id="featureListTable">
								<thead>
									<tr>
										<th class="title" align="left"><?php echo JText::_("EASYSDI_PUBLISH_TEXT_NAME"); ?></th>
										<th class="descr" align="center"><?php echo JText::_("EASYSDI_PUBLISH_TEXT_STATUS"); ?></th>
										<th class="date" align="center"><?php echo JText::_("EASYSDI_TEXT_CREATION_DATE"); ?></th>
										<th class="date" align="center"><?php echo JText::_("EASYSDI_TEXT_UPDATE_DATE"); ?></th>
										<th class="logo" align="center">&nbsp;</th>
										<th class="logo" align="center">&nbsp;</th>
										<th class="logo" align="center">&nbsp;</th>
									</tr>
								</thead>
								<tbody>
								<?php
								$k = 0;
								$i=0;
								foreach ($featureSources as $row)
								{
										$db->setQuery( "SELECT count(*) FROM #__sdi_publish_featuresource f, #__sdi_publish_layer l where l.featuresourceId=f.id AND f.id=".$row->id);
										$isDeletable = $db->loadResult();
									
								?>
									<tr class="<?php echo "row$k"; ?>">
										<td align="left"><?php echo $row->name; ?></td>
										<?php if($row->status == "AVAILABLE"){?>
										  <?php if($row->excmessage == "null"){?>
											   <td align="center"><div id="<?php echo "st".$row->featureGUID; ?>"><?php echo $row->status; ?></div></td>
											<?php }else{ ?>
											   <td align="center"><a id="<?php echo "st".$row->featureGUID; ?>" title="<?php echo JText::_('EASYSDI_PUBLISH_VIEW_FS_STATUS_DETAILS'); ?>" class="modal" href="./index.php?tmpl=component&option=com_easysdi_publish&task=showFsStats&guid=<?php echo $row->featureGUID;?>" rel="{handler:'iframe',size:{x:650,y:350}}"><?php echo $row->status; ?></a>(Update failed)</td>											
											<?php } ?>
										<?php }else if($row->status == "OUT_OF_SYNC"){ ?>
										  <td align="center"><div id="<?php echo "st".$row->featureGUID; ?>"><?php echo JText::_('EASYSDI_PUBLISH_FS_OUT_OF_SYNC'); ?></div></td>
										<?php }else{ ?>
											<td align="center"><a id="<?php echo "st".$row->featureGUID; ?>" title="<?php echo JText::_('EASYSDI_PUBLISH_VIEW_FS_STATUS_DETAILS'); ?>" class="modal" href="./index.php?tmpl=component&option=com_easysdi_publish&task=showFsStats&guid=<?php echo $row->featureGUID;?>" rel="{handler:'iframe',size:{x:650,y:350}}"><?php echo $row->status; ?></a></td>
										<?php } ?>
										<td align="center"><?php echo date("d-m-Y", strtotime($row->creation_date)); ?></td>
										<td align="center"><?php echo date("d-m-Y", strtotime($row->update_date)); ?></td>
										<td class="logo"><div title="<?php echo JText::_('EASYSDI_PUBLISH_EDIT_FS'); ?>" class="edit" onClick="window.open('index.php?option=com_easysdi_publish&task=editFeatureSource&featureSource=<?php echo $row->id; ?>', '_main');"/></td>
										<?php if(!$isDeletable){?>
											  <td class="logo"><div title="<?php echo JText::_('EASYSDI_PUBLISH_REMOVE_FS'); ?>" class="delete" onClick="return deleteFs_click('<?php echo $row->featureGUID; ?>')"/></td>
										<?php }else{ ?>
											  <td class="logo"><div title="<?php echo JText::_('EASYSDI_PUBLISH_REMOVE_FS'); ?>" class="deleteDis" onClick="alert('<?php echo JText::_("EASYSDI_PUBLISH_ERROR_REFERENCE_LAYER"); ?>')"/></td>
										<?php } ?>
									</tr>
<?php
									$k = 1 - $k;
									$i++;
								}
?>
								</tbody>
							</table>
							<br/>
							<table width="100%">
									<tr>
										<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
										<td align="center"><?php echo JText::_("EASYSDI_PUBLISH_TEXT_DISPLAY"); ?>
											<?php echo $pageNav->getLimitBox(); ?>
										</td>
										<td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
									</tr>
							</table>

						</fieldset>
				</td>
			</tr>
		</table>

<?php
echo "<!-- end featuresource section -->";
				}
					echo $tabs->endPanel();
				}
				if($userRights['GEOSERVICE_MANAGER'])
				{
					echo $tabs->startPanel(JText::_("EASYSDI_PUBLISH_BUILD_LAYERS"),"publishLayersPane");
					echo "<!-- start layer section -->";
					if($index == 1){
					echo "<br/>";
					
?>

			<input type="hidden" name="wmsUrl" id="wmsUrl" value="" />
			<input type="hidden" name="wfsUrl" id="wfsUrl" value="" />
			<input type="hidden" name="kmlUrl" id="kmlUrl" value="" />
			<input type="hidden" name="minx" id="minx" value="" />
			<input type="hidden" name="miny" id="miny" value="" />
			<input type="hidden" name="maxx" id="maxx" value="" />
			<input type="hidden" name="maxy" id="maxy" value="" />
			<input type="hidden" name="copyLayer" id="copyLayer" value="0"/>
			<input type="hidden" name="layerIdToCopy" id="layerIdToCopy" value="0"/>
			<input type="hidden" name="layerCopyName" id="layerCopyName" value=""/>

			
			

		
		<table width="100%">
			<tr>
				<td width="100%">
					<table width="100%">
						<tr>
							<td align="left"><span><?php echo JText::_("EASYSDI_PUBLISH_BUILD_LAYERS_TITLE");?></span></td>
							<td align="right" width="30px">
								<img id="loadingImg" width="25px" height="25px" src="components/com_easysdi_publish/img/loading.gif"></img>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
						<fieldset>
							<legend><?php echo JText::_("EASYSDI_PUBLISH_LAYER_LIST"); ?></legend>
							<table width="100%">
								<tr>
									<td align="left"><?php
										$text = $remainingLayers > 1 ? "EASYSDI_PUBLISH_REMAINING_LAYERS" : "EASYSDI_PUBLISH_REMAINING_LAYER";
										echo JText::_($text).$remainingLayers."/".$maxLayerForUser;
										?>
										</td>
		  					</tr>
								<tr>
									<td>&nbsp;</td>
									<td align="right"><?php if($remainingLayers > 0){ ?><a href="index.php?option=com_easysdi_publish&task=createLayer"><?php echo JText::_("EASYSDI_PUBLISH_CREATE_LAYER"); ?></a><?php } ?></td>
								</tr>
							</table>
							<br/>
							<table class="box-table" width="100%" id="layerListTable">
								<thead>
									<tr>
										<th class="titlel" align="left"><?php echo JText::_("EASYSDI_PUBLISH_TEXT_NAME"); ?></th>
										<th class="titlel" align="left"><?php echo JText::_("EASYSDI_PUBLISH_FEATURESOURCE_NAME"); ?></th>
										<th class="descr" align="center"><?php echo JText::_("EASYSDI_TEXT_CREATION_DATE"); ?></th>
										<th class="descr" align="center"><?php echo JText::_("EASYSDI_TEXT_UPDATE_DATE"); ?></th>
										<th class="logo" align="center">&nbsp;</th>
										<th class="logo" align="center">&nbsp;</th>
									</tr>
								</thead>
								<tbody>
<?php
								$k = 0;
								$i=0;
								foreach ($layers as $row)
								{
									$db->setQuery( "SELECT f.name FROM #__sdi_publish_featuresource f, #__sdi_publish_layer l where f.id=l.featuresourceId AND l.id=".$row->id);
									$fsName = $db->loadResult();
?>
									<tr class="<?php echo "row$k"; ?>">
										<td align="left"><?php echo $row->name; ?></td>
										<td align="left"><?php echo $fsName; ?></td>
										<td align="center"><?php echo date("d-m-Y", strtotime($row->creation_date)); ?></td>
										<td align="center"><?php echo date("d-m-Y", strtotime($row->update_date)); ?></td>
										<td class="logo"><div title="<?php echo JText::_('EASYSDI_PUBLISH_EDIT_LAYER'); ?>" class="edit" onClick="window.open('index.php?option=com_easysdi_publish&task=editLayer&id=<?php echo $row->id; ?>', '_main');"/></td>
									        <td class="logo"><div title="<?php echo JText::_('EASYSDI_PUBLISH_REMOVE_LAYER'); ?>" class="delete" onClick="return deleteLayer_click('<?php echo $row->layerGuid; ?>');"/></td>
									        <td class="logo"><div title="<?php echo JText::_('EASYSDI_PUBLISH_COPY_LAYER'); ?>" class="copy" onClick="return copyLayer_click(<?php echo "'".JText::_('EASYSDI_PUBLISH_COPY_LAYER_PROMPT')."','".$row->layerGuid."',".$row->id; ?>);"/></td>
									</tr>
<?php
									$k = 1 - $k;
									$i++;
								}
?>
							</tbody>
							</table>
							<br/>
							<table width="100%">
									<tr>
										<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
										<td align="center"><?php echo JText::_("EASYSDI_PUBLISH_TEXT_DISPLAY"); ?>
											<?php echo $pageNav->getLimitBox(); ?>
										</td>
										<td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
									</tr>
							</table>
						</fieldset>
				</td>
			</tr>
			</table>

<?php			
					}
					echo "<!-- stop layer section -->";
					echo $tabs->endPanel();
				}
				if($userRights['GEOSERVICE_MANAGER'] || $userRights['GEOSERVICE_DATA_MANA'])
				{
					echo $tabs->startPanel(JText::_("EASYSDI_PUBLISH_VIEW_LAYERS"),"viewLayersPane");
					echo "<br/>";
					
					//only load the ressources if we displaye the "View layers"
					if($index == 2){
					
					//load css files
        
        	JHTML::stylesheet('ext-all.css', 'components/com_easysdi_publish/js/styler/externals/ext/resources/css/');
					JHTML::stylesheet('color-picker.ux.css', 'components/com_easysdi_publish/js/styler/externals/ux/colorpicker/');
					//JHTML::stylesheet('xtheme-gray.css', 'components/com_easysdi_publish/js/styler/externals/ext/resources/css/');
					//JHTML::stylesheet('style.css', 'components/com_easysdi_publish/js/styler/externals/openlayers/theme/default/');
					JHTML::stylesheet('styler.css', 'components/com_easysdi_publish/js/styler/theme/css/');
 
	        JHTML::script('OpenLayers.js', 'components/com_easysdi_publish/js/styler/script/');
					JHTML::script('ext-base.js', 'components/com_easysdi_publish/js/styler/externals/ext/adapter/ext/');
					JHTML::script('ext-all.js', 'components/com_easysdi_publish/js/styler/externals/ext/');
					JHTML::script('GeoExt.js', 'components/com_easysdi_publish/js/styler/script/');
					JHTML::script('gxp.js', 'components/com_easysdi_publish/js/styler/script/');
					
					//uncompressed
					//Take care about dependencies when your do this!
					//Look in which order jsbuild export dependencies! (jsbuild -v -o script build_s.cfg)
					//and write the same order here
					
					
					JHTML::script('Styler.js', 'components/com_easysdi_publish/js/styler/lib/');
					JHTML::script('ColorManager.js', 'components/com_easysdi_publish/js/styler/lib/Styler/');
					JHTML::script('dispatch.js', 'components/com_easysdi_publish/js/styler/lib/Styler/');
					JHTML::script('SchemaManager.js', 'components/com_easysdi_publish/js/styler/lib/Styler/');
					JHTML::script('olx.js', 'components/com_easysdi_publish/js/styler/lib/');
					JHTML::script('SLDManager.js', 'components/com_easysdi_publish/js/styler/lib/Styler/');
					
					

					//compressed
		//			JHTML::script('Styler.js', 'components/com_easysdi_publish/js/styler/script/');					
				  
	                          	//User extensions
					JHTML::script('color-picker.ux.js', 'components/com_easysdi_publish/js/styler/externals/ux/colorpicker/');
					JHTML::script('ScaleLine.js', 'components/com_easysdi_publish/js/styler/externals/openlayers/Control/');

					//language
					JHTML::script('language.js', 'components/com_easysdi_publish/js/');

/*				
					JHTML::stylesheet('ext-all.css', 'components/com_easysdi_publish/js/styler/externals/ext/resources/css/');
					JHTML::stylesheet('color-picker.ux.css', 'components/com_easysdi_publish/js/styler/externals/ux/colorpicker/');
					JHTML::stylesheet('xtheme-gray.css', 'components/com_easysdi_publish/js/styler/externals/ext/resources/css/');
					JHTML::stylesheet('style.css', 'components/com_easysdi_publish/js/styler/externals/openlayers/theme/default/');
					JHTML::stylesheet('styler.css', 'components/com_easysdi_publish/js/styler/theme/css/');
					
					//load required js files
					//We do have it, but another version
					JHTML::script('OpenLayers.js', 'components/com_easysdi_publish/js/styler/script/');
					JHTML::script('ext-base-debug.js', 'components/com_easysdi_publish/js/styler/externals/ext/adapter/ext/');
					JHTML::script('ext-all-debug.js', 'components/com_easysdi_publish/js/styler/externals/ext/');
					
					JHTML::script('GeoExt.js', 'components/com_easysdi_publish/js/styler/script/');

					
					JHTML::script('gxp.js', 'components/com_easysdi_publish/js/styler/script/');

					//Compressed on for production
					//JHTML::script('Styler.js', 'components/com_easysdi_publish/js/styler/script/');					
					//Only for dev
					JHTML::script('Styler.js', 'components/com_easysdi_publish/js/styler/lib/');
					JHTML::script('ColorManager.js', 'components/com_easysdi_publish/js/styler/lib/Styler/');
					JHTML::script('dispatch.js', 'components/com_easysdi_publish/js/styler/lib/Styler/');
					JHTML::script('SchemaManager.js', 'components/com_easysdi_publish/js/styler/lib/Styler/');
					JHTML::script('SLDManager.js', 'components/com_easysdi_publish/js/styler/lib/Styler/');
					JHTML::script('Util.js', 'components/com_easysdi_publish/js/styler/lib/Styler/');
					JHTML::script('MultiSlider.js', 'components/com_easysdi_publish/js/styler/lib/Styler/widgets/');
					JHTML::script('RulePanel.js', 'components/com_easysdi_publish/js/styler/lib/Styler/widgets/');
					JHTML::script('ScaleLimitPanel.js', 'components/com_easysdi_publish/js/styler/lib/Styler/widgets/');
					JHTML::script('MultiSliderTip.js', 'components/com_easysdi_publish/js/styler/lib/Styler/widgets/tips/');
					//JHTML::script('LegendPanel.js', 'components/com_easysdi_publish/js/styler/lib/Styler/widgets/');

*/
					//get the layer list for the user
					$joomlaUser = JFactory::getUser();
					$results = array();					
					$db->setQuery("SELECT l.id, l.wmsUrl, l.wfsUrl, l.kmlUrl, l.bbox, l.title FROM #__sdi_publish_layer l, #__sdi_account p where p.id=l.partner_id AND p.user_id=".$joomlaUser->id);	
					$results = $db->loadObjectList();					
					$layers = array();
					foreach($results as $result){
						$temp = array();
						foreach($result as $k => $v)
							$temp[$k] = $v == null ? "" : $v;
						//add full qualified name
						$tmp = explode("=", $result->wmsUrl);
						$temp['fullname']=$tmp[count($tmp)-1];
						$layers[$result->id] = $temp;
					}
					$jsArray = new array_to_js();
					$jsArray->add_array($layers, 'layersList');
					
					//select layer
					$layerList = array();
					$db->setQuery( "SELECT l.id AS value, l.name as text FROM #__sdi_publish_layer l, #__sdi_account p where p.id=l.partner_id AND p.user_id=".$joomlaUser->id);
					$layerList = $db->loadObjectList();
					$layerList [] = JHTML::_('select.option','0', JText::_("EASYSDI_PUBLISH_CHOOSE_LAYER"));
										
?>
		<script>
	
		var userLayersList = <?php echo $jsArray->output_all(false); ?>;
		//var gs_layer = new OpenLayers.Layer.TMS("Geopole Street Map","http://tms.geopole.org/",{type : "png",layername : "geopole_street",maxResolution : 0.703125});
		
    //Required by getFeatureControl
    //OpenLayers.ProxyHost= "/components/com_easysdi_publish/core/proxy.php?proxy_url=";
    
    
    var styler_proxy_url = 'index.php?option=com_easysdi_publish&task=proxy&proxy_url';
    OpenLayers.ProxyHost = styler_proxy_url+'=';
    var styler_host = '<?php echo $user_diffusor_url;?>';
    var styler_namespace = '<?php echo JFactory::getUser()->name;?>';
    
    Ext.BLANK_IMAGE_URL = "components/com_easysdi_publish/js/styler/externals/openlayers/theme/default/img/blank.gif";
    
    Ext.onReady(function() {
    	
    	//window.styler = new Styler({});
    	
	
        window.styler = new Styler({
            baseLayers: [
                new OpenLayers.Layer.OSM(
                    "Open Street Map",
                    "http://tile.openstreetmap.org/${z}/${x}/${y}.png",
                    {numZoomLevels: 19}),
                new OpenLayers.Layer.WMS(
			"OpenLayers WMS",
			"http://labs.metacarta.com/wms/vmap0",
			{layers: 'basic'})
            ]
        });
        
    });
	

		</script>
		<table width="100%">
			<tr>
				<td>
					<!-- layer choice -->
					<!--
					<fieldset class="endpointslist">
					<legend><?php echo JText::_("EASYSDI_PUBLISH_LAYER_LIST"); ?></legend>
						<table>
							<tr>
								<td align="left"><?php echo JHTML::_("select.genericlist",$layerList, 'layerList', 'size="1" class="inputbox"', 'value', 'text', '0'); ?></td>
							</tr>
						</table>
					</fieldset>
					-->
					<!-- Services end points -->
					<fieldset class="endpointslist">
					<legend><?php echo JText::_("EASYSDI_PUBLISH_ENDPOINT_LIST"); ?></legend>
						<table>
							<tr>
								<td align="left"><?php echo JText::_("EASYSDI_PUBLISH_WMS"); ?>:</td>
								<td align="left"><div id="wmsUrl" name="wmsUrl"></div></td>	
							</tr>
							<tr>
								<td align="left"><?php echo JText::_("EASYSDI_PUBLISH_WFS"); ?>:</td>
								<td align="left"><div id="wfsUrl" name="wfsUrl"></div></td>	
							</tr>
							<tr>
								<td align="left"><?php echo JText::_("EASYSDI_PUBLISH_KML"); ?>:</td>
								<td align="left"><div id="kmlUrl" name="kmlUrl"></div></td>	
							</tr>
						</table>
					</fieldset>
					<!-- Map and styling -->
					<table>
						
						<!--
						<tr>
							<td><?php echo JText::_("EASYSDI_PUBLISH_QUERY_FEATURE_HINT"); ?></td>
						</tr>
					 <tr>
					 	<td>
							<fieldset>
							<legend><?php echo JText::_("EASYSDI_PUBLISH_MAP"); ?></legend>
								<table>
									<div id="map" class="viewmap"></div>
									<div id="nodeList"/>
								</table>
							</fieldset>
						</td>
						-->
						
					</table>					
				</td>
			</tr>
		</table>	
				
				
<?php			
				}	
					echo $tabs->endPanel();
				}				
				
				echo $tabs->endPane();

?>

			</div>
		</div>
		</div>
	</form>
	<div id="mainApp"></div>
	<div class="publish">	
		<table style="width:480px;">
			<tr>
				<td align="right">
				   <div style="width:430px;" id="errorMsg" class="errorMsg">
				     <table>
				       <tr><td id="errorMsgCode"></td></tr>
				       <tr><td id="errorMsgDescr"></td></tr>
				     </table>
				   </div>
				</td>
			</tr>
		</table>
	</div>

		<?php
	}
	
	function showFsStats($fsRow){
		
		?>
		
			<div id="metadata" class="contentin">
				<div id="excReport">
				<h2 class="contentheading"><?php echo JText::_('EASYSDI_FEATURESOURCE_STATUS_TITLE').":&nbsp;".$fsRow->name; ?></h2>
					<table>
						<tr><td>&nbsp;</td></tr>
						<!-- feature source details -->
						<tr><td><?php echo JText::_('EASYSDI_PUBLISH_TEXT_STATUS');?>:</td><td><?php echo JText::_($fsRow->status); ?></td></tr>
						<tr><td><?php echo JText::_('EASYSDI_PUBLISH_FS_GUID');?>:</td><td><?php echo $fsRow->featureguid; ?></td></tr>
						<tr><td><?php echo JText::_('EASYSDI_PUBLISH_PROJECTION');?>:</td><td><?php echo $fsRow->projection; ?></td></tr>
						<tr><td><?php echo JText::_('EASYSDI_TEXT_CREATION_DATE');?>:</td><td><?php echo $fsRow->creation_date; ?></td></tr>
						<tr><td><?php echo JText::_('EASYSDI_TEXT_UPDATE_DATE');?>:</td><td><?php echo $fsRow->update_date; ?></td></tr>
					</table>
						<!-- exceptions if there are -->
					<?php if(strlen($fsRow->exccode) > 0 && $fsRow->exccode != "null"){?>
					<fieldset>
					   <script>
					   function toggle5(showHideDiv, switchImgTag) {
                                              var ele = document.getElementById(showHideDiv);
                                              var imageEle = document.getElementById(switchImgTag);
                                              if(ele.style.display == "block") {
                                              ele.style.display = "none";
                                              imageEle.innerHTML = '<img src="components/com_easysdi_publish/img/plus.png">';
                                              }
                                              else {
                                              ele.style.display = "block";
                                               imageEle.innerHTML = '<img src="components/com_easysdi_publish/img/minus.png">';
                                               }
                                           }
					   </script>
				           <legend><?php echo JText::_("EASYSDI_PUBLISH_WPS_TRANSFORMATION_SUM"); ?></legend>
                                              <div id="headerDivImg">
                                              <div id="titleTextImg"><?php echo trim($fsRow->excmessage); ?></div>
                                              <p>
					         <a href="javascript:toggle5('contentDivImg', 'imageDivLink');" id="imageDivLink">
					            <img src="components/com_easysdi_publish/img/plus.png">
					         </a>
                                              </p></div>
                                              <div style="display: none;" id="contentDivImg"><?php echo trim($fsRow->excstacktrace); ?></div>
					</fieldset>
					<?php } ?>
				</div>
			</div>
			<?php
	}
	
	function showFormatList(){
		
		?>
			<div id="metadata" class="contentin">
				<div id="excReport">
					<h2 class="contentheading"><?php echo JText::_('EASYSDI_PUBLISH_SUPPORTED_FORMATS'); ?></h2>
					<ul>						
						<li>ESRI Shapefile</li>
						<li>MapInfo File</li>
						<li>UK .NTF</li>
						<li>SDTS</li>  
						<li>TIGER</li>     
						<li>S57</li>    
						<li>DGN</li>      
						<li>VRT</li>      
						<li>REC</li>      
						<li>BNA</li>   
						<li>CSV</li>      
						<li>NAS</li>      
						<li>GML</li>      
						<li>GPX</li>      
						<li>KML</li>      
						<li>GeoJSON</li>      
						<li>Interlis 1</li>  
						<li>Interlis 2</li>
						<li>GMT</li>
						<li>ODBC</li>   
						<li>PGeo</li>     
						<li>OGDI</li>     
						<li>XPlane</li>    
						<li>AVCBin</li>   
						<li>AVCE00</li>   
						<li>DXF</li>   
						<li>Geoconcept</li>
						<li>GeoRSS</li>
						<li>GPSTrackMaker</li>
						<li>VFK</li>
				  </ul>
				</div>
			</div>
			<?php
	}
		
}

?>