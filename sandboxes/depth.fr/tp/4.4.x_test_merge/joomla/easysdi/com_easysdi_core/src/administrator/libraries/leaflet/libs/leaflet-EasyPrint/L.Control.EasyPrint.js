 //**************
 //  EasyPrint
 L.Control.EasyPrint = L.Control.extend({
     options: {
         position: 'topleft',
         title: 'Print',
         print: 'Print',
         cancel: 'cancel',
         copyright: '',
         defaulttitle: '',
         defaultdesc: ''
     },

     onAdd: function (map) {
         var options = this.options;
         var container = L.DomUtil.create('div', 'leaflet-control-easyPrint leaflet-bar leaflet-control');

         container.title = options.title;
         this.link = L.DomUtil.create('a', 'leaflet-control-easyPrint-button leaflet-bar-part', container);
         this.link.href = '#';

         L.DomEvent
             .on(container, 'click', function (e) {
                 L.DomEvent.stopPropagation(e);
                 L.DomEvent.preventDefault(e);

                 L.easyPrint(options);
             });


         jQuery('body').on('click', '.easySDImapPrintOk', function (e) {
             e.preventDefault();
             window.print();
         });

         jQuery('body').on('click', '.easySDImapPrintCancel', function (e) {
             e.preventDefault();
             L.easyPrintCancel();
         });

         return container;
     }

 });

 L.easyPrintControl = function (options) {
     return new L.Control.EasyPrint(options);
 };

 //*********************

 L.easyPrint = function (options) {

     var container = jQuery('.leaflet-control-easyPrint');
     var mapContainer = container.parents('.leaflet-container');

     var width = mapContainer.width();
     var height = mapContainer.height();

     //  container.width(width);
     //  container.height(height);

     if (options.defaulttitle == undefined) options.defaulttitle = '';
     if (options.defaultdesc == undefined) options.defaultdesc = '';

     jQuery('body').addClass('easySDImapPrint');

     jQuery('body *').addClass('mapPrintHideMe');


     var printBlock = container.parents('.easySDImapPrintBlock');
     if (printBlock.length === 0) printBlock = container;

     printBlock.parents().removeClass('mapPrintHideMe');
     printBlock.find('*.mapPrintHideMe').removeClass('mapPrintHideMe');
     printBlock.removeClass('mapPrintHideMe');


     var html = '<div class="easySDImapPrintMeta">';
     html += '<input type="text" class="easySDImapPrintTitle" value="' + options.defaulttitle + '" placeholder="Titre"/>';
     html += '<textarea type="text" class="easySDImapPrintDesc">' + options.defaultdesc + '</textarea>';
     html += '<p class="easySDImapPrintCopyright">' + options.copyright + '</p>';
     html += '</div>';
     html += '<div class="easySDImapPrintButtons">';
     html += '<a href="#" class="easySDImapPrintOk btn btn-lg btn-large btn-primary">' + options.print + '</a>';
     html += '<a href="#" class="easySDImapPrintCancel btn btn-lg btn-large btn-default">' + options.cancel + '</a>';
     html += '</div>';
     console.log(mapContainer);
     console.log(container);
     mapContainer.after(html);

     jQuery('.easySDImapPrintMeta, .easySDImapPrintButtons').width(width);
     jQuery("html, body").animate({
         scrollTop: jQuery(document).height()
     }, 1000);
 };




 L.easyPrintCancel = function () {
     jQuery('.easySDImapPrint').removeClass('easySDImapPrint');
     jQuery('.mapPrintHideMe').removeClass('mapPrintHideMe');
     jQuery('.easySDImapPrintMeta').remove();
     jQuery('.easySDImapPrintButtons').remove();
 };