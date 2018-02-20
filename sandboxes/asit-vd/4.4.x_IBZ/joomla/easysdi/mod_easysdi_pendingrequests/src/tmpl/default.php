<?php
/**
 * @version     4.4.5
 * @package     mod_easysdi_pendingrequests
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
?>

<div>
    <div class="titlewithlink clearfix">
        <h2><?php echo JText::plural('MOD_EASYSDI_PENDINGREQUESTS_TITLE_PENDING_REQUESTS', $totalRequests); ?></h2>
        <a title="<?php echo JText::plural('MOD_EASYSDI_PENDINGREQUESTS_LINK_ALL_REQUESTS', $totalRequests); ?>" href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=requests' . $myRequestsItemString); ?>" class="link"><?php echo JText::plural('MOD_EASYSDI_PENDINGREQUESTS_LINK_ALL_REQUESTS', $totalRequests); ?></a>
    </div>

<?php if ($requests): ?>

        <?php
        $requestsListLayout = new JLayoutFile('com_easysdi_shop.requests', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_shop'));
        echo $requestsListLayout->render(array(
            'items' => $requests,
            'displayTitle' => false,
            'filterStatus' => 1
        ));
        ?>

    <?php endif; ?>
</div> 

