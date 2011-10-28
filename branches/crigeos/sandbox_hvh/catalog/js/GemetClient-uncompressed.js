/**
 * Name: Gemetclient
 * Purpose: Graphical ExtJS-based JavaScript client for GEMET Thesaurus
 * Author: Stepan Kafka <kafka email cz>
 * Copyright: Help Service - Remote Sensing s.r.o 2009
 * URL: http://bnhelp.cz
 * Licence: GNU/LGPL v3
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
/**
 * Name: Gemetclient
 * Purpose: Graphical ExtJS-based JavaScript client for GEMET Thesaurus
 * Author: Stepan Kafka <kafka email cz>
 * Copyright: Help Service - Remote Sensing s.r.o 2009
 * URL: http://bnhelp.cz
 * Licence: GNU/LGPL v3
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Class: ThesaurusReader 
 * Class to provide access to Gemet thesaurus with JSON interface and shows terms
 * date: 2008-08-15
 * version: 1.0 
 * extends: Ext.Panel
 * 
 * Parameters: 
 * config {object}
 *	possible values (key / value pair):
 *		handler - {String} - handler function (mandatory)
 *		appPath - {String} - application path (important for adressing app images etc.. If ommited, current directory is used) 
 *		url - {String} - address of gemet service - (optional, if ommited http://www.eionet.europa.eu/gemet/ is used)
 *		lang - {String} - interface/query language (default cze)
 *		outputLangs - {Array} - array of languages for output (default ['cze', 'eng'])
 *		separator - {String} -  to separate terms when whole path is returned (default ' > ')
 *		returnPath - {Boolean} - if full path is returned (default true)
 		proxy - {String} - proxy path (default: appPath+proxy.php?url=) 
 *      transl - {function} - function to provide i18n support. (default HS.i18n)
 *
 *  Example:
 *  (code)
 *  Ext.onReady(function(){
 *      ...
 *
 *      var thes = new ThesaurusReader({
 *          lang: HS.getLang(2),
 *          outputLangs: ['en','cs', 'fr', 'de'], 
 *          title: 'GEMET Thesaurus',
 *          separator: ' > ',
 *          returnPath: true,
 *          returnInspire: false,
 *          width: 300, height:400,
 *          layout: 'fit',
 *          handler: writeTerms
 *      });
 *      
 *      thes.render('thesDiv');
 *
 *      ...
 *  };
 *
 *  (end)
 */ 

