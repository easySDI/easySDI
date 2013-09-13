var map, perimeterLayer, drawControls, selectLayer, hover, polygonLayer, boxLayer, selectControl;

function initDraw() {
    polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer",{srsName: app.mapPanel.map.projection,projection: app.mapPanel.map.projection});
    boxLayer = new OpenLayers.Layer.Vector("Box layer",{srsName: app.mapPanel.map.projection,projection: app.mapPanel.map.projection});
    selectLayer = new OpenLayers.Layer.Vector("Selection",{srsName: app.mapPanel.map.projection,projection: app.mapPanel.map.projection});
    hover = new OpenLayers.Layer.Vector("Hover",{srsName: app.mapPanel.map.projection,projection: app.mapPanel.map.projection});

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


