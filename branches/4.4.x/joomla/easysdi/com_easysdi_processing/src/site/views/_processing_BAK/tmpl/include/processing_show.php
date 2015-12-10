<?php
$processing= Easysdi_processingHelper::getProcess($id);
$orders= Easysdi_processingHelper::getOrders($id);
?>


<div class="well">
    <h1><?php echo $processing->name ?></h1>
    <p><?php echo $processing->description ?></p>
</div>

<div class="well">

    <table class="table process-table table-striped">
        <thead>
            <tr>
                <th><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_CREATED_BY'); ?></th>
                <th><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_TITLE'); ?></th>
                <th><?php echo JText::_('COM_EASYSDI_PROCESSING_PARAMS'); ?></th>
                <th><?php echo JText::_('COM_EASYSDI_PROCESSING_OUTPUT'); ?></th>
                <th><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_STATUS'); ?></th>
                <th><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_CREATED'); ?></th>
                <th><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_PUBLISHED_AT'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($orders as $order) {
                ?>
                <tr>
                    <td><?php echo $order->created_by_name ?></td>
                    <td><a href="<?php echo JRoute::_('index.php?option=com_easysdi_processing&amp;tab=order&amp;action=show&amp;id='.$order->id); ?>"><?php echo $order->name ?></a></td>
                    <td></td>
                    <td></td>
                    <td><?php echo Easysdi_processingStatusHelper::status($order->status) ?></td>
                    <td><?php echo $order->created ?></td>
                    <td><?php echo $order->publish_at ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

</div>

