var map, perimeterLayer, selectControl, selectLayer, polygonLayer, selectControl, request, myLayer, fieldid, fieldname, loadingPerimeter, miniLayer, minimap, miniBaseLayer;

//Init the recapitulation map (map without control)
function initMiniMap() {
    minimap = new OpenLayers.Map({div: 'minimap', controls: []});
    miniBaseLayer = app.mapPanel.map.layers[1].clone();  
    miniBaseLayer.events.register("loadend", miniBaseLayer, initialization);
    minimap.addLayer(miniBaseLayer);
    minimap.setBaseLayer(miniBaseLayer);
    minimap.zoomToExtent(app.mapPanel.map.getExtent());
    miniLayer = new OpenLayers.Layer.Vector("miniLayer");
    minimap.addLayer(miniLayer);
    miniLayer.events.register("featuresadded", miniLayer, listenerMiniFeaturesAdded);
}

//Call after a feature was selected or drawn in the mini map
var listenerMiniFeaturesAdded = function() {
    minimap.zoomToExtent(miniLayer.getDataExtent());
};

//Call after a feature was selected or drawn in the map
var listenerFeatureAdded = function(e) {
    miniLayer.addFeatures([e.feature.clone()]);
    orderSurfaceChecking();
};

//Check if the surface of the selection is applicable
function orderSurfaceChecking(){
    var toobig = false;
    var toosmall = false;
    if (jQuery('#surfacemax').val() !== '') {
        if (parseFloat(jQuery('#t-surface').val()) > parseFloat(jQuery('#surfacemax').val()))
            toobig = true;
    }
    if (jQuery('#surfacemin').val() !== '') {
        if (parseFloat(jQuery('#t-surface').val()) < parseFloat(jQuery('#surfacemin').val()))
            toosmall = true;
    }
    if (toobig || toosmall) {
        jQuery("#alert_template").empty();
        jQuery("#alert_template").append('<span>Your current selection of ' + jQuery('#t-surface').val() + ' is not in the allowed surface range [' + jQuery('#surfacemin').val() + ',' + jQuery('#surfacemax').val() + '].</span>');
        jQuery('#alert_template').fadeIn('slow');
        jQuery('#btn-saveperimeter').attr("disabled", "disabled");
    } else {
        jQuery('#alert_template').fadeOut('slow');
        jQuery('#btn-saveperimeter').removeAttr("disabled");
    }
}

//Remove all geometries drawn
function clearLayersVector() {
    for (var j = 0; j < app.mapPanel.map.layers.length; j++) {
        if (app.mapPanel.map.layers[j].id.indexOf("Vector") !== -1) {
            app.mapPanel.map.layers[j].removeAllFeatures();
        }
    }
    for (var j = 0; j < minimap.layers.length; j++) {
        if (minimap.layers[j].id.indexOf("Vector") !== -1) {
            minimap.layers[j].removeAllFeatures();
        }
    }
}

//Clear temporary fields
function clearTemporaryFields() {
    jQuery('#t-perimeter').val('');
    jQuery('#t-perimetern').val('');
    jQuery('#t-surface').val('');
    jQuery('#t-features').val('');
    jQuery('#alert_template').fadeOut('slow');
    jQuery('#btn-saveperimeter').removeAttr("disabled");
    
}

//Reset temporary fields with initial values
function resetTemporaryFields() {
    jQuery('#t-perimeter').val(jQuery('#perimeter').val());
    jQuery('#t-perimetern').val(jQuery('#perimetern').val());
    jQuery('#t-surface').val(jQuery('#surface').val());
    jQuery('#t-features').val(jQuery('#features').val());
    jQuery('#t-level').val(jQuery('#level').val());
    jQuery('#alert_template').fadeOut('slow');
    jQuery('#btn-saveperimeter').removeAttr("disabled");
}

//Push temporary fields to final fields 
function saveTemporaryFields() {
    jQuery('#perimeter').val(jQuery('#t-perimeter').val());
    jQuery('#perimetern').val(jQuery('#t-perimetern').val());
    jQuery('#surface').val(jQuery('#t-surface').val());
    jQuery('#features').val(jQuery('#t-features').val());
    jQuery('#level').val(jQuery('#t-level').val());
}

//
function beforeFeatureAdded(event) {
    clearLayersVector();
    jQuery('#t-features').val('');
    jQuery('#t-surface').val(JSON.stringify(event.feature.geometry.getGeodesicArea(app.mapPanel.map.projection)));
}

