/**
 * EasySDI,	a	solution to	implement	easily any spatial data	infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
 *
 * This	program	is free	software:	you	can	redistribute it	and/or modify
 * it	under	the	terms	of the GNU General Public	License	as published by
 * the Free	Software Foundation, either	version	3	of the License,	or
 * any later version.
 * This	program	is distributed in	the	hope that	it will	be useful,
 * but WITHOUT ANY WARRANTY; without even	the	implied	warranty of
 * MERCHANTABILITY or	FITNESS	FOR	A	PARTICULAR PURPOSE.	 See the
 * GNU General Public	License	for	more details.
 * You should	have received	a	copy of	the	GNU	General	Public License
 * along with	this program.	 If	not, see http://www.gnu.org/licenses/gpl.html.
 */

/**
 * Provides	access to	a	single feature's details and comments	against	the	main feature table.
 * This	class	should not be	instantiated directly, but use Mixin to	add	it to	another	class.
 * In	the	class, set the following:
 *	 this.outputCntr to	the	Ext	container	to receive output.
 *	 this.featureId
 *	 this.featureType
 *	 handleCommentPost(evt)	-	a	method to	take action	when a comment is	posted.
 */
Ext.namespace("EasySDI_Map");

EasySDI_Map.FeatureDetailsHelper = function(){};

/**
 * Override	the	loadStore	method to	build	an HTML	table	for	the	single feature that
 * should	be in	the	store. Also	setup	the	map	if the feature has geometry.
 */
EasySDI_Map.FeatureDetailsHelper.prototype.loadStore = function(store) {
	var	catAndName,	html,	hasComments=false;
	Ext.each(store.data.items[0].fields.items, function(field) {
		// Ignore	the	feature	fields which the user	should not see
		if (field.name !=	'feature'	&& field.name	!= 'state' &&	field.name !=	'fid') {
			if (typeof store.data.items[0].data[field.name]!=="undefined") {
				// Comment counts	can	be ommitted	here,	since	we will	load the comments	into the form
				if (field.name!==SData.commentFeatureType.featureCommentCount) {
					catAndName=EasySDI_Map.lang.getLocal('COL_'	+	field.name).split('/');
					if (field.name=='image_file')	{
						// Display thumbnail images
						html = '<img src="'	+	store.data.items[0].data[field.name] + '"	/>';
					} else {
						html = '<span>'	+	store.data.items[0].data[field.name] + '</span>';
					}
					this.outputCntr.add({
						html:	html,
						fieldLabel:	catAndName[1]	|| field.name,
						isFormField: true,
						border:	false,
						style: "background-colour: #d7d7d7;"
					});
				}	else {
					hasComments=store.data.items[0].data[field.name]>0;
				}
			}
		}
	}, this);
	// if	Geometry required, display it
	if (typeof store.data.items[0].data.feature.geometry!=="undefined" &&	typeof this.displayGeom	!==	"undefined") {
		this.displayGeom(store.data.items[0].data.feature);
	}
	// If	there	are	any	comments,	issue	a	WFS	query	to load	them.
	if (typeof SData.commentFeatureType	!==	"undefined"	&& hasComments)	{
		this.getComments();
	}	else {
		this.addCommentEntryFields();		
	}
	this.doLayout();
};

/**
 * Issue a WFS request for the comments	and	add	them to	the	form.
 */
EasySDI_Map.FeatureDetailsHelper.prototype.getComments = function()	{
	var	protocol = new OpenLayers.Protocol.WFS({
		url: componentParams.proxiedPubWfsUrl,
		featureNS: componentParams.pubFeatureNS,
		featurePrefix: componentParams.pubFeaturePrefix,
		featureType: SData.commentFeatureType.typeName,
		srsName: componentParams.projection,
		version: componentParams.pubWfsVersion,
		filter:	new	OpenLayers.Filter.Comparison({
			type:	OpenLayers.Filter.Comparison.EQUAL_TO,
			property:	componentParams.featureIdAttribute,
			value: this.featureId
		}),
		sort:	{
			sortField: componentParams.pubFeaturePrefix	+	':enteredon',
			sortDir: 'ASC'
		}
	});
	var	fields = [];
	fields.push({name: 'comment',	type:	'string'});
	fields.push({name: 'username', type: 'string'});
	fields.push({name: 'enteredon',	type:	'date',
		convert: function(v) {
			// Implement our own convertor function, since just	using	a	dateFormat does	not	allow	us to
			// tolerate	different	time zone	expressions.
			return Date.parseDate(v.substr(0,	19), 'Y-m-dTH:i:s');
		}
	});
	var	proxy	=	new	GeoExt.data.ProtocolProxy({protocol: protocol});
	var	store	=	new	GeoExt.data.FeatureStore({
			fields:	fields,
			proxy: proxy,
			srsName: componentParams.projection
	});
	store.on('load', this.loadCommentStore,	this);
	store.load();
};

