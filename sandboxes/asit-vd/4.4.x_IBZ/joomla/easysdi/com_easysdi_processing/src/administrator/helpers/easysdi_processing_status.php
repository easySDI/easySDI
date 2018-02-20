<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


abstract class Easysdi_processingStatusHelper
{


  static private function style($st) {
    $styles=array(
    'NEW' => 'warning',
    'ACTIVE' => 'warning',
    'DONE' => 'success',
    'FAIL' => 'danger',
    'ARCHIVED' => 'default'
    );
    return (isset($styles[$st])?$styles[$st]:false);
  }

  static public function status($st)
  {
      $st=strtoupper($st);

      if (self::style($st))
       return '<span class="label label-'.self::style($st).'">'.JText::_('COM_EASYSDI_PROCESSING_STATUS_'.$st).'</span>';

     return $st;
  }
  
  static public function getStatus()
  {
     return array(
        'NEW'=>JText::_('COM_EASYSDI_PROCESSING_STATUS_NEW'),
        'ACTIVE'=>JText::_('COM_EASYSDI_PROCESSING_STATUS_ACTIVE'),
        'DONE'=>JText::_('COM_EASYSDI_PROCESSING_STATUS_DONE'),
        'FAIL'=>JText::_('COM_EASYSDI_PROCESSING_STATUS_FAIL'),
        'ARCHIVED'=>JText::_('COM_EASYSDI_PROCESSING_STATUS_ARCHIVED')
    );
  }




}
