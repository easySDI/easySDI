<?php
/**
 * @version     4.3.2
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS and JS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_dashboard/assets/css/easysdi_dashboard.css');
$document->addScript('components/com_easysdi_dashboard/assets/js/com_easysdi_dashboard.js');
$document->addScript('components/com_easysdi_dashboard/libraries/flot/jquery.flot.min.js');
$document->addScript('components/com_easysdi_dashboard/libraries/flot/jquery.flot.pie.js');
$document->addScript('components/com_easysdi_dashboard/libraries/flot/jquery.flot.tooltip.min.js');
$document->addScript('components/com_easysdi_dashboard/libraries/flot/jquery.flot.stack.min.js');
$document->addScriptDeclaration('com_easysdi_dahboard_graphcolours=[' . $this->graphcolours . ']');

//Report parameters box
$reportparametersbox = new JLayoutFile('com_easysdi_dashboard.reportparametersbox', $basePath = JPATH_COMPONENT . '/layouts');

$user = JFactory::getUser();
$userId = $user->get('id');


//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar)) {
    $this->sidebar .= $this->extra_sidebar;
}
?>
<style type="text/css">
    .btn-toolbar {
        font-size: 13px;
    }
</style>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_dashboard&view=shop'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty($this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
        <?php else : ?>
            <div id="j-main-container">
            <?php endif; ?>
            <div class="row-fluid">
                <div class="span12">
                    <h3><?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_STATIC_TITLE'); ?></h3>
                </div>
            </div>  
            <div class="clearfix"> </div>
            <div class="row-fluid">

                <div class="row-fluid">
                    <?php
                    require_once JPATH_COMPONENT . '/indicators/shop_global.html.php';
                    ?>
                </div>

            </div>

            <div class="row-fluid">
                <div class="span12">
                    <h3>
                        <?php echo JText::_('COM_EASYSDI_DASHBOARD_PERIOD_TITLE_FROM'); ?>
                        <span id="dashboard_period_label_from"></span>
                        <?php echo JText::_('COM_EASYSDI_DASHBOARD_PERIOD_TITLE_TO'); ?>
                        <span id="dashboard_period_label_to"></span>                        
                    </h3>

                </div>
            </div>  

            <div class="row-fluid">
                <div class="span4 well well-small">
                    <?php
                    require_once JPATH_COMPONENT . '/indicators/shop_responsetimeproduct.html.php';
                    ?>
                </div>

                <div class="span8 well well-small">
                    <?php
                    require_once JPATH_COMPONENT . '/indicators/shop_topextractions.html.php';
                    ?>
                </div>
            </div>

            <div class="row-fluid">
                <div class="span4 well well-small">
                    <?php
                    require_once JPATH_COMPONENT . '/indicators/shop_topusers.html.php';
                    ?>
                </div>
                <div class="span8 well well-small">
                    <?php
                    require_once JPATH_COMPONENT . '/indicators/shop_topdownloads.html.php';
                    ?>
                </div>
            </div>        
            <div class="row-fluid">
                <div class="span4">
                </div>
                <div class="span8 well well-small">
                    <?php
                    require_once JPATH_COMPONENT . '/indicators/shop_extractionstype.html.php';
                    ?>
                </div>
            </div>              

            <div>
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />

                <?php echo JHtml::_('form.token'); ?>
            </div>
            </form>

            <?php
            //Add report parameters box html + scripts
            echo $reportparametersbox->render(null);
            ?>
            <script>
                triggerFilterUpdate();
            </script>

