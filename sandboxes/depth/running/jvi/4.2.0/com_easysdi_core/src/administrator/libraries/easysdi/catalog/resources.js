js = jQuery.noConflict();
var currentUrl = location.protocol + '//' + location.host + location.pathname;

js(document).ready(function() {

    // Change publish date field to Calendar field
    Calendar.setup({
        // Id of the input field
        inputField: "published",
        // Format of the input field
        ifFormat: "%Y-%m-%d",
        // Trigger for the calendar (button ID)
        button: "published_img",
        // Alignment (defaults to "Bl")
        align: "Bl",
        singleClick: true,
        firstDay: 1
    });

    initChildrenList();

});

function initChildrenList() {
    js('.version-status').each(function(i) {
        parentId = js(this).val();
        getChildNumer(parentId);
    });
}

function getChildNumer(parentId) {
    js.get(currentUrl + '/?option=com_easysdi_core&task=version.getChildren&parentId=' + parentId, function(data) {
        response = js.parseJSON(data);
        if (response.success == 'true') {
            if (response.num > 0) {
                js('#' + response.resource_id + '_child_list').show();
                js('#' + response.resource_id + '_child_num').html(response.num);
            }
        }
    });
}

function showModal(id, modalId) {
    modalId = modalId || 'publishModal';
    js('html, body').animate({scrollTop: 0}, 'slow');
    js('#'+modalId+' input[name^="id"]').val(id);
    js('#'+modalId).modal('show');
}

function showAssignmentModal(version_id){
    js('#assigned_to').html('');
    
    js.get(currentUrl+'/?option=com_easysdi_catalog&task=metadata.getRoles&versionId='+version_id, function(data){
        var roles = js.parseJSON(data);
        
        for(var user_id in roles[4].users)
            js('#assigned_to').append(js('<option></option>').val(user_id).html(roles[4].users[user_id]));
        js('#assigned_to').trigger('liszt:updated');
        showModal(version_id, 'assignmentModal');
    });
}

function onVersionChange(resourceid) {
    var version_id = js("select#" + resourceid + "_select").val();
    
    js('.' + resourceid + '_linker').each(function() {
        var href = js(this).attr("href");
        var i = href.lastIndexOf("/");
        var newhref = href.substring(0, i + 1);
        js(this).attr("href", newhref + version_id);
    });

    js('#' + resourceid + '_child_linker').attr('href', '/resources?parentid=' + version_id);
    getChildNumer(version_id);
}

function showDeleteModal(deleteUrl) {
    js('#btn_delete').attr('href', deleteUrl);
    js('#deleteModal').modal('show');
}