var map, perimeterLayer, selectControl, selectLayer, polygonLayer, selectControl, request, myLayer, fieldid, fieldname, loadingPerimeter, miniLayer, minimap;

//Init the recapitulation map (map without control)
function initMiniMap() {
    minimap = new OpenLayers.Map({div: 'minimap', controls: []});
    var layer = app.mapPanel.map.layers[1].clone();
    minimap.addLayer(layer);
    minimap.setBaseLayer(layer);
    minimap.zoomToExtent(app.mapPanel.map.getExtent());
    miniLayer = new OpenLayers.Layer.Vector("miniLayer");

    minimap.addLayer(miniLayer);
    miniLayer.events.register("featuresadded", miniLayer, listenerMiniFeaturesAdded);
}

//
var listenerMiniFeaturesAdded = function() {
    minimap.zoomToExtent(miniLayer.getDataExtent());
};

//Call after a feature was selected or drawn in the map
var listenerFeatureAdded = function(e) {
    miniLayer.addFeatures([e.feature.clone()]);

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
};

//Remove all geometries drawn
function clearLayersVector() {
    for (var j = 0; j < app.mapPanel.map.layers.length; j++) {
        if (app.mapPanel.map.layers[j].id.indexOf("Vector") != -1) {
            app.mapPanel.map.layers[j].removeAllFeatures();
        }
    }
    for (var j = 0; j < minimap.layers.length; j++) {
        if (minimap.layers[j].id.indexOf("Vector") != -1) {
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
    if (action == 'selection') {
        if (typeof selectControl !== 'undefined') {
            selectControl.activate();
        }
    } else if (action == 'pan') {
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
        app.mapPanel.map.indoorlevelslider.changeIndoorLevelByCode(app.mapPanel.map.indoorlevelslider, JSON.parse(jQuery('#level').val()).code);
    }
}

//
var number_format = function(number, decimals, dec_point, thousands_sep) {
    number = (number + '')
            .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
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
var priceFormatter = function(price, displayCurrency) {
    if ('undefined' === typeof displayCurrency)
        displayCurrency = true;
    var c = displayCurrency ? ' ' + currency : '';

    if (price != '-' && price != 0)
        price = number_format(
                price,
                digit_after_decimal,
                decimal_symbol,
                digit_grouping_symbol
                );

    return price + c;
};

//
var updatePricing = function(pricing) {
    if (!pricing.isActivated)
        return;

    //total amount
    jQuery('span.pricingTotalAmountTI').html(priceFormatter(pricing.cal_total_amount_ti));
    jQuery('span#pricingTotalAmountTI-container').show();

    //suppliers
    jQuery.each(pricing.suppliers, function(supplierId, supplier) {
        //products
        jQuery.each(supplier.products, function(productId, product) {
            jQuery('table[rel=' + supplierId + ']>tbody>tr[rel=' + productId + ']>td.price_column').html(priceFormatter(product.cal_total_amount_ti)).show();
        });

        //footer
        jQuery('table[rel=' + supplierId + ']>tfoot>tr>td#supplier_cal_fee_ti').html(priceFormatter(supplier.cal_fee_ti));
        jQuery('table[rel=' + supplierId + ']>tfoot>tr>td#supplier_cal_total_amount_ti').html(priceFormatter(supplier.cal_total_amount_ti));
        jQuery('table[rel=' + supplierId + ']>tfoot>tr>td#supplier_cal_total_rebate_ti').html(priceFormatter(supplier.cal_total_rebate_ti));
        jQuery('table[rel=' + supplierId + ']>tfoot').show();
    });

    //platform
    jQuery('span.pricingFeeTI').html(priceFormatter(pricing.cal_fee_ti)).show();
    jQuery('#pricingTotal-table').show();
};

//Call after user validates his extent drawing
function savePerimeter() {
    if (jQuery('#t-perimeter').val() == '')
    {
        jQuery('#perimeter-recap').empty();
    } else {
        jQuery("#progress").css('visibility', 'visible');

        var extent = {"id": jQuery('#t-perimeter').val(),
            "name": jQuery('#t-perimetern').val(),
            "surface": jQuery('#t-surface').val(),
            "allowedbuffer": jQuery('#allowedbuffer').val(),
            "buffer": jQuery('#buffer').val(),
            "level": jQuery('#t-level').val(),
            "features": JSON.parse(jQuery('#t-features').val())};

        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_easysdi_shop&task=addExtentToBasket",
            data: "item=" + JSON.stringify(extent)
        }).done(function(r) {
            if (r.MESSAGE && r.MESSAGE == 'OK') {
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

                if (r.extent.name != '') {
                    jQuery('#perimeter-recap-details-title > h4').html(r.extent.name);
                }
                else {
                    jQuery('#perimeter-recap-details-title > h4').empty();
                }
                if (r.extent.level != '') {
                    jQuery('#perimeter-recap > div:nth-child(2) > div').html(r.extent.level);
                    jQuery('#perimeter-recap').show();
                }
                else {
                    jQuery('#perimeter-recap > div:nth-child(2) > div').empty();
                }
                if (r.extent.surface != '') {
                    jQuery('#perimeter-recap > div:nth-child(1) > div').html(
                            (r.extent.surface > maxmetervalue)
                            ? (r.extent.surface / 1000000).toFixed(surfacedigit) + Joomla.JText._('COM_EASYSDI_SHOP_BASKET_KILOMETER', ' km2')
                            : parseFloat(r.extent.surface).toFixed(surfacedigit) + Joomla.JText._('COM_EASYSDI_SHOP_BASKET_METER', ' m2')
                            );
                    jQuery('#perimeter-recap').show();
                }
                else {
                    jQuery('#perimeter-recap > div:nth-child(1) > div').empty();
                }

                //pricing
                updatePricing(r.pricing);
            }

            return false;
        });
    }
}

//Reproject in EPSG:4326
function reprojectWKT(wkt) {
    var features = new OpenLayers.Format.WKT().read(wkt);
    var reprojfeatures = new Array();
    if (features instanceof Array) {
        for (var i = 0; i < features.length; i++) {
            var geometry = features[i].geometry.transform(
                    new OpenLayers.Projection("EPSG:4326"),
                    new OpenLayers.Projection(app.mapPanel.map.projection)
                    );
            var reprojfeature = new OpenLayers.Feature.Vector(geometry);
            reprojfeatures.push(reprojfeature);
        }
    }
    else {
        var geometry = features.geometry.transform(
                new OpenLayers.Projection("EPSG:4326"),
                new OpenLayers.Projection(app.mapPanel.map.projection)
                );
        var reprojfeature = new OpenLayers.Feature.Vector(geometry);
        reprojfeatures.push(reprojfeature);

    }
    var reprojwkt = new OpenLayers.Format.WKT().write(reprojfeatures);
    jQuery('#perimeter-recap-details').append("<div>" + reprojwkt + "</div>");
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

var sendBasket = function() {
    jQuery('#myModalProcess').modal('show');
    processProgress('Initialisation');
    jQuery.ajax({
        url: jQuery('#adminForm').attr('action'),
        type: 'POST',
        data: jQuery('#adminForm').serialize()
    }).done(function(data) {
        var text = Joomla.JText._('COM_EASYSDI_SHOP_BASKET_PROCESS_PROGRESSING').replace('%1', data.treated).replace('%2', data.total);
        processProgress(text, data.rate);
        setTimeout(sendProduct, 500);
    });
    return false;
};

var sendProduct = function() {
    jQuery.ajax({
        url: 'index.php?option=com_easysdi_shop&task=basket.saveProduct',
        type: 'POST',
        cache: false,
        data: formToken
    }).done(function(data) {
        if ('undefined' !== typeof data.total) {
            var text = Joomla.JText._('COM_EASYSDI_SHOP_BASKET_PROCESS_PROGRESSING').replace('%1', data.treated).replace('%2', data.total);
            processProgress(text, data.rate);

            if (data.rate < 100)
                setTimeout(sendProduct, 100);
            else
                setTimeout(closeBasket, 1000);
        }
        else {
            for (var el in data) {
                Joomla.renderMessages({el: data[el]});
                jQuery('#myModalProcess').modal('hide');
            }
        }

    });
    return false;
};

var closeBasket = function() {
    processProgress(Joomla.JText._('COM_EASYSDI_SHOP_BASKET_PROCESS_ENDING'));
    jQuery.ajax({
        url: 'index.php?option=com_easysdi_shop&task=basket.finalizeSave',
        type: 'POST',
        cache: false,
        data: formToken
    }).done(function(data) {
        document.location = data.redirect;
    });
    return false;
};

var thirdpartyInfoVisibility = function() {
    if (jQuery('select#thirdparty').val() != -1)
        jQuery('#thirdparty-info').show();
    else
        jQuery('#thirdparty-info').hide();
};

jQuery(document).on('change', 'select#thirdparty', function(e) {
    var tp = jQuery(e.target).val();

    jQuery.ajax({
        type: "POST",
        url: "index.php?option=com_easysdi_shop&task=basket.saveBasketToSession",
        data: "thirdparty=" + tp
    }).done(function(r) {
        thirdpartyInfoVisibility();
        //pricing
        updatePricing(r.pricing);
        return false;
    });
});

jQuery(document).on('click', '#btn-login', function() {
    document.location.href = 'index.php?option=com_users&view=login&return=' + btoa(document.location.href);
    return false;
});

jQuery(document).on('click', '#btn-create-account', function() {
    document.location.href = 'index.php?option=com_users&view=registration&return=' + btoa(document.location.href);
    return false;
});

jQuery(document).on('change', '#termsofuse', checkTouState);

jQuery(document).on('click', 'td.action_column>a', function() {
    removeFromBasket(jQuery(this).closest('tr').attr('rel'));
    return false;
});


jQuery(document).ready(function() {
    checkTouState();

    thirdpartyInfoVisibility();

    jQuery('#toolbar button').on('click', function() {
        var task = jQuery(this).attr('rel');
        var t = jQuery('#features').val();
        if (jQuery('#features').val() === '') {
            jQuery('#modal-error').modal('show');
        } else {
            if (jQuery('#allowedbuffer').val() == 0) {
                jQuery('#perimeter-buffer').val('');
            }

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
});


