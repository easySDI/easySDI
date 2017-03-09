var map, miniBaseLayers = [], perimeterLayer, selectLayer, polygonLayer, boxLayer, selectControl, request, myLayer, fieldid, fieldname, loadingPerimeter, miniLayer, minimap, slider, customStyleMap, alertControl, userperimeter;
var meterToKilometer = 1000000;
var shopPerimeterSurface_m2 = 'm²';
var shopPerimeterSurface_km2 = 'km²';

//Init the recapitulation map (map without control)
function initMiniMap() {
    initStyleMap();
    minimap = new OpenLayers.Map({div: 'minimap', controls: []});
    copyBaseLayers();
    minimap.zoomToExtent(app.mapPanel.map.getExtent());
    miniLayer = new OpenLayers.Layer.Vector("miniLayer", {styleMap: customStyleMap});
    minimap.addLayer(miniLayer);
    miniLayer.events.register("featuresadded", miniLayer, listenerMiniFeaturesAdded);
}

/**
 * Clone the layers of the map, and register the event to load the rest of the app.
 * Does not clone the vector layers if any.
 */
function copyBaseLayers() {
    var miniBaseLayersIndex = 0;
    for (var j = 0; j < app.mapPanel.map.layers.length; j++) {
        //only copy non "vector" layers
        if (app.mapPanel.map.layers[j].id.indexOf("Vector") == -1) {
            miniBaseLayers[miniBaseLayersIndex] = app.mapPanel.map.layers[j].clone();
            //register the loadend only on the first layer
            if (miniBaseLayersIndex == 0) {
                miniBaseLayers[0].events.register("loadend", miniBaseLayers[0], initialization);
            }
            miniBaseLayersIndex++;
        }
    }
    minimap.addLayers(miniBaseLayers);
    minimap.setBaseLayer(miniBaseLayers[0]);
}

/**
 * Initialise the StyleMap with page values
 */
function initStyleMap() {
    customStyleMap = new OpenLayers.StyleMap({
        "default": new OpenLayers.Style({
            fillColor: "${getFillColor}",
            fillOpacity: "${getFillOpacity}",
            pointRadius: mapPointRadius,
            strokeColor: "${getStrokeColor}",
            strokeDashstyle: "solid",
            strokeLinecap: "round",
            strokeOpacity: 1,
            strokeWidth: "${getStrokeWidth}",
            graphicName: "circle"
        }, {
            context: {
                getStrokeColor: function (feature) {
                    if (feature.geometry != null)
                        return feature.geometry.CLASS_NAME === "OpenLayers.Geometry.Point" ? mapStrokeColor : mapStrokeColor;
                    return mapStrokeColor;
                },
                getStrokeWidth: function (feature) {
                    if (feature.geometry != null)
                        return feature.geometry.CLASS_NAME === "OpenLayers.Geometry.Point" ? mapPointStrokeWidth : mapStrokeWidth;
                    return mapPointStrokeWidth;
                },
                getFillColor: function (feature) {
                    if (feature.geometry != null)
                        return feature.geometry.CLASS_NAME === "OpenLayers.Geometry.Point" ? "#FFFFFF" : mapFillColor;
                    return "#FFFFFF";
                },
                getFillOpacity: function (feature) {
                    if (feature.geometry != null)
                        return feature.geometry.CLASS_NAME === "OpenLayers.Geometry.Point" ? 1 : mapFillOpacity;
                    return mapFillOpacity;
                }
            }
        }),
        "transform": new OpenLayers.Style({
            cursor: "${role}",
            pointRadius: mapPointRadius,
            fillColor: "#FFFFFF",
            fillOpacity: 1,
            strokeWidth: mapPointStrokeWidth,
            strokeColor: mapStrokeColor
        }, {
            context: {
                getDisplay: function (feature) {
                    // hide the resize handle at the south-east corner
                    return feature.attributes.role === "se-resize" ? "none" : "";
                }
            }
        }),
        "temporary": {
            strokeColor: mapStrokeColor,
            fillColor: mapFillColor,
            fillOpacity: mapFillOpacity,
            strokeWidth: mapStrokeWidth},
        "select": {
            strokeColor: mapStrokeColor,
            fillColor: mapFillColor,
            fillOpacity: mapFillOpacity,
            strokeWidth: mapStrokeWidth},
        "rotate": new OpenLayers.Style({
            externalGraphic: mapRotateIconURL,
            fillOpacity: 1,
            graphicXOffset: 8,
            graphicYOffset: 8,
            graphicWidth: 20,
            graphicHeight: 20,
            display: "${getDisplay}",
            pointRadius: 20,
            fillColor: "#ddd",
            strokeColor: mapStrokeColor,
            rotation: "${getRotation}"
        }, {
            context: {
                getDisplay: function (feature) {
                    // only display the rotate handle at the south-east corner
                    return feature.attributes.role === "se-rotate" ? "" : "none";
                },
                getRotation: function (feature) {
                    // rotation of transformbox
                    return -1 * selectControl.rotation;
                }
            }
        })
    });
}

