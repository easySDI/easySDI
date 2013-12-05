js = jQuery.noConflict();

js('document').ready(function() {
    
    /**
     * Set submit button state at document ready
     */
    if(js('.cbx-resourcetype').length > 0){
        setSubmitBtnState();
    }
    
    /**
     * Show searchtype button group, when advenced fieldset exist
     */
    if(js('fieldset[name="advanced"]').length > 0){
        js('#searchtype').show();
    }

    /**
     * Set submit button state on click on resource type checkboxes
     */
    js('.cbx-resourcetype').on('click', setSubmitBtnState);

    /**
     * Catch click event on searchtype button group
     */
    js('.searchtype').click(function() {
        var btn = js(this);
        if (btn.hasClass('active')) {
            return;
        }
        
        showAdvanced();

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
function showAdvanced() {
    js('fieldset[name="advanced"]').toggle('fast', function() {
        js('.searchtype').each(function() {
            if (js(this).hasClass('active')) {
                js(this).removeClass('active');
            } else {
                js(this).addClass('active');
            }
        });
    });
}

/**
 * Set submit button state
 */
function setSubmitBtnState(){
   if(js('.cbx-resourcetype:checked').length > 0){
       js('#btn-submit').removeAttr('disabled');
   }else{
       js('#btn-submit').attr('disabled','disabled');
   }
    
}

/**
 * Submit form
 */
function submitForm(){
    js('#searchform').submit();
}

