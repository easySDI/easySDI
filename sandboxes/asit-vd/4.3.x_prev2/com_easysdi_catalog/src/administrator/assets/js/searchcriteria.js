js = jQuery.noConflict();
var currentUrl = location.protocol + '//' + location.host + location.pathname;

/**
 * 
 * @param {type} catalog_id
 * @returns {undefined}filter resource type for a specific catalog
 */
function filterResourceType(catalog_id) {
    var selectedValues = js('#jform_resourcetype_id').val();

    js.get(currentUrl + '/administrator/index.php?option=com_easysdi_catalog&task=searchcriteria.getResourcesTypes&catalog_id=' + catalog_id, function(data) {
        var attributes = js.parseJSON(data);
        js('#jform_resourcetype_id').empty().trigger("liszt:updated");

        js.each(attributes, function(key, value) {
            var selected = '';
            if (js.inArray(value.id, selectedValues)>-1)
                selected = 'selected="selected"';
            
            js('#jform_resourcetype_id')
                    .append('<option value="' + value.id + '" ' + selected + ' >' + value.name + '</option>')
                    .trigger("liszt:updated");
        });

    });
}