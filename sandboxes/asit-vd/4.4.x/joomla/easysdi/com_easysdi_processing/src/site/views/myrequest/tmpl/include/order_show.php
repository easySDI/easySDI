<?php

$order= Easysdi_processingHelper::getOrder($id);
$userRoles=Easysdi_processingHelper::getCurrentUserRolesOnData($order);
?>
<h1><?php echo $order->name ?> <?php echo Easysdi_processingStatusHelper::status($order->status) ?></h1>
<h2><?php echo JText::_('COM_EASYSDI_PROCESSING_TITLE'); ?>: <?php echo $order->processing_name ?></h2>

<div class="well">
    commande passée le <?php echo $order->created ?>
    <?php if (count(array_intersect(['contact','obs','superuser'], $userRoles))>0) { ?>
     par <?php echo $order->created_by_name ?><br/>
    <?php } ?>
</div>
<h3>données</h3>
<?php  echo $order->input ?> !TODO
<h3>paramètres</h3>
<?php echo Easysdi_processingParamsHelper::table($order->processing_parameters,$order->parameters) ?>

<?php
if (NULL !== $order->publish_at) {
    ?>
    <h2>résultat</h2>

        <?php  echo $order->output ?> !TODO<br/>
        publié le <?php echo $order->publish_at ?> par <?php echo $order->contact_name ?>
        <?php
    } else {
        if (in_array('contact',$userRoles)) {
            if ('new' == $order->status) {
                ?>
                !TODO set active<br/>
                <?php
            }
            if ('active' == $order->status) {
                ?>
                !TODO set done<br/>
                !TODO set fail<br/>
                <?php
            }
        }
        if (in_array('creator',$userRoles) && 'new' == $order->status) {
                   ?>
                !TODO cancel<br/>
                  <?php
        }
    }
    //var_dump($order);
    //var_dump($userRoles);
    ?>