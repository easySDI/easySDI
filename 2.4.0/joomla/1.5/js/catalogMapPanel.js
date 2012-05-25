/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */

CatalogMapPanel = Ext.extend(Ext.form.Field, {	

	constructor: function( fieldsetId, width, height, numZoomLevels, isStereotype, label) {
		
		 this.addEvents({
	            'featureAdded' : true
	        });
		 
		this.numZoomLevels = numZoomLevels;
		this.width = width;
		this.height = height;
		this.panelwidth = Ext.getCmp(fieldsetId).getWidth();
		this.isStereotype = isStereotype;
		
		this.id = fieldsetId+"_BBox__1";
		this.fieldsetId = fieldsetId;
		
		Ext.getCmp(fieldsetId).add(new Ext.Panel( {
			id:fieldsetId+"_mapPanel__1",
			layout:"table",
			cls:'x-form-item',
			labelWidth: 200,
            layoutConfig:{columns:2,rows:2},
            border: false,
            width : this.panelwidth,
			height : height+30,
			items: [
			        {
			        	id:fieldsetId+"_mapLabel__1",
			        	xtype:'label',
			        	cls:'x-form-item-label',
			        	text:label,
			        	width :Ext.getCmp('metadataForm').labelWidth,
			        },
			        {
						id : this.id,
						width : width,
						height : height,
						xtype: 'panel',			
						frame: false,
						style :{
							position:'relative',			
							top:'30px',
							clear :'both'
						},
			        }
			        ]
		}))
		;	
		
		
	}, 
	
	addMap : function(){
	
		OpenLayers.ProxyHost="/proxy/?url=";

		this.map = new OpenLayers.Map(Ext.getCmp(this.id).body.id, { controls: [], numZoomLevels: this.numZoomLevels });

		if(defaultBBoxConfig!=""){
			var layerArray = defaultBBoxConfig.getLayers();//Ext.ux.util.clone(defaultBBoxConfig.layers);
			this.map.addLayers(layerArray);
		}
		else
			console.log("defaultBBoxConfig param is missing");
		
		
		this.map.addControl(new OpenLayers.Control.MousePosition());
		this.map.addControl(new OpenLayers.Control.Navigation());
		this.map.navCtrl = new OpenLayers.Control.NavigationHistory();
		this.map.panZoomCtrl = new OpenLayers.Control.PanZoom();
		this.map.addControl(new OpenLayers.Control.PanZoomBar());

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
		this.map.zoomToExtent(new OpenLayers.Bounds(defaultBBoxConfig.defaultExtent.left,defaultBBoxConfig.defaultExtent.bottom,defaultBBoxConfig.defaultExtent.right,defaultBBoxConfig.defaultExtent.top));
		this.map.maxExtent = this.map.getExtent();
		this.map.restrictedExtent = new OpenLayers.Bounds(defaultBBoxConfig.defaultExtent.left,defaultBBoxConfig.defaultExtent.bottom,defaultBBoxConfig.defaultExtent.right,defaultBBoxConfig.defaultExtent.top);
		
		var navCtrls = this.map.getControlsByClass('OpenLayers.Control.Navigation');
		for (var i = 0; i < navCtrls.length; i++) {
			navCtrls[i].disableZoomWheel();
		}
		
		if(this.isStereotype){
			//Add vector layer
			this.perimeterLayer = new OpenLayers.Layer.Vector("Perimeters");
			this.map.addLayer(this.perimeterLayer);
			
			this.perimeterLayer.events.register('featureadded',this.perimeterLayer, function(feature){
				if(this.features.length != 0)
					this.map.zoomToExtent(this.getDataExtent());
			});
			this.perimeterLayer.events.register('featureremoved',this.perimeterLayer, function(feature){
				if(this.features.length != 0)
					this.map.zoomToExtent(this.getDataExtent());
			});
			
			if(defaultBBoxConfig.freePerimeter == 1){
				this.map.drawBoxCtrl = new OpenLayers.Control.DrawFeature(this.perimeterLayer,OpenLayers.Handler.RegularPolygon, {
																												                    handlerOptions: {
																												                        sides: 4,
																												                        irregular: true
																												                    }
																												                });
				this.map.addControl(this.map.drawBoxCtrl);
				
				this.map.drawBoxCtrl.events.register ('featureadded',this, function(feature){
					if(!defaultBBoxConfig.freePerimeterSelector.isMaxCardReach()){
						feature.feature.id = '['+feature.feature.geometry.bounds.top+','+feature.feature.geometry.bounds.bottom+','+feature.feature.geometry.bounds.right+','+feature.feature.geometry.bounds.left+']';
						defaultBBoxConfig.freePerimeterSelector.addRecord(feature);
					}else{
						this.perimeterLayer.removeFeatures([feature.feature]);
					}
					defaultBBoxConfig.freePerimeterSelector.boundaryItemSelector.handleValidSelectionBoundaryCount();
				});
			}
			
			var initPerimeterList =  defaultBBoxConfig.initPerimeter;
			for(i = 0; i<initPerimeterList.length; i++){
				if(initPerimeterList[i].westbound!= 0 && initPerimeterList[i].southbound != 0 && initPerimeterList[i].eastbound !=0 && initPerimeterList[i].nortbound != 0){
					var bounds = new OpenLayers.Bounds(initPerimeterList[i].westbound,initPerimeterList[i].southbound,initPerimeterList[i].eastbound,initPerimeterList[i].northbound);
					var feature = new OpenLayers.Feature.Vector(bounds.toGeometry());
					feature.id = initPerimeterList[i].id;
					this.perimeterLayer.addFeatures(feature);
				}
			}

		}else{
			for (i= 0; i<Ext.getCmp(this.fieldsetId).items.length; i++){
				if(	Ext.getCmp(this.fieldsetId).items.items[i].id.indexOf("east")>=0||
						Ext.getCmp(this.fieldsetId).items.items[i].id.indexOf("west")>=0||
						Ext.getCmp(this.fieldsetId).items.items[i].id.indexOf("north")>=0||
						Ext.getCmp(this.fieldsetId).items.items[i].id.indexOf("south")>=0){
					Ext.getCmp(this.fieldsetId).items.items[i].addListener("change", this.updateMapExtent, this);
				
				}
				else if (Ext.getCmp(this.fieldsetId).items.items[0].id.indexOf("undaries")>=0){
					Ext.getCmp(this.fieldsetId).items.items[i].addListener("select", this.updateMapExtent, this);
				}
				else{}
			}
			this.map.events.register('moveend', this, this.updateFieldset);
		}
	}, 	
	
	addOverView: function(){
		this.map.baseLayer.maxExtent = this.map.maxExtent;
		var bounds;
		var viewSize = new OpenLayers.Size(180, 100);			
		bounds = this.map.maxExtent;

		var overviewLayer = this.map.baseLayer.clone();
		overviewLayer.map = null;
		overviewLayer.maxExtent = bounds;
		overviewLayer.minExtent = bounds;
		var wRes = bounds.getWidth() / viewSize.w;
		var hRes = bounds.getHeight() / viewSize.h;
		maxResolution = Math.max(wRes, hRes);

		ovControl = new OpenLayers.Control.OverviewMap({
			mapOptions :{
				maxExtent : bounds,
				restrictedExtent : bounds,
				maxResolution : maxResolution,
				minExtent : bounds,	
				minScale : null,
				maxScale : null,
				scales :null,					
				numZoomLevels :1,				
				center : bounds.getCenterLonLat()
				
			},
			layers : [overviewLayer],
			size : viewSize	
			
			
		});
			 
		// This forces the overview to never pan or zoom, since it
		// is always
		// suitable.
		ovControl.isSuitableOverview = function() {
			return true;
		};		
	
		this.map.addControl(ovControl);
	},

	addFreePerimeter:function(){
		this.updateManuallyTriggered = true;
		var extent = new Array();
		var extentInvalid = false ;
		
		Ext.each(Ext.getCmp(this.fieldsetId).items.items, function(item, index) {
			if(item.id.indexOf("east")>=0){
				if(item.isValid())
					extent["east"]= item.getValue();
				else
					extentInvalid = true;

			}else if(item.id.indexOf("west")>=0){
				if(item.isValid())
					extent["west"]= item.getValue();	
				else					
					extentInvalid = true;

			}else if(item.id.indexOf("south")>=0){
				if(item.isValid())
					extent["south"]= item.getValue();	
				else
					extentInvalid = true;

			}else if(item.id.indexOf("north")>=0){
				if(item.isValid())
					extent["north"]= item.getValue();	
				else
					extentInvalid = true;

			}else{}
		});
		
		if(!extentInvalid && extent["west"] && extent["south"] && extent["east"] && extent["north"]){
			var bounds = new OpenLayers.Bounds(extent["west"],extent["south"],extent["east"],extent["north"]);
			var feature = new OpenLayers.Feature.Vector(bounds.toGeometry());
			feature.id = extent["west"]+extent["south"]+extent["east"]+extent["north"];
			this.perimeterLayer.addFeatures(feature);
//			this.map.zoomToExtent(bounds);
		}else if (extentInvalid){
			this.updateManuallyTriggered = false;
		}
		
		
	},
	
	updateMapExtent: function(){
		this.updateManuallyTriggered = true;
		var extent = new Array();
		var extentInvalid = false ;

		Ext.each(Ext.getCmp(this.fieldsetId).items.items, function(item, index) {
			if(item.id.indexOf("east")>=0){
				if(item.isValid())
					extent["east"]= item.getValue();
				else
					extentInvalid = true;

			}else if(item.id.indexOf("west")>=0){
				if(item.isValid())
					extent["west"]= item.getValue();	
				else					
					extentInvalid = true;

			}else if(item.id.indexOf("south")>=0){
				if(item.isValid())
					extent["south"]= item.getValue();	
				else
					extentInvalid = true;

			}else if(item.id.indexOf("north")>=0){
				if(item.isValid())
					extent["north"]= item.getValue();	
				else
					extentInvalid = true;

			}else{}
		});

		//left, bottom, right, top 
		if(!extentInvalid){
			var currentBounds = new OpenLayers.Bounds(extent["west"],extent["south"],extent["east"],extent["north"]);
			this.map.zoomToExtent(currentBounds);
		}else{
			this.updateManuallyTriggered = false;
		}
	},

	updateCtrlBtns : function() {
		if (this.navButton.pressed) {
			this.navCtrl.activate();
			this.panZoomCtrl.activate();
		} else {
			this.navCtrl.deactivate();
			this.panZoomCtrl.deactivate();
		}
		if (this.zoomInBoxButton.pressed) {
			this.zoomInBoxCtrl.activate();
		} else {
			this.zoomInBoxCtrl.deactivate();
		}
		if (this.zoomOutBoxButton.pressed) {
			this.zoomOutBoxCtrl.activate();
		} else {
			this.zoomOutBoxCtrl.deactivate();
		}
		if(defaultBBoxConfig.freePerimeter == 1){
			if(this.drawBoxButton.pressed){
				this.drawBoxCtrl.activate();
			}else{
				this.drawBoxCtrl.deactivate();
			}
		}

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

		var itemslist = [ 
				          this.map.previousButton, 
				          this.map.nextButton, 
				          {xtype : 'tbseparator'}, 
				          this.map.zoomInBoxButton, 
				          this.map.zoomOutBoxButton,
				          this.map.navButton
				];
		
		if(defaultBBoxConfig.freePerimeter == 1){
			this.map.drawBoxButton = new Ext.Toolbar.Button( {
				iconCls : 'drawRectangleBoxBtn',
				enableToggle : true,
				toggleGroup : 'mapCtrl',
				allowDepress : false,
				handler : this.updateCtrlBtns,
				scope : this.map
			});
			
			itemslist = [ 
				          this.map.previousButton, 
				          this.map.nextButton, 
				          {xtype : 'tbseparator'}, 
				          this.map.zoomInBoxButton, 
				          this.map.zoomOutBoxButton,
				          this.map.navButton,
				          {xtype : 'tbseparator'}, 
				          this.map.drawBoxButton
				]
		}
		
		var topbar = this.height +30;
		Ext.getCmp(this.fieldsetId).add(new Ext.Toolbar( {
			id : this.fieldsetId+"_BBoxPanel__1",
			autoHeight : true,
			width : this.width,
			height : 30,				
			frame: false,
			style :{
				position:'relative',			
				left:Ext.getCmp(this.fieldsetId+"_mapLabel__1").getWidth()+'px',
				top:'-'+topbar+'px',
				clear :'both'
			},
			items : itemslist
		}));
	},
	
	updateItem: function(item, extent) {
		if(item.id.indexOf("east")>=0){
			item.setValue(extent["east"]);
		}else if(item.id.indexOf("west")>=0){
			item.setValue(extent["west"]);				
		}else if(item.id.indexOf("south")>=0){
			item.setValue(extent["south"]);				
		}else if(item.id.indexOf("north")>=0){
			item.setValue(extent["north"]);				
		}else if (item.getXType()=="combo"){
			item.setValue(); // setting the combobox to empty.
		}else{}
	},
	
	updateFieldset: function () {		
		if(!this.updateManuallyTriggered){
			var extent = new Array();
			var currentExtent = this.map.getExtent();
			extent["south"] = currentExtent.bottom ;
			extent["west"] = currentExtent.left ;
			extent["north"] = currentExtent.top ;
			extent["east"] = currentExtent.right ;
				
			for ( i =0; i< Ext.getCmp(this.fieldsetId).items.items.length ;i++  )
			this.updateItem(Ext.getCmp(this.fieldsetId).items.items[i], extent) ;
		}
		else
			this.updateManuallyTriggered = false;
	}
});
	
	
