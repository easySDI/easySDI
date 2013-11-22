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

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/sdimetadata.php';

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.modal');
JHtml::_('behavior.calendar');

$document = JFactory::getDocument();

$document->addScript('administrator/components/com_easysdi_core/libraries/easysdi/catalog/resources.js');
?>
<?php
if (isset($this->user)):
    if ($this->user->isResourceManager()):
        $resourcetypes = $this->user->getResourceType();
        ?>
        <?php // echo $this->itemmap->_item->text; ?>
        <div class="well">
            <div class="row-fluid">
                <form class="form-search" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=resources'); ?>" method="post">
                    <div class="btn-toolbar">
                        <div class="btn-group pull-right">
                            <select id="filter_resourcetype" name="filter_resourcetype" onchange="this.form.submit();" class="inputbox">
                                <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_RESOURCE_TYPE_FILTER'); ?></option>
                                <?php foreach ($resourcetypes as $resourcetype): ?>
                                    <option value="<?php echo $resourcetype->id; ?>" <?php
                                    if ($this->state->get('filter.resourcetype') == $resourcetype->id) : echo 'selected="selected"';
                                    endif;
                                    ?> ><?php echo $resourcetype->label; ?></option>
                                        <?php endforeach; ?>
                            </select>

                            <?php
                            //Load all status value
                            $db = JFactory::getDbo();
                            $query = $db->getQuery(true)
                                    ->select('s.value, s.id ')
                                    ->from('#__sdi_sys_metadatastate s');
                            $db->setQuery($query);
                            $metadatastate = $db->loadObjectList();
                            ?>


                            <select id="filter_status" name="filter_status" onchange="this.form.submit();" class="inputbox">
                                <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_METADATA_STATE_FILTER'); ?></option>
                                <?php foreach ($metadatastate as $status): ?>
                                    <option value="<?php echo $status->id; ?>" <?php if ($this->state->get('filter.status') == $status->id) : echo 'selected="selected"';
                        endif; ?> >
                                    <?php echo JText::_($status->value); ?></option>
        <?php endforeach; ?>
                            </select>

                            <div class=" pull-right">
                                <button class="btn hasTooltip" type="submit" title="Rechercher"><i class="icon-search"></i></button>
                                <button class="btn hasTooltip" type="button" title="Effacer" onclick="document.id('filter_search').value = '';
                                            this.form.submit();"><i class="icon-remove"></i></button>
                            </div>
                            <div class="filter-search  pull-right">
                                <label for="filter_search" class="element-invisible">Rechercher</label>
                                <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SEARCH_FILTER'); ?>" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SEARCH_FILTER'); ?>" />
                            </div>

                        </div>
                        <div class="btn-toolbar">
                            <div class="btn-group ">
                                <a class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="icon-white icon-plus-sign"></i> <?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_NEW'); ?>
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
        <?php foreach ($resourcetypes as $resourcetype): ?>
                                        <li>
                                            <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resource.edit&id=0&resourcetype=' . $resourcetype->id); ?>">
                                        <?php echo $resourcetype->label; ?></a>
                                        </li>
        <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                </form>
            </div>
        </div>
        </div>
        <?php
    endif;
endif;
?>

<div class="items">
    <div class="well">
        <div class="row-fluid">
