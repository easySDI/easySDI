/**
*	EasySDI, a solution	to implement easily	any	spatial	data infrastructure
*   Copyright (C) EasySDI Community
*   For more information : www.easysdi.org
*
*	This program is	free software: you can redistribute	it and/or	modify
*	it under the terms of	the	GNU	General	Public License as	published	by
*	the	Free Software	Foundation,	either version 3 of	the	License, or
*	any	later	version.
*	This program is	distributed	in the hope	that it	will be	useful,
*	but	WITHOUT	ANY	WARRANTY;	without	even the implied warranty	of
*	MERCHANTABILITY	or FITNESS FOR A PARTICULAR	PURPOSE.	See	the
*	GNU	General	Public License for more	details.
*	You	should have	received a copy	of the GNU General Public	License
*	along	with this	program.	If not,	see	http://www.gnu.org/licenses/gpl.html.
*/

// String	localisation prefix	FP

Ext.namespace("EasySDI_Map");

 var filterPanelMode = {none:7};
    
/**
 * EasySDI_Map.BaseFilterPanel
 * A base	class	for	panels which load	forms	into a tabPanel. They	call doLayout	when the
 * panel is	first	activated, which gets	around some	ExtJS	Layout problems	with controls	not
 * appearing until the browser is	resized.
 */
EasySDI_Map.BaseFilterPanel	=	Ext.extend(Ext.Panel,	{

  filterWillBeFast: false,

	constructor: function(config)	{

		this.filter	=	new	OpenLayers.Filter.Logical({
				type:	OpenLayers.Filter.Logical.OR,
				filters: []
		});

		config.listeners={
			'activate':	{	fn:	function(p)	{
					p.doLayout();
				},
				single:true
			}
		};

		// Event for when	the	tab's	description	changes
		this.addEvents("changedesc");

		EasySDI_Map.BaseFilterPanel.superclass.constructor.apply(this, arguments);
	},

	/**
	 * Method	should be	overridden in	subclasses.
	 */
	getDescription:	function() {
		return "not	implemented";
	}

});

/**
 * The vast	majority of	EasySDI_Map.MiscAttrPanel	and	EasySDI_Map.MiscAttrFilterPanel	are	taken
 * from	the	equivalent part	of the Drake project.
 * TODO	in this	section:
 * 1)	Confirm	any	special	front	end	processing for dates
 * 2)	Check	how	panel	scrolls	when full	up
 */
