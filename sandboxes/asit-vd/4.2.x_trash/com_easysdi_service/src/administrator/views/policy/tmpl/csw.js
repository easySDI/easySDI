// fired when a user input data in the state field
function onStateChange () {
	var values = jQuery('#jform_csw_state').val();
	if (null != values) {
		for (var i = 0; i < values.length; i++) {
			if ('3' == values[i]) {
				jQuery('#jform_csw_version_id').parent().parent().show();
				return;
			}
		}
	}
	jQuery('#jform_csw_version_id').parent().parent().hide();
}

function enableState(){
	if(jQuery('#jform_csw_anystate0').is(":checked")){
		jQuery("#jform_csw_state").val("").trigger('liszt:updated');
		jQuery('#jform_csw_state').parent().parent().hide();
		jQuery('#jform_csw_version_id').parent().parent().hide();
	}else{
		jQuery('#jform_csw_state').parent().parent().show();
		jQuery('#jform_csw_version_id').parent().parent().hide();
	}
	
}

function enableResourceType(){
	if(jQuery('#jform_csw_anyresourcetype0').is(":checked")){
		jQuery("#jform_csw_resourcetype").val("").trigger('liszt:updated');
		jQuery('#jform_csw_resourcetype').parent().parent().hide();		
	}else{
		jQuery('#jform_csw_resourcetype').parent().parent().show();
	}
	
}

function enableVisibility(){
    if(jQuery('#jform_csw_anyvisibility0').is(":checked")){
        jQuery("#jform_csw_organisms").val("").trigger('liszt:updated');
        jQuery("#jform_csw_users").val("").trigger('liszt:updated');
        jQuery('#jform_csw_accessscope_id').parent().parent().hide();
        jQuery("#csw_organisms").hide();
        jQuery("#csw_users").hide();
    }else{
        jQuery('#jform_csw_accessscope_id').parent().parent().show();
        if(jQuery('#jform_csw_accessscope_id').val() == 1){    // accessscope:public        
		jQuery("#jform_csw_organisms").val("").trigger('liszt:updated');
		jQuery("#jform_csw_users").val("").trigger('liszt:updated');
		jQuery("#csw_organisms").hide();
		jQuery("#csw_users").hide();
	}
	else if(jQuery('#jform_csw_accessscope_id').val() == 2){    // accessscope:organism
		jQuery("#csw_organisms").show();
		jQuery("#jform_csw_users").val("").trigger('liszt:updated');
		jQuery("#csw_users").hide();
	}
	else if(jQuery('#jform_csw_accessscope_id').val() == 3){
		jQuery("#csw_users").show();
		jQuery("#jform_csw_organisms").val("").trigger('liszt:updated');
		jQuery("#csw_organisms").hide();
	}
    }	
}

function onDeleteExcludedAttribute (index) {
	var parent = jQuery('.div_ea_' + index);
	parent.remove();
	var count = jQuery('#btn_add_excluded_attribute').data('count');
	jQuery('#btn_add_excluded_attribute').data('count', count -1);
	if(jQuery('#btn_add_excluded_attribute').data('count') > 0)
		jQuery('#jform_csw_anyattribute').val('0');
	else
		jQuery('#jform_csw_anyattribute').val('1');
}

function onAddExcludedAttribute () {
	var count = jQuery('#btn_add_excluded_attribute').data('count');
	jQuery('#div_excluded_attributes').append(
			'<div class="div_ea_' + count + ' input-xxlarge">' + 
				'<input type="text" name="excluded_attribute[' + count + ']" class="span10" value="" />'+
				'<span class="btn btn-danger btn-small btn_ea_delete" onClick="onDeleteExcludedAttribute(' +count+ ');return false;"><i class="icon-white icon-remove"></i></span>'+
				'<br /><br />' +
			'</div>'
		);
	count++;
	jQuery('#btn_add_excluded_attribute').data('count', count);
	if(jQuery('#btn_add_excluded_attribute').data('count') > 0)
		jQuery('#jform_csw_anyattribute').val('0');
	else
		jQuery('#jform_csw_anyattribute').val('1');
}

jQuery(document).ready(function () {
        enableVisibility();	
        enableState();
        enableResourceType();
	onStateChange();
	if(jQuery('#btn_add_excluded_attribute').data('count') > 0)
		jQuery('#jform_csw_anyattribute').val('0');
	else
		jQuery('#jform_csw_anyattribute').val('1');
	
	jQuery('input[name="jform[csw_anystate]"]').click(function () {
		enableState();
	});
        
        jQuery('input[name="jform[csw_anyvisibility]"]').click(function () {
		enableVisibility();
	});
        
        jQuery('input[name="jform[csw_anyresourcetype]"]').click(function () {
		enableResourceType();
	});
	
});

var waitFor = 0;
var rtask;
//event fired when the policy-form is submitted
jQuery(document).on('reproject', function (e, task) {
	rtask =task;
		if (jQuery('#jform_srssource').val() == '' || 
				jQuery('#jform_maxx').val() == '' ||  
				jQuery('#jform_maxy').val() == ''  ||
				jQuery('#jform_minx').val() == '' ||  
				jQuery('#jform_miny').val() == ''){
			alert ("Check your geographic filter definition.");
			return;
		}
		try{
			var source = new Proj4js.Proj(jQuery('#jform_srssource').val());
			var dest = new Proj4js.Proj('EPSG:4326');
			var maxx = jQuery('#jform_maxx').val() ;  
			var maxy = jQuery('#jform_maxy').val() ;
			var minx = jQuery('#jform_minx').val() ;  
			var miny = jQuery('#jform_miny').val() ;
			waitFor += 1;
			checkProjLoaded(minx, miny,maxx,maxy, source, dest)
			
			
		}catch (err){
			alert ("Check your geographic filter definition.");
			return;
		}
	});


function checkProjLoaded(minx, miny,maxx,maxy, source, dest) {
    if (!source.readyToUse || !dest.readyToUse) {
      window.setTimeout(Proj4js.bind(checkProjLoaded, this, minx, miny,maxx,maxy, source, dest), 500);
    } else {
	    waitFor -= 1;
	    calculateBBOX(minx, miny,maxx,maxy, source, dest);
    }
}

function calculateBBOX(minx, miny,maxx,maxy, source, dest){
	var pLowerWestCorner = new Proj4js.Point(new Array(minx,miny));   
	Proj4js.transform(source, dest, pLowerWestCorner);
	var pUpperEastCorner = new Proj4js.Point(new Array(maxx,maxy));   
	Proj4js.transform(source, dest, pUpperEastCorner);

	jQuery('#jform_eastboundlongitude').val(pUpperEastCorner.x);
	jQuery('#jform_westboundlongitude').val(pLowerWestCorner.x);
	jQuery('#jform_northboundlatitude').val(pUpperEastCorner.y);
	jQuery('#jform_southboundlatitude').val(pLowerWestCorner.y);	
	Joomla.submitform(rtask, document.getElementById('policy-form'));
}