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
$document = JFactory::getDocument();
$document->addScript(Juri::root(true) . '/components/com_easysdi_shop/views/download/tmpl/grid.js?v=' . sdiFactory::getSdiFullVersion());
$document->addScript(Juri::root(true) . '/components/com_easysdi_shop/helpers/helper.js?v=' . sdiFactory::getSdiFullVersion());

Easysdi_shopHelper::addMapShopConfigToDoc();
?>
<?php if ($this->item) : ?>
    <form class="form-inline form-validate" action="<?php echo Juri::root(true) . '/index.php?option=com_easysdi_shop&task=download.download'; ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
        <div class="download-confirm front-end-edit">
            <h1><?php echo JText::_('COM_EASYSDI_SHOP_DOWNLOAD_GRID_TITLE'); ?></h1>
            <div class="container-fluid">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="sdi-map-grid-selection" >
                            <?php
                            echo $this->mapscript;
                            ?>
                        </div>
                    </div>
                    <div class="span12">
                        <div class="sdi-map-feature-selection"> 
                            <div class="sdi-map-feature-selection-name"> 
                                <label><?php echo JText::_('COM_EASYSDI_SHOP_DOWNLOAD_GRID_SELECTION_NAME'); ?> : </label>
                                <span></span>
                            </div>
                            <div class="sdi-map-feature-selection-description"> 
                                <label><?php echo JText::_('COM_EASYSDI_SHOP_DOWNLOAD_GRID_SELECTION_DESCRIPTION'); ?> : </label>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="span6 offset3 well">
                                <br/>
                                <label class="checkbox">
                                    <input type="checkbox" id="termsofuse" > <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_I_ACCEPT') ?> <a href="<?php echo $this->paramsarray['termsofuse']; ?>" target="_blank"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_TERMS') ?></a> <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_OF_USE') ?>
                                </label>
                                <br/>
                                <br/>
                                <a href="#" id="saveSubmit" onclick="return false;" name="saveSubmit" disabled="disabled" class="btn btn btn-primary btn-block btn-large" role="button"><b><?php echo JText::_('COM_EASYSDI_SHOP_DOWNLOAD_CONFIRM_LABEL'); ?></b></a>                            
                            </div>
                        </div>
                    </div><!--/span-->
                </div><!--/row-->
            </div>
        </div>
        <script>
            var perimeter;
            perimeter = <?php echo json_encode($this->item->perimeter->_item); ?>;

            js = jQuery.noConflict();
            js(document).ready(function () {
                js('#termsofuse').change(enableSave);
                js('#featurecode').change(enableSave);
            });
            function tokenize() {
                js('#saveSubmit').attr('href', js('#saveSubmit').attr('href') + '&' + js('#id').next().attr('name') + '=' + js('#id').next().attr('value'));
            }
        </script>
        <input type = "hidden" name = "task" value = "download.download" />
        <input type = "hidden" name = "option" value = "com_easysdi_shop" />
        <input type = "hidden" name = "featurecode" id="featurecode" value = "" />        
        <input type = "hidden" name = "id" id = "id" value = "<?php echo $this->item->id; ?>" />      
        <?php echo JHtml::_('form.token'); ?>
    </form>

    <?php
else:
    echo JText::_('COM_EASYSDI_SHOP_DOWNLOAD_UNAVAILABLE');
endif;
?>