//Call after a feature was selected or drawn in the map
var listenerMiniFeaturesAdded = function () {
    minimap.zoomToExtent(miniLayer.getDataExtent());
};

//Call after a feature was selected or drawn in the map
var listenerFeatureAdded = function (e) {
    orderSurfaceChecking();
};

//Zoom on the geometry added (drawn or user specific perimeter loaded)
var listenerFeatureAddedToZoom = function (e) {
    app.mapPanel.map.zoomToExtent(e.object.getDataExtent());
};

//Check if the surface of the selection is applicable
function orderSurfaceChecking() {
    var tmpSurface = 0;
    var isSelfIntersect = false;

    for (var j = 0; j < app.mapPanel.map.layers.length; j++) {
        if (app.mapPanel.map.layers[j].id.indexOf("Vector") !== -1) {

            //do not count Draw Layers
            if (app.mapPanel.map.layers[j].name.indexOf("OpenLayers.Handler.") >= 0) {
                continue;
            }

            var layer = app.mapPanel.map.layers[j];
            for (var f = 0; f < layer.features.length; f++) {
                if (layer.features[f].geometry instanceof OpenLayers.Geometry.Polygon || layer.features[f].geometry instanceof OpenLayers.Geometry.MultiPolygon) {
                    tmpSurface += layer.features[f].geometry.getGeodesicArea(app.mapPanel.map.projection);
                }
                if (layer.features[f].geometry instanceof OpenLayers.Geometry.Polygon) {
                    isSelfIntersect = checkSelfIntersect(layer.features[f]);
                }
            }
        }
    }
    jQuery('#t-surface').val(tmpSurface);

    //display surface in modal
    displaySurface('#shop-perimeter-modal-title-surface', tmpSurface);

    if (isSelfIntersect) {
        //More than 2 intersectios for a line, mean that a line intersects another one.
        var message = Joomla.JText._('COM_EASYSDI_SHOP_BASKET_ERROR_SELFINTERSECT', 'Self-intersecting perimeter is not allowed');
        alertControl.raiseAlert('<span>' + message + '</span>');

    } else if (jQuery('#surfacemax').val() !== '' && parseFloat(tmpSurface) > parseFloat(jQuery('#surfacemax').val())) {
        //too big
        var message = getSurfaceMessage(Joomla.JText._('COM_EASYSDI_SHOP_BASKET_ERROR_AREA_TOO_LARGE', 'Your current selection of %SURFACE exceeds the %SURFACELIMIT limit.'), tmpSurface, parseFloat(jQuery('#surfacemax').val()));
        alertControl.raiseAlert('<span>' + message + '</span>');

    } else if (jQuery('#surfacemin').val() !== '' && parseFloat(tmpSurface) < parseFloat(jQuery('#surfacemin').val())) {
        //too small
        var message = getSurfaceMessage(Joomla.JText._('COM_EASYSDI_SHOP_BASKET_ERROR_AREA_TOO_SMALL', 'Your current selection of %SURFACE is under the %SURFACELIMIT minimum.'), tmpSurface, parseFloat(jQuery('#surfacemin').val()));
        alertControl.raiseAlert('<span>' + message + '</span>');

    } else {
        //no problems
        alertControl.clearAlert();
    }
}

//Displqay a surface in an HTML target
function displaySurface(csstarget, surface) {
    jQuery(csstarget).html(
            " (" +
            ((surface > maxmetervalue)
                    ? (surface / meterToKilometer).toFixed(surfacedigit) + Joomla.JText._('COM_EASYSDI_SHOP_BASKET_KILOMETER', ' km²')
                    : parseFloat(surface).toFixed(surfacedigit) + Joomla.JText._('COM_EASYSDI_SHOP_BASKET_METER', ' m²')) +
            ")");
}

