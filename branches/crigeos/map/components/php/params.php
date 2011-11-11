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

/**
 * PHP script to emit component configuration into JavaScript.
 */
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.jsLoaderUtil.php');
$jsLoader =JSLOADER_UTIL::getInstance();

$s = "Ext.namespace('SData');\n";
$s .= "Ext.BLANK_IMAGE_URL = '".$jsLoader->getPath("map","ext")."resources/images/default/s.gif';\n";
$document->addScriptDeclaration($s);

$db =& JFactory::getDBO();

/**
 * Proxy URL: used to ensure access to correct data sets for each user.
 */
$proxyURL = array('url' => JURI::base()."index.php",
                  'option' => 'com_easysdi_map',
                  'Itemid' => JRequest::getCmd("Itemid"),
                  'view' => 'proxy'
);
$proxyURLAsString = $proxyURL['url'];
foreach($proxyURL as $proxyParam => $proxyParamValue) {
	if($proxyParam != 'url') {
		$proxyURLAsString = $proxyURLAsString.(count(explode('?', $proxyURLAsString)) == 1 ? '?' : '&').$proxyParam.'='.$proxyParamValue;
	}
}

$user =& JFactory::getUser();
$loggedIn = $user->id > 0 ? 'true' : 'false';
// Convert this to public or private access for convenience
$access = $user->id > 0 ? 'private' : 'public';
$role=0;
$roleList = array();

if ($loggedIn!='false')
{ // note not boolean
$query = "SELECT aap.accountprofile_id FROM #__sdi_account a ".
		"INNER JOIN #__sdi_account_accountprofile aap ON aap.account_id=a.id WHERE a.user_id=".$user->id.
		" ORDER BY aap.accountprofile_id DESC LIMIT 1;";
$db->setQuery($query);
$result = $db->loadAssocList();
if (count($result)>0)
{
	$role=$result[0]['accountprofile_id'];
}
$query = "SELECT ap.code FROM #__sdi_account a ".
	"INNER JOIN #__sdi_account_accountprofile aap ON aap.account_id=a.id ".
	"INNER JOIN #__sdi_accountprofile ap ON aap.accountprofile_id=ap.id ".
	  " WHERE a.user_id=".$user->id.
	" ORDER BY aap.accountprofile_id DESC LIMIT 1;";

$db->setQuery($query);
$result = $db->loadAssocList();
if (!is_null($result))
{
	foreach ($result as $rec)
	{
		$roleList[] =$rec['code'];
	}
}
}
else
{
	// Get the profile role code for the default Public Role
	$query = "SELECT code FROM #__sdi_accountprofile WHERE id=".$role." ;";
	$db->setQuery($query);
	$result = $db->loadAssocList();
	// assuming id is PK there will only be one record.
	if (count($result)>0)
	{
		$roleList[] =$result[0]['code'];
	}
	// Public Role - default
	$query = "SELECT id FROM #__sdi_accountprofile
				WHERE code='public' ;";
	$db->setQuery($query);
	$result = $db->loadAssocList();
	if (count($result)>0)
	$role=$result[0]['id'];
}

// user.areaLimitedFullPrecision - set to true if the user has full precision access but only in a certain area.
$s= "var user = {
  loggedIn: ".$loggedIn.",
  id: ".$user->id.",
  name: '".$user->name."',
  areaLimitedFullPrecision: true,
  role: $role
};\n";


/**
 * Language suffix for database fields that are multi-lingual.
 * TODO: Should be read from user's Joomla settings.
 */
$lang = $user->getParam('language', 'lu').substr(0,2);

if ($lang!='lu' && $lang!='fr' && $lang!='de') {
	// force non-supported language to Luxembourgish
	$lang='lu';
}

$document = &JFactory::getDocument();

// Check the Proxy Policy document (if present) for access to layers
$valid_wfs_features = array();
$valid_wms_layers = array();
$doCheckProxyLayerPermissions = false;
$policyError = "";

$query = "SELECT * from #__sdi_configuration where name='layerProxyXMLFile';";
$db->setQuery($query);
$result = $db->loadAssocList();
if (!is_null($result) && $result->value != '') {
	foreach ($result as $rec)
	{
		$doCheckProxyLayerPermissions = true;
		extract($rec, EXTR_PREFIX_ALL, "l");
		$layerProxyXMLFile = $l_value;
	}
	// We are not checking the Operations: we do not differentiate to an operation level, so if someone needs access to any operation they get access to the whole layer.
	// We are not checking Availability Period - TODO
	$policyXML = simplexml_load_file($layerProxyXMLFile);
	foreach($policyXML->Policy as $policy){
		if((string)($policy->Subjects["All"]) != "true"){
			// need to check both roles and individual users.
			$conditionMet = false;
			if($policy->Subjects->User) {
				foreach($policy->Subjects->User as $policyUser) {
					if((string)$policyUser == $user->username || (string)$policyUser=='service') {
						$conditionMet = true;
					}
				}
			}
			if($policy->Subjects->Role) {
				foreach($policy->Subjects->Role as $policyRole){
					foreach($roleList as $checkRole) {
						if((string)$policyRole == $checkRole) {
							$conditionMet = true;
						}
					}
				}
			}
			if($conditionMet == false){
				continue;
			}
		}
		if($policy->AvailabilityPeriod){
			if((string)$policy->AvailabilityPeriod->Mask == "dd-MM-yyyy"){
				sscanf((string)$policy->AvailabilityPeriod->From->Date, "%u-%u-%u", $fromDay, $fromMonth, $fromYear);
				sscanf((string)$policy->AvailabilityPeriod->To->Date, "%u-%u-%u", $toDay, $toMonth, $toYear);
				if($toYear > 2037) { // php mktime limit
					$toYear = 2037;
				}
				if((time() < mktime(0, 0, 0, $fromMonth, $fromDay, $fromYear)) ||
				(time() > mktime(0, 0, 0, $toMonth, $toDay, $toYear))) {
					continue;
				}
			} else {
				$policyError = "alert('Unrecognised date format in Policy AvailabilityPeriod - expected dd-MM-yyyy, got ".(string)$policy->AvailabilityPeriod->Mask."');\n";
			}
		}

		foreach($policy->Servers->Server as $server){
			// Assume that the layer name is unique across different URLs. Just add the layer name to valid list
			if($server->Layers){
				foreach($server->Layers->Layer as $layer){
					if(!in_array((string)$layer->Name, $valid_wms_layers)){
						$valid_wms_layers[] = (string)$layer->Name;
					}
				}
			}
			if($server->FeatureTypes){
				foreach($server->FeatureTypes->FeatureType as $featureType){
					$layerName=((string)$server->Prefix).":".((string)$featureType->Name);
					if(!in_array($layerName, $valid_wfs_features)){
						$valid_wfs_features[] = $layerName;
					}
				}
			}
		}
	}
}
$s .= $policyError;

