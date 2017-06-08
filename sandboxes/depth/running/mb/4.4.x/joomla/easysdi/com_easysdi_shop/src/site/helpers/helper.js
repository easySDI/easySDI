js = jQuery.noConflict();
var dest, polygonLayer,selectLayer,customStyleMap;

function loadPerimeter(withdisplay) {
    initStyleMap();
    if (jQuery('#jform_perimeter').length > 0) {
        loadPolygonPerimeter(withdisplay);
    } else {
        loadWfsPerimeter();
    }
}

function loadPolygonPerimeter(withdisplay) {
    polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer", {srsName: window.appname.mapPanel.map.projection, projection: window.appname.mapPanel.map.projection, styleMap: customStyleMap});

    var wkt = jQuery('#jform_perimeter').val();
    var features = new OpenLayers.Format.WKT().read(wkt);
    if (features instanceof Array) {
        for (var i = 0; i < features.length; i++) {
            var geometry = features[i].geometry.transform(
                    new OpenLayers.Projection("EPSG:4326"),
                    new OpenLayers.Projection(window.appname.mapPanel.map.projection)
                    );
            var reprojfeature = new OpenLayers.Feature.Vector(geometry);
            polygonLayer.addFeatures([reprojfeature]);
        }
    }
    else {
        var source = new OpenLayers.Projection("EPSG:4326");
        dest = new OpenLayers.Projection(window.appname.mapPanel.map.projection);
        features.geometry.transform(source, dest);
        polygonLayer.addFeatures([features]);
    }

    window.appname.mapPanel.map.addLayers([polygonLayer]);
    window.appname.mapPanel.map.zoomToExtent(polygonLayer.getDataExtent());

    if (withdisplay === true) {
        jQuery('#perimeter-recap').append('<div id="perimeter-recap-details" style="overflow-y:scroll; height:100px;">');
        jQuery('#perimeter-recap-details').append("<div>" + wkt + "</div>");
        jQuery('#perimeter-recap').append('</div>');
    }
}

function loadWfsPerimeter() {
    var url = jQuery('#jform_wfsurl').val();
    var featuretypename = jQuery('#jform_wfsfeaturetypename').val();
    var featuretypefieldid = jQuery('#jform_wfsfeaturetypefieldid').val();
    var namespace = jQuery('#jform_wfsnamespace').val();
    var prefix = jQuery('#jform_wfsprefix').val();
    var featuretypefieldgeometry = jQuery('#jform_wfsfeaturetypefieldgeometry').val();

    var features_object = jQuery('#jform_wfsperimeter').val();

    selectLayer = new OpenLayers.Layer.Vector("Selection", {styleMap: customStyleMap});
    window.appname.mapPanel.map.addLayer(selectLayer);

    if (features_object !== "")
        var features = JSON.parse(features_object);
    else
        var features = new Array();


    var tempWFSfilterList = [];
    var tempWFSfilter;

    for (var i = 0; i < features.length; i++) {
        tempWFSfilterList.push(
                new OpenLayers.Filter.Comparison({
                    type: OpenLayers.Filter.Comparison.EQUAL_TO,
                    property: featuretypefieldid,
                    value: features[i].id
                }));
    }

    if (features.length > 1) {
        tempWFSfilter = new OpenLayers.Filter.Logical({
            type: OpenLayers.Filter.Logical.OR,
            filters: tempWFSfilterList
        });
    }
    else {
        tempWFSfilter = tempWFSfilterList[0];
    }

    var protoWFS = new OpenLayers.Protocol.WFS(
            {
                version: "1.0.0",
                url: url,
                featureType: featuretypename,
                featureNS: namespace,
                featurePrefix: prefix,
                geometryName: featuretypefieldgeometry,
                defaultFilter: tempWFSfilter
            }
    );

    protoWFS.read({
        readOptions: {output: "object"},
        resultType: "hits",
        maxFeatures: null,
        callback: function (resp) {
            selectLayer.addFeatures(resp.features);
            window.appname.mapPanel.map.zoomToExtent(selectLayer.getDataExtent());
        }
    });
}

js(document).on('click', 'button.delete', function () {
    var delete_url = 'index.php?option=com_easysdi_shop&task=pricingprofile.delete&id=';
    var profile_id = js(this).attr('data-id');
    js('#btn_delete').attr('href', delete_url + profile_id);
    js('#deleteModal').modal('show');
});

function initStyleMap() {
    customStyleMap = new OpenLayers.StyleMap({
        "default": new OpenLayers.Style({
            fillColor: mapFillColor,
            fillOpacity: mapFillOpacity,
            strokeColor: mapStrokeColor,
            strokeDashstyle: "solid",
            strokeLinecap: "round",
            strokeOpacity: 1,
            strokeWidth: mapStrokeWidth,
            graphicName: "circle"
        })
    });
}

var acturl;
        
function addOrderToBasket (n){
    if(n!=0){ jQuery('#modal-dialog-atb').modal('show');}
    else{confirmAdd();}
}

function confirmAdd() {jQuery(location).attr('href',acturl);}

function getBasketContent(callback){
    jQuery.ajax({
        cache: false,
        type: 'GET',
        url: 'index.php?option=com_easysdi_shop&task=basket.getBasketContent'
    }).done(function(r){
        try{
           window[callback](r);
        }
        catch(e){if(window.console){console.log(e);}}
    });
}

js(document).on('click', 'a[id$=_otpdownload]', function () {
    jQuery("#otpmessage").hide();
    showOTPDownloadModal(this);
});

js(document).on('submit', '#form_otp' , function( event ) {  
    confirmOtpAuth(event);
    event.preventDefault();
});

// Retrieves resource's id from HTML Element's id
var getDiffusionId = function (element) {
    var tabId = js(element).attr('id').split('_');
    return tabId[1];
};

// Retrieves resource's id from HTML Element's id
var getOrderId = function (element) {
    var tabId = js(element).attr('id').split('_');
    return tabId[0];
};

var showOTPDownloadModal = function (element) {
    jQuery('#modal-dialog-otp').modal('show');
    jQuery.ajax({
        cache: false,
        type: 'GET',
        url: 'index.php?option=com_easysdi_shop&task=order.generateOTP&order_id='+getOrderId(element)+'&diffusion_id='+getDiffusionId(element)
    }).done(function(r){
        jQuery('#order_id').val(getOrderId(element));
        jQuery('#diffusion_id').val(getDiffusionId(element));
    });
    return false;
};

var confirmOtpAuth = function (evt) {
    var otp = jQuery('#otp').val();
    var order_id = jQuery('#order_id').val();
    var diffusion_id = jQuery('#diffusion_id').val();
    jQuery.post('index.php?option=com_easysdi_shop&task=order.downloadOTP',jQuery("#form_otp").serialize(),
     function(data)
        {
            if (data.status == 'OK'){
                window.open('index.php?option=com_easysdi_shop&task=order.downloadOTP&order_id='+order_id+'&diffusion_id='+diffusion_id+'&token='+data.token,'_self');
                jQuery('#modal-dialog-otp').modal('hide');
                jQuery('#'+order_id+'_'+diffusion_id+'_otpdownload').hide();
            }else{
                jQuery("#otpmessage").html(data.msg);
                jQuery("#otpmessage").show();
                if (data.status=='ERROR_OTPCHANCE')
                {
                    jQuery('#'+order_id+'_'+diffusion_id+'_otpdownload').hide();
                }
            }
        },'json'
      );
};
