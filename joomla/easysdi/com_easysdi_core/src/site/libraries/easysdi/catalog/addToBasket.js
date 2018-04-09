/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

var request;

Joomla.submitbutton = function(id)
{
    if (document.formvalidator.isValid(document.id('adminForm'+id))) {
        jQuery('#system-message-container').empty();
        addtobasket(id);
    }

}

function addtobasket(id) {

    //var diffusion = jQuery('#diffusion_id').val();
    var diffusion = id;
    var cmd = {"id": diffusion, "properties": []};

    //templates JSON
    //    var properties = {"id": 6, 
    //                      "properties": [
    //                                        {"id": 2, "values": [{"id": 2, "value": "valeur-1"}, {"id": 3, "value": "valeur-2"}]}, 
    //                                        {"id": 3, "values": [{"id": 5, "value": "text-simpe"}]}, 
    //                                        {"id": 4, "values": [{"id": 8, "value": "check-box-3"}]}
    //                                    ]
    //                     };


     jQuery('#adminForm'+id).find(".sdi-shop-property-list").each(function() {
        var count = jQuery(this).find(":selected").length;
        if (count === 0) {
            return;
        }
        var currentId = jQuery(this).attr('id');
        var value = {"id": currentId, values: []};
        jQuery(this).find(":selected").map(function() {
            value.values.push({"id": this.value, "value": this.text});
        });

        cmd.properties.push(value);
    });

    jQuery('#adminForm'+id).find(".sdi-shop-property-text").each(function() {
        var currentId = jQuery(this).attr('id');
        var value = {"id": currentId, values: [{"id": jQuery(this).attr('propertyvalue_id'), "value": jQuery(this).val()}]};
        cmd.properties.push(value);
    });

     jQuery('#adminForm'+id).find(".sdi-shop-property-checkbox").each(function() {
        var count = jQuery(this).find(":checked").length;
        if (count === 0) {
            return;
        }
        var currentId = jQuery(this).attr('id');
        var value = {"id": currentId, values: []};
        jQuery(this).find(":checked").map(function() {
            value.values.push({"id": this.value, "value": jQuery('#' + this.id).next('label').text()});
        });
        cmd.properties.push(value);
    });

    jQuery.ajax({
        url: "index.php?option=com_easysdi_shop&task=addToBasket",//&item=" + JSON.stringify(cmd),
        type: "POST",
        data : {'item' : JSON.stringify(cmd)},
        success: function(data) {
            if(window.updateBasketContent){
                updateBasketContent(data);
            }else{
                window.parent.updateBasketContent(data);
            }
        }
    });

}


