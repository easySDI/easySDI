 easyAddLayer = function (easysdi_leaflet, layertree, serviceConnector, params) {

     var options = {
         "selectserver": "View available data from",
         "addserver": "add a server",
         "loadingtext": "loading ...",
         "nolayersfound": "No available layer"
     };
     var map = easysdi_leaflet.mapObj;

     jQuery.extend(options, params);

     var current_container = null;
     var current_service = null;

     var _this = {
         map: map,
     };

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

     var setServicesAvailableSelect = function () {
         if (current_container == null || current_container.find('.service_switcher').length == 0)
             return false;

         var services = serviceConnector.getAllServices(map);
         services._new_ = {
             servicealias: '',
             name: options.addserver,
             list: true
         };

         var select = current_container.find('.service_switcher select');
         for (var i in services) {
             if (services[i].list) {
                 if (current_service == null) current_service = services[i].servicealias;
                 jQuery('<option value="' + services[i].servicealias + '"' + (current_service == services[i].servicealias ? ' selected' : '') + '>' + services[i].name + '</option>').appendTo('select');
             }
         }

         setLayerAvailableSelect();
     }


     var setLayerAvailableSelect = function () {
         var ul = current_container.find('.available_layers ul');

         if (current_service == '') {
             ul.html('<form class="addservice">' +
                 '<div>' +
                 '<label>Type</label>' +
                 '<select name="type">' +
                 '<option value="WMS">Web Map Service (WMS)</option>' +
                 //'<option value="WMTS">Tiled Map Service (WMTS)</option>' +
                 //  '<option value="REST">WArcGIS REST Service (REST)</option>'+
                 '</select>' +
                 '</div>' +
                 '<div>' +
                 '<label>URL</label>' +
                 '<input type="text" name="url"/>' +
                 '</div>' +
                 '<input type="submit" value="' + options.addserver + '" />' +
                 '</form>');

             return false;
         }

         if (current_container == null || current_container.find('.available_layers').length == 0)
             return false;


         var layers = serviceConnector.getServiceLayers(current_service);

         if (layers === null) {
             ul.html(options.loadingtext);
             setTimeout(setLayerAvailableSelect, 500);
             setTimeout(setServicesAvailableSelect, 500);
             return false;
         }

         ul.html('');
         if (layers.length == 0) {
             ul.html('<p class="warning">' + options.nolayersfound + '</p>')
         }
         for (var i in layers) {
             var l = layers[i];

             if (isset(l.Title)) {
                 var title = l.Title;
                 var alias = l.Name;
                 jQuery('<li><a href="#" data-layer="' + alias + '">' + title + '</a></li>').appendTo(ul);
             }


             if (typeof l === 'string') jQuery('<li><a href="#" data-layer="' + l + '">' + l + '</a></li>').appendTo(ul);


         }
     }

     var addLayer = function (servicealias, layeralias) {
         var cap = serviceConnector.getCapabilities(servicealias);
         var layer = serviceConnector.getLayerData(cap, layeralias);
         var service = serviceConnector.services[servicealias];

         var overlay = true;
         var show = true;
         var group = options.defaultGroup;

         if (service.serviceconnector == 'WMS')
             var data = {
                 name: layer.Title,
                 serviceconnector: service.serviceconnector,
                 serviceurl: service.serviceurl,
                 servicealias: servicealias,
                 layername: layeralias,
                 opacity: 1,
                 format: 'image/png',
                 attribution: serviceConnector.getAttribution(layer),
                 style: '',
                 bounds: '',
                 maxZoom: '',
                 zIndex: 10000
             };


         if (service.serviceconnector == 'WMTS')
             var data = {
                 name: layer.Title,
                 serviceconnector: service.serviceconnector,
                 serviceurl: service.serviceurl,
                 servicealias: servicealias,
                 layername: layeralias,
                 opacity: 1,
                 format: 'image/png',
                 attribution: serviceConnector.getAttribution(layer),
                 style: '',
                 bounds: '',
                 maxZoom: '',
                 zIndex: 10000
                 // !TODO gestion des tilematrix, va necessiter proj4
             };

         if (service.serviceconnector == 'Google') {
             var data = {
                 name: 'Google ' + layeralias,
                 serviceconnector: service.serviceconnector,
                 servicealias: servicealias,
                 layername: layeralias,
                 opacity: 1
             };
             overlay = false;
             group = null;
         }

         if (service.serviceconnector == 'OSM') {
             var data = {
                 name: 'OSM ' + layeralias,
                 serviceconnector: service.serviceconnector,
                 servicealias: servicealias,
                 layername: layeralias,
                 opacity: 1
             };
             overlay = false;
             group = null;
         }

         if (service.serviceconnector == 'Bing') {
             var data = {
                 name: 'Bing ' + layeralias,
                 serviceconnector: service.serviceconnector,
                 servicealias: servicealias,
                 layername: layeralias,
                 opacity: 1
             };
             overlay = false;
             group = null;
         }




         easysdi_leaflet.addLayer(data, overlay, show, group);
         if (overlay == false) {
             console.log('la couche ' + data.name + ' a été ajoutée aux couches de fond');
             // !TODO gerer l'ajoute de baselayer
         }
     }


     _this.show = function (container) {


         current_container = container;
         jQuery('<div class="service_switcher">' +
             '<label>' + options.selectserver + '</label>' +
             '<select class="services"></select>' +
             '</div>' +
             '<div class="available_layers"><ul></ul></div>').appendTo(container);
         setServicesAvailableSelect();

         container.on('change', '.service_switcher select', function () {
             current_service = jQuery(this).val();
             setLayerAvailableSelect();
         });

         container.on('click', '.available_layers a', function (e) {
             e.preventDefault();
             var layer = jQuery(this).data('layer');
             addLayer(current_service, layer);
         });

         container.on('submit', '.addservice', function (e) {
             e.preventDefault();
             var url = jQuery(this).find('input[name=url]').val();
             var type = jQuery(this).find('select[name=type]').val();
             var data = {
                 serviceconnector: type,
                 servicetype: 'physical',
                 serviceurl: url,
                 servicealias: url
             };
             current_service = url;
             var ns = serviceConnector.addService(data);
             ns.list = true;
             setLayerAvailableSelect();
         });
     }




     return _this;

 }