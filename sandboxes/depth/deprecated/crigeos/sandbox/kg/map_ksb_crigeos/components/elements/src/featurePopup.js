/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community * For more information : www.easysdi.org
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

// Localisation Prefix FP

Ext.namespace("EasySDI_Map");

EasySDI_Map.FeaturePopup = Ext.extend(Ext.Window, {

  constructor: function(config) {
    // Set the output container so the parent class can add controls
    this.outputCntr = this;
    this.featureId=config.featureId;
    this.featureType=config.featureType;
    config.width=500;
    config.height=400;
    config.autoScroll=true;
    config.modal=true;
    config.layout="form";
    config.bodyStyle="padding: 8px";
    config.labelWidth=150;
    // user must be logged in to comment, so use Close or Save + Cancel buttons where appropriate
    if (user.loggedIn && typeof SData.commentFeatureType !== "undefined") {
      config.buttons=[{
        text     : EasySDI_Map.lang.getLocal('Save_comment'),
        handler  : this.saveComment,
        scope: this
      }, {
        text     : EasySDI_Map.lang.getLocal('Cancel'),
        handler  : function(){
          this.destroy();
        },
        scope: this
      }];
    } else {
      config.buttons=[{
        text     : EasySDI_Map.lang.getLocal('CLOSE'),
        handler  : function(){
          this.destroy();
        },
        scope: this
      }];
    }
    
    this.protocol = new OpenLayers.Protocol.WFS({
	    url: componentParams.proxiedPubWfsUrl,	      
	    featureNS: componentParams.pubFeatureNS,
	    featurePrefix: componentParams.pubFeaturePrefix,	    
	    featureType: this.featureType,
	    srsName: componentParams.projection,
	    version: componentParams.pubWfsVersion,
	    propertyNames: SData.defaultAttrs[this.featureType].concat([SData.commentFeatureType.featureCommentCount]),
	    filter: new OpenLayers.Filter.Comparison({
	      type: OpenLayers.Filter.Comparison.EQUAL_TO,
	      property: componentParams.featureIdAttribute,
	      value: this.featureId
	    })
	  });
	  
	  var fields=[];
	  Ext.each(SData.attrs[this.featureType], function(attr) {
	    // Grab only the default attributes
	    if (SData.defaultAttrs[this.featureType].indexOf(attr.name)!=-1 || 
	    			attr.name==SData.commentFeatureType.featureCommentCount) {	    	
	      fields.push({name: attr.name, type: attr.type});
	    }
	  }, this);	  
	  var proxy = new GeoExt.data.ProtocolProxy({protocol: this.protocol});
	  var store = new GeoExt.data.FeatureStore({
	      fields: fields,
	      proxy: proxy,
	      srsName: componentParams.projection
	  });
	  store.on('load', this.loadStore, this);
  	store.load();
  
    EasySDI_Map.FeaturePopup.superclass.constructor.apply(this, arguments);
  },

  handleCommentPost: function(evt) {
    if (!evt.success()) {
      alert(evt.priv.responseXML.firstChild.textContent);
    } else {
      this.trigger('incCommentCount', this.featureId);
    }
    this.destroy();
  }

});

Ext.mixin(EasySDI_Map.FeaturePopup, EasySDI_Map.FeatureDetailsHelper);
Ext.mixin(EasySDI_Map.FeaturePopup, EasySDI_Map.TriggerManager);