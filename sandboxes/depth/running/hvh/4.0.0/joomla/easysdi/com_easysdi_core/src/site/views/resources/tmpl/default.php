<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;
?>

<?php
if (isset($this->user)):
    if ($this->user->isResourceManager()):
        $resourcetypes = $this->user->getResourceType();
        ?>
        <div class="btn-group">
            <a class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="icon-white icon-plus-sign"></i> New
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <?php
                foreach ($resourcetypes as $resourcetype):
                    ?>
                    <li>
                        <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resourceform.edit&id=0&resourcetype='.$resourcetype->id); ?>">
                        <?php echo $resourcetype->label; ?></a>
                    </li>
                    <?php
                endforeach;
                ?>
            </ul>
        </div>
        <?php
    endif;

endif;
?>

<div class="items">
    <div class="well">
        <?php $show = false; ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Ressource name</th>
                    <th>Ressource type</th>
                    <th>Statut</th>
                    <th>Action</th>
                    <th></th>
                </tr>
            </thead>
            <tfoot>
            </tfoot>
            <tbody>
                <?php foreach ($this->items as $item) : ?>
                    <?php
                    if (is_array($this->user->authorize($item->id))): //User has some rights on this item 
                        $show = true;
                        ?>
                        <tr>
                            <?php if ($this->user->authorize($item->id, sdiUser::resourcemanager)): ?>
                                <td>
                                    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resourceform.edit&id=' . (int) $item->id); ?>"><?php echo $item->name; ?></a>
                                </td>
                            <?php else : ?>
                                <td>
                                    <?php echo $item->name; ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <?php echo $item->resourcetype_name; ?>
                            </td>
                            <td>
                                <?php if ($item->state == 1) :?>
                                <span class="label label-success">Published</span>
                                <?php else : ?>
                                <span class="label label-info">Unpublished</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-success btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                        Metadata
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php if ($this->user->authorize($item->id, sdiUser::metadataeditor) || $this->user->authorize($item->id, sdiUser::metadataresponsible)): ?>
                                            <li>
                                                <a href="#">Edit</a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($this->user->authorize($item->id, sdiUser::metadataresponsible)): ?>
                                            <li>
                                                <a href="#">Validate</a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($this->user->authorize($item->id, sdiUser::resourcemanager)): ?>
                                            <li>
                                                <a href="#">Publish / Unpublished</a>
                                            </li>
                                            <li>
                                                <a href="#">Change publish date  </a>
                                            </li>                                                
                                            <li>
                                                <a href="#">Archive</a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($this->user->authorize($item->id, sdiUser::metadataeditor) || $this->user->authorize($item->id, sdiUser::metadataresponsible)): ?>
                                            <li class="divider"></li>
                                            <li>
                                                <a href="#">Assign</a>
                                            </li>
                                            <li>
                                                <a href="#">Notify</a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($this->user->authorize($item->id, sdiUser::metadataresponsible)): ?>
                                            <li class="divider"></li>
                                            <li>
                                                <a href="#">Synchronise</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-primary btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                        Manage
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php if ($this->user->authorize($item->id, sdiUser::resourcemanager)): ?>
                                            <li>
                                                <a href="#">New version</a>
                                            </li>
                                            <li>
                                                <a href="#">Relations</a>
                                            </li>                                                
                                        <?php endif; ?>
                                        <?php if ($this->user->authorize($item->id, sdiUser::diffusionmanager) || $this->user->authorize($item->id, sdiUser::viewmanager)): ?>
                                            <li class="divider"></li>
                                        <?php endif; ?>
                                        <?php if ($this->user->authorize($item->id, sdiUser::diffusionmanager)): ?>
                                            <li>
                                                <a href="#">Distribution</a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($this->user->authorize($item->id, sdiUser::viewmanager)): ?>
                                            <li>
                                                <a href="#">Visualization</a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($this->user->authorize($item->id, sdiUser::resourcemanager)): ?>
                                            <li class="divider"></li>
                                            <li>
                                                <a href="#"><i class="icon-remove"></i> Delete this version</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>

                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>


            </tbody>
        </table>
        <?php
        if (!$show):
            echo JText::_('COM_EASYSDI_CORE_NO_ITEMS');
        endif;
        ?>
    </div>
</div>
<?php if ($show): ?>
    <div class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif; ?>

