<script type="text/javascript">
js = jQuery.noConflict();

// #n# are used as placeholder
var Links = {
    resource: {
        edit: '<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resource.edit&id=#0#')?>'
    },
    actions: {
        metadata: {
            preview: {
                class: 'modal',
                href: 'index.php?option=com_easysdi_catalog&id=#0#&tmpl=component&view=sheet&preview=editor', //'<?php echo JRoute::_('index.php?option=com_easysdi_catalog&id=#0#&tmpl=component&view=sheet&preview=editor')?>',
                property: 'metadata',
                html: "<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_VIEW_METADATA')?>",
                rel: "{handler:'iframe',size:{x:600,y:700}}"
            },
            edit: {
                href: '<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit&id=#0#')?>',
                property: 'metadata',
                html: "<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_EDIT_METADATA')?>"
            },
            publish: {
                html: "<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_PUBLISH_METADATA')?>"
            },
            inprogress: {
                href: '<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.inprogress&id=#0#')?>',
                property: 'metadata',
                html: "<?php echo JText::_('COM_EASYSDI_CORE_INPROGRESS_ITEM')?>"
            },
            changepublishdate: {
                html: "<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_CHANGEPUBLISHEDDATE_METADATA')?>"
            },
            archive: {
                href: '<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.archive&id=#0#')?>',
                property: 'metadata',
                html: "<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_ARCHIVE_METADATA')?>"
            },
            assign: {
                html: "<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_ASSIGN_METADATA')?>"
            },
            notify: {
                href: '<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.notify&id=#0#')?>',
                property: 'metadata',
                html: "<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_NOTIFY_METADATA')?>"
            },
            synchronize: {
                href: '<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.synchronize&id=#0#')?>',
                property: 'metadata',
                html: "<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SYNCHRONIZE_METADATA')?>"
            }
        },
        management: {
            new_version: {
                html: "<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_NEW_VERSION')?>",
                href: '<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.create&resource=#0#')?>',
                property: 'resource'
            },
            relation: {
                html: "<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_RELATIONS')?>",
                href: '<?php echo JRoute::_("index.php?option=com_easysdi_core&task=version.edit&id=#0#")?>',
                property: 'version'
            },
            child_list: {
                html: "<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_CHILDREN_LIST')?> (<span>0</span>)",
                href: '<?php echo JRoute::_("index.php?option=com_easysdi_core&view=resources&parentid=#0#")?>',
                property: 'version'
            },
            application: {
                html: "<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_APPLICATIONS')?>",
                href: '<?php echo JRoute::_("index.php?option=com_easysdi_core&view=applications&resource=#0#")?>',
                property: 'resource'
            },
            diffusion: {
                html: "<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_DIFFUSION')?>",
                href: '<?php echo JRoute::_("index.php?option=com_easysdi_shop&task=diffusion.edit&id=#0#")?>',
                property: 'metadata'
            },
            view: {
                html: "<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_VIEW')?>",
                href: '<?php echo JRoute::_("index.php?option=com_easysdi_map&task=visualization.edit&id=#0#")?>',
                property: 'metadata'
            },
            delete_version: {
                html: "<i class='icon-remove'></i> <?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_DELETE_VERSION')?>"
            },
            delete_resource: {
                html: "<i class='icon-remove'></i> <?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_DELETE_RESOURCE')?>"
            },
            assignment_history: {
                html: "<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_ASSIGNMENT_HISTORY')?>",
                href: '<?php echo JRoute::_("index.php?option=com_easysdi_catalog&view=assignments&metadata=#0#")?>',
                property: 'metadata'
            }
        }
    },
    ajax: {
        child_number:   '<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.getChildren&parentId=#0#')?>',
        new_version:    '<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.getNewVersionRight&metadata_id=#0#')?>',
        synchronization: '<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.getSynchronisationInfo&metadata_id=#0#')?>',
        publish_right: '<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.getPublishRight&metadata_id=#0#')?>',
        inprogress_right: '<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.getParent&versionId=#0#&parentState=#1#')?>',
        delete_child: '<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.getCascadeDeleteChild&version_id=#0#')?>',
        get_roles: '<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.getRoles&versionId=#0#')?>',
        publicable_child: '<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.getCascadePublicableChild&version_id=#0#')?>',
        inprogress_child: '<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.getInProgressChildren&resource=#0#')?>'
    },
    modal: {
        delete: '<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.remove&id=#0#')?>'
    }
};

