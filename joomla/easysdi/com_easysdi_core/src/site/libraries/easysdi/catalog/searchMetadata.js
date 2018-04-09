/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

js = jQuery.noConflict();

js('document').ready(function() {
    
    
    /**
     * Show searchtype button group, when advenced fieldset exist
     */
    if(js('fieldset[name="advanced"]').length > 0){
        js('#searchtype').show();
    }

    /**
     * Catch click event on searchtype button group
     */
    js('.searchtype').click(function() {
       toogleAdvanced();
    });

    /**
     * Set Calendar Type for "from" and "to" date field
     */
    js('.fromtodatefield').each(function() {
        Calendar.setup({
            // Id of the input field
            inputField: js(this).attr('id'),
            // Format of the input field
            ifFormat: "%Y-%m-%d",
            // Trigger for the calendar (button ID)
            button: js(this).attr('id') + "_img",
            // Alignment (defaults to "Bl")
            align: "Tl",
            singleClick: true,
            firstDay: 1
        });

    });
});

/**
 * Show or hide advanced fieldset
 */
function toogleAdvanced() {
    js('fieldset[name="advanced"]').toggle('fast');
}

/**
 * Submit form
 */
function submitForm(){
    js('#searchform').submit();
}

