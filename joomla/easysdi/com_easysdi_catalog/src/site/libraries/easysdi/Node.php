<?php

/**
 * Description of Node
 *
 * @author Administrator
 */
class Node {

    // Enum type
    public static $CLASS = 1;
    public static $ATTRIBUT = 2;
    public static $RELATIONTYPE = 3;
    
    // Enum renderType
    public static $TEXTAREA = 1;
    public static $CHECKBOX = 2;
    public static $RADIOBUTTON = 3;
    public static $LIST = 4;
    public static $TEXTBOX = 5;
    public static $DATE = 6;
    public static $DATETIME = 7;
   
    public $level;
    public $id;
    public $rel_name = '';
    public $rel_guid;
    public $guid;
    public $name;
    public $type;
    public $renderType;
    public $childs = array();
    public $lowerbound;
    public $upperbound;
    public $parent_class_name;
    public $child_name;
    public $rel_ns;

}

?>