function checkProxyLayerPermissions($doCheck, $type, $name, $valid_wms_layers, $valid_wfs_features){
	if($doCheck == false) {
		return true;
	}
	// If layer has a namespace, only test the layer name
	$name = array_pop(explode(':',$name));
	if(strtolower($type) == 'wfs'){
		return (in_array($name, $valid_wfs_features));
	}
	return (in_array($name, $valid_wms_layers));
}



// Export layer objects from the base layers table.
$query = "SELECT l.* from #__sdi_baselayer l where l.published=1 order by l.ordering ASC;";
$db->setQuery($query);
$result = $db->loadAssocList();
$s .= "SData.baseLayers = [";
$i = 0;
if (!is_null($result)) {
	foreach ($result as $rec)
	{
		extract($rec, EXTR_PREFIX_ALL, "l");
		
		if(checkProxyLayerPermissions($doCheckProxyLayerPermissions, 'WMS', $l_layers, $valid_wms_layers, $valid_wfs_features)){ // All base layers are WMS
			$cache=(($l_cache==1) ? 'true' : 'false');
			$customStyle=(($l_customStyle==1) ? 'true' : 'false');
			$i++;
			$s .= "{
		    id : '$l_id',
		    name : '$l_name',
		    url : '$l_url',
		    type : '$l_type',
		    version : '$l_version',
		    layers : '$l_layers',
		    projection : '$l_projection',
			defaultVisibility : $l_defaultvisibility,	
			defaultOpacity : $l_defaultopacity,
			metadataUrl : '$l_metadataurl',
		    imageFormat : '$l_imgformat',
		    cache : $cache,
		    customStyle : $customStyle,\n";
			if ($l_style) {
				$s .= "    style : \"$l_style\",\n";
			}else{
				$s .= "    style : \"default\",\n";
			}
			if ($l_singletile == 0){ $s .="    singletile : false,\n";}else{$s .="    singletile : true,\n";}
			if ($l_maxextent) {
				$s .= "    maxExtent : new OpenLayers.Bounds($l_maxextent),\n";
			}
			if ($l_resolutionoverscale && $l_resolutions) {
				$s .= "    resolutions : [$l_resolutions],\n";
			}
			if ($l_resolutionoverscale && $l_maxresolution) {
				$s .= "    maxResolution : $l_maxresolution,\n";
			}
			if ($l_resolutionoverscale && $l_minresolution) {
				$s .= "    minResolution : $l_minresolution,\n";
			}
			if (!$l_resolutionoverscale && $l_minScale) {
				$s .= "    minScale : $l_minScale,\n";
			}
			if (!$l_resolutionoverscale && $l_maxScale) {
				$s .= "    maxScale : $l_maxScale,\n";
			}
			if ($l_matrixset) {
				$s .= "    matrixSet : \"$l_matrixset\",\n";
			}
			if ($l_matrixids) {
				$matrixIds = explode(",",$l_matrixids);
				foreach ($matrixIds as &$value) {
				    $value = '"'.$value.'"';
				}
				$matrixIdsString = implode(",",$matrixIds);
				$matrixIdsString = "[".$matrixIdsString."]";
				$s .= "    matrixIds : $matrixIdsString,\n";
			}
			$s .= "    units : '$l_unit'
			}";
			if ($i != count($result)) $s .= ",";
		}
	}
}
$s .= "];\n";


