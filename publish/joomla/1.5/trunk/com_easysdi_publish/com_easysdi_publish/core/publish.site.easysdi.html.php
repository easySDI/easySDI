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
			<input type='hidden'  name='limitstart' value="<?php echo  $limitstart; ?>">

			
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
					if($index == 0){
					echo "<br/>";
					$param = array('size'=>array('x'=>800,'y'=>800) );
					JHTML::_("behavior.modal","a.modal",$param);
					
					
		//the WPS address
		$db->setQuery("SELECT value FROM #__easysdi_config WHERE thekey='WPS_PUBLISHER'");
		$wpsAddress = $db->loadResult();
		
		
		//Get the fs status from the server and update the featuresource object
		//
		$wpsConfig = $wpsAddress."/config";
		$fsList = "";
		$fsInProgress = Array();
		
		foreach ($featureSources as $row)
						$fsList .= $row->featureGUID.",";
		$url = $wpsConfig."?operation=ListFeatureSources&list=".$fsList;
		$xml = simplexml_load_file($url);
		
		$i=0;
		foreach ($featureSources as $row){
			$srvFs = $xml->xpath("//featuresource[@guid='$row->featureGUID']");
			if(count($srvFs) > 0){
				$srvFs = $srvFs[0];
				//echo "<pre>";  print_r($srvFs);  echo "</pre>";
				$status = (string)$srvFs->status;
				$featureSources[$i]->status = $status;
			}else{
				$featureSources[$i]->status = "UNAVAILABLE";
			}
			if($status == "CREATING")
				array_push($fsInProgress, $featureSources[$i]->featureGUID);
			$i++;
		}
					//echo "<pre>";  print_r($featureSources[0]);  echo "</pre>";
		?>	
		
				<script type="text/javascript">
			
		<?php
		echo "var fs_array = new Array();\n";
		echo "var pbArray = new Hash();\n";

		$ix = 0;
		foreach($fsInProgress as $value)
		{
			echo "fs_array.push(\"$value\");\n";
		}
		foreach($fsInProgress as $value)
		{
			$id = str_replace("-","",$value);
		  echo "var pb".$id.";\n";
		}
		echo "var base_url = '".$base_url."';\n";
		echo "var wpsConfig = '".$wpsAddress."/config"."';\n";
		?>
		

		window.addEvent('domready', function() {
		
		<?php
		foreach($fsInProgress as $value)
		{
			$id = str_replace("-","",$value);
		  echo "var pb".$id."= new dwProgressBar({
						container: $('pb$value'),
						startPercentage: 0,
						speed:3500,
						boxID: 'box$id',
						percentageID: 'perc$value',
						displayID: 'text$value',
						displayText: false
					});\n";
		}
		
		foreach($fsInProgress as $value)
		{
			$id = str_replace("-","",$value);
			echo "pbArray.set('$value', pb".$id.");\n";
		  //echo "pbArray['$value'] = pb".$id.";\n";
		}		
		?>


		  //Do featuresource progress at time interval until all fs are done
		  setTimeout("doFsProgress();",3000);

		});

		function paginateOnChange(button, tabIndex){
			$('tabIndex').value = tabIndex;
			submitbutton(button);
		}
		
		function doFsProgress(){
			  
				var reqArray = new Array();
				for (i=0; i<fs_array.length; i++){

						reqArray[i] = new Request({
							url: base_url+'components/com_easysdi_publish/core/proxy.php?proxy_url='+wpsConfig,
							method: 'get',
							data : {
								'operation':'GetTransformationProgress',
								'guid':fs_array[i]
								},
							evalResponse: true,
							//we have to wait to return the function's response.
							async : true,
							onSuccess: function(responseText, responseXML){
								if(responseXML == null)
									return;
								sfid = responseXML.getElementsByTagName('progression')[0].attributes[0].nodeValue;
								prog = parseInt(responseXML.getElementsByTagName('progression')[0].lastChild.textContent);
								stat = responseXML.getElementsByTagName('status')[0].lastChild.textContent;
								
								if(stat == "CREATING")
									pbArray.get(sfid).set(prog);
									
								if(stat == "AVAILABLE")
									pbArray.get(sfid).set(100);
								
								$('st'+sfid).innerHTML=stat;
								
								//If progress is not available
								if(prog == -1){
									 $('pb'+sfid).innerHTML = 'unsupported';
								}
								
								if((prog == 100 || prog == -1) && stat != "CREATING"){	
									for(var j=0; j<fs_array.length; j++){
										if(fs_array[j] == sfid){
											//remove fs
											pbArray.erase(sfid);
											$('pb'+sfid).style.display='none';
										}
									}
								}
		  				},
		  				onFailure: function(xhr){
		  					return false;
		  				}
		  			}).send();
					
				}
				
				//Do progress until all fs are done
				if(pbArray.getKeys().length != 0)
				   setTimeout("doFsProgress();",5000);		

		}
		</script>
		
		<table width="100%">
			<tr>
				<td width="100%">
					<table width="100%">
						<tr>
							<td align="left"><span><?php echo JText::_("EASYSDI_PUBLISH_FIND_DATA_TITLE");?></span></td>
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
										<th class="descr align="center"><?php echo JText::_("EASYSDI_PUBLISH_TEXT_PROGRESS"); ?></th>
										<th class="descr" align="center"><?php echo JText::_("EASYSDI_PUBLISH_TEXT_STATUS"); ?></th>
										<th class="descr" align="center"><?php echo JText::_("EASYSDI_TEXT_CREATION_DATE"); ?></th>
										<th class="descr" align="center"><?php echo JText::_("EASYSDI_TEXT_UPDATE_DATE"); ?></th>
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
										$db->setQuery( "SELECT count(*) FROM #__easysdi_publish_featuresource f, #__easysdi_publish_layer l where l.featuresourceId=f.id AND f.id=".$row->id);
										$isDeletable = $db->loadResult();
									
