Ext.Container.prototype.bufferResize = false;
Ext.onReady(function() {
    startMap();
});

function startMap() {
    loadingMask = new Ext.LoadMask(Ext.getBody(), { msg: msg });
    loadingMask.show();
    height = Ext.get(renderto).getHeight();
    width = Ext.get(renderto).getWidth();
    if (!height)
        height = width * 1 / 2;

    OpenLayers.ImgPath = "administrator/components/com_easysdi_core/libraries/openlayers/img/";
    GeoExt.Lang.set(langtag);
    window.appname = new gxp.Viewer(getMapConfig());
    //Add the mouseposition control if activated in the map configuration
    //Can not be done in the gxp.Viewer instanciation because it has to be done on the openlayers map object
    if (mouseposition === 'true') {
        window.appname.mapPanel.map.addControl(new OpenLayers.Control.MousePosition());
    }
    var locator = null;
    window.appname.on("ready", function() {
        window.appname.portalConfig.renderTo = "sdiNewContainer";
        if (data.urlwfslocator) {
            if (locator === null) {
                locator = {
                    xtype: "gxp_autocompletecombo",
                    listeners: {
                        select: function(list, record) {
                            var extent = new OpenLayers.Bounds();
                            extent.extend(record.data.feature.geometry.getBounds());
                            window.appname.mapPanel.map.zoomToExtent(extent);
                        }
                    },
                    url: data.urlwfslocator,
                    fieldName: data.fieldname,
                    featureType: data.featuretype,
                    featurePrefix: data.featureprefix,
                    fieldLabel: data.fieldname,
                    geometryName: data.geometryname,
                    maxFeatures: "10",
                    emptyText: "Search..."
                };
                window.appname.portal.items.items[0].items.items[0].toolbars[0].add(locator);
                window.appname.portal.items.items[0].items.items[0].toolbars[0].doLayout();
            }
        }
        loadingMask.hide();
    });

    if (cleared === "false") {
        SdiScaleLineParams = {
            bottomInUnits: data.bottomInUnits,
            bottomOutUnits: data.bottomOutUnits,
            topInUnits: data.topInUnits,
            topOutUnits: data.topOutUnits
        };
    }
    Ext.QuickTips.init();
    Ext.apply(Ext.QuickTips.getQuickTip(), { maxWidth: 1000 });
    Ext.EventManager.onWindowResize(function() {
        //Modal has to be displayed (display=block) to allow components inside to be resized
        //and, otherwhise, component sdimapcontainer will have 0 as values for both dimensions.
        var previousCss, none = false;
        if (jQuery("#modal-perimeter").css('display') == 'none') {
            previousCss = jQuery("#modal-perimeter").attr("style");
            jQuery("#modal-perimeter")
                .css({ position: 'absolute', visibility: 'hidden', display: 'block' });
            none = true;
        }
        window.appname.portal.setSize(jQuery("#sdimapcontainer").width(), jQuery("#sdimapcontainer").height());
        if (none === true) {
            jQuery("#modal-perimeter").attr("style", previousCss ? previousCss : "");
        }
    });
}

