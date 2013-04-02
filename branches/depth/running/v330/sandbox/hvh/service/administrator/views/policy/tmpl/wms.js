jQuery(document).ready(function () {
	//onClick on a layer configuration btn, we fill the modal with an ajax request
	jQuery('.btn_modify_layer').click(function () {
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
				method: 'getWmsLayerForm',
				physicalServiceID: psID,
				policyID: policyID,
				layerID: layerName,
			},
			success: function (data, textStatus, jqXHR) {
				jQuery('#layer_settings_modal .modal-body #modal_layer_form').html(data);
				jQuery('#layer_settings_modal .modal-header #layer_name').html(layerName);
				jQuery('#layer_settings_modal .modal-body .loaderImg').hide();
			},
			error: function (jqXHR, textStatus, errorThrown) {
				jQuery('#layer_settings_modal .modal-body #modal_layer_form').html('Error, please retry later.');
				jQuery('#layer_settings_modal .modal-header #layer_name').html('');
				jQuery('#layer_settings_modal .modal-body .loaderImg').hide();
			}
		});
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
		for (var i = 0; i < raw_form_array.length; i++) {
			form_values[raw_form_array[i].name] = raw_form_array[i].value;
		}
		
		//TODO: get SRS, calculate and set max/min X/Y
		var geographicFilter = form_values.geographicfilter;
		if(geographicFilter != ""){
			
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
				var theParser = new OpenLayers.Format.GML({
					featureName: "FeatureFilter",
					gmlns: "http://www.opengis.net/gml"
				});
				var bbox = theParser.read(doc)[0].geometry.getBounds().toBBOX().split(',');
				form_values['minX'] = bbox[0];
				form_values['minY'] = bbox[1];
				form_values['maxX'] = bbox[2];
				form_values['maxY'] = bbox[3];
				form_values['srs'] = srsValue;
			}
		}
		
		var get_str = '';
		for (key in form_values) {
			get_str += '&' + key + '=' + form_values[key];
		}
		var get_query = '?option=com_easysdi_service&task=wmsWebservice&method=setWmsLayerSettings' + get_str;
		
		form_values['option'] = 'com_easysdi_service';
		form_values['task'] = 'wmsWebservice';
		form_values['method'] = 'setWmsLayerSettings';
		console.log(form_values);
		
		jQuery.ajax({
			dataType: 'html',
			type: 'GET',
			url: 'index.php',
			data: form_values,
			success: function (data, textStatus, jqXHR) {
				//console.log(arguments);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				//console.log(arguments);
			}
		});
		
	});
});