var ThesaurusReader = function(config){
	
	/*this.INSPIRE 	= "http://inspire.jrc.it/theme/";
	
	this.CONCEPT 	= "http://www.eionet.europa.eu/gemet/concept/";
	this.GROUP   	= "http://www.eionet.europa.eu/gemet/group/";
	this.SUPERGROUP = "http://www.eionet.europa.eu/gemet/supergroup/";
	this.THEME   	= "http://www.eionet.europa.eu/gemet/theme/";*/
	
	this.narrower = "http://www.w3.org/2004/02/skos/core%23narrower";
	this.broader  = "http://www.w3.org/2004/02/skos/core%23broader";
	this.related  = "http://www.w3.org/2004/02/skos/core%23related";

  this.thesauri = {
    'GEMET': {
    	concept:    "http://www.eionet.europa.eu/gemet/concept/",
    	theme:      "http://www.eionet.europa.eu/gemet/theme/",        
    	group:   	"http://www.eionet.europa.eu/gemet/group/",
    	supergroup: "http://www.eionet.europa.eu/gemet/supergroup/"
    },
    'INSPIRE': {
      concept:     	thesaurusConfig,
    	theme:      thesaurusConfig,        
    	group:   	null,
    	supergroup: null,
    	firstClick: true
      },
    '1GE': {
    	url:        "http://sensors.lesprojekt.cz:9673/gemet/",
        concept:    "http://www.onegeology-europe.eu/",
    	theme:      "http://www.onegeology-europe.eu/theme",        
    	group:   	null,
    	supergroup: null    
    }
  }

	this.appPath	= "";
  if(config.appPath) this.appPath = config.appPath;
	this.url   		= "http://www.eionet.europa.eu/gemet/";
	//console.log(this.appPath);
	this.proxy 		= this.appPath+"proxy.php?url=";
	//console.log(this.proxy);
	this.lang 		= 'en';
	this.outputLangs = ['cs', 'en'];
	this.separator 	= " > ";
	this.returnPath = true;
	
  if(config.url) this.url = config.url; 
  //console.log(config.proxy);
  if(config.proxy) this.proxy = config.proxy;
  if(config.lang) this.lang = config.lang;
  if(config.outputLangs) this.outputLangs = config.outputLangs;
  if(config.separator) this.separator = config.separator;
	if(config.returnPath!='undefined') this.returnPath = config.returnPath;
    this.handler = config.handler;
	    
    this.data=null;
    this.theMask = null; 
    this.status = 0;
    
    var termsStore = new Ext.data.JsonStore({
        url: 'proxy.php',
        baseParams: {url: ''},
        language: this.lang,
        
            	root: 'results',
            	idProperty: 'term',
        		fields: [
        			{name: 'id', mapping: 'uri'}, 
        			{name: 'term', mapping: 'preferredLabel.string'},
        			{name: 'definition', mapping: 'definition'}
        		]
 
    });
    
    this.whisperCfg = function(obj, o){
      var conceptURI = this.thesauri[this.selectThes.value].concept;
      var addr = this.thesauri[this.selectThes.value].url;
      if(!addr) addr = this.url;
      obj.baseParams.url = addr+"getConceptsMatchingRegexByThesaurus?thesaurus_uri="+conceptURI+"&language="+this.lang+"&regex="+escape(obj.baseParams.query);    
    }
    
    termsStore.on('beforeload', this.whisperCfg, this, {});
    
    /*var searchField = new Ext.form.TriggerField({
      width: 150,
      minLength: 3,
      //minLengtText: 'At least 3 characters...',
      //msgTarget: 'under',
      triggerClass:'x-form-search-trigger',
      obj: this // zpetny odkaz na objekt
    });*/

    this.whisperSelect = function(cbox, record){
    		var node = {text: record.data.term, attributes:{termId:record.data.id, data:{definition: record.data.definition}}};
    		this.getById(node);
    }

   this.searchField = new Ext.form.ComboBox({
      store: termsStore,
      width: 150,
      minLength: 3,
      displayField: 'term',
      listeners: {'select': this.whisperSelect, scope: this},
      minLengtText: 'At least 3 characters...',
      msgTarget: 'under',
      triggerClass:'x-form-search-trigger',
      xtype:'combo',
      obj: this // zpetny odkaz na objekt
    });
    
   
    this.showError = function(){
      Ext.Msg.alert('Error', 'Source not found at:' + this.url);
      this.theMask.hide();      
    }
   
    this.drawTerms = function(r,o){
      this.theMask.hide();
      var root = o.options.node;
      root.getUI().getIconEl().src = Ext.BLANK_IMAGE_URL;
      root.getUI().getIconEl().className = "x-tree-node-icon";
      if(r.responseText){
        try{
        
          var data = Ext.util.JSON.decode(r.responseText);
	       
          if(data)  this.drawBranch(root, data);
          root.expand();
        }catch(e){alert('Data error!');}
      }     
    }

    this.drawBranch = function(root, data){
    	var uri = this.thesauri[this.selectThes.value];
      for(var i=0;i<data.length;i++){
        if(data[i].uri.indexOf(uri.theme)>-1) var icon=this.appPath+'img/theme.gif';
        else if(data[i].uri.indexOf(uri.group)>-1) var icon=this.appPath+'img/group.gif';
        else var icon=this.appPath+'img/term.gif';
        var node = new Ext.tree.TreeNode({
          text: data[i].preferredLabel.string,
          termId: data[i].uri,
          data: data[i],
          icon: icon,
          cls: 'thes-link'
        });
        if(uri.firstClick){ 
            node.on('click', this.returnTerm, this, data[i].termId);
        }  
        else node.on('click', this.getById, this, data[i].termId);
        root.appendChild(node);
      }        
    }

    /* empties tree structure */
    this.emptyTree = function(){
      var root = this.thesRoot;
      while(root.item(0)) root.removeChild(root.item(0));    
    }
    
    /* empties thesaurus panel */
    this.emptyAll = function(){
      var root = this.thesRoot;
      while(root.item(0)) root.removeChild(root.item(0));
      root.remove(true);
      
      this.searchField.clearValue();
      this.treePanel.topToolbar.hide();
      
      this.getTopConcepts();
    }
    

  /**
   * Runs thesaurus query by (sub)string. Ajax returns to drawTerms
   */     
    this.getByTerm = function(){
	    this.emptyTree();
      this.detailPanel.collapse();
      this.treePanel.topToolbar.hide();
      if(this.getValue().length < this.minLength) {
        Ext.Msg.alert(HS.i18n('Warning'), '&gt;= ' +this.minLength+' '+HS.i18n('characters required'));
        return false;
      }
      if(!this.theMask) this.theMask = new Ext.LoadMask(this.body);
      this.theMask.show();
      this.thesRoot.setText(HS.i18n('Found'));  
	    var conceptURI = this.thesauri[this.selectThes.value].concept; 
      Ext.Ajax.request({
        url: this.prepareRequest("getConceptsMatchingRegexByThesaurus?thesaurus_uri="+conceptURI+"&language="+this.lang+"&regex="+this.getValue()),
        //url: this.prepareRequest("getConceptsMatchingKeyword?language="+this.lang+"&search_mode=0&keyword="+this.getValue()),
        scope: this.obj,
        options: {node: this.thesRoot},
        success: this.drawTerms,
        failure: this.showError
      })
    }

	/* NEW - Returnes top concepts for thesaurus */
	this.getTopConcepts = function(){
	  var conceptURI = this.thesauri[this.selectThes.value].concept;
	  this.emptyTree();
    this.treePanel.topToolbar.hide();
    this.detailPanel.body.update('');
    this.detailPanel.collapse();
    if(!this.theMask) this.theMask = new Ext.LoadMask(this.body);
    this.theMask.show();
    this.thesRoot.setText(HS.i18n('Top concepts'));
    Ext.Ajax.request({
      url: this.prepareRequest("getTopmostConcepts?thesaurus_uri="+conceptURI+"&language="+this.lang),
      scope: this,
      options: {node: this.thesRoot},
      success: this.drawTerms,
      failure: this.showError
    })		
	}

  /**
   * getById
   * Runs thesaurus getRelatedConcepts by id. Ajax returns to drawTermsId
   */     
    this.getById = function(theNode){
    	
      if(!this.theMask) this.theMask = new Ext.LoadMask(this.body);
      this.data=theNode.attributes.termId;
  	  this.emptyTree();
  	  this.treePanel.topToolbar.show();
  	  this.thesRoot.setText(theNode.text);
      var theTitle = this.treePanel.topToolbar.items.item(2);
  	  Ext.fly(theTitle.id).update("<span class='thes-term'><b>"+theNode.text+"</b></span>", false);
      this.treePanel.doLayout();
      if(theNode.attributes.data.definition){
        this.detailPanel.body.update(theNode.attributes.data.definition.string);  
        this.detailPanel.expand();
      }
      else{
        this.detailPanel.body.update('');
        this.detailPanel.collapse();
        
      }
      var nt = new Ext.tree.TreeNode({text: HS.i18n("NT"), termId: 'nt', icon: this.appPath+'img/indicator.gif'});
      this.thesRoot.appendChild(nt);      
      Ext.Ajax.request({
        url: this.prepareRequest("getRelatedConcepts?concept_uri="+theNode.attributes.termId+"&relation_uri="+this.narrower+"&language="+this.lang),
        scope: this,
        options: {node: nt},
        success: this.drawTerms,
        failure: this.showError
      });
      var bt = new Ext.tree.TreeNode({text: HS.i18n("BT"), termId: 'bt', icon:this.appPath+'img/indicator.gif'});
      this.thesRoot.appendChild(bt);      
      Ext.Ajax.request({
        url: this.prepareRequest("getRelatedConcepts?concept_uri="+theNode.attributes.termId+"&relation_uri="+this.broader+"&language="+this.lang),
        scope: this,
        options: {node: bt},
        success: this.drawTerms,
        failure: this.showError
      });
      var rt = new Ext.tree.TreeNode({text: HS.i18n("RT"), termId: 'rt', icon:this.appPath+'img/indicator.gif'});
      this.thesRoot.appendChild(rt);      
      Ext.Ajax.request({
        url: this.prepareRequest("getRelatedConcepts?concept_uri="+theNode.attributes.termId+"&relation_uri="+this.related+"&language="+this.lang),
        scope: this,
        options: {node: rt},
        success: this.drawTerms,
        failure: this.showError
      });
      var th = new Ext.tree.TreeNode({text: HS.i18n("TH"), termId: 'th', icon:this.appPath+'img/indicator.gif'});
      this.thesRoot.appendChild(th);      
      Ext.Ajax.request({
        url: this.prepareRequest("getRelatedConcepts?concept_uri="+theNode.attributes.termId+"&relation_uri=http://www.eionet.europa.eu/gemet/2004/06/gemet-schema.rdf%23theme&language="+this.lang),
        scope: this,
        options: {node: th},
        success: this.drawTerms,
        failure: this.showError
      });
      this.thesRoot.expand();
    }
	
	/* adds proxy to URL */
    this.prepareRequest = function(arg){
      var url = this.thesauri[this.selectThes.value].url;
      if(!url) url = this.url; 
      url += arg;
      if(this.proxy) return this.proxy+escape(url);
      else return url;
    } 
    
	/* returns selected term (all languages, with paths) */
	this.returnTerm = function(obj){

	if(obj.xtype != 'button') this.data=obj.attributes.termId;
    this.theMask.show();
    this.output = {terms:{}, uri:'', version:''};
	  this.status=0;
	  for(var i=0;i<this.outputLangs.length;i++){
		Ext.Ajax.request({
          url: this.prepareRequest("getConcept?concept_uri="+this.data+"&language="+this.outputLangs[i]),
          scope: this,
          success: this.getConceptBack,
          failure: this.showError
      	});  
	  }
	} 
	
	/* getConcept */
    this.getBroaderConcept = function(uri, lang){
      Ext.Ajax.request({
        url: this.prepareRequest("getRelatedConcepts?concept_uri="+uri+"&relation_uri="+this.broader+"&language="+lang),
        scope: this,
        success: this.getConceptBack,
        failure: this.showError
      });
    }
    
	/* getConcept */
    this.getConceptBack = function(r,o){
    
    	if(r.responseText){
        try{
        
          var data = Ext.util.JSON.decode(r.responseText);
          if(!data.preferredLabel) {
          	for(var i=0;i<data.length;i++){
          	  if(data[i].uri.indexOf(this.thesauri[this.selectThes.value].concept)>-1){
          	    data = data[i];
          	    break;
          	  }  
          	}
          	if(!data.preferredLabel){
          	  this.finishTerm();
          	  return;
          	}
          }
          if(!this.output.terms[data.preferredLabel.language]){ 
          	this.output.terms[data.preferredLabel.language] = data.preferredLabel.string;
          	this.output.uri = data.uri;
          }	
          else this.output.terms[data.preferredLabel.language] = data.preferredLabel.string + this.separator + this.output.terms[data.preferredLabel.language];
          if(this.returnPath) this.getBroaderConcept(data.uri, data.preferredLabel.language);
          else this.finishTerm();
        }
        catch(e){alert('Data error!');}
      }
      else{
        this.finishTerm();
      }   
    }
    
    /* When term is returned */ 
    this.finishTerm = function(){
      this.status++;
      if(this.status==this.outputLangs.length){
        Ext.Ajax.request({
	        url: this.prepareRequest("getAvailableThesauri"),
	        scope: this,
	        success: this.returnTerms,
	        failure: this.showError
      	});      
      }  
    }
    
    this.returnTerms = function(r,o){
    
    	var data = Ext.util.JSON.decode(r.responseText);
    	for(var i=0;i<data.length;i++){
    	  if(this.output.uri.indexOf(data[i].uri)>-1){
    	    this.output.version = data[i].version;
    	    break;
    	  }
    	}
    	this.theMask.hide();
        this.handler(this.output);   	
    }
    
    /******************** user interface definition *********************/
    this.detailPanel = new Ext.Panel({
      height: 100,
      region: 'south', 
      collapsed: true, 
      collapseMode: 'mini',
      autoScroll: true, 
      cls: 'thes-description', 
      split:true
    });
          
    var tb = new Ext.Toolbar(
    	[{xtype: 'button', 
    	  text:HS.i18n("Use"), 
    	  icon:this.appPath+'img/drop-yes.gif', 
    	  cls:'x-btn-text-icon', 
    	  handler: this.returnTerm, scope:this
    	 }
    	, '-'
    	, {xtype: 'label',
    		id: 'maToolbar',
    		text: 'xxx'}]
    );
        
    this.treePanel = new Ext.tree.TreePanel({
      layout: 'fit',
      useArrows: true,
      autoScroll: true,
      region: 'center',  
      tbar: tb, 
      rootVisible: true    
    }); 

    var thesauri = new Array();
    for(th in this.thesauri) thesauri.push(th);  
    
    this.selectThes = new Ext.form.ComboBox({
      store: thesauri,
      //editable: false,
      width: 70,
      stateful: true,
      stateId: "thesaurus-selected",
      typeAhead: true,
      selectOnFocus:true,
      triggerAction: 'all',
      cls: 'thes-select',
      mode:'local', 
      value: thesauri[0]
    });

    this.thesRoot = new Ext.tree.TreeNode({
        draggable: true,
        allowChildren: true,
        leaf : false,
        singleClickExpand : true,
        text : '',
        cls: 'thes-root',
        expanded: true
      });

    tb.hide();
    
    this.treePanel.setRootNode(this.thesRoot);
        
    /*searchField.onTriggerClick = this.getByTerm;
    searchField.on('specialkey', function(f, e){
      if(e.getKey() == e.ENTER)  searchField.onTriggerClick();
    }, searchField);*/

    config.layout = 'border';
    config.tbar = [
    	this.selectThes, 
    	{handler: function(){this.getTopConcepts();}, 
    	 icon:this.appPath+'img/top.gif', cls:'x-btn-icon', tooltip: HS.i18n('Top concepts'),
    	 scope:this}, 
    	"-", 
    	HS.i18n("Search")+': ', 
    	this.searchField];
    config.items = [this.detailPanel, this.treePanel];

    ThesaurusReader.superclass.constructor.call(this,config);   

  }  

  
  Ext.extend(ThesaurusReader, Ext.Panel, {});/**
 * WARNING EassySDI has hacked this file for implementation constraints
 * 
 * Purpose: Set language parameters 
 * Author: Jachym Cepicky <jachym bnhelp cz>
 * URL: http://bnhelp.cz
 * Licence: GNU/LGPL v3
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */


/**
 * Class: HS
 * Base Help Service class with global methods and variables, common for
 * all applications
 * 
 * (copy of hslayers base class version 1.0.2)
 *
 */
HS = {};

/**
 * Property: lang
 * Current language code in ISO (3 characters) format. Default null.
 */
HS.lang = null;

/**
 * Property: defaultLang
 * Default language code: "eng"
 */
HS.defaultLang = "eng";

/**
 * Property: allLangsSet
 * Do all possible libraries (openlayers, custom, ...) set their languages
 * settings?
 */
HS.allLangsSet = false;

/**
 * Property: Lang
 * Dictionary with various languages. e.g. HS.Lang["cze"], HS.Lang["eng"]
 * ...
 */
HS.Lang = {};

/**
 * Function: i18n
 * Translation function
 *
 * Parameters:
 * code - {String} language code, in which you want the translation. If
 * ommited (the usual case), current language is taken.
 * key - {String} key for string, you want to get translation for.
 *
 * Example setting the application language:
 * (code)
 * if (!HS.lang) {
 *     var lastLang = HS.getLastLangCode();
 *     if (!lastLang) {
 *         lastLang = "czech";
 *     }
 *     HS.setLang(lastLang);
 * }
 * (end)
 */
HS.i18n = function() {

    if (!HS.getLang()) {
        HS.setLang(HS.defaultLang);
    }

    var trans = null;
    var KEY = null;
    if (typeof(arguments[0]) == typeof({})) {
        trans = arguments[0];
        KEY = arguments[1];
    }
    else {
        trans = HS.Lang;
        KEY = arguments[0];
    }

    // search the translation 
    var retString = "";
    for (var lang in trans) {
        if (lang == this.lang) {
            retString =  trans[this.lang][KEY];
        }
    }

    return (retString ? retString : KEY);
};