var Resources = (function(){
    
    function Resources(){
        var collection = [];
        
        this.add = function(resource){
            collection.push(resource);
        };
        
        this.get = function(id){
            if(arguments.length==0)
                return collection;
            
            for(var i in collection)
                if(collection[i].id == id)
                    return collection[i];
            
            return false;
        };
    }
    
    return Resources;
}());
var resources = new Resources();

// Reflect the metadatastate sys table
var metadataState = {
    INPROGRESS: 1,
    VALIDATED:  2,
    PUBLISHED:  3,
    ARCHIVED:   4,
    TRASHED:    5
};

var Metadata = (function(){
    
    function Metadata(id, name, state, stateName, publishDate){
        this.id = id;
        this.name = name;
        this.state = state;
        this.stateName = stateName;
        this.publishDate = publishDate;
    }
    
    return Metadata;
}());

var Version = (function(){
    
    function Version(id){
        var metadata = {};
        
        this.id = id;
        
        this.child_number = 0;
        
        this.metadata = function(id, name, state, stateName, publishDate){
            if(arguments.length>0)
                metadata = new Metadata(id, name, state, stateName, publishDate);
            
            return metadata;
        };
    }
    
    return Version;
}());

var Resource = (function(){
    
    function Resource(id, name, type){
        var versions = [];
        
        this.id = id;
        this.name = name;
        this.type = type;
        this.rights = {
            metadataEditor:         false,
            metadataResponsible:    false,
            resourceManager:        false,
            diffusionManager:       false,
            viewManager:            false
        };
        this.versioning = false;
        this.assignment = false;
        this.canBeChild = false;
        this.support = {
            relation:       false,
            application:    false,
            diffusion:      false,
            view:           false
        };
        
        this.version = function(){
            if(arguments.length === 0){
                console.log('ERROR');
                return false;
            }
            
            var version_id = Array.prototype.shift.apply(arguments);
            if(arguments.length === 0)
                return versions[version_id];
            else{
                var v = new Version(version_id);
                v.metadata(arguments[0], arguments[1], arguments[2], arguments[3], arguments[4]);
                versions.push(v);
                return versions;
            }
        };
        
        this.firstVersion = function(){
            return versions[0];
        };
        
        this.currentVersion = function(){
            if(js('select#'+this.id+'_select > option:selected').length>0){
                for(var i in versions)
                    if(versions[i].id == js('select#'+this.id+'_select > option:selected').val())
                        return versions[i];
            }else return this.firstVersion();
        };
        
        this.allVersions = function(){
            return versions;
        };
    }
    
    return Resource;
}());

