function initDraw() {
    polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer", {
        srsName: app.mapPanel.map.projection,
        projection: app.mapPanel.map.projection,
        styleMap: customStyleMap});

    polygonLayer.events.on({
        featuresadded: onPolygonAdded,
        featuremodified: onPolygonModified
    });


    boxLayer = new OpenLayers.Layer.Vector("Rectangle Layer", {
        srsName: app.mapPanel.map.projection,
        projection: app.mapPanel.map.projection,
        styleMap: customStyleMap});
    boxLayer.events.on({
        featuresadded: onBoxAdded
    });

    drawControls = {
        polygon: new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.Polygon, {handlerOptions: {stopDown: 0, stopUp: 0}}),
        box: new OpenLayers.Control.DrawFeature(boxLayer, OpenLayers.Handler.RegularPolygon, {handlerOptions: {stopDown: 1, stopUp: 1, irregular: 1}})
    };
    for (var key in drawControls) {
        app.mapPanel.map.addControl(drawControls[key]);
    }
    app.mapPanel.map.addLayers([polygonLayer, boxLayer]);
}

function onPolygonAdded(event) {
    putFeaturesVerticesInHiddenField(event.features[0].clone());
    orderSurfaceChecking();
    selectPolygonEdit();
}

function onPolygonModified(event) {
    putFeaturesVerticesInHiddenField(event.feature.clone());
    orderSurfaceChecking();
}

function onBoxAdded(event) {
    //only for the Box, not the edit handles...
    if (event.features[0].state == 'Insert' || event.features[0].attributes['importGeom'] == "rectangle") {
        putFeaturesVerticesInHiddenField(event.features[0].clone());
        checkMinimumRectangle(event);
        orderSurfaceChecking();
        selectRectangleEdit(event.features[0]);
    }

}

function onBoxModified(event) {
    putFeaturesVerticesInHiddenField(event.feature.clone());
    orderSurfaceChecking();
}

function checkMinimumRectangle(event) {
    feature = event.features[0];
    if (feature.geometry.getArea() < mapMinSurfaceRectangle) {
        feature.geometry.resize(50, feature.geometry.getCentroid());
    }
    boxLayer.redraw();
}

function onFeaturesAdded(event) {
    putFeaturesVerticesInHiddenField(event.features[0].clone());
}

function selectPolygonEdit() {

    setFreePerimeterTool('polygon');
    disableDrawControls();
    jQuery('#btn-perimeter1b').removeClass('active');
    jQuery('#btn-perimeter1b').blur();
    var theFeature = polygonLayer.features[polygonLayer.features.length - 1];
    selectControl = new OpenLayers.Control.ModifyFeature(polygonLayer, {clickout: false, toggle: false});
    selectControl.selectFeature(theFeature);
    initSelectcontrol(selectControl);
}

function selectRectangleEdit(feature) {
    getRectangleRotation(feature);
    setFreePerimeterTool('rectangle');
    disableDrawControls();
    jQuery('#btn-perimeter1a').removeClass('active');
    jQuery('#btn-perimeter1a').blur();
    selectControl = new OpenLayers.Control.TransformFeature(boxLayer, {
        renderIntent: "transform",
        rotationHandleSymbolizer: "rotate",
        irregular: true,
        eventListeners: {
            'transformcomplete': function (o) {
                onBoxModified(o);
            }
        }
    });

    app.mapPanel.map.addControl(selectControl);
    selectControl.setFeature(feature, {rotation: getRectangleRotation(feature)});
    selectControl.activate();
    initSelectcontrol(selectControl);
}

function getRectangleRotation(feature) {
    var dx = feature.geometry.components[0].components[1].x - feature.geometry.components[0].components[0].x;
    var dy = feature.geometry.components[0].components[1].y - feature.geometry.components[0].components[0].y;
    var angle = Math.atan2(dx, dy);
    return angle * -180 / Math.PI;
}

function putFeaturesVerticesInHiddenField(feature) {
    orderSurfaceChecking();

    var geometry = feature.geometry.transform(
            new OpenLayers.Projection(app.mapPanel.map.projection),
            new OpenLayers.Projection("EPSG:4326")
            );

    var wkt = new OpenLayers.Format.WKT();
    var featureAsString = wkt.write(feature);
    jQuery('#t-features').val(featureAsString);
}

function selectPerimeter1() {
    if (freePerimeterTool == 'polygon') {
        selectPolygon();
    }
    else {
        selectRectangle();
    }
}

function reloadFeatures1() {
    var wkt = jQuery('#features').val();
    var feature = new OpenLayers.Format.WKT().read(wkt);
    if (freePerimeterTool == 'rectangle') {
        reloadRectangle(feature);
    } else {
        setFreePerimeterTool('polygon'); //if undefined tool, default is polygon
        reloadPolygon(feature);
    }
    miniLayer.addFeatures([feature.clone()]);
}

function reloadRectangle(feature) {
    var geometry = feature.geometry.transform(
            new OpenLayers.Projection("EPSG:4326"),
            new OpenLayers.Projection(app.mapPanel.map.projection)
            );
    boxLayer.events.register("featureadded", boxLayer, listenerRectangleDrawToZoom);
    boxLayer.addFeatures([feature]);
    putFeaturesVerticesInHiddenField(feature.clone());
    selectRectangleEdit(feature);
}

var listenerRectangleDrawToZoom = function (e) {
    boxLayer.events.unregister("featureadded", boxLayer, listenerRectangleDrawToZoom);
    listenerFeatureAddedToZoom(e);
};

function reloadPolygon(feature) {
    var geometry = feature.geometry.transform(
            new OpenLayers.Projection("EPSG:4326"),
            new OpenLayers.Projection(app.mapPanel.map.projection)
            );
    polygonLayer.events.register("featureadded", polygonLayer, listenerPolygonDrawToZoom);
    polygonLayer.addFeatures([feature]);
    putFeaturesVerticesInHiddenField(feature.clone());
    selectPolygonEdit();
}

