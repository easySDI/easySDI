<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/


// no direct access
defined('_JEXEC') or die;

$user=sdiFactory::getSdiUser();
if(!$user->isEasySDI) {
    return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/easysdi_processing.php';

$doc = JFactory::getDocument();
$base_url=Juri::base(true) . '/components/com_easysdi_processing/assets';
$doc->addScript($base_url . '/js/easysdi_processing.js?v=' . sdiFactory::getSdiFullVersion());

?>
<?php //include_once(dirname(__FILE__).'/../../header.php'); ?>

<div class="items">
    <div class="well">
        <table class="table process-table table-striped">
            <thead>
                <tr>
                    <th><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_CREATED'); ?></th>
                    <th><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_TITLE'); ?></th>
                    <th><?php echo JText::_('COM_EASYSDI_PROCESSING_TITLE'); ?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($this->items as $order) {
                    //$userRoles=Easysdi_processingHelper::getCurrentUserRolesOnData($order);

                    $dispatcher = JDispatcher::getInstance();
                    $plugin_results = $dispatcher->trigger( 'onRenderProcessingOrderItem' ,array($order));

                    ?>
                    <tr data-processingplugin=<?php echo $order->plugins ?>>
                        <td><?php echo Easysdi_processingHelper::getRelativeTimeString(JFactory::getDate($order->created)); ?></td></td>
                        <!--<td><strong><a href="<?php echo JRoute::_('index.php?option=com_easysdi_processing&amp;view=myrequest&amp;id='.$order->id); ?>"><?php echo $order->name ?></a></strong> - <?php echo $order->id; ?></td>-->
                        <td><strong><a href="<?php echo JRoute::_('index.php?option=com_easysdi_processing&view=myrequest&task=myrequest.edit&id='.$order->id); ?>"><?php echo $order->name ?></a></strong> - <?php echo $order->id; ?></td>
                        <td><?php echo $order->processing ?></td>
                        <td class="orderstate">
                        <?php echo Easysdi_processingStatusHelper::status($order->status) ?>
                        <?php
                            foreach ($plugin_results as $k=>$plugin_result) {
                                if (isset($plugin_result['status'])) echo ' <span class="'.$plugin_result['plugin'].'_'.$order->id.'_status">'.$plugin_result['status'].'</span>';
                            }
                            ?>
                        </td>
                        <td><?php
                            foreach ($plugin_results as $k=>$plugin_result) {
                                if (isset($plugin_result['progression'])) echo ' <span class="'.$plugin_result['plugin'].'_'.$order->id.'_progression">'.$plugin_result['progression'].'</span>';
                                if (isset($plugin_result['parent_txt'])&&$plugin_result['parent_txt']) echo $plugin_result['parent_txt'];
                            }
                            ?></td>
                        <td>
                                <div class="btn-group sdi-btn-download-file-from-list">
                                    <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                        <i class="icon-flag-2"></i>
                                    </a>

                                    <ul class="dropdown-menu">
                                        <?php

                                        if ($order->filestorage == 'url') {
                                            ?>
                                            <li><?php echo Easysdi_processingParamsHelper::file_link($order->fileurl, $order,'input',false); ?></li>
                                            <?php
                                        }else{
                                           ?>
                                           <li><?php echo Easysdi_processingParamsHelper::file_link($order->file, $order,'input',false); ?></li>
                                           <?php
                                       }?>
                                   </ul>
                               </div>
                        </td>
                        <td>
                               <div class="btn-group">
                                <a class="btn btn-primary btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                    <?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_ACTIONS'); ?>
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a  href="<?php echo JRoute::_('index.php?option=com_easysdi_processing&amp;view=myrequest&amp;layout=edit&amp;id='.$order->id); ?>"><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_OPEN'); ?></a>
                                    </li>

                                </ul>
                            </div>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

</div>
</div>
<?php //include_once(dirname(__FILE__).'/../../footer.php'); ?>
<?php




?>

<?php if($this->items) : ?>



 <div class="pagination">
    <?php if ($this->params->def('show_pagination_results', 1)) : ?>
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
    <?php endif; ?>
    <?php echo $this->pagination->getPagesLinks(); ?>
</div>


<?php endif; ?>