<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_core/assets/css/easysdi_core.css?v=' . sdiFactory::getSdiFullVersion());
?>
<div class="row-fluid">
    <?php if (!empty($this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
        <?php else : ?>
            <div id="j-main-container">
            <?php endif; ?>

            <div class="span7">
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
            <div class="span5">
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
    </div>
    