/**
 * Function: setLang
 * Set current language according to given code. If not available, "eng"
 * will be used. <setCookie> will  be called as well.
 *
 * Parameters:
 * code - {String} language code. Can be *any* form (e.g.
 *   "?esky","cs","cze", ...). 
 * saveToCookie - {Boolean} , default: false
 *
 * Return:
 * {Boolean} it worked, or not
 */
HS.setLang = function(code, saveToCookie) {

    if (HS.allLangsSet == false) {
        
        var hsset = false;
        var olset = false;

        var hscode = null;
        var olcode = null;
        // get HS languega code
        for (var l in HS.langs) {
            breakthis = false;
            var keys = HS.langs[l];
            for (var i = 0; i < keys.length; i++) {
                if (keys[i] == code) {
                    hscode = l.split(";")[0];
                    olcode = this.getOLCode(hscode);
                    breakthis = true;
                    break;
                }
            }

            if (breakthis) { //found
                break;
            }
        }
        
        if (hscode == null) {
            hscode = "eng";
            olcode = "en";
        }

        // set lang
        this.lang = hscode;
        
        if (saveToCookie == true) {
            this.setCookie("lang",hscode);
        }

        hsset = true;

        try {
            OpenLayers.Lang.setCode(olcode);
            olset = true;
        } catch(e) {}

        if (olset && hsset) {
        }
    }
        
    return true;
};


