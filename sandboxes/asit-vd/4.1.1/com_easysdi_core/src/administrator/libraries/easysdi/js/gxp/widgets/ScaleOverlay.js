Ext.namespace("gxp");

gxp.ScaleOverlay.prototype.addScaleLine = function() {
        var scaleLinePanel = new Ext.BoxComponent({
            autoEl: {
                tag: "div",
                cls: "olControlScaleLine overlay-element overlay-scaleline"
            }
        });
        this.on("afterlayout", function(){
            scaleLinePanel.getEl().dom.style.position = 'relative';
            scaleLinePanel.getEl().dom.style.display = 'inline';

            this.getEl().on("click", this.stopMouseEvents, this);
            this.getEl().on("mousedown", this.stopMouseEvents, this);
        }, this);
        scaleLinePanel.on('render', function(){
            var scaleLine = new OpenLayers.Control.ScaleLine({
                bottomInUnits :SdiScaleLineParams.bottomInUnits,
                bottomOutUnits :SdiScaleLineParams.bottomOutUnits,
                topInUnits :SdiScaleLineParams.topInUnits,
                topOutUnits :SdiScaleLineParams.topOutUnits,
                geodesic: true,
                div: scaleLinePanel.getEl().dom
            });

            this.map.addControl(scaleLine);
            scaleLine.activate();
        }, this);
        this.add(scaleLinePanel);
    };