if($i == 0){
	$s .= "alert('Invalid configuration. No Base Layers are available.');\n";
}
// Export overviewlayer config
$query = "SELECT l.* from #__sdi_baselayer l where isoverviewlayer = 1";
$db->setQuery($query);
$result = $db->loadAssocList();
$s .= "SData.overviewLayer = [";
$i = 0;
if (!is_null($result)) {
	foreach ($result as $rec)
	{
		extract($rec, EXTR_PREFIX_ALL, "l");
		
		if(checkProxyLayerPermissions($doCheckProxyLayerPermissions, 'WMS', $l_layers, $valid_wms_layers, $valid_wfs_features)){ // All base layers are WMS
			$cache=(($l_cache==1) ? 'true' : 'false');
			$customStyle=(($l_customStyle==1) ? 'true' : 'false');
			$i++;
			$s .= "{
		    id : '$l_id',
		    name : '$l_name',
		    url : '$l_url',
		    type : '$l_type',
		    version : '$l_version',
		    layers : '$l_layers',
		    projection : '$l_projection',
			defaultVisibility : $l_defaultvisibility,	
			defaultOpacity : $l_defaultopacity,
			isoverviewlayer : $l_isoverviewlayer,
			metadataUrl : '$l_metadataurl',
		    imageFormat : '$l_imgformat',
		    cache : $cache,
		    customStyle : $customStyle,\n";
			if ($l_style) {
				$s .= "    style : \"$l_style\",\n";
			}else{
				$s .= "    style : \"default\",\n";
			}
			if ($l_singletile == 0){ $s .="    singletile : false,\n";}else{$s .="    singletile : true,\n";}
			if ($l_maxextent) {
				$s .= "    maxExtent : new OpenLayers.Bounds($l_maxextent),\n";
			}
			if ($l_resolutionoverscale && $l_resolutions) {
				$s .= "    resolutions : [$l_resolutions],\n";
			}
			if ($l_resolutionoverscale && $l_maxresolution) {
				$s .= "    maxResolution : $l_maxresolution,\n";
			}
			if ($l_resolutionoverscale && $l_minresolution) {
				$s .= "    minResolution : $l_minresolution,\n";
			}
			if (!$l_resolutionoverscale && $l_minScale) {
				$s .= "    minScale : $l_minScale,\n";
			}
			if (!$l_resolutionoverscale && $l_maxScale) {
				$s .= "    maxScale : $l_maxScale,\n";
			}
			if ($l_matrixset) {
				$s .= "    matrixSet : \"$l_matrixset\",\n";
			}
			if ($l_matrixids) {
				$matrixIds = explode(",",$l_matrixids);
				foreach ($matrixIds as &$value) {
				    $value = '"'.$value.'"';
				}
				$matrixIdsString = implode(",",$matrixIds);
				$matrixIdsString = "[".$matrixIdsString."]";
				$s .= "    matrixIds : $matrixIdsString,\n";
			}
			$s .= "    units : '$l_unit'
			}";
			if ($i != count($result)) $s .= ",";
		}
	}
}
$s .= "];\n";
// Export overlay groups objects from the __sdi_overlaygroup table.
$query = "SELECT * from #__sdi_overlaygroup g where g.published=1 order by g.ordering asc;";
$db->setQuery($query);
$result = $db->loadAssocList();

$s .= "SData.overlayGroups = [";
$i = 0;
if (!is_null($result)) {
	foreach($result as $rec)
	{
		$i++;
		extract($rec, EXTR_PREFIX_ALL, "l");
		$open = (($l_open == 1) ? 'true' : 'false');
		$s .= "{ id : $l_id, name : ".json_encode($l_name).", open: $open}";
		if ($i != count($result)) $s .= ",";
	}
};
$s .= "];\n";

// Export overlays objects from the __sdi_overlay table.
$query = "SELECT o.* from #__sdi_overlay o 
inner join #__sdi_overlaygroup og on og.id = o.group_id
where o.published=1 and og.published=1 order by og.ordering DESC,  o.ordering DESC";
$db->setQuery($query);
$result = $db->loadAssocList();

$s .= "SData.overlayLayers = [";
$i = 0;
$done_first = false;
if (!is_null($result)) {
	foreach ($result as $rec)
	{
		$i++;
		extract($rec, EXTR_PREFIX_ALL, "l");
		if(checkProxyLayerPermissions($doCheckProxyLayerPermissions, $l_url_type, $l_layers, $valid_wms_layers, $valid_wfs_features)){
			$cache=(($l_cache==1) ? 'true' : 'false');
			$customStyle=(($l_customStyle==1) ? 'true' : 'false');
			// add comma before all but the first
			if ($done_first) $s .= ",";
			$done_first=true;
			$l_name = addslashes($l_name);
			$s .= "{
		    id : $l_id,
		    group : $l_group_id,
		    name : '$l_name',
		    url : '$l_url',
		    url_type: '$l_type',    
		    version : '$l_version',
		    layers : '$l_layers',
		    projection : '$l_projection',
			defaultVisibility : $l_defaultvisibility,
			defaultOpacity : $l_defaultopacity,
			metadataUrl : '$l_metadataurl',
			imageFormat : '$l_imgformat',
			cache : $cache,
			customStyle : $customStyle,\n";
			if ($l_style) {
				$s .= "    style : \"$l_style\",\n";
			}else{
				$s .= "    style : \"default\",\n";
			}	
			if ($l_singletile == 0){ $s .="singletile : false,\n";}else{$s .="singletile : true,\n";}
			if ($l_maxextent) {
				$s .= "    maxExtent : new OpenLayers.Bounds($l_maxextent),\n";
			}
			if ($l_resolutionoverscale && $l_resolutions) {
				$s .= "resolutions : [$l_resolutions],\n";
			}
			if (!$l_resolutionoverscale && $l_minscale) {
				$s .= "minScale : $l_minscale,\n";
			}
			if (!$l_resolutionoverscale && $l_maxscale) {
				$s .= "maxScale : $l_maxscale,\n";
			}
			if ($l_matrixset) {
				$s .= "    matrixSet : \"$l_matrixset\",\n";
			}
			if ($l_matrixids) {
				$matrixIds = explode(",",$l_matrixids);
				foreach ($matrixIds as &$value) {
				    $value = '"'.$value.'"';
				}
				$matrixIdsString = implode(",",$matrixIds);
				$matrixIdsString = "[".$matrixIdsString."]";
				$s .= "    matrixIds : $matrixIdsString,\n";
			}
			$s .= "units : '$l_unit'
			}";
			//if ($i != count($result)) $s .= ",";
		} else {$s .= "// blocked access to $l_layers\n";}
	}
};
$s .= "];\n";

