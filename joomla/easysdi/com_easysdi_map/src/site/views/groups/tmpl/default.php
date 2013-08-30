<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// no direct access
defined('_JEXEC') or die;
?>

<div class="items">
    <ul class="items_list">
        <?php $show = false; ?>
        <?php foreach ($this->items as $item) :?>

                
				<?php
					if($item->state == 1 || ($item->state == 0 && JFactory::getUser()->authorise('core.edit.own',' com_easysdi_map'))):
						$show = true;
						?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_easysdi_map&view=group&id=' . (int)$item->id); ?>"><?php echo $item->guid; ?></a>
								<?php
									if(JFactory::getUser()->authorise('core.edit.state','com_easysdi_map')):
									?>
										<a href="javascript:document.getElementById('form-group-state-<?php echo $item->id; ?>').submit()"><?php if($item->state == 1):?>Unpublish<?php else:?>Publish<?php endif; ?></a>
										<form id="form-group-state-<?php echo $item->id ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=group.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="jform[guid]" value="<?php echo $item->guid; ?>" />
											<input type="hidden" name="jform[alias]" value="<?php echo $item->alias; ?>" />
											<input type="hidden" name="jform[created]" value="<?php echo $item->created; ?>" />
											<input type="hidden" name="jform[modified_by]" value="<?php echo $item->modified_by; ?>" />
											<input type="hidden" name="jform[modified]" value="<?php echo $item->modified; ?>" />
											<input type="hidden" name="jform[ordering]" value="<?php echo $item->ordering; ?>" />
											<input type="hidden" name="jform[state]" value="<?php echo (int)!((int)$item->state); ?>" />
											<input type="hidden" name="jform[checked_out]" value="<?php echo $item->checked_out; ?>" />
											<input type="hidden" name="jform[checked_out_time]" value="<?php echo $item->checked_out_time; ?>" />
											<input type="hidden" name="jform[name]" value="<?php echo $item->name; ?>" />
											<input type="hidden" name="jform[isdefaultopen]" value="<?php echo $item->isdefaultopen; ?>" />
											<input type="hidden" name="jform[access]" value="<?php echo $item->access; ?>" />
											<input type="hidden" name="jform[asset_id]" value="<?php echo $item->asset_id; ?>" />
											<input type="hidden" name="option" value="com_easysdi_map" />
											<input type="hidden" name="task" value="group.save" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
									if(JFactory::getUser()->authorise('core.delete','com_easysdi_map')):
									?>
										<a href="javascript:document.getElementById('form-group-delete-<?php echo $item->id; ?>').submit()">Delete</a>
										<form id="form-group-delete-<?php echo $item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=group.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
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
											<input type="hidden" name="jform[isdefaultopen]" value="<?php echo $item->isdefaultopen; ?>" />
											<input type="hidden" name="jform[access]" value="<?php echo $item->access; ?>" />
											<input type="hidden" name="jform[asset_id]" value="<?php echo $item->asset_id; ?>" />
											<input type="hidden" name="option" value="com_easysdi_map" />
											<input type="hidden" name="task" value="group.remove" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
								?>
							</li>
						<?php endif; ?>

        <?php endforeach; ?>
        <?php if(!$show): ?>
            There are no items in the list
        <?php endif; ?>
    </ul>
</div>
<?php if($show): ?>
    <div class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif; ?>


									<?php if(JFactory::getUser()->authorise('core.create','com_easysdi_map')): ?><a href="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=group.edit&id=0'); ?>">Add</a>
	<?php endif; ?>