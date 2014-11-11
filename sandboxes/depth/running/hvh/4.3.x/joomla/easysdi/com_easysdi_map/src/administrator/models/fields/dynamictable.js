jQuery(document).ready(function() {
    var i = 1;
    jQuery("#add_row").click(function() {
        jQuery('#level' + i).html("<td>" + (i) + "</td><td><input name='jform[label" + i + "]' id='jform_label" + i + "'  type='text' placeholder='Label' class='form-control'> </td><td><input  name='jform[code" + i + "]' id='jform_code" + i + "' type='text' placeholder='Code'  class='form-control'></td>");

        jQuery('#tab-dyn').append('<tr id="level' + (i + 1) + '"></tr>');
        i++;
    });
    jQuery("#delete_row").click(function() {
        if (i > 1) {
            jQuery("#level" + (i - 1)).html('');
            i--;
        }
    });
});