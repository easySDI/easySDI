js = jQuery.noConflict();
var childrenTable, availablechildrenTable, parents, availablechildrenData, childrenData;
var availablechildrenData = [], childrenData = [], parentsData = [];

var lastCriteria = {length: 0};
js.fn.dataTableExt.afnFiltering.push(function(oSettings, aData, iDataIndex){
    return (oSettings.nTable.id != 'sdi-availablechildren' || lastCriteria.length === 0 || (lastCriteria[aData[2]] == aData[3])) ? true : false;
});

js(document).ready(function() {
    
    availablechildrenTable = js('#sdi-availablechildren').dataTable({
        //"bFilter": false,
        "bLengthChange": false,
        "oLanguage": {
            "sSearch": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_SEARCH'),
            "sZeroRecords": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_NORESULT'),
            "sInfo": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_SHOWING') + " _START_ " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_TO') + " _END_ " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_OF') + " _TOTAL_ " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_RECORDS'),
            "sInfoEmpty": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_SHOWING') + " 0 " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_TO') + " 0 " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_OF') + " 0 " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_RECORDS'),
            "sInfoFiltered": "(filtered from _MAX_ total records)"

            ,
            "oPaginate": {
                "sNext": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_NEXT'),
                "sPrevious": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_PREVIOUS')
            }
        },
        aaData: availablechildrenData,
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
            }},
            {
                aTargets: [8],
                mData: function(child){
                    return "<button type='button' id='sdi-availablechildbutton-"+child.id+"' class='btn btn-success btn-mini' onclick='addChild("+JSON.stringify(child)+");'><i class='icon-white icon-new'></i></button>";
                },
                sClass: 'center'
            }
            
        ],
        aaSorting: [[3, 'desc']]
    });
    
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
        if(this.value == 'all'){
            lastCriteria = {length: 0};
            availablechildrenTable.fnDraw();
        }
        else if(this.value == 'last'){
            var data = availablechildrenTable.fnGetData();
            js(data).each(function(i, row){
                if('undefined' === typeof lastCriteria[row.resource] || row.version > lastCriteria[row.resource]){
                    lastCriteria[row.resource] = row.version;
                    lastCriteria.length++;
                }
                
                if(i === js(data).length-1)
                    availablechildrenTable.fnDraw();
            });
        }
    });
    
    // clear criteria
    js('#clear-btn').on('click', function(){
        js('#jform_searchtype').val('');
        availablechildrenTable.fnFilter('', 4);
        js('#jform_searchid').val('');
        availablechildrenTable.fnFilter('', 1);
        js('#jform_searchname').val('');
        availablechildrenTable.fnFilter('', 2);
        js('#jform_searchstate').val('');
        availablechildrenTable.fnFilter('', 6);
        js('input[type=radio][name="jform[searchlast]"][value=all]').attr('checked', true);
        lastCriteria = {length: 0};
        availablechildrenTable.fnFilter('');
        availablechildrenTable.fnDraw();
        this.blur();
        return false;
    });

    childrenTable = js('#sdi-children').dataTable({
        "bLengthChange": false,
        "oLanguage": {
            "sSearch": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_SEARCH'),
            "sZeroRecords": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_NORESULT'),
            "sInfo": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_SHOWING') + " _START_ " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_TO') + " _END_ " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_OF') + " _TOTAL_ " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_RECORDS'),
            "sInfoEmpty": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_SHOWING') + " 0 " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_TO') + " 0 " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_OF') + " 0 " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_RECORDS'),
            "sInfoFiltered": "(filtered from _MAX_ total records)"

            ,
            "oPaginate": {
                "sNext": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_NEXT'),
                "sPrevious": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_PREVIOUS')
            }
        },
        aaData: childrenData,
        aoColumnDefs: [
            { bVisible: false, aTargets: [0], mData: 'id' },
            { aTargets: [1], mData: 'resource' },
            { bVisible: versioning, aTargets: [2], mData: 'version' },
            { aTargets: [3], mData: 'resourcetype' },
            { aTargets: [4], mData: function(child){
                    return Joomla.JText._(child.state, child.state);
            }},
            {
                aTargets: [5],
                bVisible: !isReadonly,
                mData: function(child){
                    return "<button type='button' id='sdi-childbutton-"+child.id+"' class='btn btn-warning btn-mini' onclick='deleteChild("+JSON.stringify(child)+");'><i class='icon-white icon-minus'></i></button>";
                },
                sClass: 'center'
            }
            
        ]
    });
    
    parents = js('#sdi-parents').dataTable({
        "bFilter": true,
        "bLengthChange": false,
        "oLanguage": {
            "sSearch": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_SEARCH'),
            "sZeroRecords": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_NORESULT'),
            "sInfo": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_SHOWING') + " _START_ " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_TO') + " _END_ " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_OF') + " _TOTAL_ " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_RECORDS'),
            "sInfoEmpty": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_SHOWING') + " 0 " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_TO') + " 0 " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_OF') + " 0 " + Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_RECORDS'),
            "sInfoFiltered": "(filtered from _MAX_ total records)"

            ,
            "oPaginate": {
                "sNext": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_NEXT'),
                "sPrevious": Joomla.JText._('COM_EASYSDI_CORE_DATATABLES_PREVIOUS')
            }
        },
        aaData: parentsData,
        aoColumnDefs: [
            { bVisible: false, aTargets: [0], mData: 'id' },
            { aTargets: [1], mData: 'resource' },
            { bVisible: versioning, aTargets: [2], mData: 'version' },
            { aTargets: [3], mData: 'resourcetype' },
            { aTargets: [4], mData: function(parent){
                    return Joomla.JText._(parent.state, parent.state);
            }}
            
        ]
    });
    
    /*js('#search-btn').on('click', function(){
        //var q = [];
        js('#searchForm :input').each(function(i, input){
            if('' !== js(input).val()){
                //q[js(input).attr('name')] = js(input).val();
                console.log(js(input).attr('name'));
                switch(js(input).attr('name')){
                    case 'jform[searchid]':
                        availablechildrenTable.fnFilter(js(input).val());
                        break;
                    
                    default:
                        
                }
                
            }
        });
        
        return false;
    });*/
});

function addChild(child) {
    childrenTable.fnAddData(child);
    availablechildrenTable.fnDeleteRow(js('#sdi-availablechildbutton-' + child.id).parent().parent()[0]);
}

function deleteChild(child) {
    availablechildrenTable.fnAddData(child);
    childrenTable.fnDeleteRow(js('#sdi-childbutton-' + child.id).parent().parent()[0]);
}

Joomla.submitbutton = function(task)
{
    if (task === 'version.save' || task === 'version.apply') {
        var results = [];
        var children = childrenTable.fnGetData();
        children.each(function(value) {
            results.push(value.id);
        });

        var r = JSON.stringify(results);

        js('#jform_selectedchildren').val(r);
    }
    
    var form = document.getElementById('adminForm');
    Joomla.submitform(task, document.getElementById('adminForm'));
};