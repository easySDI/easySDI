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

<div class="items">
    <ul class="items_list">
<?php $show = false; ?>
        <?php foreach ($this->items as $item) : ?>

            
				<?php
					if($item->state == 1 || ($item->state == 0 && JFactory::getUser()->authorise('core.edit.own',' com_easysdi_core.resourcetype.'.$item->id))):
						$show = true;
						?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=resourcetype&id=' . (int)$item->id); ?>"><?php echo $item->guid; ?></a>
								<?php
									if(JFactory::getUser()->authorise('core.edit.state','com_easysdi_core.resourcetype.'.$item->id)):
									?>
										<a href="javascript:document.getElementById('form-resourcetype-state-<?php echo $item->id; ?>').submit()"><?php if($item->state == 1): echo JText::_("COM_EASYSDI_CORE_UNPUBLISH_ITEM"); else: echo JText::_("COM_EASYSDI_CORE_PUBLISH_ITEM"); endif; ?></a>
										<form id="form-resourcetype-state-<?php echo $item->id ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resourcetype.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
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
											<input type="hidden" name="jform[description]" value="<?php echo $item->description; ?>" />
											<input type="hidden" name="jform[logo]" value="<?php echo $item->logo; ?>" />
											<input type="hidden" name="jform[metadata]" value="<?php echo $item->metadata; ?>" />
											<input type="hidden" name="jform[diffusion]" value="<?php echo $item->diffusion; ?>" />
											<input type="hidden" name="jform[view]" value="<?php echo $item->view; ?>" />
											<input type="hidden" name="jform[monitoring]" value="<?php echo $item->monitoring; ?>" />
											<input type="hidden" name="jform[predefined]" value="<?php echo $item->predefined; ?>" />
											<input type="hidden" name="jform[versionning]" value="<?php echo $item->versionning; ?>" />
											<input type="hidden" name="jform[access]" value="<?php echo $item->access; ?>" />
											<input type="hidden" name="option" value="com_easysdi_core" />
											<input type="hidden" name="task" value="resourcetype.save" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
									if(JFactory::getUser()->authorise('core.delete','com_easysdi_core.resourcetype.'.$item->id)):
									?>
										<a href="javascript:document.getElementById('form-resourcetype-delete-<?php echo $item->id; ?>').submit()"><?php echo JText::_("COM_EASYSDI_CORE_DELETE_ITEM"); ?></a>
										<form id="form-resourcetype-delete-<?php echo $item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resourcetype.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
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
											<input type="hidden" name="jform[logo]" value="<?php echo $item->logo; ?>" />
											<input type="hidden" name="jform[metadata]" value="<?php echo $item->metadata; ?>" />
											<input type="hidden" name="jform[diffusion]" value="<?php echo $item->diffusion; ?>" />
											<input type="hidden" name="jform[view]" value="<?php echo $item->view; ?>" />
											<input type="hidden" name="jform[monitoring]" value="<?php echo $item->monitoring; ?>" />
											<input type="hidden" name="jform[predefined]" value="<?php echo $item->predefined; ?>" />
											<input type="hidden" name="jform[versionning]" value="<?php echo $item->versionning; ?>" />
											<input type="hidden" name="jform[access]" value="<?php echo $item->access; ?>" />
											<input type="hidden" name="option" value="com_easysdi_core" />
											<input type="hidden" name="task" value="resourcetype.remove" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
								?>
							</li>
						<?php endif; ?>

<?php endforeach; ?>
        <?php
        if (!$show):
            echo JText::_('COM_EASYSDI_CORE_NO_ITEMS');
        endif;
        ?>
    </ul>
</div>
<?php if ($show): ?>
    <div class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif; ?>


									<?php if(JFactory::getUser()->authorise('core.create','com_easysdi_core')): ?><a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resourcetype.edit&id=0'); ?>"><?php echo JText::_("COM_EASYSDI_CORE_ADD_ITEM"); ?></a>
	<?php endif; ?>