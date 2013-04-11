jQuery(document).ready(function () {
	//onClick on the button to add a new excluded attribute field
	jQuery('#btn_add_excluded_attribute').click(function () {
		var count = jQuery(this).data('count');
		jQuery('#div_excluded_attributes').append('<textarea name="excluded_attribute[' + count + ']" rows="5" class="span12"></textarea><br /><br />');
		count++;
		jQuery(this).data('count', count);
		return false;
	});
});