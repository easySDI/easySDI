<?php
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
