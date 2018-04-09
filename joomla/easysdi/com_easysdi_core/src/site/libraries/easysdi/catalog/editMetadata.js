/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

js = jQuery.noConflict();
//var currentUrl = location.protocol + '//' + location.host + location.pathname;
var tabIsOpen;
var resourcetypes;

var enumRendertype = {
    TEXTAREA: 1,
    CHECKBOX: 2,
    RADIOBUTTON: 3,
    LIST: 4,
    TEXTBOX: 5,
    DATE: 6,
    DATETIME: 7,
    GEMET: 8,
    UPLOAD: 9,
    URL: 10,
    UPLOADANDURL: 11
}

js('document').ready(function () {

    var options = {handler: 'iframe', size: {x: iframewidth, y: iframeheight}};
    SqueezeBox.initialize(options);

    // Remove scope-hidden field
    removeHidden();

    // change field into readonly
    disableVisible();

    /**
     * set initial state of relation action button
     */
    js('.add-btn').each(function () {
        setRelationAction(js(this));
    });

    /**
     * Set initial state of attribute action button
     */
    js('.attribute-action').each(function () {
        setAttributeAction(js(this));
    });

    /**
     * Retrieves resource types and displays or not the checkboxes versions. 
     */
    js.ajax({
        url: baseUrl + 'option=com_easysdi_catalog&task=ajax.getResourceType',
        type: "GET",
        async: false,
        cache: false
    }).done(function (data) {
        resourcetypes = js.parseJSON(data);

        for (var i in resourcetypes) {
            if (resourcetypes[i].versioning != 0) {
                js('#version-control-group').show();
            }
        }
    }).fail(function () {
        bootbox.alert(Joomla.JText._('COM_EASYSDI_CATALOG_ERROR_RETRIEVE_VERSION', 'COM_EASYSDI_CATALOG_ERROR_RETRIEVE_VERSION'));
    });

    /**
     * Boundaries NEW inputs events
     */
    js('input[id$=_sla_gmd_dp_northBoundLatitude_sla_gco_dp_Decimal]').each(function () {
        var parentPath = js(this).attr('id').replace('jform_', '').replace('_sla_gmd_dp_northBoundLatitude_sla_gco_dp_Decimal', '');

        js('input[id^=jform_' + parentPath + '_sla_gmd_dp_][id$=_sla_gco_dp_Decimal]').on('change', function () {
            clearbbselect(parentPath.replace('_sla_gmd_dp_geographicElement_sla_gmd_dp_EX_GeographicBoundingBox', ''));
            drawBB(parentPath);
        });

    });

    // Change date field to Calendar field
    js('.validate-sdidate, .validate-sdidatetime').each(function () {
        calendarSetup(js(this).attr('id'));
    });

    /**
     * We override the "submitbutton" function for Joomla buttonbar .
     * 
     * @param {string} task The task to execute.
     * @returns {Boolean}
     */
    Joomla.submitbutton = function (task, rel) {

        if (task == '') {
            return false;
        } else {
            var actions = task.split('.');
            var form = document.getElementById('form-metadata');
            var form_import = document.getElementById('form_replicate_resource');
            var form_xml_import = document.getElementById('form_xml_import');
            var form_csw_import = document.getElementById('form_csw_import');

            switch (actions[1]) {
                case 'cancel':

                    break;
                case 'save':
                case 'saveAndContinue':
                    Joomla.submitform(task, form);
                    return true;
                    break;
                case 'valid':
                case 'validAndClose':
                    if (document.formvalidator.isValid(form)) {
                        Joomla.submitform(task, form);
                        break;
                    } else {
                        js('html, body').animate({scrollTop: 0}, 'slow');
                    }
                    break;
                case 'show':
                    js('input[name="task"]').val(task);
                    js.ajax({
                        url: baseUrl + task,
                        type: js('#form-metadata').attr('method'),
                        data: js('#form-metadata').serialize(),
                        success: function (data) {

                            var response = js.parseJSON(data);
                            if (response.success) {
                                js('#previewModalBody').html(response.xml);
                                js('#previewModal').modal('show');
                            }

                        }
                    });
                    break;
                case 'preview':
                    js('input[name="task"]').val(task);
                    var preview = js('input[name="preview"]').val();
                    js.ajax({
                        url: baseUrl + task,
                        type: js('#form-metadata').attr('method'),
                        data: js('#form-metadata').serialize(),
                        success: function (data) {

                            var response = js.parseJSON(data);
                            if (response.success) {
                                SqueezeBox.open('index.php?option=com_easysdi_catalog&tmpl=component&view=sheet&preview=' + preview + '&type=complete&guid=' + response.guid);
                            }

                        }
                    });


                    break;
                case 'inprogress':
                    Joomla.submitform(task, form);
                    break;
                case 'publish':
                    if (document.formvalidator.isValid(form)) {
                        Joomla.submitform(task, form);
                    }
                    break;
                case 'setPublishDate':
                    if (document.formvalidator.isValid(form)) {
                        js('html, body').animate({scrollTop: 0}, 'slow');
                        var rel = js.parseJSON(rel);

                        js.ajax({
                            url: baseUrl + 'option=com_easysdi_core&task=version.getPublishRight&metadata_id=' + rel.metadata,
                            type: "GET",
                            async: false,
                            cache: false
                        }).done(function (data) {
                            var response = js.parseJSON(data);
                            if (response !== null && response.canPublish > 0) {
                                js('#system-message-container').remove();
                                bootbox.alert(Joomla.JText._('COM_EASYSDI_CATALOG_UNPUBLISHED_OR_UNVALIDATED_CHILDREN', 'COM_EASYSDI_CATALOG_UNPUBLISHED_OR_UNVALIDATED_CHILDREN'));
                            } else {
                                js.ajax({
                                    url: baseUrl + 'option=com_easysdi_core&task=version.getCascadePublicableChild&version_id=' + rel.version,
                                    type: "GET",
                                    async: false,
                                    cache: false
                                }).done(function (data_version) {
                                    var response = js.parseJSON(data_version);

                                    var children = response.versions[rel.version].children;
                                    delete response.versions[rel.version].children;
                                    js('#publishModalCurrentMetadata').html(buildVersionsTree(response.versions));

                                    if (js(children).length) {
                                        js('#publishModalChildrenList').html(buildVersionsTree(children));
                                        js('#publishModalViralPublication').attr('checked', true).trigger('change');
                                        js('#publishModalChildrenDiv').show();
                                    }
                                    else {
                                        js('#publishModalViralPublication').attr('checked', false).trigger('change');
                                    }

                                    var publish_date = js('#jform_published').val();
                                    if ('undefined' !== typeof publish_date && '0000-00-00 00:00:00' !== publish_date) {
                                        var datetime = publish_date.split(' ');
                                        js('#publish_date').val(datetime[0]);
                                    }
                                    else{
                                        var d = new Date();
                                        var day = d.getDate();
                                        var month = d.getMonth() + 1;
                                        var year = d.getFullYear();
                                        if (day < 10) {
                                            day = "0" + day;
                                        }
                                        if (month < 10) {
                                            month = "0" + month;
                                        }
                                        js('#publish_date').val(year + "-" + month + "-" + day);
                                    }

                                    js('#publishModal').modal('show');
                                });
                            }
                        }).fail(function () {
                            bootbox.alert(Joomla.JText._('COM_EASYSDI_CATALOG_ERROR_RETRIEVE_PUBLISHING_RIGHT', 'COM_EASYSDI_CATALOG_ERROR_RETRIEVE_PUBLISHING_RIGHT'));
                        });
                        break;
                    }
                    else {
                        js('html, body').animate({scrollTop: 0}, 'slow');
                    }
                    break;
                case 'publishAndClose':
                    if (document.formvalidator.isValid(form)) {
                        Joomla.submitform(task, form);
                    }
                    break;
                case 'publishWithDate':
                    js('#jform_published').val(js('#publish_date').val());
                    js('#jform_viral').val(js('#viral').val());
                    Joomla.submitbutton('metadata.publish');
                    break;
                case 'replicate':
                    confirmReplicate(task);
                    break;
                case 'searchresource':
                    searchResource(task);
                    break;
                case 'edit':
                    Joomla.submitform(task, form_import);
                    break;
                case 'import':
                    confirmImport(task);
                    break;
                case 'importxml':
                    if (document.formvalidator.isValid(form_xml_import)) {
                        Joomla.submitform('metadata.edit', form_xml_import);
                    }
                    break;
                case 'importcsw':
                    if (document.formvalidator.isValid(form_csw_import)) {
                        Joomla.submitform('metadata.edit', form_csw_import);
                    }
                    break;
                case 'reset':
                    document.location = resetMetadataUrl;
                    break;

            }

        }
    };

    js('#search_table').dataTable({
        "bFilter": false,
        "oLanguage": {
            sUrl: baseUrl + 'option=com_easysdi_core&task=proxy.run&url=' + encodeURI('http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/' + dtLang + '.json')
        },
        aaData: null,
        aoColumnDefs: [
            {aTargets: [0], mData: function (item) {
                    return "<input type='radio' name='import[id]' id='import_id_" + item.id + "' value='" + item.id + "' checked=''>";
                }},
            {aTargets: [1], mData: 'name'},
            {aTargets: [2], mData: 'vname'},
            {aTargets: [3], mData: 'guid'},
            {aTargets: [4], mData: 'rt_name'},
            {aTargets: [5], mData: function (item) {
                    return Joomla.JText._(item.status);
                }}
        ]
    });
    js('#search_table_wrapper').hide();
});