//build the message for too big / too low surface
function getSurfaceMessage(baseMessage, tmpSurface, surfaceLimit) {

    var unitString = shopPerimeterSurface_m2;

    //switch to km2 for big surface
    if (tmpSurface >= maxmetervalue) {
        tmpSurface = tmpSurface / meterToKilometer;
        surfaceLimit = surfaceLimit / meterToKilometer;
        unitString = shopPerimeterSurface_km2;
    }
    return baseMessage.replace('%SURFACE', tmpSurface.toFixed(surfacedigit) + ' ' + unitString).replace('%SURFACELIMIT', surfaceLimit.toFixed(surfacedigit) + ' ' + unitString);
}

//check if a feature is selfintersecting
function checkSelfIntersect(feature) {
    var lines = new Array();
    var isSelfIntersect = false;

    // do not test non-polygons
    if (feature.geometry instanceof OpenLayers.Geometry.Polygon) {
        var polygonSize = feature.geometry.components[0].components.length;
        var components = feature.geometry.components[0].components;
        var i = 0;
        while (i < polygonSize - 1) {
            lines.push(new OpenLayers.Geometry.LineString([
                new OpenLayers.Geometry.Point(components [i].x, components [i].y),
                new OpenLayers.Geometry.Point(components [i + 1].x, components [i + 1].y)
            ]));

            i++;
        }
        for (i = 0; i < lines.length; i++) {
            count = 0;
            for (j = 0; j < lines.length; j++) {
                //Do not compare a line with itslf
                if (i != j) {
                    if (lines[i].intersects(lines[j])) {
                        count++;
                    }
                }
                if (count > 2) {
                    //More than 2 intersectios for a line, mean that a line intersects another one.
                    isSelfIntersect = true;
                    break;
                }
            }
        }
    }
    return isSelfIntersect;
}

//Remove all geometries drawn
function clearLayersVector() {
    for (var j = 0; j < app.mapPanel.map.layers.length; j++) {
        if (app.mapPanel.map.layers[j].id.indexOf("Vector") !== -1) {
            app.mapPanel.map.layers[j].removeAllFeatures();
        }
    }
    miniLayer.removeAllFeatures();
    /*for (var j = 0; j < minimap.layers.length; j++) {
     if (minimap.layers[j].id.indexOf("Vector") !== -1) {
     minimap.layers[j].removeAllFeatures();
     }
     }*/
    jQuery('#perimeter-recap-details').empty();
}

//Clear temporary fields
function clearTemporaryFields() {
    jQuery.each(['perimeter', 'perimetern', 'surface', 'features', 'level'], function (index, value) {
        jQuery('#t-' + value).val('');
    })
    alertControl.clearAlert();

}

//Reset temporary fields with initial values
function resetTemporaryFields() {
    jQuery.each(['perimeter', 'perimetern', 'surface', 'features', 'level', 'freeperimetertool'], function (index, value) {
        jQuery('#t-' + value).val(jQuery('#' + value).val());
    })
    freePerimeterTool = jQuery('#t-freeperimetertool').val();
    alertControl.clearAlert();
}

//Push temporary fields to final fields 
function saveTemporaryFields() {
    jQuery.each(['perimeter', 'perimetern', 'surface', 'features', 'level', 'freeperimetertool'], function (index, value) {
        jQuery('#' + value).val(jQuery('#t-' + value).val());
    })
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
    reset();
}

//Reset all except level to initial ones
function resetAllSelection() {
    jQuery.each(['perimeter', 'perimetern', 'surface', 'features', 'freeperimetertool'], function (index, value) {
        jQuery('#' + value).val(jQuery('#t-' + value).val());
    })
    reset();
}

function reset() {
    freePerimeterTool = '';
    removeSelectCounter();

    clearLayersVector();
    jQuery('#perimeter-level').hide();
    jQuery('#btns-selection').show();
    jQuery('#shop-perimeter-modal-title-surface').empty();

    disableDrawControls();
}

function disableDrawControls() {
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

    /*if (app.mapPanel.map.getLayersByName("myLayer").length > 0) {
     app.mapPanel.map.removeLayer(myLayer);
     }*/
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
    } else {
        resetAllSelection();
    }
}