EasySDI_Map.MiscAttrPanel	=	Ext.extend(EasySDI_Map.BaseFilterPanel,
{
	/**
	 * Property: builderTypeNames
	 * {Array} A list	of labels	for	that correspond	to builder type	constants.
	 *		 These will	be the option	names	available	in the builder type	combo.
	 */
	builderTypeNames:	[],

	/**
	 * Property: allowedBuilderTypes
	 * {Array} List	of builder type	constants.	Default	is
	 *		 [ANY_OF,	ALL_OF,	NONE_OF].
	 */
	allowedBuilderTypes: null,

	builderType: null,

	childFiltersPanel: null,

	customizeFilterOnInit: true,

	initComponent: function()	{

		var	defConfig	=	{
			plain: true,
			border:	false,
			defaultBuilderType:	EasySDI_Map.MiscAttrPanel.ANY_OF
		};
		Ext.applyIf(this,	defConfig);

		if(this.customizeFilterOnInit) {
			this.filter	=	this.customizeFilter(this.filter);
		}

		if(this.builderTypeNames.length	<	1) {
			this.builderTypeNames.push(EasySDI_Map.lang.getLocal('FP_MA_ANY'));
			this.builderTypeNames.push(EasySDI_Map.lang.getLocal('FP_MA_ALL'));
			this.builderTypeNames.push(EasySDI_Map.lang.getLocal('FP_MA_NONE'));
			this.builderTypeNames.push(EasySDI_Map.lang.getLocal('FP_MA_NOT_ALL'));
		}

		this.builderType = this.getBuilderType();

		this.items = [{
				xtype: "panel",
				border:	false,
				items: [{
						xtype: "panel",
						border:	false,
						layout:	"column",
						style: "margin-bottom: 6px",
						defaults:	{
							border:	false
						},
						items: [{
								xtype: 'label',
								cls: "x-form-item",
								text:	EasySDI_Map.lang.getLocal('FP_MA_MATCH'),
								style: "margin-top:	4px; margin-right: 4px;"
							},
							this.createBuilderTypeCombo(),
							{
								xtype: 'label',
								cls: "x-form-item",
								text:	EasySDI_Map.lang.getLocal('FP_MA_FOLLOWING'),
								style: "margin-top:	4px; margin-left:	4px;"
						}]
				}]
			}, this.createChildFiltersPanel()
		];

		this.bbar	=	this.createToolBar();
		this.addEvents(
						/**
						 * Event:	change
						 * Fires when	the	filter changes.
						 *
						 * Listener	arguments:
						 * builder - {EasySDI_Map.MiscAttrPanel} This	filter builder.	 Call
						 *		 <getFilter> to	get	the	updated	filter.
						 */
						"change"
		);

		EasySDI_Map.MiscAttrPanel.superclass.initComponent.call(this);

	},

	/**
	 * Method: createToolBar
	 */
	createToolBar: function()	{
		var	bar	=	[{
				text:	EasySDI_Map.lang.getLocal('FP_MA_ADD_CONDITION'),
				iconCls: "x-btn-icon addSearchBtn",
				handler: function()	{
					this.addCondition();
				},
				scope: this
			},{
				text:	EasySDI_Map.lang.getLocal('FP_MA_ADD_GROUP'),
				iconCls: "x-btn-icon addSearchBtn",
				handler: function()	{
					this.addCondition(true);
				},
				scope: this
		}];
		return bar;
	},

	/**
	 * Returns an	array	of openLayers	filter objects that	describe this
	 * panel's filters.
	 *
	 * Returns:
	 * array({OpenLayers.Filter})	OpenLayers filter	object.
	 */
	getFilters:	function() {
		var	filter;
		if(this.filter)	{
			filter = this.filter.clone();
			if(filter	instanceof OpenLayers.Filter.Logical)	{
				filter = this.cleanFilter(filter);
				// After cleaning, it	might	not	be a logical filter	anymore.
				if (filter instanceof	OpenLayers.Filter.Logical	&& filter.filters.length===0)	{
					// Ignore	logical	filters	with no	content	as they	break	things and mean	nothing	anyway.
					return [];
				}
			}

		}
		return [filter];
	},

	/**
	*	Encode the misc	attrs	filter to	an object	that can be	saved.
	*/
	encode:	function() {
		var	filters=this.getFilters();
    if (filters.length>0) {
		// This	tab	only ever	generates	one	top	level	filter.
		var	dom	=	new	OpenLayers.Format.Filter.v1_0_0().write(filters[0]);
		var	filterText = this._XMLtoString(dom);
		return filterText;
    } else {
      return '';
    }
	},

	/**
	*	Decode the misc	attrs	filter from	a	saved	object.
	*/
	decode:	function(filterStr)	{
		// Get our XML as	a	DOM	object
		try	//Internet Explorer
		{
			xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
			xmlDoc.async="false";
			xmlDoc.loadXML(filterStr);
		}
		catch(e)
		{
			parser=new DOMParser();
			xmlDoc=parser.parseFromString(filterStr,"text/xml");
		}
		// Now OL	can	turn this	into a filter	object
		var	format = new OpenLayers.Format.Filter({version:"1.0.0"});
		this.filter	=	format.read(xmlDoc.firstChild);

		if(this.customizeFilterOnInit) {
			this.filter	=	this.customizeFilter(this.filter);
		}
		this.childFiltersPanel.removeAll();
		this.populateChildFiltersPanel();
	},

	clear: function()	{
		this.childFiltersPanel.removeAll();
		this.filter	=	this.customizeFilter(null);
	},

	/**
	 * Convert an	XML	DOM	element	to a string, so	it can be	passed as	a	POST parameter.
	 */
	_XMLtoString:	function(elem) {
		var	serialized;
		try	{
			// XMLSerializer exists	in current Mozilla browsers
			serializer = new XMLSerializer();
			serialized = serializer.serializeToString(elem);
		}
		catch	(e)	{
			// Internet	Explorer has a different approach	to serializing XML
			serialized = elem.xml;
		}
		return serialized;
	},

	/**
	 * Method: cleanFilter
	 * Ensures that	binary logical filters have	more than	one	child	and	that comparison
	 * filters have	a	property and value.
	 *
	 * Parameters:
	 * filter	-	{OpenLayers.Filter.Logical}	A	logical	filter.
	 *
	 * Returns:
	 * {OpenLayers.Filter} An	equivalent filter	to the input,	where	all
	 *		 binary	logical	filters	have more	than one child filter.
	 */
	cleanFilter: function(filter)	{
		var	child;
		if(filter	instanceof OpenLayers.Filter.Logical)	{
			if(filter.type !== OpenLayers.Filter.Logical.NOT &&	filter.filters.length	===	1) {
				child=filter.filters[0];
				if (child	instanceof OpenLayers.Filter.Comparison	&& !child.property)	{
					filter.filters=[];
				}	else {
					filter = this.cleanFilter(child);
				}
			}	else {
				for(var	i=0, len=filter.filters.length;	i<len; ++i)	{
					child	=	filter.filters[i];
					if(child instanceof	OpenLayers.Filter.Logical) {
						filter.filters[i]	=	this.cleanFilter(child);
					}	else if(child	instanceof OpenLayers.Filter.Comparison	&& !child.property)	{
						filter.filters.splice(i, 1);
						// repeat	current	position in	loop,	since	one	less
						i--;
					}
				}
			}
		}
		return filter;
	},

	/**
	 * Method: customizeFilter
	 * Create	a	filter that	fits the model for this	filter builder.	 This	filter
	 *		 will	not	necessarily	meet the Filter	Encoding specification.	 In
	 *		 particular, filters representing	binary logical operators may not
	 *		 have	two	child	filters.	Use	the	<getFilter>	method to	return a
	 *		 filter	that meets the encoding	spec.
	 *
	 * Parameters:
	 * filter	-	{OpenLayers.Filter}	The	input	filter.	 This	filter will	not
	 *		 be	modified.	 Register	for	events to	receive	an updated filter, or
	 *		 call	<getFilter>.
	 *
	 * Returns:
	 * {OpenLayers.Filter} A filter	that fits	the	model	used by	this builder.
	 */
	customizeFilter: function(filter)	{
		var	child, i;
		if(!filter)	{
			filter = this.wrapFilter(this.createDefaultFilter());
		}	else {
			filter = this.cleanFilter(filter);
			switch(filter.type)	{
				case OpenLayers.Filter.Logical.AND:
				case OpenLayers.Filter.Logical.OR:
					if(!filter.filters ||	filter.filters.length	===	0) {
						// give	the	filter children	if it	has	none
						filter.filters = [this.createDefaultFilter()];
					}	else {
						for(i=0; i<filter.filters.length;	++i) {
							child	=	filter.filters[i];
							if(child instanceof	OpenLayers.Filter.Logical) {
								filter.filters[i]	=	this.customizeFilter(child);
							}
						}
					}
					// wrap	in a logical OR
					filter = new OpenLayers.Filter.Logical({
							type:	OpenLayers.Filter.Logical.OR,
							filters: [filter]
					});
					break;
				case OpenLayers.Filter.Logical.NOT:
					if(!filter.filters ||	filter.filters.length	===	0) {
						filter.filters = [
							new	OpenLayers.Filter.Logical({
									type:	OpenLayers.Filter.Logical.OR,
									filters: [this.createDefaultFilter()]
							})
						];
					}	else {
						// NOT filters should	have one child only
						child	=	filter.filters[0];
						if(child instanceof	OpenLayers.Filter.Logical) {
							if(child.type	!==	OpenLayers.Filter.Logical.NOT) {
								// check children	of AND and OR
								for(i=0; i<child.filters.length; ++i)	{
									var	grandchild = child.filters[i];
									if(grandchild	instanceof OpenLayers.Filter.Logical)	{
										child.filters[i] = this.customizeFilter(grandchild);
									}
								}
							}	else {
								// silly double	negative
								if(child.filters &&	child.filters.length > 0)	{
									filter = this.customizeFilter(child.filters[0]);
								}	else {
									filter = this.wrapFilter(this.createDefaultFilter());
								}
							}
						}	else {
							// non-logical child of	NOT	should be	wrapped
							var	type;
							if(this.defaultBuilderType === EasySDI_Map.MiscAttrPanel.NOT_ALL_OF) {
								type = OpenLayers.Logical.Filter.AND;
							}	else {
								type = OpenLayers.Logical.Filter.OR;
							}
							filter.filters = [
								new	OpenLayers.Filter.Logical({
										type:	type,
										filters: [child]
								})
							];
						}
					}
					break;
				default:
					// non-logical filters get wrapped
					filter = this.wrapFilter(filter);
			}
		}
		return filter;
	},

	createDefaultFilter: function()	{
		return new OpenLayers.Filter.Comparison();
	},

	/**
	 * Method: wrapFilter
	 * Given a non-logical filter, this	creates	parent filters depending on
	 *		 the <defaultBuilderType>.
	 *
	 * Parameters:
	 * filter	-	{OpenLayers.Filter}	A	non-logical	filter.
	 *
	 * Returns:
	 * {OpenLayers.Filter} A wrapped version of	the	input	filter.
	 */
	wrapFilter:	function(filter) {
		var	type;
		if(this.defaultBuilderType === EasySDI_Map.MiscAttrPanel.ALL_OF) {
			type = OpenLayers.Filter.Logical.AND;
		}	else {
			type = OpenLayers.Filter.Logical.OR;
		}
		return new OpenLayers.Filter.Logical({
				type:	OpenLayers.Filter.Logical.OR,
				filters: [
					new	OpenLayers.Filter.Logical({
							type:	type,	filters: [filter]
					})
				]
		});
	},

	/**
	 * Method: addCondition
	 * Add a new condition or	group	of conditions	to the builder.	 This
	 *		 modifies	the	filter and adds	a	panel	representing the new condition
	 *		 or	group	of conditions.
	 */
	addCondition:	function(group)	{
		var	filter,	type;
		if(group)	{
			type = "em_filterbuilder";
			filter = this.wrapFilter(this.createDefaultFilter());
		}	else {
			type = "em_filterpanel";
			filter = this.createDefaultFilter();
		}
		var	newChild = this.newRow({
				xtype: type,
				filter:	filter,
				attributes:	this.attributes,
				customizeFilterOnInit: group &&	false,
				listeners: {
					change:	function() {
						this.fireEvent("change", this);
						this.fireEvent("changedesc", this, this.getDescription());
					 },	scope: this}
		});
		this.childFiltersPanel.add(newChild);
		this.filter.filters[0].filters.push(filter);
		this.childFiltersPanel.doLayout();
	},

	/**
	 * Method: removeCondition
	 * Remove	a	condition	or group of	conditions from	the	builder.	This
	 *		 modifies	the	filter and removes the panel representing	the	condition
	 *		 or	group	of conditions.
	 */
	removeCondition: function(panel, filter) {
		var	parent = this.filter.filters[0].filters;
		if(parent.length > 1)	{
			parent.remove(filter);
			this.childFiltersPanel.remove(panel);
		}
		this.fireEvent("change", this);
		this.fireEvent("changedesc", this, this.getDescription());
	},

	createBuilderTypeCombo:	function() {
		var	types	=	this.allowedBuilderTypes ||	[
				EasySDI_Map.MiscAttrPanel.ANY_OF,	EasySDI_Map.MiscAttrPanel.ALL_OF,
				EasySDI_Map.MiscAttrPanel.NONE_OF
		];
		var	numTypes = types.length;
		var	data = new Array(numTypes);
		var	type;
		for(var	i=0; i<numTypes; ++i)	{
			type = types[i];
			data[i]	=	[type, this.builderTypeNames[type]];
		}
		return {
				xtype: "combo",
				store: new Ext.data.SimpleStore({
						data:	data,
						fields:	["value",	"name"]
				}),
				value: this.builderType,
				displayField:	"name",
				valueField:	"value",
				triggerAction: "all",
				mode:	"local",
				listeners: {
					select:	function(combo,	record)	{
						this.changeBuilderType(record.get("value"));
						this.fireEvent("change", this);
						this.fireEvent("changedesc", this, this.getDescription());
					}, scope:	this},
				width: componentParams.MiscSearch.typeComboWidth
		};
	},

	/**
	 * Method: createChildFiltersPanel
	 * Create	the	panel	that holds all conditions	and	condition	groups.	 Since
	 *		 this	is called	after	this filter	has	been customized, we	always
	 *		 have	a	logical	filter with	one	child	filter - that	child	is also
	 *		 a logical filter.
	 *
	 * Returns:
	 * {Ext.Panel} A child filters panel.
	 */
	createChildFiltersPanel: function()	{
		this.childFiltersPanel = new Ext.Panel({
				border:	false,
				defaults:	{border: false}
		});
		this.populateChildFiltersPanel();

		return this.childFiltersPanel;
	},

	populateChildFiltersPanel: function(){
		var	grandchildren	=	this.filter.filters[0].filters;
		for(var	i=0, len=grandchildren.length; i<len;	++i) {
			var	grandchild = grandchildren[i];
			this.childFiltersPanel.add(this.newRow({
						xtype: (grandchild instanceof	OpenLayers.Filter.Logical) ?
									 "em_filterbuilder"	:	"em_filterpanel",
						filter:	grandchild,
						attributes:	this.attributes,
						listeners: {
							change:	function(item) {
								this.fireEvent("change", this);
								this.fireEvent("changedesc", this, this.getDescription());
							}, scope:	this}
			}));
		}
	},

	setFilter: function(filter)	{
		while	(this.childFiltersPanel.items.items.length > 0){
			this.childFiltersPanel.remove(this.childFiltersPanel.items.items[0]);
		}
		this.filter	=	this.customizeFilter(filter);
		this.populateChildFiltersPanel();
		this.childFiltersPanel.doLayout();
	},

	/**
	 * Method: newRow
	 * Generate	a	"row"	for	the	child	filters	panel.	This couples another
	 *		 filter	panel	or filter	builder	with a component that	allows for
	 *		 condition removal.
	 *
	 * Returns:
	 * {Ext.Panel} A panel that	serves as	a	row	in a child filters panel.
	 */
	newRow:	function(filterPanel)	{
		var	panel	=	new	Ext.Panel({
				layout:	"column",
				defaults:	{border: false},
				style: "padding: 0.25em	0.25em;",	// TODO	?
				items: [{
						border:	false,
						columnWidth: 0.1,
						items: [{
								xtype: "button",
								tooltip: EasySDI_Map.lang.getLocal('FP_MA_DEL_CONDITION'),
								cls: 'x-btn-icon',
								iconCls: "clearSearchBtn", //	removeSearchBtn?
								handler: function()	{
									this.removeCondition(panel,	filterPanel.filter);
								}, scope:	this}]
					}, {
						items: [filterPanel],
						border:	false,
						columnWidth: 0.9
				}]
		});
		return panel;
	},

	changeBuilderType: function(type)	{
		if(type	!==	this.builderType)	{
			this.builderType = type;
			var	child	=	this.filter.filters[0];
			switch(type) {
				case EasySDI_Map.MiscAttrPanel.ANY_OF:
					this.filter.type = OpenLayers.Filter.Logical.OR;
					child.type = OpenLayers.Filter.Logical.OR;
					break;
				case EasySDI_Map.MiscAttrPanel.ALL_OF:
					this.filter.type = OpenLayers.Filter.Logical.OR;
					child.type = OpenLayers.Filter.Logical.AND;
					break;
				case EasySDI_Map.MiscAttrPanel.NONE_OF:
					this.filter.type = OpenLayers.Filter.Logical.NOT;
					child.type = OpenLayers.Filter.Logical.OR;
					break;
				case EasySDI_Map.MiscAttrPanel.NOT_ALL_OF:
					this.filter.type = OpenLayers.Filter.Logical.NOT;
					child.type = OpenLayers.Filter.Logical.AND;
					break;
			}
		}
	},

	/**
	 * Method: getBuilderType
	 * Determine the builder type	based	on this	filter.
	 *
	 * Returns:
	 * {Integer} One of	the	builder	type constants.
	 */
	getBuilderType:	function() {
		var	type = this.defaultBuilderType;
		if(this.filter)	{
			var	child	=	this.filter.filters[0];
			if(this.filter.type	===	OpenLayers.Filter.Logical.NOT) {
				switch(child.type) {
					case OpenLayers.Filter.Logical.OR:
						type = EasySDI_Map.MiscAttrPanel.NONE_OF;
						break;
					case OpenLayers.Filter.Logical.AND:
						type = EasySDI_Map.MiscAttrPanel.NOT_ALL_OF;
						break;
				}
			}	else {
				switch(child.type) {
					case OpenLayers.Filter.Logical.OR:
						type = EasySDI_Map.MiscAttrPanel.ANY_OF;
						break;
					case OpenLayers.Filter.Logical.AND:
						type = EasySDI_Map.MiscAttrPanel.ALL_OF;
						break;
				}
			}
		}
		return type;
	},

	/**
	 * Return	a	natural	language description of	the	tab	filter.
	 */
	getDescription:	function() {
		var	desc="", filters=this.getFilters();
		desc = this._getSingleFilterDesc(filters);

		return desc.length>0 ? desc[0] : null;
	},

	/**
	 * Return	an array of	filter descriptions	that describe	a	filter and
	 * its children.
	 */
	_getSingleFilterDesc:	function(filters)	{
    var desc, result=[], inner=[], join;
		var	operators={
			"==":	EasySDI_Map.lang.getLocal('FP_EQ'),
			"!=":	EasySDI_Map.lang.getLocal('FP_NOT_EQ'),
			"<": EasySDI_Map.lang.getLocal('FP_LT'),
			">": EasySDI_Map.lang.getLocal('FP_GT'),
			"<=":	EasySDI_Map.lang.getLocal('FP_LT_EQ'),
			">=":	EasySDI_Map.lang.getLocal('FP_GT_EQ'),
			"~": EasySDI_Map.lang.getLocal('FP_MATCHES')
		};
		Ext.each(filters,	function(filter) {
			if (filter.CLASS_NAME=="OpenLayers.Filter.Comparison") {
				desc = filter.property + " " + operators[filter.type]	+	"	"	+	filter.value;
				result.push(desc);
			}	else if	(filter.CLASS_NAME=="OpenLayers.Filter.Logical") {
				inner	=	this._getSingleFilterDesc(filter.filters);
				desc = "";
				switch (filter.type) {
					case "||":
						join=EasySDI_Map.lang.getLocal('FP_OR');
						break;
					case "&&":
						join=EasySDI_Map.lang.getLocal('FP_AND');
						break;
					case "!":
						join=EasySDI_Map.lang.getLocal('FP_NOR');

				}
				desc=inner.join("	"+join+" ");
				if (filter.type=="!")	{
					desc=EasySDI_Map.lang.getLocal('FP_NEITHER_').replace("*", desc);
				}
				result.push(desc);
			}
		}, this);
		return result;
	}

});

