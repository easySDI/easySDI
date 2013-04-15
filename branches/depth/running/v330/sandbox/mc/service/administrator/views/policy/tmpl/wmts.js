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
				task: 'wmtsWebservice',
				method: 'getWmtsLayerForm',
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
		var form_values = {};
		for (var i = 0; i < raw_form_array.length; i++) {
			form_values[raw_form_array[i].name] = raw_form_array[i].value;
		}
		var tms_list = form_values.tms_list.split(';');
		
		if (form_values.eastBoundLongitude != '' && form_values.westBoundLongitude != '' && form_values.northBoundLatitude != '' && form_values.southBoundLatitude != '') {
			for (var i = 0; i < tms_list.length; i++) {
				var tms_identifier = tms_list[i];
				var SRS = form_values['srs[' + tms_identifier + ']'];
				
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
				form_values['srsUnit[' + tms_identifier + ']'] = dest.units;
				
				var pLowerEastCorner = new Proj4js.Point(new Array(form_values.eastBoundLongitude,form_values.southBoundLatitude));   
				Proj4js.transform(source, dest, pLowerEastCorner);
				
				var pUpperWestCorner = new Proj4js.Point(new Array(form_values.westBoundLongitude,form_values.northBoundLatitude));   
				Proj4js.transform(source, dest, pUpperWestCorner);

				form_values['minX[' + tms_identifier + ']'] = pUpperWestCorner.x;
				form_values['maxY[' + tms_identifier + ']'] = pUpperWestCorner.y;
				form_values['maxX[' + tms_identifier + ']'] = pLowerEastCorner.x;
				form_values['minY[' + tms_identifier + ']'] = pLowerEastCorner.y;
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
			},
			error: function (jqXHR, textStatus, errorThrown) {
				//console.log(arguments);
			}
		});
	});
});