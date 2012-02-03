
var i18n=function()
{this._getLocalisedStringFunction=function(x){return x;};};i18n.prototype.getLocal=function(key)
{return this._getLocalisedStringFunction(key);};i18n.prototype.setHandler=function(handler)
{this._getLocalisedStringFunction=handler;};