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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

JText::script('FREEPERIMETER');
JText::script('MYPERIMETER');
JText::script('COM_EASYSDI_SHOP_BASKET_KILOMETER');
JText::script('COM_EASYSDI_SHOP_BASKET_METER');
JText::script('COM_EASYSDI_SHOP_BASKET_PROCESS_ENDING');
JText::script('COM_EASYSDI_SHOP_BASKET_PROCESS_PROGRESSING');
JText::script('COM_EASYSDI_SHOP_BASKET_PRODUCT_FREE');
JText::script('COM_EASYSDI_SHOP_BASKET_TOOLTIP_REBATE_INFO');
JText::script('COM_EASYSDI_SHOP_BASKET_PERIMETER_YOUR_SELECTION');
JText::script('COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_ERROR_NOT_EN_POINTS');
JText::script('COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_ERROR_LIST_FORMAT');
JText::script('COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_ERROR_EMPTY_POINT_LIST');
JText::script('COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_ERROR_UNABLE_TO_CLOSE');
JText::script('COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_ERROR_UNABLE_TO_CREATE_F');
JText::script('COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_ERROR_OUTSIDE_MAP');
JText::script('COM_EASYSDI_SHOP_BASKET_ERROR_AREA');
JText::script('COM_EASYSDI_SHOP_BASKET_ERROR_AREA_TOO_LARGE');
JText::script('COM_EASYSDI_SHOP_BASKET_ERROR_AREA_TOO_SMALL');
JText::script('COM_EASYSDI_SHOP_BASKET_ERROR_SELFINTERSECT');
JText::script('COM_EASYSDI_SHOP_BASKET_PERIMETER_NO_PERIMETER_SELECTED');
JText::script('COM_EASYSDI_SHOP_BASKET_ERROR_PERIMETER_SELECTION_MISSING');
JText::script('COM_EASYSDI_SHOP_BASKET_ERROR_PERIMETER_TITLE');
JText::script('COM_EASYSDI_SHOP_BASKET_ERROR_THIRDPARTY_FIELDS_MISSING');
JText::script('COM_EASYSDI_SHOP_BASKET_ERROR_THIRDPARTY_FIELDS_MISSING_TITLE');
JText::script('COM_EASYSDI_SHOP_BASKET_LAYER_OUT_OF_RANGE');
JText::script('COM_EASYSDI_SHOP_BASKET_LAYER_OUT_OF_RANGE_TITLE');
JText::script('COM_EASYSDI_SHOP_BASKET_DEFINE_PERIMETER');
JText::script('COM_EASYSDI_SHOP_BASKET_MODIFY_PERIMETER');
JText::script('COM_EASYSDI_SHOP_BASKET_ERROR_TOO_SMALL');
JText::script('COM_EASYSDI_SHOP_BASKET_ERROR_TOO_SMALL_TITLE');
JText::script('COM_EASYSDI_SHOP_BASKET_ERROR_TOO_LARGE');
JText::script('COM_EASYSDI_SHOP_BASKET_ERROR_TOO_LARGE_TITLE');

$document = JFactory::getDocument();
$document->addScript('components/com_easysdi_shop/views/basket/tmpl/basket.js?v=' . sdiFactory::getSdiFullVersion());
$document->addScript('components/com_easysdi_shop/views/basket/tmpl/freeperimeter.js?v=' . sdiFactory::getSdiFullVersion());
$document->addScript('components/com_easysdi_shop/views/basket/tmpl/perimeter.js?v=' . sdiFactory::getSdiFullVersion());
$document->addScript('components/com_easysdi_shop/views/basket/tmpl/myperimeter.js?v=' . sdiFactory::getSdiFullVersion());
$document->addStyleSheet(Juri::root(true) . '/components/com_easysdi_shop/views/basket/tmpl/basket.css?v=' . sdiFactory::getSdiFullVersion());
Easysdi_shopHelper::addMapShopConfigToDoc();

$perimeterScript = "";

