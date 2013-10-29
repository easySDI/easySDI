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

JHTML::_('behavior.modal');


?>

<?php
if (empty($this->items)):
    echo JText::_('COM_EASYSDI_SHOP_NO_ITEMS');
else:
?>
    <div class="well">
        <div class="items">                      
            <table class="table table-striped">
                
                    <thead>
                    <tr>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_NAME') ?></th>
                        <th></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED') ?></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    
                        <tbody>
                    <?php foreach ($this->items as $item) : ?>
                        <tr class="order-line order-line-new">
                            <td><i><a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.edit&id=' . $item->id); ?>"><?php echo $item->name; ?></a></i></td>
                            <td class="ordertype">
                                <?php
                                if($item->ordertype_id == 1):
                                    $classicontype = 'icon-cart';
                                elseif ($item->ordertype_id == 2):
                                    $classicontype = 'icon-lamp';
                                else:
                                    $classicontype = 'icon-edit2';
                                endif;
                                ?>
                                <i class="<?php echo $classicontype; ?>"></i> <?php echo JText::_($item->ordertype); ?>
                            </td>
                            <td class="ordercreated"><?php echo $item->created; ?></td>
                            <td class="orderstate">
                                <?php if ($item->ordertype_id != 3 ):?>
                                <?php
                                if($item->orderstate_id == 1):
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
                                <span class="label <?php echo $classlabel; ?> "><?php echo JText::_($item->orderstate); ?></span>
                                <?php endif; ?>
                            </td>
                            <td >
                                <?php if ($item->orderstate_id == 5 || $item->orderstate_id == 3 ):?>
                                 <i class="icon-flag-2"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group pull-right">
                                    <a class="btn btn-success btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                        <?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_ACTIONS'); ?>
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php if ($item->ordertype_id == 3 ):?>
                                            <li>
                                                <a  href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=basket.load&id=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_LOAD_DRAFT_INTO_BASKET'); ?></a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>                                    
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif; ?>
