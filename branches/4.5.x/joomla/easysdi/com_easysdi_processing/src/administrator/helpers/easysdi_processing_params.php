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


abstract class Easysdi_processingParamsHelper
{

  const PREFIX = 'param_';

  static public function slugify($text)
  {
  // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

  // trim
    $text = trim($text, '-');

  // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // lowercase
    $text = strtolower($text);

  // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    if (empty($text))
    {
      return 'n-a';
    }

    return $text;
  }

  private static function forceJson(&$var) {
    if (is_string($var)) $var=json_decode($var);
  }


  public static function label($param){
    self::forceJson($param);
    $name=self::PREFIX.self::slugify($param->name);
    if ($param->type=='checkbox') return '';
    return '<label for="'.$name.'">'.$param->title.'</label>';
  }


  public static function formatValue($def, $value) {
    if (!isset($value)) {
      if ($def->required) return '<p class="alert alert-danger">'.JText::_('COM_EASYSDI_PROCESSING_UNKNOW_VAL').'</p>';
      return '<span class="text-warning">'.JText::_('COM_EASYSDI_PROCESSING_UNKNOW_VAL').'</span>';
    }

    if ($def->type=='checkbox') {
      if (true === $value) return JText::_('COM_EASYSDI_PROCESSING_BOOLEAN_TRUE');
      return JText::_('COM_EASYSDI_PROCESSING_BOOLEAN_FALSE');
    }


    if ($def->type=='file') {
      return '<a href="#'.$def->name.'">'.$value.'</a>';
    }

    if ($def->type=='select') {
      foreach ($def->values as $defvalue) {
        if ($defvalue->id === $value) return $defvalue->text.' ['.$defvalue->id.']';
      }
    }

    if ($def->type=='selectmulti') {
      $res='<ul>';
      if (is_array($value)) {
        foreach ($def->values as $defvalue) {
          if (in_array($defvalue->id,$value)) $res.='<li>'.$defvalue->text.' ['.$defvalue->id.']</li>';
        }
      }
      $res.='</ul>';
      return $res;
    }

    return $value;
  }


  public static function input($param){
    self::forceJson($param);
    $res='';
    $name=self::PREFIX.self::slugify($param->name);

    if ($param->type=='text') {
      $res .= '<input type="text" id="'.$name.'" name="'.$name.'"'
      .($param->required?' required':'')
      .' value="'.$param->default.'" class="form-control params_editor_input_source">';
    }

    if ($param->type=='number') {
      $step=(null!==$param->step?$param->step:1);
      $res .= '<input type="number" id="'.$name.'" name="'.$name.'" value="'.(float) $param->default.'"'
      .($param->min!==null?' min="'.$param->min.'"':'')
      .($param->max!==null?' max="'.$param->max.'"':'')
      .' step="'.$step.'" class="form-control params_editor_input_source">';
    }

    if ($param->type=='textarea') {
      $res .= '<textarea id="'.$name.'" name="'.$name.'"'.($param->required?' required':'').' class="form-control params_editor_input_source">'
      .$param->default
      .'</textarea>';
    }

    if ($param->type=='checkbox') {
      $res .= '<label><input type="checkbox" id="'.$name.'" name="'.$name.'"'.($param->default==true?' checked':'').' class="form-control params_editor_input_source"> '
      .$param->title
      .'</label>';
    }


    if ($param->type=='date') {
      $res .= '<input type="date" id="'.$name.'" name="'.$name.'"'
      .($param->required?' required':'')
      .' value="'.$param->default.'" class="form-control params_editor_input_source">';
    }


    if ($param->type=='file') {
      $res .= '<input type="file" id="'.$name.'" name="'.$name.'"'
      .($param->required?' required':'')
      .' value="'.$param->default.'" class="form-control params_editor_input_source">';
    }

    if ($param->type=='select' || $param->type=='selectmulti') {
      $res .= '<select id="'.$name.'" name="'.$name.'"'.($param->type=='selectmulti'?' multiple':'').($param->required?' required':'').' class="form-control params_editor_input_source">';
      $defaults=is_array($param->default)?$param->default:array($param->default);
      foreach ($param->values as $value) {
        $res .= '<option value="'.$value->id.'"'.(in_array($value->id, $defaults)?' selected':'').'>'
        .$value->text
        .'</option>';
      }
      $res .= '</select>';
    }

    if ($param->desc) {
      $res.='<p class="help-block">'.str_replace("\n", "<br>",$param->desc).'</p>';
    }

    return $res;
  }



  public static function table($paramsDef,$params,$order=null){

    self::forceJson($paramsDef);
    self::forceJson($params);

    if (null == $paramsDef) $paramsDef=array();
    if (null == $params) $params=array();

    $res='<table class="table table-bordered table-striped table-params">';

    foreach ($paramsDef as $def) {
      $defname=$def->name;
      $res.='<tr>';
      $res.='<th>'.$def->title.'</th>';
      $res.='<td>';
      if($def->type=='file') {
        if ($params->$defname!=='') {
          $res.=self::file_link($params->$defname, $order, "field");
        }
      } else {
        $res.=self::formatValue($def,$params->$defname);
      }
      $res.='</td>';
      $res.='</tr>';
    }
    $res.='</table>';
    return $res;
  }

