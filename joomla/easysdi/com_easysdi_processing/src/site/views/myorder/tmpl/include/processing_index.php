<?php
    $user_processes_types= Easysdi_processingHelper::getUserProcesses();
?>

<h1><?php echo JText::_('COM_EASYSDI_PROCESSING_INDEX_HEADER'); ?></h1>
<div class="easysdi_processtype_list">
    <?php
foreach ($user_processes_types as $processtype) {
        $userRoles=Easysdi_processingHelper::getCurrentUserRolesOnData($processtype);
                //var_dump($userRoles);
        ?>

            <div class='item'>
                <h3><?php echo $processtype->name ?></h3>
                <span class="badge"><?php echo $processtype->t_count ?></span>
                <p><?php echo $processtype->desc ?></p>

                <a href="<?php echo JRoute::_('index.php?option=com_easysdi_processing&amp;tab=order&amp;action=new&amp;processing='.$processtype->id); ?>" class="btn btn-primary btn-block"><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_BUTTON'); ?></a>
            </div>

        <?php
    }
    ?>

</div>

<?php
    //var_dump($user_processes_types);
?>
