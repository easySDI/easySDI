<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// no direct access
defined('_JEXEC') or die;
?>

<?php if( $this->item ) : ?>

    <div class="item_fields">
        
        <ul class="fields_list">

        
        
                    <li><?php echo 'id'; ?>: 
                    <?php echo $this->item->id; ?></li>

        
        
                    <li><?php echo 'guid'; ?>: 
                    <?php echo $this->item->guid; ?></li>

        
        
                    <li><?php echo 'alias'; ?>: 
                    <?php echo $this->item->alias; ?></li>

        
        
                    <li><?php echo 'created_by'; ?>: 
                    <?php echo $this->item->created_by; ?></li>

        
        
                    <li><?php echo 'created'; ?>: 
                    <?php echo $this->item->created; ?></li>

        
        
                    <li><?php echo 'modified_by'; ?>: 
                    <?php echo $this->item->modified_by; ?></li>

        
        
                    <li><?php echo 'modified'; ?>: 
                    <?php echo $this->item->modified; ?></li>

        
        
                    <li><?php echo 'ordering'; ?>: 
                    <?php echo $this->item->ordering; ?></li>

        
        
                    <li><?php echo 'state'; ?>: 
                    <?php echo $this->item->state; ?></li>

        
        
                    <li><?php echo 'checked_out'; ?>: 
                    <?php echo $this->item->checked_out; ?></li>

        
        
                    <li><?php echo 'checked_out_time'; ?>: 
                    <?php echo $this->item->checked_out_time; ?></li>

        
        
                    <li><?php echo 'user_id'; ?>: 
                    <?php echo $this->item->user_id; ?></li>

        
        
                    <li><?php echo 'acronym'; ?>: 
                    <?php echo $this->item->acronym; ?></li>

        
        
                    <li><?php echo 'logo'; ?>: 
                    <?php echo $this->item->logo; ?></li>

        
        
                    <li><?php echo 'description'; ?>: 
                    <?php echo $this->item->description; ?></li>

        
        
                    <li><?php echo 'website'; ?>: 
                    <?php echo $this->item->website; ?></li>

        
        
                    <li><?php echo 'notificationrequesttreatment'; ?>: 
                    <?php echo $this->item->notificationrequesttreatment; ?></li>

        
        
                    <li><?php echo 'catid'; ?>: 
                    <?php echo $this->item->catid; ?></li>

        
        
                    <li><?php echo 'params'; ?>: 
                    <?php echo $this->item->params; ?></li>

        

        </ul>
        
    </div>

<?php endif; ?>