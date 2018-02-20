<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_processing/models/orders.php';

/**
 * Methods supporting a list of Easysdi_processing records.
 */
class Easysdi_processingModelmyrequests extends Easysdi_processingModelorders {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        $user = sdiFactory::getSdiUser();
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState('DISTINCT ' .
                'list.select', ' a.*'
                )
            );
        $query->from('#__sdi_processing_order AS a');



        // Join over the user field 'user'
        $query->select($db->quoteName('users2.name', 'user'))
        ->join('LEFT', '#__sdi_user AS sdi_user ON sdi_user.id=a.created_by')
        ->join('LEFT', '#__users AS users2 ON users2.id=sdi_user.created_by');

        //Join over processing field 'processing_id'
        $query->select($db->quoteName('p.name', 'processing'))
        //->select($db->quoteName('p.plugins', 'plugins'))

        ->join('LEFT', '#__sdi_processing AS p ON p.id=a.processing_id');

        $query->where('p.contact_id = ' . (int) $user->id);


        // Filter by ordertype processing
        $ordertype = $this->getState('filter.orderprocessing');
        if (is_numeric($ordertype)) {
            $query->where('a.processing_id = ' . (int) $ordertype);
        }

        // Filter by orderstate state
        $orderstate = $this->getState('filter.orderstatus');
        if ($orderstate) {
            $query->where("a.status = '" . $orderstate ."'");
        }




        // Filter by ordersent state
        $ordersent = $this->getState('filter.ordersent');
        if ($ordersent !== '') {
            // Get UTC for now.
            $dNow = new JDate;
            $dStart = clone $dNow;

            switch ($ordersent) {
                case 'past_week':
                $dStart->modify('-7 day');
                break;

                case 'past_1month':
                $dStart->modify('-1 month');
                break;

                case 'past_3month':
                $dStart->modify('-3 month');
                break;

                case 'past_6month':
                $dStart->modify('-6 month');
                break;

                case 'post_year':
                case 'past_year':
                $dStart->modify('-1 year');
                break;

                case 'today':
                    // Ranges that need to align with local 'days' need special treatment.
                $app = JFactory::getApplication();
                $offset = $app->getCfg('offset');

                    // Reset the start time to be the beginning of today, local time.
                $dStart = new JDate('now', $offset);
                $dStart->setTime(0, 0, 0);

                    // Now change the timezone back to UTC.
                $tz = new DateTimeZone('GMT');
                $dStart->setTimezone($tz);
                break;
            }

            if ($ordersent == 'post_year') {
                $query->where(
                    'a.created < ' . $db->quote($dStart->format('Y-m-d H:i:s'))
                    );
            } else {
                $query->where(
                    'a.created >= ' . $db->quote($dStart->format('Y-m-d H:i:s')) .
                    ' AND a.created <=' . $db->quote($dNow->format('Y-m-d H:i:s'))
                    );
            }
        } // end ($ordersent!=='')
        // Filter by ordercompleted state
        $ordercompleted = $this->getState('filter.ordercompleted');
        if ($ordercompleted !== '') {
            // Get UTC for now.
            $dNow = new JDate;
            $dStart = clone $dNow;

            switch ($ordercompleted) {
                case 'past_week':
                $dStart->modify('-7 day');
                break;

                case 'past_1month':
                $dStart->modify('-1 month');
                break;

                case 'past_3month':
                $dStart->modify('-3 month');
                break;

                case 'past_6month':
                $dStart->modify('-6 month');
                break;

                case 'post_year':
                case 'past_year':
                $dStart->modify('-1 year');
                break;

                case 'today':
                    // Ranges that need to align with local 'days' need special treatment.
                $app = JFactory::getApplication();
                $offset = $app->getCfg('offset');

                    // Reset the start time to be the beginning of today, local time.
                $dStart = new JDate('now', $offset);
                $dStart->setTime(0, 0, 0);

                    // Now change the timezone back to UTC.
                $tz = new DateTimeZone('GMT');
                $dStart->setTimezone($tz);
                break;
            }

            if ($ordercompleted == 'post_year') {
                $query->where(
                    'a.sent < ' . $db->quote($dStart->format('Y-m-d H:i:s'))
                    );
            } else {
                $query->where(
                    'a.sent >= ' . $db->quote($dStart->format('Y-m-d H:i:s')) .
                    ' AND a.sent <=' . $db->quote($dNow->format('Y-m-d H:i:s'))
                    );
            }
        } // end ($ordersent!=='')
        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $searchOnId = '';
            if (is_numeric($search)) {
                $searchOnId = ' OR (a.id = ' . (int) $search . ')';
            }
            $search = $db->Quote('%' . $db->escape($search, true) . '%');
            $query->where('(( a.name LIKE ' . $search . ' ) '.$searchOnId. ' )');
        }

        $query->order($db->escape('created desc'));

        return $query;
    }


}
