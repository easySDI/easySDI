var inherit_policy = {};
var inherit_server = {};

jQuery(document).ready(function () {
	function calculateBBox (srcBBOX, SRS) {
		var result = {
			minX: '',
			minY: '',
			maxX: '',
			maxY: '',
			unit: ''
		};
		
		if (SRS == null) {
			SRS = 'EPSG:4326';
		}
		//if TileMatrixSet SRS is urn:ogc:def:crs:OGC:1.3:CRS84 :
		//proj4js does not support OGC authority SRS so replace it by the corresponding EPSG:4326
		if (SRS.lastIndexOf("CRS84") != -1) {
			SRS = 'EPSG:4326';
		}
		
		var source = new Proj4js.Proj('EPSG:4326');
		var dest = new Proj4js.Proj(SRS);
		result.unit = dest.units;
		
		var pLowerEastCorner = new Proj4js.Point(new Array(srcBBOX.east, srcBBOX.south));   
		Proj4js.transform(source, dest, pLowerEastCorner);
		
		var pUpperWestCorner = new Proj4js.Point(new Array(srcBBOX.west, srcBBOX.north));   
		Proj4js.transform(source, dest, pUpperWestCorner);
		
		
		result.minX = pUpperWestCorner.x;
		result.maxY = pUpperWestCorner.y;
		result.maxX = pLowerEastCorner.x;
		result.minY = pLowerEastCorner.y;
		
		return result;
	}
	
	function recalculateAll () {
		var raw_form_array = jQuery('#policy-form').serializeArray();
		var form_values = {
			inherit_policy : {},
			inherit_server: {},
			srs_units: {},
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
		
		//recalculate all the BBoxes based on policy inherited settings
		form_values.inherit_policy.recalculated = {};
		if (isNaN(form_values.inherit_policy.eastBoundLongitude) || isNaN(form_values.inherit_policy.westBoundLongitude) || isNaN(form_values.inherit_policy.northBoundLatitude) || isNaN(form_values.inherit_policy.southBoundLatitude)) {
			popAlert(Joomla.JText._('COM_EASYSDI_SERVICE_MSG_MODAL_MALFORMED_BBOX_BOUNDARIES'), 'alert-error');
			return false;
		}
		
		if (form_values.inherit_policy.eastBoundLongitude != '' && form_values.inherit_policy.westBoundLongitude != '' && form_values.inherit_policy.northBoundLatitude != '' && form_values.inherit_policy.southBoundLatitude != '') {
			for (var i = 0; i < SRSList.length; i++) {
				var srcBBOX = {
					north: form_values.inherit_policy.northBoundLatitude,
					east: form_values.inherit_policy.eastBoundLongitude,
					south: form_values.inherit_policy.southBoundLatitude,
					west: form_values.inherit_policy.westBoundLongitude
				};
				var destBBOX = calculateBBox(srcBBOX, SRSList[i]);
				form_values.inherit_policy.recalculated[SRSList[i]] = {};
				form_values.inherit_policy.recalculated[SRSList[i]].minX = destBBOX.minX;
				form_values.inherit_policy.recalculated[SRSList[i]].maxY = destBBOX.maxY;
				form_values.inherit_policy.recalculated[SRSList[i]].maxX = destBBOX.maxX;
				form_values.inherit_policy.recalculated[SRSList[i]].minY = destBBOX.minY;
				form_values.srs_units[SRSList[i]] = destBBOX.unit;
			}
		}
		else {
			if (!(form_values.inherit_policy.eastBoundLongitude == '' && form_values.inherit_policy.westBoundLongitude == '' && form_values.inherit_policy.northBoundLatitude == '' && form_values.inherit_policy.southBoundLatitude == '')) {
				popModalAlert(Joomla.JText._('COM_EASYSDI_SERVICE_MSG_MODAL_MISSING_BBOX_BOUNDARIES'), 'alert-error');
				return false;
			}
		}
		
		//recalculate all the BBoxes based on server inherited settings
		for (server in form_values.inherit_server) {
			form_values.inherit_server[server].recalculated = {};
			if (isNaN(form_values.inherit_server[server].eastBoundLongitude) || isNaN(form_values.inherit_server[server].westBoundLongitude) || isNaN(form_values.inherit_server[server].northBoundLatitude) || isNaN(form_values.inherit_server[server].southBoundLatitude)) {
				popAlert(Joomla.JText._('COM_EASYSDI_SERVICE_MSG_MODAL_MALFORMED_BBOX_BOUNDARIES'), 'alert-error');
				return false;
			}
			
			if (form_values.inherit_server[server].eastBoundLongitude != '' && form_values.inherit_server[server].westBoundLongitude != '' && form_values.inherit_server[server].northBoundLatitude != '' && form_values.inherit_server[server].southBoundLatitude != '') {
				for (var i = 0; i < SRSList.length; i++) {
					var srcBBOX = {
						north: form_values.inherit_server[server].northBoundLatitude,
						east: form_values.inherit_server[server].eastBoundLongitude,
						south: form_values.inherit_server[server].southBoundLatitude,
						west: form_values.inherit_server[server].westBoundLongitude
					};
					var destBBOX = calculateBBox(srcBBOX, SRSList[i]);
					form_values.inherit_server[server].recalculated[SRSList[i]] = {};
					form_values.inherit_server[server].recalculated[SRSList[i]].minX = destBBOX.minX;
					form_values.inherit_server[server].recalculated[SRSList[i]].maxY = destBBOX.maxY;
					form_values.inherit_server[server].recalculated[SRSList[i]].maxX = destBBOX.maxX;
					form_values.inherit_server[server].recalculated[SRSList[i]].minY = destBBOX.minY;
				}
			}
			else {
				if (!(form_values.inherit_server[server].eastBoundLongitude == '' && form_values.inherit_server[server].westBoundLongitude == '' && form_values.inherit_server[server].northBoundLatitude == '' && form_values.inherit_server[server].southBoundLatitude == '')) {
					popModalAlert(Joomla.JText._('COM_EASYSDI_SERVICE_MSG_MODAL_MISSING_BBOX_BOUNDARIES'), 'alert-error');
					return false;
				}
			}
		}
		return form_values;
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
				task: 'wmtsWebservice',
				method: 'getWmtsLayerForm',
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
					task: 'wmtsWebservice',
					method: 'deleteWmtsLayer',
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
		//we pre-calculate the bbox (EPSG:4326 -> tile matrix set srs)
		var raw_form_array = jQuery('#modal_layer_form').serializeArray();
		var form_values = {
			precalculated : JSON.stringify(recalculateAll()),
		};
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
		var tms_list = form_values.tms_list.split(';');
		
		if (isNaN(form_values.eastBoundLongitude) || isNaN(form_values.westBoundLongitude) || isNaN(form_values.northBoundLatitude) || isNaN(form_values.southBoundLatitude)) {
			popModalAlert(Joomla.JText._('COM_EASYSDI_SERVICE_MSG_MODAL_MALFORMED_BBOX_BOUNDARIES'), 'alert-error');
			return false;
		}
		
		if (form_values.eastBoundLongitude != '' && form_values.westBoundLongitude != '' && form_values.northBoundLatitude != '' && form_values.southBoundLatitude != '') {
			var srcBBOX = {
                                            north: form_values.northBoundLatitude,
                                            east: form_values.eastBoundLongitude,
                                            south: form_values.southBoundLatitude,
                                            west: form_values.westBoundLongitude
                                    };
                        for (var i = 0; i < tms_list.length; i++) {
				var tms_identifier = tms_list[i];
				var SRS = form_values['srs[' + tms_identifier + ']'];
                                
				var bbox = calculateBBox(srcBBOX,SRS);
				
				form_values['minX[' + tms_identifier + ']'] = bbox.minX;
				form_values['maxY[' + tms_identifier + ']'] = bbox.maxY;
				form_values['maxX[' + tms_identifier + ']'] = bbox.maxX;
				form_values['minY[' + tms_identifier + ']'] = bbox.minY;
				form_values['srsUnit[' + tms_identifier + ']'] = bbox.unit;
			}
		}
		else {
			if (!(form_values.eastBoundLongitude == '' && form_values.westBoundLongitude == '' && form_values.northBoundLatitude == '' && form_values.southBoundLatitude == '')) {
				popModalAlert(Joomla.JText._('COM_EASYSDI_SERVICE_MSG_MODAL_MISSING_BBOX_BOUNDARIES'), 'alert-error');
				return false;
			}
		}
		
		var get_str = '';
		for (key in form_values) {
			get_str += '&' + key + '=' + form_values[key];
		}
		
		var get_query = '?option=com_easysdi_service&task=wmtsWebservice&method=setWmtsLayerSettings' + get_str;
		
		jQuery.ajax({
			dataType: 'html',
			type: 'GET',
			url: 'index.php' + get_query,
			success: function (data, textStatus, jqXHR) {
				//console.log(arguments);
				jQuery('#layer_settings_modal').modal('hide');
				popAlert(Joomla.JText._('COM_EASYSDI_SERVICE_MSG_MODAL_SAVED'), 'alert-success');
				jQuery('#configured'+psID+layerName).addClass('label-success');
				jQuery('#configured'+psID+layerName).text(Joomla.JText._('COM_EASYSDI_SERVICE_POLICY_LAYER_SETTINGS_DEFINED'));
			},
			error: function (jqXHR, textStatus, errorThrown) {
				//console.log(arguments);
			}
		});
	});

	jQuery(document).on('recalculate', function (e, task) {
		jQuery('#precalculatedData').val(JSON.stringify(recalculateAll()));
		Joomla.submitform(task, document.getElementById('policy-form'));
	});
});