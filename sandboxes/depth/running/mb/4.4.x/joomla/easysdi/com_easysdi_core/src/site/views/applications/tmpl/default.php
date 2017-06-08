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
<div class="well">
    <form class="form-inline" action="index.php" method="post" id="adminForm" name="adminForm">
        <?php echo $this->getToolbar(); ?>
        <input type = "hidden" name = "task" value = "" />
        <input type = "hidden" name = "option" value = "com_easysdi_core" />
    </form>
</div>
<div class="clr"></div>
<div class="items">
    <div class="well">
        <?php $show = false; ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo JText::_('COM_EASYSDI_CORE_APPLICATIONS_NAME'); ?></th>
                    <th><?php echo JText::_('COM_EASYSDI_CORE_APPLICATIONS_DESCRIPTION'); ?></th>
                    <th><?php echo JText::_('COM_EASYSDI_CORE_APPLICATIONS_URL'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tfoot>

            </tfoot>
            <tbody>
                <?php foreach ($this->items as $item) : ?>
                    <?php $show = true; ?>
                    <tr>
                        <td>
                            <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=application.edit&id=' . (int) $item->id); ?>"><?php echo $item->name; ?></a>
                        </td>
                        <td>
                            <?php echo $item->description; ?>
                        </td>
                        <td>
                            <?php echo $item->url; ?>
                        </td>
                        <td>
                            <a href="javascript:document.getElementById('form-application-delete-<?php echo $item->id; ?>').submit()" class="btn btn-danger btn-mini"><i class="icon-white icon-remove"></i></a>
                            <form id="form-application-delete-<?php echo $item->id ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=application.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
                                <input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
                                <input type="hidden" name="option" value="com_easysdi_core" />
                                <input type="hidden" name="task" value="application.remove" />
                                <?php echo JHtml::_('form.token'); ?>
                            </form>
                        </td>
                    </tr>
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


