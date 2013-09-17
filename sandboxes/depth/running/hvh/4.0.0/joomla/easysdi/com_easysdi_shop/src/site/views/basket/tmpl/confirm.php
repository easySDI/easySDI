<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_shop', JPATH_ADMINISTRATOR);
$jinput = JFactory::getApplication()->input;
$action = $jinput->get('action', '', 'STRING');
switch ($action) {
    case 'draft':
        $title = "Confirm your save";
        $welltitle = "You must sign in";
        $btnlabel = "Save";
        break;
    case 'estimate':
        $title = "Confirm your estimate";
        $welltitle = "You must sign in to confirm your estimate";
        $btnlabel = "Confirm your estimate";
        break;
    case 'order':
        $title = "Confirm your order";
        $welltitle = "You must sign in to confirm your order";
        $btnlabel = "Confirm your order";
        break;
}
?>
<?php if ($this->item) : ?>
    <form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=basket.save'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">

        <div class="basket-confirm front-end-edit">
            <h1><?php echo $title; ?></h1>

            <div class="container-fluid">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="span6 offset3 well">
                                <?php if ($this->user->juser->guest) : ?>
                                <h3><?php echo $welltitle; ?></h3>
                                <input type="text" id="username" class="span12" name="username" placeholder="Username">
                                <br/>
                                <br/>
                                <input type="password" id="password" class="span12" name="password" placeholder="Password">
                                <?php endif; ?>
                                <br/>
                                <br/>
                                <label class="checkbox">
                                    <input type="checkbox" > I accept the <a>terms and conditions</a> blah blah
                                </label>
                                <br/>
                                <br/>
                                <button type="submit" id="loginSubmit" name="loginSubmit" class="btn btn btn-primary btn-block btn-large"><b><?php echo $btnlabel; ?></b></button>
                            </div>
                        </div>
                        
                            <div class="span10 offset1 well">
                                <div class="row-fluid ">
                                <h3><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_EXTRACTION_NAME'); ?></h3>
                                <table class="table table-striped">
                                    <tfoot>
                                    </tfoot>
                                    <tbody>
                                        <?php foreach ($this->item->extractions as $extraction) : ?>
                                            <tr id="<?php echo $extraction->id; ?>">
                                                <td>
                                                    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resource.edit&id=' . (int) $extraction->resource); ?>"><?php echo $extraction->name; ?></a>
                                                    <div class="small"><?php echo $extraction->organism; ?></div>
                                                    <div class="accordion" id="accordion_<?php echo $extraction->id; ?>_properties">
                                                        <div class="accordion-group">
                                                            <div class="accordion-heading">
                                                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_<?php echo $extraction->id; ?>_properties" href="#<?php echo $extraction->id; ?>_collapse">
                                                                    <?php echo JText::_("COM_EASYSDI_SHOP_BASKET_EXTRACTION_PROPERTIES"); ?>
                                                                </a>
                                                            </div>
                                                            <div id="<?php echo $extraction->id; ?>_collapse" class="accordion-body collapse">
                                                                <div class="accordion-inner">
                                                                    <?php
                                                                    foreach ($extraction->properties as $property):
                                                                        ?>
                                                                        <div class="small"><?php echo $property->name; ?> : 
                                                                            <?php
                                                                            foreach ($property->values as $value) :
                                                                                if (!empty($value->name)) :
                                                                                    echo $value->name;
                                                                                else :
                                                                                    echo $value->value;
                                                                                endif;
                                                                                echo', ';
                                                                            endforeach;
                                                                            ?>
                                                                        </div>
                                                                        <?php
                                                                    endforeach;
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>                                                
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row-fluid" >
                                <h3><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PERIMETER'); ?></h3>
                                <div class="row-fluid" >
                                    <div id="perimeter-recap" >
                                        <?php if (!empty($this->item->extent)): ?>
                                            <div><h4><?php echo $this->item->extent->name; ?></h4></div>
                                            <?php foreach ($this->item->extent->features as $feature): ?>
                                                <div><?php echo $feature->name; ?></div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row-fluid" >
                                <h3><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_THIRD_PARTY'); ?></h3>
                            </div>
                        </div>

                    </div><!--/span-->
                </div><!--/row-->
            </div>
        </div>
        <input type = "hidden" name = "task" value = "basket.save" />
            <input type = "hidden" name = "option" value = "com_easysdi_shop" />
            <?php echo JHtml::_('form.token'); ?>
    </form>

    <?php
else:
    echo JText::_('COM_EASYSDI_SHOP_ITEM_NOT_LOADED');
endif;
?>
