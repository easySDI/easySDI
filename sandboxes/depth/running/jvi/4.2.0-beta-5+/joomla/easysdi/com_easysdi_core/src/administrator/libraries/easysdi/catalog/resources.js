js = jQuery.noConflict();
var currentUrl = location.protocol + '//' + location.host + location.pathname;

// Reflect the metadatastate sys table
var metadataState = {
    INPROGRESS: 1,
    VALIDATED:  2,
    PUBLISHED:  3,
    ARCHIVED:   4,
    TRASHED:    5
};

var resetSearch = function(){
    js('#filter_resourcetype option:first, #filter_resourcetype_children option:first').attr('selected', true);
    js('#filter_status option:first, #filter_status_children option:first').attr('selected', true);
    js('#filter_search, #filter_search_children').val('');
    js('form.form-search').submit();
    return false;
};

var getResourceId = function(element, attribute){
    attribute = attribute || 'id';
    //if(typeof js(element).attr(attribute) === 'undefined') console.log(attribute);
    var tabId = js(element).attr(attribute).split('_');
    return tabId[0];
};

var getMetadataId = function(element, attribute){
    var resource_id = getResourceId(element, attribute);
    return js('select#'+resource_id+'_select option:selected').val();
};

var getVersionId = function(element, attribute){
    var resource_id = getResourceId(element, attribute);
    return js('select#'+resource_id+'_select option:selected').attr('rel');
};

js(document).ready(function() {
    
    js('li[id$=_child_list]').each(function(){
        var version_id = getVersionId(this, 'id');
        getChildNumer(version_id);
    });
    
    js('a[id$=_new_linker]').each(function(){
        var metadata_id = getMetadataId(this, 'id');
        getNewVersionRight(metadata_id);
    });
    
    js('a[class$=_sync_linker]').each(function(){
        var metadata_id = getMetadataId(this, 'class'),
            version_id = getVersionId(this, 'class');
        getSynchronisationInfo(this, metadata_id, version_id);
    });
    
    js('a[id$=_publish_linker]').each(function(){
        var metadata_id = getMetadataId(this, 'id');
        getPublishRight(this, metadata_id);
    });
    
    js('a[class$=_inprogress_linker]').each(function(){
        var version_id = getVersionId(this, 'class');
        getSetInProgressRight(this, version_id);
    });
    
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
    
    js('#search-reset').on('click', resetSearch);
    
    js(document).on('click', 'a[id$=_deleter]', function(){
        showDeleteModal(js(this).attr('rel'));
        return false;
    });
    
    js(document).on('change', 'select.version-status', function(){
        var resource_id = getResourceId(this, 'id'),
            metadata_id = getMetadataId(this, 'id'),
            version_id = getVersionId(this, 'id');
        
        changeRelationLink(resource_id, version_id);
        changeChildLink(resource_id, metadata_id);
        
        getChildNumer(version_id);
        getNewVersionRight(metadata_id);
        getPublishRight(js('a#'+resource_id+'_publish_linker'), metadata_id);
        getSynchronisationInfo(js('a.'+resource_id+'_sync_linker'), metadata_id, version_id);
        getSetInProgressRight(js('a.'+resource_id+'_inprogress_linker'), version_id);

        // change delete link
        js('a#'+resource_id+'_deleter').attr('rel', version_id);
    });

});

var getSetInProgressRight = function(element, version_id){
    if(element.length == 0) return;
    
    js.get(currentUrl + '/?option=com_easysdi_core&task=version.getParent&versionId=' + version_id+'&parentState='+metadataState.PUBLISHED, function(data){
        var response = js.parseJSON(data);
        if(response.num>0){
            js(element)
                    .attr('class', 'disabled')
                    .attr('style', 'color: #cbcbcb')
                    .tooltip({title: Joomla.JText._('COM_EASYSDI_CORE_HAS_PUBLISHED_PARENT'), html: true})
                    .on('click', function(){ return false;});
        }
        else{
            js(element)
                    .removeClass('disabled')
                    .removeAttr('style')
                    .tooltip('destroy')
                    .on('click', function(){ return true;});
        }
    });
};

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
            var message = '';
            js.each(response.cause, function(k, cause) {
                message += '<b>' + cause.message + '</b>' + '<br/>' + cause.elements + '</br>';
            });

            js('#' + response.resource_id + '_new_linker')
                    .addClass('disabled')
                    .attr('style', 'color: #CBCBCB')
                    .tooltip({title: message, html: true});
        } else {
            js('#' + response.resource_id + '_new_linker')
                    .removeAttr('style')
                    .removeAttr('class')
                    .tooltip('destroy')
                    .on('click', function(){showNewVersionModal(response.resource_id);return false;});
        }
    });
}

