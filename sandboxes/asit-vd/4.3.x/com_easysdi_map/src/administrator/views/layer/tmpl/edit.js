
var request;
var selectedservice;
var layername_select;

function init()
{
    if (jQuery('#jform_asOL').is(':checked'))
        jQuery('#asOL :input').removeAttr("disabled");
    if (jQuery('#jform_isindoor').is(':checked'))
        jQuery('#jform_levelfield').removeAttr("disabled");

    getLayers();
	
    setServiceConnector();
}

function clearLayers()
{
    jQuery("#jform_layername").empty();
    jQuery("#jform_layername").trigger("liszt:updated");
}

function getLayers()
{
    clearLayers();

    selectedservice = jQuery('#jform_service_id').find(":selected").val();
    if (!selectedservice)
        return;
	
    var layers = jQuery("#" + selectedservice).val();

    if (layers)
    {
        var jsonalllayers = jQuery("#" + selectedservice).val();
        var allayers = JSON.parse(jsonalllayers);
        jQuery.each(allayers, function(key, value) {
            addLayerOption(value, value);
        });

        if (jQuery("#jform_onloadlayername").val()) {
            jQuery("#jform_layername option[value='" + jQuery("#jform_onloadlayername").val() + "']").attr("selected", "selected");
            jQuery("#jform_layername").trigger("liszt:updated");
        }
    }
    else
    {
        request = false;
        if (window.XMLHttpRequest) {
            request = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            try {
                request = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    request = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {
                    request = false;
                }
            }
        }
        if (!request)
            return;

        var query = "index.php?option=com_easysdi_map&task=getLayers&service=" + selectedservice;

        jQuery("#progress").css('visibility', 'visible');
        request.onreadystatechange = setLayers;
        request.open("GET", query, true);
        request.send(null);
    }
}

function addLayerOption(id, value)
{
    jQuery("#jform_layername").append('<option value="' + id + '">' + value + '</option>');
    jQuery("#jform_layername").trigger("liszt:updated");
}

function setLayers()
{
    if (request.readyState == 4) {
        clearLayers();

        jQuery("#progress").css('visibility', 'hidden');
        var JSONtext = request.responseText;

        if (JSONtext == "[]") {
            return;
        }

        var ok = true;
        var JSONobject = JSON.parse(JSONtext, function(key, value) {
            if (key && typeof key === 'string' && key == 'ERROR') {
                alert(value);
                ok = false;
                return;
            }

            if (value && typeof value === 'string') {
                addLayerOption(key,value);
            }
        });

        if (ok)
        {
            if (jQuery("#jform_onloadlayername").val()) {
                jQuery("#jform_layername option[value='" + jQuery("#jform_onloadlayername").val() + "']").attr("selected", "selected");
                jQuery("#jform_layername").trigger("liszt:updated");
            }

            jQuery('<input type="hidden">').attr({
                id: selectedservice,
                name: selectedservice,
                value: JSONtext
            }).appendTo('layer-form');
        }
    }
}

function enableOlparams()
{
    if (jQuery('#jform_asOL').is(':checked'))
    {
        jQuery('#asOL :input').removeAttr("disabled");
    }
    else
    {
        jQuery("#asOL :input").val("");
        jQuery("#asOL :input").attr("disabled", true);
    }
}

function enableIndoorNavigation(){
    if (jQuery('#jform_isindoor').is(':checked'))
    {
        jQuery('#jform_levelfield').removeAttr("disabled");
    }
    else
    {
        jQuery("#jform_levelfield").val("");
        jQuery("#jform_levelfield").attr("disabled", true);
    }
}

function setServiceConnector()
{
    var serviceconnectorlist = JSON.parse(jQuery("#serviceconnectorlist").val());
    var service = jQuery('#jform_service_id').find(":selected").val();

    for (var i = 0; i < serviceconnectorlist.length; i++) {
        var serviceconnector = JSON.parse(serviceconnectorlist[i]);
        if (serviceconnector[0] == service)
        {
            var connector = serviceconnector[1];
            jQuery('#jform_serviceconnector').val(connector);

            if (connector == 2) {
                document.getElementById('jform_asOL').disabled = false;
                jQuery("#jform_asOLoptions").css("display", "block");
                jQuery("#jform_asOLoptions-lbl").css("display", "block");
                jQuery("#jform_asOLstyle").css("display", "block");
                jQuery("#jform_asOLstyle-lbl").css("display", "block");
                jQuery("#WMTS-info").css("display", "none");
                jQuery("#jform_asOLmatrixset").css("display", "none");
                jQuery("#jform_asOLmatrixset-lbl").css("display", "none");
            }
            else if (connector == 11) {
                document.getElementById('WMTS-info').style.display = "none";
                document.getElementById('jform_asOL').disabled = false;
                document.getElementById('jform_asOLoptions').style.display = "block";
                document.getElementById('jform_asOLoptions-lbl').style.display = "block";
                document.getElementById('jform_asOLstyle').style.display = "block";
                document.getElementById('jform_asOLstyle-lbl').style.display = "block";
                document.getElementById('jform_asOLmatrixset').style.display = "none";
                document.getElementById('jform_asOLmatrixset-lbl').style.display = "none";
                document.getElementById('jform_asOLstyle').className += " required";
                document.getElementById('jform_asOLstyle-lbl').className += " required";
            }
            else if (connector == 3) {
                document.getElementById('WMTS-info').style.display = "block";
                document.getElementById('jform_asOL').disabled = false;
                document.getElementById('jform_asOL').checked = true;
                document.getElementById('jform_asOLoptions').style.display = "block";
                document.getElementById('jform_asOLoptions-lbl').style.display = "block";
                document.getElementById('jform_asOLstyle').style.display = "block";
                document.getElementById('jform_asOLstyle-lbl').style.display = "block";
                document.getElementById('jform_asOLmatrixset').style.display = "block";
                document.getElementById('jform_asOLmatrixset-lbl').style.display = "block";
                document.getElementById('jform_asOLstyle').className += " required";
                document.getElementById('jform_asOLstyle-lbl').className += " required";
                document.getElementById('jform_asOLmatrixset').className += " required";
                document.getElementById('jform_asOLmatrixset-lbl').className += " required";
            }
            else {
                document.getElementById('WMTS-info').style.display = "none";
                document.getElementById('jform_asOL').checked = false;
                document.getElementById('jform_asOL').disabled = true;
                document.getElementById('jform_asOLoptions').style.display = "none";
                document.getElementById('jform_asOLoptions-lbl').style.display = "none";
                document.getElementById('jform_asOLstyle').style.display = "none";
                document.getElementById('jform_asOLstyle-lbl').style.display = "none";
                document.getElementById('jform_asOLmatrixset').style.display = "none";
                document.getElementById('jform_asOLmatrixset-lbl').style.display = "none";
                document.getElementById('jform_asOLstyle').className = "inputbox";
                document.getElementById('jform_asOLstyle-lbl').className = "hasTip";
                document.getElementById('jform_asOLmatrixset').className = "inputbox";
                document.getElementById('jform_asOLmatrixset-lbl').className = "hasTip";
            }

            if (document.getElementById('jform_asOL').checked == true)
            {
                document.getElementById('jform_asOLoptions').disabled = false;
                document.getElementById('jform_asOLstyle').disabled = false;
                document.getElementById('jform_asOLmatrixset').disabled = false;
            }
            else
            {
                document.getElementById('jform_asOLoptions').disabled = true;
                document.getElementById('jform_asOLstyle').disabled = true;
                document.getElementById('jform_asOLmatrixset').disabled = true;
            }
        }
    }
}

window.addEvent('domready', init);