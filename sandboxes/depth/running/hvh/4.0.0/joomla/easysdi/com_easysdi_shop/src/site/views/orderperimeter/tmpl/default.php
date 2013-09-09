<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_shop', JPATH_ADMINISTRATOR);

if ($this->item->perimeters) :
    ?>
    <script src="/sdi4/media/jui/js/jquery.js" type="text/javascript"></script>
    <script src="/sdi4/media/jui/js/jquery-noconflict.js" type="text/javascript"></script>
    <script src="/sdi4/media/jui/js/bootstrap.js" type="text/javascript"></script>
    <link rel="stylesheet" href="/sdi4/templates/protostar/css/template.css" type="text/css" />

    <div class="container-fluid" >
        <?php
        if (!empty($this->mapscript)) :
            ?>
            <div class="row-fluid">
                <div class="span8">
                    <div  >
                        <?php
                        echo $this->mapscript;
                        ?>
                    </div>
                </div>

                <div class="span4">
                    <div class="btn-group" data-toggle="buttons-radio">
                        <?php
                        foreach ($this->item->perimeters as $perimeter):
                            if($perimeter->id == 1):
                                ?>
                                <a href="#" class="btn" onClick=""><i class=" icon-checkbox-unchecked"></i><?php echo $perimeter->name; ?></a>
                                <br>
                                <br>
                                <a href="#" class="btn" onClick=""><i class="icon-chart"></i><?php echo $perimeter->name; ?></a>
                                <br>
                                <br>
                                <?php
                            elseif($perimeter->id == 2):
                            ?>
                            <a href="#" class="btn" onClick=""><i class="icon-user"></i><?php echo $perimeter->name; ?></a>
                            <br>
                            <br>
                            <?php
                            else:
                            ?>
                            <a href="#" class="btn" onClick=""><i class="icon-brush"></i><?php echo $perimeter->name; ?></a>
                            <br>
                            <br>
                            <?php
                            endif;
                        endforeach;
                        ?>
                    </div>

                </div>
            </div>
            <?php
        else:
            echo JText::_('COM_EASYSDI_SHOP_ERROR');
        endif;
        ?>
    </div>
    <?php
else:
    echo JText::_('COM_EASYSDI_SHOP_ERROR'); 
endif;