  public static function human_filesize($bytes, $decimals = 2) {
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
  }


  /** visualisation plug in extensions**/
  protected static $plugins_extensions=array(
    'geojson'
    );

  private static function visuplugin_geojson($filename, $order, $type) {
    $proxy_link=self::file_url($order, $type, $filename);
    return '</br>'.JText::_('COM_EASYSDI_PROCESSING_LBL_PREVIEW_OUTPUT').' : <a href="#" class="btn btn-default btn-mini openvisu"
    data-visu="geojson" data-url="'.$proxy_link.'"
    data-mapid="'.$order->processing_map_id.'" data-name="'.$filename.'">'.JText::_('COM_EASYSDI_PROCESSING_SHOW').'</a>';
  }


  public static function file_span($file, $order, $type="output", $filename="") {
    switch ($type) {
      case 'input' :
      if ($order->filestorage=='url')
      {
        $t='<span class="file" data-extension="'.pathinfo($order->fileurl, PATHINFO_EXTENSION).'">';
        $t.=pathinfo($order->fileurl, PATHINFO_BASENAME);
        $t.='</span>';
      }else{
        if (!is_null($order->file)){
            $path_parts = pathinfo($order->file);
            $t='<span class="file" data-extension="'.$path_parts['extension'].'">';
            $t.=substr($order->file,strpos($order->file,'-')+1,strlen($order->file)-strpos($order->file,'-'));
            $filepath=JComponentHelper::getParams('com_easysdi_processing')->get('upload_path'). '/' . $order->file;
            if (!file_exists($filepath)) {
              $t.=' <strong>fichier introuvable</strong>';// /*DEBUG*/.$filepath;
            } else {
              $t.='&nbsp;<small>'.self::human_filesize(filesize($filepath)).'</small>';
            }$t.='</span>';
        }else{
            $t.=' <strong>fichier introuvable</strong>';
        }
      }
      break;
      case 'output' :
      $path_parts = pathinfo($order->output);
      $t='<span class="file" data-extension="'.$path_parts['extension'].'">';
      $t.=$order->output;
      $filepath=JComponentHelper::getParams('com_easysdi_processing')->get('output_path'). '/' . $order->id . '/' . $order->outputpreview;
        if (!file_exists($filepath)) {
          $t.=' <strong>fichier introuvable</strong>';
        } else {
          $t.='&nbsp;<small>'.self::human_filesize(filesize($filepath)).'</small>';
        } $t.='</span>';
      break;
      case 'outputpreview' :
      if (isset($order->outputpreview)){
        $path_parts = pathinfo($order->outputpreview);
        $t='<span class="file" data-extension="'.$path_parts['extension'].'">';
        $t.=$order->outputpreview;
        $filepath=JComponentHelper::getParams('com_easysdi_processing')->get('output_path'). '/' . $order->id . '/' . $order->outputpreview;
        if (!file_exists($filepath)) {
          $t.=' <strong>fichier introuvable</strong>';
        } else {
          $t.='&nbsp;<small>'.self::human_filesize(filesize($filepath)).'</small>';
        }
        $t.='</span>';
      }
      break;
      case 'field' :

      $path_parts = pathinfo($file);
      $t='<span class="file" data-extension="'.$path_parts['extension'].'">';
      $t.=substr($file,strpos($file,'-')+1,strlen($file)-strpos($file,'-'));
      $filepath=JComponentHelper::getParams('com_easysdi_processing')->get('upload_path'). '/' . $file;
        if (!file_exists($filepath)) {
          $t.=' <strong>fichier introuvable</strong>';
        } else {
          $t.='&nbsp;<small>'.self::human_filesize(filesize($filepath)).'</small>';
        }
      $t.='</span>';
      break;
    }
    return $t;
  }

  public static function  file_url($order, $type="output", $filename="") {
    if (("output"==$type) || ("outputpreview"==$type)|| ("field"==$type)) return JRoute::_('index.php?option=com_easysdi_processing&task=myorder.proxy&order_id='.$order->id.'&type='.$type.'&file='.$filename);
    if (($order->file == "") && $order->fileurl == "") return "";
    return JRoute::_('index.php?option=com_easysdi_processing&task=myorder.proxy&order_id='.$order->id.'&type='.$type);
  }

  public static function file_link($file, $order, $type="output", $showPlugins=true) {
    if (strpos($file,'\\') !== false) {
        $tmp = preg_split("[\\\]",$file);
        $file = $tmp[count($tmp) - 1];
      }
    $pathinfo=pathinfo($file);
    $proxy_link=self::file_url($order, $type, $pathinfo['basename'], $file);
    if ($proxy_link<>""){
        $res='<a href="'.$proxy_link.'">'.self::file_span($file, $order, $type).'</a>';
    }else{
        $res='<a >'.self::file_span($file, $order, $type).'</a>';
    }

    if ($showPlugins && in_array($pathinfo['extension'], self::$plugins_extensions)) {
      $function_name='visuplugin_'.$pathinfo['extension'];
      $res.= self::$function_name($pathinfo['basename'], $order, $type);
    }
    return $res;
  }



}
