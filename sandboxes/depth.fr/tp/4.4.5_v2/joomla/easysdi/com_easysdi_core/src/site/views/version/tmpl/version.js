js = jQuery.noConflict();
var childrenTable, availablechildrenTable, parents,
    tmpAdded = [], tmpRemoved = [], searchlast = 'all';

var lastCriteria = {length: 0};
js.fn.dataTableExt.afnFiltering.push(function(oSettings, aData, iDataIndex){
    return (oSettings.nTable.id != 'sdi-availablechildren' || lastCriteria.length === 0 || (lastCriteria[aData[2]] == aData[3])) ? true : false;
});

js(document).ready(function() {

    var dtDefaultSettings = {
        bLengthChange: false,
        bProcessing: true,
        bServerSide: true,
        oLanguage: {
            sUrl: baseUrl + 'option=com_easysdi_core&task=proxy.run&url='+encodeURI('http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/'+dtLang+'.json')
        },
        deferRender: true
    };
    
    availablechildrenTable = js('#sdi-availablechildren').dataTable(js.extend(true, {}, dtDefaultSettings, {
        sAjaxSource: baseUrl+'option=com_easysdi_core&task=version.getAvailableChildren4DT',
        fnServerParams: function(aoData){
            aoData.push({ name: 'version', value: version });
            aoData.push({ name: 'resourcetypechild', value: resourcetypechild });
            aoData.push({ name: 'inc', value: tmpRemoved.toString()});
            aoData.push({ name: 'exc', value: tmpAdded.toString()});
            aoData.push({ name: 'searchlast', value: searchlast});
        },
        aoColumnDefs: [
            { bVisible: false, aTargets: [0], mData: 'id' },
            { bVisible: false, aTargets: [1], mData: 'guid' },
            { aTargets: [2], mData: 'resource' },
            { bVisible: versioning, aTargets: [3], mData: 'version' },
            { aTargets: [4], mData: 'resourcetype_id', bVisible: false },
            { aTargets: [5], mData: 'resourcetype' },
            { aTargets: [6], mData: 'metadatastate_id', bVisible: false },
            { aTargets: [7], mData: function(child){
                    return Joomla.JText._(child.state, child.state);
            }, bSearchable: false},
            { aTargets: [8], mData: function(child){
                    return "<button type='button' id='sdi-availablechildbutton-"+child.id+"' class='btn btn-success btn-mini' onclick='addChild("+JSON.stringify(child).replace(/'/g, ' ')+");'><i class='icon-white icon-new'></i></button>";
                }, sClass: 'center', bSearchable: false, bVisible: !isReadonly }
        ],
        aaSorting: [[3, 'desc']]
    }));
    
    // apply search criteria on available children table
    js('#jform_searchtype').on('change', function(){
        availablechildrenTable.fnFilter(this.value, 4);
    });

    js('#jform_searchid').on('keyup change', function(){
        availablechildrenTable.fnFilter(this.value, 1);
    });

    js('#jform_searchname').on('keyup change', function(){
        availablechildrenTable.fnFilter(this.value, 2);
    });
    
    js('#jform_searchstate').on('change', function(){
        availablechildrenTable.fnFilter(this.value, 6);
    });
    
    js('input[type=radio][name="jform[searchlast]"]').on('change', function(){
        searchlast = this.value;
        availablechildrenTable.fnDraw();
        
    });
    
    // clear criteria
    js('#clear-btn').on('click', function(){
        js('#jform_searchtype').val('').trigger('liszt:updated');
        availablechildrenTable.fnFilter('', 4);
        js('#jform_searchid').val('');
        availablechildrenTable.fnFilter('', 1);
        js('#jform_searchname').val('');
        availablechildrenTable.fnFilter('', 2);
        js('#jform_searchstate').val('').trigger('liszt:updated');
        availablechildrenTable.fnFilter('', 6);
        js('input[type=radio][name="jform[searchlast]"][value=all]').attr('checked', true);
        lastCriteria = {length: 0};
        availablechildrenTable.fnFilter('');
        //availablechildrenTable.fnDraw();
        this.blur();
        return false;
    });

    childrenTable = js('#sdi-children').dataTable(js.extend(true, {}, dtDefaultSettings, {
        sAjaxSource: baseUrl+'option=com_easysdi_core&task=version.getChildren4DT',
        fnServerParams: function(aoData){
            aoData.push({ name: 'version', value: version });
            aoData.push({ name: 'inc', value: tmpAdded.toString()});
            aoData.push({ name: 'exc', value: tmpRemoved.toString()});
        },
        aoColumnDefs: [
            { bVisible: false, aTargets: [0], mData: 'id' },
            { bVisible: false, aTargets: [1], mData: 'guid' },
            { aTargets: [2], mData: 'resource' },
            { bVisible: versioning, aTargets: [3], mData: 'version' },
            { aTargets: [4], mData: 'resourcetype_id', bVisible: false },
            { aTargets: [5], mData: 'resourcetype' },
            { aTargets: [6], mData: 'metadatastate_id', bVisible: false },
            { aTargets: [7], mData: function(child){
                    return Joomla.JText._(child.state, child.state);
            }, bSearchable: false},
            { aTargets: [8], mData: function(child){
                    return "<button type='button' id='sdi-childbutton-"+child.id+"' class='btn btn-warning btn-mini' onclick='deleteChild("+JSON.stringify(child).replace(/'/g, ' ')+");'><i class='icon-white icon-minus'></i></button>";
                }, sClass: 'center', bSearchable: false, bVisible: !isReadonly }
        ]
    }));
    
    parents = js('#sdi-parents').dataTable(js.extend(true, {}, dtDefaultSettings, {
        "bFilter": true,
        sAjaxSource: baseUrl+'option=com_easysdi_core&task=version.getParents4DT',
        fnServerParams: function(aoData){
            aoData.push({ name: 'version', value: version });
        },
        aoColumnDefs: [
            { bVisible: false, aTargets: [0], mData: 'id' },
            { aTargets: [1], mData: 'resource' },
            { bVisible: versioning, aTargets: [2], mData: 'version' },
            { aTargets: [3], mData: 'resourcetype' },
            { aTargets: [4], mData: function(parent){
                    return Joomla.JText._(parent.state, parent.state);
            }, bSearchable: false}
            
        ]
    }));
    
    // to avoid double click event under IE !!
    js('#toolbar button[onclick]')
        .removeAttr('onclick')
        .on('click', function(){
            var task = 'version.'+js(this).closest('div').attr('id').replace('toolbar-', '');
            
            if(task === 'version-cancel'){
                js('#jform_childrentoadd').val();
                js('#jform_childrentoremove').val();
            }
            
            js('input[name=task]').val(task);
        });

    /*Joomla.submitbutton = function(task)
    {
        if (task === 'version.save' || task === 'version.apply') {
            js('#jform_childrentoremove').val(JSON.stringify(tmpRemoved));
            js('#jform_childrentoadd').val(JSON.stringify(tmpAdded));
        }

        Joomla.submitform(task, document.getElementById('adminForm'));
    };*/
});

var addChild = function(child){
    if(tmpRemoved.indexOf(child.id) > -1)
        tmpRemoved.splice(tmpRemoved.indexOf(child.id), 1);
    else if(tmpAdded.indexOf(child.id) === -1)
        tmpAdded.push(child.id);
    
    availablechildrenTable.fnDraw();
    childrenTable.fnDraw();
    js('#jform_childrentoadd').val(JSON.stringify(tmpAdded));
};

var deleteChild = function(child){
    if(tmpAdded.indexOf(child.id) > -1)
        tmpAdded.splice(tmpAdded.indexOf(child.id), 1);
    else if(tmpRemoved.indexOf(child.id) === -1)
        tmpRemoved.push(child.id);
    
    childrenTable.fnDraw();
    availablechildrenTable.fnDraw();
    js('#jform_childrentoremove').val(JSON.stringify(tmpRemoved));
};