function getPublishRight(element, metadata_id){
    if(element.length == 0) return;
    
    js.get(currentUrl+'/?option=com_easysdi_core&task=version.getPublishRight&metadata_id='+metadata_id, function(data){
        var response = js.parseJSON(data);
        if(response.canPublish>0){
            js(element)
                    .attr('class', 'disabled')
                    .attr('style', 'color: #cbcbcb')
                    .tooltip({title: Joomla.JText._('COM_EASYSDI_CORE_UNPUBLISHED_OR_UNVALIDATED_CHILDREN'), html: true})
                    .off('click');
        }
        else{
            js(element)
                    .removeClass('disabled')
                    .removeAttr('style')
                    .tooltip('destroy')
                    .on('click', function(){showPublishModal(metadata_id, response.id);return false;});
        }
    });
}

function getSynchronisationInfo(element, metadata_id, version_id){
    if(element.length == 0) return;
    
    // check the number of resource's children - if no children, no right to synchronize
    js.get(currentUrl + '/?option=com_easysdi_core&task=version.getChildren&parentId=' + version_id, function(data) {
        var response = js.parseJSON(data);
        if (response.success == 'true') {
            if (response.num > 0) {
                js.get(currentUrl + '/?option=com_easysdi_catalog&task=metadata.getSynchronisationInfo&metadata_id=' + metadata_id, function(data) {
                    var response = js.parseJSON(data);
                    if (response.synchronized === true) {

                        var message = Joomla.JText._('COM_EASYSDI_CORE_RESOURCES_SYNCHRONIZE_BY')+' '+response.synchronized_by+'<br/>'+ Joomla.JText._('COM_EASYSDI_CORE_RESOURCES_SYNCHRONIZE_THE') +' '+ response.lastsynchronization;
                        js(element)
                                .tooltip({title: message, html: true})
                                .on('click', function(){return true;});
                    }
                });
            } else {
                js(element)
                    .attr('class', 'disabled')
                    .attr('style', 'color: #cbcbcb')
                    .tooltip({title: Joomla.JText._('COM_EASYSDI_CORE_NOT_SYNCHRONIZABLE_CAUSE_HAS_NO_CHILDREN'), html: true})
                    .on('click', function(){return false;});
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
/*function showModal(id) {
    js('html, body').animate({scrollTop: 0}, 'slow');
    js('input[name^="id"]').val(id);
    js('#publishModal').modal('show');
}*/


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

function showPublishModal(id, version_id, publishDate){
    js.get(currentUrl + '/?option=com_easysdi_core&task=version.getCascadeChild&version_id=' + version_id, function(data) {
        var response = js.parseJSON(data);
        var body = buildDeletedTree(response.versions);
        js('#publishModalChildrenList').html(body);
        
        if('undefined' !== typeof publishDate){
            var datetime = publishDate.split(' ');
            js('#publishModal #published').val(datetime[0]);
        }
        
        if(js(response.versions).length){
            js('#publishModal #viral').val(1);
        }
        
        showModal(id);
    });
    return false;
}

/**
 * Change link from relation link
 * 
 * @param {int} resource_id
 * @param {int} version_id
 */
function changeRelationLink(resource_id, metadata_id) {
    js('.' + resource_id + '_linker', '.' + resource_id + '_inprogress_linker').each(function() {
        var href = js(this).attr("href");
        var i = href.lastIndexOf("/");
        var newhref = href.substring(0, i + 1);
        js(this).attr("href", newhref + metadata_id);
    });
}

/**
 * Change link from child list link
 * 
 * @param {int} resource_id
 * @param {int} metadata_id
 */
function changeChildLink(resource_id, metadata_id) {
    js('#' + resource_id + '_child_linker').attr('href', '/resources?parentid=' + metadata_id);
}

/**
 * Show delete modal
 * 
 * @param {string} deleteUrl
 */
function showDeleteModal(version_id) {
    js.get(currentUrl + '/?option=com_easysdi_core&task=version.getCascadeDeleteChild&version_id=' + version_id, function(data) {
        var response = js.parseJSON(data);
        var body = buildDeletedTree(response.versions);
        js('#deleteModalChildrenList').html(body);
        js('#btn_delete').attr('href', location.host+'/index.php?option=com_easysdi_core&task=version.remove&id='+version_id);
        js('#deleteModal').modal('show');
    });
    return false;
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