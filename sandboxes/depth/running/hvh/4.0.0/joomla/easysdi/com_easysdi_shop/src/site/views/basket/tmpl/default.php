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

?>
<?php if ($this->item) : ?>

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
                        <tr>
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
                                <button class="btn btn-danger btn-mini pull-right "><i class="icon-white icon-remove"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->getToolbar(); ?>
    </div>


    <?php
else:
    echo JText::_('COM_EASYSDI_SHOP_ITEM_NOT_LOADED');
endif;
?>
