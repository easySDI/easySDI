var easySDI_processing = {};

jQuery(function () {
    var script_path = '/components/com_easysdi_processing/assets/js/easysdi_processing.js';
    var base_url = jQuery('script[src$="' + script_path + '"]').attr('src').replace(script_path, '');


    var loadVisu = function () {
        jQuery('[data-visu]').each(function () {
            var obj = jQuery(this)
            var visu_type = obj.data('visu');

            jQuery(this).click(function (e) {
                e.preventDefault();
                eval('loadVisu_' + visu_type + '(obj)');

            });

        });
    }




    var loadVisu_geojson = function (obj) {
        var data = obj.data();
        obj.hide();
        var obj_target = jQuery('<div id="map" class="easySDI-leaflet sidebar-map"></div>');
        var g = jQuery('<div id="easySDIMap" class="easySDImapPrintBlock"></div>');
        g.append(obj_target);
        g.insertAfter(obj);
        var url = base_url + '/index.php?option=com_easysdi_map&view=map&id=' + data.mapid + '&format=json';
        jQuery.getJSON(url, function (d) {
            var t_easySDImap = easySDImap(obj_target, d.data);
            t_easySDImap.loadGeojson(data.url, data.name, data.name);
        });

    }

    var init = function () {
        loadVisu();
    }

    init();
});