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
 * version: 1.0 -- modified for EasySDI --
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

    /* Initial config values */
    this.INSPIRE 	= thesaurusConfig;
    this.CONCEPT 	= "http://www.eionet.europa.eu/gemet/concept/";
    this.GROUP   	= "http://www.eionet.europa.eu/gemet/group/";
    this.SUPERGROUP = "http://www.eionet.europa.eu/gemet/supergroup/";
    this.THEME   	= "http://www.eionet.europa.eu/gemet/theme/";
    this.appPath	= "";

    if(config.appPath) {
        this.appPath = config.appPath;
    }

    this.url   		= "http://www.eionet.europa.eu/gemet/";
    this.proxy 		= this.appPath+"proxy.php?url=";
    this.lang 		= 'en';
    this.outputLangs = ['cs', 'en'];
    this.separator 	= " > ";
    this.returnPath = true;
    this.returnInspire = true;
	
    if(config.url) this.url = config.url; 
    if(config.proxy) this.proxy = config.proxy;
    if(config.lang) this.lang = config.lang;
    if(config.outputLangs) this.outputLangs = config.outputLangs;
    if(config.separator) this.separator = config.separator;
    if(config.returnPath!='undefined') this.returnPath = config.returnPath;
   /* if(config.returnInspire) this.returnInspire = config.returnInspire;
    if(config.transl) this.transl = config.transl else this.transl = HS.i18n;*/
    this.handler = config.handler;
	    
    this.data=null;
    this.theMask = null; 
    this.status = 0;
    
    /* Ext elements */
    var searchField = new Ext.form.TriggerField({
      width: 150,
      minLength: 3,
      //minLengtText: 'At least 3 characters...',
      //msgTarget: 'under',
      triggerClass:'x-form-search-trigger',
      obj: this // zpetny odkaz na objekt
    });
    
    /**
     * Method: showError
     * Display error
     */
    this.showError = function(){
      Ext.Msg.alert('Error', 'Source not found at:' + this.url);
      this.theMask.hide();      
    };
   
    /**
     * Method: drawTerms
     * Display gemet germs  in tree structure
     *
     * Parameters:
     * r - {HTTPResponse} 
     * o 
     */
    this.drawTerms = function(r,o){
      this.theMask.hide();
      var root = o.options.node;
      root.getUI().getIconEl().src = Ext.BLANK_IMAGE_URL;
      root.getUI().getIconEl().className = "x-tree-node-icon";
      if(r.responseText){
        try{
          var data = Ext.util.JSON.decode(r.responseText);
          this.drawBranch(root, data);
          root.expand();
        }catch(e){alert('Data error!');}
      }     
    };

    /** 
     * Method: drawBranch
     *
     * Parameters:
     * root - {Ext.Node} root element
     * data - {Object}
     */
    this.drawBranch = function(root, data){
      for(var i=0;i<data.length;i++){
        if(data[i].uri.indexOf(this.INSPIRE)>-1) var icon=this.appPath+'img/inspire.gif';
        else if(data[i].uri.indexOf(this.THEME)>-1) var icon=this.appPath+'img/eeaicon.gif';
        else if(data[i].uri.indexOf(this.GROUP)>-1) var icon=this.appPath+'img/group.gif';
        else var icon=this.appPath+'img/term.gif';
        var node = new Ext.tree.TreeNode({
          text: data[i].preferredLabel.string,
          termId: data[i].uri,
          data: data[i],
          icon: icon,
          cls: 'thes-link'
        });
        if((this.returnInspire)&&(data[i].uri.indexOf(this.INSPIRE)>-1)){ 
            node.on('click', this.returnTerm, this, data[i].termId);
        }  
        else node.on('click', this.getById, this, data[i].termId);
        root.appendChild(node);
      }        
    };

    /**
     * Method: emptyTree
     * empties tree structure 
     */
    this.emptyTree = function(){
      var root = this.thesRoot;
      while(root.item(0)) root.removeChild(root.item(0));    
    };
    
    /**
     * Method: emptyAll
     * -- Additional method for EasySDI --
     */
    this.emptyAll = function(){
    	this.toolbars[0].findByType('trigger')[0].reset();
    	this.thesRoot=new Ext.tree.TreeNode({
    		draggable: true,
    		allowChildren: true,
    		leaf : false,singleClickExpand : true,
    		text : '',
    		cls: 'thes-root',expanded: true
    	});
    	this.treePanel.setRootNode(this.thesRoot);
    	this.treePanel.topToolbar.hide();
    }
    
    /**
     * Method: getByTerm
     * Runs thesaurus query by (sub)string. Ajax returns to drawTerms
     */     
    this.getByTerm = function(){
        this.obj.emptyTree();
        this.obj.detailPanel.collapse();
        this.obj.treePanel.topToolbar.hide();

        if(this.getValue().length < this.minLength) {
            Ext.Msg.alert(HS.i18n('Warning'), '&gt;= ' +this.minLength+' '+HS.i18n('characters required'));
            return false;
        }

        if(!this.obj.theMask)
            this.obj.theMask = new Ext.LoadMask(this.obj.body);

        this.obj.theMask.show();
        this.obj.thesRoot.setText(HS.i18n('Found'));  
        Ext.Ajax.request({
            url: this.obj.prepareRequest("getConceptsMatchingRegexByThesaurus?thesaurus_uri="+
                        this.obj.CONCEPT+"&language="+this.obj.lang+"&regex="+this.getValue()),
            scope: this.obj,
            options: {node: this.obj.thesRoot},
            success: this.obj.drawTerms,
            failure: this.obj.showError
        });
    };

    /**
     * Method: getTopConcepts
     * Returnes top concepts for thesaurus 
     */
    this.getTopConcepts = function(conceptURI){
        this.emptyTree();
        this.treePanel.topToolbar.hide();
        this.detailPanel.body.update('');
        this.detailPanel.collapse();

        if(!this.theMask)
            this.theMask = new Ext.LoadMask(this.body);

        this.theMask.show();

        if(conceptURI==this.INSPIRE)
            this.thesRoot.setText(HS.i18n('INSPIRE themes'));
        else
            this.thesRoot.setText(HS.i18n('Top concepts'));

        Ext.Ajax.request({
            url: this.prepareRequest("getTopmostConcepts?thesaurus_uri="+conceptURI+"&language="+this.lang),
            scope: this,
            options: {node: this.thesRoot},
            success: this.drawTerms,
            failure: this.showError
        });
    };

    /**
     * NEW
     * Runs thesaurus getRelatedConcepts by id. Ajax returns to drawTermsId
     */     
    this.getById = function(theNode){

        if(!this.theMask)
            this.theMask = new Ext.LoadMask(this.body);

        this.data=theNode.attributes.termId;
        this.emptyTree();
        this.treePanel.topToolbar.show();
        this.thesRoot.setText(theNode.text);
        var theTitle = this.treePanel.topToolbar.items.item(2);
        //theTitle.getEl().innerHTML="<span class='thes-term'><b>"+theNode.text+"</b></span>";
        theTitle.update("<span class='thes-term'><b>"+theNode.text+"</b></span>");

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
            url: this.prepareRequest("getRelatedConcepts?concept_uri="+
                                     theNode.attributes.termId+
                                     "&relation_uri=http://www.w3.org/2004/02/skos/core%23narrower&language="+
                                     this.lang),
            scope: this,
            options: {node: nt},
            success: this.drawTerms,
            failure: this.showError
        });

        var bt = new Ext.tree.TreeNode({text: HS.i18n("BT"),
                                        termId: 'bt',
                                        icon:this.appPath+'img/indicator.gif'});

        this.thesRoot.appendChild(bt);      

        Ext.Ajax.request({
            url: this.prepareRequest("getRelatedConcepts?concept_uri="+
                                        theNode.attributes.termId+
                                        "&relation_uri=http://www.w3.org/2004/02/skos/core%23broader&language="+
                                        this.lang),
            scope: this,
            options: {node: bt},
            success: this.drawTerms,
            failure: this.showError
        });
        
        var rt = new Ext.tree.TreeNode({text: HS.i18n("RT"), termId: 'rt', icon:this.appPath+'img/indicator.gif'});
        this.thesRoot.appendChild(rt);      

        Ext.Ajax.request({
            url: this.prepareRequest("getRelatedConcepts?concept_uri="+
                     theNode.attributes.termId+
                     "&relation_uri=http://www.w3.org/2004/02/skos/core%23related&language="+
                     this.lang),
            scope: this,
            options: {node: rt},
            success: this.drawTerms,
            failure: this.showError
        });

        this.thesRoot.expand();
    };
	
    /** 
     * Method: prepareRequest
     * adds proxy to URL
     *
     * Parameters:
     * arg {String}
     *
     * Return:
     * {String} 
     */
    this.prepareRequest = function(arg){
        var url = this.url+arg;

        if(this.proxy)
            return this.proxy+escape(url);
        else
            return url;
    }; 
    
    /**
     * Method: returnTerm
     * returns selected term (all languages, with paths)
     *
     * Method:
     * obj {Ext.Object}
     */
    this.returnTerm = function(obj){
        if(obj.xtype != 'button')
            this.data=obj.attributes.termId;
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
    }; 
	
    /**
     * Method: getBroaderConcept
     *
     * Parameters:
     * uri - {String}
     * lang - {String}
     */
    this.getBroaderConcept = function(uri, lang){
      Ext.Ajax.request({
        url: this.prepareRequest("getRelatedConcepts?concept_uri="+uri+"&relation_uri=http://www.w3.org/2004/02/skos/core%23broader&language="+lang),
        scope: this,
        success: this.getConceptBack,
        failure: this.showError
      });
    };
    
    /**
     * Method: getConceptBack
     *
     * Parameters:
     * r
     * o
     */
    this.getConceptBack = function(r,o){
        if(r.responseText){
            try{
                var data = Ext.util.JSON.decode(r.responseText);
                if(!data.preferredLabel) {
                    for(var i=0;i<data.length;i++){
                        if(data[i].uri.indexOf(this.CONCEPT)>-1){
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
                else
                    this.output.terms[data.preferredLabel.language] = data.preferredLabel.string + 
                        this.separator + this.output.terms[data.preferredLabel.language];
                if(this.returnPath)
                    this.getBroaderConcept(data.uri, data.preferredLabel.language);
                else
                    this.finishTerm();
            }
            catch(e){alert('Data error!');}
        }
        else {
            this.finishTerm();
        }   
    };
    
    /**
     * Method: finishTerm
     * when term is returned 
     */ 
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
    };
    
    /**
     * Method: returnTerms
     *
     * Parameters:
     * r
     * o
     */
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
    };
    
    /* 
     * user interface definition 
     */
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
    	[{xtype: 'button', text:HS.i18n("Use"), icon:this.appPath+'img/drop-yes.gif', cls:'x-btn-text-icon', handler: this.returnTerm, scope:this}, '-', 'xxx']
    );
        
    this.treePanel = new Ext.tree.TreePanel({
      layout: 'fit',
      useArrows: true,
      autoScroll: true,
      region: 'center',  
      tbar: tb, 
      rootVisible: true    
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
        
    searchField.onTriggerClick = this.getByTerm;
    searchField.on('specialkey', function(f, e){
      if(e.getKey() == e.ENTER)  searchField.onTriggerClick();
    }, searchField);

    config.layout = 'border';
    config.tbar = [
    	{handler: function(){this.getTopConcepts(this.INSPIRE);}, 
    	 icon:this.appPath+'img/inspire.gif', cls:'x-btn-icon', tooltip: HS.i18n('INSPIRE themes'),
    	 scope:this},
    	{handler: function(){this.getTopConcepts(this.CONCEPT);}, 
    	 icon:this.appPath+'img/eeaicon.gif', cls:'x-btn-icon', tooltip: HS.i18n('GEMET top concepts'),
    	 scope:this}, 
    	"-", 
    	HS.i18n("Search")+': ', 
    	searchField];
    config.items = [this.detailPanel, this.treePanel];

    ThesaurusReader.superclass.constructor.call(this,config);   

};  
  
Ext.extend(ThesaurusReader, Ext.Panel, {});
