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
$document->addScript('components/com_easysdi_core/views/version/tmpl/version.js');
?>
<?php
if ($this->item) :
    $versioning = ($this->item->versioning == 1) ? 'true' : 'false';
    $document->addScriptDeclaration('var versioning=' . $versioning . ';');
    JText::script('COM_EASYSDI_CORE_DATATABLES_DISPLAY');
    JText::script('COM_EASYSDI_CORE_DATATABLES_RECORDSPERPAGE');
    JText::script('COM_EASYSDI_CORE_DATATABLES_SHOWING');
    JText::script('COM_EASYSDI_CORE_DATATABLES_RECORDS');
    JText::script('COM_EASYSDI_CORE_DATATABLES_NORESULT');
    JText::script('COM_EASYSDI_CORE_DATATABLES_OF');
    JText::script('COM_EASYSDI_CORE_DATATABLES_TO');
    JText::script('COM_EASYSDI_CORE_DATATABLES_NEXT');
    JText::script('COM_EASYSDI_CORE_DATATABLES_PREVIOUS');
    JText::script('COM_EASYSDI_CORE_DATATABLES_SEARCH');
    
    //METADATASTATES
    JText::script('INPROGRESS');
    JText::script('VALIDATED');
    JText::script('PUBLISHED');
    JText::script('ARCHIVED');
    JText::script('TRASHED');
    ?>

    <div class="version-edit front-end-edit">
        <?php if (!empty($this->item->id)): ?>
            <?php if ($this->item->versioning): ?>
                <h1><?php echo JText::_('COM_EASYSDI_CORE_TITLE_EDIT_VERSION') . ' ' . $this->item->resourcename . ' - ' . $this->item->name; ?></h1>
            <?php else: ?>
                <h1><?php echo JText::_('COM_EASYSDI_CORE_TITLE_EDIT_VERSION') . ' ' . $this->item->resourcename; ?></h1>
            <?php endif; ?>
        <?php endif; ?>
                
                <?php if($this->item->resourcetypechild): ?>
        <form class="form-horizontal form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.save'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
            <?php else:?>
            <div>
                <?php echo $this->getTopActionBar(); ?>
            </div>
            <?php endif;?>

            <div class="row-fluid">
                <?php if($this->item->resourcetypechild): ?>
                <!-- Criteria -->
                <div class="span12">
                    <div class="well">
                        <div class="sdi-searchcriteria">
                            <h3><?php echo JText::_('COM_EASYSDI_CORE_TITLE_SEARCH_CRITERIA'); ?></h3>
                            <?php foreach ($this->form->getFieldset('details') as $field): ?>
                                <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                    <div class="control-label"><?php echo $field->label; ?></div>
                                    <div class="controls"><?php echo $field->input; ?></div>
                                </div>
                            <?php endforeach; ?>
                            <?php if ($this->item->versioning): ?>
                                <div class="control-group" id="<?php echo $this->form->getField('searchlast')->fieldname; ?>">
                                    <div class="control-label"><?php echo $this->form->getField('searchlast')->label; ?></div>
                                    <div class="controls"><?php echo $this->form->getField('searchlast')->input; ?></div>
                                </div>
                            <?php endif; ?>
                            <div class=""><?php echo $this->getSearchToolbar(); ?></div>
                        </div>
                        <hr>
                        <div class="sdi-searchresult">
                            <script type="text/javascript">
                                availablechildrenData = <?php echo json_encode($this->item->availablechildren); ?>;
                            </script>
                            
                            <h3><?php echo JText::_('COM_EASYSDI_CORE_TITLE_SEARCH_RESULTS'); ?></h3>
                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="sdi-availablechildren" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_RESOURCENAME'); ?></th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_VERSIONNAME'); ?></th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_RESOURCETYPE'); ?></th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_METADATASTATE'); ?></th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_ADD'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    /*if (!empty($this->item->availablechildren)):
                                        foreach ($this->item->availablechildren as $child):
                                            JText::script($child->state);
                                            ?>
                                            <tr>
                                                <td><?php echo $child->id; ?></td>
                                                <td><?php echo $child->resource; ?></td>
                                                <td><?php echo $child->version; ?></td>
                                                <td><?php echo $child->resourcetype; ?></td>
                                                <td><?php echo JText::_($child->state); ?></td>
                                                <td class="center"><button type="button" onClick='addChild(<?php echo htmlspecialchars(json_encode($child),ENT_QUOTES); ?>);' class="btn btn-success btn-mini"><i class="icon-white icon-new"></i></button></td> 
                                            </tr>
                                            <?php
                                        endforeach;
                                    endif;*/
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Child -->
                <div class="row-fluid">
                    <div class="span12">
                        <div class="well">
                            <script type="text/javascript">
                                childrenData = <?php echo json_encode($this->item->children); ?>;
                            </script>
                            
                            <h3><?php echo JText::_('COM_EASYSDI_CORE_TITLE_VERSION_CHILDREN'); ?></h3>
                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="sdi-children" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_RESOURCENAME'); ?></th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_VERSIONNAME'); ?></th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_RESOURCETYPE'); ?></th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_METADATASTATE'); ?></th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_REMOVE'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    /*if (!empty($this->item->children)):
                                        foreach ($this->item->children as $child):
                                            JText::script($child->state);
                                            ?>
                                            <tr class="sdi-child-<?php echo $child->id; ?>">
                                                <td><?php echo $child->id; ?></td>
                                                <td><?php echo $child->resource; ?></td>
                                                <td><?php echo $child->version; ?></td>
                                                <td><?php echo $child->resourcetype; ?></td>
                                                <td><?php echo JText::_($child->state); ?></td> 
                                                <td class="center"><button type="button" id="sdi-childbutton-<?php echo $child->id; ?>" onClick="deleteChild('<?php echo $child->id; ?>');" class="btn btn-danger btn-mini"><i class="icon-white icon-minus"></i></button></td>                                                 
                                            </tr>
                                            <?php
                                        endforeach;
                                    endif;*/
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif;?>
                
                <!-- Parents -->
                <div class="row-fluid">
                    <div class="span12">
                        <div class="well">
                            <h3><?php echo JText::_('COM_EASYSDI_CORE_TITLE_VERSION_PARENT'); ?></h3>
                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="sdi-parents" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_RESOURCENAME'); ?></th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_VERSIONNAME'); ?></th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_RESOURCETYPE'); ?></th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_METADATASTATE'); ?></th>
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
                                                <td><?php echo $parent->resourcetype; ?></td>
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

<?php if($this->item->resourcetypechild): ?>
            <div>
                <?php echo $this->getToolbar(); ?>
            </div>

            <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
                <?php echo $field->input; ?>
            <?php endforeach; ?>  
            <input type = "hidden" name = "task" value = "" />
            <input type = "hidden" name = "option" value = "com_easysdi_core" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
<?php endif;?>


    </div>
    <?php
else:
    echo JText::_('COM_EASYSDI_CORE_ITEM_NOT_LOADED');
endif;
?>