<?php 
    $params = JComponentHelper::getParams('com_easysdi_catalog');
    $assignenabled = $params->get('assignenabled',1);
    
    foreach ($this->items as $item) : ?>
    var resource = new Resource(<?php echo $item->id;?>, '<?php echo addslashes($item->name); ?>', '<?php echo $item->resourcetype_name; ?>');
    <?php if($this->user->authorize($item->id, sdiUser::metadataeditor)): ?>resource.rights.metadataEditor = 1;<?php endif; ?>
    <?php if($this->user->authorize($item->id, sdiUser::metadataresponsible)): ?>resource.rights.metadataResponsible = 1;<?php endif; ?>
    <?php if($this->user->authorize($item->id, sdiUser::resourcemanager)): ?>resource.rights.resourceManager = 1;<?php endif; ?>
    <?php if($this->user->authorize($item->id, sdiUser::diffusionmanager)): ?>resource.rights.diffusionManager = 1;<?php endif; ?>
    <?php if($this->user->authorize($item->id, sdiUser::viewmanager)): ?>resource.rights.viewManager = 1;<?php endif; ?>
    <?php if($item->supportapplication): ?>resource.support.application = 1;<?php endif; ?>
    <?php if($item->supportdiffusion): ?>resource.support.diffusion = 1;<?php endif; ?>
    <?php if($item->supportrelation): ?>resource.support.relation = 1;<?php endif; ?>
    <?php if($item->supportview): ?>resource.support.view = 1;<?php endif; ?>
    <?php if($item->canbechild): ?>resource.canBeChild = 1;<?php endif; ?>
    <?php if($item->versioning): ?>resource.versioning = 1;<?php endif; ?>
    resource.assignment = <?php  echo $assignenabled; ?>;
    <?php foreach($item->metadata as $key => $metadata):?>
        resource.version(<?php echo $metadata->version;?>, <?php echo $metadata->id;?>, '<?php echo $metadata->name;?>', <?php echo $metadata->state;?>, '<?php echo JText::_($metadata->value);?>', '<?php echo $metadata->published;?>');
    <?php endforeach;?>
    resources.add(resource);
<?php endforeach; ?>

var buildDropDownItem = function(resource, type){
    var typeTab = type.split('.');
    var link = Links.actions[typeTab[0]][typeTab[1]];
    
    var value = '';
    switch(link.property){
        case 'metadata':
            value = resource.currentVersion().metadata().id;
            break;
            
        case 'version':
            value = resource.currentVersion().id;
            break;
            
        case 'resource':
            value = resource.id;
            break;
    }
    
    var li = js('<li></li>'),
        a = js('<a></a>')
                .attr('id', resource.id+'_'+typeTab[1])
                .html(link.html);
    
    if(link.href) a.attr('href', link.href.replace('#0#', value));
    if(link.class) a.addClass(link.class);
    if(link.rel) a.attr('rel', link.rel);
    
    return li.append(a);
};

var dropDown2HTML = function(dropdown){
    var ul = js('<ul></ul>').addClass('dropdown-menu');
    for(var i=0; i<dropdown.length; i++){
        if(dropdown[i].length==0) continue;
        if(i>0)
            ul.append(js('<li></li>').addClass('divider'));
        
        for(var j=0; j<dropdown[i].length; j++)
            ul.append(dropdown[i][j]);
    }
    return ul;
};

var buildMetadataDropDown = function(resource){
    var version = resource.currentVersion();
    var metadata = version.metadata();
    
    var div = js('<div></div>').addClass('btn-group'),
        a = js('<a></a>')
            .addClass('btn')
            .addClass('btn-success')
            .addClass('btn-small')
            .addClass('dropdown-toggle')
            .attr('data-toggle', 'dropdown')
            .html('<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_METADATA'); ?> '),
        span = js('<span></span>').addClass('caret');
    
    var dropdown = [];
    
    /* FIRST SECTION */
    var section = [];
    section.push(buildDropDownItem(resource, 'metadata.preview'));
    
    if(resource.rights.metadataEditor && js.inArray(metadata.state, [metadataState.INPROGRESS, metadataState.VALIDATED, metadataState.PUBLISHED])>-1)
        section.push(buildDropDownItem(resource, 'metadata.edit'));
    
    if(resource.rights.metadataResponsible)
        switch(metadata.state){
            case metadataState.VALIDATED:
                section.push(buildDropDownItem(resource, 'metadata.inprogress'));
                section.push(buildDropDownItem(resource, 'metadata.publish'));
                break;
                
            case metadataState.PUBLISHED:
                section.push(buildDropDownItem(resource, 'metadata.inprogress'));
                section.push(buildDropDownItem(resource, 'metadata.changepublishdate'));
                section.push(buildDropDownItem(resource, 'metadata.archive'));
                break;
                
            case metadataState.ARCHIVED:
                section.push(buildDropDownItem(resource, 'metadata.inprogress'));
                break;
        }
    
    dropdown.push(section);
    
    /* SECOND SECTION */
    section = [];
    
    if(resource.rights.metadataEditor && metadata.state==metadataState.INPROGRESS && resource.assignment == 1 ){
        section.push(buildDropDownItem(resource, 'metadata.assign'));
    }
    
    dropdown.push(section);
    
    /* THIRD SECTION */
    section = [];
    
    if(resource.rights.metadataResponsible && resource.support.relation){
        section.push(buildDropDownItem(resource, 'metadata.synchronize'));
    }
    
    dropdown.push(section);
    
    js('td#'+resource.id+'_resource_metadata_actions').empty().append(div.append(a.append(span)).append(dropDown2HTML(dropdown)));
};

