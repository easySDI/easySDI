
Ext.namespace("EasySDI_Map");EasySDI_Map.TriggerManager=function(){};EasySDI_Map.TriggerManager.prototype.registerTrigger=function(id,trigger)
{if(this._triggers==undefined)this._triggers={};if(this._triggers[id]==undefined)this._triggers[id]=[];this._triggers[id].push(trigger);};EasySDI_Map.TriggerManager.prototype.trigger=function(id,data)
{if(this._triggers==undefined)this._triggers={};if(this._triggers[id]!==undefined)
{var triggers=this._triggers[id];Ext.each(triggers,function(value)
{value(data);});}};