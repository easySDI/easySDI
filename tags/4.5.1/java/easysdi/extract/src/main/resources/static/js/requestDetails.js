/* 
 * Copyright (C) 2017 arx iT
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */



/**
 * Permanently erases a file from the output folder of a request.
 *
 * @param {int} requestId The identifier of the request
 * @param {type} label The string that describes the request
 * @param {type} button The button that was clicked to trigger this action
 */
function deleteFile(requestId, label, button) {
    _executeFileAction(requestId, button, LANG_MESSAGES.requestDetails.deleteFileConfirm,
                       [$(button).attr('data-file-path'), label]);
}



/**
 * Permanently erases a request.
 *
 * @param {int} id The identifier of the request to delete
 * @param {type} label The string that describes the request to delete
 * @param {type} button The button that was clicked to trigger this action
 */
function deleteRequest(id, label, button) {
    _executeRequestAction(id, label, button, LANG_MESSAGES.requestDetails.deleteConfirm);
}


/**
 * Abandons the processing of a request.
 *
 * @param {int}     id      The identifier of the request to reject
 * @param {string}  label   The string that describes the request to reject
 * @param {string}  remark  The string entered by the user inform the customer about the reasons why the request was
 *                           rejected.
 * @param {Object}  button  The button that was clicked to trigger this action
 */
function rejectRequest(id, label, remark, button) {
    _executeRequestAction(id, label, button, LANG_MESSAGES.requestDetails.rejectConfirm, remark);
}



/**
 * Restarts the processing of a request from the beginning.
 *
 * @param {int}     requestId      The identifier of the request whose processing must be restarted
 * @param {string}  label          The string that describes the request whose processing must be restarted
 * @param {Object}  button         The button that was clicked to trigger this action
 */
function relaunchProcess(requestId, label, button) {
    _executeRequestAction(requestId, label, button, LANG_MESSAGES.requestDetails.relaunchProcessConfirm);
}



/**
 * Reruns the active task of a request.
 *
 * @param {int}     requestId      The identifier of the request whose current task must be rerun
 * @param {string}  label          The string that describes the request whose current task must be rerun
 * @param {Object}  button         The button that was clicked to trigger this action
 */
function restartCurrentTask(requestId, label, button) {
    _executeRequestAction(requestId, label, button, LANG_MESSAGES.requestDetails.restartTaskConfirm);
}



/**
 * Reruns the active task of a request.
 *
 * @param {int}     requestId      The identifier of the request whose current task must be rerun
 * @param {string}  label          The string that describes the request whose current task must be rerun
 * @param {Object}  button         The button that was clicked to trigger this action
 */
function retryExport(requestId, label, button) {
    _executeRequestAction(requestId, label, button, LANG_MESSAGES.requestDetails.retryExportConfirm);
}



/**
 * Reexamines if a request can be associated with a process
 *
 * @param {int}     requestId      The identifier of the request to rematch with a process
 * @param {string}  label          The string that describes the request to rematch
 * @param {Object}  button         The button that was clicked to trigger this action
 */
function retryMatching(requestId, label, button) {
    _executeRequestAction(requestId, label, button, LANG_MESSAGES.requestDetails.retryMatchingConfirm);
}



/**
 * Modifies a request that is currently in standby mode so that it can proceed.
 *
 * @param {int}     id      The identifier of the request to validate
 * @param {string}  label   The string that describes the request to validate
 * @param {string}  remark  The string entered by the user to give the customer further information about the
 *                           validation
 * @param {Object}  button  The button that was clicked to trigger this action
 */
function validateRequest(id, label, remark, button) {
    _executeRequestAction(id, label, button, LANG_MESSAGES.requestDetails.validateConfirm, remark);
}


/*************************** MAP **************************/

/**
 * Initializes the map and centers it on the perimeter of the current order.
 *
 * @param {String} orderWktGeometry a string representing the perimeter of the order as a WKT geometry
 * @param {Float}  geometryArea     the surface of the area for this order in square meters
 */