js(document).on('change', '#publishModalViralPublication', function () {
    js('#publishModal #viral').val(js(this).attr('checked') === 'checked' ? 1 : 0)
});

/**
 * When the preview modal is visible, we colorize the XML.
 */
js(document).on('show.bs.modal', '#previewModal', function () {
    SyntaxHighlighter.highlight();
});

/**
 * Add validation on non-required multi-lingual fields
 */
js(document).on('change keyup blur focus', '.i18n div.controls > input, .i18n div.controls > textarea, .i18n div.controls > select', function () {
    var brothers = js(this).closest('.i18n').find('div.controls > input, div.controls > textarea, div.controls > select'),
            labels = js(this).closest('.i18n').find('div.control-label > label');
    if (this.value !== '') {
        brothers.addClass('required');
    }
    else {
        var required = false;
        js.each(brothers, function (i, brother) {
            if (brother.value !== '')
                required = true;

            if (i === brothers.length - 1) {
                if (required) {
                    brothers.addClass('required');
                }
                else {
                    brothers.removeClass('required invalid');
                    labels.removeClass('invalid');
                }
            }
        });
    }
});

/**
 * displays or not the checkboxes versions on change event
 */
js(document).on('change', '#resourcetype_id', function () {
    js('#resourcetype_id option:selected').each(function () {
        if (js(this).val() == 0) {
            for (var i in resourcetypes) {
                if (resourcetypes[i].versioning != 0) {
                    js('#version-control-group').show();
                }
            }
        } else {
            if (resourcetypes[js(this).val()].versioning == 1) {
                js('#version-control-group').show();
            } else {
                js('#version-control-group').hide();
            }
        }
    });
});

// Poll to check IDs correspondance (local DOM metadata ID et server's session metadata id)
js('document').ready(function () {
    var myMetadataId = parseInt(js('#jform_id').val());
    var remoteId = 0;
    (function poll() {
        setTimeout(function () {
            js.ajax({
                url: baseUrl + 'option=com_easysdi_catalog&task=ajax.getCurrentEditId',
                type: "GET",
                success: function (data) {
                    //compare local and remote IDs
                    remoteId = parseInt(data.id);
                    if (remoteId != NaN && remoteId > 0 && remoteId === myMetadataId) {
                        //OK, we have the correct session ID, setup the next poll recursively
                        poll();
                    } else { //IDs mismatch, lock the form
                        lockFormForSession();
                    }
                },
                // user disconnected or other session error, lock the form
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.readyState == 0 || jqXHR.status == 0) {
                        poll(); //Skip this error (back button pressed, cancelled by user etc...)
                    } else {
                        lockFormForSession();
                    }
                },
                dataType: "json"});
        }, 2000);
    })();
});