if ($this->item && $this->item->extractions) :
    $currency = $this->item->prices->cfg_currency;
    ?>

    <style type="text/css">

    </style>
    <script>
        var request, current_id,
                maxmetervalue = <?php echo intval($this->paramsarray['maxmetervalue']); ?>,
                surfacedigit = <?php echo intval($this->paramsarray['surfacedigit']); ?>,
                formToken = '<?php echo JSession::getFormToken() ?>=1',
                digit_after_decimal = <?php echo JComponentHelper::getParams('com_easysdi_shop')->get('digit_after_decimal', 2); ?>,
                decimal_symbol = '<?php echo JComponentHelper::getParams('com_easysdi_shop')->get('decimal_symbol', '.'); ?>',
                digit_grouping_symbol = "<?php echo JComponentHelper::getParams('com_easysdi_shop')->get('digit_grouping_symbol', "'"); ?>",
                currency = "<?php echo JComponentHelper::getParams('com_easysdi_shop')->get('currency', 'CHF'); ?>";
        freePerimeterTool = '<?php
    if (isset($this->item->freeperimetertool)):
        echo $this->item->freeperimetertool;
    else:
        echo('polygon');
    endif;
    ?>';
    </script>

    <form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=basket.save'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
        <div class="basket-edit front-end-edit">
            <h1><?php echo JText::plural('COM_EASYSDI_SHOP_BASKET_TITLE', $this->item->extractionsNb); ?></h1>
            <div class="well">
                <!-- PERIMETER -->
                <div class="row-fluid shop-perimeter" >
                    <div class="row-fluid shop-perimeter-title-row" ><h2><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PERIMETER'); ?><span id="shop-perimeter-title-surface"><?php
                                if (!empty($this->item->extent->surface)):
                                    echo " (";
                                    if (floatval($this->item->extent->surface) > intval($this->paramsarray['maxmetervalue'])):
                                        echo round(floatval($this->item->extent->surface) / 1000000, intval($this->paramsarray['surfacedigit']));
                                        echo JText::_('COM_EASYSDI_SHOP_BASKET_KILOMETER');
                                    else:
                                        echo round(floatval($this->item->extent->surface), intval($this->paramsarray['surfacedigit']));
                                        echo JText::_('COM_EASYSDI_SHOP_BASKET_METER');
                                    endif;
                                    echo ")";
                                endif;
                                ?></span></h2></div>
                    <hr>
                    <div class="row-fluid shop-perimeter-map-row" >
                        <div class="map-recap span6" >
                            <a href="#modal-perimeter" style="margin-bottom: 10px;" data-toggle="modal" >
                                <div id="minimap" class="minimap" style="height:250px"></div>      
                            </a>
                        </div>
                        <div  class="value-recap span6" >
                            <div id="perimeter-recap" class="row-fluid" style="<?php if (empty($this->item->extent)): ?>display: none;<?php endif; ?>" >
                                <div id="perimeter-level" style="<?php if (empty($this->item->extent->level)): ?>display: none;<?php endif; ?>">
                                    <h4><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_LEVEL'); ?></h4>
                                    <div id="perimeter-level-value"><?php if (!empty($this->item->extent->level)): ?><?php echo json_decode($this->item->extent->level)->label; ?><?php endif; ?></div>
                                </div>
                                <?php
                                if (is_array($this->item->extent->features)):
                                    $extentFeaturesObject = $this->item->extent->features;
                                else:
                                    $extentFeaturesObject = json_decode($this->item->extent->features);
                                endif;
                                ?>
                                <div id="perimeter-recap-details-title"><h4><?php echo JText::_($this->item->extent->name); ?></h4></div>
                                <div id="perimeter-recap-details" style="overflow-y:auto; height:100px;<?php if (!is_array($extentFeaturesObject)): ?>display:none;<?php endif; ?>">
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row-fluid shop-perimeter-btn-row" >                        
                        <div class="span6" >
                            <?php
                            //If one of the products is restricted and the organism of the user doesn't have a perimeter, the selection can't be done
                            if ($this->item->isrestrictedbyperimeter && $this->params->get('userperimeteractivated') == 1 && ($this->user->perimeter == '')) {
                                ?>
                                <div id="help-perimeter" class="help-block"><?php echo nl2br(JText::_('COM_EASYSDI_SHOP_BASKET_DEFINE_PERIMETER_ERROR')); ?></div>
                            <?php } else { ?>
                                <a id="defineOrderBtn" href="#modal-perimeter" class="btn btn-success" style="margin-bottom: 10px;" data-toggle="modal" >
                                    <i class="icon-white icon-location"></i> <span id="defineOrderBtnLbl"><?php echo empty($this->item->extent->features) ? JText::_('COM_EASYSDI_SHOP_BASKET_DEFINE_PERIMETER') : JText::_('COM_EASYSDI_SHOP_BASKET_MODIFY_PERIMETER'); ?></span>
                                </a>
                            <?php } ?>
                        </div>

                    </div>
                </div>
                <!-- ENDOF PERIMETER -->

                <!-- THIRD PARTY -->
                <?php if (!empty($this->thirdParties)): ?>
                    <div class="row-fluid shop-third-party" >
                        <div class="row-fluid" >
                            <h2><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_THIRD_PARTY'); ?></h2>
                            <hr>
                            <?php if ((bool) $this->paramsarray['tp_explanation_display']): ?>
                                <p><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_THIRD_PARTY_EXPLANATION'); ?></p>
                            <?php endif; ?>
                            <select id="thirdparty" name="thirdparty" class="inputbox input-xlarge">
                                <option value="-1"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_NO_THIRD_PARTY'); ?></option>
                                <?php foreach ($this->thirdParties as $thirdparty) : ?>
                                    <option value="<?php echo $thirdparty->id; ?>" <?php if ($this->item->thirdparty == $thirdparty->id) echo 'selected' ?>><?php echo $thirdparty->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ((bool) $this->paramsarray['tp_info_display']): ?>
                                <div id="thirdparty-info">
                                    <p><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_THIRD_PARTY_INFO_EXPLANATION'); ?></p>
                                    <div>
                                        <p><input type="text" required="true" name="mandate_ref" size="500" id="mandate_ref" value="<?php echo $this->item->mandate_ref; ?>" placeholder="<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_THIRD_PARTY_INFO_REFERENCE'); ?>"/></p>
                                        <p><input type="text" required="true" name="mandate_contact" size="75" id="mandate_contact" value="<?php echo $this->item->mandate_contact; ?>" placeholder="<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_THIRD_PARTY_INFO_CONTACT'); ?>"/></p>
                                        <p><input type="text" required="true" name="mandate_email" size="100" id="mandate_email" class="validate-email" value="<?php echo $this->item->mandate_email; ?>" placeholder="<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_THIRD_PARTY_INFO_EMAIL'); ?>"/></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- ENDOF THIRD PARTY -->

                <!-- PRODUCTS -->
                <div class="row-fluid shop-product">
                    <div class="row-fluid" >
                        <hr>
                        <div class="span10" >
                            <h2><?php echo JText::plural('COM_EASYSDI_SHOP_BASKET_N_SELECTED_DATA', $this->item->extractionsNb); ?>
                                <span id="pricingTotalAmountTI-container" style="<?php if (!isset($this->item->pricing) || !$this->item->pricing->isActivated || !isset($this->item->pricing->cal_total_amount_ti)): ?>display: none;<?php endif; ?>">
                                    ( <span class="pricingTotalAmountTI"><?php echo!isset($this->item->pricing) || !$this->item->pricing->isActivated || !isset($this->item->pricing->cal_total_amount_ti) ? '' : Easysdi_shopHelper::priceFormatter($this->item->pricing->cal_total_amount_ti); ?></span> )
                                </span>
                            </h2>
                        </div>
                        <div class="span2" >
                            <?php if (!empty($this->item->visualization)): ?>
                                <div class="pull-right">
                                    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_map&view=preview') . '&id=' . $this->item->visualization; ?>" target="_blank"
                                       title="<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_TOOLTIP_PREVIEW'); ?>"
                                       class="btn btn-eye btn-mini pull-right" >
                                        <i class="icon-eye"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php foreach ($this->item->extractions as $supplier_id => $supplier): ?>
                            <table class="table table-striped" rel="<?php echo $supplier_id; ?>">
                                <thead>
                                    <tr>
                                        <td class="product_column" ><h4><?php echo JText::plural('COM_EASYSDI_SHOP_BASKET_DATA_SUPPLIER', count($supplier->items)) . ' : ' . $supplier->name; ?></h4></td>
                                        <td class="price_column" style="<?php if (!isset($this->item->pricing) || !$this->item->pricing->isActivated): ?>display:none;<?php endif; ?>"><h4><?php echo JText::_('COM_EASYSDI_SHOP_PRICES_TTC'); ?></h4></td>
                                        <td class="action_column">&nbsp;</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($supplier->items as $item): ?>
                                        <tr rel="<?php echo $item->id; ?>">
                                            <td class="product_column">
                                                <a href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&view=sheet&guid=' . $item->metadataguid); ?>"><?php echo $item->name; ?></a>
                                                <ul class="product_properties">
                                                    <?php foreach ($item->properties as $property): ?>
                                                        <li id="shop-basket-property-id-<?php echo $property->id; ?>"><span class="shop-basket-property-name"><?php echo $property->name; ?> :</span> 
                                                            <?php
                                                            $c = count($property->values);
                                                            $i = 0;
                                                            foreach ($property->values as $value):
                                                                ?><span class="shop-basket-property-value"><?php
                                                                echo empty($value->value) ? $value->name : $value->value;
                                                                ?></span><?php
                                                                $i++;
                                                                if ($i < $c)
                                                                    echo ', ';
                                                            endforeach;
                                                            ?></li>
                                                        <?php endforeach; ?>
                                                </ul>
                                            </td>
                                            <td class="price_column" style="<?php if (!isset($this->item->pricing) || !$this->item->pricing->isActivated): ?>display:none;<?php endif; ?>">
                                                <?php
                                                $product = $this->item->pricing->suppliers[$supplier_id]->products[$item->id];

                                                if ($product->cfg_pricing_type == Easysdi_shopHelper::PRICING_FREE):
                                                    echo JText::_('COM_EASYSDI_SHOP_BASKET_PRODUCT_FREE');
                                                else:
                                                    echo Easysdi_shopHelper::priceFormatter($product->cal_total_amount_ti);

                                                    $rebate = false;
                                                    $as = '';
                                                    $discount = '';
                                                    if ($product->cfg_pct_category_profile_discount > 0 || $product->cfg_pct_category_supplier_discount > 0) {
                                                        $rebate = true;
                                                        if ($product->cfg_pct_category_supplier_discount > $product->cfg_pct_category_profile_discount) {
                                                            $as = $product->ind_lbl_category_supplier_discount;
                                                            $discount = $product->cfg_pct_category_supplier_discount;
                                                        } else {
                                                            $as = $product->ind_lbl_category_profile_discount;
                                                            $discount = $product->cfg_pct_category_profile_discount;
                                                        }
                                                    }
                                                    ?>
                                                    <i class="icon-white icon-info hasTooltip" 
                                                       title="<?php echo JText::sprintf('COM_EASYSDI_SHOP_BASKET_TOOLTIP_REBATE_INFO', $as, $discount); ?>"
                                                       style="<?php if (!$rebate): ?>display:none;<?php endif; ?>">
                                                    </i>
                                                <?php
                                                endif;
                                                ?>
                                            </td>
                                            <td class="action_column">
                                                <a href="#" class="btn btn-danger btn-mini pull-right" title="<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_TOOLTIP_REMOVE'); ?>"><i class="icon-white icon-remove"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot style="<?php if (!isset($this->item->pricing) || !$this->item->pricing->isActivated): ?>display:none;<?php endif; ?>">
                                    <tr class="supplier_fixed_fee_row">
                                        <td class="price_title_column price_title_fixed_fees"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_TAX'); ?></td>
                                        <td class="price_column supplier_cal_fee_ti"><?php echo isset($this->item->pricing->suppliers[$supplier_id]->cal_fee_ti) ? Easysdi_shopHelper::priceFormatter($this->item->pricing->suppliers[$supplier_id]->cal_fee_ti) : '-'; ?></td>
                                        <td class="action_column">&nbsp;</td>
                                    </tr>
                                    <tr class="supplier_total_row">
                                        <td class="price_title_column price_title_provider_total"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_SUPPLIER_SUBTOTAL'); ?></td>
                                        <td class="price_column supplier_cal_total_amount_ti"><?php echo isset($this->item->pricing->suppliers[$supplier_id]->cal_total_amount_ti) ? Easysdi_shopHelper::priceFormatter($this->item->pricing->suppliers[$supplier_id]->cal_total_amount_ti) : '-'; ?></td>
                                        <td class="action_column">&nbsp;</td>
                                    </tr>
                                    <tr class="supplier_rebate_row">
                                        <td class="price_title_column price_title_provider_discount"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_SUPPLIER_REBATE'); ?></td>
                                        <td class="price_column supplier_cal_total_rebate_ti"><?php echo isset($this->item->pricing->suppliers[$supplier_id]->cal_total_rebate_ti) ? Easysdi_shopHelper::priceFormatter($this->item->pricing->suppliers[$supplier_id]->cal_total_rebate_ti) : '-'; ?></td>
                                        <td class="action_column">&nbsp;</td>
                                    </tr>
                                </tfoot>
                            </table>
                        <?php endforeach; ?>

                        <!-- TOTAL -->
                        <table class="table table-striped" id='pricingTotal-table' style="<?php if (!isset($this->item->pricing) || !$this->item->pricing->isActivated): ?>display:none;<?php endif; ?>">
                            <thead>
                                <tr>
                                    <td><h4><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PLATFORM'); ?></h4></td>
                                    <td class="price_column">&nbsp;</td>
                                    <td class="action_column">&nbsp;</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr class="basket_fixed_fee_row">
                                    <td><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_FEE'); ?></td>
                                    <td class="price_column">
                                        <span class="pricingFeeTI"><?php echo isset($this->item->pricing->cal_fee_ti) ? Easysdi_shopHelper::priceFormatter($this->item->pricing->cal_fee_ti) : '-'; ?></span>
                                    </td>
                                    <td class="action_column">&nbsp;</td>
                                </tr>
                                <tr class="basket_total_row">
                                    <td><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_TOTAL'); ?></td>
                                    <td class="price_column">
                                        <span class="pricingTotalAmountTI"><?php echo!isset($this->item->pricing->cal_total_amount_ti) ? '-' : Easysdi_shopHelper::priceFormatter($this->item->pricing->cal_total_amount_ti); ?></span>
                                    </td>
                                    <td class="action_column">&nbsp;</td>
                                </tr>
                            </tfoot>
                        </table>
                        <!-- ENDOF TOTAL -->

                    </div>
                </div>
                <!-- ENDOF PRODUCTS -->

                <!-- INFORMATIONS -->
                <?php
                $footerMessage = (JFactory::getUser()->guest ? $this->paramsarray['shopinfomessageguest'] : $this->paramsarray['shopinfomessage']);
                if (!empty($footerMessage)):
                    ?>
                    <div class="row-fluid shop-info" >
                        <div class="row-fluid" ><h2><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_MESSAGE'); ?></h2>
                            <hr>
                            <div class="shop-information"><?php echo $footerMessage; ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- ENDOF INFORMATIONS -->

                <!-- TOOLBAR -->
                <div class="row-fluid shop-basket-toolbar" >
                    <hr>
                    <?php if ($this->get('user')->isEasySDI): ?>
                        <div id="termsofuse-container">
                            <label class="checkbox">
                                <input type="checkbox" id="termsofuse" > <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_I_ACCEPT') ?> <a href="<?php echo $this->paramsarray['termsofuse']; ?>" target="_blank"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_TERMS') ?></a> <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_OF_USE') ?>
                            </label>
                        </div>
                        <div id="ordername-container">
                            <input class="btn-toolbar" id="ordername" name="ordername" type="text" placeholder="<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_ORDER_NAME'); ?>" value="<?php
                            if (!empty($this->item->name)) : echo $this->item->name;
                            endif;
                            ?>">
                        </div>
                        <div id="toolbar-container">
                            <div class="btn-toolbar" id="toolbar">
                                <div class="btn-wrapper" id="toolbar-draft">
                                    <button class="btn btn-small" rel="basket.draft"><span class="icon-archive"></span><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_BTN_SAVE') ?></button>
                                </div>
                                <div class="btn-wrapper" id="toolbar-estimate">
                                    <button class="btn btn-small" rel="basket.estimate" <?php
                                    if (isset($this->item->pricing->cal_total_amount_ti) || !$this->item->pricing->isActivated): echo 'style="display: none;"';
                                    else : echo 'style="display: inline-block;"';
                                    endif;
                                    ?>><span class="icon-edit"></span><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_BTN_ESTIMATE') ?></button>
                                </div>
                                <div class="btn-wrapper" id="toolbar-order">
                                    <button class="btn btn-small" rel="basket.order"><span class="icon-publish"></span><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_BTN_ORDER') ?></button>
                                </div>
                            </div>

                            <input type="hidden" name="action" value="" />
                        </div>
                    <?php else: ?>
                        <div class="span5 pull-right" >
                            <button class="btn btn-small" id="btn-login" name="btn-login"><?php echo JText::_('COM_EASYSDI_CORE_LOGIN'); ?></button>
                            <button class="btn btn-small" id="btn-create-account" name="btn-create-account"><?php echo JText::_('COM_EASYSDI_CORE_CREATE_ACCOUNT'); ?></button>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- ENDOF TOOLBAR -->

            </div>
        </div>

        <div id="modal-perimeter" style="margin-left:-45%;min-height:500px; width:90%;"  class="modal invisible " tabindex="-1" role="dialog" aria-labelledby="modal-perimeter-label" data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="cancel();">×</button>
                <h3 id="myModalLabel"><span id="shop-perimeter-modal-title"><?php echo empty($this->item->extent->features) ? JText::_('COM_EASYSDI_SHOP_BASKET_DEFINE_PERIMETER') : JText::_('COM_EASYSDI_SHOP_BASKET_MODIFY_PERIMETER'); ?></span><span id="shop-perimeter-modal-title-surface"></span></h3>
            </div>
            <div class="modal-body" style="max-height: 500px;">
                <div class="container-fluid" >
                    <div class="row-fluid">
                        <div class="span9">
                            <div  >
                                <?php
                                echo $this->mapscript;
                                ?>
                            </div>
                        </div>
                        <div class="span3">
                            <?php if ($this->importEnabled): ?>
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#basket-tab-select-tools" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_TAB_SELECT_TOOLS'); ?></a>
                                    </li>
                                    <li class="pull-right">
                                        <a href="#basket-tab-import-polygon" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_TAB_IMPORT_POLY'); ?></a>
                                    </li>
                                </ul>
                            <?php else: ?>
                                <br/>
                            <?php endif; ?>
                            <div class="tab-content">
                                <!-- Begin Tabs -->
                                <div class="tab-pane active" id="basket-tab-select-tools">
                                    <div class="btn-group" data-toggle="buttons-radio">
                                        <?php
                                        foreach ($this->item->perimeters as $perimeter):
                                            if ($perimeter->id == 1):
                                                if (!$this->item->isrestrictedbyperimeter || $this->params->get('userperimeteractivated') != 1):
                                                    ?>
                                                    <div class="btn-group btn-group-perimeter-selection">
                                                        <a href="#" id="btn-perimeter<?php echo $perimeter->id; ?>a" class="btn btn-perimeter-selection" 
                                                           onClick="selectRectangle();
                                                                                   jQuery('#help-perimeter').html('<?php echo nl2br(JText::_('COM_EASYSDI_SHOP_BASKET_FREE_PERIMETER_RECTANGLE_HELP')); ?>');

                                                                                   return false;">
                                                            <i class=" icon-checkbox-unchecked"></i> <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_FREE_PERIMETER_RECTANGLE'); ?></a>
                                                    </div>        
                                                    <div class="btn-group btn-group-perimeter-selection">
                                                        <a href="#" id="btn-perimeter<?php echo $perimeter->id; ?>b"  class="btn btn-perimeter-selection" 
                                                           onClick="selectPolygon();
                                                                                   jQuery('#help-perimeter').html('<?php echo nl2br(JText::_('COM_EASYSDI_SHOP_BASKET_FREE_PERIMETER_POLYGON_HELP')); ?>');
                                                                                   return false;">
                                                            <i class="icon-star-empty"></i> <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_FREE_PERIMETER_POLYGON'); ?></a>
                                                    </div>                                       
                                                    <?php
                                                endif;
                                            elseif ($perimeter->id == 2 && $this->params->get('userperimeteractivated') == 1):
                                                if ($this->user->isEasySDI):
                                                    ?>
                                                    <div class="btn-group btn-group-perimeter-selection">
                                                        <a href="#" id="btn-perimeter<?php echo $perimeter->id; ?>" class="btn btn-perimeter-selection" 
                                                           onClick="selectPerimeter<?php echo $perimeter->id; ?>();
                                                                                   jQuery('#help-perimeter').html('<?php echo nl2br(JText::_('COM_EASYSDI_SHOP_BASKET_MY_PERIMETER_HELP')); ?>');

                                                                                   return false;">
                                                            <i class="icon-user"></i> <?php echo JText::_($perimeter->name); ?></a>


                                                        <?php
                                                        $perimeterScript.="function selectPerimeter" . $perimeter->id . "() {\n" .
                                                                "   selectMyPerimeter('" . $perimeter->id . "', '" . JText::_('MYPERIMETER') . "', '" . addslashes(preg_replace('/\r\n/', '', $this->user->perimeter)) . "');\n" .
                                                                "}\n" .
                                                                "function reloadFeatures" . $perimeter->id . "() {\n" .
                                                                "   reloadMyPerimeter('" . $perimeter->id . "', '" . JText::_('MYPERIMETER') . "', '" . addslashes(preg_replace('/\r\n/', '', $this->user->perimeter)) . "');\n" .
                                                                "}\n";
                                                        echo($perimeterScript);
                                                        ?>

                                                    </div>
                                                    <?php
                                                endif;
                                            else:
                                                ?>
                                                <div class="btn-group btn-group-perimeter-selection">
                                                    <a href="#" id="btn-perimeter<?php echo $perimeter->id; ?>" class="btn btn-perimeter-selection" 
                                                       onClick="selectPerimeter<?php echo $perimeter->id; ?>();
                                                                           jQuery('#help-perimeter').html('<?php echo nl2br(JText::_('COM_EASYSDI_SHOP_BASKET_PERIMETER_HELP')); ?>');

                                                                           return false;">
                                                        <i class="icon-grid-view"></i> <?php echo JText::_($perimeter->name); ?></a>
                                                    <!-- test ybi -->



                                                    <?php
                                                    if ($this->item->isrestrictedbyperimeter && $this->params->get('userperimeteractivated') == 1):
                                                        $perimeterScript.="var userperimeter = '" . $this->user->perimeter . "';\n";
                                                    endif;
                                                    $perimeterScript.="function selectPerimeter" . $perimeter->id . "() {\n" .
                                                            "    return selectPerimeter(" . json_encode($perimeter) . "," . (($this->item->isrestrictedbyperimeter && $this->user->isEasySDI && $this->params->get('userperimeteractivated') == 1) ? "1" : "0") . ");\n" .
                                                            "}\n" .
                                                            "function reloadFeatures" . $perimeter->id . "() {\n" .
                                                            "reloadFeatures(" . json_encode($perimeter) . ");\n" .
                                                            "}\n";
                                                    ?>
                                                </div>
                                            <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                    <div id="help-perimeter" class="help-block"><?php echo nl2br(JText::_('COM_EASYSDI_SHOP_BASKET_PAN_HELP')); ?></div>
                                </div>
                                <?php if ($this->importEnabled): ?>
                                    <div class="tab-pane" id="basket-tab-import-polygon">
                                        <p id="basket-impot-poly-help"><?php echo nl2br(JText::_('COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_HELP')); ?></p>
                                        <textarea id="basket-import-polygon-textarea" placeholder="<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_PLACEHOLDER'); ?>"></textarea>
                                        <br/>
                                        <br/>
                                        <a href="#" class="btn btn-success pull-right" onclick="importPolygonFromText();" ><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_IMPORT_POLY_BTN_LABEL'); ?></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <script>
    <?php
    echo($perimeterScript);
    ?>
                            </script>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" onclick="cancel();" aria-hidden="true"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_MODAL_BTN_CLOSE") ?></button>
                <button class="btn btn-primary" id="btn-saveperimeter" onclick="savePerimeter();" data-dismiss="modal"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_MODAL_BTN_SAVE") ?></button>
            </div>
        </div>

        <div id="modal-dialog-remove" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" >
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

        <div id="modal-error" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="errorModalLabel"  aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 id="errorModalLabel"></h3>
                </div>               
                <div class="modal-body"></div>                
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" ><?php echo JText::_('OK'); ?></button>
                </div>              
            </div>
        </div>

        <div id="myModalProcess" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabelProcess" aria-hidden="true" data-backdrop="static" data-keyboard="false" >
            <div class="modal-header">
                <h3 id="myModalLabelProcess"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PROCESSING'); ?></h3>
            </div>
            <div class="modal-body" style="max-height: 500px;">
                <span><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PROCESSING_TO_SUPPLIERS'); ?></span>
                <div class="progress progress-striped active" style="position:relative;">
                    <div  id="processProgress" class="bar"></div>
                    <div  id="processProgressText" style="position:absolute;text-align:center;width:100%"></div>
                </div>
            </div>
            <div class="modal-footer">
                <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PROCESS_TAKE_A_WHILE'); ?>
            </div>
        </div>

        <script>
            Ext.onReady(function () {
                if ('undefined' === typeof app) {
                    app = window.appname;
                }
                app.on("ready", function () {
                    jQuery('#modal-perimeter').show();
                    initMiniMap();

                    jQuery('a[href$="#modal-perimeter"]').click(function () {
                        jQuery('#modal-perimeter').removeClass('invisible');
                    });
    <?php echo($this->basketScriptPlugins); ?>
                });
            });

            function initialization() {
                miniBaseLayers[0].events.unregister("loadend", miniBaseLayers[0], initialization);
                initDraw();
                initMyPerimeter();
                addAlertControl(window.appname.mapPanel.map);
                addVisibilityChecks(window.appname.mapPanel.map);
                slider = window.appname.mapPanel.map.indoorlevelslider;
                if (slider) {
                    slider.on("indoorlevelchanged", function () {
                        jQuery('#t-level').val(JSON.stringify(slider.getLevel()));
                    });
    <?php if (isset($this->item->extent->level) && !empty($this->item->extent->level)): ?>
                        slider.changeIndoorLevelByCode(slider, "<?php echo json_decode($this->item->extent->level)->code; ?>");
    <?php else : ?>
                        jQuery('#t-level').val(JSON.stringify(slider.getLevel()));
                        jQuery('#level').val(JSON.stringify(slider.getLevel()));
    <?php endif; ?>
                }
    <?php if (!empty($this->item->extent)): ?>
                    jQuery('#btn-perimeter<?php echo $this->item->extent->id; ?>').addClass('active');
    <?php endif; ?>

    <?php if (!empty($this->item->extent) && isset($this->item->extent->features)): ?>
                    //selectPerimeter<?php echo $this->item->extent->id; ?>();
                    reloadFeatures<?php echo $this->item->extent->id; ?>();
    <?php endif; ?>
                jQuery('#modal-perimeter').hide();
            }
            ;
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
        <input type="hidden" name="features" id="features" value='<?php
        if (isset($this->item->extent->features)) {
            if (!is_array($this->item->extent->features)):
                echo $this->item->extent->features;
            else:
                echo htmlspecialchars(json_encode($this->item->extent->features), ENT_QUOTES, 'UTF-8');
            endif;
        }
        ?>' />
        <input type="hidden" name="t-perimeter" id="t-perimeter" value="<?php
        if (isset($this->item->extent->id)): echo $this->item->extent->id;
        endif;
        ?>" />
        <input type="hidden" name="t-perimetern" id="t-perimetern" value="<?php
        if (isset($this->item->extent->name)): echo $this->item->extent->name;
        endif;
        ?>" />
        <input type="hidden" name="t-features" id="t-features" value='<?php
        if (isset($this->item->extent->features)) {
            if (!is_array($this->item->extent->features)):
                echo $this->item->extent->features;
            else:
                echo htmlspecialchars(json_encode($this->item->extent->features), ENT_QUOTES, 'UTF-8');
            endif;
        }
        ?>' />
        <input type="hidden" name="t-surface" id="t-surface" value="<?php
        if (isset($this->item->extent->surface)): echo $this->item->extent->surface;
        endif;
        ?>" />
        <input type="hidden" name="surfacemin" id="surfacemin" value="<?php echo $this->item->surfacemin; ?>" />
        <input type="hidden" name="surfacemax" id="surfacemax" value="<?php echo $this->item->surfacemax; ?>" />            
        <input type="hidden" name="level" id="level" value='<?php
        if (isset($this->item->extent->level)) : echo $this->item->extent->level;
        endif;
        ?>' />
        <input type="hidden" name="t-level" id="t-level" value='<?php
        if (isset($this->item->extent->level)) : echo $this->item->extent->level;
        endif;
        ?>' />
        <input type="hidden" name="v-features" id="v-features" value="" />            
        <input type="hidden" name="task" id = "task" value = "" />
        <input type="hidden" name="option" value = "com_easysdi_shop" />
        <input type="hidden" name="id" id = "id" value = "" />
        <input type="hidden" name="surfacedigit" id = "surfacedigit" value = "<?php echo $this->paramsarray['surfacedigit']; ?>" />
        <input type="hidden" name="maxmetervalue" id = "maxmetervalue" value = "<?php echo $this->paramsarray['maxmetervalue']; ?>" />
        <input type="hidden" name="freeperimetertool" id="freeperimetertool" value='<?php
        if (isset($this->item->freeperimetertool)): echo $this->item->freeperimetertool;
        endif;
        ?>' />
        <input type="hidden" name="t-freeperimetertool" id="t-freeperimetertool" value='<?php
               if (isset($this->item->freeperimetertool)): echo $this->item->freeperimetertool;
               endif;
               ?>' />


        <?php echo JHtml::_('form.token'); ?>
    </form>

    <?php
//empty basket
else:
    $app = JFactory::getApplication();
    $emptybasketurl = $app->getParams('com_easysdi_shop')->get('emptybasketurl');
    //in case of custom empty basket page, redirect to it, otherwise show a simple message
    if (!empty($emptybasketurl)):
        $webapp = JApplicationWeb::getInstance();
        $webapp->clearHeaders();
        $webapp->setHeader('Location', JUri::base() . JRoute::_($emptybasketurl));
        header("HTTP/1.0 302 Found");
        $webapp->sendHeaders();
    else:
        echo JText::_('COM_EASYSDI_SHOP_BASKET_MESSAGE_EMPTY_BASKET');
    endif;

endif;

