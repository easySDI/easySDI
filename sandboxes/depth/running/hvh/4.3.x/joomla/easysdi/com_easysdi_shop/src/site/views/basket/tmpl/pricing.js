jQuery(document).ready(function() {
    checkTouState();

    thirdpartyInfoVisibility();

    jQuery('#toolbar button').on('click', function() {
        var task = jQuery(this).attr('rel');
        var t = jQuery('#features').val();
        if (jQuery('#features').val() === '') {
            jQuery('#modal-error').modal('show');
        } else {
            if (jQuery('#allowedbuffer').val() == 0) {
                jQuery('#perimeter-buffer').val('');
            }

            var format = new OpenLayers.Format.WMC({'layerOptions': {buffer: 0}});
            var text = format.write(minimap);
            jQuery('#wmc').val(text);

            var taskArray = task.split('.');
            jQuery('input[name=action]').val(taskArray[1]);

            jQuery('input[name=task]').val('basket.save');
            jQuery('input[name=option]').val('com_easysdi_shop');

            sendBasket();
            return false;
        }
    });
});

var thirdpartyInfoVisibility = function() {
    if (jQuery('select#thirdparty').val() != -1)
        jQuery('#thirdparty-info').show();
    else
        jQuery('#thirdparty-info').hide();
};

jQuery(document).on('change', 'select#thirdparty', function(e) {
    var tp = jQuery(e.target).val();

    jQuery.ajax({
        type: "POST",
        url: "index.php?option=com_easysdi_shop&task=basket.saveBasketToSession",
        data: "thirdparty=" + tp
    }).done(function(r) {
        thirdpartyInfoVisibility();
        //pricing
        updatePricing(r.pricing);
        return false;
    });
});

jQuery(document).on('click', '#btn-login', function() {
    document.location.href = 'index.php?option=com_users&view=login&return=' + btoa(document.location.href);
    return false;
});

jQuery(document).on('click', '#btn-create-account', function() {
    document.location.href = 'index.php?option=com_users&view=registration&return=' + btoa(document.location.href);
    return false;
});

jQuery(document).on('change', '#termsofuse', checkTouState);

jQuery(document).on('click', 'td.action_column>a', function() {
    removeFromBasket(jQuery(this).closest('tr').attr('rel'));
    return false;
});


/**
 * priceFormatter - a js implementation of the php method located in easysdi_shop.php
 * @param {mixed} price
 * @param {boolean} displayCurrency
 * @returns {String}
 */
var priceFormatter = function(price, displayCurrency) {
    if ('undefined' === typeof displayCurrency)
        displayCurrency = true;
    var c = displayCurrency ? ' ' + currency : '';

    if (price != '-' && price != 0)
        price = number_format(
                price,
                digit_after_decimal,
                decimal_symbol,
                digit_grouping_symbol
                );

    return price + c;
};

//
var updatePricing = function(pricing) {
    if (!pricing.isActivated)
        return;

    //total amount
    jQuery('span.pricingTotalAmountTI').html(priceFormatter(pricing.cal_total_amount_ti));
    jQuery('span#pricingTotalAmountTI-container').show();

    //suppliers
    jQuery.each(pricing.suppliers, function(supplierId, supplier) {
        //products
        jQuery.each(supplier.products, function(productId, product) {
            jQuery('table[rel=' + supplierId + ']>tbody>tr[rel=' + productId + ']>td.price_column').html(priceFormatter(product.cal_total_amount_ti)).show();
        });

        //footer
        jQuery('table[rel=' + supplierId + ']>tfoot>tr>td#supplier_cal_fee_ti').html(priceFormatter(supplier.cal_fee_ti));
        jQuery('table[rel=' + supplierId + ']>tfoot>tr>td#supplier_cal_total_amount_ti').html(priceFormatter(supplier.cal_total_amount_ti));
        jQuery('table[rel=' + supplierId + ']>tfoot>tr>td#supplier_cal_total_rebate_ti').html(priceFormatter(supplier.cal_total_rebate_ti));
        jQuery('table[rel=' + supplierId + ']>tfoot').show();
    });

    //platform
    jQuery('span.pricingFeeTI').html(priceFormatter(pricing.cal_fee_ti)).show();
    jQuery('#pricingTotal-table').show();
};

//
var number_format = function(number, decimals, dec_point, thousands_sep) {
    number = (number + '')
            .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                        .toFixed(prec);
            };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
            .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
                .join('0');
    }
    return s.join(dec);
};