//Reset all values to initial ones
function resetAll() {
    resetTemporaryFields();

    clearLayersVector();
    jQuery('#btns-selection').show();

    if (typeof selectControl !== 'undefined') {
        selectControl.deactivate();
        selectControl.events.unregister("featureselected", this, listenerFeatureSelected);
        selectControl.events.unregister("featureunselected", this, listenerFeatureUnselected);
        app.mapPanel.map.removeControl(selectControl);
    }
    if (app.mapPanel.map.getLayersByName("perimeterLayer").length > 0) {
        app.mapPanel.map.removeLayer(app.mapPanel.map.getLayersByName("perimeterLayer")[0]);
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

//Toggle controls
function toggleSelectControl(action) {
    if (action === 'selection') {
        if (typeof selectControl !== 'undefined') {
            selectControl.activate();
        }
    } else if (action === 'pan') {
        selectControl.deactivate();
    } else {
        resetAll();
    }
}

//Reload initial extent selection
function cancel() {
    resetAll();
    jQuery('#modal-perimeter [id^="btn-perimeter"]').removeClass('active');
    if (jQuery('#perimeter').val() !== '') {
        eval('selectPerimeter' + jQuery('#perimeter').val() + '()');
        eval('reloadFeatures' + jQuery('#perimeter').val() + '()');
        jQuery('#btn-perimeter' + jQuery('#perimeter').val()).addClass('active');
    }
    if (jQuery('#level').val() !== '') {
       app.mapPanel.map.indoorlevelslider.changeIndoorLevelByCode(app.mapPanel.map.indoorlevelslider,jQuery('#level').val());
    }
}

//Manage display according to savePerimeter response
function updateDisplay (response){
    if (response.MESSAGE && response.MESSAGE === 'OK') {
                saveTemporaryFields();
                try {
                    var features = JSON.parse(jQuery('#features').val());
                    if (jQuery.isArray(features)) {
                        jQuery('#perimeter-recap-details').empty();
                        jQuery.each(features, function() {
                            if (typeof this === "undefined")
                                return;
                            if (typeof this.name === "undefined") {
                                jQuery('#perimeter-recap-details').append(jQuery('<div>' + features + '</div>'));
                            }
                            jQuery('#perimeter-recap-details').append(jQuery('<div>' + this.name + '</div>'));
                        });
                        jQuery('#perimeter-recap-details').show();

                    }else{
                        jQuery('#perimeter-recap-details').empty().hide();
                    }
                } catch (e) {
                    jQuery('#perimeter-recap-details').empty().hide();
                }

                if (response.extent.name !== '') {
                    jQuery('#perimeter-recap-details-title > h4').html(response.extent.name);
                }
                else {
                    jQuery('#perimeter-recap-details-title > h4').empty();
                }
                if (response.extent.levelcode !== '') {
                    jQuery('#perimeter-recap > div:nth-child(2) > div').html(response.extent.levelcode);
                    jQuery('#perimeter-recap').show();
                }
                else {
                    jQuery('#perimeter-recap > div:nth-child(2) > div').empty();
                }
                if (response.extent.surface !== '') {
                    jQuery('#perimeter-recap > div:nth-child(1) > div').html(
                            (response.extent.surface > maxmetervalue)
                            ? (response.extent.surface / 1000000).toFixed(surfacedigit) + Joomla.JText._('COM_EASYSDI_SHOP_BASKET_KILOMETER', ' km2')
                            : parseFloat(response.extent.surface).toFixed(surfacedigit) + Joomla.JText._('COM_EASYSDI_SHOP_BASKET_METER', ' m2')
                            );
                    jQuery('#perimeter-recap').show();
                }
                else {
                    jQuery('#perimeter-recap > div:nth-child(1) > div').empty();
                }

                //pricing
                updatePricing(response.pricing);
            }

            return false;
}



/**/
var removeFromBasket = function(id) {
    current_id = id;
    jQuery('#modal-dialog-remove').modal('show');
};

var actionRemove = function() {
    jQuery('#task').val('removeFromBasket');
    jQuery('#id').val(current_id);
    jQuery('#adminForm').submit();
};

var checkTouState = function() {
    jQuery('#toolbar-edit>button, #toolbar-publish>button').attr('disabled', !jQuery('#termsofuse').prop('checked'));
};

var processProgress = function(txt, rate) {
    jQuery('#processProgressText').text(txt);
    if (rate)
        jQuery('#processProgress').css('width', rate + '%');
};