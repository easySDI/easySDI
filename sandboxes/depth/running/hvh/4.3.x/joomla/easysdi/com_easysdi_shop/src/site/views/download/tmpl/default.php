<?php
/**
 * @version     4.3.2
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;
?>

<script>
    js = jQuery.noConflict();
    js(document).ready(function() {        
        js('#termsofuse').change(enableDownload);
    });
    function enableDownload(){
        if ( js('#termsofuse').is(':checked') == true )
            js('#saveSubmit').removeAttr('disabled', 'disabled');            
        else
            js('#saveSubmit').attr('disabled', 'disabled');        
    }
</script>
<form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=download.download'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
    <div class="download-confirm front-end-edit">
        <h1><?php echo JText::_('COM_EASYSDI_SHOP_DOWNLOAD_CONFIRM_TITLE');; ?></h1>
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="span6 offset3 well">
                            <br/>
                            <br/>
                            <label class="checkbox">
                                <input type="checkbox" id="termsofuse" > <?php echo JText::_('COM_EASYSDI_SHOP_DOWNLOAD_CONFIRM_I_ACCEPT') ?> <a href="<?php echo $this->paramsarray['termsofuse'] ; ?>" target="_blank"><?php echo JText::_('COM_EASYSDI_SHOP_DOWNLOAD_CONFIRM_TERMS') ?></a> <?php echo JText::_('COM_EASYSDI_SHOP_DOWNLOAD_CONFIRM_OF_USE') ?>
                            </label>
                            <br/>
                            <br/>
                            <button type="submit" id="saveSubmit" name="saveSubmit" disabled="disabled" class="btn btn btn-primary btn-block btn-large"><b><?php echo JText::_('COM_EASYSDI_SHOP_DOWNLOAD_CONFIRM_LABEL');; ?></b></button>
                        </div>
                        
                    </div>
                </div><!--/span-->
                <div><?php echo JURI::base().'index.php?option=com_easysdi_shop&task=download.download&id='.$this->item->id; ?></div>
            </div><!--/row-->
        </div>
    </div>
    <input type = "hidden" name = "task" value = "download.download" />
    <input type = "hidden" name = "option" value = "com_easysdi_shop" />
    <input type = "hidden" name = "id" value = "<?php echo $this->item->id; ?>" />
        <?php echo JHtml::_('form.token'); ?>
</form>
