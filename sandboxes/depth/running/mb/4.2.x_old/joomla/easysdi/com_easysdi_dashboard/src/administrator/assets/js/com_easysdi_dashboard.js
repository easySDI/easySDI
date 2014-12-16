/**
 * @version     4.0.0
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

jQuery(document).ready(function() {

    /* updates indicator name in the reportParametersBox on madal display */
    jQuery(document).on("click", ".lunch-modal", function() {
        jQuery("#report-indicator").val(jQuery(this).data('id'));
        updateReportingDates();
    });

    /*Color progress bars*/
    for (var i = 0; i < com_easysdi_dahboard_graphcolours.length; i++)
    {
        jQuery(".bar-"+(i+1)).css('background-color',com_easysdi_dahboard_graphcolours[i]);
        jQuery(".bar-"+(i+1)).css('background-image','linear-gradient(to bottom, '+LightenDarkenColor(com_easysdi_dahboard_graphcolours[i],20)+', '+LightenDarkenColor(com_easysdi_dahboard_graphcolours[i],-20)+')');
    }
});
/* triggerFilterUpdate()
 * triggers "dashboardFiltersUpdated" event 
 * that all indicators listen to (for refresh).
 */
function triggerFilterUpdate() {
    //get current values
    var f = getFiltersValue();
    //Change value for reporting
    jQuery("#reporting-date").val(f.fromTo);
    jQuery("#dashboard_period_label_from").html(yyyymmdd(new Date(f.timestart * 1000)));
    jQuery("#dashboard_period_label_to").html(yyyymmdd(new Date(f.timeend * 1000)));
    //Update chosen fro bootstrap select
    jQuery("#reporting-date").trigger("liszt:updated");
    //Triggers the event for indicators
    jQuery.event.trigger({
        type: "dashboardFiltersUpdated",
        organism: f.organism,
        timestart: f.timestart,
        timeend: f.timeend
    });
}

/*
 * updateReportingDates
 * updates the 2 fields (from and to date) in the report time choose box
 * 
 */
function updateReportingDates() {

    // Display or hide advanced date for reporting
    var selectTimeVal = jQuery("#reporting-date").val();
    if (selectTimeVal == -1) { //custom
        jQuery(".advanced-time-reporting").show();
    } else {
        jQuery(".advanced-time-reporting").hide();
        //updates the fields with timerage selected
        jQuery("#reporting_date_from").val(yyyymmdd(new Date(selectTimeVal.split(';')[0] * 1000)));
        jQuery("#reporting_date_to").val(yyyymmdd(new Date(selectTimeVal.split(';')[1] * 1000)));
        jQuery("#report-timestart").val(selectTimeVal.split(';')[0]);
        jQuery("#report-timeend").val(selectTimeVal.split(';')[1]);
    }
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

/*
 * yyyymmdd
 * @param {js timestamp} timestamp
 * @returns {String} Date formatted YYYY-mm-dd
 */
function yyyymmdd(timestamp) {
    var yyyy = timestamp.getFullYear().toString();
    var mm = (timestamp.getMonth() + 1).toString(); // getMonth() is zero-based         
    var dd = timestamp.getDate().toString();
    return yyyy + '-' + (mm[1] ? mm : "0" + mm[0]) + '-' + (dd[1] ? dd : "0" + dd[0]);
}

/* getFiltersValue
 * returns a filter object with actual values
 * @returns {Object} filters with values
 */
function getFiltersValue() {
    var filters = new Object();
    filters.organism = jQuery('#filter-organism :selected').val();
    filters.fromTo = jQuery('#filter-date :selected').val();
    filters.timestart = filters.fromTo.split(';')[0];
    filters.timeend = filters.fromTo.split(';')[1];
    return filters;
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

/*
 * GenerateReport
 * If dates are OK, submit the reporting hidden form
 * 
 */
function generateReport() {
    updateReportingDates();
    if (checkCustomReportTime()) {
        jQuery("#report-organism").val(jQuery("#filter-organism").val());
        jQuery("#report-dataformat").val(jQuery("#reporting-format").val());
        jQuery("#report-limit").val(jQuery("#reporting-limit").val());
        jQuery('#hiddenReportForm').submit();
    }

}



