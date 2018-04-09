<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
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
						$show = true;
						?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=versionrelation&id=' . (int)$item->id); ?>"><?php echo $item->id; ?></a>
								<?php
									if(JFactory::getUser()->authorise('core.edit.state','com_easysdi_core.versionrelation.'.$item->id)):
									?>
										<a href="javascript:document.getElementById('form-versionrelation-state-<?php echo $item->id; ?>').submit()"><?php if($item->state == 1): echo JText::_("COM_EASYSDI_CORE_UNPUBLISH_ITEM"); else: echo JText::_("COM_EASYSDI_CORE_PUBLISH_ITEM"); endif; ?></a>
										<form id="form-versionrelation-state-<?php echo $item->id ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=versionrelation.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="jform[parent_id]" value="<?php echo $item->parent_id; ?>" />
											<input type="hidden" name="jform[child_id]" value="<?php echo $item->child_id; ?>" />
											<input type="hidden" name="option" value="com_easysdi_core" />
											<input type="hidden" name="task" value="versionrelation.save" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
									if(JFactory::getUser()->authorise('core.delete','com_easysdi_core.versionrelation.'.$item->id)):
									?>
										<a href="javascript:document.getElementById('form-versionrelation-delete-<?php echo $item->id; ?>').submit()"><?php echo JText::_("COM_EASYSDI_CORE_DELETE_ITEM"); ?></a>
										<form id="form-versionrelation-delete-<?php echo $item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=versionrelation.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="jform[parent_id]" value="<?php echo $item->parent_id; ?>" />
											<input type="hidden" name="jform[child_id]" value="<?php echo $item->child_id; ?>" />
											<input type="hidden" name="option" value="com_easysdi_core" />
											<input type="hidden" name="task" value="versionrelation.remove" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
								?>
							</li>

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


									<?php if(JFactory::getUser()->authorise('core.create','com_easysdi_core')): ?><a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=versionrelation.edit&id=0'); ?>"><?php echo JText::_("COM_EASYSDI_CORE_ADD_ITEM"); ?></a>
	<?php endif; ?>