// File input section
//==============================
js('document').ready(function () {
    js('.file-controls').each(function () {
        if (js(this).children('input').val().length > 0) {
            js(this).children('.btn-preview, .btn-delete').show();
        }
    });
});

/**
 * Lisener on file preview btn
 */
js(document).on('click', '.file-controls .btn-preview', function () {
    var url = js(this).parent().children('input').val();
    window.open(url, '_blank');
});

/**
 * Lisener on fle delete btn
 */
js(document).on('click', '.file-controls .btn-delete', function () {
    var parent = js(this).parent();
    parent.children('input').val('');
    parent.children('.btn-preview').hide();
    js(this).hide();
});

/**
 * Load url value in file field
 */
js(document).on('click', '#fileModal .btn-success', function () {
    js('#' + js('#file_source_field').val()).val(js('#file_url').val());
    js('#fileModal').modal('hide');
    console.log(js('#' + js('#file_source_field').val()).parent());
    js('#' + js('#file_source_field').val()).parent().children('.btn-preview, .btn-delete').show();
});


/**
 * Show file popup
 */
js(document).on('click', '.attach-btn', function () {
    resetFileUploadTab();
    resetFileUrlTab();
    js('#fileModal .btn-success').prop("disabled", true);
    var rendertype = parseInt(js(this).attr('rendertypeId'));
    switch (rendertype) {
        case enumRendertype.UPLOAD:
            js('#fileModal .url').removeClass('active in').hide();
            js('#fileModal .upload').addClass('active in').show();
            break;
        case enumRendertype.URL:
            js('#fileModal .upload').removeClass('active in').hide();
            js('#fileModal .url').addClass('active in').show();
            break;
        case enumRendertype.UPLOADANDURL:
            js('#fileModal .url').removeClass('active in').show();
            js('#fileModal .upload').addClass('active in').show();
            break;
    }

    js('#file_source_field').val(js(this).prev().attr('id'));
    js('#fileModal').modal('show');
});

/**
 * check file url on lost focus
 */
js(document).on('blur', '#fileUrl', function () {

    var url = js('#fileUrl').val();
    js('#fileUrlValidate').hide();

    if (url.length > 0) {
        js.ajax({
            url: baseUrl + 'option=com_easysdi_catalog&task=ajax.checkFileUrl&url=' + url,
            type: "GET",
            cache: false
        }).done(function (data) {
            if (data.code === 200) {
                js('#fileUrlValidate').removeClass('alert alert-error').addClass('alert alert-success').html(Joomla.JText._('COM_EASYSDI_CATALOG_FILE_VALIDATE_OK')).show();
                js('#file_url').val(js('#fileUrl').val());
                js('#fileModal .btn-success').prop("disabled", false);
                resetFileUploadTab();
            } else {
                js('#fileUrlValidate').removeClass('alert alert-success').addClass('alert alert-error').html(Joomla.JText._('COM_EASYSDI_CATALOG_FILE_VALIDATE_KO')).show();
            }
        }).fail(function () {
            js('#fileUrlValidate').removeClass('alert alert-success').addClass('alert alert-error').html(Joomla.JText._('COM_EASYSDI_CATALOG_FILE_VALIDATE_UNABLE')).show();
        });
    }
});

js(function () {
    js('#fileUpload').fileupload({
        dataType: 'json',
        add: function (e, data) {
            js('#fileUploadValidate').hide();
            js('.progress').show();
            data.submit();
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            js('.progress .bar').css('width', progress + '%');
        },
        done: function (e, data) {
            var result = data.result;
            if (result.status === 'success') {
                js('#fileUploadValidate').removeClass('alert alert-error').addClass('alert alert-success').html(Joomla.JText._('COM_EASYSDI_CATALOG_FILE_UPLOAD_SUCCES')).show();
                js('#fileUploadPreview').show();
                js('#fileUploadPreview a').attr('href', result.files.fileUpload.url);
                js('#fileUploadPreview img').attr('src', result.files.fileUpload.thumbnail);
                js('#file_url').val(result.files.fileUpload.url);
                js('#fileModal .btn-success').prop("disabled", false);
                resetFileUrlTab();
            } else {
                console.log('fail');
                js('#fileUploadValidate').removeClass('alert alert-success').addClass('alert alert-error').html(result.error);
            }

        }
    });
});

function resetFileUploadTab() {
    js('.progress, #fileUploadPreview, #fileUploadValidate').hide();
}

function resetFileUrlTab() {
    js('#fileUrlValidate').hide();
    js('#fileUrl').val('');
}

// ENd file input section
//==============================


/**
 * Add field
 */
js(document).on('click', '.attribute-add-btn', function () {
    var parent = js(this).parent();
    var relid = parent.attr('data-relid');
    var parent_path = parent.attr('data-parentpath');
    var uuid = getUuid('attribute-add-btn', this.id);

    js.ajax({
        url: baseUrl + 'option=com_easysdi_catalog&view=ajax&parent_path=' + parent_path + '&relid=' + relid,
        type: "GET",
        async: false,
        cache: false
    }).done(function (data) {
        js('.attribute-group' + uuid).last().after(data);
        if (js(data).find('select') !== null) {
            chosenRefresh();
        }

        js(data).find('.validate-sdidate, .validate-sdidatetime').each(function () {
            calendarSetup(js(this).attr('id'));
        });

        // refresh validator
        document.formvalidator.attachToForm(js('#form-metadata'));
        setAttributeAction(parent);

        // change field into readonly
        disableVisible();
    }).fail(function () {
        bootbox.alert(Joomla.JText._('COM_EASYSDI_CATALOG_ERROR_ADD_ATTRIBUTE_RELATION', 'COM_EASYSDI_CATALOG_ERROR_ADD_ATTRIBUTE_RELATION'));
    });

});

/**
 * remove field from form
 */
