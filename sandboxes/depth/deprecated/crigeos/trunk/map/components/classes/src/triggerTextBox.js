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
 * This class is based on an autocomplete combobox, but has no drop down list off its own,
 * instead triggers the population (requery) of a store, which can be used in other
 * places. This has been constructed with the purpose of the store also driving a
 * gridpanel.
 */

EasySDI_Map.triggerTextBox = Ext.extend(Ext.form.TriggerField, {
  // private
  defaultAutoCreate : {tag: "input", type: "text", size: "24"},
  
  hideTrigger:true,

  minChars : 1,

  timedDelay: 500,

  // private
  initEvents : function(){
    EasySDI_Map.triggerTextBox.superclass.initEvents.call(this);

    this.keyNav = new Ext.KeyNav(this.el, {
            scope : this,
            doRelay : function(foo, bar, hname){
              if(hname == 'down'){
                return Ext.KeyNav.prototype.doRelay.apply(this, arguments);
              }
              return true;
            },
            forceKeyDown : true
    });
    this.dqTask = new Ext.util.DelayedTask(this.timedFunction, this);
    this.el.on("keyup", this.onKeyUp, this);
  },

  // private
  onDestroy : function(){
    if (this.dqTask){
      this.dqTask.cancel();
      this.dqTask = null;
    }
    EasySDI_Map.triggerTextBox.superclass.onDestroy.call(this);
  },

  // private
  onKeyUp : function(e){
    if(!e.isSpecialKey()){
      this.lastKey = e.getKey();
      this.dqTask.delay(this.timedDelay);
    }
  },

  // private
  timedFunction : function(){
    var q = this.getRawValue();
    if(q === undefined || q === null){
      q = '';
    }
    if(q.length >= this.minChars){
      if(this.handler){
        this.handler.call(this.handlerScope || this, this.getRawValue());
      }
    }
  },

  getRawValue : function(){
    var v = this.rendered ? this.el.getValue() : Ext.value(this.value, '');
    if(v === this.emptyText){
      v = '';
    }
    return v;
  },

  clearValue : function(){
    this.setRawValue('');
    this.applyEmptyText();
    this.value = '';
  }

});


EasySDI_Map.triggerSearchStoreBox = Ext.extend(EasySDI_Map.triggerTextBox, {
  // private
  queryParam: 'query',

  doQuery : function(q, forceAll){
    if(q === undefined || q === null){
      q = '';
    }
    if(forceAll === true || (q.length >= this.minChars)){
      this.store.baseParams[this.queryParam] = "*"+q;
      this.store.load({});
    };
  },

  // private
  timedFunction : function(){
    this.doQuery(this.getRawValue());
  },

  bindStore : function(store){
    if(store){
      this.store = Ext.StoreMgr.lookup(store);
    } else {
      this.store = null;
    }
  }

});
