<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;
?>

 <?php
        if (isset($this->user)):
            if ($this->user->isResourceManager()):
                $resourcetypes = $this->user->getResourceType();
                ?>
                <div class="btn-group">
                    <a class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="icon-white icon-plus-sign"></i> New
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php
                        foreach ($resourcetypes as $resourcetype):
                            ?>
                            <li>
                                <a href="index.php/component/easysdi_core/?view=resourceform&layout=edit&resourcetype=<?php echo $resourcetype->id; ?>"><?php echo $resourcetype->label; ?></a>
                            </li>
                            <?php
                        endforeach;
                        ?>
                    </ul>
                </div>
            <?php endif;
            
        endif;
        ?>
                
<div class="items">
    <div class="well">
        <?php $show = false; ?>
        <table class="table">
            <tbody>
                <tr>
                    <th>Ressource name</th>
                    <th>Published</th>
                    <th>
                        Delete
                    </th>

                </tr>
               

                    <?php foreach ($this->items as $item) : ?>
                
                        <?php if (is_array($this->user->authorize($item->id))): //USer has some rights on this item?>
                 <tr>
                            <?php
                            if ($this->user->authorize($item->id, sdiUser::resourcemanager)):
                                $show = true;
                                ?>
                
                                <td>
                                    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=resourceform&id=' . (int) $item->id); ?>"><?php echo $item->name; ?></a>
                                </td>
                                <td>
                                    <a href="javascript:document.getElementById('form-resource-state-<?php echo $item->id; ?>').submit()"><?php
                                        if ($item->state == 1): echo JText::_("COM_EASYSDI_CORE_UNPUBLISH_ITEM");
                                        else: echo JText::_("COM_EASYSDI_CORE_PUBLISH_ITEM");
                                        endif;
                                        ?></a>

                                    <form id="form-resource-state-<?php echo $item->id ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resource.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
                                        <input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
                                        <input type="hidden" name="jform[guid]" value="<?php echo $item->guid; ?>" />
                                        <input type="hidden" name="jform[alias]" value="<?php echo $item->alias; ?>" />
                                        <input type="hidden" name="jform[created]" value="<?php echo $item->created; ?>" />
                                        <input type="hidden" name="jform[modified_by]" value="<?php echo $item->modified_by; ?>" />
                                        <input type="hidden" name="jform[modified]" value="<?php echo $item->modified; ?>" />
                                        <input type="hidden" name="jform[ordering]" value="<?php echo $item->ordering; ?>" />
                                        <input type="hidden" name="jform[state]" value="<?php echo (int) !((int) $item->state); ?>" />
                                        <input type="hidden" name="jform[checked_out]" value="<?php echo $item->checked_out; ?>" />
                                        <input type="hidden" name="jform[checked_out_time]" value="<?php echo $item->checked_out_time; ?>" />
                                        <input type="hidden" name="jform[name]" value="<?php echo $item->name; ?>" />
                                        <input type="hidden" name="jform[description]" value="<?php echo $item->description; ?>" />
                                        <input type="hidden" name="jform[organism_id]" value="<?php echo $item->organism_id; ?>" />
                                        <input type="hidden" name="jform[resourcetype_id]" value="<?php echo $item->resourcetype_id; ?>" />
                                        <input type="hidden" name="jform[access]" value="<?php echo $item->access; ?>" />
                                        <input type="hidden" name="option" value="com_easysdi_core" />
                                        <input type="hidden" name="task" value="resource.save" />
                                        <?php echo JHtml::_('form.token'); ?>
                                    </form>
                                </td>
                                <td>
                                    <a href="javascript:document.getElementById('form-resource-delete-<?php echo $item->id; ?>').submit()"><?php echo JText::_("COM_EASYSDI_CORE_DELETE_ITEM"); ?></a>
                                    <form id="form-resource-delete-<?php echo $item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resource.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
                                        <input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
                                        <input type="hidden" name="jform[guid]" value="<?php echo $item->guid; ?>" />
                                        <input type="hidden" name="jform[alias]" value="<?php echo $item->alias; ?>" />
                                        <input type="hidden" name="jform[created_by]" value="<?php echo $item->created_by; ?>" />
                                        <input type="hidden" name="jform[created]" value="<?php echo $item->created; ?>" />
                                        <input type="hidden" name="jform[modified_by]" value="<?php echo $item->modified_by; ?>" />
                                        <input type="hidden" name="jform[modified]" value="<?php echo $item->modified; ?>" />
                                        <input type="hidden" name="jform[ordering]" value="<?php echo $item->ordering; ?>" />
                                        <input type="hidden" name="jform[state]" value="<?php echo $item->state; ?>" />
                                        <input type="hidden" name="jform[checked_out]" value="<?php echo $item->checked_out; ?>" />
                                        <input type="hidden" name="jform[checked_out_time]" value="<?php echo $item->checked_out_time; ?>" />
                                        <input type="hidden" name="jform[name]" value="<?php echo $item->name; ?>" />
                                        <input type="hidden" name="jform[description]" value="<?php echo $item->description; ?>" />
                                        <input type="hidden" name="jform[organism_id]" value="<?php echo $item->organism_id; ?>" />
                                        <input type="hidden" name="jform[resourcetype_id]" value="<?php echo $item->resourcetype_id; ?>" />
                                        <input type="hidden" name="jform[access]" value="<?php echo $item->access; ?>" />
                                        <input type="hidden" name="option" value="com_easysdi_core" />
                                        <input type="hidden" name="task" value="resource.remove" />
                                        <?php echo JHtml::_('form.token'); ?>
                                    </form>

                                </td>
                            <?php endif; ?>
                                </tr>
                        <?php endif; ?>
                                
                    <?php endforeach; ?>

                
            </tbody>
        </table>
        <?php
        if (!$show):
            echo JText::_('COM_EASYSDI_CORE_NO_ITEMS');
        endif;
        ?>
    </div>
</div>
        <?php if ($show): ?>
            <div class="pagination">
                <p class="counter">
                    <?php echo $this->pagination->getPagesCounter(); ?>
                </p>
                <?php echo $this->pagination->getPagesLinks(); ?>
            </div>
        <?php endif; ?>

       