function loadOrderGeometryMap(orderWktGeometry, geometryArea) {
    
    initializeMap().then(function(map) {
        _addOrderGeometryToMap(orderWktGeometry, map);
        _initializeLayerSwitcher(map);
    });

    $('#orderAreaSize').text(_getAreaSizeText(geometryArea, 2));
}



/**
 * Creates a map to display the perimeter of the current order.
 *
 * @param {String} orderWktGeometry the string that contains the coordinates of the request extent in the
 *                                               WKT format
 * @param {ol.Map} map              the OpenLayers map to add the order geometry to
 */
function _addOrderGeometryToMap(orderWktGeometry, map) {
    var orderGeometryLayer = _createOrderGeometryLayer(orderWktGeometry, map.getView().getProjection());
    map.addLayer(orderGeometryLayer);
    map.getView().fit(orderGeometryLayer.getSource().getExtent(), {
        padding : [10, 10, 10, 10]
    });
}



/**
 * Builds a vector layer to display the extent of the current request.
 *
 * @param {String}             orderWktGeometry the string that contains the coordinates of the request extent in the
 *                                               WKT format
 * @param {ol.proj.Projection} mapProjection    the OpenLayers projection object used by the map
 * @returns {ol.layer.Vector} the layer to add to the map to show the perimeter of the request
 */
function _createOrderGeometryLayer(orderWktGeometry, mapProjection) {
    var wktFormat = new ol.format.WKT();
    var feature = wktFormat.readFeature(orderWktGeometry, {
        dataProjection : 'EPSG:4326',
        featureProjection : mapProjection
    });

    var orderGeometryStyle = new ol.style.Style({
        fill : new ol.style.Fill({
            color : 'rgba(126,237,24,0.5)'
        }),
        stroke : new ol.style.Stroke({
            color : '#723A09',
            width : 1.25
        })
    });

    return new ol.layer.Vector({
        title : LANG_MESSAGES.requestDetails.mapLayers.polygon.title,
        source : new ol.source.Vector({
            features : [feature]
        }),
        style : orderGeometryStyle
    });
}



/**
 * Computes a text representation of an area.
 *
 * @param {Number} rawAreaSize the area
 * @param {int} decimals the number of digits to round the area to
 * @returns {String} the area as a text
 */
function _getAreaSizeText(rawAreaSize, decimals) {
    var areaSizeUnit = "m²";
    var roundingFactor = Math.pow(10, decimals);
        var areaSize = rawAreaSize;

    if (areaSize >= 100000) {
        areaSize = rawAreaSize / 1000000;
        areaSizeUnit = "km²";
    }

    return (Math.round(areaSize * roundingFactor) / roundingFactor) + "\xA0" + areaSizeUnit;
}



/**
 * Adds a component that allows to switch layers on or off and change the base map.
 *
 * @param {ol.Map} map the OpenLayers map to add the layer switcher to
 */
function _initializeLayerSwitcher(map) {
    var layerSwitcher = new ol.control.LayerSwitcher({
        tipLabel: LANG_MESSAGES.requestDetails.layerSwitcher.tooltip
    });

    map.addControl(layerSwitcher);
}



/******************* BACKGROUND METHODS *******************/

/**
 * Carries an action on a request based on a button click, if the user confirms it.
 *
 * @param {Integer} requestId          the number that identifies the request to execute the action on
 * @param {String}  label              the string that describes the request to execute the action on
 * @param {Object}  button             the button that triggers the action to execute
 * @param {Object}  confirmationTexts  the object that contains the localized strings to ask the user for a
 *                                      confirmation of the action
 * @param {string}  remark             the string entered by the user to give the customer further information about
 *                                      the operation. Can be <code>null</code> if the action allows it.
 */
function _executeRequestAction(requestId, label, button, confirmationTexts, remark) {

    if (!requestId || isNaN(requestId) || !button || !confirmationTexts) {
        return;
    }

    var alertButtonsTexts = LANG_MESSAGES.generic.alertButtons;
    var message = confirmationTexts.message.replace('\{0\}', label);
    var confirmedCallback = function() {

        if (remark) {
            $('#remark').val(remark);
        }

        $('#actionForm').attr('action', $(button).attr('data-action'));
        $('#actionForm').submit();
    };

    showConfirm(confirmationTexts.title, message, confirmedCallback, null, alertButtonsTexts.yes,
            alertButtonsTexts.no);
}



