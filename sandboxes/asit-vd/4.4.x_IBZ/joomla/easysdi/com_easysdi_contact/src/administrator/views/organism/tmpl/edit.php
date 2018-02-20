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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

$base_url = JURI::root() . 'components/com_easysdi_core/libraries';

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_contact/assets/css/easysdi_contact.css?v=' . sdiFactory::getSdiFullVersion());
$document->addStyleSheet($base_url.'/DataTables-1.9.4/media/css/jquery.dataTables.css');
$document->addScript($base_url.'/DataTables-1.9.4/media/js/jquery.dataTables.min.js');
$document->addScript('components/com_easysdi_contact/views/organism/tmpl/contact_organism.js?v=' . sdiFactory::getSdiFullVersion());
?>
<script type="text/javascript">
    orgId = <?php echo(empty($this->item->id) ? '0' : $this->item->id); ?>;
    lblYes = '<?php echo JText::_('JYES'); ?>';
    lblNo = '<?php echo JText::_('JNO'); ?>';
    dtLang = '<?php $l =  explode(' ', JFactory::getLanguage()->getName()); echo $l[0] ?>';
    siteRoot = '<?php echo(JURI::root()); ?>';
    invalidFormMsg = '<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>';

    window.addEvent('domready', function ()
    {
        initAddressByType('billing');
        initAddressByType('delivry');
    })

    jQuery(document).ready(function () {
        jQuery('a[data-toggle="tab"][href*="#usersandroles"]').on('shown', function (e) {
            if (!jQuery('#user-roles-table').hasClass("dataTable") && orgId > 0) {
                creatRoleTable();
            }
        });
    });