var buildManagementDropDown = function(resource){
    var div = js('<div></div>').addClass('btn-group'),
        a = js('<a></a>')
            .addClass('btn')
            .addClass('btn-primary')
            .addClass('btn-small')
            .addClass('dropdown-toggle')
            .attr('data-toggle', 'dropdown')
            .html('<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_MANAGE'); ?> '),
        span = js('<span></span>').addClass('caret');
    
    var dropdown = [];
    
    /* FIRST SECTION */
    if(resource.rights.resourceManager){
        var section = [];
        
        if(resource.versioning)
            section.push(buildDropDownItem(resource, 'management.new_version'));
        
        if(resource.support.relation || resource.canBeChild)
            section.push(buildDropDownItem(resource, 'management.relation'));
        
        if(resource.support.relation)
            section.push(buildDropDownItem(resource, 'management.child_list'));
        
        if(resource.support.application)
            section.push(buildDropDownItem(resource, 'management.application'));
        
        dropdown.push(section);
    }
    
    /* SECOND SECTION */
    if(resource.rights.diffusionManager && resource.support.diffusion){
        var section = [];
        
        section.push(buildDropDownItem(resource, 'management.diffusion'));
        
        dropdown.push(section);
    }
    
    /* THIRD SECTION */
    if(resource.rights.viewManager && resource.support.view){
        var section = [];
        
        section.push(buildDropDownItem(resource, 'management.view'));
        
        dropdown.push(section);
    }
    
    /* FOURTH SECTION */
    if(resource.rights.resourceManager){
        var section = [];
        
        section.push(buildDropDownItem(resource, resource.versioning && resource.allVersions().length>1 ? 'management.delete_version' : 'management.delete_resource'));
        
        dropdown.push(section);
    }
    
    /* FIFTH SECTION */
    if(resource.rights.metadataEditor  && resource.assignment == 1){
        var section = [];
        
        section.push(buildDropDownItem(resource, 'management.assignment_history'));
        
        dropdown.push(section);
    }
    
    js('td#'+resource.id+'_resource_management_actions').empty().append(div.append(a.append(span)).append(dropDown2HTML(dropdown)));
}

var buildStatusCell = function(resource){
    if(resource.versioning){
        var select = js('<select></select>')
                .attr('id', resource.id+'_select')
                .addClass('inputbox')
                .addClass('version-status');
        
        var versions = resource.allVersions();
        
        js(versions).each(function(i, version){
            if('undefined' !== typeof version){
                var metadata = version.metadata();
                var option = js('<option></option>')
                        .val(version.id)
                        .html(metadata.name+' : '+metadata.stateName);

                select.append(option);
            }
        });
        
        js('td#'+resource.id+'_resource_versions').empty().append(select);
    }
    else{
        var version = resource.firstVersion();
        var metadata = version.metadata();
        
        var span = js('<span></span>')
                .addClass('label')
                .html(metadata.stateName);
        switch(metadata.state){
            case 1: span.addClass('label-warning');break;
            case 2:
            case 5:
                span.addClass('label-info');break;
            case 3: span.addClass('label-success');break;
            case 4: span.addClass('label-inverse');break;
        }
        js('td#'+resource.id+'_resource_versions').empty().append(span);
    }
};

