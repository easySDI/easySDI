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

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/sdimetadata.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/resources.js.php';

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.modal');
JHtml::_('behavior.calendar');

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_core/assets/css/resources.css?v=' . sdiFactory::getSdiFullVersion());
?>
<style>
    .tooltip{
        width: 250px;
    }

    .tooltip-inner {
        white-space:pre-wrap;
    }

</style>
<script type="text/javascript">
    var baseUrl = "<?php echo JUri::base(); ?>index.php?";
</script>
<div class="core front-end-edit">
    <?php if (!empty($this->parent)): ?>
        <h1><?php echo $this->parent->name; ?>: <?php echo $this->parent->version_name; ?></h1>
    <?php else : ?>
        <h1><?php echo JText::_('COM_EASYSDI_CORE_TITLE_RESOURCES'); ?></h1>
    <?php endif; ?>

    <?php
    if (isset($this->user)):
        $resourcetypes = $this->user->getResourceType();
        ?>
        <div class="well sdi-searchcriteria">
            <div class="row-fluid">
                <form id='criterias' class="form-search" action="" method="post">
                    <input type='hidden' id='filter_ordering' name='filter_ordering' value='ASC'/>

                    <div class="btn-group pull-left">
                        <?php if (empty($this->parent) && $this->user->isResourceManager()) : ?>
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
                        <?php elseif (!empty($this->parent)): ?>
                            <a class="btn btn-success" href="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=resources'); ?>">
                                <i class="icon-white icon-plus-sign"></i> <?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_BACK'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="btn-group pull-right">
                        <?php if (count($this->userOrganisms) > 1): ?>
                            <div id="filterorganism" >
                                <select id="filter_userorganism" name="filter_userorganism" onchange="this.form.submit();" class="inputbox">
                                    <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_USER_ORGANISM_FILTER'); ?></option>
                                    <?php foreach ($this->userOrganisms as $userOrganism): ?>
                                        <option value="<?php echo $userOrganism->id; ?>" <?php
                                        $filterName = (!empty($this->parent)) ? 'filter.userorganism.children' : 'filter.userorganism';
                                        if ($this->state->get($filterName) == $userOrganism->id) : echo 'selected="selected"';
                                        endif;
                                        ?> ><?php echo $userOrganism->name; ?></option>
                                            <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div id="filtertype" >
                            <select id="filter_resourcetype<?php if (!empty($this->parent)): ?>_children<?php endif; ?>" name="filter_resourcetype<?php if (!empty($this->parent)): ?>_children<?php endif; ?>" onchange="this.form.submit();" class="inputbox">
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

                            <select id="filter_status<?php if (!empty($this->parent)): ?>_children<?php endif; ?>" name="filter_status<?php if (!empty($this->parent)): ?>_children<?php endif; ?>" onchange="this.form.submit();" class="inputbox">
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
                                <label for="filter_search<?php if (!empty($this->parent)): ?>_children<?php endif; ?>" class="element-invisible">Rechercher</label>
                                <input type="text" name="filter_search<?php if (!empty($this->parent)): ?>_children<?php endif; ?>" id="filter_search<?php if (!empty($this->parent)): ?>_children<?php endif; ?>" placeholder="<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SEARCH_FILTER'); ?>" value="<?php
                                $filterName = (!empty($this->parent)) ? 'filter.search.children' : 'filter.search';
                                echo $this->state->get($filterName);
                                ?>" title="<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SEARCH_FILTER'); ?>" />

                                <button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                                <button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" id="search-reset"><i class="icon-remove"></i></button>
                            </div>
                        </div>

                    </div>
                </form>

            </div>


        </div>
        <?php
    endif;
    ?>

    <div class="items">
        <div class="well">
            <div class="row-fluid">
                <?php
                $show = count($this->items);
                if ($show):
                    ?>
                    <table id="resources" class="table table-striped">
                        <thead>
                            <tr>
                                <th id="resources_name"  class="resource_name"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_NAME'); ?><span id='resources_ordering'><?php echo $this->state->get('filter.ordering'); ?></span></th>
                                <th class="resource_type"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_RESOURCETYPE'); ?></th>
                                <th class="resource_versions"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_STATE'); ?></th>
                                <th class="resource_metadata_actions"><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_ACTIONS'); ?></th>
                                <th class="resource_management_actions"></th>
                            </tr>
                        </thead>
                        <tfoot>
                        </tfoot>
                        <tbody>
                            <?php foreach ($this->items as $item) : ?>
                                <tr id="<?php echo $item->id; ?>_resource">
                                    <td id="<?php echo $item->id; ?>_resource_name" class="resource_name">

                                    </td>
                                    <td id="<?php echo $item->id; ?>_resource_type" class="resource_type">

                                    </td>
                                    <td id="<?php echo $item->id; ?>_resource_versions" class="resource_versions">

                                    </td>
                                    <td id="<?php echo $item->id; ?>_resource_metadata_actions" class="resource_metadata_actions">

                                    </td>
                                    <td id="<?php echo $item->id; ?>_resource_management_actions" class="resource_management_actions">

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php
                else:
                    echo JText::_('COM_EASYSDI_CORE_NO_ITEMS');
                endif;
                ?>
            </div>
        </div>
    </div>
    <?php if ($show): ?>
        <div class="pagination">
            <p class="resources_counter">
                <?php
                $resourceFrom = ($this->pagination->pagesCurrent - 1) * $this->pagination->limit + 1;
                $resourceTo = min($resourceFrom + $this->pagination->limit - 1, $this->pagination->total);

                echo JText::_('COM_EASYSDI_CORE_RESOURCES_RESULTS') . ' ' . $resourceFrom . ' ' . JText::_('COM_EASYSDI_CORE_RESOURCES_TO') . ' ' . $resourceTo . ' ' . JText::_('COM_EASYSDI_CORE_RESOURCES_OF') . ' ' . $this->pagination->total;
                ?>
            </p>
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
        <input type="hidden" id="viral" name="viral" value="0"/>
        <input type="hidden" id="redirectURL" name="redirectURL" value="index.php?option=com_easysdi_core&view=resources"/>
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
                    <?php echo JText::_('COM_EAYSDI_CORE_PUBLISH_CONFIRM'); ?>
                    <span id="publishModalCurrentMetadata"></span>
                    <div id="publishModalChildrenDiv" style="display:none">
                        <input type="checkbox" id="publishModalViralPublication"> <?php echo JText::_('COM_EAYSDI_CORE_PUBLISH_CHILDREN_CONFIRM'); ?>
                        <span id="publishModalChildrenList"></span>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
                    <button type="submit" class="btn btn-primary" ><?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_PUBLISH_METADATA'); ?></button>
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
                <button type="button" class="btn" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
                <a href="#" id="btn_delete"><button type="button" class="btn btn-danger"><?php echo JText::_('COM_EASYSDI_CORE_DELETE_ITEM'); ?></button></a>
            </div>
        </div>
    </div>
