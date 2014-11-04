function getMapConfig (){
    var config ={};
    config.proxy = proxyhost;
    config.about =  { 
                            title: data.title, 
                            "abstract": data.abstract
                         };
    config.portalConfig =
                        {
                        renderTo:renderto,
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
                            }  
                        ]
                    };
    //tools
    var layertreeactivated = false;
    if(data.tools !== null && data.tools.length > 0){
        for(index = 0 ; index < data.tools.length; ++index){
            if(data.tools[index].alias == 'layertree' ){
                config.portalConfig.items.push({
                        id: "westpanel",
                        xtype: "panel",
                        header: false,
                        split: true,
                        collapsible: true,
                        collapseMode: "mini",
                        hideCollapseTool: true,
                        layout: "fit",
                        region: "west",
                        width: 200, 
                        items:[ ]
                    });
                    layertreeactivated = true;                    
            }
            if(data.tools[index].alias == 'getfeatureinfo' ){
                config.portalConfig.items.push(
                        {
                                id:"hiddentbar",
                                xtype:"panel",
                                split: false,
                                layout: "fit",
                                height:0,
                                region:"south",
                                items:[]
                            })
            }
        }
    }
    
    if (layertreeactivated == false){
        config.portalConfig.items.push(
                {
                        id: "westpanel",
                        xtype: "panel",
                        header: false,
                        split: false,
                        layout: "fit",
                        region: "west",
                        width: 0
                    });
    }
    config.tools = [];
    var layermanager = {
                            ptype: "sdi_gxp_layermanager",
                            rootNodeText: data.rootnodetext,
                            defaultGroup: defaultgroup,
                            outputConfig: {
                            id: "tree",
                            border: true,
                            tbar: [] 
                            },
                            outputTarget : "westpanel"
                    };
    layermanager.groups = {};
    for(index = 0 ; index < groups.length ; ++index){
        if(groups[index].alias == "background"){
            layermanager.groups[groups[index].alias] = {title : groups[index].title, expanded : groups[index].expanded, exclusive : true};
        }else{
           layermanager.groups[groups[index].alias] = {title : groups[index].title, expanded : groups[index].expanded};
        }        
    }    
    config.tools.push (layermanager);
    
    for(index = 0 ; index < data.tools.length ; ++ index){
        switch (data.tools[index].alias){
            case 'googleearth':
                var tool ={ptype: "gxp_googleearth", actionTarget: ["map.tbar", "globe.tbar"]}; 
                config.tools.push (tool); 
                config.tools.push ({actions: ["-"],actionTarget: "map.tbar"});
                break;
            case 'navigationhistory':
                var tool = {
                    ptype: "gxp_navigationhistory",
                    actionTarget: "map.tbar"
                    };
                config.tools.push (tool); 
                break;
            case 'navigation':
                var tool ={
                    ptype: "gxp_navigation",
                    actionTarget: "map.tbar", 
                    toggleGroup: "navigation"
                    };
                config.tools.push (tool); 
                break;
            case 'zoom':
                var tool ={
                    ptype: "gxp_zoom",
                    actionTarget: "map.tbar",
                    toggleGroup: "navigation",
                    showZoomBoxAction: true,
                    controlOptions: {zoomOnClick: false}
                    };
                config.tools.push (tool);
                break;
            case 'zoomtoextent':
                if(layertreeactivated ==true){
                    var tool ={
                        ptype: "gxp_zoomtoextent",
                        actionTarget: "map.tbar"
                        };
                    config.tools.push (tool);
                    tool = {
                        ptype: "gxp_zoomtolayerextent",
                        actionTarget: {target: "tree.contextMenu", index: 0}
                        };
                    config.tools.push (tool);
                }
                break;
            case 'measure':
                var tool ={
                    ptype: "gxp_measure",
                    toggleGroup: "navigation",
                    actionTarget: "map.tbar"
                    };
                config.tools.push ({actions: ["-"],actionTarget: "map.tbar"});
                config.tools.push (tool);                 
                break;
            case 'addlayer':
                if(layertreeactivated ==true){
                    var tool ={
                        ptype: "gxp_addlayers",
                        actionTarget: "tree.tbar"
                        };
                    config.tools.push (tool);
                }
                break;
            case 'searchcatalog':
                if(layertreeactivated ==true){
                    var tool ={
                        ptype: "sdi_searchcatalog",
                        actionTarget: "tree.tbar",
                        url: "index.php?option=com_easysdi_catalog&view=catalog&id=",
                        iwidth : mwidth,
                        iheight : mheight
                        };
                    config.tools.push (tool);
                }
                break;
            case 'layerdetailsheet':
                if(layertreeactivated ==true){
                var tool ={
                        ptype: "sdi_layerdetailsheet",
                        actionTarget: ["tree.contextMenu"],
                        iwidth : mwidth,
                        iheight : mheight
                        };
                        config.tools.push (tool);
                }
                break;
            case 'layerdownload':
                if(layertreeactivated ==true){
                var tool ={ptype: "sdi_layerdownload",
                        actionTarget: ["tree.contextMenu"],
                        iwidth : mwidth,
                        iheight : mheight};
                        config.tools.push (tool);
                }
                break;
            case 'layerorder':
                if(layertreeactivated ==true){
                var tool ={ptype: "sdi_layerorder",
                        actionTarget: ["tree.contextMenu"],
                        iwidth : mwidth,
                        iheight : mheight};
                        config.tools.push (tool);
                }
                break;
            case 'removelayer':
                if(layertreeactivated ==true){
                var tool ={ptype: "gxp_removelayer",
                        actionTarget: ["tree.contextMenu"]};
                        config.tools.push (tool);
                }
                break;
            case 'layerproperties':
                if(layertreeactivated ==true){
                var tool ={ptype: "gxp_layerproperties",
                        id: "layerproperties",
                        actionTarget: ["tree.contextMenu"]};
                        config.tools.push (tool);
                }
                break;
            case 'getfeatureinfo':
                var tool = {
                    ptype: "gxp_wmsgetfeatureinfo",
                    popupTitle: "Feature Info", 
                    toggleGroup: "interaction", 
                    format: "' . $tool->params . '", 
                    actionTarget: "hiddentbar",
                    defaultAction: 0
                    }
                config.tools.push (tool);                     
                break;
            case 'googlegeocoder':
                var tool = {
                    ptype: "gxp_googlegeocoder",
                    outputTarget: "map.tbar"
                    }
                config.tools.push (tool);                         
                break;
            case 'print':
                break;
                
        }
    }
 
   config.defaultSourceType = "sdi_gxp_wmssource";
   
   config.sources ={"ol": { ptype: "sdi_gxp_olsource" }};
   
    for(index = 0; index < services.length ; ++index){
        if(services[index].url != null){
            config.sources[services[index].alias] = { ptype : services[index].ptype, url : services[index].url };
        }
        else{
            config.sources[services[index].alias] = { ptype : services[index].ptype};
        }
   }
   
   //Map
   config.map ={id: "sdimap",
            title: "Map",
            header:false,
            projection: data.srs,
            maxExtent:JSON.parse("[" + data.maxextent + "]"),
            maxResolution: data.maxresolution,
            units: data.units
        };
    if(data.centercoordinates)
        config.map["center"] = JSON.parse("[" + data.centercoordinates+ "]");
    if(data.restrictedextent)
        config.map["restrictedExtent"] = JSON.parse("[" + data.restrictedextent+ "]");
    if(data.zoom)
        config.map["zoom"] = data.zoom;
    
    //Layers
config.map.layers =[];

//If not cleared
config.mapItems = [            
            {
                xtype: "gx_zoomslider",
                vertical: true,
                height: 100
            }        
            ,
            {
                xtype: "sdi_gxp_scaleoverlay"
            }
        ];
        
        config.mapPlugins = 
        [
            {
                ptype: "sdi_gxp_loadingindicator",
                loadingMapMessage: "COM_EASYSDI_MAP_LAYER_LOAD_MESSAGE"
            }
        ];
    return config;
}