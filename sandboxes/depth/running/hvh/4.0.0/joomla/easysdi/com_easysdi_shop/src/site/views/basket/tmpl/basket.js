var map, perimeterLayer, drawControls, selectLayer, hover, polygonLayer, boxLayer, selectControl;

function initDraw() {
    polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer", {
        projection: app.mapPanel.map.projection}
    );
    boxLayer = new OpenLayers.Layer.Vector("Box layer", {
        projection: app.mapPanel.map.projection});
    
    selectLayer = new OpenLayers.Layer.Vector("Selection");
    
    hover = new OpenLayers.Layer.Vector("Hover");

    polygonLayer.events.on({
        featuresadded: onFeaturesAdded
    });

    app.mapPanel.map.addLayers([polygonLayer, boxLayer, selectLayer, hover]);
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
    
    clearAll() ;

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

function clearAll() {

//    app.mapPanel.map.getLayersByName("Polygon Layer")[0].removeAllFeatures();
//    app.mapPanel.map.getLayersByName("Polygon Layer")[0].refresh({force: true});
//    app.mapPanel.map.getLayersByName("Box layer")[0].removeAllFeatures();
//    app.mapPanel.map.getLayersByName("Box layer")[0].refresh({force: true});
//    app.mapPanel.map.getLayersByName("Selection")[0].removeAllFeatures();
//    app.mapPanel.map.getLayersByName("Selection")[0].refresh({force: true});
    
    for(var j=0; j < app.mapPanel.map.layers.length; j++){
      if(app.mapPanel.map.layers[j].__proto__.CLASS_NAME == "OpenLayers.Layer.Vector"){
          app.mapPanel.map.layers[j].removeAllFeatures();
      }
  }
    
     app.mapPanel.map.removeControl(selectControl);

    for (key in drawControls) {
        var control = drawControls[key];
        control.deactivate();
    }
}