js(document).on('click', '.attribute-remove-btn', function () {
    var parent = js(this).parent();
    var uuid = getUuid('attribute-remove-btn', this.id);

    sdiDangerConfirm(Joomla.JText._('COM_EASYSDI_CATALOG_DELETE_RELATION_CONFIRM', 'COM_EASYSDI_CATALOG_DELETE_RELATION_CONFIRM'), function (result) {
        if (result) {
            js.ajax({
                url: baseUrl + 'option=com_easysdi_catalog&task=ajax.removeNode&uuid=' + uuid,
                type: "GET",
                async: false,
                cache: false
            }).done(function () {
                js('#attribute-group' + uuid).remove();
                setAttributeAction(parent);
            }).fail(function () {
                bootbox.alert(Joomla.JText._('COM_EASYSDI_CATALOG_ERROR_REMOVE_ATTRIBUTE_RELATION', 'COM_EASYSDI_CATALOG_ERROR_REMOVE_ATTRIBUTE_RELATION'));
            });
        }
    });
});

/**
 * Add filedset to from when user click on add-btn
 * 
 * Add listner on add buttons
 */
js(document).on('click', '.add-btn', function () {
    var relid = js(this).attr('data-relid');
    var parent_path = js(this).attr('data-parentpath');
    var uuid = getUuid('add-btn', this.id);
    var button = js(this);

    js.ajax({
        url: baseUrl + 'option=com_easysdi_catalog&view=ajax&parent_path=' + parent_path + '&relid=' + relid,
        type: "GET",
        async: false,
        cache: false,
        beforeSend: function () {
            button.attr('disabled', true);
        }
    }).done(function (data) {

        var elmt = (js('.fds' + uuid).length > 0) ? js('.fds' + uuid).last() : button.parent();
        elmt.after(data);

        if (js(data).find('select') !== null) {
            chosenRefresh();
        }

        js(data).find('.validate-sdidate, .validate-sdidatetime').each(function () {
            calendarSetup(js(this).attr('id'));
        });

        // add tooltips on new fields
        addTooltips();

        // remove hidden fields
        removeHidden();

        // change field into readonly
        disableVisible();

        // refresh validator
        document.formvalidator.attachToForm(js('#form-metadata'));

        setRelationAction(button);

        // Set bouton state in data block
        js(data).find('.add-btn').each(function () {
            setRelationAction(js(this));
        });

        // Set attribute bouton state in data block
        js(data).find('.attribute-add-btn').each(function () {
            setAttributeAction(js(this).parent());
        });
    }).fail(function () {
        bootbox.alert(Joomla.JText._('COM_EASYSDI_CATALOG_ERROR_ADD_RELATION', 'COM_EASYSDI_CATALOG_ERROR_ADD_RELATION'));
    }).always(function () {
        button.attr('disabled', false);
    });

});

/**
 * Remove fieldset from form
 */
js(document).on('click', '.remove-btn', function () {
    var id = this.id;
    var xpath = js(this).attr('data-xpath');

    sdiDangerConfirm(Joomla.JText._('COM_EASYSDI_CATALOG_DELETE_RELATION_CONFIRM', 'COM_EASYSDI_CATALOG_DELETE_RELATION_CONFIRM'), function (result) {
        if (result) {

            var uuid = getUuid('remove-btn', id);
            js.ajax({
                url: baseUrl + 'option=com_easysdi_catalog&task=ajax.removeNode&uuid=' + uuid,
                type: "GET",
                async: false,
                cache: false
            }).done(function () {
                js('#fds' + uuid).remove();
                setRelationAction(js('#add-btn' + xpath));
            }).fail(function () {
                bootbox.alert(Joomla.JText._('COM_EASYSDI_CATALOG_ERROR_REMOVE_RELATION', 'COM_EASYSDI_CATALOG_ERROR_REMOVE_RELATION'));
            });

        }
    });
});

/**
 * Collapse inner-fieldset
 */
js(document).on('click', '.collapse-btn', function () {
    var uuid = getUuid('collapse-btn', this.id);
    var button = js(this);
    js('#inner-fds' + uuid).toggle('fast', function () {
        if (js('#inner-fds' + uuid).is(':visible')) {
            button.children().first().removeClass('icon-arrow-right').addClass('icon-arrow-down');
        } else {
            button.children().first().removeClass('icon-arrow-down').addClass('icon-arrow-right');
        }
    });

});


/**
 * Open or close all fieldset
 */
js(document).on('click', '#btn_toggle_all', function () {
    toogleAll(js(this));
});

/**
 * 
 * @param {type} versions
 * @returns {String}
 * @deprecated use buildVersionsTree instead
 */
var buildDeletedTree = function (versions) {
    return buildVersionsTree(versions);
}

var buildVersionsTree = function (versions) {
    var body = '<ul>';

    js.each(versions, function (k, version) {
        body += '<li>' + version.resource_name + ' : ' + version.version_name + ' <a href="/index.php?option=com_easysdi_catalog&task=metadata.edit&id=' + version.metadata_id + '" target="_top"><i class="icon-edit"></i></a>';
        if (typeof version.children === 'undefined') {
            body += '</li>';
        } else {
            body += buildDeletedTree(version.children)
            body += '</li>';
        }


    });

    body += '</ul>'

    return body;
};

/**
 * AddTooltips on label and field
 * Warning $$ = motools
 * 
 * @returns void
 */
function addTooltips() {
    $$('.hasTooltip').each(function (el) {
        var title = el.get('title');
        if (title) {
            var parts = title.split('::', 2);
            el.store('tip:title', parts[0]);
            el.store('tip:text', parts[1]);
        }
    });

    new Tips($$('.hasTooltip'), {"maxTitleChars": 50, "fixed": false});
}

function setRelationAction(element) {
    var upperbound = js(element).attr('data-upperbound');
    var lowerbound = js(element).attr('data-lowerbound');
    var uuid = getUuid('add-btn', js(element).attr('id'));

    var occurance = js('.fds' + uuid).length;

    if (occurance == upperbound) {
        js('#add-btn' + uuid).hide();
    }

    if (occurance == lowerbound) {
        js('.fds' + uuid + ' a.remove-btn').hide();
    }

    if (occurance < upperbound) {
        js('#add-btn' + uuid).show();
    }

    if (occurance > lowerbound) {
        js('.fds' + uuid + ' a.remove-btn').show();
    }

    if (occurance < upperbound && occurance > lowerbound) {
        js('#add-btn' + uuid).show();
        js('.fds' + uuid + ' a.remove-btn').show();
    }
}

