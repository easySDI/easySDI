<?php
/**
 * @version     4.3.2
 * @package     mod_easysdi_pendingrequests
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHTML::_('behavior.modal');
JHtml::_('behavior.tooltip');
JHTML::_('behavior.calendar');
JHtml::_('formbehavior.chosen', 'select');
?>

<div>
    <div class="titlewithlink clearfix">
        <h2><?php echo JText::plural('MOD_EASYSDI_PENDINGREQUESTS_TITLE_PENDING_REQUESTS', $totalRequests); ?></h2>
        <a title="<?php echo JText::plural('MOD_EASYSDI_PENDINGREQUESTS_LINK_ALL_REQUESTS', $totalRequests); ?>" href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=requests' . $myRequestsItemString); ?>" class="link"><?php echo JText::plural('MOD_EASYSDI_PENDINGREQUESTS_LINK_ALL_REQUESTS', $totalRequests); ?></a>
    </div>

    <?php if ($requests): ?>

        <table class="table table-hover table-striped">
            <tbody>
                <?php foreach ($requests as $item) : ?>
                    <tr class="order-line order-line-new <?php echo('sdi-orderstate-' . preg_replace('/\s+/', '', $item->orderstate) . ' ' . 'sdi-ordertype-' . preg_replace('/\s+/', '', $item->ordertype) ); ?>">
                        <td class="ordercreated">
                            <span class="hasTip" title="<?php echo JHtml::date($item->created, JText::_('DATE_FORMAT_LC2')); ?>">
                                <?php echo Easysdi_shopHelper::getRelativeTimeString(JFactory::getDate($item->created)); ?>
                            </span>
                        </td>
                        <td class="ordername">
                            <strong>
                                <a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=request.edit&id=' . $item->id); ?>"><?php echo $item->name; ?></a>
                            </strong> - <?php echo $item->id; ?>
                            <?php
                            //only show order type if estimate
                            if ($item->ordertype_id == 2):
                                $classicontype = 'icon-lamp';
                                ?>
                                <i class="<?php echo $classicontype; ?>"></i> <?php echo JText::_($item->ordertype); ?>
                                <?php
                            endif;
                            ?>

                        </td>

                        <td class="orderclient">
                            <span  class="hasTip" title="<?php echo($item->clientname); ?>">
                                <?php echo($item->organismname); ?>
                            </span>
                        </td>
                        <td class="orderproductcount">
                            <span  class="label" >
                                <?php echo( JText::plural('COM_EASYSDI_SHOP_REQUESTS_N_PRODUCTS_TO_PROCESS', $item->productcount)); ?>
                            </span>
                        </td>                            
                        <td class="orderactions">
                            <a class="btn btn-primary btn-small pull-right" href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=request.edit&id=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_SHOP_REQUESTS_REPLY') ?></a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div> 

