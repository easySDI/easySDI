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



/**
 * Initializes the map and centers it on the perimeter of the current order.
 *
 * @param {String} orderWktGeometry a string representing the perimeter of the order as a WKT geometry
 * @param {Float}  geometryArea     the surface of the area for this order in square meters
 */
function loadOrderGeometryMap(orderWktGeometry, geometryArea) {
    var rasterLayer = new ol.layer.Tile({
        source : new ol.source.OSM()
    });

    var mapProjection = rasterLayer.getSource().getProjection();
    var wktFormat = new ol.format.WKT();
    var feature = wktFormat.readFeature(orderWktGeometry, {
        dataProjection : 'EPSG:4326',
        featureProjection : rasterLayer.getSource().getProjection().getCode()
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

    var vector = new ol.layer.Vector({
        source : new ol.source.Vector({
            features : [feature]
        }),
        style : orderGeometryStyle
    });

    var map = new ol.Map({
        layers : [rasterLayer, vector],
        target : 'orderMap'
    });
    map.getView().fit(feature.getGeometry(), {
        padding : [10, 10, 10, 10]
    });

    $('#orderAreaSize').text(getAreaSizeText(geometryArea, 2));
}



/**
 * Computes a text representation of an area.
 *
 * @param {Number} rawAreaSize the area
 * @param {int} decimals the number of digits to round the area to
 * @returns {String} the area as a text
 */
function getAreaSizeText(rawAreaSize, decimals) {
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
 * Obtains the geodesic size of a polygon area
 *
 * @param {Polygon}  polygon  the polygon to measure
 * @param {Map}      map      the map that the polygon is coming from
 * @returns {Number} the area in square meters
 */
function getGeodesicArea(polygon, map) {
    var wgs84Sphere = new ol.Sphere(6378137);
    var sourceProjection = map.getView().getProjection();
    var geometry = (polygon.clone().transform(sourceProjection, 'EPSG:4326'));
    var coordinates = geometry.getLinearRing(0).getCoordinates();
    return Math.abs(wgs84Sphere.geodesicArea(coordinates));
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
        console.log("ERROR - Could not fetch the necessary info to process a click on "
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
});