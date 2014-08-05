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
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

JText::script('FREEPERIMETER');
JText::script('MYPERIMETER');
JText::script('COM_EASYSDI_SHOP_BASKET_KILOMETER');
JText::script('COM_EASYSDI_SHOP_BASKET_METER');

$document = JFactory::getDocument();
$document->addScript('components/com_easysdi_shop/views/basket/tmpl/basket.js');
$document->addScript('components/com_easysdi_shop/views/basket/tmpl/freeperimeter.js');
$document->addScript('components/com_easysdi_shop/views/basket/tmpl/perimeter.js');
$document->addScript('components/com_easysdi_shop/views/basket/tmpl/myperimeter.js');
$document->addScript('components/com_easysdi_shop/helpers/helper.js');

?>

<style type="text/css">
    div.shop-product .table td{ width: 75%; word-break: break-all}
    div.shop-product .table td.price_column{ width: 10%; text-align: right}
    div.shop-product .table td.action_column{ width: 5%}
    
    div.shop-product .table tfoot td{ text-align: right}
</style>

<?php if ($this->item /*&& $this->item->extractions*/) : 
    $currency = $this->item->prices->currency;
?>
    <script>
        var request;
        var current_id;

        function removeFromBasket(id) {
            current_id = id;
            jQuery('#modal-dialog-remove').modal('show');
        }

        function actionRemove() {
            jQuery('#task').val('removeFromBasket');
            jQuery('#id').val(current_id);
            jQuery('#adminForm').submit();
        }
        
        var checkTouState = function(){
            jQuery('#toolbar-edit>button, #toolbar-publish>button').attr('disabled', !jQuery('#termsofuse').prop('checked'));
        };


        jQuery(document).ready(function() {
            jQuery('#btn-login').on('click', function(){
                document.location.href = 'index.php?option=com_users&view=login&return='+btoa(document.location.href);
                return false;
            });
            
            jQuery('#btn-create-account').on('click', function(){
                document.location.href = 'index.php?option=com_users&view=registration&return='+btoa(document.location.href);
                return false;
            });
            
            jQuery('#termsofuse').on('change', checkTouState);
            checkTouState();
            
            Joomla.submitbutton = function(task)
            {
                if (jQuery('#features').val() === '') {
                    jQuery('#modal-error').modal('show');
                } else {
                    if (jQuery('#allowedbuffer').val() == 0) {
                        jQuery('#perimeter-buffer').val('');
                    }

                    var format = new OpenLayers.Format.WMC({'layerOptions': {buffer: 0}});
                    var text = format.write(minimap);
                    jQuery('#wmc').val(text);
                    
                    taskArray = task.split('.');
                    jQuery('input[name=action]').val(taskArray[1]);
                    
                    Joomla.submitform('basket.save', document.getElementById('adminForm'));
                }

            }
        })

    </script>
    
    <form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=basket'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
        <div class="basket-edit front-end-edit">
            <h1><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_TITLE'); ?></h1>
            <div class="well">
                <!-- PERIMETER -->
                <div class="row-fluid shop-perimeter" >
                    <div class="row-fluid" ><h3><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PERIMETER'); ?></h3></div>
                    <hr>
                    <div class="row-fluid" >
                        <div class="map-recap span6" >
                            <div id="minimap" class="minimap" style="height:250px"></div>                   
                        </div>
                        <div  class="value-recap span6" >
                            <div id="perimeter-buffer" class="row-fluid hide" >
                                <div><h4><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_BUFFER'); ?></h4>
                                    <input id="buffer" name="buffer" type="text" placeholder="" class="input-xlarge" value="<?php if (!empty($this->item->buffer)) echo (float)$this->item->buffer; ?>">
                                </div>                                
                            </div>
                            <div id="perimeter-recap" class="row-fluid" >
                                <?php if (!empty($this->item->extent)): ?>
                                    <div><h4><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_SURFACE'); ?></h4>
                                        <div><?php
                                        if (!empty($this->item->extent->surface)) :
                                            if (floatval($this->item->extent->surface) > intval($this->paramsarray['maxmetervalue'])):
                                                echo round(floatval($this->item->extent->surface) / 1000000, intval($this->paramsarray['surfacedigit']));
                                                echo JText::_('COM_EASYSDI_SHOP_BASKET_KILOMETER');
                                            else:
                                                echo round(floatval($this->item->extent->surface), intval($this->paramsarray['surfacedigit']));
                                                echo JText::_('COM_EASYSDI_SHOP_BASKET_METER');
                                            endif;
                                        endif;
                                            ?></div>
                                    </div>                                
                                    <div><h4><?php echo JText::_($this->item->extent->name); ?></h4></div>
                                    <?php
                                    if (is_array($this->item->extent->features)):
                                        ?> <div id="perimeter-recap-details" style="overflow-y:scroll; height:100px;"> <?php                                    
                                        foreach ($this->item->extent->features as $feature):
                                            ?>
                                            <div><?php echo $feature->name; ?></div>
                                            <?php
                                        endforeach;
                                        ?></div><?php
                                    endif;
                                    ?>
                                    
                              <?php endif; ?>
                                  
                            </div>                   
                        </div>
                       
                    </div>
                    
                    <div class="row-fluid" >                        
                            <div class="span6" >
                                <?php
                                //If one of the products is restricted and the organism of the user doesn't have a perimeter, the selection can't be done
                                if ($this->item->isrestrictedbyperimeter && ($this->user->perimeter=='')){?>
                                    <div id="help-perimeter" class="help-block"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_DEFINE_PERIMETER_ERROR'); ?></div>
                                 <?php }else{ ?>
                                    <a href="#modal-perimeter" class="btn btn-success" style="margin-bottom: 10px;" data-toggle="modal" >
                                       <i class="icon-white icon-location"></i>
                                       <span id="defineOrderBtn"> <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_DEFINE_PERIMETER'); ?></span></a>
                                 <?php } ?>
                            </div>
                         
                        </div>
                </div>
                <!-- ENDOF PERIMETER -->
                
                <!-- THIRD PARTY -->
                <?php if (!empty($this->thirdParties)): ?>
                    <div class="row-fluid shop-third-party" >
                        <div class="row-fluid" ><h3><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_THIRD_PARTY'); ?></h3>
                        <hr>
                       <select id="thirdparty" name="thirdparty" class="inputbox input-xlarge">
                            <option value="-1"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_NO_THIRD_PARTY'); ?></option>
                            <?php foreach ($this->thirdParties as $thirdparty) : ?>
                                <option value="<?php echo $thirdparty->id; ?>" <?php if ($this->item->thirdparty == $thirdparty->id) echo 'selected' ?>><?php echo $thirdparty->name; ?></option>
                            <?php endforeach; ?>
                       </select></div>
                    </div>
                <?php endif; ?>
                <!-- ENDOF THIRD PARTY -->
                
                <!-- PRODUCTS -->
                <div class="row-fluid shop-product">
                    <div class="row-fluid" >
                        <div class="span6" >
                            <h3><?php //echo JText::_('COM_EASYSDI_SHOP_BASKET_EXTRACTION_NAME'); ?></h3>
                        </div>
                        <div class="span6" >
                            <?php if (!empty($this->item->visualization)): ?>
                                <div class="pull-right">
                                    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_map&view=preview') . '&id=' . $this->item->visualization; ?>" target="_blank"
                                       title="<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_TOOLTIP_PREVIEW'); ?>"
                                       class="btn btn-success btn-mini pull-right" >
                                        <i class="icon-eye"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row-fluid" >
                        <hr>
                        <h3>X <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_SELECTED_DATA'); ?> ( XXX.XX <?php echo $currency;?>)</h3>
                        
                        <?php foreach($this->item->extractions as $supplier_id => $supplier): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <td><h4><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_DATA_SUPPLIER') . ' : ' . $supplier->name; ?></h4></td>
                                    <td class="price_column"><?php echo JText::_('COM_EASYSDI_SHOP_PRICES_TTC'); ?></td>
                                    <td class="action_column">&nbsp;</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($supplier->items as $item): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&view=sheet&guid=' . $item->metadataguid); ?>"><?php echo $item->name; ?></a>
                                        <ul>
                                            <?php foreach($item->properties as $property): ?>
                                            <li><?php echo $property->name; ?> : 
                                                <?php 
                                                $c=count($property->values);
                                                $i=0;
                                                foreach($property->values as $value): 
                                                    echo empty($value->value) ? $value->name : $value->value;
                                                    $i++;
                                                    if($i<$c) echo ', ';
                                                endforeach;
                                                ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                    <td class="price_column"><?php echo isset($this->item->prices->suppliers[$supplier_id]->products[$item->id]->priceIT) ? number_format($this->item->prices->suppliers[$supplier_id]->products[$item->id]->priceIT, 2) : '-';?> <?php echo $currency;?></td>
                                    <td class="action_column">
                                        <a href="#" class="btn btn-danger btn-mini pull-right" title="<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_TOOLTIP_REMOVE'); ?>" 
                                           onClick="removeFromBasket(<?php echo $item->id; ?>);return false;"><i class="icon-white icon-remove"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_TAX'); ?></td>
                                    <td class="price_column"><?php echo isset($this->item->prices->suppliers[$supplier_id]->tax) ? number_format($this->item->prices->suppliers[$supplier_id]->tax, 2) : '-';?> <?php echo $currency;?></td>
                                    <td class="action_column">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_SUPPLIER_SUBTOTAL'); ?></td>
                                    <td class="price_column"><?php echo isset($this->item->prices->suppliers[$supplier_id]->priceIT) ? number_format($this->item->prices->suppliers[$supplier_id]->priceIT, 2) : '-';?> <?php echo $currency;?></td>
                                    <td class="action_column">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_REBATE'); ?></td>
                                    <td class="price_column"><?php echo isset($this->item->prices->suppliers[$supplier_id]->rebate['total']) ? number_format($this->item->prices->suppliers[$supplier_id]->rebate['total'], 2) : '-';?> <?php echo $currency;?></td>
                                    <td class="action_column">&nbsp;</td>
                                </tr>
                            </tfoot>
                        </table>
                        <?php endforeach; ?>
                        
                        <!-- TOTAL -->
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <td><h4><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PLATFORM'); ?></h4></td>
                                    <td class="price_column">&nbsp;</td>
                                    <td class="action_column">&nbsp;</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_FEE'); ?></td>
                                    <td class="price_column"><?php echo isset($this->item->prices->tax) ? number_format($this->item->prices->tax, 2) : '-';?> <?php echo $currency;?></td>
                                    <td class="action_column">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_TOTAL'); ?></td>
                                    <td class="price_column"><?php echo isset($this->item->prices->priceIT) ? number_format($this->item->prices->priceIT, 2) : '-';?> <?php echo $currency;?></td>
                                    <td class="action_column">&nbsp;</td>
                                </tr>
                            </tfoot>
                        </table>
                        <!-- ENDOF TOTAL -->
                        
                    </div>
                </div>
                <!-- ENDOF PRODUCTS -->
                
                <!-- INFORMATIONS -->
                <?php if (empty($this->paramsarray['shopinfomessage'])): ?>
                    <div class="row-fluid shop-info" >
                        <div class="row-fluid" ><h3><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_MESSAGE'); ?></h3>
                        <hr>
                        <div class="shop-information"><?php echo $this->paramsarray['shopinfomessage']; ?></div></div>
                    </div>
                <?php endif; ?>
                <!-- ENDOF INFORMATIONS -->
                
                <!-- TOOLBAR -->
                <div class="row-fluid " >
                    <hr>
                    <?php if($this->get('user')->isEasySDI):?>
                        <div>
                            <label class="checkbox">
                                    <input type="checkbox" id="termsofuse" > <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_I_ACCEPT') ?> <a href="<?php echo $this->paramsarray['termsofuse'] ; ?>" target="_blank"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_TERMS') ?></a> <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_OF_USE') ?>
                                </label>
                        </div>
                        <div  class="span5 pull-right" >
                            <?php echo $this->getToolbar(); ?>
                            <input type="hidden" name="action" value="" />
                        </div>
                        <div class="pull-right">
                            <input class="btn-toolbar" id="ordername" name="ordername" type="text" placeholder="<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_ORDER_NAME'); ?>" value="<?php if (!empty($this->item->name)) echo $this->item->name; ?>">
                        </div>
                    <?php else:?>
                        <div  class="span5 pull-right" >
                            <button class="btn btn-small" id="btn-login" name="btn-login"><?php echo JText::_('COM_EASYSDI_CORE_LOGIN');?></button>
                            <button class="btn btn-small" id="btn-create-account" name="btn-create-account"><?php echo JText::_('COM_EASYSDI_CORE_CREATE_ACCOUNT');?></button>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- ENDOF TOOLBAR -->
                
            </div>
        </div>

        <div id="modal-perimeter" style="margin-left:-45%;min-height:500px; width:90%" class="modal show fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_DEFINE_PERIMETER'); ?></h3>
            </div>
            <div class="modal-body" style="max-height: 500px;">
                <div class="container-fluid" >
                    <div class="row-fluid">
                        <div class="span8" >
                            <div class="alert alert-info" id="alert_template" style="display: none;">                      
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span8">
                            <div  >
                                <?php
                                echo $this->mapscript;
                                ?>
                            </div>
                        </div>
                        <div class="span4">
                            <br>
                            <br>
                            <div class="row-fluid">
                            </div>
                            <div class="row-fluid">
                                <div class="span3 offset1">
                                    <div class="btn-group" data-toggle="buttons-radio">
                                        <a href="#" id="btn-pan" class="btn btn-perimeter-selection <?php if (!isset($this->item->extent->id) || $this->item->extent->id===''){echo "active";};?>"  onClick="toggleSelectControl('pan');jQuery('#help-perimeter').html('<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PAN_HELP'); ?>'); return false;"><i class="icon-move"></i> <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PAN'); ?></a><br>
                                                    <br>
                                        <?php
                                        foreach ($item->perimeters as $perimeter):
                                            if ($perimeter->id == 1):
                                                if (!$this->item->isrestrictedbyperimeter):
                                                    ?>
                                                    <a href="#" id="btn-perimeter<?php echo $perimeter->id; ?>a" class="btn btn-perimeter-selection" 
                                                       onClick="selectRectangle();jQuery('#help-perimeter').html('<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_FREE_PERIMETER_RECTANGLE_HELP'); ?>');
                                                                jQuery('#allowedbuffer').val(<?php echo $perimeter->allowedbuffer; ?>);
                                                                jQuery('#perimeter-buffer').<?php if ($perimeter->allowedbuffer == 1): echo 'show';else: echo 'hide';endif;?>();return false;">
                                                       <i class=" icon-checkbox-unchecked"></i> <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_FREE_PERIMETER_RECTANGLE'); ?></a>
                                                    <br>
                                                    <br>
                                                    <a href="#" id="btn-perimeter<?php echo $perimeter->id; ?>b"  class="btn btn-perimeter-selection" 
                                                       onClick="selectPolygon();jQuery('#help-perimeter').html('<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_FREE_PERIMETER_POLYGON_HELP'); ?>');
                                                                jQuery('#allowedbuffer').val(<?php echo $perimeter->allowedbuffer; ?>);
                                                                jQuery('#perimeter-buffer').<?php if ($perimeter->allowedbuffer == 1): echo 'show'; else: echo 'hide'; endif;?>(); return false;">
                                                        <i class="icon-star-empty"></i> <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_FREE_PERIMETER_POLYGON'); ?></a>
                                                    <br>
                                                    <br>                                        
                                                    <?php
                                                endif;
                                            elseif ($perimeter->id == 2):
                                                if ($this->user->isEasySDI):
                                                    ?>
                                                    <a href="#" id="btn-perimeter<?php echo $perimeter->id; ?>" class="btn btn-perimeter-selection" 
                                                       onClick="selectPerimeter<?php echo $perimeter->id; ?>(); jQuery('#help-perimeter').html('<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_MY_PERIMETER_HELP'); ?>');
                                                                jQuery('#allowedbuffer').val(<?php echo $perimeter->allowedbuffer; ?>);
                                                                jQuery('#perimeter-buffer').<?php if ($perimeter->allowedbuffer == 1): echo 'show';else: echo 'hide';endif;?>();return false;">
                                                        <i class="icon-user"></i> <?php echo JText::_($perimeter->name); ?></a>

                                                    <script>
                                                        function selectPerimeter<?php echo $perimeter->id; ?>() {
                                                            selectMyPerimeter('<?php echo $perimeter->id; ?>', '<?php echo JText::_('MYPERIMETER'); ?>', '<?php echo addslashes(preg_replace('/\r\n/', '', $this->user->perimeter)); ?>');
                                                        }
                                                        function reloadFeatures<?php echo $perimeter->id; ?>() {
                                                            selectMyPerimeter('<?php echo $perimeter->id; ?>', '<?php echo JText::_('MYPERIMETER'); ?>', '<?php echo addslashes(preg_replace('/\r\n/', '', $this->user->perimeter)); ?>');
                                                        }
                                                    </script>
                                                    <br>
                                                    <br>
                                                    <?php
                                                endif;
                                            else: ?>
                                                <a href="#" id="btn-perimeter<?php echo $perimeter->id; ?>" class="btn btn-perimeter-selection" 
                                                   onClick="selectPerimeter<?php echo $perimeter->id; ?>();jQuery('#help-perimeter').html('<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PERIMETER_HELP'); ?>');
                                                            jQuery('#allowedbuffer').val(<?php echo $perimeter->allowedbuffer; ?>);
                                                            jQuery('#perimeter-buffer').<?php if ($perimeter->allowedbuffer == 1): echo 'show';else: echo 'hide';endif;?>();return false;">
                                                   <i class="icon-grid-view"></i> <?php echo JText::_($perimeter->name); ?></a>
                                                <script>
                                                    <?php if ($this->item->isrestrictedbyperimeter):?>
                                                        //var userperimeter = '<?php echo addslashes(preg_replace('/\s+/', '', $this->user->perimeter)); ?>';
                                                        var userperimeter = '<?php echo $this->user->perimeter; ?>';
                                                    <?php endif;?>
                                                        function selectPerimeter<?php echo $perimeter->id; ?>() {
                                                            return selectPerimeter(<?php if ($this->item->isrestrictedbyperimeter && $this->user->isEasySDI) : echo 1; else : echo 0; endif;?>, "<?php echo $perimeter->id; ?>", "<?php echo $perimeter->name; ?>", "<?php echo $perimeter->wmsurl; ?>", "<?php echo $perimeter->layername; ?>", "<?php echo $perimeter->wfsurl; ?>", "<?php echo $perimeter->featuretypename; ?>", "<?php echo $perimeter->namespace; ?>", "<?php echo $perimeter->featuretypefieldgeometry; ?>", "<?php echo $perimeter->featuretypefieldid; ?>", "<?php echo $perimeter->featuretypefieldname; ?>", "<?php echo $perimeter->prefix; ?>");
                                                        }
                                                        function reloadFeatures<?php echo $perimeter->id; ?>() {
                                                            reloadFeatures("<?php echo $perimeter->wfsurl; ?>", "<?php echo $perimeter->featuretypename; ?>", "<?php echo $perimeter->featuretypefieldid; ?>");
                                                        }
                                                </script>
                                                <br>
                                                <br>
                                            <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div id="help-perimeter" class="help-block"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PAN_HELP'); ?></div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" onclick="cancel();" aria-hidden="true"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_MODAL_BTN_CLOSE") ?></button>
                <button class="btn btn-primary" id="btn-saveperimeter" onclick="savePerimeter();" data-dismiss="modal"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_MODAL_BTN_SAVE") ?></button>
            </div>
        </div>

        <div id="modal-dialog-remove" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_DIALOG_HEADER") ?></h3>
            </div>
            <div class="modal-body">
                <p><div id="modal-dialog-remove-body-text"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_CONFIRM_REMOVE_ITEM") ?></div></p>
            </div>
            <div class="modal-footer">
                <button onClick="current_id = null;" class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_MODAL_BTN_CANCEL") ?></button>
                <button onClick="actionRemove();" class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_MODAL_BTN_REMOVE") ?></button>
            </div>
        </div>

        <div id="modal-error" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-header">
                    <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>-->
                    <h3 id="myModalLabel"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_ERROR_PERIMETER_TITLE") ?></h3>
                </div>               
                <div class="modal-body">
                    <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
                    <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_ERROR_PERIMETER_SELECTION_MISSING'); ?>
                </div>                
                <!--<div class="modal-footer">
                    <button type="button" class="btn btn-primary"><?php echo JText::_('OK'); ?></button>
                </div>              -->
            </div>
        </div>

        <script>
            Ext.onReady(function() {
                app.on("ready", function() {
                    initMiniMap();
                    initDraw();
//                    jQuery('#perimeter-buffer').hide();
                    <?php if (!empty($this->item->extent)): ?>
                        <?php if (!empty($this->item->extent->allowedbuffer) && $this->item->extent->allowedbuffer == 1): ?>
                                        jQuery('#perimeter-buffer').show();
                        <?php endif; ?>
                        <?php if ($this->item->extent->id == 1):
                            ?>
                                        jQuery('#btn-perimeter1a').addClass('active');
                            <?php
                        else :
                            ?>
                                        jQuery('#btn-perimeter<?php echo $this->item->extent->id; ?>').addClass('active');
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (!empty ($this->item->extent) && isset($this->item->extent->features) && is_string($this->item->extent->features)):        ?>
                        reprojectWKT("<?php echo $this->item->extent->features; ?>");
                    <?php endif;?>
                    <?php if (!empty($this->item->extent) && isset($this->item->extent->features)) : ?>
                        selectPerimeter<?php echo $this->item->extent->id; ?>();
                        reloadFeatures<?php echo $this->item->extent->id; ?>();
                    <?php endif; ?>
                });
            });
        </script>
        <input type="hidden" name="perimeter" id="perimeter" value="<?php
        if (isset($this->item->extent) && !empty($this->item->extent)): echo $this->item->extent->id;
        endif;
        ?>" />
        <input type="hidden" name="perimetern" id="perimetern" value="<?php
        if (isset($this->item->extent->name) && !empty($this->item->extent->name)): echo $this->item->extent->name;
        endif;
        ?>" />
        <input type="hidden" name="surface" id="surface" value="<?php
        if (isset($this->item->extent->surface) && !empty($this->item->extent->surface)): echo $this->item->extent->surface;
        endif;
        ?>" />
        <input type="hidden" name="allowedbuffer" id="allowedbuffer" value="" />
        <input type="hidden" name="features" id="features" value='<?php
        if (isset($this->item->extent->features)){
            if (!empty($this->item->extent) && !is_array($this->item->extent->features)): 
                echo $this->item->extent->features; 
            elseif (!empty($this->item->extent)): 
                echo htmlspecialchars(json_encode($this->item->extent->features), ENT_QUOTES, 'UTF-8') ;
            endif;
        }
        ?>' />
        <input type="hidden" name="t-perimeter" id="t-perimeter" value="<?php
        if (isset($this->item->extent->id) && !empty($this->item->extent)): echo $this->item->extent->id;
        endif;
        ?>" />
        <input type="hidden" name="t-perimetern" id="t-perimetern" value="<?php
        if (isset($this->item->extent->name) && !empty($this->item->extent)): echo $this->item->extent->name;
        endif;
        ?>" />
        <input type="hidden" name="t-features" id="t-features" value='<?php
        if (isset($this->item->extent->features))
        {
            if (!empty($this->item->extent) && !is_array($this->item->extent->features)): 
                echo $this->item->extent->features; 
            elseif (!empty($this->item->extent)): 
                echo htmlspecialchars(json_encode($this->item->extent->features), ENT_QUOTES, 'UTF-8') ;
            endif;
        }
        ?>' />
        <input type="hidden" name="t-surface" id="t-surface" value="<?php
        if (isset($this->item->extent->surface) && !empty($this->item->extent)): echo $this->item->extent->surface;
        endif;
        ?>" />
        <input type = "hidden" name= "surfacemin" id="surfacemin" value="<?php echo $this->item->surfacemin; ?>" />
        <input type = "hidden" name = "surfacemax" id="surfacemax" value="<?php echo $this->item->surfacemax; ?>" />            
        <input type = "hidden" name = "v-features" id="v-features" value="" />            
        <input type = "hidden" name = "task" id = "task" value = "" />
        <input type = "hidden" name = "option" value = "com_easysdi_shop" />
        <input type = "hidden" name = "id" id = "id" value = "" />
        <input type = "hidden" name = "surfacedigit" id = "surfacedigit" value = "<?php echo $this->paramsarray['surfacedigit']; ?>" />
        <input type = "hidden" name = "maxmetervalue" id = "maxmetervalue" value = "<?php echo $this->paramsarray['maxmetervalue']; ?>" />
        
        <?php echo JHtml::_('form.token'); ?>
    </form>

    <?php
else:
    echo JText::_('COM_EASYSDI_SHOP_BASKET_MESSAGE_EMPTY_BASKET');
endif;
?>
