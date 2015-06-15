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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');


$document = JFactory::getDocument();
$document->addScript('components/com_easysdi_shop/helpers/helper.js');
$base_url = Juri::base(true) . '/administrator/components/com_easysdi_core/libraries';
//TODO : do not include proj here !!
$document->addScript($base_url . '/proj4js-1.1.0/lib/proj4js-compressed.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/defs/EPSG2056.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/defs/EPSG21781.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/projCode/somerc.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/projCode/merc.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/projCode/lcc.js');


?>
<?php if ($this->item) : ?> 
    <div class="order-edit front-end-edit">
        <h1><?php echo JText::_('COM_EASYSDI_SHOP_ORDER_TITLE'); ?></h1>
        <form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=order'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
            <div class="order-edit front-end-edit">
                <div >
                    <div class="row-fluid">
                        <div class="span10 offset1 well">
                            <div class="row-fluid ">
                                <h3><?php echo $this->item->basket->name; ?></h3>
                                <div class="row-fluid" >
                                    <div class="span4 order-edit-label" >
                                        <?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_CREATED'); ?>
                                    </div>
                                    <div class="span8 order-edit-value" >
                                        <?php echo $this->item->created; ?>
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span4 order-edit-label" >
                                        <?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_ORDERSTATE_ID'); ?>
                                    </div>
                                    <div class="span8 order-edit-value" >
                                        <?php echo JText::_($this->item->orderstate); ?>
                                    </div>
                                </div>
                                
                                <?php if(!is_null($this->item->validated)): ?>
                                <div class="row-fluid">
                                    <div class="span4 order-edit-label" >
                                        <?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_VALIDATED_REASON'); ?>
                                    </div>
                                    <div class="span8 order-edit-value" >
                                        <?php echo nl2br($this->item->validated_reason); ?>
                                    </div>
                                </div>
                                <?php endif;?>
                                
                                <div class="row-fluid">
                                    <div class="span4 order-edit-label" >
                                        <?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_ORDERTYPE_ID'); ?>
                                    </div>
                                    <div class="span8 order-edit-value" >
                                        <?php echo JText::_($this->item->ordertype); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (!empty($this->item->basket->thirdparty)): ?>
                                <div class="row-fluid" >
                                    <h3><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_THIRDPARTY_ID'); ?></h3>                                   
                                    <span ><?php echo $this->item->basket->thirdorganism; ?></span>                                    
                                </div>
                            <?php endif; ?>
                            
                            <div class="row-fluid ">
                                <h3><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_EXTRACTION_NAME'); ?></h3>
                                <table class="table table-striped">
                                    <tfoot>
                                    </tfoot>
                                    <tbody>
                                        <?php foreach ($this->item->basket->extractions as $extraction) : ?>
                                            <tr id="<?php echo $extraction->id; ?>">
                                                <td>
                                                    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&view=sheet&preview=public&guid=' . $extraction->metadataguid); ?>"><?php echo $extraction->name; ?></a>
                                                    <div class="small"><?php echo $extraction->organism; ?></div>
                                                    <div class="accordion" id="accordion_<?php echo $extraction->id; ?>_properties">
                                                        <div class="accordion-group">
                                                            <div class="accordion-heading">
                                                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_<?php echo $extraction->id; ?>_properties" href="#<?php echo $extraction->id; ?>_collapse">
                                                                    <?php echo JText::_("COM_EASYSDI_SHOP_BASKET_EXTRACTION_PROPERTIES"); ?>
                                                                </a>
                                                            </div>
                                                            <div id="<?php echo $extraction->id; ?>_collapse" class="accordion-body in">
                                                                <div class="accordion-inner">
                                                                    <?php
                                                                    foreach ($extraction->properties as $property):
                                                                        ?>
                                                                        <div class="small">
                                                                            <div class="order-property-label" >
                                                                                <?php echo $property->name; ?> :
                                                                            </div>
                                    
                                                                            <?php
                                                                            foreach ($property->values as $value) :
                                                                                ?>
                                                                                <div class="order-property-value" >
                                                                                <?php
                                                                                if (!empty($value->value)) :
                                                                                    echo $value->value;
                                                                                else :
                                                                                    echo $value->name;
                                                                                endif;
                                                                                if (next($property->values)==true) echo', ';
                                                                                ?>
                                                                                </div>
                                                                                <?php
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
                                                    <?php if ($extraction->productstate_id == 1) : ?>
                                                        <div class="diffusion-order-result">
                                                            <div class="span5">
                                                                <?php if (!empty($extraction->file)) : ?>
                                                                    <a target="RAW" href="index.php?option=com_easysdi_shop&task=order.download&id=<?php echo $extraction->id; ?>&order=<?php echo $this->item->id; ?>" class="btn btn-success" onClick="">
                                                                        <i class="icon-white icon-flag-2"></i> 
                                                                        <?php echo $extraction->name; ?>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="span5">
                                                                <div class="container-fluid">
                                                                    <div class="row-fluid">
                                                                        <div class="span2 order-edit-label" >
                                                                            <?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_FEE'); ?>
                                                                        </div>

                                                                        <div class="span4 order-edit-value" >
                                                                            <?php echo $extraction->fee; ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row-fluid">
                                                                        <div class="span2 order-edit-label" >
                                                                            <?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_COMPLETED'); ?>
                                                                        </div>

                                                                        <div class="span4 order-edit-value" >
                                                                            <?php echo $extraction->completed; ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row-fluid">
                                                                        <div class="span2 order-edit-label" >
                                                                            <?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_CREATED_BY'); ?>
                                                                        </div>

                                                                        <div class="span4 order-edit-value" >
                                                                            <?php echo $extraction->created_by; ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row-fluid">
                                                                        <div class="span2 order-edit-label" >
                                                                            <?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_REMARK'); ?>
                                                                        </div>

                                                                        <div class="span4 order-edit-value" >
                                                                            <?php echo $extraction->remark; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>        
                                                <td>

                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php Easysdi_shopHelper::getHTMLOrderPerimeter($this->item); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <?php if($this->state->get('layout.validation') && $this->item->orderstate_id == 8): ?>
                <p id="validation_remark">
                    <label for="reason"><?php echo JText::_('COM_EASYSDI_SHOP_ORDER_VALIDATION_REMARK'); ?>:</label>
                    <textarea id="reason" name="reason" <?php if(!$this->isValidationManager):?>disabled="disabled"<?php endif;?>></textarea>
                </p>
                <?php endif;
                echo $this->getToolbar(); ?>
            </div>
            <?php if($this->item->basket->extent->id == 1 || $this->item->basket->extent->id == 2  ):?>
                <?php echo $this->form->getInput('perimeter', null, $this->item->basket->extent->features); ?>
            <?php else : 
                foreach($this->item->basket->perimeters as $perimeter):
                     if($perimeter->id == $this->item->basket->extent->id):
                         echo $this->form->getInput('wfsfeaturetypefieldid', null, $perimeter->featuretypefieldid);
                        echo $this->form->getInput('wfsfeaturetypename', null, $perimeter->featuretypename);
                        echo $this->form->getInput('wfsurl', null, $perimeter->wfsurl);
                        echo $this->form->getInput('wfsnamespace', null, $perimeter->namespace);
                        echo $this->form->getInput('wfsprefix', null, $perimeter->prefix);
                        echo $this->form->getInput('wfsfeaturetypefieldgeometry', null, $perimeter->featuretypefieldgeometry);
                        break;
                     endif;
                endforeach;
                ?>
                <?php echo $this->form->getInput('wfsperimeter', null, json_encode($this->item->basket->extent->features)); ?>
            <?php endif; ?>
            <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
                <?php echo $field->input; ?>
            <?php endforeach; ?>  
            <input type = "hidden" name = "task" value = "" />
            <input type = "hidden" name = "id" value = "<?php echo $this->item->id; ?>" />
            <input type = "hidden" name = "option" value = "com_easysdi_shop" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
    <script>
        Ext.onReady(function() {
            window.appname.on("ready", function() {
                loadPerimeter(false);
            })
        })
    </script>
    <script type="text/javascript">
        jQuery(document).ready(function(){
           if(jQuery('textarea#reason').length){
               jQuery('div#toolbar-delete>button').prop('disabled', true);
               jQuery('textarea#reason').on('input propertychange', function(){
                   jQuery('div#toolbar-delete>button').prop('disabled', !(this.value.length>20));
               });
           }
        });
    </script>
    <?php
else:
    echo JText::_('COM_EASYSDI_SHOP_ITEM_NOT_LOADED');
endif;
?>
