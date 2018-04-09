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

        
        
                    <li><?php echo 'addresstype_id'; ?>: 
                    <?php echo $this->item->addresstype_id; ?></li>

        
        
                    <li><?php echo 'organismcomplement'; ?>: 
                    <?php echo $this->item->organismcomplement; ?></li>

        
        
                    <li><?php echo 'organism'; ?>: 
                    <?php echo $this->item->organism; ?></li>

        
        
                    <li><?php echo 'civility'; ?>: 
                    <?php echo $this->item->civility; ?></li>

        
        
                    <li><?php echo 'firstname'; ?>: 
                    <?php echo $this->item->firstname; ?></li>

        
        
                    <li><?php echo 'lastname'; ?>: 
                    <?php echo $this->item->lastname; ?></li>

        
        
                    <li><?php echo 'function'; ?>: 
                    <?php echo $this->item->function; ?></li>

        
        
                    <li><?php echo 'address'; ?>: 
                    <?php echo $this->item->address; ?></li>

        
        
                    <li><?php echo 'addresscomplement'; ?>: 
                    <?php echo $this->item->addresscomplement; ?></li>

        
        
                    <li><?php echo 'postalcode'; ?>: 
                    <?php echo $this->item->postalcode; ?></li>

        
        
                    <li><?php echo 'postalbox'; ?>: 
                    <?php echo $this->item->postalbox; ?></li>

        
        
                    <li><?php echo 'locality'; ?>: 
                    <?php echo $this->item->locality; ?></li>

        
        
                    <li><?php echo 'country'; ?>: 
                    <?php echo $this->item->country; ?></li>

        
        
                    <li><?php echo 'phone'; ?>: 
                    <?php echo $this->item->phone; ?></li>

        
        
                    <li><?php echo 'mobile'; ?>: 
                    <?php echo $this->item->mobile; ?></li>

        
        
                    <li><?php echo 'fax'; ?>: 
                    <?php echo $this->item->fax; ?></li>

        
        
                    <li><?php echo 'email'; ?>: 
                    <?php echo $this->item->email; ?></li>

        
        
                    <li><?php echo 'sameascontact'; ?>: 
                    <?php echo $this->item->sameascontact; ?></li>

        

        </ul>
        
    </div>

<?php endif; ?>