<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// no direct access
defined('_JEXEC') or die;
?>

<div class='catalog catalog-assignments'>
    <h1><?php echo JText::_('COM_EASYSDI_CATALOG_TITLE_ASSIGNMENTS'); ?> <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resource.edit&id=' . (int) $this->state->resource['id']); ?>"><?php echo $this->state->resource['name'] ?></a></h1>
     <div class="well">
         <div class="row-fluid">
             <div class="btn-group pull-left">
                 <a class="btn btn-success" href="<?php echo $this->backUrl; ?>">
                    <?php echo JText::_('COM_EASYSDI_CATALOG_BACK'); ?>
                </a>
             </div>
         </div>
     </div>
    <div class="items">
        <div class="well">
            <div class="row-fluid">
                <table  class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo JText::_('COM_EASYSDI_CATALOG_ASSIGNMENTS_ASSIGNED_BY'); ?></th>
                            <th><?php echo JText::_('COM_EASYSDI_CATALOG_ASSIGNMENTS_ASSIGNED_TO'); ?></th>
                            <th><?php echo JText::_('COM_EASYSDI_CATALOG_ASSIGNMENTS_ASSIGNED_TIME'); ?></th>
                            <th><?php echo JText::_('COM_EASYSDI_CATALOG_ASSIGNMENTS_ASSIGNED_TEXT'); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tfoot>
                    </tfoot>
                    <tbody>
                        <?php foreach ($this->items as $item): ?>
                            <tr>
                                <td><?php echo $item->assigned_by ?></td>
                                <td><?php echo $item->assigned_to ?></td>
                                <td><?php echo $item->assigned ?></td>
                                <td><?php echo nl2br($item->text) ?></td>
                                <td></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>



</div>