function setAttributeAction(element) {
    var upperbound = js(element).attr('data-upperbound');
    var lowerbound = js(element).attr('data-lowerbound');
    var buttonclass = js(element).attr('data-button-class');
    var occurance = js('.attribute-action' + buttonclass).length;

    if (occurance == 1) {
        js('.attribute-action' + buttonclass + '>a.attribute-add-btn').show();
        js('.attribute-action' + buttonclass + '>a.attribute-remove-btn').hide();
    } else if (occurance > lowerbound && occurance < upperbound) {
        js('.attribute-action' + buttonclass + '>a.attribute-add-btn').hide();
        js('.attribute-action' + buttonclass + '>a.attribute-add-btn').last().show();
        js('.attribute-action' + buttonclass + '>a.attribute-remove-btn').show();
    } else if (occurance == upperbound) {
        js('.attribute-action' + buttonclass + '>a.attribute-add-btn').hide();
        js('.attribute-action' + buttonclass + '>a.attribute-remove-btn').show();
    }
}

/**
 * 
 * @param {type} task
 * @returns {undefined}
 */
function searchResource(task) {
    js('#search_table_wrapper').hide();
    js('input[name="task"]').val(task);

    js.ajax({
        url: baseUrl + 'option=com_easysdi_catalog&task=' + task,
        type: js('#form_search_resource').attr('method'),
        data: js('#form_search_resource').serialize(),
        success: function (data) {
            var response = js.parseJSON(data);
            if (response.success) {
                if (response.total > 0) {
                    js('#import-btn').show();
                } else {
                    js('#import-btn').hide();
                }
                js('#search_table').dataTable().fnClearTable();
                js('#search_table').dataTable().fnAddData(response.result);
                js('#search_table_wrapper, #search_table').show();
            }
        }
    });
}

function importSwitch(task) {
    var actions = task.split('.');

    js.ajax({
        url: baseUrl + 'option=com_easysdi_catalog&task=' + actions[0] + '.' + actions[1] + '&id=' + actions[2],
        type: "GET",
        async: false,
        cache: false
    }).done(function (data) {
        var response = js.parseJSON(data);

        if (response.success) {
            js('.import_importref_id').val(response.result.id);
            if (response.result.cswservice_id !== null) {
                js('#importCswModal').modal('show');
            } else {
                js('#importXmlModal').modal('show');
            }
        }
    }).fail(function () {
        bootbox.alert(Joomla.JText._('COM_EASYSDI_CATALOG_ERROR_RETRIEVE_IMPORT_REF', 'COM_EASYSDI_CATALOG_ERROR_RETRIEVE_IMPORT_REF'));
    });
}

/**
 * Toogle all fieldset
 */
function toogleAll(button) {
    if (tabIsOpen) {
        button.text(Joomla.JText._('COM_EASYSDI_CATALOG_OPEN_ALL'));
        js('.inner-fds').hide();
        js('.collapse-btn>i').removeClass('icon-arrow-down').addClass('icon-arrow-right');
        tabIsOpen = false;
    } else {
        button.text(Joomla.JText._('COM_EASYSDI_CATALOG_CLOSE_ALL'));
        js('.inner-fds').show();
        js('.collapse-btn>i').removeClass('icon-arrow-right').addClass('icon-arrow-down');
        tabIsOpen = true;
    }
}

function addOrRemoveCheckbox(id, relid, parent_path, path) {
    var checked = js('#' + id).is(':checked');
    if (checked) {
        addToStructure(relid, parent_path);
    } else {
        removeFromStructure(path);
    }

}

function addToStructure(relid, parent_path) {
    js.ajax({
        url: baseUrl + 'option=com_easysdi_catalog&view=ajax&parent_path=' + parent_path + '&relid=' + relid,
        type: "GET",
        async: false,
        cache: false
    });

}

function allopen() {
    js('.inner-fds').show();
}

function confirmImport(task) {
    bootbox.confirm(Joomla.JText._('COM_EASYSDI_CATALOG_METADATA_SAVE_WARNING', 'COM_EASYSDI_CATALOG_METADATA_SAVE_WARNING'), function (result) {
        if (result) {
            importSwitch(task);
        }
    });
}

function confirmReplicate() {
    bootbox.confirm(Joomla.JText._('COM_EASYSDI_CATALOG_METADATA_SAVE_WARNING', 'COM_EASYSDI_CATALOG_METADATA_SAVE_WARNING'), function (result) {
        if (result) {
            js('#searchModal').modal('show');
        }
    });

}

function confirmReset() {
    bootbox.confirm(Joomla.JText._("COM_EASYSDI_CATALOG_METADATA_ARE_YOU_SURE", "COM_EASYSDI_CATALOG_METADATA_ARE_YOU_SURE"), function (result) {
        if (result) {

        }
    });
}

function removeFromStructure(id) {
    var uuid = getUuid('remove-btn-', id);

    js.ajax({
        url: baseUrl + 'option=com_easysdi_catalog&task=ajax.removeNode&uuid=' + uuid,
        type: "GET",
        async: false,
        cache: false
    }).done(function (data) {
        var response = js.parseJSON(data);
        return response.success;
    }).fail(function () {
        bootbox.alert(Joomla.JText._('COM_EASYSDI_CATALOG_ERROR_REMOVE', 'COM_EASYSDI_CATALOG_ERROR_REMOVE'));
    });
}

function confirmEmptyFile(id) {
    bootbox.confirm(Joomla.JText._("COM_EASYSDI_CATALOG_METADATA_ARE_YOU_SURE", "COM_EASYSDI_CATALOG_METADATA_ARE_YOU_SURE"), function (result) {
        if (result) {
            emptyFile(id);
        }
    });
}