var enableResourceLink = function(resource){
    if(resource.rights.resourceManager){
        var a = js('<a></a>')
                .html(resource.name)
                .attr('href', Links.resource.edit.replace('#0#', resource.id));
        js('td#'+resource.id+'_resource_name').empty().append(a);
    }
    else js('td#'+resource.id+'_resource_name').empty().html(resource.name);
};

// Retrieves resource's id from HTML Element's id
var getResourceId = function(element){
    var tabId = js(element).attr('id').split('_');
    return tabId[0];
};

// Retrieves metadata's id from HTML Element's id reading the Resources collection
var getMetadataId = function(element){
    return resources.get(getResourceId(element)).currentVersion().metadata().id;
};

// Retrieves version's id from HTML Element's id reading the Resources collection
var getVersionId = function(element){
    return resources.get(getResourceId(element)).currentVersion().id;
};

var buildVersionsTree = function(versions){
    var ul = js('<ul></ul>');
    
    js.each(versions, function(i, version){
        var li = js('<li></li>').html(version.resource_name+' : '+version.version_name);
        var a = js('<a></a>')
                .attr('href', Links.actions.metadata.edit.href.replace('#0#', version.metadata_id))
                .attr('target', '_top');
        var icon = js('<i></i>').addClass('icon-edit');
        
        li.append(a.append(icon));
        
        if('undefined' !== typeof version.children)
            li.append(buildVersionsTree(version.children));
        
        ul.append(li);
    });
    
    return ul;
};

var resetSearch = function(){
    js('#filter_resourcetype option:first, #filter_resourcetype_children option:first').attr('selected', true);
    js('#filter_status option:first, #filter_status_children option:first').attr('selected', true);
    js('#filter_search, #filter_search_children').val('');
    js('form.form-search').submit();
    return false;
};

// Set the child's number of the current version
var getChildNumber = function(element){
    if(element.length === 0) return;
    
    var resource = resources.get(getResourceId(element));
    var version = resource.currentVersion();
    try{
        js.ajax({
            cache: false,
            type: 'GET',
            url: Links.ajax.child_number.replace('#0#', version.id)
        }).done(function(data){
            try{
                var response = js.parseJSON(data);
                if(response.success == "true"){
                    js(element).find('span').html(response.num);
                    version.child_number = response.num;
                    
                    if(resource.rights.metadataResponsible)
                        getSynchronizationInfo(js('a#'+resource.id+'_synchronize'));
                }
            }
            catch(e){
                if(window.console){
                    console.log(e);
                    console.log(data);
                }
            }
        });
    }
    catch(e){
        console.warn('Catch error');
        setTimeout(function(){getChildNumber(element)}, 50);
    }
};

// Checks if new version link should be available for the current resource
var getNewVersionRight = function(element){
    if(element.length === 0) return;
    
    js.ajax({
        cache: false,
        type: 'GET',
        url: Links.ajax.new_version.replace('#0#', getMetadataId(element))
    }).done(function(data){
        try{
            var response = js.parseJSON(data);
            if(response.canCreate === false){
                var message = '';
                js.each(response.cause, function(i, cause){
                    message += '<b>'+cause.message+'</b><br/>'+cause.elements+'<br/>';
                });

                js(element)
                        .addClass('disabled')
                        .css('color', '#cbcbcb')
                        .tooltip({title: message, html: true})
                        .on('click', function(){return false;});
            }
            else
                js(element)
                    .css('color', 'inherit')
                    .removeClass('disabled')
                    .tooltip('destroy')
                    .on('click', function(){return true;});
        }
        catch(e){
            if(window.console){
                console.log(e);
                console.log(data);
            }
        }
    });
};