$s .= "SData.localisationLayers = [";
//$query = "SELECT * from #__easysdi_perimeter_definition where is_localisation != 0;";
$query = "SELECT * from #__sdi_geolocation;";
$db->setQuery($query);
$result = $db->loadAssocList();
$i = 0;
foreach ($result as $rec)
{
	$i++;
	extract($rec, EXTR_PREFIX_ALL, "l");
	/**
	 * Output data from the perimeter definition. Note, we need a way of linking these layers with a parent/child
	 * relationship. To do this, set a parent_id and parent_fk_field_name into this list.
	 * extract_id_from_fid - set to true if the id attribute is the pk so get's buried in the feature ID instead
	 * of it's own attribute.
	 * Because all these are accessed through WFS rather than WMS, we build the proxyed url here
	 */
	$s .= "{
    id: '$l_id',
    wfs_url: '$l_wfsurl',
    wms_url: '$l_wmsurl',
    layer_name: '$l_layername',
    area_field_name: '$l_areafield',
    name_field_name: '$l_namefield',
    id_field_name: '$l_idfield',    
    feature_type_name: '$l_featuretypename',
    maxfeatures: '$l_maxfeatures',
    img_format: '$l_imgformat',
    min_scale: '$l_minresolution',
    max_scale: '$l_maxresolution',
    title: '$l_name',
    parent_id: '$l_parentid',
    parent_fk_field_name: '$l_parentfkfield',
    extract_id_from_fid: false
  }";
	if ($i != count($result)) $s .= ",";
}
$s .= "];\n";

// The distinct results grids should be configurable for other uses of the web map module.
/**
 * Param SData.distinctResultsGrids
 * List of grids that are added to the query results in addition to the main feature tab, and the
 * selection tab. Each grid represents a distinct set of values from the main grid. This would normally
 * be a foreign key in the main list, the values of which can be used to identify a distinct list of the
 * lookup records in another table. For example, if the main grid contains a list of observations of
 * species, the distinct list of species can be pulled out into a second grid.
 * Each grid has the following structure:
 gridname: {
 title: display caption for the grid's tab
 featureType: the feature type used to describe the lookup table to populate the grid
 attrs: the list of attributes obtained for the grid columns.
 Each attribute can have a name, type (string, int etc), width and visible property,
 and should refer to an attribute available in the featureType. The grid column caption
 is set by the internationalisation string COL_ + the attribute name
 distinctFk: the foreign key field used in the main table to identify the distinct values
 distinctPk: the primary key in the lookup table that describes the distinct values that are
 to be listed.
 rowDetailsFeatureType: if specified, then a link to a details report is available for each row,
 populated using the output of a single feature from this feature type. If the feature type
 includes a geometry, then an overview map is added to the report.
 }
 *
 */
$s .= "\nSData.distinctResultsGrids = {";
$query = "SELECT ft.name, erg.*, ftrd.name as rd_feature_type from #__sdi_resultgrid erg ".
    "INNER JOIN #__sdi_featuretype ft ON ft.id=erg.featuretype ".
    "INNER JOIN #__sdi_featuretype ftrd ON ftrd.id=erg.rowdetailsfeaturetype;";
$db->setQuery($query);
$result = $db->loadAssocList();
$i = 0;
foreach ($result as $rec)
{
	$i++;
	extract($rec, EXTR_PREFIX_ALL, "l");
	$s .= "\n  '$l_internal_name' : {
    title: '$l_title'
   ,featureType: '$l_name'
   ,distinctFk: '$l_distinctfk' // the foreign key in the main feature type
   ,distinctPk: '$l_distinctpk' // the primary key in the distinct feature type
   ,rowDetailsFeatureType: '$l_rd_feature_type'\n  }";
	if ($i != count($result)) $s .= ",";
}
$s .= "};\n\n";

/**
 * The simple searches are configurable for other uses of the web map module.
 * title - Display title
 * dropDownFeatureType - The feature type used for auto-complete of the Search For box.
 * dropDownDisplayAttr - The display attribute for this auto-complete box
 * dropDownIdAttr - the ID attribute from the auto-complete feature type that will be used to filter on the
 * 	main list of features.
 * searchAttribute - The attribute in the main list of features that will be filtered against
 * operator - The filter operator, e.g. "==", "~". See OpenLayers.Filter.Comparison. Default "==".
 * additionalFilters - Other fixed filters that are added for this filter type
 * 	attribute - The attribute in the main list of features to filter on
 * 	value - The fixed value to filter for
 *   operator - The filter operator, e.g. "==", "~". See OpenLayers.Filter.Comparison. Default "==".
 * gridConfig
 *   distinctResultsGrids - array of the distinct tabs that are available.
 */

