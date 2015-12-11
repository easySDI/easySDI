<?php
/**
*** @version     4.0.0
* @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// no direct access
defined('_JEXEC') or die;

?>
<?php include_once(dirname(__FILE__).'/../../header.php'); ?>
<div class="well">
    <div class="btn-group">
      <a class="btn dropdown-toggle  btn-primary" data-toggle="dropdown" href="#">
            <?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_BUTTON'); ?>
            <span class="caret"></span>
        </a>

        <ul class="dropdown-menu">
            <?php
            foreach ($this->user_processes as $processtype) {
                ?>
                <li><a href="<?php echo JRoute::_('index.php?option=com_easysdi_processing&amp;view=processing&amp;task=test&amp;id='.$processtype->id); ?>"><?php echo $processtype->name ?></a></li>
                <?php
            }
            ?>
        </ul>
    </div>
</div>

<div class="well">


    <table class="table process-table table-striped">
        <thead>
            <tr>
                <th><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_TITLE'); ?></th>
                <th><?php echo JText::_('COM_EASYSDI_PROCESSING_TITLE'); ?></th>
                <th><?php echo JText::_('COM_EASYSDI_PROCESSING_PARAMS'); ?></th>
                <th><?php echo JText::_('COM_EASYSDI_PROCESSING_OUTPUT'); ?></th>
                <th><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_STATUS'); ?></th>
                <th><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_CREATED'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($this->items as $order) {
                $userRoles=Easysdi_processingHelper::getCurrentUserRolesOnData($order);
                ?>
                <tr>
                    <td><a href="<?php echo JRoute::_('index.php?option=com_easysdi_processing&amp;view=order&amp;id='.$order->id); ?>"><?php echo $order->name ?></a></td>
                    <td><?php echo $order->processing_name ?> (<?php echo JText::_('COM_EASYSDI_PROCESSING_CONTACT_LABEL'); ?>: <?php echo $order->contact_name; ?>)</td>
                    <td></td>
                    <td></td>
                    <td><?php echo Easysdi_processingStatusHelper::status($order->status) ?></td>
                    <td><?php echo $order->created ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

</div>
<?php include_once(dirname(__FILE__).'/../../footer.php'); ?>
<?php



/*
?>

<?php if($this->items) : ?>

    <div class="items">

            <ul class="items_list">

    <?php foreach ($this->items as $item) :?>

                <li><a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=user&id=' . (int)$item->id); ?>"><?php echo $item->guid; ?></a></li>

    <?php endforeach; ?>

            </ul>

    </div>

     <div class="pagination">
        <?php if ($this->params->def('show_pagination_results', 1)) : ?>
            <p class="counter">
                <?php echo $this->pagination->getPagesCounter(); ?>
            </p>
        <?php endif; ?>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>


<?php endif;*/ ?>