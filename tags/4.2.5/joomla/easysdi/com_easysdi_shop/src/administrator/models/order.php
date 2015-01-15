<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Easysdi_shop model.
 */
class Easysdi_shopModelorder extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_EASYSDI_SHOP';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Order', $prefix = 'Easysdi_shopTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_easysdi_shop.order', 'order', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_easysdi_shop.edit.order.data', array());

		if (empty($data)) {
			$data = $this->getItem();

		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{

		/*if ($item = parent::getItem($pk)) {

			//Do any procesing on fields here if needed

		}*/

        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        $db = $this->getDbo();

        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*'
                )
        );
        $query->from('#__sdi_order AS a');


        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');



        // Join over the user field 'user'
        $query->select('users2.name AS user')
        ->join('LEFT', '#__sdi_user AS sdi_user ON sdi_user.id=a.user_id')
        ->join('LEFT', '#__users AS users2 ON users2.id=sdi_user.user_id');

        // Join over the orderstate field 'orderstate'
        $query->select('orderstate.value AS orderstate')
        ->join('LEFT', '#__sdi_sys_orderstate AS orderstate ON orderstate.id = a.orderstate_id');

        // Join over the ordertype field 'ordertype'
        $query->select('ordertype.value AS ordertype')
        ->join('LEFT', '#__sdi_sys_ordertype AS ordertype ON ordertype.id = a.ordertype_id');

        // Join over the thirdparty field 'thirdparty'
        $query->select('users3.name AS thirdparty')
        ->join('LEFT', '#__sdi_user AS sdi_user2 ON sdi_user2.id=a.thirdparty_id')
        ->join('LEFT', '#__users AS users3 ON users3.id=sdi_user2.user_id');

        // Join over the diffusion field 'products'
        /*$query->select("GROUP_CONCAT(diffusion.name SEPARATOR '".PHP_EOL."') AS products")
        ->join('LEFT', '#__sdi_order_diffusion AS order_diffusion ON order_diffusion.order_id =a.id')
        ->join('LEFT', '#__sdi_diffusion AS diffusion ON diffusion.id=order_diffusion.diffusion_id');*/

        $query->select($query->concatenate(array('diffusion.name', $query->quote(' ('), 'organism.name' , $query->quote(')') )) . ' as product')
                ->join('LEFT', '#__sdi_order_diffusion AS order_diffusion ON order_diffusion.order_id =a.id')
                ->join('LEFT', '#__sdi_diffusion AS diffusion ON diffusion.id=order_diffusion.diffusion_id')
                ->join('LEFT', '#__sdi_resource AS resource ON resource.id=diffusion.version_id')
                ->join('LEFT', '#__sdi_organism AS organism ON organism.id=resource.organism_id')
                ->group('a.id');

        $query->where('a.id = '. (int)$pk);

        $db->setQuery($query);
        $items= $db->loadObjectList();
        
        $products = array();
        foreach ($items as $item) {
            $products[] = $item->product;
        }
        
        $item->products = implode('</br>'.PHP_EOL, $products);
	
        return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
                                $query = $db->getQuery(true);
                                $query->select('MAX(ordering)');
                                $query->from('FROM #__sdi_order');
                                
				$db->setQuery($query);
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}

}