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

JHtml::_('behavior.keepalive');
JHTML::_('behavior.modal');
JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root(true) . '/components/com_easysdi_dashboard/assets/css/easysdi_dashboard.css?v=' . sdiFactory::getSdiFullVersion());
$document->addScript(JURI::root(true) . '/components/com_easysdi_dashboard/assets/js/com_easysdi_dashboard.js?v=' . sdiFactory::getSdiFullVersion());
$document->addScript(JURI::root(true) . '/components/com_easysdi_core/libraries/flot-0.8.3/jquery.flot.min.js');
$document->addScript(JURI::root(true) . '/components/com_easysdi_core/libraries/flot-0.8.3/jquery.flot.pie.js');
$document->addScript(JURI::root(true) . '/components/com_easysdi_core/libraries/flot-0.8.3/jquery.flot.tooltip.min.js');
$document->addScript(JURI::root(true) . '/components/com_easysdi_core/libraries/flot-0.8.3/jquery.flot.stack.min.js');
$document->addScriptDeclaration('com_easysdi_dahboard_graphcolours=[' . $this->graphcolours . ']');
?>
<div class="dashboard dashboard-shop front-end-edit">
    <h1><?php echo JText::_('COM_EASYSDI_DASHBOARD_TITLE_SHOP'); ?></h1>
    <div class="well sdi-searchcriteria">
        <div class="row-fluid">
            <form class="form-search" action="<?php echo JRoute::_('index.php?option=com_easysdi_dashboard&view=shop'); ?>" method="post">
                <div class="btn-toolbar">
                    <div class="btn-group pull-right">

                        <?php echo Easysdi_dashboardHelper::getFrontendFilters($this->user); ?>

                    </div>
                </div>

            </form>
        </div>
    </div>

    <?php
    $tmpLayout = new JLayoutFile('com_easysdi_dashboard.global_dates', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_dashboard'));
    echo $tmpLayout->render(null);
    ?>

    <div class="items">
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
    </div>
</div>

<script>
    triggerFilterUpdate();
</script>
