<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_core
 * @copyright	
 * @license		
 * @author		
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_core/assets/css/easysdi_core.css');
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
    