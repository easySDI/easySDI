jQuery(function () {

    var param_types = ['text', 'textarea', 'number', 'checkbox', 'select', 'selectmulti', 'date', 'file'];

    var initParamsEditor = function () {
        var obj = jQuery(this);
        var json = obj.val();
        if ('' == json) json = '[]';
        json = JSON.parse(json);

        var main_div = jQuery('<div class="params_editor"></div>');
        var params_div = jQuery('<div></div>');
        var html = '<div class="btn-group btn-large dropup">';
        html += '<a class="btn dropdown-toggle  btn-primary" data-toggle="dropdown" href="#">';
        html += 'ajouter un paramètre&nbsp;';
        html += '<span class="caret"></span>';
        html += '</a>';

        html += '<ul class="dropdown-menu">';

        jQuery.each(param_types, function (i, value) {
            html += '<li><a href="#" data-type="' + value + '">' + value + '</a></li>';
        });

        html += '</ul>';
        html += '</div>';
        var add_param_button = jQuery(html);
        var preview_div = jQuery('<div></div>');

        main_div.insertBefore(obj);
        params_div.appendTo(main_div);
        add_param_button.appendTo(main_div);
        preview_div.appendTo(main_div);

        var itemToEditor = function (i, item) {

            var html = '<fieldset class="param' + i + '"><legend>' + item.name + ' [' + item.type + '] <a href="#" class="btn btn-danger btn-mini params_editor_delete_field">delete</a></legend>';
            html += '<input type="hidden" data-param="type" id="param_type_' + i + '" value="' + item.type + '">';
            html += '<div class="control-group"><label class="control-label" for="param_name_' + i + '">name</label><div class="controls"><input type="text" data-param="name" id="param_name_' + i + '" value="' + item.name + '"></div></div>';
            html += '<div class="control-group"><label class="control-label" for="param_title_' + i + '">title</label><div class="controls"><input type="text" data-param="title" id="param_title_' + i + '" value="' + item.title + '"></div></div>';

            html += '<div class="control-group"><div class="controls"><label class="control-label" for="param_required_' + i + '"><input type="checkbox" data-param="required" id="param_required_' + i + '" value=true  ' + (item.required ? ' checked' : '') + '>&nbsp;required</label></div></div>';

            if ('text' == item.type) {
                html += '<div class="control-group"><label class="control-label" for="param_default_' + i + '">default</label><div class="controls"><input type="text" data-param="default" id="param_default_' + i + '" value="' + (item.default === undefined ? '' : item.default) + '"></div></div>';
            }

            if ('textarea' == item.type) {
                html += '<div class="control-group"><label class="control-label" for="param_default_' + i + '">default</label><div class="controls"><textarea data-param="default" id="param_default_' + i + '">' + (item.default === undefined ? '' : item.default) + '</textarea></div></div>';
            }

            if ('date' == item.type) {
                html += '<div class="control-group"><label class="control-label" for="param_default_' + i + '">default</label><div class="controls"><input type="date" data-param="default" id="param_default_' + i + '" value="' + (item.default === undefined ? '' : item.default) + '"></div></div>';
            }

            if ('file' == item.type) {
                // gerer les types de fichiers ? <input accept="file_extension|audio/*|video/*|image/*|media_type"> + copie en info
                html += '<input type="hidden" data-param="default" id="param_default_' + i + '" value="">';
            }

            if ('number' == item.type) {
                html += '<div class="control-group"><label class="control-label" for="param_step_' + i + '">step</label><div class="controls"><input type="number" data-param="step" id="param_step_' + i + '" min=0 step="any" value=' + (item.step === undefined ? '' : item.step) + ' ></div></div>';
                html += '<div class="control-group"><label class="control-label" for="param_min_' + i + '">min</label><div class="controls"><input type="number" data-param="min" id="param_min_' + i + '"' + (item.max === undefined ? '' : ' max=' + item.max) + ' step=' + (item.step === undefined ? '"any"' : item.step) + ' value=' + item.min + '></div></div>';
                html += '<div class="control-group"><label class="control-label" for="param_max_' + i + '">max</label><div class="controls"><input type="number" data-param="max" id="param_max_' + i + '"' + (item.min === undefined ? '' : ' min=' + item.min) + ' step=' + (item.step === undefined ? '"any"' : item.step) + ' value=' + item.max + '></div></div>';

                html += '<div class="control-group"><label class="control-label" for="param_default_' + i + '">default</label><div class="controls"><input type="number" data-param="default" id="param_default_' + i + '"' + (item.min === undefined ? '' : ' min=' + item.min) + (item.max === undefined ? '' : ' max=' + item.max) + ' step=' + (item.step === undefined ? '"any"' : item.step) + ' value=' + item.default+'></div></div>';
            }

            if ('checkbox' == item.type) {
                html += '<div class="control-group"><label class="control-label" for="param_default_' + i + '">default</label><div class="controls">';
                html += '<select data-param="default" id="param_default_' + i + '">';
                html += '<option value=true' + (item.default ? ' selected' : '') + '>checked</option>';
                html += '<option value=false' + (item.default === undefined || !item.default ? ' selected' : '') + '>unchecked</option>';
                html += '</select>';
                html += '</div></div>';
            }

            if ('select' == item.type || 'selectmulti' == item.type) {
                html += '<div class="control-group"><label class="control-label" for="param_values_' + i + '">values</label><div class="controls">';
                html += '<table class="table table-strip table-bordered">';
                html += '<tr><th>name</th><th>title</th><td></td></tr>';
                if (typeof item.values === 'object')
                    jQuery.each(item.values, function (k, v) {
                        html += '<tr class="valueline">';
                        html += '<td><input type="text" data-param="values_id" value="' + v.id + '"/></td>';
                        html += '<td><input type="text" data-param="values_text" value="' + v.text + '"/></td>';
                        html += '<td><a href="#" class="btn btn-warning btn-mini params_editor_delete_line">delete</a></td></tr>';
                    });
                html += '<tr><td><input type="text" id="add_param_id_' + i + '" placeholder="name"/></td><td><input type="text" id="add_param_text_' + i + '" placeholder="title"/></td><td><a href="#" class="btn btn-primary btn-mini params_editor_add_value" data-paramid="' + i + '">add value</a></td></tr>';
                html += '</table>';
                html += '</div></div>';
            }

            if ('select' == item.type) {
                html += '<div class="control-group"><label class="control-label" for="param_default_' + i + '">default</label><div class="controls">';
                html += '<select data-param="default" id="param_default_' + i + '">';
                if (typeof item.values === 'object')
                    jQuery.each(item.values, function (k, v) {
                        html += '<option value="' + v.id + '"' + (item.default == v.id ? ' selected' : '') + '>' + v.text + '</option>';
                    });
                html += '</select>';
                html += '</div></div>';
            }

            if ('selectmulti' == item.type) {
                html += '<div class="control-group"><label class="control-label" for="param_default_' + i + '">default</label><div class="controls">';
                html += '<select data-param="default" id="param_default_' + i + '" multiple>';
                if (typeof item.values === 'object')
                    jQuery.each(item.values, function (k, v) {
                        html += '<option value="' + v.id + '"' + (jQuery.inArray(v.id, item.default) > -1 ? ' selected' : '') + '>' + v.text + '</option>';
                    });
                html += '</select>';
                html += '</div></div>';
            }

            html += '<div class="control-group"><label class="control-label" for="param_desc_' + i + '">description</label><div class="controls"><textarea data-param="desc" id="param_desc_' + i + '">' + (item.desc === undefined ? '' : item.desc) + '</textarea></div></div>';

            html += '</fieldset>';

            return html;
        }

        var preview = function () {
            var html = '';
            html += '<h4>preview</h4>';

            return jQuery(html);
        }

        var update = function () {
            var div = jQuery('<form></form>');
            jQuery.each(json, function (i, item) {
                div.append(itemToEditor(i, item));
            });
            params_div.html(div);
            // preview_div.html(preview);
        }

        var slugify = function (str) {
            str = str.trim();
            str = str.replace(/[^a-z0-9-]/gi, '-').
            replace(/-+/g, '-').
            replace(/^-|-$/g, '');
            return str.toLowerCase();
        }

        var updateJson = function () {
            var j = [];
            params_div.find('fieldset').each(function () {
                var i = {};
                jQuery(this).find('[data-param]').each(function () {
                    var o = jQuery(this);
                    var val = o.val();
                    if ('required' === o.data('param')) val = o.prop('checked');


                    if ('checkbox' === i.type && 'default' === o.data('param')) val = (val === 'true');
                    if ('number' === i.type && jQuery.inArray(o.data('param'), ['min', 'max', 'step', 'default']) > -1) {
                        if ("" == val) {
                            val = null;
                        } else {
                            val = parseFloat(val);
                        }
                    }
                    if (o.data('param') == 'values_id' || o.data('param') == 'values_text') {
                        if (undefined == i[o.data('param')]) i[o.data('param')] = [];
                        i[o.data('param')].push(val);

                    } else {
                        i[o.data('param')] = val;
                    }
                });

                if (undefined != i.values_id) {
                    i['values'] = [];
                    jQuery.each(i.values_id, function (k, v) {
                        i['values'].push({
                            id: v,
                            text: i.values_text[k]
                        });
                    });
                    delete i['values_id'];
                    delete i['values_text'];
                }


                if (i.required === false) delete i.required;
                if (i.desc === '') delete i.desc;
                if (i.default === '') delete i.default;
                if (i.name == undefined) i.name = '';
                i.name = slugify(i.name);
                if ('' === i.name) i.name = 'field' + (j.length + 1);
                if (i.title == undefined || i.title == '') i.title = i.name;
                if (i.default === '') delete i.default;
                if (i.step === '' || i.step == 0 || i.step == null) delete i.step;

                j.push(i);

            });

            json = j;
        }

        var changeParam = function () {
            updateJson();
            obj.val(JSON.stringify(json));
            update();
        }

        var addParam = function (e) {
            e.preventDefault();
            params_div.append(jQuery('<fieldset><input type="hidden" data-param="type" value="' + jQuery(this).data('type') + '"/></fieldset>'));
            changeParam();
        }

        var addValue = function (e) {
            e.preventDefault();

            var param_id = jQuery(this).data('paramid');
            var fieldset = params_div.find('fieldset.param' + param_id);
            var v_id = jQuery('#add_param_id_' + param_id).val();
            var v_text = jQuery('#add_param_text_' + param_id).val();
            fieldset.append('<input type="hidden" data-param="values_id" value="' + v_id + '">');
            fieldset.append('<input type="hidden" data-param="values_text" value="' + v_text + '">');

            changeParam();
        }

        var deleteLine = function (e) {
            e.preventDefault();
            var line = jQuery(this).parents('tr');
            line.remove();
            changeParam();
        }

        var deleteField = function (e) {
            e.preventDefault();
            var field = jQuery(this).parents('fieldset');
            field.remove();
            changeParam();
        }

        main_div.on('change', '*[data-param]', changeParam)
            .on('click', '*[data-type]', addParam)
            .on('click', '.params_editor_add_value', addValue)
            .on('click', '.params_editor_delete_line', deleteLine)
            .on('click', '.params_editor_delete_field', deleteField);

        update();
    }



    jQuery('.params_editor_field').each(initParamsEditor);

    var initParamsEditorTarget = function () {
        var target = jQuery(this);
        var form = jQuery(this).parents('form');

        form.submit(function (e) {
            var res = {};
            form.find('.params_editor_input_source').each(function () {
                var obj = jQuery(this);
                var name = obj.prop('name').replace('param_', '');
                var value = obj.val();
                var ptype = obj.prop('type');
                if (ptype == 'checkbox') value = (value == 'on');
                if (ptype == 'number') value = parseFloat(value);

                res[name] = value;
            });
            target.val(JSON.stringify(res));
        });
    }

    jQuery('.params_editor_input_target ').each(initParamsEditorTarget);


});