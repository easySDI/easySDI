js = jQuery.noConflict();
var childrenTable, availablechildrenTable, parents;
var availablechildrenData = [], childrenData = [], parentsData = [];

js(document).ready(function() {
    
    availablechildrenTable = js('#sdi-availablechildren').dataTable({
        "bFilter": false,
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
            { aTargets: [1], mData: 'resource' },
            { bVisible: versioning, aTargets: [2], mData: 'version' },
            { aTargets: [3], mData: 'resourcetype' },
            { aTargets: [4], mData: function(child){
                    return Joomla.JText._(child.state, child.state);
            }},
            {
                aTargets: [5],
                mData: function(child){
                    return "<button type='button' id='sdi-availablechildbutton-"+child.id+"' class='btn btn-success btn-mini' onclick='addChild("+JSON.stringify(child)+");'><i class='icon-white icon-new'></i></button>";
                },
                sClass: 'center'
            }
            
        ],
        aaSorting: [[2, 'desc']]
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
    Joomla.submitform(task, document.getElementById('adminForm'));
}