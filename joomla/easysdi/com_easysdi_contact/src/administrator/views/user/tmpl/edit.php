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

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_contact/assets/css/easysdi_contact.css?v=' . sdiFactory::getSdiFullVersion());

JText::script('COM_EASYSDI_CONTACT_FORM_USER_EDIT_LINK');
JText::script('COM_EASYSDI_CONTACT_FORM_ORGANISM_EDIT_LINK');
?>
<script type="text/javascript">
    Joomla.submitbutton = function (task)
    {
        if (task == 'user.cancel' || document.formvalidator.isValid(document.id('user-form'))) {
            Joomla.submitform(task, document.getElementById('user-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }

    function disableAddressType(disable, type)
    {
        var elem = document.getElementById('user-form').elements;
        for (var i = 0; i < elem.length; i++)
        {
            var tofind = 'jform[' + type + '_';
            if (elem[i].getAttribute('name') != null) {
                if (elem[i].getAttribute('name').indexOf(tofind) != -1
                        && elem[i].getAttribute('name').indexOf('sameascontact') == -1
                        && elem[i].getAttribute('type') != 'hidden')
                {
                    elem[i].disabled = disable;
                    elem[i].value = '';
                }
            }
        }
    }

    window.addEvent('domready', function ()
    {
        initAddressByType('billing');
        initAddressByType('delivry');
        makeUserEditLink();
        makeOrganismEditLink();
        jQuery('#jform_user_id').change(function () {
            makeUserEditLink();
        });
        jQuery('#jform_organismsMember').change(function () {
            makeOrganismEditLink();
        });
    })

    function initAddressByType(type)
    {
        var elem = document.getElementById('jform_' + type + '_sameascontact1');
        if (elem.checked == true)
        {
            disableAddressType(true, type);
        }
    }

    function makeUserEditLink() {
        jQuery('#edit_joomla_user').remove();
        var currentUserId = jQuery('#jform_user_id_id').val();
        var linkText = Joomla.JText._('COM_EASYSDI_CONTACT_FORM_USER_EDIT_LINK', 'Edit joomla user');
        if (jQuery.isNumeric(currentUserId)) {
            jQuery(" <a />", {
                id: "edit_joomla_user",
                name: "edit_joomla_user",
                href: "index.php?option=com_users&task=user.edit&id=" + currentUserId,
                text: linkText
            }).insertAfter('#jform_user_id_id');
        }
    }

    function makeOrganismEditLink() {
        jQuery('#edit_organism_link').remove();
        var currentOrgId = jQuery('#jform_organismsMember').val();

        var linkText = Joomla.JText._('COM_EASYSDI_CONTACT_FORM_ORGANISM_EDIT_LINK', 'Edit organism');
        if (jQuery.isNumeric(currentOrgId)) {
            jQuery(" <a />", {
                id: "edit_organism_link",
                name: "edit_organism_link",
                href: "index.php?option=com_easysdi_contact&task=organism.edit&id=" + currentOrgId,
                text: linkText
            }).appendTo(jQuery('#jform_organismsMember').parent());
        }
    }

</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_contact&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="user-form" class="form-validate">
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_CONTACT_TAB_NEW_USER') : JText::sprintf('COM_EASYSDI_CONTACT_TAB_EDIT_USER', $this->item->id); ?></a></li>
                <li><a href="#roles" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CONTACT_TAB_ROLES'); ?></a></li>
                <li><a href="#contactaddress" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CONTACT_TAB_CONTACTADDRESS'); ?></a></li>
                <li><a href="#billingaddress" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CONTACT_TAB_BILLINGADDRESS'); ?></a></li>
                <li><a href="#delivryaddress" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CONTACT_TAB_DELIVRYADDRESS'); ?></a></li>	
                <li><a href="#orderingoptions" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CONTACT_TAB_ORDERINGOPTIONS'); ?></a></li>					
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
                <div class="tab-pane" id="roles">
                    <?php foreach ($this->form->getFieldset('roles') as $field): ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        </div>
                        <?php if ($field->fieldname == 'organismsMember') echo '<hr>'; ?>
                    <?php endforeach; ?>
                </div>
                <div class="tab-pane" id="contactaddress">
                    <?php foreach ($this->form->getFieldset('contactaddress') as $field): ?>
                        <?php
                        $property = substr($field->id, 14);
                        $defaultvalue = null;
                        if ($property == 'addresstype_id')
                            $defaultvalue = '1';
                        else if ($property == 'user_id')
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
                        else if ($property == 'user_id')
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
                        else if ($property == 'user_id')
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
                <div class="tab-pane" id="orderingoptions">
                    <?php foreach ($this->form->getFieldset('orderingoptions') as $field): ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
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
                            <?php echo $this->form->getValue('user'); ?>
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