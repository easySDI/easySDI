<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class Easysdi_mapViewMap extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $app = JFactory::getApplication();
        $user = JFactory::getUser();
        $this->state = $this->get('State');
        $this->item = $this->get('Data');
        $this->params = $app->getParams('com_easysdi_map');
        $this->form = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        if (!$this->item) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_MAP_MAP_NOT_FOUND'), 'error');
            return;
        }

        if (!in_array($this->item->access, $user->getAuthorisedViewLevels())) {
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'notice');
            return;
        }

        $config = '{';
        $proxyhost = $this->params->get('proxyhost');
        if (!empty($proxyhost)) {
            $config .= 'proxy :"' . $proxyhost . '"';
        }
        $config .= 'about: 
                        { 
                            title: "' . $this->item->title . '", 
                            "abstract": "' . $this->item->abstract . '
                         },
                    portalConfig: 
                        {
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
                            }
                    },                   
                    tools: [
                        {
                            ptype: "sdi_gxp_layermanager",
                             rootNodeText: "' . $this->item->rootnodetext . '",';

        foreach ($this->item->groups as $group) :
            if ($group->isdefault) {
                //Acces not allowed
                if (!in_array($group->access, $user->getAuthorisedViewLevels()))
                    break;
                $config .= 'defaultGroup: "' . $group->alias . '",';
                break;
            }
        endforeach;

        $config .= 'outputConfig: {
        id: "tree",
        border: true,
        tbar: [] 
        },
        groups: {';


        //Groups are added in the order saved in the database
        foreach ($this->item->groups as $group) :
            //Acces not allowed
            if (!in_array($group->access, $user->getAuthorisedViewLevels()))
                continue;

            if ($group->isbackground) {
                $config .= '
                "background": {
                title: "' . $group->name . '", 
                exclusive: true,';
                if ($group->isdefaultopen) :
                    $config .= 'expanded: "true"},';
                else :
                    $config .= 'expanded: "false"},';
                endif;
            }
            else {
                $config .= '"' . $group->alias . '" : {
                    title : "' . $group->name . '",';
                if ($group->isdefaultopen) :
                    $config .= 'expanded: "true"},';
                else :
                    $config .= 'expanded: "false"},';
                endif;
            }
        endforeach;

        $config .= '},';
        $config .= ' outputTarget: "westpanel"
        },';

        foreach ($this->item->tools as $tool) {
            switch ($tool->alias) {
                case 'googleearth':
                    $config .= '
                    {
                    ptype: "gxp_googleearth",
                    actionTarget: ["map.tbar", "globe.tbar"]
                    },
                    {
                    actions: ["-"],
                    actionTarget: "map.tbar"
                    },
                    ';
                    break;
                case 'navigationhistory':
                    $config .= '
                    {
                    ptype: "gxp_navigationhistory",
                    actionTarget: "map.tbar"
                    },
                    ';
                    break;
                case 'navigation':
                    $config .= '
                    {
                    ptype: "gxp_navigation",
                    actionTarget: "map.tbar", 
                    toggleGroup: "navigation"
                    },
                    ';
                    break;
                case 'zoom':
                    $config .= '
                    {
                    ptype: "gxp_zoom",
                    actionTarget: "map.tbar",
                    toggleGroup: "navigation",
                    showZoomBoxAction: true,
                    controlOptions: {zoomOnClick: false}
                    },
                    ';
                    break;
                case 'zoomtoextent':
                    $config .= '
                    {
                    ptype: "gxp_zoomtoextent",
                    actionTarget: "map.tbar"
                    },
                    {
                    ptype: "gxp_zoomtolayerextent",
                    actionTarget: {target: "tree.contextMenu", index: 0}
                    },
                    ';
                    break;
                case 'measure':
                    $config .= '
                    {
                    actions: ["-"],
                    actionTarget: "map.tbar"
                    },
                    {
                    ptype: "gxp_measure",
                    toggleGroup: "measure",
                    actionTarget: "map.tbar"
                    },
                    ';
                    break;
                case 'addlayer':
                    $config .= '
                    {
                    ptype: "gxp_addlayers",
                    actionTarget: "tree.tbar"
                    },
                    ';
                    break;
                case 'removelayer':
                    $config .= '
                    {
                    ptype: "gxp_removelayer",
                    actionTarget: ["tree.tbar", "tree.contextMenu"]
                    },
                    ';
                    break;

                case 'layerproperties':
                    $config .= '
                    {
                    ptype: "gxp_layerproperties",
                    id: "layerproperties",
                    actionTarget: ["tree.tbar", "tree.contextMenu"]
                    },
                    ';
                    break;

                case 'getfeatureinfo':
                    $config .= '
                    {
                    ptype: "gxp_wmsgetfeatureinfo",
                    popupTitle: "Feature Info", 
                    toggleGroup: "interaction", 
                    format: "html", 
                    actionTarget: "hiddentbar",
                    defaultAction: 0
                    },

                    ';
                    break;
                case 'googlegeocoder':
                    $config .= '
                    {
                    actions: ["-"],
                    actionTarget: "map.tbar"
                    },
                    {
                    ptype: "gxp_googlegeocoder",
                    outputTarget: "map.tbar"
                    },
                    ';
                    break;
                case 'print':
                    if (!$this->params->get('printserviceurl'))
                        continue;
                    else
                        $config .= '
                    {
                    actions: ["-"],
                    actionTarget: "map.tbar"
                    },
                    {
                    ptype: "sdi_gxp_print",
                    customParams: {outputFilename: "GeoExplorer-print"},
                    printService: "' . $this->params->get('printserviceurl') . '",';
                    if ($this->params->get('printserviceprinturl') == '')
                        $config .= 'printURL : "' . $this->params->get('printserviceurl') . 'print.pdf",';
                    else
                        $config .= 'printURL : "' . $this->params->get('printserviceprinturl') . '",';
                    if ($this->params->get('printservicecreateurl') == '')
                        $config .= ' createURL : "' . $this->params->get('printserviceurl') . 'create.json",';
                    else
                        $config .= ' createURL : "' . $this->params->get('printservicecreateurl') . '",';

                    $config .= 'includeLegend: true, 
                    actionTarget: "map.tbar",
                    showButtonText: false
                    },
                    ';
                    break;
            }
        }
        $config .= '
        ],';

        // layer sources
        switch ($this->item->defaultserviceconnector_id) {
            case 2 :
                $config .= '
                defaultSourceType: "gxp_wmssource",
                ';
                break;
            case 11 :
                $config .= '
                defaultSourceType: "gxp_wmscsource",
                ';
                break;
        }

        $config .= '
        sources: 
        {
        "ol": { ptype: "gxp_olsource" }, ';

        foreach ($this->item->physicalservices as $service) {
            //Acces not allowed
            if (!in_array($service->access, $user->getAuthorisedViewLevels()))
                continue;
            switch ($service->serviceconnector_id) {
                case 2 :
                    $config .= ' 
                    "' . $service->alias . '":
                    {
                    ptype: "gxp_wmssource",
                    url: "' . $service->resourceurl . '"
                    },
                    ';
                    break;
                case 11 :
                    $config .= ' 
                    "' . $service->alias . '":
                    {
                    ptype: "gxp_wmscsource",
                     url: "' . $service->resourceurl . '"
                    },
                    ';
                    break;
                case 12 :
                    $config .= ' 
                    "' . $service->alias . '":
                    {
                    ptype: "sdi_gxp_bingsource"
                    },
                    ';
                    break;
                case 13 :
                    $config .= ' 
                    "' . $service->alias . '":
                    {
                    ptype: "sdi_gxp_googlesource"
                    },
                    ';
                    break;
                case 14 :
                    $config .= ' 
                    "' . $service->alias . '":
                    {
                    ptype: "sdi_gxp_osmsource"
                    },
                    ';
                    break;
            }
        }
        if (isset($this->item->virtualservices)) {
            foreach ($this->item->virtualservices as $service) {
                switch ($service->serviceconnector_id) {
                    case 2 :
                        $config .= ' 
                    "' . $service->alias . '":
                        {
                        ptype: "gxp_wmssource",
                        url: "<?php echo $service->url; ?>"
                        },
                        ';
                        break;
                    case 11 :
                        $config .= ' 
                    "' . $service->alias . '":
                        {
                        ptype: "gxp_wmscsource",
                        url: "<?php echo $service->url; ?>"
                        },
                    ';
                }
            }
        }
        $config .= ' 
        },

        // map and layers
        map: 
        {
        id: "sdimap", // id needed to reference map in portalConfig above
        title: "Map",
        header:false,
        projection: "' . $this->item->srs . '",
        center: [' . $this->item->centercoordinates . '],
        maxExtent : [' . $this->item->maxextent . '],
        restrictedExtent: [' . $this->item->maxextent . '],
        maxResolution: ' . $this->item->maxresolution . ',
        units: "' . $this->item->unit . '",
        layers: 
        [
        ';

        //Layers have to be added the lowest before the highest
        //To do that, the groups have to be looped in reverse order
        $groups_reverse = array_reverse($this->item->groups);
        foreach ($groups_reverse as $group) {
            //Acces not allowed
            if (!in_array($group->access, $user->getAuthorisedViewLevels()))
                continue;

            if (!empty($group->layers)) {
                foreach ($group->layers as $layer) {
                    //Acces not allowed
                    if (!in_array($layer->access, $user->getAuthorisedViewLevels()))
                        continue;

                    if ($layer->asOL || $layer->serviceconnector == 'WMTS') {
                        switch ($layer->serviceconnector) {
                            case 'WMTS' :
                                $config .= ' 
                                {
                                source: "ol",
                                type: "OpenLayers.Layer.WMTS",
                                args: [
                                {
                                name:"' . $layer->name . '", 
                                url : "' . $layer->serviceurl . '", 
                                layer: "' . $layer->layername . '", ';

                                if ($layer->isdefaultvisible == 1)
                                    $config .= 'visibility: "true",';
                                else
                                    $config .= 'visibility: "false",';
                                if ($layer->istiled == 1)
                                    $config .= 'singleTile: "true"';
                                else
                                    $config .= 'singleTile: "false"';

                                $config .= 'transitionEffect: "resize",
                                opacity: ' . $layer->opacity . ',
                                style: "' . $layer->asOLstyle . '",
                                matrixSet: "' . $layer->asOLmatrixset . '",';
                                if (!empty($layer->metadatalink)) {
                                    $config .= 'metadataURL: "' . $layer->metadatalink . '",';
                                }

                                $config .= $layer->asOLoptions;

                                $config .=' }
                                ],';
                                if ($group->isbackground)
                                    $config .= 'group: "background"';
                                else
                                    $config .= 'group: "' . $group->alias . '"';
                                $config .='
                                },
                                ';
                                break;
                            case 'WMS' :
                                $config .= ' 
                                {
                                source : "ol",
                                type : "OpenLayers.Layer.WMS",
                                args: 
                                [
                                "' . $layer->name . '",
                                "' . $layer->serviceurl . '",
                                {
                                layers: "' . $layer->layername . '", 
                                version: "' . $layer->version . '"
                                },
                                {';


                                if ($layer->isdefaultvisible == 1)
                                    $config .= 'visibility :  "true"';
                                else
                                    $config .= 'visibility :  "false"';
                                $config .= ',';

                                if ($layer->istiled == 1)
                                    $config .= 'singleTile :  "true"';
                                else
                                    $config .= 'singleTile :  "false"';
                                $config .=',
                                opacity: ' . $layer->opacity . ',
                                transitionEffect: "resize",
                                style: "' . $layer->asOLstyle . '",';

                                if (!empty($layer->metadatalink)) {
                                    $config .='
                                    metadataURL: "' . $layer->metadatalink . '",';
                                }
                                $config .= '}';
                                $config .= $layer->asOLoptions;
                                $config .= '}
                                ],';

                                if ($group->isbackground)
                                    $config .= ' group : "background"';
                                else
                                    $config .= ' group : "' . $group->alias . '"';
                                $config .= '
                                },
                                ';
                                break;
                            case 'WMSC' :
                                $config .= ' 
                                {
                                source : "ol",
                                type : "OpenLayers.Layer.WMS",
                                args: 
                                [
                                "' . $layer->name . '",
                                "' . $layer->serviceurl . '",
                                {
                                layers: "' . $layer->layername . '", 
                                version: "' . $layer->version . '",
                                tiled: true
                                },
                                {';

                                if ($layer->isdefaultvisible == 1)
                                    $config .= 'visibility :  "true",';
                                else
                                    $config .= 'visibility :  "false",';


                                if ($layer->istiled == 1)
                                    $config .= 'singleTile :  "true",';
                                else
                                    $config .= 'singleTile :  "false",';

                                $config .= 'opacity: ' . $layer->opacity . ',
                                transitionEffect: "resize",
                                style: "' . $layer->asOLstyle . '",';

                                if (!empty($layer->metadatalink)) {
                                    $config .= '
                                    metadataURL: "' . $layer->metadatalink . '",';
                                }
                                $config .=$layer->asOLoptions;

                                $config .= '}],';
                                if ($group->isbackground)
                                    $config .= ' group : "background"';
                                else
                                    $config .= ' group : "' . $group->alias . '"';
                                $config .= '
                                },
                                ';
                                break;
                        }
                    }
                    else {
                        switch ($layer->serviceconnector) {
                            case 'WMTS':
                                break;
                            default :
                                $config .= '
                                {
                                source: "' . $layer->servicealias . '",
                                //tiled value gives the transitionEffect value see WMSSource.js l.524';

                                if ($layer->istiled == 1)
                                    $config .= 'tiled :  "true",';
                                else
                                    $config .= 'tiled :  "false",';

                                if (!empty($layer->version)) {
                                    $config .= 'version: "' . $layer->version . '",';
                                }
                                if (!empty($layer->metadatalink)) {
                                    $config .= 'metadataURL: "' . $layer->metadatalink . '",';
                                }
                                $config .= 'name: "' . $layer->layername . '",
                                title: "' . $layer->name . '",';
                                if ($group->isbackground)
                                    $config .= ' group : "background",';
                                else
                                    $config .= ' group : "' . $group->alias . '",';
                                if ($group->alias == "background")
                                    $config .= 'fixed: true,';

                                if ($layer->isdefaultvisible == 1)
                                    $config .= 'visibility :  "true",';
                                else
                                    $config .= 'visibility :  "false",';

                                $config .= 'opacity: ' . $layer->opacity . '
                                },
                                ';
                                break;
                        }
                    }
                }
            }
        }
        $config .= '
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
        ptype: "sdi_gxp_loadingindicator",
        loadingMapMessage: "' . JText::_('COM_EASYSDI_MAP_LAYER_LOAD_MESSAGE') . '"
}
]
';
        $config .='}';
        $dispatcher	= JEventDispatcher::getInstance();
        JPluginHelper::importPlugin('content');
        $results = $dispatcher->trigger('onContentPrepare', array('com_easysdi_map', $this->item, $config, $offset));

        $this->_prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument() {
        $app = JFactory::getApplication();
        $menus = $app->getMenu();
        $title = null;

// Because the application sets a default page title,
// we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_EASYSDI_MAP_DEFAULT_PAGE_TITLE'));
        }

//$title = $this->params->get('page_title', '');
        $title = $this->item->name;
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }

}
