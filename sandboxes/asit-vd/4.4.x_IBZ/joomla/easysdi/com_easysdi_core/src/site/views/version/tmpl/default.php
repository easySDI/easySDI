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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_core/libraries/DataTables-1.9.4/media/css/jquery.dataTables.css');
$document->addScript('components/com_easysdi_core/libraries/DataTables-1.9.4/media/js/jquery.dataTables.min.js');
$document->addScript('components/com_easysdi_core/views/version/tmpl/version.js?v=' . sdiFactory::getSdiFullVersion());
?>
<?php
require_once JPATH_BASE.'/components/com_easysdi_catalog/libraries/easysdi/dao/SdiLanguageDao.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/sdimetadata.php';
/* datatable language */
$ldao = new SdiLanguageDao();
$user = new sdiUser();
$userParams = json_decode($user->juser->params);
$dtLanguage = $ldao->getDefaultLanguage()->datatable;
foreach($ldao->getAll() as $dtLang){
    if($dtLang->code === $userParams->language){
        $dtLanguage = $dtLang->datatable;
    }
}
?>
<script type="text/javascript">
    var dtLang = "<?php  echo ucfirst(strtolower($dtLanguage));?>";
</script>
<?php
if ($this->item) :
    //METADATASTATES
    JText::script('INPROGRESS');
    JText::script('VALIDATED');
    JText::script('PUBLISHED');
    JText::script('ARCHIVED');
    
    $versioning = ($this->item->versioning == 1) ? 'true' : 'false';
    $document->addScriptDeclaration('var versioning=' . $versioning . ';');
    $isReadonly = in_array($this->item->metadatastate, array(sdiMetadata::INPROGRESS, sdiMetadata::VALIDATED)) || $this->user->authorizeOnMetadata($this->item->id, sdiUser::resourcemanager) ? 'false' : 'true';
    $document->addScriptDeclaration("var isReadonly = ".$isReadonly.";");
    ?>

<style type="text/css">
    #searchlast > div.controls > fieldset > *{float:left;}
    #searchlast > div.controls > fieldset > label{margin-right: 1em;}
</style>

    <div class="version-edit front-end-edit">
        <?php if (!empty($this->item->id)): 
            $document->addScriptDeclaration("var version = ".$this->item->id."");
            $document->addScriptDeclaration("var resourcetypechild = '".$this->item->resourcetypechild."';");
            $document->addScriptDeclaration("var baseUrl = '".JUri::base()."/index.php?';");
            ?>
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
                <?php echo $this->getTopActionBar();?>
            </div>
            <?php endif;?>

            <div class="row-fluid">
                <?php if($this->item->resourcetypechild): ?>
                <!-- Criteria -->
                <div class="row-fluid">
                <div class="span12">
                    <div class="well">
                        <div class="sdi-searchcriteria form-horizontal form-inline form-validate">
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
                            <div class="">
                                <button id="clear-btn" class="btn btn-small"><span class="icon-clear"></span><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_CLEAR_BTN');?></button>
                        </div>
                        </div>
                        <hr>
                        <div class="sdi-searchresult">
                            <h3><?php echo JText::_('COM_EASYSDI_CORE_TITLE_SEARCH_RESULTS'); ?></h3>
                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="sdi-availablechildren" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>GUID</th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_RESOURCENAME'); ?></th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_VERSIONNAME'); ?></th>
                                        <th>RESOURCETYPE_ID</th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_RESOURCETYPE'); ?></th>
                                        <th>STATE_ID</th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_METADATASTATE'); ?></th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_ADD'); ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                </div>
                
                
                <!-- Child -->
                <div class="row-fluid">
                    <div class="span12">
                        <div class="well">
                            <h3><?php echo JText::_('COM_EASYSDI_CORE_TITLE_VERSION_CHILDREN'); ?></h3>
                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="sdi-children" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>GUID</th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_RESOURCENAME'); ?></th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_VERSIONNAME'); ?></th>
                                        <th>RESOURCETYPE_ID</th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_RESOURCETYPE'); ?></th>
                                        <th>STATE_ID</th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_METADATASTATE'); ?></th>
                                        <th><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_REMOVE'); ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
                                <tbody></tbody>
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
