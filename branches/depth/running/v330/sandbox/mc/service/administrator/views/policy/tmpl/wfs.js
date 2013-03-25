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
				task: 'wfsWebservice',
				method: 'getFeatureTypeForm',
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
		var get_query = '?option=com_easysdi_service&task=wfsWebservice&method=setFeatureTypeSettings&' + jQuery('#modal_layer_form').serialize();
		
		jQuery.ajax({
			dataType: 'html',
			type: 'GET',
			url: 'index.php' + get_query,
			success: function (data, textStatus, jqXHR) {
				//console.log(arguments);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				//console.log(arguments);
			}
		});
	});
});