//add the alert control (can show message in map)
function addAlertControl(map) {
    alertControl = new OpenLayers.Control.Panel({
        displayClass: "sdiMapAlertControl hide",
    });
    OpenLayers.Util.extend(alertControl, {
        raiseAlert: function (message) {
            jQuery('#btn-saveperimeter').attr("disabled", "disabled");
            if (message)
                jQuery('.sdiMapAlertControl').html('<p style="vertical-align: middle">' + message + '</p>');
            else
                jQuery('.sdiMapAlertControl').empty();
            jQuery('.sdiMapAlertControl').show();
        },
        clearAlert: function () {
            jQuery('.sdiMapAlertControl').hide();
            jQuery('.sdiMapAlertControl').empty();
            jQuery('#btn-saveperimeter').removeAttr("disabled");

        }
    });

    map.addControl(alertControl);
    alertControl.deactivate();
}

//add the visibility checker
function addVisibilityChecks(map) {
    map.events.register('changelayer', this, function (e) {
        if (e.property == "visibility" && e.layer.name == "perimeterLayer") {
            checkPerimeterLayerVisibility(map);
        }
    });

    map.events.register('addlayer', this, function (e) {
        if (e.layer.name == "perimeterLayer") {
            checkPerimeterLayerVisibility(map);
        }
    });

    map.events.register('removelayer', this, function (e) {
        if (e.layer.name == "perimeterLayer") {
            checkPerimeterLayerVisibility(map);
        }
    });
}

//check perimeterLayer visiblity
function checkPerimeterLayerVisibility(map) {
    removeVisiblityWarning();
    if (map.getLayersByName('perimeterLayer').length) {
        if (!map.getLayersByName('perimeterLayer')[0].inRange) {
            showVisiblityWarning();
        }
    }

}

//shows an icon next to the selected perimeter if layer is not visible
function showVisiblityWarning() {
    var warningButton = jQuery('<button disabled onclick="return false;" id="perimeter-visibility-warn-button" class="btn btn-warning disabled hasTip" title="' + Joomla.JText._('COM_EASYSDI_SHOP_BASKET_LAYER_OUT_OF_RANGE_TITLE', 'Perimeter is not visible') + '"><i class="icon icon-eye-blocked"></i></button>');
    jQuery('#perimeter-select-counter').after(warningButton);
    warningButton.popover({
        trigger: 'hover ',
        placement: 'bottom',
        content: Joomla.JText._('COM_EASYSDI_SHOP_BASKET_LAYER_OUT_OF_RANGE', 'Please zoom')
    });

}

//remove the visibility warn icon
function removeVisiblityWarning() {
    jQuery('#perimeter-visibility-warn-button').remove();
}

//Reload initial extent selection
function cancel() {
    resetAll();
    jQuery('#modal-perimeter [id^="btn-perimeter"]').removeClass('active');
    if (jQuery('#perimeter').val() !== '') {
        eval('reloadFeatures' + jQuery('#perimeter').val() + '()');
        jQuery('#btn-perimeter' + jQuery('#perimeter').val()).addClass('active');
    }
    if (jQuery('#level').val() !== '' && slider) {
        var level = JSON.parse(jQuery('#level').val());
        app.mapPanel.map.indoorlevelslider.changeIndoorLevelByCode(app.mapPanel.map.indoorlevelslider, level.code);
        jQuery('#perimeter-level-value').val(app.mapPanel.map.indoorlevelslider.getLevel().label);
        jQuery('#perimeter-level').show();
    }

    setFreePerimeterTool(jQuery('#freeperimetertool').val());
}

//Add counter next to selection tool
function addSelectCounter(perimeterId) {
    removeSelectCounter();
    var counterHTML = '<button disabled onclick="return false;" id="perimeter-select-counter" data-perimeter-id="' + perimeterId + '" class="btn btn-primary disabled">0</button>';
    jQuery('#btn-perimeter' + perimeterId).after(counterHTML);
    jQuery("#perimeter-select-counter").popover({
        container: '#btn-perimeter' + perimeterId,
        trigger: 'hover ',
        placement: 'bottom',
        title: Joomla.JText._('COM_EASYSDI_SHOP_BASKET_PERIMETER_YOUR_SELECTION', 'Your selection:'),
        content: function () {
            var selectCounterPopover = "";
            if (selectLayer.features.length > 0) {
                for (var i = 0; i < selectLayer.features.length; i++) {
                    selectCounterPopover += (selectLayer.features[i].attributes[fieldname] + '<br/>');
                }
            } else {
                selectCounterPopover += Joomla.JText._('COM_EASYSDI_SHOP_BASKET_PERIMETER_NO_PERIMETER_SELECTED', 'No perimeter selected');
            }
            return selectCounterPopover;
        }
    });
}

