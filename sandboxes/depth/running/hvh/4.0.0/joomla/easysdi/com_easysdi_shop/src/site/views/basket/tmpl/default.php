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
JText::script('COM_EASYSDI_SHOP_BASKET_CONFIRM_REMOVE_ITEM');
?>
<?php if ($this->item->extractions) : ?>
    <script>
        var request;
        var current_id;

        function removeFromBasket(id) {
            current_id = id;
            jQuery('#modal-dialog-remove-body-text').text(Joomla.JText._('COM_EASYSDI_SHOP_BASKET_CONFIRM_REMOVE_ITEM'));
            jQuery('#modal-dialog-remove').modal('show');
        }

        function actionRemove() {
            initRequest();
            var query = "index.php?option=com_easysdi_shop&task=removeFromBasket&id=" + current_id;
            request.onreadystatechange = reloadBasketContent;
            request.open("GET", query, true);
            request.send(null);
        }

        function reloadBasketContent(){
            if (request.readyState == 4) {
                updateBasketContent();
                jQuery('#'+current_id).remove();                
                current_id= null;
            }
        }



    </script>

    <div id="modal-dialog-remove" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_DIALOG_HEADER") ?></h3>
        </div>
        <div class="modal-body">
            <p><div id="modal-dialog-remove-body-text"></div></p>
        </div>
        <div class="modal-footer">
            <button onClick="current_id = null; " class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_MODAL_BTN_CANCEL") ?></button>
            <button onClick="actionRemove(); " class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_MODAL_BTN_REMOVE") ?></button>
        </div>
    </div>

    <form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=basket'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">

        <div class="basket-edit front-end-edit">
            <h1><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_TITLE'); ?></h1>

            <div class="well">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_EXTRACTION_NAME'); ?></th>
                            <th><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_EXTRACTION_DELETE'); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                    </tfoot>
                    <tbody>
                        <?php foreach ($this->item->extractions as $extraction) : ?>
                            <tr id="<?php echo $extraction->id; ?>">
                                <td>
                                    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resource.edit&id=' . (int) $extraction->resource); ?>"><?php echo $extraction->name; ?></a>
                                    <div class="small"><?php echo $extraction->organism; ?></div>
                                    <div class="accordion" id="accordion_<?php echo $extraction->id; ?>_properties">
                                        <div class="accordion-group">
                                            <div class="accordion-heading">
                                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_<?php echo $extraction->id; ?>_properties" href="#<?php echo $extraction->id; ?>_collapse">
                                                    <?php echo JText::_("COM_EASYSDI_SHOP_BASKET_EXTRACTION_PROPERTIES"); ?>
                                                </a>
                                            </div>
                                            <div id="<?php echo $extraction->id; ?>_collapse" class="accordion-body collapse">
                                                <div class="accordion-inner">
                                                    <?php
                                                    foreach ($extraction->properties as $property):
                                                        ?>
                                                        <div class="small"><?php echo $property->name; ?> : 
                                                            <?php
                                                            foreach ($property->values as $value) :
                                                                if (!empty($value->name)) :
                                                                    echo $value->name;
                                                                else :
                                                                    echo $value->value;
                                                                endif;
                                                                echo', ';
                                                            endforeach;
                                                            ?>
                                                        </div>
                                                        <?php
                                                    endforeach;
                                                    ?>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-mini pull-right " onClick="removeFromBasket(<?php echo $extraction->id; ?>); return false;"><i class="icon-white icon-remove"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php echo $this->getToolbar(); ?>
        </div>
    </form>

    <?php
else:
    echo JText::_('COM_EASYSDI_SHOP_BASKET_MESSAGE_EMPTY_BASKET');
endif;
?>
