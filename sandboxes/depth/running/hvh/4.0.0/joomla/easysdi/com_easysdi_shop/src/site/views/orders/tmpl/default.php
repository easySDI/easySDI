<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

        const property_list = 1;
        const property_multiplelist = 2;
        const property_checkbox = 3;
        const property_text = 4;
        const property_textarea = 5;
        const property_message = 6;

$db = JFactory::getDbo();
$db->setQuery('SELECT  d.id as diffusion, pv.id as propertyvalue, p.id as property, p.mandatory as propertymandatory, p.propertytype_id as propertytype FROM #__sdi_diffusion d 
    INNER JOIN #__sdi_diffusion_propertyvalue dpv ON dpv.diffusion_id = d.id
    INNER JOIN #__sdi_propertyvalue pv ON pv.id = dpv.propertyvalue_id
    INNER JOIN #__sdi_property p ON p.id = pv.property_id');

$items = $db->loadObjectList();


?>
<script>
    var request;

    function addtobasket() {
        
        request = false;
        if (window.XMLHttpRequest) {
            request = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            try {
                request = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    request = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {
                    request = false;
                }
            }
        }
        if (!request)
            return;

        var properties = {"id":6,"properties":[{"id":2,"values":[{"id":2,"value":"valeur-1"},{"id":3,"value":"valeur-2"}]},{"id":3,"values":[{"id":5,"value":"text-simpe"}]},{"id":4,"values":[{"id":8,"value":"check-box-3"}]}]};
        var query = "index.php?option=com_easysdi_shop&task=addToBasket&item="+ JSON.stringify(properties);

        jQuery("#progress").css('visibility', 'visible');
        request.onreadystatechange = updateBasketContent;
        request.open("GET", query, true);
        request.send(null);
    }
    
    function addtobasket2() {
        
        request = false;
        if (window.XMLHttpRequest) {
            request = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            try {
                request = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    request = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {
                    request = false;
                }
            }
        }
        if (!request)
            return;

        var properties = {"id":10,"properties":[{"id":2,"values":[{"id":2,"value":"valeur-1"}]},{"id":3,"values":[{"id":5,"value":"text-simpe"}]},{"id":4,"values":[{"id":8,"value":"check-box-3"}]}]};
        var query = "index.php?option=com_easysdi_shop&task=addToBasket&item="+ JSON.stringify(properties);

        jQuery("#progress").css('visibility', 'visible');
        request.onreadystatechange = updateBasketContent;
        request.open("GET", query, true);
        request.send(null);
    }

</script>
<div>
    <button class="btn btn-success btn-large" onclick="addtobasket();">Add to basket</button>
</div>

<div>
    <button class="btn btn-success btn-large" onclick="addtobasket2();">Add to basket</button>
</div>
<?php

foreach ($items as $item):
    var_dump($item);
endforeach;
?>
    
<div class="items">
    <ul class="items_list">
        <?php $show = false; ?>
        <?php foreach ($this->items as $item) : ?>


            <?php
            if ($item->state == 1 || ($item->state == 0 && JFactory::getUser()->authorise('core.edit.own', ' com_easysdi_shop.order.' . $item->id))):
                $show = true;
                ?>
                <li>
                    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=order&id=' . (int) $item->id); ?>"><?php echo $item->guid; ?></a>
                    <?php
                    if (JFactory::getUser()->authorise('core.edit.state', 'com_easysdi_shop.order.' . $item->id)):
                        ?>
                        <a href="javascript:document.getElementById('form-order-state-<?php echo $item->id; ?>').submit()"><?php if ($item->state == 1): echo JText::_("COM_EASYSDI_SHOP_UNPUBLISH_ITEM");
            else: echo JText::_("COM_EASYSDI_SHOP_PUBLISH_ITEM");
            endif; ?></a>
                        <form id="form-order-state-<?php echo $item->id ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
                            <input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
                            <input type="hidden" name="jform[guid]" value="<?php echo $item->guid; ?>" />
                            <input type="hidden" name="jform[alias]" value="<?php echo $item->alias; ?>" />
                            <input type="hidden" name="jform[created]" value="<?php echo $item->created; ?>" />
                            <input type="hidden" name="jform[modified_by]" value="<?php echo $item->modified_by; ?>" />
                            <input type="hidden" name="jform[modified]" value="<?php echo $item->modified; ?>" />
                            <input type="hidden" name="jform[ordering]" value="<?php echo $item->ordering; ?>" />
                            <input type="hidden" name="jform[state]" value="<?php echo (int) !((int) $item->state); ?>" />
                            <input type="hidden" name="jform[checked_out]" value="<?php echo $item->checked_out; ?>" />
                            <input type="hidden" name="jform[checked_out_time]" value="<?php echo $item->checked_out_time; ?>" />
                            <input type="hidden" name="jform[name]" value="<?php echo $item->name; ?>" />
                            <input type="hidden" name="jform[access]" value="<?php echo $item->access; ?>" />
                            <input type="hidden" name="jform[asset_id]" value="<?php echo $item->asset_id; ?>" />
                            <input type="hidden" name="jform[ordertype_id]" value="<?php echo $item->ordertype_id; ?>" />
                            <input type="hidden" name="jform[orderstate_id]" value="<?php echo $item->orderstate_id; ?>" />
                            <input type="hidden" name="jform[user_id]" value="<?php echo $item->user_id; ?>" />
                            <input type="hidden" name="jform[thirdparty_id]" value="<?php echo $item->thirdparty_id; ?>" />
                            <input type="hidden" name="jform[buffer]" value="<?php echo $item->buffer; ?>" />
                            <input type="hidden" name="jform[surface]" value="<?php echo $item->surface; ?>" />
                            <input type="hidden" name="jform[remark]" value="<?php echo $item->remark; ?>" />
                            <input type="hidden" name="jform[sent]" value="<?php echo $item->sent; ?>" />
                            <input type="hidden" name="jform[completed]" value="<?php echo $item->completed; ?>" />
                            <input type="hidden" name="option" value="com_easysdi_shop" />
                            <input type="hidden" name="task" value="order.save" />
                        <?php echo JHtml::_('form.token'); ?>
                        </form>
                        <?php
                    endif;
                    if (JFactory::getUser()->authorise('core.delete', 'com_easysdi_shop.order.' . $item->id)):
                        ?>
                        <a href="javascript:document.getElementById('form-order-delete-<?php echo $item->id; ?>').submit()"><?php echo JText::_("COM_EASYSDI_SHOP_DELETE_ITEM"); ?></a>
                        <form id="form-order-delete-<?php echo $item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
                            <input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
                            <input type="hidden" name="jform[guid]" value="<?php echo $item->guid; ?>" />
                            <input type="hidden" name="jform[alias]" value="<?php echo $item->alias; ?>" />
                            <input type="hidden" name="jform[created_by]" value="<?php echo $item->created_by; ?>" />
                            <input type="hidden" name="jform[created]" value="<?php echo $item->created; ?>" />
                            <input type="hidden" name="jform[modified_by]" value="<?php echo $item->modified_by; ?>" />
                            <input type="hidden" name="jform[modified]" value="<?php echo $item->modified; ?>" />
                            <input type="hidden" name="jform[ordering]" value="<?php echo $item->ordering; ?>" />
                            <input type="hidden" name="jform[state]" value="<?php echo $item->state; ?>" />
                            <input type="hidden" name="jform[checked_out]" value="<?php echo $item->checked_out; ?>" />
                            <input type="hidden" name="jform[checked_out_time]" value="<?php echo $item->checked_out_time; ?>" />
                            <input type="hidden" name="jform[name]" value="<?php echo $item->name; ?>" />
                            <input type="hidden" name="jform[access]" value="<?php echo $item->access; ?>" />
                            <input type="hidden" name="jform[asset_id]" value="<?php echo $item->asset_id; ?>" />
                            <input type="hidden" name="jform[ordertype_id]" value="<?php echo $item->ordertype_id; ?>" />
                            <input type="hidden" name="jform[orderstate_id]" value="<?php echo $item->orderstate_id; ?>" />
                            <input type="hidden" name="jform[user_id]" value="<?php echo $item->user_id; ?>" />
                            <input type="hidden" name="jform[thirdparty_id]" value="<?php echo $item->thirdparty_id; ?>" />
                            <input type="hidden" name="jform[buffer]" value="<?php echo $item->buffer; ?>" />
                            <input type="hidden" name="jform[surface]" value="<?php echo $item->surface; ?>" />
                            <input type="hidden" name="jform[remark]" value="<?php echo $item->remark; ?>" />
                            <input type="hidden" name="jform[sent]" value="<?php echo $item->sent; ?>" />
                            <input type="hidden" name="jform[completed]" value="<?php echo $item->completed; ?>" />
                            <input type="hidden" name="option" value="com_easysdi_shop" />
                            <input type="hidden" name="task" value="order.remove" />
                        <?php echo JHtml::_('form.token'); ?>
                        </form>
                        <?php
                    endif;
                    ?>
                </li>
            <?php endif; ?>

        <?php endforeach; ?>
        <?php
        if (!$show):
            echo JText::_('COM_EASYSDI_SHOP_NO_ITEMS');
        endif;
        ?>
    </ul>
</div>
        <?php if ($show): ?>
    <div class="pagination">
        <p class="counter">
        <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
    <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif; ?>


<?php if (JFactory::getUser()->authorise('core.create', 'com_easysdi_shop')): ?><a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.edit&id=0'); ?>"><?php echo JText::_("COM_EASYSDI_SHOP_ADD_ITEM"); ?></a>
<?php endif; ?>