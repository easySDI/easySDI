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
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function() {

    });
    function onVersionChange(resourceid) {
        js('.' + resourceid + '_linker').each(function() {
            var href = js(this).attr("href");
            var i = href.lastIndexOf("/");
            var newhref = href.substring(0, i + 1);
            js(this).attr("href", newhref + js("select#" + resourceid + "_select").val());
        });
    }
</script>
<?php
if (isset($this->user)):
    if ($this->user->isResourceManager()):
        $resourcetypes = $this->user->getResourceType();
        ?>
 <div class="well">
     <form class="form-inline">
        <div class="btn-group">
            <a class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="icon-white icon-plus-sign"></i> <?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_NEW');?>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <?php
                foreach ($resourcetypes as $resourcetype):
                    ?>
                    <li>
                        <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resource.edit&id=0&resourcetype=' . $resourcetype->id); ?>">
                            <?php echo $resourcetype->label; ?></a>
                    </li>
                    <?php
                endforeach;
                ?>
            </ul>
        </div>
 </form>
 </div>
        <?php
    endif;
endif;
?>

<div class="items">
    <div class="well">
        <?php $show = false; ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_NAME');?></th>
                    <th><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_RESOURCETYPE');?></th>
                    <th><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_STATE');?></th>
                    <th><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_ACTIONS');?></th>
                    <th></th>
                </tr>
            </thead>
            <tfoot>
            </tfoot>
            <tbody>
                <?php foreach ($this->items as $item) : ?>
                    <?php
                    if (is_array($this->user->authorize($item->id))): //User has some rights on this item 
                        $show = true;
                        ?>
                        <tr>
                            <?php if ($this->user->authorize($item->id, sdiUser::resourcemanager)): ?>
                                <td>
                                    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resource.edit&id=' . (int) $item->id); ?>"><?php echo $item->name; ?></a>
                                </td>
                            <?php else : ?>
                                <td>
                                    <?php echo $item->name; ?>
                                    <div class="small">
                                        <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <td>
                                <?php echo $item->resourcetype_name; ?>
                            </td>
                            <td>
                                <?php
                                //Load versions
                                $db = JFactory::getDbo();
                                $query = $db->getQuery(true)
                                        ->select('m.id, v.name, s.value, s.id AS state, v.id as version')
                                        ->from('#__sdi_version v')
                                        ->innerJoin('#__sdi_metadata m ON m.version_id = v.id')
                                        ->innerJoin('#__sdi_sys_metadatastate s ON s.id = m.metadatastate_id')
                                        ->where('v.resource_id = ' . $item->id)
                                        ->order('v.name DESC');
                                $db->setQuery($query);
                                $metadata = $db->loadObjectList();

                                if ($item->versioning) :
                                    ?>
                                    <select id="<?php echo $item->id; ?>_select" onchange="onVersionChange(<?php echo $item->id; ?>)">
                                        <?php foreach ($metadata as $key => $value) { ?>
                                            <option value="<?php echo $value->id; ?>"><?php echo $value->name; ?> : <?php echo JText::_($value->value); ?></option>
                                        <?php } ?>
                                    </select>
                                <?php else : ?>
                                    <?php if ($metadata[0]->state == 1) : ?>
                                        <span class="label label-warning"><?php echo JText::_($metadata[0]->value); ?></span>
                                    <?php elseif ($metadata[0]->state == 2): ?>
                                        <span class="label label-info"><?php echo JText::_($metadata[0]->value); ?></span>
                                    <?php elseif ($metadata[0]->state == 3): ?>
                                        <span class="label label-success"><?php echo JText::_($metadata[0]->value); ?></span>
                                    <?php elseif ($metadata[0]->state == 4): ?>
                                        <span class="label label-inverse"><?php echo JText::_($metadata[0]->value); ?></span>
                                    <?php elseif ($metadata[0]->state == 5): ?>
                                        <span class="label label-info"><?php echo JText::_($metadata[0]->value); ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-success btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                        <?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_METADATA');?>
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php if ($this->user->authorize($item->id, sdiUser::metadataeditor) || $this->user->authorize($item->id, sdiUser::metadataresponsible)): ?>
                                            <li>
                                                <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_EDIT_METADATA'); ?></a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($this->user->authorize($item->id, sdiUser::metadataresponsible)): ?>
                                            <li>
                                                <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.validate&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_VALIDATE_METADATA'); ?></a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($this->user->authorize($item->id, sdiUser::resourcemanager)): ?>
                                            <li>
                                                <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.publish&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_PUBLISH_METADATA'); ?></a>
                                            </li>
                                            <li>
                                                <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.publishdate&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_CHANGEPUBLISHEDDATE_METADATA'); ?></a>
                                            </li>                                                
                                            <li>
                                                <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.archive&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_ARCHIVE_METADATA'); ?></a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($this->user->authorize($item->id, sdiUser::metadataeditor) || $this->user->authorize($item->id, sdiUser::metadataresponsible)): ?>
                                            <li class="divider"></li>
                                            <li>
                                                <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.assign&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_ASSIGN_METADATA'); ?></a>
                                            </li>
                                            <li>
                                                <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.notify&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_NOTIFY_METADATA'); ?></a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($this->user->authorize($item->id, sdiUser::metadataresponsible)): ?>
                                            <li class="divider"></li>
                                            <li>
                                                <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.synchronize&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SYNCHRONIZE_METADATA'); ?></a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-primary btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                        <?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_MANAGE');?>
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php if ($this->user->authorize($item->id, sdiUser::resourcemanager) && $item->versioning): ?>
                                            <li>
                                                <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.edit'); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_NEW_VERSION'); ?></a>
                                            </li>
                                            <li>
                                                <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.editrelations&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_RELATIONS'); ?></a>
                                            </li>               
                                        <?php endif; ?>
                                        <?php if ($this->user->authorize($item->id, sdiUser::resourcemanager)): ?>
                                            <li>
                                                <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=applications&resource='. $item->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_APPLICATIONS'); ?></a>
                                            </li>
                                        <?php endif; ?>

                                        <?php if ($this->user->authorize($item->id, sdiUser::diffusionmanager)): ?>
                                            <li class="divider"></li>
                                            <li>
                                                <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.edit&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_DIFFUSION'); ?></a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($this->user->authorize($item->id, sdiUser::viewmanager)): ?>
                                            <li>
                                                <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=visualization.edit&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_VIEW'); ?></a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($this->user->authorize($item->id, sdiUser::resourcemanager)): ?>
                                            <li class="divider"></li>
                                            <li>
                                                <?php if ($item->versioning) : ?>
                                                    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.remove&id=' . $metadata[0]->version); ?>"><i class="icon-remove"></i> <?php if(count($metadata) > 1 ) echo JText::_('COM_EASYSDI_CORE_RESOURCES_DELETE_VERSION') ; else echo JText::_('COM_EASYSDI_CORE_RESOURCES_DELETE_RESOURCE'); ?></a>
                                                <?php else : ?>
                                                    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.remove&id=' . $metadata[0]->version); ?>"><i class="icon-remove"></i> <?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_DELETE_RESOURCE'); ?></a>
                                                <?php endif; ?>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>

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

