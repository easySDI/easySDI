<?php
$user_process_orders_count=Easysdi_processingHelper::getUserProcessOrdersCount();
?>
<ul class="nav nav-tabs nav-stacked">
    <li<?php echo $tab=='order'?' class="active"':'' ?>>
    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_processing'); ?>">
        <?php echo JText::_('COM_EASYSDI_MY_PROCESSING_ORDER_LINK'); ?> <span class="badge pull-right"><?php echo $user_process_orders_count; ?></span>
    </a>
</li>
</ul>

<?php
$contact_processes= Easysdi_processingHelper::getContactProcesses();
if(count($contact_processes)>0) {
    ?>
    <h4><?php echo JText::_('COM_EASYSDI_MY_PROCESSING_REQUEST_LINK') ?> :</h4>
    <ul class="nav nav-tabs nav-stacked">
        <?php
        foreach ($contact_processes as $process) {
            ?>
            <li<?php echo ($tab=='processing'&&$action=='show'&&$id==$process->id)?' class="active"':'' ?>>
                <a href="<?php echo JRoute::_('index.php?option=com_easysdi_processing&tab=processing&action=show&alias='.$process->alias); ?>">
                <?php
                echo $process->name;
                if ($process->t_new_count>0) { echo '<span class="badge badge-warning pull-right">'.$process->t_new_count.' en attente</span>'; }
                if ($process->t_active_count>0) { echo '<span class="badge badge-warning pull-right">'.$process->t_active_count.' en cours</span>'; }
                ?>

                </a>
            </li>

            <?php
        }
        ?>
    </ul>
    <?php
}
?>
