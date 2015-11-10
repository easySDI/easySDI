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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if(task == 'order.cancel')
            location.href='./index.php?option=com_easysdi_shop&view=orders';
    }
</script>

<?php
    $item= $this->item;
?>
<h1><?php echo $item->name; ?></h1>
<div class='row'>
<div class="span6 offset3 well">


        <dl>

            <dt><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_NAME'); ?>&nbsp;</dt>
            <dl><?php echo $item->name; ?></dl>

            <dt><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_ORDERTYPE'); ?>&nbsp;</dt>
            <dl><?php
                if ('order'===$item->ordertype) {
                    echo '<i class="icon-cart"></i>&nbsp;';
                }
                echo JText::_($item->ordertype);
                ?></dl>

                <dt><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_STATE'); ?>&nbsp;</dt>
                <dl><?php
                    if ($item->ordertype_id != 3):
                        if ($item->orderstate_id == 1):
                            $classlabel = '';
                        elseif ($item->orderstate_id == 2):
                            $classlabel = '';
                        elseif ($item->orderstate_id == 3):
                            $classlabel = 'label-success';
                        elseif ($item->orderstate_id == 4):
                            $classlabel = 'label-warning';
                        elseif ($item->orderstate_id == 5):
                            $classlabel = 'label-info';
                        elseif ($item->orderstate_id == 6):
                            $classlabel = 'label-inverse';
                        endif;
                        ?>
                        <span class="label <?php echo $classlabel; ?> "><?php
                            echo JText::_($item->orderstate);
                            ?></span><?php
                            endif;
                            ?></dl>

                            <dt><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_USER'); ?>&nbsp;</dt>
                            <dl><?php echo $item->client; ?></dl>


                            <dt><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED'); ?>&nbsp;</dt>
                            <dl><?php echo $item->created; ?></dl>

                            <?php
                            if ('0000-00-00 00:00:00'!=$item->completed) {
                            ?>
                            <dt><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_COMPLETED'); ?>&nbsp;</dt>
                            <dl><?php echo $item->completed; ?></dl>
                            <?php
                            }
                            ?>

                            <dt><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_PRODUCTS'); ?>&nbsp;
                           <?php
                           $products= explode(PHP_EOL, $item->products);
                           ?></dt>
                           <dl><ul><?php
                           foreach ($products as $product) {
                               echo "<li>".$product."</li>";
                           }
                           ?></ul></dl>

                        </dl>
                    </div>
                </div>