EasySDI_Map.MiscAttrPanel.ANY_OF = 0;
EasySDI_Map.MiscAttrPanel.ALL_OF = 1;
EasySDI_Map.MiscAttrPanel.NONE_OF	=	2;
EasySDI_Map.MiscAttrPanel.NOT_ALL_OF = 3;

Ext.reg('em_filterbuilder',	EasySDI_Map.MiscAttrPanel);


EasySDI_Map.MiscAttrFilterPanel	=	Ext.extend(Ext.Panel,	{

	/**
	 * Property: filter
	 * {OpenLayers.Filter} Optional	non-logical	filter provided	in the initial
	 *		 configuration.	 To	retreive the filter, use <getFilter> instead
	 *		 of	accessing	this property	directly.
	 */
	filter:	null,

	/**
	 * Property: attributes
	 * A configured	attributes store for use in
	 *		 the filter	property combo.
	 */
	attributes:	null,

	/**
	 * Property: attributesComboConfig
	 * {Object}
	 */
	attributesComboConfig: null,

	/**
   * Property: comparisons
   * A configured list of the available comparison types
	 */
	comparisons: null,

	/**
   * Property: keylistComparisons
   * A configured list of the available comparison types for keylist attributes
   */
  keylistComparisons: null,

  /**
	 * Property: attributesComboConfig
	 * {Object}
	 */
	comparisonComboConfig: null,

	initComponent: function()	{

		var	defConfig	=	{
			plain: true,
			border:	false
		};
		Ext.applyIf(this,	defConfig);

		if(!this.filter) {
			this.filter	=	this.createDefaultFilter();
		}
		// Get the display names for each	attribute	allowed	in the filter
		var	attrArray	=	[];
		Ext.each(componentParams.MiscSearch.attrList,	function(attr) {
			var	catAndName=EasySDI_Map.lang.getLocal('COL_'	+	attr);
			attrArray.push([attr,	catAndName.split('/')[1]]);
		}, this);

		if(!this.attributes) {
			this.attributes	=	new	Ext.data.SimpleStore({
					fields:	['fieldName',	'name'],
					data:	attrArray
			});
		}

		var	defAttributesComboConfig = {
				xtype: "combo",
				store: this.attributes,
				editable:	false,
				triggerAction: "all",
				hideLabel: true,
				allowBlank:	false,
				displayField:	"name",
				valueField:	"fieldName",
				value: this.filter.property,
				mode:	'local',
				listeners: {
					select:	function(combo,	record)	{
						this.filter.property = record.get("fieldName");
            if (record.get("fieldName").substring(record.get("fieldName").length-7)=='keylist') {
			  // The comparison can only be Like for keylists.
              this.comparisonCombo.setValue(OpenLayers.Filter.Comparison.LIKE);
              this.filter.type=OpenLayers.Filter.Comparison.LIKE;
              this.comparisonCombo.disable();
            } else {
              this.comparisonCombo.enable();
            }
						this.fireEvent("change", this.filter);
					}, scope:	this},
				width: componentParams.MiscSearch.attrComboWidth
		};
		this.attributesComboConfig = this.attributesComboConfig	|| {};
		Ext.applyIf(this.attributesComboConfig,	defAttributesComboConfig);

		if(!this.comparisons)	{
			this.comparisons = new Ext.data.SimpleStore({
					 fields: ['value', 'name'],
					 data: componentParams.MiscSearch.compList
			});
		}
    if(!this.keylistComparisons) {
      this.keylistComparisons = new Ext.data.SimpleStore({
           fields: ['value', 'name'],
           data: componentParams.MiscSearch.keylistCompList
      });
    }
		if (this.filter.type === undefined ||	this.filter.type === null) {
			this.filter.type = this.comparisons.data.items[0].data.value;
		}
		var	defCompComboConfig = {
				xtype: "combo",
				store: this.comparisons,
				editable:	false,
				triggerAction: "all",
				hideLabel: true,
				allowBlank:	false,
				displayField:	"name",
				valueField:	"value",
				value: this.filter.type,
				mode:	'local',
				listeners: {
					select:	function(combo,	record)	{
						this.filter.type = record.get("value");
						this.fireEvent("change", this.filter);
				}, scope:	this},
				width: componentParams.MiscSearch.compComboWidth
		};

		this.comparisonComboConfig = this.comparisonComboConfig	|| {};
		Ext.applyIf(this.comparisonComboConfig,	defCompComboConfig);

		this.items = this.createFilterItems();

		this.addEvents(
				/**
				 * Event:	change
				 * Fires when	the	filter changes.
				 *
				 * Listener	arguments:
				 * filter	-	{OpenLayers.Filter}	This filter.
				 */
				"change"
		);

		EasySDI_Map.MiscAttrFilterPanel.superclass.initComponent.call(this);
	},

	/**
	 * Method: createDefaultFilter
	 * May be	overridden to	change the default filter.
	 *
	 * Returns:
	 * {OpenLayers.Filter} By	default, returns a comarison filter.
	 */
	createDefaultFilter: function()	{
		return new OpenLayers.Filter.Comparison();
	},

	/**
	 * Method: createFilterItems
	 * Creates a panel config	containing filter	parts.
	 */
	createFilterItems: function()	{
    this.comparisonCombo = new Ext.form.ComboBox(this.comparisonComboConfig);

		return [{
				layout:	"column",
				border:	false,
				defaults:	{border: false},
				items: [{
						width: this.attributesComboConfig.width,
						items: [this.attributesComboConfig]
					}, {
						width: this.comparisonComboConfig.width,
            items: this.comparisonCombo
					}, {
						items: [{
								xtype: "textfield",
								width: componentParams.MiscSearch.textBoxWidth,
								value: this.filter.value,
								allowBlank:	false,
								listeners: {
									change:	function(el, value)	{
										this.filter.value	=	value;
										this.fireEvent("change", this.filter);
									}, scope:	this}
						}]
				}]
		}];
	}

});

Ext.reg('em_filterpanel',	EasySDI_Map.MiscAttrFilterPanel);

// End of	the	Misc Attributes	search panel

// Filter	Place	panel

EasySDI_Map.PlaceTreeLoader	=	Ext.extend(Ext.tree.TreeLoader,	{
	constructor: function(layers,	layerGroups){
		this.currentLayerGroup = false;
		this.currentRootFilter = false;
		// Build a proxy for each	of the searchable	layers
		Ext.each(SData.localisationLayers, function(layer) {
			var	parts	=	layer.feature_type_name.split(":");
			layer.proxy	=	new	GeoExt.data.ProtocolProxy({protocol:
		        new OpenLayers.Protocol.WFS.v1_0_0({
		            url: layer.wfs_url,
		            srsName: componentParams.projection,
		            featureType: parts[1],
		            featurePrefix: parts[0],
		            featureNS: layer.featureNS,
		            format: new OpenLayers.Format.WFST.v1_0_0_Sortable({
		                    version: "1.0.0",
		                    featureType: parts[1],
		                    featureNS: layer.featureNS,
		                    featurePrefix: parts[0],
		                    srsName: componentParams.projection
		            })
				})
			});
			layer.reader = new GeoExt.data.FeatureReader({}, EasySDI_Map.data.recordize([
						{name: 'name', type: 'string', mapping:	layer.name_field_name},
						{name: 'id', type: 'string', mapping:	layer.id_field_name}
			]));
		}, this);
		this.layerGroups = layerGroups;
		// layers	is just	a	conveniently indexed version of	SData.localisationLayers.
		this.layers	=	layers;
		//com_easysdi_map : this condition allows SData.localisationLayers to be empty
		if(SData.localisationLayers[0])this.url = SData.localisationLayers[0].wfs_url;
		EasySDI_Map.PlaceTreeLoader.superclass.constructor.call(this);
	},

	setLayerGroupID	:	function(id) {
		this.currentLayerGroup = parseInt(id);
	},

	setRootFilter	:	function(value){
		this.currentRootFilter = value;
	},

	requestData	:	function(node, cb){
		var	index	=	node.isRoot	?	0	:	node.attributes.layerLevel;
		if(this.fireEvent("beforeload",	this,	node,	cb)	!==	false){
			if ((!node.isRoot	|| this.currentRootFilter	!==	false) &&
					(this.currentLayerGroup	!==	false) &&	typeof this.layers['id'	+	this.currentLayerGroup].proxy	!= "undefined")	{
				var	options	=	{node:node,	cb:cb};
				var	layer=this.layers['id' + this.currentLayerGroup];
				if (node.isRoot){
					options.filter = new OpenLayers.Filter.Comparison({
								type:	OpenLayers.Filter.Comparison.LIKE,
								property:	layer.name_field_name,
								value: this.currentRootFilter,
								matchCase: false
					});
				}	else {
					var	subLayer = this.layers['id'+layer.child];
					if (subLayer.extract_id_from_fid)	{
						// Need	to reconstitute	a	fid	-	take the second	part of	the	feature	type name	as the fid prefix
						var	id = layer.feature_type_name.split(':')[1] + '.' + node.attributes.record.data.id;
						options.filter = new OpenLayers.Filter.FeatureId({
							fids:	[id]
						});
					}	else {
						options.filter = new OpenLayers.Filter.Comparison({
							type:	OpenLayers.Filter.Comparison.EQUAL_TO,
							property:	subLayer.parent_fk_field_name,
							value: node.attributes.record.data.id
						});
					}
				}
				layer.proxy.load({}, layer.reader, this.addNodes,	this,	options);
				return;
			}
		}
		// if	the	load is	cancelled, make	sure we	notify
		// the node	that we	are	done
		if(typeof	cb ==	"function"){
			cb();
		}
	},

	addNodes : function(o, options,	success){
		var	layer, newLevel, node	=	options.node,	i;

		layer	=	this.layers['id' + this.currentLayerGroup];
		if (node.isRoot) {
			newLevel=1;
		}	else {
			// Walk	down the layer hierarchy to	the	current	level
			for	(i=0;	i<node.attributes.layerLevel;	i++) {
				layer	=	this.layers['id' + layer.child];
			}
			newLevel = node.attributes.layerLevel+1;
		}
		for(i	=	0; i < o.records.length; i++)	{
			if (layer.extract_id_from_fid) {
				o.records[i].set(layer.id_field_name,	o.records[i].data.fid.split('.')[1]);
			}
			var	n	=	this.createNode(o.records[i],	newLevel,	layer);
			if(n)	{
				node.beginUpdate();
				node.appendChild(n);
				node.endUpdate();
			}
		}

		options.cb(this, node);
	},

	createNode : function(record,	level, layer)	{
		var	node;
		if (typeof layer.child ==	"undefined") {
			// layer has no	children,	so just	a	normal tree	node
			node = new Ext.tree.TreeNode({text:	record.data.name,	record:	record,	layerLevel:	level});
		}	else {
			node = new Ext.tree.AsyncTreeNode({text: record.data.name, record: record, layerLevel: level});
		}
		return node;
	}

});


