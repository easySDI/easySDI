/**
 * @version     4.4.5
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

jQuery(document).ready(function () {

    /* updates indicator name in the reportParametersBox on madal display */
    jQuery(document).on("click", ".lunch-modal", function () {
        jQuery("#report-indicator").val(jQuery(this).data('id'));
        updateReportingDates();
    });

    /*Color progress bars*/
    for (var i = 0; i < com_easysdi_dahboard_graphcolours.length; i++)
    {
        jQuery(".bar-" + (i + 1)).css('background-color', com_easysdi_dahboard_graphcolours[i]);
        jQuery(".bar-" + (i + 1)).css('background-image', 'linear-gradient(to bottom, ' + LightenDarkenColor(com_easysdi_dahboard_graphcolours[i], 20) + ', ' + LightenDarkenColor(com_easysdi_dahboard_graphcolours[i], -20) + ')');
    }

    hideOrgFilterIfOnlyOne();
});

/**
 * Called when the date dropdown changes
 * @returns {void}
 */
function dateFiltersChanged() {
    var f = getFiltersValue();
    if (f.fromTo == 0) {
        showCustomDateFilter();
    } else {
        hideCustomDateFilter();
    }
    triggerFilterUpdate();
}

/**
 * Hide avanced date filters
 * @returns {void}
 */
function hideCustomDateFilter() {
    jQuery('.sdi-dashboard-custom-dates').hide();
}

/**
 * Show avanced date filters
 * @returns {void}
 */
function showCustomDateFilter() {
    jQuery('.sdi-dashboard-custom-dates').show();

}

/**
 * If there's only on organism, hide select
 * @returns {void}
 */
function hideOrgFilterIfOnlyOne() {
    if (jQuery('#filter-organism option').size() == 1) {
        jQuery('#filterorganism').hide();
    }
}

/* triggerFilterUpdate()
 * triggers "dashboardFiltersUpdated" event 
 * that all indicators listen to (for refresh).
 * @returns {void}
 */
function triggerFilterUpdate() {
    //get current values
    var f = getFiltersValue();

    //Triggers the event for indicators
    jQuery.event.trigger({
        type: "dashboardFiltersUpdated",
        organism: f.organism,
        timestart: f.timestart,
        timeend: f.timeend
    });
    //update links
    updateReportLinks(f);
}

/**
 * updateReportLinks(filters)
 * Update link for reports (csv, pdf...) with filters
 * @param {object} filters from getFiltersValue
 * @returns {void}
 */
function updateReportLinks(filters) {
    //sdi-dashboard-report-link
    jQuery(".sdi-dashboard-report-link").each(function () {
        var reportFormat = jQuery(this).data('sdiReportFormat');
        var reportIndicator = jQuery(this).data('sdiReportIndicator');
        var url = 'index.php?option=com_easysdi_dashboard&task=getData&indicator=' + reportIndicator +
                '&organism=' + filters.organism +
                '&timestart=' + filters.timestart +
                '&timeend=' + filters.timeend +
                '&dataformat=' + reportFormat +
                '&format=raw';
        jQuery(this).attr('href', url);
    });
}


/*
 * checkCustomReportTime
 * check that from and to dates are coherent
 * @returns {boolean} True if dates are OK
 */
function checkCustomReportTime() {
    //jQuery("#reporting_date_from")
    var dateFrom = Date.parse(jQuery("#reporting_date_from").val());
    var dateTo = Date.parse(jQuery("#reporting_date_to").val());
    var tsFrom = dateFrom.getTime() / 1000;
    var tsTo = dateTo.getTime() / 1000;
    if (tsFrom < tsTo) {
        jQuery('#report-timestart').val(tsFrom);
        jQuery('#report-timeend').val(tsTo);
        return true;
    }
    else {
        alert(Joomla.JText._('COM_EASYSDI_DASHBOARD_ERROR_DATES_PROBLEM'));
        return false;
    }
}


/* getFiltersValue
 * returns a filter object with actual values
 * @returns {Object} filters with values
 */
