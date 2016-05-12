 easyLayer = function (map, params) {

     var options = {
         baseGroupName: '',
         addlayers: 'add layers'
     };

     jQuery.extend(options, params);

     var _this = {
         map: map,
         groups: [],
         baseLayer: {},
         layers: {},
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



     var onLayerChange = function (e) {
         var layerId = L.Util.stamp(e.layer);
         if (e.type === 'layeradd') {
             onLayerOn(layerId);
         } else {
             onLayerOff(layerId);
         }
     }

     //map events
     map.on('layeradd', onLayerChange, this)
         .on('layerremove', onLayerChange, this)
         .on('zoomend', function () {
             _this.update();
         });

     var getLayerById = function (layerId) {
         var rlayer = false;
         jQuery.each(_this.baseLayer, function (i, layer) {
             var _layerId = L.Util.stamp(layer.layer);
             if (_layerId == layerId) rlayer = layer;
         });

         jQuery.each(_this.groups, function (i, group) {
             var layers = _this.layers[group];
             jQuery.each(layers, function (j, layer) {
                 var _layerId = L.Util.stamp(layer.layer);
                 if (_layerId == layerId) rlayer = layer;
             });
         });

         return rlayer;
     }

     _this.getLayerById = getLayerById;



     var onLayerOn = function (layerId) {
         container.find('.easyLayerTree .layer' + layerId).addClass('on');
         //overlay
         jQuery.each(_this.groups, function (i, group) {
             var layers = _this.layers[group];
             if (isset(layers) && isset(layers[layerId])) {
                 layers[layerId].on = true;
             }
         });
         //baselayer
         if (isset(_this.baseLayer[layerId])) {

             jQuery.each(_this.baseLayer, function (i, baselayer) {
                 //for (var i in _this.baseLayer) {
                 baselayer.on = false;
             });
             _this.baseLayer[layerId].on = true;
         }

     }

     var onLayerOff = function (layerId) {
         container.find('.easyLayerTree .layer' + layerId).removeClass('on');
         //overlay
         jQuery.each(_this.groups, function (i, group) {
             var layers = _this.layers[group];
             if (isset(layers) && isset(layers[layerId])) {
                 layers[layerId].on = false;
             }
         });
     }


     _this.addTo = function (div) {
         container = div;
         container.on('change', '.easyLayerTree input[type=checkbox]', function () {
             _this.switchLayer(this.name);
         });
         container.on('change', '.easyLayerTree input[type=radio]', function () {
             var target_id = this.value;
             jQuery.each(_this.baseLayer, function (i, blayer) {
                 if (i !== target_id) {
                     _this.switchLayer(i, 'off');
                 }
             });

             _this.switchLayer(target_id, 'on');
         });
     }


     var layerObj = function (name, layer, overlay) {
         return {
             name: name,
             layer: layer,
             overlay: overlay,
             on: null
         };
     }


     _this.addOverlay = function (layer, name, group) {
         if (-1 === jQuery.inArray(group, _this.groups)) {
             _this.addGroup(group);
             _this.layers[group] = {};
         }

         _this.layers[group][L.Util.stamp(layer)] = layerObj(name, layer, true);
         _this.update();
     }

     _this.addBaseLayer = function (layer, name) {
         _this.baseLayer[L.Util.stamp(layer)] = layerObj(name, layer, false);
         _this.update();
     }


     _this.addGroup = function (group) {
         _this.groups.push(group);
     }

     _this.setBaseGroupName = function (name) {
         options.baseGroupName = name;
         _this.update();
     }

     _this.getBaseGroupName = function () {
         return options.baseGroupName;
     }

     _this.removeGroup = function (group) {
         var index = jQuery.inArray(group, _this.groups);
         if (index > -1) {
             _this.groups.splice(index, 1);
         }

     }

     _this.switchLayer = function (layerId, mode) {
         var layerObj = getLayerById(layerId);

         if ((layerObj.on && mode !== 'on') || mode === 'off') {
             _this.map.removeLayer(layerObj.layer);

         } else {
             _this.map.addLayer(layerObj.layer);
         }
     }

     var checkZoom = function () {
         var zoom = map.getZoom();
         jQuery.each(_this.groups, function (i, group) {
             jQuery.each(_this.layers[group], function (layerId, layerObj) {
                 var zoomOk = (zoom >= layerObj.layer.options.minZoom && zoom <= layerObj.layer.options.maxZoom);
                 if (zoomOk) {
                     container.find('.easyLayerTree .layer' + layerId).removeClass('outOfZoom');
                 } else {
                     container.find('.easyLayerTree .layer' + layerId).addClass('outOfZoom');
                 }

             });
         });
     }


     _this.update = debounce(function () {
         if (container !== null) {
             container.html('<h4>' + options.title + '</h4><ul class="easyLayerTree"></ul>');

             if (Object.keys(_this.baseLayer).length > 0)
                 container.find('.easyLayerTree').append('<li><a href="#" class="grouplink basegroup">' + options.baseGroupName + '</a></li>');
             var ul = jQuery('<ul class="groupbasegroup"></ul>').appendTo(container.find('.easyLayerTree'));
             jQuery.each(_this.baseLayer, function (i, layer) {
                 var layerId = L.Util.stamp(layer.layer);
                 jQuery('<li class="baselayer layer' + layerId + (layer.on ? ' on' : '') + (isset(layer.layer.data.serviceconnector) ? ' LC_' + layer.layer.data.serviceconnector : '') + '"><label><input name="baselayer" value="' + layerId + '" type="radio" ' + (layer.on ? ' checked=checked' : '') + '> ' + layer.name + '</label></li>').appendTo(ul);

             });

             jQuery.each(_this.groups, function (i, group) {
                 container.find('.easyLayerTree').append('<li><a href="#" class="grouplink group' + i + ' data-group="' + i + '">' + group + '</a></li>');
                 var ul = jQuery('<ul class="group' + i + '"></ul>').appendTo(container.find('.easyLayerTree'));
                 jQuery.each(_this.layers[group], function (j, layer) {
                     var layerId = L.Util.stamp(layer.layer);
                     jQuery('<li class="layer' + layerId + (layer.on ? ' on' : '') + (isset(layer.layer.data.serviceconnector) ? ' LC_' + layer.layer.data.serviceconnector : '') + '"><label><input name="' + layerId + '" type="checkbox" ' + (layer.on ? ' checked=checked' : '') + '> ' + layer.name + '</label></li>').appendTo(ul);
                 });
             });

             if (options.addLayer !== false)
                 jQuery('<div><a href="#" class="addLayerBtn">' + options.addlayers + '</a></div>').appendTo(container);

             checkZoom();
         }
     }, 100);


     return _this;

 }