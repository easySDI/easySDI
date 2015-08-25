 easyGetFeature = function (map, layertree, serviceconnector, params) {

     var options = {
         "queryablelayers_title": "Select a layer",
         "noqueryablelayers": "No queryanle layer.",
         "emptyselection": "empty",
         "noresults": "nothing found"
     };

     jQuery.extend(options, params);

     var _this = {
         map: map,
         layertree: layertree,
         query: []
     };

     var container = null;
     var container_info = null;
     var container_results = null;

     var marker_layer = L.layerGroup();

     var current_layerID = null;

     var queryable = [];


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
         .on('click', function (e) {
             _this.onclick(e);
         });

     jQuery('#sidebar').on('click', '.sidebar-tabs a', function () {
         _this.update();
     })

     window.addEventListener('getCapabilities', function (e) {
         _this.update();
     });



     _this.addTo = function (div) {
         container = div;

         container_info = jQuery('<div class="getfeature_info"></div>').appendTo(container);
         container_results = jQuery('<div class="getfeature_results"></div>').appendTo(container);

         container_info.on('change', 'select.queryable_layers', function () {
             current_layerID = jQuery(this).val();
         });

         container_results.on('click', '.removeResult', function (e) {
             e.stopPropagation();
             queryid = jQuery(this).data('queryid');
             removeRes(_this.query[queryid]);
         });

         container_results.on('click', '.removeAllResult', function (e) {
             e.stopPropagation();
             for (var i in _this.query)
                 removeRes(_this.query[i]);
         });

         container_results.on('click', '.found', function () {
             queryid = jQuery(this).data('queryid');
             showRes(_this.query[queryid]);
         });

     }

     var removeRes = function (queryRes) {
         queryRes.html = false;
         if (isset(queryRes.obj))
             marker_layer.removeLayer(queryRes.obj);
         _this.updateResults();
     };

     var showRes = function (queryRes) {
         queryRes.obj.openPopup();
         map.panTo(queryRes.latlng);
     };


     _this.onclick = function (e) {
         var layerId = container.find('.queryable_layers').val();
         _this.getFeature(layertree.getLayerById(layerId), e)

     }


     _this.getFeature = function (layer, event) {
         var loc = event.containerPoint;

         var url = serviceconnector.getFeatureUrl(layer.layer, map, loc);

         var request = {
             loading: true,
             layer: layer,
             latlng: event.latlng,
             html: null
         };

         _this.query.push(request);
         var id = _this.query.length - 1;
         request = _this.query[id];
         request.id = id;
         _this.updateResults();

         jQuery.ajax({
             type: "GET",
             url: url,
             success: function (data) {
                 request.loading = false;
                 if (data != null) {
                     var table = jQuery('<div></div>').html(data).find('table');
                     if (table.length > 0) {
                         request.html = '';
                         jQuery.each(table, function () {
                             request.html += '<table class="featureInfo easygetfeature_table">' + jQuery(this).html() + '</table>';
                         });
                     } else {
                         request.html = 'none';
                         setTimeout(function () {
                             removeRes(_this.query[request.id]);
                         }, 1000);
                     }
                 }
                 var evt = new CustomEvent('getFeature', request);
                 window.dispatchEvent(evt);
                 _this.updateResults();
             }
         }).fail(function () {
             request.html = false;
             _this.updateResults();
         });
     }



     _this.update = debounce(function () {
         var queryable = [];
         jQuery(map._container).removeClass('getFeatureOn');
         if (container_info !== null) {

             container_info.html('');

             for (var i in layertree.baseLayer) {
                 var layer = layertree.baseLayer[i];
                 if (layer.on) {
                     if (serviceconnector.getQueryable(layer.layer))
                         queryable.push(layer);
                 }
             }


             for (var i in layertree.groups) {
                 var group = layertree.groups[i];
                 for (var j in layertree.layers[group]) {
                     var layer = layertree.layers[group][j];
                     if (layer.on) {
                         var query_url = serviceconnector.getQueryable(layer.layer, map);
                         if (query_url != false && query_url != null)
                             queryable.push(layer);
                     }
                 }
             }


             if (queryable.length > 0) {
                 //container_info.append(jQuery('<p>Il y a ' + (queryable.length == 1 ? ' un couche interrogeable' : queryable.length + ' couches interrogeables') + ':</p>'));
                 container_info.append(jQuery('<p>' + options.queryablelayers_title + '</p>'));
                 var select = jQuery('<select class="queryable_layers"></select>');
                 container_info.append(select);
                 jQuery.each(queryable, function (i, rqueryable){
                 //for (var i in queryable) {
                     var layerId = L.Util.stamp(rqueryable.layer);
                     jQuery('<option value="' + layerId + '"' + (current_layerID == layerId ? ' selected' : '') + '>' + queryable[i].name + '</option>').appendTo(select);
                 });

                 if (jQuery('#sidebar #getfeature').hasClass('active') && !jQuery('#sidebar').hasClass('collapsed')) {
                     if (!map.hasLayer(marker_layer))
                         map.addLayer(marker_layer);
                     jQuery(map._container).addClass('getFeatureOn');
                 } else {
                     if (map.hasLayer(marker_layer))
                         map.removeLayer(marker_layer);
                 }
             } else {
                 container_info.html('<p class="warning">' + options.noqueryablelayers + '</p>');
             }


         }
     }, 250);



     var addQueryObj = function (query) {

         var html_result = query.html;

         var nlayer = new L.Marker(query.latlng)
             .bindPopup(html_result, {
                 className: 'easygetfeature_popup'
             })
             .addTo(marker_layer)
             .openPopup();

         query.obj = nlayer;
     }

     _this.updateResults = debounce(function () {

         if (container_results !== null) {

             container_results.html('');

             var query_shown = jQuery.grep(_this.query, function (v) {
                 return v.loading == false && v.html != null && v.html != false;
             });
             if (query_shown.length > 1)
                 container_results.append('<a href="#" class="removeAllResult" rel="tooltip" title="' + options.emptyselection + '"><i class="fa fa-eraser"></i></a>');

             jQuery.each(_this.query, function (i, query){
             //for (var i in _this.query) {
                 //var query = _this.query[i];
                 if (query.loading) {
                     var html = '<div class="query' + i + ' loading"></div>';
                     var div = jQuery(html).appendTo(container_results);
                     div.append(jQuery('<p>' + query.layer.name + '</p>'));
                     div.append(jQuery('<p><small>' + query.latlng.lat + ', ' + query.latlng.lng + '</small></p>'));
                 } else {
                     if (query.html !== false) {
                         if (query.html !== 'none') {
                             var html = '<div class="query' + i + ' found" data-queryid=' + i + '></div>';
                         } else {
                             var html = '<div class="query' + i + ' none" data-queryid=' + i + '></div>';
                         }
                         var div = jQuery(html).appendTo(container_results);
                         if (query.html !== 'none')
                             div.append('<div class="query-btns"><a href="#" class="removeResult" data-queryid="' + i + '" rel="tooltip" title="Enlever le résultat"><i class="fa fa-times-circle"></i></a></div>');
                         div.append(jQuery('<p>' + query.layer.name + '</p>'));
                         div.append(jQuery('<p><small>' + query.latlng.lat + ', ' + query.latlng.lng + '</small></p>'));
                         if (query.html !== 'none') {
                             if (query.obj == null) addQueryObj(query);
                         } else {
                             div.append(jQuery('<p class="warning"><small>' + options.noresults + '</small></p>'));
                         }
                     }

                 }
             });

         }
     }, 250);


     _this.showPanel = function (sidebar) {
         sidebar.open();
         if (!jQuery('#sidebar #getfeature').hasClass('active')) {
             jQuery('#sidebar .sidebar-content.active,#sidebar .sidebar-tabs li.active').removeClass('active');
             jQuery('#sidebar #getfeature').addClass('active');
             jQuery('#sidebar a[href=#getfeature]').parent().addClass('active');
         }
         _this.update();
     }

     return _this;

 }