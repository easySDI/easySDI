GeographicExtentStereotypeMapPanel = new Ext.extend(Ext.Panel, {	

	constructor: function( fieldsetId) {
		this.id = fieldsetId+"_StereotypeMap";
		this.fieldsetId = fieldsetId;
		Ext.getCmp(fieldsetId).add( new Ext.Panel( {
			id : fieldsetId+"_StereotypeMap",
			width : 250,
			height : 250,
			xtype: 'panel',		
			style :
			{
				position:'relative',
				top:'10px',
				left:'30px'		
			}
		}));
	}, 
	
	addMap : function(){
		
		OpenLayers.ProxyHost="/proxy/?url=";
	
		this.map = new OpenLayers.Map(Ext.getCmp(this.id).body.id, { controls: [],numZoomLevels : 5 });

		if(mapConfigOption!=""){
			var layerArray = mapConfigOption.getLayers();
			this.map.addLayers(layerArray);
		}
		else
			console.log("defaultBBoxConfig param is missing");

	
		this.map.addControl(new OpenLayers.Control.PanZoomBar());		
		this.map.navCtrl = new OpenLayers.Control.NavigationHistory();
		// parent control must be added to the map
		this.map.addControl(this.map.navCtrl);
		// Zoom in
		this.map.zoomInBoxCtrl = new OpenLayers.Control.ZoomBox();
		this.map.addControl( this.map.zoomInBoxCtrl);
		// Zoom out
		this.map.zoomOutBoxCtrl = new OpenLayers.Control.ZoomBox( {
			out : true
		});
		this.map.addControl(this.map.zoomOutBoxCtrl);

		this.updateManuallyTriggered = false;
		this.map.zoomToExtent(new OpenLayers.Bounds(mapConfigOption.defaultExtent.left,mapConfigOption.defaultExtent.bottom,mapConfigOption.defaultExtent.right,mapConfigOption.defaultExtent.top));

		var navCtrls = this.map.getControlsByClass('OpenLayers.Control.Navigation');
		for (var i = 0; i < navCtrls.length; i++) {
			navCtrls[i].disableZoomWheel();
		}
		
//		for (i= 0; i<Ext.getCmp(this.fieldsetId).items.length; i++){
//			if(	Ext.getCmp(this.fieldsetId).items.items[i].id.indexOf("east")>=0||
//					Ext.getCmp(this.fieldsetId).items.items[i].id.indexOf("west")>=0||
//					Ext.getCmp(this.fieldsetId).items.items[i].id.indexOf("north")>=0||
//					Ext.getCmp(this.fieldsetId).items.items[i].id.indexOf("south")>=0){
//				Ext.getCmp(this.fieldsetId).items.items[i].addListener("change", this.updateMapExtent, this);
//			
//			}
//			else if (Ext.getCmp(this.fieldsetId).items.items[0].id.indexOf("undaries")>=0){
//				Ext.getCmp(this.fieldsetId).items.items[i].addListener("select", this.updateMapExtent, this);
//			}
//			else{}
//		}

		this.map.events.register('moveend', this, this.updateFieldset);


		
		

	},
	
	addToolbar : function(){

		// General tool bar items

		this.map.previousButton = new Ext.Toolbar.Button( {
			iconCls : 'previousBtn',

			handler : function() {
				this.map.navCtrl.previousTrigger();
			},
			scope : this
		});



		this.map.nextButton = new Ext.Toolbar.Button( {
			iconCls : 'nextBtn',

			handler : function() {
				this.map.navCtrl.nextTrigger();
			},
			scope : this
		});



		this.map.navButton = new Ext.Toolbar.Button( {
			iconCls : 'navBtn',

			enableToggle : true,
			toggleGroup : 'mapCtrl',
			allowDepress : false,
			handler : this.updateCtrlBtns,
			scope : this.map,
			pressed : true
		});



		this.map.zoomInBoxButton = new Ext.Toolbar.Button( {
			iconCls : 'zoomInBoxBtn',

			enableToggle : true,
			toggleGroup : 'mapCtrl',
			allowDepress : false,
			handler : this.updateCtrlBtns,
			scope : this.map
		});


		this.map.zoomOutBoxButton = new Ext.Toolbar.Button( {
			iconCls : 'zoomOutBoxBtn',

			enableToggle : true,
			toggleGroup : 'mapCtrl',
			allowDepress : false,
			handler : this.updateCtrlBtns,
			scope : this.map
		});



		Ext.getCmp(this.fieldsetId).add(
		new Ext.Toolbar( {
			id : this.fieldsetId+"_BBoxPanel",
			autoHeight : true,
			width : 500,
			height : 30,				
			frame: false,
			style :{position:'relative',			
				left:'0px',
				top:'-500px',
				clear :'both'
			},
			items : [ this.map.previousButton, this.map.nextButton, {
				xtype : 'tbseparator'
			}, this.map.zoomInBoxButton, this.map.zoomOutBoxButton,this.map.navButton
			]
		}));


	}
	
});