var listenerPolygonDrawToZoom = function (e) {
    polygonLayer.events.unregister("featureadded", polygonLayer, listenerPolygonDrawToZoom);
    listenerFeatureAddedToZoom(e);
};

function selectPolygon() {
    resetAllSelection();
    setFreePerimeterTool('polygon');
    selectControl = new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.Polygon, {handlerOptions: {stopDown: 0, stopUp: 0}});
    jQuery('#t-features').val('');
    initSelectcontrol(selectControl);
}

function selectRectangle() {
    resetAllSelection();
    setFreePerimeterTool('rectangle');
    selectControl = new OpenLayers.Control.DrawFeature(boxLayer, OpenLayers.Handler.RegularPolygon, {handlerOptions: {stopDown: 1, stopUp: 1, irregular: 1}});
    jQuery('#t-features').val('');
    initSelectcontrol(selectControl);
}

function initSelectcontrol(selectControl) {
    app.mapPanel.map.addControl(selectControl);
    jQuery('#t-perimeter').val('1');
    jQuery('#t-perimetern').val(Joomla.JText._('FREEPERIMETER', 'Périmètre libre'));
    toggleSelectControl('selection');
}

function setFreePerimeterTool(tool) {
    freePerimeterTool = tool;
    jQuery('#t-freeperimetertool').val(tool);
}

function importPolygonFromText() {

    var text = jQuery('#basket-import-polygon-textarea').val();
    var feature = getPolygonFromText(text);
    if (feature) {
        if (feature.attributes['importGeom'] == "rectangle") {
            resetAllSelection();
            boxLayer.addFeatures([feature.clone()]);
        } else {
            resetAllSelection();
            polygonLayer.addFeatures([feature.clone()]);
        }
        app.mapPanel.map.zoomToExtent(feature.geometry.getBounds());
    }
}

function getPolygonFromText(text) {
    //clean text :
    //replace any separator by a space
    text = text.replace(/,|;|\t/g, " ");
    //replace double spaces by one
    text = text.replace(/  +/g, " ");
    //replace all by tabs
    text = text.replace(/ /g, "\t");

    //splint in points lines
    var lines = text.split("\n");

    //add lon/lat at beginning of the array
    if (lines[0].trim() != "lon\tlat") {
        lines.unshift("lon\tlat");
    }

    //add first point to the end if needed
    if (lines[1].trim() != lines[lines.length - 1].trim())
        lines.push(lines[1]);

    //2 points  + 1 header line
    if (lines.length < 3) {
        alert(Joomla.JText._("COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_ERROR_NOT_EN_POINTS"));
        return false;
    }

    //Emptyline to end string
    lines.push("");

    //group in a string
    featureText = lines.join("\n");

    //read the string and build collection
    var reader = new OpenLayers.Format.Text();
    var points = reader.read(featureText);
    var pointsGeom = [];

    if (!points) {
        alert(Joomla.JText._("COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_ERROR_LIST_FORMAT"));
        return false;
    } else if (!points.length || points.length == 0) {
        alert(Joomla.JText._("COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_ERROR_EMPTY_POINT_LIST"));
        return false;
    }

    //extract geometries from features
    for (var j = 0; j < points.length; j++) {
        if (!app.mapPanel.map.getMaxExtent().contains(points[j].geometry.x, points[j].geometry.y)) {
            alert(Joomla.JText._("COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_ERROR_OUTSIDE_MAP"));
            return false;
        }
        pointsGeom.push(points[j].geometry.clone());
    }

    var feature;
    var attribs;

    //is a polygon
    if (pointsGeom.length > 3) {
        //build polygon
        var polygon = new OpenLayers.Geometry.Polygon([new OpenLayers.Geometry.LinearRing(pointsGeom)]);
        if (!polygon) {
            alert(Joomla.JText._("COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_ERROR_UNABLE_TO_CLOSE"));
            return false;
        }
        attribs = {importGeom: "polygon"};
        feature = new OpenLayers.Feature.Vector(polygon, attribs);
    } else { // is a box (2 points)
        // reorder points, to have the rotate handle in the right place (bottom right)
        var xMin = pointsGeom[1].x < pointsGeom[0].x ? pointsGeom[1].x : pointsGeom[0].x;
        var xMax = pointsGeom[1].x < pointsGeom[0].x ? pointsGeom[0].x : pointsGeom[1].x;
        var yMin = pointsGeom[1].y < pointsGeom[0].y ? pointsGeom[1].y : pointsGeom[0].y;
        var yMax = pointsGeom[1].y < pointsGeom[0].y ? pointsGeom[0].y : pointsGeom[1].y;

        var newPoints = [
            new OpenLayers.Geometry.Point(xMax, yMin),
            new OpenLayers.Geometry.Point(xMax, yMax),
            new OpenLayers.Geometry.Point(xMin, yMax),
            new OpenLayers.Geometry.Point(xMin, yMin),
            new OpenLayers.Geometry.Point(xMax, yMin)
        ];
        var rectangle = new OpenLayers.Geometry.Polygon([new OpenLayers.Geometry.LinearRing(newPoints)]);
        if (!rectangle) {
            alert(Joomla.JText._("COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_ERROR_UNABLE_TO_CLOSE"));
            return false;
        }
        attribs = {importGeom: "rectangle"};
        feature = new OpenLayers.Feature.Vector(rectangle, attribs);
    }

    if (feature) {
        return feature;
    } else {
        alert(Joomla.JText._("COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_ERROR_UNABLE_TO_CREATE_F"));
        return false;
    }
}
