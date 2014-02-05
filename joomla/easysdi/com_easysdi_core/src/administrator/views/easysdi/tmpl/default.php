<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_core/assets/css/easysdi_core.css');
?>
<div class="row-fluid">
    <div class="span2">
        <div class="sidebar-nav">
            <form action="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=easysdi'); ?>" method="post" name="adminForm" id="adminForm">
                <ul class="nav nav-list">
                    <!--<li class="nav-header"><?php echo JText::_('COM_EASYSDI_CORE_ICON_SDI_HEADER_SUBMENU'); ?></li>-->
                    <li class="active"><a href="<?php echo JRoute::_('index.php?option=com_easysdi_core'); ?>"><i class="icon-home icon-white"></i> <?php echo JText::_('COM_EASYSDI_CORE_ICON_SDI_HOME'); ?></a></li>
                    <?php foreach ($this->navLinks as $navLink): ?>
                        <li><a href="<?php echo $navLink['link']; ?>"><?php echo $navLink['text']; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </form>
        </div>
    </div>

    <div class="span6">
        <div class="row-fluid">
            <div class="span12">
                <?php
                foreach ($this->moduleseasysdi_left as $module) {
                    $output = JModuleHelper::renderModule($module, array('style' => 'well'));
                    $params = new JRegistry;
                    $params->loadString($module->params);
                    echo $output;
                }
                ?>
            </div>
        </div>
    </div>
    <div class="span4">
        <?php
        foreach ($this->moduleseasysdi_right as $module) {
            $output = JModuleHelper::renderModule($module, array('style' => 'well'));
            $params = new JRegistry;
            $params->loadString($module->params);
            echo $output;
        }
        ?>
    </div>
</div>