</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_contact&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="organism-form" class="form-validate">
    <div class="row-fluid">
        <div class="span10 form-horizontal">

            <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_CONTACT_TAB_NEW_ORGANISM') : JText::sprintf('COM_EASYSDI_CONTACT_TAB_EDIT_ORGANISM', $this->item->id); ?></a></li>
                <?php if (!empty($this->item->id)): ?>
                    <li><a href="#usersandroles" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CONTACT_TAB_USERSANDROLES'); ?></a></li>
                <?php endif; ?>
                <li><a href="#orderuser" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CONTACT_TAB_EXTRACT'); ?></a></li>
                <li><a href="#contactaddress" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CONTACT_TAB_CONTACTADDRESS'); ?></a></li>
                <li><a href="#billingaddress" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CONTACT_TAB_BILLINGADDRESS'); ?></a></li>
                <li><a href="#delivryaddress" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CONTACT_TAB_DELIVRYADDRESS'); ?></a></li>	
                <li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CONTACT_TAB_PUBLISHING'); ?></a></li>
                <?php if ($this->canDo->get('core.admin')): ?>
                    <li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CONTACT_TAB_RULES'); ?></a></li>
                <?php endif ?>
            </ul>

            <div class="tab-content">
                <!-- Begin Tabs -->
                <div class="tab-pane active" id="details">
                    <?php foreach ($this->form->getFieldset('details') as $field): ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="tab-pane" id="usersandroles">


                    <table id="user-roles-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo JText::_('COM_EASYSDI_CONTACT_TITLE_ORGANISM'); ?></th>
                                <th><?php echo JText::_('COM_EASYSDI_CONTACT_TITLE_USER'); ?></th>
                                <th><?php echo JText::_('COM_EASYSDI_CONTACT_FORM_LBL_USER_ORGANISMS_RM'); ?></th>
                                <th><?php echo JText::_('COM_EASYSDI_CONTACT_FORM_LBL_USER_ORGANISMS_MR'); ?></th>
                                <th><?php echo JText::_('COM_EASYSDI_CONTACT_FORM_LBL_USER_ORGANISMS_ME'); ?></th>
                                <th><?php echo JText::_('COM_EASYSDI_CONTACT_FORM_LBL_USER_ORGANISMS_DM'); ?></th>
                                <th><?php echo JText::_('COM_EASYSDI_CONTACT_FORM_LBL_USER_ORGANISMS_VM'); ?></th>
                                <th><?php echo JText::_('COM_EASYSDI_CONTACT_FORM_LBL_USER_ORGANISMS_ER'); ?></th>
                                <th><?php echo JText::_('COM_EASYSDI_CONTACT_FORM_LBL_USER_ORGANISMS_PM'); ?></th>
                                <th><?php echo JText::_('COM_EASYSDI_CONTACT_FORM_LBL_USER_ORGANISMS_TM'); ?></th>
                                <th><?php echo JText::_('COM_EASYSDI_CONTACT_FORM_LBL_USER_ORGANISMS_MANAGER'); ?></th>
                            </tr>
                        </thead>

                        <tbody>

                        </tbody>
                        <tfoot style="display: table-header-group;">
                            <tr>
                                <th class="org-col"></th>
                                <th class="user-col"></th>
                                <th class="role-col"></th>
                                <th class="role-col"></th>
                                <th class="role-col"></th>
                                <th class="role-col"></th>
                                <th class="role-col"></th>
                                <th class="role-col"></th>
                                <th class="role-col"></th>
                                <th class="role-col"></th>
                                <th class="role-col"></th>

                            </tr>
                        </tfoot>

                    </table>
                </div>
                <div class="tab-pane" id="orderuser">
                    <?php foreach ($this->form->getFieldset('orderuser') as $field): ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="tab-pane" id="contactaddress">
                    <?php foreach ($this->form->getFieldset('contactaddress') as $field): ?>
                        <?php
                        $property = substr($field->id, 14);
                        $defaultvalue = null;
                        if ($property == 'addresstype_id')
                            $defaultvalue = '1';
                        else if ($property == 'organism_id')
                            $defaultvalue = $this->item->id;
                        else {
                            if (isset($this->contactitem) && !empty($this->contactitem))
                                $defaultvalue = $this->contactitem->$property;
                        }
                        ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $this->form->getInput(substr($field->id, 6), null, $defaultvalue); ?></div>
                        </div>

                    <?php endforeach; ?>
                </div>
                <div class="tab-pane" id="billingaddress">
                    <?php foreach ($this->form->getFieldset('billingaddress') as $field): ?>
                        <?php
                        $property = substr($field->id, 14);
                        $defaultvalue = null;
                        if ($property == 'addresstype_id')
                            $defaultvalue = '2';
                        else if ($property == 'organism_id')
                            $defaultvalue = $this->item->id;
                        else {
                            if (isset($this->billingitem) && !empty($this->billingitem))
                                $defaultvalue = $this->billingitem->$property;
                        }
                        ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $this->form->getInput(substr($field->id, 6), null, $defaultvalue); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="tab-pane" id="delivryaddress">
                    <?php foreach ($this->form->getFieldset('delivryaddress') as $field): ?>
                        <?php
                        $property = substr($field->id, 14);
                        $defaultvalue = null;
                        if ($property == 'addresstype_id')
                            $defaultvalue = '3';
                        else if ($property == 'organism_id')
                            $defaultvalue = $this->item->id;
                        else {
                            if (isset($this->delivryitem) && !empty($this->delivryitem))
                                $defaultvalue = $this->delivryitem->$property;
                        }
                        ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $this->form->getInput(substr($field->id, 6), null, $defaultvalue); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="tab-pane" id="publishing">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('created'); ?></div>
                    </div>
                    <?php if ($this->item->modified_by) : ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('modified_by'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('modified_by'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('modified'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('modified'); ?></div>
                        </div>
                    <?php endif; ?>
                </div>


                <?php if ($this->canDo->get('core.admin')): ?>
                    <div class="tab-pane" id="permissions">
                        <fieldset>
                            <?php echo $this->form->getInput('rules'); ?>
                        </fieldset>
                    </div>
                <?php endif; ?>
            </div>
            <!-- End Tabs -->
        </div>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

        <!-- Begin Sidebar -->
        <div class="span2">
            <h4><?php echo JText::_('JDETAILS'); ?></h4>
            <hr />
            <fieldset class="form-vertical">
                <div class="control-group">
                    <div class="control-group">
                        <div class="controls">
                            <?php echo $this->form->getValue('name'); ?>
                        </div>
                    </div>
                    <?php
                    if ($this->canDo->get('core.edit.state')) {
                        ?>
                        <div class="control-label">
                            <?php echo $this->form->getLabel('state'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('state'); ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('access'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('access'); ?>
                    </div>
                </div>
            </fieldset>
        </div>
        <!-- End Sidebar -->
    </div>
</form>