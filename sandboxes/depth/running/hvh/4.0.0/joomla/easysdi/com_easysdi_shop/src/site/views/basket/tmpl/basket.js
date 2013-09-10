var map, drawControls;

function initDraw() {
    var polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer",{
           projection: app.mapPanel.map.projection}
          );
    var boxLayer = new OpenLayers.Layer.Vector("Box layer",{
           projection: app.mapPanel.map.projection});

polygonLayer.events.on({
    featuresadded: onFeaturesAdded
});

    app.mapPanel.map.addLayers([polygonLayer, boxLayer]);
    drawControls = {
        polygon: new OpenLayers.Control.DrawFeature(polygonLayer,OpenLayers.Handler.Polygon),
        box: new OpenLayers.Control.DrawFeature(boxLayer,OpenLayers.Handler.RegularPolygon)
    };

    for (var key in drawControls) {
        app.mapPanel.map.addControl(drawControls[key]);
    }
    
    

    
}
function toggleControl(element) {
    for (key in drawControls) {
        var control = drawControls[key];
        if (element == key ) {
            control.activate();
        } else {
            control.deactivate();
        }
    }
}

function onFeaturesAdded(event){
    var bounds = event.features[0].geometry.getBounds();
    var answer = "bottom: " + bounds.bottom  + "\n";
    answer += "left: " + bounds.left  + "\n";
    answer += "right: " + bounds.right  + "\n";
    answer += "top: " + bounds.top  + "\n";
    alert(answer);
}


