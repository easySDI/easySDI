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

JText::script('COM_EASYSDI_CORE_RESOURCES_SYNCHRONIZE_BY');
JText::script('COM_EASYSDI_CORE_RESOURCES_SYNCHRONIZE_THE');
JText::script('COM_EASYSDI_CORE_UNPUBLISHED_CHILDREN');

$document = JFactory::getDocument();
$document->addScript('administrator/components/com_easysdi_core/libraries/easysdi/catalog/resources.js');
$document->addStyleSheet('components/com_easysdi_core/assets/css/resources.css');
?>
<style> 
    .tooltip{
        width: 250px;
    }
    
    .tooltip-inner {
        white-space:pre-wrap;
    }
    
</style>
<div class="core front-end-edit">
    <?php if (!empty($this->parent)): ?>
        <h1><?php echo $this->parent->name; ?>: <?php echo $this->parent->version_name; ?></h1>
    <?php else : ?>
        <h1><?php echo JText::_('COM_EASYSDI_CORE_TITLE_RESOURCES'); ?></h1>
    <?php endif; ?>

    <?php
    if (isset($this->user)):
        if ($this->user->isResourceManager()):
            $resourcetypes = $this->user->getResourceType();
            ?>
            <?php // echo $this->itemmap->_item->text; ?>
            <div class="well">
                <div class="row-fluid">
                    <form class="form-search" action="" method="post">

                        <div class="btn-group pull-left">
                            <?php if (empty($this->parent)) : ?>
                                <a class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="icon-white icon-plus-sign"></i> <?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_NEW'); ?>
                                    <span class="caret"></span>
                                </a>
                            <?php else: ?>
                                 <a class="btn btn-success dropdown-toggle" href="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=resources'); ?>">
                                    <i class="icon-white icon-plus-sign"></i> <?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_BACK'); ?>
                                </a>
                            <?php endif; ?>
                            <ul class="dropdown-menu">
                                <?php foreach ($resourcetypes as $resourcetype): ?>
                                    <li>
                                        <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resource.edit&id=0&resourcetype=' . $resourcetype->id); ?>">
                                            <?php echo $resourcetype->label; ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="btn-group pull-right">

                            <div id="filtertype" >
                                <select id="filter_resourcetype<?php if (!empty($this->parent)): ?>_children<?php endif;?>" name="filter_resourcetype<?php if (!empty($this->parent)): ?>_children<?php endif;?>" onchange="this.form.submit();" class="inputbox">
                                    <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_RESOURCE_TYPE_FILTER'); ?></option>
                                    <?php foreach ($resourcetypes as $resourcetype): ?>
                                        <option value="<?php echo $resourcetype->id; ?>" <?php
                                        $filterName = (!empty($this->parent)) ? 'filter.resourcetype.children' : 'filter.resourcetype';
                                        if ($this->state->get($filterName) == $resourcetype->id) : echo 'selected="selected"';
                                        endif;
                                        ?> ><?php echo $resourcetype->label; ?></option>
                                            <?php endforeach; ?>
                                </select>


                            </div>
                            <div id="filterstatus">

                                <select id="filter_status<?php if (!empty($this->parent)): ?>_children<?php endif;?>" name="filter_status<?php if (!empty($this->parent)): ?>_children<?php endif;?>" onchange="this.form.submit();" class="inputbox">
                                    <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_METADATA_STATE_FILTER'); ?></option>
                                    <?php foreach ($this->metadatastates as $status): ?>
                                        <option value="<?php echo $status->id; ?>" <?php
                                        $filterName = (!empty($this->parent)) ? 'filter.status.children' : 'filter.status';
                                        if ($this->state->get($filterName) == $status->id) : echo 'selected="selected"';
                                        endif;
                                        ?> >
                                            <?php echo JText::_($status->value); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div id="filtersearch" >
                                <div class="filter-search">
                                    <label for="filter_search<?php if (!empty($this->parent)): ?>_children<?php endif;?>" class="element-invisible">Rechercher</label>
                                    <input type="text" name="filter_search<?php if (!empty($this->parent)): ?>_children<?php endif;?>" id="filter_search<?php if (!empty($this->parent)): ?>_children<?php endif;?>" placeholder="<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SEARCH_FILTER'); ?>" value="<?php $filterName = (!empty($this->parent)) ? 'filter.search.children' : 'filter.search'; echo $this->state->get($filterName); ?>" title="<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SEARCH_FILTER'); ?>" />

                                    <button class="btn hasTooltip" type="submit" title="Rechercher"><i class="icon-search"></i></button>
                                    <button class="btn hasTooltip" type="button" title="Effacer" id="search-reset"><i class="icon-remove"></i></button>
                                </div>
                            </div>

                        </div>
                    </form>

                </div>


            </div>
            <?php
        endif;
    endif;
    ?>

    <div class="items">
        <div class="well">
            <div class="row-fluid">
                <?php $show = count($this->items); ?>
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
                            <tr>
                                <?php if ($this->user->authorize($item->id, sdiUser::resourcemanager)): ?>
                                    <td>
                                        <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resource.edit&id=' . (int) $item->id); ?>"><?php echo $item->name; ?></a>
                                    </td>
                                <?php else : ?>
                                    <td>
                                        <?php echo $item->name; ?>                                       
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <?php echo $item->resourcetype_name; ?>
                                </td>
                                <td>
                                    <?php if ($item->versioning) : ?>

                                        <select id="<?php echo $item->id; ?>_select" onchange="onVersionChange(<?php echo $item->id; ?>)" class="inputbox version-status">
                                            <?php foreach ($item->metadata as $key => $value) { ?>
                                                <option value="<?php echo $value->id; ?>" rel="<?php echo $value->version; ?>"><?php echo $value->name; ?> : <?php echo JText::_($value->value); ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php else : ?>
                                        <?php if ($item->metadata[0]->state == 1) : ?>
                                            <span class="label label-warning"><?php echo JText::_($item->metadata[0]->value); ?></span>
                                        <?php elseif ($item->metadata[0]->state == 2): ?>
                                            <span class="label label-info"><?php echo JText::_($item->metadata[0]->value); ?></span>
                                        <?php elseif ($item->metadata[0]->state == 3): ?>
                                            <span class="label label-success"><?php echo JText::_($item->metadata[0]->value); ?></span>
                                        <?php elseif ($item->metadata[0]->state == 4): ?>
                                            <span class="label label-inverse"><?php echo JText::_($item->metadata[0]->value); ?></span>
                                        <?php elseif ($item->metadata[0]->state == 5): ?>
                                            <span class="label label-info"><?php echo JText::_($item->metadata[0]->value); ?></span>
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
                                        
                                        <?php
                                        /**
                                         * building the dropdown accross an array of arrays solves divider defect
                                         */
                                        $dropdown = array();
                                        
                                        /* FIRST SECTION */
                                        $section = array(
                                            "<li><a class='{$item->id}_linker modal' rel='{handler:\"iframe\",size:{x:600,y:700}}' href='".JRoute::_('index.php?option=com_easysdi_catalog&tmpl=component&view=sheet&preview=editor&id=' . $item->metadata[0]->id)."'>".JText::_('COM_EASYSDI_CORE_RESOURCES_VIEW_METADATA')."</a></li>"
                                        );
                                        
                                        if ($this->user->authorize($item->id, sdiUser::metadataeditor))
                                            array_push($section, 
                                                "<li><a class='{$item->id}_linker' href='".JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit&id=' . $item->metadata[0]->id)."'>".JText::_('COM_EASYSDI_CORE_RESOURCES_EDIT_METADATA')."</a></li>"
                                            );
                                        
                                        if ($this->user->authorize($item->id, sdiUser::metadataresponsible)){
                                            if ($item->metadata[0]->state == sdiMetadata::VALIDATED)
                                                array_push($section, 
                                                    "<li><a class='{$item->id}_linker' id='{$item->metadata[0]->id}_publish_linker' href=''>".JText::_('COM_EASYSDI_CORE_RESOURCES_PUBLISH_METADATA')."</a></li>"
                                                );
                                            
                                            if ($item->metadata[0]->state == sdiMetadata::PUBLISHED)
                                                array_push($section, 
                                                    "<li><a class='{$item->id}_linker' href='".JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.inprogress&id=' . $item->metadata[0]->id)."'>".JText::_('COM_EASYSDI_CORE_INPROGRESS_ITEM')."</a></li>",
                                                    "<li><a class='{$item->id}_modaler' href='#' onclick='showPublishModal({$item->metadata[0]->id}, \"{$item->metadata[0]->published}\");return false;'>".JText::_('COM_EASYSDI_CORE_RESOURCES_CHANGEPUBLISHEDDATE_METADATA')."</a></li>",
                                                    "<li><a class='{$item->id}_linker' href='".JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.archive&id=' . $item->metadata[0]->id)."'>".JText::_('COM_EASYSDI_CORE_RESOURCES_ARCHIVE_METADATA')."</a></li>"
                                                );
                                            
                                            if ($item->metadata[0]->state == sdiMetadata::ARCHIVED)
                                                array_push($section, 
                                                    "<li><a class='{$item->id}_linker' href='".JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.inprogress&id=' . $item->metadata[0]->id)."'>".JText::_('COM_EASYSDI_CORE_INPROGRESS_ITEM')."</a></li>"
                                                );
                                        }
                                        
                                        array_push($dropdown, $section);
                                        
                                        /* SECOND SECTION - optional */
                                        if ($item->metadata[0]->state == sdiMetadata::INPROGRESS && $this->user->authorize($item->id, sdiUser::metadataeditor))
                                            array_push($dropdown, array(
                                                "<li><a class='{$item->id}_modaler' href='#' onclick='showAssignmentModal(\"{$item->metadata[0]->version}\");return false;'>".JText::_('COM_EASYSDI_CORE_RESOURCES_ASSIGN_METADATA')."</a></li>"
                                                //, "<li><a class='{$item->id}_linker' href='".JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.notify&id=' . $item->metadata[0]->id)."'>".JText::_('COM_EASYSDI_CORE_RESOURCES_NOTIFY_METADATA')."</a></li>"
                                            ));
                                        
                                        /* THIRD SECTION - optional */
                                        if ($this->user->authorize($item->id, sdiUser::metadataresponsible) && $item->supportrelation)
                                            array_push($dropdown, array(
                                                "<li><a class='{$item->id}_sync_linker' href='".JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.synchronize&id=' . $item->metadata[0]->id)."'>".JText::_('COM_EASYSDI_CORE_RESOURCES_SYNCHRONIZE_METADATA')."</a></li>"
                                            ));
                                        
                                        ?>
                                        
                                        <ul class="dropdown-menu">
                                            <?php
                                            foreach($dropdown as $iSection => $section){
                                                if($iSection>0)
                                                    echo "<li class='divider'></li>";
                                                foreach($section as $manageLink)
                                                    echo $manageLink;
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </td>
                                <!-- Manage -->
                                <td>
                                    <div class="btn-group">
                                        <a class="btn btn-primary btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                            <?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_MANAGE'); ?>
                                            <span class="caret"></span>
                                        </a>
                                        
                                        <?php
                                        /**
                                         * building the dropdown accross an array of arrays solves divider defect
                                         */
                                        $dropdown = array();
                                        
                                        /* FIRST SECTION - optional */
                                        if($this->user->authorize($item->id, sdiUser::resourcemanager)){
                                            $section = array();
                                            
                                            if($item->versioning)
                                                array_push($section, "<li><a id='{$item->id}_new_linker' href=''>".JText::_('COM_EASYSDI_CORE_RESOURCES_NEW_VERSION')."</a></li>");
                                            
                                            if($item->supportrelation || $item->canbechild)
                                                array_push($section,
                                                    "<li><a class='{$item->id}_linker' href='".JRoute::_("index.php?option=com_easysdi_core&task=version.edit&id={$item->metadata[0]->version}")."'>".JText::_('COM_EASYSDI_CORE_RESOURCES_RELATIONS')."</a></li>"
                                                    );
                                            
                                            if($item->supportrelation)
                                                array_push($section,
                                                    "<li class='child_list' id='{$item->id}_child_list'><a id='{$item->id}_child_linker' href='".JRoute::_("index.php?option=com_easysdi_core&view=resources&parentid={$item->metadata[0]->version}")."'>".JText::_('COM_EASYSDI_CORE_RESOURCES_CHILDREN_LIST')." (<span id='{$item->metadata[0]->version}_child_num'>0</span>)</a></li>"
                                                    );
                                            
                                            if($item->supportapplication)
                                                array_push($section, "<li><a href='index.php?option=com_easysdi_core&view=applications&resource={$item->id}'>".JText::_('COM_EASYSDI_CORE_RESOURCES_APPLICATIONS')."</a></li>");
                                        
                                            
                                            array_push($dropdown, $section);
                                            $section = false;
                                        }
                                        
                                        /* SECOND SECTION - optional */
                                        if($this->user->authorize($item->id, sdiUser::diffusionmanager) && $item->supportdiffusion)
                                            array_push($dropdown, array(
                                                "<li><a class='{$item->id}_linker' href='".JRoute::_("index.php?option=com_easysdi_shop&task=diffusion.edit&id={$item->metadata[0]->id}")."'>".JText::_('COM_EASYSDI_CORE_RESOURCES_DIFFUSION')."</a></li>"
                                            ));
                                        
                                        /* THIRD SECTION - optional */
                                        if($this->user->authorize($item->id, sdiUser::viewmanager) && $item->supportview)
                                            array_push($dropdown, array(
                                                "<li><a class='{$item->id}_linker' href='".JRoute::_("index.php?option=com_easysdi_map&task=visualization.edit&id={$item->metadata[0]->id}")."'>".JText::_('COM_EASYSDI_CORE_RESOURCES_VIEW')."</a></li>"
                                            ));
                                        
                                        /* FOURTH SECTION - optional */
                                        if($this->user->authorize($item->id, sdiUser::resourcemanager)){
                                            if($item->versioning)
                                                array_push($dropdown, array(
                                                    "<li><a href='#' onclick='showDeleteModal(\"".JRoute::_("index.php?option=com_easysdi_core&task=version.remove&id={$item->metadata[0]->version}")."\", {$item->metadata[0]->version});return false;'><i class='icon-remove'></i> ".JText::_(count($item->metadata)>1?'COM_EASYSDI_CORE_RESOURCES_DELETE_VERSION':'COM_EASYSDI_CORE_RESOURCES_DELETE_RESOURCE')."</a></li>"
                                                ));
                                            else
                                                array_push($dropdown, array(
                                                    "<li><a href='#' onclick='showDeleteModal(\"".JRoute::_("index.php?option=com_easysdi_core&task=version.remove&id={$item->metadata[0]->version}")."\", {$item->metadata[0]->version});return false;'><i class='icon-remove'></i> ".JText::_('COM_EASYSDI_CORE_RESOURCES_DELETE_RESOURCE')."</a></li>"
                                                ));
                                        }
                                        
                                        /* FIFTH SECTION - optional */
                                        if($this->user->authorize($item->id, sdiUser::metadataeditor))
                                            array_push($dropdown, array(
                                                "<li><a href='".JRoute::_("index.php?option=com_easysdi_catalog&view=assignments&version={$item->metadata[0]->version}&limitstart=".JFactory::getApplication()->input->getInt('start', 0, 'int'))."'>".JText::_('COM_EASYSDI_CORE_RESOURCES_ASSIGNMENT_HISTORY')."</a></li>"
                                            ));
                                        ?>
                                        
                                        <ul class="dropdown-menu">
                                            <?php
                                            foreach($dropdown as $iSection => $section){
                                                if($iSection>0)
                                                    echo "<li class='divider'></li>";
                                                foreach($section as $manageLink)
                                                    echo $manageLink;
                                            }
                                            ?>
                                        </ul>
                                    </div>
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
    </div>
    <?php if ($show): ?>
        <div class="pagination">
            <p class="counter">
                <?php echo $this->pagination->getPagesCounter(); ?>
            </p>
            <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
    <?php endif; ?>
</div>
<style>
    div.modal.fade{top:-100%}
</style>
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
                                <input type="text" name="published" id="published" value="" class="required" aria-required="true" required="required" aria-invalid="false"><button class="btn" id="published_img" ><i class="icon-calendar"></i></button>
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

<!-- Create new version modal -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo JText::_('COM_EASYSDI_CORE_ADD_ITEM_MODAL_TITLE'); ?></h4>
            </div>
            <div id="createModalBody" class="modal-body">
                <b><?php echo JText::_('COM_EASYSDI_CORE_ADD_ITEM_MODAL_BODY'); ?></b>
                <span id="createModalChildrenList"></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Delete modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo JText::_('COM_EASYSDI_CORE_DELETE_ITEM'); ?></h4>
            </div>
            <div id="deleteModalBody" class="modal-body">
                <?php echo JText::_('COM_EAYSDI_CORE_DELETE_CONFIRM'); ?>
                <span id="deleteModalChildrenList"></span>
            </div>
            <div class="modal-footer">
                <a href="#" id="btn_delete"><button type="button" class="btn btn-danger"><?php echo JText::_('COM_EASYSDI_CORE_DELETE_ITEM'); ?></button></a>
                <button type="button" class="btn btn-success" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Modal -->
<div class="modal fade" id="assignmentModal" tabindex="-1" role="dialog" aria-labelledby="assignmentModalLabel" aria-hidden="true">
    <form id="form_assign" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.assign'); ?>" method="post" class="form-validate form-horizontal">
        <input type="hidden" id="id" name="id" value=""/>
        <input type="hidden" id="task" name="task" value="metadata.assign"/>
        <input type="hidden" id="assigned_by" name="assigned_by" value="<?php echo $this->user->id; ?>"/>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="assignModalLabel"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCE_ASSIGNMENT'); ?></h4>
                </div>
                <div class="modal-body">

                    <div class="control-group">
                        <!-- Assign To field -->
                        <div class="control-label">
                            <label id="assign_to-lbl" for="assigned_to" class="" aria-invalid="false"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCE_ASSIGN_TO'); ?></label>
                        </div>
                        <div class="controls">
                            <div class="input-append">
                                <select id="assigned_to" name="assigned_to"></select>
                            </div>
                        </div>
                        <!-- Assign Message field -->
                        <div class="control-label">
                            <label id="assign_msg-lbl" for="assign_msg" class="" aria-invalid="false"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCE_ASSIGN_MESSAGE'); ?></label>
                        </div>
                        <div class="controls">
                            <div class="input-append">
                                <textarea cols="150" rows="15" id="assign_msg" name="assign_msg">Type a message...</textarea>
                            </div>
                        </div>
                        <!-- Child Checkbox field -->
                        <div id="assign_child_controls">
                            <div class="control-label">
                                <label id="assign_child-lbl" for="assign_child" class="" aria-invalid="false"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCE_ASSIGN_CHILD'); ?></label>
                            </div>
                            <div class="controls">
                                <div class="input-append">
                                    <input type="checkbox" id="assign_child" name="assign_child" value="1">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" ><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_ASSIGN_METADATA'); ?></button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>