var getSynchronizationInfo = function(element){
    if(element.length === 0) return;
    
    var resource = resources.get(getResourceId(element));
    var version = resource.currentVersion();
    var metadata = version.metadata();
    
    if(version.child_number > 0){
        js.ajax({
            cache: false,
            type: 'GET',
            url: Links.ajax.synchronization.replace('#0#', metadata.id)
        }).done(function(data){
            try{
                var response = js.parseJSON(data);

                if(response.synchronized === true){
                    var message = '<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SYNCHRONIZE_BY')?> '+response.synchronized_by+'<br/><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SYNCHRONIZE_THE')?> '+response.lastsynchronization;
                    js(element)
                            .removeClass('disabled')
                            .css('color', 'inherit')
                            .tooltip({title: message, html: true})
                            .on('click', function(){return true;});
                }
            }
            catch(e){
                if(window.console){
                    console.log(e);
                    console.log(data);
                }
            }
        });
    }
    else{
        js(element)
                .addClass('disabled')
                .css('color', '#cbcbcb')
                .tooltip({title: "<?php echo JText::_('COM_EASYSDI_CORE_NOT_SYNCHRONIZABLE')?>", html: true})
                .on('click', function(){return false;});
    }
};

var getPublishRight = function(element){
    if(element.length === 0) return;
    
    var metadata_id = getMetadataId(element);
    js.ajax({
        cache: false,
        type: 'GET',
        url: Links.ajax.publish_right.replace('#0#', metadata_id)
    }).done(function(data){
        try{
            var response = js.parseJSON(data);
            if(response !== null && response.canPublish>0)
                js(element)
                    .addClass('disabled')
                    .css('color', '#cbcbcb')
                    .tooltip({title: "<?php echo JText::_('COM_EASYSDI_CORE_UNPUBLISHED_OR_UNVALIDATED_CHILDREN')?>", html: true})
                    .off('click');
            else
                js(element)
                    .removeClass('disabled')
                    .css('color', 'inherit')
                    .tooltip('destroy')
                    .on('click', function(){showPublishModal(this);return false;});
        }
        catch(e){
            if(window.console){
                console.log(e);
                console.log(data);
            }
        }
    });
};

var getSetInProgressRight = function(element){
    if(element.length === 0) return;
    
    js.ajax({
        cache: false,
        type: 'GET',
        url: Links.ajax.inprogress_right.replace('#0#', getVersionId(element)).replace('#1#', metadataState.PUBLISHED)
    }).done(function(data){
        try{
            var response = js.parseJSON(data);
            if(response.num>0){
                js(element)
                        .attr('class', 'disabled')
                        .css('color', '#cbcbcb')
                        .tooltip({title: "<?php echo JText::_('COM_EASYSDI_CORE_HAS_PUBLISHED_PARENT')?>", html: true})
                        .on('click', function(){ return false;});
            }
            else
                js(element)
                        .removeClass('disabled')
                        .css('color', 'inherit')
                        .tooltip('destroy')
                        .on('click', function(){ return true;});
        }
        catch(e){
            if(window.console){
                console.log(e);
                console.log(data);
            }
        }
    });
};

var showModal = function(id, modalId){
    modalId = modalId || 'publishModal';
    js('html, body').animate({scrollTop: 0}, 'slow');
    js('#'+modalId+' input[name^="id"]').val(id);
    js('#'+modalId).modal('show');
};

var showDeleteModal = function(element){
    if(element.length === 0) return;
    
    var version_id = getVersionId(element);
    js.ajax({
        cache: false,
        type: 'GET',
        url: Links.ajax.delete_child.replace('#0#', version_id)
    }).done(function(data){
        try{
            var response = js.parseJSON(data);
            var ul = buildVersionsTree(response.versions);
            js('#deleteModalChildrenList').html(ul);
            js('#btn_delete').attr('href', Links.modal.delete.replace('#0#', version_id));
            js('#deleteModal').modal('show');
        }
        catch(e){
            if(window.console){
                console.log(e);
                console.log(data);
            }
        }
    });
};

