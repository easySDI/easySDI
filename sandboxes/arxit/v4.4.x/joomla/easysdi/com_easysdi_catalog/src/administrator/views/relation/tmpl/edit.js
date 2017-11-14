function onChangeSearchFilter() {
    var isselected = js("#jform_issearchfilter0").prop("checked");
    switch (isselected) {
        case true:
            js("#searchfilterdefinition").show();
            break;
        case false:
            js("#searchfilterdefinition").hide();
            break;
    }
}

function onChangeChildType() {
    var childtype = js("#jform_childtype_id :selected").val();
    switch (childtype) {
        case "0":
            js("#classchilddefinition").hide();
            js("#commondefinition").hide();
            js("#attributechilddefinition").hide();
            js("#defaultvalue").hide();
            js("#resourcetypedefinition").hide();
            break;
        case "1":
            js("#classchilddefinition").show();
            js("#commondefinition").show();
            js("#attributechilddefinition").hide();
            js("#defaultvalue").hide();
            js("#resourcetypedefinition").hide();
            break;
        case "2":
            js("#classchilddefinition").hide();
            js("#commondefinition").hide();
            js("#attributechilddefinition").show();
            js("#defaultvalue").show();
            js('[id^="defaultvalue-"]').hide();
            js("#resourcetypedefinition").hide();
            break;
        case "3":
            js("#classchilddefinition").hide();
            js("#commondefinition").show();
            js("#attributechilddefinition").hide();
            js("#defaultvalue").hide();
            js("#resourcetypedefinition").show();
            break;
    }
    
    onChangeRenderType();
}


function onChangeRenderType() {
    js('[id^="defaultvalue-"]').hide();
    if (js.inArray(stereotype, ['1', '2', '4', '5', '7', '8', '12', '13']) !== -1) {
        if (js("#jform_rendertype_id :selected").val() === '1')
            js('#defaultvalue-textarea').show();
        else if (js("#jform_rendertype_id :selected").val() === '5')
            js('#defaultvalue-textbox').show();
        else if (js("#jform_rendertype_id :selected").val() === '6')
            js('#defaultvalue-date').show();
    } else if (stereotype === '3') {
        if (js("#jform_rendertype_id :selected").val() === '1')
            js('#defaultvalue-localetextarea').show();
        else if (js("#jform_rendertype_id :selected").val() === '5')
            js('#defaultvalue-localetextbox').show();
    } else if (js.inArray(stereotype, ['6', '9', '10']) !== -1) {
        if (js("#jform_upperbound").val() <= 1) {
            var selected = js('#jform_hiddendefaultlist').val().split(',')[0];
            js('#jform_defaultlist').empty().trigger("liszt:updated");
            js('#jform_defaultlist')
                        .append('<option value=""></option>')
                        .trigger("liszt:updated")
                        ;
            js.each(attributevalue, function(key, value) {
                js('#jform_defaultlist')
                        .append('<option value="' + key + '">' + value + '</option>')
                        .trigger("liszt:updated")
                        ;
            });
            
            if(selected.length > 0){
                js('#jform_defaultlist option[value=' + selected + ']').attr('selected', 'selected').trigger("liszt:updated");
            }
            js('#jform_defaultlist').trigger("change");
            js('#defaultvalue-list').show();
        } else {
            var selected = js('#jform_hiddendefaultlist').val().split(',');
            js('#jform_defaultmultiplelist').empty().trigger("liszt:updated");
             js('#jform_defaultmultiplelist')
                        .append('<option value=""></option>')
                        .trigger("liszt:updated")
                        ;
            js.each(attributevalue, function(key, value) {
                js('#jform_defaultmultiplelist')
                        .append('<option value="' + key + '">' + value + '</option>')
                        .trigger("liszt:updated")
                        ;
            });
            console.log(selected);
           
            js.each(selected, function (i, value){
                if(value.length > 0){
                    js('#jform_defaultmultiplelist option[value=' + value + ']').attr('selected', 'selected').trigger("liszt:updated");
                }
            })
            
            js('#jform_defaultmultiplelist').trigger("change");
            js('#defaultvalue-multiplelist').show();
        }
    }
}

function onChangeAttributeChild() {
    js('#loader').show();
    var attributechild_id = js("#jform_attributechild_id :selected").val();
    if (attributechild_id == '') {
        js('#loader').hide();
        return;
    }
    var rendertype_id = js("#jform_rendertype_id :selected").val();
    var uriencoded = url + attributechild_id;
    js.ajax({
        type: 'Get',
        url: uriencoded,
        success: function(data) {
            var attributes = js.parseJSON(data);
            js('#jform_rendertype_id').empty().trigger("liszt:updated");
            var rendertype = {};
            attributevalue = {};
            js.each(attributes, function(key, value) {
                rendertype[value.rendertypeid] = value.rendertypevalue;
                stereotype = value.stereotypeid;
                attributevalue [value.attributevalueid] = value.attributevaluevalue;
            });
            js.each(rendertype, function(key, value) {
                js('#jform_rendertype_id')
                        .append('<option value="' + key + '">' + value + '</option>')
                        .trigger("liszt:updated")
                        ;
            });
            
            if(rendertype_id.length > 0){
                js('#jform_rendertype_id option[value=' + rendertype_id + ']').attr('selected', 'selected').trigger("liszt:updated");
            }
            
            onChangeRenderType();
            js('#loader').hide();
        }
    })
}
    