EasySDI_Map.PlaceFilterPanel = Ext.extend(EasySDI_Map.BaseFilterPanel,
{
	// A list	of the layers	created	by this	panel, so	we can clean up.
	mapLayers: [],

	// total area	of the current selection
	selectionArea: 0,

	// which type	of selection mode	are	we in	(existing|new)
	mode:	null,

	_defaults	:	{
		title	:	EasySDI_Map.lang.getLocal('FP_FP_TITLE'),
		layout : 'form'
	},

	constructor	:	function(config)
	{
		config = Ext.merge({}, this._defaults, config);

		// build a convenient	list of	layers by	index
		this.layers	=	[];
		Ext.each(SData.localisationLayers, function(layer) {
			this.layers['id' + layer.id]=layer;
		}, this);

		// Now make	sure the parent	layers have	their	children in	the	title
		Ext.each(SData.localisationLayers, function(layer) {
			if (typeof layer.parent_id!="undefined"	&& typeof	layer.updatedParentTitle=="undefined"	&&
					layer.parent_id>-1)	{
				this.layers['id' + layer.parent_id].title	+= '->'	+	layer.title;
				this.layers['id' + layer.parent_id].child	=	layer.id;
				// Flag	the	layer	so we	don't	redo the title generation	in future
				layer.updatedParentTitle = true;
			}
		}, this);

		// Now build the list	of layers	for	the	combo.
		var	layerData	=	[];
		Ext.each(SData.localisationLayers, function(layer) {
			if (typeof layer.parent_id=="undefined"	|| layer.parent_id==-1)	{
				layerData.push([this.layers['id' + layer.id].title,	layer.id]);
			}
		}, this);

		this.targetDataStore = new Ext.data.SimpleStore({
				fields:	['display'],
				data:	[]
		});	//this is	made "this." so	it can be	accessed for the chosen	list

		var	loader = new EasySDI_Map.PlaceTreeLoader(this.layers,	layerData);
		this.loader	=	loader;

		// create	individual components

		var	layerComboBox	=	new	Ext.form.ComboBox({
				fieldLabel:	EasySDI_Map.lang.getLocal("FP_FP_EXISTING_LAYER"),
				store: new Ext.data.SimpleStore({
					 fields: ['layername', 'id'],
					 data: layerData
			 }),
			 emptyText:	EasySDI_Map.lang.getLocal("FP_FP_EMPTY_LAYER"),
			 displayField: 'layername',
			 valueField: 'id',
			 typeAhead:	true,
			 triggerAction:	'all',
			 mode: 'local',
			 width:	componentParams.PlaceSearch.dropDownWidth,
			 minListWidth: componentParams.PlaceSearch.dropDownWidth
		});
		this.layerComboBox = layerComboBox;

		var	searchBox	=	new	EasySDI_Map.triggerTextBox({
				fieldLabel:	EasySDI_Map.lang.getLocal('Search'),
				width: componentParams.PlaceSearch.dropDownWidth,
				disabled:	true,
				minChars:	componentParams.PlaceSearch.minChars,
				value: '',
				handler: function(value){
					this.loader.setRootFilter(value+"*");
					this.treePanel.getRootNode().reload();
				},
				handlerScope:	this
		});

		// now the item	selector panel
		var	treePanel	=	new	Ext.tree.TreePanel({
					columnWidth: 0.5,
					width:0, //	needed otherwise the width isn't calculated	correctly
					lines: true,
					rootVisible: false,
					root:	new	Ext.tree.AsyncTreeNode({text:	'Root	-	hidden'}), //	this should	not	be seen	so no	need to	internationalise
					selModel:	new	Ext.tree.MultiSelectionModel({}),
					loader:	loader,
					height:	componentParams.PlaceSearch.itemSelector.height
		});
		this.treePanel = treePanel;

		var	buttonPanel	=	new	Ext.Panel({
					isFormField: true,
					border:	false,
					height:	componentParams.PlaceSearch.itemSelector.height,
					width: 42,
					items	:	[{
							xtype: 'button',
							iconCls: "rightArrow",
							style: "display: inline-block; margin: 8px;",
							tooltip: EasySDI_Map.lang.getLocal('FP_ADD_ITEMS_TOOLTIP'),
							handler: function	() {
								var	selectedNodes	=	treePanel.selModel.getSelectedNodes();
								for(var	i	=	0,l	=	selectedNodes.length;	i	<	l; i++){
                  this._addExistingFeatureToSelection(selectedNodes[i].attributes.record.data.feature,
                      selectedNodes[i].attributes.record.data.id,
                      selectedNodes[i].attributes.record.data.name+' ('+this.layerComboBox.getRawValue()+')');
								}
								treePanel.selModel.clearSelections(false);
							},
							scope: this
						},{
							xtype: 'button',
							iconCls: "leftArrow",
							style: "display: inline-block; margin: 8px;",
							tooltip: EasySDI_Map.lang.getLocal('FP_REMOVE_ITEMS_TOOLTIP'),
							handler: function	() {
								this.targetGrid.selModel.each(function (record)	{
									this.targetGrid.store.remove(record);
									this._updateSelArea(this.selectionArea - record.data.feature.geometry.getArea());
                  // need to scan features as just using a straight removeFeatures doesn't work
                  for(var i = this.selectionLayer.features.length-1; i>=0; i--){
                      if (this.selectionLayer.features[i].attributes.isExistingPlace === true) {
                          if (this.selectionLayer.features[i].fid == record.data.feature.fid) {
                              this.selectionLayer.destroyFeatures(this.selectionLayer.features[i]);
                          }
                      }
                  }
								}, this);
								this.targetGrid.selModel.clearSelections(false);
                Ext.each(this.mapLayers, function (mapLayer){
                    mapLayer.redraw();
                }, this);
                this.selectionLayer.redraw();
							},
							scope: this
						},{
							xtype: 'button',
							iconCls: "clearSearchBtn",
							style: "display: inline-block; margin: 8px;",
							tooltip: EasySDI_Map.lang.getLocal('FP_REMOVE_ALL_ITEMS_TOOLTIP'),
							handler: this._clearSelection,
							scope: this
					}]
		});

		var	columnModel	=	new	Ext.grid.ColumnModel([{
				columnWidth: 50,
				sortable:	true,
				dataIndex: 'name',
				id:	'name'
		}]);
		this.targetGrid	=	new	Ext.grid.GridPanel({
				height:	componentParams.PlaceSearch.itemSelector.height,
				columnWidth: 0.5,
				cls: 'grid-no-header',
				width:0,	// needed	otherwise	the	width	isn't	calculated correctly
				store: this.targetDataStore,
				colModel:	columnModel,
				autoExpandColumn : 'name',
				selModel:	new	Ext.grid.RowSelectionModel({})
	 });
	 this.targetGrid.selModel.grid=this.targetGrid;

		var	itemSelector = new Ext.Panel({
				 layout	:	'column',
				 border: false,
				 height: componentParams.PlaceSearch.itemSelector.height,
				 items : [
						 this.treePanel,
						 buttonPanel,
						 this.targetGrid
					]
		});
		this.itemSelector	=	itemSelector;

		// Set layer box selection event:

		layerComboBox.on('select', function()	{
				this.loader.setLayerGroupID(this.layerComboBox.value);
				this.loader.setRootFilter(false);
				searchBox.enable();
				searchBox.setValue("");
				this.treePanel.getRootNode().reload();
				this._removePlaceSelectionLayers();
				var	layer	=	this.layers['id'+this.layerComboBox.value];
				this._addLayerGroupToMap(layer);
		}, this);

		var	filterPlace	=	new	Ext.form.FormPanel({
			border:	false,
			autoWidth: true,
			height:	202,
			items: [layerComboBox,
							searchBox,
								itemSelector]});


		var	drawmethod = new Ext.data.SimpleStore({
			fields:	['item'],
			data : [
							[EasySDI_Map.lang.getLocal("FP_DRAW_POLYGON")],
							[EasySDI_Map.lang.getLocal("FP_DRAW_BOX")],
							[EasySDI_Map.lang.getLocal("FP_DRAW_LINE")],
							[EasySDI_Map.lang.getLocal("FP_DRAW_POINT")]]
		});

		// Create	a	vector layer to	capture	polygons onto, and a control to	do the drawing
		this.selectionLayer	=	new	OpenLayers.Layer.Vector("Selection Layer", {displayInLayerSwitcher:	false});
    var options = {drawFeature: this._addDrawnGeomToSelection.createDelegate(this)};
		this.polygonControl	=	new	OpenLayers.Control.DrawFeature(this.selectionLayer,	OpenLayers.Handler.Polygon,	options);
		this.pointControl	=	new	OpenLayers.Control.DrawFeature(this.selectionLayer,	OpenLayers.Handler.Point,	options);
		this.boxControl	=	new	OpenLayers.Control.DrawFeature(this.selectionLayer,	OpenLayers.Handler.RegularPolygon, options);
		this.boxControl.handler.setOptions({irregular:true});
		this.pathControl = new OpenLayers.Control.DrawFeature(this.selectionLayer, OpenLayers.Handler.Path,	options);

		config.mapPanel.map.addLayers([this.selectionLayer]);
		config.mapPanel.map.addControl(this.polygonControl);
		config.mapPanel.map.addControl(this.pointControl);
		config.mapPanel.map.addControl(this.boxControl);
		config.mapPanel.map.addControl(this.pathControl);

		var	drawMethodCombo	=	new	Ext.form.ComboBox({
				fieldLabel:	'<span qtip="'+EasySDI_Map.lang.getLocal('00000004')+'"	class="helptip">?</span>'+EasySDI_Map.lang.getLocal('00000005'),
				store: drawmethod,
				emptyText: EasySDI_Map.lang.getLocal("FP_DRAW_SELECT"),
				displayField:	'item',
				triggerAction: 'all',
				mode:	'local',
				width: componentParams.PlaceSearch.dropDownWidth,
				minListWidth:	componentParams.PlaceSearch.dropDownWidth
		});
		this.drawMethodCombo = drawMethodCombo;

		drawMethodCombo.on('select', function()	{
			this.polygonControl.deactivate();
			this.boxControl.deactivate();
			this.pathControl.deactivate();
			this.pointControl.deactivate();
				if (this.drawMethodCombo.value ==	EasySDI_Map.lang.getLocal("FP_DRAW_POLYGON"))	{
					this.polygonControl.activate();
				}	else if	(this.drawMethodCombo.value	== EasySDI_Map.lang.getLocal("FP_DRAW_BOX")) {
					this.boxControl.activate();
				}	else if	(this.drawMethodCombo.value	== EasySDI_Map.lang.getLocal("FP_DRAW_LINE"))	{
					this.pathControl.activate();
				}	else if	(this.drawMethodCombo.value	== EasySDI_Map.lang.getLocal("FP_DRAW_POINT")) {
					this.pointControl.activate();
				}
		}, this);

		var	fieldSet1	=	new	Ext.form.FieldSet({
				title: EasySDI_Map.lang.getLocal("FP_FP_EXISTING_TITLE"),
				checkboxToggle:	true,
				autoHeight:	true,
				collapsed: true,
				items: [
					filterPlace,
					{
						html:	EasySDI_Map.lang.getLocal('FP_FP_CLICK_SHAPE'),
						border:	false
					}
				]
		});

		var	fieldSet2	=	new	Ext.form.FieldSet({
				title: EasySDI_Map.lang.getLocal('00000002'),
				checkboxToggle:true,
				autoHeight:	true,
				collapsed: true,
				items: [{
					html:	EasySDI_Map.lang.getLocal('00000003')	+	'<br/><br/>',
					border:	false
				},
				drawMethodCombo,
				{
						xtype: 'form',
						id:	'upload-form',
						autoHeight:	true,
						fileUpload:	true,
						border:	false,
						layout:	'auto',
						items: [{
								xtype: 'label',
								html:	'<span qtip="'+EasySDI_Map.lang.getLocal('00000007')+'"	class="helptip">?</span>'+EasySDI_Map.lang.getLocal('00000008')
						},{
								xtype: 'textfield',
								label: '<span	qtip="'+EasySDI_Map.lang.getLocal('00000007')+'" class="helptip">?</span>'+EasySDI_Map.lang.getLocal('00000008'),
								inputType: 'file'	// NB	its	not	possible to	apply	an accept="ZIP"	filter through JS
						},{
								xtype: 'button',
								text:	'Upload',
								style: 'margin-left: 2px;',
								handler: this._uploadShp,
								scope: this
						}]
				}]
		});

		fieldSet1.on('beforeexpand', function()	{
			this.mode	=	"existing";
			fieldSet2.collapse();
			this.polygonControl.deactivate();
			this.boxControl.deactivate();
			this.pathControl.deactivate();
			this.pointControl.deactivate();
    this._clearSelection();
    if (this.layerComboBox.value) {
      layerComboBox.fireEvent('select');
    }
		}, this);
		fieldSet2.on('beforeexpand', function()	{
			this.mode	=	"new";
			fieldSet1.collapse();
			drawMethodCombo.fireEvent('select');
    this._clearSelection();
    this._removePlaceSelectionLayers();
		}, this);

    var areaDisplay = new Ext.Panel({
        layout : 'column',
        border: false,
        items : [{xtype: 'label',
                  width: 100,
                  html: '<span qtip="<b>'+EasySDI_Map.lang.getLocal('FP_FP_SELECTION')+'</b><br/>'+EasySDI_Map.lang.getLocal("FP_FP_SELECTION_HELP")+'" class="helptip">?</span>'+EasySDI_Map.lang.getLocal("FP_FP_SELECTION")+':'
                },{
                  xtype: 'textfield',
				id:	'selection',
				readOnly:	true,
				value: '0'
                },{
                  xtype: 'button',
                  text: EasySDI_Map.lang.getLocal('FP_FP_RESET_BUTTON'),
                  style: "display: inline-block; margin-left: 20px; margin-bottom: 8px;",
                  tooltip: EasySDI_Map.lang.getLocal('FP_FP_RESET_BUTTON_HELP'),
                  handler: this._clearSelection,
                  getSize: function () { return this.el.getSize();}, // hack so we can use a column layout
                  scope: this
                }]
    });

    var bufferDisplay = new Ext.Panel({
        layout : 'column',
        border: false,
        items : [{xtype: 'label',
                  width: 100,
                  html:  '<span qtip="<b>'+EasySDI_Map.lang.getLocal('FP_FP_BUFFER')+'</b><br/>'+EasySDI_Map.lang.getLocal("FP_FP_BUFFER_HELP")+'" class="helptip">?</span>'+EasySDI_Map.lang.getLocal("FP_FP_BUFFER")
                },{
                  xtype: 'textfield',
				id:	'buffer',
                  value: '0'
                },{
                  xtype: 'button',
                  text: EasySDI_Map.lang.getLocal('FP_FP_BUFFER_BUTTON'),
                  style: "display: inline-block; margin-left: 20px;",
                  tooltip: EasySDI_Map.lang.getLocal('FP_FP_BUFFER_BUTTON_HELP'),
                  getSize: function () { return this.el.getSize();}, // hack so we can use a column layout
                  handler: this._updateBuffer,
                  scope: this
                }]
    });
    config.items =  [
      areaDisplay,
      bufferDisplay,
			fieldSet1,
			fieldSet2
		];
		EasySDI_Map.PlaceFilterPanel.superclass.constructor.apply(this,	[config]);
	},

	/**
	*	Encode the place filter	to an	object that	can	be saved.	This just	encodes	the	GML	into the object	-	not	saving
	*	the	list of	places in	case their boundaries	change.
	*/
	encode:	function() {
		var	features=[];
		for	(var i=0;	i<this.selectionLayer.features.length; i++)	{
			if (this.selectionLayer.features[i].attributes.isOrigPolygon)	{
				features.push(this.selectionLayer.features[i]);
			}
		}
		var	format=new OpenLayers.Format.GML.v2({featureName:	'user',	featureType: 'user',
				featureNS: componentParams.pubFeatureNS, featurePrefix:	componentParams.pubFeaturePrefix});
		return {
			buffer:	Ext.getCmp('buffer').getRawValue(),
			gml: format.write(features)
		};
	},

	/**
	*	Decode the place filter	from a saved object.
	*/
	decode:	function(object) {
		this._clearSelection();
    this.mode="new";
		var	buffer=Ext.getCmp('buffer');
		if (buffer.rendered) {
			Ext.getCmp('buffer').setRawValue(object.buffer);
		}	else {
			Ext.getCmp('buffer').setValue(object.buffer);
		}
		var	format=new OpenLayers.Format.GML.v2({featureName:	'user',	featureType: 'user',
				featureNS: componentParams.pubFeatureNS, featurePrefix:	componentParams.pubFeaturePrefix});
		var	features=format.read(object.gml);
		Ext.each(features, function(feature) {
        if(feature.attributes.isExistingPlace == "false"){
            this._addDrawnGeomToSelection(feature.geometry);
        } else {
            feature.fid = feature.attributes.fid;
          this._addExistingFeatureToSelection(feature,
              feature.attributes.reference,
              feature.attributes.name);
            this.mode="existing";
        }
		}, this);
	},

	/**
	*	Clear	this filter	back to	it's default state.
	*/
	clear: function()	{
		this._clearSelection();
	},

	/**
	*	Issue	an Ajax	request	to the shp2Gml service to	take a zipped	uploaded shp file	and	convert	it to
	*	GML.
	*/
	_uploadShp:	function() {
		Ext.Ajax.request({
			url: componentParams.shp2GmlUrl,
			form:	Ext.getCmp('upload-form').getForm().id,
			success: function(response,	opts)	{
				var	parser = new OpenLayers.Format.GML();
				var	features = parser.read(response.responseXML);
				for	(var i=0;	i<features.length; i++)	{
          this._addDrawnGeomToSelection(features[i].geometry);
				}
			},
			scope: this
		});
	},

	/**
	 * Adds	a	layer	and	all	it's children	to the map so	that the user	can	select the items.
	 */
	_addLayerGroupToMap: function(layer) {
    // double check not already displayed : occurs if pressing 'build' multiple times
    for(var i=0; i<this.mapLayers.length; i++){
      if(this.mapLayers[i].name == layer.title){
        return;
		}
			}
    var style = OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style["default"]);
    var wmsOptions = {
	    LAYERS: layer.feature_type_name,
	    SERVICE: 'WMS',
	    STYLES: '',
	    SRS: componentParams.projection,
	    TRANSPARENT: true,
	    FORMAT: 'image/png',
	    VERSION: componentParams.pubWmsVersion
	  };
	  var mapLayer = new OpenLayers.Layer.WMS(layer.title,
	    layer.wms_url,
	    wmsOptions,
	  { isBaseLayer: false }
		);
		this.mapPanel.map.addLayer(mapLayer);
		this.mapLayers.push(mapLayer);
	  var parts = layer.feature_type_name.split(":");
	  var clickFeatureCtrl = new OpenLayers.Control.GetFeature({
	        protocol: new OpenLayers.Protocol.WFS({
	        version: componentParams.pubWfsVersion,
	        url: layer.wfs_url,
	        srsName: componentParams.projection,
	        featureType: 'rec_sect',
	        featurePrefix: 'eai',
	        featureNS: 'http://www.easysdi.org/eai'
	    }),
	    layer: layer,
	    toggle: true
	    });
	  clickFeatureCtrl.events.register("featureselected", this, this._clickExistingPlace);
	  clickFeatureCtrl.events.register("featureunselected", this, this._unclickExistingPlace);

		// Add a control to	detect feature clicks	on the layer
		this.mapPanel.map.addControl(clickFeatureCtrl);
		mapLayer.clickFeatureCtrl	=	clickFeatureCtrl;
		clickFeatureCtrl.activate();
		if (typeof layer.child !=	"undefined") {
			// recurse to	add	the	children
			this._addLayerGroupToMap(this.layers['id'+layer.child]);
		}
	},

	/**
	 * Hide	the	place	selection	stuff	when the filter	builder	is hidden
	 */
	doHide:	function() {
		// Drawing layer hidden
		this.selectionLayer.setVisibility(false);
		this._removePlaceSelectionLayers();
	},

	/**
	 * Hide	the	place	selection	stuff	when the filter	builder	is hidden
	 */
	doShow:	function() {
		// Drawing layer displayed
		this.selectionLayer.setVisibility(true);
		if (this.layerComboBox.value)	{
			this._addLayerGroupToMap(this.layers['id'+this.layerComboBox.value]);
		}
	},

	 /**
	 * Hide	layers that	are	for	selection	of places
	 */
	_removePlaceSelectionLayers: function()	{
		for	(var i=0;	i<this.mapLayers.length; i++)	{
			this.mapPanel.map.removeControl(this.mapLayers[i].clickFeatureCtrl);
			this.mapLayers[i].clickFeatureCtrl = null;
			this.mapPanel.map.removeLayer(this.mapLayers[i]);
		}
		this.mapLayers = [];
	},

	/**
	 * Adds	a	drawn, uploaded	or selected	geometry to	the	selection	layer	for	the	filter.
	 * Also	adds a second	buffer polygon if	required.
   * Note that the second buffer polygon is only added if the object is not an existing place - this
   * is due to the restriction on existing place boundaries being too complex, so the filter
   * is set using a vectorised field, rather than the geometry itself. This does not allow
   * for the addition of an arbitrary buffer around the geometry.
	 */
  _addGeomToSelection: function(geometry, fid, reference, name) {
  var isExistingPlace = !!fid;
    var buffer = parseInt(Ext.getCmp('buffer').getRawValue());
		// create	a	style	for	the	outer	query	polygon
		var	bufferStyle	=	this._getStyle(true);
		var	innerStyle = this._getStyle(false);
    var style = (buffer <= 0 || (componentParams.useVectorisedLocations && isExistingPlace)) ? bufferStyle : innerStyle;
		if (geometry.CLASS_NAME=='OpenLayers.Bounds')	{
			// Bounding	boxed	need to	be converted to	true geometries
			geometry=geometry.toGeometry();
		}
		// Create	the	polygon	as drawn
		var	feature	=	new	OpenLayers.Feature.Vector(geometry,	{},	style);
		feature.attributes.isOrigPolygon = true;
    feature.attributes.isExistingPlace = isExistingPlace;
    feature.fid = fid;
    feature.attributes.fid = fid;
    feature.attributes.reference = reference;
    feature.attributes.name = name;

    this.selectionLayer.addFeatures([feature]);
    this.selectionLayer.setVisibility(true);
    var area;
    // Add the buffer polygon if required
    if (buffer>0 && (!componentParams.useVectorisedLocations || !isExistingPlace)) {
			buffer = new OpenLayers.Feature.Vector(
				this._buffer(geometry, buffer),
				{},
				bufferStyle
			);
			this.selectionLayer.addFeatures([buffer]);
			area=buffer.geometry.getArea() ||	0;
		}	else {
			area = geometry.getArea()	|| 0;
		}

		// Record	the	feature	area in	the	selection	summary	box
		if (typeof area!="undefined")	{
			this._updateSelArea(this.selectionArea + area);
		}
	this.selectionLayer.div.style.display = "block";
    this.selectionLayer.div.childNodes[0].style.display = "block";
    this.selectionLayer.div.childNodes[0].childNodes[0].style.display = "block";
		
	},

	/**
   * Adds a drawn or uploaded geometry to the selection layer for the filter.
   */
  _addDrawnGeomToSelection: function(geometry) {
    this._addGeomToSelection(geometry, false, false, false); // no fid, vector id or name
  },

  /**
   * Adds a drawn or uploaded geometry to the selection layer for the filter.
   */
  _addExistingFeatureToSelection: function(feature, id, name) {
      // may have already been added via clicking the map so compare fids
      for(var j = 0; j < this.targetDataStore.data.items.length; j++){
          if (this.targetDataStore.data.items[j].data.feature.fid == feature.fid) {
              return;
          }
      }
      var newData = {name: name,
                     id: id,
                     feature: feature};
      this.targetDataStore.add([new Ext.data.Record(newData)]);
      // Add the feature to the map selection layer
      this._addGeomToSelection(feature.geometry,
                  feature.fid, // needed to generated vectorised location field and for reference.
                  id, // used when filtering using vectorised locations.
                  name); // name used when populating after 'Build'
  },

  /**
   * Adds a drawn or uploaded geometry to the selection layer for the filter.
   */
  _removeExistingFeatureFromSelection: function(feature) {
    var j;
  for (j=0; j<this.selectionLayer.features.length; j++) {
    if (this.selectionLayer.features[j].fid==feature.fid) {
      this.selectionLayer.removeFeatures([this.selectionLayer.features[j]]);
    break;
    }
  }
  for(j = 0; j < this.targetDataStore.data.items.length; j++){
      if (this.targetDataStore.data.items[j].data.feature.fid == feature.fid) {
        this.targetDataStore.removeAt(j);
    break; // from loop
      }
    }
  this._updateSelArea(this.selectionArea - feature.geometry.getArea());
  },

  /**
	 * Clicking	an existing	place	adds it	to the list	of polygons	to query on.
	 */
  _clickExistingPlace: function(e) {
    this._addExistingFeatureToSelection(e.feature,
                     e.feature.attributes[e.object.layer.id_field_name],
                     e.feature.attributes[e.object.layer.name_field_name]+' ('+e.object.layer.title+')');
  },

  /**
   * Second click on an existing place removes it from the filter.
   */
  _unclickExistingPlace: function(e) {
    this._removeExistingFeatureFromSelection(e.feature);
	},

	/**
	 * Update	the	selection	area text	box.
	 *
	 * param newArea - new total area. If	not	defined, works it	out.
	 */
	_updateSelArea:	function(newArea)	{
		if (newArea	!==	undefined) {
			this.selectionArea = newArea;
		}	else {

		}
		var	selBox = Ext.getCmp('selection');
		if (newArea<10000) {
			selBox.setValue(Math.round(newArea)	+	'm2');
		}	else {
			newArea	=	Math.round(newArea / 10000)	/	100; //	2	decimal	places
			selBox.setValue(newArea	+	'km2');
		}
		this.fireEvent("change", this);
		// Event to	set	the	search description
		this.fireEvent("changedesc", this, this.getDescription());
	},

	/**
	*	Return the plain text	description	of the filters active	in the place filter	tab.
	*/
	getDescription:	function() {
		var	desc,	selBox = Ext.getCmp('selection');
    if (this.mode=="new" && selBox.getValue()>0) {
			desc = EasySDI_Map.lang.getLocal('FP_IN_SELECTED_AREA_').replace('*',	selBox.getValue());
		}	else {
			// existing	polygons so	display	place	names
			var	places=[];
			Ext.each(this.targetGrid.store.data.items, function(item)	{
				places.push(item.data.name);
			}, this);
      for (var i=0; i<this.selectionLayer.features.length; i++) {
          if (!this.selectionLayer.features[i].attributes.isExistingPlace) {
              places.push(EasySDI_Map.lang.getLocal('FP_UNNAMED_AREA').replace('*', selBox.getValue()));
              break;
          }
      }
			if (places.length==1)	{
				desc = EasySDI_Map.lang.getLocal('FP_IN_').replace('*',	places[0]);
			}	else if	(places.length>1)	{
				desc = EasySDI_Map.lang.getLocal('FP_IN_ONE_OF_').replace('*', places.join(',	'));
			}	else {
				desc = null;
			}
		}
		return desc;
	},

	/**
	 * Create	a	buffered version of	an existing	geometry.
	 */
	_buffer: function(geom,	dist)	{
		if (geom.CLASS_NAME=="OpenLayers.Geometry.Polygon")	{
			// Make	an assumption	that this	is called	when a single	object has been	drawn
			// no	holes
			var	inner=geom.components[0].clone();
			// make	sure that	the	polygon	is anticlockwise
			if(this._isClockwise(inner)) {
					inner.components.reverse();
			}
			return this._bufferPolygon(inner,	dist);
		}	else if	(geom.CLASS_NAME=="OpenLayers.Geometry.Rectangle") {
			alert('Rectangle to	do');
		}	else if	(geom.CLASS_NAME=="OpenLayers.Geometry.LineString")	{
			// create	ring components	from line	components
			// done	by retracing the line	backwards	and	then adding	to original
			var	comp = geom.components.slice().reverse();
			comp.shift();
			comp = geom.components.concat(comp);
			return this._bufferPolygon(new OpenLayers.Geometry.LinearRing(comp), dist);
		}	else if	(geom.CLASS_NAME=="OpenLayers.Geometry.Point") {
			return OpenLayers.Geometry.Polygon.createRegularPolygon(
				geom,	dist,	60
			);
		}
	},

	/**
	 * Method: _isClockwise
	 * Determine whether the linear	ring is	ordered	clockwise.
	 *
	 * Returns:
	 * {Boolean} Points	are	in clockwise order.
	 */
	_isClockwise:	function(geom) {
		var	sum	=	0;
		var	p0,	p1;
		for(var	i=0; i<geom.components.length	-	1; ++i)	{
				p0 = geom.components[i];
				p1 = geom.components[i+1];
				sum	+= (p0.x * p1.y) - (p0.y * p1.x);
		}
		return (sum	<	0);
	},

	_linesIntersect: function(P1,	P2,	P3,	P4,	intersection){
		// lines are defined by	m*P1+(1-m)*P2: each	has	2	coordinates: end up	with 2 equations with	2	variables:
		// m*P1x+(1-m)*P2x = n*P3x+(1-n)*P4x
		// m*P1y+(1-m)*P2y = n*P3y+(1-n)*P4y
		var	n	=	((P3.y - P1.y)*(P2.x - P1.x) - (P3.x - P1.x)*(P2.y - P1.y))	/	((P4.x - P3.x)*(P2.y - P1.y) - (P4.y - P3.y)*(P2.x-P1.x));
		var	m	=	(P3.x	+	n*(P4.x-P3.x)	-	P1.x)/(P2.x-P1.x);
		if (n	>	0	&& n < 1 &&	m	>	0	&& m < 1)
		{
			intersection.x = P1.x	+	m*(P2.x	-	P1.x);
			intersection.y = P1.y	+	m*(P2.y	-	P1.y);
//			alert(n+"	:	"+m+"	:	"+intersection.x+" : "+intersection.y);
			return true;
		}
		return false;
	},

	// If	we have	turned to	the	left,	then we	have an	external angle,	and	we
	// need	to add a series	of points	forming	a	curve	between
	// the last	line section and this	one.
	// Turning right will	mean an	internal angle,	no curve required.
	_bufferCurve:	function(p1, p2, p3, dist, points){
			// calculate cross product z axis	to work	out	if turned	left or	right
			var	dir	=	(p2.x	-	p1.x)*(p3.y	-	p2.y)	-	(p3.x	-	p2.x)*(p2.y	-	p1.y);
			var	degPerStep = 10;
			// if	turned left, calculate angle between them, and hence number	of steps
			if (dir	>= 0)	{
				var	lastlen	=	Math.sqrt((p2.x	-	p1.x)*(p2.x	-	p1.x)	+	(p2.y	-	p1.y)*(p2.y	-	p1.y));
				var	thislen	=	Math.sqrt((p3.x	-	p2.x)*(p3.x	-	p2.x)	+	(p3.y	-	p2.y)*(p3.y	-	p2.y));
				var	cosdot = ((p2.x	-	p1.x)*(p3.x	-	p2.x)	+	(p3.y	-	p2.y)*(p2.y	-	p1.y))/(thislen*lastlen);
				cosdot = (cosdot < -1	?	-1 : (cosdot > 1 ? 1 : cosdot)); //	any	minute calc	erros
				var	angle	=	Math.acos(cosdot);
				var	angleOffset	=	Math.atan2(p2.y	-	p1.y,	p2.x - p1.x) + (Math.PI	/	2);
				var	numpoints	=	Math.ceil(angle*180/(Math.PI*degPerStep));
        var dx, dy;
				for	(var k=1;	k	<	numpoints; k++){
						dx = -dist * Math.cos(angleOffset+ angle*k/numpoints);
						dy = -dist * Math.sin(angleOffset+ angle*k/numpoints);
						var	pc1	=	p2.clone();
						pc1.move(dx, dy);
						// check intersections with	these	line elements
						for	(var j=0;	j<points.length-1; ++j)	{
								var	intersection = pc1.clone();
								if(this._linesIntersect(points[points.length-1], pc1,	points[j], points[j+1],	intersection)) {
										points.length	=	j+1; //	remove all points	after	the	intersection
										points.push(intersection);
								}
						}
						points.push(pc1);
				}
			}
	},

	// This	makes	some assumptions:
	//	Lines	don't	cross
	//	No complex shapes
	// Start at	first	point	and	move down	the	line on	the	Right	hand side	(anticlockwise)
	_bufferPolygon:	function(geom, dist) {
		var	p0,	p1,	angle, dx, dy, intersection;
		var	points = [];
		if(geom.components.length	<	2) {
			return OpenLayers.Geometry.Polygon.createRegularPolygon(
					geom.components[0],	dist,	60
			);
		}

		for	(var i=0;	i<geom.components.length-1;	++i) {
			p0 = geom.components[i].clone();
			p1 = geom.components[i + 1].clone();
			if(p0.x	== p1.x	&& p0.y	== p1.y) {
				continue;
			}
			angle	=	Math.atan2(p1.y	-	p0.y,	p1.x - p0.x) + (Math.PI	/	2);
			dx = -dist * Math.cos(angle);
			dy = -dist * Math.sin(angle);
			p0.move(dx,	dy);
			if(i === 0)	{
				points.push(p0);
			}
			p1.move(dx,	dy);
			if ( i > 0)	{
				this._bufferCurve(geom.components[i-1],	geom.components[i],	geom.components[i+1],	dist,	points);
			}
			// now compare this	line segment with	existing lines
			for	(var j=0;	j<points.length-1; ++j)	{
				intersection = p1.clone();
				if(this._linesIntersect(p0,	p1,	points[j], points[j+1],	intersection)) {
					points.length	=	j+1; //	remove all points	after	the	intersection
					points.push(intersection);
				}
			}
			points.push(p1);
		}
		// fill	in final line/curve	and	check	intersections	for	closing	line
		// NB	we assume	that the last	point	and	the	first	are	the	same
		this._bufferCurve(geom.components[geom.components.length-2], geom.components[0], geom.components[1], dist, points);

		return new OpenLayers.Geometry.Polygon(new OpenLayers.Geometry.LinearRing(points));
	},


	/**
	 * Walk	through	the	features on	the	drawing	layer	and	refresh	the	buffer ones.
	 */
	_updateBuffer: function()	{
    var buffer = parseInt(Ext.getCmp('buffer').getRawValue());
		var	toRemove=[], toAdd = [], totalArea=0,	newVector;
		var	bufferStyle	=	this._getStyle(true);
		for	(var i=0;	i<this.selectionLayer.features.length; i++)	{
			if (this.selectionLayer.features[i].attributes.isOrigPolygon)	{
        if (buffer>0 && (!componentParams.useVectorisedLocations || !this.selectionLayer.features[i].attributes.isExistingPlace)) {
					newVector	=	new	OpenLayers.Feature.Vector(
						this._buffer(this.selectionLayer.features[i].geometry, buffer),
						{},
						bufferStyle
					);
					toAdd.push(newVector);
					totalArea	+= newVector.geometry.getArea();
				}	else {
					totalArea	+= this.selectionLayer.features[i].geometry.getArea();
				}
			}	else {
				toRemove.push(this.selectionLayer.features[i]);
			}
		}
    this.selectionLayer.removeFeatures(toRemove);
		this.selectionLayer.addFeatures(toAdd);
		this._updateSelArea(totalArea);
	},

	/**
	 * Clear the selection for the query polygons. Removes them	from the map layer,	and	also the
	 * selected	places grid.
	 */
	_clearSelection: function()	{
		this.targetGrid.selModel.clearSelections(false);
		this.targetGrid.store.each(function	(record) {
			this.targetGrid.store.remove(record);
		}, this);
		this.selectionLayer.destroyFeatures();
    // force a redraw of the other layer(s) in case
    Ext.each(this.mapLayers, function (mapLayer){
        mapLayer.redraw();
    }, this);
		this._updateSelArea(0);
	},

	/**
	 * Retrieves an	OpenLayers style object	for	drawn	polygons or	their	buffer zones.
	 */
	_getStyle: function(isBuffer)	{
		var	style	=	OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style["default"]);
		if (isBuffer)	{
      OpenLayers.Util.extend(style, {fillColor: '#0099ff', fillOpacity: 0.5});
		}	else {
      OpenLayers.Util.extend(style, {fillOpacity: 0.2});
		}
		return style;
	},

	/**
	 * Returns the Openlayers	filter objects that	represent	the	polygons created by	this tab.
	 */
	getFilters:	function() {
    var filters = [], buffer = parseInt(Ext.getCmp('buffer').getRawValue());
		var	geomName = SData.searchLayer.geometryName;
		// Do	we use the taxonomic activity	range	(buffered) version of	the	geom to	filter against?
		// This	is specific	to Recorder	Web	GIS	but	it will	degrade	gracefully if	the	control	is missing
    var control=Ext.getCmp('activity_range');
		if (control	&& control.checked)	{
			geomName +=	'_buffered';
		}
		for	(var i=0;	i<this.selectionLayer.features.length; i++)	{
			if (this.selectionLayer.features[i].attributes.isOrigPolygon)	{
          if (componentParams.useVectorisedLocations && this.selectionLayer.features[i].attributes.isExistingPlace) {
            filters.push(new OpenLayers.Filter.Comparison({
                type: OpenLayers.Filter.Comparison.LIKE,
                value: this.selectionLayer.features[i].attributes.reference,
                property: this.selectionLayer.features[i].fid.split('.')[0]+((control && control.checked) ? '_buffered' : '')+(user.loggedIn ? '_private' : '_public')+'_keylist'
              }));
          } else {
				filters.push(new OpenLayers.Filter.Spatial({
						type:	OpenLayers.Filter.Spatial.DWITHIN,
						value: this.selectionLayer.features[i].geometry,
						property:	geomName,
						distance:	buffer,
						distanceUnits: "m"
				}));
			}
		}
    }
		if (filters.length===0)	{
			return [];
		}	else if	(filters.length==1)	{
			return [filters[0]];
		}	else {
			// multiple	polygons,	so OR	them together
			return [new	OpenLayers.Filter.Logical({
				filters: filters,
				type:	OpenLayers.Filter.Logical.OR
			})];
		}
	}

});

