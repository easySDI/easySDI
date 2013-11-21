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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$document = JFactory::getDocument();
$document->addStyleSheet('administrator/components/com_easysdi_core/libraries/DataTables-1.9.4/media/css/jquery.dataTables.css');
$document->addScript('administrator/components/com_easysdi_core/libraries/DataTables-1.9.4/media/js/jquery.dataTables.min.js');
?>
<?php if ($this->item) : ?>
    <script>
        js = jQuery.noConflict();
        var childrenTable, availablechildrenTable, parents;
        js(document).ready(function() {
            availablechildrenTable = js('#sdi-availablechildren').dataTable({
                "bFilter": false,
                "bLengthChange": false,
                "aoColumnDefs": [
                    {"bVisible": false, "aTargets": [0]}
                ]});

            childrenTable = js('#sdi-children').dataTable({
                "bLengthChange": false,
                "aoColumnDefs": [
                    {"bVisible": false, "aTargets": [0]}
                ]});
            parents = js('#sdi-parents').dataTable({
                "bFilter": true,
                "bLengthChange": false,
                "aoColumnDefs": [
                    {"bVisible": false, "aTargets": [0]}
                ]});


        });

        function addChild(child) {
            js('#sdi-children').dataTable().fnAddData([
                child.id,
                child.resource,
                child.version,
                child.state,
                '<button type="button" id="sdi-childbutton-' + child.id + '" onClick="deleteChild(\'' + child.id + '\');" class="btn btn-info btn-mini"><i class="icon-white icon-minus"></i></button>'

            ]);
        }

        function deleteChild(child) {
            childrenTable.fnDeleteRow(js('#sdi-childbutton-' + child).parent().parent()[0]);
        }

        Joomla.submitbutton = function(task)
        {
            if (task === 'version.save') {
                var results = [];
                var  children = childrenTable.fnGetData();
                children.each(function(value) {
                    results.push(value[0]);
                });
                
                var r = JSON.stringify(results);

                js('#children').val(r);
            }
            Joomla.submitform(task, document.getElementById('adminForm'));
        }

    </script>
    <div class="resource-edit front-end-edit">
        <?php if (!empty($this->item->id)): ?>
            <h1><?php echo JText::_('COM_EASYSDI_CORE_TITLE_EDIT_VERSION') . ' ' . $this->item->name; ?></h1>
        <?php endif; ?>
        <form class="form-horizontal form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.save'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">

            <div class="row-fluid">
                <div class="span12">
                    <div class="well">
                        <?php foreach ($this->form->getFieldset('details') as $field): ?>
                            <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                <div class="control-label"><?php echo $field->label; ?></div>
                                <div class="controls"><?php echo $field->input; ?></div>
                            </div>
                        <?php endforeach; ?>

                        <div class="pull-right"><?php echo $this->getSearchToolbar(); ?></div>
                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <div class="span12">
                    <div class="well">
                        <table cellpadding="0" cellspacing="0" border="0" class="display" id="sdi-availablechildren" width="100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Resource</th>
                                    <th>Version</th>
                                    <th>Statut</th>
                                    <th>Ajouter</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                if (!empty($this->item->availablechildren)):
                                    foreach ($this->item->availablechildren as $child):
                                        ?>
                                        <tr>
                                            <td><?php echo $child->id; ?></td>
                                            <td><?php echo $child->resource; ?></td>
                                            <td><?php echo $child->version; ?></td>
                                            <td><?php echo JText::_($child->state); ?></td> 
                                            <td class="center"><button type="button" onClick='addChild(<?php echo json_encode($child); ?>);' class="btn btn-success btn-mini"><i class="icon-white icon-new"></i></button></td> 
                                        </tr>
                                        <?php
                                    endforeach;
                                endif;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span12">
                        <div class="well">
                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="sdi-children" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Resource</th>
                                        <th>Version</th>
                                        <th>Statut</th>
                                        <th>Ajouter</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    if (!empty($this->item->children)):
                                        foreach ($this->item->children as $child):
                                            ?>
                                            <tr class="sdi-child-<?php echo $child->id; ?>">
                                                <td><?php echo $child->id; ?></td>
                                                <td><?php echo $child->resource; ?></td>
                                                <td><?php echo $child->version; ?></td>
                                                <td><?php echo JText::_($child->state); ?></td> 
                                                <td class="center"><button type="button" id="sdi-childbutton-<?php echo $child->id; ?>" onClick="deleteChild('<?php echo $child->id; ?>');" class="btn btn-danger btn-mini"><i class="icon-white icon-minus"></i></button></td>                                                 
                                            </tr>
                                            <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span12">
                        <div class="well">
                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="sdi-parents" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Resource</th>
                                        <th>Version</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    if (!empty($this->item->parents)):
                                        foreach ($this->item->parents as $parent):
                                            ?>
                                            <tr>
                                                <td><?php echo $parent->id; ?></td>
                                                <td><?php echo $parent->resource; ?></td>
                                                <td><?php echo $parent->version; ?></td>
                                                <td><?php echo JText::_($parent->state); ?></td> 
                                            </tr>
                                            <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div>
                <?php echo $this->getToolbar(); ?>
            </div>

            <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
                <?php echo $field->input; ?>
            <?php endforeach; ?>  
            <input type = "hidden" name = "task" value = "" />
            <input type = "hidden" name = "children" value = "" />
            <input type = "hidden" name = "option" value = "com_easysdi_core" />
            <?php echo JHtml::_('form.token'); ?>
        </form>



    </div>
    <?php
else:
    echo JText::_('COM_EASYSDI_CORE_ITEM_NOT_LOADED');
endif;
?>