/**
 * Carries an action on a request output file based on a button click, if the user confirms it.
 *
 * @param {Integer} requestId          the number that identifies the request to execute the action on
 * @param {Object}  button             the button that triggers the action to execute
 * @param {Object}  confirmationTexts  the object that contains the localized strings to ask the user for a
 *                                      confirmation of the action
 * @param {Array}  confirmationValues  the array that contains the values to insert instead of the placeholders in the
 *                                      confirmation message. Can be <code>null</code> if the message contains no
 *                                      placeholder.
 */
function _executeFileAction(requestId, button, confirmationTexts, confirmationValues) {

    if (!requestId || isNaN(requestId) || !button || !confirmationTexts) {
        return;
    }

    var alertButtonsTexts = LANG_MESSAGES.generic.alertButtons;
    var message = confirmationTexts.message;

    if (confirmationValues) {

        for (var valueIndex = 0; valueIndex < confirmationValues.length; valueIndex++) {
            message = message.replace('\{' + valueIndex + '\}', confirmationValues[valueIndex]);
        }
    }

    var confirmedCallback = function() {
        $('#actionForm').attr('action', $(button).attr('data-action'));
        $('#targetFile').val($(button).attr('data-file-path'));
        $('#actionForm').submit();
    };

    showConfirm(confirmationTexts.title, message, confirmedCallback, null, alertButtonsTexts.yes,
            alertButtonsTexts.no);
}



/**
 * Carries the appropriate action after a button click.
 *
 * @param {Object}   button         the button that was clicked to trigger this method
 * @param {Function} actionFunction the function to call to carry the action for the button
 * @param {String}   remarkFieldId  the string that identifies the item that contains the remark to pass to the action
 *                                  function, or <code>null</code> if it does not take a remark
 */
function _handleButtonClick(button, actionFunction, remarkFieldId) {
    var id = parseInt($('#requestId').val());
    var label = $('#requestLabel').val();

    if (!button || isNaN(id) || !label) {
        console.error("ERROR - Could not fetch the necessary info to process a click on "
                + ((button) ? button.id : "an action button"));
        return;
    }

    if (remarkFieldId) {
        actionFunction(id, label, $('#' + remarkFieldId).val(), button);
    } else {
        actionFunction(id, label, button);
    }
}



/********************* EVENT HANDLERS *********************/

$(function() {
    $('#standbyValidateButton').on('click', function() {
        _handleButtonClick(this, validateRequest, 'standbyValidateRemark');
    });

    $('#standbyCancelButton').on('click', function() {
        _handleButtonClick(this, rejectRequest, 'standbyCancelRemark');
    });

    $('#errorCancelButton').on('click', function() {
        _handleButtonClick(this, rejectRequest, 'errorCancelRemark');
    });

    $('#errorRelaunchButton').on('click', function() {
        _handleButtonClick(this, relaunchProcess);
    });

    $('#standbyRestartButton').on('click', function() {
        _handleButtonClick(this, relaunchProcess);
    });

    $('#errorRestartButton').on('click', function() {
        _handleButtonClick(this, restartCurrentTask);
    });


    $('#errorRetryMatchingButton').on('click', function() {
        _handleButtonClick(this, retryMatching);
    });


    $('#errorRetryExportButton').on('click', function() {
        _handleButtonClick(this, retryExport);
    });

    $('#requestDeleteButton').on('click', function() {
        _handleButtonClick(this, deleteRequest);
    });

    $('.file-delete-button').on('click', function() {
        _handleButtonClick(this, deleteFile);
    });

    $('#file-upload-button').on('click', function() {
        $('#filesToAdd').click();
    });

    $('#filesToAdd').on('change', function() {
        $('#file-upload-button').prop('disabled', true);
        $('#actionForm').attr('enctype', 'multipart/form-data');
        $('#actionForm').attr('action', $('#file-upload-button').attr('data-action'));
        $('#actionForm').submit();
    });
});