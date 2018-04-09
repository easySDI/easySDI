var easySDImap;

jQuery(document).ready(function($) {

    var script_path = 'libs/easySDI_leaflet.pack/easySDI_leaflet.pack.min.js';

    var scripts = document.getElementsByTagName("script");

    var local_url = '';

    // Look through them trying to find ourselves
    for (var i = 0; i < scripts.length; i++) {
        if (scripts[i].src.indexOf(script_path) > -1) {
            var local_url = scripts[i].src.substring(0, scripts[i].src.indexOf(script_path));
        }
    }

    if (local_url.length == 0) {
        script_path = 'libs/easysdi_leaflet/easysdi_leaflet.js';
        // Look through them trying to find ourselves
        for (var i = 0; i < scripts.length; i++) {
            if (scripts[i].src.indexOf(script_path) > -1) {
                var local_url = scripts[i].src.substring(0, scripts[i].src.indexOf(script_path));
            }
        }
    }

    var isset = function(variable) {
        return typeof(variable) != "undefined" && variable !== null;
    };

    var addIfSet = function(obj, name, value) {
        if (isset(value)) {
            obj[name] = value;
        }
    };

    var byOrdering = function(a, b) {
        {
            var aOrder = isset(a.ordering) ? parseInt(a.ordering) : 0;
            var bOrder = isset(b.ordering) ? parseInt(b.ordering) : 0;
            return ((aOrder < bOrder) ? -1 : ((aOrder > bOrder) ? 1 : 0));
        }
    }

    var projectedToLatLng = function(y, x, crs) {
        var projected = L.point(y, x).divideBy(6378137); //6378137 sphere radius
        return crs.projection.unproject(projected);
    };

    var LatLngFromString = function(string, crs) {

        var v = string.split(',');
        var nb_coords = v.length / 2;
        if (nb_coords != Math.round(nb_coords)) {
            console.log('ERROR: nb impair de coordonnées ' + v.length);
            return null;
        }
        if (nb_coords == 1) {
            if (crs !== undefined) {
                return projectedToLatLng(v[0], v[1], crs);
            } else {
                return L.latLng(v[1], v[0]);
            }
        }

        if (nb_coords > 1) {
            var res = [];
            for (i = 0; i <= nb_coords - 1; i++) {
                if (crs !== undefined) {
                    res.push(projectedToLatLng(v[i * 2], v[i * 2 + 1], crs));
                } else {
                    res.push(L.latLng(v[i * 2 + 1], v[i * 2]));
                }
            }
            return res;
        }
    };



    //https://gist.github.com/aymanfarhat/5608517
    function urlObject(options) {
        "use strict";
        /*global window, document*/

        var url_search_arr,
            option_key,
            i,
            urlObj,
            get_param,
            key,
            val,
            url_query,
            url_get_params = {},
            a = document.createElement('a'),
            default_options = {
                'url': window.location.href,
                'unescape': true,
                'convert_num': true
            };

        if (typeof options !== "object") {
            options = default_options;
        } else {
            for (option_key in default_options) {
                if (default_options.hasOwnProperty(option_key)) {
                    if (options[option_key] === undefined) {
                        options[option_key] = default_options[option_key];
                    }
                }
            }
        }

        a.href = options.url;
        url_query = a.search.substring(1);
        url_search_arr = url_query.split('&');

        if (url_search_arr[0].length > 1) {
            for (i = 0; i < url_search_arr.length; i += 1) {
                get_param = url_search_arr[i].split("=");

                if (options.unescape) {
                    key = decodeURI(get_param[0]);
                    val = decodeURI(get_param[1]);
                } else {
                    key = get_param[0];
                    val = get_param[1];
                }

                if (options.convert_num) {
                    if (val.match(/^\d+$/)) {
                        val = parseInt(val, 10);
                    } else if (val.match(/^\d+\.\d+$/)) {
                        val = parseFloat(val);
                    }
                }

                if (url_get_params[key] === undefined) {
                    url_get_params[key] = val;
                } else if (typeof url_get_params[key] === "string") {
                    url_get_params[key] = [url_get_params[key], val];
                } else {
                    url_get_params[key].push(val);
                }

                get_param = [];
            }
        }

        urlObj = {
            protocol: a.protocol,
            hostname: a.hostname,
            host: a.host,
            port: a.port,
            hash: a.hash.substr(1),
            pathname: a.pathname,
            search: a.search,
            parameters: url_get_params
        };

        return urlObj;
    }



    // Changes XML to JSON
    function xmlToJson(xml) { //http://davidwalsh.name/convert-xml-json

        // Create the return object
        var obj = {};

        if (xml.nodeType == 1) { // element
            // do attributes
            if (xml.attributes.length > 0) {
                obj["@attributes"] = {};
                for (var j = 0; j < xml.attributes.length; j++) {
                    var attribute = xml.attributes.item(j);
                    obj["@attributes"][attribute.nodeName] = attribute.nodeValue;
                }
            }
        } else if (xml.nodeType == 3) { // text
            obj = xml.nodeValue;
        }

        // do children
        if (xml.hasChildNodes()) {
            for (var i = 0; i < xml.childNodes.length; i++) {
                var item = xml.childNodes.item(i);
                var nodeName = item.nodeName;
                if (typeof(obj[nodeName]) == "undefined") {
                    obj[nodeName] = xmlToJson(item);
                } else {
                    if (typeof(obj[nodeName].push) == "undefined") {
                        var old = obj[nodeName];
                        obj[nodeName] = [];
                        obj[nodeName].push(old);
                    }
                    obj[nodeName].push(xmlToJson(item));
                }
            }
        }
        return obj;
    };




    easymapServiceConnector = function(proxy) {

        var _this = {
            services: {},
            proxy: proxy,
        };

        _this.addService = function(data) {
            if (!isset(_this.services[data.servicealias])) {
                _this.services[data.servicealias] = {
                    serviceconnector: data.serviceconnector,
                    servicetype: data.servicetype,
                    serviceurl: data.serviceurl,
                    servicealias: data.servicealias
                }
                if (data.serviceconnector == 'WMS' || data.serviceconnector == 'WMTS')
                    _this.getCapabilities(data.servicealias);
            }
            return _this.services[data.servicealias];
        };



        _this.getAllServices = function(map) {
            jQuery.each(map.contextMapData.services, function(i, s) {
                //for (var i in map.contextMapData.services) {
                //var s = map.contextMapData.services[i];
                var ns = _this.addService(s);
                ns.name = s.name;
                ns.list = true;
            });
            return _this.services;
        };


        _this.getServiceLayers = function(servicealias) {
            var service = _this.services[servicealias];

            if (service.serviceconnector == 'WMS' || service.serviceconnector == 'WMTS') {
                var cap = _this.getCapabilities(servicealias);
                return getLayers(cap);
            }

            switch (service.serviceconnector) {

                case "Google":
                    return ['ROADMAP', 'SATELLITE', 'HYBRID', 'TERRAIN'];
                    break;

                case "Bing":
                    //return ['Aerial', 'AerialWithLabels', 'Road', 'collinsBart', 'ordnanceSurvey'];
                    break;

                case "OSM":
                    return ['mapnik'];
                    break;

                default:
                    return false;
            }
        };


        var loadCapabilities = function(servicealias) {
            var service = _this.services[servicealias];
            var url = service.serviceurl + '?service=' + service.serviceconnector + '&request=GetCapabilities';
            if (isset(_this.proxy)) url = proxy + encodeURIComponent(url);

            service.loading = true;

            jQuery.ajax({
                type: "GET",
                url: url,
                dataType: "text",
                success: function(xml) {
                    service.loading = false;
                    if (xml != null) {
                        service.getCapabilitiesXML = xml;
                        service.getCapabilities = new WMSCapabilities(xml).toJSON();
                        if (!isset(service.name)) updateServiceName(servicealias);
                    }
                    var evt = new CustomEvent('getCapabilities', service);
                    window.dispatchEvent(evt);
                }
            }).fail(function() {
                service.getCapabilities = false;
            });
        };


        _this.getCapabilities = function(servicealias) {
            var service = _this.services[servicealias];
            if (isset(service.getCapabilities)) return service.getCapabilities;
            if (!isset(service.loading))
                loadCapabilities(servicealias);

            return null;
        };



        var getLayers = function(capabilities) {


            if (capabilities == null) return null;
            var res = [];

            var recurLayer = function(layer, pretitle, niv) {
                if (!isset(niv)) niv = 0;
                if (!isset(pretitle)) pretitle = '';
                if (isset(layer.Layer)) {
                    jQuery.each(layer.Layer, function(i, l) {
                        recurLayer(l, (niv > 1) ? pretitle + layer.Title + ' ' : pretitle, niv + 1);
                    });
                } else {
                    layer.Group = pretitle;
                    res.push(layer);
                }
            }


            if (isset(capabilities.Capability)) {
                var Layers = jQuery.extend({}, capabilities.Capability.Layer);
                recurLayer(Layers, '', 0);
            }

            return res;
        }

        var _getLayerData = function(capabilities, layername) {
            var rlayer = false;
            var layers = getLayers(capabilities);
            jQuery.each(layers, function(i, lay) {
                //for (var i in layers) {
                //var lay = layers[i];
                var layname;
                if (isset(lay.Name)) {
                    layname = lay.Name;
                }
                if (layname == layername) {
                    rlayer = lay;
                }
            });
            return rlayer;
        };

        _this.getLayerData = function(capabilities, layername) {
            return _getLayerData(capabilities, layername);
        }


        _this.getLegendURL = function(layer) {
            if (layer.data.serviceconnector != 'WMS' && layer.data.serviceconnector != 'WMTS') return false;
            var cap = _this.getCapabilities(layer.data.servicealias, layer);
            if (!isset(cap) || typeof cap !== 'object') return null;

            var lay = _getLayerData(cap, layer.data.layername);

            if (isset(lay.Style)) {
                if (isset(lay.Style[0].LegendURL)) {
                    if (isset(lay.Style[0].LegendURL[0].OnlineResource)) {
                        return lay.Style[0].LegendURL[0].OnlineResource;
                    } else {
                        return lay.Style[0].LegendURL[0];
                    }
                }
            }
            return false;
        };

        _this.getLegendGraphic = function(layer, map) {
            if (layer.data.serviceconnector != 'WMS' && layer.data.serviceconnector != 'WMTS') return false;
            var service = _this.services[layer.data.servicealias];
            var crs_code = map.contextMapData.srs;
            var scale = 128 * 6378137 / (Math.pow(2, map.getZoom()));
            var url = service.serviceurl + '?service=' + service.serviceconnector + '&request=GetLegendGraphic&CRS=' + crs_code + '&LAYER=' + layer.data.layername + '&transparent=true&format=image%2Fpng&legend_options=fontAntiAliasing%3Atrue%3BfontSize%3A11%3BfontName%3AAria&scale=' + scale;

            return url;
        }

        _this.getAttribution = function(layer) {
            if (isset(layer.Layer)) return _this.getAttribution(layer.Layer[0]);
            if (!isset(layer.Attribution)) return null;
            var html = layer.Attribution['Title'];
            if (isset(layer.Attribution['LogoURL']))
                html = '<img src="' + layer.Attribution['LogoURL']['OnlineResource'] + '" alt="" />' + html;
            if (isset(layer.Attribution['OnlineResource']))
                html = '<a href="' + layer.Attribution['OnlineResource'] + '">' + html + '</a>';
            return html;
        }

        var updateServiceName = function(serviceAlias) {
            var cap = _this.services[serviceAlias].getCapabilities;
            if (isset(cap.Service))
                _this.services[serviceAlias].name = cap.Service.Title;
        }


        _this.getBBox = function(layer, map) {
            if (layer.data.serviceconnector != 'WMS' && layer.data.serviceconnector != 'WMTS') return false;
            var cap = _this.getCapabilities(layer.data.servicealias, layer);
            if (!isset(cap) || typeof cap !== 'object') return null;

            var srs = map.contextMapData.srs;
            var lay = _getLayerData(cap, layer.data.layername);

            if (isset(lay.BoundingBox)) {
                var boxes = lay.BoundingBox;

                for (var i in boxes) {
                    var attr = boxes[i];
                    if ('CRS:84' == attr.crs) {
                        var bb = LatLngFromString(attr.extent[0] + ',' + attr.extent[1] + ',' + attr.extent[2] + ',' + attr.extent[3]);
                        return L.latLngBounds(bb);
                    }
                }

                for (var i in boxes) {
                    var attr = boxes[i];
                    if (srs == attr.crs) {
                        var bb = LatLngFromString(attr.extent[0] + ',' + attr.extent[1] + ',' + attr.extent[2] + ',' + attr.extent[3], map.options.crs);
                        return L.latLngBounds(bb);
                    }
                }

            }


            return false;
        };


        var _getFeatureUrl = function(capabilities, layer, map, loc) {

            var wmsParams = {
                request: 'GetFeatureInfo',
                query_layers: layer.data.layername,
                layers: layer.data.layername,
                info_format: '',
                feature_count: 10,
                X: Math.round(loc.x),
                Y: Math.round(loc.y)
            };


            jQuery.each(capabilities.Capability.Request.GetFeatureInfo.Format, function(k, format) {
                if (format == 'text/plain' && wmsParams.info_format == '') wmsParams.info_format = format;
                if (format == 'text/html') wmsParams.info_format = format;
            });

            var bounds = map.getBounds();
            var size = map.getSize();
            var wmsVersion = parseFloat(layer.wmsParams.version);
            var crs = map.options.crs || map.options.crs;
            var projectionKey = wmsVersion >= 1.3 ? 'crs' : 'srs';
            var nw = crs.project(bounds.getNorthWest());
            var se = crs.project(bounds.getSouthEast());

            var params = {
                'width': size.x,
                'height': size.y
            };
            params[projectionKey] = crs.code; //'CRS:84';
            params.bbox = (
                wmsVersion >= 1.3 && crs === L.CRS.EPSG4326 ? [se.y, nw.x, nw.y, se.x] : [nw.x, se.y, se.x, nw.y]
            ).join(',');

            L.extend(wmsParams, params);
            var url = capabilities.Capability.Request.GetFeatureInfo.DCPType[0].HTTP.Get.OnlineResource;
            url += L.Util.getParamString(wmsParams, url);
            if (isset(_this.proxy)) url = proxy + encodeURIComponent(url);

            return url;
        }



        _this.getQueryable = function(layer) {
            if (layer.data.serviceconnector != 'WMS' && layer.data.serviceconnector != 'WMTS') return false;
            var cap = _this.getCapabilities(layer.data.servicealias, layer);
            if (!isset(cap) || typeof cap !== 'object') return null;

            var lay = _getLayerData(cap, layer.data.layername);

            if (isset(lay.queryable)) {
                if (lay.queryable == true) {
                    return true;
                }
            }

            return false;
        };


        _this.getFeatureUrl = function(layer, map, loc) {
            if (!isset(layer)) return false;
            if (layer.data.serviceconnector != 'WMS' && layer.data.serviceconnector != 'WMTS') return false;
            var cap = _this.getCapabilities(layer.data.servicealias, layer);
            if (!isset(cap) || typeof cap !== 'object') return null;

            return _getFeatureUrl(cap, layer, map, loc);

        };




        return _this;
    };




    easySDImap = function(obj, data, options) {

        var params = {

        };
        jQuery.extend(params, options);
        var _easySDImap = {};
        var map;
        var baseLayers = {};
        var overlays = {};
        var services = {};
        var mapOptions = {
            zoomControl: false,
            attributionControl: false,
            dragging: false,
            touchZoom: false,
            doubleClickZoom: false,
            scrollWheelZoom: false,
            boxZoom: false,
            keyboard: false,
        };
        var container;

        if (options == undefined) {
            options = {};
            if (options.mapoptions == undefined) {
                options.mapoptions = {};
            }
        }

        mapOptions = jQuery.extend(true, mapOptions, options.mapoptions);


        var controlLayer, controlLegend, controlFeature;
        var serviceConnector;
        var lastBaseLayer = null;

        var baseLayerGroup = '';

        var url_obj = urlObject(window.location.href);

        var lang;
        var boundsLatLng;
        var contextMapData;

        _easySDImap.params = params;

        var pushTool = function(alias, control, params) {
            _easySDImap.tools.push({
                alias: alias,
                control: control,
                params: params
            });
        }


        var init = function() {

            container = obj;

            var h = jQuery(window).height();
            container.height(h * 0.95);

            // order mapdata arrays
            contextMapData.groups.sort(byOrdering);
            contextMapData.services.sort(byOrdering);

            _easySDImap.contextMapData = contextMapData;

            serviceConnector = easymapServiceConnector(contextMapData.proxyhost);

            mapOptions.crs = setCRS(contextMapData.srs);
            mapOptions.center = LatLngFromString(contextMapData.centercoordinates, mapOptions.crs);
            mapOptions.zoom = parseFloat(contextMapData.zoom);

            var bounds = LatLngFromString(contextMapData.maxextent, mapOptions.crs);
            mapOptions.maxBounds = L.latLngBounds(bounds);
            boundsLatLng = mapOptions.maxBounds;

            map = L.map(container[0], mapOptions);
            _easySDImap.mapObj = map;

            var minZoom = map.getBoundsZoom(mapOptions.maxBounds);
            map.options.minZoom = minZoom;

            map.contextMapData = contextMapData;



            // initialisation tools



            _easySDImap.tools = [];

            // initialisation tools Hors params
            pushTool('attribution', addTool('attribution'));

            jQuery.each(contextMapData.tools, function(i, t) {
                //for (var i in contextMapData.tools) {
                //var t = contextMapData.tools[i];
                var ntool = addTool(t.alias, t.params);
                if (ntool !== false)
                    pushTool(t.alias, ntool, t.params);
            });


            /*
            fixe display print-left
            var printProvider = L.print.provider({
                method: 'GET',
                url: 'http://lebouzin/print-servlet/pdf',
                autoLoad: true,
                dpi: 90
            });*/

            /*var printControl = L.control.print({
                provider: printProvider
            });*/
            //map.addControl(printControl);




            if (!isset(controlLayer)) {
                controlLayer = L.control.groupedLayers(baseLayers, overlays, {
                    autoZIndex: false
                }).addTo(map); // creation du controller de couches
            }

            var layerorder = 1000;
            var reversegroup = "";
            // creation de couches
            jQuery.each(contextMapData.groups, function(g, group) {
                var overlay = (group.isbackground != '1');
                if (isset(group.layers)) {
                    if (group.isbackground == 1) {
                        var has_default = false;
                        for (var index = 0; index < group.layers.length; index++) {
                            var element = group.layers[index];
                            if (element.id == data.default_backgroud_layer) {
                                has_default = true;
                            }
                        }
                        if (!has_default) {
                            data.default_backgroud_layer = group.layers[0].id
                        }

                        reversegroup = group.layers;
                    } else {
                        reversegroup = group.layers.reverse();
                    }
                    jQuery.each(reversegroup, function(l, layer) {
                        var show = (layer.isdefaultvisible == '1');
                        layer.ordering = layerorder;
                        addLayer(layer, overlay, show, group.name);
                        layerorder--;
                    });
                }
            });

            var geoJsonDataObj = obj.siblings('.addGeoJson');
            if (geoJsonDataObj.length > 0) {
                var ld = L.geoJson(JSON.parse(geoJsonDataObj.html()), {
                    style: {
                        className: 'addedGeoJsonLayer'
                    }
                }); // ajouter style
                overlays['textareaGeoJson'] = ld;
                ld.addTo(map);
                geoJsonDataObj.remove();
            }



            if (isset(lastBaseLayer)) {
                if (lastBaseLayer.data.servicealias == "google") {
                    var gmap_layer = new L.Google(lastBaseLayer.data.layername);
                    map.addLayer(gmap_layer);
                } else {
                    lastBaseLayer.addTo(map);
                }
            }

            obj.addClass('easySDImap');



            // data


            _easySDImap.obj = obj;
            _easySDImap.addLayer = addLayer;
            _easySDImap.getContext = getContext;
            _easySDImap.setContext = setContext;
            _easySDImap.layers = {
                baseLayers: baseLayers,
                overlays: overlays
            };
            _easySDImap.services = services;

            //update map bbox from URL param
            if (isset(url_obj.parameters.bbox)) {
                console.info('use url bbox ' + url_obj.parameters.bbox);
                _easySDImap.setBBox(url_obj.parameters.bbox);
            }

            setTimeout(updateMapFromContext, 200);


        };

        var updateMapFromContext = function() {
            //update map context from params
            if (isset(_easySDImap.params.context)) {
                if (typeof _easySDImap.params.context != "object") {
                    eval('_easySDImap.params.context=' + _easySDImap.params.context + ';');
                }
                setContext(_easySDImap.params.context);
            }


            //update map bbox from URL param
            if (isset(url_obj.parameters.bbox)) {
                console.info('use url bbox ' + url_obj.parameters.bbox);
                _easySDImap.setBBox(url_obj.parameters.bbox);
            }
        }

        _easySDImap.setBBox = function(bbox) {
            var url_bbox = L.latLngBounds(LatLngFromString(bbox, mapOptions.crs));
            map.fitBounds(url_bbox);
        }

        var hasTool = function(toolname) {
            var rtool = false;
            jQuery.each(_easySDImap.contextMapData.tools, function(t, tool) {
                //for (var t in _easySDImap.contextMapData.tools) {
                //var tool = _easySDImap.contextMapData.tools[t];
                if (tool.alias == toolname) rtool = tool;
            });
            return rtool;
        };

        _easySDImap.getTool = function(toolname) {
            var rtool = false;
            jQuery.each(_easySDImap.tools, function(t, tool) {
                //for (var t in _easySDImap.tools) {
                // var tool = _easySDImap.tools[t];
                if (tool.alias == toolname) rtool = tool.control;
            });
            return rtool;
        }


        var addTool = function(toolname, params) {
            switch (toolname) {
                case 'googleearth':
                    return false;
                    break;

                case 'navigation':
                    return initNavigation(params);
                    break;

                case 'zoom':
                    return initZoom(params);
                    break;

                case 'navigationhistory':
                    return false;
                    break;

                case 'zoomtoextent':
                    return false;
                    break;

                case 'measure':
                    return initMeasure(params);
                    break;

                case 'googlegeocoder':
                    return initGeocoder('google', params);
                    break;

                case 'print':
                    return initPrint(params);
                    break;

                case 'addlayer':
                    return false;
                    break;

                case 'removelayer':
                    return false;
                    break;

                case 'layerproperties':
                    return false;
                    break;

                case 'getfeatureinfo':
                    return false;
                    break;

                case 'layertree':
                    return initLayertree(params);
                    break;

                case 'scaleline':
                    return initScaleline(params);;
                    break;

                case 'mouseposition':
                    return false;
                    break;

                case 'wfslocator':
                    return false;
                    break;

                case 'searchcatalog':
                    return false;
                    break;

                case 'layerdetailsheet':
                    return false;
                    break;

                case 'layerdownload':
                    return false;
                    break;

                case 'layerorder':
                    return false;
                    break;

                case 'attribution':
                    return initAttribution(params);
                    break;



                default:
                    console.info('ERROR Tool ' + toolname + ' non géré');
            }



        };

        var setCRS = function(srs) {
            if (srs == 'EPSG:3857') return L.CRS.EPSG3857;
            if (srs == 'EPSG:4326') return L.CRS.EPSG4326;
            if (srs == 'EPSG:3395') return L.CRS.EPSG3395;
            // !TODO gestion autres proj4Leaflet
            return null;
        };

        var getOloptions = function(opt) {
            if (!isset(opt) || opt == '') return [];
            var asOLoptions = JSON.parse(opt); //opt.replace('OpenLayers.', '_ImportOL.');
            //eval('var asOLoptions= {' + opt + '};');
            return asOLoptions;
            //return [];
        };

        var _ImportOL = {};
        _ImportOL.Bounds = function(b1, b2, b3, b4) {
            var bounds = LatLngFromString(b1 + ',' + b2 + ',' + b3 + ',' + b4, mapOptions.crs.crs);
            return L.latLngBounds(bounds);
        };

        var getLayersStatus = function() {
            var res = {
                baseLayers: [],
                overlays: []
            };

            jQuery.each(baseLayers, function(i, baseLayer) {
                //for (var i in baseLayers) {
                res.baseLayers.push({
                    layer: i,
                    status: isset(baseLayer._map)
                });
            });

            jQuery.each(overlays, function(g, overlay) {
                //for (var g in overlays) {
                res.overlays.push({
                    layer: g,
                    status: isset(overlay._map)
                });
            });

            return res;
        };


        var getContext = function() {
            var bbox = map.getBounds().toBBoxString();
            return {
                bbox: bbox,
                layers: getLayersStatus()
            };
        };


        var setContext = function(c) {
            var coords = c.bbox.split(',');
            coords = [
                [coords[1], coords[0]],
                [coords[3], coords[2]]
            ];
            map.fitBounds(coords);

            jQuery.each(c.layers.baseLayers, function(i, contextBaseLayer) {

                if (isset(baseLayers[contextBaseLayer])) {
                    if (contextBaseLayer.status) {
                        map.addLayer(baseLayers[contextBaseLayer.layer]);
                    } else {
                        map.removeLayer(baseLayers[contextBaseLayer.layer]);
                    }

                }
            });

            jQuery.each(c.layers.overlays, function(i2, contextOverlay) {
                if (isset(overlays[contextOverlay.layer])) {
                    if (contextOverlay.status) {
                        map.addLayer(overlays[contextOverlay.layer]);
                    } else {
                        map.removeLayer(overlays[contextOverlay.layer]);
                    }

                }
            });
        };




        var addLayer = function(data, overlay, show, group) {
            //console.info(data.name+' ['+data.serviceconnector+']');
            serviceConnector.addService(data);

            var l = null;
            switch (data.serviceconnector) {

                case 'WMS':
                    l = addWMS(data);
                    break;

                case 'WMTS':
                    l = addWMTS(data);
                    break;

                case 'OSM':
                    l = addOSM(data);
                    break;

                case 'Google':
                    l = addGoogle(data);
                    break;

                case 'Bing':
                    l = addBing(data);
                    break;

                default:
                    console.error('ERROR ' + data.serviceconnector + ' non géré');
                    return false;
            }


            if (l !== null && l !== false) {

                l.data = data;

                if (overlay === false) {
                    if (isset(group))
                        setBaseLayerGroup(group);
                    addBaseLayer(l, data.name);
                } else {
                    addOverlay(l, group, data.name);
                }

                if (show) {
                    if (isset(l.addTo)) {
                        l.addTo(map);
                    }
                }
            }
            return l;
        };

        var setBaseLayerGroup = function(group) {
            baseLayerGroup = group;
            if (isset(controlLayer.setBaseGroupName))
                controlLayer.setBaseGroupName(baseLayerGroup);
        }



        var addBaseLayer = function(layer, name) {
            if (!isset(layer.data)) layer.data = {};
            if (isset(layer.setZIndex)) {
                layer.setZIndex(1);
            }

            baseLayers[name] = layer;
            if (isset(controlLayer))
                controlLayer.addBaseLayer(layer, name);
            if (layer.data.id === contextMapData.default_backgroud_layer) {
                lastBaseLayer = layer;
            }

        };
        _easySDImap.addBaseLayer = addBaseLayer;

        var addOverlay = function(layer, group, name) {
            if (!isset(layer.data)) layer.data = {};
            overlays[name] = layer;
            if (isset(controlLayer))
                controlLayer.addOverlay(layer, name, group);
        };
        _easySDImap.addOverlay = addOverlay;


        var loadGeojson = function(url, group, name) {
            if (!isset(group)) group = url;
            if (!isset(name)) name = group;
            jQuery.getJSON(url, function(geodata) {

                var geojson_layer = L.Proj.geoJson(geodata, {
                    style: function(feature) {
                        var options = {
                            weight: 2,
                            opacity: 1
                        };
                        options.maxZoom = 50
                        return options;
                    },
                    onEachFeature: function(feature, tlayer) {

                        tlayer.on('click', function(e) {

                            var html = '<table class="table table-bordered table-striped" style="display: block; max-height: 400px; overflow: auto">';
                            jQuery.each(feature.properties, function(k, v) {
                                html += '<tr><th>' + k + '</th><td>' + v + '</td></tr>';
                            })
                            html += '</table>';

                            tlayer.bindPopup(html, {
                                maxWidth: 500
                            }).openPopup();
                        });
                    }

                });

                addOverlay(geojson_layer, group, name);
                _easySDImap.mapObj.fitBounds(geojson_layer.getBounds());
                setTimeout(function() {
                    _easySDImap.mapObj.addLayer(geojson_layer);
                }, 10);

            });

        };
        _easySDImap.loadGeojson = loadGeojson;


        var changeIGNkey = function(url, key) {
            if (isset(key))
                url = url.replace(/\.ign\.fr\/[\w]*\/geoportail\//gi, '.ign.fr/' + key + '/geoportail/');
            return url;
        }



        var addWMS = function(data) {

            var url = data.serviceurl;
            url = changeIGNkey(url, params.ignkey);

            var options = {};

            addIfSet(options, 'layers', data.layername);
            options.format = 'image/png';

            addIfSet(options, 'opacity', parseFloat(data.opacity));
            options.zIndex = 10;

            addIfSet(options, 'zIndex', parseInt(data.ordering) + 10);
            addIfSet(options, 'zIndex', data.zIndex);


            addIfSet(options, 'format', data.format);
            addIfSet(options, 'attribution', data.attribution);
            addIfSet(options, 'style', data.style);
            addIfSet(options, 'bounds', data.bounds);
            addIfSet(options, 'maxZoom', data.maxZoom);


            var o = getOloptions(data.asOLoptions);
            addIfSet(options, 'format', o.format);
            addIfSet(options, 'attribution', o.attribution);
            addIfSet(options, 'style', o.style);
            addIfSet(options, 'bounds', o.bounds);



            o.maxZoom = 24; // default leaflet TileLayermaxZoom is 18

            if (isset(o.numZoomLevels)) {
                o.maxZoom = parseInt(o.numZoomLevels) - 1;
            }
            addIfSet(options, 'maxZoom', o.maxZoom);


            options.transparent = true;
            data.TileLayer_options = options;
            options.pane = map.getPanes().tilePane;
            if (parseInt(data.istiled)) {
                return new L.tileLayer.wms(url, options);
            } else {
                return new L.nonTiledLayer.wms(url, options);
            }
        };


        var addWMTS = function(data) {

            var url = data.serviceurl;
            url = changeIGNkey(url, params.ignkey);
            var options = {};

            addIfSet(options, 'layer', data.layername);
            addIfSet(options, 'tilematrixSet', data.asOLmatrixset);

            addIfSet(options, 'opacity', parseFloat(data.opacity));
            options.zIndex = 10;
            addIfSet(options, 'zIndex', parseInt(data.ordering) + 10);

            var o = getOloptions(data.asOLoptions);

            options.format = 'image/png';
            addIfSet(options, 'format', o.format);
            addIfSet(options, 'attribution', o.attribution);
            addIfSet(options, 'style', o.style);
            addIfSet(options, 'bounds', o.bounds);

            if (isset(o.numZoomLevels)) {
                o.maxZoom = parseInt(o.numZoomLevels) - 1;
            }
            addIfSet(options, 'maxZoom', o.maxZoom);

            if (isset(o.maxResolution)) {
                o.minZoom = parseInt(o.maxResolution);
                o.minZoom = o.maxZoom - Math.floor(Math.log(o.minZoom) / Math.log(2)) + 1; // calcule minZoom level a partir de maxResolution
            }
            addIfSet(options, 'minZoom', o.minZoom);

            addIfSet(options, 'matrixIds', o.matrixIds);

            if (isset(o.matrixIds)) {
                options.topLeftCorner = new L.LatLng(20037508, -20037508);
            }


            if (isset(options.topLeftCorner)) {
                jQuery.each(options.matrixIds, function(m, matrixId) {
                    //for (var m in options.matrixIds) {
                    options.matrixIds[m] = {
                        //identifier: options.matrixIds[m],
                        identifier: matrixId,
                        topLeftCorner: options.topLeftCorner
                    };
                });
            }

            addIfSet(options, 'format', data.format);
            addIfSet(options, 'attribution', data.attribution);
            addIfSet(options, 'style', data.style);
            addIfSet(options, 'bounds', data.bounds);
            addIfSet(options, 'maxZoom', data.maxZoom);

            data.TileLayer_options = options;

            return new L.TileLayer.WMTS(url, options);
        };




        var addOSM = function(data) {
            return L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            });
        };


        var addGoogle = function(data) {
            if (typeof(google) !== 'undefined') {
                return new L.Google(data.layername);
            }
            return false;
        };

        var addBing = function(data) {
            /*if (isset(L.BingLayer))
            return new L.BingLayer(data.layername);*/
            return false;
        };

        //**********
        // Navigation
        // *********

        var initNavigation = function(params) {
            var options = {
                position: 'topleft',
                zoomInTitle: i18n.t('tools_tooltips.zoomInTitle'),
                zoomOutTitle: i18n.t('tools_tooltips.zoomOutTitle')
            };
            jQuery.extend(options, params);

            map.dragging.enable();
            map.touchZoom.enable();
            map.doubleClickZoom.enable();
            map.scrollWheelZoom.enable();
            map.boxZoom.enable();
            map.keyboard.enable();
            //map.tap.enable();

            var tool = L.control.zoom(options);
            tool.addTo(map);
            return tool;
        };


        //**********
        // Zoom
        // *********

        var initZoom = function(params) {
            var options = {
                position: 'topleft',
                title: i18n.t('tools_tooltips.zoomBoxTitle')
            };
            jQuery.extend(options, params);
            var tool = L.control.zoomBox(options); //https://github.com/consbio/Leaflet.ZoomBox
            tool.addTo(map);
            return tool;
        };


        //**********
        // Measure
        // *********
        var initMeasure = function(params) {
            var options = {
                position: 'topright',
                activeColor: '#D9534F',
                completedColor: '#5BC0DE'
            };
            jQuery.extend(options, i18n.t('measure', {
                returnObjectTrees: true
            }));
            jQuery.extend(options, params);
            var tool = L.control.measure(options); //https://github.com/answerquest/leaflet-measure/tree/1b4776a7ce3a25170f5c66bf2ccbd927bf568abd
            tool.addTo(map);
            return tool;
        };



        //**********
        // Attribution
        // *********
        var initAttribution = function(params) {
            var options = {
                position: 'bottomright',
                prefix: false
            };
            jQuery.extend(options, params);
            var tool = L.control.attribution(options);
            tool.addTo(map);
            return tool;
        };



        //**********
        // Geocoder
        // *********
        var initGeocoder = function(provider, params) {
            var options = {
                position: 'topleft',
                language: lang,
                bounds: boundsLatLng.toBBoxString()
            };
            jQuery.extend(options, i18n.t('geocoder', {
                returnObjectTrees: true
            }));
            jQuery.extend(options, params);


            if (provider == 'google')
                options.geocoder = new L.Control.Geocoder.Google({
                    language: options.language,
                    bounds: options.bounds
                });

            var tool = L.Control.geocoder(options); //https://github.com/perliedman/leaflet-control-geocoder

            //affichage résultat
            tool.markGeocode = function(result) {
                var bbox = result.bbox;
                map.fitBounds(bbox);
                map._geocodeMarker = new L.Marker(result.center)
                    .bindPopup(result.html || result.name)
                    .addTo(map)
                    .openPopup();
                setTimeout(function() {
                    map.removeLayer(map._geocodeMarker);
                }, 3000);
            };
            tool.addTo(map);
            return tool;
        };


        //********
        // Print
        // ******


        var initPrint = function(params) {
            var d = new Date();
            var options = {
                position: 'topright',
                copyright: '© ' + _easySDImap.contextMapData.sitename + ' ' + d.getFullYear(),
                defaulttitle: _easySDImap.contextMapData.title,
                defaultdesc: _easySDImap.contextMapData.abstract,
            };
            jQuery.extend(options, i18n.t('print', {
                returnObjectTrees: true
            }));
            jQuery.extend(options, params);

            var control = L.easyPrintControl(options);
            control.addTo(map);
            return control;
        };


        //*******
        //scaleline
        //*******

        var initScaleline = function(params) {
            var graphicScale = L.control.graphicScale({
                fill: 'hollow',
                position: 'bottomright'
            }).addTo(map);
        };


        //*******
        //layertree
        //*******

        var initLayertree = function(params) {
            var _this = {};
            var options = {
                position: 'topleft',
                title: _easySDImap.contextMapData.name,
                baseGroupName: baseLayerGroup,
                addlayer: hasTool('addlayer'),
                removelayer: hasTool('removelayer'),
                layerproperties: hasTool('layerproperties'),
                getfeatureinfo: hasTool('getfeatureinfo'),
                searchcatalog: hasTool('searchcatalog'),
                layerdetailsheet: hasTool('layerdetailsheet'),
                layerdownload: hasTool('layerdownload'),
                layerorder: hasTool('layerorder'),
                defaultGroup: _easySDImap.contextMapData.default_group,
                sharelink: (_easySDImap.params.sharelink == true)

            };

            jQuery.extend(options, i18n.t('layertree', {
                returnObjectTrees: true
            }));
            jQuery.extend(options, params);

            var sidebar_html = jQuery('<div id="easysdi_leaflet_sidebar" class="sidebar collapsed">' +
                '<ul class="sidebar-tabs sidebar-tabs-top" role="tablist"></ul>' +
                '<ul class="sidebar-tabs sidebar-tabs-bottom" role="tablist"></ul>' +
                '<div class="sidebar-content active"></div>' +
                '</div>').prependTo('#easySDIMap');

            // tree
            jQuery('<li><a href="#tree" role="tab" title="' + i18n.t('tools_tooltips.layertree') + '"><i class="fa fa-bars"></i></a></li>').appendTo(sidebar_html.find('.sidebar-tabs-top'));
            _this.panelTree = jQuery('<div class="sidebar-pane" id="tree"></div>').appendTo(sidebar_html.find('.sidebar-content'));

            _this.easyLayer = easyLayer(map, options);
            controlLayer = _this.easyLayer;
            controlLayer.addTo(_this.panelTree);

            pushTool('panelTree', _this.panelTree);

            //legend
            jQuery('<li><a href="#legend" role="tab" title="' + i18n.t('tools_tooltips.legend') + '"><i class="fa fa-newspaper-o"></i></a></li>').appendTo(sidebar_html.find('.sidebar-tabs-top'));
            _this.panelLegend = jQuery('<div class="sidebar-pane" id="legend"></div>').appendTo(sidebar_html.find('.sidebar-content'));

            _this.easyLegend = easyLegend(map, controlLayer, serviceConnector, options);
            controlLegend = _this.easyLegend;
            controlLegend.addTo(_this.panelLegend);


            //addLayer
            if (options.addLayer !== false) {
                _this.easyAddLayer = easyAddLayer(_easySDImap, controlLayer, serviceConnector, options);
                sidebar_html.on('click', '.addLayerBtn', function(e) {
                    e.preventDefault();
                    var target = jQuery('<div class="addlayer_container"></div>');
                    jQuery(this).after(target).hide();
                    _this.easyAddLayer.show(target);
                });
                pushTool('addlayer', _this.easyAddLayer);
            }



            //getFeature
            if (options.getfeatureinfo !== false) {
                _this.panelFeature = jQuery('<div class="sidebar-pane" id="getfeature"></div>').appendTo(sidebar_html.find('.sidebar-content'));
                console.log("popup", data.popupheight, data.popupwidth);
                var popup_size = {};
                if (data.popupheight > 0 && data.popupheight != false) {
                    popup_size.popupheight = data.popupheight;
                } else {
                    popup_size.popupheight = 200;
                }
                if (data.popupwidth > 0 && data.popupwidth != false) {
                    popup_size.popupwidth = data.popupwidth;
                } else {
                    popup_size.popupwidth = 350;
                }


                _this.easyGetFeature = easyGetFeature(map, controlLayer, serviceConnector, options, popup_size);
                controlGetFeature = _this.easyGetFeature;
                controlGetFeature.addTo(_this.panelFeature);

                map.on('click', function(e) {
                    // controlGetFeature.showPanel(_this.sidebar);
                });
                pushTool('getfeatureinfo', controlGetFeature);
            }


            //sharelink
            if (options.sharelink) {

                function htmlEncode(value) {
                    return $('<div/>').text(value).html();
                }

                function htmlDecode(value) {
                    return $('<div/>').html(value).text();
                }

                var updateShareLink = function() {
                    if (!_easySDImap.getContext) {
                        setTimeout(updateShareLink, 250);
                        return false;
                    }
                    var base_url = location.protocol + "//" + location.host;
                    var context_url = _easySDImap.params.url;
                    if ((context_url.search('http://') != 0) && (context_url.search('https://') != 0) && (context_url.search('//') != 0)) {
                        context_url = base_url + context_url;
                    }
                    var context = _easySDImap.getContext();
                    var script_path = 'libs/easySDI_leaflet.pack/easySDI_leaflet.pack.min.js';

                    var scripts = document.getElementsByTagName("script");

                    var local_url = '';

                    // Look through them trying to find ourselves
                    for (var i = 0; i < scripts.length; i++) {
                        if (scripts[i].src.indexOf(script_path) > -1) {
                            var local_url = scripts[i].src.substring(0, scripts[i].src.indexOf(script_path));
                        }
                    }

                    if (local_url.length == 0) {
                        script_path = 'libs/easysdi_leaflet/easysdi_leaflet.js';
                        // Look through them trying to find ourselves
                        for (var i = 0; i < scripts.length; i++) {
                            if (scripts[i].src.indexOf(script_path) > -1) {
                                var local_url = scripts[i].src.substring(0, scripts[i].src.indexOf(script_path));
                            }
                        }
                    }


                    code = '';

                    code += '<!-- ---------------------------------- -->' + "\r\n";
                    code += '<!-- CODE A AJOUTER DANS LE BLOC <head> -->' + "\r\n";
                    code += '<!-- ---------------------------------- -->' + "\r\n";
                    code += '<link rel="stylesheet" href="' + local_url + 'libs/leaflet/leaflet.css" type="text/css"/>' + "\r\n";
                    code += '<link rel="stylesheet" href="' + local_url + 'libs/easySDI_leaflet.pack/main.css" type="text/css"/>' + "\r\n";

                    code += "\r\n";
                    code += "\r\n";

                    code += '<!-- -------------------------------------------------- -->' + "\r\n";
                    code += '<!-- CODE A AJOUTER OU VOUS SOUHAITEZ AFFICHER LA CARTE -->' + "\r\n";
                    code += '<!-- -------------------------------------------------- -->' + "\r\n";
                    code += '<div id="easySDIMap" class="easySDImapPrintBlock">' + "\r\n";
                    code += '   <div id="map" class="easySDI-leaflet sidebar-map" data-url="' + context_url + '"';
                    code += ' data-context=\'' + JSON.stringify(context) + "'";
                    if (isset(_easySDImap.params.ignkey)) code += ' data-ignkey="VOTRE_CLEF_IGN"';
                    code += '></div>' + "\r\n";
                    code += '</div>' + "\r\n";

                    code += "\r\n";


                    code += "<script> window.jQuery ||  document.write('<script src=\"https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js\"><\\\/script>');</script>" + "\r\n";
                    code += "<script> window.L ||  document.write('<script src=\"https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.5/leaflet.js\"><\\\/script>');</script>" + "\r\n";
                    code += '<script src="' + local_url + 'libs/easySDI_leaflet.pack/easySDI_leaflet.pack.js" type="text/javascript"></script>' + "\r\n";
                    if (jQuery.inArray('Google', context.layers.baseLayer) != -1 || jQuery.inArray('Google', context.layers.overlays) != -1) {
                        code += '<script src="https://maps.google.com/maps/api/js?v=3&sensor=false" type="text/javascript"></script>' + "\r\n";
                    }

                    code += '';

                    html = '<h4>Vous pouvez intégrer cette carte à votre site en utilisant le code suivant:</h4>';
                    html += '<pre class="sharelink">' + htmlEncode(code) + '</pre>';




                    jQuery('#sharelink').html(html);
                }

                jQuery('<li><a href="#sharelink" role="tab" title="' + i18n.t('tools_tooltips.sharelink') + '"><i class="fa fa-share-alt"></i></a></li>').appendTo(sidebar_html.find('.sidebar-tabs-top'));
                _this.panelSharelink = jQuery('<div class="sidebar-pane" id="sharelink"><pre></pre></div>').appendTo(sidebar_html.find('.sidebar-content'));
                updateShareLink();

                map.on('moveend', function(e) {
                    updateShareLink();
                });
                map.on('layeradd', function(e) {
                    updateShareLink();
                });
                map.on('layerremove', function(e) {
                    updateShareLink();
                });

            }


            _this.sidebar = L.control.sidebar('easysdi_leaflet_sidebar', options);
            _this.sidebar.addTo(map);




            return _this;
        };


        //*******

        if (data === undefined) {
            var textarea = obj.find('textarea');
            contextMapData = jQuery.parseJSON(textarea.val());
            textarea.remove();

        } else {
            contextMapData = data;
        }

        lang = contextMapData.lang;
        var i18nPath = local_url + 'locales';

        i18n.init({
            resGetPath: i18nPath + '/' + lang + '/translation.json',
            lng: lang
        }, function(t) {
            init();
        });



        obj.data('_easySDImap', _easySDImap);

        return _easySDImap;
    };


    // auto init div.easySDI-leaflet[data-url]
    jQuery('div.easySDI-leaflet[data-url]').each(function() {
        var obj = jQuery(this);
        jQuery.getJSON(obj.data('url'), {}, function(data) {
            var n = easySDImap(obj, data.data, obj.data());
            if (obj.data('callback') !== undefined) {
                var c = obj.data('callback');
                if (jQuery.isFunction(c)) {
                    c(n);
                } else {
                    try {
                        ce = eval(c);
                        ce(n);
                    } catch (e) {
                        console.error('invalid callback ' + c + ' ' + e);
                    }
                }
                //  if (jQuery.isFunction(c)) c(n);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            if (textStatus !== 'abort') {
                console.log("error " + textStatus);
                console.log("incoming Text " + jqXHR.responseText);
            }
        });
    });


    jQuery.fn.extend({
        easySDImap: function(data, callback) {
            var map_array = [];
            this.each(function() {
                if (data !== undefined) {
                    var n = easySDImap(jQuery(this), data);
                    if (jQuery.isFunction(callback)) callback(n);
                    return n;
                }
                map_array.push(jQuery(this).data('_easySDImap'));
            });
            return map_array.length == 1 ? map_array[0] : map_array;
        }
    });



});



/************/