/**
 * Load	event	handler	for	the	comments store.	Populates	the	comments into	the	form.
 */
EasySDI_Map.FeatureDetailsHelper.prototype.loadCommentStore	=	function(store)	{
	var	catAndName,	hasComments=false, html='';
	Ext.each(store.data.items, function(item)	{
		html +=	'<div	class="comment"><span>Posted by	'	+	item.data.username + ' on	'	+	item.data.enteredon.toLocaleString() + '</span><br/>';
		html +=	item.data.comment;
		html +=	'</div>';
	}, this);
	// If	reloading	the	comments,	just update	the	control's	body
	if (typeof this.commentsCtrl !== "undefined")	{
		this.commentsCtrl.body.update(html);
	}	else {
		this.commentsCtrl	=	new	Ext.Panel({
			html:	html,
			fieldLabel:	EasySDI_Map.lang.getLocal('POPUP_COMMENTS'),
			isFormField: true,
			border:	false
		});
		this.outputCntr.add(this.commentsCtrl);
	}
	this.addCommentEntryFields();
	this.doLayout();
};

/**
 * Add the data	entry	controls for a new comment to	be entered.
 */
EasySDI_Map.FeatureDetailsHelper.prototype.addCommentEntryFields = function()	{
	if (user.loggedIn	&& typeof	SData.commentFeatureType !== "undefined")	{
		// Clear the new comments	control	if already added
		if (typeof this.newCommentCtrl !== "undefined")	{
			this.newCommentCtrl.setValue('');
		}	else {
			this.newCommentCtrl	=	new	Ext.form.TextArea({
				fieldLabel:	EasySDI_Map.lang.getLocal('POPUP_NEW_COMMENT'),
				id:	'new_comment',
				anchor:	"90%"
			});
			this.outputCntr.add(this.newCommentCtrl);
		}
	}
};

/**
 * Use WFST	to post	the	comment	to the database.
 */
EasySDI_Map.FeatureDetailsHelper.prototype.saveComment = function()	{
	// get trimmer version of	comment
	var	comment=Ext.get('new_comment').getValue().replace(/^\s*(\S*(\s+\S+)*)\s*$/,	"$1");
	if (comment	!= "") {
		var	protocol = new OpenLayers.Protocol.WFS({
			url: componentParams.proxiedPubWfsUrl,
			featureNS: componentParams.pubFeatureNS,
			featurePrefix: componentParams.pubFeaturePrefix,
			featureType: SData.commentFeatureType.typeName,
			srsName: componentParams.projection,
			version: componentParams.pubWfsVersion
		});
		// Create	a	geometry-less	vector feature to	help post	the	attributes for the comment
		d=new	Date();
		var	isodate	=	d.getFullYear()	+	'-'	+	this.padZero(d.getMonth()+1) + '-' + this.padZero(d.getDate()) +
					'T'	+	this.padZero(d.getHours()) + ':' + this.padZero(d.getMinutes())	+	':'	+	this.padZero(d.getSeconds())+
					this.timezoneOffset(d);
		var	values={
			comment: comment,
			userid:	user.id,
			username:	user.name,
			enteredon: isodate
		};
		values[componentParams.featureIdAttribute]=this.featureId;
		var	feature	=	new	OpenLayers.Feature.Vector(null,	values);
		feature.state	=	OpenLayers.State.INSERT;
		protocol.commit([feature], {
			callback:	typeof this.handleCommentPost!=="undefined"	?	this.handleCommentPost : null,
			scope: this
		});
	}	else {
		this.destroy();
	}
};

/**
 * Convert a date	into a timezone	offset string	suitable for a ISO 8601	date.
 * param date	d	Date to	convert.
 */
EasySDI_Map.FeatureDetailsHelper.prototype.timezoneOffset	=	function(d)	{
	// offset	in hours
	var	h=d.getTimezoneOffset()/60;
	var	r	=	(d>=0) ? '+' : '-';
	if (Math.abs(h)<10)	{
		r	+= '0';
	}
	r	+= Math.abs(h) + ':00';
	return r;
};


/**
 * Add a leading zero	to make	2	digits if	required.
 */
EasySDI_Map.FeatureDetailsHelper.prototype.padZero = function(n) {
	return ('0'	+	n).slice(-2);
};