function emptyFile(id) {
    var uuid = getUuid('empty-btn-', id);
    var replaceUuid = uuid.replace(/-/g, '_');
    js('#jform_' + replaceUuid + '_filetext').attr('value', '');
    js('#jform_' + replaceUuid + '_filehiddendelete').attr('value', '');
    js('#preview-' + uuid).hide();
    js('#empty-file-' + uuid).hide();
}

/**
 * 
 * @param {string} prefix
 * @param {string} string
 * @returns {array}
 */
function getUuid(prefix, string) {
    string = string.replace(prefix, '');
    return string;
}

function getOccuranceCount(className) {
    var nbr = js(className).length;
    return nbr;
}

function chosenRefresh() {
    js('select').chosen({
        disable_search_threshold: 10,
        allow_single_deselect: true,
        placeholder_text_multiple: Joomla.JText._('JGLOBAL_SELECT_SOME_OPTIONS', 'JGLOBAL_SELECT_SOME_OPTIONS'),
        placeholder_text_single: Joomla.JText._('JGLOBAL_SELECT_AN_OPTION', 'JGLOBAL_SELECT_AN_OPTION'),
        no_results_text: Joomla.JText._('JGLOBAL_SELECT_NO_RESULTS_MATCH', 'JGLOBAL_SELECT_NO_RESULTS_MATCH')
    });
}

function setBoundary(parentPath, value) {
    if (value == '')
        return;

    js.ajax({
        url: encodeURI(baseUrl + 'option=com_easysdi_catalog&task=ajax.getBoundaryByName&value=' + value),
        type: "GET",
        async: false,
        cache: false
    }).done(function (data) {
        var response = js.parseJSON(data);
        var replaceId = parentPath.replace(/-/g, '_');
        js('#jform_' + replaceId + '_sla_gmd_dp_geographicElement_sla_gmd_dp_EX_GeographicBoundingBox_sla_gmd_dp_northBoundLatitude_sla_gco_dp_Decimal').attr('value', response.northbound);
        js('#jform_' + replaceId + '_sla_gmd_dp_geographicElement_sla_gmd_dp_EX_GeographicBoundingBox_sla_gmd_dp_southBoundLatitude_sla_gco_dp_Decimal').attr('value', response.southbound);
        js('#jform_' + replaceId + '_sla_gmd_dp_geographicElement_sla_gmd_dp_EX_GeographicBoundingBox_sla_gmd_dp_eastBoundLongitude_sla_gco_dp_Decimal').attr('value', response.eastbound);
        js('#jform_' + replaceId + '_sla_gmd_dp_geographicElement_sla_gmd_dp_EX_GeographicBoundingBox_sla_gmd_dp_westBoundLongitude_sla_gco_dp_Decimal').attr('value', response.westbound);

        var map_parent_path = replaceId + '_sla_gmd_dp_geographicElement_sla_gmd_dp_EX_GeographicBoundingBox';
        drawBB(map_parent_path);
    });
}

function clearbbselect(parent_path) {

    js('#jform_' + parent_path + '_sla_sdi_dp_extentType_sla_gco_dp_CharacterString').val('').trigger('liszt:updated');
    js('#jform_' + parent_path + '_sla_gmd_dp_description_sla_gco_dp_CharacterString').val('').trigger('liszt:updated');
}

function drawBB(parent_path) {
    var top = js('#jform_' + parent_path + '_sla_gmd_dp_northBoundLatitude_sla_gco_dp_Decimal').attr('value');
    var bottom = js('#jform_' + parent_path + '_sla_gmd_dp_southBoundLatitude_sla_gco_dp_Decimal').attr('value');
    var right = js('#jform_' + parent_path + '_sla_gmd_dp_eastBoundLongitude_sla_gco_dp_Decimal').attr('value');
    var left = js('#jform_' + parent_path + '_sla_gmd_dp_westBoundLongitude_sla_gco_dp_Decimal').attr('value');
    if (top != '' && bottom != '' && left != '' && right != '') {

        var map = window['map_' + parent_path];
        var dest = new Proj4js.Proj(map.getProjection());
        var source = new Proj4js.Proj("EPSG:4326");
        var bottom_left = new Proj4js.Point(left, bottom);
        var top_right = new Proj4js.Point(right, top);
        Proj4js.transform(source, dest, bottom_left);
        Proj4js.transform(source, dest, top_right);
        var bounds = new OpenLayers.Bounds(bottom_left.x, bottom_left.y, top_right.x, top_right.y);
        var box = new OpenLayers.Feature.Vector(bounds.toGeometry());
        var layer = window['polygonLayer_' + parent_path];
        layer.addFeatures([box]);

        // re-set NSEW input values to avoid any projection changes
        js('#jform_' + parent_path + '_sla_gmd_dp_northBoundLatitude_sla_gco_dp_Decimal').val(top);
        js('#jform_' + parent_path + '_sla_gmd_dp_southBoundLatitude_sla_gco_dp_Decimal').val(bottom);
        js('#jform_' + parent_path + '_sla_gmd_dp_eastBoundLongitude_sla_gco_dp_Decimal').val(right);
        js('#jform_' + parent_path + '_sla_gmd_dp_westBoundLongitude_sla_gco_dp_Decimal').val(left);
    }
}


function calendarSetup(field) {
    js('#' + field).wrap('<div class="input-append"></div>');
    js('#' + field).after('<button class="btn" id="' + field + '_img"><i class="icon-calendar"></i></button>');

    Calendar.setup({
        // Id of the input field
        inputField: field,
        // Format of the input field
        ifFormat: "%Y-%m-%d",
        // Trigger for the calendar (button ID)
        button: field + "_img",
        // Alignment (defaults to "Bl")
        align: "Tl",
        singleClick: true,
        firstDay: 1
    });
}

function removeHidden() {
    js('.scope-hidden').remove();
}

function disableVisible() {

    js(':input[readonly][class*="validate-sdidate"], .scope-visible :input[class*="validate-sdidate"]').prop('disabled', true).removeAttr('readonly').removeClass('validate-sdidate validate-sdidatetime');
    js('fieldset.scope-visible').prev('.action a').remove();
    js('fieldset.scope-visible .remove-btn, fieldset.scope-visible .add-btn, fieldset.scope-visible .attribute-add-btn').remove();
    js('.scope-visible select').trigger("liszt:updated");
}

