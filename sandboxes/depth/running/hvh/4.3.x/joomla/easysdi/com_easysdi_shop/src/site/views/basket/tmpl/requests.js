var sendBasket = function() {
    jQuery('#myModalProcess').modal('show');
    processProgress('Initialisation');
    jQuery.ajax({
        url: jQuery('#adminForm').attr('action'),
        type: 'POST',
        data: jQuery('#adminForm').serialize()
    }).done(function(data) {
        var text = Joomla.JText._('COM_EASYSDI_SHOP_BASKET_PROCESS_PROGRESSING').replace('%1', data.treated).replace('%2', data.total);
        processProgress(text, data.rate);
        setTimeout(sendProduct, 500);
    });
    return false;
};

var sendProduct = function() {
    jQuery.ajax({
        url: 'index.php?option=com_easysdi_shop&task=basket.saveProduct',
        type: 'POST',
        cache: false,
        data: formToken
    }).done(function(data) {
        if ('undefined' !== typeof data.total) {
            var text = Joomla.JText._('COM_EASYSDI_SHOP_BASKET_PROCESS_PROGRESSING').replace('%1', data.treated).replace('%2', data.total);
            processProgress(text, data.rate);

            if (data.rate < 100)
                setTimeout(sendProduct, 100);
            else
                setTimeout(closeBasket, 1000);
        }
        else {
            for (var el in data) {
                Joomla.renderMessages({el: data[el]});
                jQuery('#myModalProcess').modal('hide');
            }
        }

    });
    return false;
};

var closeBasket = function() {
    processProgress(Joomla.JText._('COM_EASYSDI_SHOP_BASKET_PROCESS_ENDING'));
    jQuery.ajax({
        url: 'index.php?option=com_easysdi_shop&task=basket.finalizeSave',
        type: 'POST',
        cache: false,
        data: formToken
    }).done(function(data) {
        document.location = data.redirect;
    });
    return false;
};

//Call after user validates his extent drawing
function savePerimeter() {
    if (jQuery('#t-perimeter').val() == '')
    {
        jQuery('#perimeter-recap').empty();
    } else {
        jQuery("#progress").css('visibility', 'visible');

        var extent = {"id": jQuery('#t-perimeter').val(),
            "name": jQuery('#t-perimetern').val(),
            "surface": jQuery('#t-surface').val(),
            "allowedbuffer": jQuery('#allowedbuffer').val(),
            "buffer": jQuery('#buffer').val(),
            "levelcode": jQuery('#t-level').val(),
            "features": jQuery('#t-features').val()};

        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_easysdi_shop&task=addExtentToBasket",
            data: "item=" + JSON.stringify(extent)
        }).done(updateDisplay);
    }
}