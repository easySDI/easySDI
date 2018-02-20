jQuery(document).ready(function () {
	function calculateBBox (geographicFilter) {
		var result = {
			minX: '',
			minY: '',
			maxX: '',
			maxY: '',
			srs: '',
			error: false
		};
		
		if(geographicFilter == "" || geographicFilter == undefined){
			return result;
		}
		
		geographicFilter = geographicFilter.replace(/^\s*|\s*$/g,'');
		if(geographicFilter.length != 0){
			//Get the srs name
			var index = geographicFilter.indexOf("srsName=\"", 0);
			var indexEnd = geographicFilter.indexOf("\"", index+9) ;
			var srsValue = geographicFilter.substring(index+9, indexEnd);

			//Complete filter GML
			geographicFilter = "<gml:featureMembers xmlns:gml=\"http://www.opengis.net/gml\"><gml:FeatureFilter xmlns:gml=\"http://www.opengis.net/gml\">" + geographicFilter + "</gml:FeatureFilter></gml:featureMembers>";
			//Load filter as DOMDocument
			if (window.ActiveXObject) {
				var doc = new ActiveXObject('Microsoft.XMLDOM');
				doc.async = 'false';
				doc.loadXML(geographicFilter);
			}
			else {
				var parser = new DOMParser();
				var doc = parser.parseFromString(geographicFilter,'text/xml');
			}
			console.log(doc);
			var theParser = new OpenLayers.Format.GML({
				featureName: "FeatureFilter",
				gmlns: "http://www.opengis.net/gml"
			});
			try {
				var bbox = theParser.read(doc)[0].geometry.getBounds().toBBOX().split(',');
			}
			catch (err){
				result.error = true;
				return result;
			}
			result.minX = bbox[0];
			result.minY = bbox[1];
			result.maxX = bbox[2];
			result.maxY = bbox[3];
			result.srs = srsValue;
		}
		return result;
	}
	
	//onClick on a layer configuration btn, we fill the modal with an ajax request
	jQuery('.btn_modify_layer').click(function () {
		var psID = jQuery(this).data('psid');
		var vsID = jQuery(this).data('vsid');
		var policyID = jQuery(this).data('policyid');
		var layerName = jQuery(this).data('layername');
		
		jQuery('#modal_alert').html('');
		
		jQuery.ajax({
			dataType: 'html',
			type: 'GET',
			url: 'index.php',
			data: {
				option: 'com_easysdi_service',
				task: 'wmsWebservice',
				method: 'getWmsLayerForm',
				physicalServiceID: psID,
				virtualServiceID: vsID,
				policyID: policyID,
				layerID: layerName,
			},
			success: function (data, textStatus, jqXHR) {
				jQuery('#layer_settings_modal .modal-body #modal_layer_form').html(data);
				jQuery('#layer_settings_modal .modal-header #layer_name').html(layerName);
				jQuery('#layer_settings_modal .modal-body .loaderImg').hide();
			},
			error: function (jqXHR, textStatus, errorThrown) {
				jQuery('#layer_settings_modal .modal-body #modal_layer_form').html(Joomla.JText._('COM_EASYSDI_SERVICE_MODAL_ERROR'));
				jQuery('#layer_settings_modal .modal-header #layer_name').html('');
				jQuery('#layer_settings_modal .modal-body .loaderImg').hide();
			}
		});
	});
	
	//onClick on a layer deletion btn, we call the deletion script with an ajax request
	jQuery('.btn_delete_layer').click(function () {
		if (confirm(Joomla.JText._('COM_EASYSDI_SERVICE_CONFIRM_DELETION'))) {
			var psID = jQuery(this).data('psid');
			var policyID = jQuery(this).data('policyid');
			var layerName = jQuery(this).data('layername');
			
			jQuery.ajax({
				dataType: 'html',
				type: 'GET',
				url: 'index.php',
				data: {
					option: 'com_easysdi_service',
					task: 'wmsWebservice',
					method: 'deleteWmsLayer',
					physicalServiceID: psID,
					policyID: policyID,
					layerID: layerName,
				},
				success: function (data, textStatus, jqXHR) {
					popAlert(Joomla.JText._('COM_EASYSDI_SERVICE_MSG_DELETION_COMPLETE'), 'alert-success');
					jQuery('#configured'+psID+layerName).removeClass('label-success');
					jQuery('#configured'+psID+layerName).text(Joomla.JText._('COM_EASYSDI_SERVICE_POLICY_LAYER_SETTINGS_INHERITED'));
				},
				error: function (jqXHR, textStatus, errorThrown) {
					
				}
			});
		}
	});
	
	//onClose we flush the modal content
	jQuery('#layer_settings_modal').on('hidden', function () {
		jQuery('#layer_settings_modal .modal-body #modal_layer_form').html('');
		jQuery('#layer_settings_modal .modal-body .loaderImg').show();
	});
	
	//on submission of the modal
	jQuery('#layer_settings_modal .modal-footer .btn-primary').click(function () {
		//we pre-calculate the bbox
		var raw_form_array = jQuery('#modal_layer_form').serializeArray();
		var form_values = {};
		var psID= null;
		var layerName= null;
		for (var i = 0; i < raw_form_array.length; i++) {
			form_values[raw_form_array[i].name] = raw_form_array[i].value;
			if(raw_form_array[i].name == 'layerID'){
				layerName = raw_form_array[i].value;
			}
			if(raw_form_array[i].name == 'psID'){
				psID = raw_form_array[i].value;
			}
		}
		
		var geographicFilter = form_values.geographicfilter;
		var bbox = calculateBBox(geographicFilter);
		
		if (bbox.error) {
			popModalAlert('malformed filter', 'alert-error');
			return false;
		}
		
		form_values['minX'] = bbox.minX;
		form_values['minY'] = bbox.minY;
		form_values['maxX'] = bbox.maxX;
		form_values['maxY'] = bbox.maxY;
		form_values['srs'] = bbox.srs;
		
		var get_str = '';
		for (key in form_values) {
			get_str += '&' + key + '=' + form_values[key];
		}
		var get_query = '?option=com_easysdi_service&task=wmsWebservice&method=setWmsLayerSettings' + get_str;
		
		form_values['option'] = 'com_easysdi_service';
		form_values['task'] = 'wmsWebservice';
		form_values['method'] = 'setWmsLayerSettings';
		
		jQuery.ajax({
			dataType: 'html',
			type: 'GET',
			url: 'index.php',
			data: form_values,
			success: function (data, textStatus, jqXHR) {
				jQuery('#layer_settings_modal').modal('hide');
				jQuery('#configured'+psID+layerName).addClass('label-success');
				jQuery('#configured'+psID+layerName).text(Joomla.JText._('COM_EASYSDI_SERVICE_POLICY_LAYER_SETTINGS_DEFINED'));
				popAlert(Joomla.JText._('COM_EASYSDI_SERVICE_MSG_MODAL_SAVED'), 'alert-success');
			},
			error: function (jqXHR, textStatus, errorThrown) {
				//console.log(arguments);
			}
		});
		
	});
	
	//event fired when the policy-form is submitted
	jQuery(document).on('recalculate', function (e, task) {
		//we pre-calculate the bboxes
		var raw_form_array = jQuery('#policy-form').serializeArray();
		var form_values = {
			inherit_policy : {},
			inherit_server: {},
		};
		for (var i = 0; i < raw_form_array.length; i++) {
			var key = raw_form_array[i].name;
			var value = raw_form_array[i].value;
			
			if (-1 != key.search(/^inherit_policy/g)) {
				var indexes = key.substr(0, key.length - 1).split(/\[|\]\[/g);
				form_values.inherit_policy[indexes[2]] = value;
				form_values.inherit_policy['id'] = indexes[1];
			}
			else if (-1 != key.search(/^inherit_server/g)) {
				var indexes = key.substr(0, key.length - 1).split(/\[|\]\[/g);
				if (undefined == form_values.inherit_server[indexes[1]]) {
					form_values.inherit_server[indexes[1]] = {};
				}
				form_values.inherit_server[indexes[1]][indexes[2]] = value;
			}
		}
		
		//calculate for inherit_policy
		var geographicFilter = form_values.inherit_policy.geographicfilter;
		var bbox = calculateBBox(geographicFilter);
		
		if (bbox.error) {
			popAlert('malformed filter', 'alert-error');
			return false;
		}
		jQuery('#inherit_policy_' + form_values.inherit_policy.id + '_maxx').val(bbox.maxX);
		jQuery('#inherit_policy_' + form_values.inherit_policy.id + '_maxy').val(bbox.maxY);
		jQuery('#inherit_policy_' + form_values.inherit_policy.id + '_minx').val(bbox.minX);
		jQuery('#inherit_policy_' + form_values.inherit_policy.id + '_miny').val(bbox.minY);
		jQuery('#inherit_policy_' + form_values.inherit_policy.id + '_srssource').val(bbox.srs);
		
		//calculate for inherit_server
		for (key in form_values.inherit_server) {
			var geographicFilter = form_values.inherit_server[key].geographicfilter;
			var bbox = calculateBBox(geographicFilter);
		
			if (bbox.error) {
				popAlert('malformed filter', 'alert-error');
				return false;
			}
			
			jQuery('#inherit_server_' + key + '_maxx').val(bbox.maxX);
			jQuery('#inherit_server_' + key + '_maxy').val(bbox.maxY);
			jQuery('#inherit_server_' + key + '_minx').val(bbox.minX);
			jQuery('#inherit_server_' + key + '_miny').val(bbox.minY);
			jQuery('#inherit_server_' + key + '_srssource').val(bbox.srs);
		}
		
		Joomla.submitform(task, document.getElementById('policy-form'));
	});
});