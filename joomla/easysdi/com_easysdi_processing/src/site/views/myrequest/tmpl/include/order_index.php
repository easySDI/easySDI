<?php

$user_processes_order= Easysdi_processingHelper::getUserProcessOrders();
$user_processes_types= Easysdi_processingHelper::getUserProcesses();
?>
<div class="well">
    <div class="btn-group">
      <a class="btn dropdown-toggle  btn-primary" data-toggle="dropdown" href="#">
            <?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_BUTTON'); ?>
            <span class="caret"></span>
        </a>

        <ul class="dropdown-menu">
            <?php
            foreach ($user_processes_types as $processtype) {
                ?>
                <li><a href="<?php echo JRoute::_('index.php?option=com_easysdi_processing&amp;tab=order&amp;action=new&amp;processing='.$processtype->alias); ?>"><?php echo $processtype->name ?></a></li>
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
            foreach ($user_processes_order as $order) {
                $userRoles=Easysdi_processingHelper::getCurrentUserRolesOnData($order);
                ?>
                <tr>
                    <td><a href="<?php echo JRoute::_('index.php?option=com_easysdi_processing&amp;tab=order&amp;action=show&amp;id='.$order->id); ?>"><?php echo $order->name ?></a></td>
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

<?php
var_dump($user_processes_order);
?>