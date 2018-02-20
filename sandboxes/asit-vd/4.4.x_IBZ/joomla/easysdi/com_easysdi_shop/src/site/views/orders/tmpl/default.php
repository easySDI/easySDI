<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHTML::_('behavior.modal');
JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');

JFactory::getDocument()->addScript(Juri::root(true) . '/components/com_easysdi_shop/helpers/helper.js?v=' . sdiFactory::getSdiFullVersion());
require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';
?>
<div class="shop front-end-edit">
    <h1><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_ORDERS'); ?></h1>
    <div class="well sdi-searchcriteria">
        <div class="row-fluid">
            <form class="form-search" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=orders'); ?>" method="post">
                <div class="btn-toolbar">
                    <div class="btn-group pull-right">
                        <fieldset class="radio btn-group btn-group-yesno" id="filterarchive">
                            <input type="radio" id="archived0" name="filter_archived" value="1" <?php if ($this->state->get('filter.archived') == 1): ?> checked='checked'<?php endif; ?> onClick="this.form.submit();">
                            <label for="archived0" class="btn"><?php echo JText::_('COM_EASYSDI_SHOP_REQUESTS_ACTIVE'); ?></label>
                            <input type="radio" id="archived1" name="filter_archived" value="0" <?php if ($this->state->get('filter.archived') == 0): ?> checked='checked'<?php endif; ?> onClick="this.form.submit();">
                            <label for="archived1" class="btn"><?php echo JText::_('COM_EASYSDI_SHOP_REQUESTS_ARCHIVED'); ?></label>                            
                        </fieldset>
                        <?php if (count($this->organisms) > 1): ?>
                            <div id="filterorganism" >
                                <select id="filter_organism" name="filter_organism" onchange="this.form.submit();" class="inputbox">
                                    <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_ORDERS_ORGANISM_FILTER'); ?></option>
                                    <?php foreach ($this->organisms as $organism): ?>
                                        <option value="<?php echo $organism->id; ?>" <?php
                                        $filterName = (!empty($this->parent)) ? 'filter.userorganism.children' : 'filter.userorganism';
                                        if ($this->state->get('filter.organism') == $organism->id) : echo 'selected="selected"';
                                        endif;
                                        ?> ><?php echo $organism->name; ?></option>
                                            <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div id="filtertype">
                            <select id="filter_type" name="filter_type" onchange="this.form.submit();" class="inputbox">
                                <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_ORDERS_TYPE_FILTER'); ?></option>
                                <?php foreach ($this->ordertype as $type): ?>
                                    <option value="<?php echo $type->id; ?>" <?php
                                    if ($this->state->get('filter.type') == $type->id) : echo 'selected="selected"';
                                    endif;
                                    ?> >
                                        <?php echo JText::_($type->value); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="filterstatus">
                            <select id="filter_status" name="filter_status" onchange="this.form.submit();" class="inputbox">
                                <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_ORDERS_STATE_FILTER'); ?></option>
                                <?php foreach ($this->orderstate as $status): ?>
                                    <option value="<?php echo $status->id; ?>" <?php
                                    if ($this->state->get('filter.status') == $status->id) : echo 'selected="selected"';
                                    endif;
                                    ?> >
                                        <?php echo JText::_($status->value); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="filtersearch">
                            <label for="filter_search" class="element-invisible">Rechercher</label>
                            <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_EASYSDI_CORE_ORDERS_SEARCH_FILTER'); ?>" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SEARCH_FILTER'); ?>" />
                            <button class="btn hasTooltip" type="submit" title="Rechercher"><i class="icon-search"></i></button>
                            <button class="btn hasTooltip" type="button" title="Effacer" onclick="document.id('filter_search').value = '';
                                    this.form.submit();"><i class="icon-remove"></i></button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="items">
        <div class="well">                      
            <?php
            if (count($this->items)):
                $ordersListLayout = new JLayoutFile('com_easysdi_shop.orders', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_shop'));
                echo $ordersListLayout->render(array(
                    'items' => $this->items,
                    'displayTitle' => true
                ));
            else:
                echo JText::_('COM_EASYSDI_SHOP_ORDERS_NO_ORDERS');
            endif;
            ?>
        </div>
    </div>

    <div class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
</div>

<?php
echo Easysdi_shopHelper::getAddToBasketModal();

//If OTP option is enabled, load the OTP Modal
if ($this->params->get('otpactivated', 0) == 1) {
    echo Easysdi_shopHelper::downloadOTPProductModal();
}
?>