//onClick on the button to add a new excluded attribute field
function onAddIncludedAttribute () {
	var count = jQuery('#btn_add_included_attribute').data('count');
	jQuery('#div_included_attributes').append('<textarea name="included_attribute[' + count + ']" rows="5" class="span12"></textarea><br /><br />');
	count++;
	jQuery('#btn_add_included_attribute').data('count', count);
}

jQuery(document).ready(function () {
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
				task: 'wfsWebservice',
				method: 'getFeatureTypeForm',
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
					method: 'deleteFeatureType',
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
		var get_query = '?option=com_easysdi_service&task=wfsWebservice&method=setFeatureTypeSettings&' + jQuery('#modal_layer_form').serialize();
		
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