function getFiltersValue() {
    var filters = new Object();
    filters.organism = jQuery('#filter-organism :selected').val();
    filters.fromTo = jQuery('#filter-date :selected').val();
    if (filters.fromTo == 0) {
        //"from" custom date
        filters.timestart = Math.ceil((dateToTimestamp(jQuery('#sdi-dashboard-custom-from').val(), false)) / 1000);
        //"to" custom date
        var end = dateToTimestamp(jQuery('#sdi-dashboard-custom-to').val(), true);
        if (end == 0) {
            end = Date.now();
        }
        filters.timeend = Math.floor((end) / 1000);
    } else {
        filters.timestart = filters.fromTo.split(';')[0];
        filters.timeend = filters.fromTo.split(';')[1];
    }

    return filters;
}

function dateToTimestamp(dateString, endOfDay) {

    var dateArray = dateString.split("-");
    if (!dateArray[2]) {
        return 0;
    }
    // var theDate = new Date(dateArray[0], dateArray[1] - 1, dateArray[2], (endOfDay ? 23 : 0), (endOfDay ? 59 : 0), (endOfDay ? 59 : 0), (endOfDay ? 999 : 0));
    var theDate = new Date();
    theDate.setUTCFullYear(dateArray[0]);
    theDate.setUTCMonth(dateArray[1] - 1);
    theDate.setUTCDate(dateArray[2]);
    theDate.setUTCHours(endOfDay ? 23 : 0);
    theDate.setUTCMinutes(endOfDay ? 59 : 0);
    theDate.setUTCSeconds(endOfDay ? 59 : 0);
    theDate.setUTCMilliseconds(0);
    return (theDate.getTime());
}

/*
 * LightenDarkenColor
 * @params {string} col : a html color, {integer} amount : a positive or negative value
 * @retuns {string} a html color
 * Usage :
 * Lighten
 * var NewColor = LightenDarkenColor("#F06D06", 20); 
 * 
 * Darken
 * var NewColor = LightenDarkenColor("#F06D06", -20);
 * 
 * Source: http://css-tricks.com/snippets/javascript/lighten-darken-color/
 */
function LightenDarkenColor(col, amt) {

    var usePound = false;
    if (col[0] == "#") {
        col = col.slice(1);
        usePound = true;
    }
    var num = parseInt(col, 16);
    var r = (num >> 16) + amt;
    if (r > 255)
        r = 255;
    else if (r < 0)
        r = 0;
    var b = ((num >> 8) & 0x00FF) + amt;
    if (b > 255)
        b = 255;
    else if (b < 0)
        b = 0;
    var g = (num & 0x0000FF) + amt;
    if (g > 255)
        g = 255;
    else if (g < 0)
        g = 0;
    return (usePound ? "#" : "") + (g | (b << 8) | (r << 16)).toString(16);
}

/*
 * 
 * @returns {undefined}
 */
function toggleResutlDiv(indicatorDiv, status) {
    switch (status) {
        case 'result-success':
            jQuery(indicatorDiv + " .result-success").fadeIn();
            jQuery(indicatorDiv + " .no-result").hide();
            jQuery(indicatorDiv + " .waiting-for-result").hide();
            break;
        case 'no-result':
            jQuery(indicatorDiv + " .result-success").hide();
            jQuery(indicatorDiv + " .no-result").fadeIn();
            jQuery(indicatorDiv + " .waiting-for-result").hide();
            break;
        case 'waiting-for-result':
            jQuery(indicatorDiv + " .result-success").hide();
            jQuery(indicatorDiv + " .no-result").hide();
            jQuery(indicatorDiv + " .waiting-for-result").fadeIn();
            break;
    }
}

function dashboardFillTable(indicatorName, json) {

    //empty header
    jQuery("#div_" + indicatorName + " .result-success thead tr").empty();
    //fill header
    jQuery.each(json.columns_title, function (key, value) {
        jQuery("#div_" + indicatorName + " .result-success thead tr:last").append('<th>' + value + '</th>');
    });
    //empty table
    jQuery("#div_" + indicatorName + " .result-success tbody").empty();
    //fill table
    jQuery.each(json.data, function (key, value) {
        jQuery("#div_" + indicatorName + " .result-success tbody:last").append('<tr>');
        jQuery.each(value, function (k, v) {
            jQuery("#div_" + indicatorName + " .result-success tbody tr:last").append('<td>' + v + '</td>');
        });
        jQuery("#div_" + indicatorName + " .result-success tbody:last").append('</tr>');
    });
}