//Update select counter
function updateSelectCounter(features) {
    jQuery("#perimeter-select-counter").text(features.length);
}

//Removes all select counters
function removeSelectCounter() {
    jQuery("#perimeter-select-counter").remove();
}

//
var number_format = function (number, decimals, dec_point, thousands_sep) {
    number = (number + '')
            .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                        .toFixed(prec);
            };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
            .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
                .join('0');
    }
    return s.join(dec);
};

/**
 * priceFormatter - a js implementation of the php method located in easysdi_shop.php
 * @param {mixed} price
 * @param {boolean} displayCurrency
 * @returns {String}
 */
var priceFormatter = function (price, displayCurrency) {
    if ('undefined' === typeof displayCurrency)
        displayCurrency = true;
    var c = displayCurrency ? ' ' + currency : '';

    if (price != null && 'undefined' !== typeof price && price != 0) {
        price = number_format(
                price,
                digit_after_decimal,
                decimal_symbol,
                digit_grouping_symbol
                );
        return price + c;
    } else if ('undefined' !== typeof price && price != null && price == 0) {
        return '0' + c;
    } else {
        return '-';
    }
};

//
var updatePricing = function (pricing) {
    if (!pricing.isActivated)
        return;

    //total amount
    jQuery('span.pricingTotalAmountTI').html(priceFormatter(pricing.cal_total_amount_ti));
    jQuery('span#pricingTotalAmountTI-container').show();

    //suppliers
    jQuery.each(pricing.suppliers, function (supplierId, supplier) {
        //header
        jQuery('table[rel=' + supplierId + ']>thead>tr>td.price_column').show();

        //products
        jQuery.each(supplier.products, function (productId, product) {
            var displayedPrice;
            if (product.cfg_pricing_type == 1) {
                displayedPrice = Joomla.JText._('COM_EASYSDI_SHOP_BASKET_PRODUCT_FREE', 'free');
            } else {
                displayedPrice = priceFormatter(product.cal_total_amount_ti);
                var as = '',
                        discount = '',
                        title = '',
                        profileDiscount = parseFloat(product.cfg_pct_category_profile_discount),
                        supplierDiscount = parseFloat(product.cfg_pct_category_supplier_discount);

                if (profileDiscount > 0 || supplierDiscount > 0) {
                    if (supplierDiscount > profileDiscount) {
                        as = product.ind_lbl_category_supplier_discount;
                        discount = supplierDiscount;
                    } else {
                        as = product.ind_lbl_category_profile_discount;
                        discount = profileDiscount;
                    }
                    title = Joomla.JText._('COM_EASYSDI_SHOP_BASKET_TOOLTIP_REBATE_INFO', 'As %s, you get a discount of %s%%').replace(/(.*)(?:%s)(.*)(?:%s%)(.*)/gi, '$1' + as + '$2' + discount + '$3');
                    jQuery('table[rel=' + supplierId + ']>tbody>tr[rel=' + productId + ']>td.price_column>i.icon-info').attr('title', title).show();
                } else {
                    jQuery('table[rel=' + supplierId + ']>tbody>tr[rel=' + productId + ']>td.price_column>i.icon-info').attr('title', title).hide();
                }
            }
            var i = jQuery('table[rel=' + supplierId + ']>tbody>tr[rel=' + productId + ']>td.price_column>i.icon-info');
            jQuery('table[rel=' + supplierId + ']>tbody>tr[rel=' + productId + ']>td.price_column').html(displayedPrice + ' ').append(i).show();
            jQuery('table[rel=' + supplierId + ']>tbody>tr[rel=' + productId + ']>td.price_column>i.icon-info').tooltip({"html": true, "container": "body"});
        });

        //footer
        jQuery('table[rel=' + supplierId + ']>tfoot>tr>td.supplier_cal_fee_ti').html(priceFormatter(supplier.cal_fee_ti));
        jQuery('table[rel=' + supplierId + ']>tfoot>tr>td.supplier_cal_total_amount_ti').html(priceFormatter(supplier.cal_total_amount_ti));
        jQuery('table[rel=' + supplierId + ']>tfoot>tr>td.supplier_cal_total_rebate_ti').html(priceFormatter(supplier.cal_total_rebate_ti));
        jQuery('table[rel=' + supplierId + ']>tfoot').show();
    });

    //Estimate button
    if (pricing.cal_total_amount_ti === null) {
        jQuery('#toolbar-estimate>button').show();
    } else {
        jQuery('#toolbar-estimate>button').hide();
    }

    //platform
    jQuery('span.pricingFeeTI').html(priceFormatter(pricing.cal_fee_ti)).show();
    jQuery('#pricingTotal-table').show();
};

