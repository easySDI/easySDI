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

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_shop', JPATH_ADMINISTRATOR);
$jinput = JFactory::getApplication()->input;
$action = $jinput->get('action', '', 'STRING');
switch ($action) {
    case 'draft':
        $title = JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_DRAFT_TITLE');
        $welltitle = JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_DRAFT_MSG');
        $btnlabel = JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_DRAFT_LABEL');
        break;
    case 'estimate':
        $title = JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_ESTIMATE_TITLE');
        $welltitle = JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_ESTIMATE_TITLE');
        $btnlabel = JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_ESTIMATE_TITLE');
        break;
    case 'order':
        $title = JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_ORDER_TITLE');
        $welltitle = JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_ORDER_TITLE');
        $btnlabel = JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_ORDER_TITLE');
        break;
}
?>
<?php if ($this->item) : ?>
<script>
    js = jQuery.noConflict();
    js(document).ready(function() {        
        js('#termsofuse').change(enableSave);
    });
    function enableSave(){
        if ( js('#termsofuse').is(':checked') == true )
            js('#saveSubmit').removeAttr('disabled', 'disabled');            
        else
            js('#saveSubmit').attr('disabled', 'disabled');        
    }
</script>
    <form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=basket.save'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
        <div class="basket-confirm front-end-edit">
            <h1><?php echo $title; ?></h1>
            <div class="container-fluid">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="span6 offset3 well">
                                <?php if ($this->user->juser->guest) : ?>
                                <h3><?php echo $welltitle; ?></h3>
                                <input type="text" id="username" class="span12" name="username" placeholder="Username">
                                <br/>
                                <br/>
                                <input type="password" id="password" class="span12" name="password" placeholder="Password">
                                <?php endif; ?>
                                <br/>
                                <br/>
                                <label class="checkbox">
                                    <input type="checkbox" id="termsofuse" > <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_I_ACCEPT') ?> <a href="<?php echo $this->paramsarray['termsofuse'] ; ?>" target="_blank"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_TERMS') ?></a> <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_OF_USE') ?>
                                </label>
                                <br/>
                                <br/>
                                <button type="submit" id="saveSubmit" name="saveSubmit" disabled="disabled" class="btn btn btn-primary btn-block btn-large"><b><?php echo $btnlabel; ?></b></button>
                            </div>
                        </div>
                    </div><!--/span-->
                </div><!--/row-->
            </div>
        </div>
        <input type = "hidden" name = "task" value = "basket.save" />
        <input type = "hidden" name = "option" value = "com_easysdi_shop" />
        <input type = "hidden" name = "action" value = "<?php echo JFactory::getApplication()->input->get('action', '', 'string') ?>" />
            <?php echo JHtml::_('form.token'); ?>
    </form>

    <?php
else:
    echo JText::_('COM_EASYSDI_SHOP_ITEM_NOT_LOADED');
endif;
?>
