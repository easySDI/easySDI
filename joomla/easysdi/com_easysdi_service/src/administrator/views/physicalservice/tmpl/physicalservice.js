jQuery(document).ready(function() {
    enableOrganism();
});

function enableOrganism() {
    if (jQuery('#jform_servicescope_id').val() != 2) {
        jQuery("#jform_organisms").val("").trigger('liszt:updated');
        jQuery("#organisms").hide();
    }
    else
    {
        jQuery("#organisms").show();
    }
}

Joomla.submitbutton = function(task)
{
    if (task == 'physicalservice.cancel' || document.formvalidator.isValid(document.id('physicalservice-form'))) {
        Joomla.submitform(task, document.getElementById('physicalservice-form'));
    }
    else {
        alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED', 'Form validation failed'));
    }
}

var request;

function negoVersionService() {
    var url = document.getElementById("jform_resourceurl").value;
    var user = document.getElementById("jform_resourceusername").value;
    var password = document.getElementById("jform_resourcepassword").value;
    if (document.getElementById("jform_serviceurl"))
        var serurl = document.getElementById("jform_serviceurl").value;
    if (document.getElementById("jform_serviceusername"))
        var seruser = document.getElementById("jform_serviceusername").value;
    if (document.getElementById("jform_servicepassword"))
        var serpassword = document.getElementById("jform_servicepassword").value;
    var service = document.getElementById("jform_serviceconnector").value;

    var query = "index.php?option=com_easysdi_service&task=negotiation&resurl=" + url + "&resuser=" + user + "&respassword=" + password + "&service=" + service;
    if (serurl && serurl.length > 0)
    {
        query = query + "&serurl=" + url + "&seruser=" + user + "&serpassword=" + password;
    }
    request = getHTTPObject();
    document.getElementById("progress").style.visibility = "visible";
    request.onreadystatechange = getSupportedVersions;
    request.open("GET", query, true);
    request.send(null);
}

function getHTTPObject() {
    var xhr = false;
    if (window.XMLHttpRequest) {
        xhr = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        try {
            xhr = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                xhr = false;
            }
        }
    }
    return xhr;
}

function getSupportedVersions()
{
    if (request.readyState == 4) {
        document.getElementById("progress").style.visibility = "hidden";
        var JSONtext = request.responseText;
        if (JSONtext == "[]") {
            dv = document.createElement('span');
            dv.className = "label label-important";
            txt = document.createTextNode(Joomla.JText._('COM_EASYSDI_SERVICE_FORM_DESC_SERVICE_NEGOTIATION_ERROR', 'Negotiation process failed'));
            dv.appendChild(txt);
            document.getElementById('div-supportedversions').appendChild(dv);
            document.getElementById('jform_compliance').value = "";
            return;
        }
        var arrcompliance = new Array();
        document.getElementById('div-supportedversions').innerHTML = '';
        var JSONobject = JSON.parse(JSONtext);
        for(key in JSONobject){
            var value = JSONobject[key];
            var type;

            if (key && typeof key === 'string' && key == 'ERROR') {
                dv = document.createElement('span');
                dv.className = "label label-important";
                txt = document.createTextNode(value);
                dv.appendChild(txt);
                document.getElementById('div-supportedversions').appendChild(dv);
                document.getElementById('jform_compliance').value = "";
                return;
            }
            if (value && typeof value === 'string') {
                dv = document.createElement('span');
                dv.className = "label label-info";
                txt = document.createTextNode(value);
                dv.appendChild(txt);
                document.getElementById('div-supportedversions').appendChild(dv);

                arrcompliance.push(key);
            }
        };
        if (arrcompliance.length > 0)
            document.getElementById('jform_compliance').value = JSON.stringify(arrcompliance);
        else
            document.getElementById('jform_compliance').value = "";
    }
}