function getMapConfig() {
    var config = {};

    config.proxy = proxyhost;
    config.about = {
        title: data.title,
        "abstract": data.abstract
    };

    //Portal config
    config.portalConfig = {
        renderTo: renderto,
        width: width,
        height: height,
        layout: "border",
        region: "center",
        items: [{
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
        }]
    };

    //tools
    var layertreeactivated = false;
    if (data.tools !== null && data.tools.length > 0) {
        for (index = 0; index < data.tools.length; ++index) {
            if (data.tools[index].alias === 'layertree') {
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
                    items: []
                });
                layertreeactivated = true;
            }
            if (data.tools[index].alias === 'getfeatureinfo') {
                config.portalConfig.items.push({
                    id: "hiddentbar",
                    xtype: "panel",
                    split: false,
                    layout: "fit",
                    height: 0,
                    region: "south",
                    items: []
                });
            }
        }
    }
    if (layertreeactivated === false) {
        config.portalConfig.items.push({
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
        outputTarget: "westpanel"
    };

    //Groups
    layermanager.groups = {};
    for (index = 0; index < groups.length; ++index) {
        layermanager.groups[groups[index].alias] = { title: groups[index].title, expanded: groups[index].expanded };
    }
    layermanager.groups['background'] = { title: backgroundname, exclusive: true, expanded: backgroundexpanded };
    config.tools.push(layermanager);
    for (index = 0; index < data.tools.length; ++index) {
        switch (data.tools[index].alias) {
            case 'googleearth':
                var tool = { ptype: "gxp_googleearth", actionTarget: ["map.tbar", "globe.tbar"] };
                config.tools.push(tool);
                config.tools.push({ actions: ["-"], actionTarget: "map.tbar" });
                break;
            case 'navigationhistory':
                var tool = {
                    ptype: "gxp_navigationhistory",
                    actionTarget: "map.tbar"
                };
                config.tools.push(tool);
                break;
            case 'navigation':
                var tool = {
                    ptype: "gxp_navigation",
                    actionTarget: "map.tbar",
                    toggleGroup: "navigation"
                };
                config.tools.push(tool);
                break;
            case 'zoom':
                var tool = {
                    ptype: "gxp_zoom",
                    actionTarget: "map.tbar",
                    toggleGroup: "navigation",
                    showZoomBoxAction: true,
                    controlOptions: { zoomOnClick: false }
                };
                config.tools.push(tool);
                break;
            case 'zoomtoextent':
                if (layertreeactivated === true) {
                    var tool = {
                        ptype: "gxp_zoomtoextent",
                        actionTarget: "map.tbar"
                    };
                    config.tools.push(tool);
                    tool = {
                        ptype: "gxp_zoomtolayerextent",
                        actionTarget: { target: "tree.contextMenu", index: 0 }
                    };
                    config.tools.push(tool);
                }
                break;
            case 'measure':
                var tool = {
                    ptype: "gxp_measure",
                    toggleGroup: "navigation",
                    actionTarget: "map.tbar"
                };
                config.tools.push({ actions: ["-"], actionTarget: "map.tbar" });
                config.tools.push(tool);
                break;
            case 'addlayer':
                if (layertreeactivated === true) {
                    var tool = {
                        ptype: "gxp_addlayers",
                        actionTarget: "tree.tbar"
                    };
                    config.tools.push(tool);
                }
                break;
            case 'searchcatalog':
                if (layertreeactivated === true) {
                    var tool = {
                        ptype: "sdi_searchcatalog",
                        actionTarget: "tree.tbar",
                        url: "index.php?preview=map&tmpl=component&option=com_easysdi_catalog&view=catalog&id=" + data.tools[index].params,
                        iwidth: mwidth,
                        iheight: mheight
                    };
                    config.tools.push(tool);
                }
                break;
            case 'layerdetailsheet':
                if (layertreeactivated === true) {
                    var tool = {
                        ptype: "sdi_layerdetailsheet",
                        actionTarget: ["tree.contextMenu"],
                        iwidth: mwidth,
                        iheight: mheight
                    };
                    config.tools.push(tool);
                }
                break;
            case 'layerdownload':
                if (layertreeactivated === true) {
                    var tool = {
                        ptype: "sdi_layerdownload",
                        actionTarget: ["tree.contextMenu"],
                        iwidth: mwidth,
                        iheight: mheight
                    };
                    config.tools.push(tool);
                }
                break;
            case 'layerorder':
                if (layertreeactivated === true) {
                    var tool = {
                        ptype: "sdi_layerorder",
                        actionTarget: ["tree.contextMenu"],
                        iwidth: mwidth,
                        iheight: mheight
                    };
                    config.tools.push(tool);
                }
                break;
            case 'removelayer':
                if (layertreeactivated === true) {
                    var tool = {
                        ptype: "gxp_removelayer",
                        actionTarget: ["tree.contextMenu"]
                    };
                    config.tools.push(tool);
                }
                break;
            case 'layerproperties':
                if (layertreeactivated === true) {
                    var tool = {
                        ptype: "gxp_layerproperties",
                        id: "layerproperties",
                        actionTarget: ["tree.contextMenu"]
                    };
                    config.tools.push(tool);
                }
                break;
            case 'getfeatureinfo':
                var tool = {
                    ptype: "gxp_wmsgetfeatureinfo",
                    //popupTitle: "Feature Info",
                    popupWidth: popup_size.popupwidth,
                    popupHeight: popup_size.popupheight,
                    toggleGroup: "interaction",
                    format: data.tools[index].params,
                    actionTarget: "hiddentbar",
                    defaultAction: 0
                };
                config.tools.push(tool);
                break;
            case 'googlegeocoder':
                config.tools.push({ actions: ["-"], actionTarget: "map.tbar" });
                var tool = {
                    ptype: "sdi_gxp_googlegeocoder",
                    outputTarget: "map.tbar"
                };
                config.tools.push(tool);
                break;
            case 'print':
                if (params.printserviceurl !== null) {
                    config.tools.push({ actions: ["-"], actionTarget: "map.tbar" });
                    var tool = {
                        ptype: "sdi_gxp_print",
                        customParams: { outputFilename: "GeoExplorer-print" },
                        printService: params.printserviceurl,
                        includeLegend: true,
                        actionTarget: "map.tbar",
                        showButtonText: false
                    };
                    if (params.printserviceprinturl === '') {
                        tool.printURL = params.printserviceurl + 'print.pdf';
                    } else {
                        tool.printURL = params.printserviceprinturl;
                    };
                    if (params.printservicecreateurl === '') {
                        tool.createURL = params.printserviceurl + 'create.json';
                    } else {
                        tool.createURL = params.printservicecreateurl;
                    };
                    config.tools.push(tool);
                }
                break;

        }
    }

    //Default source
    config.defaultSourceType = "sdi_gxp_wmssource";

    //Sources
    config.sources = { "ol": { ptype: "sdi_gxp_olsource" } };

    for (index = 0; index < services.length; ++index) {
        if (services[index].url !== null) {
            config.sources[services[index].alias] = { ptype: services[index].ptype, url: services[index].url };
        } else {
            config.sources[services[index].alias] = { ptype: services[index].ptype };
        }
    }

    //Map
    config.map = {
        id: "sdimap",
        title: "Map",
        header: false,
        projection: data.srs,
        maxExtent: JSON.parse("[" + data.maxextent + "]"),
        maxResolution: data.maxresolution,
        units: data.units
    };

    //config.map["resolutions"] = JSON.parse("[4000.0,3750.0,3500.0,3250.0,3000.0,2750.0,2500.0,2250.0,2000.0,1750.0,1500.0,1250.0,1000.0,750.0,650.0,500.0,250.0,100.0,50.0,20.0,10.0,5.0,2.5,2.0,1.5,1.0,0.5,0.25,0.1,0.05]");
    if (data.centercoordinates)
        config.map["center"] = JSON.parse("[" + data.centercoordinates + "]");
    if (data.restrictedextent)
        config.map["restrictedExtent"] = JSON.parse("[" + data.restrictedextent + "]");
    if (data.zoom)
        config.map["zoom"] = data.zoom;
    if (cleared === "true") {
        config.map.controls = [];
    }

    //Layers
    config.map.layers = [];
    for (index = 0; index < layers.length; ++index) {
        var layer = layers[index];
        switch (layer.source) {
            case 'ol':
                var openlayer;
                switch (layer.type) {
                    case 'OpenLayers.Layer.WMTS':
                        openlayer = {
                            source: "ol",
                            type: layer.type,
                            group: layer.group,
                            args: [{
                                name: layer.name,
                                url: layer.url,
                                layer: layer.layer,
                                visibility: layer.visibility,
                                singleTile: layer.singleTile,
                                transitionEffect: layer.transitionEffect,
                                opacity: layer.opacity,
                                style: layer.asOLstyle,
                                matrixSet: layer.matrixSet
                            }]
                        };
                        if (layer.asOLoptions) {
                            var options = JSON.parse(layer.asOLoptions);
                            for (var key in options) {
                                var option = options[key];
                                if (typeof option === 'string' && option.indexOf("new OpenLayers") > -1) {
                                    openlayer.args[0][key] = eval(option);
                                } else {
                                    openlayer.args[0][key] = option;
                                }
                            }
                        }
                        //"maxExtent": "new OpenLayers.Bounds(420000,30000,900000,350000)", 
                        //openlayer.args[0]['maxExtent'] = new OpenLayers.Bounds(420000,30000,900000,350000);
                        break;
                    case 'OpenLayers.Layer.WMS':
                        openlayer = {
                            source: "ol",
                            type: layer.type,
                            group: layer.group,
                            args: [
                                layer.name,
                                layer.url,
                                {
                                    layers: layer.layers,
                                    version: layer.version,
                                    tiled: true
                                },
                                {
                                    visibility: layer.visibility,
                                    singleTile: layer.singleTile,
                                    transitionEffect: layer.transitionEffect,
                                    opacity: layer.opacity
                                }
                            ]
                        };
                        if (layer.style) {
                            openlayer.args[3].style = layer.style;
                        };
                        if (layer.asOLoptions) {
                            var options = JSON.parse(layer.asOLoptions);
                            for (var key in options) {
                                var option = options[key];
                                if (typeof option === 'string' && option.indexOf("new OpenLayers") > -1) {
                                    openlayer.args[key] = eval(option);
                                } else {
                                    openlayer.args[key] = option;
                                }
                            }
                        }
                        break;
                }
                if (layer.servertype) {
                    openlayer.servertype = layer.servertype;
                };
                if (layer.isindoor) {
                    openlayer.isindoor = layer.isindoor;
                };
                if (layer.levelfield) {
                    openlayer.levelfield = layer.levelfield;
                };
                if (layer.href) {
                    openlayer.href = layer.href;
                };
                if (layer.download) {
                    openlayer.download = layer.download;
                };
                if (layer.order) {
                    openlayer.order = layer.order;
                };
                config.map.layers.push(openlayer);
                break;

            default:
                var overlay = {};
                for (var property in layer) {
                    if (layer.hasOwnProperty(property)) {
                        overlay[property] = layer[property];
                    }
                }
                config.map.layers.push(overlay);
                break;
        }
    }

    if (cleared === "false") {
        config.mapItems = [{
                xtype: "gx_zoomslider",
                aggressive: true,
                vertical: true,
                height: 100
            }


        ];

        if (data.topInUnits || data.bottomOutUnits || data.topInUnits || data.topOutUnits) {
            config.mapItems.push({
                xtype: "sdi_gxp_scaleoverlay"
            });
        }

        //Indoor navigation
        if (data.level) {
            //Levels are store in reverse order in the database 
            data.level.reverse();
            var defaultvalue;
            for (var key in data.level) {
                if (data.level[key].defaultlevel == "1") {
                    defaultvalue = parseInt(key);
                }
            }
            var maxvalue = data.level.length - 1;
            var l = 18;
            var h = (l + (l / (data.level.length))) * (data.level.length - 1);

            config.mapItems.push({
                xtype: "sdi_indoorlevelslider",
                value: defaultvalue,
                minValue: 0,
                maxValue: maxvalue,
                levels: data.level,
                increment: 1,
                isFormField: true,
                plugins: new sdi.widgets.IndoorLevelSliderTip({ template: '<div>{level}</div>', levels: data.level }),
                aggressive: false,
                vertical: true,
                height: h
            })

            var ul = document.createElement('ul');
            for (var i = data.level.length - 1; i >= 0; i--) {
                var li = Ext.DomHelper.append(ul, {
                    tag: 'li',
                    html: data.level[i].label,
                    style: { "line-height": l + 'px' }
                }, true);
                li.on({
                    click: (function(i) {
                        window.appname.mapPanel.map.indoorlevelslider.changeIndoorLevel(window.appname.mapPanel.map.indoorlevelslider, i);
                    }).createDelegate(this, [i])
                });
            }
            config.mapItems.push({
                cls: 'levellabelpanel',
                id: 'levellabelpanel',
                border: false,
                style: "position: absolute; right: 10px; top: 20px; z-index: 1000;",
                contentEl: ul
            });
        }
    }

    config.mapPlugins = [{
        ptype: "sdi_gxp_loadingindicator",
        loadingMapMessage: layermsg
    }];
    return config;
}