<?php $show = false; ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_NAME'); ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_RESOURCETYPE'); ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_STATE'); ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_ACTIONS'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                </tfoot>
                <tbody>
                    <?php foreach ($this->items as $item) : ?>
                        <?php
                        //Load versions
                        $db = JFactory::getDbo();
                        $filter_status = $this->state->get('filter.status');
                        if (!empty($filter_status)):
                            $query = $db->getQuery(true)
                                    ->select('m.id, v.name, s.value, s.id AS state, v.id as version')
                                    ->from('#__sdi_version v')
                                    ->innerJoin('#__sdi_metadata m ON m.version_id = v.id')
                                    ->innerJoin('#__sdi_sys_metadatastate s ON s.id = m.metadatastate_id')
                                    ->where('v.resource_id = ' . $item->id)
                                    ->where('m.metadatastate_id = ' . $filter_status)
                                    ->order('v.name DESC');
                        else :
                            $query = $db->getQuery(true)
                                    ->select('m.id, v.name, s.value, s.id AS state, v.id as version')
                                    ->from('#__sdi_version v')
                                    ->innerJoin('#__sdi_metadata m ON m.version_id = v.id')
                                    ->innerJoin('#__sdi_sys_metadatastate s ON s.id = m.metadatastate_id')
                                    ->where('v.resource_id = ' . $item->id)
                                    ->order('v.name DESC');
                        endif;
                        $db->setQuery($query);
                        $metadata = $db->loadObjectList();
                        
                        $s = $query->__toString();

                        //If no version are visible according to the filter
                        if (count($metadata) == 0)
                            continue;

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
        <?php if ($item->versioning) : ?>

                                        <select id="<?php echo $item->id; ?>_select" onchange="onVersionChange(<?php echo $item->id; ?>)" class="inputbox">
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
                                    <!-- Actions on metadata -->
                                    <div class="btn-group">
                                        <a class="btn btn-success btn-small dropdown-toggle" data-toggle="dropdown" href="#">
        <?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_METADATA'); ?>
                                            <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu">
        <?php if ($this->user->authorize($item->id, sdiUser::metadataeditor) || $this->user->authorize($item->id, sdiUser::metadataresponsible)): ?>
                                                <li>
                                                    <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_EDIT_METADATA'); ?></a>
                                                </li>
                                                <li>
                                                    <a class="<?php echo $item->id; ?>_linker modal" rel="{handler:'iframe',size:{x:600,y:700}}" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&tmpl=component&view=sheet&preview=editor&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_VIEW_METADATA'); ?></a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if ($this->user->authorize($item->id, sdiUser::metadataresponsible)): ?>

            <?php if ($metadata[0]->state == sdiMetadata::VALIDATED): ?>
                                                    <li>
                                                        <a class="<?php echo $item->id; ?>_linker" href="#" onclick="showModal('<?php echo $item->id; ?>')"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_PUBLISH_METADATA'); ?></a>
                                                    </li>
                                                <?php endif; ?>
            <?php if ($metadata[0]->state == sdiMetadata::PUBLISHED): ?>
                                                    <li>
                                                        <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.inprogress&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_INPROGRESS_ITEM'); ?></a>
                                                    </li>
                                                    <li>
                                                        <a class="<?php echo $item->id; ?>_linker" href="#" onclick="showModal('<?php echo $item->id; ?>')"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_CHANGEPUBLISHEDDATE_METADATA'); ?></a>
                                                    </li>
                                                    <li>
                                                        <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.archive&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_ARCHIVE_METADATA'); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if ($metadata[0]->state == sdiMetadata::ARCHIVED): ?>
                                                    <li>
                                                        <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.inprogress&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_INPROGRESS_ITEM'); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php /* if ($this->user->authorize($item->id, sdiUser::metadataeditor)): ?>
                                              <li class="divider"></li>
                                              <li>
                                              <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.assign&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_ASSIGN_METADATA'); ?></a>
                                              </li>
                                              <li>
                                              <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.notify&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_NOTIFY_METADATA'); ?></a>
                                              </li>
                                              <?php endif; */ ?>
                                            <?php /* if ($this->user->authorize($item->id, sdiUser::metadataresponsible)): ?>
                                              <li class="divider"></li>
                                              <li>
                                              <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.synchronize&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SYNCHRONIZE_METADATA'); ?></a>
                                              </li>
                                              <?php endif; */ ?>
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a class="btn btn-primary btn-small dropdown-toggle" data-toggle="dropdown" href="#">
        <?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_MANAGE'); ?>
                                            <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu">
        <?php if ($this->user->authorize($item->id, sdiUser::resourcemanager) && $item->versioning): ?>
                                                <li>
                                                    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.create&resource='. $item->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_NEW_VERSION'); ?></a>
                                                </li>
                                                <?php endif; ?>
                                                 <?php if ($this->user->authorize($item->id, sdiUser::resourcemanager)): ?>
                                                <li>
                                                    <a class="<?php echo $item->id; ?>_linker" href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.edit&id=' . $metadata[0]->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_RELATIONS'); ?></a>
                                                </li>               
                                            <?php endif; ?>
        <?php if ($this->user->authorize($item->id, sdiUser::resourcemanager)): ?>
                                                <li>
                                                    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=applications&resource=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_APPLICATIONS'); ?></a>
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
                                                        <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.remove&id=' . $metadata[0]->version); ?>"><i class="icon-remove"></i> <?php
                                                            if (count($metadata) > 1)
                                                                echo JText::_('COM_EASYSDI_CORE_RESOURCES_DELETE_VERSION');
                                                            else
                                                                echo JText::_('COM_EASYSDI_CORE_RESOURCES_DELETE_RESOURCE');
                                                            ?></a>
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
</div>
<?php if ($show): ?>
    <div class="pagination">
        <p class="counter">
        <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
    <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif; ?>

<!-- Publish Modal -->
<div class="modal fade" id="publishModal" tabindex="-1" role="dialog" aria-labelledby="publishModalLabel" aria-hidden="true">
     <form id="form_publish" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.publish'); ?>" method="post" class="form-validate form-horizontal">
    <input type="hidden" id="id" name="id" value=""/>
    <input type="hidden" id="task" name="task" value="metadata.publish"/>
         <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="publishModalLabel"><?php echo JText::_('COM_EASYSDI_CORE_PUBLISH_DATE'); ?></h4>
            </div>
            <div class="modal-body">
                    
                    <div class="control-group">
                        <div class="control-label"><label id="publish_date-lbl" for="publish_date" class="" aria-invalid="false"><?php echo JText::_('COM_EASYSDI_CORE_PUBLISH_DATE'); ?></label></div>
                        <div class="controls"><div class="input-append">
                                <input type="text" name="published" id="published" value="" class="required" aria-required="true" required="required" aria-invalid="false"><button class="btn" id="published_img"><i class="icon-calendar"></i></button>
                            </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" ><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_PUBLISH_METADATA'); ?></button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
            </div>
        </div>
    </div>
    </form>
</div>