/**
 * Function: getLang
 * Get current language code.
 *
 * Parameters:
 * type - {String} *2* *3* *"ol"*
 * 
 * Return:
 * code - {String} in 3 characters format
 */
HS.getLang = function(type) {

    if (!type) {
        type = 3;
    }

    if (!HS.lang) {
        return null;
    }
    else {
        return HS.getCodeFromLanguage(HS.lang,type);
    }
};

/**
 * Function: getLastLangCode
 * Get language code from URL or Cookie. URL first, if not found, cookie
 * last. If not found, null returned
 *
 * Returns:
 * null or {String} 3 characters code
 */
HS.getLastLangCode = function() {

    var code = null;
    /* parse link location */
    if (window.location.search.length > 0) {
        var search = window.location.search;
        var params = search.substr(1,search.length); /* without "?" */
        params = params.split("&"); 
        for (var i = 0; i < params.length; i++) {
            var param = params[i].split("=");

            /* language */
            if (param[0] == "lang") {
                code = HS.getCodeFromLanguage(param[1],3); 
            }
        }
    }

    if (!code) {
        try {
                code = HS.getCookie("lang");
                code = HS.getCodeFromLanguage(code);
        }
        catch(e) {}
    }
    
    return code;
};

/**
 * Function: getCodeFromLanguage
 * Get desired code 
 * Params:
 * code {String} anything, that identifies the language, from "eng" to * "cesky"
 * type {String} currenly: 2: en, 3: eng, "ol":  en
 */