$query = "SELECT * from #__sdi_simplesearchty
pe;";
$db->setQuery($query);
$result = $db->loadAssocList();
if (count($result)>0) {
	$s .= "\nSData.simpleSearchTypes = [";
	$i = 0;
	foreach ($result as $rec)
	{
		$i++;
		extract($rec, EXTR_PREFIX_ALL, "l");
		$s .= "{\n    title : EasySDI_Map.lang.getLocal('$l_code')
	   ,untranslatedTitle : '$l_code'
	   ,dropDownFeatureType : '$l_dropdownfeaturetype'
	   ,dropDownDisplayAttr : '$l_dropdowndisplayattr'
	   ,searchAttribute : '$l_searchattribute'
	   ,operator : '$l_operator'\n";
		if(!is_null($l_dropdownidattr) && strlen($l_dropdownidattr) > 0) {
			$s .= "   ,dropDownIdAttr : '$l_dropdownidattr'\n";
		}
		$filterquery = "SELECT * from #__sdi_simplesearchfilter ssaf
	    INNER JOIN #__sdi_sst_saf link ON link.id_saf = ssaf.id
	    WHERE link.id_sst = $l_id;";
		$db->setQuery($filterquery);
		$filters = $db->loadAssocList();
		$j = 0;
		if (!is_null($filters)) {
			$s .= "   ,additionalFilters : [";
			foreach ($filters as $filter)
			{
				$j++;
				extract($filter, EXTR_PREFIX_ALL, "lf");
				$s .= "{\n      attribute: '$lf_attribute'
	     ,value: '$lf_value'
	    }";
				if ($j != count($filters)) $s .= ",";
			};
			$s .= "]\n";
		};
		$gridquery = "SELECT * from #__sdi_resultgrid erg
	    INNER JOIN #__sdi_sst_erg link ON link.id_erg = erg.id
	    WHERE link.id_sst = $l_id;";
		$db->setQuery($gridquery);
		$grids = $db->loadAssocList();
		$j = 0;
		if (!is_null($grids)) {
			$s .= "   ,gridConfig : {
	      distinctResultsGrids : [";
			foreach ($grids as $grid)
			{
				$j++;
				extract($grid, EXTR_PREFIX_ALL, "lg");
				$s .= "'$lg_name'";
				if ($j != count($grids)) $s .= ", ";
			};
			$s .= "]\n    }\n";
		};
		$s .= "  }";
		if ($i != count($result)) $s .= ",";
	}
	$s .= "];\n\n";
}
else
{
	$s .= "\nSData.simpleSearchTypes = [";

	$s .= "{\n    title : EasySDI_Map.lang.getLocal('notDefined')
	   ,untranslatedTitle : 'Not defined'
	   ,dropDownFeatureType : 'dummy'
	   ,dropDownDisplayAttr : 'dummy'
	   ,searchAttribute : 'dummy'
	   ,operator : 'dummy'\n";
	$s .= "  }";
	$s .= "];\n\n";
}/*
else {
$s .= "alert('Invalid configuration. No simple search methods are available.');\n";
}
*/

/**
 *  This is the feature type that is searched to get the searchable features. The feature type can
 * have multiple geometries for different search precisions.
 * SData.searchLayer.featureType - name of feature type to load searchable features from.
 * SData.searchLayer.geometryName - name of default geom field in this feature type.
 * SData.searchLayer.rowDetailsFeatureType = the
 feature type accessed to produce the details report of a single row. $access will be replaced with private or public
 depending on authorisation. {geom} is replaced with the geometry name required for loading different precisions.
 * styles: array of styles to cycle through for each search bar.
 * TODO: searchPrecisions.selected - behaviour should be auto-select first when system first loads. Subsequent- remember user settings.
 */
// Retrieve the search layer that is stored in the database
$query = "SELECT ft.id as ft_id, ft.name, ftdet.name as det_name, sl.* from #__sdi_searchlayer sl ".
"JOIN #__sdi_featuretype ft on ft.id=sl.featuretype ".
"JOIN #__sdi_featuretype ftdet on ftdet.id=sl.rowdetailsfeaturetype where enable <> 0;";
$db->setQuery($query);
$result = $db->loadAssocList();
$i=0;
if (!is_null($result) && count($result)>0) {
	$s .= "\nSData.searchLayer = {";
	foreach ($result as $rec)
	{
		if( $i == 0) { // only process first record.
			extract($rec, EXTR_PREFIX_ALL, "l");
			$searchLayer_ft_id=$l_ft_id;
	  $l_name=str_replace('<access>', $access, $l_name);
	  $l_det_name=str_replace('<access>', $access, $l_det_name);
	  $s .= "\n    featureType: '$l_name{geom}'
   ,geometryName: '$l_geometryname'
   ,rowDetailsFeatureType: '$l_det_name'\n";
	  $s .= "   ,styles: [ ";
	  if(!is_null($l_styles) && strlen($l_styles) > 0) {
	  	$s.=stripslashes($l_styles);
	  }
	  $s.= " ]\n";
		};
		$i++;
	}
	$s .= "};\n";
}/* else {
$s .= "alert('Invalid configuration. No search layer defined.');\n";
}
*/

/**
 * The list of available precisions for searches to return features. Each precision represents a geom
 * attribute available in the feature type indicated by SData.searchLayer.featureType.
 * SData.searchPrecisions[] = name of geom field.
 * SData.searchPrecisions[].title = Title of the search precision in the tree.
 * SData.searchPrecisions[].minScale = MinResolution of layer.
 * SData.searchPrecisions[].minScale = MaxResolution of layer.
 * SData.searchPrecisions[].lowScaleSwitchTo = Geom name of search precision that should be replaced if above the maxScale (optional).
 * SData.searchPrecisions[].style = specify any style overrides here. Generally, the larger the geom, the lower the fillOpacity should be
 *   set to.
 */
// Retrieve the precisions that are stored in the database
$query = "SELECT * from #__sdi_precision;";
$db->setQuery($query);
$result = $db->loadAssocList();
$s .= "

