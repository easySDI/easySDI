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

    initActionList();
    
    js('#search-reset').on('click', resetSearch);

});

var resetSearch = function(){
    js('#filter_resourcetype option:first, #filter_resourcetype_children option:first').attr('selected', true);
    js('#filter_status option:first, #filter_status_children option:first').attr('selected', true);
    js('#filter_search, #filter_search_children').val('');
    js('form.form-search').submit();
    return false;
};

/**
 * Initalise child list
 */
function initActionList() {
    js('.version-status').each(function(i) {
        var metadata_id = js(this).val();
        getChildNumer(metadata_id);
        getNewVersionRight(metadata_id);
        getSynchronisationInfo(metadata_id);
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

function getNewVersionRight(metadata_id) {
    js.get(currentUrl + '/?option=com_easysdi_core&task=version.getNewVersionRight&metadata_id=' + metadata_id, function(data) {
        var response = js.parseJSON(data);
        if (response.canCreate === false) {
            js('#' + response.resource_id + '_new_linker').attr('class', 'disabled');
            js('#' + response.resource_id + '_new_linker').attr('style', 'color: #CBCBCB');

            var message = '';
            js.each(response.cause, function(k, cause) {
                message += '<b>' + cause.message + '</b>' + '<br/>' + cause.elements + '</br>';
            });

            var options = {title: message, html: true};
            js('#' + response.resource_id + '_new_linker').tooltip(options);
        } else {
            js('#' + response.resource_id + '_new_linker').removeAttr('style');
            js('#' + response.resource_id + '_new_linker').removeAttr('css');
            
            js('#' + response.resource_id + '_new_linker').on('click', function(){showNewVersionModal(response.resource_id);return false;});
        }
    });
}

function getSynchronisationInfo(metadata_id){
    js.get(currentUrl + '/?option=com_easysdi_catalog&task=metadata.getSynchronisationInfo&metadata_id=' + metadata_id, function(data) {
        var response = js.parseJSON(data);
        if (response.synchronized === true) {
            
            var message = Joomla.JText._('COM_EASYSDI_CORE_RESOURCES_SYNCHRONIZE_BY')+' '+response.synchronized_by+'<br/>'+ Joomla.JText._('COM_EASYSDI_CORE_RESOURCES_SYNCHRONIZE_THE') +' '+ response.lastsynchronization;
            var options = {title: message, html: true};
            js('#' + response.resource_id + '_sync_linker').tooltip(options);
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
        
        if(roles['hasChildren']==='false'){
            js('#assign_child_controls').hide();
        }else{
            js('#assign_child_controls').show();
        }
        showModal(version_id, 'assignmentModal');
    });
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
    getNewVersionRight(version_id)
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
        js(this).attr("href", newhref + js("select#" + resourceid + "_select").val());
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
function showDeleteModal(deleteUrl, version_id) {

    js.get(currentUrl + '/?option=com_easysdi_core&task=version.getCascadeDeleteChild&version_id=' + version_id, function(data) {
        var response = js.parseJSON(data);
        var body = buildDeletedTree(response.versions);
        js('#deleteModalChildrenList').html(body);
        js('#btn_delete').attr('href', deleteUrl);
        js('#deleteModal').modal('show');
    });
}

function buildDeletedTree(versions) {
    var body = '<ul>';

    js.each(versions, function(k, version) {
        body += '<li>' + version.resource_name + ' : ' + version.version_name + ' <a href="/index.php?option=com_easysdi_catalog&task=metadata.edit&id=' + version.metadata_id + '" target="_top"><i class="icon-edit"></i></a>';
        if (typeof version.children === 'undefined') {
            body += '</li>';
        } else {
            body += buildDeletedTree(version.children)
            body += '</li>';
        }


    });

    body += '</ul>'

    return body;
}

/**
 * 
 * @param {int} resource_id
 */
function showNewVersionModal(resource_id) {
    js.get(currentUrl + '/?option=com_easysdi_core&task=version.getInProgressChildren&resource=' + resource_id, function(data) {
        var response = js.parseJSON(data);
        if (response.total > 0) {
            var body = '<ul>';
            js.each(response.versions, function(k, version) {
                body += '<li>' + version.resource_name + ' : ' + version.version_name + ' <a href="/index.php?option=com_easysdi_catalog&task=metadata.edit&id=' + version.metadata_id + '" target="_top"><i class="icon-edit"></i></a></li>';
            });
            body += '</ul>';
            js('#createModalChildrenList').html(body);
            js('#createModal').modal('show');
        } else {
            var createUrl = currentUrl + '/?option=com_easysdi_core&task=version.create&resource=' + resource_id;
            window.location.href = createUrl;
        }
    });
}