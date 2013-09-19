var map, perimeterLayer, selectControl, selectLayer, polygonLayer, selectControl, request, myLayer, fieldid, fieldname, loadingPerimeter,miniLayer;
function initMiniMap(){
    miniLayer = new OpenLayers.Layer.Vector("miniLayer", {srsName: miniapp.mapPanel.map.projection, projection: miniapp.mapPanel.map.projection});
    miniapp.mapPanel.map.addLayer(miniLayer);    
}

function clearLayersVector(){
    for (var j = 0; j < app.mapPanel.map.layers.length; j++) {
        if (app.mapPanel.map.layers[j].__proto__.CLASS_NAME === "OpenLayers.Layer.Vector") {
            app.mapPanel.map.layers[j].removeAllFeatures();
        }
    }
    
    for (var j = 0; j < miniapp.mapPanel.map.layers.length; j++) {
        if (miniapp.mapPanel.map.layers[j].__proto__.CLASS_NAME === "OpenLayers.Layer.Vector") {
            miniapp.mapPanel.map.layers[j].removeAllFeatures();
        }
    }
}

function clearTemporaryFields(){
    jQuery('#t-perimeter').val('');
    jQuery('#t-perimetern').val('');
    jQuery('#t-surface').val('');
    jQuery('#t-features').val('');
}

function resetTemporaryFields(){
    jQuery('#t-perimeter').val(jQuery('#perimeter').val());
    jQuery('#t-perimetern').val(jQuery('#perimetern').val());
    jQuery('#t-surface').val(jQuery('#surface').val());
    jQuery('#t-features').val(jQuery('#features').val());
}

function saveTemporaryFields(){
    jQuery('#perimeter').val(jQuery('#t-perimeter').val());
    jQuery('#perimetern').val(jQuery('#t-perimetern').val());
    jQuery('#surface').val(jQuery('#t-surface').val());
    jQuery('#features').val(jQuery('#t-features').val());
}

function beforeFeatureAdded(event) {
    clearLayersVector();
    
    jQuery('#t-features').val('');
    jQuery('#t-surface').val('');
}

function resetAll() {
    resetTemporaryFields();
    clearLayersVector();
    jQuery('#btn-selection').show();
    
    if (typeof selectControl !== 'undefined') {
        selectControl.deactivate();
        jQuery('#btn-selection').removeClass('active');
        selectControl.events.unregister("featureselected", this, listenerFeatureSelected);
        selectControl.events.unregister("featureunselected", this, listenerFeatureUnselected);
        app.mapPanel.map.removeControl(selectControl);
    }
    if (app.mapPanel.map.getLayersByName("perimeterLayer").length > 0) {
        perimeterLayer.events.unregister("loadend", perimeterLayer, listenerLoadEnd);
        app.mapPanel.map.removeLayer(perimeterLayer);
        app.mapPanel.map.removeLayer(selectLayer);        
    }
    if (app.mapPanel.map.getLayersByName("myLayer").length > 0) {
        app.mapPanel.map.removeLayer(myLayer);
    }    
    for (key in drawControls) {
        var control = drawControls[key];
        control.deactivate();
    }
}

function toggleSelectControl(){
    if(selectControl.active)
        selectControl.deactivate();
    else
        selectControl.activate();
}

function cancel() {
    resetAll();
    jQuery('#modal-perimeter [id^="btn-perimeter"]').removeClass('active');
    if (jQuery('#perimeter').val() != '') {
        eval('selectPerimeter' + jQuery('#perimeter').val() + '()');
        eval('reloadFeatures' + jQuery('#perimeter').val() + '()');
        jQuery('#btn-perimeter' + jQuery('#perimeter').val()).addClass('active');
    }
}

function savePerimeter() {
    jQuery("#progress").css('visibility', 'visible');
    request = false;
    if (window.XMLHttpRequest) {
        request = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        try {
            request = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                request = false;
            }
        }
    }
    if (!request) {
        alert('Error');
        return;
    }

    var extent = {"id": jQuery('#t-perimeter').val(), 
                    "name": jQuery('#t-perimetern').val(), 
                    "surface": jQuery('#t-surface').val(), 
                    "allowedbuffer": jQuery('#allowedbuffer').val(),
                    "buffer": jQuery('#buffer').val(),
                    "features": JSON.parse(jQuery('#t-features').val())};
    var query = "index.php?option=com_easysdi_shop&task=addExtentToBasket&item=" + JSON.stringify(extent);
    request.onreadystatechange = displayExtentRecap;
    request.open("GET", query, true);
    request.send(null);
}

function displayExtentRecap() {
    if (request.readyState === 4) {
        saveTemporaryFields();
        
        jQuery('#perimeter-recap').empty();
        jQuery('#perimeter-recap').append("<div><h3>"+Joomla.JText._('COM_EASYSDI_SHOP_BASKET_SURFACE', 'Surface')+"</h3>");
        jQuery('#perimeter-recap').append("<div>" + jQuery('#surface').val() + "</div></div>");
        
        jQuery('#perimeter-recap').append("<div><h3>" + jQuery('#perimetern').val() + "</h3></div>");
        var features_text = jQuery('#features').val();
        
        if (features_text === '')
            return;
        
        try {
            var features = JSON.parse(features_text);
            
            jQuery.each(features, function(index, value) {
                if (typeof value === "undefined")
                    return true;
                
                if (typeof value.name === "undefined"){
                    jQuery('#perimeter-recap').append("<div>" + features + "</div>");
                    return false;
                }
                jQuery('#perimeter-recap').append("<div>" + value.name + "</div>");
            });
        } catch (e) {
            jQuery('#perimeter-recap').append("<div>" + features_text + "</div>");
        }
            
        
    }
}