SData.searchPrecisions = {";
$i=0;
if (!is_null($result)) {
	foreach ($result as $rec)
	{
		$i++;
		extract($rec, EXTR_PREFIX_ALL, "l");
		$s .= "\n    $l_name : {
        title : '$l_description'\n";
		if(!is_null($l_maxresolution) && strlen($l_maxresolution) > 0 && $l_max_resolution > 0) {
			$s .= "       ,maxScale : $l_maxresolution\n";
		}
		if(!is_null($l_minresolution) && strlen($l_minresolution) > 0 && $l_minresolution > 0) {
			$s .= "       ,minScale : $l_minresolution\n";
		}
		if(!is_null($l_lowscaleswitchto) && strlen($l_lowscaleswitchto) > 0) {
			$s .= "       ,lowScaleSwitchTo : '$l_lowscaleswitchto'\n";
		}
		if(!is_null($l_style) && strlen($l_style) > 0) {
			$s .= "       ,style : { $l_style }\n";
		}
		$s .= "    }";
		if ($i != count($result)) $s .= ",";
	}
};
$s .= "};
";

/**
 * List of attributes available for each feature type
 */
$s .= "
SData.attrs = [];
SData.defaultAttrs = [];\n";
$query = "SELECT * from #__sdi_featuretype;";
$db->setQuery($query);
$ftypes = $db->loadAssocList();
if (!is_null($ftypes) && count($ftypes)>0) {
	foreach ($ftypes as $ftype) {
		extract($ftype, EXTR_PREFIX_ALL, "f");
		$query = "SELECT DISTINCT a.* from #__sdi_featuretypeattribute a ".
				"INNER JOIN #__sdi_ftatt_profile ap ON ap.ftatt_id=a.id AND ap.profile_id=".$role.
				" where a.id=$f_id;";
		$db->setQuery($query);
		$attrs = $db->loadAssocList();
		$f_name=str_replace('<access>', $access, $f_featuretypename);
		$s .= "SData.attrs.$f_featuretypename = [";
		$i=0;
		$defaults = array(); // list of attributes that are initially visible in the grid
		foreach ($attrs as $attr) {
			$i++;
			extract($attr, EXTR_PREFIX_ALL, "a");
			$a_name=str_replace('<lang>', $lang->_lang, $a_name);
			$s .= "{\n\tname: '$a_name',\n".
				"\ttype: '$a_datatype'";			
			if ($a_visible==0) {
				$s .= ",\n\tvisible: false";
			} else {
				if (!empty($a_width)) {
					$s .= ",\n\twidth: $a_width";
				}
				if ($a_initialvisibility!=0) {
					$defaults[] = "'$a_name'";
				}
			}
			$s .= "\n}";
			if ($i != count($attrs)) $s .= ", ";
		}
		$s .= "];\n";
		/**
		 * SData.defaultAttrs describes the list of attributes that are visible for a new user who has
		 * not configured their columns.
		 */
		$s .= "SData.defaultAttrs.$f_featuretypename = new Array(".implode(', ', $defaults).") ;\n";
	}
}

/**
 * SData.detailsReport describes the geometry attribute used for the advanced report pages. If missing,
 * then the details report will not have a overview map.
 */
$s .= "SData.detailsReportGeoms = {\n";
$query = "SELECT DISTINCT * from #__sdi_featuretype WHERE geometry IS NOT NULL AND geometry<>'';";
$db->setQuery($query);
$ftypes = $db->loadAssocList();
if (!is_null($ftypes) && count($ftypes)>0) {
	$i=0;
	foreach ($ftypes as $ftype) {
		$i++;
		$f_name = str_replace('<access>',$access, $ftype['featuretypename']);
		$s .= "\t$f_name : '".$ftype['geometry']."'";
		if ($i < count($ftypes)) $s .= ",\n";
	}
}
$s .= "\n}\n";

/**
 * Specify the optional comments feature type here. Used to capture comments stored via WFST.
 * The comment feature type should be of the structure, replacing featureFk with the name of the field
 * in the main features table that acts as a unique identifier.
 *   id serial NOT NULL,
 *   <<featureFk>> integer NOT NULL,
 *   "comment" character varying NOT NULL,
 *   userId integer NOT NULL,
 *   enteredOn timestamp NOT NULL
 *
 *   typeName: The name of the feature type to store comments into.
 *   featureCommentCount: The name of the attribute in the main features table which stores a count of the comments.
 */
$s .= "SData.commentFeatureType = {\n";
$query = "SELECT * from #__sdi_commentfeaturetype WHERE enable!=0 LIMIT 1;";
$db->setQuery($query);
$commenttypes = $db->loadAssocList();
if (!is_null($commenttypes) && count($commenttypes)>0) {
	$s .= "\ttypeName: '".$commenttypes[0]['featuretypename']."',\n";
	$s .= "\tfeatureCommentCount: '".$commenttypes[0]['countattribute']."'";
}
$s .= "\n}\n";

// TODO - these should be dynamically read from PHP component parameters
// This has been done partially, with only those set up in the db as default no longer hard coded.
$s .="
var componentParams = {
";

// Retrieve the component params that are stored in the database
// these are simple key : value pairs
// compDefaults holds defaults for entries not initially populated in the DB. This functionality allows
// them to be added into the DB at a later point, without having to change the code
// Any new simple key=>value entries in componentParams shopuld be added to this list.
$compDefaults = array(
  'pubWmsUrl' => 'http://localhost:8080/geoserver/wms' // url of publication database Wms service
// This is the actual server: it is not used on its own - the code sends the requests via the proxy
// This is also the case for pubWfsUrl, though this is fetched from the DB: a bit of code later
// coverts pubWfsUrl to a proxied version.
,'defaultCoordMapZoom' => 4  // default zoom level when coordinates provided to location comboBox
,'autocompleteNumChars' => 4 // number of characters that have to be entered in the simple search autocomplete comboboxes
// before the WFS calls are made to create the appropriate drop down lists.
,'autocompleteUseFID' => 1   // determines whether the simple search 'In Place' autocomplete combobox overrides the value
// for the localisation layer ID name and uses the WFS feature ID (fid) instead.
// 0 : use the ID column stored in DB, 1: use the FID (this is the default)
// Unless you know what you are doing, and know that the column you have chosen is unique
// and present in the WFS data, this should be left as 1 (i.e. use fid).
,'autocompleteMaxFeat' => 50 // Max number of features returned by each WFS call for simple seach autocomplete comboboxes.
);

$query = "SELECT * from #__sdi_configuration WHERE module_id=(SELECT id FROM #__sdi_list_module WHERE code = 'MAP' LIMIT 1);";
$db->setQuery($query);
$result = $db->loadAssocList();
if (!is_null($result)) {
	foreach ($result as $rec)
	{
		$i++;
		extract($rec, EXTR_PREFIX_ALL, "l");
		if($l_name == 'mapMaxExtent')
			$s .= "	$l_name :  new OpenLayers.Bounds($l_value), ";
		else if($l_name == 'mapResolutions' ){
			if((trim($l_value))!= "")
				$s .= "	$l_name :  [$l_value], ";
			else 
				$s .= "	$l_name : '',";
		}else
			$s .= "    $l_name : '$l_value',";
		if(!is_null($l_description) && strlen($l_description) > 0) {
			$s .= " // $l_description";
		}
		if (array_key_exists($l_name, $compDefaults)) {
			unset($compDefaults[$l_name]);
		}
		$s .= "\n";
	}
};
if (count($compDefaults) > 0) {
	foreach ($compDefaults as $compDefaultsKey => $compDefaultsValue)
	{
		$s .= "    ".$compDefaultsKey." : '".$compDefaultsValue."',  // Default value not overridden by DB\n";
	}
};


// Include proxy details.
$s .= "
  proxyURL: {
    url: '".$proxyURL['url']."',
    params: {";
$i = 0;
$first = true;
foreach($proxyURL as $proxyParam => $proxyParamValue) {
	$i++;
	if($proxyParam != 'url') {
		$first = false;
		$s .= $proxyParam.": '".$proxyParamValue."'";
	}
	if ($i != count($proxyURL) && !$first) $s .= ",";
}

$s .="},
    asString: '".$proxyURLAsString."'
  },
";

// Retrieve the projection details from the database
$query = "SELECT * from #__sdi_projection where enable <> 0;";
$db->setQuery($query);
$result = $db->loadAssocList();
$s .= "  displayProjections: [";
$i = 0;
if (!is_null($result) && count($result)>0) {
	foreach ($result as $rec)
	{
		$i++;
		extract($rec, EXTR_PREFIX_ALL, "l");
		$s .= "\n    {name : '$l_name', title : EasySDI_Map.lang.getLocal('$l_code'), numDigits : $l_numDigits";
		if(!is_null($l_proj4text) && strlen($l_proj4text) > 0) {
			$s .= ", proj4text : '$l_proj4text'}";
		} else  {
			$s .= "}";
		}
		if ($i != count($result)) $s .= ",";
	}
} else {
	// Use a default of just 4326 support
	$s .= "\n    {name : 'WGS84', title : EasySDI_Map.lang.getLocal('EPSG4326'), numDigits : 2}";
};

$s .="\n  ],
	
  
  MiscSearch: {
      attrList: [ " ;
if($searchLayer_ft_id)
{
	$query = "SELECT a.name from #__sdi_featuretypeattribute a
		INNER JOIN #__sdi_ftatt_profile p ON a.id = p.ftatt_id  
		where a.iftatt_id=$searchLayer_ft_id AND a.miscsearch=1 AND p.profile_id=$role;";
	$db->setQuery($query);
	$result = $db->loadAssocList();
	$i = 0;
	if (!is_null($result) && count($result)>0) {
		foreach ($result as $rec)
		{
			$s .= "'".$rec['name']."'";
			if ($i != count($result)) $s .= ",\n";
		}
	}
}
//TODO --> change the component configuration to have the MiscSearch list like this :
/*
 'simple_classification_$lang',
 'themes',
 'any_name'
 ";

 if ($role>=2) {
 $s .= ",
 'associated_biotopes',
 'biotope_classification',
 'determiner_name_key'
 ";
 }
 if ($role>=3) {
 $s .= ",
 'actual_taxon_name',
 'actual_taxon_authority',
 'preferred_taxon_authority',
 'sample_recorders_keylist',
 'spatial_ref',
 'survey_responsible_key',
 'survey_keywords'";
 }
 */
$s .="],
      attrComboWidth: 200,
      compList: [
        [OpenLayers.Filter.Comparison.EQUAL_TO, '='],
        [OpenLayers.Filter.Comparison.NOT_EQUAL_TO, '<>'],
        [OpenLayers.Filter.Comparison.LESS_THAN, '<'],
        [OpenLayers.Filter.Comparison.GREATER_THAN, '>'],
        [OpenLayers.Filter.Comparison.LESS_THAN_OR_EQUAL_TO, '<='],
        [OpenLayers.Filter.Comparison.GREATER_THAN_OR_EQUAL_TO, '>='],
        [OpenLayers.Filter.Comparison.LIKE, EasySDI_Map.lang.getLocal('Like')]
      ],
      compComboWidth: 50,
      textBoxWidth: 120,
      typeComboWidth: 60
  },
  PlaceSearch: {
      dropDownWidth: 200
     ,minChars: 1
     ,itemSelector: {
          height: 130
      }
  },
  authorisedTo: {    
    ";

$query = 'SELECT r.code '.
		'FROM #__sdi_list_role r '.
		'INNER JOIN #__sdi_profile_role pr ON pr.role_id=r.id '.
		"WHERE pr.profile_id=$role";
$db->setQuery($query);
$result = $db->loadAssocList();
$i=0;
foreach ($result as $rec) {
	$i++;
	$s .= "'".$rec['code']."': true";
	if ($i != count($result)) $s .= ",";
}
//Add the dataprecision in the authorization list if role is not public
//Todo : see with John how he wants to make it
/*if($role!=0)
 {
 $s .= ',dataPrecision: true' ;
 }*/
/*
 $s .= "
 }
 ,
 WMSFilterSupport : true ,

 };
 ";
 */
$s .= "
  } 
  
}; 
";
$s .= " componentParams.proxiedPubWfsUrl = '".$proxyURLAsString."&url='+componentParams.pubWfsUrl; ";


/*
 * Loading extensions :
 * The current version of com_easysdi_map supports extensions for elements 'FilterPanel' and 'SearchPanel'.
 * This section loads the extensions classes name that have to be instanciated in place of the defaults one
 */
/*  FilterPanelClassName :'FilterPanel'  FilterPanelClassName :'RwgFilterPanel' */
$query = "SELECT * FROM #__sdi_mapextension WHERE extended_object='FilterPanel' OR extended_object='SearchPanel' ";
$db->setQuery($query);
$extensions = $db->loadObjectList();
//Default classes name
$filterPanel='FilterPanel';
$searchPanel='SearchPanel';
foreach ($extensions as $extension)
{
	if ($extension->extended_object == 'FilterPanel'){$filterPanel = $extension->extension_object;}
	if ($extension->extended_object == 'SearchPanel'){$searchPanel = $extension->extension_object;}
}
$s .="
var extensionClasses = {
	FilterPanelClassName :'$filterPanel',
	SearchPanelClassName :'$searchPanel'
	};
";

/*
 * Display options :
 * Loads from the database the display options set up via com_easysdi_map back-end.
 * The elements that are currently support a display option are :
 * - simple search toolbar
 * - advanced search toolbar
 * - data precision panel
 * - localisation toolbar
 * - map toolbar
 * - map overview panel
 * - map annotation toolbar
 * - map coordinate toolbar
 */
// Retrieve the element display options that are stored in the database
$query = "SELECT * FROM #__sdi_mapdisplayoption ;";
$db->setQuery($query);
$options = $db->loadObjectList();
$i = 1;
$s .="
var componentDisplayOption = {
	";
foreach ($options as $option)
{
	$s .= $option->object."Enable : ";
	$option->enable == 1 ? $s .= "true ": $s .= "false ";
	$i <> count($options) ? $s .= ", \n" : $s.="" ;
	$i++;
}
$s .="
}; \n
";

$s .= "
componentParams.getMapImageFormat =
[
	{
		value :'image/png',  
		text : 'png'
	}

];
";
//	},
//	{
//		value :'image/jpeg',
//		text : 'jpeg'
//	},
//	{
//		value :'image/gif',
//		text : 'gif'
//	},
//	{
//		value :'image/wbmp',
//		text : 'wbmp'
//	},
//	{
//		value :'image/tiff',
//		text : 'tiff'
//	}
/*"
 SimpleSearchEnable : true,
 AdvancedSearchEnable : true,
 DataPrecisionEnable : true,
 LocalisationEnable : true,
 ToolBarEnable : true,
 MapOverviewEnable : true
 };
 ";*/

//Annotation styles
$query = "SELECT * FROM #__sdi_annotationstyle ;";
$db->setQuery($query);
$styles = $db->loadObjectList();
$i = 1;
$s .="
var annotationStyle = [
	";
foreach ($styles as $style)
{
	$s .= "{";
	$s .= "text : '". $style->name."',";
	$s .= "fillColor : '". $style->fillcolor."',";
	$s .= "fillOpacity : ". $style->fillopacity.",";
	$s .= "strokeColor : '". $style->strokecolor."',";
	$s .= "strokeOpacity : ". $style->strokeopacity.",";
	$s .= "strokeWidth : ". $style->strokewidth.",";
	$s .= "strokeLinecap : '". $style->strokelinecap."',";
	$s .= "strokeDashstyle : '". $style->strokedashstyle."',";
	$s .= "pointRadius : ". $style->pointradius.",";
	$s .= "externalGraphic : '". $style->externalgraphic."',";
	$s .= "graphicWidth : ". $style->graphicwidth.",";
	$s .= "graphicHeight : ". $style->graphicheight.",";
	$s .= "graphicOpacity : ". $style->graphicopacity.",";
	$s .= "graphicXOffset : ". $style->graphicxoffset.",";
	$s .= "graphicYOffset : ". $style->graphicyoffset;
	$s .= "}";
	$i <> count($styles) ? $s .= ", \n" : $s.="" ;
	$i++;
}
if($i == 1)
{
	//No defined style, give a default one
	$s .= "{";
	$s .= "text : 'Annotation',";
	$s .= "fillColor : '#cc7543',";
	$s .= "fillOpacity : 1,";
	$s .= "strokeColor : '#cc7543',";
	$s .= "strokeOpacity : 1,";
	$s .= "strokeWidth : 2,";
	$s .= "strokeLinecap : 'butt',";
	$s .= "strokeDashstyle : 'longdashdot',";
	$s .= "pointRadius : 5,";
	$s .= "externalGraphic : '',";
	$s .= "graphicWidth : 0,";
	$s .= "graphicHeight : 0,";
	$s .= "graphicOpacity : 0,";
	$s .= "graphicXOffset : 0,";
	$s .= "graphicYOffset : 0";
	$s .= "}";
}
$s .="
]; \n
";

// Retrieve the details report feature types so we can sort them in column category order
$query = "SELECT featuretypename from #__sdi_featuretype ft
INNER JOIN #__sdi_featuretype_usage ftu ON ftu.ft_id=ft.id
WHERE ftu.usage_id=3 ";
$db->setQuery($query);
$result = $db->loadAssocList();
foreach ($result as $ftype) {
	$f_name = str_replace('<access>',$access, $ftype['featuretypename']);
	$s .= "
SData.attrs.$f_name.sort(
  function(a,b) {
    colA = EasySDI_Map.lang.getLocal('COL_' + a.name).split('/');
    colB = EasySDI_Map.lang.getLocal('COL_' + b.name).split('/');
    if (catOrder.indexOf(colA[0])<catOrder.indexOf(colB[0])) {
      return -1;
    } else if (catOrder.indexOf(colA[0])>catOrder.indexOf(colB[0])) {
      return 1;
    } else {
      return 0;
    }
  }
);
";
}

$document->addScriptDeclaration($s);
?>