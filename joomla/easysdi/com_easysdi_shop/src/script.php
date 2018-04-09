<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class com_easysdi_shopInstallerScript {
    /*
     * $parent is the class calling this method.
     * $type is the type of change (install, update or discover_install, not uninstall).
     * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
     * If preflight returns false, Joomla will abort the update and undo everything already done.
     */

    function preflight($type, $parent) {

        // Installing component manifest file version
        $this->release = $parent->get("manifest")->version;

        $this->version = $this->getParam("version");

        // Show the essential information at the install/update back-end
        echo '<p>EasySDI component Shop [com_easysdi_shop]';
        echo '<br />' . JText::_('COM_EASYSDI_SHOP_INSTALL_SCRIPT_MANIFEST_VERSION') . $this->release;
    }

    /*
     * $parent is the class calling this method.
     * install runs after the database scripts are executed.
     * If the extension is new, the install method is run.
     * If install returns false, Joomla will abort the install and undo everything already done.
     */

    function install($parent) {
        // You can have the backend jump directly to the newly installed component configuration page
        // $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
    }

    /*
     * $parent is the class calling this method.
     * update runs after the database scripts are executed.
     * If the extension exists, then the update method is run.
     * If this returns false, Joomla will abort the update and undo everything already done.
     */

    function update($parent) {
        // You can have the backend jump directly to the newly updated component configuration page
        // $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
    }

    /*
     * $parent is the class calling this method.
     * $type is the type of change (install, update or discover_install, not uninstall).
     * postflight is run after the extension is registered in the database.
     */

    function postflight($type, $parent) {
        $db = JFactory::getDbo();

        if ($type == 'install') {
            JTable::addIncludePath(JPATH_ADMINISTRATOR . "/../libraries/joomla/database/table");
            JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables');

            //Create the free perimeter
            $row = JTable::getInstance('perimeter', 'easysdi_shopTable');
            $row->id = 1;
            $row->alias = 'freeperimeter';
            $row->ordering = 1;
            $row->state = 1;
            $row->name = 'Free perimeter';
            $row->accessscope_id = 1;
            $row->perimetertype_id = 1;
            $row->access = 1;
            $row->created_by = 356;
            $row->created = '0002-11-30 00:00:00';
            $row->checked_out = 0;
            $row->checked_out_time = '0002-11-30 00:00:00';
            $result1 = $row->store();
            if (!(isset($result1)) || !$result1) {
                JError::raiseError(42, JText::_('COM_EASYSDI_SHOP_POSTFLIGHT_SCRIPT_BACKGROUND_ERROR') . $row->getError());
                return false;
            }

            //Create my perimeter
            $row = JTable::getInstance('perimeter', 'easysdi_shopTable');
            $row->id = 2;
            $row->alias = 'myperimeter';
            $row->ordering = 1;
            $row->state = 1;
            $row->name = 'My perimeter';
            $row->accessscope_id = 1;
            $row->perimetertype_id = 1;
            $row->access = 1;
            $row->created_by = 356;
            $row->created = '0002-11-30 00:00:00';
            $row->checked_out = 0;
            $row->checked_out_time = '0002-11-30 00:00:00';
            $result2 = $row->store();
            if (!(isset($result2)) || !$result2) {
                JError::raiseError(42, JText::_('COM_EASYSDI_SHOP_POSTFLIGHT_SCRIPT_BACKGROUND_ERROR') . $row->getError());
                return false;
            }

            //add default order account
            $user = JFactory::getUser();
            $params['orderaccount'] = $user->id;
            $this->setParams($params);
        }

        if ($type == 'update' && $this->release <= '4.3.2') {
            $archiveorderdelay = $this->getParamValue("archiveorderdelay");
            if (!empty($archiveorderdelay)) {
                $this->setParams(array("cleanuporderdelay" => $archiveorderdelay));
            }
        }

        if ($type == 'update' && $this->release == '4.4.4' && version_compare($this->version, $this->release) == -1) {
            $rounding = $this->getParamValue("rounding");

            $vat = $this->getParamValue("vat");
            if (isset($vat)) {
                $sql = "UPDATE `#__sdi_organism` SET fixed_fee_te = fixed_fee_te / (1+(" . $vat . "/100))";
                $db->setQuery($sql);
                $db->execute();
            }

            $overall_default_fee = $this->getParamValue("overall_default_fee");
            if (isset($overall_default_fee)) {
                $overall_default_fee = $overall_default_fee / (1 + ($this->getParamValue("vat") / 100));
                if (isset($rounding)) {
                    $overall_default_fee = round($overall_default_fee / $rounding, 0) * $rounding;
                }
                $params = array("overall_default_fee" => $overall_default_fee);
                $this->setParams($params);
            }

            if (isset($rounding)) {
                $sql = "UPDATE `#__sdi_pricing_order` SET cfg_overall_default_fee_te = round(cfg_overall_default_fee_te / (1+(cfg_vat/100))/" . $rounding . ", 0)*" . $rounding;
                $db->setQuery($sql);
                $db->execute();
                $sql = "UPDATE `#__sdi_pricing_order_supplier` s SET s.cfg_fixed_fee_te = round(s.cfg_fixed_fee_te / (1+((SELECT o.cfg_vat FROM `#__sdi_pricing_order` o WHERE o.id = s.pricing_order_id)/100))/" . $rounding . ", 0)*" . $rounding;
                $db->setQuery($sql);
                $db->execute();
                $sql = "UPDATE `#__sdi_pricing_order_supplier_product_profile` pospp SET pospp.cfg_fixed_fee_te = round(pospp.cfg_fixed_fee_te / (1+((SELECT o.cfg_vat FROM `#__sdi_pricing_order` o INNER JOIN `#__sdi_pricing_order_supplier` pos ON o.id = pos.pricing_order_id  INNER JOIN `#__sdi_pricing_order_supplier_product` posp ON pos.id = posp.pricing_order_supplier_id  WHERE posp.id = pospp.pricing_order_supplier_product_id)/100))/" . $rounding . ",0)*" . $rounding;
                $db->setQuery($sql);
                $db->execute();
            } else {
                $sql = "UPDATE `#__sdi_pricing_order` SET cfg_overall_default_fee_te = cfg_overall_default_fee_te / (1+(cfg_vat/100))";
                $db->setQuery($sql);
                $db->execute();
                $sql = "UPDATE `#__sdi_pricing_order_supplier` s SET s.cfg_fixed_fee_te = s.cfg_fixed_fee_te / (1+((SELECT o.cfg_vat FROM `#__sdi_pricing_order` o WHERE o.id = s.pricing_order_id)/100))";
                $db->setQuery($sql);
                $db->execute();
                $sql = "UPDATE `#__sdi_pricing_order_supplier_product_profile` pospp SET pospp.cfg_fixed_fee_te = pospp.cfg_fixed_fee_te / (1+((SELECT o.cfg_vat FROM `#__sdi_pricing_order` o INNER JOIN `#__sdi_pricing_order_supplier` pos ON o.id = pos.pricing_order_id  INNER JOIN `#__sdi_pricing_order_supplier_product` posp ON pos.id = posp.pricing_order_supplier_id  WHERE posp.id = pospp.pricing_order_supplier_product_id)/100))";
                $db->setQuery($sql);
                $db->execute();
            }

            $params = array("overall_fee_apply_vat" => 1);
            $this->setParams($params);
        }

        $query = $db->getQuery(true);
        $query->delete('#__menu');
        $query->where('title = ' . $db->quote('com_easysdi_shop'));
        $db->setQuery($query);
        $db->query();
    }

    /*
     * $parent is the class calling this method
     * uninstall runs before any other action is taken (file removal or database processing).
     */

    function uninstall($parent) {
        
    }

    /*
     * get a variable from the manifest file (actually, from the manifest cache).
     */

    function getParam($name) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('manifest_cache');
        $query->from('#__extensions');
        $query->where('name = ' . $db->quote('com_easysdi_shop'));
        $db->setQuery($query);
        $manifest = json_decode($db->loadResult(), true);
        return $manifest[$name];
    }

    /*
     * get a variable from the params field.
     */

    function getParamValue($name) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('params');
        $query->from('#__extensions');
        $query->where('name = ' . $db->quote('com_easysdi_shop'));
        $db->setQuery($query);
        $manifest = json_decode($db->loadResult(), true);
        return $manifest[$name];
    }

    /*
     * sets parameter values in the component's row of the extension table
     */

    function setParams($param_array) {
        if (count($param_array) > 0) {
            // read the existing component value(s)
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('params');
            $query->from('#__extensions');
            $query->where('name = ' . $db->quote('com_easysdi_shop'));

            $db->setQuery($query);
            $params = json_decode($db->loadResult(), true);
            // add the new variable(s) to the existing one(s)
            foreach ($param_array as $name => $value) {
                $params[(string) $name] = (string) $value;
            }
            // store the combined new and existing values back as a JSON string
            $paramsString = json_encode($params);
            $query = $db->getQuery(true);
            $query->update('#__extensions');
            $query->set('params = ' . $db->quote($paramsString));
            $query->where('name = ' . $db->quote('com_easysdi_shop'));
            $db->setQuery($query);
            $db->query();
        }
    }

}
