<?php
/**
*** @version     4.0.0
* @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013. All rights reserved.
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

        
        
                    <li><?php echo 'name'; ?>: 
                    <?php echo $this->item->name; ?></li>

        
        
                    <li><?php echo 'serviceconnector_id'; ?>: 
                    <?php echo $this->item->serviceconnector_id; ?></li>

        
        
                    <li><?php echo 'published'; ?>: 
                    <?php echo $this->item->published; ?></li>

        
        
                    <li><?php echo 'resourceauthentication_id'; ?>: 
                    <?php echo $this->item->resourceauthentication_id; ?></li>

        
        
                    <li><?php echo 'resourceurl'; ?>: 
                    <?php echo $this->item->resourceurl; ?></li>

        
        
                    <li><?php echo 'resourceusername'; ?>: 
                    <?php echo $this->item->resourceusername; ?></li>

        
        
                    <li><?php echo 'resourcepassword'; ?>: 
                    <?php echo $this->item->resourcepassword; ?></li>

        
        
                    <li><?php echo 'serviceauthentication_id'; ?>: 
                    <?php echo $this->item->serviceauthentication_id; ?></li>

        
        
                    <li><?php echo 'serviceurl'; ?>: 
                    <?php echo $this->item->serviceurl; ?></li>

        
        
                    <li><?php echo 'serviceusername'; ?>: 
                    <?php echo $this->item->serviceusername; ?></li>

        
        
                    <li><?php echo 'servicepassword'; ?>: 
                    <?php echo $this->item->servicepassword; ?></li>

        
        
                    <li><?php echo 'catid'; ?>: 
                    <?php echo $this->item->catid; ?></li>

        
        
                    <li><?php echo 'params'; ?>: 
                    <?php echo $this->item->params; ?></li>

        

        </ul>
        
    </div>

<?php endif; ?>