/**
 * Call after user validates his extent drawing
 */
function savePerimeter() {

    //clean mini layer then copy all features from vector layers
    miniLayer.removeAllFeatures();
    for (var j = 0; j < app.mapPanel.map.layers.length; j++) {
        if (app.mapPanel.map.layers[j].id.indexOf("Vector") !== -1) {
            var layer = app.mapPanel.map.layers[j];
            for (var f = 0; f < layer.features.length; f++) {
                if (layer.features[f].geometry instanceof OpenLayers.Geometry.Polygon || layer.features[f].geometry instanceof OpenLayers.Geometry.MultiPolygon) {
                    miniLayer.addFeatures([layer.features[f].clone()]);
                }
            }
        }
    }

    if (jQuery('#t-perimeter').val() == '') {
        jQuery('#perimeter-recap').empty();
    } else {
        jQuery("#progress").css('visibility', 'visible');

        var extent = {"id": jQuery('#t-perimeter').val(),
            "name": jQuery('#t-perimetern').val(),
            "surface": jQuery('#t-surface').val(),
            "level": jQuery('#t-level').val(),
            "features": jQuery('#t-features').val(),
            "freeperimetertool": jQuery('#t-freeperimetertool').val()};

        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_easysdi_shop&task=addExtentToBasket",
            data: "item=" + encodeURIComponent(JSON.stringify(extent))
        }).done(updateDisplay);
    }
}

function changePerimeterEditLabel() {
    var theLabel = jQuery('#features').val() === '' ? Joomla.JText._('COM_EASYSDI_SHOP_BASKET_DEFINE_PERIMETER') : Joomla.JText._('COM_EASYSDI_SHOP_BASKET_MODIFY_PERIMETER');
    jQuery('#modal-perimeter #myModalLabel #shop-perimeter-modal-title').text(theLabel);
    jQuery('#defineOrderBtnLbl').text(theLabel);
}

/**
 * Manage display according to savePerimeter response
 * @param response savePerimeter response
 */
function updateDisplay(response) {
    if (response.MESSAGE && response.MESSAGE === 'OK') {
        saveTemporaryFields();
        try {
            var features = JSON.parse(jQuery('#features').val());
            if (jQuery.isArray(features)) {
                jQuery('#perimeter-recap-details').empty();
                jQuery.each(features, function () {
                    if (typeof this === "undefined")
                        return;
                    if (typeof this.name === "undefined") {
                        jQuery('#perimeter-recap-details').append(jQuery('<div>' + features + '</div>'));
                    } else {
                        jQuery('#perimeter-recap-details').append(jQuery('<div>' + this.name + '</div>'));
                    }
                });
                jQuery('#perimeter-recap-details').show();

            } else {
                jQuery('#perimeter-recap-details').empty().hide();
            }
        } catch (e) {
            jQuery('#perimeter-recap-details').empty().hide();
        }

        if (response.extent.name !== '') {
            jQuery('#perimeter-recap-details-title > h4').html(response.extent.name);
        } else {
            jQuery('#perimeter-recap-details-title > h4').empty();
        }
        if (response.extent.level !== '') {
            jQuery('#perimeter-level-value').html(JSON.parse(response.extent.level).label);
            jQuery('#perimeter-level').show();
            jQuery('#perimeter-recap').show();
        } else {
            jQuery('#perimeter-level-value').empty();
            jQuery('#perimeter-level').hide();
        }
        if (response.extent.surface !== '') {
            displaySurface('#shop-perimeter-title-surface', response.extent.surface);
            jQuery('#perimeter-recap').show();
        } else {
            jQuery('#shop-perimeter-title-surface').empty();
        }
        //btns
        changePerimeterEditLabel();
        //pricing
        updatePricing(response.pricing);
    }
    return false;
}

