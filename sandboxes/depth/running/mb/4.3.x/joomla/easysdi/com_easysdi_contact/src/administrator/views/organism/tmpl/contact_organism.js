var dtLang, lblYes, lblNo, siteRoot, orgId, invalidFormMsg;

Joomla.submitbutton = function (task)
{
    if (task == 'organism.cancel' || document.formvalidator.isValid(document.id('organism-form'))) {
        Joomla.submitform(task, document.getElementById('organism-form'));
    }
    else {
        alert(invalidFormMsg);
    }
}

function fnCreateSelect(aData)
{
    var r = '<select><option value=""></option>', i, iLen = aData.length;
    for (i = 0; i < iLen; i++)
    {
        r += '<option value="' + aData[i] + '">' + aData[i] + '</option>';
    }
    return r + '</select>';
}

function initAddressByType(type)
{
    var elem = document.getElementById('jform_' + type + '_sameascontact1');
    if (elem.checked == true)
    {
        disableAddressType(true, type);
    }
}

function disableAddressType(disable, type)
{
    var elem = document.getElementById('organism-form').elements;
    for (var i = 0; i < elem.length; i++)
    {
        var tofind = 'jform[' + type + '_';
        if (elem[i].getAttribute('name') != null) {
            if (elem[i].getAttribute('name').indexOf(tofind) != -1
                    && elem[i].getAttribute('name').indexOf('sameascontact') == -1
                    && elem[i].getAttribute('type') != 'hidden')
            {
                elem[i].disabled = disable;
                elem[i].value = '';
            }
        }
    }
}

jQuery(document).ready(function () {


    /*
     * Function: fnGetColumnData
     * Purpose:  Return an array of table values from a particular column.
     * Returns:  array string: 1d data array
     * Inputs:   object:oSettings - dataTable settings object. This is always the last argument past to the function
     *           int:iColumn - the id of the column to extract the data from
     *           bool:bUnique - optional - if set to false duplicated values are not filtered out
     *           bool:bFiltered - optional - if set to false all the table data is used (not only the filtered)
     *           bool:bIgnoreEmpty - optional - if set to false empty values are not filtered from the result array
     * Author:   Benedikt Forchhammer <b.forchhammer /AT\ mind2.de>
     */
    jQuery.fn.dataTableExt.oApi.fnGetColumnData = function (oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty) {

        // check that we have a column id
        if (typeof iColumn == "undefined")
            return new Array();
        // by default we only want unique data
        if (typeof bUnique == "undefined")
            bUnique = true;
        // by default we do want to only look at filtered data
        if (typeof bFiltered == "undefined")
            bFiltered = true;
        // by default we do not want to include empty values
        if (typeof bIgnoreEmpty == "undefined")
            bIgnoreEmpty = true;
        // list of rows which we're going to loop through
        var aiRows;
        // use only filtered rows
        if (bFiltered == true)
            aiRows = oSettings.aiDisplay;
        // use all rows
        else
            aiRows = oSettings.aiDisplayMaster; // all row numbers
        // set up data array   
        var asResultData = new Array();
        for (var i = 0, c = aiRows.length; i < c; i++) {
            iRow = aiRows[i];
            var aData = this.fnGetData(iRow);
            var sValue = aData[iColumn];
            // ignore empty values?
            if (bIgnoreEmpty == true && sValue.length == 0)
                continue;
            // ignore unique values?
            else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1)
                continue;
            // else push the value onto the result data array
            else
                asResultData.push(sValue);
        }
        return asResultData;
    };

});

function creatRoleTable() {
    jQuery.ajax({
        url: "index.php?option=com_easysdi_contact&task=users.getUsersAndRolesByOrganismIDAjax&organismId=" + orgId,
        success: function (result) {
            var trHTML;
            result = jQuery.parseJSON(result);
            //<i class="icon-publish"><span class="hide">' + lblYes + '</span></i>
            jQuery.each(result, function (i, item) {
                trHTML += '<tr>' +
                        '<td>' + item.org_name + '</td>' +
                        '<td><a href="index.php?option=com_easysdi_contact&task=user.edit&id=' + item.sdi_user_id + '">' + item.fullusername + '</a></td>' +
                        '<td class="' + (item.resourcemanager == null ? 'role-no' : 'role-yes') + '">' + (item.resourcemanager == null ? lblNo : lblYes) + '</td>' +
                        '<td class="' + (item.metadataresponsible == null ? 'role-no' : 'role-yes') + '">' + (item.metadataresponsible == null ? lblNo : lblYes) + '</td>' +
                        '<td class="' + (item.metadataeditor == null ? 'role-no' : 'role-yes') + '">' + (item.metadataeditor == null ? lblNo : lblYes) + '</td>' +
                        '<td class="' + (item.diffusionmanager == null ? 'role-no' : 'role-yes') + '">' + (item.diffusionmanager == null ? lblNo : lblYes) + '</td>' +
                        '<td class="' + (item.previewmanager == null ? 'role-no' : 'role-yes') + '">' + (item.previewmanager == null ? lblNo : lblYes) + '</td>' +
                        '<td class="' + (item.extractionresponsible == null ? 'role-no' : 'role-yes') + '">' + (item.extractionresponsible == null ? lblNo : lblYes) + '</td>' +
                        '<td class="' + (item.pricingmanager == null ? 'role-no' : 'role-yes') + '">' + (item.pricingmanager == null ? lblNo : lblYes) + '</td>' +
                        '<td class="' + (item.validationmanager == null ? 'role-no' : 'role-yes') + '">' + (item.validationmanager == null ? lblNo : lblYes) + '</td>' +
                        '<td class="' + (item.organismmanager == null ? 'role-no' : 'role-yes') + '">' + (item.organismmanager == null ? lblNo : lblYes) + '</td>' +
                        '</tr>';
            });
            jQuery('#user-roles-table tbody').append(trHTML);
            var oTable = jQuery('#user-roles-table').dataTable({
                "oLanguage": {
                    sUrl: siteRoot + '/index.php?option=com_easysdi_core&task=proxy.run&url=' + encodeURI('http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/' + dtLang + '.json')
                }
            });
            /* Add a select menu for each TH element in the table footer */
            jQuery("#user-roles-table tfoot th").each(function (i) {
                var theSelect = fnCreateSelect(oTable.fnGetColumnData(i));
                if (this.hasClass('user-col'))
                    return;
                this.innerHTML = theSelect;
                jQuery('select', this).change(function () {
                    oTable.fnFilter(jQuery(this).val(), i);
                });
            });
            //replace content
            jQuery("#user-roles-table .role-yes").html('<span class="icon-save"></span>');
        }
    });
}