/**
 * Locks the form, shows an error message and scroll up the page.
 * @returns {void}
 */
function lockFormForSession() {
    //disable form
    disableCompleteForm()
    //show message
    Joomla.renderMessages({'error': ['<b>' + Joomla.JText._('COM_EASYSDI_CATALOG_ERROR_MD_LOCKED_TITLE', 'COM_EASYSDI_CATALOG_ERROR_MD_LOCKED_TITLE') + '</b><br/>' + Joomla.JText._('COM_EASYSDI_CATALOG_ERROR_MD_LOCKED_MESSAGE', 'COM_EASYSDI_CATALOG_ERROR_MD_LOCKED_MESSAGE')]});
    //move to top
    js("html, body").animate({scrollTop: 0}, 'slow');
}

/**
 * Disable all editable fields and button (except return and collapse form)
 * @returns {void}
 */
function disableCompleteForm() {
    //disable form
    js('.metadata-edit.front-end-edit #form-metadata select').prop('disabled', true).trigger("liszt:updated");
    js('.metadata-edit.front-end-edit #form-metadata button').prop('disabled', true);
    js('.metadata-edit.front-end-edit #form-metadata a.btn').not('.collapse-btn').prop('disabled', true).addClass('disabled');
    js('.metadata-edit.front-end-edit #form-metadata :input').filter(':text,:password,textarea').prop('disabled', true);
    js('.metadata-edit.front-end-edit .btn-toolbar #import').prop('disabled', true);
    js('.metadata-edit.front-end-edit .btn-toolbar #btn-reset').prop('disabled', true).addClass('disabled');
}

/**
 * easySDI custom bootbox confirm message with 'danger' button to match 
 * https://forge.easysdi.org/issues/1006 and https://forge.easysdi.org/issues/924
 * for 'risky actions'. Uses bootbox.dialog.
 * @param {string} message Text for the dialog
 * @param {function} cb Callback function
 * @returns void
 */
function sdiDangerConfirm(message, cb) {
    bootbox.dialog(message,
            [
                {
                    "label": Joomla.JText._('COM_EASYSDI_CORE_BOOTBOX_OVERRIDE_CANCEL', 'Cancel'),
                    "callback": function () {
                        if (typeof cb == 'function') {
                            cb(false);
                        }
                    }
                }, {
                    "label": Joomla.JText._('COM_EASYSDI_CORE_BOOTBOX_OVERRIDE_CONFIRM', 'Confirm'),
                    "class": "btn-danger",
                    "callback": function () {
                        if (typeof cb == 'function') {
                            cb(true);
                        }
                    }
                }
            ]);
}

/**
 * Decodes common HTML entities
 * @param {String} texte
 * @returns {String}
 */
