 easyGetFeature = function(map, layertree, serviceconnector, params, popup_size) {

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
     var last_event;

     var isset = function(variable) {
         return typeof(variable) != "undefined" && variable !== null;
     };


     function debounce(func, wait, immediate) { //http://davidwalsh.name/essential-javascript-functions
         var timeout;
         return function() {
             var context = this,
                 args = arguments;
             var later = function() {
                 timeout = null;
                 if (!immediate) func.apply(context, args);
             };
             var callNow = immediate && !timeout;
             clearTimeout(timeout);
             timeout = setTimeout(later, wait);
             if (callNow) func.apply(context, args);
         };
     };



     map.on('layeradd', function() {
             _this.update();
         })
         .on('layerremove', function() {
             _this.update();
         })
         .on('click', function(e) {
             _this.onclick(e);
         });

     jQuery('#sidebar').on('click', '.sidebar-tabs a', function() {
         _this.update();
     })

     window.addEventListener('getCapabilities', function(e) {
         _this.update();
     });



     _this.addTo = function(div) {
         container = div;

         container_info = jQuery('<div class="getfeature_info"></div>').appendTo(container);
         container_results = jQuery('<div class="getfeature_results"></div>').appendTo(container);

         /*container_info.on('change', 'select.queryable_layers', function() {
             current_layerID = jQuery(this).val();
         });*/

         container_results.on('click', '.removeResult', function(e) {
             e.stopPropagation();
             e.preventDefault();
             queryid = jQuery(this).data('queryid');
             removeRes(_this.query[queryid]);
         });

         container_results.on('click', '.removeAllResult', function(e) {
             e.stopPropagation();
             e.preventDefault();
             for (var i in _this.query)
                 removeRes(_this.query[i]);
         });

         container_results.on('click', '.found', function(e) {
             e.preventDefault();
             queryid = jQuery(this).data('queryid');
             showRes(_this.query[queryid]);
         });

     }

     var removeRes = function(queryRes) {
         queryRes.html = false;
         if (isset(queryRes.obj))
             marker_layer.removeLayer(queryRes.obj);
         _this.updateResults();
     };

     var showRes = function(queryRes) {
         queryRes.obj.openPopup();
         map.panTo(queryRes.latlng);
     };

     var collapse_start =
         '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">';



     var collapse_end = '</div>';

     var tmp_collapse = "";
     var nbr_active_layer = 0;
     var first_layer;

     _this.onclick = function(e) {
         nbr_active_layer = 0;
         first_layer = "in";
         layertree.groups.forEach(function(group) {
             for (var index in layertree.layers[group]) {
                 var attr = layertree.layers[group][index];
                 if (attr.on == true) {
                     nbr_active_layer++;
                 }

             };
         });
         tmp_collapse = "";
         if (jQuery('.leaflet-zoom-box-crosshair').length == 0) {

             layertree.groups.forEach(function(group) {
                 for (var index in layertree.layers[group]) {

                     var attr = layertree.layers[group][index];
                     if (attr.on == true) {
                         last_event = e;
                         _this.getFeature(layertree.getLayerById(attr.layer._leaflet_id), e);
                     }

                 };
             });
         }
     }





     _this.getFeature = function(layer, event = last_event) {

         var loc = event.containerPoint;
         var url = serviceconnector.getFeatureUrl(layer.layer, map, loc);

         request = {
             loading: true,
             layer: layer,
             latlng: event.latlng,
         };
         _this.query.push(request);
         var id = _this.query.length - 1;
         request = _this.query[id];
         request.id = id;
         request.html = "";
         jQuery.ajax({
             type: "GET",
             url: url,
             success: function(data) {
                 request.loading = false;
                 if (data != null) {
                     var table = jQuery('<div></div>').html(data).find('table');
                     if (table.length > 0) {




                         jQuery.each(table, function() {
                             request.html += '<table class="featureInfo easygetfeature_table">' + jQuery(this).html() + '</table>';
                         });

                         var collapse =
                             '<div class="panel panel-default">' +
                             '<div class="panel-heading" role="tab" id="headingOne">' +
                             '<h4 class="panel-title">' +
                             '<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse' + layer.layer._leaflet_id + '" aria-expanded="true" aria-controls="collapse' + layer.layer._leaflet_id + '">' +
                             layer.name +
                             '</a>' +
                             '</h4>' +
                             '</div>' +
                             '<div id="collapse' + layer.layer._leaflet_id + '" class="panel-collapse collapse ' + first_layer + '" role="tabpanel" aria-labelledby="heading' + layer.layer._leaflet_id + '">' +
                             '<div class="panel-body">' +
                             '<pre class="featureInfo easygetfeature_pre">' + request.html + '</pre>' +
                             '</div>' +
                             '</div>' +
                             '</div>';


                         tmp_collapse += collapse;
                         request.html = collapse_start + tmp_collapse + collapse_end;
                         first_layer = "";

                         var evt = new CustomEvent('getFeature', request);
                         window.dispatchEvent(evt);
                         nbr_active_layer--;
                         _this.updateResults();

                     } else {
                         data = data.replace('GetFeatureInfo results:', '').trim();
                         if (data.length > 0 && data.search('Search returned no results.') == -1 && data.search('ul') > 0) {

                             var collapse =
                                 '<div class="panel panel-default">' +
                                 '<div class="panel-heading" role="tab" id="headingOne">' +
                                 '<h4 class="panel-title">' +
                                 '<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse' + layer.layer._leaflet_id + '" aria-expanded="true" aria-controls="collapse' + layer.layer._leaflet_id + '">' +
                                 layer.name +
                                 '</a>' +
                                 '</h4>' +
                                 '</div>' +
                                 '<div id="collapse' + layer.layer._leaflet_id + '" class="panel-collapse collapse ' + first_layer + '" role="tabpanel" aria-labelledby="heading' + layer.layer._leaflet_id + '">' +
                                 '<div class="panel-body featureInfo easygetfeature_pre">' +
                                 data +
                                 '</div>' +
                                 '</div>' +
                                 '</div>';




                             tmp_collapse += collapse;
                             request.html = collapse_start + tmp_collapse + collapse_end;
                             first_layer = "";

                             var evt = new CustomEvent('getFeature', request);
                             window.dispatchEvent(evt);
                             _this.updateResults();
                         } else {

                         }
                     }
                 }

             }

         }).fail(function() {
             request.html = false;
         });
     }



     _this.update = debounce(function() {
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



         }
     }, 250);



     var addQueryObj = function(query) {

         var html_result = query.html;

         var nlayer = new L.Marker(query.latlng)
             .bindPopup(html_result, {
                 // className: 'easygetfeature_popup',
                 maxWidth: popup_size.popupwidth,
                 minWidth: popup_size.popupwidth,
                 minHeight: popup_size.popupheight,
                 maxHeight: popup_size.popupheight,
             })
             .addTo(marker_layer)
             .openPopup();

         if (!jQuery('#sidebar #getfeature').hasClass('active')) {
             var popup = L.popup({
                     //className: 'easygetfeature_popup',
                     maxWidth: popup_size.popupwidth,
                     minWidth: popup_size.popupwidth,
                     minHeight: popup_size.popupheight,
                     maxHeight: popup_size.popupheight,
                 })
                 .setLatLng(query.latlng)
                 .setContent(html_result)

             .openOn(map);



         }


         query.obj = nlayer;
     }

     _this.updateResults = debounce(function() {

         if (container_results !== null) {

             container_results.html('');

             var query_shown = jQuery.grep(_this.query, function(v) {
                 return v.loading == false && v.html != null && v.html != false;
             });
             if (query_shown.length > 1)
                 container_results.append('<a href="#" class="removeAllResult" rel="tooltip" title="' + options.emptyselection + '"><i class="fa fa-eraser"></i></a>');

             jQuery.each(_this.query, function(i, query) {
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
             //var left = document.getElementsByClassName("easygetfeature_popup")[0].style.left.split("px")
             //left = Number(left[0]) - ((popup_size.popupwidth - document.getElementsByClassName("easygetfeature_popup")[0].clientWidth) / 2);

             //var bottom = document.getElementsByClassName("easygetfeature_popup")[0].style.bottom.split("px")

             //bottom = Number(bottom[0]) - ((popup_size.popupheight - document.getElementsByClassName("easygetfeature_popup")[0].clientHeight));



             //document.getElementsByClassName("easygetfeature_popup")[0].style.left = left + "px";
             //document.getElementsByClassName("easygetfeature_popup")[0].style.bottom = bottom + "px";
             //document.getElementsByClassName("easygetfeature_popup")[0].style.width = popup_size.popupwidth + "px";
             //document.getElementsByClassName("easygetfeature_popup")[0].style.height = popup_size.popupheight + "px";
             //document.getElementsByClassName("leaflet-popup-content-wrapper")[0].style.height = popup_size.popupheight + "px";
             //document.getElementsByClassName("leaflet-popup-content")[0].style.maxHeight = popup_size.popupheight - 20 + "px";
         }
     }, 250);


     _this.showPanel = function(sidebar) {
         sidebar.open();
         _this.update();
     }

     return _this;

 }