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

class HTML_catalog{


	function listCatalogContent($pageNav,$cswResults,$option, $total,$searchCriteria,$maxDescr){
		/*global $mainframe;*/
/*		foreach($_POST as $key => $val) 
		echo '$_POST["'.$key.'"]='.$val.'<br />';*/
		
		
		//$pageNav = new JPagination('100',$limitstart,$limit);
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		
		?>
		<div id="page">
		<h2 class="contentheading"><?php echo JText::_("EASYSDI_CATALOG_TITLE"); ?></h2>
		<div class="contentin">
		
		<h3><?php echo JText::_("EASYSDI_CATALOG_METADATA_TYPE"); ?></h3>
		<table>
			<tr>
				<td><a href=""><?php echo JText::_("EASYSDI_CATALOG_METADATA_PUBLIC"); ?></a></td>
			</tr>
			<tr>
				<td><a href=""><?php echo JText::_("EASYSDI_CATALOG_METADATA_PRIVATE"); ?></a></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
		</table>

			<form name="catalog_search_form" id="catalog_search_form"  method="GET">
			<input type="hidden" name="option" id="option" value="<?php echo JRequest::getVar('option' );?>">
			<input type="hidden" name="view" id="view" value="<?php echo JRequest::getVar('view' );?>">
			<input type="hidden" name="bboxMinX" id="bboxMinX" value="<?php echo JRequest::getVar('bboxMinX', "-180" );?>"> 
			<input type="hidden" name="bboxMinY" id="bboxMinY" value="<?php echo JRequest::getVar('bboxMinY', "-90" );?>"> 
			<input type="hidden" name="bboxMaxX" id="bboxMaxX" value="<?php echo JRequest::getVar('bboxMaxX', "180" ); ?>">
			<input type="hidden" name="bboxMaxY" id="bboxMaxY" value="<?php echo JRequest::getVar('bboxMaxY', "90" );?>">
			<input type="hidden" name="Itemid" id="Itemid" value="<?php echo JRequest::getVar('Itemid');?>">
			<input type="hidden" name="lang" id="lang" value="<?php echo JRequest::getVar('lang');?>">
			<input type="hidden" name="tabIndex" id="tabIndex" value="">
			<h3><?php echo JText::_("EASYSDI_CATALOG_SEARCH_CRITERIA_TITLE"); ?></h3>
		
				<?php
				$index = JRequest::getVar('tabIndex', 0);
				$tabs =& JPANE::getInstance('Tabs', array('startOffset'=>$index));
				echo $tabs->startPane("catalogPane");
				echo $tabs->startPanel(JText::_("EASYSDI_TEXT_SIMPLE_CRITERIA"),"catalogPanel1");
				?> <br/>

			<table width="100%">
				<tr>
					<td>
						<table width="100%">
							<tr>
								<td align="left"><b><?php echo JText::_("EASYSDI_CATALOG_FILTER_TITLE");?></b>&nbsp;
								<input type="text" id="simple_filterfreetextcriteria"  name="simple_filterfreetextcriteria" value="<?php echo JRequest::getVar('simple_filterfreetextcriteria');?>" class="inputbox" /></td>
							</tr>
						</table>
						
					</td>
				</tr>
				</table>
				<table>
				<tr>
					<td>
					<button type="submit" class="easysdi_search_button"
						onclick="clearDetailsForm();
								 document.getElementById('tabIndex').value = '0';
								 document.getElementById('catalog_search_form').submit()">
								 <?php echo JText::_("EASYSDI_CATALOG_SEARCH_BUTTON"); ?></button>
					</td>
					<td>
					<button type="submit" class="easysdi_clear_button"
						onclick="clearForm();
								 document.getElementById('tabIndex').value = '0';
								document.getElementById('catalog_search_form').submit()">
								<?php echo JText::_("EASYSDI_CATALOG_CLEAR_BUTTON"); ?></button>
					</td>
				</tr>
			</table>
				<?php
				echo $tabs->endPanel();
				echo $tabs->startPanel(JText::_("EASYSDI_TEXT_ADVANCED_CRITERIA"),"catalogPanel2");
				?><br/>
			<table width="100%" >
				<tr>
					<td><?php
					HTML_catalog::generateMap();
					?></td>
				</tr>
			</table>
			<table>
				<tr>
					<td>
					<button type="submit" class="easysdi_search_button"
						onclick="clearForm();
								 document.getElementById('tabIndex').value = '1';
								 document.getElementById('catalog_search_form').submit()">
								 <?php echo JText::_("EASYSDI_CATALOG_SEARCH_BUTTON"); ?></button>
					</td>
					<td>
					<button type="submit" class="easysdi_clear_button"
						onclick="clearDetailsForm();
								  document.getElementById('tabIndex').value = '1';
								 document.getElementById('catalog_search_form').submit()">
						<?php echo JText::_("EASYSDI_CATALOG_CLEAR_BUTTON"); ?></button>
					</td>
				</tr>
			</table>
			<script  type="text/javascript">
				function clearDetailsForm ()
				{
					document.getElementById('filterfreetextcriteria').value = '';
					 document.getElementById('filter_visible').value = '';
					 document.getElementById('partner_id').value = '';
					 document.getElementById('filter_orderable').value = '';
					 document.getElementById('filter_theme').value = '';	
				}
				function clearForm()
				{
					document.getElementById('simple_filterfreetextcriteria').value = '';
				}
			</script>
			<?php
			echo $tabs->endPanel();
			echo $tabs->endPane();
			?>
		</form>

		 <?php if($cswResults){ ?> <br/>
<table width="100%">
	<tr>
		<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
		<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
	</tr>
</table>
<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>

<span class="easysdi_number_of_metadata_found"><?php echo JText::_("EASYSDI_CATALOG_NUMBER_OF_METADATA_FOUND");?>
		<?php echo $total ?> </span>
<table class="mdsearchresult">
<!--
	<thead>
		<tr>

	 		<th><?php echo JText::_('EASYSDI_CATALOG_PRODUCT_SHARP'); ?></th>
			<th><?php echo JText::_('EASYSDI_CATALOG_ORDERABLE'); ?></th>

			<th><?php echo JText::_('EASYSDI_CATALOG_ROOT_LOGO'); ?></th>
			<th><?php echo JText::_('EASYSDI_CATALOG_PRODUCT_NAME'); ?></th>
		</tr>
	</thead>
-->
	<?php
	$i=0;
	$param = array('size'=>array('x'=>800,'y'=>800) );
	JHTML::_("behavior.modal","a.modal",$param);



	$xpath = new DomXPath($cswResults);
	$xpath->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
	$nodes = $xpath->query('//gmd:MD_Metadata');

	foreach($nodes  as $metadata){
			
		$i++;
			
		$md = new geoMetadata($metadata);
		?>
		<tr>
			<!-- <td><?php echo $i; ?></td>  -->
			<?php
			$query = "select count(*) from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
			$db->setQuery( $query);

			$metadataId_count = $db->loadResult();

			if ($db->getErrorNum()) {
				$metadataId_count = '0';
			}


			$query = "select count(*) from #__easysdi_product where previewBaseMapId is not null AND previewBaseMapId>0 AND metadata_id = '".$md->getFileIdentifier()."'";

			$db->setQuery( $query);

			$hasPreview = $db->loadResult();
			if ($db->getErrorNum()) {
				$hasPreview = 0;

			}

			$queryPartnerID = "select partner_id from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
			$db->setQuery($queryPartnerID);
			$partner_id = $db->loadResult();
			
			$queryPartnerLogo = "select partner_logo from #__easysdi_community_partner where partner_id = ".$partner_id;
			$db->setQuery($queryPartnerLogo);
			$partner_logo = $db->loadResult();
			
			$query="select CONCAT( CONCAT( a.address_agent_firstname, ' ' ) , a.address_agent_lastname ) AS name from #__easysdi_community_partner p inner join #__easysdi_community_address a on p.partner_id = a.partner_id WHERE p.partner_id = ".$partner_id ." and a.type_id=1" ;
			$db->setQuery($query);
			$supplier= $db->loadResult();
			
			$user =& JFactory::getUser();
			$language = $user->getParam('language', '');
			
			$logoWidth = config_easysdi::getValue("logo_width");
			$logoHeight = config_easysdi::getValue("logo_height");
		
			$isMdPublic = false;
			$isMdFree = true;
			if( $metadataId_count != 0)
			{
				//Define if the md is free or not
				$queryPartnerID = "select is_free from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
				$db->setQuery($queryPartnerID);
				$is_free = $db->loadResult();
				if($is_free == 0)
				{
					$isMdFree = false;
				}
				
				//Define if the md is public or not
				$queryPartnerID = "select external from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
				$db->setQuery($queryPartnerID);
				$external = $db->loadResult();
				if($external == 1)
				{
					$isMdPublic = true;
				}
			}
			
			
			?>
			 
	  <td valign="top" rowspan=3>
	    <img width="<?php echo $logoWidth ?>px" height="<?php echo $logoHeight ?>px" src="<?php echo $partner_logo;?>" alt="<?php echo JText::_('EASYSDI_CATALOG_ROOT_LOGO');?>"></img>
	  </td>
	  <td colspan=3><span class="mdtitle"><a><?php echo $md->getDataIdentificationTitle();?></a></span>
	  </td>
	  <td valign="top" rowspan=2>
	    <table id="info_md">
		  <tr>
		     <td><div <?php if($isMdPublic) echo 'class="publicMd"'; else echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_PRIVATEMD").'" class="privateMd"';?>></div></td>
		  </tr>
		  <tr>
		     <td><div <?php if($metadataId_count>0) echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_ORDERABLE").'" class="easysdi_product_exists"'; else echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_NOTORDERABLE").'" class="easysdi_product_does_not_exist"';?>></div></td>
		  </tr>
		  <tr>
		     <td><div <?php if($isMdFree) echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_FREEMD").'" class="freeMd"'; else echo 'class="notFreeMd"';?>></div></td>
		  </tr>
		</table>
	  </td>
	 </tr>
	 <tr>
	  <td colspan=3><span class="mddescr"><?php echo substr($md->getDescription($language), 0, $maxDescr); if(strlen($md->getDescription($language))>$maxDescr)echo" [...]";?></span></td>
	 </tr>
	 <tr> 
	 <!--
	 <a	class="<?php if ($metadataId_count>0) {echo "easysdi_orderable";} else {echo "easysdi_not_orderable";} ?>" 
		    href="./index.php?option=com_easysdi_shop&view=shop" target="_self"><?php echo JText::_("EASYSDI_VIEW_MD_FILE"); ?>
		 </a>
	 --> 
	  <td><span class="mdviewfile">
	  	<a class="modal"
				title="<?php echo JText::_("EASYSDI_VIEW_MD_FILE"); ?>"
				href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $md->getFileIdentifier();  ?>"
				rel="{handler:'iframe',size:{x:650,y:550}}"><?php echo JText::_("EASYSDI_VIEW_MD_FILE"); ?>
			</a></span>
	  </td>
	  	<?php if ($hasPreview > 0){ ?>
	  <td><span class="mdviewproduct">
	    <a class="modal" href="./index.php?tmpl=component&option=com_easysdi_catalog&task=previewProduct&metadata_id=<?php echo $md->getFileIdentifier();?>"
			rel="{handler:'iframe',size:{x:650,y:550}}"><?php echo JText::_("EASYSDI_PREVIEW_PRODUCT"); ?></a></span>
      </td>
		<?php } ?>
	  <td>&nbsp;</td>
	 </tr>
	 <tr>
	   <td colspan=4>&nbsp;</td>
	 </tr>
	 

	 <?php
	}
	?>
	</table>
	
	<?php } ?></div>
	</div>
	<?php

	}
	function generateMap(){
		global  $mainframe;
		$option= JRequest::getVar('option');
		$db =& JFactory::getDBO();
		$partners = array();
		$partners[0]='';
		$query = "SELECT  #__easysdi_community_partner.partner_id as value, partner_acronym as text FROM `#__easysdi_community_partner` INNER JOIN `#__easysdi_product` ON #__easysdi_community_partner.partner_id = #__easysdi_product.partner_id GROUP BY #__easysdi_community_partner.partner_id";
		$db->setQuery( $query);
		$partners = array_merge( $partners, $db->loadObjectList() );
		if ($db->getErrorNum()) 
		{
		}
		
		
		$themes = array();
		$themes[] = JHTML::_('select.option', '', '');
		$query = "SELECT #__easysdi_metadata_topic_category.code as value, #__easysdi_metadata_topic_category.value as text FROM `#__easysdi_metadata_topic_category`";
		$db->setQuery( $query);
		$themes = array_merge( $themes, $db->loadObjectList() );		
		HTML_catalog::alter_array_value_with_Jtext($themes);
		
		?>
	<script type="text/javascript" src="./administrator/components/com_easysdi_core/common/lib/js/openlayers2.7/OpenLayers.js"></script>
	<script type="text/javascript" src="./administrator/components/com_easysdi_core/common/lib/js/proj4js/lib/proj4js.js"></script>
	<script type="text/javascript" src="./administrator/components/com_easysdi_core/common/lib/js/proj4js/lib/defs/EPSG21781.js"></script>
	<table width="100%">
	<tr>
		<td width="50%" valign="top" >
		<table>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_CATALOG_FILTER_TITLE");?></td>
				<td><input type="text" name="filterfreetextcriteria" id="filterfreetextcriteria"
					value="<?php echo JRequest::getVar('filterfreetextcriteria');?>" class="inputbox" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_CATALOG_FILTER_PARTNER");?></td>
				<td><?php echo JHTML::_("select.genericlist", $partners, 'partner_id', 'size="1" class="inputbox" ', 'value', 'text', JRequest::getVar('partner_id')); ?>
				</td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_CATALOG_FILTER_THEME");?></td>
				<td><?php echo JHTML::_("select.genericlist", $themes, 'filter_theme', 'size="1" class="inputbox" ', 'value', 'text', JRequest::getVar('filter_theme')); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_CATALOG_FILTER_VISIBLE");?></td>
				<td><input type="checkbox" id="filter_visible" name="filter_visible" <?php if (JRequest::getVar('filter_visible')) echo " checked"; ?> class="inputbox" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_CATALOG_FILTER_ORDERABLE");?></td>
				<td><input type="checkbox" id="filter_orderable" name="filter_orderable" <?php if (JRequest::getVar('filter_orderable')) echo " checked"; ?> class="inputbox" /></td>
			</tr>
		</table>
		</td>
		<td width="50%">
		<table>
			<tr>
				<td>
				<div id="map" class="tinymap"></div>
				</td>
			</tr>
			<tr>
				<td>
				<div id="panelDiv" class="olControlEditingToolbar"></div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>


<br/>


<div id="docs"></div>
<br/>
<script  type="text/javascript">
      
var vectors = null;            
function initMap(){
 //OpenLayers.ProxyHost="components/com_easysdi_shop/proxy.php?url=";




<?php
//global  $mainframe;
//$db =& JFactory::getDBO(); 
	
	
$query = "select * from #__easysdi_basemap_definition where def = 1"; 
$db->setQuery( $query);
$rows = $db->loadObjectList();		  
if ($db->getErrorNum()) {						
			echo "<div class='alert'>";			
			echo 			$db->getErrorMsg();
			echo "</div>";
}
?>
				   map = new OpenLayers.Map('map', {
                projection: new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"),
                displayProjection: new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"),
                units: "<?php echo $rows[0]->unit; ?>",
                minResolution: <?php echo $rows[0]->minResolution; ?>,
               maxResolution: <?php echo $rows[0]->maxResolution; ?>,    
                maxExtent: new OpenLayers.Bounds(<?php echo $rows[0]->maxExtent; ?>)
				<?php
					if($rows[0]->restrictedExtent == '1') echo  ",restrictedExtent: new OpenLayers.Bounds(".$rows[0]->maxExtent.")\n"
			    ?>
				<?php
					if($rows[0]->restrictedScales != '') echo  ",scales: [".$rows[0]->restrictedScales."]\n"
			    ?>
				//,controls: []
            });
				  
				 baseLayerVector = new OpenLayers.Layer.Vector(
                "BackGround",
                {isBaseLayer: true,transparent: "true"}
            ); 
				  map.addLayer(baseLayerVector);


<?php

$query = "select * from #__easysdi_basemap_content where basemap_def_id = ".$rows[0]->id." order by ordering"; 
$db->setQuery( $query);
$rows = $db->loadObjectList();		  
if ($db->getErrorNum()) {						
			echo "<div class='alert'>";			
			echo 			$db->getErrorMsg();
			echo "</div>";
}
$i=0;
foreach ($rows as $row){				  
?>				
				  
				layer<?php echo $i; ?> = new OpenLayers.Layer.<?php echo $row->url_type; ?>( "<?php echo $row->name; ?>",
                    
                    
                    <?php 
					if ($row->user != null && strlen($row->user)>0){
						//if a user and password is requested then use the joomla proxy.
						$proxyhost = config_easysdi::getValue("PROXYHOST");
						$proxyhost = $proxyhost."&type=wms&basemapscontentid=$row->id&url=";
						echo "\"$proxyhost".urlencode  (trim($row->url))."\",";												
					}else{	
						//if no user and password then don't use any proxy.					
						echo "\"$row->url\",";	
					}					
					?>
                    
                    
                    {layers: '<?php echo $row->layers; ?>', format : "<?php echo $row->img_format; ?>",transparent: "true"},                                          
                     {singleTile: <?php echo $row->singletile; ?>},                                                    
                     {     
                      maxExtent: new OpenLayers.Bounds(<?php echo $row->maxExtent; ?>),
                      	minScale: <?php echo $row->minResolution; ?>,
                        maxScale: <?php echo $row->maxResolution; ?>,                 
                     projection:"<?php echo $row->projection; ?>",
                      units: "<?php echo $row->unit; ?>",
                      transparent: "true"
                     }
                    );
                 map.addLayer(layer<?php echo $i; ?>);
<?php 
$i++;
} ?>                    

			 

                
               map.addControl(new OpenLayers.Control.LayerSwitcher());
                map.addControl(new OpenLayers.Control.Attribution());                                
            vectors = new OpenLayers.Layer.Vector(
                "Vector Layer",
                {isBaseLayer: false,transparent: "true"                                
                }
            );
            
           
                       
                        
            map.addLayer(vectors);
            map.zoomToMaxExtent();
            
            var containerPanel = document.getElementById("panelDiv");
            var panel = new OpenLayers.Control.Panel({div: containerPanel});
            
		  var panelEdition = new OpenLayers.Control.Panel({div: containerPanel});
         
			
		 	rectControl = new OpenLayers.Control.DrawFeature(vectors, OpenLayers.Handler.RegularPolygon,{'displayClass':'olControlDrawFeatureRectangle'});
			rectControl.featureAdded = function(event) { removeSelection();setLonLat(event);};												
			rectControl.handler.setOptions({irregular: true});                                  
        
                
        panelEdition.addControls([rectControl] );
            
              map.addControl(panelEdition);      
               showSelection();   	
}

function showSelection(){

	if (document.getElementById("bboxMinX").value == "-180" && 	
  		document.getElementById("bboxMinY").value == "-90" &&
  		document.getElementById("bboxMaxX").value == "180" &&  	
  		document.getElementById("bboxMaxY").value == "90" ){
  		//show nothing
  		} else{
  		
  		bounds = new OpenLayers.Bounds(document.getElementById("bboxMinX").value,document.getElementById("bboxMinY").value,document.getElementById("bboxMaxX").value,document.getElementById("bboxMaxY").value).toGeometry()
  		bounds.transform(new OpenLayers.Projection("EPSG:4326"),new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"));
  		 vectors.addFeatures([new OpenLayers. Feature. Vector(bounds )]);
  		
  		}

}

function removeSelection(){

if (vectors.features.length > 1){
vectors.removeFeatures(vectors.features[0]);
}

}

function setLonLat(feature){



	var bounds = feature.geometry.getBounds();
	

 var transformedBounds =  	bounds.transform(new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"),new OpenLayers.Projection("EPSG:4326"));
  	  	   	  	 
  document.getElementById("bboxMinX").value =transformedBounds.left; 	
  document.getElementById("bboxMinY").value =transformedBounds.bottom;
  document.getElementById("bboxMaxX").value =transformedBounds.right; 	
  document.getElementById("bboxMaxY").value =transformedBounds.top;
  
  
}

initMap();

</script>



<?php

	}
	
	function alter_array_value_with_Jtext(&$rows)
	{		
		if (count($rows)>0)
		{
			foreach($rows as $key => $row)
			{		  	
      			$rows[$key]->text = JText::_($rows[$key]->text);
  			}			    
		}
	}

	function getPages($pageNav)
	{
		echo 'getPages';
		//$list = array();
		$pages = $pageNav->getPagesLinks();
		$links = $pages['pages'];
		foreach ($links as $page )
		{
			echo $page;	
		}
		//return $list;	
	}
}


?>