var showAssignmentModal = function(element){
    var resource = resources.get(getResourceId(element));
    var version = resource.currentVersion();
    
    js.ajax({
        cache: false,
        type: 'GET',
        url: Links.ajax.get_roles.replace('#0#', version.id)
    }).done(function(data){
        var roles = js.parseJSON(data);
        js('#assigned_to').empty();
        for(var user_id in roles[4].users)
            js('#assigned_to').append(js('<option></option>').val(user_id).html(roles[4].users[user_id]));
        js('#assigned_to').trigger('liszt:updated');
        
        if(roles['hasViralChildren']==='false'){
            js('#assign_child_controls').hide();
        }else{
            js('#assign_child_controls').show();
        }
        showModal(version.metadata().id, 'assignmentModal');
    });
};

var showPublishModal = function(element){
    var resource = resources.get(getResourceId(element));
    var version = resource.currentVersion();
    var metadata = version.metadata();
    
    js.ajax({
        cache: false,
        type: 'GET',
        url: Links.ajax.publicable_child.replace('#0#', version.id)
    }).done(function(data) {
        try{
            var response = js.parseJSON(data);
            js('#publishModalChildrenList').html(buildVersionsTree(response.versions));

            if('undefined' !== typeof metadata.publishDate && '0000-00-00 00:00:00' !== metadata.publishDate){
                var datetime = metadata.publishDate.split(' ');
                js('#publishModal #published').val(datetime[0]);
            }

            if(js(response.versions).length){
                js('#publishModal #viral').val(1);
            }

            showModal(metadata.id);
        }
        catch(e){
            if(window.console){
                console.log(e);
                console.log(data);
            }
        }
    });
    return false;
};

/*var showNewVersionModal = function(resource_id){
    js.get(Links.ajax.inprogress_child.replace('#0#', resource_id), function(data) {
        var response = js.parseJSON(data);
        if (response.total > 0) {
            js('#createModalChildrenList').html(buildVersionsTree(response.versions));
            js('#createModal').modal('show');
            return false;
        }
    });
};*/

var buildActionsCell = function(resource, reload){
    reload = reload || false;
    
    buildMetadataDropDown(resource);
    buildManagementDropDown(resource);
    
    // Performs some action on dropdowns links initialisation
    /*if(!reload){ // for all lines
        js('a[id$=_child_list]').each(function(){getChildNumber(this);});
        js('a[id$=_new_version]').each(function(){getNewVersionRight(this);});
        js('a[id$=_publish]').each(function(){getPublishRight(this);});
        js('a[id$=_inprogress]').each(function(){getSetInProgressRight(this);});
        SqueezeBox.assign(js('a[id$=_preview]'));
    }
    else{*/ // for the re-generated line only
        getChildNumber(js('a#'+resource.id+'_child_list'));
        getNewVersionRight(js('a#'+resource.id+'_new_version'));
        getPublishRight(js('a#'+resource.id+'_publish'));
        getSetInProgressRight(js('a#'+resource.id+'_inprogress'));
        SqueezeBox.assign(js('a#'+resource.id+'_preview'));
    //}
};

js(document).ready(function(){
    
    // Build resource's lines
    js(resources.get()).each(function(id, resource){
        if('undefined' !== typeof resource){
            enableResourceLink(resource);
            js('td#'+resource.id+'_resource_type').empty().html(resource.type);
            buildStatusCell(resource);
            
            buildActionsCell(resource);
        }
    });
    
    // Set events
    js(document).on('click', '#search-reset', resetSearch);
    
    js(document).on('click', 'a[id$=_delete_version], a[id$=_delete_resource]', function(){showDeleteModal(this);return false;});
    
    js(document).on('click', 'a[id$=_assign]', function(){showAssignmentModal(this);return false;});
    
    js(document).on('click', 'a[id$=_changepublishdate]', function(){showPublishModal(this)});
    
    // Fix action's link style
    js(document).on('hover', 'td[id$=_actions] a', function(){js(this).css('cursor', 'pointer')});
    
    // Fix version's select style and event
    js('td[id$=_resource_versions] > select')
            .chosen()
            .on('change', function(){buildActionsCell(resources.get(getResourceId(this)));})
            ;
    
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
});
</script>