</div>

<!-- removeWithOrphan modal -->
<div class="modal fade" id="removeWithOrphanModal" tabindex="-1" role="dialog" aria-labelledby="removeWithOrphanModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo JText::_('COM_EASYSDI_CORE_DELETE_ITEM'); ?></h4>
            </div>
            <div id="removeWithOrphanModalBody" class="modal-body">
                <?php echo JText::_('COM_EAYSDI_CORE_DELETE_PROBLEM'); ?>
                <span id="missingMetadata"></span>
                <br>
                <?php echo JText::_('COM_EAYSDI_CORE_REMOVEWITHORPHAN_CONFIRM'); ?>
                <span id="removeWithOrphanModalChildrenList"></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" id="btn_removewithorphan_cancel" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
                <a href="#" id="btn_removewithorphan"><button type="button" class="btn btn-danger"><?php echo JText::_('COM_EASYSDI_CORE_REMOVEWITHORPHAN_ITEM'); ?></button></a>
            </div>
        </div>
    </div>
</div>

<!-- synchronize modal -->
<div class="modal fade" id="synchronizeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo JText::_('COM_EASYSDI_CORE_SYNCHRONIZE_TITLE'); ?></h4>
            </div>
            <div id="synchronizeModalBody" class="modal-body">
                <?php echo JText::_('COM_EASYSDI_CORE_SYNCHRONIZE_CONFIRM'); ?>
            </div>
            <div class="modal-footer">
                <a href="#" id="btn_synchronize"><button type="button" class="btn btn-success"><?php echo JText::_('COM_EASYSDI_CORE_SYNCHRONIZE_TITLE'); ?></button></a>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
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
                                <textarea cols="150" rows="15" id="assign_msg" name="assign_msg" placeholder="<?php echo JText::_('COM_EASYSDI_CORE_RESOURCE_ASSIGN_MESSAGE_PLACEHOLDER'); ?>"></textarea>
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

