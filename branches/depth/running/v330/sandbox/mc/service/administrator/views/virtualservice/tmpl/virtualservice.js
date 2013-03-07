		Joomla.submitbutton = function(task)
		{
			if (task == 'virtualservice.cancel' || document.formvalidator.isValid(document.id('virtualservice-form'))) {
				Joomla.submitform(task, document.getElementById('virtualservice-form'));
			}
			else {
				alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED', 'Form validation failed'));
			}
		}
		
		
		function updateAgregatedVersion ()
		{
			var supportedVersionsArray ;
			
			jQuery('#div-supportedversions').html("");
			jQuery('#jform_compliance').val("");
			jQuery('#jform_physicalservice_id :selected').each(function(i, selected){ 
				var selected = jQuery(selected).text();
				var versions = selected.split(' - ')[1];
				var versionsArray = versions.substring(1, versions.length -1).split('-');

				if(supportedVersionsArray){
					var j = supportedVersionsArray.length;
					while(j--){
						if(!contains(versionsArray,supportedVersionsArray[j])){
							supportedVersionsArray.splice(1,j);
						}
					}
					
				}else{
					supportedVersionsArray = versionsArray;
				}
				 
			});
			
			jQuery('#jform_compliance').val(JSON.stringify(supportedVersionsArray));

			if(supportedVersionsArray && supportedVersionsArray.length > 0)
			{
				jQuery('#div-supportedversions').html(createSupportedVersionLabel(supportedVersionsArray)) ;
			}
		}

		function contains(arr, findValue) {
		    var i = arr.length;
		     
		    while (i--) {
		        if (arr[i] === findValue) return true;
		    }
		    return false;
		}

		function createSupportedVersionLabel(versions){
			var html = '';
			for( var i = 0 ; i < versions.length ; i++ ){
				html += '<span class="label label-info">';
				html += versions[i];
				html += '</span>';
			}
			return html;
		}