?>
									<tr class="<?php echo "row$k"; ?>">
										<td align="left"><?php echo $row->name; ?></td>
										<td><div class="prog" id="<?php echo "pb".$row->featureGUID; ?>"/></td>
										<?php if($row->status == "AVAILABLE"){?>	
											<td align="center"><div id="<?php echo "st".$row->featureGUID; ?>"><?php echo $row->status; ?></div></td>
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

								
							</table>
						</fieldset>
				</td>
			</tr>
		</table>

<?php
				}
					echo $tabs->endPanel();
				}
				if($userRights['GEOSERVICE_MANAGER'])
				{
					echo $tabs->startPanel(JText::_("EASYSDI_PUBLISH_BUILD_LAYERS"),"publishLayersPane");
					if($index == 1){
					echo "<br/>";
					
?>

		<table width="100%">
			<tr>
				<td width="100%">
					<table width="100%">
						<tr>
							<td align="left"><span><?php echo JText::_("EASYSDI_PUBLISH_BUILD_LAYERS_TITLE");?></span></td>
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
									$db->setQuery( "SELECT f.name FROM #__easysdi_publish_featuresource f, #__easysdi_publish_layer l where f.id=l.featuresourceId AND l.id=".$row->id);
									$fsName = $db->loadResult();
?>
									<tr class="<?php echo "row$k"; ?>">
										<td align="left"><?php echo $row->name; ?></td>
										<td align="left"><?php echo $fsName; ?></td>
										<td align="center"><?php echo date("d-m-Y", strtotime($row->creation_date)); ?></td>
										<td align="center"><?php echo date("d-m-Y", strtotime($row->update_date)); ?></td>
										<td class="logo"><div title="<?php echo JText::_('EASYSDI_PUBLISH_EDIT_LAYER'); ?>" class="edit" onClick="window.open('index.php?option=com_easysdi_publish&task=editLayer&id=<?php echo $row->id; ?>', '_main');"/></td>
									  <td class="logo"><div title="<?php echo JText::_('EASYSDI_PUBLISH_REMOVE_LAYER'); ?>" class="delete" onClick="return deleteLayer_click('<?php echo $row->layerGuid; ?>');"/></td>
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
						</table>
						</fieldset>
				</td>
			</tr>
			</table>

<?php			
					}
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
					JHTML::stylesheet('xtheme-gray.css', 'components/com_easysdi_publish/js/styler/externals/ext/resources/css/');
					JHTML::stylesheet('style.css', 'components/com_easysdi_publish/js/styler/externals/openlayers/theme/default/');
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
					
					/*
					JHTML::script('Styler.js', 'components/com_easysdi_publish/js/styler/lib/');
					JHTML::script('ColorManager.js', 'components/com_easysdi_publish/js/styler/lib/Styler/');
					JHTML::script('dispatch.js', 'components/com_easysdi_publish/js/styler/lib/Styler/');
					JHTML::script('SchemaManager.js', 'components/com_easysdi_publish/js/styler/lib/Styler/');
					JHTML::script('olx.js', 'components/com_easysdi_publish/js/styler/lib/');
					JHTML::script('SLDManager.js', 'components/com_easysdi_publish/js/styler/lib/Styler/');
					*/
					

					//compressed
					JHTML::script('Styler.js', 'components/com_easysdi_publish/js/styler/script/');					
				  
				  JHTML::script('color-picker.ux.js', 'components/com_easysdi_publish/js/styler/externals/ux/colorpicker/');
					

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
					$db->setQuery("SELECT l.id, l.wmsUrl, l.wfsUrl, l.kmlUrl, l.bbox, l.title FROM #__easysdi_publish_layer l, #__easysdi_community_partner p where p.partner_id=l.partner_id AND p.user_id=".$joomlaUser->id);	
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
					$db->setQuery( "SELECT l.id AS value, l.name as text FROM #__easysdi_publish_layer l, #__easysdi_community_partner p where p.partner_id=l.partner_id AND p.user_id=".$joomlaUser->id);
					$layerList = $db->loadObjectList();
					$layerList [] = JHTML::_('select.option','0', JText::_("EASYSDI_PUBLISH_CHOOSE_LAYER"));
							
?>
		<script>
	
		var userLayersList = <?php echo $jsArray->output_all(false); ?>;
		//var gs_layer = new OpenLayers.Layer.TMS("Geopole Street Map","http://tms.geopole.org/",{type : "png",layername : "geopole_street",maxResolution : 0.703125});
		
    //Required by getFeatureControl
    //OpenLayers.ProxyHost= "/components/com_easysdi_publish/core/proxy.php?proxy_url=";
    
    var styler_proxy_url = '/components/com_easysdi_publish/core/proxy.php?proxy_url';
    OpenLayers.ProxyHost = styler_proxy_url+'=';
    var styler_host = '<?php echo $user_diffusor_url;?>';
    Ext.BLANK_IMAGE_URL = "theme/img/blank.gif";
    
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
	<div class="publish">	
		<table style="width:480px;">
				<tr>
					<td align="right"><div style="width:430px;" id="errorMsg" class="errorMsg"></td>
				</tr>
		</table>
	</div>

		<?php
	}
	
	function showFsStats($fsRow){
		
		?>
		
			<div id="metadata" class="contentin">
				<div id="excReport">
					<h2 class="contentheading"><?php echo JText::_('EASYSDI_FEATURESOURCE_STATUS_TITLE'); ?></h2>
					<table>
						<tr><td>&nbsp;</td></tr>
						<!-- feature source details -->
						<tr><td><?php echo JText::_('EASYSDI_PUBLISH_FEATURESOURCE_NAME');?>:</td><td><?php echo $fsRow->name; ?></td></tr>
						<tr><td><?php echo JText::_('EASYSDI_PUBLISH_PROJECTION');?>:</td><td><?php echo $fsRow->projection; ?></td></tr>
						<tr><td><?php echo JText::_('EASYSDI_TEXT_CREATION_DATE');?>:</td><td><?php echo $fsRow->creation_date; ?></td></tr>
						<tr><td><?php echo JText::_('EASYSDI_TEXT_UPDATE_DATE');?>:</td><td><?php echo $fsRow->update_date; ?></td></tr>
						<!-- exceptions if there are -->
						<?php if(strlen($fsRow->excdetail) > 0 && $fsRow->excdetail != "null"){?>
						<tr><td>&nbsp;</td></tr>
						<tr><td><?php echo JText::_('EASYSDI_ERROR_DETAIL');?>:</td><td><?php echo JText::_($fsRow->excdetail); ?></td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td><?php echo JText::_('EASYSDI_ERROR_MESSAGE');?>:</td></tr>
						<tr><td colspan="2"><div id="excMessage"><?php echo trim($fsRow->excmessage); ?></div></td></tr>
					  <?php } ?>
					</table>
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