HS.getCodeFromLanguage = function(code,type) {
    
    if (!type) {
        type = 3;
    }

    for (var l in HS.langs) {
        breakthis = false;
        var keys = HS.langs[l];
        for (var i = 0; i < keys.length; i++) {
            if (keys[i] == code) {
                codes = l.split(";");


                switch(type) {
                    case 2: return codes[2]; break;
                    case 3: return codes[0]; break;
                    case "ol": return codes[0]; break;
                    default: return "eng"; break;
                }
            }
        }
    }
    return null;
};

/**
 * Function: setCookie
 * Set the cookie of given name with given value.
 * Called from <setLang>
 *
 * Parameters:
 * c_name - {String}  name of the cookie
 * value - {String} value of the cookie
 * expiredays - {Integer} when it expires, default is never
 */
HS.setCookie = function(c_name,value,expiredays) {
    var exdate=new Date();
    exdate.setDate(exdate.getDate()+expiredays);
    document.cookie=c_name+ "=" +escape(value)+
    ((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
};

/**
 * Function: getCookie
 * Get value of the cookie with given name. Used in <getLang>
 *
 * Parameters:
 * c_name - {String} name of the cookie
 * 
 * Returns:
 * {String} or ""
 */
HS.getCookie = function(c_name) {
    if (document.cookie.length>0) {
        c_start=document.cookie.indexOf(c_name + "=");
        if (c_start!=-1) { 
            c_start=c_start + c_name.length+1; 
            c_end=document.cookie.indexOf(";",c_start);
            if (c_end==-1) c_end=document.cookie.length;
            return unescape(document.cookie.substring(c_start,c_end));
        } 
    }
    return "";
};

/**
 * WARNING EassySDI has hacked this definition for implementation constraints
 * 
 * Property: langs
 * List of langue codes in various formats.
 */
    HS.langs = {
                "en;en;en":    ["en","eng","english"],
                "de;de;de":    ["de","ger","deutsch"],
                "fr;fr;fr":    ["fr","fre","france"],
                "pl;pl;pl":    ["pl","pol","polska"],
                "it:it;it":    ["it","ita","italiano","italien"],
                "ru:ru;ru":    ["rus","ru","russe"],
                "es:es:spa":   ["es","spa","espagnol","castillan"],
                "sk:sk:sk":    ["slk","slo","sk","slovensky","slovak"],
                "cs;cs-CZ;cs": ["cz","cze","cs-CZ","czech","cesky","cs"],
                "lv;lv-LV;lv": ["lv","lav","lv-LV","latvian","latv"]
    },

/**
 * Method: initLangs
 * Initialize <HS.Lang> property for all available languages
 */
HS.initLangs = function() {
    for (var lang in this.langs) {
        HS.Lang[lang.split(";")[0]] = {};
    }
};

HS.getOLCode = function(code) {
    for (var c in this.langs) {
        var codes = c.split(";");
        if (code == codes[0]){
            return (codes.length > 1 ? codes[1] : codes[0]);
        }
    }
    return "en";
};

/**
 * Function: setDefaultLanguage
 * Set derault value for this application and init langs. Use this method
 * for initial lanuage settings.
 */
HS.setDefaultLanguage = function() {
    var lastLang = HS.getLastLangCode();
    if (!lastLang) {
        lastLang = HS.defaultLang;
    }
    HS.setLang(lastLang);
};
HS.initLangs();
/**
 * Purpose: Translation file for Getmetclient
 * Author: Stepan Kafka <kafka email cz>
 * Copyright: Help Service - Remote Sensing s.r.o 2009
 * URL: http://bnhelp.cz
 * Licence: GNU/LGPL v3
 *
 *  This file is part of Gemetclient
 *
 *  Gemetclient is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Gemetclient is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HS.langs = {
                "eng;en;en":    ["en","eng","english"],
                "ger;de;de":    ["de","ger","deutsch"],
                "fre;fr;fr":    ["fr","fre","france"],
                "pol;pl;pl":    ["pl","pol","polska"],
                "ita:it;it":    ["it","ita","italiano","italien"],
                "rus:ru;ru":    ["rus","ru","russe"],
                "spa:es:spa":   ["es","spa","espagnol","castillan"],
                "slk:sk:sk":    ["slk","slo","sk","slovensky","slovak"],
                "cze;cs-CZ;cs": ["cz","cze","cs-CZ","czech","cesky","cs"],
                "lav;lv-LV;lv": ["lv","lav","lv-LV","latvian","latv"]
    }
 */

/* HSLayers language definition */
// Czech
HS.Lang["cze"]["Home"]="Výchozí";
HS.Lang["cze"]["Search"]="Hledat";
HS.Lang["cze"]["BT"]="Širší termíny";
HS.Lang["cze"]["NT"]="Užší termíny";
HS.Lang["cze"]["RT"]="Příbuzné termíny";
HS.Lang["cze"]["Use"]="Použít";
HS.Lang["cze"]["Themes"]="Témata";
HS.Lang["cze"]["Groups"]="Skupiny";
HS.Lang["cze"]["Warning"]="Výstraha";
HS.Lang["cze"]["characters required"]="znaků vyžadováno";
HS.Lang["cze"]["Top concepts"]="Výchozí témata";
HS.Lang["cze"]["Found"]="Nalezeno";
HS.Lang["cze"]["INSPIRE themes"]="INSPIRE - témata";
HS.Lang["cze"]['GEMET top concepts']="GEMET - základní koncepty";
// English
HS.Lang["eng"]["Home"]="Home";
HS.Lang["eng"]["Search"]="Search";
HS.Lang["eng"]["Use"]="Use";
HS.Lang["eng"]["Themes"]="Themes";
HS.Lang["eng"]["Groups"]="Groups";
HS.Lang["eng"]["Warning"]="Warning";
HS.Lang["eng"]["characters required"]="characters required";
HS.Lang["eng"]["Top concepts"]="Top concepts";
HS.Lang["eng"]["Found"]="Found";
HS.Lang["eng"]["INSPIRE themes"]="INSPIRE themes";
HS.Lang["eng"]['GEMET top concepts']="GEMET top concepts";
HS.Lang["eng"]["BT"]="Broader terms";
HS.Lang["eng"]["NT"]="Narrower terms";
HS.Lang["eng"]["RT"]="Related terms";
// French
HS.Lang["fra"]["Home"]="Accueil";
HS.Lang["fra"]["Search"]="Rechercher";
HS.Lang["fra"]["Use"]="Utiliser";
HS.Lang["fra"]["Themes"]="Thèmes";
HS.Lang["fra"]["Groups"]="Groupes";
HS.Lang["fra"]["Warning"]="Avertissement";
HS.Lang["fra"]["characters required"]="caractères requis";
HS.Lang["fra"]["Top concepts"]="Concepts";
HS.Lang["fra"]["Found"]="Trouvé";
HS.Lang["fra"]["INSPIRE themes"]="Thèmes INSPIRE";
HS.Lang["fra"]['GEMET top concepts']="Concepts GEMET";
HS.Lang["fra"]["BT"]="Termes plus généraux";
HS.Lang["fra"]["NT"]="Termes plus précis";
HS.Lang["fra"]["RT"]="Termes relatifs";
// German
HS.Lang["ger"]["Home"]="Accueil";
HS.Lang["ger"]["Search"]="Rechercher";
HS.Lang["ger"]["Use"]="Utiliser";
HS.Lang["ger"]["Themes"]="Thèmes";
HS.Lang["ger"]["Groups"]="Groupes";
HS.Lang["ger"]["Warning"]="Avertissement";
HS.Lang["ger"]["characters required"]="caractères requis";
HS.Lang["ger"]["Top concepts"]="Concepts";
HS.Lang["ger"]["Found"]="Trouvé";
HS.Lang["ger"]["INSPIRE themes"]="Thèmes INSPIRE";
HS.Lang["ger"]['GEMET top concepts']="Concepts GEMET";
HS.Lang["ger"]["BT"]="Termes plus généraux";
HS.Lang["ger"]["NT"]="Termes plus précis";
HS.Lang["ger"]["RT"]="Termes relatifs";
