var map, perimeterLayer, drawControls, selectLayer, hover, polygonLayer, boxLayer, selectControl, request;

function initDraw() {
    polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    boxLayer = new OpenLayers.Layer.Vector("Box layer", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    selectLayer = new OpenLayers.Layer.Vector("Selection", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    hover = new OpenLayers.Layer.Vector("Hover", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});

    polygonLayer.events.on({
        featuresadded: onFeaturesAdded
    });

    app.mapPanel.map.addLayers([polygonLayer, boxLayer, selectLayer, hover]);
    app.mapPanel.map.addLayers([polygonLayer, boxLayer]);

    drawControls = {
        polygon: new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.Polygon),
        box: new OpenLayers.Control.DrawFeature(boxLayer, OpenLayers.Handler.RegularPolygon)
    };

    for (var key in drawControls) {
        app.mapPanel.map.addControl(drawControls[key]);
    }
}

function toggleControl(element) {
    if (app.mapPanel.map.getLayersByName("perimeterLayer").length > 0) {
        app.mapPanel.map.removeLayer(perimeterLayer);
    }

    reinitAll();

    for (key in drawControls) {
        var control = drawControls[key];
        if (element == key) {
            control.activate();
        } else {
            control.deactivate();
        }
    }
}

function onFeaturesAdded(event) {
    var bounds = event.features[0].geometry.getBounds();
    var answer = "bottom: " + bounds.bottom + "\n";
    answer += "left: " + bounds.left + "\n";
    answer += "right: " + bounds.right + "\n";
    answer += "top: " + bounds.top + "\n";
    alert(answer);
}

function reinitAll() {

    jQuery('#t-perimeter').val(jQuery('#perimeter').val());
    jQuery('#t-perimetern').val(jQuery('#perimetern').val());
    jQuery('#t-features').val(jQuery('#features').val());
    

    for (var j = 0; j < app.mapPanel.map.layers.length; j++) {
        if (app.mapPanel.map.layers[j].__proto__.CLASS_NAME == "OpenLayers.Layer.Vector") {
            app.mapPanel.map.layers[j].removeAllFeatures();
        }
    }

    app.mapPanel.map.removeControl(selectControl);

    for (key in drawControls) {
        var control = drawControls[key];
        control.deactivate();
    }
}

function savePerimeter() {
    
    //Put in the session
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

    var perimeter = {"id": jQuery('#t-perimeter').val(), "name": jQuery('#t-perimetern').val(), "features": JSON.parse(jQuery('#t-features').val())};
    var query = "index.php?option=com_easysdi_shop&task=addPerimeterToBasket&item=" + JSON.stringify(perimeter);
    request.onreadystatechange = displayPerimeterRecap;
    request.open("GET", query, true);
    request.send(null);
}

function displayPerimeterRecap() {
    if (request.readyState === 4) {
        jQuery('#perimeter').val(jQuery('#t-perimeter').val());
        jQuery('#perimetern').val(jQuery('#t-perimetern').val());
        jQuery('#features').val(jQuery('#t-features').val());
    
        jQuery('#perimeter-recap').empty();
        jQuery('#perimeter-recap').append("<div><h3>" + jQuery('#perimetern').val() + "</h3></div>");

        var features_text = jQuery('#features').val();
        if (features_text !== '')
            var features = JSON.parse(features_text);
        else
            return;
        jQuery.each(features, function(index, value) {
            if (typeof value === "undefined")
                return true;
            jQuery('#perimeter-recap').append("<div>" + value.name + "</div>");
        });
    }
}


