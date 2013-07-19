<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_service/tables/physicalservice.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_service/tables/virtualservice.php';

/**
 * Easysdi_map model.
 */
class Easysdi_mapModelMap extends JModelForm {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication('com_easysdi_map');

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_easysdi_map.edit.map.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            JFactory::getApplication()->setUserState('com_easysdi_map.edit.map.id', $id);
        }
        $this->setState('map.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);
    }

    /**
     * Method to get the map object as a JSON object to use in the gxp.viewer 
     * constructor to instanciate a geoviewer
     * @param integer	The id of the object to get.
     * @return JSON object
     */
   /* public function getJSONdata ($id = null){
        $this->getData();
        
        $app            = JFactory::getApplication();
        $params         = $app->getParams('com_easysdi_map');
        
        //Init
        $str = '{';
        
        //Proxy Host
        $proxyhost = $params->get('proxyhost');
        if (!empty($proxyhost))
        {
            $str .= ' proxy:"'.$proxyhost.'",';
        }
                    
        //
        $str .= '   about: { 
                        title: "'.$this->_item->title.'", 
                        "abstract": "'.$this->_item->abstract.'"
                    }, 
                    portalConfig: {
                        renderTo:"sdimapcontainer",
                        width: width, 
                        height: height,
                        layout: "border",
                        region: "center",
                        items: [
                            {
                                id: "centerpanel",
                                xtype: "panel",
                                layout: "card",
                                region: "center",
                                border: false,
                                activeItem: 0, 
                                items: [
                                    "sdimap",
                                    {
                                        xtype: "gxp_googleearthpanel",
                                        id: "globe",
                                        tbar: [],
                                        mapPanel: "sdimap"
                                    }
                                ]
                            }, 
                            {
                                id: "westpanel",
                                xtype: "panel",
                                header: false,
                                split: true,
                                collapsible: true,
                                collapseMode: "mini",
                                hideCollapseTool: true,
                                layout: "fit",
                                region: "west",
                                width: 200
                            },
                            {
                                id:"hiddentbar",
                                xtype:"toolbar",
                                border: false,
                                height:0,
                                region:"south",
                                items:[]
                            },
                            {
                                xtype: "gxp_autocompletecombo",
                                region:"east",
                                url: "http://localhost/proxy3300/topp",
                                fieldName: "STATE_NAME",
                                featureType: "states",
                                featurePrefix: "topp",
                                fieldLabel: "Title",
                                geometryName:"the_geom",
                                maxFeatures:"10",
                                emptyText: "Search..."
                            }
                        ],

                    },
                    // Tools
                    tools: [
                        {
                            ptype: "sdi_gxp_layermanager",
                            rootNodeText: "'.$this->_item->rootnodetext.'",';
                            foreach ($this->_item->groups as $group)
                            {
                                if($group->isdefault)
                                {
                                    //Acces not allowed
                                    if(!in_array($group->access, $user->getAuthorisedViewLevels()))
                                        break;
                                        
                                    $str .= ' defaultGroup: "'.$group->alias.'",';
                                    break;
                                }
                            } 
                            $str .= 'outputConfig: {
                                        id: "tree",
                                        border: true,
                                        tbar: [] 
                                     },
                                     groups: {';
                            
                            //Groups are added in the order saved in the database
                            foreach ($this->item->groups as $group)
                            {
                                //Acces not allowed
                                if(!in_array($group->access, $user->getAuthorisedViewLevels()))
                                    continue;
                                $expanded = 'false';
                                if ($group->isdefaultopen) 
                                    $expanded = 'true'; 
                                if($group->isbackground)
                                {
                                    
                                    $str .= '"background": {
                                                title: "'.$group->name.'", 
                                                exclusive: true,
                                                expanded: "'.$expanded.'"
                                             },';
                                }
                                else
                                {
                                    $str .= '"'.$group->alias.'":{
                                            title : "'.$group->name.'",
                                            expanded: "'.$expanded.'"
                                            },';
                                }
                                $str .= '},
                                         outputTarget: "westpanel"
                                    },';
                    foreach ($this->item->tools as $tool)
                    {
                            switch ($tool->alias)
                            {
                                    case 'googleearth':
                                    {
                                           $str.= '
                                            { 
                                                ptype: "gxp_googleearth",
                                                actionTarget: ["map.tbar", "globe.tbar"]
                                            },
                                            {
                                    actions: ["-"],
                                    actionTarget: "map.tbar"
                                },
                                        <?php
                                        break;
                                    case 'navigationhistory':
                                            ?>
                                            {
                                    ptype: "gxp_navigationhistory",
                                    actionTarget: "map.tbar"
                                },
                                            <?php 
                                            break;
                                    case 'navigation':
                                            ?>
                                                    {
                                                    ptype: "gxp_navigation",
                                                    actionTarget: "map.tbar", 
                                                    toggleGroup: "navigation"
                                                },
                                                <?php 
                                                break;
                                    case 'zoom':
                                            ?>
                                             {
                                     ptype: "gxp_zoom",
                                     actionTarget: "map.tbar",
                                     toggleGroup: "navigation",
                                     showZoomBoxAction: true,
                                     controlOptions: {zoomOnClick: false}
                                 },
                                            <?php 
                                            break;
                                    case 'zoomtoextent':
                                            ?>
                                            {
                                    ptype: "gxp_zoomtoextent",
                                    actionTarget: "map.tbar"
                                },
                                {
                                    ptype: "gxp_zoomtolayerextent",
                                    actionTarget: {target: "tree.contextMenu", index: 0}
                                },
                                            <?php 
                                            break;
                                    case 'measure':
                                            ?>
                                            {
                                    actions: ["-"],
                                    actionTarget: "map.tbar"
                                },
                                            {
                                                    ptype: "gxp_measure",
                                                    toggleGroup: "measure",
                                    actionTarget: "map.tbar"
                                            },
                                            <?php 
                                            break;
                                    case 'addlayer':
                                            ?>
                                            {
                                    ptype: "gxp_addlayers",
                                    actionTarget: "tree.tbar"
                                },
                                            <?php 
                                            break;
                                    case 'removelayer':
                                            ?>
                                {
                                   ptype: "gxp_removelayer",
                                   actionTarget: ["tree.tbar", "tree.contextMenu"]
                                },
                                            <?php 
                                            break;

                                    case 'layerproperties':
                                            ?>
                                            {
                                                    ptype: "gxp_layerproperties",
                                                    id: "layerproperties",
                                                    actionTarget: ["tree.tbar", "tree.contextMenu"]
                                            },
                                            <?php 
                                            break;

                                    case 'getfeatureinfo':
                                            ?>

                                            {
                                                    ptype: "gxp_wmsgetfeatureinfo",
                                                    popupTitle: "Feature Info", 
                                                    toggleGroup: "interaction", 
                                                    format: "html", 
                                                    actionTarget: "hiddentbar",
                                                    defaultAction: 0
                                            },

                                            <?php 
                                            break;
                                    case 'googlegeocoder':
                                            ?>
                                            {
                                    actions: ["-"],
                                    actionTarget: "map.tbar"
                                },
                                            {
                                                    ptype: "gxp_googlegeocoder",
                                                    outputTarget: "map.tbar"
                                            },
                                            <?php
                                            break;
                                    case 'print':
                                            if(!$this->params->get('printserviceurl'))
                                                    continue;
                                            else 
                                            ?>
                                            {
                                    actions: ["-"],
                                    actionTarget: "map.tbar"
                                },
                                            {
                                                    ptype: "sdi_gxp_print",
                                                    customParams: {outputFilename: 'GeoExplorer-print'},
                                                printService: "<?php echo $this->params->get('printserviceurl');?>",
                                                printURL: "<?php if($this->params->get('printserviceprinturl')=='') echo $this->params->get('printserviceurl').'print.pdf'; else  echo $this->params->get('printserviceprinturl');?>",
                                                createURL: "<?php if($this->params->get('printservicecreateurl') == '') echo  $this->params->get('printserviceurl').'create.json'; else  echo $this->params->get('printservicecreateurl');?>",
                                                includeLegend: true, 
                                                actionTarget: "map.tbar",
                                                showButtonText: false
                                            },
                                            <?php
                                            break;
                            }
                    }
                    ?>
                    ],

                    // layer sources
                    <?php
                                    switch ($this->item->defaultserviceconnector_id)
                    {
                            case 2 :
                            ?>
                            defaultSourceType: "gxp_wmssource",
                            <?php
                            break;
                        case 11 :
                            ?>
                            defaultSourceType: "gxp_wmscsource",
                            <?php
                            break;
                    }
                    ?>

                    sources: 
                    {
                            "ol": { ptype: "gxp_olsource" }, 
                            <?php
                        foreach ($this->item->physicalservices as $service)
                            {
                                    //Acces not allowed
                                    if(!in_array($service->access, $user->getAuthorisedViewLevels()))
                                            continue;
                                            switch ($service->serviceconnector_id)
                                    {
                                            case 2 :
                                                    ?>
                                                    "<?php echo $service->alias ?>":
                                                    {
                                                    ptype: "gxp_wmssource",
                                                    url: "<?php echo $service->resourceurl;?>"
                                                    },
                                                    <?php
                                                    break;
                                            case 11 :
                                                    ?>
                                                    "<?php echo $service->alias ?>":
                                                    {
                                                    ptype: "gxp_wmscsource",
                                                    url: "<?php echo $service->resourceurl;?>"
                                                    },
                                                    <?php
                                                    break;
                                            case 12 :
                                                    ?>
                                                    "<?php echo $service->alias ?>":
                                                    {
                                                    ptype: "sdi_gxp_bingsource"
                                                    },
                                                    <?php
                                                    break;
                                            case 13 : 
                                                    ?>
                                                    "<?php echo $service->alias ?>":
                                                    {
                                                    ptype: "sdi_gxp_googlesource"
                                                    },
                                                    <?php
                                                    break;
                                            case 14 :
                                                    ?>
                                                    "<?php echo $service->alias ?>":
                                                    {
                                                    ptype: "sdi_gxp_osmsource"
                                                    },
                                                    <?php
                                                    break;
                                    }

                            }
                            if(isset($this->item->virtualservices)){
                                    foreach ($this->item->virtualservices as $service)
                                    {
                                            switch ($service->serviceconnector_id)
                                            {
                                                    case 2 :
                                                    ?>
                                                    "<?php echo $service->alias ?>":
                                                    {
                                                        ptype: "gxp_wmssource",
                                                        url: "<?php echo $service->url;?>"
                                                    },
                                                <?php
                                                break;
                                                case 11 :
                                                ?>
                                                    "<?php echo $service->alias ?>":
                                                    {
                                                        ptype: "gxp_wmscsource",
                                                        url: "<?php echo $service->url;?>"
                                                    },
                                                <?php
                                            }

                                    }
                                            }
                            ?>

                    },

                    // map and layers
                    map: 
                    {
                        id: "sdimap", // id needed to reference map in portalConfig above
                        title: "Map",
                        header:false,
                        projection: "<?php echo $this->item->srs;?>",
                        center: [<?php echo $this->item->centercoordinates;?>],
                        maxExtent : [<?php echo $this->item->maxextent;?>],
                        restrictedExtent: [<?php echo $this->item->maxextent;?>],
                            maxResolution: <?php echo $this->item->maxresolution;?>,
                            units: "<?php echo $this->item->unit;?>",
                        layers: 
                        [

                         <?php
                         //Layers have to be added the lowest before the highest
                         //To do that, the groups have to be looped in reverse order
                         $groups_reverse = array_reverse($this->item->groups);
                         foreach ($groups_reverse as $group)
                         {
                            //Acces not allowed
                            if(!in_array($group->access, $user->getAuthorisedViewLevels()))
                                    continue;

                            if(!empty ($group->layers) )
                            {
                                    foreach ($group->layers as $layer)
                                    {
                                            //Acces not allowed
                                            if(!in_array($layer->access, $user->getAuthorisedViewLevels()))
                                                    continue;

                                            if($layer->asOL || $layer->serviceconnector == 'WMTS')
                                            {
                                                    switch ($layer->serviceconnector)
                                                    {
                                                            case 'WMTS' :
                                                                    ?>
                                                                    {
                                                                            source: "ol",
                                                                        type: "OpenLayers.Layer.WMTS",
                                                                        args: [
                                                                            {
                                                                                    name:"<?php echo $layer->name;?>", 
                                                                                url : "<?php echo $layer->serviceurl;?>", 
                                                                                layer: "<?php echo $layer->layername;?>", 
                                                                                visibility: <?php  if ($layer->isdefaultvisible == 1) echo "true"; else echo "false"; ?>,
                                                                                singleTile: <?php if ($layer->istiled == 1) echo "true"; else echo "false"; ?>,
                                                                                transitionEffect: 'resize',
                                                                                opacity: <?php echo $layer->opacity;?>,
                                                                                    style: "<?php echo $layer->asOLstyle;  ?>",
                                                                                    matrixSet: "<?php echo $layer->asOLmatrixset;  ?>",
                                                                                <?php if (!empty($layer->metadatalink)){?>
                                                                                    metadataURL: "<?php echo $layer->metadatalink;  ?>",
                                                                                        <?php }?>
                                                                                    <?php 
                                                                    echo  $layer->asOLoptions;
                                                                    ?>
                                                                             }
                                                                         ],
                                                                         group: "<?php if($group->isbackground)echo 'background'; else echo $group->alias;?>"
                                                                     },
                                                                     <?php
                                                                    break;
                                                            case 'WMS' : 
                                                                    ?>
                                                                    {
                                                                            source : "ol",
                                                                            type : "OpenLayers.Layer.WMS",
                                                                            args: 
                                                                    [
                                                                                                            "<?php echo $layer->name;?>",
                                                                                                            "<?php echo $layer->serviceurl;?>",
                                                                                                            {

                                                                                                                    layers: "<?php echo $layer->layername;?>", 
                                                                                                                    version: "<?php echo $layer->version;  ?>"
                                                                                                            },
                                                                                                            {
                                                                                                                     visibility: <?php  if ($layer->isdefaultvisible == 1) echo "true"; else echo "false"; ?>,
                                                                                                                     singleTile: <?php if ($layer->istiled == 1) echo "true"; else echo "false"; ?>,
                                                                                                                     opacity: <?php echo $layer->opacity;?>,
                                                                                                                     transitionEffect: 'resize',
                                                                                                                     style: "<?php echo $layer->asOLstyle;  ?>",
                                                                                                                     <?php 
                                                                                                                     if (!empty($layer->metadatalink)){
                                                                                                                     ?>
                                                                                                    metadataURL: "<?php echo $layer->metadatalink;  ?>",
                                                                                             <?php }?>
                                                                                            <?php echo  $layer->asOLoptions; ?>
                                                                                                            }
                                                                    ],
                                                                    group: "<?php if($group->isbackground)echo 'background'; else echo $group->alias;?>"
                                                                    },
                                                                    <?php 
                                                                    break;
                                                            case 'WMSC' :
                                                                    ?>
                                                                    {
                                                                            source : "ol",
                                                                        type : "OpenLayers.Layer.WMS",
                                                                        args: 
                                                                        [
                                                                                    "<?php echo $layer->name;?>",
                                                                                    "<?php echo $layer->serviceurl;?>",
                                                                                    {
                                                                                            layers: "<?php echo $layer->layername;?>", 
                                                                                            version: "<?php echo $layer->version;  ?>",
                                                                                            tiled: true
                                                                                    },
                                                                                    {
                                                                                            visibility: <?php  if ($layer->isdefaultvisible == 1) echo "true"; else echo "false"; ?>,
                                                                                            singleTile: <?php if ($layer->istiled == 1) echo "true"; else echo "false"; ?>,
                                                                                            opacity: <?php echo $layer->opacity;?>,
                                                                                            transitionEffect: 'resize',
                                                                                            style: "<?php echo $layer->asOLstyle;  ?>",
                                                                                            <?php 
                                                                                            if (!empty($layer->metadatalink)){
                                                                                            ?>
                                                                                            metadataURL: "<?php echo $layer->metadatalink;  ?>",
                                                                                            <?php }?>
                                                                                            <?php echo  $layer->asOLoptions; ?>
                                                                                            }
                                                                         ],
                                                                        group: "<?php if($group->isbackground)echo 'background'; else echo $group->alias;?>"
                                                                    },
                                                                    <?php 
                                                                    break;
                                                    }
                                            }
                                            else 
                                            {
                                                    switch ($layer->serviceconnector)
                                                    {
                                                            case 'WMTS':
                                                                     break;
                                                                                    default :
                                                                                            ?>
                                                                                            {
                                                                            source: "<?php echo $layer->servicealias;  ?>",
                                                                            //tiled value gives the transitionEffect value see WMSSource.js l.524
                                                                            tiled: <?php if ($layer->istiled == 1) echo "true"; else echo "false"; ?>,
                                                                            <?php if (!empty($layer->version)){?>
                                                                            version: "<?php echo $layer->version;  ?>",
                                                                                                    <?php }?>
                                                                            <?php if (!empty($layer->metadatalink)){?>
                                                                            metadataURL: "<?php echo $layer->metadatalink;  ?>",
                                                                            <?php }?>
                                                                            name: "<?php echo $layer->layername;?>",
                                                                            title: "<?php echo $layer->name;?>",
                                                                            group: "<?php if($group->isbackground)echo 'background'; else echo $group->alias;?>",
                                                                            <?php if ($group->alias == "background") echo "fixed: true,";?>
                                                                            visibility: <?php  if ($layer->isdefaultvisible == 1) echo "true"; else echo "false"; ?>,
                                                                            opacity: <?php echo $layer->opacity;?>
                                                                    },
                                                                    <?php
                                                                    break;
                                                                            }
                                                                    }	
                                                            }
                            }
                         } 
                         ?>
                        ]
                    }
                    ,
                    mapItems: 
                    [
                            {
                            xtype: "gx_zoomslider",
                            vertical: true,
                            height: 100
                            },
                            {
                                    xtype: "gxp_scaleoverlay"
                        }
                    ],
                    mapPlugins:
                    [
                                            {
                                                ptype: 'sdi_gxp_loadingindicator',
                                                loadingMapMessage: '<?php echo JText::_('COM_EASYSDI_MAP_LAYER_LOAD_MESSAGE');?>'
                                            }
                    ]
                }";
    }
    */
    
    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($id = null) {
        if ($this->_item === null) {
            $this->_item = false;

            if (empty($id)) {
                $id = $this->getState('map.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {

                if ($table->state != 1)
                    return $this->_item;

                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(1);
                $this->_item = JArrayHelper::toObject($properties, 'JObject');

                //Get the unit value
                $db = JFactory::getDbo();
                $db->setQuery('SELECT alias FROM #__sdi_sys_unit WHERE id=' . $this->_item->unit_id);
                try {
                    $unit = $db->loadResult();
                    $this->_item->unit = $unit;
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }

                //Load the groups
                $groupTable = JTable::getInstance('group', 'easysdi_mapTable');
                if ($groups = $groupTable->loadIdsByMapId($id)) {
                    $this->_item->groups = array();
                    foreach ($groups as $group) {
                        $groupTable = JTable::getInstance('group', 'easysdi_mapTable');
                        $groupTable->loadWithLayers($group->id, true);
                        $groupTable->isbackground = $group->isbackground;
                        $groupTable->isdefault = $group->isdefault;
                        $this->_item->groups[] = $groupTable;
                    }
                }

                //Load the services
                $physicalserviceTable = JTable::getInstance('physicalservice', 'easysdi_serviceTable');
                if ($services = $physicalserviceTable->loadIdsByMapId($id)) {
                    $this->_item->physicalservices = array();
                    if ($services) {
                        foreach ($services as $service) {
                            $physicalserviceTable = JTable::getInstance('physicalservice', 'easysdi_serviceTable');
                            $physicalserviceTable->loadWithAccessInheritance($service, true);
                            if ($physicalserviceTable->state == 0)
                                continue;
                            $this->_item->physicalservices[] = $physicalserviceTable;
                        }
                    }
                }
                $virtualserviceTable = JTable::getInstance('virtualservice', 'easysdi_serviceTable');
                if ($services = $virtualserviceTable->loadIdsByMapId($id)) {
                    $this->_item->virtualservices = array();
                    if ($services) {
                        $params = JComponentHelper::getParams('com_easysdi_service');
                        foreach ($services as $service) {
                            $virtualserviceTable = JTable::getInstance('virtualservice', 'easysdi_serviceTable');
                            $virtualserviceTable->load($service, true);
                            if (empty($virtualserviceTable->reflectedurl)) {
                                $virtualserviceTable->url = $params->get('proxyurl') . $virtualserviceTable->alias;
                            } else {
                                $virtualserviceTable->url = $virtualserviceTable->reflectedurl;
                            }
                            $this->_item->virtualservices[] = $virtualserviceTable;
                        }
                    }
                }
                //Load the tools
                $toolTable = JTable::getInstance('tool', 'easysdi_mapTable');
                if ($tools = $toolTable->loadIdsByMapId($id)) {
                    $this->_item->tools = array();
                    foreach ($tools as $tool) {
                        $toolTable = JTable::getInstance('tool', 'easysdi_mapTable');
                        $toolTable->load($tool, true);
                        $this->_item->tools[] = $toolTable;
                    }
                }
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }
        }

        return $this->_item;
    }

    public function getTable($type = 'Map', $prefix = 'Easysdi_mapTable', $config = array()) {
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to check in an item.
     *
     * @param	integer		The id of the row to check out.
     * @return	boolean		True on success, false on failure.
     * @since	1.6
     */
    public function checkin($id = null) {
        // Get the id.
        $id = (!empty($id)) ? $id : (int) $this->getState('map.id');

        if ($id) {

            // Initialise the table
            $table = $this->getTable();

            // Attempt to check the row in.
            if (method_exists($table, 'checkin')) {
                if (!$table->checkin($id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to check out an item for editing.
     *
     * @param	integer		The id of the row to check out.
     * @return	boolean		True on success, false on failure.
     * @since	1.6
     */
    public function checkout($id = null) {
        // Get the user id.
        $id = (!empty($id)) ? $id : (int) $this->getState('map.id');

        if ($id) {

            // Initialise the table
            $table = $this->getTable();

            // Get the current user object.
            $user = JFactory::getUser();

            // Attempt to check the row out.
            if (method_exists($table, 'checkout')) {
                if (!$table->checkout($user->get('id'), $id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML 
     * 
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_easysdi_map.map', 'map', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        $data = $this->getData();

        return $data;
    }

    /**
     * Method to save the form data.
     *
     * @param	array		The form data.
     * @return	mixed		The user id on success, false on failure.
     * @since	1.6
     */
    public function save($data) {
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('map.id');
        $user = JFactory::getUser();

        if ($id) {
            //Check the user can edit this item
            $authorised = $user->authorise('core.edit', 'map.' . $id);
        } else {
            //Check the user can create new items in this section
            $authorised = $user->authorise('core.create', 'com_easysdi_map');
        }

        if ($authorised !== true) {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        }

        $table = $this->getTable();
        if ($table->save($data) === true) {
            return $id;
        } else {
            return false;
        }
    }

}