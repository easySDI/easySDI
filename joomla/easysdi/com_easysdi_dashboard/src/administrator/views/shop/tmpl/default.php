<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
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
$document->addStyleSheet(JURI::root(true) . '/components/com_easysdi_dashboard/assets/css/easysdi_dashboard.css?v=' . sdiFactory::getSdiFullVersion());
$document->addScript(JURI::root(true) . '/components/com_easysdi_dashboard/assets/js/com_easysdi_dashboard.js?v=' . sdiFactory::getSdiFullVersion());
$document->addScript(JURI::root(true) . '/components/com_easysdi_core/libraries/flot-0.8.3/jquery.flot.min.js');
$document->addScript(JURI::root(true) . '/components/com_easysdi_core/libraries/flot-0.8.3/jquery.flot.pie.js');
$document->addScript(JURI::root(true) . '/components/com_easysdi_core/libraries/flot-0.8.3/jquery.flot.tooltip.min.js');
$document->addScript(JURI::root(true) . '/components/com_easysdi_core/libraries/flot-0.8.3/jquery.flot.stack.min.js');
$document->addScriptDeclaration('com_easysdi_dahboard_graphcolours=[' . $this->graphcolours . ']');

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

    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">

        <div class="row-fluid">
            <div class="span12">
                <h2><?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_STATIC_TITLE'); ?></h2>
            </div>
        </div>  
        <div class="clearfix"> </div>
        <div class="row-fluid">
            <div class="row-fluid">
                <?php
                $tmpLayout = new JLayoutFile('com_easysdi_dashboard.shop_global', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_dashboard'));
                echo $tmpLayout->render(null);
                ?>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12">
                <?php
                $tmpLayout = new JLayoutFile('com_easysdi_dashboard.global_dates', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_dashboard'));
                echo $tmpLayout->render(null);
                ?>
            </div>
        </div>  

        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <div class="span12 well well-small">
                        <?php
                        $tmpLayout = new JLayoutFile('com_easysdi_dashboard.shop_toporganisms', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_dashboard'));
                        echo $tmpLayout->render(null);
                        ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12 well well-small">
                        <?php
                        $tmpLayout = new JLayoutFile('com_easysdi_dashboard.shop_topusers', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_dashboard'));
                        echo $tmpLayout->render(null);
                        ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12 well well-small">
                        <?php
                        $tmpLayout = new JLayoutFile('com_easysdi_dashboard.shop_responsetimeproduct', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_dashboard'));
                        echo $tmpLayout->render(null);
                        ?>
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="row-fluid">
                    <div class="span12 well well-small">
                        <?php
                        $tmpLayout = new JLayoutFile('com_easysdi_dashboard.shop_topproviders', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_dashboard'));
                        echo $tmpLayout->render(null);
                        ?>
                    </div>
                </div>                
                <div class="row-fluid">
                    <div class="span12 well well-small">
                        <?php
                        $tmpLayout = new JLayoutFile('com_easysdi_dashboard.shop_topextractions', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_dashboard'));
                        echo $tmpLayout->render(null);
                        ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12 well well-small">
                        <?php
                        $tmpLayout = new JLayoutFile('com_easysdi_dashboard.shop_topdownloads', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_dashboard'));
                        echo $tmpLayout->render(null);
                        ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12 well well-small">
                        <?php
                        $tmpLayout = new JLayoutFile('com_easysdi_dashboard.shop_extractionstype', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_dashboard'));
                        echo $tmpLayout->render(null);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div>

            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <?php echo JHtml::_('form.token'); ?>
        </div>

        <script>
            triggerFilterUpdate();
        </script>
    </div>
</form>