function html_entity_decode(texte) {
    texte = texte.replace(/&quot;/g, '"'); // 34 22
    texte = texte.replace(/&amp;/g, '&'); // 38 26	
    texte = texte.replace(/&#39;/g, "'"); // 39 27
    texte = texte.replace(/&lt;/g, '<'); // 60 3C
    texte = texte.replace(/&gt;/g, '>'); // 62 3E
    texte = texte.replace(/&circ;/g, '^'); // 94 5E
    texte = texte.replace(/&lsquo;/g, '‘'); // 145 91
    texte = texte.replace(/&rsquo;/g, '’'); // 146 92
    texte = texte.replace(/&ldquo;/g, '“'); // 147 93
    texte = texte.replace(/&rdquo;/g, '”'); // 148 94
    texte = texte.replace(/&bull;/g, '•'); // 149 95
    texte = texte.replace(/&ndash;/g, '–'); // 150 96
    texte = texte.replace(/&mdash;/g, '—'); // 151 97
    texte = texte.replace(/&tilde;/g, '˜'); // 152 98
    texte = texte.replace(/&trade;/g, '™'); // 153 99
    texte = texte.replace(/&scaron;/g, 'š'); // 154 9A
    texte = texte.replace(/&rsaquo;/g, '›'); // 155 9B
    texte = texte.replace(/&oelig;/g, 'œ'); // 156 9C
    texte = texte.replace(/&#357;/g, ''); // 157 9D
    texte = texte.replace(/&#382;/g, 'ž'); // 158 9E
    texte = texte.replace(/&Yuml;/g, 'Ÿ'); // 159 9F
    texte = texte.replace(/&nbsp;/g, ' '); // 160 A0
    texte = texte.replace(/&iexcl;/g, '¡'); // 161 A1
    texte = texte.replace(/&cent;/g, '¢'); // 162 A2
    texte = texte.replace(/&pound;/g, '£'); // 163 A3
    texte = texte.replace(/&curren;/g, ' '); // 164 A4
    texte = texte.replace(/&yen;/g, '¥'); // 165 A5
    texte = texte.replace(/&brvbar;/g, '¦'); // 166 A6
    texte = texte.replace(/&sect;/g, '§'); // 167 A7
    texte = texte.replace(/&uml;/g, '¨'); // 168 A8
    texte = texte.replace(/&copy;/g, '©'); // 169 A9
    texte = texte.replace(/&ordf;/g, 'ª'); // 170 AA
    texte = texte.replace(/&laquo;/g, '«'); // 171 AB
    texte = texte.replace(/&not;/g, '¬'); // 172 AC
    texte = texte.replace(/&shy;/g, '­'); // 173 AD
    texte = texte.replace(/&reg;/g, '®'); // 174 AE
    texte = texte.replace(/&macr;/g, '¯'); // 175 AF
    texte = texte.replace(/&deg;/g, '°'); // 176 B0
    texte = texte.replace(/&plusmn;/g, '±'); // 177 B1
    texte = texte.replace(/&sup2;/g, '²'); // 178 B2
    texte = texte.replace(/&sup3;/g, '³'); // 179 B3
    texte = texte.replace(/&acute;/g, '´'); // 180 B4
    texte = texte.replace(/&micro;/g, 'µ'); // 181 B5
    texte = texte.replace(/&para/g, '¶'); // 182 B6
    texte = texte.replace(/&middot;/g, '·'); // 183 B7
    texte = texte.replace(/&cedil;/g, '¸'); // 184 B8
    texte = texte.replace(/&sup1;/g, '¹'); // 185 B9
    texte = texte.replace(/&ordm;/g, 'º'); // 186 BA
    texte = texte.replace(/&raquo;/g, '»'); // 187 BB
    texte = texte.replace(/&frac14;/g, '¼'); // 188 BC
    texte = texte.replace(/&frac12;/g, '½'); // 189 BD
    texte = texte.replace(/&frac34;/g, '¾'); // 190 BE
    texte = texte.replace(/&iquest;/g, '¿'); // 191 BF
    texte = texte.replace(/&Agrave;/g, 'À'); // 192 C0
    texte = texte.replace(/&Aacute;/g, 'Á'); // 193 C1
    texte = texte.replace(/&Acirc;/g, 'Â'); // 194 C2
    texte = texte.replace(/&Atilde;/g, 'Ã'); // 195 C3
    texte = texte.replace(/&Auml;/g, 'Ä'); // 196 C4
    texte = texte.replace(/&Aring;/g, 'Å'); // 197 C5
    texte = texte.replace(/&AElig;/g, 'Æ'); // 198 C6
    texte = texte.replace(/&Ccedil;/g, 'Ç'); // 199 C7
    texte = texte.replace(/&Egrave;/g, 'È'); // 200 C8
    texte = texte.replace(/&Eacute;/g, 'É'); // 201 C9
    texte = texte.replace(/&Ecirc;/g, 'Ê'); // 202 CA
    texte = texte.replace(/&Euml;/g, 'Ë'); // 203 CB
    texte = texte.replace(/&Igrave;/g, 'Ì'); // 204 CC
    texte = texte.replace(/&Iacute;/g, 'Í'); // 205 CD
    texte = texte.replace(/&Icirc;/g, 'Î'); // 206 CE
    texte = texte.replace(/&Iuml;/g, 'Ï'); // 207 CF
    texte = texte.replace(/&ETH;/g, 'Ð'); // 208 D0
    texte = texte.replace(/&Ntilde;/g, 'Ñ'); // 209 D1
    texte = texte.replace(/&Ograve;/g, 'Ò'); // 210 D2
    texte = texte.replace(/&Oacute;/g, 'Ó'); // 211 D3
    texte = texte.replace(/&Ocirc;/g, 'Ô'); // 212 D4
    texte = texte.replace(/&Otilde;/g, 'Õ'); // 213 D5
    texte = texte.replace(/&Ouml;/g, 'Ö'); // 214 D6
    texte = texte.replace(/&times;/g, '×'); // 215 D7
    texte = texte.replace(/&Oslash;/g, 'Ø'); // 216 D8
    texte = texte.replace(/&Ugrave;/g, 'Ù'); // 217 D9
    texte = texte.replace(/&Uacute;/g, 'Ú'); // 218 DA
    texte = texte.replace(/&Ucirc;/g, 'Û'); // 219 DB
    texte = texte.replace(/&Uuml;/g, 'Ü'); // 220 DC
    texte = texte.replace(/&Yacute;/g, 'Ý'); // 221 DD
    texte = texte.replace(/&THORN;/g, 'Þ'); // 222 DE
    texte = texte.replace(/&szlig;/g, 'ß'); // 223 DF
    texte = texte.replace(/&agrave;/g, 'à'); // 224 E0
    texte = texte.replace(/&aacute;/g, 'á'); // 225 E1
    texte = texte.replace(/&acirc;/g, 'â'); // 226 E2
    texte = texte.replace(/&atilde;/g, 'ã'); // 227 E3
    texte = texte.replace(/&auml;/g, 'ä'); // 228 E4
    texte = texte.replace(/&aring;/g, 'å'); // 229 E5
    texte = texte.replace(/&aelig;/g, 'æ'); // 230 E6
    texte = texte.replace(/&ccedil;/g, 'ç'); // 231 E7
    texte = texte.replace(/&egrave;/g, 'è'); // 232 E8
    texte = texte.replace(/&eacute;/g, 'é'); // 233 E9
    texte = texte.replace(/&ecirc;/g, 'ê'); // 234 EA
    texte = texte.replace(/&euml;/g, 'ë'); // 235 EB
    texte = texte.replace(/&igrave;/g, 'ì'); // 236 EC
    texte = texte.replace(/&iacute;/g, 'í'); // 237 ED
    texte = texte.replace(/&icirc;/g, 'î'); // 238 EE
    texte = texte.replace(/&iuml;/g, 'ï'); // 239 EF
    texte = texte.replace(/&eth;/g, 'ð'); // 240 F0
    texte = texte.replace(/&ntilde;/g, 'ñ'); // 241 F1
    texte = texte.replace(/&ograve;/g, 'ò'); // 242 F2
    texte = texte.replace(/&oacute;/g, 'ó'); // 243 F3
    texte = texte.replace(/&ocirc;/g, 'ô'); // 244 F4
    texte = texte.replace(/&otilde;/g, 'õ'); // 245 F5
    texte = texte.replace(/&ouml;/g, 'ö'); // 246 F6
    texte = texte.replace(/&divide;/g, '÷'); // 247 F7
    texte = texte.replace(/&oslash;/g, 'ø'); // 248 F8
    texte = texte.replace(/&ugrave;/g, 'ù'); // 249 F9
    texte = texte.replace(/&uacute;/g, 'ú'); // 250 FA
    texte = texte.replace(/&ucirc;/g, 'û'); // 251 FB
    texte = texte.replace(/&uuml;/g, 'ü'); // 252 FC
    texte = texte.replace(/&yacute;/g, 'ý'); // 253 FD
    texte = texte.replace(/&thorn;/g, 'þ'); // 254 FE
    texte = texte.replace(/&yuml;/g, 'ÿ'); // 255 FF
    return texte;
}

