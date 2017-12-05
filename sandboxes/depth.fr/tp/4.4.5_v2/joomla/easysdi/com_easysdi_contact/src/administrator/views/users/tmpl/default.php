<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_contact/assets/css/easysdi_contact.css?v=' . sdiFactory::getSdiFullVersion());

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$archived = $this->state->get('filter.published') == 2 ? true : false;
$trashed = $this->state->get('filter.published') == -2 ? true : false;
$canOrder = $user->authorise('core.edit.state', 'com_easysdi_contact');
$saveOrder = $listOrder == 'a.ordering';
if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_easysdi_contact&task=users.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'userList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).on('click', '.btn-juser', function () {
        js('#userjoomlaaction').val(this.id);
        Joomla.submitform('users.delete');
    });

    Joomla.orderTable = function () {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
    Joomla.submitbutton = function (task)
    {
        if (task === 'users.delete') {
            js('#usermngmt').modal('show');
        }
        else {
            Joomla.submitform(task);
        }
    }

</script>

<?php
//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar)) {
    $this->sidebar .= $this->extra_sidebar;
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_contact&view=users'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty($this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
        <?php else : ?>
            <div id="j-main-container">
            <?php endif; ?>
            <div id="filter-bar" class="btn-toolbar">
                <div class="filter-search btn-group pull-left">
                    <label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER'); ?></label>
                    <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_EASYSDI_CONTACT_SEARCH_IN_USER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_EASYSDI_CONTACT_SEARCH_IN_USER'); ?>" />
                </div>
                <div class="btn-group pull-left hidden-phone">
                    <button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                    <button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value = '';
                            this.form.submit();"><i class="icon-remove"></i></button>
                </div>
                <div class="btn-group pull-right hidden-phone">
                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
                    <?php echo $this->pagination->getLimitBox(); ?>
                </div>
                <div class="btn-group pull-right hidden-phone">
                    <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
                    <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                        <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
                        <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
                        <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
                    </select>
                </div>
                <div class="btn-group pull-right">
                    <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
                    <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                        <option value=""><?php echo JText::_('JGLOBAL_SORT_BY'); ?></option>
                        <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
                    </select>
                </div>
            </div>
            <div class="clearfix"> </div>
            <table class="table table-striped" id="userList">
                <thead>
                    <tr>
                        <?php if (isset($this->items[0]->ordering)): ?>
                            <th width="1%" class="nowrap center hidden-phone">
                                <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                            </th>
                        <?php endif; ?>
                        <th width="1%" class="hidden-phone">
                            <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                        </th>
                        <?php if (isset($this->items[0]->state)): ?>
                            <th width="1%" class="nowrap center">
                                <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                            </th>
                        <?php endif; ?>
                        <th class='left'>
                            <?php echo JHtml::_('grid.sort', 'COM_EASYSDI_CONTACT_FORM_LBL_USER_NAME', 'a.name', $listDirn, $listOrder); ?>
                        </th>
                        <th class='left'>
                            <?php /* echo JText::_('COM_EASYSDI_CONTACT_FORM_LBL_USER_MEMBER_ORGANISM'); */ ?>
                            <?php echo JHtml::_('grid.sort', 'COM_EASYSDI_CONTACT_FORM_LBL_USER_MEMBER_ORGANISM', 'o.name', $listDirn, $listOrder); ?>
                        </th>
                        <th class='left'>
                            <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
                        </th>
                        <?php if (isset($this->items[0]->id)): ?>
                            <th width="1%" class="nowrap center hidden-phone">
                                <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                            </th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tfoot>
                    <?php
                    if (isset($this->items[0])) {
                        $colspan = count(get_object_vars($this->items[0]));
                    } else {
                        $colspan = 10;
                    }
                    ?>
                    <tr>
                        <td colspan="<?php echo $colspan ?>">
                            <?php echo $this->pagination->getListFooter(); ?>
                        </td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    foreach ($this->items as $i => $item) :
                        $ordering = $listOrder == 'a.ordering';
                        $canCreate = $user->authorise('core.create', 'com_easysdi_contact.category.' . $item->catid);
                        $canEdit = $user->authorise('core.edit', 'com_easysdi_contact.category.' . $item->catid);
                        $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                        $canEditOwn = $user->authorise('core.edit.own', 'com_easysdi_contact.category.' . $item->catid) && $item->created_by == $userId;
                        $canChange = $user->authorise('core.edit.state', 'com_easysdi_contact.category.' . $item->catid) && $canCheckin;

                        $item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_easysdi_contact&task=edit&id=' . $item->catid);
                        ?>
                        <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid ?>">

                            <?php if (isset($item->ordering)): ?>
                                <td class="order nowrap center hidden-phone">
                                    <?php
                                    if ($canChange) :
                                        $disableClassName = '';
                                        $disabledLabel = '';
                                        if (!$saveOrder) :
                                            $disabledLabel = JText::_('JORDERINGDISABLED');
                                            $disableClassName = 'inactive tip-top';
                                        endif;
                                        ?>
                                        <span class="sortable-handler hasTooltip <?php echo $disableClassName ?>" title="<?php echo $disabledLabel ?>">
                                            <i class="icon-menu"></i>
                                        </span>
                                        <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
                                    <?php else : ?>
                                        <span class="sortable-handler inactive" >
                                            <i class="icon-menu"></i>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td class="center hidden-phone">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>
                            <?php if (isset($this->items[0]->state)): ?>
                                <td class="center">
                                    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'users.', $canChange, 'cb'); ?>
                                </td>
                            <?php endif; ?>
                            <td class="nowrap has-context">
                                <div class="pull-left">
                                    <?php if (isset($item->checked_out) && $item->checked_out) : ?>
                                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'users.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php if (($canEdit || $canEditOwn) && $canCheckin) : ?>
                                        <a href="<?php echo JRoute::_('index.php?option=com_easysdi_contact&task=user.edit&id=' . (int) $item->id); ?>">
                                            <?php echo $this->escape($item->name); ?></a>
                                    <?php else : ?>
                                        <?php echo $this->escape($item->name); ?>
                                    <?php endif; ?>
                                    <span class="small">

                                    </span>
                                    <div class="small">
                                        <?php echo JText::_('COM_EASYSDI_CONTACT_FORM_LBL_USER_USERNAME') . ' : ' . $this->escape($item->username); ?>
                                    </div>
                                </div>
                                <div class="pull-left">
                                    <?php
                                    // Create dropdown items
                                    JHtml::_('dropdown.edit', $item->id, 'user.');
                                    JHtml::_('dropdown.divider');
                                    if ($item->state) :
                                        JHtml::_('dropdown.unpublish', 'cb' . $i, 'users.');
                                    else :
                                        JHtml::_('dropdown.publish', 'cb' . $i, 'users.');
                                    endif;
                                    JHtml::_('dropdown.divider');

                                    if ($archived) :
                                        JHtml::_('dropdown.unarchive', 'cb' . $i, 'users.');
                                    else :
                                        JHtml::_('dropdown.archive', 'cb' . $i, 'users.');
                                    endif;

                                    if ($item->checked_out) :
                                        JHtml::_('dropdown.checkin', 'cb' . $i, 'users.');
                                    endif;

                                    if ($trashed) :
                                        JHtml::_('dropdown.untrash', 'cb' . $i, 'users.');
                                    else :
                                        JHtml::_('dropdown.trash', 'cb' . $i, 'users.');
                                    endif;

                                    // render dropdown list
                                    echo JHtml::_('dropdown.render');
                                    ?>
                                </div>
                            </td>
                            <td class="left hidden-phone">
                                <a href="<?php echo JRoute::_('index.php?option=com_easysdi_contact&task=organism.edit&id=' . (int) $item->member_organism_id); ?>">
                                    <?php echo $this->escape($item->member_organism); ?>
                                </a>
                            </td>
                            <td class="small hidden-phone">
                                <?php echo $item->access_level; ?>
                            </td>
                            <?php if (isset($this->items[0]->id)): ?>
                                <td class="center hidden-phone">
                                    <?php echo (int) $item->id; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="userjoomlaaction" id="userjoomlaaction" value="" />
            <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
            <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </div>

        <div class="modal fade" id="usermngmt" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo JText::_('COM_EASYSDI_CONTACT_USER_DELETE_ACTION_TITLE'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <p><?php echo JText::_('COM_EASYSDI_CONTACT_USER_DELETE_ACTION_BODY'); ?> </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btn_cancel" class="btn btn-default " data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
                        <button type="button" id="btn_delete" class="btn btn-danger btn-juser"><?php echo JText::_('COM_EASYSDI_CONTACT_USER_DELETE_ACTION_DELETE'); ?></button>
                        <button type="button" id="btn_disable" class="btn btn-warning btn-juser"><?php echo JText::_('COM_EASYSDI_CONTACT_USER_DELETE_ACTION_DISABLE'); ?></button>
                        <button type="button" id="btn_keep" class="btn btn-primary btn-juser"><?php echo JText::_('COM_EASYSDI_CONTACT_USER_DELETE_ACTION_KEEP'); ?></button>
                    </div>
                </div>
            </div>
        </div>
</form>