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
JHTML::_('behavior.calendar');
JHtml::_('formbehavior.chosen', 'select');
?>
<div class="shop front-end-edit">
    <h1><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_REQUESTS'); ?></h1>
    <div class="well sdi-searchcriteria">
        <div class="row-fluid">
            <form class="form-search" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=requests'); ?>" method="post">
                <div class="btn-group pull-right">

                    <fieldset class="radio btn-group btn-group-yesno" id="filterstatus">
                        <input type="radio" id="state0" name="filter_status" value="0"<?php if ($this->state->get('filter.status') == 0): ?> checked='checked'<?php endif; ?> onClick="this.form.submit();">
                        <label for="state0" class="btn"><?php echo JText::_('COM_EASYSDI_SHOP_REQUESTS_STATUS_DONE'); ?></label>
                        <input type="radio" id="state1" name="filter_status" value="1"<?php if ($this->state->get('filter.status') == 1): ?> checked='checked'<?php endif; ?> onClick="this.form.submit();">
                        <label for="state1" class="btn"><?php echo JText::_('COM_EASYSDI_SHOP_REQUESTS_STATUS_TODO'); ?></label>
                    </fieldset>
                    <?php if (count($this->organisms) > 1): ?>
                        <div id="filterorganism" >
                            <select id="filter_userorganism" name="filter_userorganism" onchange="this.form.submit();" class="inputbox">
                                <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_REQUESTS_ORGANISM_FILTER'); ?></option>
                                <?php foreach ($this->organisms as $organism): ?>
                                    <option value="<?php echo $organism->id; ?>" <?php
                                    if ($this->state->get('filter.organism') == $organism->id) : echo 'selected="selected"';
                                    endif;
                                    ?> ><?php echo $organism->name; ?></option>
                                        <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>


                    <?php if ($this->state->get('filter.status') == '0'): //advanced filters for "historic" requests (=done)?>
                        <div id="filterclientorganism" >
                            <select id="filter_clientorganism" name="filter_clientorganism" onchange="this.form.submit();" class="inputbox">
                                <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_REQUESTS_CLIENTORGANISM_FILTER'); ?></option>
                                <?php foreach ($this->clientorganisms as $clientorganism): ?>
                                    <option value="<?php echo $clientorganism->id; ?>" <?php
                                    if ($this->state->get('filter.clientorganism') == $clientorganism->id) : echo 'selected="selected"';
                                    endif;
                                    ?> ><?php echo $clientorganism->name; ?></option>
                                        <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="filtersentfrom" >
                            <?php
                            $datefromval = $this->state->get('filter.sentfrom');
                            if (!($datefromval & strlen($datefromval) > 1))
                                $datefromval = '';
                            echo JHTML::_('calendar', $datefromval, 'filter_sentfrom', 'filter_sentfrom', '%Y-%m-%d', array('placeholder' => JText::_('COM_EASYSDI_CORE_REQUESTS_DATEFROM_FILTER'), 'onchange' => 'this.form.submit();'));
                            ?>
                        </div>
                        <div id="filtersentto" >
                            <?php
                            $datetoval = $this->state->get('filter.sentto');
                            if (!($datetoval & strlen($datetoval) > 1))
                                $datetoval = '';
                            echo JHTML::_('calendar', $datetoval, 'filter_sentto', 'filter_sentto', '%Y-%m-%d', array('placeholder' => JText::_('COM_EASYSDI_CORE_REQUESTS_DATETO_FILTER'), 'onchange' => 'this.form.submit();'));
                            ?>
                        </div>
                    <?php endif; ?>

                    <div id="filterstatus">                        
                        <select id="filter_status" name="filter_type" onchange="this.form.submit();" class="inputbox">
                            <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_REQUESTS_TYPE_FILTER'); ?></option>
                            <?php foreach ($this->ordertype as $ordertype): ?>
                                <option value="<?php echo $ordertype->id; ?>" <?php
                                if ($this->state->get('filter.type') == $ordertype->id) : echo 'selected="selected"';
                                endif;
                                ?> >
                                    <?php echo JText::_($ordertype->value); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="filtersearch">
                        <label for="filter_search" class="element-invisible">Rechercher</label>
                        <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_EASYSDI_CORE_REQUESTS_SEARCH_FILTER'); ?>" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SEARCH_FILTER'); ?>" />
                        <button class="btn hasTooltip" type="submit" title="Rechercher"><i class="icon-search"></i></button>
                        <button class="btn hasTooltip" type="button" title="Effacer" onclick="document.id('filter_search').value = '';
                                this.form.submit();"><i class="icon-remove"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="items">
        <div class="well">                      
            <?php
            if (count($this->items)):
                $requestsListLayout = new JLayoutFile('com_easysdi_shop.requests', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_shop'));
                echo $requestsListLayout->render(array(
                    'items' => $this->items,
                    'displayTitle' => true,
                    'filterStatus' => $this->state->get('filter.status')
                ));
            else:
                echo JText::_('COM_EASYSDI_SHOP_REQUESTS_NO_REQUESTS');
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
