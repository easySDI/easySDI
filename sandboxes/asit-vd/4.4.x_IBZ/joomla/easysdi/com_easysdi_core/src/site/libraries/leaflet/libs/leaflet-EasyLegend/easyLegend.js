 easyLegend = function (map, layertree, serviceconnector, params) {

     var options = {
         "zoomOnExtends": "Zoom on layer",
         "order": "Order",
         "download": "Download",
         "metadata": "Info",
         "remove": "Remove",
         "openLegend": "Open",
         "legendTitle": "Legend"
     };

     jQuery.extend(options, params);

     var _this = {
         map: map,
         layertree: layertree,
     };

     var container = null;

     var isset = function (variable) {
         return typeof (variable) != "undefined" && variable !== null;
     };


     function debounce(func, wait, immediate) { //http://davidwalsh.name/essential-javascript-functions
         var timeout;
         return function () {
             var context = this,
                 args = arguments;
             var later = function () {
                 timeout = null;
                 if (!immediate) func.apply(context, args);
             };
             var callNow = immediate && !timeout;
             clearTimeout(timeout);
             timeout = setTimeout(later, wait);
             if (callNow) func.apply(context, args);
         };
     };



     map.on('layeradd', function () {
         _this.update();
     })
         .on('layerremove', function () {
             _this.update();
         })
         .on('zoomend', function () {
             _this.update();
         });

     window.addEventListener('getCapabilities', function (e) {
         _this.update();
     });



     _this.addTo = function (div) {
         container = div;

         container.on('click', '.removeLayer', function (event) {
             event.preventDefault();
             layertree.switchLayer(jQuery(this).data('layerid'), 'off');
             layertree.update();
         });

         container.on('click', '.zoomlink', function (event) {
             event.preventDefault();
             var bb = jQuery(this).data('bbox').split(',');
             bb = L.latLngBounds([
                 [bb[1], bb[0]],
                 [bb[3], bb[2]]
             ]);
             map.fitBounds(bb);
             return false;
         });


     }

     var getIGNlegend = function (layername, zoom) {
         if (layername == 'GEOGRAPHICALGRIDSYSTEMS.CASSINI')
             return 'http://www.geoportail.gouv.fr/depot/api/legende/LEG_GEOGRAPHICALGRIDSYSTEMS_ETATMAJOR.jpg';

         if (layername == 'GEOGRAPHICALGRIDSYSTEMS.ETATMAJOR' || layername == 'GEOGRAPHICALGRIDSYSTEMS.ETATMAJOR40')
             return 'http://www.geoportail.gouv.fr/depot/api/legende/LEG_GEOGRAPHICALGRIDSYSTEMS_CASSINI.jpg';


         if (layername == 'GEOGRAPHICALGRIDSYSTEMS.MAPS.SCAN-EXPRESS.STANDARD') {
             if (zoom >= 18) return 'http://www.geoportail.gouv.fr/depot/api/legende/LEG_GM_SCAN-EXPRESS_STANDARD_GE.jpg';
             if (zoom >= 15) return 'http://www.geoportail.gouv.fr/depot/api/legende/LEG_GM_SCAN-EXPRESS_STANDARD_25K.jpg';
             if (zoom >= 11) return 'http://www.geoportail.gouv.fr/depot/api/legende/LEG_GM_SCAN-EXPRESS_STANDARD_100K.jpg';
             if (zoom >= 9) return 'http://www.geoportail.gouv.fr/depot/api/legende/LEG_GM_SCAN-EXPRESS_STANDARD_250K.jpg';
             return 'http://www.geoportail.gouv.fr/depot/api/legende/LEG_GM_SCAN-EXPRESS_STANDARD_1000K.jpg';
         }

         if (layername == 'GEOGRAPHICALGRIDSYSTEMS.MAPS.SCAN-EXPRESS.CLASSIQUE') {
             if (zoom >= 18) return 'http://www.geoportail.gouv.fr/depot/api/legende/LEG_GM_SCAN-EXPRESS_CLASSIQUE_GE.jpg';
             if (zoom >= 15) return 'http://www.geoportail.gouv.fr/depot/api/legende/LEG_GM_SCAN-EXPRESS_CLASSIQUE_25K.jpg';
             if (zoom >= 11) return 'http://www.geoportail.gouv.fr/depot/api/legende/LEG_GM_SCAN-EXPRESS_CLASSIQUE_100K.jpg';
             if (zoom >= 9) return 'http://www.geoportail.gouv.fr/depot/api/legende/LEG_GM_SCAN-EXPRESS_CLASSIQUE_250K.jpg';
             return 'http://www.geoportail.gouv.fr/depot/api/legende/LEG_GM_SCAN-EXPRESS_CLASSIQUE_1000K.jpg';
         }

         if (layername == 'GEOGRAPHICALGRIDSYSTEMS.MAPS') {
             if (zoom >= 18) return false;
             if (zoom >= 15) return false;
             if (zoom >= 13) return 'http://www.geoportail.gouv.fr/depot/api/legende/LEG_GEOGRAPHICALGRIDSYSTEMS_SCANDEP.jpg';
             return 'http://www.geoportail.gouv.fr/depot/api/legende/LEG_GEOGRAPHICALGRIDSYSTEMS_SCANREG.jpg';
         }

         if (layername == 'ADMINISTRATIVEUNITS.BOUNDARIES')
             return 'http://www.geoportail.gouv.fr/depot/api/legende/LEG_ADMINISTRATIVEUNITS_BOUNDARIES.jpg';

         return false;
     }


     var addLegendBtns = function (target, layer) {
         var layerId = L.Util.stamp(layer.layer);
         var btn_div = jQuery('<div class="action_btns"></div>');
         target.append(btn_div);

         //  console.log(layer.layer.data.layername, serviceconnector.getQueryable(layer.layer));

         var bb = serviceconnector.getBBox(layer.layer, map);
         if (bb !== false && bb !== null)
             jQuery('<a href="#"  class="zoomlink" rel="tooltip" data-bbox="' + bb.toBBoxString() + '" title="' + options.zoomOnExtends + '"><i class="fa fa-search"></i></a>').appendTo(btn_div);

         if (options.layerorder !== false && layer.layer.data.hasextraction == 1)
             jQuery('<a href="' + layer.layer.data.extractionurl + '" target=_blank class="commandlink" rel="tooltip" title="' + options.order + '"><i class="fa fa-shopping-cart"></i></a>').appendTo(btn_div);

         if (options.layerdownload !== false && layer.layer.data.hasdownload == 1)
             jQuery('<a href="' + layer.layer.data.downloadurl + '" target=_blank class="downloadlink" rel="tooltip" title="' + options.download + '"><i class="fa fa-download"></i></a>').appendTo(btn_div);

         if (options.layerdetailsheet !== false && isset(layer.layer.data.metadatalink) && layer.layer.data.metadatalink !== '')
             jQuery('<a href="' + layer.layer.data.metadatalink + '" target=_blank class="metadatalink" rel="tooltip" title="' + options.metadata + '"><i class="fa fa-info-circle"></i></a>').appendTo(btn_div);

         if (layer.overlay === true)
             jQuery('<a href="#" class="removeLayer" data-layerId="' + layerId + '" rel="tooltip" title="' + options.remove + '"><i class="fa fa-times-circle"></i></a>').appendTo(btn_div);



     }


     var addLegendImage = function (target, layer, map) {
         var url = false;
         if (isset(layer.legendUrl)) {
             url = layer.legendUrl;
         } else {
             var zoom = map.getZoom();
             var legendUrl = serviceconnector.getLegendURL(layer.layer);
             url = legendUrl;

             // gestion legendes IGN
             if (url == 'http://www.geoportail.gouv.fr/depot/LEGEND.jpg') {
                 url = getIGNlegend(layer.layer.data.layername, zoom);
             }

             if (url === false) {
                 url = serviceconnector.getLegendGraphic(layer.layer, map);
             }

         }
         if (url != false && url != null) {
             html = '<br>';
             html += '<a href="' + url + '" target=_blank title="' + options.openLegend + '" rel="tooltip"><img src="' + url + '" alt=""/></a>';
             target.append(html);
         }

     }



     _this.update = debounce(function () {
         if (container !== null) {
             container.html('<h4>' + options.title + '<br><small>' + options.legendTitle + '</small></h4><ul class="easyLegend"></ul>');
             var lastGroup = '';



             for (var i in layertree.baseLayer) {
                 var layer = layertree.baseLayer[i];
                 if (layer.on) {
                     if (lastGroup !== 'baselayer') {
                         container.find('.easyLegend').append('<li><a href="#" class="grouplink basegroup">' + layertree.getBaseGroupName() + '</a></li>');
                         var ul = jQuery('<ul class="group' + i + '"></ul>').appendTo(container.find('.easyLegend'));
                         lastGroup = 'baselayer';
                     }
                     var li = jQuery('<li class="baselayer layer' + i + (isset(layer.layer.data.serviceconnector) ? ' LC_' + layer.layer.data.serviceconnector : '') + '"></li>').appendTo(ul);
                     addLegendBtns(li, layer);
                     jQuery('<span>' + layer.name + '</span>').appendTo(li);
                     addLegendImage(li, layer, map);
                     li.append('<div class="clearfix"></div>');

                 }
             }


             for (var i in layertree.groups) {
                 var group = layertree.groups[i];

                 for (var j in layertree.layers[group]) {
                     var layer = layertree.layers[group][j];
                     if (layer.on) {
                         if (lastGroup !== 'group' + i) {
                             container.find('.easyLegend').append('<li><a href="#" class="grouplink group' + i + ' data-group="' + i + '">' + group + '</a></li>');
                             var ul = jQuery('<ul class="group' + i + '"></ul>').appendTo(container.find('.easyLegend'));
                             lastGroup = 'group' + i;
                         }
                         var li = jQuery('<li class="layer' + j + (isset(layer.layer.data.serviceconnector) ? ' LC_' + layer.layer.data.serviceconnector : '') + '"></li>').appendTo(ul);
                         addLegendBtns(li, layer);
                         jQuery('<span>' + layer.name + '</span>').appendTo(li);
                         addLegendImage(li, layer, map);
                         li.append('<div class="clearfix"></div>');

                     }
                 }
             }
         }
     }, 100);


     return _this;

 }