/**
*	The	main advanced	filter panel class.
*/
EasySDI_Map.FilterPanel	=	Ext.extend(Ext.Panel,	{

	tabs:	[],

	descs: [],

	constructor: function(config)	{
		this.mapPanel	=	config.mapPanel;
		this.innerSearchBar	=	config.innerSearchBar;
		this._initTabs();
		// Now create	the	tab	strip
		this.filterTabs	=	new	Ext.TabPanel({
			defaults : {
				autoHeight:	true,
				autoScroll:	true,
				bodyStyle: 'padding:10px'
			},
		      listeners :{
		    	  beforetabchange :function(tp,  newTab, oldTab){
		    		 // console.log("beforetabchange");
		    		
		    		  //console.log("tab ="+newTab.el.id);
		    		//  console.log("-----");
		    		  var tabIds = tp.items.keys;
		    		  for(var i in tabIds)
		    		  {
		    		     
		    		     if(Ext.getCmp(tabIds[i])){
		    		    	 if(Ext.getCmp(tabIds[i]).el){
				    		     Ext.fly(Ext.getCmp(tabIds[i]).el).removeClass('x-tab-strip-active');
				    		     Ext.fly(Ext.getCmp(tabIds[i]).el).removeClass('x-hide-display');
				    		     Ext.fly(Ext.getCmp(tabIds[i]).el).addClass('x-hide-display');
		    		    	 }
		    		     }
		    		  }
		    		 
		    		  
		    		/*  if(oldTab){
		    		  if(oldTab.el){    			 
		    			  Ext.fly(oldTab.el).removeClass('x-tab-strip-active');
		    			  Ext.fly(oldTab.el).addClass('x-hide-display');


		    		  }}*/
		    		  
		    		  if(newTab){
		    		  if(newTab.el){    		
		    			this.newTabId =newTab.el.id;
		    		  }
		    		  }
		    		 
		    	  },
		    	  tabchange: function(tp, newTab){
		    		
		    		 if(this.newTabId){
		    			 //Ext.getCmp(this.newTabId).setVisible(1);
		    			 ;
		    			 
		   			     Ext.fly(Ext.getCmp(this.newTabId).el).removeClass('x-hide-display');
		   				 Ext.fly(Ext.getCmp(this.newTabId).el).addClass('x-tab-strip-active');
		    		

		    			
		    		 }
		    	  }
		      }    ,
			items	:	this.tabs
		});
		config.region="center";
		config.items = [
			this.filterTabs
		];
		this.searchDesc	=	new	Ext.form.TextArea({
			height:	34,
			width: 390,
			readOnly:	true
		});
		config.bbar	=	[
			new	Ext.Toolbar({
				width: 482,
				height:	40,
				border:	 false,
				items: [
					this.searchDesc,
					"->",
					{
            text: EasySDI_Map.lang.getLocal('FP_ADV_SEARCH_APPLY_FILTER'),
						handler: this._applyFilter,
						scope: this,
						cls: 'x-form-toolbar-standardButton'
					}
				]
			})
		];

		this.featureLabel	=	EasySDI_Map.lang.getLocal('FP_ANY_FEATURES');
		EasySDI_Map.FilterPanel.superclass.constructor.apply(this, arguments);

	},

	/**
	* Moved from rwgFilterPanel
	* TODO : see what has to be done here
	*/
	SetMode: function(mode) 
	{
		this.mode = filterPanelMode.none;
		this._activateFirstTab();
	},
	
	/*_activateFirstTab: function() 
	{
		// Set the first visible tab to active. Can't just check visible property as not yet rendered.
	 	var tabActive = false;
      	Ext.each(this.filterTabs.items.items, function(item) 
      	{
        	item = this.filterTabs.getComponent(item);
        	var el = this.filterTabs.getTabEl(item);
        	if (!tabActive && el && el.style.display != 'none') 
        	{
          		this.filterTabs.setActiveTab(item);
          		tabActive = true;
        	}
      	}, this);
     },*/
     _activateFirstTab: function() {
         // Set the first visible tab to active. Can't just check visible property as not yet rendered.
       	;
       	  var tabIds =  this.filterTabs.items.keys;
       
   		  for(var i in tabIds)
   		  {
   		     if(i==0){
   		    	 if(Ext.getCmp(tabIds[i]).el){
   	    		     Ext.fly(Ext.getCmp(tabIds[i]).el).removeClass('x-hide-display');
   	    		     Ext.fly(Ext.getCmp(tabIds[i]).el).addClass('x-tab-strip-active');
   		    	 }
   		     }
   		     else if (Ext.getCmp(tabIds[i])){
   		    	 if(Ext.getCmp(tabIds[i]).el){
   	    		     Ext.fly(Ext.getCmp(tabIds[i]).el).removeClass('x-tab-strip-active');
   	    		     Ext.fly(Ext.getCmp(tabIds[i]).el).removeClass('x-hide-display');
   	    		     Ext.fly(Ext.getCmp(tabIds[i]).el).addClass('x-hide-display');
   		    	 }
   		     }
   		     else{}
   		  }
   		 
       },
       
       activateSearchtab : function() {
     
    	 	var setFirstTabVisible = false;
        	var visibleTabs =  this.tabs;
        	for(var i in visibleTabs)
        	{
        		
        		if(i){
        			if(visibleTabs[i].tabEl){
    	    			if((visibleTabs[i].tabEl.style.display !="none") && !setFirstTabVisible){
    	    				visibleTabs[i].setVisible(true);
    	    				visibleTabs[i].show();
    	    				setFirstTabVisible= true;
    	
    	    			}else{
    	
    	    				visibleTabs[i].setVisible(false);
    	    				visibleTabs[i].hide();
    	    				Ext.fly(visibleTabs[i].el).removeClass('x-tab-strip-active');    				
    	    				Ext.fly(visibleTabs[i].el).addClass('x-hide-display');
    	    				Ext.fly(visibleTabs[i].tabEl).removeClass('x-tab-strip-active');
    	    			}
        			}
        		}   	
        		

        	}

       },
	
	/**
	 * Updates the filter	to match the filter	definition in	the	search bar.	Triggerable	so it	can	be called
	 * when	everything is	ready	or after load.
	 */
	updateFilter:	function() {
		//com_easysdi_map : If no tab exists (eg : no right defined for the current role), 
		//exit from this function to avoid unexpected error in the display of the filter panel
		if(this.tabs.length === 0 ) {return;}
		
		var	filterData =	Ext.util.JSON.decode(this.innerSearchBar.filterData);
		Ext.each(this.filterTabs.items.items,	function(tab,	i) {
      var item = this.filterTabs.getComponent(tab);
      	var	el = this.filterTabs.getTabEl(item);
			// Check the element to	see	if it	will be	made visible - too early to	use	isVisible!
			if (el.style.display==="") {
				if (typeof filterData!=="undefined"	&& typeof	filterData[tab.title]!=="undefined") {
					if (typeof tab.decode==="undefined") {
						alert(tab.title	+	'	has	no decode	method');
					}	else {
						tab.decode(filterData[tab.title]);
					}
				}	else {
					tab.clear();
				}
      }
    }, this);
    // Now build the initial description - separated out as decoding complicates things
    this.descs = [];
    Ext.each(this.filterTabs.items.items, function(tab, i) {
        var item = this.filterTabs.getComponent(tab);
        var el = this.filterTabs.getTabEl(item);
        if (el.style.display==="") {
				var	desc=tab.getDescription();
				if (desc!==""	&& desc!==null)	{
					this.descs.push({id: tab.id, desc: desc});
				}
			}
		}, this);
		this._updateDescription();
	},

	/**
	 * Function	allowing the child panels	to update	the	parent search	description.
	 */
	_setSearchDesc:	function(tab,	desc)	{
		var	exists=false;
		for	(var i=0;	i<this.descs.length; i++)	{
			if (this.descs[i].id ==	tab.id)	{
				if (desc===""	|| desc===null)	{
					// remove	the	description
					this.descs.splice(i, 1);
				}	else {
					this.descs[i].desc=desc;
				}
				exists = true;
			}
		}
		if (!exists	&& desc!=="" &&	desc!==null) { this.descs.push({id:	tab.id,	desc:	desc});	}
		this._updateDescription();
	},

	/**
	 * Use the descriptions	already	obtained from	each tab to	update the label.
	 */
	_updateDescription:	function() {
		var	fullDesc='';
		Ext.each(this.descs, function(item,	i) {
			fullDesc +=	item.desc;
			if (i<this.descs.length-1) { fullDesc	+= ',	'; }
		}, this);

		this.searchDesc.setValue(this.featureLabel + ' ' + fullDesc);
	},

	_initTabs: function()	{
		if (componentParams.authorisedTo.ADV_SEARCH_MISC)	{
			this.otherAttrsTab = new EasySDI_Map.MiscAttrPanel({
				title: EasySDI_Map.lang.getLocal('FP_MA_TITLE')
			});
			this.otherAttrsTab.on("changedesc",	this._setSearchDesc, this);
			this.tabs.push(this.otherAttrsTab);
		}
		if (componentParams.authorisedTo.ADV_SEARCH_PLACE) {
			this.placeTab	=	new	EasySDI_Map.PlaceFilterPanel({
				title: EasySDI_Map.lang.getLocal('FP_FP_TITLE'),
				mapPanel:	this.mapPanel
			});
			this.placeTab.on("changedesc", this._setSearchDesc,	this);
			this.tabs.push(this.placeTab);
		}
	},

	/**
   * Handle clicking of the apply filter button, which builds an OGC filter and passes it to the
   * _applyBuiltFilter method to actually run it. If the filter is likely to be slow the user is
   * warned and given a chance to abort.
	 */
	_applyFilter:	function() {
    var filters=[], switchAccess, tabFilter, filterWillBeFast = false, item, el;
		Ext.each(this.filterTabs.items.items,	function(tab)	{
      item = this.filterTabs.getComponent(tab);
      el = this.filterTabs.getTabEl(item);
			if (el.style.display==="") {
				if (typeof tab.getFilters	== "undefined")	{
					alert("Tab " + tab.title + " does	not	have a getFilters	method");
				}	else {
          tabFilter = tab.getFilters();
          if (tabFilter.length>0 && tab.filterWillBeFast) {
            filterWillBeFast=true;
          }
					filters	=	filters.concat(tab.getFilters());
					if (typeof tab.switchAccess	!= "undefined" &&	tab.switchAccess!==null) {
						// tab is	requesting a different access	precision	to normal.
						switchAccess = tab.switchAccess;
					}
				}
			}

    }, this);
    // Give user chance to abort if the filter will be slow
    if (filterWillBeFast) {
    this._applyBuiltFilter(filters, switchAccess);
  } else {
      Ext.MessageBox.confirm('Confirm', 'The filter you have chosen is likely to take a long time to run. Are you sure you want to run it?', function(btn, text){
      if (btn == 'yes'){
        this._applyBuiltFilter(filters, switchAccess);
      }
    }, this);
    }

  },

  /**
   * Actually applies the filters that have been built, and closes the filter panel down.
   */
  _applyBuiltFilter: function(filters, switchAccess) {
    var item, el;
    Ext.each(this.filterTabs.items.items, function(tab) {
			if (tab.doHide)	{
				// Tell	tabs they	are	being	hidden
				tab.doHide();
			}
    });
		// Now ensure	we filter	for	taxa or	biotopes as	required
		var	domain=null;
		/*
		if (this.mode	== filterPanelMode.taxonStatus ||
				this.mode	== filterPanelMode.taxa	||
				this.mode	== filterPanelMode.allTaxa)	{
			domain='T';
		}	if (this.mode	== filterPanelMode.biotopeStatus ||
				this.mode	== filterPanelMode.biotopes	||
				this.mode	== filterPanelMode.allBiotopes)	{
			domain='B';
		}	if (domain!==null) {
			filters.push(new OpenLayers.Filter.Comparison({
				type:	OpenLayers.Filter.Comparison.EQUAL_TO,
				property:	'domain',
				value: domain
			}));
		}*/
		this.innerSearchBar.filter = this._joinFilters(filters);
		this.trigger('doSearch', {
			searchBar: this.innerSearchBar,
			switchAccess:	switchAccess
		});
		this.innerSearchBar.searchDesc = this.searchDesc.getValue();
		    this.innerSearchBar.searchSpatial =
        typeof this.placeTab != "undefined" && this.placeTab.getDescription() !== null ? 1 : 0;
    var searchType;
    switch (this.mode) {
     /* case filterPanelMode.taxonStatus:
        searchType = "ADV_TAXON_STATUS";
        break;
      case filterPanelMode.biotopeStatus:1,
        searchType = "ADV_BIOTOPE_STATUS";
        break;
      case filterPanelMode.taxa:
        searchType = "ADV_TAXA";
        break;
      case filterPanelMode.biotopes:
        searchType = "ADV_BIOTOPES";
        break;
      case filterPanelMode.allTaxa:
        searchType = "ADV_ALL_BIOTOPES";
        break;
      case filterPanelMode.allBiotopes:
        searchType = "ADV_ALL_BIOTOPES";
        break;*/
      default:
        searchType = "ADV_ANYTHING";
    }
    this.innerSearchBar.searchType = searchType;
    
		this.innerSearchBar.filterData = this._encodeFilterData();
		this.innerSearchBar.searchMode = this.mode;
		this.trigger('hideFilterPanel');
	},

	/**
	*	Encode the filter	to JSON	that can be	saved.
	*/
	_encodeFilterData: function()	{
    var parts={}, item, el;
		Ext.each(this.filterTabs.items.items,	function(tab)	{
      item = this.filterTabs.getComponent(tab);
      el = this.filterTabs.getTabEl(item);
      if (el.style.display==="") {
				if (typeof tab.encode==="undefined") {
					alert(tab.title	+	'	has	no encode	method');
				}	else {
					parts[tab.title]=tab.encode();
				}
			}
    }, this);
		return Ext.util.JSON.encode(parts);
	},

	_joinFilters:	function(filters)	{
		if (filters.length==1) {
			return filters[0];
		}	else {
			// multiple	filters, so	AND	them together
			return new OpenLayers.Filter.Logical({
				filters: filters,
				type:	OpenLayers.Filter.Logical.AND
			});
		}
	}

});

Ext.mixin(EasySDI_Map.FilterPanel, EasySDI_Map.TriggerManager);