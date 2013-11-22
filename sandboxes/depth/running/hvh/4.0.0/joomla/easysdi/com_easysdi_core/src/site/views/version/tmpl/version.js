js = jQuery.noConflict();
var childrenTable, availablechildrenTable, parents;
js(document).ready(function() {

    availablechildrenTable = js('#sdi-availablechildren').dataTable({
        "bFilter": false,
        "bLengthChange": false,
        "aoColumnDefs": [
            {"bVisible": false, "aTargets": [0]},
            {"bVisible": versioning, "aTargets": [2]}
        ],
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
        }
    });

    childrenTable = js('#sdi-children').dataTable({
        "bLengthChange": false,
        "aoColumnDefs": [
            {"bVisible": false, "aTargets": [0]},
            {"bVisible": versioning, "aTargets": [2]},
            {"sClass": "center", "aTargets": [5]}
        ],
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
        }
    });
    parents = js('#sdi-parents').dataTable({
        "bFilter": true,
        "bLengthChange": false,
        "aoColumnDefs": [
            {"bVisible": false, "aTargets": [0]},
            {"bVisible": versioning, "aTargets": [2]}
        ],
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
        }
    });


});

function addChild(child) {
    js('#sdi-children').dataTable().fnAddData([
        child.id,
        child.resource,
        child.version,
        child.resourcetype,
        Joomla.JText._(child.state),
        '<button type="button" id="sdi-childbutton-' + child.id + '" onClick="deleteChild(\'' + child.id + '\');" class="btn btn-warning btn-mini"><i class="icon-white icon-minus"></i></button>'

    ]);
}

function deleteChild(child) {
    childrenTable.fnDeleteRow(js('#sdi-childbutton-' + child).parent().parent()[0]);
}

Joomla.submitbutton = function(task)
{
    if (task === 'version.save') {
        var results = [];
        var children = childrenTable.fnGetData();
        children.each(function(value) {
            results.push(value[0]);
        });

        var r = JSON.stringify(results);

        js('#jform_selectedchildren').val(r);
    }
    Joomla.submitform(task, document.getElementById('adminForm'));
}