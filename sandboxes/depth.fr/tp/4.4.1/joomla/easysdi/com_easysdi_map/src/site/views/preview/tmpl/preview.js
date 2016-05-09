Ext.onReady(
    function() {
        window.appname.on("ready",
            function() {
                for (index = 0; index < items.length; ++index) {
                    var preview = items[index];
                    window['sourceConfig' + preview.id] = {
                        id: preview.sourceConfig.alias,
                        ptype: preview.sourceConfig.ptype,
                        hidden: "true",
                        url: preview.sourceConfig.url
                    };
                    switch (preview.service.serviceconnector_id) {
                        case "2":
                        case "11":
                            window['layerConfig' + preview.id] = {
                                group: preview.defaultgroup.alias,
                                type: "OpenLayers.Layer.WMS",
                                name: preview.maplayer.layername,
                                attribution: preview.maplayer.attribution,
                                href: preview.href,
                                download: preview.download,
                                opacity: 1,
                                source: preview.service.alias,
                                tiled: true,
                                title: preview.maplayer.name,
                                iwidth: preview.mwidth,
                                iheight: preview.mheight,
                                isindoor: preview.maplayer.isindoor,
                                servertype: preview.service.server,
                                levelfield: preview.maplayer.levelfield,
                                visibility: true};
                            break;
                        case "3":
                            window['layerConfig' + preview.id] = {
                                group: preview.defaultgroup.alias,
                                type: "OpenLayers.Layer.WMTS",
                                name: preview.maplayer.layername,
                                source: preview.service.alias,
                                title: preview.maplayer.name,
                                args: [{
                                        name: preview.maplayer.name,
                                        layer: preview.maplayer.layername,
                                        matrixSet: preview.maplayer.asOLmatrixset,
                                        url: preview.service.resourceurl,
                                        style: preview.maplayer.asOLstyle
                                    }]
                               };
                            if (preview.maplayer.asOLoptions) {
                                var options = JSON.parse(preview.maplayer.asOLoptions);
                                for (var key in options) {
                                    var option = options[key];
                                    if (typeof option === 'string' && option.indexOf("new OpenLayers") > -1) {
                                        window['layerConfig' + preview.id].args[0][key] = eval(option);
                                    } else {
                                        window['layerConfig' + preview.id].args[0][key] = option;
                                    }
                                }
                            }
                            break;

                    }
                    var queue = window.appname.addExtraLayer(window['sourceConfig' + preview.id], window['layerConfig' + preview.id]);
                    gxp.util.dispatch(queue, window.appname.reactivate, window.appname);
                }
            })
        }
);