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
    <form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=request'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
        <div class="order-edit front-end-edit">
            <h1><?php echo JText::_('COM_EASYSDI_SHOP_ORDER_TITLE'); ?></h1>
            <div >
                <div class="row-fluid">
                    <div class="span10 offset1 well">
                        <div class="row-fluid ">
                            <h3><?php echo $this->item->basket->name; ?></h3>
                            <div class="row-fluid" >
                                <div class="span4 order-edit-label" >
                                    <?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_CREATED'); ?>
                                </div>
                                <div class="span6 order-edit-value" >
                                    <?php echo $this->item->created; ?>
                                </div>
                            </div>
                            <div class="row-fluid">
                                <div class="span4 order-edit-label" >
                                    <?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_ORDERSTATE_ID'); ?>
                                </div>
                                <div class="span6 order-edit-value" >
                                    <?php echo JText::_($this->item->orderstate); ?>
                                </div>
                            </div>
                            <div class="row-fluid">
                                <div class="span4 order-edit-label" >
                                    <?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_ORDERTYPE_ID'); ?>
                                </div>
                                <div class="span6 order-edit-value" >
                                    <?php echo JText::_($this->item->ordertype); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row-fluid ">
                            <h3><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_EXTRACTION_NAME'); ?></h3>
                            <table class="table table-striped">
                                <tfoot>
                                </tfoot>
                                <tbody>
                                    <?php foreach ($this->item->basket->extractions as $extraction) : ?>
                                        <?php if (in_array($extraction->id, $this->authorizeddiffusion)) : ?>
                                            <tr id="<?php echo $extraction->id; ?>">
                                                <td>
                                                    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&view=sheet&preview=editor&guid=' . $extraction->metadataguid); ?>"><?php echo $extraction->name; ?></a>
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
                                                                                if (!empty($value->value)) :
                                                                                    echo $value->value;
                                                                                else :
                                                                                    echo $value->name;
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

                                                    <div class="row-fluid diffusion-order-result">
                                                        <div class="span2">
                                                            <span class="badge badge-info"><i class="icon-white icon-upload"></i></span>                                                                
                                                        </div>
                                                        <div class="span10">
                                                            <div class="row-fluid">
                                                                <?php if ($extraction->productstate_id == 2) : ?>
                                                                    <div class="span8">
                                                                        <input type="file" name="jform[file][<?php echo $extraction->id; ?>][]" id="file_<?php echo $extraction->id; ?>" >                                                                             
                                                                    </div>
                                                                <?php else : ?>
                                                                    <div class="span2 order-edit-label" >
                                                                        <?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_FILE'); ?>
                                                                    </div>

                                                                    <div class="span6 order-edit-value" >
                                                                        <?php echo $extraction->file .' ('. $extraction->size . ' B)'; ?>
                                                                    </div>
                                                                <?php endif; ?>


                                                            </div>
                                                            <div class="row-fluid">
                                                                <div class="span2 order-edit-label" >
                                                                    <?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_FEE'); ?>
                                                                </div>
                                                                <div class="span6 order-edit-value" >
                                                                    <?php if ($extraction->productstate_id == 2) : ?>
                                                                        <input type="text" id="fee_<?php echo $extraction->id; ?>" name="jform[fee][<?php echo $extraction->id; ?>]" placeholder="">
                                                                    <?php else : ?>
                                                                        <?php echo $extraction->fee; ?>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>

                                                            <div class="row-fluid">
                                                                <div class="span2 order-edit-label" >
                                                                    <?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_REMARK'); ?>
                                                                </div>

                                                                <div class="span6 order-edit-value" >
                                                                    <?php if ($extraction->productstate_id == 2) : ?>
                                                                        <textarea id="remark_<?php echo $extraction->id; ?>" name="jform[remark][<?php echo $extraction->id; ?>]" rows="6" placeholder=""></textarea>
                                                                    <?php else : ?>
                                                                        <?php echo $extraction->remark; ?>
                                                                    <?php endif; ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>        
                                                <td>

                                                </td>
                                            </tr>
                                        <input type = "hidden" name = "jform[diffusion][]" value = "<?php echo $extraction->id; ?>" />
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="row-fluid" >
                            <h3><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PERIMETER'); ?></h3>
                            <hr>
                            <div class="row-fluid" >
                                <div class="map-recap span6" >
                                    <div id="minimap" class="minimap" style="height:250px"></div>                   
                                </div>
                                <div  class="value-recap span6" >
                                    <div id="perimeter-buffer" class="row-fluid hide" >
                                        <div><h3><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_BUFFER'); ?></h3>
                                            <input id="buffer" name="buffer" type="text" placeholder="" class="input-xlarge" value="<?php if (!empty($this->item->basket->buffer)) echo $this->item->basket->buffer; ?>">
                                        </div>                                
                                    </div>
                                    <div id="perimeter-recap" class="row-fluid" >
                                        <?php if (!empty($this->item->basket->extent)): ?>
                                            <div><h3><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_SURFACE'); ?></h3>
                                                <div><?php if (!empty($this->item->basket->extent->surface)) echo $this->item->basket->extent->surface; ?></div>
                                            </div>                                
                                            <div><h3><?php echo $this->item->basket->extent->name; ?></h3></div>
                                            <?php
                                            if (!is_array($this->item->basket->extent->features)):
                                                $features = explode(',', $this->item->basket->extent->features);
                                                foreach ($features as $feature):
                                                    ?>
                                                    <div><?php echo $feature; ?></div>
                                                    <?php
                                                endforeach;
                                            else :
                                                foreach ($this->item->basket->extent->features as $feature):
                                                    ?>
                                                    <div><?php echo $feature->name; ?></div>
                                                    <?php
                                                endforeach;
                                            endif;
                                            ?>

                                        <?php endif; ?>
                                    </div>                           
                                </div>
                            </div>

                        </div>

                        <?php if (!empty($this->item->basket->thirdparty)): ?>
                            <div class="row-fluid" >
                                <h3><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_THIRDPARTY_ID'); ?></h3>
                                <hr>
                                <input id="thirdparty" name="thirdparty" type="text" placeholder="" class="input-xlarge" value="<?php echo $this->item->basket->thirdparty; ?>">                               
                            </div>
                        <?php endif; ?>


                    </div>
                </div>
            </div>
        </div>
        <script>

        </script>
        <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
            <?php echo $field->input; ?>
        <?php endforeach; ?>  
        <input type = "hidden" name = "task" value = "" />
        <input type = "hidden" name = "option" value = "com_easysdi_shop" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
    <?php echo $this->getToolbar(); ?>

    <?php
else:
    echo JText::_('COM_EASYSDI_SHOP_ITEM_NOT_LOADED');
endif;
?>
