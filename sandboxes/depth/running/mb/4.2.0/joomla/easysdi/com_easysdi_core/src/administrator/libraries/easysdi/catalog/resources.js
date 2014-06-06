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

/**
 * Initalise child list
 */
function initChildrenList() {
    js('.version-status').each(function(i) {
        var parentId = js(this).val();
        getChildNumer(parentId);
    });
}

/**
 * 
 * @param {int} parentId parent version id
 * @returns void
 */
function getChildNumer(parentId) {
    js.get(currentUrl + '/?option=com_easysdi_core&task=version.getChildren&parentId=' + parentId, function(data) {
        var response = js.parseJSON(data);
        if (response.success == 'true') {
            if (response.num > 0) {
                js('#' + response.resource_id + '_child_list').show();
                js('#' + response.resource_id + '_child_num').html(response.num);
            } else {
                js('#' + response.resource_id + '_child_list').hide();
            }
        }
    });
}

/**
 * Show publish modal
 * 
 * @param {int} id
 * @returns void
 */
function showModal(id) {
    js('html, body').animate({scrollTop: 0}, 'slow');
    js('input[name^="id"]').val(id);
    js('#publishModal').modal('show');
}

/**
 * Change link id on version change
 * 
 * @param {int} resource_id
 * @returns void
 */
function onVersionChange(resource_id) {
    var version_id = js("select#" + resource_id + "_select").val();

    changeRelationLink(resource_id, version_id);
    changeChildLink(resource_id, version_id);

    getChildNumer(version_id);
}

/**
 * Change link from relation link
 * 
 * @param {int} resource_id
 * @param {int} version_id
 */
function changeRelationLink(resource_id, version_id) {
    js('.' + resource_id + '_linker').each(function() {
        var href = js(this).attr("href");
        var i = href.lastIndexOf("/");
        var newhref = href.substring(0, i + 1);
        js(this).attr("href", newhref + version_id);
    });
}

/**
 * Change link from child list link
 * 
 * @param {int} resource_id
 * @param {int} version_id
 */
function changeChildLink(resource_id, version_id) {
    js('#' + resource_id + '_child_linker').attr('href', '/resources?parentid=' + version_id);
}

/**
 * Show delete modal
 * 
 * @param {string} deleteUrl
 */
function showDeleteModal(deleteUrl) {
    js('#btn_delete').attr('href', deleteUrl);
    js('#deleteModal').modal('show');
}

/**
 * 
 * @param {string} createUrl
 * @param {int} resource_id
 */
function showNewVersionModal(createUrl, resource_id) {
    js.get(currentUrl + '/?option=com_easysdi_core&task=version.getInProgressChildren&resource=' + resource_id, function(data) {
        var response = js.parseJSON(data);
        if (response.total > 0) {
            var body = '<ul>';
            js.each(response.versions, function(k, version) {
                body += '<li>' + version.resource_name + ' : ' + version.version_name + ' <a href="/index.php?option=com_easysdi_catalog&task=metadata.edit&id=' + version.metadata_id + '" target="_top"><i class="icon-edit"></i></a></li>';
            });
            body += '</ul>';
            js('#btn_create').attr('href', createUrl);
            js('#createModalChildrenList').html(body);
            js('#createModal').modal('show');
        } else {
            window.location.href = createUrl;
        }
    });
}