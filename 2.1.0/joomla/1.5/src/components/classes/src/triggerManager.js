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

/**
* Adds trigger functionality to a class - we will implement this through Ext.mixin
*/

Ext.namespace("EasySDI_Map");

EasySDI_Map.TriggerManager = function(){};

EasySDI_Map.TriggerManager.prototype.registerTrigger = function(id, trigger)
{
  if (this._triggers == undefined) this._triggers = {};
  if (this._triggers[id] == undefined) this._triggers[id] = [];
  this._triggers[id].push(trigger);
};

/**
 * Trigger a function across decoupled classes
 */
EasySDI_Map.TriggerManager.prototype.trigger = function(id, data)
{
  if (this._triggers == undefined) this._triggers = {};
  if (this._triggers[id] !== undefined)
  {
    var triggers = this._triggers[id];
    Ext.each(triggers, function(value)
    {
      value(data);
    });
  }
};