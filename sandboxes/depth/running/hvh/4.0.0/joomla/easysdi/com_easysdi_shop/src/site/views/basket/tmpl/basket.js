var map, drawControls;

function initDraw(){
    
   
                var polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer");
                var boxLayer = new OpenLayers.Layer.Vector("Box layer");

                app.mapPanel.map.addLayers([  polygonLayer, boxLayer]);
    drawControls = {
        polygon: new OpenLayers.Control.DrawFeature(polygonLayer,
                OpenLayers.Handler.Polygon),
        box: new OpenLayers.Control.DrawFeature(boxLayer,
                OpenLayers.Handler.RegularPolygon, {
            handlerOptions: {
                sides: 3,
                irregular: true
            }
        }
        )
    };

    for (var key in drawControls) {
        app.mapPanel.map.addControl(drawControls[key]);
    }
    
    app.mapPanel.map.zoomToMaxExtent();
}
function toggleControl(element) {
    for (key in drawControls) {
        var control = drawControls[key];
        if (element.value == key && element.checked) {
            control.activate();
        } else {
            control.deactivate();
        }
    }
}

function selectRectangle() {
    for (key in drawControls) {
        var control = drawControls[key];
        if (key == 'box') {
            control.activate();
        } else {
            control.deactivate();
        }
    }
    
}
function selectPolygon() {
    for (key in drawControls) {
        var control = drawControls[key];
        if (key == 'polygon') {
            control.activate();
        } else {
            control.deactivate();
        }
    }
    
}