/**
 * Check third party details if enabled and a TP is selected
 * @returns {Boolean}
 */
function checkThirdPartyDetails() {
    //check if details fields are present and a third party selected
    if (jQuery('select#thirdparty').val() != -1 && jQuery('#mandate_ref').length) {

        if (!jQuery('#mandate_ref').val().length)
            return false;
        if (!jQuery('#mandate_contact').val().length)
            return false;
        if (!jQuery('#mandate_email').val().length)
            return false;
        //mail regex
        var mailRegex = /^[a-zA-Z0-9.!#$%&â€™*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
        return mailRegex.test(jQuery('#mandate_email').val());
    }
    return true;
}

/**
 * Shows the basket error modal
 * @param {type} message Modal body
 * @param {type} title Modal title
 */
function showBasketModalError(message, title) {
    jQuery('#modal-error .modal-header h3').text(title);
    jQuery('#modal-error .modal-body').text(message);
    jQuery('#modal-error').modal('show');
}



/**/
var removeFromBasket = function (id) {
    current_id = id;
    jQuery('#modal-dialog-remove').modal('show');
};

var actionRemove = function () {
    jQuery('#task').val('removeFromBasket');
    jQuery('#id').val(current_id);
    jQuery('#adminForm').submit();
};

var checkTouState = function () {
    jQuery('#toolbar-estimate>button, #toolbar-order>button').attr('disabled', !jQuery('#termsofuse').prop('checked'));
};

var processProgress = function (txt, rate) {
    jQuery('#processProgressText').text(txt);
    if (rate)
        jQuery('#processProgress').css('width', rate + '%');
};

var sendBasket = function () {
    jQuery('#myModalProcess').modal('show');
    processProgress('Initialisation');
    jQuery.ajax({
        url: jQuery('#adminForm').attr('action'),
        type: 'POST',
        data: jQuery('#adminForm').serialize()
    }).done(function (data) {
        var text = Joomla.JText._('COM_EASYSDI_SHOP_BASKET_PROCESS_PROGRESSING').replace('%1', data.treated).replace('%2', data.total);
        processProgress(text, data.rate);
        setTimeout(sendProduct, 500);
    });
    return false;
};

var sendProduct = function () {
    jQuery.ajax({
        url: 'index.php?option=com_easysdi_shop&task=basket.saveProduct',
        type: 'POST',
        cache: false,
        data: formToken
    }).done(function (data) {
        if ('undefined' !== typeof data.total) {
            var text = Joomla.JText._('COM_EASYSDI_SHOP_BASKET_PROCESS_PROGRESSING').replace('%1', data.treated).replace('%2', data.total);
            processProgress(text, data.rate);

            if (data.rate < 100)
                setTimeout(sendProduct, 100);
            else
                setTimeout(closeBasket, 1000);
        } else {
            for (var el in data) {
                Joomla.renderMessages({el: data[el]});
                jQuery('#myModalProcess').modal('hide');
            }
        }

    });
    return false;
};

var closeBasket = function () {
    processProgress(Joomla.JText._('COM_EASYSDI_SHOP_BASKET_PROCESS_ENDING'));
    jQuery.ajax({
        url: 'index.php?option=com_easysdi_shop&task=basket.finalizeSave',
        type: 'POST',
        cache: false,
        data: formToken
    }).done(function (data) {
        document.location = data.redirect;
    });
    return false;
};

var thirdpartyInfoVisibility = function () {
    if (jQuery('select#thirdparty').val() != -1)
        jQuery('#thirdparty-info').show();
    else
        jQuery('#thirdparty-info').hide();
};

jQuery(document).on('change', 'select#thirdparty', function (e) {
    var tp = jQuery(e.target).val();
    var on = jQuery('#ordername').val();
    var rf = jQuery('#mandate_ref').val();
    var ct = jQuery('#mandate_contact').val();
    var em = jQuery('#mandate_email').val();
    jQuery.ajax({
        type: "POST",
        url: "index.php?option=com_easysdi_shop&task=basket.saveBasketToSession",
        data: {ordername: on,
            thirdparty: tp,
            mandate_ref: rf,
            mandate_contact: ct,
            mandate_email: em}
    }).done(function (r) {
        thirdpartyInfoVisibility();
        //pricing
        updatePricing(r.pricing);
        return false;
    });
});

jQuery(document).on('change', '#ordername', function (e) {
    savebasket();
});
jQuery(document).on('change', '#mandate_ref', function (e) {
    savebasket();
});
jQuery(document).on('change', '#mandate_contact', function (e) {
    savebasket();
});
jQuery(document).on('change', '#mandate_email', function (e) {
    savebasket();
});

var savebasket = function () {
    var tp = jQuery('select#thirdparty').val();
    var on = jQuery('#ordername').val();
    var rf = jQuery('#mandate_ref').val();
    var ct = jQuery('#mandate_contact').val();
    var em = jQuery('#mandate_email').val();
    jQuery.ajax({
        type: "POST",
        url: "index.php?option=com_easysdi_shop&task=basket.saveBasketToSession",
        data: {ordername: on,
            thirdparty: tp,
            mandate_ref: rf,
            mandate_contact: ct,
            mandate_email: em}
    }).done(function (r) {
        return false;
    });
};

jQuery(document).on('click', '#btn-login', function () {
    document.location.href = 'index.php?option=com_users&view=login&return=' + btoa(document.location.href);
    return false;
});

jQuery(document).on('click', '#btn-create-account', function () {
    document.location.href = 'index.php?option=com_users&view=registration&return=' + btoa(document.location.href);
    return false;
});

jQuery(document).on('change', '#termsofuse', checkTouState);

jQuery(document).on('click', 'td.action_column>a', function () {
    removeFromBasket(jQuery(this).closest('tr').attr('rel'));
    return false;
});


jQuery(document).ready(function () {
    checkTouState();

    thirdpartyInfoVisibility();

    //disbale 'return' key submit
    jQuery('form input').bind('keydown', function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
        }
    });

    jQuery('#toolbar button').on('click', function () {
        var task = jQuery(this).attr('rel');
        var t = jQuery('#features').val();
        if (jQuery('#features').val() === '') { // no perimeter
            showBasketModalError(
                    Joomla.JText._('COM_EASYSDI_SHOP_BASKET_ERROR_PERIMETER_SELECTION_MISSING', 'You have to select an extent to go further'),
                    Joomla.JText._('COM_EASYSDI_SHOP_BASKET_ERROR_PERIMETER_TITLE', 'Perimeter missing')
                    );
            return false;
        } else if (jQuery('#surfacemin').val() !== '' && parseFloat(jQuery('#surface').val()) < parseFloat(jQuery('#surfacemin').val())) { // perimeter too small
            showBasketModalError(
                    Joomla.JText._('COM_EASYSDI_SHOP_BASKET_ERROR_TOO_SMALL', 'The order perimeter is too small, please click on the map to modify it'),
                    Joomla.JText._('COM_EASYSDI_SHOP_BASKET_ERROR_TOO_SMALL_TITLE', 'Order perimeter too small')
                    );
            return false;
        } else if (jQuery('#surfacemax').val() !== '' && parseFloat(jQuery('#surface').val()) > parseFloat(jQuery('#surfacemax').val())) { // perimeter too large
            showBasketModalError(
                    Joomla.JText._('COM_EASYSDI_SHOP_BASKET_ERROR_TOO_LARGE', 'The order perimeter is too large, please click on the map to modify it'),
                    Joomla.JText._('COM_EASYSDI_SHOP_BASKET_ERROR_TOO_LARGE_TITLE', 'Order perimeter too large')
                    );
            return false;
        } else if (!checkThirdPartyDetails()) { // thirdparty details
            showBasketModalError(
                    Joomla.JText._('COM_EASYSDI_SHOP_BASKET_ERROR_THIRDPARTY_FIELDS_MISSING', 'You have to fill third party and mandate details'),
                    Joomla.JText._('COM_EASYSDI_SHOP_BASKET_ERROR_THIRDPARTY_FIELDS_MISSING_TITLE', 'Third party details missing')
                    );
            return false;
        } else {
            var format = new OpenLayers.Format.WMC({'layerOptions': {buffer: 0}});
            var text = format.write(minimap);
            jQuery('#wmc').val(text);

            var taskArray = task.split('.');
            jQuery('input[name=action]').val(taskArray[1]);

            jQuery('input[name=task]').val('basket.save');
            jQuery('input[name=option]').val('com_easysdi_shop');

            sendBasket();
            return false;
        }
    });
}
);


